<?php
add_action('wp_ajax_mensio_ajax_Table_Reviews', 'mensio_ajax_Table_Products_Reviews');
add_action('wp_ajax_mensio_ajax_Load_Review_Data', 'mensio_ajax_Load_Review_Data');
add_action('wp_ajax_mensio_ajax_Delete_Review_Data', 'mensio_ajax_Delete_Review_Data');
add_action('wp_ajax_mensio_ajax_Delete_Review_Multi', 'mensio_ajax_Delete_Review_Multi');
function Mensio_Admin_Products_Reviews() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Products_Reviews_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Products'));
    $Page->Set_CustomMenuItems('') ;
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">Product Reviews<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      <div class="DivResizer"></div>
      '.wp_nonce_field('Active_Page_Reviews').'
      <hr>
      <div id="DIV_Table">
        <div id="TBL_Reviews_Wrapper" class="TBL_DataTable_Wrapper">
          '.$Page->GetDataTable().'
        </div>
      <div class="DivResizer"></div>
      </div>
      <div id="DIV_Edit"></div>');
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Products','Reviews');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Products_Reviews() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Reviews_Form();
    $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch);
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Load_Review_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $RevID = filter_var($_REQUEST['Review'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Reviews_Form();
    $RtrnData = $Page->LoadReviewData($RevID);
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Delete_Review_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $RelID = filter_var($_REQUEST['Review'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Reviews_Form();
    $RtrnData = $Page->RemoveReviewData($RelID);
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Delete_Review_Multi() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Reviews = filter_var($_REQUEST['Reviews'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Reviews_Form();
    $Reviews = explode(';', $Reviews);
    foreach ($Reviews as $RelID) {
      if ($RelID !== '') {
        $RtrnData = $Page->RemoveReviewData($RelID);
        if ($RtrnData['ERROR'] === 'TRUE') { break; }
      }
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}