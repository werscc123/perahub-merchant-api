<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OP_Controller extends CI_Controller{
    protected $is_auth = true; //是否鉴权 true 确定，false 不鉴权
    protected $resource_model = '';//资源模型
    private $response_data = [];//返回的数据包
    protected $rules_config = [ //参数校验规则

    ];
    protected $req = [ //预先配置接收
        'path'=>[],
        'query'=>[],
        'form_data'=>[],
        'body'=>[],
        'header'=>[],
    ];//参数数据包 path query form_data json.body header
    public function __construct()
    {
        parent::__construct();
        //实例资源模型
        if((!$this->resource_model)||!$this->load->model($this->resource_model,'',true)){
            throw new Exception('model not set!',1000);//抛出程序异常 程序异常表示需要修改源码
        }



        $this->_getParams();//获取参数
        if(!$this->checkParams()){//校验参数
            $this->display();
        }
    }


    public function _remap($name)
    {
        if(!strstr($name,'get')
            &&!strstr($name,'create')
            &&!strstr($name,'update')
            &&!strstr($name,'modify')
            &&!strstr($name,'delete')){
            show_404();
        }
        if(method_exists($this,$name)){
            $this->$name();
        }else{
            $this->oper_resource($name);
        }

    }
    public function __call($name, $arguments)
    {
        $this->oper_resource($name);
    }


    protected function checkParams(){//参数检验
        $auth = true;
        $arg = func_get_args();
        if(isset($arg[0])&&$arg[0]===false){
            $auth = false;
        }
        if($this->is_auth&&$auth){//需要鉴权
            if(!isset($this->req['header']['authorization'])||!$this->req['header']['authorization']||!$this->checkAuth()){
                $this->response403();
            }
        }

        if($this->rules_config){//校验参数
            $this->load->library('form_validation');
            $rule_data = [];
            foreach ($this->req as $req){
                $rule_data = array_merge($rule_data,$req);
            }
            $method = $this->router->fetch_method();
            if(isset($this->rules_config[$method])){
                $this->form_validation->set_data($rule_data);
                $this->form_validation->set_rules($this->rules_config[$method]);
                $this->form_validation->set_error_delimiters('','');
                if($this->form_validation->run()===false){
                    $this->setError(ERR_PARAMS,trim($this->form_validation->error_string(),"\n"),406);
                    return false;
                }
            }
        }
        return true;
    }
    private function _getParams(){ // 整理获取资源列表

        if(!empty($this->req['path'])){//接收path参数
            $path_arry = $this->uri->segment_array();
            $path_index = 0;
            foreach ($this->req['path'] as $path_key=>$path_value){
                if(isset($path_arry[2+$path_index])){
                    //设定资源主键
                    $this->req['path'][$path_key] = $path_arry[2+$path_index];
                    $path_index+=2;
                }
            }
        }
        if(!empty($this->req['query'])){//接收query 参数
            foreach ($this->req['query'] as $query_key=>$query_value){
                $this->req['query'][$query_key] = $this->input->get($query_key,true);
            }
        }

        if(!empty($this->req['form_data'])) {//接收form_data
            if(in_array($this->input->server('REQUEST_METHOD'),['PUT','PATCH','DELETE'])){
                if(!$this->req['form_data'] = $this->input->input_stream()){
                    $input_stream = $this->input->raw_input_stream;
                    preg_match_all('/name="(.*)"\s\s\s\s(\S*)/',$input_stream,$data);
                    if(isset($data[1])){
                        foreach ($data[1] as $k=>$v){
                            if(isset($data[2][$k])){
                                if(isset($this->req['form_data'][$v])){
                                    $this->req['form_data'][$v] = $data[2][$k];
                                }
                            }
                        }
                    }
                }

            }else{
                foreach ($this->req['form_data'] as $form_data_key => $form_data_value) {
                    $this->req['form_data'][$form_data_key] = $this->input->post($form_data_key, true);
                }
            }

        }

        if(!empty($this->req['body'])){//接收body/json参数
            $body = $this->input->raw_input_stream;
            $body_arr = @json_decode($body,true);
            if($body_arr){
                foreach ($this->req['body'] as $body_key=>$body_value){
                    $this->req['body'][$body_key] = $body_arr[$body_key];
                }
            }
        }
        if(!empty($this->req['header'])){//接收header 参数
            foreach ($this->req['header'] as $header_key =>$header_value){

                if($header_key==='authorization'||$header_key==='Authorization'){
                    if(strstr($this->input->get_request_header($header_key),'Bearer ')){
                        $header_value = substr($this->input->get_request_header($header_key),strlen('Bearer '));
                    }else{
                        $header_value = $this->input->get_request_header($header_key);
                    }
                }
                $this->req['header'][$header_key] = $header_value;
            }
        }
    }
    protected function checkAuth(){
        $this->load->model('authorization');
        @list($a_header,$a_payload,$a_sign) = explode('.',$this->req['header']['authorization']);
        //spid
        if(!$a_payload){
            return false;
        }
        if(!$payload = base64_decode($a_payload,true)){
            $this->setError(ERR_PARAMS,'jwt error');
            return false;
        }

        $payload_obj = @json_decode($payload);
        $spid = $payload_obj->sub;
        $this->load->model('user');
        $user = $this->user->getBySpid($spid);
        $auth_key = $user->ele_jwt;
        if(!$authorization = $this->authorization->checkAuth($this->req['header']['authorization'],$auth_key)){
            return false;
        }
        header('authorization: '.$authorization);
        return true;
    }

    protected function checkUser($uid){
        @list($a_header,$a_payload,$a_sign) = explode('.',$this->req['header']['authorization']);
        //spid
        if(!$a_payload){
            return false;
        }
        if(!$payload = base64_decode($a_payload,true)){
            $this->setError(ERR_PARAMS,'jwt error');
            return false;
        }


        $payload_obj = @json_decode($payload);
        return $payload_obj->uid===$uid;
    }

    public function oper_resource($oper){
        $result = $this->{$this->resource_model}->$oper($this->req);
        if(!$result){
            if($this->{$this->resource_model}->hasError()){
                list($code,$message) = $this->{$this->resource_model}->getError();
                $this->setError($code,$message,500);
            }else{
                if($result===false){
                    $this->setError(1000,'system error',500);
                }
                $this->response_data = [];
            }
        }else{
            if(is_array($result)){
                $this->response_data = $result;
            }else{
                if($result===true){
                    $this->response_data = [];
                }else{
                    $this->response_data = (array)$result;
                }
            }
        }
        //输出前数据格式化
        $this->format($this->response_data);

        $this->display();
    }
    protected function format(&$data){
        function item_format(&$value){
            foreach($value as $o_key=>$o){
                if(!$o&&$o!==0&&$o!=='0'&&!is_array($o)){
                    $value[$o_key] = '';
                }elseif(strstr($o_key,'time')){
                    $value[$o_key] = date('Y-m-d H:i:s',$o);
                }
                
            }
        }
        $data = json_decode(json_encode($data),true);
        reset($data);
        if(!is_array(current($data))){
            item_format($data);
        }else{
            foreach ($data as $value){
                item_format($value);
            }
        }
    }
    protected function setError($code=EXIT_SUCCESS,$message,$statusCode=406){
        $this->response_data['code'] = $code;
        $this->response_data['message'] = $message;
        set_status_header($statusCode);
    }
    protected function response403(){
        $this->setError(ERR_403,'No authentication',403);
        $this->display();
    }
    protected function response404(){
        $this->setError(ERR_404,'resource is not exists!',404);
        $this->display();
    }

    protected function display($data_format='json',$data=null){
        if($data){
            $this->response_data = $data;
        }
        echo $this->load->view($data_format,['data'=>$this->response_data],true);
        die(0);
    }
    protected function displayError($model,$error_message=''){
        if(method_exists($model,'hasError')){
            if($model->hasError()){
                list($code,$message) = $model->getError();
                $this->setError($code,$message,500);
                $this->display();
            }
            set_status_header(500,$error_message);
            $this->display('json',['code'=>1000,'message'=>$error_message]);
        }else{
            throw new Exception(1000,$error_message);
        }
    }
}