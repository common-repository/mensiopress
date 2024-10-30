<?php
class mensio_store extends mensio_core_db {
  private $ThemeLang;
  private $ThmActiveLang;
  private $AdminLang;
  private $Name;
  private $Country;
  private $tzone;
  private $City;
  private $Street;
  private $Number;
  private $Phone;
  private $Fax;
  private $Email;
  private $GglAnalytics;
  private $GglMap;
  private $Logo;
  private $Currency;
  private $CurrUpdate;
  private $Barcode;
  private $OrderSerial;
  private $TblRows;
  private $NotifTime;
  private $Metrics;
  private $TermID;
  private $Terms;
  private $WPUser;
  private $Page;
  private $Access;
  private $Host;
  private $SMTPAuth;
  private $SMTPSecure;
  private $Username;
  private $Password;
  private $Port;
  private $From;
  private $FromName;
  private $MailsPerMinute;
  private $Template;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->ThemeLang ='';
    $this->AdminLang = '';
    $this->Name = '';
    $this->ThmActiveLang = '';
    $this->Country = '';
    $this->tzone = '';
    $this->City = '';
    $this->Street = '';
    $this->Number = '';
    $this->Phone = '';
    $this->Fax = '';
    $this->Email = '';
    $this->GglAnalytics = '';
    $this->GglMap = '';
    $this->Logo = '';
    $this->Currency = '';
    $this->CurrUpdate = '';
    $this->Barcode = '';
    $this->OrderSerial = '';
    $this->TblRows = '';
    $this->NotifTime = '';
    $this->Metrics = '';
    $this->Terms = '';
    $this->WPUser = '';
    $this->Page = '';
    $this->Access = '';
    $this->Host = '';
    $this->SMTPAuth = '';
    $this->SMTPSecure = '';
    $this->Port = '';
    $this->Username = '';
    $this->Password = '';
    $this->From = '';
    $this->FromName = '';
    $this->MailsPerMinute = '';
    $this->Template = '';
  }
  final public function Set_ThemeLang($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->ThemeLang = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_AdminLang($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->AdminLang = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_ThmActiveLang($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        if ($this->ThmActiveLang === '') { $this->ThmActiveLang = $ClrVal; }
          else { $this->ThmActiveLang .= ';'.$ClrVal; }
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Name($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' ,.-');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Name = addslashes($ClrVal);
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
  final public function Set_TZone($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN','/');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->tzone = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_City($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' ,.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->City = addslashes($ClrVal);
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
  final public function Set_Number($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' ,.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Number = addslashes($ClrVal);
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
        $this->Phone = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Fax($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Fax = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Email($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN','@-_.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Email = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_GglAnalytics($Value) {
		$SetOk = false;
    if (($Value !== '') && ($Value !== 'NOANALYTICS')) {
      $Check = true;
      if (strpos($Value, 'GoogleAnalyticsObject') === false) { $Check = false; }
      if (strpos($Value, 'www.google-analytics.com') === false) { $Check = false; }
      if ($Check) {
        $this->GglAnalytics = addslashes($Value);
        $SetOk = true;
      }
    } else {
      $this->GglAnalytics = 'NOANALYTICS';
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_GglMap($Value) {
		$SetOk = false;
    if ($Value !== 'NOMAP') {
      $Check = true;
      if (strpos($Value, 'https://www.google.com/maps') === false) { $Check = false; }
      if ($Check) {
        $this->GglMap = addslashes($Value);
        $SetOk = true;
      }
    } else {
      $this->GglMap = 'NOMAP';
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Logo($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN','-_.:/');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $ClrVal = str_replace(get_site_url().'/', '', $ClrVal);
        $this->Logo = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Currency($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Currency = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_CurrUpdate($Value) {
		$SetOk = false;
    if (($Value === 0) || ($Value === '0') || ($Value === false)) {
        $this->CurrUpdate = '0';
        $SetOk = true;
    } else {
        $this->CurrUpdate = '1';
        $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Barcode($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN','-');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Barcode = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_OrderSerial($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN','-');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->OrderSerial = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_TblRows($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->TblRows = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_NotifTime($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->NotifTime = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Metrics($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN',':;');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Metrics = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_TermID($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->TermID = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Terms($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $this->Terms = addslashes($Value);
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_WPUser($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' _.\-@');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Query = 'SELECT * FROM '.$prfx.'users WHERE user_login = "'.$ClrVal.'"';
        $DataSet = $wpdb->get_results($Query);
        if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
          foreach ($DataSet as $Row) {
            $this->WPUser = $Row->ID;
            $SetOk = true;
          }
        }
      }
    }
		return $SetOk;
	}
  final public function Set_Page($Value) {
		$SetOk = false;
    switch ($Value) {
      case 'products': case 'customers': case 'orders':
      case 'marketing': case 'reports': case 'design':
      case 'settings': case 'system':
        $this->Page = $Value;
        $SetOk = true;
        break;
    }
		return $SetOk;
	}
  final public function Set_Access($Value) {
		$SetOk = false;
    if (($Value === 1) || ($Value === '1') || ($Value === true)) {
      $this->Access = '1';
      $SetOk = true;
    } else {
      $this->Access = '0';
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Host($Value) {
		$SetOk = false;
    $ClrVal = $this->ClearValue($Value,'EN','_.-/:');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $this->Host = $ClrVal;
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_SMTPAuth($Value) {
		$SetOk = false;
    if (($Value === 1) || ($Value === '1') || ($Value === true)) {
      $this->SMTPAuth = '1';
      $SetOk = true;
    } else {
      $this->SMTPAuth = '0';
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_SMTPSecure($Value) {
		$SetOk = false;
    if (($Value === 'ssl') || ($Value === 'tls')) {
      $this->SMTPSecure = $Value;
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Port($Value) {
		$SetOk = false;
    $ClrVal = $this->ClearValue($Value,'NM');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $this->Port = $ClrVal;
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Username($Value) {
		$SetOk = false;
    $ClrVal = $this->ClearValue($Value,'EN','_.-/:@');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $this->Username = $ClrVal;
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Password($Value) {
		$SetOk = false;
    $ClrVal = $this->ClearValue($Value,'EN');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $this->Password = $ClrVal;
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_From($Value) {
		$SetOk = false;
    $ClrVal = $this->ClearValue($Value,'EN','_.-/:@');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $this->From = $ClrVal;
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_FromName($Value) {
		$SetOk = false;
    $ClrVal = $this->ClearValue($Value,'AN',' ');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $this->FromName = $ClrVal;
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_MailsPerMinute($Value) {
		$SetOk = false;
    $ClrVal = $this->ClearValue($Value,'NM');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      if (($ClrVal > 0) && ($ClrVal < 11)) {
        $this->MailsPerMinute = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Template($Value) {
		$SetOk = false;
    switch ($Value) {
      case 'Sales': case 'Status': case 'Ticket':
      case 'Register': case 'PswdConfirm': case 'GeneralMail':
        $this->Template = $Value;
        $SetOk = true;
        break;
    }
		return $SetOk;
	}
  public function CheckTblRows() {
    $RtrnMsg = 'OK';
    if ($this->TblRows !== '') {
      if ($this->TblRows < 0 ) {
        $RtrnMsg = 'Value of Rows MUST BE a positive number';
      }
      if ($this->TblRows > 100 ) {
        $RtrnMsg = 'Displaying more than 100 rows per table page may drop respond times to unuseable levels. Please select a number between 1 - 100';
      }
    } else {
      $RtrnMsg = 'No value found';
    }
    return $RtrnMsg;
  }
  public function LoadStoreData() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'store';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function GetNewID() {
    return $this->GetNewUUID();
  }
  public function UpdateStoreData() {
    $JobDone = false;
    $FldQuery = '';
    if ($this->Get_UUID() !='' ) {
      if ($this->ThemeLang !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'themelang = "'.$this->ThemeLang.'"';
      }
      if ($this->ThmActiveLang !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'thmactivelang = "'.$this->ThmActiveLang.'"';
      }
      if ($this->AdminLang !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'adminlang = "'.$this->AdminLang.'"';
      }
      if ($this->Name !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'name = "'.$this->Name.'"';
      }
      if ($this->Country !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'country = "'.$this->Country.'"';
      }
      if ($this->tzone !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'tzone = "'.$this->tzone.'"';
      }
      if ($this->City !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'city = "'.$this->City.'"';
      }
      if ($this->Street !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'street = "'.$this->Street.'"';
      }
      if ($this->Number !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'number = "'.$this->Number.'"';
      }
      if ($this->Phone !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'phone = "'.$this->Phone.'"';
      }
      if ($this->Fax !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'fax = "'.$this->Fax.'"';
      }
      if ($this->Email !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'email = "'.$this->Email.'"';
      }
      if ($this->GglAnalytics !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'gglstats = "'.$this->GglAnalytics.'"';
      }
      if ($this->GglMap !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'gglmap = "'.$this->GglMap.'"';
      }
      if ($this->Logo !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'logo = "'.$this->Logo.'"';
      }
      if ($this->Currency !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'currency = "'.$this->Currency.'"';
      }
      if ($this->CurrUpdate !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'update_currency = "'.$this->CurrUpdate.'"';
      }
      if ($this->Barcode !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'barcode = "'.$this->Barcode.'"';
      }
      if ($this->OrderSerial !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'orderserial = "'.$this->OrderSerial.'"';
      }
      if ($this->TblRows !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'tblrows = "'.$this->TblRows.'"';
      }
      if ($this->NotifTime !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'notiftime = "'.$this->NotifTime.'"';
      }
      if ($this->Metrics !== '') {
        if ($FldQuery !== '') { $FldQuery .= ', '; }
        $FldQuery .= 'metrics = "'.$this->Metrics.'"';
      }
      if ($FldQuery !== '') {
        global $wpdb;
        $prfx = $wpdb->prefix.'mns_';
        $Query = $wpdb->prepare(
          'UPDATE '.$prfx.'store SET '.$FldQuery.' WHERE uuid = %s',
          $this->Get_UUID()
        );
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
  public function LoadStoreTermsOfUse($last=false) {
    $DataSet = array();
    $limit = '';
    if ($last) { $limit = 'LIMIT 1'; }
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'store_terms
      WHERE store = "'.$this->Get_UUID().'"
      ORDER BY editdate DESC '.$limit;
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadStoreTermsOfUseData() {
    $DataSet = array();
    if ($this->TermID !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'store_terms WHERE uuid = "'.$this->TermID.'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadStoreMainCurrency() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT '.$prfx.'currencies_codes.*
      FROM '.$prfx.'currencies_codes, '.$prfx.'store
      WHERE '.$prfx.'currencies_codes.uuid = '.$prfx.'store.currency';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function AddNewStoreTerms() {
    $JobDone = false;
    $Error = false;
    $Date = date("Y-m-d H:i:s");
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->TermID === '') { $Error = true; }
    if ($this->Terms === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'store_terms (uuid,store,useterms,editdate,published,active)
          VALUES (%s,%s,%s,%s,"0","0")',
        $this->TermID,
        $this->Get_UUID(),
        $this->Terms,
        $Date
      );
      if (false !== $wpdb->query($Query)) { $JobDone = $Date; }
    }
    return $JobDone;
  }
  public function UpdateStoreTerms() {
    $JobDone = false;
    $Error = false;
    $Date = date("Y-m-d H:i:s");
    if ($this->TermID === '') { $Error = true; }
    if ($this->Terms === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'store_terms SET useterms = %s,editdate = %s WHERE uuid = %s',
        $this->Terms,
        $Date,
        $this->TermID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = $Date; }
    }
    return $JobDone;
  }
  public function UpdatePublishedTermsOfUse() {
    $JobDone = false;
    $Date = date("Y-m-d H:i:s");
    if ($this->TermID !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare('UPDATE '.$prfx.'store_terms SET active = "0"');
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'store_terms SET editdate = %s,published = "1",active = "1" WHERE uuid = %s',
        $Date,
        $this->TermID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = $Date; }
    }
    return $JobDone;
  }
  public function DeleteTermOfUse() {
    $JobDone = false;
    if ($this->TermID !== '') {
      $DataSet = $this->LoadStoreTermsOfUseData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if (!$Row->published) {
            global $wpdb;
            $prfx = $wpdb->prefix.'mns_';
            $Query = $wpdb->prepare(
              'DELETE FROM '.$prfx.'store_terms WHERE uuid = %s',
              $this->TermID
            );
            if (false !== $wpdb->query($Query)) { $JobDone = true; }
          }
        }
      }
    }
    return $JobDone;
  }
  public function LoadUserPermissions() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT '.$prfx.'store_users_permissions.*, '.$wpdb->prefix.'users.user_login
      FROM '.$prfx.'store_users_permissions, '.$wpdb->prefix.'users
      WHERE '.$prfx.'store_users_permissions.store IN (SELECT uuid FROM '.$prfx.'store)
      AND '.$prfx.'store_users_permissions.userID = '.$wpdb->prefix.'users.ID';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function AddNewUserPermissions() {
    $JobDone = false;
    if ($this->WPUser !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'store_users_permissions (userID, store, products, customers, orders, marketing, reports, design, settings, system)
        VALUES (%s,(SELECT uuid FROM '.$prfx.'store),"0","0","0","0","0","0","0","0")',
        $this->WPUser
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateUserPermissions() {
    $JobDone = false;
    $Error = false;
    if ($this->WPUser === '') { $Error = true; }
    if ($this->Page === '') { $Error = true; }
    if ($this->Access === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'store_users_permissions SET '.$this->Page.' = %s WHERE userID = %s',
        $this->Access,
        $this->WPUser
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function LoadUnselectedUsers() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$wpdb->prefix.'users WHERE ID NOT IN
      ( SELECT userID FROM '.$prfx.'store_users_permissions )
    AND ID IN
      ( SELECT user_id FROM '.$wpdb->prefix.'usermeta WHERE meta_key = "wp_user_level" AND meta_value = "10" )';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function UpdateMailSettings($Server) {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($Server === 'sendmail') {
      if ($this->Get_UUID() !== '') {
        $Query = $wpdb->prepare(
          'UPDATE '.$prfx.'store SET mailsettings = "'.$this->MailsPerMinute.'" WHERE uuid = %s',
          $this->Get_UUID()
        );
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
      }
    } else {
      if ($this->Get_UUID() === '') { $Error = true; }
      if ($this->Host === '') { $Error = true; }
      if ($this->SMTPAuth === '') { $Error = true; }
      if ($this->SMTPSecure === '') { $Error = true; }
      if ($this->Port === '') { $Error = true; }
      if ($this->Username === '') { $Error = true; }
      if ($this->Password === '') { $Error = true; }
      if ($this->From === '') { $Error = true; }
      if ($this->FromName === '') { $Error = true; }
      if (!$Error) {
        $Query = 'UPDATE '.$prfx.'store SET mailsettings = "Host:'.$this->Host.';;SMTPAuth:'.$this->SMTPAuth.';;SMTPSecure:'.$this->SMTPSecure.';;Port:'.$this->Port.';;Username:'.$this->Username.';;Password:'.$this->Password.';;From:'.$this->From.';;FromName:'.$this->FromName.';;MailsPerMinute:'.$this->MailsPerMinute.'" WHERE uuid = "'.$this->Get_UUID().'"';
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
  public function LoadStoreMailTemplate() {
    $RtrnData = '';
    if ($this->Template !== '') { $RtrnData = $this->LoadMailTemplate($this->Template); }
    return $RtrnData;
  }
  public function UpdateMailTemplatePost($TemplateHTML) {
    $JobDone = false;
    switch ($this->Template) {
      case 'Sales': case 'Status': case 'Ticket':
      case 'Register': case 'PswdConfirm': case 'GeneralMail':
        $PostID = '';
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Query = 'SELECT * FROM '.$prfx.'posts WHERE post_type = "mensio_mailtemplate" AND post_name = "'.strtolower($this->Template).'"';
        $DataSet = $wpdb->get_results($Query);
        if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
          foreach ($DataSet as $Row) { $PostID = $Row->ID; }
        }
        $CrUser = wp_get_current_user();
        $Date = date('Y-m-d H:i:s');
        if ($PostID === '') {
          $Query = 'INSERT INTO '.$prfx.'posts (post_author, post_date, post_date_gmt, post_content, post_title, post_status, post_name, post_modified, post_modified_gmt, post_type) VALUES ("'.$CrUser->ID.'","'.$Date.'","'.$Date.'","'.addslashes($TemplateHTML).'","'.$this->Template.'","publish","'.$this->Template.'","'.$Date.'","'.$Date.'","mensio_mailtemplate")';
        } else {
          $Query = 'UPDATE '.$prfx.'posts SET post_content = "'.$TemplateHTML.'" WHERE ID = '.$PostID;
        }
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
        break;
    }
    return $JobDone;
  }
}
