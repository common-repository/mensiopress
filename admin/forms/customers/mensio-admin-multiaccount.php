<?php
add_action('wp_ajax_mensio_ajax_Table_MultiAccount', 'mensio_ajax_Table_MultiAccount');
add_action('wp_ajax_mensio_ajax_Modal_View_MultiAccount_Customer', 'mensio_ajax_Modal_View_MultiAccount_Customer');
add_action('wp_ajax_mensio_ajax_Edit_MultiAccount', 'mensio_ajax_Edit_MultiAccount');
add_action('wp_ajax_mensio_ajax_Save_MultiAccount_Data', 'mensio_ajax_Save_MultiAccount_Data');
function Mensio_Admin_Multiaccount() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Multiaccount_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Customers'));
    $Page->Set_CustomMenuItems('
              <div class="menu_button_row">
                <button id="BTN_Save_Header" class="button BtnGreen" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
                <button id="BTN_Back_Header" class="button" title="Back">
                  <i class="fa fa-arrow-left"></i>
                </button>
              </div>') ;
    $Page->Set_MainPlaceHolder(
     '<h1 class="Mns_Page_HeadLine">Corporate<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      '.wp_nonce_field('Active_Page_MultiAccounts').' 
      <div class="PageInfo">'.MENSIO_PAGEINFO_MultiAccounts.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Table">
        <div id="TBL_MultiAccount_Wrapper" class="TBL_DataTable_Wrapper">
          '.$Page->GetDataTable().'
        </div>
      <div class="DivResizer"></div>
      </div>
      <div id="DIV_Edit">
        <div id="MultiWrap" class="Mns_Tab_Wrapper">
          <input type="hidden" id="FLD_Customer" value="" class="form-control">
          <label class="label_symbol">Industry (Sectors)</label>
          <select id="FLD_Sector" class="form-control">
            '.$Page->LoadSectorsOptions().'
          </select>
          <label class="label_symbol">Name</label>
          <input type="text" id="FLD_Name" value="" class="form-control">
          <label class="label_symbol">Tin</label>
          <input type="text" id="FLD_Tin" value="" class="form-control">
          <label class="label_symbol">Web Site</label>
          <input type="text" id="FLD_WebSite" value="" class="form-control">
          <label class="label_symbol">Main E-Mail</label>
          <input type="text" id="FLD_EMail" value="" class="form-control">    
          <div class="button_row">
            <button id="BTN_Save" class="button BtnGreen" title="Save">
              <i class="fa fa-floppy-o" aria-hidden="true"></i>
            </button>
            <button id="BTN_Back" class="button" title="Back">
              <i class="fa fa-arrow-left"></i>
            </button> 
          </div>
        <div class="DivResizer"></div>
        </div>
      <div class="DivResizer"></div>
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Customers','MultiAccounts');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_MultiAccount() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Multiaccount_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch);
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Modal_View_MultiAccount_Customer() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Customer = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Multiaccount_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->ShowMultiAccountModalInfo($Customer);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Edit_MultiAccount() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Customer = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Multiaccount_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadMultiAccountData($Customer);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Save_MultiAccount_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Multiaccount_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateMultiaccountData($Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}