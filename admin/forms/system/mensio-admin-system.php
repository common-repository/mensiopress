<?php
function Mensio_Admin_System() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_System_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('System'));
    $Page->Set_CustomMenuItems('') ;
    $Page->Set_MainPlaceHolder(
     '<h1 class="Mns_Page_HeadLine">About</h1>
      '.wp_nonce_field('Active_Page_System_About').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_SysAbout.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div class="AboutWrapper">
        <div class="logo-wrapper">
          <img src="'.plugins_url('mensiopress/admin/icons/default/mensiopress-logo-landscape-500.png').'" alt="Mensio Image">
        </div>
        <div class="text-wrapper">
          <h3>Welcome to Mensiopress,</h3>
          <p>We present you with an e-commerce web application, available as a plug-in for Wordpress. This software aims to cover the essential needs of most e-commerce business, while attempting a deeper approach than any other similar single plugin available.</p>
          <p>Main features include:
            <ul>
              <li>extensive product catalog maintenance</li>
              <li>serious multi-lingual support</li>
              <li>original block-based page builder</li>
              <li>various levels for administration privileges</li>
              <li>multi-currency support</li>
              <li>custom shipping methods</li>
              <li>basic reporting</li>
              <li>and more</li>
            </ul>
          </p>
          <p>You are welcome to install and explore. Comments and suggestions are taken into account for future releases and updates.</p>
          <p>Mensiopress was designed and coded by a team of developers and marketing experts residing and working in Athens, Greece. More information is available at <a href="http://www.mensiopress.org." target="_blank">http://www.mensiopress.org.</a></p>
        </div>
        <div class="sign-wrapper">
          <p>The Mensiopress Team,</p>
          <p>May, 2018</p>
        </div>
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('System','About');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
