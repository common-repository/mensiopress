<?php
add_action('wp_ajax_mensio_ajax_Update_Store_Mail_Settings', 'mensio_ajax_Update_Store_Mail_Settings');
add_action('wp_ajax_mensio_ajax_Store_Check_Mail', 'mensio_ajax_Store_Check_Mail');
add_action('wp_ajax_mensio_ajax_Load_Template_For_Edit', 'mensio_ajax_Load_Template_For_Edit');
add_action('wp_ajax_mensio_ajax_Update_Mail_Template', 'mensio_ajax_Update_Mail_Template');
add_action('wp_ajax_mensio_ajax_Load_Mail_Template_Info_modal', 'mensio_ajax_Load_Mail_Template_Info_modal');
add_action('wp_ajax_mensio_ajax_Store_Send_Test_Mail', 'mensio_ajax_Store_Send_Test_Mail');
function Mensio_Admin_Settings_MailSettings() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Store();
    $Page->Load_Page_CSS('mensio-admin-settings-store');
    $Page->Load_Page_JS('mensio-admin-settings-mail');
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Settings'));
    $Page->Set_CustomMenuItems('
      <div class="menu_button_row"> 
        <span class="Btn-ui-id Btn-ui-id-1">
          <button id="Btn_TempInfo_Header" class="button" title="Help">
            <i class="fa fa-question-circle fa-lg" aria-hidden="true"></i>
          </button>
          <button id="Btn_TempTest_Header" class="button" title="Send Test Mail">
            <i class="fa fa-envelope" aria-hidden="true"></i>
          </button>
          <button id="BTN_ViewMailTemplate_Header" class="button" title="Open Preview">
            <i class="fa fa-eye" aria-hidden="true"></i>
          </button>
          <button id="BTN_SaveMailTemplate_Header" class="button BtnGreen BTN_Save" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>
        </span>
        <button id="BTN_SaveMail_Header" class="button BtnGreen BTN_Save Btn-ui-id Btn-ui-id-2" title="Save">
          <i class="fa fa-floppy-o" aria-hidden="true"></i>
        </button>
      </div>');
    $DataSet = $Page->LoadStoreSettingsData();
    $FLD_MailsPerMinute = '<option value="1">1</option>
      <option value="2">2</option>
      <option value="3">3</option>
      <option value="4">4</option>
      <option value="5">5</option>
      <option value="6">6</option>
      <option value="7">7</option>
      <option value="8">8</option>
      <option value="9">9</option>
      <option value="10">10</option>';
    $FLD_MailsPerMinute = str_replace('value="'.$DataSet['MailsPerMinute'].'"', 'value="'.$DataSet['MailsPerMinute'].'" selected', $FLD_MailsPerMinute);
    if ($DataSet['MailSettings'] === 'sendmail') {
      $MailTypeOptions = '<option value="sendmail" selected>Local Server</option><option value="smtp">Remote Server</option>';
      $SMTPWraper = 'Hidden';
      $FLD_Host = '';
      $FLD_SMTPAuth = '<option value="0" selected>No</option><option value="1">Yes</option>';
      $FLD_SMTPSecure = '<option value="ssl" selected>SSL</option><option value="tls">TLS</option>';
      $FLD_Port = '';
      $FLD_Username = '';
      $FLD_Password = '';
      $FLD_From = '';
      $FLD_FromName = '';
    } else {
      $MailTypeOptions = '<option value="sendmail">Local Server</option><option value="smtp" selected>Remote Server</option>';
      $SMTPWraper = '';
      $FLD_Host = $DataSet['Host'];
      $FLD_SMTPAuth = '<option value="0" selected>No</option><option value="1">Yes</option>';
      if ($DataSet['SMTPAuth'] === '1') {
        $FLD_SMTPAuth = '<option value="0">No</option><option value="1" selected>Yes</option>';
      }
      $FLD_SMTPSecure = '<option value="ssl" selected>SSL</option><option value="tls">TLS</option>';
      if ($DataSet['SMTPSecure'] === 'tls') {
        $FLD_SMTPSecure = '<option value="ssl">SSL</option><option value="tls" selected>TLS</option>';
      }
      $FLD_Port = $DataSet['Port'];
      $FLD_Username = $DataSet['Username'];
      $FLD_Password = $DataSet['Password'];
      $FLD_From = $DataSet['From'];
      $FLD_FromName = $DataSet['FromName'];
    }
    ob_start();
    wp_editor('','FLD_MailTemplates',array('tinymce'=>false));
    $MailTemplatesEditor = ob_get_clean(); 
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">Mail Settings</h1>
      '.wp_nonce_field('Active_Page_Store_Settings').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_MailSettings.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Edit">
        <div id="tabs">
          <ul>
            <li><a href="#TabMailTemplates">
              <i class="fa fa-file-text-o" aria-hidden="true"></i>
              Templates
            </a></li>
            <li><a href="#TabMail">
              <i class="fa fa-envelope" aria-hidden="true"></i>
              Settings
            </a></li>
          </ul>
          <div id="TabMailTemplates" class="TermsCols">
            <input type="hidden" id="FLD_Store" value="'.$DataSet['uuid'].'">
            <div class="MailTmplWraper">
              <div class="MailTmplSlctr">
                <label class="label_symbol">Select Template</label>
                <hr>
                <div id="Btn_GeneralMail" class="TemplateSelector">General Mail</div>
                <div id="Btn_Sales" class="TemplateSelector">Sales Invoice</div>
                <div id="Btn_Status" class="TemplateSelector">Sales Status</div>
                <div id="Btn_Ticket" class="TemplateSelector">Ticket Update Info</div>
                <div id="Btn_Register" class="TemplateSelector">Registration Confirmation</div>
                <div id="Btn_PswdConfirm" class="TemplateSelector">Password Confirmation</div>
              </div>
              <div class="MailTmplEdit">
                '.$MailTemplatesEditor.'
              </div>
              <div id="MailTempPreviewDIV">
                <div id="MailTempPreview"></div>
                <div class="button_row">
                  <button id="BTN_TempPreviewClose" class="button" title="Close Preview">
                    <i class="fa fa-eye-slash" aria-hidden="true"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="button_row">
              <button id="Btn_TempInfo" class="button" title="Help">
                <i class="fa fa-question-circle fa-lg" aria-hidden="true"></i>
              </button>
              <button id="Btn_TempTest" class="button" title="Send Test Mail">
                <i class="fa fa-envelope" aria-hidden="true"></i>
              </button>
              <button id="BTN_ViewMailTemplate" class="button" title="Open Preview">
                <i class="fa fa-eye" aria-hidden="true"></i>
              </button>
              <button id="BTN_SaveMailTemplate" class="button BtnGreen BTN_Save" title="Save">
                <i class="fa fa-floppy-o" aria-hidden="true"></i>
              </button>
            </div>
            <div class="DivResizer"></div>
          </div>
          <div id="TabMail" class="TermsCols">
            <div class="TabMailWraper">
              <div class="clshddn">
                <label class="label_symbol">Type</label>
                <select id="FLD_Mailer" class="form-control">
                  '.$MailTypeOptions .'
                </select>
              </div>
              <div id="RemoteWraper" class="'.$SMTPWraper.'">
                <label class="label_symbol">Host</label>
                <input type="text" id = "FLD_Host" class="form-control" value ="'.$FLD_Host.'">
                <label class="label_symbol">Enable SMTP Authentication</label>
                <select id="FLD_SMTPAuth" class="form-control">
                  '.$FLD_SMTPAuth.'
                </select>
                <label class="label_symbol">SMTP Security Protocol</label>
                <select id="FLD_SMTPSecure" class="form-control">
                  '.$FLD_SMTPSecure.'
                </select>
                <label class="label_symbol">Port</label>
                <input type="text" id = "FLD_Port" class="form-control" value ="'.$FLD_Port.'">
                <label class="label_symbol">Username</label>
                <input type="text" id = "FLD_Username" class="form-control" value ="'.$FLD_Username.'">
                <label class="label_symbol">Password</label>
                <input type="text" id = "FLD_Password" class="form-control" value ="'.$FLD_Password.'">
                <label class="label_symbol">From Address</label>
                <input type="text" id = "FLD_From" class="form-control" value ="'.$FLD_From.'">
                <label class="label_symbol">From User</label>
                <input type="text" id = "FLD_FromName" class="form-control" value ="'.$FLD_FromName.'">
              </div>
              <label class="label_symbol">Number of Sending E-Mails Per Minute</label>
              <select id="FLD_MailsPerMinute" class="form-control">
                '.$FLD_MailsPerMinute.'
              </select>
              <label class="label_symbol">Test Mail Address</label>
              <input type="text" id = "FLD_TstMlAddr" class="form-control" value ="">
              <button id="Btn_SendTestMail" class="button BtnGreen">
                <i class="fa fa-envelope" aria-hidden="true"></i>
                Send Test Mail
              </button>
            </div>
            <div class="button_row">
              <button id="BTN_SaveMail" class="button BtnGreen BTN_Save" title="Save">
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
    $Page->SetActiveSubPage('Settings','MailSettings');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Update_Store_Mail_Settings() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Store = filter_var($_REQUEST['Store'],FILTER_SANITIZE_STRING);
    $Mailer = filter_var($_REQUEST['Mailer'],FILTER_SANITIZE_STRING);
    $Host = filter_var($_REQUEST['Host'],FILTER_SANITIZE_STRING);
    $SMTPAuth = filter_var($_REQUEST['SMTPAuth'],FILTER_SANITIZE_STRING);
    $SMTPSecure = filter_var($_REQUEST['SMTPSecure'],FILTER_SANITIZE_STRING);
    $Port = filter_var($_REQUEST['Port'],FILTER_SANITIZE_STRING);
    $Username = filter_var($_REQUEST['Username'],FILTER_SANITIZE_STRING);
    $Password = $_REQUEST['Password'];
    $From = filter_var($_REQUEST['From'],FILTER_SANITIZE_STRING);
    $FromName = filter_var($_REQUEST['FromName'],FILTER_SANITIZE_STRING);
    $MailsPerMinute = filter_var($_REQUEST['MailsPerMinute'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateStoreMailSettings($Store,$Mailer,$Host,$SMTPAuth,$SMTPSecure,$Port,$Username,$Password,$From,$FromName,$MailsPerMinute);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die(); 
}
function mensio_ajax_Store_Check_Mail() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Address = filter_var($_REQUEST['Address'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SendTestMail($Address);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die(); 
}
function mensio_ajax_Store_Send_Test_Mail() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Store = filter_var($_REQUEST['Store'],FILTER_SANITIZE_STRING);
    $Template = filter_var($_REQUEST['Template'],FILTER_SANITIZE_STRING);
    $Address = filter_var($_REQUEST['Address'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SendTestMail($Address,$Template,$Store);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die(); 
}
function mensio_ajax_Load_Template_For_Edit() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Store = filter_var($_REQUEST['Store'],FILTER_SANITIZE_STRING);
    $Template = filter_var($_REQUEST['Template'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadTemplateForEditing($Store,$Template);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die(); 
}
function mensio_ajax_Update_Mail_Template() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Name = filter_var($_REQUEST['Name'],FILTER_SANITIZE_STRING);
    $Template = addslashes($_REQUEST['Template']);
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateMailTemplate($Name,$Template);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die(); 
}
function mensio_ajax_Load_Mail_Template_Info_modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadMailTemplateInfo();
    }
    unset($Page);
  }
  echo $RtrnData;
  die(); 
}