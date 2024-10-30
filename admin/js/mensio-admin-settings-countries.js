'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
jQuery( function() { jQuery("#tabs").tabs(); });
function Mensio_CountriesSubPager($SubPage) {
  switch ($SubPage) {
    case 'Edit':
      jQuery("#DIV_Table").hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Edit Mode');
      jQuery( "#tabs" ).tabs({"active": 0});
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
function Mensio_Countries_BackToTable() {
  jQuery('.form-control').val('');
  var $Page = jQuery('#Countries_PageSelector_Header').val();
  var $Rows = jQuery('#Countries_RowSelector_Header').val();
  var $Search = jQuery('#Countries_SearchFld').val();
  var $Sorter = jQuery('#Countries_SorterCol').val();
  Mensio_CallAjaxTableLoader('Countries',$Page,$Rows,$Search,$Sorter,$sec);
  Mensio_CountriesSubPager('Table');            
}
function Mensio_Load_Country_Data($Country) {
  jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Country_Data',
        'Country' : $Country
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#FLD_Country').val(data.Country);
          jQuery('#FLD_Continent').val(data.Continent);
          jQuery('#FLD_iso2').val(data.iso2);
          jQuery('#FLD_iso3').val(data.iso3);
          jQuery('#FLD_domain').val(data.domain);
          jQuery('#FLD_idp').val(data.idp);
          jQuery('#FLD_Currency').val(data.currency);
          var $Trans = data.Translations.split('??');
          var $Translation = '';
          for (var $i=0; $i < $Trans.length; ++$i) {
            $Translation = $Trans[$i].split('::');
            jQuery('#'+$Translation[0]).val($Translation[1]);
          }
          Mensio_CountriesSubPager('Edit');
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
  });
}
function Mensio_SaveCountryData() {
  var $Err = false;
  var $FldID = '';
  var $val = '';
  var $ValPckg = Array();
  var $Cntr = jQuery('#FLD_Country').val();
  if ($Cntr === '') { $Err = true; }
  var $FrmCtrl = jQuery('.form-control');
  for (var $i=0; $i < $FrmCtrl.length; ++$i) {
    $FldID = $FrmCtrl[$i].id;
    $val = jQuery('#'+$FldID).val();
    if ($val === '') { $Err = true; }
      else { $ValPckg.push({"Field":$FldID,"Value":$val}); }
  }
  if ($Err) {
    alert('One or more fields were empty');
  } else {
    var $Data = JSON.stringify($ValPckg);
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Save_Country_Edit_Data',
          'Country' : $Cntr,
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
function Mensio_CountriesQuickUpdate() {
  jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action' : 'mensio_ajax_Countries_Quick_Update'
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#MnsModal').html(data.Modal);
          jQuery( function() { jQuery("#mdltabs").tabs(); });
          jQuery('#MnsModal').toggle('slide');
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
  });  
}
function Mensio_Delete_Country_Data($Country) {
  var answer = confirm('Are you sure you want to DELETE the Country?');
  if (answer === true) {
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Delete_Country_Data',
          'Country' : $Country
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          Mensio_Countries_BackToTable();
          Mensio_Append_New_PopUp(data.Message);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
    });
  }
}
function Mensio_CountriesClearing($Slctrs) {
  var answer = confirm('You are about to DELETE more than one Entry!!!!\nAre you sure?');
  if (answer === true) {
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Countries_Quick_Delete',
          'Data' : $Slctrs
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          Mensio_Countries_BackToTable();
          Mensio_Append_New_PopUp(data.Message);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
    });
  }
}
jQuery(document).ready(function() {
  jQuery('#ButtonArea').on('click', '#BTN_AddNew', function() {
    jQuery('.form-control').val('');
    jQuery('#FLD_Country').val('NewCountry');
    jQuery( "#tabs" ).tabs({"active": 0});
    Mensio_CountriesSubPager('Edit');
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Back', function() {
    Mensio_Countries_BackToTable();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Back_Header', function() {
    Mensio_Countries_BackToTable();
  });
  jQuery('#TBL_Countries_Wrapper').on('click', '.Mns_Subline_EditOption', function() {
    var $Action = '';
    var $Myid = jQuery(this).attr('id');
    $Myid = $Myid.split('_');
    switch ($Myid[2]) {
      case 'Edit':
        Mensio_Load_Country_Data($Myid[3]);
        break;
      case 'Delete':
        Mensio_Delete_Country_Data($Myid[3]);
        break;
    }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_SaveCountryData();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_SaveCountryData();
  });
  jQuery('.TBL_DataTable_Wrapper').on('click', '.BTN_BulkActions',function() {
    var $Slctrs = jQuery('#Countries_MultiSelectTblIDs').val();
    if ($Slctrs !== '') {
      var $BlkSlctr = jQuery('.Bulk_Selector').val();
      switch ($BlkSlctr) {
        case 'QED':
          Mensio_CountriesQuickUpdate();
          break;
        case "DEL":
          Mensio_CountriesClearing($Slctrs);
          break;
      }
    }
  });
  jQuery('.Modal_Wrapper').on('click', '.Mdl_Btn_Close', function() {
    Mensio_Countries_BackToTable();
  });
  jQuery('.Modal_Wrapper').on('change', '.Mdl-form-control',function() {
    var $TabIndex = jQuery("#mdltabs").tabs('option', 'active');
    if ($TabIndex === 0) {
      jQuery('#mdltabs').tabs('option','disabled', [1]);
    }else {
      jQuery('#mdltabs').tabs('option','disabled', [0]);
    }
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_ModalSave',function() {
    var $TabIndex = jQuery("#mdltabs").tabs('option', 'active');
    var $Field = '';
    if ($TabIndex === 0) { $Field = 'FLD_MDL_Continent'; }
      else { $Field = 'FLD_MDL_Currency'; }
    var $Data = jQuery('#'+$Field).val();
    var $Slctrs = jQuery('#Countries_MultiSelectTblIDs').val();
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Save_Country_Quick_Edit',
          'Selected' : $Slctrs,
          'Field' : $Field,
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
    jQuery('#mdltabs').tabs('option','disabled', []);
  }); 
});