'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
jQuery('#ButtonArea').on('click', '#BTN_Delete', function() {
   var answer = confirm('Are you sure you want to DELETE the notifications?');
  if (answer === true) { 
      jQuery.ajax({
          type: 'post',
          url: ajaxurl,
          data: { 'Security': $sec,
            'action': 'mensio_ajax_Delete_Notification'
          },
          success:function(data) {
            data = jQuery.parseJSON(data);
            jQuery('#TBL_Notifications_Wrapper').html(data);
          },
          error: function(errorThrown){
            alert(errorThrown);
          }
      });
    } 
});
jQuery('.TBL_DataTable_Wrapper').on('click', '.BTN_BulkActions',function() {
var $ABlkSlctr = jQuery('.Bulk_Selector').val();
var $field = jQuery('#Notifications_MultiSelectTblIDs').val();
  if (($ABlkSlctr !== '') && ($field !== '')) {
    if ($ABlkSlctr === '1'){
     var answer = confirm('Are you sure you want to DELETE the notifications?');    
     if (answer === true) { 
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
        'action': 'mensio_ajax_Delete_MultiNotification',
        'MultNotification':$field
        },
        success:function(data) {
          jQuery('#TBL_Notifications_Wrapper').html(data);
        },
        error: function(errorThrown){
          alert(errorThrown);
        } 
      });
     }
    }
  }
});
jQuery('#Mensio_HeadBar').on('click', '#BTN_Export', function() {
  alert('Not Ready Yet');
});
