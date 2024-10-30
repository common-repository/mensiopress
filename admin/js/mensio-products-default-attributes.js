'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_Products_DefaultAttributes_SubPager($SubPage) {
  switch ($SubPage) {
    case 'Edit':
      jQuery("#DIV_Table").hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Edit Mode');
      jQuery("#DIV_Edit").show(800);
      jQuery(".menu_button_row").show();
      break;
    case 'Table':
      jQuery("#DIV_Edit").hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Table Mode');
      jQuery("#DIV_Table").show(800);
      jQuery(".menu_button_row").hide();
  }
}
function Mensio_Products_DefaultAttributes_BackToTable() {
  jQuery('.form-control').val('');
  var $Page = jQuery('#DefaultAttributes_PageSelector_Header').val();
  var $Rows = jQuery('#DefaultAttributes_RowSelector_Header').val();
  var $Search = jQuery('#DefaultAttributes_SearchFld').val();
  var $Sorter = jQuery('#DefaultAttributes_SorterCol').val();
  Mensio_CallAjaxTableLoader('DefaultAttributes',$Page,$Rows,$Search,$Sorter,$sec);
  Mensio_Products_DefaultAttributes_SubPager('Table');
}
function Mensio_Products_DefaultAttributes_Edit($Attribute) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    dataType: 'text',
    data: { 'Security': $sec,
      'action' : 'mensio_ajax_Products_Edit_Attribute_Data',
      'Attribute' : $Attribute
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#FLD_Attribute').val(data.Attribute);
        jQuery('#FLD_Name').html(data.Name);
        jQuery('#FLD_Type').html(data.Type);
        jQuery('#FLD_Visibility').prop('checked', false);
        jQuery('#FLD_Visibility').val('0');
        if (data.Visibility === '1') {
          jQuery('#FLD_Visibility').prop('checked', true);
          jQuery('#FLD_Visibility').val('1');
        }
        jQuery('#InputDiv').html(data.InputControl);
        jQuery('#AttrValList').html(data.ValueList);
        jQuery('#FLD_ValueID').val('NewValue');
        jQuery('#FLD_Color_Hex').wpColorPicker({
          width: 200,
          mode: 'hsl',
          palettes: false,
          change: function(event, ui){
            var val = jQuery('#FLD_Color_Hex').val();
            jQuery.ajax({
              type: 'post',
              url: ajaxurl,
              dataType: 'text',
              data: { 'Security': $sec,
                'action' : 'mensio_ajax_Products_Convert_Hex_To_RGB',
                'Value' : val
              },
              success:function(data) {
                data = jQuery.parseJSON(data);
                jQuery('#R').html(data.r);
                jQuery('#G').html(data.g);
                jQuery('#B').html(data.b);
              },
              error: function(errorThrown){
                alert(errorThrown);
              }
            });
          },
          clear: function() {}
        });
        Mensio_Products_DefaultAttributes_SubPager('Edit');
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Products_DefaultAttributes_TgVisible($Attribute) {
  var $vsbl = jQuery('#DefaultAttributes_visibility_'+$Attribute).val();
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    dataType: 'text',
    data: { 'Security': $sec,
      'action' : 'mensio_ajax_Products_Toggle_Global_Attribute_Visiblity',
      'Attribute' : $Attribute
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR !== 'FALSE') {
        Mensio_Append_New_PopUp(data.Message);
      } else {
        if ($vsbl === '1') {
          jQuery('#DefaultAttributes_visibility_'+$Attribute).prop('checked', false);
          jQuery('#DefaultAttributes_visibility_'+$Attribute).val('0');
        } else {
          jQuery('#DefaultAttributes_visibility_'+$Attribute).prop('checked', true);
          jQuery('#DefaultAttributes_visibility_'+$Attribute).val('1');
        }
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Products_DefaultAttributes_Translate($attr) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action' : 'mensio_ajax_Global_Attribute_Load_Translation',
      'Attribute' : $attr
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
jQuery(document).ready(function() {
  jQuery('#ButtonArea').on('click', '#BTN_EditAttrType', function() {
    Mensio_Products_DefaultAttributes_SubPager('Table');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action' : 'mensio_ajax_Global_Attribute_Load_AttrType'
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
  jQuery('#DIV_Edit').on('click', '#Btn_OpenMediaModal', function() {
    if (this.window === undefined) {
      this.window = wp.media({
        title: 'Select Image',
        library: {type: 'image'},
        multiple: false,
        button: {text: 'Select'}
      });
      var self = this; // Needed to retrieve our variable in the anonymous function below
      this.window.on('select', function() {
        var $Image = self.window.state().get('selection').first().toJSON();
        jQuery('#FLD_Logo').val($Image.url);
        jQuery("#DispImg").attr("src",$Image.url);
        jQuery('#NOSAVEWARN').show();
      });
    }
    this.window.open();
  });
  jQuery('#DIV_Edit').on('click', '#Btn_ClearImg', function() {
    jQuery('#FLD_Logo').val('No Image');
    jQuery("#DispImg").attr("src",'');
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Back', function() {
    Mensio_Products_DefaultAttributes_BackToTable();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Back_Header', function() {
    Mensio_Products_DefaultAttributes_BackToTable();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Delete', function() {
    var $Cat = jQuery('#FLD_Attribute').val();
    Mensio_Products_DefaultAttributes_Delete($Cat);
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.BTN_BulkActions', function() {
    var $Action = jQuery('.Bulk_Selector').val();
    var $Selections = jQuery('#DefaultAttributes_MultiSelectTblIDs').val();
    if ($Action === 'VIS') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        dataType: 'text',
        data: { 'Security': $sec,
          'action' : 'mensio_ajax_Products_Toggle_Global_Attribute_Visiblity_Bulk',
          'Selections' : $Selections
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR !== 'FALSE') {
            Mensio_Append_New_PopUp(data.Message);
          } else {
            Mensio_Products_DefaultAttributes_BackToTable();
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.Mns_Tbl_Body_Table_Fld_Check', function() {
    var $EditOption = jQuery(this).attr('id');
    $EditOption = $EditOption.split('_');
    Mensio_Products_DefaultAttributes_TgVisible($EditOption[2]);
  });
  jQuery('.TBL_DataTable_Wrapper').on('click', '.Mns_Subline_EditOption',function() {
    var $EditOption = jQuery(this).attr('id');
    $EditOption = $EditOption.split('_');
    switch ($EditOption[2]) {
      case 'Visible':
        Mensio_Products_DefaultAttributes_TgVisible($EditOption[3]);
        break;
      case 'Edit':
        Mensio_Products_DefaultAttributes_Edit($EditOption[3]);
        break;
      case 'Translate':
        Mensio_Products_DefaultAttributes_Translate($EditOption[3]);
        break;
    }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Translate', function() {
    var $attr = jQuery('#FLD_Attribute').val();
    Mensio_Products_DefaultAttributes_Translate($attr);
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Translate_Header', function() {
    var $attr = jQuery('#FLD_Attribute').val();
    Mensio_Products_DefaultAttributes_Translate($attr);
  });
  jQuery('#DIV_Edit').on('click', '#BTN_AddValue',function() {
    var $Err = false;
    var $attr = jQuery('#FLD_Attribute').val();
    var $id = jQuery('#FLD_ValueID').val();
    var $AttrName = jQuery('#FLD_Name').html();
    var $AttrType = jQuery('#FLD_Type').html();
    var $EntryType = '';
    var $Value = '';
    switch($AttrType) {
      case 'Text':
        $Value = jQuery('#FLD_AttrValue').val();
        $Value = $Value.replace(/\s+/g, '');
        if ($Value === '') { $Err = true; }
        break;
      case 'Color':
        $Value = jQuery('#FLD_ColorName').val();
        $Value = $Value.replace(/\s+/g, '');
        if ($Value === '') { $Err = true; }
          else { $Value = 'Name:'+$Value; }
        var $ClrHex = jQuery('#FLD_Color_Hex').val();
        $ClrHex = $ClrHex.replace(/\s+/g, '');
        if ($Value !== '') {
          $Value = $Value+';Hex:'+$ClrHex;
          $Value = $Value+';R:'+jQuery('#R').html();
          $Value = $Value+';G:'+jQuery('#G').html();
          $Value = $Value+';B:'+jQuery('#B').html();
        } else {
          $Err = true;
        }
        break;
      case 'Image':
        $Value = jQuery('#FLD_ColorName').val();
        $Value = $Value.replace(/\s+/g, '');
        if ($Value === '') { $Err = true; }
          else { $Value = 'Name:'+$Value; }
        var $Img = jQuery('#FLD_Logo').val();
        $Img = $Img.replace(/\s+/g, '');
        if ($Img === '') { $Err = true; }
          else { $Value = $Value+';Img:'+$Img; }
        break;
      default:
        $EntryType = jQuery('#ActiveEntryType').val();
        if ($EntryType === 'Single') {
          $Value = jQuery('#FLD_AttrValue').val();
          $Value = $Value.replace(/\s+/g, '');
          if ($Value === '') { $Err = true; }
            else { $Value = 'Sngl:'+$Value; }
        } else {
          var $MinVal = jQuery('#FLD_MinVal').val();
          $MinVal = $MinVal.replace(/\s+/g, '');
          if ($MinVal === '') { $Err = true; }
          var $MaxVal = jQuery('#FLD_MaxVal').val();
          $MaxVal = $MaxVal.replace(/\s+/g, '');
          if ($MaxVal === '') { $Err = true; }
          var $Step = jQuery('#FLD_Step').val();
          $Step = $Step.replace(/\s+/g, '');
          if ($Step === '') { $Err = true; }
          if (!$Err) { $Value = 'Min:'+$MinVal+';Max:'+$MaxVal+';Step:'+$Step; }
        }
        break;
    }
    if (!$Err) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        dataType: 'text',
        data: { 'Security': $sec,
          'action' : 'mensio_ajax_Products_Save_Global_Attribute_Value',
          'Attribute' : $attr,
          'Name' : $AttrName,
          'Type' : $AttrType,
          'ValID' : $id,
          'Value' : $Value
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#AttrValList').html(data.ValueList);
            jQuery('#FLD_AttrValue').val('');
            jQuery('#FLD_ValueID').val('NewValue');
            jQuery('#NOSAVEWARN').hide();
          } else {
            Mensio_Append_New_PopUp(data.Message);
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    } else {
      alert('No empty values allowed');
    }
  });
  jQuery('#DIV_Edit').on('click', '.AttrValEdit',function() {
    var $id = jQuery(this).attr('id');
    $id = $id.replace('EDT_','');
    jQuery('#FLD_ValueID').val($id);
    var $val =  jQuery('#'+$id).html();
    var $name = jQuery('#FLD_Name').html();
    var $type = jQuery('#FLD_Type').html();
    if ($type === 'Text') {
      jQuery('#FLD_AttrValue').val($val);
    } else {
      if ($name === 'Color') {
        jQuery('#FLD_AttrValue').val($val);
        var $Value = jQuery('#CLR_'+$id).val();
        $Value = $Value.split(';');
        jQuery('#FLD_ColorName').val($Value[0]);
        jQuery('#FLD_Color_Hex').val($Value[1]);
        jQuery('#R').html($Value[2]);
        jQuery('#G').html($Value[3]);
        jQuery('#B').html($Value[4]);
        jQuery('.wp-color-result').css("background-color", $Value[1]);
      } else {
        jQuery('#FLD_AttrValue').val($val);
        jQuery('#MultipleDiv').hide();
        jQuery('#SingleDiv').show();
        jQuery('#ActiveEntryType').val('Single');
        jQuery('#BTN_MultipleVal').prop('disabled', true);
      }
    }
  });
  jQuery('#DIV_Edit').on('click', '.AttrValDelete',function() {
    var answer = confirm('Are you sure you want to DELETE the Attribute Value?');
    if (answer === true) {
      var $attr = jQuery('#FLD_Attribute').val();
      var $id = jQuery(this).attr('id');
      $id = $id.replace('DEL_','');
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        dataType: 'text',
        data: { 'Security': $sec,
          'action' : 'mensio_ajax_Products_Remove_Global_Attribute_Value',
          'Attribute' : $attr,
          'Value' : $id
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#AttrValList').html(data.ValueList);
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
  jQuery('#DIV_Edit').on('click', '#FLD_Visibility',function() {
    var $Attribute = jQuery('#FLD_Attribute').val();
    if (jQuery(this).is(':checked')) { jQuery(this).val('1'); }
      else { jQuery(this).val('0'); }
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      dataType: 'text',
      data: { 'Security': $sec,
        'action' : 'mensio_ajax_Products_Toggle_Global_Attribute_Visiblity',
        'Attribute' : $Attribute
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR !== 'FALSE') {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '.CatLstBtn',function() {
    var $id = jQuery(this).attr('id');
    if ($id === 'BTN_SingleVal') {
      jQuery('#MultipleDiv').hide();
      jQuery('#SingleDiv').show();
      jQuery('#ActiveEntryType').val('Single');
    } else {
      jQuery('#SingleDiv').hide();
      jQuery('#MultipleDiv').show();
      jQuery('#ActiveEntryType').val('Multiple');
    }
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_AttrTransSave', function() {
    var $Attr = jQuery('#MDL_TransAttribute').val();
    var $Err = false;
    var $FldID = '';
    var $val = '';
    var $ValPckg = Array();
    var $FrmCtrl = jQuery('.TransFlds');
    for (var $i=0; $i < $FrmCtrl.length; ++$i) {
      $FldID = $FrmCtrl[$i].id;
      $val = jQuery('#'+$FldID).val();
      if ($val === '') { $Err = true; }
        else { $ValPckg.push({ "Field": $FldID, "Value": $val}); }
    }
    if ($Err) {
      alert('One or more fields were empty ');
    } else {
      var $Data = JSON.stringify($ValPckg);
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Update_Global_Attribute_Translations',
          'Attribute': $Attr,
          'Data' : $Data
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          jQuery('#MnsModal').toggle( "slide" );
          Mensio_Append_New_PopUp(data.Message);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }    
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_SaveMetrics', function() {
    var $Store = jQuery('#MDL_Store').val();
    var $Color = jQuery('#MDL_Color').val();
    var $Height = jQuery('#MDL_Height').val();
    var $Length = jQuery('#MDL_Length').val();
    var $Size = jQuery('#MDL_Size').val();
    var $Volume = jQuery('#MDL_Volume').val();
    var $Weight = jQuery('#MDL_Weight').val();
    var $Width = jQuery('#MDL_Width').val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Update_Store_Product_Metrics',
        'Store': $Store,
        'Color' : $Color,
        'Height' : $Height,
        'Length' : $Length,
        'Size' : $Size,
        'Volume' : $Volume,
        'Weight' : $Weight,
        'Width' : $Width
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        Mensio_Append_New_PopUp(data.Message);
        jQuery('#NOSAVEWARN').hide();
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
});