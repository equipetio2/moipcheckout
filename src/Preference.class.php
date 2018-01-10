<?php
class Preference{
	private $httpConn;
	private $url = "preferences/notifications";

	private $moipId;
	private $events;
	private $target;
	private $media;

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
		$arr["events"] = $this->events;
		$arr["target"] = $this->target;
		$arr["media"]  = $this->media;

		return $arr;
	}

	public function Create($httpConn) {
		return $httpConn->httpPost($this->url, $this->ToArray());
	}

	public function Consult($httpConn){
		$retorno = $httpConn->httpGet($this->url, null);

		return $retorno;
	}

	public function Delete($httpConn){
		$url_delete = $this->url . "/" . $this->moipId;
		$retorno = $httpConn->httpDelete($url_delete, null);

		return $retorno;
	}

}

?>