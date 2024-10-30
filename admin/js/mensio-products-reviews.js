'use strict';
jQuery(document).ready(function() {
  jQuery('.Modal_Wrapper').on('click', '.MdlDelBtn',function() {
    var $Review = jQuery(this).attr('id');
    $Review = $Review.split('_');
    var answer = confirm('Are you sure you want to DELETE the review?');
    if (answer === true) {
      jQuery.ajax({
          type: 'post',
          url: ajaxurl,
          data: {
            'action': 'mensio_ajax_Delete_Review_Data',
            'Review' : $Review[1]
          },
          success:function(data) {
            data = jQuery.parseJSON(data);
            if (data.ERROR === 'FALSE') {
              jQuery('#ReviewTableDiv').html(data.Table);
            } else {
              Mensio_Append_New_PopUp(data.Message);
            }
          },
          error: function(errorThrown){
            alert(errorThrown);
          }
      });
    }
  });
  jQuery('.Modal_Wrapper').on('click', '.MdlViewBtn',function() {
    var $Review = jQuery(this).attr('id');
    $Review = $Review.split('_');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: {
        'action': 'mensio_ajax_Load_Review_Data',
        'Review' : $Review[1]
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#ReviewViewDiv').html(data.Review);
          jQuery("#ReviewTableDiv").hide(function() {
            jQuery("#ReviewViewDiv").show(800);
          });
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_Back', function() {
    jQuery("#ReviewViewDiv").hide(800, function() {
      jQuery("#ReviewTableDiv").show(800);
    });
  });
});