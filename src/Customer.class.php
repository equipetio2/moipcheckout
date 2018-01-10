<?php
class Customer{
	private $httpConn;
	private $url = "customers";

	private $moipId;
	private $ownId;
	private $fullname;
	private $email;

	private $taxDocument = Array(
		"type" => null,
		"number" => null
	);
	
	private $phone =  Array(
		"countryCode" => null,
		"areaCode" => null,
		"number" => null
	);

	private $shippingAddress =  Array(
		"city" => null,
		"complement" => null,
		"district" => null,
		"street" => null,
		"streetNumber" => null,
		"zipCode" => null,
		"state" => null,
		"country" => null
	);

	public function __set($key, $value){
		$this->$key = $value;
	}

	public function __get($key){
		return $this->$key;
	}

	private function DataArray(){
		$dataArray = Array(
			"ownId" => $this->ownId,
			"fullname" => $this->fullname,
			"email" => $this->email,
			"url" => $this->url,
			"taxDocument" => $this->taxDocument,
			"phone" => $this->phone,
			"shippingAddress" => $this->shippingAddress
		);

		return $dataArray;
	}

	public function Create($httpConn) {
		$params = $this->DataArray();

		$retorno = $httpConn->httpPost($this->url, $params);

		$moipCustomer = json_decode($retorno);

		if(!isset($moipCustomer->errors) && !isset($moipCustomer->error)){
			$this->moipId = $moipCustomer->id;
		}
		
		return $retorno;
	}

	public function Consult($httpConn) {
		$consultUrl = $this->url . "/" . $this->moipId;
		$retorno = $httpConn->httpGet($consultUrl, null);

		return $retorno;
	}

}

?>