<?php
add_action('wp_ajax_mensio_ajax_Dashboard_Load_Info_Modal', 'mensio_ajax_Dashboard_Load_Info_Modal');
add_action('wp_ajax_mensio_ajax_Dashboard_Update_Informed_Info', 'mensio_ajax_Dashboard_Update_Informed_Info');
add_action('wp_ajax_mensio_Dashboard_Check_For_New_Notifications', 'mensio_Dashboard_Check_For_New_Notifications');
function Mensio_Admin_DashBoard() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_DashBoard_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('DashBoard'));
    $Page->Set_CustomMenuItems('');
    $Page->Set_MainPlaceHolder(
     '<h1 class="Mns_Page_HeadLine">Dashboard</h1>
      '.wp_nonce_field('Active_Page_DashBoard').' 
      <div class="PageInfo">'.MENSIO_PAGEINFO_DashBoard.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Edit">
        '.$Page->GetStdDashboardElements().'
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('DashBoard','DashBoard');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Dashboard_Load_Info_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $ID = filter_var($_REQUEST['ID'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_DashBoard_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadDashboardInfoModal($Type,$ID);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Dashboard_Update_Informed_Info() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $ID = filter_var($_REQUEST['ID'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_DashBoard_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateDashboardInformedInfo($ID);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_Dashboard_Check_For_New_Notifications() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_DashBoard_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->GetFreeDashboardElements();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
