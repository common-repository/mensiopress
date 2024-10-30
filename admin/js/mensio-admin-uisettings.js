'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_Update_UISettings() {
  var $Store = jQuery('#FLD_Store').val();
  var $TblRows = jQuery('#FLD_TblRows').val();
  var $NotifTime = jQuery('#FLD_NotifTime').val();
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Update_Store_UI_Settings',
      'Store': $Store,
      'TblRows': $TblRows,
      'NotifTime': $NotifTime
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      Mensio_Append_New_PopUp(data.Message);
      Mensio_Load_PopUp_TimeOut();
      jQuery('#NOSAVEWARN').hide();
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
jQuery(document).ready(function() {
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_Update_UISettings();
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_Update_UISettings();
    jQuery('#NOSAVEWARN').hide();
  });
});