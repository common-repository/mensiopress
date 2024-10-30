<?php
class Mensio_Admin_Multiaccount_Form extends mensio_core_form {
	private $DataSet;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->LoadMultiAccountsDataSet();
    $this->ActivePage = 'MultiAccounts';
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
     plugin_dir_url( __FILE__ ) . '../../js/mensio-multiaccount.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function LoadMultiAccountsDataSet($InSorter='') {
    $this->DataSet = '';
    $Customers = new mensio_customers();
    if ($InSorter != '') {
      $Customers->Set_Sorter($InSorter);
    }
    $this->DataSet = $Customers->LoadMultiAccountList();
    unset($Customers);
  }  
  private function SearchMultiAccountDataSet($InSearch, $InSorter='') {
    $this->DataSet = '';
    $Customers = new mensio_customers();
    if ($InSorter != '') {
      $Customers->Set_Sorter($InSorter);
    }
    if (!$Customers->Set_SearchString($InSearch)) {
      $Customers->Set_SearchString('');
    }
    $this->DataSet = $Customers->LoadMultiAccountList();
    unset($Customers);
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='',$InSearch='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    if ($InSearch !== '') {
      if ($InSorter !== '') { $tbl->Set_Sorter($InSorter); }
      $this->SearchMultiAccountDataSet($InSearch,$InSorter);
    } elseif ($InSorter !== '') {
        $tbl->Set_Sorter($InSorter);
        $this->LoadMultiAccountsDataSet($InSorter);
    }
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array(
     ''=>'No Actions'
    ));
    $tbl->Set_EditColumn('name');
    $tbl->Set_EditOptionsSubline(array(
      'View','Edit'
    ));
    $tbl->Set_Columns(array(
      'uuid:uuid:plain-text',
      'name:Name:plain-text',
      'type:Type:small',
      'created:Created:small'
    ));
    $RtrnTable = $tbl->CreateTable(
      'MultiAccount',
      $this->DataSet,
      array('uuid','name','type','created')
    );
    unset($tbl);    
    return $RtrnTable;
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
                <label>Created : </label>'.$Row->created.'<br>
                <label>Type : </label>'.$Row->multitype.'<br>
              </fieldset>
              <br>
              <fieldset>
                <legend>
                  <i class="faStyled fa fa-address-card-o fa-fw" aria-hidden="true"></i>
                  Credentials
                </legend>
                <label>Title : </label>'.$Row->name.'<br>
                <label>Tin : </label>'.$Row->tin.'<br>
                <label>Web Page : </label>'.$Row->website.'<br>
                <label>E-Mail : </label>'.$Row->email.'<br>
              </fieldset>
              <br>
              <fieldset>
                <legend>
                  <i class="faStyled fa fa-address-card-o fa-fw" aria-hidden="true"></i>
                  Activity Report
                </legend>
                <label>Active : </label>'.$Active.'<br>
                <label>Last Login : </label>'.$Row->lastlogin.'<br>
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
        $Notes = '';
        if ($Row->notes !== 'none') {
          $Notes = '<label>Notes :</label><br>'.$Row->notes;
        }
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
              <label>Region : </label>'.$Row->region.'<br>
              <br>
              <label>City : </label>'.$Row->city.'<br>
              <label>Zip Code : </label>'.$Row->zipcode.'<br>
              <label>Street : </label>'.$Row->street.'<br>
              <br>
              <label>Contact Phone : </label>'.$Row->phone.'<br>
              <br>
              '.$Notes.'
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
  public function ShowMultiAccountModalInfo($CustCode) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Modal'=>'');
    $NoteType = 'Info';
    $Customers = new mensio_customers();
    if (!$Customers->Set_CustomerUUID($CustCode)) {
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
              '.$this->MDLGeneralDiv($Customers->LoadMultiAccountData()).'
            <div class="DivResizer"></div>
            </div>
          </div>
          <div id="mdltabs-2">
            <div class="mdltabinfowrap">
              '.$this->MDLAddressesDiv($Customers->LoadMultiAccountAddress()).'
            <div class="DivResizer"></div>
            </div>
          </div>
          <div id="mdltabs-3">
            <div class="mdltabinfowrap">
              '.$this->MDLContactDiv($Customers->LoadMultiAccountContact()).'
            <div class="DivResizer"></div>
            </div>
          </div>
        </div>';
      $RtrnData['Modal'] = $this->CreateModalWindow('Multi Account Info',$ModalBody);
    }
    unset($Customers);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadSectorsOptions() {
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
  public function LoadMultiAccountData($CustCode) {
    $RtrnData = array ('ERROR'=>'FALSE', 'Message'=>'', 'Customer'=>'',
      'Sector'=>'', 'Name'=>'', 'Tin'=>'', 'WebSite'=>'', 'EMail'=>'');
    $Customers = new mensio_customers();
    if (!$Customers->Set_CustomerUUID($CustCode)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] = 'Problem with customer code';
    } else {
      $Data = $Customers->LoadMultiAccountData();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $RtrnData['Customer'] = $Row->customer;
          $RtrnData['Sector'] = $Row->sector;
          $RtrnData['Name'] = $Row->name;
          $RtrnData['Tin'] = $Row->tin;
          $RtrnData['WebSite'] = $Row->website;
          $RtrnData['EMail'] = $Row->email;
        }
      }
    }
    unset($Customers);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function UpdateMultiaccountData($Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Customer = new mensio_customers();
    $Data = stripslashes($Data);
    $Data = json_decode($Data, true);
    if (is_array($Data)) {
      foreach ($Data as $DataRow) {
        if (substr($DataRow['Field'],0,4) === 'FLD_') {
          switch ($DataRow['Field']) {
            case 'FLD_Customer':
              if (!$Customer->Set_CustomerUUID($DataRow['Value'])) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Value "'.$DataRow['Value'].'" of the Multi Account code is not correct<br>';
              }              
              break;
            case 'FLD_Sector':
              if (!$Customer->Set_Sector($DataRow['Value'])) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Value "'.$DataRow['Value'].'" of the Sector code is not correct<br>';
              }              
              break;
            case 'FLD_Name':
              if (!$Customer->Set_CompanyName($DataRow['Value'])) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Value "'.$DataRow['Value'].'" of the Name is not correct<br>';
              }              
              break;
            case 'FLD_Tin':
              if (!$Customer->Set_Tin($DataRow['Value'])) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Value "'.$DataRow['Value'].'" of the Tin is not correct<br>';
              }              
              break;
            case 'FLD_WebSite':
              if (!$Customer->Set_WebSite($DataRow['Value'])) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Value "'.$DataRow['Value'].'" of the Web Site is not correct<br>';
              }              
              break;
            case 'FLD_EMail':
              if (!$Customer->Set_CompanyEMail($DataRow['Value'])) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Value "'.$DataRow['Value'].'" of the E-Mail is not correct<br>';
              }              
              break;
          }
        }
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        if (!$Customer->UpdateMultiAccountRecord()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Problem while updating Multi Account record<br>';
        }
      }
    }
    unset($Customer);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data Saved Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
}