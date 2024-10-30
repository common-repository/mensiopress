<?php
add_action('wp_ajax_mensio_ajax_Update_Store_UI_Settings', 'mensio_ajax_Update_Store_UI_Settings');
function Mensio_Admin_Settings_UISettings() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Store();
    $Page->Load_Page_CSS('mensio-admin-uisettings');
    $Page->Load_Page_JS('mensio-admin-uisettings');
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Settings'));
    $Page->Set_CustomMenuItems('
      <div class="menu_button_row">
        <button id="BTN_Save_Header" class="button BtnGreen Btn-ui-id Btn-ui-id-2" title="Save">
          <i class="fa fa-floppy-o" aria-hidden="true"></i>
        </button>
      </div>'
    );
    $DataSet = $Page->LoadStoreSettingsData();
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">UI Settings</h1>
      '.wp_nonce_field('Active_Page_Store_Settings').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_UISettings.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Edit">
        <div class="ProductTab">
          <input type="hidden" id="FLD_Store" value="'.$DataSet['uuid'].'">
          <label class="label_symbol">Default Table Rows Per Page</label>
          <input type="number" id="FLD_TblRows" class="form-control" value="'.$DataSet['TblRows'].'">
          <label class="label_symbol">Notification display time</label>
          <input type="number" id="FLD_NotifTime" class="form-control" value="'.$DataSet['NotifTime'].'">
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
    $Page->SetActiveSubPage('Settings','UISettings');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Update_Store_UI_Settings() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Store = filter_var($_REQUEST['Store'],FILTER_SANITIZE_STRING);
    $TblRows = filter_var($_REQUEST['TblRows'],FILTER_SANITIZE_STRING);
    $NotifTime = filter_var($_REQUEST['NotifTime'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateStoreTableView($Store,$TblRows,$NotifTime);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
