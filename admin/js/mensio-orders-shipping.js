'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_Shipping_ClearValues() {
    jQuery('#FLD_Courier').val('NewEntry');
    jQuery('#FLD_Name').val('');
    jQuery('#FLD_DeliverySpeed').val('');
    jQuery('#FLD_BillingType').val('WEIGHT');
    jQuery('#FLD_Active').val(0);
    jQuery('#FLD_Active').attr('checked', false);
    jQuery('#ShippingList').html('');
}
function Mensio_Shipping_SubPager($SubPage) {
  switch ($SubPage) {
    case 'Edit':
      jQuery('#DIV_Table').hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Edit Mode');
      jQuery('#DIV_Edit').show(800);
      jQuery('.menu_button_row').show();
      break;
    case 'Table':
      jQuery('.menu_button_row').hide();
      jQuery('#DIV_Edit').hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Table Mode');
      jQuery('#DIV_Table').show(800);
  }
}
function Mensio_Shipping_BackToTable() {
  Mensio_Shipping_ClearValues();
  var $Page = jQuery('#Shipping_PageSelector_Header').val();
  var $Rows = jQuery('#Shipping_RowSelector_Header').val();
  var $Search = jQuery('#Shipping_SearchFld').val();
  var $Sorter = jQuery('#Shipping_SorterCol').val();
  Mensio_CallAjaxTableLoader('Shipping',$Page,$Rows,$Search,$Sorter,$sec);
  Mensio_Shipping_SubPager('Table');
}
function Mensio_Edit_Courier($Courier) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Shipping_Load_Courier_Data',
      'Courier': $Courier
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#FLD_Courier').val(data.Courier);
        jQuery('#FLD_Name').val(data.Name);
        jQuery('#FLD_DeliverySpeed').val(data.DeliverySpeed);
        jQuery('#FLD_BillingType').val(data.BillingType);
        jQuery('#ShippingList').html(data.ShippingList);
        jQuery('#FLD_Active').val(data.Active);
        jQuery('#FLD_Active').attr('checked', false);
        if (data.Active === '1') {
          jQuery('#FLD_Active').attr('checked', true);
        }
        Mensio_Shipping_SubPager('Edit');
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Update_Courrier_Data() {
  var $Err = false;
  var $Courier = jQuery('#FLD_Courier').val();
  var $Name = jQuery('#FLD_Name').val();
  var $DeliverySpeed = jQuery('#FLD_DeliverySpeed').val();
  var $BillingType = jQuery('#FLD_BillingType').val();
  var $Active = jQuery('#FLD_Active').val();
  if ($Courier === '') {$Err = true;}
  if ($Name === '') {$Err = true;}
  if ($DeliverySpeed === '') {$Err = true;}
  if (($BillingType !== 'WEIGHT') && ($BillingType !== 'PRICE')) {$Err = true;}
  if (($Active !== '0') && ($Active !== '1')) {$Err = true;}
  if ($Err) {
    alert('One or more fields have empty values');
  } else {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Shipping_Update_Courier_Data',
        'Courier': $Courier,
        'Name': $Name,
        'DeliverySpeed': $DeliverySpeed,
        'BillingType': $BillingType,
        'Active': $Active
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        jQuery('#FLD_Courier').val(data.Courier);
        Mensio_Append_New_PopUp(data.Message);
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  }
}
jQuery(document).ready(function() {
  jQuery('#DIV_Edit').on('click', '#BTN_Back', function() {
    Mensio_Shipping_BackToTable();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Back_Header', function() {
    Mensio_Shipping_BackToTable();
  });
  jQuery('#ButtonArea').on('click', '#BTN_AddNew', function() {
    Mensio_Shipping_ClearValues();
    Mensio_Shipping_SubPager('Edit');
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.BTN_BulkActions', function() {
    var $check = '';
    var $id = '';
    var $Action = jQuery('.Bulk_Selector').val();
    var $Selections = jQuery('#Shipping_MultiSelectTblIDs').val();
    switch ($Action) {
      case 'ACTV':
        $check = '1';
        break;
      case 'DCTV':
        $check = '0';
        break;
    }
    $Selections = $Selections.split(';');
    for (var i=0; i<$Selections.length; ++i) {
      if ($Selections[i] !== '') {
        $id = '#Shipping_active_'+$Selections[i];
        if ($check === '1') { jQuery($id).attr('checked', true); }
          else { jQuery($id).attr('checked', false); }
        jQuery($id).val($check);
        jQuery.ajax({
          type: 'post',
          url: ajaxurl,
          data: { 'Security': $sec,
            'action': 'mensio_ajax_Shipping_Update_Courier_Active_Status',
            'Courier': $Selections[i],
            'Active': $check
          },
          success:function(data) {
            data = jQuery.parseJSON(data);
            if (data.ERROR === 'TRUE') { Mensio_Append_New_PopUp(data.Message); }
          },
          error: function(errorThrown){
            alert(errorThrown);
          }
        });
      }
    }
  });
  jQuery('.TBL_DataTable_Wrapper').on('click', '.Mns_Subline_EditOption',function() {
    var $EditOption = jQuery(this).attr('id');
    $EditOption = $EditOption.split('_');
    switch ($EditOption[2]) {
      case 'Edit':
        Mensio_Edit_Courier($EditOption[3]);
        break;
    }
  });
  jQuery('.TBL_DataTable_Wrapper').on('click', '.Mns_Tbl_Body_Table_Fld_Check',function() {
    var $val = jQuery(this).val();
    if ($val === '0') { $val = '1'; }
      else { $val = '0'; }
    jQuery(this).val($val);
    var $id = jQuery(this).attr('id');
    var $id = $id.split('_');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Shipping_Update_Courier_Active_Status',
        'Courier': $id[2],
        'Active': $val
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        Mensio_Append_New_PopUp(data.Message);
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#FLD_Active', function() {
    var $val = jQuery(this).val();
    if ($val === '0') { $val = '1'; }
      else { $val = '0'; }
    jQuery(this).val($val);
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_Update_Courrier_Data();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_Update_Courrier_Data();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_AddShippingOption',function() {
    var $Courier = jQuery('#FLD_Courier').val();
    var $Country = jQuery('#ActiveCountry').val();
    if ($Courier === 'NewEntry') {
      alert('Please save the courier data first');
    } else {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Shipping_Load_Option_Modal',
          'Courier': $Courier,
          'Country': $Country
        },
        success:function(data) {
          jQuery('#MnsModal').html(data);
          jQuery('#MnsModal').toggle('slide');
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('.Modal_Wrapper').on('click', '#MDL_AddNewShippingOption', function() {
    var $Err = false;
    var $Courier = jQuery('#FLD_Courier').val();
    var $Cntr = jQuery('#MDL_FLD_Country').val();
    var $Weight = jQuery('#MDL_FLD_Weight').val();
    var $Price = jQuery('#MDL_FLD_Price').val();
    if ($Cntr === '') { $Err = true; }
    if ($Weight === '') { $Err = true; }
    if ($Price === '') { $Err = true; }
    if ($Err) {
      alert('One or more fields are empty');
    } else {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Shipping_Add_Shipping_Option',
          'Courier': $Courier,
          'Country': $Cntr,
          'Weight': $Weight,
          'Price': $Price
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#ShippingList').html(data.ShippingList);
            jQuery('.ListCountrySelector').hide();
            jQuery('#'+$Cntr).show();
            jQuery('#LST_'+$Cntr).toggle('slide',{direction:'up'});
            jQuery('#CHK_'+$Cntr).val('1');            
            jQuery('#ActiveCountry').val($Cntr);
            jQuery('#MDL_FLD_Weight').val('');
            jQuery('#MDL_FLD_Price').val('');
            jQuery('#MDL_FLD_Weight').focus();
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
  jQuery('#DIV_Edit').on('click', '.ListElementNameDiv',function() {
    var $id = jQuery(this).parent().attr('id');
    var $chk = jQuery('#CHK_'+$id).val();
    if ($chk === '0') {
      jQuery('.ListCountrySelector').hide();
      jQuery('#'+$id).show();
      jQuery('#LST_'+$id).toggle('slide',{direction:'up'});
      jQuery('#CHK_'+$id).val('1');
      jQuery('#ActiveCountry').val($id);
    } else {
      jQuery('#LST_'+$id).toggle('slide', {direction:'up'},function () {
        jQuery('.ListCountrySelector').show();
        jQuery('#CHK_'+$id).val('0');
        jQuery('#ActiveCountry').val('');
      });
    }
  });
  jQuery('#DIV_Edit').on('click', '.BTN_RemoveCountry',function() {
    var $Courier = jQuery('#FLD_Courier').val();
    var $Cntr = jQuery(this).attr('id');
    $Cntr = $Cntr.replace('BTN_','');
    var answer = confirm('Are you sure you want to DELETE the Country Shipping Options?');
    if (answer === true) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Shipping_Disable_Shipping_Country',
          'Courier': $Courier,
          'Country': $Cntr
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#ShippingList').html(data.ShippingList);
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
  jQuery('#DIV_Edit').on('click', '.BTN_RemoveOption',function() {
    var $Option = jQuery(this).attr('id');
    var answer = confirm('Are you sure you want to DELETE the Shipping Option?');
    if (answer === true) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Shipping_Disable_Shipping_Option',
          'Option': $Option
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#TR_'+$Option).hide();
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
});