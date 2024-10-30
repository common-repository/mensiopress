'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
jQuery( function() { jQuery("#tabs").tabs(); });
function Mensio_CurrencySubPager($SubPage) {
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
      break;
  }
}
function Mensio_SaveCurrencyData() {
  var $Err = false;
  var $FldID = '';
  var $Val = '';
  var $ValPckg = Array();
  var $Fields = jQuery('.form-control');
  for (var $i=0; $i < $Fields.length; ++$i) {
    $FldID = $Fields[$i].id;
    $Val = jQuery('#'+$FldID).val();
    if (($Val === '') || ($Val === ' ')) {
      $Err = true;
    } else {
      $ValPckg.push({ "Field": $FldID, "Value": $Val});
    }
  }
  if ($Err) {
    alert('Fields have empty values');
  } else {
    var $Data = JSON.stringify($ValPckg);
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Update_Currencies_Data',
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
function Mensio_Save_Currency_Data() {
  var $Err = false;
  var $FldID = '';
  var $Val = '';
  var $Fields = jQuery('.form-control');
  for (var $i=0; $i < $Fields.length; ++$i) {
    $FldID = $Fields[$i].id;
    $Val = jQuery('#'+$FldID).val();
    if (($Val === '') || ($Val === ' ')) { $Err = true; }
  }
  if ($Err) {
    alert('Fields have empty values');
  } else {    
    var $Curr = jQuery('#FLD_Currency').val();
    var $Code = jQuery('#FLD_Code').val();
    if (($Code !== '') && ($Code !== ' ')) {
      if ($Curr === 'NewCurrency') {
        jQuery.ajax({
          type: 'post',
          url: ajaxurl,
          data: { 'Security': $sec,
            'action': 'mensio_ajax_Currency_AddNew',
            'code': $Code
          },
          success:function(data) {
            data = jQuery.parseJSON(data);
            if (data.ERROR === 'FALSE') {
              var $Icon = jQuery('#FLD_Icon').val();
              if (($Icon === '') || ($Icon === ' ')) { jQuery('#FLD_Icon').val('No Icon'); }
              jQuery('#FLD_Currency').val(data.Currency);
              Mensio_SaveCurrencyData();
            }
            Mensio_Append_New_PopUp(data.Message);
          },
          error: function(errorThrown){
            alert(errorThrown);
          }
        });    
      } else {
        Mensio_SaveCurrencyData();
      }
    }
  }
}
jQuery(document).ready(function() {
  jQuery('#TBL_Currencies_Wrapper').on('click', '.Mns_Tbl_Body_Table_Fld_Check',function() {
    var $Chck = jQuery(this).attr('id');
    var $Value = jQuery('#'+$Chck).val();
    if (($Value === '0') || ($Value === '1')) {
      if ($Value === '0') {
        $Value = '1';
        jQuery('#'+$Chck).prop('checked', true);
      } else {
        $Value = '0';
        jQuery('#'+$Chck).prop('checked', false);
      }
      jQuery('#'+$Chck).val($Value);
      var $Item = $Chck.split('_');
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Update_Currency_LeftPos', // Here we write the php function
          'Currency' : $Item[2],
          'LeftPos' : $Value
        },
        success:function(data) { // IF Correct this outputs the result of the ajax request
          if (data.ERROR === 'TRUE') { Mensio_Append_New_PopUp(data.Message); }
        },
        error: function(errorThrown){ // IF Error this outputs the result of the ajax request
          alert(errorThrown);
        }
      });    
    } 
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.BTN_BulkActions', function() {
    var $ABlkSlctr = jQuery('.Bulk_Selector').val();
    var $CurRows = jQuery('#Currencies_MultiSelectTblIDs').val();
    if (($ABlkSlctr !== '') && ($CurRows !== '')) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Bulk_Update_Currency_LeftPos', // Here we write the php function
          'Currency' : $CurRows,
          'LeftPos' : $ABlkSlctr
        },
        success:function(data) { // IF Correct this outputs the result of the ajax request
          if (data === 'OK') {
            var $Page = jQuery('#Currencies_PageSelector_Header').val();
            var $Rows = jQuery('#Currencies_RowSelector_Header').val();
            var $Search = jQuery('#Currencies_SearchFld').val();
            var $Sorter = jQuery('#Currencies_SorterCol').val();
            Mensio_CallAjaxTableLoader('Currencies',$Page,$Rows,$Search,$Sorter,$sec);
            Mensio_CurrencySubPager('Table');              
          } else {
            Mensio_Append_New_PopUp(data.Message);
          }
        },
        error: function(errorThrown){ // IF Error this outputs the result of the ajax request
          alert(errorThrown);
        }
      });       
    }
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.Mns_Subline_EditOption', function() {
    var $Option = jQuery(this).attr('id');
    $Option = $Option.split('_');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Currency_Data',
        'Currency' : $Option[3]
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#FLD_Currency').val(data.KeyCode);
          jQuery('#FLD_Code').val(data.ShortCode);
          jQuery('#FLD_Symbol').val(data.Symbol);
          jQuery('#FLD_Left').val(data.Left);
          jQuery('#FLD_Icon').val(data.Icon);
          jQuery('#DIV_CurrTrans').html(data.Translations);
          Mensio_CurrencySubPager('Edit');
        }
        Mensio_Append_New_PopUp(data.Message);
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });  
  jQuery('#DIV_Edit').on('click', '#BTN_Back', function() {
    var $Page = jQuery('#Currencies_PageSelector_Header').val();
    var $Rows = jQuery('#Currencies_RowSelector_Header').val();
    var $Search = jQuery('#Currencies_SearchFld').val();
    var $Sorter = jQuery('#Currencies_SorterCol').val();
    Mensio_CallAjaxTableLoader('Currencies',$Page,$Rows,$Search,$Sorter,$sec);
    Mensio_CurrencySubPager('Table');
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Back_Header', function() {
    var $Page = jQuery('#Currencies_PageSelector_Header').val();
    var $Rows = jQuery('#Currencies_RowSelector_Header').val();
    var $Search = jQuery('#Currencies_SearchFld').val();
    var $Sorter = jQuery('#Currencies_SorterCol').val();
    Mensio_CallAjaxTableLoader('Currencies',$Page,$Rows,$Search,$Sorter,$sec);
    Mensio_CurrencySubPager('Table');
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_Save_Currency_Data();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_Save_Currency_Data();
  });
  jQuery('#ButtonArea').on('click', '#BTN_AddNew', function() {
    jQuery('#FLD_Currency').val('NewCurrency');
    jQuery('#FLD_Code').val('');
    jQuery('#FLD_Symbol').val('');
    jQuery('#FLD_Left').val('');
    jQuery('#FLD_Icon').val('No Icon');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Currencies_ClearTransFields'
      },
      success:function(data) {
        jQuery('#DIV_CurrTrans').html(data);
        Mensio_CurrencySubPager('Edit');
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });    
  });
});
