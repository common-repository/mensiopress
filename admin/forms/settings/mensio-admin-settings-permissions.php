<?php
add_action('wp_ajax_mensio_ajax_Update_Permission_Pages', 'mensio_ajax_Update_Permission_Pages');
add_action('wp_ajax_mensio_ajax_Load_User_Permission_Modal', 'mensio_ajax_Load_User_Permission_Modal');
add_action('wp_ajax_mensio_ajax_Update_Permission_User_List', 'mensio_ajax_Update_Permission_User_List');
function Mensio_Admin_Settings_UserPermissions() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Store();
    $Page->Load_Page_CSS('mensio-admin-settings-store');
    $Page->Load_Page_JS('mensio-admin-settings-permissions');
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Settings'));
    $Page->Set_CustomMenuItems('
      <div class="menu_button_row"> 
        <button id="BTN_AddPermUser_Header" class="button" title="Add User">
          <i class="fa fa-plus" aria-hidden="true"></i>
        </button>
        <button id="BTN_UpdtPrmsns_Header" class="button BtnGreen BTN_Save" title="Save">
          <i class="fa fa-floppy-o" aria-hidden="true"></i>
        </button>
      </div>');
    $DataSet = $Page->LoadStoreSettingsData();
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">User Permissions</h1>
      '.wp_nonce_field('Active_Page_Store_Settings').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_UserPermissions.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Edit">
        <div class="ProductTab">
          <div id="TabPermissions">
          <input type="hidden" id="FLD_Store" value="'.$DataSet['uuid'].'">
            <div id="PermissionsTblDiv">
              '.$Page->LoadUserPerTable().'
            </div>
            <div class="button_row">
              <button id="BTN_AddPermUser" class="button" title="Add User">
                <i class="fa fa-plus" aria-hidden="true"></i>
              </button>
              <button id="BTN_UpdtPrmsns" class="button BtnGreen BTN_Save" title="Save">
                <i class="fa fa-floppy-o" aria-hidden="true"></i>
              </button>
            </div>
            <div class="DivResizer"></div>
          </div>
        </div>
      <div class="DivResizer"></div>
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Settings','UserPermissions');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Update_Permission_Pages() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateUserPermissionList($Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_User_Permission_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadUserPermissionModal();
    }
    unset($Page);
  }
  echo $RtrnData;
  die(); 
}
function mensio_ajax_Update_Permission_User_List() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $User = filter_var($_REQUEST['User'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->AddNewUserToPermissions($User);
      if ($RtrnData['ERROR'] === 'FALSE') { $RtrnData['Message'] = $Page->LoadUserPerTable(); }
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die(); 
}