<?php
class MoipData{

	private $sandbox;
	private $auth;
	
	private $sandboxData = Array(
		
		'credentials' => array(
			"token" => "MOIP TOKEN",
			"key" => "MOIP TOKEN",
			"publicKey" => "MOIP PUBLIC KEY"
		),
		
		'url' => "https://sandbox.moip.com.br/v2"
	);	

	private $productionData = Array(
		'credentials' => array(
			"token" => "",
			"key" => "",
			"publicKey" => ""
		),
		
		'url' => "https://api.moip.com.br/v2"
	);

	public function __construct($sandbox = true) {
		if (!function_exists('curl_init')) {
            throw new Exception('CURL library is required.');
        }

		$this->sandbox = $sandbox;
		$credentials = $this->getCredentials();
		$this->auth = base64_encode($credentials['token'].":".$credentials['key']);
	}

	function __get($name) {
	    return array_key_exists($name, $this->$name) ? $this->values[$name] : null;
	}

	private function getEnviromentData($key) {
		if ($this->sandbox) {
			return $this->sandboxData[$key];
		} else {
			return $this->productionData[$key];
		}
	}

	public function getCredentials() {
		return $this->getEnviromentData('credentials');
	}

	public function setProductionCredentials($key, $value) {
		$this->productionData["credentials"][$key] = $value;
	}

	public function isSandbox() {
		return (bool)$this->sandbox;
	}

	public function httpPost($url, array $data = null, $timeout = null, $charset = null) {
		return $this->curlConnection('POST', $url, $data, $timeout, $charset);
    }

    public function httpGet($url, array $data = null, $timeout = null, $charset = null) {
		if($data != null){
			$params = "";
			foreach($data as $key => $value){
				$params .=  $key . "=" . $value;
			}
        	$url = $url . "?" . $params;
		}else{
			$url = $url;
		}
		return $this->curlConnection('GET', $url, null, $timeout, $charset);
    }

    public function httpDelete($url, array $data = null, $timeout = null, $charset = null) {
		if($data != null){
			$params = "";
			foreach($data as $key => $value){
				$params .=  $key . "=" . $value;
			}
        	$url = $url . "?" . $params;
		}else{
			$url = $url;
		}
		return $this->curlConnection('DELETE', $url, null, $timeout, $charset);
    }
	
	private function curlConnection($method, $url, $data, $charset = 'ISO-8859-1') {
		$credentials = $this->getCredentials();
		
		$this->auth = base64_encode($credentials['token'].":".$credentials['key']);
        
        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic '. $this->auth
        );

        $ch = curl_init($this->getEnviromentData("url") . "/" . $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if(strtoupper($method) == "POST"){
        	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        }elseif(strtoupper($method) == "GET"){
        	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        }if(strtoupper($method) == "DELETE"){
        	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;        
    }
}

?>