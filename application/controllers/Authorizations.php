<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authorizations extends OP_Controller{
    protected $is_auth = false;
    protected $resource_model = 'authorization';
    protected $req = [
        'form_data'=>[
            'login_name'=>'',
            'password'=>'',
            'uuid'=>'',
            'code'=>''
        ],
        'path'=>[
            'authorization'=>''
        ]
    ];
    protected $rules_config = [
        'create'=>[
            [
                'field'=>'login_name',
                'label'=>'login_name',
                'rules'=>['required','min_length[5]'],
                ['required'=>'login name can\'t empty']
            ],
            [
                'field'=>'password',
                'label'=>'password',
                'rules'=>['required'],
                ['required'=>'password can\'t empty'],
            ],
           [
               'field'=>'uuid',
               'label'=>'uuid',
               'rules'=>['required'],
               ['required'=>'uuid can\'t empty']
           ],
           [
               'field'=>'code',
               'label'=>'code',
               'rules'=>['required','exact_length[6]'],
               ['required'=>'verify code can\'t empty']
           ]
        ],
        'delete'=>[
            [
                'field'=>'authorization',
                'label'=>'authorization',
                'rules'=>['required'],
                ['required'=>'authorization can\'t empty']
            ]
        ]
    ];
    protected function checkParams()
    {
        if(!parent::checkParams()){
            return false;
        }
        $method = $this->router->fetch_method();
        if($method=='create'){//创建时
            //校验login_name 是否存在
            if(!$user = $this->{$this->resource_model}->user->getBySpid($this->req['form_data']['login_name'])){
                if($this->{$this->resource_model}->user->hasError()){
                    list($code,$message) = $this->{$this->resource_model}->user->getError();
                    $this->setError($code,$message);
                }else{
                    $this->setError(MYSQLI_SERVER_PS_OUT_PARAMS,'user not is exists');
                }
                return false;
            }
            //校验密码是否正确 todo 调用 thrift 接口
            $response = OP_Model::thriftCall('verify_server/verify_login_pass',[
                'uid'=>$user->id,
                'login_pass'=>md5($this->req['form_data']['password']),
                'user_type'=>'2',
            ]);
            if ($response['check_state'] !== true){
                $this->setError(ERR_PARAMS,'password error!');
                return false;
            }
            
            //校验验证码是否正确
           $this->load->library('CI_Redis',null,'redis');
           $word = $this->redis->get($this->req['form_data']['uuid']);
           if($word){
               if(strtolower($word) !== strtolower($this->req['form_data']['code'])){
                   $this->setError(ERR_PARAMS,'verify code is error!');
                   return false;
               }
               //成功销毁验证码
               $this->redis->del($this->req['form_data']['uuid']);
           }else{
               $this->setError(ERR_PARAMS,'verify code not is exists or expired!');
               return false;
           }

        }
        return true;
    }

    public function create(){
        $login_name = $this->req['form_data']['login_name'];
        //拿到user
        $user = $this->authorization->user->getBySpid($login_name);
        $ex_data = ['uid'=>$user->id,'name'=>$user->display_name,'sub'=>$user->spid];
        $auth_key = md5(time().$user->id.$user->spid);
        $authorization = $this->authorization->create($ex_data,$auth_key);
        //保存到数据库
        $this->authorization->user->update(['ele_jwt'=>$auth_key]);
        $this->display('json',['authorization'=>$authorization,'uid'=>(int)$user->id]);
    }
    public function delete(){
        $authorization = urldecode(urldecode($this->req['path']['authorization']));
        list($header,$payload,$sign) = explode('.',$authorization);
        $payload_arr = json_decode(base64_decode($payload));
         //spid
        $user = $this->authorization->user->getBySpid($payload_arr->sub);
 
        if(!$user){
            $this->setError(ERR_PARAMS,'user not exists');
            return false;
        }
        if(!$this->authorization->checkAuth($authorization,$user->ele_jwt)){
            $this->setError(ERR_PARAMS,'authorization error');
            return false;
        }

        if(!$this->authorization->user->update(['ele_jwt'=>''])){
            $this->setError(ERR_PARAMS,'network error');
            return false;
        }
        $this->display();
    }
}