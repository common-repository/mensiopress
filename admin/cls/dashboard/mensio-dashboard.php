<?php
class mensio_dashboard extends mensio_core_db {
  public function __construct() {
    if (!defined('WPINC')) {
      die();
    }
  }
  public function GetVisitsToSalesData() {
    $DataSet = array('Visits' => 0, 'Sales' => 0, 'TotalVisits' => 0, 'Overflow' => 0);
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT COUNT(*) as Visits FROM ' . $prfx . 'customers_history WHERE visitdate >= DATE(NOW()) + INTERVAL -7 DAY';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $DataSet['Visits'] = $Row->Visits;
      }
    }
    $Query = 'SELECT COUNT(*) as Sales FROM ' . $prfx . 'orders WHERE created >= DATE(NOW()) + INTERVAL -7 DAY';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $DataSet['Sales'] = $Row->Sales;
      }
    }
    $Query = 'SELECT COUNT(*) as TotalVisits FROM ' . $prfx . 'customers_history';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $DataSet['TotalVisits'] = $Row->TotalVisits;
      }
    }
    if ($DataSet['Sales'] > $DataSet['Visits']) {
      $DataSet['Overflow'] = $DataSet['Sales'] - $DataSet['Visits'];
    }
    return $DataSet;
  }
  public function GetGuestsToCustomersData() {
    $DataSet = array('Guests' => 0, 'Customers' => 0);
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT COUNT(*) as Guests FROM ' . $prfx . 'customers_history
      WHERE visitdate >= DATE(NOW()) + INTERVAL -7 DAY
      AND customer LIKE "Guest%"';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $DataSet['Guests'] = $Row->Guests;
      }
    }
    $Query = 'SELECT COUNT(*) as Customers FROM ' . $prfx . 'customers_history
      WHERE visitdate >= DATE(NOW()) + INTERVAL -7 DAY
      AND customer NOT LIKE "Guest%"';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $DataSet['Customers'] = $Row->Customers;
      }
    }
    return $DataSet;
  }
  public function GetNewMensioLogs($Type) {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM ' . $prfx . 'mensiologs WHERE log_type = "' . $Type . '" AND informed = FALSE';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function GetCredentialData($Username) {
    $RtrnData = array('Username' => '', 'Date' => '', 'Name' => '');
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM ' . $prfx . 'credentials WHERE username = "' . $Username . '"';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $RtrnData['Username'] = $Row->username;
        $RtrnData['Date'] = $Row->lastlogin;
        $RtrnData['Name'] = $Row->lastname . ' ' . $Row->firstname;
      }
    }
    return $RtrnData;
  }
  public function GetSalesData($Serial) {
    $DataSet = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM ' . $prfx . 'orders WHERE serial = "' . $Serial . '"';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      $DataSet = array();
      foreach ($Data as $Row) {
        $DataSet['uuid'] = $Row->uuid;
        $DataSet['refnumber'] = $Row->refnumber;
        $DataSet['serial'] = $Row->serial;
        $DataSet['created'] = $Row->created;
        $DataSet['customer'] = $this->GetCustomerName($Row->customer);
        $DataSet['status'] = $this->GetSalesStatusName($Row->uuid);
        $DataSet['Products'] = $this->GetSalesProducts($Row->uuid);
      }
    }
    return $DataSet;
  }
  private function GetCustomerName($CustomerID) {
    $CustName = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM ' . $prfx . 'credentials WHERE uuid = "' . $CustomerID . '"';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $CustName = $Row->username . ' (' . $Row->lastname . ' ' . $Row->firstname . ')';
      }
    }
    return $CustName;
  }
  private function GetSalesStatusName($OrderID) {
    $Status = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM ' . $prfx . 'orders_status_type WHERE uuid IN (
      SELECT status FROM ' . $prfx . 'orders_status WHERE orders = "' . $OrderID . '"
    )';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $Status = $Row->name;
      }
    }
    return $Status;
  }
  private function GetSalesProducts($OrderID) {
    $Products = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT ' . $prfx . 'orders_products.*, ' . $prfx . 'products_descriptions.name
      FROM ' . $prfx . 'orders_products, ' . $prfx . 'products_descriptions
      WHERE ' . $prfx . 'orders_products.product = ' . $prfx . 'products_descriptions.product
      AND ' . $prfx . 'products_descriptions.language = "' . $this->LoadAdminLang() . '"
      AND ' . $prfx . 'orders_products.orders = "' . $OrderID . '"';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        if ($Products === '') {
          $Products = $Row->name . '::' . ($Row->amount + 0);
        } else {
          $Products .= '||' . $Row->name . '::' . ($Row->amount + 0);
        }
      }
    }
    return $Products;
  }
  final public function GetCustomerData($ID) {
    $DataSet = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM ' . $prfx . 'credentials WHERE username = "' . $ID . '"';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $DataSet['username'] = $Row->username;
        $DataSet['lastname'] = $Row->lastname;
        $DataSet['firstname'] = $Row->firstname;
        $Address = $this->GetCountryName($Row->uuid);
        $DataSet['country'] = $Address['country'];
        $DataSet['city'] = $Address['city'];
        $DataSet['street'] = $Address['street'];
        $DataSet['zipcode'] = $Address['zipcode'];
        $DataSet['phone'] = $Address['phone'];
      }
    }
    return $DataSet;
  }
  private function GetCountryName($ID) {
    $DataSet = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT ' . $prfx . 'countries_names.name
      FROM ' . $prfx . 'countries_names, ' . $prfx . 'addresses
      WHERE ' . $prfx . 'countries_names.language = "' . $this->LoadAdminLang() . '"
      AND ' . $prfx . 'countries_names.country = ' . $prfx . 'addresses.country
      AND ' . $prfx . 'addresses.credential = "' . $ID . '"';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $DataSet['country'] = $Row->name;
      }
    }
    $Query = 'SELECT * FROM ' . $prfx . 'addresses WHERE credential = "' . $ID . '"';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $DataSet['city'] = $Row->city;
        $DataSet['street'] = $Row->street;
        $DataSet['zipcode'] = $Row->zipcode;
        $DataSet['phone'] = $Row->phone;
      }
    }
    return $DataSet;
  }
  final public function GetTicketData($ID) {
    $DataSet = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT ' . $prfx . 'customers_tickets.*, ' . $prfx . 'credentials.username
      FROM ' . $prfx . 'customers_tickets, ' . $prfx . 'credentials
      WHERE ' . $prfx . 'customers_tickets.customer = ' . $prfx . 'credentials.uuid
      AND ' . $prfx . 'customers_tickets.ticket_code = "' . $ID . '"';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $DataSet['username'] = $Row->username;
        $DataSet['ticket_code'] = $Row->ticket_code;
        $DataSet['date'] = $Row->dateadded;
        $DataSet['title'] = $Row->title;
        $DataSet['content'] = $Row->content;
      }
    }
    return $DataSet;
  }
  public function UpdateMensioLogsInformed($ID) {
    $JobDone = false;
    if (is_integer(intval($ID))) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare( 
        'UPDATE ' . $prfx . 'mensiologs SET informed = TRUE WHERE code = %s', $ID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function GetInstalledMsg() {
    $DataSet = array('ShowMessage' => false, 'Date' => '');
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT * FROM ' . $prfx . 'options WHERE option_name = "mensio_installed_NewSetupMsg"';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        if ($Row->option_value === 'Show') {
          $DataSet['ShowMessage'] = true;
        }
      }
    }
    $Query = 'SELECT * FROM ' . $prfx . 'options WHERE option_name = "mensio_installed_Date"';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $DataSet['Date'] = $Row->option_value;
      }
    }
    return $DataSet;
  }
}
