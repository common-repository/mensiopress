'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
var $Loading;
var $PopUpTmOut = '';
function Mensio_tmce_getContent(editor_id, textarea_id) {
  if ( typeof editor_id === 'undefined' ) editor_id = wpActiveEditor;
  if ( typeof textarea_id === 'undefined' ) textarea_id = editor_id;
  if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
    return tinyMCE.get(editor_id).getContent();
  }else{
    return jQuery('#'+textarea_id).val();
  }
}
function Mensio_tmce_setContent(content, editor_id, textarea_id) {
  if ( typeof editor_id === 'undefined' ) editor_id = wpActiveEditor;
  if ( typeof textarea_id === 'undefined' ) textarea_id = editor_id;
  if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
    return tinyMCE.get(editor_id).setContent(content);
  }else{
    return jQuery('#'+textarea_id).val(content);
  }
  $OldEditVal = content;
}
function Mensio_tmce_focus(editor_id, textarea_id) {
  if ( typeof editor_id === 'undefined' ) editor_id = wpActiveEditor;
  if ( typeof textarea_id === 'undefined' ) textarea_id = editor_id;
  if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
    return tinyMCE.get(editor_id).focus();
  }else{
    return jQuery('#'+textarea_id).focus();
  }
}
function Mensio_Load_PopUp_TimeOut() {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_Check_PopUp_Time_Out'
    },
    success:function(data) {
      $PopUpTmOut = data;
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Append_New_PopUp($PopUp) {
  $PopUp = $PopUp.replace(/\\/g, '');
  jQuery('#Mns_PopUp_Wrapper').append($PopUp);
  jQuery('#Mns_PopUp_Wrapper').toggle('slide', {direction:'right'});
  setTimeout (function() {
      jQuery('#Mns_PopUp_Wrapper').fadeOut('slow',function (){
        jQuery('#Mns_PopUp_Wrapper').html('');
      });
    },
    $PopUpTmOut
  );
}
function Mensio_stripJSONslashes(str) {
 return str.replace(/\\/g, "");
}
window.addEventListener('load',function() {
  jQuery('#MENSIOLoader').hide();
  jQuery('#MENSIO').show();
  jQuery('#MENSIOFootBar').show();
  document.body.className+=' folded';
});
jQuery(document).ready(function() {
  document.body.className+=' folded';
  jQuery('.TBL_DataTable_Wrapper').on('change', '.Bulk_Selector',function() {
    var $val = jQuery(this).val();
    jQuery('.Bulk_Selector').val($val);
  });
  setInterval(function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_Check_For_New_Notifications'
      },
      success:function(data) {
        jQuery('#Mns_HeaderBar_Notification_DropDown').html(data);
      }
    });
  }, 60000); // Every 1 minute
  jQuery('.Modal_Wrapper').on('click', '.Mdl_Btn_Close', function() {
    var $id = jQuery(this).attr( "id" );
    $id = $id.replace('CLS_','#');
    jQuery($id+' .modal-body').html('');
    jQuery($id).toggle( "slide" );
  });    
  if ($PopUpTmOut === '') { Mensio_Load_PopUp_TimeOut(); }
  jQuery('#Mns_PopUp_Wrapper').on('click','#Btn_PopUp_Close', function() {
    jQuery(this).parent().parent().hide();
  });
  jQuery('#DIV_Edit').on('change', function() {
    jQuery('#NOSAVEWARN').show();
  });
  jQuery('#DIV_Edit').on('change', '.form-control',function() {
    jQuery('#NOSAVEWARN').show();
  });
  jQuery('#DIV_Edit').on('change', '.ex-form-control',function() {
    jQuery('#NOSAVEWARN').show();
  });
  jQuery('#DIV_Edit').on('click', '#Btn_Save, #BTN_Save, .Btn_Save',function() {
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#DIV_Edit').on('click', '#Btn_Back, #BTN_Back, .Btn_Back',function() {
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#Mensio_HeadBar').on('click', '#Btn_Save_Header, #BTN_Save_Header, .Btn_Save_Header',function() {
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#Mensio_HeadBar').on('click', '#Btn_Back_Header, #BTN_Back_Header, .Btn_Back_Header',function() {
    jQuery('#NOSAVEWARN').hide();
  });
});