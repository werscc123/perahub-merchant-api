<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Histories extends OP_Controller {
    protected $resource_model = 'history';

    protected $req = [
        'path'=>[
            'listid'=>''
        ],
        'header'=>[
            'authorization'=>''
        ],
        'query'=>[
            'type'=>'',
            'start_date'=>'',
            'end_date'=>'',
            'state'=>'',
            'min_amount'=>'',
            'max_amount'=>'',
            'payee_uid'=>'',
            'list_id'=>'',
            'spbillno'=>'',
            'page'=>'',
            'page_size'=>'',
            'generate_excel'=>'',
            'sub_spuid'=>'' ,
            'refund_state'=>''
        ],
        'form_data'=>[
            'spuid'=>'',
            'pin'=>'',
            'refund_amount'=>''
        ]
    ];
    protected $rules_config=[
        'create'=>[
            [
                'field'=>'listid',
                'label'=>'listid',
                'rules'=>['required']
            ],
            [
                'field'=>'spuid',
                'label'=>'spuid',
                'rules'=>['required']
            ]
        ],
        'create_refund'=>[
            [
                'field'=>'spuid',
                'label'=>'spuid',
                'rules'=>['required']
            ],
            [
                'field'=>'listid',
                'label'=>'listid',
                'rules'=>['required']
            ],
            [
                'field'=>'pin',
                'label'=>'pin',
                'rules'=>['required']
            ],
            [
                'field'=>'refund_amount',
                'label'=>'refund_amount',
                'rules'=>['required','integer']
            ]
        ]
    ];

    protected function checkParams()
    {
        if(!parent::checkParams()){
            return false;
        }
        
        $method = $this->router->fetch_method();
        if($method=='create_refund'){//创建退款时
            //校验pin
            $response = OP_Model::thriftCall('verify_server/verify_pay_pass',[
                'uid'=>$this->req['form_data']['spuid'],
                'pay_pass'=>md5($this->req['form_data']['pin']),
                'user_type'=>2
            ]);
            if(!isset($response['check_state'])||!$response['check_state']){
                $this->setError(ERR_PARAMS,'Original password error!');
                return false;
            }
           // 校验是否操作本用户
           if(!$this->checkUser($this->req['form_data']['spuid'])){
                $this->setError(ERR_PARAMS,'uid is error');
                return false;
            }
        }
        return true;
    }
    
    public function get_refunds(){
        $response = $this->history->refund->gets($this->req);
        $this->display('json',$response);
    }
    public function create_refund(){
        $response = $this->history->refund->create($this->req);
        if(!$response){
            $this->displayError($this->history->refund);
        }
        $this->display('json',$response);
    }
}