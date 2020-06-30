<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bind extends OP_Controller {
    protected $is_auth = false;
    protected $resource_model = 'bindcode';
    protected $req = [
        'path'=>[
            'code'=>''
        ],
    ];
    protected $rules_config=[
        'get'=>[
            [
                'field'=>'code',
                'label'=>'code',
                'rules'=>['required'],
            ],
        ]
    ];
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('tool');
    }
    public function get(){
        $code = $this->req['path']['code'];
        $response = $this->bindcode->getByCode($code);
        if($this->bindcode->hasError()){
            $this->displayError($this->bindcode);
        }
        $data = [];
        $this->load->helper('tool');
        foreach($response as $key=>$value){
            if(in_array($key,['id','app_id','name','icon'])){
                if($key==='id'){
                    $key= 'aid';
                }else if($key==='icon'){
                    $value = get_img_url($value);
                }
                 $data[$key] = $value;
            }
        }
        $this->display('json',$data);
    }
}