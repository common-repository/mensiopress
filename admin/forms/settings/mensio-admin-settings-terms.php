<?php
add_action('wp_ajax_mensio_ajax_Update_Store_Use_Terms', 'mensio_ajax_Update_Store_Use_Terms');
add_action('wp_ajax_mensio_ajax_Update_Publish_Terms', 'mensio_ajax_Update_Publish_Terms');
add_action('wp_ajax_mensio_Show_Terms_View_Modal', 'mensio_Show_Terms_View_Modal');
add_action('wp_ajax_mensio_Edit_Terms_Of_Use', 'mensio_Edit_Terms_Of_Use');
add_action('wp_ajax_mensio_Remove_Terms_Of_Use', 'mensio_Remove_Terms_Of_Use');
function Mensio_Admin_Settings_TermsOfService() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Store();
    $Page->Load_Page_CSS('mensio-admin-settings-store');
    $Page->Load_Page_JS('mensio-admin-settings-terms');
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Settings'));
    $Page->Set_CustomMenuItems('
      <div class="menu_button_row"> 
        <button id="BTN_NewTerms_Header" class="button" title="New">
          <i class="fa fa-plus" aria-hidden="true"></i>
        </button>
        <button id="BTN_UpdtPrmsns_Header" class="button BtnGreen BTN_Save" title="Save">
          <i class="fa fa-floppy-o" aria-hidden="true"></i>
        </button>
        <button id="BTN_Publish_Header" class="button" title="Publish">
          <i class="fa fa-upload" aria-hidden="true"></i>
        </button>
        <button id="BTN_DelTerm_Header" class="button BtnRed" title="Delete">
          <i class="fa fa-times" aria-hidden="true"></i>
        </button>
      </div>');
    $DataSet = $Page->LoadStoreSettingsData();
    ob_start();
    wp_editor($DataSet['Term'],'FLD_TermsOfUse');
    $TermsOfUseEditor = ob_get_clean();
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">Terms of Service</h1>
      '.wp_nonce_field('Active_Page_Store_Settings').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_TermsOfService.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Edit">
        <div id="TabUseTerms" class="ProductTab">
          <input type="hidden" id="FLD_Store" value="'.$DataSet['uuid'].'">
          <div id="TermsListDiv" class="TermsCols">
            <label class="label_symbol">Terms List</label>
            <hr>
            <div id="TermsListWraper">
              '.$Page->LoadTermsOfUseList($DataSet['uuid'],$DataSet['TermID']).'
            </div>
            <div class="DivResizer"></div>
          </div>
          <div id="TermsEditDiv" class="TermsCols">
            <input type="hidden" id="FLD_TermsCode" value="'.$DataSet['TermID'].'">
            <div class="PublDateDiv">
              Last Updated : <span id="PublishedWrap"></span>
            </div>
            <div class="TermsWrapper">
              '.$TermsOfUseEditor.'
            </div>
            <div class="DivResizer"></div>
          </div>
          <div class="DivResizer"></div>
          <div class="button_row">
            <button id="BTN_NewTerms" class="button" title="New">
              <i class="fa fa-plus" aria-hidden="true"></i>
            </button>
            <button id="BTN_TermsOfUse" class="button BtnGreen BTN_Save" title="Save">
              <i class="fa fa-floppy-o" aria-hidden="true"></i>
            </button>
            <button id="BTN_Publish" class="button" title="Publish">
              <i class="fa fa-upload" aria-hidden="true"></i>
            </button>
            <button id="BTN_DelTerm" class="button BtnRed" title="Delete">
              <i class="fa fa-times" aria-hidden="true"></i>
            </button>
          </div>
          <div class="DivResizer"></div>
        </div>
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Settings','TermsOfService');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Update_Store_Use_Terms() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Store = filter_var($_REQUEST['Store'],FILTER_SANITIZE_STRING);
    $Term = filter_var($_REQUEST['Term'],FILTER_SANITIZE_STRING);
    $TermsOfUse = wp_kses_post($_REQUEST['TermsOfUse']);
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateStoreUseTerms($Store,$Term,$TermsOfUse);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die(); 
}
function mensio_ajax_Update_Publish_Terms() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Store = filter_var($_REQUEST['Store'],FILTER_SANITIZE_STRING);
    $Term = filter_var($_REQUEST['Term'],FILTER_SANITIZE_STRING);
    $TermsOfUse = wp_kses_post($_REQUEST['TermsOfUse']);
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateStoreUseTerms($Store,$Term,$TermsOfUse);
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Term = $RtrnData['Term'];
      $RtrnData = $Page->UpdatePublishTerms($Store,$Term);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die(); 
}
function mensio_Show_Terms_View_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Term = filter_var($_REQUEST['Term'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadTermsViewModal($Term);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_Edit_Terms_Of_Use() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Term = filter_var($_REQUEST['Term'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadTermsViewData($Term);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_Remove_Terms_Of_Use() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Store = filter_var($_REQUEST['Store'],FILTER_SANITIZE_STRING);
    $Term = filter_var($_REQUEST['Term'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RemoveTermsViewData($Store,$Term);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}