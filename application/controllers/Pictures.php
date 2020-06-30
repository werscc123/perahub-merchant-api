<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/***
 * 图片
 */
class Pictures extends OP_Controller {
    protected $is_auth = false;
    protected $resource_model = 'picture';
    protected $req = [
        'path'=>[
            'uri'=>''
        ],
        'form_data'=>[
            'sign'=>'',
            'file'=>'',
        ],
    ];
    protected $rules_config=[
        'create'=>[
            [
                'field'=>'sign',
                'label'=>'sign',
                'rules'=>['required'],
            ],
        ]
    ];
    public function get(){
        $this->load->helper('tool');
        $this->display('json',['img_url'=>get_img_url(urldecode($this->req['path']['uri']))]) ;
    }
}