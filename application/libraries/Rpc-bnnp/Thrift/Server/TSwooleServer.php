<?php
namespace Thrift\Server;

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Server\TServer;
use Thrift\Transport\TSwooleTransport;

class TSwooleServer extends TServer {

	protected $processor = NULL;
	protected $serverName = NULL;
	protected $serverHost = NULL;
	protected $serverPort = NULL;

	function onManagerStart($serv) {
		cli_set_process_title("php thrift {$this->serverName}.php: manager");
		$this->notice("php thrift {$this->serverName}.php: manager start.");
	}

	function onStart($serv) {
		cli_set_process_title("php thrift {$this->serverName}.php: master");
		$this->notice("php thrift {$this->serverName}.php: master start.");
	}

	function onWorkerStart($serv) {
		cli_set_process_title("php thrift {$this->serverName}.php: worker");
		$this->notice("php thrift {$this->serverName}.php: worker start.");
	}

	function notice($log) {
		echo '[' . date('Y-m-d H:i:s') . '] ' . $log . PHP_EOL;
	}

	public function onReceive($serv, $fd, $from_id, $data) {
		//为了开发环境方便，修改业务接口代码无需重启服务
		//在这里重新实例化handler,再实例化processor
		//生产环境就直接使用外部传参的$this->processor_
		if (!defined('IS_PRODUCT_ENV') || IS_PRODUCT_ENV !== TRUE) {
			$processor_class = "\\BNNPServer\\{$this->serverName}\\{$this->serverName}Processor";
			$handler_class = "\\App\\Handler";
			$handler = new $handler_class();
			$handler->clientInfo = $serv->getClientInfo($fd);
			$this->processor = new $processor_class($handler);
		} else {
			$this->processor = $this->processor_;
		}

		$transport = new TSwooleTransport();
		$transport->setHandle($fd);
		$transport->buffer = $data;
		$transport->server = $serv;
		$protocol = new TBinaryProtocol($transport, true, true);

		try {
			$protocol->fname = $this->serverName;
			$this->processor->process($protocol, $protocol);
		} catch (\Exception $e) {
			$this->notice('CODE:' . $e->getCode() . ' MESSAGE:' . $e->getMessage() . "\n" . $e->getTraceAsString());
		}
	}

	function serve() {
		if (empty($this->serverName) || empty($this->serverHost) || empty($this->serverPort)) {
			$this->notice(sprintf('Have not set such server name: %s, host: %s, port: %s', $this->serverName, $this->serverHost, $this->serverPort));
			die();
		}
		$this->stop();
		$serv = new \swoole_server($this->serverHost, $this->serverPort);
		$serv->on('managerStart', [$this, 'onManagerStart']);
		$serv->on('start', [$this, 'onStart']);
		$serv->on('workerStart', [$this, 'onWorkerStart']);
		$serv->on('receive', [$this, 'onReceive']);
		$serv->set(array(
			'worker_num' => 1,
			'daemonize' => 1, //后台常驻
			'dispatch_mode' => 1, //1: 轮循, 3: 争抢
			'open_length_check' => true, //打开包长检测
			'package_max_length' => 8192000, //最大的请求包长度,8M
			'package_length_type' => 'N', //长度的类型，参见PHP的pack函数
			'package_length_offset' => 0, //第N个字节是包长度的值
			'package_body_offset' => 4, //从第几个字节计算长度
			'log_file' => '/data/log/thrift/swoole/' . strtolower($this->serverName) . '_error.log',
		));
		$serv->start();
	}

	public function stop() {
		$this->transport_->close();
	}

	public function __call($name, $args) {
		if (strtolower(substr($name, 0, 3)) == 'set') {
			$property = lcfirst(substr($name, 3));
			if (property_exists($this, $property)) {
				$this->$property = $args[0] ?? NULL;
				return TRUE;
			}
		}
		return FALSE;
	}

}
