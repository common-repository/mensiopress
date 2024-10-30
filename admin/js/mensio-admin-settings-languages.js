'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
var $ValueCheck = '';
function Mensio_ClearLanguageFields() {
  jQuery('#FLD_LangSelection').val('0');
  jQuery('#FLD_Code').val('');
  jQuery('#FLD_Active').val('0');
  jQuery('#DIV_TransToOther').html('');
  jQuery('#DIV_TransToThis').html('');
}
function Mensio_RefreshNewLanguageForm(data) {
  jQuery('#FLD_LangSelection').html(data.Options);
  jQuery('#FLD_LangSelection').val(data.KeyCode);
  jQuery('#FLD_Code').val(data.ShortCode);
  jQuery('#FLD_Active').val(parseInt(data.Active));
  jQuery('#DIV_TransToOther').html(data.TransFrom);
  jQuery('#DIV_TransToThis').html(data.TransTo);
  jQuery('.Mns_ExtraFields').show();
}
function Mensio_UpdateLanguageTranslations($Type) {
  var $ActLang = jQuery('#FLD_LangSelection').val();
  var $Language = '';
  var $ToLanguage = '';
  var $Fields = jQuery('.'+$Type);
  var $FldId = '';
  var $FldVal = '';
  for(var i = 0; i < $Fields.length; i++){
    $FldVal = jQuery($Fields[i]).val();
    $FldId = jQuery($Fields[i]).attr('id');
    $FldId = $FldId.split('_');
    $FldId = $FldId[2];
    if ($Type === 'From') {
      $Language = jQuery('#FLD_LangSelection').val();
      $ToLanguage = $FldId;
    } else {
      $Language = $FldId;
      $ToLanguage = jQuery('#FLD_LangSelection').val();
    }
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Update_Language_Translations',
        'Language' : $Language,
        'ToLanguage' : $ToLanguage,
        'Name' : $FldVal
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#FLD_LangSelection').html(data.Options);
          jQuery('#FLD_LangSelection').val($ActLang);
        }
        Mensio_Append_New_PopUp(data.Message);
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  }
}
function Mensio_LanguageCheckColumnPressed($Chck) {
  var $Action = '';
  var $Row = $Chck.split('_');
  var $Value = jQuery('#'+$Chck).val();
  if (($Value === '0') || ($Value === '1')) {
    if ($Value === '0') {
      $Value = '1';
      jQuery('#'+$Chck).prop('checked', true);
    } else {
      $Value = '0';
      jQuery('#'+$Chck).prop('checked', false);
    }
    switch ($Row[1]) {
      case 'active':
        $Action = 'mensio_ajax_Update_Language_Active';
        break;
      case 'admin':
        $Action = 'mensio_ajax_Update_Language_Admin';
        break;
      case 'theme':
        $Action = 'mensio_ajax_Update_Language_Theme';
        break;
    }
    jQuery('#'+$Chck).val($Value);
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': $Action, // Here we write the php function
        'Language' : $Row[2],
        'Active' : $Value
      },
      success:function(data) { // IF Correct this outputs the result of the ajax request
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'TRUE') {
          if ($Value === '0') {
            $Value = '1';
            jQuery('#'+$Chck).prop('checked', true);
          }
        } else {
          if (($Row[1] === 'admin') || ($Row[1] === 'theme')) {
            var $Page = jQuery('#Languages_PageSelector_Header').val();
            var $Rows = jQuery('#Languages_RowSelector_Header').val();
            var $Search = jQuery('#Languages_SearchFld').val();
            var $Sorter = jQuery('#Languages_SorterCol').val();
            Mensio_CallAjaxTableLoader('Languages',$Page,$Rows,$Search,$Sorter,$sec);
          }
        }
        Mensio_Append_New_PopUp(data.Message);
      },
      error: function(errorThrown){ // IF Error this outputs the result of the ajax request
        alert(errorThrown);
      }
    });    
  }  
}
function Mensio_LanguageEditButtonPressed($Button) {
  $Button = $Button.split('_');
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Update_Language_Data',
      'Language' : $Button[1]
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'TRUE') {
        Mensio_Append_New_PopUp(data.Message);
      } else {
        jQuery('#FLD_LangSelection').val(data.KeyCode);
        jQuery('#FLD_Code').val(data.ShortCode);
        jQuery('#FLD_Active').val(parseInt(data.Active));
        jQuery('#FLD_Icon').val(data.Icon);
        if (data.Icon !== 'No Image') {
          jQuery("#DispImg").attr("src",data.IconImage);
        } else {
          var img = jQuery('#DefImg').val();
          jQuery("#DispImg").attr("src",img);
        }
        jQuery('#DIV_TransToOther').html(data.TransFrom);
        jQuery('#DIV_TransToThis').html(data.TransTo);
        jQuery("#DIV_LangTable").hide(800);
        jQuery('.Mns_Page_Breadcrumb').html('Edit Mode');
        jQuery("#DIV_LangEdit").show(800);
        jQuery(".menu_button_row").show();
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Save_Language_Data() {
  var $Action = '';
  var $Language = jQuery('#FLD_LangSelection').val();
  var $ShortCode = jQuery('#FLD_Code').val();
  var $Active = jQuery('#FLD_Active').val();
  var $Icon = jQuery('#FLD_Icon').val();
  if ($Icon === '') { $Icon = 'No Image'; }
  if ($Language === '0') {
    $Action = 'mensio_ajax_Add_New_Language';
  } else {
    $Action = 'mensio_ajax_Update_Language_Basic_Data';
  }
  if (($ShortCode === '') || ($ShortCode === ' ')) {
    jQuery('#DIV_Error_Wrapper').html('Code Field is Empty');
    jQuery('#DIV_Error_Wrapper').show();
  } else {
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': $Action,
          'Language' : $Language,
          'ShortCode' : $ShortCode,
          'Active' : $Active,
          'Icon' : $Icon
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            if ($Language === '0') {
              Mensio_RefreshNewLanguageForm(data);
            } else {
              Mensio_UpdateLanguageTranslations('From');
              Mensio_UpdateLanguageTranslations('To');
            }
            jQuery("#FLD_LangSelection").prop('disabled', false);
          }
          Mensio_Append_New_PopUp(data.Message);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
    });
  }
}
jQuery(document).ready(function() {
  jQuery('.form-control').on('focus', function() {
    $ValueCheck = jQuery(this).val();
  });
  jQuery('#DIV_TransToOther').on('focus', '.form-control', function() {
    $ValueCheck = jQuery(this).val();
  });
  jQuery('#DIV_TransToThis').on('focus', '.form-control', function() {
    $ValueCheck = jQuery(this).val();
  });
  jQuery('.form-control').on('blur', function() {
    var $NewVal = jQuery(this).val();
    if ($ValueCheck !== $NewVal) {
      jQuery("#FLD_LangSelection").prop('disabled', true);
    }
  });
  jQuery('#DIV_TransToOther').on('blur', '.form-control', function() {
    var $NewVal = jQuery(this).val();
    if ($ValueCheck !== $NewVal) {
      jQuery("#FLD_LangSelection").prop('disabled', true);
    }
  });
  jQuery('#DIV_TransToThis').on('blur', '.form-control', function() {
    var $NewVal = jQuery(this).val();
    if ($ValueCheck !== $NewVal) {
      jQuery("#FLD_LangSelection").prop('disabled', true);
    }
  });
  jQuery('#Mns_HeaderBar_Alert_Button').on('click', function() {
      jQuery("#FLD_LangSelection").prop('disabled', false);
  });
  jQuery('#ButtonArea').on('click', '#BTN_AddNew', function() {
    Mensio_ClearLanguageFields();
    jQuery('.Mns_ExtraFields').hide();
    jQuery("#DIV_LangTable").hide(800);
    jQuery("#FLD_LangSelection").prop('disabled', true);
    jQuery('.Mns_Page_Breadcrumb').html('Edit Mode');
    jQuery("#DIV_LangEdit").show(800);
    jQuery(".menu_button_row").show();
  });
  jQuery('.TBL_DataTable_Wrapper').on('click', '.BTN_BulkActions',function() {
    var $Value = jQuery('.Bulk_Selector').val();
    var $RowId = '';
    var $Admin = '';
    var $Theme = '';
    var $Selections = jQuery('#Languages_MultiSelectTblIDs').val();
    if ($Selections !== '') {
      $Selections = $Selections.split(';');
      for (var i=0; i<$Selections.length; ++i) {
        if ($Selections[i] !== '') {
          $RowId = '#Languages_active_'+$Selections[i];
          $Admin = '0';
          if (jQuery('#Languages_admin_'+$Selections[i]).is(':checked')) {
            $Admin = '1';
          }
          $Theme = '0';
          if (jQuery('#Languages_theme_'+$Selections[i]).is(':checked')) {
            $Theme = '1';
          }
          if (($Admin === '0') && ($Theme === '0')) {
            if ($Value === '0') { 
              jQuery($RowId).attr('checked', false);
            } else {
              jQuery($RowId).attr('checked', true);
            }
            jQuery.ajax({
                type: 'post',
                url: ajaxurl,
                data: { 'Security': $sec,
                  'action': 'mensio_ajax_Update_Language_Active', // Here we write the php function
                  'Language' : $Selections[i],
                  'Active' : $Value
                },
                success:function(data) { // IF Correct this outputs the result of the ajax request
                  data = jQuery.parseJSON(data);
                  Mensio_Append_New_PopUp(data.Message);
                },
                error: function(errorThrown){ // IF Error this outputs the result of the ajax request
                  alert(errorThrown);
                }
            });
          }
        }
      }
    }
  });
  jQuery('.TBL_DataTable_Wrapper').on('change', '.Mns_Tbl_Body_Table_Fld_Check',function() {
    Mensio_LanguageCheckColumnPressed(jQuery(this).attr('id'));
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.Mns_Tbl_Btn', function() {
    jQuery('.Mns_ExtraFields').show();
    Mensio_LanguageEditButtonPressed(jQuery(this).attr('id'));
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.Mns_Subline_EditOption', function() {
    var $Option = jQuery(this).attr('id');
    $Option = $Option.split('_');
    switch ($Option[2]) {
      case 'Active':
          Mensio_LanguageCheckColumnPressed($Option[1]+'_active_'+$Option[3]);
          break;
      case 'Admin':
          Mensio_LanguageCheckColumnPressed($Option[1]+'_admin_'+$Option[3]);
          break;
      case 'Theme':
          Mensio_LanguageCheckColumnPressed($Option[1]+'_theme_'+$Option[3]);
          break;
      case 'Edit':
          Mensio_LanguageEditButtonPressed($Option[1]+'_'+$Option[3]+'_BtnEdit');
          break;
    }
  });
  jQuery('#FLD_LangSelection').on('change',function() {
    var $Language = jQuery('#FLD_LangSelection').val();
    if ($Language === '0') {
      Mensio_ClearLanguageFields();
      jQuery('.Mns_ExtraFields').hide();
    } else {
      jQuery('.Mns_ExtraFields').show();
      jQuery.ajax({
          type: 'post',
          url: ajaxurl,
          data: { 'Security': $sec,
            'action': 'mensio_ajax_Update_Language_Data',
            'Language' : $Language
          },
          success:function(data) {
            data = jQuery.parseJSON(data);
            if (data.ERROR === 'TRUE') {
              Mensio_Append_New_PopUp(data.Message);
            } else {
              jQuery('#FLD_LangSelection').val(data.KeyCode);
              jQuery('#FLD_Code').val(data.ShortCode);
              jQuery('#FLD_Active').val(parseInt(data.Active));
              jQuery('#FLD_Icon').val(data.Icon);
              if (data.Icon !== 'No Image') {
                jQuery("#DispImg").attr("src",data.Icon);
              } else {
                var img = jQuery('#DefImg').val();
                jQuery("#DispImg").attr("src",img);
              }
              jQuery('#DIV_TransToOther').html(data.TransFrom);
              jQuery('#DIV_TransToThis').html(data.TransTo);
            }
          },
          error: function(errorThrown){
            alert(errorThrown);
          }
      });  
    }
  });
  jQuery('.FLD_YesNo_Selection').on('change', function() {
    var $Action = '';
    var $Chck = jQuery(this).attr('id');
    var $Value = jQuery(this).val();
    var $Language = jQuery('#FLD_LangSelection').val();
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Update_Language_Active', // Here we write the php function
          'Language' : $Language,
          'Active' : $Value
        },
        success:function(data) { // IF Correct this outputs the result of the ajax request
          if (data.ERROR === 'TRUE') {
            Mensio_Append_New_PopUp(data.Message);
            if ($Value === '0') {
              jQuery('#'+$Chck).val(1);
            } else {
              jQuery('#'+$Chck).val(0);
            }
          }
        },
        error: function(errorThrown){ // IF Error this outputs the result of the ajax request
          alert(errorThrown);
        }
    });
  });
  jQuery('#DIV_LangEdit').on('click', '#BTN_Save', function() {
    Mensio_Save_Language_Data();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_Save_Language_Data();
  });
  jQuery('#Mns_OpenMediaModal').on('click', function() {
    var $img = jQuery('#FLD_Icon').val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Flag_Icon_Form',
        'Image' : $img
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
  jQuery('#MnsModal').on('click', '.IconElementWrap', function() {
    var $NewIcon = jQuery(this).attr('id');
    var $Path = jQuery('#DefPath').val();
    var $Icon = $Path + $NewIcon + '.png';
    console.log($Icon);
    jQuery('#FLD_Icon').val($NewIcon);
    jQuery("#DispImg").attr("src",$Icon);
    jQuery('#NOSAVEWARN').show();
  });
  jQuery('#Mns_ClearImg').on('click', function() {
    jQuery('#FLD_Image').val('No Image');
    var img = jQuery('#DefImg').val();
    jQuery("#DispImg").attr("src",img);
  });
  jQuery('#DIV_LangEdit').on('click', '#BTN_Back', function() {
    jQuery(".menu_button_row").hide();
    jQuery("#DIV_LangEdit").hide(800, function() {
      var $Page = jQuery('#Languages_PageSelector_Header').val();
      var $Rows = jQuery('#Languages_RowSelector_Header').val();
      var $Search = jQuery('#Languages_SearchFld').val();
      var $Sorter = jQuery('#Languages_SorterCol').val();
      Mensio_CallAjaxTableLoader('Languages',$Page,$Rows,$Search,$Sorter,$sec);
    });
    jQuery('.Mns_Page_Breadcrumb').html('Table Mode');
    jQuery("#DIV_LangTable").show(800);
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Back_Header', function() {
    jQuery(".menu_button_row").hide();
    jQuery("#DIV_LangEdit").hide(800, function() {
      var $Page = jQuery('#Languages_PageSelector_Header').val();
      var $Rows = jQuery('#Languages_RowSelector_Header').val();
      var $Search = jQuery('#Languages_SearchFld').val();
      var $Sorter = jQuery('#Languages_SorterCol').val();
      Mensio_CallAjaxTableLoader('Languages',$Page,$Rows,$Search,$Sorter,$sec);
    });
    jQuery('.Mns_Page_Breadcrumb').html('Table Mode');
    jQuery("#DIV_LangTable").show(800);
  });
});