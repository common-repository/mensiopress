<?php
if ( ! defined( 'WPINC' ) ) { die; }
add_action('wp_ajax_mensio_ajax_Table_Notifications', 'mensio_ajax_Table_Notifications');
add_action('wp_ajax_mensio_ajax_Delete_Notification', 'mensio_ajax_Delete_Notification');
add_action('wp_ajax_mensio_ajax_Delete_MultiNotification', 'mensio_ajax_Delete_MultiNotification');
add_action('wp_ajax_mensio_ajax_Notifications_PDF_Export', 'mensio_ajax_Notifications_PDF_Export');
function Mensio_Admin_System_Notifications() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_System_Notifications();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $RtrnTable = $Page->GetNotificationDataTable();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('System'));
    $Page->Set_CustomMenuItems('') ;
    $Page->Set_MainPlaceHolder('
      <div id="InfoPageSelectorDiv">
        <a id="Notifications" class="button" href="admin.php?page=Mensio_Admin_System_Notifications" title="Info for the Back Office">
          Back Office
        </a>
        <a id="Logs" class="button" href="admin.php?page=Mensio_Admin_System_Logs" title="Info for the front end actions">
          Front End
         </a>
      </div>
      <h1 class="Mns_Page_HeadLine">Back Office<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      <div id="ButtonArea">
        <button id="BTN_Delete">
          <i class="fa fa-trash action-icon" aria-hidden="true"></i>
          Delete
        </button>
      </div>
      '.wp_nonce_field('Active_Page_Mensio_Notifications').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_SysLogs.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Table">
        <div id="TBL_Notifications_Wrapper" class="TBL_DataTable_Wrapper">
          '.$RtrnTable.'
        </div>
      <div class="DivResizer"></div>
      </div> '
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('System','Notifications');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Notifications() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_System_Notifications();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetNotificationDataTable($InPage,$InRows);
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Delete_Notification() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_System_Notifications();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteNotificationData();
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Delete_MultiNotification(){
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $MultCodes = filter_var($_REQUEST['MultNotification'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_System_Notifications();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteMultiNotificationData($MultCodes);
    }
    unset($Page); 
  }
  echo $RtrnData;
  die(); 
}
function mensio_ajax_Notifications_PDF_Export() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_System_Notifications();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->ExportToPDF();
    }
    unset($Page);
  }
  echo $RtrnData;
  die(); 
}
