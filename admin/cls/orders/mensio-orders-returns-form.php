<?php
class Mensio_Admin_Returns_Returns_Form extends mensio_core_form {
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-orders',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-orders.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-orders',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-orders.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function LoadReturnsDataSet($InSearch,$InSorter) {
    $Error = false;
    $DataSet = array();
    $Returns = new mensio_orders_returns();
    if ($InSearch !== '') {
      if (!$Returns->Set_SearchString($InSearch)) { $Error = true; }
    }
    if (!$Returns->Set_Sorter($InSorter)) { $Error = true; }
    if (!$Error) {
      $DataSet = $Returns->LoadReturnsDataSet();
    }
    unset($Returns);
    return $DataSet;
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='name',$InSearch='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    $tbl->Set_Sorter($InSorter);
    $DataSet = $this->LoadReturnsDataSet($InSearch,$InSorter);
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array(
     'VIS'=>'Toggle Visible'
    ));
    $tbl->Set_EditColumn('name');
    $tbl->Set_EditOptionsSubline(array(
      'Edit'
    ));
    $tbl->Set_Columns(array(
      'uuid:uuid:plain-text',
      'name:Category:plain-text',
      'visibility:Visible:input-checkbox',
      'image:Icon:img'
    ));
    $RtrnTable = $tbl->CreateTable(
      'Returns',
      $DataSet,
      array('uuid','image','name','visibility')
    );
    unset($tbl);    
    return $RtrnTable;
  }
}