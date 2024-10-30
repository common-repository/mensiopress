'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_Settings_UpdateStoreSupportLanguages() {
  var $Store = jQuery('#FLD_Store').val();
  var $Admin = jQuery('#FLD_AdminLang').val();
  var $Theme = jQuery('#FLD_ThemeLang').val();
  var $Active = '';
  var $FldID = '';
  var $val = '';
  var $FrmCtrl = jQuery('.ChkLang');
  for (var $i=0; $i < $FrmCtrl.length; ++$i) {
    $FldID = $FrmCtrl[$i].id;
    $val = jQuery('#'+$FldID).val();
    if ($val === '1') {
      if ($Active === '') { $Active = $FldID; }
        else { $Active = $Active + ';' + $FldID; }
    }
  }
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Update_Store_Support_Languages',
      'Store': $Store,
      'Admin' : $Admin,
      'Theme' : $Theme,
      'Active' : $Active
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
  jQuery('#DIV_Edit').on('click', '.ChkLang', function() {
    var $val = jQuery(this).val();
    if ($val === '0') { jQuery(this).val('1'); }
      else { jQuery(this).val('0'); }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_Settings_UpdateStoreSupportLanguages();
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_Settings_UpdateStoreSupportLanguages();
    jQuery('#NOSAVEWARN').hide();
  });
});