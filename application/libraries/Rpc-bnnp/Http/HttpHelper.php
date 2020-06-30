<?php

namespace BnnpRpc\Http;

use BnnpRpc\Exception\HttpException;

class HttpHelper {

	public static $connectTimeout = 30000; //30 second
	public static $readTimeout = 80000; //80 second

	public static function curl($url, $httpMethod = "GET", $postFields = NULL, $headers = NULL,$recv = TRUE) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
		// if (ENABLE_HTTP_PROXY) {
		// 	curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
		// 	curl_setopt($ch, CURLOPT_PROXY, HTTP_PROXY_IP);
		// 	curl_setopt($ch, CURLOPT_PROXYPORT, HTTP_PROXY_PORT);
		// 	curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		// }
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postFields) ? self::getPostHttpBody($postFields) : $postFields);

		if (self::$readTimeout) {
			// curl_setopt($ch, CURLOPT_TIMEOUT, self::$readTimeout);
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, self::$readTimeout);
		}
		if (self::$connectTimeout) {
			// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connectTimeout);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, self::$connectTimeout);
		}
		//https request
		if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}
		if (is_array($headers) && 0 < count($headers)) {
			$httpHeaders = self::getHttpHearders($headers);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
		}
		$httpResponse = new HttpResponse();
		if($recv == TRUE){
			$httpResponse->setBody(curl_exec($ch));
			$httpResponse->setStatus(curl_getinfo($ch, CURLINFO_HTTP_CODE));
			if (curl_errno($ch)) {
				throw new HttpException("Server unreachable: Errno: " . curl_errno($ch) . " " . curl_error($ch), curl_errno($ch));
			}
		}else{
			curl_exec($ch);
		}
		
		curl_close($ch);
		return $httpResponse;
	}
	public static function getPostHttpBody($postFildes) {
		$content = "";
		foreach ($postFildes as $apiParamKey => $apiParamValue) {
			$content .= "$apiParamKey=" . urlencode($apiParamValue) . "&";
		}
		return substr($content, 0, -1);
	}
	public static function getHttpHearders($headers) {
		$httpHeader = array();
		foreach ($headers as $key => $value) {
			array_push($httpHeader, $key . ":" . $value);
		}
		return $httpHeader;
	}
	public static function setConnectTime($connectTimeout){
		self::$connectTimeout = $connectTimeout;
	}
	public static function setReadTimeout($readTimeout){
		self::$readTimeout = $readTimeout;
	}
}
