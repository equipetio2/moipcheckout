<?php
require_once "MoipData.php";

class Webhook{
	private $httpConn;
	private $url = "webhooks";

	private $resourceId;
	private $event;

	public function __construct(){
		$this->media = "WEBHOOK";
	}
	
	public function __set($key, $value){
		$this->$key = $value;
	}

	public function __get($key){
		return $this->$key;
	}

	public function ToArray(){
		$arr = Array();
		$arr["resourceId"] = $this->resourceId;
		$arr["event"] = $this->event;

		return $arr;
	}

	public function Resend($httpConn) {
		return $httpConn->httpPost($this->url, $this->ToArray());
	}

	public function Consult($httpConn){
		$params = Array(
			"resourceId" => $this->resourceId
		);
		$retorno = $httpConn->httpGet($this->url, $params);

		return $retorno;
	}

}

?>