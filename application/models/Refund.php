<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Refund extends OP_Model
{
    protected $table_name = '';

    
    public function gets($req){
        $query_req  = [
            'list_type'=>5,
            'spuid'=>'212457',
            'key_listid'=>$req['query']['list_id'],
        ];
        $response = parent::thriftCall('order_server/merchant_order_list',$query_req);
        return $response;
    }
    public function create($req){
        //查询订单数据
        $response = parent::thriftCall('order_server/query_order_info',['listid'=>$req['path']['listid']]);
        $order_info  = $response['order'];
        $create_req = [
            'spuid'=>$req['form_data']['spuid'],
            'appid'=>$order_info['appid'],
            'refund_listid' => $req['path']['listid'],
            'spbillno' => $order_info['spbillno'].'tt',
            'refund_money' =>$req['form_data']['refund_amount']
        ];
        $response = parent::thriftCall('pay_gate_server/pay_refund_apply',$create_req);
        return $response;
    }
}
