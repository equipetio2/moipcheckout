<?php
class Payment{
	private $httpConn;
	private $url;

	private $paymentId; //PK
	private $orderId; //FK

	private $installmentCount;
	private $fundingInstrument;
	
	public function __set($key, $value){
		$this->$key = $value;
	}

	public function __get($key){
		return $this->$key;
	}

	public function ToArray(){
		$arr = Array();
		$arr["installmentCount"] = $this->installmentCount;
		$arr["fundingInstrument"] = $this->fundingInstrument;

		return $arr;
	}

	public function Create($httpConn) {
		$this->url = "orders/".$this->orderId."/payments";
		return $httpConn->httpPost($this->url, $this->ToArray());
	}

	public function Consult($httpConn) {
		$this->url = "payments/".$this->paymentId;
		$retorno = $httpConn->httpGet($this->url, null);

		return $retorno;
	}

}

?>