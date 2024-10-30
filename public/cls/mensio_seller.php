<?php
class mensio_seller {
  private $VisitID;
  private $Customer;
  private $IPAddress;
  private $UserName;
  private $Hashkey;
  private $Password;
  private $OpSystem;
  private $Browser;
  private $ScreenSize;
  private $SearchString;
  private $BrandID;
  private $CategoryID;
  private $ProductID;
  private $NewCustomerData;
  private $Firstname;
  private $Lastname;
  private $Country;
  private $Region;
  private $City;
  private $Street;
  private $Zipcode;
  private $Phone;
  private $CompanyName;
  private $Tin;
  private $WebSite;
  private $EMail;
  private $Sector;
  private $TtlWeight;
private $OrderCustomer;
private $Serial;
private $BlngAddress;
private $SendAddress;
private $Shipping;
private $NewOrderID;
private $NewOrderProduct;
private $NewOrderAmount;
private $NewOrderPrice;
private $NewOrderDiscount;
private $NewOrderTax;
private $ticketText;
private $ticketID;
private $replyText;
private $ReviewType;
private $ReviewText;
private $ReviewTitle;
private $ReviewValue;
private $UUID;
protected $TimeZone;
  public function __construct() {
    $this->VisitID = '';
    $this->Customer = '';
    $this->IPAddress = '';
    $this->UserName = '';
    $this->Hashkey = '';
    $this->Password = '';
    $this->OpSystem = '';
    $this->Browser = '';
    $this->ScreenSize = '';
    $this->SearchString = '';
    $this->BrandID = '';
    $this->CategoryID = '';
    $this->ProductID = '';
    $this->NewCustomerData = '';
    $this->Lastname = '';
    $this->Firstname = '';
    $this->Country = '';
    $this->Region = '';
    $this->City = '';
    $this->Street = '';
    $this->Zipcode = '';
    $this->Phone = '';
    $this->CompanyName = '';
    $this->Tin = '';
    $this->WebSite = '';
    $this->EMail = '';
    $this->Sector = '';
    $this->TtlWeight = '';
    $this->UUID = '';
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
  protected function GetStoreActiveTimezone() {
    $this->TimeZone = 'DEFAULT';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'store';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $this->TimeZone = $Row->tzone;
      }
    }
  }
  protected function ConvertDateToTimezone($Date,$Format='Y-m-d H:i:s',$TZone=''){
    if ($TZone === '') { $TZone = $this->TimeZone; }
    $Date = new DateTime($Date,new DateTimeZone('UTC'));
    $Date->setTimezone( new DateTimeZone($TZone) );
    return $Date->format($Format);
  }
    final public function Set_UUID($Value) {
            $SetOK = false;
            $ClrVal = $this->ClearUUID($Value);
            if ($ClrVal != false) {
                    $this->UUID = $ClrVal;
                    $SetOK = true;
            }
            return $SetOK;
    }
	protected function Get_UUID() {
    return $this->UUID;
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
  final public function Set_VisitID($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->VisitID = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Customer($Value) {
		$SetOk = false;
    if ($Value !== '') {
      if (substr($Value,0,6) === 'Guest-') {
        $ClrVal = $this->ClearValue($Value,'EN','-.');
        if (mb_strlen($ClrVal) === mb_strlen($Value)) {
          $this->Customer = $ClrVal;
          $SetOk = true;
        }
      } else {
        $ClrVal = $this->ClearUUID($Value);
        if ($ClrVal != false) {
          $this->Customer = $ClrVal;
          $SetOk = true;
        }
      }
    }
		return $SetOk;
	}
	final public function Set_IPAddress($Value) {
		$SetOk = false;
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
		return $SetOk;
	}
	final public function Set_UserName($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = sanitize_email($Value);
      if (is_email($ClrVal)) {
        $this->UserName = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
	final public function Set_OpSystem($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN','- ');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->OpSystem = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
	final public function Set_Browser($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN','- ');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Browser = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
	final public function Set_ScreenSize($Value) {
		$SetOk = false;
    $Value = str_replace(' x ','-',$Value);
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM','-');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->ScreenSize = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
	final public function Set_SearchString($Value) {
		$SetOk = false;
    $Value = mb_ereg_replace('[^\p{L}\p{N}]','%',$Value);
    $ClrVal = $this->ClearValue($Value,'AN','%');
		if (mb_strlen($ClrVal) === mb_strlen($Value)) {
			$this->SearchString = '%'.$ClrVal.'%';
			$SetOk = true;
		}
		return $SetOk;
	}
  final public function Set_ProductID($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->ProductID = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_BrandID($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->BrandID = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_CategoryID($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->CategoryID = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
	final public function Set_Password($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $this->Password = $Value;
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_NewCustomerData($Value) {
		$SetOk = false;
    $this->NewCustomerData = '';
    $JSONData = json_decode($Value,true);
    if (json_last_error() === JSON_ERROR_NONE) {
      $this->NewCustomerData = $JSONData;
			$SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_NewPassword($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = password_hash($Value, PASSWORD_DEFAULT);
      if ($ClrVal !== '') {
        $this->Hashkey = substr($ClrVal,0,7);
        $this->Password = substr($ClrVal,7, strlen($ClrVal));
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
  final public function Set_City($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' ');
      $ClrVal = $this->ClearValue($ClrVal,'TX');
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
  final public function Set_CompanyName($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' ');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->CompanyName = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
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
  final public function Set_EMail($Value) {
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
  final public function Set_TtlWeight($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM','.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->TtlWeight = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
  }
  final public function Set_TicketText($Value) {
    if ($Value != false) {
        $this->ticketText = $Value;
    }
    return $Value;
  }
  final public function Set_TicketID($Value) {
    if ($Value != false) {
        $this->ticketID = $Value;
    }
    return $Value;
  }
  final public function Set_TicketReply($Value) {
      if ($Value != false) {
        $this->replyText = $Value;
      }
    return $Value;
  }
  final public function Set_ReviewTitle($Value) {
      if ($Value != false) {
        $this->ReviewTitle = $Value;
      }
    return $Value;
  }
  final public function Set_ReviewText($Value) {
      if ($Value != false) {
        $this->ReviewText = $Value;
      }
    return $Value;
  }
  final public function Set_ReviewType($Value) {
      if ($Value != false) {
        $this->ReviewType = $Value;
      }
    return $Value;
  }
  final public function Set_ReviewValue($Value) {
      if ($Value != false) {
        $this->ReviewValue = $Value;
      }
    return $Value;
  }
  public function CheckVisitorFromIPAddress() {
    $Data = false;
    if ($this->IPAddress !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT uuid FROM '.$prfx.'mns_credentials WHERE loginip = %s',
        $this->IPAddress
      );
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        if (count($DataSet) === 1) {
          foreach ($DataSet as $Row) { $Data = $Row->uuid; }
        }
      }
    }
    return $Data;
  }
  public function AddIPAddressToHistory() {
    $RtrnData = array('Error'=>true,'Data'=>'');
    $Error = false;
    if ($this->Customer === '') { $Error = true; }
    if ($this->IPAddress === '') { $Error = true; }
    if ($this->OpSystem === '') { $Error = true; }
    if ($this->Browser === '') { $Error = true; }
    if ($this->ScreenSize === '') { $Error = true; }
    if (!$Error){
      global $wpdb;
      $prfx = $wpdb->prefix;
      $SessionID = $this->GetNewID();
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'mns_customers_history
          (uuid,customer,addressip,opsystem,browser,screensize,visitdate)
        VALUES (%s,%s,%s,%s,%s,%s,%s)',
        $SessionID,
        $this->Customer,
        $this->IPAddress,
        $this->OpSystem,
        $this->Browser,
        $this->ScreenSize,
        date("Y-m-d H:i:s")
      );
      if (false !== $wpdb->query($Query)) {
        $RtrnData['Error'] = false;
        $RtrnData['Data'] = $SessionID;
      }
    } else {
      $RtrnData['Data'] = 'One or more required values are empty.<br>The History entry could not be written.';
    }
    return $RtrnData;
  }
  public function CheckIfVisitedPageExists() {
    $RtrnData = false;
    $Error = false;
    if ($this->VisitID === '') { $Error = true; }
    if ($this->ProductID === '') { $Error = true; }
    if (!$Error){
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'mns_customers_history_pages WHERE historyid = %s AND product = %s',
        $this->VisitID,
        $this->ProductID
      );
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) { $RtrnData = true; }
      }
    }
    return $RtrnData;
  }
  public function AddtoHistoryVisitedPage() {
    $RtrnData = array('Error'=>true,'Data'=>'');
    $Error = false;
    if ($this->VisitID === '') { $Error = true; }
    if ($this->ProductID === '') { $Error = true; }
    if (!$Error){
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'mns_customers_history_pages (historyid,product) VALUES (%s,%s)',
        $this->VisitID,
        $this->ProductID
      );
      if (false !== $wpdb->query($Query)) {
        $RtrnData['Error'] = false;
        $RtrnData['Data'] = '';
      } else {
        $RtrnData['Data'] = 'Problem with the sql execution.';
      }
    } else {
      $RtrnData['Data'] = 'One or more required values are empty.';
    }
    return $RtrnData;
  }
  public function GetVisitorInfo() {
    $RtrnData = array('Error' => true, 'Data' => '');
    $Data = array('CustID'=>'','CredID'=>'','Type'=>'','LastLogin'=>'','TermsNotice'=>'');
    if ($this->Customer !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT '.$prfx.'mns_credentials.* , '.$prfx.'mns_customers_types.name
         FROM '.$prfx.'mns_credentials, '.$prfx.'mns_customers, '.$prfx.'mns_customers_types
         WHERE '.$prfx.'mns_credentials.uuid = %s
         AND '.$prfx.'mns_credentials.customer = '.$prfx.'mns_customers.uuid
         AND '.$prfx.'mns_customers.type = '.$prfx.'mns_customers_types.uuid',
        $this->Customer
      );
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Data['CustID'] = $Row->customer;
          $Data['CredID'] = $Row->uuid;
          $Data['Type'] = $Row->name;
          $Data['LastLogin'] = $Row->lastlogin;
          $Data['TermsNotice'] = $Row->termsnotice;
        }
        $RtrnData['Error'] = false;
        $RtrnData['Data'] = $Data;
      }
    }
    return $RtrnData;
  }
  public function GetDefaultThemeLanguage() {
    $RtrnData = array('Error' => true, 'Data' => array());
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT *,`'.$prfx.'mns_languages_codes`.`code` FROM '.$prfx.'mns_store,'.$prfx.'mns_languages_codes where '.$prfx.'mns_languages_codes.uuid='.$prfx.'mns_store.themelang';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $RtrnData['Data']['uuid'] = $Row->themelang;
        $RtrnData['Data']['code'] = $Row->code;
        $RtrnData['Error'] = false;
      }
    }
    return $RtrnData;
  }
  public function GetActiveThemeLanguages() {
    $RtrnData = array('Error' => true, 'Data' => '');
    $thmactivelang = '';
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT * FROM '.$prfx.'mns_store';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $thmactivelang = $Row->thmactivelang;
      }
    }
    $curLang=false;
    if(!empty($_SESSION['MensioThemeLang'])){
        $curLang=$_SESSION['MensioThemeLang'];
    }
    else{
        $get=$this->GetDefaultThemeLanguage();
        $curLang=$get['Data']['uuid'];
    }
    $object=new mnsFrontEndObject();
    $store=$object->mnsFrontEndStoreData();
    $langs=explode(";",$store['languages']);
    $Query = 'SELECT `'.$prfx.'mns_languages_codes`.*,`'.$prfx.'mns_languages_names`.`name`'
            . 'FROM `'.$prfx.'mns_languages_codes`,`'.$prfx.'mns_languages_names`
            where
            `'.$prfx.'mns_languages_codes`.`active`=1 and
            `'.$prfx.'mns_languages_names`.`language`=`'.$prfx.'mns_languages_codes`.`uuid` and
            `'.$prfx.'mns_languages_names`.`tolanguage`="'.$curLang.'"';
    $Query='SELECT
            `'.$prfx.'mns_languages_codes`.*,
            `'.$prfx.'mns_languages_names`.`name`
        FROM
            `'.$prfx.'mns_languages_codes`,
            `'.$prfx.'mns_languages_names`
        where
            `'.$prfx.'mns_languages_codes`.`active`=1 and
            `'.$prfx.'mns_languages_names`.`language`=`'.$prfx.'mns_languages_codes`.`uuid` and
            `'.$prfx.'mns_languages_names`.`tolanguage`=`'.$prfx.'mns_languages_names`.`language`';
    if(!empty($langs)){
        $Query.=' AND (';
        $i=1;
        foreach($langs as $lang){
            $Query.='`'.$prfx.'mns_languages_codes`.`uuid`="'.$lang.'"';
            if($i!=count($langs)){
                $Query.=" OR ";
            }
            $i++;
        }
        $Query.=')';
    }
    $Query.='
        order by `uuid`';
    $RtrnData['Data'] = $wpdb->get_results($Query);
    $RtrnData['Error'] = false;
    return $RtrnData;
  }
  public function SetThemeLanguage($shortcode) {
      if(empty($shortcode)){
          $Result=false;
      }
      else{
            global $wpdb;
            $prfx = $wpdb->prefix;
            $Query='SELECT `uuid` from `'.$prfx.'mns_languages_codes` where `code`="'.$shortcode.'"';
            $Data = $wpdb->get_results($Query);
            foreach($Data as $Row){
                $Result=$Row->uuid;
            }
      }
      return $Result;
  }
  public function GetSearchResults($Limit=false) {
    $RtrnData = array('Error' => false, 'Data' => '');
    $Data = array('Brands' => '', 'Categories' => '', 'Tags'=>'','Products'=>'');
    if ($this->SearchString !== '') {
      $Data['Brands'] = $this->GetSearchBrandsResults($Limit);
      $Data['Categories'] = $this->GetSearchCategoriesResults($Limit);
      $Data['Tags'] = $this->GetSearchTagsResults($Limit);
      $Data['Products'] = $this->GetSearchProductsResults($Limit);
      if (!empty($_GET['mns_keyword']) && substr(urldecode(filter_var($_GET['mns_keyword'])), 0, 1) == '#'){
        $Data['Brands'] = array();
        $Data['Categories'] = array();
        $Data['Tags'] = $this->GetSearchTagsResults($Limit);
        $Data['Products'] = array();
      }
    } else {
      $RtrnData['Error'] = false;
    }
    $RtrnData['Data'] = $Data;
    return $RtrnData;
  }
  private function GetSearchBrandsResults($Limit=false) {
    $DataSet = array();
    if ($this->SearchString !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT DISTINCT '.$prfx.'mns_products.*, '.$prfx.'mns_products_images.file AS MainImage,
          '.$prfx.'mns_products_status.name AS StatusName, '.$prfx.'mns_products_descriptions.name AS ProductName
         FROM '.$prfx.'mns_products, '.$prfx.'mns_products_images, '.$prfx.'mns_brands,
           '.$prfx.'mns_products_categories, '.$prfx.'mns_products_status,
           '.$prfx.'mns_products_descriptions
         WHERE '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_images.product
         AND '.$prfx.'mns_products_images.main = TRUE
         AND '.$prfx.'mns_products.status = '.$prfx.'mns_products_status.uuid
         AND '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_descriptions.product
         AND '.$prfx.'mns_products_descriptions.language = "'.$_SESSION['MensioThemeLang'].'"
         AND '.$prfx.'mns_products.brand = '.$prfx.'mns_brands.uuid
         AND '.$prfx.'mns_brands.name LIKE %s '.$Limit,
        $this->SearchString
      );
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  private function GetBrandData($brandID) {
    $DataSet = array();
    if ($this->SearchString !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT * from `'.$prfx.'mns_brands` where `uuid`= %s',
        $brandID
      );
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  private function GetSearchCategoriesResults($Limit=false) {
    $DataSet = array();
    if ($this->SearchString !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT DISTINCT '.$prfx.'mns_products.*, '.$prfx.'mns_products_images.file AS MainImage,
          '.$prfx.'mns_products_status.name AS StatusName, '.$prfx.'mns_products_descriptions.name AS ProductName
         FROM '.$prfx.'mns_products, '.$prfx.'mns_products_images,
           '.$prfx.'mns_products_categories, '.$prfx.'mns_categories_names,
           '.$prfx.'mns_products_status, '.$prfx.'mns_products_descriptions
         WHERE '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_images.product
         AND '.$prfx.'mns_products_images.main = TRUE
         AND '.$prfx.'mns_products.status = '.$prfx.'mns_products_status.uuid
         AND '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_descriptions.product
         AND '.$prfx.'mns_products_descriptions.language = "'.$_SESSION['MensioThemeLang'].'"
         AND '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_categories.product
         AND '.$prfx.'mns_products_categories.category = '.$prfx.'mns_categories_names.category
         AND '.$prfx.'mns_categories_names.language = "'.$_SESSION['MensioThemeLang'].'"
         AND '.$prfx.'mns_categories_names.name LIKE %s '.$Limit,
        $this->SearchString
      );
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function TranslateCategory(){
      $Result=false;
      if(!empty($this->CategoryID)){
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Query="SELECT `name` from `".$prfx."mns_categories_names` where `language`='".$_SESSION['MensioThemeLang']."'
                  and `category`='".$this->CategoryID."'";
        $DataSet = $wpdb->get_results($Query);
        $Result=$DataSet;
      }
      return $Result;
  }
  private function GetSearchTagsResults($Limit=false) {
    $DataSet = array();
    if ($this->SearchString !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT DISTINCT '.$prfx.'mns_products.*, '.$prfx.'mns_products_images.file AS MainImage,
          '.$prfx.'mns_products_status.name AS StatusName, '.$prfx.'mns_products_descriptions.name AS ProductName
        FROM '.$prfx.'mns_products, '.$prfx.'mns_products_images, '.$prfx.'mns_products_status,
          '.$prfx.'mns_products_descriptions, '.$prfx.'mns_products_tags
        WHERE '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_images.product
        AND '.$prfx.'mns_products_images.main = TRUE
        AND '.$prfx.'mns_products.status = '.$prfx.'mns_products_status.uuid
        AND '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_descriptions.product
        AND '.$prfx.'mns_products_descriptions.language = "'.$_SESSION['MensioThemeLang'].'"
        AND '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_tags.product
        AND '.$prfx.'mns_products_tags.tags LIKE %s '.$Limit,
        $this->SearchString
      );
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  private function GetSearchProductsResults($Limit=false) {
    $DataSet = array();
    if ($this->SearchString !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT DISTINCT '.$prfx.'mns_products.*, '.$prfx.'mns_products_images.file AS MainImage,
          '.$prfx.'mns_products_status.name AS StatusName, '.$prfx.'mns_products_descriptions.name AS ProductName
        FROM '.$prfx.'mns_products, '.$prfx.'mns_products_images, '.$prfx.'mns_products_status,
          '.$prfx.'mns_products_descriptions
        WHERE '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_images.product
        AND '.$prfx.'mns_products_images.main = TRUE
        AND '.$prfx.'mns_products.status = '.$prfx.'mns_products_status.uuid
        AND '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_descriptions.product
        AND '.$prfx.'mns_products_descriptions.language = "'.$_SESSION['MensioThemeLang'].'"
        AND ('.$prfx.'mns_products_descriptions.description LIKE %s
          OR '.$prfx.'mns_products_descriptions.name LIKE %s
          OR '.$prfx.'mns_products_descriptions.notes LIKE %s
          OR '.$prfx.'mns_products.code LIKE %s)'.$Limit,
        $this->SearchString,
        $this->SearchString,
        $this->SearchString,
        $this->SearchString
      );
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadCategoryProductsList($order=false,$Limit=false) {
    $DataSet = array();
    $lang = 'themelang';
    if ($this->CategoryID !== '') {
        if($order=="NAME-A"){$Order="name ASC";}
        elseif($order=="NAME-Z"){$Order="name DESC";}
        elseif($order=="CREATED-A"){$Order="created ASC";}
        elseif($order=="CREATED-Z"){$Order="created DESC";}
        elseif($order=="CHEAP"){$Order="price ASC";}
        elseif($order=="EXPENSIVE"){$Order="price DESC";}
        elseif($order=="RATINGS-A"){$Order="Ratings ASC";}
        elseif($order=="RATINGS-Z"){$Order="Ratings DESC";}
        elseif($order=="AVAILABILITY"){$Order="stock DESC";}
        else{$Order="name ASC";}
        $Order=" order by ".$Order;
        if($Limit==true){
            $Limit=" LIMIT ".$Limit;
        }
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = '
        SELECT
        (SELECT sum(rvalue) FROM '.$prfx.'mns_reviews where '.$prfx.'mns_reviews.product='.$prfx.'mns_products.uuid) AS Ratings,
        '.$prfx.'mns_products.*, '.$prfx.'mns_products_descriptions.description,
          '.$prfx.'mns_products_descriptions.name, '.$prfx.'mns_products_descriptions.notes,
          '.$prfx.'mns_products_images.file, '.$prfx.'mns_brands.name as brandname
        FROM '.$prfx.'mns_products, '.$prfx.'mns_products_categories, '.$prfx.'mns_brands,
          '.$prfx.'mns_products_images, '.$prfx.'mns_products_descriptions, '.$prfx.'mns_store
        WHERE '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_descriptions.product
        AND '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_images.product
        AND '.$prfx.'mns_products.brand = '.$prfx.'mns_brands.uuid
        AND  '.$prfx.'mns_products_images.main = TRUE
        AND '.$prfx.'mns_products_descriptions.language = '.$prfx.'mns_store.'.$lang.'
        AND '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_categories.product
        AND '.$prfx.'mns_products_categories.category LIKE "'.$this->CategoryID.'"'.$Order.$Limit;
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function Status($UUID){
      if($UUID=="StockRelated"){
          return false;
      }
      $DataSet=false;
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query="SELECT `".$prfx."mns_products_status`.`icon`,
                `".$prfx."mns_products_status`.`color`,
                `".$prfx."mns_products_status_descriptions`.`name`
          from `".$prfx."mns_products_status`,`".$prfx."mns_products_status_descriptions` where
              `".$prfx."mns_products_status_descriptions`.`status`='".$UUID."' and `".$prfx."mns_products_status_descriptions`.`language`=
                    '".$_SESSION['MensioThemeLang']."' and `".$prfx."mns_products_status_descriptions`.`status`=`".$prfx."mns_products_status`.`uuid`";
      if($wpdb->get_results($Query)){
          $DataSet = $wpdb->get_results($Query);
          $DataSet = $DataSet[0];
      }
    return $DataSet;
  }
  public function StockStatus($ProductID=false,$Stock=false){
      $Return=false;
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query="SELECT
                `".$prfx."mns_products_stock_status`.`icon`,
                `".$prfx."mns_products_stock_status`.`color`,
                `".$prfx."mns_products_stock_status_descriptions`.`name`,
                `".$prfx."mns_products`.`stock` as `currentStock`
          from
                `".$prfx."mns_products_stock_status`,
                `".$prfx."mns_products_stock_status_descriptions`,
                `".$prfx."mns_products`
          where
                `".$prfx."mns_products_stock_status`.`product`=`".$prfx."mns_products`.`uuid` and
                `".$prfx."mns_products_stock_status`.`product`='".$ProductID."' and
                `".$prfx."mns_products`.`stock`<=`".$prfx."mns_products_stock_status`.`stock` and
                `".$prfx."mns_products_stock_status_descriptions`.`language`='".$_SESSION['MensioThemeLang']."' and
                `".$prfx."mns_products_stock_status_descriptions`.`stock_status`=`".$prfx."mns_products_stock_status`.`uuid`
                order by `".$prfx."mns_products_stock_status`.`stock` ASC
              ";
      if($DataSet = $wpdb->get_results($Query)){
            $Return=$DataSet[0];
      }
      return $Return;
  }
  public function LoadBrandFilters() {
    $DataSet = array();
    if ($this->BrandID !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT '.$prfx.'mns_attributes_values.*, '.$prfx.'mns_attributes.name, '.$prfx.'mns_attributes.visibility
          FROM '.$prfx.'mns_attributes_values, '.$prfx.'mns_attributes, '.$prfx.'mns_products_categories,  '.$prfx.'mns_products
          WHERE '.$prfx.'mns_attributes_values.attribute = '.$prfx.'mns_attributes.uuid
          AND '.$prfx.'mns_attributes.category = '.$prfx.'mns_products_categories.category
          AND '.$prfx.'mns_products_categories.product = '.$prfx.'mns_products.uuid
          AND '.$prfx.'mns_products.brand = %s
          ORDER BY '.$prfx.'mns_attributes_values.attribute',
        $this->BrandID
      );
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadProductAttributeValues() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = 'SELECT '.$prfx.'mns_products_attributes.*,
              '.$prfx.'mns_attributes_values.uuid AS value_uuid,
              '.$prfx.'mns_attributes_values.attribute AS attribute_uuid,
        '.$prfx.'mns_attributes_values.value, '.$prfx.'mns_attributes.name
        FROM '.$prfx.'mns_products_attributes, '.$prfx.'mns_attributes_values, '.$prfx.'mns_attributes
        WHERE '.$prfx.'mns_products_attributes.attribute_value = '.$prfx.'mns_attributes_values.uuid
        AND '.$prfx.'mns_attributes_values.attribute = '.$prfx.'mns_attributes.uuid
        AND '.$prfx.'mns_products_attributes.product = "'.$this->Get_UUID().'"
        ORDER BY '.$prfx.'mns_attributes.name';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function TranslateAttribute($Attribute,$Language){
      $Result=false;
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Query='SELECT `name` from `'.$prfx.'mns_attributes_values`
                  where `attribute`="'.$Attribute.'" and
                  `language`="'.$Language.'"';
        $DataSet = $wpdb->get_results($Query);
        if(!empty($DataSet)){
            $Result=$DataSet[0]->name;
        }
        return $Result;
  }
  public function LoadCategoryBrands() {
    $DataSet=false;
    if ($this->CategoryID !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query =
        '
        SELECT
                DISTINCT ('.$prfx.'mns_products.brand),'.$prfx.'mns_brands.name
        from
                '.$prfx.'mns_products,
            '.$prfx.'mns_products_categories,
            '.$prfx.'mns_brands
        where
                '.$prfx.'mns_products_categories.product='.$prfx.'mns_products.uuid
            and '.$prfx.'mns_products_categories.category LIKE "%'.$this->CategoryID.'%"
            and '.$prfx.'mns_brands.uuid='.$prfx.'mns_products.brand order by '.$prfx.'mns_brands.name';
      $Data = $wpdb->get_results($Query);
      $i=0;
      foreach($Data as $data){
        $DataSet[$i]['uuid']=$data->brand;
        $DataSet[$i]['name']=$data->name;
        $i++;
      }
    }
    return $DataSet;
  }
  public function LoadCategoryFilters() {
    $DataSet = array();
    if ($this->CategoryID !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $variation=false;
      if(MENSIO_FLAVOR=='FREE' && $wpdb->get_var("SHOW TABLES LIKE '".$prfx."mns_products_variations'")){
        $variation="and `".$prfx."mns_products_attributes`.`product` NOT IN (SELECT `variation` from `".$prfx."mns_products_variations` where `variation`=`".$prfx."mns_products_attributes`.`product`)";
      }
      $Query =
        'SELECT
            '.$prfx.'mns_products_attributes.attribute_value as uuid,
            '.$prfx.'mns_attributes.uuid as attribute,
            '.$prfx.'mns_products_attributes.product,
            '.$prfx.'mns_attributes.name,
            '.$prfx.'mns_attributes_names.name as finalName,
            '.$prfx.'mns_attributes_values.value
        FROM
            '.$prfx.'mns_products_attributes,
            '.$prfx.'mns_attributes_values,
            '.$prfx.'mns_attributes,
            '.$prfx.'mns_attributes_names
        where
            '.$prfx.'mns_products_attributes.product IN
            (SELECT '.$prfx.'mns_products_categories.product from '.$prfx.'mns_products_categories where '.$prfx.'mns_products_categories.category LIKE "%'.$this->CategoryID.'%")
            and '.$prfx.'mns_products_attributes.attribute_value='.$prfx.'mns_attributes_values.uuid
            and '.$prfx.'mns_attributes_values.attribute='.$prfx.'mns_attributes.uuid
            and '.$prfx.'mns_attributes_names.attribute='.$prfx.'mns_attributes.uuid
            and '.$prfx.'mns_attributes_names.language="'.$_SESSION['MensioThemeLang'].'"
            '.$variation.'
        order by '.$prfx.'mns_attributes.name,
              '.$prfx.'mns_attributes_values.value';
      $DataSet = $wpdb->get_results($Query);
    }
    else{
      global $wpdb;
      $prfx = $wpdb->prefix;
      $variation=false;
      if(MENSIO_FLAVOR=='FREE' && $wpdb->get_var("SHOW TABLES LIKE '".$prfx."mns_products_variations'")){
      }
      $Query='SELECT
              '.$prfx.'mns_attributes.name,
              '.$prfx.'mns_attributes_values.attribute,
              '.$prfx.'mns_attributes.category,
              '.$prfx.'mns_attributes_values.uuid,
              '.$prfx.'mns_attributes_values.value
        FROM
            '.$prfx.'mns_attributes,
            '.$prfx.'mns_attributes_values
        WHERE
            '.$prfx.'mns_attributes.uuid = '.$prfx.'mns_attributes_values.attribute
        AND '.$prfx.'mns_attributes_values.uuid IN ( SELECT attribute_value FROM '.$prfx.'mns_products_attributes )
                '.$variation.'
              ORDER BY '.$prfx.'mns_attributes.name,value'
              ;
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function GetFilterAttributeID($attributeValueID){
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query='SELECT `attribute` FROM `'.$prfx.'mns_attributes_values` WHERE `uuid`="'.$attributeValueID.'"';
      $DataSet = $wpdb->get_results($Query);
      return $DataSet[0]->attribute;
  }
  function LoadProductRecordData($ForAdmin=true) {
    $DataSet = array();
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      if(!empty($_SESSION['MensioThemeLang'])){
             $langChoose='AND ('.$prfx.'mns_products_descriptions.language = "'.$_SESSION['MensioThemeLang'].'"
                            OR '.$prfx.'mns_products_descriptions.language = '.$prfx.'mns_store.adminlang
                        )';
      }
      $Query = 'SELECT '.$prfx.'mns_products.*, '.$prfx.'mns_products_descriptions.description,
          '.$prfx.'mns_products_descriptions.name, '.$prfx.'mns_products_descriptions.notes
        FROM '.$prfx.'mns_products, '.$prfx.'mns_products_descriptions, '.$prfx.'mns_store
        WHERE '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_descriptions.product
        '.$langChoose.'
        AND '.$prfx.'mns_products.uuid = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
      if(count($DataSet)==0){
        $Query = 'SELECT '.$prfx.'mns_products.*
          FROM '.$prfx.'mns_products
          WHERE '.$prfx.'mns_products.uuid = "'.$this->Get_UUID().'"';
        $DataSet = $wpdb->get_results($Query);
      }
    }
    return $DataSet;
  }
  public function LoadProductWeight($prodID) {
    $Weight = 0;
    global $wpdb;
    $prfx = $wpdb->prefix;
    $DataSet=false;
    $Query = 'SELECT '.$prfx.'mns_attributes_values.value as weight
        FROM '.$prfx.'mns_attributes_values, '.$prfx.'mns_attributes, '.$prfx.'mns_products_attributes
        WHERE '.$prfx.'mns_attributes_values.uuid = '.$prfx.'mns_products_attributes.attribute_value
        AND '.$prfx.'mns_products_attributes.product = "'.$prodID.'"
        AND '.$prfx.'mns_attributes_values.attribute = '.$prfx.'mns_attributes.uuid
        AND '.$prfx.'mns_attributes.name = "Weight"';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $Weight = $Row->weight;
      }
    }
    return $Weight;
  }
  public function getBarcodes($prodID){
    global $wpdb;
    $prfx = $wpdb->prefix;
    $DataSet=false;
    if(!empty($prodID)){
    $Query = 'SELECT '.$prfx.'mns_products_barcodes.barcode
      FROM '.$prfx.'mns_products_barcodes
      WHERE '.$prfx.'mns_products_barcodes.product="'.$prodID.'"';
    $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadProductTags(){
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query='SELECT `tags` from `'.$prfx.'mns_products_tags` where `product`="'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadProductRecordImages() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = 'SELECT * FROM '.$prfx.'mns_products_images WHERE product = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadRelativeProducts() {
    $DataSet = array();
    if ($this->ProductID !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query='
            SELECT
                `product`
            from `'.$prfx.'mns_products_relations`
                    where
                `relation`=(
                SELECT
                    `relation`
                        from `'.$prfx.'mns_products_relations`
                            where `product`="'.$this->ProductID.'"
                )';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function CheckLoginCredentials() {
    $RtrnData = array('Error' => true, 'Data' => '');
    $Data = array('Customer' => '','UserName' => '','Title' => '',
      'FirstName' => '','LastName' => '','TermsCheck' => true,'OtherIPAddress'=> false);
    $Error = false;
    if ($this->IPAddress === '') { $Error = true; }
    if ($this->UserName === '') { $Error = true; }
    if ($this->Password === '') { $Error = true; }
    if (!$Error){
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'mns_credentials WHERE username = %s',
        $this->UserName
      );
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if (!$Row->deleted) {
            $Hashed = $Row->hashkey.$Row->password;
            if (($this->CheckLoginPassword($this->Password,$Hashed))) {
              $RtrnData['Error'] = false;
              $Data['Customer'] = $Row->customer;
              $Data['Credential'] = $Row->uuid;
              $Data['UserName'] = $Row->username;
              $Data['Title'] = $Row->title;
              $Data['FirstName'] = $Row->firstname;
              $Data['LastName'] = $Row->lastname;
              $Data['TermsCheck'] = $this->CheckTermsNotice($Row->termsnotice);
              $Data['LastLogin'] = $Row->lastlogin;
              $Data['Active'] = $Row->active;
              $this->Set_VisitID($_SESSION['mnsVisitID']);
              $this->Set_Customer($Row->uuid);
              $this->UpdateVisitCustomer();
              if ($Row->loginip !== $this->IPAddress) {
                $Data['OtherIPAddress'] = true;
              }
              $RtrnData['Data'] = $Data;
            }
          }
        }
      }
    }
    return $RtrnData;
  }
  private function CheckLoginPassword($Pswd,$Hashed) {
    $RtrnData = false;
    if ($Pswd !== '') {
      if (password_verify($Pswd, $Hashed)) { $RtrnData = true; }
    }
    return $RtrnData;
  }
  public function GetActiveTermsNotice($use=false) {
    $RtrnData = false;
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT * FROM '.$prfx.'mns_store_terms
       WHERE published = TRUE AND active = TRUE
       ORDER BY editdate DESC LIMIT 1';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $RtrnData = $Row->useterms;
      }
      if($use="check"){
          $RtrnData=$DataSet;
      }
    }
    return $RtrnData;
  }
  public function CheckTermsNotice($TermsDate) {
    $RtrnData = false;
    if ($TermsDate !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = 'SELECT * FROM '.$prfx.'mns_store_terms
         WHERE published = TRUE AND active = TRUE
         ORDER BY editdate DESC LIMIT 1';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if ($Row->editdate > $TermsDate) {
              $RtrnData = array(
                  "uuid"=>$Row->uuid,
                  "date"=>$Row->editdate,
                  "Text"=>$Row->useterms);
              $RtrnData = $Row->useterms;
          }
        }
      }
    }
    return $RtrnData;
  }
  private function UpdateVisitCustomer() {
    $RtrnData = false;
    $Error = false;
    if ($this->VisitID === '') { $Error = true; }
    if ($this->Customer === '') { $Error = true; }
    if (!$Error){
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'mns_customers_history SET customer = %s WHERE uuid = %s',
        $this->Customer,
        $this->VisitID
      );
      if (false !== $wpdb->query($Query)) { $RtrnData = true; }
    }
    return $RtrnData;
  }
  public function GetCustomerLoginHistory() {
    $RtrnData = false;
    $Error = false;
    if ($this->Customer === '') { $Error = true; }
    if (!$Error){
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = 'SELECT * from '.$prfx.'mns_customers_history where customer = "'.$this->Customer.'" order by visitdate DESC';
      if (false !== $wpdb->query($Query)) { $RtrnData = $wpdb->get_results($Query); }
    }
    return $RtrnData;
  }
  public function GetCustomerData($CustomerID,$what) {
    $Data = '';
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = '
        SELECT '.$prfx.'mns_credentials.'.$what.'
        FROM '.$prfx.'mns_credentials
        WHERE `uuid`= "'.$CustomerID.'"';
      $Data = $wpdb->get_results($Query);
      if(!empty($Data[0]->$what)){
          $Data=$Data[0]->$what;
      }
      else{
          $Data=false;
      }
    return $Data;
    }
  public function LoadCompanyCredentials($UUID) {
    $Data = '';
    $uuid = $UUID;
    if ($uuid !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = '
        SELECT '.$prfx.'mns_credentials.*, '.$prfx.'mns_customers.type,
          '.$prfx.'mns_customers.created,
          '.$prfx.'mns_customers.source, '.$prfx.'mns_customers.ipaddress,
          '.$prfx.'mns_customers_types.name, '.$prfx.'mns_customers_types.multcred
        FROM '.$prfx.'mns_customers_types, '.$prfx.'mns_customers, '.$prfx.'mns_credentials
        WHERE '.$prfx.'mns_customers_types.uuid = '.$prfx.'mns_customers.type
        AND '.$prfx.'mns_customers.uuid = '.$prfx.'mns_credentials.customer
        AND '.$prfx.'mns_credentials.deleted = 0
        AND '.$prfx.'mns_credentials.customer =  "'.$uuid.'"';
      $Data = $wpdb->get_results($Query);
    }
    return $Data;
  }
  public function AddNewCompanyCredentials($CustID) {
    $JobDone = false;
    $Error = false;
    $Query = '';
    if (!$this->Set_UserName($this->NewCustomerData['email'])) { $Error = true; }
    if (!$this->Set_Lastname($this->NewCustomerData['lastname'])) { $Error = true; }
    if (!$this->Set_Firstname($this->NewCustomerData['firstname'])) { $Error = true; }
    if (($this->NewCustomerData['title'] !== 'Mr') && ($this->NewCustomerData['title'] !== 'Mrs')) { $Error = true; }
    if (!$this->Set_NewPassword($this->NewCustomerData['password'])) { $Error = true; }
    if (!$Error) {
      $date = date("Y-m-d H:i:s");
      $IPAddress=$_SERVER['REMOTE_ADDR'];
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'mns_credentials
          (uuid,customer,guuid,username,password,encryption,hashkey,title,firstname,lastname,active,lastlogin,loginip,termsnotice,deleted)
          VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,"1",%s,%s,%s,"0")',
        $this->GetNewID(),
        $CustID,
        $this->GetNewID(),
        $this->UserName,
        $this->Password,
        'BLOWFISH',
        $this->Hashkey,
        $this->NewCustomerData['title'],
        $this->Firstname,
        $this->Lastname,
        $date,
        $IPAddress,
        $date
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function GetCustomerTypes() {
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT * FROM '.$prfx.'mns_customers_types WHERE name != "Guest"';
    $RtrnData = $wpdb->get_results($Query);
    return $RtrnData;
  }
  public function GetCountryCodes() {
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT
            '.$prfx.'mns_countries_codes.*,
            '.$prfx.'mns_countries_names.name,
                (SELECT
                    `'.$prfx.'mns_countries_names`.`name` as `originalName`
                 from
                    `'.$prfx.'mns_countries_names`,
                    `'.$prfx.'mns_store`
                 where
                    `'.$prfx.'mns_countries_names`.`country`=`'.$prfx.'mns_countries_codes`.`uuid` AND
                    `'.$prfx.'mns_countries_names`.`language`=`'.$prfx.'mns_store`.`adminlang`
                 ) as `originalName`,
                (SELECT
                    `'.$prfx.'mns_countries_names`.`country` as `originalID`
                 from
                    `'.$prfx.'mns_countries_names`,
                    `'.$prfx.'mns_store`
                 where
                    `'.$prfx.'mns_countries_names`.`country`=`'.$prfx.'mns_countries_codes`.`uuid` AND
                    `'.$prfx.'mns_countries_names`.`language`=`'.$prfx.'mns_store`.`adminlang`
                 ) as `originalID`
      FROM '.$prfx.'mns_countries_codes, '.$prfx.'mns_countries_names
      WHERE '.$prfx.'mns_countries_codes.uuid = '.$prfx.'mns_countries_names.country
      AND '.$prfx.'mns_countries_names.language = "'.$_SESSION['MensioThemeLang'].'"
      ORDER BY '.$prfx.'mns_countries_names.name';
    $RtrnData = $wpdb->get_results($Query);
    return $RtrnData;
  }
  public function GetBusinessSectorTypes($Parent='TopLevel',$Level=0,$RtrnData=array()) {
    $DataSet = $this->LoadBusinessSectorsList($Parent);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      ++$Level;
      foreach ($DataSet as $Row) {
        $Name = '';
        if ($Parent !== 'TopLevel') {
          for ($i = 1; $i < $Level; $i++) { $Name .= '--'; }
        }
        $Name .= $Row->name;
        $RtrnData[$Row->uuid] = $Name;
        $RtrnData = $this->GetBusinessSectorTypes($Row->uuid,$Level,$RtrnData);
      }
    }
    return $RtrnData;
  }
  private function LoadBusinessSectorsList($Parent) {
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT '.$prfx.'mns_sectors_codes.*, '.$prfx.'mns_sectors_names.name
      FROM '.$prfx.'mns_sectors_codes, '.$prfx.'mns_sectors_names
      WHERE '.$prfx.'mns_sectors_codes.uuid = '.$prfx.'mns_sectors_names.sector
      AND '.$prfx.'mns_sectors_names.language = "'.$_SESSION['MensioThemeLang'].'"
      AND '.$prfx.'mns_sectors_codes.parent = "'.$Parent.'"
      ORDER BY '.$prfx.'mns_sectors_names.name';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function GetCountryRegions() {
    $DataSet = array();
    if ($this->Country !== '') {
      $Data = $this->LoadRegionDataSet();
      if ($Data !== '') {
        $Data = explode(';;',$Data);
        $i = 0;
        foreach ($Data as $Row) {
          if ($Row !== '') {
            $RowData = explode('++',$Row);
            $DataSet[$i]['uuid'] = $RowData[0];
            $DataSet[$i]['type'] = $RowData[1];
            $DataSet[$i]['level'] = $RowData[2];
            $DataSet[$i]['name'] = $RowData[3];
            $DataSet[$i]['parent'] = $RowData[4];
          }
          ++$i;
        }
      } else {
        $DataSet = 'NOREGION';
      }
    }
    return $DataSet;
  }
  private function LoadRegionDataSet($Parent='TopLevel') {
    $DataSet = '';
    if ($this->Country !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = '
      SELECT '.$prfx.'mns_regions_codes.*, '.$prfx.'mns_regions_names.name,
        '.$prfx.'mns_regions_types.level
      FROM '.$prfx.'mns_regions_codes, '.$prfx.'mns_regions_names, '.$prfx.'mns_store,
        '.$prfx.'mns_regions_types
      WHERE '.$prfx.'mns_regions_codes.uuid = '.$prfx.'mns_regions_names.region
      AND '.$prfx.'mns_regions_codes.type = '.$prfx.'mns_regions_types.uuid
      AND '.$prfx.'mns_regions_names.language = "'.$_SESSION['MensioThemeLang'].'"
      AND '.$prfx.'mns_regions_codes.country = "'.$this->Country.'"
      AND '.$prfx.'mns_regions_codes.parent = "'.$Parent.'"
      ORDER BY '.$prfx.'mns_regions_names.name';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $DataSet .= $Row->uuid.'++'.$Row->type.'++'.$Row->level.'++'.$Row->name.'++'.$Row->parent.';;';
          $DataSet .= $this->LoadRegionDataSet($Row->uuid);
        }
      }
    }
    return $DataSet;
  }
  public function DisableUser($user_uuid){
    $Error = false;
    if ($user_uuid === '') { $Error = true; }
    if (!$Error){
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = 'UPDATE `'.$prfx.'mns_credentials` set `deleted`=1 where `uuid` = "'.$user_uuid.'"';
      if(!$wpdb->query($Query)){
          $Error=true;
      }
    }
    return $Error;
  }
  public function UpdateUserCredentials($user_uuid,$what,$new){
    $Error = false;
    if ($user_uuid === '') { $Error = true; }
    if (!$Error){
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = 'UPDATE `'.$prfx.'mns_credentials` set `'.$what.'`="'.$new.'" where `uuid` = "'.$user_uuid.'"';
      if(!$wpdb->query($Query)){
          $Error=true;
      }
    }
    return $Error;
  }
  public function VerifyUser($user_uuid=false){
    $RtrnData = false;
    $Error = false;
    if ($user_uuid === '') { $Error = true; }
    if (!$Error){
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT `active` from `'.$prfx.'mns_credentials` where `active`= 0 and `uuid` = %s',
        $user_uuid
      );
      if(!$wpdb->query($Query)){
          $Error=true;
      }
    }
    if (!$Error){
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'UPDATE `'.$prfx.'mns_credentials` SET `active`=1 WHERE uuid = %s and `active`=0',
        $user_uuid
      );
      $Query = $wpdb->prepare(
        'UPDATE `'.$prfx.'mns_contacts` SET `validated`=1 WHERE credential = %s',
        $user_uuid
      );
      if (false !== $wpdb->query($Query)) { $RtrnData = true; }
    }
    return $RtrnData;
  }
  public function GetUserIDByUsername($email=false){
      $Result=false;
      global $wpdb;
      $prfx = $wpdb->prefix;
      if($email){
          $Query='SELECT * from `'.$prfx.'mns_credentials` where `username` = "'.$email.'"';
          $res=$wpdb->get_results($Query);
          $Result=$res[0];
      }
      return $Result;
  }
  public function PasswordResetEmail($arr=array()){
        $to=$arr['to'];
        $subject="Password Reset";
        $message="Your password has been reset to <b>".$arr['password']."</b>";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: '.get_bloginfo().' <site@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
        mail($to,$subject,$message,$headers);
  }
  public function SignUpNewCustomer() {
    $RtrnData = array('Error' => false, 'Data' => '');
    $CType = $this->CheckCustomerType($this->NewCustomerData['user_type']);
    if ($CType['uuid'] === '') {
      $RtrnData['Error'] = true;
      $RtrnData['Data'] .= 'Problem was found with Character type in Visit ID : '.$_SESSION['mnsVisitID'].'</br>';
    } else {
      $IPAddress = $this->GetVisitorIPAddress();
      $Customer = $this->AddNewCustomerMain($CType['uuid'], $IPAddress);
      if ($Customer['CustID'] === '') {
        $RtrnData['Error'] = true;
        $RtrnData['Data'] .= 'Visit ID : '.$_SESSION['mnsVisitID'].'</br>';
        $RtrnData['Data'] .= 'Problem inserting new customer : '.$this->NewCustomerData['lastname'].' '.$this->NewCustomerData['firstname'].'</br>';
      } else {
        if (!$this->AddNewCustomerCredentials($Customer['CustID'],$Customer['CredID'],$IPAddress)) {
          $RtrnData['Error'] = true;
          $RtrnData['Data'] .= 'Visit ID : '.$_SESSION['mnsVisitID'].'</br>';
          $RtrnData['Data'] .= 'Problem inserting new customer credentials for : '.$this->NewCustomerData['lastname'].' '.$this->NewCustomerData['firstname'].'</br>';
        }
        if (!$RtrnData['Error']) {
          if ($CType['multcred']) {
            if (!$this->AddNewCustomerCompany($Customer['CustID'])) {
              $RtrnData['Error'] = true;
              $RtrnData['Data'] .= 'Visit ID : '.$_SESSION['mnsVisitID'].'</br>';
              $RtrnData['Data'] .= 'Problem inserting new Company data for : '.$this->NewCustomerData['lastname'].' '.$this->NewCustomerData['firstname'].'</br>';
            }
          }
          if (!$this->AddNewCustomerAddress($Customer['CustID'],$Customer['CredID'])) {
            $RtrnData['Error'] = true;
            $RtrnData['Data'] .= 'Visit ID : '.$_SESSION['mnsVisitID'].'</br>';
            $RtrnData['Data'] .= 'Problem inserting new Address data for : '.$this->NewCustomerData['lastname'].' '.$this->NewCustomerData['firstname'].'</br>';
          }
          if (!$this->AddNewCustomerContacts($Customer['CustID'],$Customer['CredID'])) {
            $RtrnData['Error'] = true;
            $RtrnData['Data'] .= 'Visit ID : '.$_SESSION['mnsVisitID'].'</br>';
            $RtrnData['Data'] .= 'Problem inserting new Contact data for : '.$this->NewCustomerData['lastname'].' '.$this->NewCustomerData['firstname'].'</br>';
          }
        }
        if (!$RtrnData['Error']) {
          $RtrnData = $this->GetNewCustomerLoginData($Customer['CredID']);
        }
      }
    }
    return $RtrnData;
  }
  public function GuestToUser($CredID=false,$CustID=false,$user_type=false,$password=false,$firstname=false,$lastname=false,$title=false){
    $RtrnData=false;
    if($CredID==true){
        global $wpdb;
        $prfx = $wpdb->prefix;
        $userTypeID=$this->CheckCustomerType($user_type);
        $Query="UPDATE `".$prfx."mns_customers` set `type`='".$userTypeID['uuid']."' where `uuid`='".$CustID."'";
        $wpdb->query($Query);
        $upd=new mensio_customers();
        $upd->Set_UUID($CredID);
        $upd->Set_Firstname($firstname);
        $upd->Set_Lastname($lastname);
        $upd->Set_Title($title);
        $upd->Set_Password($password);
        $upd->UpdateCustomerRecord();
        $Query="UPDATE `".$prfx."mns_credentials` set
                    `termsnotice`='".date("Y-m-d H:i:s")."'
                where `uuid`='".$CredID."'";
        $wpdb->query($Query);
        $Query="UPDATE `".$prfx."mns_addresses` set
                    `deleted`='1'
                where `customer`='".$CustID."'";
        $wpdb->query($Query);
        $Query="UPDATE `".$prfx."mns_contacts` set
                    `deleted`='1'
                where `credential`='".$CredID."'";
        $wpdb->query($Query);
        $RtrnData=true;
    }
    return $RtrnData;
  }
  public function SendWelcomeMail($arr=array()){
        $to = $arr['email'];
        $subject = "Verify User";
        $Store=new mnsFrontEndObject();
        $Store=$Store->mnsFrontEndStoreData();
        $getSignPageID=new mnsGetFrontEndLink();
        $signupPageID=$getSignPageID->SignupPage();
        $UUID=$_SESSION['mnsUser']['Credential'];
        $UUID=MensioEncodeUUID($UUID);
        $ar=array(
            "STORENAME"=>$Store['name'],
            "STOREMAIL"=>$Store['email'],
            "REGISTERCONFIRM"=>site_url()."/?page_id=".$signupPageID."&MensioVerifyUser=". $UUID
            );
        $seller=new mensio_seller();
        if($seller->getMailTemplate("Register",$ar)){
            $message= stripslashes_deep($seller->getMailTemplate("Register",$ar));
        }
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: '.get_bloginfo().' <site@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
        mail($to,$subject,$message,$headers);
        if(empty($message)){
            $message=false;
        }
      return true;
  }
  public function CheckNewCustomerData() {
    $RtrnData = array('Error' => false, 'user_type'=>false, 'email' => false, 'lastname' => false,
      'firstname' => false, 'title' => false, 'password' => false, 'company_name' => false,
      'company_sector' => false, 'company_tin' => false, 'website' => false,
      'company_email' => false, 'country' => false, 'region' => false, 'city' => false,
      'address' => false, 'zip_code' => false, 'phone' => false
    );
    $CType = $this->CheckCustomerType($this->NewCustomerData['user_type']);
    if ($CType['uuid'] === '') {
      $RtrnData['Error'] = true;
      $RtrnData['user_type'] = true;
    } else {
      if (!$this->Set_UserName($this->NewCustomerData['email'])) {
        $RtrnData['Error'] = true;
        $RtrnData['email'] = true;
      }
      if (!$this->Set_Lastname($this->NewCustomerData['lastname'])) {
        $RtrnData['Error'] = true;
        $RtrnData['lastname'] = true;
      }
      if (!$this->Set_Firstname($this->NewCustomerData['firstname'])) {
        $RtrnData['Error'] = true;
        $RtrnData['firstname'] = true;
      }
      if (($this->NewCustomerData['title'] !== 'Mr') && ($this->NewCustomerData['title'] !== 'Mrs')) {
        $RtrnData['Error'] = true;
        $RtrnData['title'] = true;
      }
      if (!$this->Set_Country($this->NewCustomerData['country'])) {
        $RtrnData['Error'] = true;
        $RtrnData['country'] = true;
      }
      if (!$this->Set_Region($this->NewCustomerData['region'])) {
        $RtrnData['Error'] = true;
        $RtrnData['region'] = true;
      }
      if (!$this->Set_City($this->NewCustomerData['city'])) {
        $RtrnData['Error'] = true;
        $RtrnData['city'] = true;
      }
      if (!$this->Set_Street($this->NewCustomerData['address'])) {
        $RtrnData['Error'] = true;
        $RtrnData['address'] = true;
      }
      if (!$this->Set_Zipcode($this->NewCustomerData['zip_code'])) {
        $RtrnData['Error'] = true;
        $RtrnData['zip_code'] = true;
      }
      if (!$this->Set_Phone($this->NewCustomerData['phone'])) {
        $RtrnData['Error'] = true;
        $RtrnData['phone'] = true;
      }
      if ($CType['multcred']) {
        if (!$this->Set_NewPassword($this->NewCustomerData['password'])) {
          $RtrnData['Error'] = true;
          $RtrnData['password'] = true;
        }
        if (!$this->Set_CompanyName($this->NewCustomerData['company_name'])) {
          $RtrnData['Error'] = true;
          $RtrnData['company_name'] = true;
        }
        if (!$this->Set_Sector($this->NewCustomerData['company_sector'])) {
          $RtrnData['Error'] = true;
          $RtrnData['company_sector'] = true;
        }
        if (!$this->Set_Tin($this->NewCustomerData['company_tin'])) {
          $RtrnData['Error'] = true;
          $RtrnData['company_tin'] = true;
        }
        if (!$this->Set_WebSite($this->NewCustomerData['website'])) {
          $RtrnData['Error'] = true;
          $RtrnData['website'] = true;
        }
        if (!$this->Set_EMail($this->NewCustomerData['company_email'])) {
          $RtrnData['Error'] = true;
          $RtrnData['company_email'] = true;
        }
      }
    }
    return $RtrnData;
  }
  public function CheckIfUserNameExists() {
    $RtrnData = false;
    if ($this->UserName !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'mns_credentials WHERE username = %s',
        $this->UserName
      );
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) { $RtrnData = true; }
    }
    return $RtrnData;
  }
  public function GetUserIDsByMail($email=false) {
    $RtrnData=false;
    if(!empty($email)){
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT
            `'.$prfx.'mns_credentials`.`uuid`,
            `'.$prfx.'mns_credentials`.`customer`
        FROM
            '.$prfx.'mns_credentials
        WHERE
            username = %s',
        $email
      );
        $DataSet = $wpdb->get_results($Query);
        if(!empty($DataSet[0])){
            $RtrnData=$DataSet[0];
        }
    }
    return $RtrnData;
  }
  public function GetCustomerType($CredID=false) {
    $RtrnData=false;
    if(!empty($CredID)){
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare('
                SELECT
                    `wp_mns_customers_types`.`name`
                from
                    `wp_mns_customers_types`,
                    `wp_mns_customers`
                where
                    `wp_mns_customers`.`uuid`=%s AND
                    `wp_mns_customers`.`type`=`wp_mns_customers_types`.`uuid`',
              $CredID
              );
        $DataSet = $wpdb->get_results($Query);
        if(!empty($DataSet[0])){
            $RtrnData=$DataSet[0]->name;
        }
    }
    return $RtrnData;
  }
  private function GetVisitorIPAddress() {
    $RtrnData = '';
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = $wpdb->prepare(
      'SELECT * FROM '.$prfx.'mns_customers_history WHERE uuid = %s',
      $_SESSION['mnsVisitID']
    );
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) { $RtrnData = $Row->addressip; }
    }
    return $RtrnData;
  }
  private function CheckCustomerType($CType) {
    $RtrnData = array('uuid'=>'','multcred'=>false);
    $ClrVal = $this->ClearValue($CType,'TX');
    $ClrVal = $this->ClearValue($ClrVal,'EN');
    if (strlen($ClrVal) === strlen($CType)) {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'mns_customers_types WHERE name = %s',
        $ClrVal
      );
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData['uuid'] = $Row->uuid;
          $RtrnData['multcred'] = $Row->multcred;
        }
      }
    }
    return $RtrnData;
  }
  private function AddNewCustomerMain($CType, $IPAddress) {
    $RtrnData = array('CustID'=>'','CredID'=>'');
    $Error = false;
    if ($CType === '') { $Error = true; }
    if ($IPAddress === '') { $Error = true; }
    if (!$Error) {
      $NewID = $this->GetNewID();
      $NewCred = $this->GetNewID();
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'mns_customers (uuid,type,created,source,ipaddress,main)
          VALUES (%s,%s,%s,"S",%s,%s)',
        $NewID,
        $CType,
        date("Y-m-d H:i:s"),
        $IPAddress,
        $NewCred
      );
      if (false !== $wpdb->query($Query)) {
        $RtrnData['CustID'] = $NewID;
        $RtrnData['CredID'] = $NewCred;
      }
    }
    return $RtrnData;
  }
  private function AddNewCustomerCredentials($CustID,$CredID,$IPAddress) {
    $JobDone = false;
    $Error = false;
    $Query = '';
    if (!$this->Set_UserName($this->NewCustomerData['email'])) { $Error = true; }
    if (!$this->Set_Lastname($this->NewCustomerData['lastname'])) { $Error = true; }
    if (!$this->Set_Firstname($this->NewCustomerData['firstname'])) { $Error = true; }
    if (($this->NewCustomerData['title'] !== 'Mr') && ($this->NewCustomerData['title'] !== 'Mrs')) { $Error = true; }
    if (!$this->Set_NewPassword($this->NewCustomerData['password'])) { $Error = true; }
    if (!$Error) {
      $date = date("Y-m-d H:i:s");
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'mns_credentials
          (uuid,customer,guuid,username,password,encryption,hashkey,title,firstname,lastname,active,lastlogin,loginip,termsnotice,deleted)
          VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,"0",%s,%s,%s,"0")',
        $CredID,
        $CustID,
        $this->GetNewID(),
        $this->UserName,
        $this->Password,
        'BLOWFISH',
        $this->Hashkey,
        $this->NewCustomerData['title'],
        $this->Firstname,
        $this->Lastname,
        $date,
        $IPAddress,
        $date
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  private function AddNewCustomerCompany($CustID) {
    $JobDone = false;
    $Error = false;
    if (!$this->Set_CompanyName($this->NewCustomerData['company_name'])) { $Error = true; }
    if (!$this->Set_Sector($this->NewCustomerData['company_sector'])) { $Error = true; }
    if (!$this->Set_Tin($this->NewCustomerData['company_tin'])) { $Error = true; }
    if (!$this->Set_WebSite($this->NewCustomerData['website'])) { $Error = true; }
    if (!$this->Set_EMail($this->NewCustomerData['company_email'])) { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'mns_companies (customer,sector,name,tin,website,email)
          VALUES (%s,%s,%s,%s,%s,%s)',
        $CustID,
        $this->NewCustomerData['company_sector'],
        $this->NewCustomerData['company_name'],
        $this->NewCustomerData['company_tin'],
        $this->NewCustomerData['website'],
        $this->NewCustomerData['company_email']
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  final public function AddNewCustomerAddress($CustID,$CredID,$AddressType=false) {
    $JobDone = false;
    $Error = false;
    if (!$this->Set_UserName($this->NewCustomerData['email'])) { $Error = true; }
    if (!$this->Set_Lastname($this->NewCustomerData['lastname'])) { $Error = true; }
    if (!$this->Set_Firstname($this->NewCustomerData['firstname'])) { $Error = true; }
    if (!$this->Set_Country($this->NewCustomerData['country'])) { $Error = true; }
    if (!$this->Set_Region($this->NewCustomerData['region'])) { $Error = true; }
    if (!$this->Set_City($this->NewCustomerData['city'])) { $Error = true; }
    if (!$this->Set_Street($this->NewCustomerData['address'])) { $Error = true; }
    if (!$this->Set_Zipcode($this->NewCustomerData['zip_code'])) { $Error = true; }
    if (!$this->Set_Phone($this->NewCustomerData['phone'])) { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix;
      if ($AddressType === false){
          $AddressType = '(SELECT uuid FROM '.$prfx.'mns_addresses_type WHERE name = "Billing / Shipping")';
      } else {
          $AddressType = '"'.$AddressType.'"';
      }
      $UUID=$this->GetNewID();
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'mns_addresses
          (uuid,customer,credential,type,fullname,country,city,region,street,zipcode,phone,notes,deleted)
          VALUES (%s,%s,%s,'.$AddressType.',%s,%s,%s,%s,%s,%s,%s,"--","0")',
        $UUID,
        $CustID,
        $CredID,
        $this->Lastname.' '.$this->Firstname,
        $this->Country,
        $this->City,
        $this->Region,
        $this->Street,
        $this->Zipcode,
        $this->Phone
      );
      if (false !== $wpdb->query($Query)) { $JobDone = $UUID; }
    }
    return $JobDone;
  }
  private function AddNewCustomerContacts($CustID,$CredID) {
    $JobDone = false;
    $Error = false;
    if (!$this->Set_EMail($this->NewCustomerData['email'])) { $Error = true; }
    if (!$this->Set_Phone($this->NewCustomerData['phone'])) { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'mns_contacts (uuid,credential,type,value,validated,deleted)
          VALUES (%s,%s,(SELECT uuid FROM '.$prfx.'mns_contacts_type WHERE name = "E-Mail"),%s,"0","0")',
        $this->GetNewID(),
        $CredID,
        $this->EMail
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'mns_contacts (uuid,credential,type,value,validated,deleted)
          VALUES (%s,%s,(SELECT uuid FROM '.$prfx.'mns_contacts_type WHERE name = "Phone"),%s,"0","0")',
        $this->GetNewID(),
        $CredID,
        $this->Phone
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      if (!$Error) { $JobDone = true; }
    }
    return $JobDone;
  }
  final public function AddCustomerContact($CredID,$Type,$Value) {
    $JobDone = false;
    $Error = false;
    switch ($Type) {
      case 'Phone':
      case 'Mobile':
      case 'Fax':
        if (!$this->Set_Phone($Value)) { $Error = true; }
        break;
      case 'E-Mail':
        if (!$this->Set_EMail($Value)) { $Error = true; }
        break;
      default:
        $Error = true;
        break;
    }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'mns_contacts (uuid,credential,type,value,validated)
          VALUES (%s,%s,(SELECT uuid FROM '.$prfx.'mns_contacts_type WHERE name = %s),%s,"1")',
        $this->GetNewID(),
        $CredID,
        $Type,
        $Value
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      if (!$Error) { $JobDone = true; }
    }
    return $JobDone;
  }
  private function GetNewCustomerLoginData($CustID) {
    $RtrnData = array('Error' => true, 'Data' => '');
    $Data = array('Customer' => '','UserName' => '','Title' => '','FirstName' => '',
      'LastName' => '','TermsCheck' => true,'OtherIPAddress'=> false
    );
    if ($CustID !== ''){
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'mns_credentials WHERE uuid = %s',
        $CustID
      );
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if (!$Row->deleted) {
            $RtrnData['Error'] = false;
            $Data['Customer'] = $Row->customer;
            $Data['Credential'] = $Row->uuid;
            $Data['UserName'] = $Row->username;
            $Data['Title'] = $Row->title;
            $Data['FirstName'] = $Row->firstname;
            $Data['LastName'] = $Row->lastname;
            $Data['TermsCheck'] = $this->CheckTermsNotice($Row->termsnotice);
            $Data['LastLogin'] = $Row->lastlogin;
            $this->Set_VisitID($_SESSION['mnsVisitID']);
            $this->Set_Customer($Row->uuid);
            $this->UpdateVisitCustomer();
            if ($Row->loginip !== $this->IPAddress) {
              $Data['OtherIPAddress'] = true;
            }
            $RtrnData['Data'] = $Data;
          }
        }
      }
    }
    return $RtrnData;
  }
  public function GetCountryShippingMethods() {
    $RtrnData = array('Error'=>true, 'Data'=>'');
    if($this->Country !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      if ($this->TtlWeight === '') {
        $Weight = '(SELECT MAX(weight) FROM '.$prfx.'mns_orders_shipping WHERE country = "'.$this->Country.'" GROUP BY courier)';
      } else {
        $Weight = '(SELECT weight FROM '.$prfx.'mns_orders_shipping WHERE weight >= "'.$this->TtlWeight.'" GROUP BY courier)';
      }
      $Query = $wpdb->prepare(
        'SELECT '.$prfx.'mns_couriers_type.*, '.$prfx.'mns_orders_shipping.uuid AS shipping, '.$prfx.'mns_orders_shipping.price
        FROM '.$prfx.'mns_couriers_type, '.$prfx.'mns_orders_shipping
        WHERE '.$prfx.'mns_couriers_type.uuid = '.$prfx.'mns_orders_shipping.courier
        AND '.$prfx.'mns_couriers_type.active = TRUE
        AND '.$prfx.'mns_orders_shipping.weight IN '.$Weight.'
        AND '.$prfx.'mns_orders_shipping.disabled = FALSE
        AND '.$prfx.'mns_orders_shipping.country = %s',
        $this->Country
      );
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $RtrnData['Error'] = false;
        $RtrnData['Data'] = $DataSet;
      }
    }
    return $RtrnData;
  }
  public function WriteInLog($Type=false,$log=false){
      $this->GetStoreActiveTimezone();
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Store=new mnsFrontEndObject();
      $Store=$Store->mnsFrontEndStoreData();
      $Query='INSERT INTO `'.$prfx.'mns_mensiologs` values(NULL,"'.$Store['id'].'","'.$Type.'",NULL,"'.$log.'","0")';
      $wpdb->query($Query);
  }
  public function checkCoupon($Customer,$Coupon){
      $Dataset=false;
      if(!empty($Customer) && !empty($Coupon)){
            global $wpdb;
            $prfx = $wpdb->prefix;
            $Query='SELECT
                    `'.$prfx.'mns_customers_coupons`.`uuid`,
                    `'.$prfx.'mns_customers_coupons`.`coupon`,
                    `'.$prfx.'mns_coupons`.`title`,
                    `'.$prfx.'mns_discounts`.`uuid` as `discountCoupon`,
                    `'.$prfx.'mns_discounts`.`discount` as `DiscountPercent`,
                    `'.$prfx.'mns_discounts`.`flatdisc` as `DiscountFlat`,
                    `'.$prfx.'mns_discounts`.`datestart`,
                    `'.$prfx.'mns_discounts`.`dateend`
                from `'.$prfx.'mns_customers_coupons`,`'.$prfx.'mns_coupons`,`'.$prfx.'mns_discounts`
                where
                    `'.$prfx.'mns_customers_coupons`.`orders`="NOTUSED" and
                    `'.$prfx.'mns_customers_coupons`.`customer`="'.$Customer.'" and
                    `'.$prfx.'mns_customers_coupons`.`cpnkey`="'.$Coupon.'" and
                    `'.$prfx.'mns_coupons`.`uuid`=`'.$prfx.'mns_customers_coupons`.`coupon` and
                    `'.$prfx.'mns_coupons`.`discount`=`'.$prfx.'mns_discounts`.`uuid`';
            if(count($wpdb->get_results($Query))==1){
                $Dataset=$wpdb->get_results($Query);
            }
      }
      return $Dataset;
  }
  public function AddCouponToOrder($orderID,$cpnUUID,$cpnID){
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query='UPDATE `'.$prfx.'mns_customers_coupons` set `orders`="'.$orderID.'"
            where `uuid`="'.$cpnUUID.'"';
    $Query=$wpdb->query($Query);
    $Query='INSERT INTO `'.$prfx.'mns_orders_discounts` VALUES ("'.$orderID.'","'.$cpnID.'")';
    $Query=$wpdb->query($Query);
  }
  public function hasCoupon($orderID){
      $Dataset=false;
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Query='SELECT
                    `'.$prfx.'mns_orders_discounts`.*,
                    `'.$prfx.'mns_discounts`.`name`,
                    `'.$prfx.'mns_discounts`.`discount` as `PercentDiscount`,
                    `'.$prfx.'mns_discounts`.`flatdisc` as `FlatDiscount`
                    from
                        `'.$prfx.'mns_orders_discounts`,
                        `'.$prfx.'mns_discounts`
                    where
                        `'.$prfx.'mns_orders_discounts`.`orders`="'.$orderID.'" and
                        `'.$prfx.'mns_discounts`.`uuid`=`'.$prfx.'mns_orders_discounts`.`discount`
                ';
        if(count($wpdb->get_results($Query))==1){
            $Dataset=$wpdb->get_results($Query);
        }
      return $Dataset;
  }
  public function GetShippingMethod($ID) {
    $RtrnData = array('Error'=>true, 'Data'=>'');
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query =
        'SELECT '.$prfx.'mns_couriers_type.*, '.$prfx.'mns_orders_shipping.price, '.$prfx.'mns_orders_shipping.weight
        FROM '.$prfx.'mns_couriers_type, '.$prfx.'mns_orders_shipping
        WHERE '.$prfx.'mns_couriers_type.uuid = "'.$ID.'"
        AND '.$prfx.'mns_couriers_type.active = TRUE
        AND '.$prfx.'mns_orders_shipping.weight
        AND '.$prfx.'mns_orders_shipping.disabled = FALSE
        ';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $RtrnData['Error'] = false;
        $RtrnData['Data'] = $DataSet;
      }
    return $RtrnData;
  }
  public function GetShippingData($ID) {
    $RtrnData = array('Error'=>true, 'Data'=>'');
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query =
        'SELECT '.$prfx.'mns_orders_shipping.*
        FROM '.$prfx.'mns_orders_shipping
        WHERE '.$prfx.'mns_orders_shipping.uuid = "'.$ID.'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $RtrnData['Error'] = false;
        $RtrnData['Data'] = $DataSet;
      }
    return $RtrnData;
  }
  public function AddNewOrderNotification($Data) {
    $this->GetStoreActiveTimezone();
    $JobDone = false;
    $Error = false;
    if (!is_array($Data)) { $Error = true; }
    if (empty($Data['Order'])) { $Error = true; }
    if (empty($Data['RefNum'])) { $Error = true; }
    if (empty($Data['Date'])) { $Error = true; }
    if (empty($Data['Customer'])) { $Error = true; }
    if (!$Error) {
      $Log = '<span class="InfoElementLabel">Internal :</span> <span id="XXXX">'.$Data['Order'].'</span><br>
      <span class="InfoElementLabel">Serial :</span> #'.$Data['RefNum'].'<br>
      <span class="InfoElementLabel">Placed :</span> '.$this->ConvertDateToTimezone($Data['Date']).'<br>
      <span class="InfoElementLabel">Customer :</span> '.$Data['Customer'].'<br>';
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'mns_mensiologs (log_store,log_type,log,informed)
          VALUES ((SELECT uuid FROM '.$prfx.'mns_store),"Orders",%s,"0")',
        $Log
      );
      if (false === $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function AddNewTicketNotification($Data) {
    $JobDone = false;
    $Error = false;
    if (!is_array($Data)) { $Error = true; }
    if (empty($Data['Title'])) { $Error = true; }
    if (empty($Data['Code'])) { $Error = true; }
    if (empty($Data['Date'])) { $Error = true; }
    if (empty($Data['User'])) { $Error = true; }
    if (!$Error) {
      $this->GetStoreActiveTimezone();
      $Log = '<span class="InfoElementLabel">Title :</span> '.$Data['Title'].'<br>
      <span class="InfoElementLabel">Code :</span> <span id="XXXX">'.$Data['Code'].'</span><br>
      <span class="InfoElementLabel">Added :</span> '.$this->ConvertDateToTimezone($Data['Date']).'<br>
      <span class="InfoElementLabel">Customer :</span> '.$Data['User'].'<br>';
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'mns_mensiologs (log_store,log_type,log,informed)
          VALUES ((SELECT uuid FROM '.$prfx.'mns_store),"Tickets",%s,"0")',
        $Log
      );
      if (false === $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdatePaymentMethod($Answer){
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'UPDATE `'.$prfx.'mns_orders_payment` set `answer`="'.$Answer.'" where `answer`="StartingConnection" and `orders`="'.$this->NewOrderID.'"';
    $Query=$wpdb->query($Query);
    return $Query;
  }
  public function getOrderData($ID){
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT * FROM `'.$prfx.'mns_orders` where `'.$prfx.'mns_orders`.`uuid` LIKE "'.$ID.'"';
    $Results = $wpdb->get_results($Query);
    return $Results;
  }
  public function getOrderIDbySerial($Serial){
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT `uuid` FROM `'.$prfx.'mns_orders` where `'.$prfx.'mns_orders`.`serial` LIKE "'.$Serial.'"';
    $UUID = $wpdb->get_results($Query);
    $UUID=$UUID[0]->uuid;
    return $UUID;
  }
  public function getOrdersProducts($ID){
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query='SELECT * FROM `'.$prfx.'mns_orders_products` where `'.$prfx.'mns_orders_products`.`orders` = "'.$ID.'"';
    $Results = $wpdb->get_results($Query);
    return $Results;
  }
  public function getOrdersData($ID){
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT * FROM `'.$prfx.'mns_orders` where `'.$prfx.'mns_orders`.`uuid` = "'.$ID.'"';
    $Results = $wpdb->get_results($Query);
    return $Results;
  }
  public function getOrderPaymentData($ID){
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT * FROM `'.$prfx.'mns_orders_payment` where `'.$prfx.'mns_orders_payment`.`orders` = "'.$ID.'"';
    $Results = $wpdb->get_results($Query);
    return $Results;
  }
  public function getOrderSerial(){
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT `orderserial` FROM `'.$prfx.'mns_store`';
    $Serial = $wpdb->get_results($Query);
    $Serial=$Serial[0]->orderserial;
    return $Serial;
  }
  public function Set_Serial(){
    $get=new mensio_orders();
    $this->Serial=$get->GetNewSerial();
    return $get->GetNewSerial();
  }
  public function Set_OrderCustomer($Value){
    $SetOk = false;
    if ($Value !== '') {
        $this->OrderCustomer = $Value;
        $SetOk = true;
    }
    return $SetOk;
  }
  public function Set_BlngAddress($Value) {
    $SetOk = false;
    if ($Value !== '') {
        $this->BlngAddress = $Value;
        $SetOk = true;
    }
    return $SetOk;
  }
  public function Set_SendAddress($Value) {
    $SetOk = false;
    if ($Value !== '') {
        $this->SendAddress = $Value;
        $SetOk = true;
    }
    return $SetOk;
  }
  public function Set_Shipping($Value) {
    $SetOk = false;
    if ($Value !== '') {
        $this->Shipping = $Value;
        $SetOk = true;
    }
    return $SetOk;
  }
  public function FindNewOrderShipping($Country,$Weight) {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix;
    if (!$Error) {
        $WeightSQL=false;
        if($Weight){
            $WeightSQL='weight >= "'.$Weight.'" and ';
        }
        $Query = 'SELECT `uuid`,`weight` FROM '.$prfx.'mns_orders_shipping WHERE '.$WeightSQL.'`country`="'.$Country.'"';
        $Orders = $wpdb->get_results($Query);
        if(!empty($Orders)){
            $JobDone=$Orders[0]->uuid;
        }
    }
    return $JobDone;
  }
  public function FindAddressCountry($Address) {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix;
    if (!$Error) {
        $Query = 'SELECT `country` FROM '.$prfx.'mns_addresses WHERE uuid >= "'.$Address.'"';
        $Orders = $wpdb->get_results($Query);
        $JobDone=$Orders[0]->country;
    }
    return $JobDone;
  }
  public function InsertNewOrderData() {
    $JobDone = false;
    $Error = false;
    if ($this->Serial === '') { $Error = true; }
    if ($this->OrderCustomer === '') { $Error = true; }
    if ($this->BlngAddress === '') { $Error = true; }
    if ($this->SendAddress === '') { $Error = true; }
    if ($this->Shipping === '') { $Error = true; }
    if (!$Error) {
        $refNumber=$this->CreateNewRefNumberForSeller();
        $UUID=$this->GetNewID();
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Query = $wpdb->prepare(
            'INSERT INTO '.$prfx.'mns_orders (uuid,serial,refnumber,created,customer,billingaddr,
            sendingaddr,shipping,orderip,complete)
            VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)',
        $UUID,
        $this->Serial,
        $refNumber,
        date("Y-m-d H:i:s"),
        $this->OrderCustomer,
        $this->BlngAddress,
        $this->SendAddress,
        $this->Shipping,
        $_SERVER['REMOTE_ADDR'],
        '0'
      );
      if (false !== $wpdb->query($Query)) {
          $JobDone['orderID'] = $UUID;
          $JobDone['Serial'] = $this->Serial;
          $JobDone['refNumber'] = $refNumber;
      }
    }
    return $JobDone;
  }
  private function CreateNewRefNumberForSeller() {
    $RefNum = '';
    global $wpdb;
    $prfx = $wpdb->prefix;
    do {
      $RefNum = rand(100,999).'-'.rand(1000000,9999999).'-'.rand(1000000,9999999);
      $Query = 'SELECT * FROM '.$prfx.'mns_orders WHERE refnumber = "'.$RefNum.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) { $RefNum = ''; }
    } while ($RefNum === '');
    return $RefNum;
  }
  public function AddPaymentMethodToOrder($PaymentMethod,$Answer) {
    $JobDone = false;
    $Error = false;
    if ($this->NewOrderID === '') { $Error = true; }
    if ($PaymentMethod === '') { $Error = true; }
    if (!$Error) {
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Query = $wpdb->prepare(
            'INSERT INTO '.$prfx.'mns_orders_payment (orders,payment,answer)
            VALUES (%s,%s,%s)',
        $this->NewOrderID,
        $PaymentMethod,
        $Answer
      );
      if (false !== $wpdb->query($Query)) {
          $JobDone=true;
      }
    }
    return $JobDone;
  }
  public function Set_OrderToComplete($Value){
    global $wpdb;
    $Data=array();
    $prfx = $wpdb->prefix;
    $Query = 'SELECT `uuid` FROM `'.$prfx.'mns_orders`'
            . 'WHERE `'.$prfx.'mns_orders`.`serial` = "'.$Value.'" and `'.$prfx.'mns_orders`.`complete`="0" LIMIT 0,1';
    $Orders = $wpdb->get_results($Query);
    if($Orders){
        $this->NewOrderID=$Orders[0]->uuid;
    }
    else{
        return false;
    }
  }
  public function GiverOrderStatus($NewStatus) {
    $JobDone = false;
    if ($this->NewOrderID !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'UPDATE `'.$prfx.'mns_orders_status` set `active` = "0" where `orders`="%s"',
        $this->NewOrderID
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'INSERT INTO `'.$prfx.'mns_orders_status` values ("%s" , (SELECT `uuid` from `'.$prfx.'mns_orders_status_type` where `name`="'.$NewStatus.'"), "'.date("Y-m-d H:i:s").'",1 )',
        $this->NewOrderID
      );
      if (false !== $wpdb->query($Query)) {
        $JobDone = true;
      }
    }
    return $JobDone;
  }
  public function UpdateOrderToComplete() {
    $JobDone = false;
    if ($this->NewOrderID !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'mns_orders SET complete = "1" WHERE uuid = %s',
        $this->NewOrderID
      );
      if (false !== $wpdb->query($Query)) {
        $JobDone = true;
      }
    }
    return $JobDone;
  }
  public function Set_NewOrderID($Value){
    $SetOk = false;
    if ($Value !== '') {
        $this->NewOrderID = $Value;
        $SetOk = true;
    }
    return $SetOk;
  }
  public function Set_NewOrderProduct($Value){
    $SetOk = false;
    if ($Value !== '') {
        $this->NewOrderProduct = $Value;
        $SetOk = true;
    }
    return $SetOk;
  }
  public function Set_NewOrderAmount($Value){
    $SetOk = false;
    if ($Value !== '') {
        $this->NewOrderAmount = $Value;
        $SetOk = true;
    }
    return $SetOk;}
  public function Set_NewOrderPrice($Value){
    $SetOk = false;
    if ($Value !== '') {
        $this->NewOrderPrice= $Value;
        $SetOk = true;
    }
    return $SetOk;}
  public function Set_NewOrderDiscount($Value){
    $SetOk = false;
    if ($Value !== '') {
        $this->NewOrderDiscount = $Value;
        $SetOk = true;
    }
    return $SetOk;
  }
  public function Set_NewOrderTax($Value){
    $SetOk = false;
    if ($Value !== '') {
        $this->NewOrderTax = $Value;
        $SetOk = true;
    }
    return $SetOk;}
  public function AddOrderProduct($FullPrice) {
    $JobDone = false;
    $Error = false;
    if ($this->NewOrderID === '') { $Error = true; }
    if ($this->Product === '') { $Error = true; }
    if ($this->Amount === '') { $Error = true; }
    if ($this->Price === '') { $Error = true; }
    if ($this->Discount === '') { $Error = true; }
    if ($this->Tax === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $FPrice = ($this->NewOrderAmount * ($this->NewOrderPrice-( $this->NewOrderPrice * ($this->Discount/100) ) ) );
      $FPrice =$FPrice+( $FPrice * ( $this->Tax/100) );
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'mns_orders_products
         (orders,product,amount,price,discount,taxes,fullprice)
         VALUES (%s,%s,%s,%s,%s,%s,%s)',
        $this->NewOrderID,
        $this->NewOrderProduct,
        $this->NewOrderAmount,
        $this->NewOrderPrice,
        $this->NewOrderDiscount,
        $this->NewOrderTax,
        $FullPrice
      );
      if (false !== $wpdb->query($Query)) {
        $getProd=new mnsFrontEndObject();
        $Prod=$getProd->mnsFrontEndProduct($this->NewOrderProduct);
        if($Prod['overstock']==0){
            $Query = $wpdb->prepare(
              'UPDATE '.$prfx.'mns_products SET stock = stock - '.$this->NewOrderAmount.' WHERE uuid = %s',
              $this->NewOrderProduct
            );
        }
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
  public function LoadAllCustomerOrders( $Limit=false,$DateLimit=false){
    global $wpdb;
    $Data=array();
    $prfx = $wpdb->prefix;
    if($DateLimit!=false){
        $DateLimit=" and (`created` BETWEEN '".$DateLimit['DateFrom']." 00:00:00' AND '".$DateLimit['DateTo']." 23:59:59') ";
    }
    $Query = 'SELECT * FROM `'.$prfx.'mns_orders`,`'.$prfx.'mns_orders_products`'
            . 'WHERE `'.$prfx.'mns_orders`.`customer` = "'.$this->Customer.'" and `'.$prfx.'mns_orders`.`uuid`=`'.$prfx.'mns_orders_products`.`orders`'
            .$DateLimit
            . 'order by `'.$prfx.'mns_orders`.`created` DESC'.$Limit;
    $Orders = $wpdb->get_results($Query);
    $i=0;
    foreach($Orders as $order){
            $GetStatus='SELECT `'.$prfx.'mns_orders_status`.`status`,`'.$prfx.'mns_orders_status_type`.`name` from `'.$prfx.'mns_orders_status`,`'.$prfx.'mns_orders_status_type` where `'.$prfx.'mns_orders_status`.`orders`="'.$order->uuid.'" and `'.$prfx.'mns_orders_status`.`status`=`'.$prfx.'mns_orders_status_type`.`uuid`';
            $Status= $wpdb->get_results($GetStatus);
        $orderID=$order->uuid;
        $Data[ $orderID ]['created']=$order->created;
        $Data[ $orderID ]['serial']=$order->serial;
        $Data[ $orderID ]['refnumber']=$order->refnumber;
        $Data[ $orderID ]['shipping']=$order->shipping;
        $Data[ $orderID ]['billingaddress']=$order->billingaddr;
        $Data[ $orderID ]['shippingaddress']=$order->sendingaddr;
        if(!count($Status)){
            $Data[ $orderID ]['status']=" ";
        }
        else{
            $Data[ $orderID ]['status']=end($Status)->name;
        }
        $Data[ $orderID ]['Products'][$i]['product']=$order->product;
        $Data[ $orderID ]['Products'][$i]['amount']=$order->amount;
        $Data[ $orderID ]['Products'][$i]['fullprice']=((($order->price*$order->amount)*($order->taxes/100))+($order->price*$order->amount));
        $Data[ $orderID ]['Products'][$i]['fullprice']=$Data[ $orderID ]['Products'][$i]['fullprice']-($Data[ $orderID ]['Products'][$i]['fullprice']*($order->discount/100));
        $i++;
    }
    return $Data;
  }
  public function LoadUserTickets(){
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
      if($this->Customer){
        $Query = 'SELECT '.$prfx.'mns_customers_tickets.*, '.$prfx.'mns_credentials.lastname, '.$prfx.'mns_credentials.firstname
            FROM '.$prfx.'mns_customers_tickets, '.$prfx.'mns_credentials
            WHERE '.$prfx.'mns_customers_tickets.customer = '.$prfx.'mns_credentials.uuid and '.$prfx.'mns_customers_tickets.customer="'.$this->Customer.'"';
        $Result = $wpdb->get_results($Query);
      }
      return $Result;
  }
  public function LoadUserTicketReplies(){
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
      if($this->Customer && $this->ticketID){
        $Query = 'SELECT '.$prfx.'mns_customers_tickets_history.*
            FROM '.$prfx.'mns_customers_tickets_history
            WHERE '.$prfx.'mns_customers_tickets_history.ticket = "'.$this->ticketID.'"';
        $Result = $wpdb->get_results($Query);
      }
      return $Result;
  }
  public function InsertNewText($TicketTitle){
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
      if($this->Customer && $this->ticketText){
          $UUID=$this->GetNewID();
          $ticketCode = uniqid('', true);
            $Query = $wpdb->prepare(
              'INSERT INTO '.$prfx.'mns_customers_tickets
                (uuid,customer,ticket_code,dateclosed,title,content,closed)
              VALUES (%s,%s,%s,%s,%s,%s,%s)',
              $UUID,
              $this->Customer,
              $ticketCode,
              '1900-01-01 00:00:01',
              $TicketTitle,
              $this->ticketText,
              '0'
            );
            if (false !== $wpdb->query($Query)) {
                $Result=array(
                    "code"=>$ticketCode,
                    "ID"=>$UUID
                );
            }
      }
      return $Result;
  }
  public function InsertNewReply(){
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
      if($this->Customer && $this->ticketID){
            $Query = $wpdb->prepare(
              'INSERT INTO '.$prfx.'mns_customers_tickets_history
                (ticket,replyauthor,replydate,replytext)
              VALUES (%s,%s,%s,%s)',
              $this->ticketID,
              $this->Customer,
              date("Y-m-d H:i:s"),
              $this->replyText
            );
            if (false !== $wpdb->query($Query)) {
                $Result=true;
            }
      }
      return $Result;
  }
  public function NewReview(){
    global $wpdb;
    $UUID=$this->GetNewID();
    $prfx = $wpdb->prefix;
        $Result=true;
        $Query = $wpdb->prepare(
            'INSERT INTO '.$prfx.'mns_reviews
              (uuid,product,customer,rtype,rvalue,title,notes,created,changed)
            VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)',
        $UUID,
        $this->ProductID,
        $this->Customer,
        $this->ReviewType,
        $this->ReviewValue,
        $this->ReviewTitle,
        $this->ReviewText,
        date("Y-m-d H:i:s"),
        date("Y-m-d H:i:s")
      );
    if (false !== $wpdb->query($Query)) {
        $RtrnData['Error'] = false;
        $RtrnData['Data'] = '';
    } else {
        $RtrnData['Data'] = 'Problem with the sql execution.';
    }
    return $Result;
  }
  public function AllReviewTypes(){
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
        $Query = 'SELECT * from `'.$prfx.'mns_ratings_types`';
        $Result = $wpdb->get_results($Query);
      return $Result;
  }
  public function AllProductReviews(){
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
        $Query = 'SELECT * from `'.$prfx.'mns_reviews` where `product`="'.$this->ProductID.'"';
        $arr = $wpdb->get_results($Query);
        $i=0;
        foreach($arr as $res){
            $Result[$i]['ID']=$res->uuid;
            $Result[$i]['text']=$res->notes;
                $Result[$i]['CustomerFirstName']=$this->GetCustomerData($res->customer,"firstname");
                $Result[$i]['CustomerLastName']=$this->GetCustomerData($res->customer,"lastname");
            $Result[$i]['text']=$res->notes;
            $Result[$i]['title']=$res->title;
            $Result[$i]['customerID']=$res->customer;
            $Result[$i]['value']=$res->rvalue;
            $Result[$i]['when']=$res->created;
            $i++;
        }
      return $Result;
  }
  public function MensioSearchSlug($slug){
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
        $Query = 'SELECT *  FROM `'.$prfx.'mns_store_slugs` WHERE `slug`="'.$slug.'"';
        $Result = $wpdb->get_results($Query);
      return $Result;
  }
  public function MensioSearchForSlug($uuid){
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
        $Query = 'SELECT *  FROM `'.$prfx.'mns_store_slugs` WHERE `uuid`="'.$uuid.'"';
        $Result = $wpdb->get_results($Query);
      return $Result;
  }
  public function LoadBrandsAndProducts($order){
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
        $Query="SELECT `".$prfx."mns_brands`.*, count(*) as `manyProducts` from `".$prfx."mns_brands`,`".$prfx."mns_products` where `".$prfx."mns_products`.`brand`=`".$prfx."mns_brands`.`uuid` group by `brand` order by count(*) ".$order;
        $Result = $wpdb->get_results($Query);
      return $Result;
  }
  public function LoadCategoriesAndProducts($order){
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
        $Query="SELECT `".$prfx."mns_categories_codes`.*, count(*) as `manyProducts`
                from `".$prfx."mns_categories_codes`,`".$prfx."mns_products_categories`,
                `".$prfx."mns_categories_names` where `".$prfx."mns_products_categories`.`category`=`".$prfx."mns_categories_codes`.`uuid`
                and `".$prfx."mns_categories_codes`.`uuid`=`".$prfx."mns_categories_names`.`category` group by `uuid`
                order by count(*)".$order;
        $Result = $wpdb->get_results($Query);
      return $Result;
  }
  public function LoadTopLevelCategories($brandID=false){
    $Result[0] = array();
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
      if($brandID!=""){
      $Query = 'SELECT '.$prfx.'mns_categories_codes.*,'.$prfx.'mns_categories_names.name AS translation
        FROM '.$prfx.'mns_categories_codes, '.$prfx.'mns_categories_names, '.$prfx.'mns_store
        WHERE '.$prfx.'mns_categories_codes.uuid = '.$prfx.'mns_categories_names.category
        AND '.$prfx.'mns_categories_names.language = '.$prfx.'mns_store.thmactivelang
        AND '.$prfx.'mns_categories_codes.uuid IN (
          SELECT '.$prfx.'mns_products_categories.category
          FROM '.$prfx.'mns_products_categories, '.$prfx.'mns_products, '.$prfx.'mns_brands
          WHERE '.$prfx.'mns_products_categories.product = '.$prfx.'mns_products.uuid
          AND '.$prfx.'mns_products.brand = "'.$brandID.'"
        )';
        $DataSet = $wpdb->get_results($Query);
        if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
          $i = 0;
          $Check = '';
          foreach ($DataSet as $Row) {
            $TopLevel = $this->FindTopLevelCategory($Row->uuid);
            if (!strpos($Check, $TopLevel['uuid'])) {
              $Result[$i]['uuid'] = $TopLevel['uuid'];
              $Result[$i]['name'] = $TopLevel['name'];
              $Result[$i]['image'] = $TopLevel['image'];
              $Result[$i]['Check'] = $Check;
              ++$i;
              $Check .= $TopLevel['uuid'].'  ';
            }
          }
        }
      }
      else{
      $Query = 'SELECT '.$prfx.'mns_categories_codes.*,'.$prfx.'mns_categories_names.name AS translation
        FROM '.$prfx.'mns_categories_codes, '.$prfx.'mns_categories_names, '.$prfx.'mns_store, '.$prfx.'mns_categories_tree
        WHERE '.$prfx.'mns_categories_codes.uuid = '.$prfx.'mns_categories_names.category
        AND '.$prfx.'mns_categories_names.language = "'.$_SESSION['MensioThemeLang'].'"
        AND '.$prfx.'mns_categories_tree.category='.$prfx.'mns_categories_codes.uuid
        AND  '.$prfx.'mns_categories_tree.parent="TopLevel"
        AND '.$prfx.'mns_categories_codes.visibility=1 ORDER BY name';
        $DataSet = $wpdb->get_results($Query);
        if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
          $i = 0;
          $Check = '';
          foreach ($DataSet as $Row) {
              $Result[$i]['uuid'] = $Row->uuid;
              $Result[$i]['name'] = $Row->name;
              $Result[$i]['image'] = $Row->image;
              $i++;
          }
        }
      }
      return $Result;
  }
  public function FindTopLevelCategory($CategoryID) {
    $Result = '';
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
      $Query = 'SELECT * FROM '.$prfx.'mns_categories_tree WHERE category = "'.$CategoryID.'"';
        $DataSet = $wpdb->get_results($Query);
        if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
          foreach ($DataSet as $Row) {
            $Parent = $Row->parent;
            if ($Parent === 'TopLevel') { $Result = $this->GetCategoryMainData($CategoryID); }
              else { $Result = $this->FindTopLevelCategory($Parent); }
          }
        }
      return $Result;
  }
  private function GetCategoryMainData($CategoryID) {
    $Result = array();
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
      $Query = 'SELECT '.$prfx.'mns_categories_codes.*,'.$prfx.'mns_categories_names.name AS translation
        FROM '.$prfx.'mns_categories_codes, '.$prfx.'mns_categories_names, '.$prfx.'mns_store
        WHERE '.$prfx.'mns_categories_codes.uuid = '.$prfx.'mns_categories_names.category
        AND '.$prfx.'mns_categories_names.language = '.$prfx.'mns_store.thmactivelang
        AND '.$prfx.'mns_categories_codes.uuid = "'.$CategoryID.'"';
        $DataSet = $wpdb->get_results($Query);
        if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
          foreach ($DataSet as $Row) {
            $Result['uuid'] = $Row->uuid;
            $Result['name'] = $Row->translation;
            $Result['image'] = $Row->image;
          }
        }
      return $Result;
  }
  public function LoadChildCategories($catID){
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
        $Query="SELECT `".$prfx."mns_categories_tree`.`category`,`".$prfx."mns_categories_codes`.* from `".$prfx."mns_categories_tree`,`".$prfx."mns_categories_codes` where `".$prfx."mns_categories_tree`.`parent`='".$catID."' and `".$prfx."mns_categories_tree`.`category`=`".$prfx."mns_categories_codes`.`uuid`";
        $Result = $wpdb->get_results($Query);
      return $Result;
  }
  public function LoadCategoryParent($catID){
    global $wpdb;
    $prfx = $wpdb->prefix;
      $Result=false;
        $Query="SELECT
                `".$prfx."mns_categories_tree`.`parent`,`".$prfx."mns_categories_names`.`name`
                from `".$prfx."mns_categories_tree`,`".$prfx."mns_categories_names` where
                `".$prfx."mns_categories_tree`.`category`='".$catID."' and
                `".$prfx."mns_categories_tree`.`parent`=`".$prfx."mns_categories_names`.`category`";
        $Result = $wpdb->get_results($Query);
        if($Result){
            $Result=$Result[0];
        }
      return $Result;
  }
  public function GetBundles(){
    global $wpdb;
    $prfx = $wpdb->prefix;
    $prodID=$this->Get_UUID();
      $Result=false;
        $Query='SELECT
                    `'.$prfx.'mns_products_descriptions`.`name`,
                    `'.$prfx.'mns_products_bundles`.*,
                    `'.$prfx.'mns_products_images`.`file` as `main_image`
                from
                `'.$prfx.'mns_products_descriptions`,
                `'.$prfx.'mns_products_bundles`,
                `'.$prfx.'mns_products_images`,
                `'.$prfx.'mns_store`
                where
                `'.$prfx.'mns_products_bundles`.`bundle`="'.$prodID.'" and
                `'.$prfx.'mns_products_bundles`.`product`=`'.$prfx.'mns_products_descriptions`.`product` AND
                `'.$prfx.'mns_products_descriptions`.`language`=`'.$prfx.'mns_store`.`themelang` and
                `'.$prfx.'mns_products_images`.`product`=`'.$prfx.'mns_products_bundles`.`product` AND
                `'.$prfx.'mns_products_images`.`main`=1';
        $Result = $wpdb->get_results($Query);
      return $Result;
  }
  public function LoadProductOffers($ForAdmin=true){
    $DataSet = array();
    $CompTbl = '';
    $Searcher = '';
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = '
      SELECT '.$prfx.'mns_products.*, '.$prfx.'mns_products_descriptions.description,
        '.$prfx.'mns_products_descriptions.name, '.$prfx.'mns_products_descriptions.notes,
        '.$prfx.'mns_products_images.file, '.$prfx.'mns_brands.name as brandname
      FROM '.$prfx.'mns_products, '.$prfx.'mns_products_descriptions, '.$prfx.'mns_brands,
        '.$prfx.'mns_products_images, '.$prfx.'mns_store'.$CompTbl.'
      WHERE '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_descriptions.product
      AND '.$prfx.'mns_products.uuid = '.$prfx.'mns_products_images.product
      AND '.$prfx.'mns_products.brand = '.$prfx.'mns_brands.uuid
      AND  '.$prfx.'mns_products_images.main = TRUE
      AND '.$prfx.'mns_products_descriptions.language = '.$prfx.'mns_store.'.$lang.'
      AND '.$prfx.'mns_products.discount>0
      '.$Searcher.'
      AND '.$prfx.'mns_products.uuid NOT IN (SELECT variation FROM '.$prfx.'mns_products_variations)';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
    public function FiltersHTML($value,$name,$uuid=false,$filter=false,$return=false){
        $extraClass=false;
        $Style=false;
        $Store=new mnsFrontEndObject();
        $StoreData=$Store->mnsFrontEndStoreData();
        $metrics=$StoreData['metrics'];
        $ar1=explode(";",$metrics);
        $metrics=array();
        foreach($ar1 as $ar){
            $ar2=explode(":",$ar);
            $metrics[$ar2[0]]=$ar2[1];
        }
            if(empty($metrics[$name])){
                $value=$value;
            }
            elseif($metrics[$name]=='HEX'){
                if (!empty($value) && strpos($value, ';') !== false) {
                    $Ar=explode(";",$value);
                    $array=array();
                    foreach($Ar as $keys){
                            $key=explode(":",$keys);
                            $array[$key[0]]=$key[1];
                    }
                    if(!empty($array['Hex'])){
                        $value="<div class='HexValue' style='border:1px solid #cecece;display:inline-block;width:25px;background:".$array['Hex'].";'>&nbsp;</div>";
                    }
                    else{
                        $value=false;
                    }
                    $extraClass.=" HexFilter";
                    $Style=" style='
                                width: auto;
                                height: auto;
                                line-height: 0px;'";
                }
                else{
                    $value= false;
                }
            }
            elseif($metrics[$name]=='Img'){
                if (!empty($value) && strpos($value, ';') !== false) {
                    $Ar=explode(";",$value);
                    $array=array();
                    foreach($Ar as $keys){
                            $key=explode(":",$keys);
                            $array[$key[0]]=$key[1];
                    }
                    if(!empty($array['Hex'])){
                        $value="<div class='HexValue' style='border:1px solid #cecece;display:inline-block;width:25px;background:".$array['Hex'].";'>&nbsp;</div>";
                    }
                    else{
                        $value=false;
                    }
                    $extraClass.=" HexFilter";
                    $Style=" style='
                                width: auto;
                                height: auto;
                                line-height: 0px;'";
                }
                else{
                    $value= false;
                }
            }
            elseif($metrics[$name]=='RGB' && !is_array ($value)){
                $value=false;
            }
            elseif($metrics[$name]=='IMG'){
                $Ar=explode(";",$value);
                $array=array();
                foreach($Ar as $keys){
                    $key=explode(":",$keys);
                    $array[$key[0]]=$key[1];
                }
                $value=false;
                if(!empty($array['Img'])){
                    $value="<img src='".get_site_url()."/".$array['Img']."' style='height:25px;'/>";
                }
                else{
                    $value=false;
                }
            }
            elseif($metrics[$name]=='TXT' && !is_array ($value)){
                    $value=$value;
                    if (strpos($value, ';') !== false) {
                        $Ar=explode(";",$value);
                        $array=array();
                        foreach($Ar as $keys){
                            $key=explode(":",$keys);
                            $array[$key[0]]=$key[1];
                        }
                        $value=false;
                        if(empty($array['Img'])){
                            $value=false;
                        }
                        else{
                            $value=$value;
                        }
                    }
            }
            elseif($metrics[$name]=='CMT' && !is_array ($value)){
                $value=$value."&nbsp;cm";
            }
            elseif($metrics[$name]=='MMT' && !is_array ($value)){
                $value=$value."&nbsp;mm";
            }
            elseif($metrics[$name]=='MTR' && !is_array ($value)){
                $value=$value."&nbsp;m";
            }
            elseif($metrics[$name]=='INC' && !is_array ($value)){
                $value=$value."&nbsp;inches";
            }
            elseif($metrics[$name]=='FOT' && !is_array ($value)){
                $value=$value."&nbsp;feet";
            }
            elseif($metrics[$name]=='YRD' && !is_array ($value)){
                $value=$value."&nbsp;yards";
            }
            elseif($metrics[$name]=='NUM' && is_numeric ($value)){
                $value=$value;
            }
            elseif($metrics[$name]=='GAL' && !is_array ($value)){
                $value=$value."&nbsp;gal";
            }
            elseif($metrics[$name]=='LTR' && !is_array ($value)){
                $value=$value."&nbsp;lt";
            }
            elseif($metrics[$name]=='CMT' && !is_array ($value)){
                $value=$value."&nbsp;m<sup>3</sup>";
            }
            elseif($metrics[$name]=='CFT' && !is_array ($value)){
                $value=$value."&nbsp;ft<sup>3</sup>";
            }
            elseif($metrics[$name]=='KLG' && !is_array ($value)){
                $value=$value."&nbsp;kg";
            }
            elseif($metrics[$name]=='GRM' && !is_array ($value)){
                $value=$value."&nbsp;gr";
            }
            else{
                $value=false;
            }
            if($return=="value"){
                return $value;
            }
        $html=false;
          if(!empty($value)){
              $checked='';
            $html='<label filter="'.MensioEncodeUUID($uuid).'" class="filter check'.$extraClass.'"'.$Style.'><span>'.$value.'</span><span><input type="checkbox" name="filter[]" value="'.MensioEncodeUUID($filter).":::".MensioEncodeUUID($uuid).'" '.$checked.'  /></span></label>';
          }
          return $html;
    }
    public function FilterSearch($keyword=false,$brands=false,$categories=false,$atts=false,$from=0,$limit=10,$order=false){
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Dataset=false;
        if(!empty($atts)){
            $get=new mensio_seller();
            $AttrTable=array();
            foreach($atts as $att){
                $att=MensioDecodeUUID($att);
                $AttrTable[$this->GetFilterAttributeID($att)][]=$att;
            }
            foreach($AttrTable as $attr){
            }
                $attrSearch.=' attribute_value = "4588517a-dec3-11e7-86fc-f44d306ad78a" and '.$prfx.'products.uuid IN (
                                    SELECT product FROM `'.$prfx.'products_attributes`
                                    WHERE attribute_value = "458377dd-dec3-11e7-86fc-f44d306ad78a"
                                    OR attribute_value = "45849d58-dec3-11e7-86fc-f44d306ad78a"
                                    OR attribute_value = "4588517a-dec3-11e7-86fc-f44d306ad78a"
                                )
                            ) and ';
                $extraTbls=",'.$prfx.'products_attributes";
        }
        if(!empty($brands)){
            $i=1;
            $brandSearch='(';
            foreach($brands as $brand){
                $brandSearch.=$prfx."mns_products.brand='".MensioDecodeUUID($brand)."' ";
                if($i<count($atts)){
                    $brandSearch.=' or ';
                }
                $i++;
            }
            $brandSearch.=')';
        }
        $Query='
        SELECT
            (SELECT sum('.$prfx.'mns_reviews.rvalue) from '.$prfx.'mns_reviews where '.$prfx.'mns_reviews.product='.$prfx.'mns_products.uuid) as reviews,
            '.$prfx.'mns_products_descriptions.name,
            '.$prfx.'mns_products.*
        from
            '.$prfx.'mns_products,
            '.$prfx.'mns_products_descriptions,
            '.$prfx.'mns_products_categories,
            '.$prfx.'mns_store
            '.$extraTbls.'
        where
            '.$prfx.'mns_products.uuid='.$prfx.'mns_products_categories.product and
            '.$prfx.'mns_products_categories.category="'.$categories.'" and
            '.$prfx.'mns_products_descriptions.product='.$prfx.'mns_products.uuid and
                '.$attrSearch.'
            '.$prfx.'mns_products_descriptions.language='.$prfx.'mns_store.adminlang'.$order;
        echo "Not Ready";
        die;
      return $Dataset;
  }
  public function getMaxMinCategoryPrices($categories=array()){
    global $wpdb;
    $prfx = $wpdb->prefix;
    if(empty($categories)){
        $Query='
          select
                  ((min(`price`)*(`tax`/100)+min(`price`)) -
              ((min(`price`)*(`tax`/100)+min(`price`))*(discount/100)))
                  as minPrice,
                  ((max(`price`)*(`tax`/100)+max(`price`)) -
              ((max(`price`)*(`tax`/100)+max(`price`))*(discount/100)))
                  as maxPrice
          from
                  `'.$prfx.'mns_products`';
        $Res = $wpdb->get_results($Query);
        $DataSet['minPrice']=$Res[0]->minPrice;
        $DataSet['maxPrice']=$Res[0]->maxPrice;
    }
    return $DataSet;
  }
  public function getMaxMinEshopPrices(){
    global $wpdb;
    $prfx = $wpdb->prefix;
    if(empty($categories)){
        $Query='
            SELECT
                (MAX(`price`)*MAX(`price`)/100)+MAX(`price`) as `maxPrice`,
                (MIN(`price`)*MIN(`price`)/100)+MIN(`price`) as `minPrice`
            FROM
                `'.$prfx.'mns_products`';
        $Res = $wpdb->get_results($Query);
        $DataSet['minPrice']=$Res[0]->minPrice;
        $DataSet['maxPrice']=$Res[0]->maxPrice;
    }
    return $DataSet;
  }
  public function getMailTemplate($template,$Replaces=array()){
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query='SELECT `post_content` from `'.$prfx.'posts` where `post_type`="mensio_mailtemplate" and `post_title`="'.$template.'"';
    $Res = $wpdb->get_results($Query);
    $FinalText=$Res[0]->post_content;
    foreach($Replaces as $replace=>$value){
        $FinalText=str_replace("[%".$replace."%]",$value,$FinalText);
    }
    return $FinalText;
  }
    public function GetAddress($UUID){
        $Rtrn=array();
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Query="SELECT * from `".$prfx."mns_addresses` where `uuid`='".$UUID."'";
        $Data=$wpdb->get_results($Query);
        foreach($Data as $data){
            $Rtrn['fullname']=$data->fullname;
            $Rtrn['city']=$data->city;
            $Rtrn['street']=$data->street;
            $Rtrn['zipcode']=$data->zipcode;
            $Rtrn['country']=$data->country;
                $Query="SELECT * from `".$prfx."mns_countries_names` where `country`='".$data->country."' and `language`='".$_SESSION['MensioThemeLang']."'";
                $Data=$wpdb->get_results($Query);
                foreach($Data as $dt){
                    $Rtrn['countryText']=$dt->name;
                }
            $Rtrn['region']=$data->region;
                $Query="SELECT * from `".$prfx."mns_regions_names` where `region`='".$data->country."' and `language`='".$_SESSION['MensioThemeLang']."'";
                $Data=$wpdb->get_results($Query);
                foreach($Data as $dt){
                    $Rtrn['regionText']=$data->region;
                }
        }
        return $Rtrn;
    }
    public function UpdateAddress($what,$newValue,$address){
        $Rtrn="false";
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Query="SELECT `uuid` from `".$prfx."mns_orders` where `sendingaddr`='".$address."' or `billingaddr`='".$address."'";
        if(count($wpdb->get_results($Query))==0){
            $Query="UPDATE `".$prfx."mns_addresses` set `".$what."`='".$newValue."'  where `uuid`='".$address."'";
            $wpdb->query($Query);
            $Rtrn="true";
        }
        else{
            $Query="UPDATE `".$prfx."mns_addresses` set `deleted`='1'  where `uuid`='".$address."'";
            $wpdb->query($Query);
            $Rtrn="deleted";
        }
        return $Rtrn;
    }
    public function RemoveContact($Contact,$Credential){
        $Rtrn="false";
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Query="UPDATE `".$prfx."mns_contacts` set `deleted`='1' where `uuid`='".$Contact."' and `credential`='".$Credential."'";
        $wpdb->query($Query);
        return $Rtrn;
    }
    public function RemoveAddress($Address,$Credential){
        $Rtrn="false";
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Query="UPDATE `".$prfx."mns_addresses` set `deleted`='1' where `uuid`='".$Address."' and `credential`='".$Credential."'";
        $wpdb->query($Query);
        return $Rtrn;
    }
        final public function VerifyPageIntegrity($Passphrase,$ref) {
            $IsCorrect = false;
            if (wp_verify_nonce( $Passphrase, $ref)) {
                $IsCorrect = true;
            }
            return $IsCorrect;
        }
}
