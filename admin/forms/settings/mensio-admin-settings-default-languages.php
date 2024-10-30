<?php
add_action('wp_ajax_mensio_ajax_Update_Store_Support_Languages', 'mensio_ajax_Update_Store_Support_Languages');
function Mensio_Admin_Settings_DefaultLanguages() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Store();
    $Page->Load_Page_CSS('mensio-admin-settings-store');
    $Page->Load_Page_JS('mensio-admin-settings-defaultlanguages');
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Settings'));
    $Page->Set_CustomMenuItems('
      <div class="menu_button_row"> 
        <button id="BTN_Save_Header" class="button BtnGreen Btn-ui-id Btn-ui-id-4" title="Save">
          <i class="fa fa-floppy-o" aria-hidden="true"></i>
        </button>
      </div>');
    $DataSet = $Page->LoadStoreSettingsData();
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">Default Languages</h1>
      '.wp_nonce_field('Active_Page_Store_Settings').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_DefaultLanguages.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Edit">
        <div id="TabLanguages" class="ProductTab">
          <input type="hidden" id="FLD_Store" value="'.$DataSet['uuid'].'">
          <label class="label_symbol">Main Admin Language</label>
          <select id="FLD_AdminLang" class="form-control">
            '.$Page->LoadLanguageOptions($DataSet['AdminLang']).'
          </select>
          <label class="label_symbol">Main Theme Language</label>
          <select id="FLD_ThemeLang" class="form-control">
            '.$Page->LoadLanguageOptions($DataSet['ThemeLang']).'
          </select>
          <label class="label_symbol">Theme Active Languages</label>
          <div id="LangDiv" class="ListDiv">
            '.$Page->LoadActiveThemeLanguages($DataSet['ThmActiveLang']).'
          </div>
          <div class="button_row">
            <button id="BTN_Save" class="button BtnGreen" title="Save">
              <i class="fa fa-floppy-o" aria-hidden="true"></i>
            </button>
          </div>
          <div class="DivResizer"></div>
        </div>
      <div class="DivResizer"></div>
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Settings','DefaultLanguages');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Update_Store_Support_Languages() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Store = filter_var($_REQUEST['Store'],FILTER_SANITIZE_STRING);
    $Admin = filter_var($_REQUEST['Admin'],FILTER_SANITIZE_STRING);
    $Theme = filter_var($_REQUEST['Theme'],FILTER_SANITIZE_STRING);
    $Active = filter_var($_REQUEST['Active'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateStoreSupportLanguages($Store,$Admin,$Theme,$Active);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}