<?php
function Mensio_Admin_System_Register() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Register_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('System'));
    $Page->Set_CustomMenuItems('') ;
    $Page->Set_MainPlaceHolder(
     '<h1 class="Mns_Page_HeadLine">Registration Info</h1>
      '.wp_nonce_field('Active_Page_User_Registration').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_SysRegister.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_DashWrapper">
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('System','Register');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
