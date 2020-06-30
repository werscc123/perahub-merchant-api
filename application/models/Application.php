<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Application extends OP_Model{
    protected $table_name = 'application';
    protected $default_primary_name = 'id';
    protected $child_resource_model=['app_directory','authorization'];
    protected $display_primary_name = 'aid';

    public function create($req){
        //生成appid
        $CI = &get_instance();
        $CI->load->helper('tool');
        $res = openssl_pkey_new();
        openssl_pkey_export($res, $privkey);
        $d= openssl_pkey_get_details($res);
        $pubkey = $d['key'];
        $appid = substr('bnnp'.substr(strtolower(md5(guid().time())),rand(0,15)).time(),0,32);
        $req['form_data']['app_id'] = $appid;
        $req['form_data']['is_bind'] = 1; //当前默认都可以绑定
        $req['form_data']['state'] = 0;
        $req['form_data']['rp_public_key'] = $pubkey;
        $req['form_data']['rp_private_key']=$privkey;
        $response = parent::create($req);
        $aid = $response['aid'];
        //默认绑定创建者
        $time = time();
        $this->db->query("insert into t_app_bind (aid,uid,spid,bind_state,create_time) 
        values ($aid,{$req['form_data']['creator_uid']},'{$req['form_data']['creator_uin']}',1,'$time')");
        // 后台审核
        $response = parent::httpCall('manage_auth_server/create_application_audit',[
            'aid'=>$aid,
            'app_create_time'=>time(),
            'app_create_uid'=>$req['form_data']['creator_uid'],
            'app_create_uin'=>$req['form_data']['creator_uin'],
            'creator_type'=>2,
            'app_name'=>$req['form_data']['name'],
            'app_icon'=>$req['form_data']['icon'],
            'app_introduction'=>$req['form_data']['introduction'],
            'site_url'=>$req['form_data']['site_url']
        ]);
        return [];
    }
    public function gets($req){
        if($req['query']['state']){
            $state = $req['query']['state'];
            if(strstr($req['query']['state'],',')){
                $state = explode(',',$req['query']['state']);
            }
            $this->db->where_in('state',$state);
        }
        if($req['query']['creator_uid']){
            $this->db->where(['creator_uid'=>$req['query']['creator_uid']]);
        }
        if($req['query']['bind_uid']){
            $query = $this->db->query("select * from t_app_bind where uid=".$req['query']['bind_uid'].' and bind_state=1');
            $app_binds = $query->result_array();
            $aids = array_column($app_binds,'aid');
            if($aids){
                $this->db->where_in('id',$aids);
            }else{
				return [];
			}
        }
        $this->db->select("id,app_id,name,icon,auditor_name,auth_url,auto_contract,
        create_time,creator_uid,creator_uin,introduction,rp_public_key,site_url,state,update_time");
        return parent::gets($req);
    }
}