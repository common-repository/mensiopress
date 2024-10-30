<?php
class mensio_customers extends mensio_core_db {
  private $CustomerUUID;
  private $Type;
  private $MultiCred;
  private $Source;
  private $IPAddress;
  private $Guuid;
  private $Username;
  private $Password;
  private $Encryption;
  private $Hashkey;
  private $Title;
  private $Firstname;
  private $Lastname;
  private $Active;
  private $Lastlogin;
  private $LoginIP;
  private $Sector;
  private $NewCompany;
  private $Company;
  private $Name;
  private $Tin;
  private $WebSite;
  private $EMail;
  private $Address;
  private $AddressType;
  private $Fullname;
  private $Country;
  private $Region;
  private $City;
  private $Street;
  private $Zipcode;
  private $Phone;
  private $Notes;
  private $Contact;
  private $ContactType;
  private $Value;
  private $Validated;
  private $ExtraFilters;
  private $SearchString;
  private $Sorter;
  private $NewEntry;
  private $Deleted;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->CustomerUUID = '';
    $this->Type = '';
    $this->MultiCred = false;
    $this->Source = '';
    $this->IPAddress = '';
    $this->Guuid = '';
    $this->Username = '';
    $this->Password = '';
    $this->Encryption = 'BLOWFISH';
    $this->Hashkey = '';
    $this->Title = '';
    $this->Firstname = '';
    $this->Lastname = '';
    $this->Active = '';
    $this->Lastlogin = '';
    $this->LoginIP = '';
    $this->Active = '1';
    $this->Sector = '';
    $this->NewCompany = false;
    $this->Company = '';
    $this->Name = '';
    $this->Tin = '';
    $this->WebSite = '';
    $this->EMail = '';
    $this->Address = '';
    $this->AddressType = '';
    $this->Fullname = '';
    $this->Country = '';
    $this->City = '';
    $this->Region = '';
    $this->Street = '';
    $this->Zipcode = '';
    $this->Phone = '';
    $this->Notes = '';
    $this->Contact = '';
    $this->ContactType = '';
    $this->Value = '';
    $this->Validated = '';
    $this->ExtraFilters = '';
    $this->SearchString = '';
    $this->Sorter = '';
    $this->Deleted = false ;
    $this->NewEntry = false;
  }
  final public function Set_CustomerUUID($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->CustomerUUID = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Type($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Type = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Source($Value) {
		$SetOk = false;
		switch ($Value) {
      case 'S': case 'F': case 'T': case 'G':
        $this->Source = $Value;
        $SetOk = true;
        break;
		}
		return $SetOk;
	}
  final public function Set_IPAddress($Value) {
		$SetOk = false;
    if ($Value === 'UNKNOWN') {
      $this->IPAddress = $Value;
      $SetOk = true;
    } else {
      if ($Value !== '') {
        $ClrVal = $this->ClearValue($Value,'NM','.');
        if (mb_strlen($ClrVal) === mb_strlen($Value)) {
          $ValArray = explode ('.',$ClrVal);
          if ((is_array($ValArray)) && (count($ValArray) === 4)) {
            $this->IPAddress = $ClrVal;
            $SetOk = true;
          }
        }
      }
    }
		return $SetOk;
	}
  final public function Set_Guuid($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Guuid = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Username($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN','-_@.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $ClrVal = sanitize_email($Value);
        if (is_email($ClrVal)) {
          $this->Username = $ClrVal;
          $SetOk = true;
        }
      }
    }
		return $SetOk;
	}
  final public function Set_Password($Value) {
		$SetOk = false;
    if ($Value === '') {
      $this->Password = 'EMPTY';
      $SetOk = true;
    } else {
      $ClrVal = password_hash($Value, PASSWORD_DEFAULT);
      if ($ClrVal !== '') {
        $this->Hashkey = substr($ClrVal,0,7);
        $this->Password = substr($ClrVal,7, strlen($ClrVal));
        $SetOk = true;
      }
		}
		return $SetOk;
	}
  final public function Set_Encryption($Value) {
		$SetOk = false;
    $this->Encryption = 'BLOWFISH';
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Encryption = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Title($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'TX',' .');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Title = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Firstname($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'TX',' ');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Firstname = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Lastname($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'TX',' ');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Lastname = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Lastlogin($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM','-:');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Lastlogin = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_LoginIP($Value) {
		$SetOk = false;
    if ($Value === 'UNKNOWN') {
      $this->LoginIP = $Value;
      $SetOk = true;
    } else {
      if ($Value !== '') {
        $ClrVal = $this->ClearValue($Value,'NM','.');
        if (mb_strlen($ClrVal) === mb_strlen($Value)) {
          $ValArray = explode ('.',$ClrVal);
          if ((is_array($ValArray)) && (count($ValArray) === 4)) {
            $this->LoginIP = $ClrVal;
            $SetOk = true;
          }
        }
      }
    }
		return $SetOk;
	}
  final public function Set_Active($Value) {
    if (($Value === false) || ($Value === 0) || ($Value === '0')) {
      $this->Active = '0';
    } else {
      $this->Active = '1';
    }
    return true;
	}
  final public function Set_Sector($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Sector = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Company($Value) {
		$SetOk = false;
    if ($Value !== '') {
      if ($Value === 'NewMultiAccount') {
        $this->Company = $this->Get_UUID();
        $this->NewCompany = true;
        $SetOk = true;
      } else {
        $ClrVal = $this->ClearUUID($Value);
        if ($ClrVal != false) {
          $this->Company = $ClrVal;
          $this->NewCompany = false;
          $SetOk = true;
        }
      }
    }
		return $SetOk;
	}
  final public function Set_CompanyName($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' ');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Name = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
  }
  final public function Set_Tin($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN','-');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Tin = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
  }
  final public function Set_WebSite($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = esc_url_raw($Value);
      if ($ClrVal !== '') {
        $this->WebSite = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
  }
  final public function Set_CompanyEMail($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = sanitize_email($Value);
      if (is_email($ClrVal)) {
        $this->EMail = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
  }
  final public function Set_Address($Value) {
		$SetOk = false;
    if ($Value === 'NewAddress') {
      $this->Address = $this->GetNewUUID();
      $this->NewEntry = true;
      $SetOk = true;
    } else {
      if ($Value !== '') {
        $ClrVal = $this->ClearUUID($Value);
        if ($ClrVal != false) {
          $this->Address = $ClrVal;
          $SetOk = true;
        }
      }
    }
		return $SetOk;
	}
  final public function Set_AddressType($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->AddressType = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Fullname($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN',' ,.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Fullname = addslashes($ClrVal);
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
  final public function Set_City($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN');
      $ClrVal = $this->ClearValue($ClrVal,'TX');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->City = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
  }
  final public function Set_Region($Value) {
		$SetOk = false;
    if ($Value === 'NOREGION') {
        $this->Region = $Value;
        $SetOk = true;
    } else {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Region = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Street($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' ,.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Street = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
  }
  final public function Set_Zipcode($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' ');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Zipcode = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
  }
  final public function Set_Phone($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Phone = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
  }
  final public function Set_Notes($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' ,');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Notes = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
  }
  final public function Set_Contact($Value) {
		$SetOk = false;
    if ($Value === 'NewContact') {
      $this->Contact = $this->GetNewUUID();
      $this->NewEntry = true;
      $SetOk = true;
    } else {
      if ($Value !== '') {
        $ClrVal = $this->ClearUUID($Value);
        if ($ClrVal != false) {
          $this->Contact = $ClrVal;
          $SetOk = true;
        }
      }
    }
		return $SetOk;
	}
  final public function Set_ContactType($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->ContactType = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Value($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN','#@$&-?_ ');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Value = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Validated($Value) {
    if (($Value === false) || ($Value === 0) || ($Value === '0')) {
      $this->Validated = '0';
    } else {
      $this->Validated = '1';
    }
    return true;
	}
  final public function Set_Deleted($Value) {
    if (($Value === false) || ($Value === 0) || ($Value === '0')) {
      $this->Deleted = '0';
    } else {
      $this->Deleted = '1';
    }
    return true;
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
  final public function Set_SearchString($Value) {
		$SetOk = false;
    $Value = mb_ereg_replace('[^\p{L}\p{N}]','%',$Value);
    $ClrVal = $this->ClearValue($Value,'AN','%');
		if (mb_strlen($ClrVal) === mb_strlen($Value)) {
			$this->SearchString = $ClrVal;
			$SetOk = true;
		}
		return $SetOk;
	}
  final public function Set_Sorter($Value) {
		$SetOk = false;
		$ClrVal = $this->ClearValue($Value,'EN',' ');
		$ClrVal = $this->ClearValue($ClrVal,'TX',' ');
		if (mb_strlen($ClrVal) === mb_strlen($Value)) {
			$this->Sorter = $ClrVal;
			$SetOk = true;
		}
		return $SetOk;
	}
  final public function Return_Set_Password() {
    $RtrnData = $this->Hashkey.$this->Password;
    return $RtrnData;
  }
  public function LoadTableList($Deleted="FALSE") {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $SortBy = 'created DESC';
    if ($this->Sorter !== '') { $SortBy = $this->Sorter;}
    $Searcher = '';
    $CompTbl = '';
    if ($this->ExtraFilters !== '') {
      if ((is_array($this->ExtraFilters)) && (!empty($this->ExtraFilters[0]))) {
        foreach ($this->ExtraFilters as $Row) {
          switch($Row['Field']) {
            case 'CustTypes':
              if (($Row['Value'] !== '') && ($Row['Value'] !== '0')) {
                $Searcher .= 'AND '.$prfx.'customers.type = "'.$Row['Value'].'"';
              }
              break;
            case 'Companies':
              if (($Row['Value'] !== '') && ($Row['Value'] !== '0')) {
                $CompTbl = ','.$prfx.'companies';
                $Searcher .= '
                AND '.$prfx.'customers.uuid = '.$prfx.'companies.customer
                AND '.$prfx.'companies.customer = "'.$Row['Value'].'" ';
              }
              break;
            case 'Dates':
              if (($Row['Value'] !== '') && ($Row['Value'] !== '0')) {
                $this->SearchString = $Row['Value'];
              }
              break;
          }
        }
      }
    }
    if ($this->SearchString !== '') {
      $DateSearch = '';
      $DateSearch = mb_ereg_replace('[^0-9]','',$this->SearchString);
      if ($DateSearch !== '') {
        $DateSearch = 'OR '.$prfx.'customers.created LIKE "%'.$this->SearchString.'%"';
      }
      $Searcher .= ' AND (
        '.$prfx.'credentials.firstname LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'credentials.lastname LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'credentials.username LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'customers_types.name LIKE "%'.$this->SearchString.'%"
        '.$DateSearch.'
        )';
    }
      $Query = '
      SELECT '.$prfx.'credentials.uuid, '.$prfx.'credentials.firstname,
          '.$prfx.'credentials.active, '.$prfx.'credentials.lastname,
          '.$prfx.'customers_types.name, '.$prfx.'customers.created
      FROM '.$prfx.'customers_types, '.$prfx.'customers,
        '.$prfx.'credentials'.$CompTbl.'
      WHERE '.$prfx.'customers_types.uuid = '.$prfx.'customers.type
      AND '.$prfx.'customers.uuid = '.$prfx.'credentials.customer
      AND '.$prfx.'credentials.deleted = '.$Deleted.'
      '.$Searcher.' ORDER BY '.$SortBy;
    $Data = $wpdb->get_results($Query);
    $i = 0;
    foreach ($Data as &$Row) {
      $DataSet[$i]['uuid'] = $Row->uuid;
      $DataSet[$i]['name'] = $Row->firstname.' '.$Row->lastname;
      $DataSet[$i]['created'] = date("d/m/Y", strtotime($Row->created));
      $DataSet[$i]['type'] = $Row->name;
      $DataSet[$i]['active'] = 'No';
      if ($Row->active === '1') { $DataSet[$i]['active'] = 'Yes'; }
      ++$i;
    }
    return $DataSet;
  }
  public function LoadMultiAccountList() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $SortBy = 'created DESC';
    if ($this->Sorter !== '') { $SortBy = $this->Sorter;}
    $Searcher = '';
    if ($this->SearchString !== '') {
      $Searcher .= ' AND (
        '.$prfx.'companies.name LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'companies.tin LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'companies.website LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'companies.email LIKE "%'.$this->SearchString.'%")';
    }
    $Query = 'SELECT '.$prfx.'companies.*, '.$prfx.'customers.created,
      '.$prfx.'customers_types.name AS multitype
      FROM '.$prfx.'companies,'.$prfx.'customers_types,
        '.$prfx.'customers
      WHERE '.$prfx.'companies.customer = '.$prfx.'customers.uuid
      AND '.$prfx.'customers.type = '.$prfx.'customers_types.uuid
      '.$Searcher.' ORDER BY '.$SortBy;
    $Data = $wpdb->get_results($Query);
    $i = 0;
    foreach ($Data as &$Row) {
      $DataSet[$i]['uuid'] = $Row->customer;
      $DataSet[$i]['name'] = $Row->name;
      $DataSet[$i]['type'] = $Row->multitype;
      $DataSet[$i]['created'] = date("d/m/Y", strtotime($Row->created));
      ++$i;
    }
    return $DataSet;
  }
  public function LoadCustomerData() {
    $Data = '';
    $uuid = $this->Get_UUID();
    if ($uuid !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'credentials.*, '.$prfx.'customers.type,
          '.$prfx.'customers.created,
          '.$prfx.'customers.source, '.$prfx.'customers.ipaddress,
          '.$prfx.'customers_types.name, '.$prfx.'customers_types.multcred
        FROM '.$prfx.'customers_types, '.$prfx.'customers, '.$prfx.'credentials
        WHERE '.$prfx.'customers_types.uuid = '.$prfx.'customers.type
        AND '.$prfx.'customers.uuid = '.$prfx.'credentials.customer
        AND '.$prfx.'credentials.uuid =  "'.$uuid.'"';
      $Data = $wpdb->get_results($Query);
    }
    return $Data;
  }
  final public function CheckIfIsMain() {
    $IsMain = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT COUNT(*) as rcrds FROM '.$prfx.'customers WHERE main =  "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
         if ($Row->rcrds > 0) { $IsMain = true; }
        }
      }
    }
    return $IsMain;
  }
  public function LoadMultiAccountData() {
    $Data = '';
    $uuid = $this->CustomerUUID;
    if ($uuid !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'companies.*, '.$prfx.'customers.*, '.$prfx.'customers_types.name as multitype
        FROM '.$prfx.'customers_types, '.$prfx.'customers, '.$prfx.'companies
        WHERE '.$prfx.'customers_types.uuid = '.$prfx.'customers.type
        AND '.$prfx.'customers.uuid = '.$prfx.'companies.customer
        AND '.$prfx.'customers.uuid =  "'.$uuid.'"';
      $Data = $wpdb->get_results($Query);
    }
    return $Data;
  }
  public function LoadCustomerAddress() {
    $Data = '';
    $uuid = $this->Get_UUID();
    if ($uuid !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'addresses.*, '.$prfx.'addresses_type.name
        FROM '.$prfx.'addresses_type, '.$prfx.'addresses
        WHERE '.$prfx.'addresses_type.uuid = '.$prfx.'addresses.type
        AND '.$prfx.'addresses.credential = "'.$uuid.'"
        ORDER BY '.$prfx.'addresses.type';
      $Data = $wpdb->get_results($Query);
    }
    return $Data;
  }
  public function LoadCompanyAddress() {
    $Data = '';
    $uuid = $this->Get_UUID();
    if ($uuid !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'addresses.*, '.$prfx.'addresses_type.name
        FROM '.$prfx.'addresses_type, '.$prfx.'addresses, '.$prfx.'credentials
        WHERE '.$prfx.'addresses_type.uuid = '.$prfx.'addresses.type
        AND '.$prfx.'addresses.customer = '.$prfx.'credentials.customer
        AND '.$prfx.'addresses.credential = "MainAddress"
        AND '.$prfx.'credentials.uuid = "'.$uuid.'"
        ORDER BY '.$prfx.'addresses.type';
      $Data = $wpdb->get_results($Query);
    }
    return $Data;
  }
  public function LoadMultiAccountAddress() {
    $Data = '';
    $uuid = $this->CustomerUUID;
    if ($uuid !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'addresses.*, '.$prfx.'addresses_type.name
        FROM '.$prfx.'addresses_type, '.$prfx.'addresses, '.$prfx.'customers
        WHERE '.$prfx.'addresses_type.uuid = '.$prfx.'addresses.type
        AND '.$prfx.'addresses.customer = '.$prfx.'customers.uuid
        AND '.$prfx.'customers.uuid = "'.$uuid.'"
        ORDER BY '.$prfx.'addresses.type';
      $Data = $wpdb->get_results($Query);
    }
    return $Data;
  }
  public function LoadAddressData() {
    $Data = false;
    if ($this->Address !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'addresses WHERE uuid = "'.$this->Address.'"';
      $Data = $wpdb->get_results($Query);
    }
    return $Data;
  }
  public function LoadCustomerContact() {
    $Data = '';
    $uuid = $this->Get_UUID();
    if ($uuid !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'contacts.*, '.$prfx.'contacts_type.name
        FROM '.$prfx.'contacts, '.$prfx.'contacts_type
        WHERE '.$prfx.'contacts_type.uuid = '.$prfx.'contacts.type
        AND '.$prfx.'contacts.credential = "'.$uuid.'"
        ORDER BY '.$prfx.'contacts.type';
      $Data = $wpdb->get_results($Query);
    }
    return $Data;
  }
  public function LoadMultiAccountContact() {
    $Data = '';
    $uuid = $this->CustomerUUID;
    if ($uuid !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'contacts.*, '.$prfx.'contacts_type.name
        FROM '.$prfx.'contacts, '.$prfx.'contacts_type,
          '.$prfx.'credentials, '.$prfx.'customers
        WHERE '.$prfx.'contacts_type.uuid = '.$prfx.'contacts.type
        AND '.$prfx.'contacts.credential = '.$prfx.'credentials.uuid
        AND '.$prfx.'credentials.customer = '.$prfx.'customers.uuid
        AND '.$prfx.'customers.uuid = "'.$uuid.'"
        ORDER BY '.$prfx.'contacts.type';
      $Data = $wpdb->get_results($Query);
    }
    return $Data;
  }
  public function LoadContactData() {
    $Data = false;
    if ($this->Contact !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'contacts WHERE uuid = "'.$this->Contact.'"';
      $Data = $wpdb->get_results($Query);
    }
    return $Data;
  }
  public function GetCustomerTypes() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'customers_types';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function GetCustomerTypesFilter() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Searcher = '';
    if ($this->Company !== '') {
      if ($Searcher === '') { $Searcher .= ' WHERE'; }
        else { $Searcher .= ' AND'; }
      $Searcher .= ' uuid IN (SELECT type FROM '.$prfx.'customers WHERE uuid = "'.$this->Company.'")';
    }
    if ($this->Lastlogin !== '') {
      if ($Searcher === '') { $Searcher .= ' WHERE'; }
        else { $Searcher .= ' AND'; }
      $Searcher .= ' uuid IN (SELECT '.$prfx.'customers.type FROM '.$prfx.'customers,'.$prfx.'credentials
        WHERE '.$prfx.'credentials.customer = '.$prfx.'customers.uuid
        AND '.$prfx.'credentials.lastlogin = "'.$this->Lastlogin.'")';
    }
    $Query = 'SELECT * FROM '.$prfx.'customers_types'.$Searcher;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function GetCompaniesDataSet() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'companies';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function GetCompaniesFilter() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Searcher = '';
    if ($this->Type !== '') {
      if ($Searcher === '') { $Searcher .= ' WHERE'; }
        else { $Searcher .= ' AND'; }
      $Searcher .= ' customer IN (SELECT uuid FROM '.$prfx.'customers WHERE type = "'.$this->Type.'")';
    }
    if ($this->Lastlogin !== '') {
      if ($Searcher === '') { $Searcher .= ' WHERE'; }
        else { $Searcher .= ' AND'; }
      $Searcher .= ' customer IN (SELECT '.$prfx.'customers.uuid FROM '.$prfx.'customers,'.$prfx.'credentials
        WHERE '.$prfx.'credentials.customer = '.$prfx.'customers.uuid
        AND '.$prfx.'credentials.lastlogin = "'.$this->Lastlogin.'")';
    }
    $Query = 'SELECT * FROM '.$prfx.'companies'.$Searcher;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function GetAddressTypeDataSet() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'addresses_type';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function GetCreatedDates() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'customers ORDER BY created DESC';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function GetCreatedDatesFilter() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Searcher = '';
    if ($this->Company !== '') {
      if ($Searcher === '') { $Searcher .= ' WHERE'; }
        else { $Searcher .= ' AND'; }
      $Searcher .= ' uuid IN (SELECT customer FROM '.$prfx.'companies WHERE customer = "'.$this->Company.'")';
    }
    if ($this->Type !== '') {
      if ($Searcher === '') { $Searcher .= ' WHERE'; }
        else { $Searcher .= ' AND'; }
      $Searcher .= ' uuid IN (SELECT uuid FROM '.$prfx.'customers WHERE type = "'.$this->Type.'")';
    }
    $Query = 'SELECT * FROM '.$prfx.'customers '.$Searcher.' ORDER BY created DESC';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function OrderHistoryFound() {
    $Found = false;
    $uuid = $this->Get_UUID();
    if ($uuid !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'orders.*
          FROM '.$prfx.'orders, '.$prfx.'customers, '.$prfx.'credentials
          WHERE '.$prfx.'orders.customer = '.$prfx.'customers.uuid
          AND '.$prfx.'customers.uuid = '.$prfx.'credentials.customer
          AND '.$prfx.'credentials.uuid = "'.$uuid.'"';
    }
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      $Found = true;
    }
    return $Found;
  }
  public function DeleteSingleAcount() {
    $JobDone = false;
    $UUID = $this->Get_UUID();
    if ($UUID !=='') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'credentials
          SET active= FALSE, deleted = TRUE
          WHERE uuid = %s',
        $UUID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RestoreSingleAcount() {
    $JobDone = false;
    $UUID = $this->Get_UUID();
    if ($UUID !=='') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'credentials
          SET deleted = FALSE
          WHERE uuid = %s',
        $UUID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function GetNewCustomerID() {
    return $this->GetNewUUID();
  }
  public function CheckIfMain() {
    $MainFound = false;
    if ($this->Get_UUID() !== '') {
    global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'customers WHERE main = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $MainFound = true;
        }
      }
    }
    return $MainFound;
  }
  public function CheckTypeIfMultiCred() {
    $MultiCred = false;
    if ($this->Type !== '') {
    global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'customers_types
        WHERE uuid = "'.$this->Type.'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $MultiCred = $Row->multcred;
        }
      }
    }
    return $MultiCred;
  }
  public function UsernameInUse() {
    $IsUsed = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'credentials
      WHERE username = "'.$this->Username.'"
      AND uuid != "'.$this->Get_UUID().'"';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        if ($Row->uuid !== $this->Get_UUID()) {
          $IsUsed = true;
        }
      }
    }
    return $IsUsed;
  }
  public function LoadTypeCompanies() {
    $DataSet = false;
    if ($this->Type !== '') {
    global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'customers.*, '.$prfx.'companies.*
        FROM '.$prfx.'customers_types, '.$prfx.'customers,
          '.$prfx.'companies,'.$prfx.'credentials
        WHERE '.$prfx.'customers_types.uuid = '.$prfx.'customers.type
        AND '.$prfx.'customers.uuid = '.$prfx.'companies.customer
        AND '.$prfx.'customers.main = '.$prfx.'credentials.uuid
        AND '.$prfx.'credentials.deleted = FALSE
        AND '.$prfx.'customers_types.uuid = "'.$this->Type.'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  private function GetCustomerFromMain() {
    $Customer = false;
    $uuid = $this->Get_UUID();
    if ($uuid !== '') {
    global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'customers WHERE main = "'.$uuid.'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Customer = $Row->uuid;
        }
      }
    }
    return $Customer;
  }
  public function InsertNewCustomer() {
    $JobDone = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Credential = $this->Get_UUID();
    $Created = date("Y-m-d H:i:s");
    $Customer = $this->GetNewUUID();
    $GUUID = $this->GetNewUUID();
      if (($this->NewCompany) && ($this->Company === $Credential)) {
        $this->Company = $Customer;
      } elseif (!$this->CheckTypeIfMultiCred()) {
        $this->Company = $Customer;
      }
    if ($Customer === $this->Company) {
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'customers (uuid,type,created,source,ipaddress,main)
          VALUES (%s,%s,%s,%s,%s,%s)',
        $Customer,
        $this->Type,
        $Created,
        $this->Source,
        $this->IPAddress,
        $Credential
      );
      if (false === $wpdb->query($Query)) { $JobDone = 'Problems while Inserting to customers table<br>'.$TQ; }
      if ($this->NewCompany) {
        if ($JobDone === '') {
          $Query = $wpdb->prepare(
            'INSERT INTO '.$prfx.'companies (customer,sector,name,tin,website,email)
              VALUES (%s,%s,%s,%s,%s,%s)',
            $this->Company,
            $this->Sector,
            $this->Name,
            $this->Tin,
            $this->WebSite,
            $this->EMail
          );
          if (false === $wpdb->query($Query)) { $JobDone = 'Problems while Inserting to companies table'; }
        }
      }
    }
    if ($JobDone === '') {
      if ($this->Password !== 'EMPTY') {
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'credentials
            (uuid,customer,guuid,username,password,encryption,hashkey,title,firstname,lastname,active,lastlogin,loginip,termsnotice,deleted)
            VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,"2000-01-01 12:00:00","0")',
          $Credential,
          $this->Company,
          $GUUID,
          $this->Username,
          $this->Password,
          $this->Encryption,
          $this->Hashkey,
          $this->Title,
          $this->Firstname,
          $this->Lastname,
          $this->Active,
          $Created,
          $_SERVER['REMOTE_ADDR']
        );
        if (false === $wpdb->query($Query)) { $JobDone = 'Problems while Inserting to credentials table'; }
      } else {
         $JobDone = 'Password was empty !?!';
      }
    }
    return $JobDone;
  }
  public function UpdateCustomerRecord() {
    $JobDone = false;
    $Error = false;
    if ($this->Type !== '') {
      if (!$this->UpdateCustomerType()) { $Error = true; }
    }
    $SetStr = '';
    $Sep = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'UPDATE '.$prfx.'credentials SET ';
    if ($this->Guuid !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'guuid = "'.$this->Guuid.'"';
    }
    if ($this->Company !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'customer = "'.$this->Company.'"';
    }
    if ($this->Username !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'username = "'.$this->Username.'"';
    }
    if ($this->Password !== 'EMPTY') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'password = "'.$this->Password.'"';
    }
    if ($this->Encryption !== 'BLOWFISH') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'encryption = "'.$this->Encryption.'"';
    }
    if ($this->Hashkey !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'hashkey = "'.$this->Hashkey.'"';
    }
    if ($this->Title !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'title = "'.$this->Title.'"';
    }
    if ($this->Firstname !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'firstname = "'.$this->Firstname.'"';
    }
    if ($this->Lastname !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'lastname = "'.$this->Lastname.'"';
    }
    if ($this->Active !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'active = "'.$this->Active.'"';
    }
    if ($this->Lastlogin !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'lastlogin = "'.$this->Lastlogin.'"';
    }
    if ($this->LoginIP !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'loginip = "'.$this->LoginIP.'"';
    }
    if ($SetStr != '') {
      $Query .= $SetStr.' WHERE uuid = "'.$this->Get_UUID().'"';
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    if (!$Error) { $JobDone = true; }
    return $JobDone;
  }
  public function UpdateMultiAccountRecord() {
    $JobDone = '';
    $Error = false;
    if ($this->CustomerUUID === '') { $Error = true; }
    if ($this->Sector === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Tin === '') { $Error = true; }
    if ($this->WebSite === '') { $Error = true; }
    if ($this->EMail === '') { $Error = true; }
    if (!$Error){
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'companies
         SET sector = %s,name = %s,tin = %s,website = %s,email = %s
         WHERE customer = %s',
        $this->Sector,
        $this->Name,
        $this->Tin,
        $this->WebSite,
        $this->EMail,
        $this->CustomerUUID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  private function UpdateCustomerType() {
    $JobDone = false;
    $this->MultiCred = $this->CheckTypeIfMultiCred();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'customers WHERE main = "'.$this->Get_UUID().'"';
    $DataSet = $wpdb->get_results($Query);
    $Customer = '';
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $Customer = $Row->uuid;
        $OldType = $Row->type;
      }
    }
    if ($OldType !== $this->Type) {
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'customers SET type = %s WHERE uuid = %s',
        $this->Type,
        $Customer
      );
      if (false === $wpdb->query($Query)) {
        $JobDone = 'Problems while updating type';
      }
      if ($this->MultiCred) {
        $Empty = false;
        if ($this->Sector === '') { $Empty = true; }
        if ($this->Name === '') { $Empty = true; }
        if ($this->Tin === '') { $Empty = true; }
        if ($this->WebSite === '') { $Empty = true; }
        if ($this->EMail === '') { $Empty = true; }
        if (!$Empty) {
          $Query = $wpdb->prepare(
            'DELETE FROM '.$prfx.'companies WHERE customer = %s',
            $Customer
          );
          $wpdb->query($Query);
          $Query = $wpdb->prepare(
            'INSERT INTO '.$prfx.'companies (customer,sector,name,tin,website,email)
              VALUES (%s,%s,%s,%s,%s,%s)',
            $Customer,
            $this->Sector,
            $this->Name,
            $this->Tin,
            $this->WebSite,
            $this->EMail
          );
          $wpdb->query($Query);
          $this->Company = $Customer;
        }
      } else {
        $this->Company = $Customer;
      }
    }
    return $JobDone;
  }
  public function LoadSelectorTypes($Table) {
    $DataSet = false;
    if (($Table === 'addresses') || ($Table === 'contacts')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.$Table.'_type';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function UpdateAddressData() {
    $JobDone = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($this->NewEntry) {
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'addresses (uuid,customer,credential,type,fullname,country,city,region,street,zipcode,phone,notes,deleted)
          VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,"0")',
        $this->Address,
        $this->CustomerUUID,
        $this->Get_UUID(),
        $this->AddressType,
        $this->Fullname,
        $this->Country,
        $this->City,
        $this->Region,
        $this->Street,
        $this->Zipcode,
        $this->Phone,
        $this->Notes
      );
    } else {
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'addresses SET type = %s,fullname = %s,country = %s,
          city = %s,region = %s,street = %s,zipcode = %s,phone = %s,notes = %s
          WHERE uuid = %s',
        $this->AddressType,
        $this->Fullname,
        $this->Country,
        $this->City,
        $this->Region,
        $this->Street,
        $this->Zipcode,
        $this->Phone,
        $this->Notes,
        $this->Address
      );
    }
    if (false !== $wpdb->query($Query)) { $JobDone = true; }
    return $JobDone;
  }
  public function UpdateContactData() {
    $JobDone = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($this->NewEntry) {
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'contacts (uuid,credential,type,value,validated,deleted)
          VALUES (%s,%s,%s,%s,%s,"0")',
        $this->Contact,
        $this->Get_UUID(),
        $this->ContactType,
        $this->Value,
        $this->Validated
      );
    } else {
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'contacts SET type = %s,value = %s,validated = %s
          WHERE uuid = %s',
        $this->ContactType,
        $this->Value,
        $this->Validated,
        $this->Contact
      );
    }
    if (false !== $wpdb->query($Query)) { $JobDone = true; }
    return $JobDone;
  }
  public function DeleteCustomerAddress() {
    $JobDone = false;
    $Error = false;
    if ($this->Address === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'orders
        WHERE billingaddr = "'.$this->Address.'"
        OR sendingaddr = "'.$this->Address.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) { $Error = true; }
      if (!$Error) {
        $Query = $wpdb->prepare(
          'DELETE FROM '.$prfx.'addresses WHERE uuid = %s',
          $this->Address
        );
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
  public function DeleteCustomerContact() {
    $JobDone = false;
    $Error = false;
    if ($this->Contact === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'contacts WHERE uuid = %s',
        $this->Contact
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function LoadGroupsList() {
    $SortBy = 'name';
    if ($this->Sorter !== '') { $SortBy = $this->Sorter;}
    if ($this->SearchString !== '') {
      $Searcher .= ' WHERE name LIKE "%'.$this->SearchString.'%"';
    }
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'groups '.$Searcher.' ORDER BY '.$SortBy;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadCustomerOrderHistory() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
    $Query = '
      SELECT '.$prfx.'orders.*, '.$prfx.'orders_status_type.name
      FROM '.$prfx.'orders, '.$prfx.'orders_status, '.$prfx.'orders_status_type
      WHERE '.$prfx.'orders.uuid = '.$prfx.'orders_status.orders
      AND '.$prfx.'orders_status.active = TRUE
      AND '.$prfx.'orders_status.status = '.$prfx.'orders_status_type.uuid
      AND '.$prfx.'orders.customer = "'.$this->Get_UUID().'"
      ORDER BY '.$prfx.'orders.created DESC';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
}
