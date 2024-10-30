'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_DelCustomer_View($Customer) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action' : 'mensio_ajax_Modal_View_Customer',
      'Customer' : $Customer
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#MnsModal').html(data.Modal);
        jQuery('#MnsModal').toggle('slide');
        jQuery( function() { jQuery("#mdltabs").tabs(); });
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_DelCustomer_Restore($Customer) {
  var answer = confirm('Are you sure you want to RESTORE the customer?');
  if (answer === true) {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Restore_Deleted_Customer',
        'Customer' : $Customer
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        var $Page = jQuery('#DelCustomers_PageSelector_Header').val();
        var $Rows = jQuery('#DelCustomers_RowSelector_Header').val();
        var $Search = jQuery('#DelCustomers_SearchFld').val();
        var $Sorter = jQuery('#DelCustomers_SorterCol').val();
        Mensio_CallAjaxTableLoader('DelCustomers',$Page,$Rows,$Search,$Sorter,$sec);
        Mensio_Append_New_PopUp(data.Message);
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  }  
}
jQuery(document).ready(function() {
  jQuery('.TBL_DataTable_Wrapper').on('click','.BTN_BulkActions', function() {
    var $Action = jQuery('.Bulk_Selector').val();
    if ($Action === 'RSTR') {
      var $Slcts = jQuery('#DelCustomers_MultiSelectTblIDs').val();
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Restore_Multi_Deleted_Customer',
          'Data' : $Slcts
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          var $Page = jQuery('#DelCustomers_PageSelector').val();
          var $Rows = jQuery('#DelCustomers_RowSelector').val();
          var $Search = jQuery('#DelCustomers_SearchFld').val();
          var $Sorter = jQuery('#DelCustomers_SorterCol').val();
          Mensio_CallAjaxTableLoader('DelCustomers',$Page,$Rows,$Search,$Sorter);
          Mensio_Append_New_PopUp(data.Message);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('.TBL_DataTable_Wrapper').on('click', '.Mns_Subline_EditOption',function() {
    var $EditOption = jQuery(this).attr('id');
    $EditOption = $EditOption.split('_');
    switch ($EditOption[2]) {
      case 'View':
        Mensio_DelCustomer_View($EditOption[3]);
        break;
      case 'Restore':
        Mensio_DelCustomer_Restore($EditOption[3]);
        break;
    }
  });
});