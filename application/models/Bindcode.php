<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bindcode extends OP_Model{
    protected $table_name = 'application';
    protected $default_primary_name = 'id';
    protected $child_resource_model=['authorization','application'];
    protected $display_primary_name = 'aid';

    public function create($req){
        $CI = &get_instance();
        $CI->load->helper('string');
        $bind_code = random_string();
        $ex_data = ['sub'=>$req['path']['id'].$req['form_data']['aid'],'aid'=>$req['form_data']['aid']];
        $authorization = $this->authorization->create($ex_data,$bind_code);
        $time = time();
        $exp = $time +24*60*60 ;//过期时间24小时后
        //保存到数据库
        $this->db->query(" update t_application set bind_code ='".$bind_code."',bind_exp_time='".$exp."' where id=".$req['form_data']['aid']);
      
        return ['bind_code'=>urlencode(base64_encode($authorization))];
    }
    public function getByCode($code){
        $code_info = base64_decode(urldecode($code));
        list($header,$pload,$sign) = explode('.',$code_info);
        $payload_obj = @json_decode(base64_decode($pload,true));
        if(!$payload_obj){
            $this->_setError(ERR_PARAMS,'code is error');
            return false;
        }
        $app_info = $this->application->getById($payload_obj->aid);
        if(!$app_info){
            $this->_setError(ERR_PARAMS,'code is error');
            return false;
        }
        return $app_info;
    }
}