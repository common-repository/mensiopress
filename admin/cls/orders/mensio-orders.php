<?php
class mensio_orders extends mensio_core_db {
  private $Customer;
  private $BlngAddress;
  private $SendAddress;
  private $Product;
  private $Shipping;
  private $Serial;
  private $Amount;
  private $Price;
  private $Tax;
  private $DiscountID;
  private $Discount;
  private $Status;
  private $SplitOrder;
  private $PaymentType;
  private $Payment;
  private $OrderDate;
  private $Sorter;
  private $SearchString;
  private $ExtraFilters;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Customer = '';
    $this->BlngAddress = '';
    $this->SendAddress = '';
    $this->Product = '';
    $this->Shipping ='';
    $this->Serial = '';
    $this->Amount = '';
    $this->Price = '';
    $this->Tax = '';
    $this->DiscountID = '';
    $this->Discount = '';
    $this->Status = '';
    $this->SplitOrder = '';
    $this->PaymentType = '';
    $this->Payment = '';
    $this->OrderDate = '';
    $this->Sorter = 'created DESC';
    $this->SearchString = '';
    $this->ExtraFilters = '';
  }
  final public function Set_Customer($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Customer = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_BlngAddress($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->BlngAddress = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_SendAddress($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->SendAddress = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Product($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Product = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Amount($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM',',.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Amount = $ClrVal;
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
  final public function Set_Tax($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM',',.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Tax = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}  
  final public function Set_Discount($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM',',.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Discount = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}  
  final public function Set_DiscountID($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->DiscountID = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Status($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Status = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_SplitOrder($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->SplitOrder = $ClrVal;
        $SetOk = true;
      }
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
  final public function Set_Serial() {
		$SetOk = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'orders ORDER BY serial DESC LIMIT 1';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $TopSerial = $Row->serial;
        $TopSerial = explode('-',$TopSerial);
        $LstArr = count($TopSerial) - 1;
        $this->Serial = $TopSerial[$LstArr] + 1;
        $len = strlen($this->Serial);
        for ($i=6; $i>$len; --$i) {
          $this->Serial = '0'.$this->Serial;
        }
      }
    } else {
      $this->Serial = '000001';
    }
    if ($this->Serial !== '') {
      $Query = 'SELECT * FROM '.$prfx.'store';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          switch ($Row->orderserial) {
            case 'YYYYMMDD-nnnnnn':
              $String = date("Y-m-d");
              $String = str_replace('-', '', $String);
              $this->Serial = $String.'-'.$this->Serial;
              break;
            case 'YYYYMM-nnnnnn':
              $String = date("Y-m");
              $String = str_replace('-', '', $String);
              $this->Serial = $String.'-'.$this->Serial;
              break;
            case 'YYYY-nnnnnn':
              $this->Serial = date("Y").'-'.$this->Serial;
              break;
            case 'nnnnnn':
              break;
            default:
              $this->Serial = $Row->orderserial.'-'.$this->Serial;
              break;
          }
      		$SetOk = true;
        }
      }
    }
		return $SetOk;
	}
  final public function GetNewSerial() {
    $this->Set_Serial();
    return $this->Serial;
  }
  final public function Set_PaymentType($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->PaymentType = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Payment($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Payment = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_OrderDate($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM','-:');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->OrderDate = $ClrVal;
        $SetOk = true;
      }
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
    $JSONData = json_decode($Value,true);
    if (json_last_error() === JSON_ERROR_NONE) {
      $this->ExtraFilters = $JSONData;
			$SetOk = true;
    }    
		return $SetOk;
	}
  public function GetNewID() {
    return $this->GetNewUUID();
  }
  public function LoadOrdersListDataSet() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $ExtraTable = '';
    $Searcher = '';
    if ($this->ExtraFilters !== '') {
      if ((is_array($this->ExtraFilters)) && (!empty($this->ExtraFilters[0]))) {
        foreach ($this->ExtraFilters as $Row) {
          switch($Row['Field']) {
            case 'Status':
              if (($Row['Value'] !== '') && ($Row['Value'] !== '0')) {
                $Searcher .= 'AND '.$prfx.'orders.uuid IN (
                  SELECT orders FROM '.$prfx.'orders_status
                  WHERE status = "'.$Row['Value'].'" AND active = TRUE
                )';
              }
              break;
            case 'Customers':
              if (($Row['Value'] !== '') && ($Row['Value'] !== '0')) {
                $Searcher .= 'AND '.$prfx.'orders.customer = "'.$Row['Value'].'" ';
              }
              break;
            case 'Payment':
              if (($Row['Value'] !== '') && ($Row['Value'] !== '0')) {
                $ExtraTable = ', '.$prfx.'orders_payment, '.$prfx.'store_payment';
                $Searcher .= 'AND '.$prfx.'orders.uuid = '.$prfx.'orders_payment.orders
                  AND '.$prfx.'orders_payment.payment = '.$prfx.'store_payment.uuid
                  AND '.$prfx.'store_payment.type = "'.$Row['Value'].'" ';
              }
              break;
            case 'Date':
              if (($Row['Value'] !== '') && ($Row['Value'] !== '0')) {
                $this->SearchString = $Row['Value'];
              }
              break;
          }
        }
      }
    }
    if ($this->SearchString !== '') {
      $Searcher .= 'AND (serial LIKE "%'.$this->SearchString.'%"
        OR refnumber LIKE "%'.$this->SearchString.'%"
        OR created  LIKE "%'.$this->SearchString.'%")';
    }    
    $Query = '
      SELECT '.$prfx.'orders.*, '.$prfx.'orders_status_type.name
      FROM '.$prfx.'orders, '.$prfx.'orders_status, '.$prfx.'orders_status_type'.$ExtraTable.'
      WHERE '.$prfx.'orders.uuid = '.$prfx.'orders_status.orders
      AND '.$prfx.'orders_status.active = TRUE
      AND '.$prfx.'orders_status.status = '.$prfx.'orders_status_type.uuid
      '.$Searcher.'
      ORDER BY '.$this->Sorter;
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      $i = 0;
      foreach ($Data as $Row) {
        $DataSet[$i]['uuid'] = $Row->uuid;
        $DataSet[$i]['serial'] = $Row->serial.'<br>#'.$Row->refnumber;
        $DataSet[$i]['created'] = date("d/m/Y", strtotime($Row->created));
        $DataSet[$i]['orderip'] = $Row->orderip;
        $DataSet[$i]['status'] = $Row->name;
        $Compl = 'Active';
        if ($Row->complete) { $Compl = 'Complete'; }
        $DataSet[$i]['complete'] = $Compl;
        ++$i;
      }
    }
    return $DataSet;
  }
  public function GetOrderData() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'orders WHERE uuid = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadOrdersStatus() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'orders_status_type
      WHERE uuid IN (SELECT status FROM '.$prfx.'orders_status)
      ORDER BY name';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadOrdersStatusfilter() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Searcher = '';
    if ($this->Customer !== '') {
      $Searcher .= 'AND uuid IN (SELECT '.$prfx.'orders_status.status
        FROM '.$prfx.'orders_status, '.$prfx.'orders
        WHERE '.$prfx.'orders_status.active = TRUE
        AND '.$prfx.'orders_status.orders = '.$prfx.'orders.uuid
        AND '.$prfx.'orders.customer = "'.$this->Customer.'")';
    }
    if ($this->PaymentType !== '') {
      $Searcher .= 'AND uuid IN (SELECT '.$prfx.'orders_status.status
        FROM '.$prfx.'orders_status, '.$prfx.'orders, '.$prfx.'orders_payment,
          '.$prfx.'store_payment
        WHERE '.$prfx.'orders_status.orders = '.$prfx.'orders.uuid
        AND '.$prfx.'orders.uuid = '.$prfx.'orders_payment.orders
        AND '.$prfx.'orders_payment.payment = '.$prfx.'store_payment.uuid
        AND '.$prfx.'store_payment.type = "'.$this->PaymentType.'")';
    }
    if ($this->OrderDate !== '') {
      $Searcher .= 'AND uuid IN (SELECT '.$prfx.'orders_status.status
        FROM '.$prfx.'orders_status, '.$prfx.'orders
        WHERE '.$prfx.'orders_status.orders = '.$prfx.'orders.uuid
        AND '.$prfx.'orders.created LIKE "'.$this->OrderDate.'%")';
    }
    $Query = 'SELECT * FROM '.$prfx.'orders_status_type
      WHERE uuid IN (SELECT status FROM '.$prfx.'orders_status WHERE active = TRUE)
      '.$Searcher.'
      ORDER BY name';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadOrdersCustomers() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'credentials WHERE uuid IN (
      SELECT customer FROM '.$prfx.'orders WHERE deleted = FALSE
    ) ORDER BY lastname';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadOrdersCustomersFilter() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Searcher = '';
    if ($this->Status !== '') {
      $Searcher .= ' AND uuid IN (SELECT '.$prfx.'orders.customer
        FROM '.$prfx.'orders, '.$prfx.'orders_status
        WHERE '.$prfx.'orders.uuid= '.$prfx.'orders_status.orders
        AND '.$prfx.'orders_status.status = "'.$this->Status.'")';
    }
    if ($this->PaymentType !== '') {
      $Searcher .= ' AND uuid IN (SELECT '.$prfx.'orders.customer
        FROM '.$prfx.'orders, '.$prfx.'orders_payment, '.$prfx.'store_payment
        WHERE '.$prfx.'orders.uuid = '.$prfx.'orders_payment.orders
        AND '.$prfx.'orders_payment.payment = '.$prfx.'store_payment.uuid
        AND '.$prfx.'store_payment.type = "'.$this->PaymentType.'")';
    }
    if ($this->OrderDate !== '') {
      $Searcher .= ' AND uuid IN (SELECT customer FROM '.$prfx.'orders WHERE created LIKE "'.$this->OrderDate.'%")';
    }
    $Query = 'SELECT * FROM '.$prfx.'credentials WHERE uuid IN
      (SELECT customer FROM '.$prfx.'orders WHERE deleted = FALSE)
    '.$Searcher.'
    ORDER BY lastname';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadOrdersPayment() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'orders_payment_type ORDER BY name';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadOrdersPaymentFilter() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Searcher = '';
    if ($this->Status !== '') {
      if ($Searcher === '') { $Searcher .= ' WHERE'; }
        else { $Searcher .= ' AND'; }
      $Searcher .= ' uuid IN (SELECT '.$prfx.'store_payment.type
        FROM '.$prfx.'store_payment, '.$prfx.'orders_payment, '.$prfx.'orders, '.$prfx.'orders_status
        WHERE '.$prfx.'store_payment.uuid = '.$prfx.'orders_payment.payment
        AND '.$prfx.'orders_payment.orders = '.$prfx.'orders.uuid
        AND '.$prfx.'orders.uuid = '.$prfx.'orders_status.orders
        AND '.$prfx.'orders_status.status = "'.$this->Status.'")';
    }
    if ($this->Customer !== '') {
      if ($Searcher === '') { $Searcher .= ' WHERE'; }
        else { $Searcher .= ' AND'; }
      $Searcher .= ' uuid IN (SELECT '.$prfx.'store_payment.type
        FROM '.$prfx.'store_payment, '.$prfx.'orders_payment, '.$prfx.'orders
        WHERE '.$prfx.'store_payment.uuid = '.$prfx.'orders_payment.payment
        AND '.$prfx.'orders_payment.orders = '.$prfx.'orders.uuid
        AND '.$prfx.'orders.customer = "'.$this->Customer.'")';
    }
    if ($this->OrderDate !== '') {
      if ($Searcher === '') { $Searcher .= ' WHERE'; }
        else { $Searcher .= ' AND'; }
      $Searcher .= ' uuid IN (SELECT '.$prfx.'store_payment.type
        FROM '.$prfx.'store_payment, '.$prfx.'orders_payment, '.$prfx.'orders
        WHERE '.$prfx.'store_payment.uuid = '.$prfx.'orders_payment.payment
        AND '.$prfx.'orders_payment.orders = '.$prfx.'orders.uuid
        AND '.$prfx.'orders.created LIKE "'.$this->OrderDate.'%")';
    }
    $Query = 'SELECT * FROM '.$prfx.'orders_payment_type '.$Searcher.' ORDER BY name';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;    
  }
  public function LoadOrdersDates() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'orders ORDER BY created DESC';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadOrdersDatesFilter() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Searcher = '';
    if ($this->Status !== '') {
      if ($Searcher === '') { $Searcher .= ' WHERE'; }
        else { $Searcher .= ' AND'; }
      $Searcher .= ' uuid IN (
        SELECT orders FROM '.$prfx.'orders_status
        WHERE active = TRUE AND status = "'.$this->Status.'")';
    }
    if ($this->Customer !== '') {
      if ($Searcher === '') { $Searcher .= ' WHERE'; }
        else { $Searcher .= ' AND'; }
      $Searcher .= ' customer = "'.$this->Customer.'"';
    }
    if ($this->PaymentType !== '') {
      if ($Searcher === '') { $Searcher .= ' WHERE'; }
        else { $Searcher .= ' AND'; }
      $Searcher .= ' uuid IN (SELECT orders
        FROM '.$prfx.'orders, '.$prfx.'orders_payment, '.$prfx.'store_payment
        WHERE '.$prfx.'orders.uuid = '.$prfx.'orders_payment.orders
        AND '.$prfx.'orders_payment.payment = '.$prfx.'store_payment.uuid
        AND '.$prfx.'store_payment.type = "'.$this->PaymentType.'")';
    }
    $Query = 'SELECT * FROM '.$prfx.'orders '.$Searcher.' ORDER BY created DESC';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;    
  }
  public function LoadOrderDiscounts() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'discounts WHERE uuid IN
        (SELECT discount FROM '.$prfx.'orders_discounts
          WHERE orders = "'.$this->Get_UUID().'")';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function CheckOrdersDiscount() {
    $Found = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->DiscountID === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'orders_discounts
        WHERE orders = "'.$this->Get_UUID().'" AND discount = "'.$this->DiscountID.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) { $Found = true; }
    }
    return $Found;
  }
  public function LoadOrderShipping() {
    $Shipping = 0;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'orders_shipping.*
        FROM '.$prfx.'orders, '.$prfx.'orders_shipping
        WHERE '.$prfx.'orders.shipping = '.$prfx.'orders_shipping.uuid
        AND '.$prfx.'orders.uuid = "'.$this->Get_UUID().'"
        ORDER BY '.$prfx.'orders_shipping.courier';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Shipping = $Row->price; }
      }
    }
    return $Shipping;
  }
  public function LoadOrderProducts($ForAdmin=true) {
    $DataSet = false;
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      if (MENSIO_FLAVOR !== 'FREE') {
          $Query = 'SELECT DISTINCT '.$prfx.'orders_products.*, '.$prfx.'products.code,
          '.$prfx.'products.stock, '.$prfx.'products_descriptions.name
        FROM '.$prfx.'orders_products, '.$prfx.'products, '.$prfx.'products_descriptions,
          '.$prfx.'products_variations, '.$prfx.'store
        WHERE (
          '.$prfx.'orders_products.product = '.$prfx.'products.uuid
          AND '.$prfx.'orders_products.product = '.$prfx.'products_descriptions.product
          AND '.$prfx.'products_descriptions.language = '.$prfx.'store.adminlang
          AND '.$prfx.'orders_products.orders = "'.$this->Get_UUID().'"
        )
        OR (
          '.$prfx.'orders_products.product = '.$prfx.'products_variations.variation
          AND '.$prfx.'products_variations.product = '.$prfx.'products.uuid
          AND '.$prfx.'products.uuid = '.$prfx.'products_descriptions.product
          AND '.$prfx.'products_descriptions.language = '.$prfx.'store.adminlang
          AND '.$prfx.'orders_products.orders = "'.$this->Get_UUID().'"
        )';
      } else {
          $Query = 'SELECT DISTINCT '.$prfx.'orders_products.*, '.$prfx.'products.code,
          '.$prfx.'products.stock, '.$prfx.'products_descriptions.name
        FROM '.$prfx.'orders_products, '.$prfx.'products, '.$prfx.'products_descriptions, '.$prfx.'store
        WHERE (
          '.$prfx.'orders_products.product = '.$prfx.'products.uuid
          AND '.$prfx.'orders_products.product = '.$prfx.'products_descriptions.product
          AND '.$prfx.'products_descriptions.language = '.$prfx.'store.adminlang
          AND '.$prfx.'orders_products.orders = "'.$this->Get_UUID().'"
        )';
      }
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadOrderStatusHistory() {
    $DataSet = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'orders_status.*, '.$prfx.'orders_status_type.name,
          '.$prfx.'orders_status_type.final
        FROM '.$prfx.'orders_status,'.$prfx.'orders_status_type
        WHERE '.$prfx.'orders_status.status = '.$prfx.'orders_status_type.uuid
        AND '.$prfx.'orders_status.orders = "'.$this->Get_UUID().'"
        ORDER BY '.$prfx.'orders_status.changed';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function GetOrderPaymentName() {
    $PaymentName = '';
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'orders_payment_type.*
        FROM '.$prfx.'store_payment, '.$prfx.'orders_payment, '.$prfx.'orders_payment_type
        WHERE '.$prfx.'store_payment.uuid = '.$prfx.'orders_payment.payment
        AND '.$prfx.'store_payment.type = '.$prfx.'orders_payment_type.uuid
        AND '.$prfx.'orders_payment.orders = "'.$this->Get_UUID().'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $PaymentName = $Row->name; }
      }
    }
    return $PaymentName;
  }
  public function GetActivePaymentMethods() {
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT '.$prfx.'mns_store_payment.*, '.$prfx.'mns_store_payment_descriptions.*, '.$prfx.'mns_orders_payment_type.name
        FROM '.$prfx.'mns_store_payment, '.$prfx.'mns_store_payment_descriptions, '.$prfx.'mns_orders_payment_type, '.$prfx.'mns_store
        WHERE '.$prfx.'mns_store_payment.store = '.$prfx.'mns_store.uuid
        AND '.$prfx.'mns_store_payment.type = '.$prfx.'mns_orders_payment_type.uuid
        AND '.$prfx.'mns_store_payment.uuid = '.$prfx.'mns_store_payment_descriptions.payment
        AND '.$prfx.'mns_store_payment_descriptions.language = '.$prfx.'mns_store.adminlang
        AND '.$prfx.'mns_store_payment.active = TRUE';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function GetOrderPaymentData() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'orders_payment.*, '.$prfx.'orders_payment_type.name, '.$prfx.'orders.complete
        FROM '.$prfx.'orders, '.$prfx.'orders_payment, '.$prfx.'store_payment, '.$prfx.'orders_payment_type
        WHERE '.$prfx.'orders.uuid = '.$prfx.'orders_payment.orders
        AND '.$prfx.'orders_payment.payment = '.$prfx.'store_payment.uuid
        AND '.$prfx.'store_payment.type = '.$prfx.'orders_payment_type.uuid
        AND '.$prfx.'orders_payment.orders = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function GetSplitOrderChildsData() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'orders WHERE uuid IN (
        SELECT split_order FROM '.$prfx.'orders_split_relations WHERE orders = "'.$this->Get_UUID().'"
      )';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadNonOrderProducts($OrderPrds,$ForAdmin=true) {
    $DataSet = array();
    $Error = false;
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->SearchString === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $ExceptClause = '';
      if (($OrderPrds !== '') && (is_array($OrderPrds))) {
        foreach ($OrderPrds as $Row) {
          $ExceptClause .= ' AND '.$prfx.'products.uuid != "'.$Row.'"';
        }
      }
      $Query = 'SELECT '.$prfx.'products.*, '.$prfx.'brands.name as BrandName,
          '.$prfx.'products_descriptions.name as Name,
          '.$prfx.'products_images.file as Image
        FROM '.$prfx.'products, '.$prfx.'brands, '.$prfx.'products_descriptions,
          '.$prfx.'products_images, '.$prfx.'store
        WHERE '.$prfx.'products.brand = '.$prfx.'brands.uuid
        AND '.$prfx.'products.uuid = '.$prfx.'products_descriptions.product
        AND '.$prfx.'products_descriptions.language = '.$prfx.'store.'.$lang.'
        AND '.$prfx.'products.uuid = '.$prfx.'products_images.product
        AND '.$prfx.'products_images.main = TRUE
        '.$ExceptClause.'
        AND ('.$prfx.'products.code LIKE "%'.$this->SearchString.'%"
          OR '.$prfx.'products_descriptions.name LIKE "%'.$this->SearchString.'%")
        ORDER BY '.$prfx.'products.code';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        $i = 0;
        foreach ($Data as $Row) {
          $DataSet[$i]['uuid'] = $Row->uuid;
          $DataSet[$i]['BrandName'] = $Row->BrandName;
          $DataSet[$i]['Code'] = $Row->code;
          $DataSet[$i]['Name'] = $Row->Name;
          $DataSet[$i]['Image'] = $Row->Image;
          $DataSet[$i]['Stock'] = $Row->stock;
          $DataSet = $this->CheckForNonOrderVariations($DataSet,$Row->uuid,$ExceptClause);
          $i = count($DataSet);
        }
      }
    }
    return $DataSet;
  }
  private function CheckForNonOrderVariations($DataSet,$ProductID,$ExceptClause) {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT '.$prfx.'products.*, '.$prfx.'brands.name as BrandName,
          '.$prfx.'products_descriptions.name as Name,
          '.$prfx.'products_images.file as Image
        FROM '.$prfx.'products, '.$prfx.'brands, '.$prfx.'products_descriptions,
          '.$prfx.'products_images, '.$prfx.'store
        WHERE '.$prfx.'products.brand = '.$prfx.'brands.uuid
        AND '.$prfx.'products.uuid = '.$prfx.'products_images.product
        AND  '.$prfx.'products_images.main = TRUE
        AND '.$prfx.'products_descriptions.product = "'.$ProductID.'"
        AND '.$prfx.'products_descriptions.language = '.$prfx.'store.adminlang
        AND '.$prfx.'products.uuid IN (SELECT variation FROM '.$prfx.'products_variations
          WHERE product = "'.$ProductID.'")
        '.$ExceptClause.'
        ORDER BY '.$prfx.'products.code';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      $i = count($DataSet);
      foreach ($Data as $Row) {
        $DataSet[$i]['uuid'] = $Row->uuid;
        $DataSet[$i]['BrandName'] = $Row->BrandName;
        $DataSet[$i]['Code'] = $Row->code;
        $DataSet[$i]['Name'] = $Row->Name;
        $DataSet[$i]['Image'] = $Row->Image;
        $DataSet[$i]['Stock'] = $Row->stock;
        ++$i;
      }
    }
    return $DataSet;
  }
  public function LoadShippingOptions() {
    $DataSet = array();
    if ($this->SendAddress !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'orders_shipping.*, '.$prfx.'couriers_type.name
        FROM '.$prfx.'orders_shipping, '.$prfx.'couriers_type, '.$prfx.'addresses, '.$prfx.'countries_codes
        WHERE '.$prfx.'orders_shipping.courier = '.$prfx.'couriers_type.uuid
        AND '.$prfx.'orders_shipping.country = '.$prfx.'addresses.country
        AND '.$prfx.'orders_shipping.country = '.$prfx.'countries_codes.uuid
        AND '.$prfx.'addresses.uuid = "'.$this->SendAddress.'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function GetProductStock() {
    $Stock = 0;
    if ($this->Product !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'products WHERE uuid = "'.$this->Product.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Stock = $Row->stock; }
      }
    }
    return $Stock;
  }
  public function InsertNewOrderData() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Serial === '') { $Error = true; }
    if ($this->Customer === '') { $Error = true; }
    if ($this->BlngAddress === '') { $Error = true; }
    if ($this->SendAddress === '') { $Error = true; }
    if ($this->Shipping === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'orders (uuid,serial,refnumber,created,customer,billingaddr,
          sendingaddr,shipping,orderip,complete)
          VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)',
        $this->Get_UUID(),
        $this->Serial,
        $this->CreateNewRefNumber(),
        date("Y-m-d H:i:s"),
        $this->Customer,
        $this->BlngAddress,
        $this->SendAddress,
        $this->Shipping,
        $_SERVER['REMOTE_ADDR'],
        '0'
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  private function CreateNewRefNumber() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $RefNum = '';
    do {
      $RefNum = rand(100,999).'-'.rand(1000000,9999999).'-'.rand(1000000,9999999);
      $Query = 'SELECT * FROM '.$prfx.'orders WHERE refnumber = "'.$RefNum.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) { $RefNum = ''; }
    } while ($RefNum === '');
    return $RefNum;
  }
  public function UpdateOrderData() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Serial === '') { $Error = true; }
    if ($this->Customer === '') { $Error = true; }
    if ($this->BlngAddress === '') { $Error = true; }
    if ($this->SendAddress === '') { $Error = true; }
    if ($this->Shipping === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'orders SET customer = %s, billingaddr = %s,
          sendingaddr = %s, shipping = %s WHERE uuid = %s',
        $this->Customer,
        $this->BlngAddress,
        $this->SendAddress,
        $this->Shipping,
        $this->Get_UUID()
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateOrderToComplete() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'orders SET complete = "1" WHERE uuid = %s',
        $this->Get_UUID()
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function ClearOrderProducts() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = 'SELECT * FROM '.$prfx.'orders_products WHERE orders = "'.$this->Get_UUID().'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Query = $wpdb->prepare(
            'UPDATE '.$prfx.'products SET stock = stock + '.$Row->amount.' WHERE uuid = %s',
            $Row->product
          );
          $wpdb->query($Query);
        }
      }      
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'orders_products WHERE orders = %s',
        $this->Get_UUID()
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function AddOrderProduct() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Product === '') { $Error = true; }
    if ($this->Amount === '') { $Error = true; }
    if ($this->Price === '') { $Error = true; }
    if ($this->Discount === '') { $Error = true; }
    if ($this->Tax === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $FPrice = (($this->Amount * $this->Price) + $this->Tax) - $this->Discount;
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'orders_products
         (orders,product,amount,price,discount,taxes,fullprice)
         VALUES (%s,%s,%s,%s,%s,%s,%s)',
        $this->Get_UUID(),
        $this->Product,
        $this->Amount,
        $this->Price,
        $this->Discount,
        $this->Tax,
        $FPrice
      );
      if (false !== $wpdb->query($Query)) {
        $Query = $wpdb->prepare(
          'UPDATE '.$prfx.'products SET stock = stock - '.$this->Amount.' WHERE uuid = %s',
          $this->Product
        );
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
  public function LoadStatusList() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'orders_status_type WHERE uuid NOT IN
        (SELECT status FROM '.$prfx.'orders_status WHERE orders = "'.$this->Get_UUID().'")
        ORDER BY name';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadActiveStatusName() {
    $Name = '';
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'orders_status_type.*
        FROM '.$prfx.'orders_status, '.$prfx.'orders_status_type
        WHERE '.$prfx.'orders_status.status = '.$prfx.'orders_status_type.uuid
        AND '.$prfx.'orders_status.active = TRUE
        AND '.$prfx.'orders_status.orders = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Name = $Row->name;
        }
      }
    }
    return $Name;
  }
  public function InsertNewOrderStatus() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'orders_status (orders,status,changed,active)
         VALUES (%s,(SELECT uuid FROM '.$prfx.'orders_status_type WHERE name = "Submitted"),%s,"1")',
        $this->Get_UUID(),
        date("Y-m-d H:i:s")
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function AddOrderNewStatus() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Status === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'orders_status SET active = 0 WHERE orders = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'orders_status (orders,status,changed,active)
         VALUES (%s,%s,%s,"1")',
        $this->Get_UUID(),
        $this->Status,
        date("Y-m-d H:i:s")
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function AddNewSplitOrder($RefNumber) {
    $Error = false;
    $NewOrder = '';
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Customer === '') { $Error = true; }
    if ($this->BlngAddress === '') { $Error = true; }
    if ($this->SendAddress === '') { $Error = true; }
    if ($this->Shipping === '') { $Error = true; }
    if (!$Error) {
      $OrderID = $this->GetNewID();
      $this->Set_Serial();
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'orders (uuid,serial,refnumber,created,customer,billingaddr,
          sendingaddr,shipping,orderip,complete)
          VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,"0")',
        $OrderID,
        $this->Serial,
        $RefNumber,
        date("Y-m-d H:i:s"),
        $this->Customer,
        $this->BlngAddress,
        $this->SendAddress,
        $this->Shipping,
        $_SERVER['REMOTE_ADDR']
      );
      $wpdb->query($Query);
      $DataSet = $this->GetParentOrderStatusList();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Query = $wpdb->prepare(
            'INSERT INTO '.$prfx.'orders_status (orders,status,changed,active)
             VALUES (%s,%s,%s,%s)',
            $OrderID,
            $Row->status,
            date("Y-m-d H:i:s"),
            $Row->active
          );
          $wpdb->query($Query);
        }
      }
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'orders_split_relations (orders,split_order)
          VALUES (%s,%s)',
        $this->Get_UUID(),
        $OrderID
      );
      $wpdb->query($Query);
      $Payment = $this->GetParentOrderPayment();
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'orders_payment (orders,payment,answer)
          VALUES (%s,%s,%s)',
        $OrderID,
        $Payment['payment'],
        $Payment['answer']
      );
      if (false !== $wpdb->query($Query)) { $NewOrder = $OrderID; }
    }
    return $NewOrder;
  }
  private function GetParentOrderStatusList() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'orders_status.* FROM '.$prfx.'orders_status, '.$prfx.'orders_status_type
        WHERE '.$prfx.'orders_status.orders = "'.$this->Get_UUID().'"
        AND '.$prfx.'orders_status.status = '.$prfx.'orders_status_type.uuid
        AND '.$prfx.'orders_status_type.name != "Split Order"
        ORDER BY '.$prfx.'orders_status.changed';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  private function GetParentOrderPayment() {
    $DataSet = array('payment'=>'','answer'=>'');
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'orders_payment WHERE orders = "'.$this->Get_UUID().'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $DataSet['payment'] = $Row->payment;
          $DataSet['answer'] = $Row->answer;
        }
      }
    }
    return $DataSet;
  }
  public function AddSplitOrderProduct() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Product === '') { $Error = true; }
    if ($this->SplitOrder === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'orders_products SET orders = %s WHERE orders = %s AND product = %s',
        $this->SplitOrder,
        $this->Get_UUID(),
        $this->Product
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function SetSplitOrderStatus() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'orders_status SET active = 0 WHERE orders = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'orders_payment SET answer = "Split" WHERE orders = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'orders_status (orders,status,changed,active)
         VALUES (%s,(SELECT uuid FROM '.$prfx.'orders_status_type WHERE name = "Split Order"),%s,"1")',
        $this->Get_UUID(),
        date("Y-m-d H:i:s")
      );
      if (false !== $wpdb->query($Query)) { 
        $Query = $wpdb->prepare(
          'UPDATE '.$prfx.'orders SET complete = TRUE WHERE uuid = %s',
          $this->Get_UUID()
        );
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
  public function AddOrderDiscount() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->DiscountID === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'orders_discounts (orders,discount) VALUES (%s,%s)',
        $this->Get_UUID(),
        $this->DiscountID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RemoveOrderDiscount() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->DiscountID === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'orders_discounts WHERE orders = %s AND discount = %s',
        $this->Get_UUID(),
        $this->DiscountID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdatePaymentStatus() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'orders_payment SET answer = "Completed" WHERE orders = %s',
        $this->Get_UUID()
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'orders_status SET active = FALSE WHERE orders = %s',
        $this->Get_UUID()
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'orders_status (orders,status,changed,active)
         VALUES (%s,(SELECT uuid FROM '.$prfx.'orders_status_type WHERE name = "Active"),%s,"1")',
        $this->Get_UUID(),
        date("Y-m-d H:i:s")
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      if (!$Error) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function GetCustomerMailData() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'credentials.title, '.$prfx.'credentials.firstname,
          '.$prfx.'credentials.lastname, '.$prfx.'contacts.value
        FROM '.$prfx.'orders, '.$prfx.'credentials, '.$prfx.'contacts, '.$prfx.'contacts_type
        WHERE '.$prfx.'orders.customer = '.$prfx.'credentials.uuid
        AND '.$prfx.'orders.customer = '.$prfx.'contacts.credential
        AND '.$prfx.'contacts.type = '.$prfx.'contacts_type.uuid
        AND '.$prfx.'contacts_type.name = "E-Mail"
        AND '.$prfx.'orders.uuid = "'.$this->Get_UUID().'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        $i = 0;
        foreach ($Data as $Row) {
          $DataSet[$i]['Title'] = $Row->title;
          $DataSet[$i]['FName'] = $Row->firstname;
          $DataSet[$i]['LName'] = $Row->lastname;
          $DataSet[$i]['Mail'] = $Row->value;
          ++$i;
        }
      }
    }
    return $DataSet;
  }
  public function LoadOrderReferenceNumber() {
    $RefNum = '';
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'orders WHERE uuid = "'.$this->Get_UUID().'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $RefNum = $Row->refnumber; }
      }
    }
    return $RefNum;
  }
  public function LoadOrdersMailTemplate($Name) {
    $Template = '';
    if (($Name === 'Sales') || ($Name === 'Status')) {
      $Template = $this->LoadMailTemplate($Name);
    }
    return $Template;
  }
  public function UpdateOrderPayment() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Payment === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'DELETE FROM '.$prfx.'orders_payment WHERE orders = "'.$this->Get_UUID().'"';
      if (false !== $wpdb->query($Query)) {
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'orders_payment (orders,payment,answer) VALUES (%s,%s,%s)',
          $this->Get_UUID(),
          $this->Payment,
          'Payment type set/changed internaly at '.date("Y-m-d H:i:s")
        );
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
}