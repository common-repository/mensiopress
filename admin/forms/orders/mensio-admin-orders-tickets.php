<?php
add_action('wp_ajax_mensio_ajax_Table_Tickets', 'mensio_ajax_Table_Tickets');
add_action('wp_ajax_mensio_ajax_Load_Ticket_Data', 'mensio_ajax_Load_Ticket_Data');
add_action('wp_ajax_mensio_ajax_Load_Ticket_Order_View', 'mensio_ajax_Load_Ticket_Order_View');
add_action('wp_ajax_mensio_ajax_Load_Ticket_Reply_Form', 'mensio_ajax_Load_Ticket_Reply_Form');
add_action('wp_ajax_mensio_ajax_Send_Ticket_Reply', 'mensio_ajax_Send_Ticket_Reply');
add_action('wp_ajax_mensio_ajax_Close_Ticket_Data', 'mensio_ajax_Close_Ticket_Data');
function Mensio_Admin_Orders_Tickets() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Tickets_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Orders'));
    $Page->Set_CustomMenuItems('
              <div class="menu_button_row"> 
                <button id="BTN_Reply_Header" class="button BtnGreen" title="Reply to the ticket">
                  <i class="fa fa-reply" aria-hidden="true"></i>
                </button>
                <button id="BTN_Close_Header" class="button BtnRed" title="Close the ticket">
                  <i class="fa fa-lock" aria-hidden="true"></i>
                </button>
                <button id="BTN_Back_Header" class="button" title="Back">
                  <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </button>
              </div>');
    $Page->Set_MainPlaceHolder(
     '<h1 class="Mns_Page_HeadLine">Tickets<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      '.wp_nonce_field('Active_Page_Tickets').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_Tickets.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Table">
        <div id="TBL_Tickets_Wrapper" class="TBL_DataTable_Wrapper">
          '.$Page->GetDataTable().'
        </div>
      <div class="DivResizer"></div>
      </div>
      <div id="DIV_Edit">
        <div id="TicketHistoryWrap"></div>
        <div class="DivResizer"></div>
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Orders','Tickets'); // uniqid('MnsPrs',true);
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Tickets() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Tickets_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch);
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Load_Ticket_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Ticket = filter_var($_REQUEST['Ticket'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Tickets_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadTicketHistoryData($Ticket);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Ticket_Order_View() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $OrderID = filter_var($_REQUEST['OrderID'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Tickets_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadTicketOrderViewModal($OrderID);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Ticket_Reply_Form() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Tickets_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadTicketReplyForm();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Send_Ticket_Reply() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Ticket = filter_var($_REQUEST['Ticket'],FILTER_SANITIZE_STRING);
    $Customer = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $Reply = filter_var($_REQUEST['Reply'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Tickets_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SendTicketReply($Ticket,$Customer,$Reply);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Close_Ticket_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Ticket = filter_var($_REQUEST['Ticket'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Tickets_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->CloseTicketData($Ticket);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
