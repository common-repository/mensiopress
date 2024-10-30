'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
jQuery( function() { jQuery("#tabs").tabs(); });
function Mensio_Settings_UpdateStoreMail($CheckMail) {
  var $Store = jQuery('#FLD_Store').val();
  var $Mailer = jQuery('#FLD_Mailer').val();
  var $Host = jQuery('#FLD_Host').val();
  var $SMTPAuth = jQuery('#FLD_SMTPAuth').val();
  var $SMTPSecure = jQuery('#FLD_SMTPSecure').val();
  var $Port = jQuery('#FLD_Port').val();
  var $Username = jQuery('#FLD_Username').val();
  var $Password = jQuery('#FLD_Password').val();
  var $From = jQuery('#FLD_From').val();
  var $FromName = jQuery('#FLD_FromName').val();
  var $MailsPerMinute = jQuery('#FLD_MailsPerMinute').val();
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Update_Store_Mail_Settings',
      'Store': $Store,
      'Mailer': $Mailer,
      'Host': $Host,
      'SMTPAuth': $SMTPAuth,
      'SMTPSecure': $SMTPSecure,
      'Port': $Port,
      'Username': $Username,
      'Password': $Password,
      'From': $From,
      'FromName': $FromName,
      'MailsPerMinute': $MailsPerMinute
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      Mensio_Append_New_PopUp(data.Message);
      jQuery('#NOSAVEWARN').hide();
      if ($CheckMail) {
        var $Address = jQuery('#FLD_TstMlAddr').val();
        jQuery.ajax({
          type: 'post',
          url: ajaxurl,
          dataType: 'text',
          data: { 'Security': $sec,
            'action': 'mensio_ajax_Store_Check_Mail',
            'Address': $Address
          },
          success:function(data) {
            data = jQuery.parseJSON(data);
            jQuery('#Mns_PopUp_Wrapper').hide();
            Mensio_Append_New_PopUp(data.Message);
          },
          error: function(errorThrown){
            alert(errorThrown);
          }
        });
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Settings_SaveMailTemplate() {
  var $id = jQuery('.ActiveTemplate').attr('id');
  $id = $id.replace('Btn_','');
  var $Template = Mensio_tmce_getContent('FLD_MailTemplates');
  var $Test = $Template.replace(/\n/g, '');
  $Test = $Test.replace(/\s+/g, '');
  if ($Test === '') { $Template = 'EMPTY'; }
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Update_Mail_Template',
      'Name': $id,
      'Template': $Template
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') { jQuery('#NOSAVEWARN').hide(); }
      Mensio_Append_New_PopUp(data.Message);
      Mensio_tmce_setContent(Mensio_stripJSONslashes(data.Template),'FLD_MailTemplates');
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Load_Mail_Template_View($ShowForm) {
  var $Template = Mensio_tmce_getContent('FLD_MailTemplates');
  var $Test = $Template.replace(/\n/g, '');
  $Test = $Test.replace(/\s+/g, '');
  if ($Test !== '') {
    console.log('template found');
    jQuery('#MailTempPreview').html($Template);
    if ($ShowForm) { jQuery('#MailTempPreviewDIV').slideToggle(); }
  } else {
    console.log('template empty');
  }
}
jQuery(document).ready(function() {
  jQuery('.Btn-ui-id').hide();
  jQuery('.Btn-ui-id-1').show();
  jQuery('.ui-tabs-anchor').click(function(){
    var tab = jQuery(this).attr('id');
    jQuery('.Btn-ui-id').hide();
    tab = '.Btn-'+tab;
    jQuery(tab).show();
  });
  jQuery('#DIV_Edit').on('click', '.BTN_Save', function() {
    var $id = jQuery(this).attr('id');
    $id = $id.replace('BTN_','');
    switch ($id) {
      case 'SaveMail':
        Mensio_Settings_UpdateStoreMail(false);
        break;
      case 'SaveMailTemplate':
        Mensio_Settings_SaveMailTemplate();
        break;
    }
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#Mensio_HeadBar').on('click', '.BTN_Save', function() {
    var $id = jQuery(this).attr('id');
    $id = $id.replace('BTN_','');
    $id = $id.replace('_Header','');
    switch ($id) {
      case 'SaveMail':
        Mensio_Settings_UpdateStoreMail(false);
        break;
      case 'SaveMailTemplate':
        Mensio_Settings_SaveMailTemplate();
        break;
    }
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#DIV_Edit').on('change', '#FLD_Mailer', function() {
    var $val = jQuery(this).val();
    if ($val === 'smtp') { jQuery('#RemoteWraper').slideDown(); }
      else { 
        jQuery('#RemoteWraper').slideUp();
        jQuery('#FLD_Host').val('');
        jQuery('#FLD_SMTPAuth').val(0);
        jQuery('#FLD_SMTPSecure').val('ssl');
        jQuery('#FLD_Port').val('');
        jQuery('#FLD_Username').val('');
        jQuery('#FLD_Password').val('');
        jQuery('#FLD_From').val('');
        jQuery('#FLD_FromName').val('');
    }
  });
  jQuery('#DIV_Edit').on('click', '#Btn_SendTestMail', function() {
    Mensio_Settings_UpdateStoreMail(true);
  });
  jQuery('#DIV_Edit').on('click', '.TemplateSelector', function() {
    var $Store = jQuery('#FLD_Store').val();
    var $id = jQuery(this).attr('id');
    jQuery('.TemplateSelector').removeClass('ActiveTemplate');
    jQuery('#'+$id).addClass('ActiveTemplate');
    $id = $id.replace('Btn_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      dataType: 'text',
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Template_For_Edit',
        'Store' : $Store,
        'Template' : $id
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          Mensio_tmce_setContent(Mensio_stripJSONslashes(data.Template),'FLD_MailTemplates');
          jQuery('#NOSAVEWARN').hide();
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#Btn_TempInfo', function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Mail_Template_Info_modal'
      },
      success:function(data) {
        jQuery('#MnsModal').html(data);
        jQuery('#MnsModal').toggle('slide');
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#Mensio_HeadBar').on('click', '#Btn_TempInfo_Header', function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Mail_Template_Info_modal'
      },
      success:function(data) {
        jQuery('#MnsModal').html(data);
        jQuery('#MnsModal').toggle('slide');
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#BTN_ViewMailTemplate', function() {
    Mensio_Load_Mail_Template_View(true);
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_ViewMailTemplate_Header', function() {
    Mensio_Load_Mail_Template_View(true);
  });
  jQuery('#DIV_Edit').on('click', '#BTN_TempPreviewClose', function() {
    jQuery('#MailTempPreviewDIV').slideToggle();
  });
  jQuery('#DIV_Edit').on('keyup', '#FLD_MailTemplates', function() {
    Mensio_Load_Mail_Template_View(false);
  });
   jQuery('#DIV_Edit').on('click', '#Btn_TempTest', function() {
    var $Store = jQuery('#FLD_Store').val();
    var $Address = jQuery('#FLD_TstMlAddr').val();
    var $Template = jQuery('.ActiveTemplate').attr('id');
    $Template = $Template.replace('Btn_','');
    switch($Template) {
      case 'Sales': case 'Status': case 'Ticket': case 'Register': 
        jQuery.ajax({
          type: 'post',
          url: ajaxurl,
          dataType: 'text',
          data: { 'Security': $sec,
            'action': 'mensio_ajax_Store_Send_Test_Mail',
            'Store': $Store,
            'Template': $Template,
            'Address': $Address
          },
          success:function(data) {
            data = jQuery.parseJSON(data);
            jQuery('#Mns_PopUp_Wrapper').hide();
            Mensio_Append_New_PopUp(data.Message);
          },
          error: function(errorThrown){
            alert(errorThrown);
          }
        });
        break;
    }
  });
  jQuery('#Mensio_HeadBar').on('click', '#Btn_TempTest_Header', function() {
    var $Store = jQuery('#FLD_Store').val();
    var $Address = jQuery('#FLD_TstMlAddr').val();
    var $Template = jQuery('.ActiveTemplate').attr('id');
    $Template = $Template.replace('Btn_','');
    switch($Template) {
      case 'Sales': case 'Status': case 'Ticket': case 'Register': 
        jQuery.ajax({
          type: 'post',
          url: ajaxurl,
          dataType: 'text',
          data: { 'Security': $sec,
            'action': 'mensio_ajax_Store_Send_Test_Mail',
            'Store': $Store,
            'Template': $Template,
            'Address': $Address
          },
          success:function(data) {
            data = jQuery.parseJSON(data);
            jQuery('#Mns_PopUp_Wrapper').hide();
            Mensio_Append_New_PopUp(data.Message);
          },
          error: function(errorThrown){
            alert(errorThrown);
          }
        });
        break;
    }
  });
 });