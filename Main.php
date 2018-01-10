<?php
	
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

    require_once "Config.php";
    require_once "src/MoipData.php";
    require_once "src/Customer.class.php";
    require_once "src/Order.class.php";
    require_once "src/Payment.class.php";
    require_once "src/Preference.class.php";
    require_once "src/Webhook.class.php";
	
	class Main{
        private $Customer;
        private $Order;
        private $Payment;
        private $HttpConn;

        private $ownIdHash = ""; //Será adicionada antes dos "ownID", deve ser única para cada aplicação, para não haver conflitos no moip

        public function __construct(){
            $this->HttpConn = new MoipData();
        }

        public function setProductionData($productionToken, $productionKey){
            $this->HttpConn->setProductionCredentials("token",  $productionToken);
            $this->HttpConn->setProductionCredentials("key",    $productionKey);
        }

        public function createCustomer($customerData){
            $this->Customer = new Customer();
            
            if(isset($customerData["moipId"])){
                $this->Customer->__set("moipId", $customerData["moipId"]);
                return $this->Customer->Consult($this->HttpConn);
            }else{
                $this->Customer->__set("ownId", 			$this->ownIdHash . "-" . $customerData["ownId"]);
                $this->Customer->__set("fullname", 			$customerData["fullname"]);
                $this->Customer->__set("email", 			$customerData["email"]);
                $this->Customer->__set("phone", 			$customerData["phone"]);
                $this->Customer->__set("taxDocument",		$customerData["taxDocument"]);
                $this->Customer->__set("shippingAddress",	$customerData["shippingAddress"]);
                return $this->Customer->Create($this->HttpConn);
            }
        }

        public function createOrder($orderData){
            $this->Order = new Order();
            
            if(isset($orderData["moipId"])){
                $this->Order->__set("moipId", $orderData["moipId"]);
                return $this->Order->Consult($this->HttpConn);
            }else{
                $this->Order->__set("ownId",     $this->ownIdHash . "-" . $orderData["ownId"]);
                $this->Order->__set("amount", $orderData["amount"]);
                $this->Order->__set("items",     $orderData["items"]);
                $this->Order->setCustomer($this->Customer->moipId);
                return $this->Order->Create($this->HttpConn);
            }
        }

        public function createPayment($paymentData){
            $this->Payment = new Payment();
            $this->Payment->__set("orderId", $this->Order->moipId);
            $this->Payment->__set("installmentCount", $paymentData["installmentCount"]);
            $this->Payment->__set("fundingInstrument", $paymentData["fundingInstrument"]);
            return $this->Payment->Create($this->HttpConn);
        }

         public function ConsultPayment($paymentData){
            $this->Payment = new Payment();
            $this->Payment->__set("paymentId", $paymentData["moipId"]);
            return $this->Payment->Consult($this->HttpConn);
        }

        public function getPublicKey(){
            return SANDBOXPUBLICKEY;            
        }

        public function paymentStatusName($paymentMethod){
            switch (strtoupper($paymentMethod)) {
                case 'CREATED':
                    return "Criado";
                    break;

                case 'WAITING':
                    return "Aguardando confirmação";
                    break;

                case 'IN_ANALYSIS':
                    return "Em análise";
                    break;

               case 'PRE_AUTHORIZED':
                    return "Valor de pagamento reservado";
                    break;

                case 'AUTHORIZED':
                    return "Pagamento Confirmado";
                    break;

                case 'CANCELLED':
                    return "Cancelado";
                    break;

                case 'REFUNDED':
                    return "Reembolsado";
                    break;

                case 'REVERSED':
                    return "Revertido";
                    break;

                case 'SETTLED':
                    return "Disponível";
                    break;

                default:
                    return "Status não identificado";
                    break;
            }
        }

        public function paymentMethodName($paymentMethod){
            switch ($paymentMethod) {
                case 'BOLETO':
                    return "Boleto";
                    break;

                case 'ONLINE_BANK_DEBIT':
                    return "Débito Online";
                    break;
                
                case 'CREDIT_CARD':
                    return "Cartão de Crédito";
                    break;

                default:
                    return "Método de pagamento não identificado";
                    break;
            }
        }

        public function getOnlineDebitBanks(){
            $arrayBank = Array();
            
            //Add new bank
            $newBank = Array(
                "name" => "BANCO ITAU S.A.",
                "imgName" => "debito_itau.png",
                "displayName" => "Itaú",
                "code" => 341
            );
            array_push($arrayBank, $newBank);
            //End add new bank

            return $arrayBank;
        }

        public function getCardBrands(){
            $arrayBrands = Array();
            
           //Add new brands
            $brand = Array(
                "name" => "VISA",
                "imgName" => "cc_visa.png",
                "displayName" => "VISA"
            );
            array_push($arrayBrands, $brand);
           
            $brand = Array(
                "name" => "MASTERCARD",
                "imgName" => "cc_mastercard.png",
                "displayName" => "Mastercard"
            );
            array_push($arrayBrands, $brand);

            $brand = Array(
                "name" => "AMEX",
                "imgName" => "cc_amex.png",
                "displayName" => "Amex"
            );
            array_push($arrayBrands, $brand);

            $brand = Array(
                "name" => "DINERS",
                "imgName" => "cc_diners.png",
                "displayName" => "Diners"
            );
            array_push($arrayBrands, $brand);
            
            $brand = Array(
                "name" => "ELO",
                "imgName" => "cc_elo.png",
                "displayName" => "Elo"
            );
            array_push($arrayBrands, $brand);
            
            $brand = Array(
                "name" => "HIPER",
                "imgName" => "cc_hiper.png",
                "displayName" => "Hiper"
            );
            array_push($arrayBrands, $brand);

            $brand = Array(
                "name" => "HIPERCARD",
                "imgName" => "cc_hipercard.png",
                "displayName" => "Hipercard"
            );
            array_push($arrayBrands, $brand);
             //End add new brands
            
            return $arrayBrands;
        }        

        public function createPreference($preferenceData){
            $this->Preference = new Preference();
            $this->Preference->__set("target", $preferenceData["target"]);
            $this->Preference->__set("events", $preferenceData["events"]);
            return $this->Preference->Create($this->HttpConn);
        }     

        public function consultPreference(){
            $this->Preference = new Preference();
            return $this->Preference->Consult($this->HttpConn);
        }

         public function deletePreference($preferenceData){
            $this->Preference = new Preference();
            $this->Preference->__set("moipId", $preferenceData["moipId"]);
            return $this->Preference->Delete($this->HttpConn);
        }

        public function consultWebhook($resourceData){
            $this->Webhook = new Webhook();
            $this->Webhook->__set("resourceId", $resourceData["resourceId"]);
            return $this->Webhook->Consult($this->HttpConn);
        }

        public function resendWebhook($resourceData){
            $this->Webhook = new Webhook();
            $this->Webhook->__set("resourceId", $resourceData["resourceId"]);
            $this->Webhook->__set("event", $resourceData["event"]);
            return $this->Webhook->Resend($this->HttpConn);
        }    
    }
	
?>