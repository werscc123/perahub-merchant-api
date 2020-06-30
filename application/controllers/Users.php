<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/***
 * 验证码资源
 */
class Users extends OP_Controller {
    protected $is_auth = true;
    protected $resource_model = 'user';
    protected $req = [
        'path'=>[
            'id'=>'',
            'aid'=>''
        ],
        'header'=>[
            'authorization'=>''
        ],
        'query'=>[
            'verify_type'=>'email',
            'send_object'=>'',
            'busi_type'=>'',
            'authorization'=>'',
            'partner_id'=>'',
            'registered_name'=>'',
            'page_size'=>'',
            'page'=>'',
            'sub_spid'=>'',
            'keyword'=>''
        ],
        'form_data'=>[
            'uid'=>'',
            'spid'=>'',
            'busi_type'=>'',
            'send_object'=>'',
            'verify_type'=>'',
            'password'=>'',
            'bind_code'=>'',
            'aid'=>'',
            'registration_name'=>'',
            'company_name'=>'',
            'registration_area'=>'',
            'business_category'=>'',
            'work_content'=>'',
            'phone_number'=>'',
            'store_name'=>'',
            'store_address'=>'',
            'website'=>'',
            'avatar'=>'',
            'code_uuid'=>'',
            'otp'=>'',
            'code'=>'',
            'otp_uuid'=>'',
            'authorization'=>'',
            'body'=>'',
            'registered_area'=>'',
            'registered_name'=>''
        ]
    ];
    protected $rules_config=[
        'create_otp'=>[
            [
                'field'=>'id',
                'label'=>'uid',
                'rules'=>['required'],
                ['required'=>'uid can\'t empty']
            ],
            [
                'field'=>'verify_type',
                'label'=>'verify_type',
                'rules'=>['required','in_list[email,sms]'],
            ],
            [
                'field'=>'send_object',
                'label'=>'send_object',
                'rules'=>['required'],
                ['required'=>'send_object can\'t empty']
            ],
            [
                'field'=>'busi_type',
                'label'=>'busi_type',
                'rules'=>['in_list[26,28]'],
                ['in_list[26,28]'=>'please business type enter 26 or 28']
            ]
        ],
        'get_partners'=>[
            [
                'field'=>'id',
                'label'=>'uid',
                'rules'=>['required','integer'],
            ],
            [
                'field'=>'page',
                'label'=>'page',
                'rules'=>['integer'],
            ],
            [
                'field'=>'page_size',
                'label'=>'page_size',
                'rules'=>['integer'],
            ]
        ],
        'create_bindcode'=>[
            [
                'field'=>'aid',
                'label'=>'aid',
                'rules'=>['required','integer'],
            ]
        ],
        'create_sub'=>[
            [
                'field'=>'id',
                'label'=>'uid',
                'rules'=>['required'],
            ],
            [
                'field'=>'registered_name',
                'label'=>'registered_name',
                'rules'=>['required','max_length[20]'],
            ],
            [
                'field'=>'company_name',
                'label'=>'company_name',
                'rules'=>['required','max_length[20]'],
            ],
            [
                'field'=>'registered_area',
                'label'=>'registered_area',
                'rules'=>['required','max_length[20]'],
            ],
            [
                'field'=>'business_category',
                'label'=>'business_category',
                'rules'=>['required','max_length[20]'],
            ],
            [
                'field'=>'work_content',
                'label'=>'work_content',
                'rules'=>['required','max_length[20]'],
            ],
            [
                'field'=>'phone_number',
                'label'=>'phone_number',
                'rules'=>['required','max_length[20]'],
            ],
            [
                'field'=>'store_name',
                'label'=>'store_name',
                'rules'=>['max_length[20]'],
            ],
            [
                'field'=>'store_address',
                'label'=>'store_address',
                'rules'=>['max_length[20]'],
            ],
            [
                'field'=>'website',
                'label'=>'website',
                'rules'=>['valid_url'],
            ],
        ],
        'create_authorization'=>[
            [
                'field'=>'otp_uuid',
                'label'=>'otp_uuid',
                'rules'=>['required'],
            ],
            [
                'field'=>'otp',
                'label'=>'otp',
                'rules'=>['required'],
            ],
            [
                'field'=>'code_uuid',
                'label'=>'code_uuid',
                'rules'=>['required'],
            ],
            [
                'field'=>'code',
                'label'=>'code',
                'rules'=>['required'],
            ],
            [
                'field'=>'id',
                'label'=>'uid',
                'rules'=>['required'],
            ],
        ]
    ];
        protected function checkParams()
        {
           
            $method = $this->router->fetch_method();
            if($method==='create_otp'){
                $user_info = $this->user->getById($this->req['path']['id']);
                if(!$user_info){
                    $user_info = $this->user->getBySpid($this->req['path']['id']);
                }
                if($this->req['form_data']['verify_type']==='email'){
                    if($this->req['form_data']['send_object']!==$user_info->owner_email){
                        $this->setError(ERR_PARAMS,'email error');
                        return false;
                    }
                }else if($this->req['form_data']['verify_type']==='sms'){
                    if($this->req['form_data']['send_object']!==$user_info->owner_mobile){
                        $this->setError(ERR_PARAMS,'mobile error');
                        return false;
                    }
                }
                if(!parent::checkParams(false)){
                    return false;
                }
                return true;
            }elseif($method==='modify'){
                $user_info = $this->user->getById($this->req['path']['id']);

                if(!$user_info){
                    $user_info = $this->user->getBySpid($this->req['path']['id']);
                }
                $this->req['path']['id'] = $user_info->id;
                //局部修改用户
                if(isset($this->req['form_data']['password'])&&$this->req['form_data']['password']){
                    //修改密码，需要验证
                    if($this->req['query']['verify_type']==='current'){
                        //使用原密码修改，校验原密码
                        if($this->req['query']['busi_type']===26){
                            //校验登录密码
                            $response = OP_Model::thriftCall('verify_server/verify_login_pass',[
                                'uid'=>$this->req['path']['id'],
                                'login_pass'=>md5($this->req['query']['authorization']),
                                'user_type'=>2,
                            ]);
                        }else{
                            //校验支付密码
                            $response = OP_Model::thriftCall('verify_server/verify_pay_pass',[
                                'uid'=>$this->req['path']['id'],
                                'pay_pass'=>md5($this->req['query']['authorization']),
                                'user_type'=>2
                            ]);
                        }
                        if(!isset($response['check_state'])||!$response['check_state']){
                            $this->setError(ERR_PARAMS,'Original password error!');
                            return false;
                        }
                    }elseif($this->req['query']['verify_type']){
                       //使用票据校验
                        if(!$this->application->authorization->checkAuth($this->req['query']['authorization'],'user_authorization')){
                            $this->setError(ERR_PARAMS,'authorization is error!');
                            return false;
                        }
                       
                    }else{
                        $this->setError(ERR_PARAMS,'verify_type error');
                        return false;
                    }
                    //去掉不需要的参数
                    if(intval($this->req['query']['busi_type'])===26){
                        // 修改登录密码
                        $this->req['form_data']['login_pass'] = $this->req['form_data']['password'];
                    }else{
                        $this->req['form_data']['pin'] = $this->req['form_data']['password'];
                    }
                    unset($this->req['form_data']['password']);
                    if(!parent::checkParams(false)){
                        return false;
                    }
                    return true;
                }else{
                    if(!$this->checkUser($this->req['path']['id'])){
                        $this->setError(ERR_PARAMS,'uid is error');
                        return false;
                    }
                    $keys_arr = array_keys($this->req['form_data']);
                    foreach($keys_arr as $key){
                        if(!in_array($key,['password','avatar','state','body'])){
                            $this->setError(ERR_PARAMS,'params error');
                            return false;
                        }
                    }
                }
            }elseif($method==='create_partner'){
                // 校验是否操作本用户
                if(!$this->checkUser($this->req['path']['id'])){
                    $this->setError(ERR_PARAMS,'uid is error');
                    return false;
                }
                //校验bind_code 是否正确
                $bind_code = $this->req['form_data']['bind_code'];
                if(!$bind_code_data = base64_decode(urldecode($bind_code),true)){
                    $this->setError(ERR_PARAMS,'bind code error');
                    return false;
                }
                list($header,$pload,$sign) = explode('.',$bind_code_data);
                $payload_obj = @json_decode(base64_decode($pload,true));
                //校验域名
                $host = isset($_SERVER['REMOTE_HOST'])?$_SERVER['REMOTE_HOST']:$_SERVER['HTTP_HOST'];
                if($host!==$payload_obj->aud){
                    $this->setError(ERR_PARAMS,'domain error');
                    return false;
                }
                if($payload_obj->exp<=time()){
                    $this->setError(ERR_PARAMS,'bind_code expired');
                    return false;
                }

                //校验是否已绑定
                $query = $this->user->db->query("select count(*) count from t_app_bind where aid = ".$payload_obj->aid.' and uid = '.$this->req['path']['id']);
                $row = $query->row();
                if($row->count>0){
                    $this->setError(ERR_PARAMS,'aid binding already');
                    return false;
                }

                //取得应用信息
                $query = $this->user->db->query("select * from t_application where id=".$payload_obj->aid);
                $app = $query->row();
                $auth_key = $app->bind_code;
                $a_sign = hash_hmac('sha256', $header.'.'.$pload, $auth_key);
                if($sign!==$a_sign){
                    $this->setError(ERR_PARAMS,'bind_code error');
                    return false;
                }
                $form_data['aid'] = $payload_obj->aid;
                $form_data['uid'] = $this->req['path']['id'];
                $user = $this->user->getByID($this->req['path']['id']);
                $form_data['spid'] =$user->spid;
                $form_data['bind_code'] = $bind_code;
                $form_data['bind_state'] = 1;
                $this->req['form_data'] = $form_data;
                
            }elseif($method==='create_bindcode'){
                //校验应用是否存在或者已经绑定
                $query = $this->{$this->resource_model}->db->query("select count(*) as count from t_application where id = ".$this->req['form_data']['aid']);
                $row = $query->row();
                if(!$row->count){
                    $this->setError(ERR_PARAMS,'aid is error');
                    return false;
                }
            }elseif($method==='delete_partner'){
                $query = $this->{$this->resource_model}->db->query("select count(*) as count from t_application where creator_uid = ".$this->req['path']['id'] ." and id=".$this->req['path']['aid']);
                $row = $query->row();
                if($row->count){
                    $this->setError(ERR_PARAMS,'bind is creator can\'t relieve');
                    return false;
                }
            }elseif($method==='create_authorization'){
                $user_info = $this->user->getBySpid($this->req['path']['id']);
                if(!$user_info){
                    $user_info = $this->user->getById($this->req['path']['id']);
                }
                if(!$user_info){
                    $this->setError(ERR_PARAMS,'user is not exists');
                    return false;
                }
                //验证验证码
                $this->load->library('CI_Redis',null,'redis');
                $word = $this->redis->get($this->req['form_data']['code_uuid']);
                if($word){
                    if(strtolower($word) !== strtolower($this->req['form_data']['code'])){
                        $this->setError(ERR_PARAMS,'verify code is error!');
                        return false;
                    }
                    //成功销毁验证码
                    $this->redis->del($this->req['form_data']['code_uuid']);
                }else{
                    $this->setError(ERR_PARAMS,'verify code not is exists or expired!');
                    return false;
                }
                //验证otp
                $word = $this->redis->get($this->req['form_data']['otp_uuid']);
                if($word){
                    if(strtolower($word) !== strtolower($this->req['form_data']['otp'])){
                        $this->setError(ERR_PARAMS,'otp is error!');
                        return false;
                    }
                    //成功销毁otp
                    $this->redis->del($this->req['form_data']['otp_uuid']);
                }else{
                    $this->setError(ERR_PARAMS,'otp not is exists or expired!');
                    return false;
                }
                if(!parent::checkParams(false)){
                    return false;
                }
                return true;
            }elseif($method==='create_sub'){
                $user_info = $this->user->getBySpid($this->req['path']['id']);
                if(!$user_info){
                    $user_info = $this->user->getById($this->req['path']['id']);
                }
                if(!$user_info){
                    $this->setError(ERR_PARAMS,'user is not exists');
                    return false;
                }
                $this->req['form_data']['spid'] = $user_info->spid;
                return true;
            }
            if(!parent::checkParams()){
                return false;
            }
            return true;
        }
        protected function format(&$data)
        {
            parent::format($data);
            $method = $this->router->fetch_method();
            if($method==='get'){
                $this->load->helper('tool');
                $data['avatar'] =get_img_url($data['avatar']);
            }
        }
    public function get(){
        $response = $this->user->getById($this->req['path']['id']);
        $this->format($response);
        if($response){
            $this->display('json',$response);
        }
        $this->setError(ERR_PARAMS,'resource is not exists');
        $this->display();
    }

    public function create_otp(){
        $response = $this->user->otp->create($this->req);
        $this->display('json',$response);
    }

    public function create_authorization(){
        $authorization = $this->application->authorization->create(
            [
                'sub'=>$this->req['path']['id'],
            ],'user_authorization');
        $this->display('json',['authorization'=>$authorization]);
    }
}