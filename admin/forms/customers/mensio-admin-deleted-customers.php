<?php
add_action('wp_ajax_mensio_ajax_Table_DelCustomers', 'mensio_ajax_Table_DelCustomers');
add_action('wp_ajax_mensio_ajax_Restore_Deleted_Customer', 'mensio_ajax_Restore_Deleted_Customer');
add_action('wp_ajax_mensio_ajax_Restore_Multi_Deleted_Customer', 'mensio_ajax_Restore_Multi_Deleted_Customer');
function Mensio_Admin_Deleted_Customers() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Deleted_Customers_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Customers'));
    $Page->Set_CustomMenuItems('') ;
    $Page->Set_MainPlaceHolder(
     '<h1 class="Mns_Page_HeadLine">Deleted Customers<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      '.wp_nonce_field('Active_Page_Deleted').' 
      <div class="PageInfo">'.MENSIO_PAGEINFO_Deleted.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Table">
        <div id="TBL_DelCustomers_Wrapper" class="TBL_DataTable_Wrapper">
          '.$Page->GetDataTable().'
        </div>
      <div class="DivResizer"></div>
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Customers','Deleted');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_DelCustomers() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Deleted_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch);
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Restore_Deleted_Customer() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Customer = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Deleted_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RestoreCustomer($Customer);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Restore_Multi_Deleted_Customer() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InData = filter_var($_REQUEST['Data'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Deleted_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $InData = explode(';',$InData);
      if (is_array($InData)) {
        foreach ($InData as $Customer) {
          if ($Customer !== '') {
            $RtrnData = $Page->RestoreCustomer($Customer);
          }
        }
      }
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}