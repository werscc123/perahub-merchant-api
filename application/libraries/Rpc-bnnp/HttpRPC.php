<?php
namespace BnnpRpc;

use BnnpRpc\Http\HttpHelper;
use BnnpRpc\Exception\RpcException;

class HttpRPC {

	private $serv;

	private $host;

	private $port = 80;

	private $scheme = 'http';

	private $domain = '';

	private $connect_timeout;

	private $read_timeout;

	private $signer;

	private $header;

	public function __construct($serv, $config) {
		$this->serv = $serv;
		foreach ($config as $key => $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}
		if (empty($this->host)) {
			throw new RpcException("Http host not exists");
		}
		if (!empty($this->domain)) {
			$this->header['Host'] = $this->domain;
		}
	}

	public function __call($name, $args) {
		$url = $this->prep_url($name);
		$requestData = $this->getRequestPackage($args);
		if (!empty($connect_timeout)) {
			HttpHelper::setConnectTime($connect_timeout);
		}
		if (!empty($read_timeout)) {
			HttpHelper::setReadTimeout($read_timeout);
		}
		log_message('debug',$url);
		log_message('debug','http request: '. json_encode($requestData));
		$httpResponse = HttpHelper::curl($url,'POST',$requestData,$this->header);
		log_message('debug','http response: '.$httpResponse->getBody());
		return $this->parseResponsePackage($httpResponse->getBody());
	}

	private function prep_url($uri) {

		$url = $this->scheme . '://' . trim($this->host, '/');

		if ($this->port != '80' && $this->port != '443') {
			$url .= ':' . $this->port;
		}

		$url .= '/' . $this->serv . '/' . $uri;

		return $url;
	}

	public function getRequestPackage($requestData) {
		$requestData = $requestData[0]??[];
		global $global_request;
		$requestData['MSG_NO'] = $global_request['msgno'] ?? NULL;
		if ($this->signer != NULL) {
			if (isset($requestData['sign_pm'])) {
				unset($requestData['sign_pm']);
			}
			// log_message('debug', 'signer');
			$this->signer->setVersion();
			$requestData['sign_cn'] = $this->signer->getVersion();
			$requestData = array_filter($requestData, 'strlen');
			$requestData = array_map('strval', $requestData);
			ksort($requestData);
			//请求加密源串用&拼接
			log_message('debug', json_encode($requestData));
			$source = md5(json_encode($requestData));
			$requestData['sign_pm'] = $this->signer->publicEncryptSign($source);

		}
		return $requestData;
	}

	public function parseResponsePackage(string $responseStr) {
		$responseData = json_decode($responseStr, TRUE);
		if ($this->signer != NULL) {
			$sign = $responseData['sign_pm'] ?? NULL;
			$sign_cn = $responseData['sign_cn'] ?? NULL;
			if (empty($sign) || empty($sign_cn)) {
				//throw new RpcException('rpc response sign or sign_cn empty!');
			}
			$this->signer->setVersion($sign_cn);
			$decrypt_sign = $this->signer->publicDecryptSign($sign);
			unset($responseData['sign_pm']);
			ksort($responseData);
			//回包加密用户json格式
			if (md5(json_encode($responseData)) != $decrypt_sign) {
				//throw new RpcException('rpc response sign error!');
			}
		}
		return $responseData;
	}

	public function setSigner($signer) {
		$this->signer = $signer;
	}

}