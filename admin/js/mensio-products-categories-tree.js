'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_Save_Categories_Hierarchy() {
  var Treelist = jQuery('#Treelist').val();
  if (Treelist !== '') {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Update_Category_Tree',
        'Treelist' : Treelist
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        Mensio_Append_New_PopUp(data.Message);
        if (data.ERROR === 'FALSE') { jQuery('#NOSAVEWARN').hide(); }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  }
}
jQuery(document).ready(function() {
  jQuery('.dd').nestable({});
  jQuery('#OldTree').val(
    JSON.stringify(jQuery('#nestable').nestable('serialize'))
  );
  jQuery('#nestable').nestable().on('change', function() {
    var OldTree = jQuery('#OldTree').val();
    var Treelist = jQuery(this).nestable('serialize');
    Treelist = JSON.stringify(Treelist);
    jQuery('#Treelist').val(Treelist);
    if (Treelist !== OldTree) {
      jQuery('#NOSAVEWARN').show();
    } else {
      jQuery('#NOSAVEWARN').hide();
    }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_Save_Categories_Hierarchy();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_Save_Categories_Hierarchy();
  });
});