<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class History extends OP_Model{
    protected $table_name = '';
    protected $primary = '';
    protected $primary_name = [];
    protected $child_resource_model=['refund'];

    public function gets($req){
    
        $query_req  = [
            'list_type'=>$req['query']['type'],
            'start_time'=>$req['query']['start_date'],
            'end_time'=>$req['query']['end_date'],
            'trade_state'=>$req['query']['state'],
            'refund_state'=>$req['query']['refund_state'],
            'min_amount'=>$req['query']['min_amount'],
            'max_amount'=>$req['query']['max_amount'],
            'spuid'=>$req['query']['payee_uid'],
            'key_listid'=>$req['query']['list_id'],
            'key_spbillno'=>$req['query']['spbillno'],
        ];
        if($req['query']['sub_spuid']){
            $sub_spuid = @explode(',',$req['query']['sub_spuid']);
            $query_req['sub_spuid'] = $sub_spuid;
        }
    
        if(!$req['query']['generate_excel']){
            $query_req['page']  = $req['query']['page'];
            $query_req['limit']= $req['query']['page_size'];
        }
        $response = parent::thriftCall('order_server/merchant_order_list',$query_req);
        if($req['query']['generate_excel']){
            //生成excel
        }
        return $response;
    }
}