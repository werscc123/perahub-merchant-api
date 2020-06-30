<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Partner extends OP_Model{
    protected $table_name = 'app_bind';
    protected $primary = '';
    protected $primary_name = ['id'];
    protected $default_primary_name = 'id';
    protected $display_primary_name = 'bind_id';

    public function gets($req){
        $data = [
            'total_count'=>0,
            'page'=>intval($req['query']['page']),
            'page_size'=>intval($req['query']['page_size']),
            'data'=>[]
        ];
        if($req['query']['partner_id']){
            $query = $this->db->query("select * from t_user where id=".$req['path']['id']);
            $rows = $query->result_array();
            $data['data'] = $rows;
            $data['total_count'] = 1;
            $data['page'] = 1;
            return $data;
        }
        //查询全部绑定关系
        $query = $this->db->query("select * from t_application where creator_uid = ".$req['path']['id'] . ' and state=1');
        $apps = $query->result_array();
        //获取所有aid
        $aids = array_column($apps,'id');
        //查询所有user
        if(!$aids){
            return $data;
        }
        $sql  = "select 
        a.id as bind_id,
        a.aid,a.uid,
        b.spid,
        b.id as partner_id,
        b.registered_name,
        b.display_name 
        from t_app_bind as a left join t_user as b on a.uid = b.id where a.aid
        in (".implode(',',$aids).") and bind_state=1
        ";
        $sqlCount = "select count(*) as count from t_app_bind as a left join t_user as b on a.uid = b.id where 
        a.aid in (".implode(',',$aids).") and bind_state=1
        ";
      

        $where = '';
        $num = null;
        if($req['query']['page']&&$req['query']['page_size']){
            $num = (intval($req['query']['page'])-1)*intval($req['query']['page_size']);
        }
        if($req['query']['registered_name']){
            $where .= " and b.registered_name like '%{$req['query']['registered_name']}%'";
        }
        $sql .= $where;
        $sqlCount .= $where;
        //查询总数
        $this->db->reset_query();
        
        if($req['query']['page']&&$req['query']['page_size']){
            $data['total_count'] = $this->db->query($sqlCount)->row()->count;
            $this->db->reset_query();
            $data['data'] = $this->db->query($sql)->result_array();
        }else{
            $data = $this->db->query($sql)->result_array();
        }
       
        return $data;
    }
}