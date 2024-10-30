<?php
add_action('wp_ajax_mensio_ajax_Update_PayOnDelivery_Data', 'mensio_ajax_Update_PayOnDelivery_Data');
add_action('wp_ajax_mensio_ajax_Load_Payment_Translations_Modal', 'mensio_ajax_Load_Payment_Translations_Modal');
add_action('wp_ajax_mensio_ajax_Load_Payment_Language_Translations', 'mensio_ajax_Load_Payment_Language_Translations');
add_action('wp_ajax_mensio_ajax_Update_Payment_Language_Translations', 'mensio_ajax_Update_Payment_Language_Translations');
add_action('wp_ajax_mensio_ajax_Update_BankDeposit_Data', 'mensio_ajax_Update_BankDeposit_Data');
add_action('wp_ajax_mensio_ajax_Load_Bank_Account_Modal', 'mensio_ajax_Load_Bank_Account_Modal');
add_action('wp_ajax_mensio_ajax_Update_Bank_Account_Data', 'mensio_ajax_Update_Bank_Account_Data');
add_action('wp_ajax_mensio_ajax_Remove_Bank_Account_Data', 'mensio_ajax_Remove_Bank_Account_Data');
add_action('wp_ajax_mensio_ajax_Update_Gateway_Data', 'mensio_ajax_Update_Gateway_Data');
add_action('wp_ajax_mensio_ajax_Load_Default_Landing_Pages_Modal', 'mensio_ajax_Load_Default_Landing_Pages_Modal');
add_action('wp_ajax_mensio_ajax_Load_Update_Landing_Pages', 'mensio_ajax_Load_Update_Landing_Pages');
function Mensio_Admin_Settings_Payments() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Payment_Methods_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Settings'));
    $Page->Set_CustomMenuItems('
              <div class="menu_button_row"> 
                <button id="BTN_Save_Header" class="button BtnGreen" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
              </div>');
    $Page->Set_MainPlaceHolder('
      <h1 class="Mns_Page_HeadLine">Payment Methods<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      <div id="ButtonArea">
        <button id="BTN_DfltLndPages" title="Set Default Landing Pages">
          <i class="fa fa-pencil action-icon" aria-hidden="true"></i>
          Set Default Landing Pages
        </button>
      </div>
      '.wp_nonce_field('Active_Page_Payment_Methods').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_PaymentMethods.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Edit">
        <input type="hidden" id="ActiveTab" value="OnDelivery">
        <div id="tabs">'.$Page->CreatePaymentTab().'</div>
        <div class="DivResizer"></div>
        <div class="button_row">
          <button id="BTN_Save" class="button BtnGreen" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>
        </div>
        <div class="DivResizer"></div>
      </div>');
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Settings','PaymentMethods');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Update_PayOnDelivery_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $PoD = filter_var($_REQUEST['PoD'],FILTER_SANITIZE_STRING);
    $Active = filter_var($_REQUEST['active'],FILTER_SANITIZE_STRING);
    $Language = filter_var($_REQUEST['Lang'],FILTER_SANITIZE_STRING);
    $Description = filter_var($_REQUEST['Descr'],FILTER_SANITIZE_STRING);
    $Notes = filter_var($_REQUEST['Notes'],FILTER_SANITIZE_STRING);
    $ShipOpt = filter_var($_REQUEST['ShipOpt'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Payment_Methods_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdatePayOnDeliveryData($PoD,$Active,$Language,$Description,$Notes,$ShipOpt);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Payment_Translations_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $PoP = filter_var($_REQUEST['Pay'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Payment_Methods_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadPaymentTranslationsModal($PoP);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Payment_Language_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Payment = filter_var($_REQUEST['Payment'],FILTER_SANITIZE_STRING);
    $Language = filter_var($_REQUEST['Language'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Payment_Methods_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadPaymentTranslations($Payment,$Language);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Payment_Language_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Payment = filter_var($_REQUEST['Payment'],FILTER_SANITIZE_STRING);
    $Language = filter_var($_REQUEST['Language'],FILTER_SANITIZE_STRING);
    $Description = filter_var($_REQUEST['Desc'],FILTER_SANITIZE_STRING);
    $Notes = filter_var($_REQUEST['Notes'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Payment_Methods_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdatePaymentTranslations($Payment,$Language,$Description,$Notes);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_BankDeposit_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Pay = filter_var($_REQUEST['Pay'],FILTER_SANITIZE_STRING);
    $Active = filter_var($_REQUEST['Active'],FILTER_SANITIZE_STRING);
    $Language = filter_var($_REQUEST['Lang'],FILTER_SANITIZE_STRING);
    $Description = filter_var($_REQUEST['Descr'],FILTER_SANITIZE_STRING);
    $Notes = filter_var($_REQUEST['Notes'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Payment_Methods_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateBankDepositData($Pay,$Active,$Language,$Description,$Notes);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Bank_Account_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $BnkAccnt = filter_var($_REQUEST['BnkAccnt'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Payment_Methods_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadBankAccountModal($BnkAccnt);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Bank_Account_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Pay = filter_var($_REQUEST['Pay'],FILTER_SANITIZE_STRING);
    $Account = filter_var($_REQUEST['Account'],FILTER_SANITIZE_STRING);
    $Icon = filter_var($_REQUEST['Icon'],FILTER_SANITIZE_STRING);
    $Bank = filter_var($_REQUEST['Bank'],FILTER_SANITIZE_STRING);
    $Name = filter_var($_REQUEST['Name'],FILTER_SANITIZE_STRING);
    $Number = filter_var($_REQUEST['Number'],FILTER_SANITIZE_STRING);
    $Routing = filter_var($_REQUEST['Routing'],FILTER_SANITIZE_STRING);
    $IBAN = filter_var($_REQUEST['IBAN'],FILTER_SANITIZE_STRING);
    $Swift = filter_var($_REQUEST['Swift'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Payment_Methods_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateBankDepositAccountData($Pay,$Account,$Icon,$Bank,$Name,$Number,$Routing,$IBAN,$Swift);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Remove_Bank_Account_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Pay = filter_var($_REQUEST['Pay'],FILTER_SANITIZE_STRING);
    $Account = filter_var($_REQUEST['BnkAccnt'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Payment_Methods_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RemoveBankDepositAccountData($Pay,$Account);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Gateway_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Gateway = filter_var($_REQUEST['Gateway'],FILTER_SANITIZE_STRING);
    $Pay = filter_var($_REQUEST['Pay'],FILTER_SANITIZE_STRING);
    $Active = filter_var($_REQUEST['Active'],FILTER_SANITIZE_STRING);
    $Language = filter_var($_REQUEST['Lang'],FILTER_SANITIZE_STRING);
    $Description = filter_var($_REQUEST['Descr'],FILTER_SANITIZE_STRING);
    $Notes = filter_var($_REQUEST['Notes'],FILTER_SANITIZE_STRING);
    $Params = $_REQUEST['Params'];
    $Page = new Mensio_Admin_Payment_Methods_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateGateWayData($Gateway,$Pay,$Active,$Language,$Description,$Notes,$Params);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Default_Landing_Pages_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $BnkAccnt = filter_var($_REQUEST['BnkAccnt'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Payment_Methods_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadDefaultLandingPagesModal();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Update_Landing_Pages() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $OldSuccess = filter_var($_REQUEST['OldSuccess'],FILTER_SANITIZE_STRING);
    $Success = filter_var($_REQUEST['Success'],FILTER_SANITIZE_STRING);
    $OldFailed = filter_var($_REQUEST['OldFailed'],FILTER_SANITIZE_STRING);
    $Failed = filter_var($_REQUEST['Failed'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Payment_Methods_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateLandingPagesData($OldSuccess,$Success,$OldFailed,$Failed);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}