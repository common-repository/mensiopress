<?php
class mensio_shipping extends mensio_core_db {
  private $Name;
  private $DeliverySpeed;
  private $BillingType;
  private $Active;
  private $Shipping;
  private $Courier;
  private $Country;
  private $Price;
  private $Weight;
  private $Disabled;
  private $Sorter;
  private $SearchString;
  private $ExtraFilters;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Name = '';
    $this->DeliverySpeed = '';
    $this->BillingType = '';
    $this->Active = '';
    $this->Shipping = '';
    $this->Courier = '';
    $this->Country = '';
    $this->Price = '';
    $this->Weight = '';
    $this->Disabled = '';
    $this->Sorter = 'name';
    $this->SearchString = '';
    $this->ExtraFilters = '';
  }
  final public function Set_Name($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' ,-');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Name = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_DeliverySpeed($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' ,-');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->DeliverySpeed = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_BillingType($Value) {
		$SetOk = false;
    if (($Value === 'WEIGHT') || ($Value === 'PRICE')) {
      $this->BillingType = $Value;
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Active($Value) {
		$SetOk = false;
    if (($Value === 1) || ($Value === '1') || ($Value === true)) {
        $this->Active = '1';
        $SetOk = true;
    } else {
        $this->Active = '0';
        $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Shipping($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Shipping = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Courier($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Courier = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Country($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Country = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Price($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM',',.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Price = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}  
  final public function Set_Weight($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM',',.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Weight = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}  
  final public function Set_Disabled($Value) {
		$SetOk = false;
    if (($Value === 1) || ($Value === '1') || ($Value === true)) {
        $this->Disabled = '1';
        $SetOk = true;
    } else {
        $this->Disabled = '0';
        $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Sorter($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN',' ');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Sorter = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_SearchString($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $Value = mb_ereg_replace('[^\p{L}\p{N}]','%',$Value);
      $ClrVal = $this->ClearValue($Value,'EN','%');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->SearchString = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_ExtraFilters($Value) {
		$SetOk = false;
    $this->ExtraFilters = '';
    $JSONData = stripslashes($Value);
    $JSONData = json_decode($JSONData,true);
    if (json_last_error() === JSON_ERROR_NONE) {
      $this->ExtraFilters = $JSONData;
			$SetOk = true;
    }
		return $SetOk;
	}
  public function LoadShippingDataSet() {
    $Searcher = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    switch ($this->Sorter) {
      case 'billing':
        $this->Sorter = 'billing_type';
        break;
      case 'billing DESC':
        $this->Sorter = 'billing_type DESC';
        break;
    }
    if ($this->SearchString !== '') {
      $Searcher .= 'WHERE '.$prfx.'couriers_type.name LIKE "%'.$this->SearchString.'%"';
    }
    $Query = 'SELECT * FROM '.$prfx.'couriers_type '.$Searcher.' ORDER BY '.$this->Sorter;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadCourierTypeData() {
    $RtrnData = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'couriers_type WHERE uuid = "'.$this->Get_UUID().'"';
      $RtrnData = $wpdb->get_results($Query);
    }
    return $RtrnData;
  }
  public function LoadCourierShippingData($ForAdmin=true) {
    $RtrnData = array();
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'orders_shipping. *, '.$prfx.'countries_names.name
        FROM '.$prfx.'orders_shipping, '.$prfx.'countries_names, '.$prfx.'store
        WHERE  '.$prfx.'orders_shipping.courier = "'.$this->Get_UUID().'"
        AND '.$prfx.'orders_shipping.country = '.$prfx.'countries_names.country
        AND '.$prfx.'countries_names.language = '.$prfx.'store.'.$lang.'
        AND  '.$prfx.'orders_shipping.disabled = FALSE
        ORDER BY  '.$prfx.'countries_names.name, '.$prfx.'orders_shipping.weight';
      $RtrnData = $wpdb->get_results($Query);
    }
    return $RtrnData;
  }
  public function GetNewCode() {
    return $this->GetNewUUID();
  }
  public function CheckIfShippingIsUsed() {
    $Found = false;
    if ($this->Shipping !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS CountRows FROM '.$prfx.'orders
        WHERE shipping = "'.$this->Shipping.'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if ($Row->CountRows > 0) { $Found = true; }
        }
      }
    }
    return $Found;
  }
  public function InsertNewCourierData() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->DeliverySpeed === '') { $Error = true; }
    if ($this->BillingType === '') { $Error = true; }
    if ($this->Active === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'couriers_type
          (uuid,name,delivery_speed,billing_type,active)
          VALUES (%s,%s,%s,%s,%s)',
        $this->Get_UUID(),
        $this->Name,
        $this->DeliverySpeed,
        $this->BillingType,
        $this->Active
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }    
    }
    return $JobDone;
  }
  public function UpdateCourierData() {
    $JobDone = false;
    $ColSet = '';
    if ($this->Get_UUID() !== '') {
      if ($this->Name !== '') {
        if ($ColSet !== '') { $ColSet .=','; }
        $ColSet .= ' name = "'.$this->Name.'"';
      }
      if ($this->DeliverySpeed !== '') {
        if ($ColSet !== '') { $ColSet .=','; }
        $ColSet .= ' delivery_speed = "'.$this->DeliverySpeed.'"';
      }
      if ($this->BillingType !== '') {
        if ($ColSet !== '') { $ColSet .=','; }
        $ColSet .= ' billing_type = "'.$this->BillingType.'"';
      }
      if ($this->Active !== '') {
        if ($ColSet !== '') { $ColSet .=','; }
        $ColSet .= ' active = "'.$this->Active.'"';
      }
      if ($ColSet !== '') {
        global $wpdb;
        $prfx = $wpdb->prefix.'mns_';
        $Query = $wpdb->prepare(
          'UPDATE '.$prfx.'couriers_type SET'.$ColSet.' WHERE uuid = %s',
          $this->Get_UUID()
        );
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
  public function FoundShippingEntry() {
    $RtrnData = '';
    $Error = false;
    if ($this->Courier === '') { $Error = true; }
    if ($this->Country === '') { $Error = true; }
    if ($this->Weight === '') { $Error = true; }
    if ($this->Price === '') { $Error = true; }
    if ($Error) {
      $RtrnData = 'Empty';
    } else {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'orders_shipping WHERE courier = %s
          AND country = %s AND price = %s AND weight = %s',
        $this->Courier,
        $this->Country,
        $this->Weight,
        $this->Price
      );
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if ($Row->disabled) {
            $RtrnData = 'Disabled';
            $this->Set_Shipping($Row->uuid);
          } else {
            $RtrnData = 'Enabled';
          }
        }
      } else {
        $RtrnData = 'NotFound';
      }
    }
    return $RtrnData;
  }
  public function ReEnabledShippingEntry($Enable=true) {
    $JobDone = false;
    if ($Enable) { $Value = 'FALSE'; }
      else { $Value = 'TRUE'; }
    if ($this->Shipping !== '') {
        global $wpdb;
        $prfx = $wpdb->prefix.'mns_';
        $Query = $wpdb->prepare(
          'UPDATE '.$prfx.'orders_shipping SET disabled = '.$Value.' WHERE uuid = %s',
          $this->Shipping
        );
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function InsertNewShippingEntry() {
    $JobDone = false;
    $Error = false;
    if ($this->Courier === '') { $Error = true; }
    if ($this->Country === '') { $Error = true; }
    if ($this->Weight === '') { $Error = true; }
    if ($this->Price === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'orders_shipping
          (uuid,courier,country,price,weight,disabled)
          VALUES (%s,%s,%s,%s,%s,"0")',
        $this->GetNewUUID(),
        $this->Courier,
        $this->Country,
        $this->Price,
        $this->Weight
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }    
    }
    return $JobDone;
  }
  public function DisableCourierCountry() {
    $JobDone = false;
    $Error = false;
    if ($this->Courier === '') { $Error = true; }
    if ($this->Country === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
        $Query = $wpdb->prepare(
          'UPDATE '.$prfx.'orders_shipping SET disabled = TRUE
            WHERE courier = %s AND country = %s',
        $this->Courier,
        $this->Country
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }    
    }
    return $JobDone;
  }
}