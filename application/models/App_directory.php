<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_directory extends OP_Model{
    protected $table_name = 'app_directory';
    protected $default_primary_name = 'id';
    protected $child_resource_model=[];
    protected $display_primary_name = 'dir_id';
    public function gets($req){
        if($req['query']['type']){
            $this->db->where(['type'=>$req['query']['type']]);
        }
        return parent::gets($req);
    }
    public function create($req){
        //查询appid
        $query = $this->db->query('select app_id from t_application where id='.$req['path']['id']);
        $row = $query->row();
        $req['form_data']['app_id'] = $row->app_id;
        return  parent::create($req);
    }
}
