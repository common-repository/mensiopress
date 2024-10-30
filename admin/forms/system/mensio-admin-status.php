<?php
function Mensio_Admin_System_Status() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Status_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('System'));
    $Page->Set_CustomMenuItems('') ;
    global $wp_version;
    $SqlInfo = $Page->GetMySQLInfo();
    $Page->Set_MainPlaceHolder(
     '<h1 class="Mns_Page_HeadLine">Status Info</h1>
      '.wp_nonce_field('Active_Page_System_Status').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_SysStatus.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Edit">
        <div class="DashPanel"> 
          <div class="PanelHeader">
            <div class="PanelName">Mensiopress</div>
          </div>
          <div class="PanelInfo">
            <table class="infotbl">
              <tr>
                <td class="LblCol">Mensiopress version</td>
                <td class="InfoCol">'.MENSIO_VERSION.'</td>
              </tr>
              <tr>
                <td class="LblCol">Mensiopress Path</td>
                <td class="InfoCol">'.MENSIO_PATH.'</td>
              </tr>
              <tr>
                <td class="LblCol">Mensiopress Short Path</td>
                <td class="InfoCol">'.MENSIO_SHORTPATH.'</td>
              </tr>
              <tr>
                <td class="LblCol">Mensiopress Uploads Path</td>
                <td class="InfoCol">'.MENSIO_UPLOAD_DIR.'</td>
              </tr>
            </table>
          </div>
          <div class="PanelFooter"></div>
        </div>
        <div class="DashPanel"> 
          <div class="PanelHeader">
            <div class="PanelName">Wordpress</div>
          </div>
          <div class="PanelInfo">
            <table class="infotbl">
              <tr>
                <td class="LblCol">Wordpress Version</td>
                <td class="InfoCol">'.$wp_version.'</td>
              </tr>
              <tr>
                <td class="LblCol">Debug Mode</td>
                <td class="InfoCol">'.$Page->GetDebugIcon().'</td>
              </tr>
              <tr>
                <td class="LblCol">Language</td>
                <td class="InfoCol">'.get_locale().'</td>
              </tr>
              <tr>
                <td class="LblCol">Home URL</td>
                <td class="InfoCol">'.home_url().'</td>
              </tr>
              <tr>
                <td class="LblCol">Content URL</td>
                <td class="InfoCol">'.content_url().'</td>
              </tr>
              <tr>
                <td class="LblCol">Content DIR</td>
                <td class="InfoCol">'.WP_CONTENT_DIR.'</td>
              </tr>
              <tr>
                <td class="LblCol">Plugins URL</td>
                <td class="InfoCol">'.plugins_url().'</td>
              </tr>
              <tr>
                <td class="LblCol">Plugins DIR</td>
                <td class="InfoCol">'.WP_PLUGIN_DIR.'</td>
              </tr>
              <tr>
                <td class="LblCol">Is Multisite</td>
                <td class="InfoCol">'.$Page->GetMultisiteIcon().'</td>
              </tr>
            </table>
          </div>
          <div class="PanelFooter"></div>
        </div>
        <div class="DashPanel"> 
          <div class="PanelHeader">
            <div class="PanelName">Server</div>
          </div>
          <div class="PanelInfo">
            <table class="infotbl">
              <tr>
                <td class="LblCol">Server version</td>
                <td class="InfoCol">'.$_SERVER['SERVER_SOFTWARE'].'</td>
              </tr>
              <tr>
                <td class="LblCol">Server Protocol</td>
                <td class="InfoCol">'.$_SERVER['SERVER_PROTOCOL'].'</td>
              </tr>
              <tr>
                <td class="LblCol">SQL Version</td>
                <td class="InfoCol">'.$SqlInfo['Version'].'</td>
              </tr>
              <tr>
                <td class="LblCol">SQL Info</td>
                <td class="InfoCol">'.$SqlInfo['Info'].'</td>
              </tr>
              <tr>
                <td class="LblCol">PHP version</td>
                <td class="InfoCol">'.phpversion().'</td>
              </tr>
              <tr>
                <td class="LblCol">Time Limit</td>
                <td class="InfoCol">'.ini_get('max_execution_time').'</td>
              </tr>
              <tr>
                <td class="LblCol">Post Max Size</td>
                <td class="InfoCol">'.ini_get('post_max_size').'</td>
              </tr>
              <tr>
                <td class="LblCol">Max File Upload</td>
                <td class="InfoCol">'.ini_get('max_file_uploads').'</td>
              </tr>
              <tr>
                <td class="LblCol">PHP Extensions</td>
                <td class="InfoCol">'.$Page->GetPHPExtensions().'</td>
              </tr>
            </table>
          </div>
          <div class="PanelFooter"></div>
        </div>      
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('System','Status');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
