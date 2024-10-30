'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
jQuery(document).ready(function() {
  setInterval(function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_Dashboard_Check_For_New_Notifications'
      },
      success:function(data) {
        jQuery('#BasicInfoDiv').html(data);
      }
    });
  }, 30000); // Every 30 seconds
  Mensio_Display_VisitsToSales_Chart();
  jQuery('#DIV_Edit').on('click', '.InfoElementButtonMore',function() {
    var $elem = jQuery(this).attr('id');
    $elem = $elem.split('_');
    var $id = jQuery('#'+$elem[0]+'Code_'+$elem[1]).html();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Dashboard_Load_Info_Modal',
        'Type': $elem[0],
        'ID': $id
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
  jQuery('#DIV_Edit').on('click', '.InfoElementButtonClose',function() {
    var $id = jQuery(this).attr('id');
    $id = $id.split('_');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Dashboard_Update_Informed_Info',
        'ID': $id[1]
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#'+$id[0]+'_'+$id[1]+'_Element').toggle('slide');
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#Btn_CloseWellcome',function() {
    jQuery('#WellcomeScreen').hide();
  });
});