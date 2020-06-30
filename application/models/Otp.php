<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Otp extends OP_Model{
    protected $table_name = '';
    protected $primary = '';
    protected $primary_name = [];

    public function create($req){
        $CI =& get_instance();
        $CI->load->helper('string');
        $CI->load->helper('tool');
        $code = random_string();
        $uuid = uuid();
        $CI->load->library('CI_Redis',null,'redis');
        $CI->redis->set($uuid,$code); //设置
        $CI->redis->EXPIRE($uuid, 5*60); //设置过期时间 （30 min）
        //判断发送类型
        if($req['form_data']['verify_type']==='sms'){
            //发送短信
            $request['phone_numbers'] = array_flip([$req['form_data']['send_object']]);
            $request['busi_type'] = intval($req['form_data']['busi_type']);
            $request['template'] = 'otp_email';
            $request['text'] = ['code'=>$code];

            $response = parent::thriftCall('msg_dispatch_server/send_sms',$request);
        }elseif($req['form_data']['verify_type']==='email'){
            //发送邮件
            $request['emails'] = array_flip([$req['form_data']['send_object']]);
            $request['busi_type'] = intval($req['form_data']['busi_type']);
            $request['template'] = 'otp_email';
            $request['text'] = ['code'=>$code];

            $response = parent::thriftCall('msg_dispatch_server/send_email',$request);
        }
        if($response){
            return ['uuid'=>$uuid];
        }
        $this->_setError(ERR_REMOTE,'origin remote error');
        return false;
    }
}