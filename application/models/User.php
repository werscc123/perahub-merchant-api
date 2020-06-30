<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends OP_Model{
    protected $table_name = 'user';
    protected $primary = '';
    protected $primary_name = ['spid','id'];
    protected $default_primary_name = 'id';
    protected $display_primary_name = 'uid';
    protected $child_resource_model = ['otp','partner','bindcode','sub','authorization'];

    public function modify($req){
        if(isset($req['form_data']['login_pass'])&&$req['form_data']['login_pass']){
            //修改登录密码
            $response= parent::thriftCall('user_server/modify_login_pwd',[
                'uid'=>$req['path']['id'],
                'password'=>md5($req['form_data']['login_pass']),
                'user_type'=>2
            ]);
            if(!$response){
                $this->_setError(ERR_REMOTE,'failed change of login password');
                return false;
            }
        }
        if(isset($req['form_data']['pin'])&&$req['form_data']['pin']){
            //修改支付密码
            $response = parent::thriftCall('user_server/modify_pay_pwd',[
                'uid'=>$req['path']['id'],
                'password'=>md5($req['form_data']['pin']),
                'user_type'=>2
            ]);
            if(!$response){
                $this->_setError(ERR_REMOTE,'failed change of pay password');
                return false;
            }
        }
        if(isset($req['form_data']['state'])&&$req['form_data']['state']){
            // 修改激活状态
            $req['form_data']['verify_time'] = time();
            Partner::httpCall('manage_auth_server/create_user_audit',[
                'uid'=>$req['path']['id'],
                'audit_type'=>'Merchant',
                'attach'=>json_encode(['level'=>2,'limit'=>$req['form_data']['body']['trading_limit']]),
                'document_type' => $req['form_data']['body']['document_type'],
                'upload'=>$req['form_data']['body']['upload'],
                'limit'=>$req['form_data']['body']['trading_limit'],
                'officers'=>json_encode([
                    [
                        'name'=>$req['form_data']['body']['name'],
                        'mobile'=>$req['form_data']['body']['mobile'],
                        'address'=>$req['form_data']['body']['current_address'],
                        'birthday'=>$req['form_data']['body']['birthday'],
                        'nationality'=>$req['form_data']['body']['nationality'],
                        'work_nature'=>$req['form_data']['body']['nature'],
                        'funds_source'=>$req['form_data']['body']['source_funds']
                    ]
                ])
            ]);
            unset($req['form_data']['state']);
            unset($req['form_data']['body']);
        }
        unset($req['form_data']['pin']);
        unset($req['form_data']['login_pass']);

        return  parent::modify($req);
    }
}