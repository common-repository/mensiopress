'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_ContinentSubPager($SubPage) {
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
function Mensio_SaveContinentsTranslations() {
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
    var $lang = jQuery('.Mns_Btn_selected').attr('id');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Update_Continents_Translations',
        'Language': $lang,
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
  jQuery('#BTN_Edit').on('click', function() {
    Mensio_ContinentSubPager('Edit');
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Back', function() {
    var $Page = jQuery('#Continents_PageSelector_Header').val();
    var $Rows = jQuery('#Continents_RowSelector_Header').val();
    var $Search = jQuery('#Continents_SearchFld').val();
    var $Sorter = jQuery('#Continents_SorterCol').val();
    Mensio_CallAjaxTableLoader('Continents',$Page,$Rows,$Search,$Sorter,$sec);
    Mensio_ContinentSubPager('Table');
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Back_Header', function() {
    var $Page = jQuery('#Continents_PageSelector_Header').val();
    var $Rows = jQuery('#Continents_RowSelector_Header').val();
    var $Search = jQuery('#Continents_SearchFld').val();
    var $Sorter = jQuery('#Continents_SorterCol').val();
    Mensio_CallAjaxTableLoader('Continents',$Page,$Rows,$Search,$Sorter,$sec);
    Mensio_ContinentSubPager('Table');
  });
  jQuery('#DIV_LangSelect').on('click', '.Mns_Language_Selector_Btn', function() {
    jQuery('.Mns_Language_Selector_Btn').removeClass('Mns_Btn_selected');
    var $Btn = jQuery(this).attr('id');
    jQuery('#'+$Btn).addClass('Mns_Btn_selected');
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Load_Continent_Translations',
          'Language' : $Btn
        },
        success:function(data) {
          jQuery('#DIV_FLD_Translations').html(data);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
    });    
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_SaveContinentsTranslations();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_SaveContinentsTranslations();
  });
});