'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_Settings_UpdateStoreSalesSettings() {
  var $Store = jQuery('#FLD_Store').val();
  var $Curr = jQuery('#FLD_Currency').val();
  var $OrderSerial = jQuery('#FLD_OrderSerial').val();
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Update_Store_Sales_Settings',
      'Store': $Store,
      'Currency' : $Curr,
      'OrderSerial' : $OrderSerial
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      Mensio_Append_New_PopUp(data.Message);
      jQuery('#NOSAVEWARN').hide();
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
jQuery(document).ready(function() {
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_Settings_UpdateStoreSalesSettings();
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_Settings_UpdateStoreSalesSettings();
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#DIV_Edit').on('change', '.OrderTmplSelector', function() {
    var $id = jQuery(this).val();
    if ($id === 'Custom') {
      jQuery('#FLD_OrderSerial').prop('disabled', false);
      jQuery('#FLD_OrderSerial').val('');
      jQuery('#FLD_OrderSerial').focus();
    } else {
      jQuery('#FLD_OrderSerial').val($id);
      jQuery('#FLD_OrderSerial').prop('disabled', true);
    }
  });
});