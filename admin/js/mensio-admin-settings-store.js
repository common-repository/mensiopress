'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_Settings_UpdateStoreInfo() {
  var $Err = false;
  var $FldID = '';
  var $val = '';
  var $empty = '';
  var $ValPckg = Array();
  var $Store = jQuery('#FLD_Store').val();
  var $FrmCtrl = jQuery('.StoreBasicData');
  for (var $i=0; $i < $FrmCtrl.length; ++$i) {
    $FldID = $FrmCtrl[$i].id;
    $val = jQuery('#'+$FldID).val();
    if (($FldID === 'FLD_GglAnalytics') && ($val === '')) { $val = 'NOANALYTICS'; }
    if (($FldID === 'FLD_GglMap') && ($val === '')) { $val = 'NOMAP'; }
    if ($val === '') {
      $Err = true;
      $FldID = $FldID.replace('FLD_','');
      $empty = $empty+'\r\n'+$FldID;
    } else {
      $ValPckg.push({ "Field": $FldID, "Value": $val});
    }
  }
  if ($Err) {
    alert('One or more fields were empty '+$empty);
  } else {
    var $Data = JSON.stringify($ValPckg);
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Update_Store_Basic_Data',
        'Store': $Store,
        'Data' : $Data
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
}
jQuery(document).ready(function() {
  jQuery('#DIV_Edit').on('click', '#Btn_OpenMediaModal', function() {
    if (this.window === undefined) {
      this.window = wp.media({
        title: 'Select Image',
        library: {type: 'image'},
        multiple: false,
        button: {text: 'Select'}
      });
      var self = this; // Needed to retrieve our variable in the anonymous function below
      this.window.on('select', function() {
        var $Image = self.window.state().get('selection').first().toJSON();
        jQuery('#FLD_Logo').val($Image.url);
        jQuery("#DispImg").attr("src",$Image.url);
        jQuery('#NOSAVEWARN').show();
      });
    }
    this.window.open();
  });
  jQuery('#DIV_Edit').on('click', '#Btn_ClearImg', function() {
    jQuery('#FLD_Logo').val('No Image');
    jQuery("#DispImg").attr("src",'');
    jQuery('#NOSAVEWARN').show();
  });
  jQuery('#DIV_Edit').on('click', '.BTN_Save', function() {
    Mensio_Settings_UpdateStoreInfo();
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#Mensio_HeadBar').on('click', '.BTN_Save', function() {
    Mensio_Settings_UpdateStoreInfo();
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#DIV_Edit').on('click', '#Btn_TempInfo', function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Store_Google_Analytics_Help'
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
});