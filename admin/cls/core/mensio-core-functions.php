<?php
add_action('wp_ajax_mensio_Check_PopUp_Time_Out', 'mensio_Check_PopUp_Time_Out');
add_action('wp_ajax_mensio_Check_For_New_Notifications', 'mensio_Check_For_New_Notifications');
add_action('wp_ajax_mensio_Load_Orders_Logs', 'mensio_Load_Orders_Logs');
add_action('wp_ajax_mensio_Load_Returns_Logs', 'mensio_Load_Returns_Logs');
add_action('wp_ajax_mensio_Load_Customers_Logs', 'mensio_Load_Customers_Logs');
add_action('wp_ajax_mensio_Load_Tickets_Logs', 'mensio_Load_Tickets_Logs');
add_action('wp_ajax_mensio_Load_Support_Logs', 'mensio_Load_Support_Logs');
add_action('wp_ajax_mensio_Load_Info_Logs', 'mensio_Load_Info_Logs');
add_action('wp_ajax_mensio_update_notification_informed', 'mensio_update_notification_informed');
function mensio_Check_For_New_Notifications() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Ctrl = new mensio_core_form();
    $Ctrl->SetTimerCallPage();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Ctrl->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Ctrl->CheckNotification();
    }
    unset($Ctrl);
  }
  echo $RtrnData;
  die();
}
function mensio_Load_Orders_Logs() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Ctrl = new mensio_core_form();
    $Ctrl->SetTimerCallPage();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Ctrl->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Ctrl->LoadOrdersLogs();
    }
    unset($Ctrl);
  }
  echo $RtrnData;
  die();
}
function mensio_Load_Customers_Logs() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Ctrl = new mensio_core_form();
    $Ctrl->SetTimerCallPage();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Ctrl->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Ctrl->LoadCustomersLogs();
    }
    unset($Ctrl);
  }
  echo $RtrnData;
  die();
}
function mensio_Load_Tickets_Logs() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Ctrl = new mensio_core_form();
    $Ctrl->SetTimerCallPage();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Ctrl->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Ctrl->LoadTicketsLogs();
    }
    unset($Ctrl);
  }
  echo $RtrnData;
  die();
}
function mensio_Load_Info_Logs() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Ctrl = new mensio_core_form();
    $Ctrl->SetTimerCallPage();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Ctrl->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Ctrl->LoadNotifications();
    }
    unset($Ctrl);
  }
  echo $RtrnData;
  die();
}
function mensio_Check_PopUp_Time_Out() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Ctrl = new mensio_core_form();
    $Ctrl->SetTimerCallPage();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Ctrl->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Ctrl->LoadPopUpTimeOut();
    }
    unset($Ctrl);
  }
  echo $RtrnData;
  die();
}
function mensio_update_notification_informed() {
  $RtrnData = array('Form'=>'','Info'=>'');
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Active = filter_var($_REQUEST['Active'],FILTER_SANITIZE_STRING);
    $Log = filter_var($_REQUEST['Log'],FILTER_SANITIZE_STRING);
    if ($Log !== 'ALL') { $Log = filter_var($Log,FILTER_SANITIZE_NUMBER_INT); }
    $Ctrl = new mensio_core_form();
    $Ctrl->SetTimerCallPage();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Ctrl->VerifyPageIntegrity($Security[0],$Security[1])) {
      if ($Active === 'Info') { $RtrnData['Form'] = $Ctrl->UpdateNotification($Log); }
        else { $RtrnData['Form'] = $Ctrl->UpdateLogs($Active, $Log); }
      $RtrnData['Info'] = $Ctrl->CheckNotification();
    }
    unset($Ctrl);
  }
  $RtrnData = json_encode($RtrnData);  
  echo $RtrnData;
  die();
}