<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Captcha extends OP_Model{
    protected $table_name = '';
    protected $primary = '';
    protected $primary_name = [];

    public function create($req){
        $config = array(
            'img_path'  => './captcha/',
            'img_url'   => '/captcha/',
            'word_length'   => 6,
            'font_size'=>'32',
            'img_width'	=> '100',
            'img_height'	=> '30',
        );

        if(!is_writable('./captcha/')){
            mkdir('./captcha',0777,true);
        }

        $cap = create_captcha($config);
        $img_dir =  './captcha/'.$cap['filename'];
        $img_base64 = img_to_base64($img_dir);
        unlink($img_dir);
        $uuid = uuid();
        $word = $cap["word"];
        $CI =& get_instance();
        //存入redis
        $CI->load->library('CI_Redis',null,'redis');
        $CI->redis->set($uuid, $word); //设置
        $CI->redis->EXPIRE($uuid, 5*60); //设置过期时间 （30 min）
        return ['uuid'=>$uuid,'img'=>$img_base64,'code'=>$word];
    }
}