<?php
namespace Thrift\Client;

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TSocket;

class ThriftClient {

	protected $socket_;

	protected $transport_;

	protected $protocol_;

	protected $client_;

	public function __construct($serverName, $serverHost, $serverPort, $sendTimeout = NULL, $recvTimeout = NULL) {
		$this->socket_ = new TSocket($serverHost, $serverPort);

		if (!is_null($sendTimeout)) {
			$this->socket_->setSendTimeout($sendTimeout);
		}
		if (!is_null($recvTimeout)) {
			$this->socket_->setRecvTimeout($recvTimeout);
		}

		$this->transport_ = new TFramedTransport($this->socket_, 1024, 1024);
		$this->protocol_ = new TBinaryProtocol($this->transport_);
		$this->client_ = $this->clientFactory($serverName);
	}

	protected function clientFactory($serverName) {
		$this->clientClass_ = $clientClass = "\\BNNPServer\\{$serverName}\\{$serverName}Client";
		return new $clientClass($this->protocol_);
	}

	public function getClassName() {
		return $this->clientClass_;
	}

	public function __call($name, $args) {
		$this->transport_->open();
		$resp = call_user_func_array([$this->client_, $name], $args);
		$this->transport_->close();
		return $resp;
	}

}