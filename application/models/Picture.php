<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Picture extends OP_Model
{
    protected $table_name = '';
    protected $primary_name = 'uri';//资源主索引名 可为数组
    protected $default_primary_name = 'uri';//当前默认主键名

    public function create($req){
        $CI= &get_instance();
        $CI->load->config('uri_config');
        $url = config_item('file_upload');
        $ch = curl_init($url);
        if(!isset($_FILES['file'])||$_FILES['file']['size']>3*1024*1024){
            $this->_setError(ERR_PARAMS,'file size exceeded');
            return false;
        }
        $cfile = curl_file_create($_FILES['file']['tmp_name'],$_FILES['file']['type'],$_FILES['file']['name']);
        $data = array('content' => $cfile,'type'=>$req['form_data']['sign']);
        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}
        curl_setopt($ch , CURLOPT_URL , $url);
        curl_setopt($ch,  CURLOPT_HEADER,false);
        curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch , CURLOPT_POST, 1);
        curl_setopt($ch , CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($response,true);
        $return_arr['image_url'] = $output['data']['url'];
        $return_arr['image_uri'] = $output['data']['uri'];
        return $return_arr;
    }

}
