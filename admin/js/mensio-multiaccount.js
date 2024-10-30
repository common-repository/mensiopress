'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_MultiAccountSubPager($SubPage) {
  switch ($SubPage) {
    case 'Edit':
      jQuery("#DIV_Table").hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Edit Mode');
      jQuery("#DIV_Edit").show(800);
      jQuery(".menu_button_row").show();
      break;
    case 'Table':
      jQuery(".menu_button_row").hide();
      jQuery("#DIV_Edit").hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Table Mode');
      jQuery("#DIV_Table").show(800);
  }
}
function Mensio_MultiAccount_BackToTable() {
  jQuery('.form-control').val('');
  var $Page = jQuery('#MultiAccount_PageSelector_Header').val();
  var $Rows = jQuery('#MultiAccount_RowSelector_Header').val();
  var $Search = jQuery('#MultiAccount_SearchFld').val();
  var $Sorter = jQuery('#MultiAccount_SorterCol').val();
  Mensio_CallAjaxTableLoader('MultiAccount',$Page,$Rows,$Search,$Sorter,$sec);
  Mensio_MultiAccountSubPager('Table');
}
function Mensio_View_MultiAccount($Customer) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action' : 'mensio_ajax_Modal_View_MultiAccount_Customer',
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
function Mensio_Edit_MultiAccount($Customer) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action' : 'mensio_ajax_Edit_MultiAccount',
      'Customer' : $Customer
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#FLD_Customer').val(data.Customer);
        jQuery('#FLD_Sector').val(data.Sector);
        jQuery('#FLD_Name').val(data.Name);
        jQuery('#FLD_Tin').val(data.Tin);
        jQuery('#FLD_WebSite').val(data.WebSite);
        jQuery('#FLD_EMail').val(data.EMail);
        Mensio_MultiAccountSubPager('Edit');
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_MultiAccount_SaveData() {
  var $Err = false;
  var $FldID = '';
  var $val = '';
  var $empty = '';
  var $ValPckg = Array();
  var $FrmCtrl = jQuery('.form-control');
  for (var $i=0; $i < $FrmCtrl.length; ++$i) {
    $FldID = $FrmCtrl[$i].id;
    $val = jQuery('#'+$FldID).val();
    if ($val === '') {
      $Err = true;
      $empty = $empty+','+$FldID;
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
        'action': 'mensio_ajax_Save_MultiAccount_Data',
        'Data' : $Data
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
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
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Back', function() {
    Mensio_MultiAccount_BackToTable();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Back_Header', function() {
    Mensio_MultiAccount_BackToTable();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_MultiAccount_SaveData();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_MultiAccount_SaveData();
  });
  jQuery('.TBL_DataTable_Wrapper').on('click', '.Mns_Subline_EditOption',function() {
    var $EditOption = jQuery(this).attr('id');
    $EditOption = $EditOption.split('_');
    switch ($EditOption[2]) {
      case 'View':
        Mensio_View_MultiAccount($EditOption[3]);
        break;
      case 'Edit':
        Mensio_Edit_MultiAccount($EditOption[3]);
        break;
    }
  });
});