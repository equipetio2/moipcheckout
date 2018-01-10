<?php
class Order{
	private $httpConn;
	private $url = "orders";

	private $moipId;
	private $ownId;
	private $amount;
	private $items = Array();
	private $customer;
	
	public function __construct() {
		
	}

	public function __set($key, $value){
		$this->$key = $value;
	}

	public function __get($key){
		return $this->$key;
	}

	public function setCustomer($customerId){
		$this->customer = Array(
			"id" => $customerId
		);
	}

	private function dataArray(){
		$dataArray = Array(
			"ownId" => $this->ownId,
			"amount" => $this->amount,
			"items" => $this->items,
			"customer" => $this->customer
		);

		return $dataArray;
	}

	public function Create($httpConn) {
		$retorno = $httpConn->httpPost($this->url, $this->dataArray());

		$moipOrder = json_decode($retorno);

		if(!isset($moipOrder->errors)){
			$this->moipId = $moipOrder->id;
		}
		
		return $retorno;
	}

	public function Consult($httpConn) {
		$params = Array(
			"id" => $this->moipId
		);
		$retorno = $httpConn->httpGet($this->url, $params);

		return $retorno;
	}

}

?>