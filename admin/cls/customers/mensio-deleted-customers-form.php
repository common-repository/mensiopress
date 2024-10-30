<?php
class Mensio_Admin_Deleted_Customers_Form extends mensio_core_form {
	private $DataSet;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->LoadCustomersDataSet();
    $this->ActivePage = 'Deleted';
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
     MENSIO_PLGTITLE.'-delcustomers',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-delcustomers.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function LoadCustomersDataSet($InSorter='') {
    $this->DataSet = '';
    $Customers = new mensio_customers();
    if ($InSorter != '') {
      $Customers->Set_Sorter($InSorter);
    }
    $this->DataSet = $Customers->LoadTableList('TRUE');
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
    $this->DataSet = $Customers->LoadTableList('TRUE');
    unset($Customers);
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='',$InSearch='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    if ($InSearch !== '') {
      if ($InSorter !== '') { $tbl->Set_Sorter($InSorter); }
      $this->SearchCustomersDataSet($InSearch,$InSorter);
    } elseif ($InSorter !== '') {
        $tbl->Set_Sorter($InSorter);
        $this->LoadCustomersDataSet($InSorter);
    }
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array(
     'RSTR'=>'Restore'
    ));
    $tbl->Set_EditColumn('name');
    $tbl->Set_EditOptionsSubline(array(
      'View','Restore'
    ));
    $tbl->Set_Columns(array(
      'uuid:uuid:plain-text',
      'name:Name:plain-text',
      'type:Type:small',
      'created:Created:small',
      'active:Active:small'
    ));
    $RtrnTable = $tbl->CreateTable(
      'DelCustomers',
      $this->DataSet,
      array('uuid','name','active','type','created')
    );
    unset($tbl);    
    return $RtrnTable;
  }
  public function RestoreCustomer($CustCode) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $Customers = new mensio_customers();
    if (!$Customers->Set_UUID($CustCode)) {
      $RtrnData['ERROR'] === 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Selected customer code not correct<br>';
    } else {
      if (!$Customers->RestoreSingleAcount()) {
        $RtrnData['ERROR'] === 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Selected customer could not be restored<br>';
      }
    }
    unset($Customers);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Accounts Activated Successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
}