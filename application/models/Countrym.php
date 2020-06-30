<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Countrym extends OP_Model{
    protected $table_name = '';
    protected $primary = '';
    protected $primary_name = [];

    public function gets($req){
        $response = parent::thriftCall('user_server/query_country_list',[]);
        if(isset($response['country_list'])){
            return $response['country_list'];
        }
        return [];
    }
}