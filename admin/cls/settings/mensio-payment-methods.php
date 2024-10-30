<?php
class mensio_payment_methods extends mensio_core_db {
  private $Active;
  private $Language;
  private $Description;
  private $Instructions;
  private $ShippingOption;
  private $BankAccount;
  private $Icon;
  private $Bank;
  private $Name;
  private $Number;
  private $Routing;
  private $IBAN;
  private $Swift;
  private $Parameter;
  private $Value;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Active = '0';
    $this->Language = '';
    $this->Description = '';
    $this->Instructions = '';
    $this->ShippingOption = '';
    $this->BankAccount = '';
    $this->Icon = '';
    $this->Bank = '';
    $this->Name = '';
    $this->Number = '';
    $this->Routing = '';
    $this->IBAN = '';
    $this->Swift = '';
    $this->Parameter = '';
    $this->Value = '';
  }
	final public function Set_Active($Value) {
		$SetOK = false;
    if (($Value == 1) || ($Value == '1') || ($Value == true)) {
      $this->Active = '1';
      $SetOK = true;
    } else {
      $this->Active = '0';
      $SetOK = true;
    }
		return $SetOK;
	}
  final public function Set_Language($Value) {
		$SetOk = false;
		$ClrVal = $this->ClearUUID($Value);
		if ($ClrVal != false) {
			$this->Language = $ClrVal;
			$SetOk = true;
		}
		return $SetOk;
	}
  final public function Set_Description($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $this->Description = wp_kses_post($Value);
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Instructions($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $this->Instructions = wp_kses_post($Value);
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_ShippingOption($Value) {
		$SetOk = false;
		$ClrVal = $this->ClearUUID($Value);
		if ($ClrVal != false) {
			$this->ShippingOption = $ClrVal;
			$SetOk = true;
		}
		return $SetOk;
	}
  final public function Set_BankAccount($Value) {
		$SetOk = false;
		$ClrVal = $this->ClearUUID($Value);
		if ($ClrVal != false) {
			$this->BankAccount = $ClrVal;
			$SetOk = true;
		}
		return $SetOk;
	}
  final public function Set_Icon($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'EN','-_./:=?&');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $ClrVal = str_replace(get_site_url().'/','',$ClrVal); //get_site_url().'/'.
      $this->Icon = $ClrVal;
      $SetOK = true;
    }
    return $SetOK;
  }
  final public function Set_Bank($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'AN','- ');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $SetOK = true;
      $this->Bank = $ClrVal;
    }
    return $SetOK;
  }
  final public function Set_Name($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'EN',' ');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $SetOK = true;
      $this->Name = $ClrVal;
    }
    return $SetOK;
  }
  final public function Set_Number($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'EN',' ');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $SetOK = true;
      $this->Number = $ClrVal;
    }
    return $SetOK;
  }
  final public function Set_Routing($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'EN','-');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $SetOK = true;
      $this->Routing = $ClrVal;
    }
    return $SetOK;
  }
  final public function Set_IBAN($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'EN',' ');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $SetOK = true;
      $this->IBAN = $ClrVal;
    }
    return $SetOK;
  }
  final public function Set_Swift($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'EN',' ');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $SetOK = true;
      $this->Swift = $ClrVal;
    }
    return $SetOK;
  }
  final public function Set_Parameter($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'EN',' -');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $SetOK = true;
      $this->Parameter = $ClrVal;
    }
    return $SetOK;
  }
  final public function Set_Value($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'EN','-_./:=?&+');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $ClrVal = str_replace(get_site_url().'/','',$ClrVal);
      $this->Value = $ClrVal;
      $SetOK = true;
    }
    return $SetOK;
  }
  final public function GetNewCode() {
    return $this->GetNewUUID();
  }
  final public function GetStoreID() {
    $Store = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'store';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $Store = $Row->uuid;
      }
    }
    return $Store;
  }
  public function GetPaymentMethodType() {
    $RtrnData = '';
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'SELECT '.$prfx.'orders_payment_type.*
          FROM '.$prfx.'store_payment, '.$prfx.'orders_payment_type
          WHERE '.$prfx.'store_payment.type = '.$prfx.'orders_payment_type.uuid
          AND '.$prfx.'store_payment.uuid = %s',
        $this->Get_UUID()
      );
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData = $Row->name;
        }
      }
    }
    return $RtrnData;
  }
  public function GetPaymentMethodTranslation() {
    $RtrnData = array('Description'=>'','Instructions'=>'');
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Language === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'store_payment_descriptions WHERE payment = %s AND language = %s',
        $this->Get_UUID(),
        $this->Language
      );
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData['Description'] = $Row->description;
          $RtrnData['Instructions'] = $Row->instructions;
        }
      }
    }
    return $RtrnData;
  }
  private function LoadPaymentBasicData($PayType,$ForAdmin=true) {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($ForAdmin) {
      $lang = $prfx.'store.adminlang';
    }else {
      if (!isset($_SESSION['MensioThemeLang'])) {
        $lang = $prfx.'store.themelang';
      } else {
        $lang = '"'.$_SESSION['MensioThemeLang'].'"';
      }
    }
    $Query = 'SELECT DISTINCT '.$prfx.'store_payment.*, '.$prfx.'store_payment_descriptions.*
      FROM '.$prfx.'store_payment , '.$prfx.'store_payment_descriptions,
        '.$prfx.'store, '.$prfx.'languages_codes, '.$prfx.'orders_payment_type
      WHERE '.$prfx.'store_payment.store = '.$prfx.'store.uuid
      AND '.$prfx.'store_payment.uuid = '.$prfx.'store_payment_descriptions.payment
      AND '.$lang.' = '.$prfx.'store_payment_descriptions.language
      AND '.$prfx.'store_payment.type = '.$prfx.'orders_payment_type.uuid
      AND '.$prfx.'orders_payment_type.name = "'.$PayType.'"';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  final public function LoadStorePayments() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT '.$prfx.'store_payment.*, '.$prfx.'orders_payment_type.name
      FROM '.$prfx.'store_payment, '.$prfx.'orders_payment_type
      WHERE '.$prfx.'store_payment.type = '.$prfx.'orders_payment_type.uuid';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  private function AddDummyPaymentValues($PayType) {
    $Payment = $this->GetNewUUID();
    $Store = '';
    $PayTypeID = '';
    if ($PayType !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Store = $this->GetStoreID();
      $Query = 'SELECT * FROM '.$prfx.'orders_payment_type WHERE name = "'.$PayType.'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $PayTypeID = $Row->uuid;
        }
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'store_payment (uuid,store,type,active)
            VALUES (%s,%s,%s,"0")',
          $Payment,
          $Store,
          $PayTypeID
        );
        $wpdb->query($Query);
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'store_payment_descriptions (payment,language,description,instructions)
            VALUES (%s,(SELECT adminlang FROM '.$prfx.'store WHERE uuid = %s),"-","-")',
          $Payment,
          $Store
        );
        $wpdb->query($Query);
      }
    }
  }
  public function LoadPayOnDeliveryData($ForAdmin=true) {
    $DataSet = $this->LoadPaymentBasicData('On Delivery',$ForAdmin);
    if ((!is_array($DataSet)) || (empty($DataSet[0]))) {
      $this->AddDummyPaymentValues('On Delivery');
      $DataSet = $this->LoadPaymentBasicData('On Delivery',$ForAdmin);
    }
    return $DataSet;
  }
  public function LoadBankDepositData($ForAdmin=true) {
    $DataSet = $this->LoadPaymentBasicData('Bank Deposit',$ForAdmin);
    if ((!is_array($DataSet)) || (empty($DataSet[0]))) {
      $this->AddDummyPaymentValues('Bank Deposit');
      $DataSet = $this->LoadPaymentBasicData('Bank Deposit',$ForAdmin);
    }
    return $DataSet;
  }
  public function LoadGatewayData($Gateway,$ForAdmin=true) {
    $DataSet = $this->LoadPaymentBasicData($Gateway,$ForAdmin);
    if ((!is_array($DataSet)) || (empty($DataSet[0]))) {
      $this->AddDummyPaymentValues($Gateway);
      $DataSet = $this->LoadPaymentBasicData($Gateway,$ForAdmin);
    }
    return $DataSet;
  }
  public function LoadPayOnDeliveryShippingOptions() {
    $RtrnData = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'store_payment_delivery WHERE payment = %s',
        $this->Get_UUID()
      );
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $i = 0;
        foreach ($DataSet as $Row) {
          $RtrnData[$i] = $Row->shipping;
          ++$i;
        }
      }
    }
    return $RtrnData;
  }
  public function LoadBankAccountList() {
    $RtrnData = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'store_payment_bank WHERE payment = %s ORDER BY account_bank',
        $this->Get_UUID()
      );
      $RtrnData = $wpdb->get_results($Query);
    }
    return $RtrnData;
  }
  public function LoadBankAccountData() {
    $RtrnData = array();
    if ($this->BankAccount !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'store_payment_bank WHERE uuid = %s',
        $this->BankAccount
      );
      $RtrnData = $wpdb->get_results($Query);
    }
    return $RtrnData;
  }
  public function LoadGatewayParameters() {
    $RtrnData = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'store_payment_gateways WHERE payment = %s ORDER BY parameter',
        $this->Get_UUID()
      );
      $RtrnData = $wpdb->get_results($Query);
    }
    return $RtrnData;
  }
  public function UpdatePaymentActivation() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Active === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'store_payment SET active = %s WHERE uuid = %s',
        $this->Active,
        $this->Get_UUID()
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdatePaymentTranslation() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Language === '') { $Error = true; }
    if ($this->Description === '') { $Error = true; }
    if ($this->Instructions === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'store_payment_descriptions
          WHERE payment = %s AND language = %s',
        $this->Get_UUID(),
        $this->Language
      );
      if (false !== $wpdb->query($Query)) {
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'store_payment_descriptions (payment,language,description,instructions)
            VALUES (%s,%s,%s,%s)',
          $this->Get_UUID(),
          $this->Language,
          $this->Description,
          $this->Instructions
        );
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
  public function ShippingOptionFound() {
    $RecFound = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->ShippingOption === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'store_payment_delivery WHERE payment = %s AND shipping = %s',
        $this->Get_UUID(),
        $this->ShippingOption
      );
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $RecFound = true;
      }
    }
    return $RecFound;
  }
  public function AddPayOnDeliveryShippingOption() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->ShippingOption === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'store_payment_delivery (payment,shipping)
          VALUES (%s,%s)',
        $this->Get_UUID(),
        $this->ShippingOption
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RemovePayOnDeliveryShippingOption() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->ShippingOption === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'store_payment_delivery WHERE payment = %s AND shipping = %s',
        $this->Get_UUID(),
        $this->ShippingOption
      );
      if (false !== $wpdb->query($Query)) {$JobDone = true; }
    }
    return $JobDone;
  }
  public function AddNewBankAccountData() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->BankAccount === '') { $Error = true; }
    if ($this->Bank === '') { $Error = true; }
    if ($this->Icon === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Number === '') { $Error = true; }
    if ($this->Routing === '') { $Error = true; }
    if ($this->IBAN === '') { $Error = true; }
    if ($this->Swift === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'store_payment_bank
          (uuid,payment,account_bank,account_icon,account_name,account_number,account_routing,account_iban,account_swift)
          VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)',
        $this->BankAccount,
        $this->Get_UUID(),
        $this->Bank,
        $this->Icon,
        $this->Name,
        $this->Number,
        $this->Routing,
        $this->IBAN,
        $this->Swift
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateBankAccountData() {
    $JobDone = false;
    $Error = false;
    if ($this->BankAccount === '') { $Error = true; }
    if ($this->Bank === '') { $Error = true; }
    if ($this->Icon === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Number === '') { $Error = true; }
    if ($this->Routing === '') { $Error = true; }
    if ($this->IBAN === '') { $Error = true; }
    if ($this->Swift === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'store_payment_bank SET account_bank = %s, account_icon = %s,
          account_name = %s, account_number = %s, account_routing = %s, account_iban = %s, account_swift = %s
         WHERE uuid = %s',
        $this->Bank,
        $this->Icon,
        $this->Name,
        $this->Number,
        $this->Routing,
        $this->IBAN,
        $this->Swift,
        $this->BankAccount
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function DeleteBankAccountData() {
    $JobDone = false;
    if ($this->BankAccount !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'store_payment_bank WHERE uuid = %s',
        $this->BankAccount
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateGatewayParameter() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Parameter === '') { $Error = true; }
    if ($this->Value === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'store_payment_gateways SET value = %s
          WHERE payment = %s AND parameter = %s',
        $this->Value,
        $this->Get_UUID(),
        trim($this->Parameter)
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function LoadSalesDefaultLandingPages() {
    $DataSet = array('Success'=>'--', 'Failed'=>'--');
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'store_payment LIMIT 1';
    $Data = $wpdb->get_results($Query);    
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $DataSet['Success'] = $Row->success_page;
        $DataSet['Failed'] = $Row->failed_page;
      }
    }
    return $DataSet;
  }
  public function UpdateSalesDefaultLandingPages($Type,$OldVal) {
    $JobDone = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = $wpdb->prepare(
      'UPDATE '.$prfx.'store_payment SET '.$Type.'_page = %s',
      $this->Value
    );
    $wpdb->query($Query);
    $Query = $wpdb->prepare(
      'UPDATE '.$prfx.'store_payment_gateways SET value = %s WHERE parameter LIKE %s AND value = %s',
      $this->Value,
      '%Return%'.ucfirst($Type).'%Page%',
      $OldVal
    );
    if (false !== $wpdb->query($Query)) { $JobDone = true; }
    return $JobDone;
  }
}