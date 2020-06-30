<?php
namespace BnnpRpc;

use BnnpRpc\Exception\RpcException;
use BNNPServer\Common\COMException;
use Exception;
use Thrift\Client\ThriftClient;

class ThriftRPC {

	private $serv;

	private $host;

	private $port;

	private $send_timeout;

	private $recv_timeout;

	private $signer;

	public function __construct($serv, $config) {
		$this->serv = $this->_transName($serv);
		foreach ($config as $key => $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}
	}

	public function __call($name, $args) {
		$name = $this->_transName($name);
		$client = $this->_rpcClient();
		$clientArgs = $this->_refClientArgs($client->getClassName(), $name, $args);
		log_message('debug', 'thrift requests: '.json_encode($clientArgs));

		try {
			$rsp = call_user_func_array([$client, $name], $clientArgs);
			$data = $this->_parseResponse($rsp);
		} catch (COMException $e) {
			$data = ['result' => $e->code, 'res_info' => $e->message];
		}

		log_message('debug', 'thrift response: '.json_encode($data));

		return $data;
	}

	private function _rpcClient() {
		if (empty($this->host) || empty($this->port)) {
			throw new RpcException('Thrift server host or port not exists');
		}
		return new ThriftClient($this->serv, $this->host, $this->port, $this->send_timeout, $this->recv_timeout);
	}

	private function _getRequestHeader($className, $func) {
		global $global_request;

		$header = new $className();
		$header->request_name = $func;
		$header->uin = $global_request['uin'] ?? '';
		$header->uid = $global_request['uid'] ?? 0;
		$header->token = $global_request['token'] ?? '';
		$header->refresh_token = $global_request['refresh_token'] ?? '';
		$header->appid = $global_request['appid'] ?? '';
		$header->app_key = $global_request['app_key'] ?? '';
		$header->msgno = $global_request['msgno'] ?? NULL;
		list($header->sign, $header->sign_type) = $this->_sign();

		return $header;
	}

	private function _getRequestData($className, $args) {
		$tspec = $className::$_TSPEC ?? [];
		foreach($tspec as $key=>$field){
			if ($field['type'] === 12 && !empty($args[$field['var']]) && is_array($args[$field['var']])) {
				$args[$field['var']] = $this->_getRequestData($field['class'],$args[$field['var']]);
			}elseif ($field['type'] = 15 && !empty($field['etype']) && $field['etype'] === 12 && !empty($args[$field['var']]) && is_array($args[$field['var']])) {
				foreach($args[$field['var']] as $item){
					$newItems[] = $this->_getRequestData($field['elem']['class'],$item);
				}
				$args[$field['var']] = $newItems;
			}
		}
		return new $className($args);
	}

	private function _refClientArgs($name, $func, $args) {
		//Target our class
		$reflector = new \ReflectionClass($name);

		$clientArgs = [];
		//Get the parameters of a method
		$parameters = $reflector->getMethod($func)->getParameters();

		//Loop through each parameter and get the type
		foreach ($parameters as $param) {
			//Before you call getClass() that class must be defined!
			$className = $param->getClass()->name;

			if ($className == 'BNNPServer\\Common\\Header') {
				$clientArgs[] = $this->_getRequestHeader($className, $func);
			} else {
				$clientArgs[] = $this->_getRequestData($className, $args[0] ?? []);
			}
		}

		return $clientArgs;
	}

	private function _transName($name, $glue = '_') {
		$name = implode('', array_map('ucfirst', explode('_', $name)));
		return $name;
	}

	private function _parseResponse($rsp) {
		$rsp = $this->_O2A($rsp);
		return array_merge(['result' => 0, 'res_info' => 'ok'], $rsp);
	}

	private function _O2A($obj) {
		$obj = (array) $obj;
		foreach ($obj as $key => $value) {
			if (is_array($value) || is_object($value)) {
				$obj[$key] = $this->_O2A($value);
			}
		}
		return $obj;
	}

	private function _sign() {
		return ['', ''];
	}

	public function setSigner($signer) {
		$this->signer = $signer;
	}

}