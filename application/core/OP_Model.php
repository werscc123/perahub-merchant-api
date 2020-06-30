<?php

use BnnpRpc\ThriftRPC;

defined('BASEPATH') OR exit('No direct script access allowed');

class OP_Model extends CI_Model{
    protected $table_name;//表名
    protected $primary = '';//资源主索引
    protected $primary_name = 'id';//资源主索引名 可为数组
    protected $default_primary_name = '';//当前默认主键名
    protected $child_resource_model = [];//子资源模型
    protected $only_data;//当前模型数据
    protected $display_primary_name = '';//返回时使用的id 名
    private $_has_error = false;
    private $error = ['code'=>0,'message'=>''];
    public function __construct()
    {
        parent::__construct();
        $load = $this->__get('load');
        foreach ($this->child_resource_model as $resource_model){//自动实例化子资源
            $load->model($resource_model,'',true);
        }
    }
    private function _getOnly(){//根据主键获取数据
        if(!$this->only_data){
            //未查询数据
            if($this->_bindPrimary($this->default_primary_name,$this->primary)){
                return false;
            }
        }
        return $this->only_data;
    }
    protected function setPrimaryName($primary_name){
        $this->primary_name = $primary_name;
    }
    public function update($data,$where =''){
        $this->_has_error = false;
        if(!$where){
            if($this->primary){
                $where =[$this->_getDefaultPrimary()=>$this->primary];
            }else{
                $this->_setError(1004,'where not is exists');
                return false;
            }
        }
        $this->_dropPrimary();
        $data['update_time'] =time();
        return $this->db->update($this->table_name,$data,$where);
    }
    public function __call($method,$arg_array){
        $this->_has_error = false;
        $load = $this->__get('load');
        $load->helper('tool');
        $method = strtolower(to_under_score($method));
        if(strstr($method,'_by_')){
            list($method,$primary_name) = explode('_by_',$method);
            if($this->_getDefaultPrimary()!=$primary_name||$this->primary!=$arg_array[0]){
               if (!$this->_bindPrimary($primary_name,$arg_array[0])){
                   return false;
               }
            }
            return  $this->_getOnly();
        }else if($method==='get'){
            //根据path参数获取数据
            $req = $arg_array[0];
            reset($req['path']);
            if(!$this->_bindPrimary(key($req['path']),current($req['path']))){
                return false;
            }
            return  $this->_getOnly();
        }else if($method==='gets'){
            //获取全部
            $req = $arg_array[0];

            //处理query
            if(isset($req['query'])){

            }
            return $this->_gets();
        }else if($method==='delete'){

            //绑定主键
            $req = $arg_array[0];
            if(!$this->_bindPrimary($this->_getDefaultPrimary(),$req['path'][$this->_getDefaultPrimary()])){
                $this->_setError(1000,'resource is not exists');
                return false;
            }
            $this->db->where($this->_getDefaultPrimary(),$req['path'][$this->_getDefaultPrimary()]);
            return $this->db->delete($this->table_name);
        }else if(strstr($method,'create_')){
            //绑定主键
            $req = $arg_array[0];
            if(!$this->_bindPrimary(array_keys($req['path'])[0],$req['path'][array_keys($req['path'])[0]])){
                $this->_setError(1000,'resource is not exists');
                return false;
            }
            $child_class = substr($method,7);
            if(!$this->$child_class){
                show_404();
                return false;
            }
            $response = $this->$child_class->child_create($req,$this->display_primary_name);
            if($this->$child_class->hasError()){
                list($code,$message) = $this->$child_class->getError();
                $this->_setError($code,$message);
                return false;
            }
            return $response;
            
        }else if(strstr($method,'get_')){
            if(substr($method,-1,1)==='s'){
                $child_class = rtrim(substr($method,4),'s');
                if(!$this->$child_class->table_name){
                    show_404();
                    return false;
                }

                $req = $arg_array[0];
                reset($req['path']);
                $this->$child_class->where([$this->display_primary_name=>current($req['path'])]);
                return $this->$child_class->gets($req);
            }

        }else if($method ==='modify'){
            //绑定主键
            $req = $arg_array[0];
            if(!$this->_bindPrimary(array_keys($req['path'])[0],$req['path'][array_keys($req['path'])[0]])){
                $this->_setError(1000,'resource is not exists');
                return false;
            }
            foreach ($req['form_data'] as $key=>$value){
                if($value){
                    $data[$key] = $value;
                }
            }
            if(isset($data)){
                return $this->update($data,[$this->default_primary_name=>$this->primary]);
            }else{
                return true;
            }

        }else if(strstr($method,'delete_')){
                $child_class = rtrim(substr($method,7),'s');
                if(!$this->$child_class->table_name){
                    show_404();
                    return false;
                }
                $req = $arg_array[0];
                $this->$child_class->where([$this->display_primary_name=>current($req['path'])]);
                $child = end($req['path']);
                $child_key = key($req['path']);
                if($child_key ==$this->$child_class->display_primary_name){
                    $key = 'id';
                }else{
                    $key = $child_key;
                }
                $this->$child_class->where([$key=>$child]);
                $this->$child_class->db->delete($this->$child_class->table_name);
                if($this->db->affected_rows()===0){
                    $this->_setError(ERR_404,'resource is empty');
                    return false;
                }
                return true;
        }else if(method_exists($this->db,$method)){
            if(isset($arg_array[1])){
                return $this->db->$method($arg_array[0],$arg_array[1]);

            }elseif(isset($arg_array[0])){
                return $this->db->$method($arg_array[0]);
            }else{
                return $this->db->$method();
            }

        }
        return false;
    }
    public function insert_batch($data){
        return $this->db->insert_batch($this->table_name,$data);
    }
    public function join($table_name,$condition,$type){
        return $this->db->join($table_name,$condition,$type);
    }
    public function get_display_primary_name(){
        return $this->display_primary_name;
    }
    public function create($req){
        foreach ($req['form_data'] as $key=>$value){
            if($value||$value===0||$value==='0'){
                $this->db->set($key,$value);
            }

        }
        $this->db->set('create_time',time());
        if($this->db->insert($this->table_name)){
            return [$this->display_primary_name=>$this->db->insert_id()];
        }
        return false;
    }
    public function child_create($req,$parent_primary_name=''){
        //剥离外键
        if($parent_primary_name){
            $this->db->set($parent_primary_name,current($req['path']));
        }
        return $this->create($req);
    }
    private function _gets(){
        if(!$this->table_name){
            show_404();
            return false;
        }
        $query = $this->db->get($this->table_name);
        return $result = $query->result();
    }
    public function count_all_results($reset=false){
        return $this->db->count_all_results($this->table_name,$reset);
    }
    private function _bindPrimary($primary_name,$value){
        if(!$value){
            $this->_setError(ERR_PARAMS,'primary is empty');
            return false;
        }
        if(!$this->primary_name){
            $this->_setError(1004,'model primary index is not set');
            return false;
        }
        if(is_array($this->primary_name)){
            if(!in_array($primary_name,$this->primary_name)){
                $this->_setError(1004,'model primary index is not set');
                return false;
            }
        }else{
            if($this->primary_name!=$primary_name){
                $this->_setError(1004,'model primary index is not set');
                return false;
            }
        }
        $this->default_primary_name = $primary_name;
        $this->primary = $value;
        $where = "{$this->default_primary_name} = '{$this->primary}'";
        $query = $this->db->get_where($this->table_name,$where);
        $result = $query->result();
        if(!isset($result[0])){
            return false;
        }
        $this->only_data = $result[0];
        return true;
    }
    private function _dropPrimary(){
        $this->primary = '';
    }

    public static function thriftCall($uri,$requestData){
        require_once APPPATH . '/libraries/Rpc-bnnp/Thrift/ClassLoader/ThriftClassLoader.php';
        //注册Relay目录的命名空间
        $load = new \Thrift\ClassLoader\ThriftClassLoader();
        $load->registerNamespace('BnnpRpc', dirname(__DIR__) . '/libraries/Rpc-bnnp');
        $load->registerNamespace('Thrift', dirname(__DIR__) . '/libraries/Rpc-bnnp/Thrift');
        $load->registerNamespace('BNNPServer', dirname(__DIR__) . '/gen-php/BNNPServer');
        $load->register();
        @list($serverName,$serverFunc) = explode('/',$uri);
        $serverConfigs =  include  APPPATH.'config/' . ENVIRONMENT . '/Rpc_config.php';
        $serverConfig = $serverConfigs[$serverName]['thrift'];
    
        $client = new \BnnpRpc\ThriftRPC($serverName, $serverConfig);
        $result =  $client->$serverFunc($requestData);
        
        if(!isset($result['result'])||$result['result']!==0){
            return false;
        }
        return $result;
    }
    public static function httpCall($uri,$requestData){
        require_once dirname(__DIR__) . '/libraries/Rpc-bnnp/Thrift/ClassLoader/ThriftClassLoader.php';
        //注册Relay目录的命名空间
        $load = new \Thrift\ClassLoader\ThriftClassLoader();
        $load->registerNamespace('BnnpRpc', dirname(__DIR__) . '/libraries/Rpc-bnnp');
        $load->registerNamespace('Thrift', dirname(__DIR__) . '/libraries/Rpc-bnnp/Thrift');
        $load->registerNamespace('BNNPServer', dirname(__DIR__) . '/gen-php/BNNPServer');
        $load->register();
        @list($serverName,$serverFunc) = explode('/',$uri);
        $serverConfigs =  include  APPPATH.'config/' . ENVIRONMENT . '/Rpc_config.php';
        $serverConfig = $serverConfigs[$serverName]['http'];
        $client = new \BnnpRpc\HttpRPC($serverName,$serverConfig);
        $result =  $client->$serverFunc($requestData);
        
        if(!isset($result['result'])||$result['result']!==0){
            return false;
        }
        return $result;
    }

    private function _getDefaultPrimary(){
        if(!$this->default_primary_name){
            if(is_array($this->primary_name)){
                $this->default_primary_name = $this->primary_name[current($req)];
            }else{
                $this->default_primary_name = $this->primary_name;
            }
        }
        return $this->default_primary_name;
    }
    protected final function _setError($code,$message){
        $this->_has_error = true;
        $this->error = ['code'=>$code,'message'=>$message];
    }
    public final function getError(){
        return [$this->error['code'],$this->error['message']];
    }
    public final function hasError(){
        //是否有错误
        return $this->_has_error;
    }
}