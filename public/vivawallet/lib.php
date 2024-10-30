<?php
class OrderRequest {}
class PaymentRequest {}
class NativeCheckout {
	private $merchantId;
	private $apiKey; 
	private $baseApiUrl;
        private $paymentsUrl = "/api/transactions";
	private $nativeCheckoutSourceCode;
    private $paymentsCreateOrderUrl = "/api/orders";
	private $resultObj = "";
        public function Set_MerchantID($Value){
            $this->merchantId = $Value;
        }
        public function Set_ApiKey($Value){
            $this->apiKey = $Value;
        }
        public function Set_baseApiUrl($Value){
            $this->baseApiUrl = $Value;
        }
        public function Set_nativeCheckoutSourceCode($Value){
            $this->nativeCheckoutSourceCode = $Value;
        }
	public function MakePayment($amount,$cardToken,$installments){
		$orderCode=$this->CreateOrder($amount,$installments);
		$obj=new PaymentRequest();
		$obj->Amount=$amount;
		$obj->OrderCode=$orderCode;
		$obj->SourceCode=$this->nativeCheckoutSourceCode;
		$obj->CreditCard["Token"]=$cardToken;
		$obj->Installments=$installments;
		$resultObj = $this->ExecuteCall($this->baseApiUrl.$this->paymentsUrl,$obj);
		if ($resultObj->ErrorCode==0){	//success when ErrorCode = 0
			return $resultObj->TransactionId;
		}
		else{
			echo 'The following error occured: ' . $resultObj->ErrorText;
			return '0';
		}	
	}
	private function CreateOrder($amount,$installments){
		$obj=new OrderRequest();
		$obj->Amount=$amount;
		$obj->SourceCode=$this->nativeCheckoutSourceCode;
		$obj->MaxInstallments=$installments;
		$resultObj = $this->ExecuteCall($this->baseApiUrl.$this->paymentsCreateOrderUrl,$obj);
		if ($resultObj->ErrorCode==0){	//success when ErrorCode = 0
			return $resultObj->OrderCode;
		}
		else{
			echo 'The following error occured: ' . $resultObj->ErrorText;
			return 0;
		}	
	}	
	private function ExecuteCall($postUrl,$postobject){
		$postargs=json_encode($postobject);
		$session = curl_init($postUrl);
		curl_setopt($session, CURLOPT_POST, true);
		curl_setopt($session, CURLOPT_POSTFIELDS, $postargs);
		curl_setopt($session, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',                                                                                
			'Content-Length: ' . strlen($postargs))                                                                       
		);   
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_USERPWD, $this->merchantId.':'.$this->apiKey);
		curl_setopt($session, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
		curl_setopt($session, CURLOPT_HEADER, true);
		$response = curl_exec($session);
		$header_len = curl_getinfo($session, CURLINFO_HEADER_SIZE);
		$resHeader = substr($response, 0, $header_len);
		$resBody =  substr($response, $header_len);
		try {
			if(is_object(json_decode($resBody))){
				$resultObj=json_decode($resBody);
			}else{
				preg_match('#^HTTP/1.(?:0|1) [\d]{3} (.*)$#m', $resHeader, $match);
				throw new Exception(trim($match[1]));
			}
		} catch( Exception $e ) {
			echo $e->getMessage();
		}
		curl_close($session);
		return $resultObj;
	}
}
?>
