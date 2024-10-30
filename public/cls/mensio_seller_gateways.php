<?php
class mensio_seller_gateways {
  private $Customer;
  private $CstmrAddress;
  private $Order;
  public function __construct() {
    $this->Customer = '';
    $this->CstmrAddress = '';
    $this->Order = '';
  }
	private function ClearValue($Value,$Type='AN',$SpCh='NONE') {
    switch($Type) {
      case 'TX':
        $Patern = '[^\p{L}]';
        break;
      case 'EN':
        $Patern = '[^A-Za-z0-9]';
        break;
      case 'NM':
        $Patern = '[^0-9]';
        break;
      default:
        $Patern = '[^\p{L}\p{N}]';
        break;
    }
    if ($SpCh != 'NONE') {
      $Patern = str_replace(']','\\'.$SpCh.']', $Patern);
    }
    $Value = mb_ereg_replace($Patern, '', $Value);
    return $Value;
	}
	private function ClearUUID($Value) {
		$RtrnVal = false;
		if (mb_strlen($Value) == 36) {
			$ClrVal = $this->ClearValue($Value,'EN','-');
			if (mb_strlen($ClrVal) == 36) {
				$ValArray = explode('-',$ClrVal);
				if ((is_array($ValArray)) && (count($ValArray) == 5)) {
					$RtrnVal = $ClrVal;
				}
			}
		}
		return $RtrnVal;
	}
	private function GetNewID() {
    $NewUUID = '';
    global $wpdb;
    $Query = 'SELECT uuid() AS uuid';
    $DataRows = $wpdb->get_results($Query);
    foreach ( $DataRows as $Row) {
    	$NewUUID = $Row->uuid;
    }
    unset($DataRows);
    return $NewUUID;
	}
  final public function Set_Customer($Value) {
		$SetOk = false;
    if ((is_array($Value)) && (!empty($Value[0]))) {
      $this->Customer = $Value;
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_CustomerAddress($Value) {
		$SetOk = false;
    if ((is_array($Value)) && (!empty($Value[0]))) {
      $this->CstmrAddress = $Value;
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Order($Value) {
		$SetOk = false;
    if ((is_array($Value)) && (!empty($Value[0]))) {
      $this->Order = $Value;
      $SetOk = true;
    }
		return $SetOk;
	}
  public function GetActivePaymentMethods() {
    $RtrnData = array('Error' => true, 'Data' => '');
    $Data = array();
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = $wpdb->prepare(
      'SELECT '.$prfx.'mns_store_payment.*, '.$prfx.'mns_store_payment_descriptions.*, '.$prfx.'mns_orders_payment_type.name
        FROM '.$prfx.'mns_store_payment, '.$prfx.'mns_store_payment_descriptions, '.$prfx.'mns_orders_payment_type, '.$prfx.'mns_store
        WHERE '.$prfx.'mns_store_payment.store = '.$prfx.'mns_store.uuid
        AND '.$prfx.'mns_store_payment.type = '.$prfx.'mns_orders_payment_type.uuid
        AND '.$prfx.'mns_store_payment.uuid = '.$prfx.'mns_store_payment_descriptions.payment
        AND '.$prfx.'mns_store_payment_descriptions.language = %s
        AND '.$prfx.'mns_store_payment.active = TRUE',
      $_SESSION['MensioThemeLang']
    );
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      $i = 0;
      foreach ($DataSet as $Row) {
        $Data[$i]['Payment'] = $Row->uuid;
        $Data[$i]['Type'] = $Row->name;
        $Data[$i]['Description'] = $Row->description;
        $Data[$i]['Instructions'] = $Row->instructions;
        ++$i;
      }
      $RtrnData['Data'] = $Data;
      $RtrnData['Error'] = false;
    }
    return $RtrnData;
  }
  public function GetPaymentMethodData($ID,$Method,$GtwName='') {
    $RtrnData = array('Error' => true, 'Data' => '');
    $Data = array();
    switch ($Method) {
      case 'Delivery':
        $Data = $this->LoadOnDeliveryData($ID);
        $RtrnData['Error'] = false;
        $RtrnData['Data'] = $Data;
        break;
      case 'Deposit':
        $Data = $this->LoadBankDepositData($ID);
        $RtrnData['Error'] = false;
        $RtrnData['Data'] = $Data;
        break;
      case 'Gateway':
        $Data = $this->LoadPaymentGatewayData($ID,$GtwName);
        $RtrnData['Error'] = false;
        $RtrnData['Data'] = $Data;
        break;
    }
    return $RtrnData;
  }
  private function LoadOnDeliveryData($ID) {
    $RtrnData = array('Description' => '', 'Instructions'=>'', 'Options' => '');
    $PayMethod = new mensio_payment_methods();
    if ($PayMethod->Set_UUID($ID)) {
      $Data = $PayMethod->LoadPayOnDeliveryData(false);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $RtrnData['Description'] = $Row->description;
          $RtrnData['Instructions'] = $Row->instructions;
        }
      }
      $RtrnData['Options'] = $PayMethod->LoadPayOnDeliveryShippingOptions();
    }
    unset($PayMethod);
    return $RtrnData;
  }
  private function LoadBankDepositData($ID) {
    $RtrnData = array('Description' => '', 'Instructions'=>'', 'Options' => '');
    $PayMethod = new mensio_payment_methods();
    if ($PayMethod->Set_UUID($ID)) {
      $Data = $PayMethod->LoadBankDepositData(false);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $RtrnData['Description'] = $Row->description;
          $RtrnData['Instructions'] = $Row->instructions;
        }
      }
      $RtrnData['Options'] = $PayMethod->LoadBankAccountList();
    }
    unset($PayMethod);
    return $RtrnData;
  }
  private function LoadPaymentGatewayData($ID,$GtwName) {
    $RtrnData = array('Description' => '', 'Instructions'=>'', 'Options' => '');
    $PayMethod = new mensio_payment_methods();
    if ($PayMethod->Set_UUID($ID)) {
      $Data = $PayMethod->LoadGatewayData($GtwName,false);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $RtrnData['Description'] = $Row->description;
          $RtrnData['Instructions'] = $Row->instructions;
        }
      }
      $RtrnData['Options'] = $PayMethod->LoadGatewayParameters();
    }
    unset($PayMethod);
    return $RtrnData;
  }
  public function AddCustomerNewOrder() {
    $RtrnData = array('Error' => false, 'Data' => '');
    if (!is_array($this->Customer)) { $RtrnData['Error'] = true; }
    if (!is_array($this->CstmrAddress)) { $RtrnData['Error'] = true; }
    if (!is_array($this->Order)) { $RtrnData['Error'] = true; }
    if (!$RtrnData['Error']) {
      if ($this->Customer['CustID'] === 'Guest') {
        $CustID = $this->RegisterNewCustomer();
        $AddrID = $this->RegisterNewCustomerAddress();
      }
    }
    return $RtrnData;
  }
  private function RegisterNewCustomer() {
    $Seller = new mensio_seller();
    $data = array(
              "email"=>$_SESSION['mnsUser']['Data']['UserName'],
              "lastname"=>$_SESSION['mnsUser']['Data']['LastName'],
              "firstname"=>$_SESSION['mnsUser']['Data']['FirstName'],
              "country"=>$country_id,
              "region"=>$region_id,
              "city"=>filter_var($_REQUEST['mns_city']),
              "address"=>filter_var($_REQUEST['mns_NewAddress']),
              "zip_code"=>filter_var($_REQUEST['mns_zip_code']),
              "phone"=>filter_var($_REQUEST['mns_phone'])
            );        
    $this->Customer = $Seller->SignUpNewCustomer();
  }
}