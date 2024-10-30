<?php
class Mensio_Admin_Customers_Form extends mensio_core_form {
	private $DataSet;
  private $NewEntry;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->LoadCustomersDataSet();
    $this->NewEntry = false;
    $this->ActivePage = 'Accounts';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-customers',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-customers.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-customers',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-customers.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  public function LoadAddressOptions() {
    $Options = '';
    $Countries = new mensio_customers();
    $Data = $Countries->GetAddressTypeDataSet();
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as &$Row) {
        $Options .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
      }
    }
    unset($Countries);
    return $Options;
  }
  public function LoadCountryOptions() {
    $Options = '';
    $Countries = new mensio_countries();
    $Data = $Countries->GetCountriesDataSet();
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as &$Row) {
        $Options .= '<option value="'.$Row->uuid.'">'.$Row->country.'</option>';
      }
    }
    unset($Countries);
    return $Options;
  }
  public function LoadCustomerTypes() {
    $Customers = new mensio_customers();
    $Data = $Customers->GetCustomerTypes();
    unset($Customers);
    $Options = '';
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        if (MENSIO_FLAVOR !== 'FREE') {
          $Options .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
        } else {
          if (($Row->name === 'Guest') || ($Row->name === 'Individual')) {
            $Options .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
          }
        }
      }
    }            
    return $Options;
  }
  private function LoadExtraActions($Data) {
    $CustType = '';
    $Company = '';
    $Date = '';
    $Data = stripslashes($Data);
    $Data = json_decode($Data,true);
    if (json_last_error() === JSON_ERROR_NONE) {
      if (is_array($Data)) {
        foreach ($Data as $Row) {
          switch ($Row['Field']) {
            case 'CustTypes':
              $CustType = $Row['Value'];
              break;
            case 'Companies':
              $Company = $Row['Value'];
              break;
            case 'Dates':
              $Date = $Row['Value'];
              break;
          }
        }
      }
    }
    $ExtraActions[0] = $this->LoadCustomerTypesOptions($CustType,$Company,$Date);
    $ExtraActions[1] = $this->LoadCompaniesOptions($CustType,$Company,$Date);
    $ExtraActions[2] = $this->LoadDatesOptions($CustType,$Company,$Date);
    return $ExtraActions;
  }
  private function LoadCustomerTypesOptions($CustType,$Company,$Date) {
    $Error = false;
    $Options = array('name'=>'CustTypes','options'=>'');
    $Customers = new mensio_customers();
    if (($Company !== '') && ($Company !== '0')) {
      if ( !$Customers->Set_Company($Company) ) { $Error = true; }
    }
    if (($Date !== '') && ($Date !== '0')) {
      if ( !$Customers->Set_Lastlogin($Date) ) { $Error = true; }
    }
    if (!$Error) {
      $Data = $Customers->GetCustomerTypesFilter();
      $Options['options'] = '<option value="0">Select Type</option>';
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Options['options'] .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
        }
      }
      if ($CustType === '') {
        $Options['options'] = str_replace('value="0"', 'value="0" selected', $Options['options']);
      } else {
        $Options['options'] = str_replace('value="'.$CustType.'"', 'value="'.$CustType.'" selected', $Options['options']);
      }
    }
    unset($Customers);
    return $Options;
  }
  private function LoadCompaniesOptions($CustType,$Company,$Date) {
    $Error = false;
    $Options = array('name'=>'Companies','options'=>'');
    $Customers = new mensio_customers();
    if (($CustType !== '') && ($CustType !== '0')) {
      if ( !$Customers->Set_Type($CustType) ) { $Error = true; }
    }
    if (($Date !== '') && ($Date !== '0')) {
      if ( !$Customers->Set_Lastlogin($Date) ) { $Error = true; }
    }
    if (!$Error) {
      $Data = $Customers->GetCompaniesFilter();
      $Options['options'] = '<option value="0">Select Multi Account</option>';
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Options['options'] .= '<option value="'.$Row->customer.'">'.$Row->name.'</option>';
        }
      }
      if ($Company === '') {
        $Options['options'] = str_replace('value="0"','value="0" selected',$Options['options']);
      } else {
        $Options['options'] = str_replace('value="'.$Company.'"','value="'.$Company.'" selected',$Options['options']);
      }    
    }    
    unset($Customers);
    return $Options;
  }
  private function LoadDatesOptions($CustType,$Company,$Date) {
    $Error = false;
    $Options = array('name'=>'Dates','options'=>'');
    $Customers = new mensio_customers();
    if (($CustType !== '') && ($CustType !== '0')) {
      if ( !$Customers->Set_Type($CustType) ) { $Error = true; }
    }
    if (($Company !== '') && ($Company !== '0')) {
      if ( !$Customers->Set_Company($Company) ) { $Error = true; }
    }
    if (!$Error) {
      $DataSet = $Customers->GetCreatedDatesFilter();
      $Month = '';
      $Options['options'] = '<option value="0">Select Date</option>';
      foreach ($DataSet as $Row) {
        if ($Month !== substr($Row->created, 0, 7)) {
          $Month = substr($Row->created, 0, 7);
          $Data = explode('-',$Month);
          $Name = date('F', mktime(0, 0, 0, intval($Data[1]), 1, intval($Data[0])));
          $Options['options'] .= '<option value="'.$Month.'">'.$Name.' '.$Data[0].'</option>';
        }
      }
      if ($Date === '') {
        $Options['options'] = str_replace('value="0"', 'value="0" selected', $Options['options']);
      } else {
        $Options['options'] = str_replace('value="'.$Date.'"', 'value="'.$Date.'" selected', $Options['options']);
      }    
    }    
    unset($Customers);
    return $Options;
  }
  private function LoadCustomersDataSet($InSorter='') {
    $this->DataSet = '';
    $Customers = new mensio_customers();
    if ($InSorter != '') {
      $Customers->Set_Sorter($InSorter);
    }
    $this->DataSet = $Customers->LoadTableList();
    unset($Customers);
  }
  private function SearchCustomersDataSet($InSearch, $InSorter='') {
    $this->DataSet = '';
    $Customers = new mensio_customers();
    if ($InSorter != '') {
      $Customers->Set_Sorter($InSorter);
    }
    if (!$Customers->Set_SearchString($InSearch)) {
      $Customers->Set_SearchString('');
    }
    $this->DataSet = $Customers->LoadTableList();
    unset($Customers);
  }
  private function FilterCustomersDataSet($InSearch,$JSONData,$InSorter) {
    $this->DataSet = '';
    $Customers = new mensio_customers();
    if ($InSorter != '') {
      $Customers->Set_Sorter($InSorter);
    }
    if (!$Customers->Set_SearchString($InSearch)) {
      $Customers->Set_SearchString('');
    }
    $Customers->Set_ExtraFilters($JSONData);
    $this->DataSet = $Customers->LoadTableList();
    unset($Customers);
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='',$InSearch='',$JSONData='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    if ($JSONData !== '') {
      if ($InSorter !== '') { $tbl->Set_Sorter($InSorter); }
      $this->FilterCustomersDataSet($InSearch,$JSONData,$InSorter);
    } elseif ($InSearch !== '') {
        if ($InSorter !== '') { $tbl->Set_Sorter($InSorter); }
        $this->SearchCustomersDataSet($InSearch,$InSorter);
      } elseif ($InSorter !== '') {
          $tbl->Set_Sorter($InSorter);
          $this->LoadCustomersDataSet($InSorter);
        }
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array(
     'ACT'=>'Activate',
     'DAC'=>'Deactivate'
    ));
    $ExtraActions = $this->LoadExtraActions($JSONData);
    $tbl->Set_ExtraActions($ExtraActions);
    $tbl->Set_EditColumn('name');
    $tbl->Set_EditOptionsSubline(array(
      'View', 'Edit', 'Delete'
    ));
    $tbl->Set_Columns(array(
      'uuid:uuid:plain-text',
      'name:Name:plain-text',
      'type:Type:small',
      'created:Created:small',
      'active:Active:small'
    ));
    $RtrnTable = $tbl->CreateTable(
      'Customers',
      $this->DataSet,
      array('uuid','name','active','type','created')
    );
    unset($tbl,$Data);    
    return $RtrnTable;
  }
  public function UpdateActivation($InData,$Active) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = 'Success';
    $InData = explode(';',$InData);
    if (is_array($InData)) {
      $Customers = new mensio_customers();
      foreach ($InData as $Row) {
        if ($Row !== '') {
          if (!$Customers->Set_UUID($Row)) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Problem with selected entry code<br>';
          }
          $Customers->Set_Active($Active);
          if ($RtrnData['ERROR'] === 'FALSE') {
            if (!$Customers->UpdateCustomerRecord()) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Problem while updating Data Set<br>';
            }
          }
        }
      }
      unset($Customers);
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if ($Active) {
        $RtrnData['Message'] = 'Accounts Activated Successfully<br>';
      } else {
        $RtrnData['Message'] = 'Accounts Deactivated Successfully<br>';
      }
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  private function MDLGeneralDiv($Data) {
    $General = '';
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $Active = 'NO';
        if ($Row->active) { $Active = 'YES'; }
        $General = '
              <fieldset>
                <legend>
                  <i class="faStyled fa fa-info-circle fa-fw" aria-hidden="true"></i>
                  Account info
                </legend>
                <label>Created : </label>'.$this->ConvertDateToTimezone($Row->created).'<br>
                <label>Type : </label>'.$Row->name.'<br>
                <label>Username : </label>'.$Row->username.'<br>
              </fieldset>
              <br>
              <fieldset>
                <legend>
                  <i class="faStyled fa fa-address-card-o fa-fw" aria-hidden="true"></i>
                  Credentials
                </legend>
                <label>Title : </label>'.$Row->title.'<br>
                <label>First Name : </label>'.$Row->firstname.'<br>
                <label>Last Name : </label>'.$Row->lastname.'<br>
              </fieldset>
              <br>
              <fieldset>
                <legend>
                  <i class="faStyled fa fa-address-card-o fa-fw" aria-hidden="true"></i>
                  Activity Report
                </legend>
                <label>Active : </label>'.$Active.'<br>
                <label>Last Login : </label>'.$this->ConvertDateToTimezone($Row->lastlogin).'<br>
                <label>Basic Source : </label>'.$Row->source.'<br>
                <label>Ip Address : </label>'.$Row->ipaddress.'<br>
              </fieldset>';
      }
    }
    return $General;
  }
  private function MDLAddressesDiv($Data) {
    $Addresses = '';
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $Country = new mensio_countries();
        if (!$Country->Set_UUID($Row->country)) {
          $CountryName = '---';
        } else {
          $CountryName = $Country->GetCountryName();
        }
        unset($Country);
        $Active = 'NO';
        if ($Row->active) { $Active = 'YES'; }
        switch ($Row->name) {
          case 'Shipping':
            $icon = '<i class="faStyled fa fa-ship fa-fw" aria-hidden="true"></i>';
            break;
          case 'Billing':
            $icon = '<i class="faStyled fa fa-money fa-fw" aria-hidden="true"></i>';
            break;
          case 'Both':
            $icon = '<i class="faStyled fa fa-shopping-bag fa-fw" aria-hidden="true"></i>';
            break;
        }
        $RegName = '';
        $Regions = new mensio_regions();
        if ($Regions->Set_UUID($Row->region)) {
          $RegName = $Regions->LoadRegionName();
        }
        unset($Regions);
        $Addresses .= '
            <fieldset>
              <legend>
                '.$icon.'
                Address info
              </legend>          
              <label>Type : </label>'.$Row->name.'<br>
              <br>
              <label>Receiver : </label>'.$Row->fullname.'<br>
              <label>Country : </label>'.$CountryName.'<br>
              <label>Region : </label>'.$RegName.'<br>
              <br>
              <label>City : </label>'.$Row->city.'<br>
              <label>Zip Code : </label>'.$Row->zipcode.'<br>
              <label>Street : </label>'.$Row->street.'<br>
              <br>
              <label>Contact Phone : </label>'.$Row->phone.'<br>
              <br>
              <label>Notes :</label><br>
              '.$Row->notes.'
            </fieldset>';
      }
    }
    return $Addresses;
  }
  private function MDLContactDiv($Data) {
    $Contact = '';
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $Valid = 'NO';
        if ($Row->validated) { $Valid = 'YES'; }
        $Contact .= '
            <fieldset>
              <legend>
                <i class="faStyled fa fa-inbox fa-fw" aria-hidden="true"></i>
                Communication
              </legend>          
              <label>Type : </label>'.$Row->name.'<br>
              <label>Contact : </label>'.$Row->value.'<br>
              <br>
              <label>Validated : </label>'.$Valid.'<br>
            </fieldset>';
      }
    }
    return $Contact;
  }
  public function ShowCustomerModalInfo($CustCode) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Modal'=>'');
    $NoteType = 'Info';
    $Customers = new mensio_customers();
    if (!$Customers->Set_UUID($CustCode)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] = 'Problem with customer code';
    } else {
      $ModalBody = '<div id="mdltabs">
          <ul>
            <li><a href="#mdltabs-1">
              <i class="fa fa-user" aria-hidden="true"></i>
              General
            </a></li>
            <li><a href="#mdltabs-2">
              <i class="fa fa-address-book" aria-hidden="true"></i>
              Addresses
            </a></li>
            <li><a href="#mdltabs-3">
              <i class="fa fa-envelope" aria-hidden="true"></i>
              Contact
            </a></li>
          </ul>
          <div id="mdltabs-1">
            <div class="mdltabinfowrap">
              '.$this->MDLGeneralDiv($Customers->LoadCustomerData()).'
            <div class="DivResizer"></div>
            </div>
          </div>
          <div id="mdltabs-2">
            <div class="mdltabinfowrap">
              '.$this->MDLAddressesDiv($Customers->LoadCustomerAddress()).'
              '.$this->MDLAddressesDiv($Customers->LoadCompanyAddress()).'
            <div class="DivResizer"></div>
            </div>
          </div>
          <div id="mdltabs-3">
            <div class="mdltabinfowrap">
              '.$this->MDLContactDiv($Customers->LoadCustomerContact()).'
            <div class="DivResizer"></div>
            </div>
          </div>
        </div>';
      $RtrnData['Modal'] = $this->CreateModalWindow('Customer Info',$ModalBody);
    }
    unset($Customers);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  private function CheckIfMainCred($Credential) {
    $MainCred = false;
    $Customers = new mensio_customers();
    if ($Customers->Set_UUID($Credential)) {
      $MainCred = $Customers->CheckIfMain();
    }
    unset($Customers);
    return $MainCred;
  }
  private function EditGeneralDiv($Data) {
    $TypeField = '';
    $title = '';
    $firstname = '';
    $lastname = '';
    $Disabled = '';
    $CustTypes = $this->LoadCustomerTypes();
    if ((is_array($Data)) && (!empty($Data[0]))) {
      $ExtraFields = '';
      foreach ($Data as $Row) {
        $Credential = $Row->uuid;
        $CustTypes = str_replace(
          'value="'.$Row->type.'"',
          'value="'.$Row->type.'" selected',
          $CustTypes
        );
        $title = $Row->title;
        $firstname = $Row->firstname;
        $lastname = $Row->lastname;
      }
      $ExtraFields = $this->CheckCustomersType($Row->type,$Row->customer);
      if (!$this->CheckTypeIsGuest($Row->type)) { $Disabled = 'disabled'; }
    }
    if (($this->CheckIfMainCred($Credential)) || ($this->NewEntry)) {
      $TypeField = '<label class="label_symbol">Type</label>
          <select id="FLD_CustomerType" class="form-control" '.$Disabled.'>
            '.$CustTypes.'
          </select>';
    } else {
      $ExtraFields = str_replace('<option value="NewMultiAccount">New Multi Account</option>','',$ExtraFields);
    }
    $Options = '<option value="NoTitle">No Title</option><option value="Mr.">Mr.</option><option value="Miss">Miss</option>';
    $Options = str_replace('value="'.$title.'">', 'value="'.$title.'" selected>', $Options);
    $General = '
        <div class="EditWrapper">
          '.$TypeField.'
          <div id="MultiAcc">'.$ExtraFields['Fields'].'</div>
          <label class="label_symbol">User Title</label>
          <select id="FLD_Title" class="form-control">
            '.$Options.'
          </select>
          <label class="label_symbol">First Name</label>
          <input type="text" id="FLD_FirstName" value="'.$firstname.'" class="form-control">
          <label class="label_symbol">Last Name</label>
          <input type="text" id="FLD_LastName" value="'.$lastname.'" class="form-control">
          <div class="button_row">
            <button id="BTN_Save" class="button BtnGreen" title="Save">
              <i class="fa fa-floppy-o" aria-hidden="true"></i>
            </button>
            <button id="BTN_Delete" class="button BtnRed" title="Delete">
              <i class="fa fa-trash" aria-hidden="true"></i>
            </button>
            <button id="BTN_Back" class="button" title="Back">
              <i class="fa fa-arrow-left"></i>
            </button> 
          </div>
        <div class="DivResizer"></div>
        </div>';
    return $General;
  }
  private function EditInfoDiv($Data) {
    $active = '1';
    $created = 'NOW';
    $lastlogin = '-';
    $lastloginIP = '';
    $source = 'S';
    $ipaddress = 'local';
    $username = '';
    $pswd = $this->CreateNewPassword();
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $active = $Row->active;
        $created = $this->ConvertDateToTimezone($Row->created);
        $lastlogin = $this->ConvertDateToTimezone($Row->lastlogin);
        $lastloginIP = $Row->loginip;
        $source = $Row->source;
        $ipaddress = $Row->ipaddress;
        $username = $Row->username;
      }
    }
    $Info['Info'] = '
          <div id="infoCard">
            <label class="label_symbol">Info</label>
            <hr>
            <table id="infoTbl">
              <tr>
                <td class="lblcol">Created <span>:</span></td>
                <td class="infocol">'.$created.'</td>
              </tr>
              <tr>
                <td class="lblcol">Registered IP <span>:</span></td>
                <td class="infocol">'.$ipaddress.'</td>
              </tr>
              <tr>
                <td class="lblcol">Last Login <span>:</span></td>
                <td class="infocol">'.$lastlogin.'</td>
              </tr>
              <tr>
                <td class="lblcol">Last Login IP <span>:</span></td>
                <td class="infocol">'.$lastloginIP.'</td>
              </tr>
              <tr>
                <td class="lblcol">Basic Source <span>:</span></td>
                <td class="infocol">'.$source.'</td>
              </tr>
            </table>
          <div class="DivResizer"></div>
          </div>';
    $Info['Data'] = '
        <div class="EditWrapper">
          <label class="label_symbol">Status</label>
          <select id="FLD_Active" class="form-control">
            <option value="0">Inactive</option>
            <option value="1">Active</option>
          </select>
          <label class="label_symbol">User Name</label>
          <input type="text" id="FLD_UserName" value="'.$username.'" class="form-control">
          <div class="PassDiv">
            <input type="hidden" id="FLD_Password" value="" class="form-control">
            <button id="BTN_NewPass" class="button" title="Change Password">
              <i class="fa fa-key" aria-hidden="true"></i>
              Change Password
            </button>
          </div>
          <div class="button_row">
            <button id="BTN_Save" class="button BtnGreen" title="Save">
              <i class="fa fa-floppy-o" aria-hidden="true"></i>
            </button>
            <button id="BTN_Delete" class="button BtnRed" title="Delete">
              <i class="fa fa-trash" aria-hidden="true"></i>
            </button>
            <button id="BTN_Back" class="button" title="Back">
              <i class="fa fa-arrow-left"></i>
            </button> 
          </div>
        <div class="DivResizer"></div>          
        </div>';
    $Info['Data'] = str_replace('value="'.$active.'"','value="'.$active.'" selected',$Info['Data']);    
    return $Info;
  }
  private function EditAddressDiv($Data) {
    $Addresses = '';
    if ((is_array($Data)) && (!empty($Data[0]))) {
      $Addresses = '<table id="TblAddress" class="InfoTable">
                    <thead>
                      <tr>
                        <th class="BtnCol"></th>
                        <th class="CustInfoType">Type</th>
                        <th class="">Receiver</th>
                        <th class="">Address</th> 
                        <th class="CustContact">Contact</th> 
                        <th class="CustContact">Active</th> 
                      </tr>
                    </thead>
                    <tbody>';
      foreach ($Data as $Row) {
        $del = 'YES';
        if ($Row->deleted) { $del = 'NO'; }
        $Addresses .= '
                <tr>
                  <td>
                    <div id="Edit_'.$Row->uuid.'" class="ESBtns AddrDelBtn foldedBtns" title="Edit">
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                    </div>
                  </td>
                  <td class="CustInfoType">'.$Row->name.'</td>
                  <td>'.$Row->fullname.'</td>
                  <td>'.$Row->street.', '.$Row->city.' '.$Row->zipcode.'</td>
                  <td class="CustContact">'.$Row->phone.'</td>
                  <td class="CustContact">'.$del.'</td>
                </tr>';
      }
      $Addresses .= '</tbody></table>';
    }
    return $Addresses;
  }
  private function EditContactDiv($Data) {
    $Contacts = '';
    if ((is_array($Data)) && (!empty($Data[0]))) {
      $Contacts = '
        <table id="TblContact" class="InfoTable">
          <thead>
            <tr>
              <th class="BtnCol"></th>
              <th class="CustInfoType">Type</th>
              <th class="">Contact</th>
              <th class="CustContact">Validated</th> 
              <th class="CustContact">Active</th> 
            </tr>
          </thead>
          <tbody>';
      foreach ($Data as $Row) {
        $Valid = 'NO';
        if ($Row->validated) { $Valid = 'YES'; }
        $del = 'YES';
        if ($Row->deleted) { $del = 'NO'; }
        $Contacts .= '
          <tr>
            <td>
              <div id="Edit_'.$Row->uuid.'" class="ESBtns ContDelBtn" title="Edit">
                <i class="fa fa-pencil" aria-hidden="true"></i>
              </div>
            </td>
            <td class="CustInfoType">'.$Row->name.'</td>
            <td>'.$Row->value.'</td>
            <td class="CustContact">'.$Valid.'</td>
            <td class="CustContact">'.$del.'</td>
          </tr>';
      }
      $Contacts .= '</tbody></table>';
    }
    return $Contacts;
  }
  private function LoadCustomerHistory($CustCode) {
    $History = '<div id="HistoryWrapper">
                  <table class="InfoTable">
                    <thead>
                      <tr>
                        <th class="BtnCol"></th>
                        <th class="">Code</th>
                        <th class="">Created</th>
                        <th class="">Status</th> 
                      </tr>
                    </thead>
                    <tbody>';
    $Customers = new mensio_customers();
    if ($Customers->Set_UUID($CustCode)) {
      $DataSet = $Customers->LoadCustomerOrderHistory();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $History .= '<tr>
                        <td class="BtnCol">
                          <div class="ESBtnsDivs">
                            <div id="VW_'.$Row->uuid.'" class="ESBtns BTN_View" title="View Order Details">
                              <i class="fa fa-eye" aria-hidden="true"></i>
                            </div>
                          </div>
                          <div class="ESBtnsDivs">
                            <div id="ST_'.$Row->uuid.'" class="ESBtns BTN_Status" title="View Order Status Hsitory">
                              <i class="fa fa-history" aria-hidden="true"></i>
                            </div>
                          </div>
                        </td>
                        <td class="">'.$Row->serial.'</td>
                        <td class="">'.$this->ConvertDateToTimezone($Row->created).'</td>
                        <td class="">'.$Row->name.'</td> 
                      </tr>';
        }
      }
    }
    unset($Customers);
    $History .= '</tbody></table></div>';
    return $History;
  }
  public function LoadCustomerEditing($CustCode='') {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','EditForm'=>'');
    $NoteType = 'Info';
    $CustomerData = '';
    $CustomerAddress = '';
    $CompanyAddress = '';
    $CustomerContact = '';
    if ($CustCode !== '') {
      $Customers = new mensio_customers();
      if (!$Customers->Set_UUID($CustCode)) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] = 'Problem with customer code';
      } else {
        $CustomerData = $Customers->LoadCustomerData();
        $CustomerAddress = $Customers->LoadCustomerAddress();
        $CompanyAddress = $Customers->LoadCompanyAddress();
        $CustomerContact = $Customers->LoadCustomerContact();
      }
      unset($Customers);
      $this->NewEntry = false;
    } else {
      $CustCode = 'NewCustomer';
      $this->NewEntry = true;
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Info = $this->EditInfoDiv($CustomerData);
      $RtrnData['EditForm'] = '
      <div class="Mns_Tab_Wrapper">
        <input type="hidden" id="FLD_Customer" value="'.$CustCode.'" class="">
        <div id="tabs">
          <ul>
            <li><a href="#General">
              <i class="fa fa-user" aria-hidden="true"></i>
              General
            </a></li>
            <li><a href="#Info">
              <i class="fa fa-sign-in" aria-hidden="true"></i>
              Credentials
            </a></li>
            <li><a href="#Addresses">
              <i class="fa fa-address-book" aria-hidden="true"></i>
              Addresses
            </a></li>
            <li><a href="#Contact">
              <i class="fa fa-envelope" aria-hidden="true"></i>
              Contact
            </a></li>
            <li><a href="#History">
              <i class="fa fa-shopping-cart" aria-hidden="true"></i>
              History
            </a></li>
          </ul>
          <div id="General" class="Mns_Tab_Container">
            <div class="GenInfoColDiv">
              '.$Info['Info'].'
            </div>
            <div class="GenEditColDiv">
              '.$this->EditGeneralDiv($CustomerData).'
            </div>
          </div>
          <div id="Info" class="Mns_Tab_Container">
            '.$Info['Data'].'
          </div>
          <div id="Addresses" class="Mns_Tab_Container">
            <div class="AddBtnDiv">
              <button id="BTN_AddAddress" class="BtnEditAdd">
                <i class="fa fa-plus" aria-hidden="true"></i>
                New Address
              </button>
              <button id="BTN_Back" class="button" title="Back">
                <i class="fa fa-arrow-left"></i>
              </button> 
            </div>
            <div id="AddressListDiv" class="BtnListDiv">
              '.$this->EditAddressDiv($CustomerAddress).'
              '.$this->EditAddressDiv($CompanyAddress).'
            </div>
          </div>
          <div id="Contact" class="Mns_Tab_Container">
            <div class="AddBtnDiv">
              <button id="BTN_AddContact" class="BtnEditAdd">
                <i class="fa fa-plus" aria-hidden="true"></i>
                New Contact
              </button>
              <button id="BTN_Back" class="button" title="Back">
                <i class="fa fa-arrow-left"></i>
              </button> 
            </div>
            <div id="ContactListDiv"class="BtnListDiv">
              '.$this->EditContactDiv($CustomerContact).'
            </div>
          </div>
          <div id="History" class="Mns_Tab_Container">
            '.$this->LoadCustomerHistory($CustCode).'
          </div>
        </div>
      </div>
      <div class="DivResizer"></div>';
     }
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function CheckCustomersType($Type,$Code='') {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'',
      'Multi'=>'FALSE','Fields'=>'');
    $NoteType = '';
    $Customers = new mensio_customers();
    if (!$Customers->Set_Type($Type)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with customer type<br>';
    } else {
      $Multi = $Customers->CheckTypeIfMultiCred();
      if ($Multi == true) {
        $OptionTypes = $Customers->LoadTypeCompanies();
        $Options = '<option value="NewMultiAccount">New Multi Account</option>';
        if ((is_array($OptionTypes)) && (!empty($OptionTypes[0]))) {
          foreach ($OptionTypes as $Row) {
            $Options .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
          }
        }
        $Multiflds = '';
        if ($Code !== '') {
          $Options = str_replace('value="'.$Code.'"','value="'.$Code.'" selected',$Options);
        } else {
          $Multiflds = $this->LoadNewMultiFields();
        }
        $RtrnData['Multi'] = 'TRUE';
        $RtrnData['Fields'] = '
          <label class="label_symbol">Select Multi Account</label>
          <select id="FLD_Company" class="form-control">
            '.$Options.'
          </select>
          <div id="NewMultiFields">'.$Multiflds.'</div>';
      } else {
        $RtrnData['Multi'] = 'FALSE';
        $RtrnData['Fields'] = '';
      }
    }
    unset($Customers);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function CheckTypeIsGuest($type) {
    $RtrnData = false;
    $Customers = new mensio_customers();
    if ($Customers->Set_Type($type)) {
      $DataSet = $Customers->GetCustomerTypes();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if (($type === $Row->uuid) && ($Row->name === 'Guest')) {
            $RtrnData = true;
          }
        }
      }
    }
    unset($Customers);
    return $RtrnData;
  }
  public function LoadNewMultiFields() {
    $ExtraFlds = '
          <label class="label_symbol">Industry (Sectors)</label>
          <select id="FLD_Sector" class="form-control">
            '.$this->LoadSectorsOptions().'
          </select>
          <div id="MultiAcc"></div>
          <label class="label_symbol">Name</label>
          <input type="text" id="FLD_Name" value="" class="form-control">
          <label class="label_symbol">Tin</label>
          <input type="text" id="FLD_Tin" value="" class="form-control">
          <label class="label_symbol">Web Site</label>
          <input type="text" id="FLD_WebSite" value="" class="form-control">
          <label class="label_symbol">Main E-Mail</label>
          <input type="text" id="FLD_EMail" value="" class="form-control">
          <hr>';
    return $ExtraFlds;
  }
  private function LoadSectorsOptions() {
    $Options = '';
    $Sectors = new mensio_sectors();
    $TopLevel = $Sectors->LoadSectorsList();
    foreach ($TopLevel as $Row) {
      $Options .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
      $Options .= $this->LoadSubSectorsOptions($Row->uuid,0);
    }
    unset($Sectors);
    return $Options;
  }  
  private function LoadSubSectorsOptions($Parent,$Path) {
    $Options = '';
    $Sectors = new mensio_sectors();
    if ($Sectors->Set_Parent($Parent)) {
      $SubLevel = $Sectors->LoadSectorsList();
      ++$Path;
      foreach ($SubLevel as $Row) {
        $Span = '';
        for ($i = 0; $i < $Path; ++$i) {
          $Span .= '--';
        }
        $Options .= '<option value="'.$Row->uuid.'">'.$Span.' '.$Row->name.'</option>';
        $Options .= $this->LoadSubSectorsOptions($Row->uuid,$Path);
      }
    }
    unset($Sectors);
    return $Options;
  }
  public function LoadNewPasswordModalForm() {
    $ModalBody = '<div>
        <input type="text" id="MDL_FLD_Password" value="" class="form-control">
        <div id="MDLPswdAlert_Week" class="MDLPswdAlerts">
          <span>Password strength is weak ....</span>
        </div>
        <div id="MDLPswdAlert_Good" class="MDLPswdAlerts">
          <span>Good Password strength</span>
        </div>
        <div class="button_row">
          <button id="BTN_SvNewPass" class="button" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>
          <button id="BTN_CrtNewPass" class="button" title="Create New Password">
            <i class="fa fa-plus" aria-hidden="true"></i>
          </button>
        </div>
      </div>';
    return $this->CreateModalWindow('Change Password',$ModalBody);
  }
  public function CreateNewPassword() {
    return wp_generate_password(24,true,true);
  }
  public function CheckCustomersNewPasswordStrength($Value) {
    $RtrnData = array('answer'=>'Good','test'=>'');
    if (strlen($Value) < 8) {
        $RtrnData['answer'] = 'Week';
    } else {
      $RtrnData['test'] .= $test.'<br>';
      $test = mb_ereg_replace('[^A-Za-z]', '', $Value);
      if (strlen($test) == 0) { $RtrnData['answer'] = 'Week'; }
      $RtrnData['test'] .= $test.'<br>';
      $test = mb_ereg_replace('[^0-9]', '', $Value);
      if (strlen($test) == 0) { $RtrnData['answer'] = 'Week'; }
      $RtrnData['test'] .= $test.'<br>';
    }
    return $RtrnData;
  }
  public function RemoveCustomerData($CustCode) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','EditForm'=>'');
    $NoteType = '';
    $Customers = new mensio_customers();
    if (!$Customers->Set_UUID($CustCode)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] = 'Problem with customer code';
    } else {
      if (!$Customers->DeleteSingleAcount()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] = 'Account could not be deleted';
      }
    }
    unset($Customers);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Customer Deleted Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  public function UpdateCustomerData($CustCode,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Customer'=>'');
    $NewCustomer = false;
    $NoteType = '';
    $PswdChngd = false;
    $NewPass = '';
    $Customer = new mensio_customers();
    if ($CustCode === 'NewCustomer') {
      $CustCode = $Customer->GetNewCustomerID();
      $NewCustomer = true;
    }
    if (!$Customer->Set_UUID($CustCode)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Customer code<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Data = stripslashes($Data);
      $Data = json_decode($Data, true);
      if (is_array($Data)) {
        foreach ($Data as $DataRow) {
          if (substr($DataRow['Field'],0,4) === 'FLD_') {
            $SetValue = $this->FindSetFun($DataRow['Field']);
            if ($SetValue !== '') {
              if (!$Customer->$SetValue($DataRow['Value'])) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $Lbl = str_replace('FLD_','',$DataRow['Field']);
                if ($Lbl === 'CustomerType') {
                  $RtrnData['Message'] .= 'Please select a correct value for the customer type<br>';
                } else {
                  $RtrnData['Message'] .= 'Value "'.$DataRow['Value'].'" of the field '.$Lbl.' is not correct<br>';
                }
              } else {
                if ($DataRow['Field'] === 'FLD_Password') {
                  $PswdChngd = true;
                  $NewPass = $DataRow['Value'];
                }
              }
            }
          }
        }
        if ($RtrnData['ERROR'] === 'FALSE') {
          if ($Customer->UsernameInUse()) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Info';
            $RtrnData['Message'] .= 'Username is already in use <br>';
          }
        }
        if ($RtrnData['ERROR'] === 'FALSE') {
          if ($NewCustomer) {
            if ($Customer->Set_IPAddress($this->GetIPAddress())) {
              $Customer->Set_Source('S');
              $Answer = $Customer->InsertNewCustomer();
              if ($Answer !== '') {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Problem while saving new customer record<br>'.$Answer;
              }
            } else {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Problem with IP address<br>';
            }
          } else {
            if (!$Customer->UpdateCustomerRecord()) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Problem while updating customer record<br>'.$Answer;
            } else {
              if ($PswdChngd) {
                $RtrnData['Message'] .=  $this->SendNewPassReply($CustCode,$NewPass);
              }
            }
          }
        }
      }
    }
    unset($Customer);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data Saved Successfully';
      $RtrnData['Customer'] = $CustCode;
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function SendNewPassReply($CustCode,$NewPass) {
    $Message = '';
    $NoteType = 'Success';
    $CstmrMail = $this->LoadCustomerEMailList($CustCode);
    if ($CstmrMail === '') {
      $Message .= 'Customer Has No E-Mails<br>
        New Password could not been send<br>
        Please find another way to inform the client<br>';
    } else {
      $Message = $this->SendCustomerNewPassEMail($CstmrMail,$NewPass);
    }
    return $Message;
  }
  private function LoadCustomerEMailList($CustCode) {
    $CstmrMail = '';
    $Customers = new mensio_customers();
    if ($Customers->Set_UUID($CustCode)) {
      $DataSet = $Customers->LoadCustomerContact();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if (($Row->name ==='E-Mail') && ($Row->validated)) {
            if ($CstmrMail === '') { $CstmrMail = $Row->value; }
              else { $CstmrMail .= '::'.$Row->value; }
          }
        }
      }
    }
    unset($Customers,$DataSet);
    return $CstmrMail;
  }
  private function SendCustomerNewPassEMail($MailList,$NewPass) {
    $Message = '';
    $Reply = 'The Password for your account was changed successfully to : '.$NewPass;
    $MailList = explode('::',$MailList);
    $subject = 'New Password';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    if (!wp_mail( $MailList, $subject, $Reply, $headers )) {
      $Message .= 'Sending New Password E-Mail encounter a problem<br>';
    } else {
      $Message .= 'Sending New Password E-Mail successfully<br>';
    }
    return $Message;
  }
  private function GetIPAddress() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP')) {
        $ipaddress = getenv('HTTP_CLIENT_IP');
    } else if(getenv('HTTP_X_FORWARDED_FOR')) {
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    } else if(getenv('HTTP_X_FORWARDED')) {
        $ipaddress = getenv('HTTP_X_FORWARDED');
    }else if(getenv('HTTP_FORWARDED_FOR')) {
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    }else if(getenv('HTTP_FORWARDED')) {
       $ipaddress = getenv('HTTP_FORWARDED');
    } else if(getenv('REMOTE_ADDR')) {
        $ipaddress = getenv('REMOTE_ADDR');
    } else {
        $ipaddress = 'UNKNOWN';
    }
    return $ipaddress;
  }
  public function LoadAddressModal($Credential,$Address) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Modal'=>'');
    $Data = $this->LoadAddressData($Credential,$Address);
    $Customers = new mensio_customers();
    $AddTypes = $Customers->LoadSelectorTypes('addresses');
    unset($Customers);
    $Types = '<select id="MDL_AddressType" class="form-control MDL_Fields">';
    if ((is_array($AddTypes)) && (!empty($AddTypes[0]))) {
      foreach ($AddTypes as $Row) {
        $Types .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
      }
    }
    $Types .= '</select>';
    $Types = str_replace('value="'.$Data['Type'].'"','value="'.$Data['Type'].'" selected', $Types);
    $Countries = new mensio_countries();
    $CountList = $Countries->GetCountriesDataSet();
    unset($Countries);
    $Countries = '<select id="MDL_Country" class="form-control MDL_Fields">';
    $Countries .= '<option value="">Select Country</option>';
    if ((is_array($CountList)) && (!empty($CountList[0]))) {
      foreach ($CountList as $Row) {
        $Countries .= '<option value="'.$Row->uuid.'">'.$Row->country.'</option>';
      }
    }
    $Countries .= '</select>';
    $Countries = str_replace('value="'.$Data['Country'].'"','value="'.$Data['Country'].'" selected', $Countries);
    $Regions = '';
    if ($Data['Country'] !== '') {
      $Regions = $this->LoadRegionOptions($Data['Country']);
      $Regions = str_replace('value="'.$Data['Region'].'"','value="'.$Data['Region'].'" selected', $Regions);
    }
    if ($Address === '') { $Address = 'NewAddress'; }
    $ModalBody = '
      <input type="hidden" id="MDL_Customer" value = "'.$Data['Customer'].'" class="MDL_Fields">
      <input type="hidden" id="MDL_Credential" value = "'.$Credential.'" class="MDL_Fields">
      <input type="hidden" id="MDL_Address" value = "'.$Address.'" class="MDL_Fields">
      <label class="label_symbol">Address Type</label>
      '.$Types.'
      <label class="label_symbol">Receiver Name</label>
      <input type="text" id="MDL_Fullname" value = "'.$Data['Fullname'].'" class="form-control MDL_Fields">
      <label class="label_symbol">Country</label>
      '.$Countries.'
      <label class="label_symbol">Region</label>
      <select id="MDL_Region" class="form-control MDL_Fields">
        '.$Regions.'
      </select>
      <label class="label_symbol">City</label>
      <input type="text" id="MDL_City" value = "'.$Data['City'].'" class="form-control MDL_Fields">
      <label class="label_symbol">Street</label>
      <input type="text" id="MDL_Street" value = "'.$Data['Street'].'" class="form-control MDL_Fields">
      <label class="label_symbol">Zip Code</label>
      <input type="text" id="MDL_Zipcode" value = "'.$Data['Zipcode'].'" class="form-control MDL_Fields">
      <label class="label_symbol">Phone</label>
      <input type="text" id="MDL_Phone" value = "'.$Data['Phone'].'" class="form-control MDL_Fields">
      <label class="label_symbol">Notes</label>
      <input type="text" id="MDL_Notes" value = "'.$Data['Notes'].'" class="form-control MDL_Fields">
      <button id="BTN_Address_ModalSave" class="button Mdl_SaveBtn" title="Save">
        <i class="fa fa-floppy-o" aria-hidden="true"></i>
      </button>';
    $RtrnData['Modal'] = $this->CreateModalWindow('Edit Address',$ModalBody);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  private function LoadAddressData($Credential,$Address) {
    $RtrnData = array ('Customer' => '', 'Type' => '', 'Fullname' => '',
      'Country' => '', 'City' => '', 'Region' => '', 'Street' => '',
      'Zipcode' => '', 'Phone' =>'', 'Notes' => '');
    $Customers = new mensio_customers();
    if (!$Customers->Set_UUID($Credential)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] = 'Problem with customer code';
    } else {
      $Data = $Customers->LoadCustomerData();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $RtrnData['Customer'] = $Row->customer;
        }
      }
      if ($Address !== '') {
        if (!$Customers->Set_Address($Address)) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] = 'Problem with address code';
        } else {
          $Data = $Customers->LoadAddressData();
          if ((is_array($Data)) && (!empty($Data[0]))) {
            foreach ($Data as $Row) {
              $RtrnData['Type'] = $Row->type;
              $RtrnData['Fullname'] = $Row->fullname;
              $RtrnData['Country'] = $Row->country;
              $RtrnData['City'] = $Row->city;
              $RtrnData['Region'] = $Row->region;
              $RtrnData['Street'] = $Row->street;
              $RtrnData['Zipcode'] = $Row->zipcode;
              $RtrnData['Phone'] = $Row->phone;
              if ($Row->notes !== 'none') {
                $RtrnData['Notes'] = $Row->notes;
              }
            }
          }
        }
      }
    }
    unset($Customers);
    return $RtrnData;
  }
  public function LoadRegionOptions($Country) {
    $Options = '<option value="NOREGION">No Regions Available</option>';
    $Regions = new mensio_regions();
    if ($Regions->Set_Country($Country)) {
      $Data = $Regions->LoadCountryRegions();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        $Options = ''; 
        foreach ($Data as $Row) {
          $Options .= '<option value="'.$Row['uuid'].'">'.$Row['name'].'</option>';
        }
      }
    }
    unset($Regions);
    return $Options;
  }
  public function LoadContactModal($Credential,$Contact) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Modal'=>'');
    $Data = $this->LoadContactData($Contact);
    $Customers = new mensio_customers();
    $ContTypes = $Customers->LoadSelectorTypes('contacts');
    unset($Customers);
    $Types = '<select id="MDL_ContactType" class="form-control MDL_Fields">';
    if ((is_array($ContTypes)) && (!empty($ContTypes[0]))) {
      foreach ($ContTypes as $Row) {
        $Types .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
      }
    }
    $Types .= '</select>';
    $Types = str_replace('value="'.$Data['Type'].'"','value="'.$Data['Type'].'" selected', $Types);
    if ($Contact === '') { $Contact = 'NewContact'; }
    $ModalBody = '
      <input type="hidden" id="MDL_Contact" value = "'.$Contact.'" class="MDL_Fields">
      <input type="hidden" id="MDL_Credential" value = "'.$Credential.'" class="MDL_Fields">
      <label class="label_symbol">Contact Type</label>
      '.$Types.'
      <label class="label_symbol">Contact</label>
      <input type="text" id="MDL_Value" value = "'.$Data['Value'].'" class="form-control MDL_Fields">
      <label class="label_symbol">Validated</label>
      <select id="MDL_Validated" class="form-control MDL_Fields">
        <option value="0">NO</option>
        <option value="1">YES</option>
      </select>
      <button id="BTN_Contact_ModalSave" class="button Mdl_SaveBtn" title="Save">
        <i class="fa fa-floppy-o" aria-hidden="true"></i>
      </button>';
    $ModalBody = str_replace('value="'.$Data['Validated'].'"','value="'.$Data['Validated'].'" selected', $ModalBody);
    $RtrnData['Modal'] = $this->CreateModalWindow('Edit Address',$ModalBody);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  private function LoadContactData($Contact) {
    $RtrnData = array ('Type' => '', 'Value' =>'', 'Validated' => '');
    if ($Address !== '') {
      $Customers = new mensio_customers();
      if ($Customers->Set_Contact($Contact)) {
        $Data = $Customers->LoadContactData();
        if ((is_array($Data)) && (!empty($Data[0]))) {
          foreach ($Data as $Row) {
            $RtrnData['Type'] = $Row->type;
            $RtrnData['Value'] = $Row->value;
            $RtrnData['Validated'] = $Row->validated;
          }
        }
      }
      unset($Customers);
    }
    return $RtrnData;
  }
  public function UpdateModalData($Tab,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','List'=>'');
    $Customer = new mensio_customers();
    $Data = stripslashes($Data);
    $Data = json_decode($Data, true);
    if (is_array($Data)) {
      foreach ($Data as $DataRow) {
        if (substr($DataRow['Field'],0,4) === 'MDL_') {
          $SetValue = $this->FindSetFun($DataRow['Field']);
          if ($SetValue !== '') {
            if (!$Customer->$SetValue($DataRow['Value'])) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $Lbl = str_replace('MDL_','',$DataRow['Field']);
              $RtrnData['Message'] .= 'Value "'.$DataRow['Value'].'" of the field '.$Lbl.' is not correct<br>';
            }
          }
        }
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        if ($Tab === 'Address') {
          if (!$Customer->UpdateAddressData()) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Problem while updating customer address<br>';
          }
        }
        if ($Tab === 'Contact') {
          if (!$Customer->UpdateContactData()) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Problem while updating customer contact<br>'.$answer;
          }
        }
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data Saved Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    if ($Tab === 'Address') {
      $RtrnData['List'] = $this->EditAddressDiv($Customer->LoadCustomerAddress());
      $RtrnData['List'] .= $this->EditAddressDiv($Customer->LoadCompanyAddress());
    }
    if ($Tab === 'Contact') {
      $RtrnData['List'] = $this->EditContactDiv($Customer->LoadCustomerContact());
    }
    unset($Customer);
    return $RtrnData;
  }
  private function FindSetFun($Field) {
    $SetFun = '';
    switch ($Field) {
      case 'FLD_CustomerType':
        $SetFun = 'Set_Type';
        break;
      case 'FLD_Title':
        $SetFun = 'Set_Title';
        break;
      case 'FLD_FirstName':
        $SetFun = 'Set_Firstname';
        break;
      case 'FLD_LastName':
        $SetFun = 'Set_Lastname';
        break;
      case 'FLD_Active':
        $SetFun = 'Set_Active';
        break;
      case 'FLD_UserName':
        $SetFun = 'Set_Username';
        break;
      case 'FLD_Password':
        $SetFun = 'Set_Password';
        break;
      case 'FLD_Sector':
        $SetFun = 'Set_Sector';
        break;
      case 'FLD_Company':
        $SetFun = 'Set_Company';
        break;
      case 'FLD_Name':
        $SetFun = 'Set_CompanyName';
        break;
      case 'FLD_Tin':
        $SetFun = 'Set_Tin';
        break;
      case 'FLD_WebSite':
        $SetFun = 'Set_WebSite';
        break;
      case 'FLD_EMail':
        $SetFun = 'Set_CompanyEMail';
        break;
      case 'MDL_Credential':
        $SetFun = 'Set_UUID';
        break;
      case 'MDL_Customer':
        $SetFun = 'Set_CustomerUUID';
        break;
      case 'MDL_Address':
        $SetFun = 'Set_Address';
        break;
      case 'MDL_AddressType':
        $SetFun = 'Set_AddressType';
        break;
      case 'MDL_Fullname':
        $SetFun = 'Set_Fullname';
        break;
      case 'MDL_Country':
        $SetFun = 'Set_Country';
        break;
      case 'MDL_City':
        $SetFun = 'Set_City';
        break;
      case 'MDL_Region':
        $SetFun = 'Set_Region';
        break;
      case 'MDL_Street':
        $SetFun = 'Set_Street';
        break;
      case 'MDL_Zipcode':
        $SetFun = 'Set_Zipcode';
        break;
      case 'MDL_Phone':
        $SetFun = 'Set_Phone';
        break;
      case 'MDL_Notes':
        $SetFun = 'Set_Notes';
        break;
      case 'MDL_Contact':
        $SetFun = 'Set_Contact';
        break;
      case 'MDL_ContactType':
        $SetFun = 'Set_ContactType';
        break;
      case 'MDL_Value':
        $SetFun = 'Set_Value';
        break;
      case 'MDL_Validated':
        $SetFun = 'Set_Validated';
        break;
    }
    return $SetFun;
  }
  public function DeleteModalData($CustCode,$Tab,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','List'=>'');
    $Customer = new mensio_customers();
    if ($Customer->Set_UUID($CustCode)) {
      switch ($Tab) {
        case 'Address':
          if (!$Customer->Set_Address($Data)) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] = 'Address could not be deleted, wrong code<br>';
          } else {
            if (!$Customer->DeleteCustomerAddress()) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] = 'Address Could not be Deleted';
            }
          }
          break;
        case 'Contact':
          if (!$Customer->Set_Contact($Data)) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] = 'Contact could not be deleted, wrong code<br>';
          } else {
            if (!$Customer->DeleteCustomerContact()) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] = 'Contact Could not be Deleted';
            }
          }
          break;
        default:
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] = 'Entry Tab Not Correct<br>If you see this message then OOOPS!!';
          break;
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        $NoteType = 'Success';
        $RtrnData['Message'] = 'Entry Deleted Successfully';
      }
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    if ($Tab === 'Address') {
      $RtrnData['List'] = $this->EditAddressDiv($Customer->LoadCustomerAddress());
      $RtrnData['List'] .= $this->EditAddressDiv($Customer->LoadCompanyAddress());
    }
    if ($Tab === 'Contact') {
      $RtrnData['List'] = $this->EditContactDiv($Customer->LoadCustomerContact());
    }
    unset($Customer);
    return $RtrnData;
  }
  public function ModalViewOrderDetails($OrderID) {
    $ModalBody = '';
    $Orders = new Mensio_Admin_Orders_Form();
    $Data = $Orders->LoadOrderData($OrderID);
    unset($Orders);
    $Products = $this->LoadOrderProducts($OrderID);
    $ModalBody = '<div>
      <div class="AddrDiv">
        <label class="label_symbol">Billing Address</label>
        <hr>
        '.$Data['BAData'].'
      </div>
      <div class="AddrDiv">
        <label class="label_symbol">Shipping Address</label>
        <hr>
        '.$Data['SAData'].'
      </div>
      <div class="DivResizer"></div>
      <div class="PrdDiv">
        <label class="label_symbol">Products</label>
        <hr>
        <table class="ProductTable">
          <thead>
            <tr>
              <th>Product</th>
              <th class="SmlCol">Amount</th>
            </tr>
          </thead>
          <tbody id="OrderStatusHistory">
            '.$Products.'
          </tbody>
        </table>
      </div>
    </div>';
    return $this->CreateModalWindow('Order '.$Data['Serial'].' Data',$ModalBody);
  }
  public function ModalViewOrderStatusHistory($OrderID) {
    $ModalBody = '';
    $Orders = new Mensio_Admin_Orders_Form();
    $Data = $Orders->LoadStatusTag($OrderID);
    unset($Orders);
    $ModalBody = '<div>
      <table class="ProductTable">
        <thead>
          <tr>
            <th>Status</th>
            <th class="SmlCol">Date</th>
            <th class="SmlCol">Time</th>
            <th class="SmlCol">Active</th>
          </tr>
        </thead>
        <tbody id="OrderStatusHistory">
          '.$Data['StatusTag'].'
        </tbody>
      </table>
    </div>';
    return $this->CreateModalWindow('Order Status History',$ModalBody);
  }
  public function LoadOrderProducts($OrderID) {
    $RtrnData = '';
    $Orders = new mensio_orders();
    if ($Orders->Set_UUID($OrderID)) {
      $DataSet = $Orders->LoadOrderProducts();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $RowClass = 'OddTblLine';
        foreach ($DataSet as $Row) {
          $RtrnData .= '
                <tr class="'.$RowClass.'">
                <td class="">
                  '.$Row->code.' - '.$Row->name.'</td>
                <td class="">
                  '.($Row->amount + 0).'
                </td>
              </tr> ';
        }
      }
    }
    unset($Orders);
    return $RtrnData;
  }
}