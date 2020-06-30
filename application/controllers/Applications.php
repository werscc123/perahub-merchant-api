<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Applications extends OP_Controller{
    protected $is_auth = true;
    protected $resource_model = 'application';
    protected $req = [
        'path'=>[
             'id'=>'',
             'dir_id'=>''
        ],
        'header'=>[
            'authorization'=>''
        ],
        'query'=>[
            'state'=>'',
            'creator_uid'=>'',
            'bind_uid'=>'',
            'type'=>''
        ],
        'form_data'=>[
            'name'=>'',
            'icon'=>'',
            'site_url'=>'',
            'introduction'=>'',
            'creator_uid'=>'',
            'creator_uin'=>'',
            'type'=>'',
            'path'=>'',
            'pin'=>'',
            'rq_public_key'=>'',
            'authorization'=>''
        ]
    ];
    protected $rules_config=[
        'create'=>[
            [
                'field'=>'name',
                'label'=>'name',
                'rules'=>['required','max_length[10]']
            ],
            [
                'field'=>'icon',
                'label'=>'icon',
                'rules'=>['required','valid_url'],
            ],
            [
                'field'=>'site_url',
                'label'=>'site_url',
                'rules'=>['required','valid_url'],
            ],
            [
                'field'=>'creator_uid',
                'label'=>'creator_uid',
                'rules'=>['required','integer']
            ],
            [
                'field'=>'creator_uin',
                'label'=>'creator_uin',
                'rules'=>['required']
            ]
        ],
        'modify'=>[
            [
                'field'=>'id',
                'label'=>'aid',
                'rules'=>['required']
            ],
            [
                'field'=>'icon',
                'label'=>'icon',
                'rules'=>['valid_url']
            ]
        ],
        'delete_app_directorys'=>[
            [
                'field'=>'id',
                'label'=>'aid',
                'rules'=>['required']
            ],
            [
                'field'=>'dir_id',
                'label'=>'id',
                'rules'=>['required']
            ]
        ]
        
    ];
    public function checkParams(){
        if(!parent::checkParams()){
            return false;
        }
        $method = $this->router->fetch_method();
        if($method==='create_authorization'){
            //校验支付密码
            //授权信息中取得用户
            $authorization = $this->req['header']['authorization'];
            list($header,$payload,$sign) = explode('.',$authorization);
            $payload_arr = json_decode(base64_decode($payload));
            //spid
            $user = $this->authorization->user->getBySpid($payload_arr->sub);
            //获取应用信息
            $app = $this->application->getById($this->req['path']['id']);
            if($app ->creator_uid!=$user->id){
                $this->setError(ERR_PARAMS,'not is your application');
                return false;
            }
            $response = OP_Model::thriftCall('verify_server/verify_pay_pass',[
                'uid'=>$user->id,
                'pay_pass'=>md5($this->req['form_data']['pin']),
                'user_type'=>2
            ]);
            if(!isset($response['check_state'])||!$response['check_state']){
                $this->setError(ERR_PARAMS,'pin is error');
                return false;
            }
        }elseif($method==='modify'){
            if(isset($this->req['form_data']['rq_public_key'])&&$this->req['form_data']['rq_public_key']){
                //更新秘钥必须使用pin
                if(!$this->req['form_data']['authorization']){
                    $this->setError(ERR_PARAMS,'authorization is empty!');
                    return false;
                }
                //校验票据
                if(!$this->application->authorization->checkAuth($this->req['form_data']['authorization'],'app_authorization')){
                    $this->setError(ERR_PARAMS,'authorization is error!');
                    return false;
                }
                unset($this->req['form_data']['authorization']);
            }
        }
        return true;
    }
    protected function format(&$data)
    {
        parent::format($data);
        $method = $this->router->fetch_method();
        if($method==='gets'){
            $this->load->helper('tool');
            foreach($data as $k=>$v){
                $data[$k]['icon'] = get_img_url($v['icon']);
            }
        }
    }
    public function create_authorization(){
        $authorization = $this->application->authorization->create(
            [
                'sub'=>$this->req['path']['id'],
                'aid'=>$this->req['path']['id']
            ],'app_authorization');
        $this->display('json',['authorization'=>$authorization]);
    }
}