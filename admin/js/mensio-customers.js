'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_CustomersSubPager($SubPage) {
  switch ($SubPage) {
    case 'Edit':
      jQuery("#DIV_Table").hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Edit Mode');
      jQuery("#DIV_Edit").show(800);
      jQuery(".menu_button_row").show();
      jQuery("#HdBarBtnWrap").show();
      break;
    case 'Table':
      jQuery(".menu_button_row").hide();
      jQuery("#DIV_Edit").hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Table Mode');
      jQuery("#DIV_Table").show(800);
  }
}
function Mensio_Customer_BackToTable() {
  jQuery('.form-control').val('');
  var $Page = jQuery('#Customers_PageSelector_Header').val();
  var $Rows = jQuery('#Customers_RowSelector_Header').val();
  var $Search = jQuery('#Customers_SearchFld').val();
  var $Sorter = jQuery('#Customers_SorterCol').val();
  Mensio_CallAjaxTableLoader('Customers',$Page,$Rows,$Search,$Sorter,$sec);
  Mensio_CustomersSubPager('Table');
}
function Mensio_CustomerView($Customer) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: {
      'action' : 'mensio_ajax_Modal_View_Customer',
      'Security': $sec,
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
function Mensio_AddNewCustomer() {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: {
      'action' : 'mensio_ajax_New_Customer',
      'Security': $sec
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#DIV_Edit').html(data.EditForm);
        jQuery("#tabs").tabs();
        jQuery('#tabs').tabs({ disabled: [2,3,4] });
        Mensio_CustomersSubPager('Edit');
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_CustomerEdit($Customer) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: {
      'action' : 'mensio_ajax_Edit_Customer',
      'Security': $sec,
      'Customer' : $Customer
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#DIV_Edit').html(data.EditForm);
        jQuery("#tabs").tabs();
        Mensio_CustomersSubPager('Edit');
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Customer_UpdateExtraFields($val) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: {
      'action': 'mensio_ajax_Customers_Type_Changed',
      'Security': $sec,
      'Type': $val
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.Multi === 'TRUE') {
        var $Notif = data.Fields.replace(/\\/g, '');
        jQuery('#MultiAcc').html($Notif);
        jQuery('#MultiAcc').slideDown('slow');
      } else {
        jQuery('#MultiAcc').slideUp('slow');
        jQuery('#MultiAcc').html('');
     }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });    
}
function Mensio_CustomerSaveData() {
  var $Cstmr = jQuery('#FLD_Customer').val();
  var $Err = false;
  var $FldID = '';
  var $CustType = '';
  var $val = '';
  var $empty = '';
  var $ValPckg = Array();
  var $FrmCtrl = jQuery('.form-control');
  for (var $i=0; $i < $FrmCtrl.length; ++$i) {
    $FldID = $FrmCtrl[$i].id;
    $val = jQuery('#'+$FldID).val();
    if ($FldID === 'FLD_CustomerType') { $CustType = $val; }
    if (($val === '') && ($FldID !== 'FLD_Password')) {
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
        data: {
          'action': 'mensio_ajax_Save_Customer_Data',
          'Security': $sec,
          'Customer': $Cstmr,
          'Data' : $Data
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          Mensio_Append_New_PopUp(data.Message);
          if (data.ERROR === 'FALSE') {
            jQuery('#FLD_Customer').val(data.Customer);
            jQuery('#FLD_Password').val('');
            jQuery('#tabs').tabs("enable",2);
            jQuery('#tabs').tabs("enable",3);
            jQuery('#tabs').tabs("enable",4);
            Mensio_Customer_UpdateExtraFields(data.Customer);
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
    });
  }
}
function Mensio_CustomerDelete($Customer) {
  var answer = confirm('Are you sure you want to DELETE the customer?');
  if (answer === true) {
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
          'action': 'mensio_ajax_Delete_Customer_Data',
          'Security': $sec,
          'Customer' : $Customer
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') { Mensio_Customer_BackToTable(); }
          Mensio_Append_New_PopUp(data.Message);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
    });
  }
}
function Mensio_CustomerEditAddress($Address) {
  var $Customer = jQuery('#FLD_Customer').val();
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: {
      'action': 'mensio_ajax_Modal_Customer_Address',
      'Customer': $Customer,
      'Security': $sec,
      'Address': $Address
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#MnsModal').html(data.Modal);
        jQuery('#MnsModal').toggle('slide');
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }          
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_CustomerEditContact($Contact) {
  var $Customer = jQuery('#FLD_Customer').val();
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: {
      'action': 'mensio_ajax_Modal_Customer_Contact',
      'Security': $sec,
      'Customer': $Customer,
      'Contact': $Contact
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#MnsModal').html(data.Modal);
        jQuery('#MnsModal').toggle('slide');
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }          
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_CustomerDeleteInfo($Tab,$id) {
  var answer = confirm('Are you sure you want to DELETE the entry?');
  if (answer === true) {
    var $Customer = jQuery('#FLD_Customer').val();
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
          'action': 'mensio_ajax_Customer_Delete_Modal_Data',
          'Security': $sec,
          'Customer' : $Customer,
          'Tab' : $Tab,
          'Data' : $id
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          Mensio_Append_New_PopUp(data.Message);
          if (data.ERROR === 'FALSE') {
            jQuery('#'+$Tab+'ListDiv').html(data.List);
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
    });
  }
}
function Mensio_UpdateModalData($Tab) {
  var $Err = false;
  var $FldID = '';
  var $CustType = '';
  var $val = '';
  var $ValPckg = Array();
  var $FrmCtrl = jQuery('.MDL_Fields');
  for (var $i=0; $i < $FrmCtrl.length; ++$i) {
    $FldID = $FrmCtrl[$i].id;
    $val = jQuery('#'+$FldID).val();
    if (($FldID === 'MDL_Notes') && ($val === '')) { $val = 'none'; }
    if ($val === '') { $Err = true; }
      else { $ValPckg.push({ "Field": $FldID, "Value": $val}); }
  }
  if ($Err) {
    alert('One or more fields were empty');
  } else {
    var $Data = JSON.stringify($ValPckg);
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
          'action': 'mensio_ajax_Customer_Update_Modal_Data',
          'Security': $sec,
          'Tab' : $Tab,
          'Data' : $Data
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          Mensio_Append_New_PopUp(data.Message);
          jQuery('#MnsModal').toggle( "slide" );
          jQuery('#MnsModal').html('');
          if (data.ERROR === 'FALSE') {
            jQuery('#'+$Tab+'ListDiv').html(data.List);
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
    });
  }
}
jQuery(document).ready(function() {
  jQuery('.TBL_DataTable_Wrapper').on('click','.BTN_BulkActions', function() {
    var $id = jQuery(this).attr('id');
    $id = $id.split('_');
    var $Table = $id[1];
    var $Page = jQuery('#'+$Table+'_PageSelector').val();
    var $Rows = jQuery('#'+$Table+'_RowSelector').val();
    var $Search = jQuery('#'+$Table+'_SearchFld').val();
    var $Sorter = jQuery('#'+$Table+'_SorterCol').val();    
    var $JSONData = JSON.stringify(Mensio_GetExtraSelectors());
    var $Action = jQuery('.Bulk_Selector').val();
    var $Slcts = jQuery('#'+$Table+'_MultiSelectTblIDs').val();
    if (($Action !== '') && ($Slcts !== '')) {
      switch ($Action) {
        case 'ACT':
          $Action = 'mensio_ajax_Update_Customers_Activate';
          break;
        case 'DAC':
          $Action = 'mensio_ajax_Update_Customers_Deactivate';
          break;
      }
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
          'action': $Action,
          'Security': $sec,
          'Page' : $Page,
          'Rows' : $Rows,
          'Search' : $Search,
          'Sorter' : $Sorter,
          'ExtraActions' : $JSONData,
          'Data' : $Slcts
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          Mensio_Append_New_PopUp(data.Message);
          jQuery('#TBL_'+$Table+'_Wrapper').html(data.Table);
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
        Mensio_CustomerView($EditOption[3]);
        break;
      case 'Edit':
        Mensio_CustomerEdit($EditOption[3]);
        break;
      case 'Delete':
        Mensio_CustomerDelete($EditOption[3]);
        break;
    }
  });
  jQuery('#DIV_Edit').on('click', '.ui-tabs-anchor', function(){
    var tab = jQuery(this).attr('id');
    tab = parseInt(tab.replace('ui-id-',''));
    if (tab > 2) { jQuery('#HdBarBtnWrap').hide(); }
      else { jQuery('#HdBarBtnWrap').show(); }
  });
  jQuery('#ButtonArea').on('click', '#BTN_AddNew', function() {
    Mensio_AddNewCustomer();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Back', function() {
    Mensio_Customer_BackToTable();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Back_Header', function() {
    Mensio_Customer_BackToTable();
  });
  jQuery('#DIV_Edit').on('change', '#FLD_CustomerType',function() {
    var $val = jQuery(this).val();
    Mensio_Customer_UpdateExtraFields($val);
  });
  jQuery('#DIV_Edit').on('change', '#FLD_Company',function() {
    var $val = jQuery(this).val();
    if ($val !== 'NewMultiAccount') {
      jQuery('#NewMultiFields').slideUp('slow');
      jQuery('#NewMultiFields').html('');
    } else {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
          'action': 'mensio_ajax_Customers_Company_Changed',
          'Security': $sec,
          'Type': $val
        },
        success:function(data) {
          jQuery('#NewMultiFields').html(data);
          jQuery('#NewMultiFields').slideDown('slow');
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_NewPass',function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: {
        'action' : 'mensio_ajax_Load_Customer_NewPswd_Modal',
        'Security': $sec
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
  jQuery('#MnsModal').on('click', '#BTN_CrtNewPass',function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: {
        'action': 'mensio_ajax_Customers_New_Password',
        'Security': $sec
      },
      success:function(data) {
        jQuery('#MDL_FLD_Password').val(data);
        jQuery.ajax({
          type: 'post',
          url: ajaxurl,
          data: {
            'action': 'mensio_ajax_Check_Customers_New_Password_Strength',
            'Security': $sec,
            'Value': data
          },
          success:function(data) {
            data = jQuery.parseJSON(data);
            jQuery('.MDLPswdAlerts').hide();
            jQuery('#MDLPswdAlert_'+data.answer).show();
          },
          error: function(errorThrown){
            alert(errorThrown);
          }
        });        
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#MnsModal').on('keyup', '#MDL_FLD_Password',function() {
    var $val = jQuery(this).val();
    $val = $val.replace(' ','');
    if ($val !== '') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
          'action': 'mensio_ajax_Check_Customers_New_Password_Strength',
          'Security': $sec,
          'Value': $val
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          jQuery('.MDLPswdAlerts').hide();
          jQuery('#MDLPswdAlert_'+data.answer).show();
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    } else {
      jQuery('.MDLPswdAlerts').hide();
    }
  });
  jQuery('#MnsModal').on('click', '#BTN_SvNewPass',function() {
    var $val = jQuery('#MDL_FLD_Password').val();
    $val = $val.replace(' ','');
    if ($val !== '') {
      jQuery('#FLD_Password').val($val);
      jQuery('#NOSAVEWARN').show();
    }
    jQuery('#MnsModal').toggle('slide');
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_CustomerSaveData();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_CustomerSaveData();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Delete', function() {
    var $Customer = jQuery('#FLD_Customer').val();
    Mensio_CustomerDelete($Customer);
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Delete_Header', function() {
    var $Customer = jQuery('#FLD_Customer').val();
    Mensio_CustomerDelete($Customer);
  });
  jQuery('#DIV_Edit').on('click', '#BTN_AddAddress', function() {
    var $Customer = jQuery('#FLD_Customer').val();
    if ($Customer === 'NewCustomer') {
      alert('Please Insert Customer and then come back here');
    } else {
      Mensio_CustomerEditAddress('');
    }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_AddContact', function() {
    var $Customer = jQuery('#FLD_Customer').val();
    if ($Customer === 'NewCustomer') {
      alert('Please Insert Customer and then come back here');
    } else {
      Mensio_CustomerEditContact('');
    }
  });
  jQuery('#DIV_Edit').on('click', '.AddrDelBtn', function() {
    var $id = jQuery(this).attr('id');
    $id = $id.split('_');
    switch ($id[0]) {
      case 'Edit':
        Mensio_CustomerEditAddress($id[1]);
        break;
      case 'Del':
        Mensio_CustomerDeleteInfo('Address',$id[1]);
        break;
    }
  });
  jQuery('#DIV_Edit').on('click', '.ContDelBtn', function() {
    var $id = jQuery(this).attr('id');
    $id = $id.split('_');
    switch ($id[0]) {
      case 'Edit':
        Mensio_CustomerEditContact($id[1]);
        break;
      case 'Del':
        Mensio_CustomerDeleteInfo('Contact',$id[1]);
        break;
    }
  });
  jQuery('.Modal_Wrapper').on('change', '#MDL_Country', function() {
    var $country = jQuery(this).val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: {
        'action': 'mensio_ajax_Customers_Load_Region_Options',
        'Security': $sec,
        'Country' : $country
      },
      success:function(data) {
        jQuery('#MDL_Region').html(data);
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('click', '.Mdl_SaveBtn', function() {
    var $id = jQuery(this).attr('id');
    $id = $id.split('_');
    Mensio_UpdateModalData($id[1]);
  });
  jQuery('#DIV_Edit').on('click', '.BTN_View', function() {
    var $id = jQuery(this).attr('id');
    $id = $id.replace('VW_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: {
        'action' : 'mensio_ajax_Customer_Modal_View_Order_Details',
        'Security': $sec,
        'Order' : $id
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
  jQuery('#DIV_Edit').on('click', '.BTN_Status', function() {
    var $id = jQuery(this).attr('id');
    $id = $id.replace('ST_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: {
        'action' : 'mensio_ajax_Customer_Modal_View_Order_Status_History',
        'Security': $sec,
        'Order' : $id
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