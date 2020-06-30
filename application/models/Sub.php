<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sub extends OP_Model{
    protected $table_name = 'sub_user';
    protected $primary = '';
    protected $primary_name = ['sub_spid','id'];
    protected $default_primary_name = 'id';
    protected $display_primary_name = 'sub_id';
    
    public function create($req){
        //生成sub_spid
        $CI =& get_instance();
        $CI->load->library('CI_Redis',null,'redis');
        do{
            $sub_spid = 'SUBSP'.str_pad($CI->redis->incr('build:sub_spuser:id'),9,0,0); //设置
            $query = $this->db->query("select count(*) as count from t_sub_user where sub_spid='".$sub_spid."'");
            $row = $query->row();
        }while($row->count);
        $req['form_data']['sub_spid'] = $sub_spid;
        return parent::create($req);
    }
    public function gets($req){
        $this->db->where(['uid'=>$req['path']['id']]);

        if($req['query']['sub_spid']){
            $this->db->where(['sub_spid'=>$req['query']['sub_spid']]);
        }
        
        if($req['query']['keyword']){
            $this->db->group_start();
            $this->db->like('registered_name', $req['query']['keyword']); 
            $this->db->or_like('company_name',$req['query']['keyword']);
            $this->db->or_like('sub_spid',$req['query']['keyword']);
            $this->db->group_end();
        }
        if($req['query']['page']&&$req['query']['page_size']){
            $num = (intval($req['query']['page'])-1)*intval($req['query']['page_size']);
            $data['total_count'] = $this->db->count_all_results($this->table_name);
            $data['page'] = $req['query']['page'];
            $data['page_size'] = $req['query']['page_size'];
            $this->db->where(['uid'=>$req['path']['id']]);
            if($req['query']['sub_spid']){
                $this->db->where(['sub_spid'=>$req['query']['sub_spid']]);
            }
            
            if($req['query']['keyword']){
                $this->db->group_start();
                $this->db->like('registered_name', $req['query']['keyword']); 
                $this->db->or_like('company_name',$req['query']['keyword']);
                $this->db->or_like('sub_spid',$req['query']['keyword']);
                $this->db->group_end();
            }
            $this->db->limit($req['query']['page_size'],$num);
            $data['data'] = $this->db->get($this->table_name)->result_array();
        }else{
            $data = $this->db->get($this->table_name)->result_array();
        }
        return $data;
    }
    
}