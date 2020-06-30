<?php
namespace BnnpRpc\Auth;

class RsaSigner {

	public $pubExt = 'pub';

	public $priExt = 'pri';

	private $_certPath;

	public $verList = ['001', '002'];

	public $ver = NULL;

	public function __construct(array $config = []) {
		$this->initialize($config);
		$this->_certPath = dirname(__DIR__).'/Cert/';
	}

	/**
	 * Initialize Preferences
	 *
	 * @param	array	$config	Initialization parameters
	 * @return	AliPush
	 */
	public function initialize(array $config = []) {

		if ($config) {
			foreach ($config as $key => $val) {
				if (property_exists($this, $key)) {
					$this->$key = $val;
				}
			}
		}

		return $this;
	}

	public function getMethod() {
		return "RSA";
	}

	public function setVersion($ver = NULL) {
		if (isset($ver) && in_array($ver, $this->verList)) {
			return $this->ver = $ver;
		}
		return $this->ver = $this->verList[array_rand($this->verList, 1)];
	}

	public function getVersion() {
		return $this->ver;
	}

	public function publicEncryptSign($source, $ver = NULL) {
		// $this->setVersion($ver);
		$key = $this->_getPublicKey();
		log_message('debug',$key);
		return openssl_public_encrypt($source, $encrypted, $key) ? base64_encode($encrypted) : NULL;
	}

	public function publicDecryptSign($sign, $ver = NULL) {
		// $this->setVersion($ver);
		$key = $this->_getPublicKey();
		return openssl_public_decrypt(base64_decode($sign), $decrypted, $key) ? $decrypted : NULL;
	}

	public function privateEncryptSign($source, $ver = NULL) {
		// $this->setVersion($ver);
		$key = $this->_getPrivateKey();
		return openssl_private_decrypt($source, $encrypted, $key) ? base64_encode($encrypted) : NULL;
	}

	public function privateDecryptSign($sign, $ver = NULL) {
		// $this->setVersion($ver);
		$key = $this->_getPrivateKey();
		return openssl_private_decrypt(base64_decode($sign), $decrypted, $key) ? $decrypted : NULL;
	}

	private function _getPublicKey() {
		$filePath = rtrim($this->_certPath, '/') . '/' . $this->ver . '.' . $this->pubExt;
		if (!file_exists($filePath)) {
			throw new \Exception("rsa key file not exists.");
		}
		log_message('debug',openssl_pkey_get_public(file_get_contents($filePath)));
		return openssl_pkey_get_public(file_get_contents($filePath));
	}

	private function _getPrivateKey() {
		$filePath = rtrim($this->_certPath, '/') . '/' . $this->ver . '.' . $this->priExt;
		if (!file_exists($filePath)) {
			throw new \Exception("rsa key file not exists.");
		}
		return openssl_pkey_get_private(file_get_contents($filePath));
	}

}