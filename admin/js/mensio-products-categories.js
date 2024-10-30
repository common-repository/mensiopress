'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_Products_Categories_SubPager($SubPage) {
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
function Mensio_Products_Categories_BackToTable() {
  jQuery('.form-control').val('');
  var $Page = jQuery('#Categories_PageSelector_Header').val();
  var $Rows = jQuery('#Categories_RowSelector_Header').val();
  var $Search = jQuery('#Categories_SearchFld').val();
  var $Sorter = jQuery('#Categories_SorterCol').val();
  Mensio_CallAjaxTableLoader('Categories',$Page,$Rows,$Search,$Sorter,$sec);
  Mensio_Products_Categories_SubPager('Table');
}
function Mensio_Products_Categories_TgVisible($Category) {
  jQuery.ajax({
   type: 'post',
   url: ajaxurl,
   data: { 'Security': $sec,
    'action': 'mensio_ajax_Products_Categories_Toggle_Visibility',
    'Data' : $Category
   },
   success:function(data) {
    data = jQuery.parseJSON(data);
    Mensio_Append_New_PopUp(data.Message);
    var $id = '';
    var $val = '';
    if ($Category.length === 36) { $Category = $Category+';' }
    var $Cat = $Category.split(';');
    for (var i=0; i<= $Cat.length; i++) {
      $id = '#Categories_visibility_'+$Cat[i];
      $val = jQuery($id).val();
      if ($val === '1') {
        jQuery($id).prop('checked', false);
        jQuery($id).val('0');
      } else {
        jQuery($id).prop('checked', true);
        jQuery($id).val('1');
      }
    }
    jQuery('.Mns_Tbl_Body_Table_Ctrl_Check').prop('checked', false);
    jQuery('.Mns_Tbl_Body_Table_Ctrl_Check').val('0');
    jQuery('.Mns_Tbl_Head_Check').prop('checked', false);
    jQuery('.Mns_Tbl_Head_Check').val('0');
    jQuery('.Bulk_Selector').val('');
   },
   error: function(errorThrown){
    alert(errorThrown);
   }
  });
}
function Mensio_Products_Categories_Edit($Category) {
  jQuery.ajax({
   type: 'post',
   url: ajaxurl,
   data: { 'Security': $sec,
    'action': 'mensio_ajax_Products_Load_Categories_Data',
    'Category' : $Category
   },
   success:function(data) {
    data = jQuery.parseJSON(data);
    if (data.ERROR === 'FALSE') {
      jQuery('#FLD_Category').val(data.Category);
      jQuery('#FLD_Name').val(data.Name);
      if (data.Visible === '1') {
        jQuery('#FLD_Visibility').prop('checked', true);
      } else {
        jQuery('#FLD_Visibility').prop('checked', false);
      }
      jQuery('#FLD_Slug').val(data.Slug);
      jQuery('#TransList').html(data.Translations);
      jQuery('#FLD_Image').val(data.Image);
      jQuery("#DispImg").attr("src",data.Image);
      jQuery("#AttrListDiv").html(data.Attributes);
      jQuery("#ValuesListDiv").html(data.Values);
      jQuery('#BTN_AddAttr').prop('disabled', false);
      jQuery('#BTN_AddAttrVal').prop('disabled', true);
      jQuery('#FLD_ValueID').val('NewValue');
      Mensio_Products_Categories_SubPager('Edit');
      jQuery("#TransList").show();
    } else {
      Mensio_Append_New_PopUp(data.Message);
    }
   },
   error: function(errorThrown){
    alert(errorThrown);
   }
  });
}
function Mensio_Products_Save_Categories_Data() {
  var $Cat = jQuery('#FLD_Category').val();
  var $Name = jQuery('#FLD_Name').val();
  var $Slug = jQuery('#FLD_Slug').val();
  var $Img = jQuery('#FLD_Image').val();
  var $Vsbl = '0';
  if (jQuery('#FLD_Visibility').is(":checked")) { $Vsbl = '1'; }
  if (($Name !== '') && ($Name !== ' ')) {
    jQuery.ajax({
     type: 'post',
     url: ajaxurl,
     data: { 'Security': $sec,
      'action': 'mensio_ajax_Products_Update_Categories_Data',
      'Category' : $Cat,
      'Slug': $Slug,
      'Name' : $Name,
      'Image' : $Img,
      'Visible' : $Vsbl
     },
     success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#FLD_Category').val(data.Category);
        jQuery('#TransList').html(data.Translations);
        if ($Cat !== 'NewCategory') {
          Mensio_Products_Update_Category_Attributes();
        } else {
          jQuery('#BTN_AddAttr').prop('disabled', false);
          Mensio_Append_New_PopUp(data.Message);
        }
        jQuery("#TransList").show();
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }
     },
     error: function(errorThrown){
      alert(errorThrown);
     }
    });
  } else {
    alert('Please enter a category name');
  }
}
function Mensio_Products_Update_Category_Attributes() {
  var $Err = false;
  var $FldID = '';
  var $Name = '';
  var $Visible = '';
  var $ValPckg = Array();
  var $FrmCtrl = jQuery('.FldAttribute');
  for (var $i=0; $i < $FrmCtrl.length; ++$i) {
    $FldID = $FrmCtrl[$i].id;
    $Name = jQuery('#ATNM_'+$FldID).val();
    $Visible = '0';
    if (jQuery('#ATVS_'+$FldID).is(":checked")) { $Visible = '1'; }
    if ($Name === '') { $Err = true;}
      else { $ValPckg.push({"Attribute": $FldID,"Name": $Name,"Visible": $Visible}); }
  }
  if ($Err) {
    alert('One or more fields were empty');
  } else {
    var $Data = JSON.stringify($ValPckg);
    var $cat = jQuery('#FLD_Category').val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Update_Category_Attributes',
        'Category' : $cat,
        'Data' : $Data
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery("#AttrListDiv").html(data.Attributes);
          jQuery("#ValuesListDiv").html(data.values);
          jQuery('#BTN_AddAttrVal').prop('disabled', true);
        }
        Mensio_Append_New_PopUp(data.Message);
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  }
}
function Mensio_Products_Categories_Delete($Category) {
  var answer = confirm('Are you sure you want to DELETE the Category?\r\n\n\
              It will also erase ALL attributes and values of the category');
  if (answer === true) {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      dataType: 'text',
      data: { 'Security': $sec,
        'action' : 'mensio_ajax_Products_Delete_Category_Data',
        'Category' : $Category
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#FLD_Category').val('NewCategory');
          jQuery('#FLD_Name').val('');
          jQuery('#FLD_Slug').val('');
          jQuery('#FLD_Visibility').prop('checked', false);
          jQuery('#FLD_Image').val('No Image');
          jQuery("#DispImg").attr("src",'');
          jQuery("#AttrListDiv").html('');
          jQuery("#ValuesListDiv").html('');
          jQuery('#BTN_AddAttr').prop('disabled', true);
          jQuery('#BTN_AddAttrVal').prop('disabled', true);
          jQuery('#FLD_ValueID').val('NewValue');
          jQuery('#TransList').html('');
          Mensio_Products_Categories_BackToTable();
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
  jQuery('#ButtonArea').on('click', '#BTN_AddNew', function() {
    jQuery('#FLD_Category').val('NewCategory');
    jQuery('#FLD_Name').val('');
    jQuery('#FLD_Slug').val('');
    jQuery('#FLD_Visibility').prop('checked', false);
    var $dfltimg = jQuery('#DefaultImage').val();
    jQuery('#FLD_Image').val($dfltimg);
    jQuery("#DispImg").attr("src",$dfltimg);
    jQuery("#AttrListDiv").html('');
    jQuery("#ValuesListDiv").html('');
    jQuery('#BTN_AddAttr').prop('disabled', true);
    jQuery('#BTN_AddAttrVal').prop('disabled', true);
    jQuery('#FLD_ValueID').val('NewValue');
    jQuery("#TransList").hide();
    jQuery('#TransList').html('');
    Mensio_Products_Categories_SubPager('Edit');
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Back', function() {
    Mensio_Products_Categories_BackToTable();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Back_Header', function() {
    Mensio_Products_Categories_BackToTable();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_Products_Save_Categories_Data();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_Products_Save_Categories_Data();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Delete', function() {
    var $Cat = jQuery('#FLD_Category').val();
    Mensio_Products_Categories_Delete($Cat);
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Delete_Header', function() {
    var $Cat = jQuery('#FLD_Category').val();
    Mensio_Products_Categories_Delete($Cat);
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.BTN_BulkActions', function() {
    var $Action = jQuery('.Bulk_Selector').val();
    var $Selections = jQuery('#Categories_MultiSelectTblIDs').val();
    if ($Action === 'VIS') {
      Mensio_Products_Categories_TgVisible($Selections);
    }
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.Mns_Tbl_Body_Table_Fld_Check', function() {
    var $EditOption = jQuery(this).attr('id');
    $EditOption = $EditOption.split('_');
     Mensio_Products_Categories_TgVisible($EditOption[2]);
  });
  jQuery('.TBL_DataTable_Wrapper').on('click', '.Mns_Subline_EditOption',function() {
    var $EditOption = jQuery(this).attr('id');
    $EditOption = $EditOption.split('_');
    switch ($EditOption[2]) {
      case 'View':
        Mensio_Products_Categories_View($EditOption[3]);
        break;
      case 'Visible':
        Mensio_Products_Categories_TgVisible($EditOption[3]);
        break;
      case 'Edit':
        Mensio_Products_Categories_Edit($EditOption[3]);
        break;
      case 'Delete':
        Mensio_Products_Categories_Delete($EditOption[3]);
        break;
    }
  });
  jQuery('#Mns_OpenMediaModal').on('click', function() {
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
        jQuery('#FLD_Image').val($Image.url);
        jQuery("#DispImg").attr("src",$Image.url);
        jQuery('#NOSAVEWARN').show();
      });
    }
    this.window.open();
  });
  jQuery('#Mns_ClearImg').on('click', function() {
    jQuery('#FLD_Logo').val('No Image');
    jQuery("#DispImg").attr("src",'');
    jQuery('#NOSAVEWARN').show();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_CatTrans', function() {
    var $Cat = jQuery('#FLD_Category').val();
    if ($Cat !== 'NewCategory') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action' : 'mensio_ajax_Products_Load_Category_Translations',
          'Category' : $Cat
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
    } else {
      alert('Save Category First');
    }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_AddAttr', function() {
    var $Cat = jQuery('#FLD_Category').val();
    if ($Cat !== 'NewCategory') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action' : 'mensio_ajax_Products_Add_Category_Attribute',
          'Category' : $Cat
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery("#AttrListDiv").html(data.Attributes);
            jQuery("#ValuesListDiv").html(data.Values);
            jQuery('#BTN_AddAttrVal').prop('disabled', true);
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
  jQuery('#DIV_Edit').on('click', '.BTN_EditAttr', function() {
    jQuery('.AttrDataDiv').hide();
    jQuery('.AttrHeaderDiv').removeClass('SelectedAttr');
    var $id = jQuery(this).attr('id');
    $id = $id.split('_');
    jQuery('#ActiveAttribute').val($id[1]);
    jQuery('#AttrID_'+$id[1]).addClass('SelectedAttr');
    jQuery('#DataDiv_'+$id[1]).slideToggle('slow');
    jQuery('#ValueEdit_'+$id[1]).slideToggle('slow');
    jQuery('#BTN_AddAttrVal').prop('disabled', false);
  });
  jQuery('#DIV_Edit').on('click', '.BTN_TransAttr', function() {
    var $id = jQuery(this).attr('id');
    $id = $id.replace('Trans_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action' : 'mensio_ajax_Attribute_Load_Translation',
        'Attribute' : $id
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
  jQuery('#DIV_Edit').on('click', '.BTN_RemoveAttr', function() {
    var answer = confirm('Are you sure you want to DELETE the Attribute?\r\nBe carefull It will also erase all values of the attribute');
    if (answer === true) {
      var $id = jQuery(this).attr('id');
      var $Category = jQuery('#FLD_Category').val();
      $id = $id.split('_');
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action' : 'mensio_ajax_Products_Delete_Category_Attribute',
          'Category' : $Category,
          'Attribute' : $id[1]
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#ActiveAttribute').val('');
            jQuery("#AttrListDiv").html(data.Attributes);
            jQuery("#ValuesListDiv").html('');
            jQuery('#BTN_AddAttrVal').prop('disabled', true);
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
  jQuery('#DIV_Edit').on('click', '#BTN_AddAttrVal', function() {
    var $attr = jQuery('#ActiveAttribute').val();
    if ($attr !== '') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action' : 'mensio_ajax_Products_Add_Category_Attribute_Value',
          'Attribute' : $attr,
          'ValueID' : 'NewValue',
          'Name' : 'NewValue'
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery("#ValList_"+$attr).html(data.Values);
            jQuery("#ValList_"+$attr).show();
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
  jQuery('#DIV_Edit').on('click', '.BTN_EditAttrVal', function() {
    var $attr = jQuery(this).attr('id');
    $attr = $attr.split('_');
    var $valID = jQuery('#FLD_ValueID').val();
    var $val = jQuery('#FLD_AttrValue_'+$attr[2]).val();
    if (($val !== '') && ($val !== ' ')) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action' : 'mensio_ajax_Products_Add_Category_Attribute_Value',
          'Attribute' : $attr[2],
          'ValueID' : $valID,
          'Name' : $val
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            var $actv = jQuery('#ActiveAttribute').val();
            jQuery('#ValueInput_'+$actv).toggle('Slide');
            jQuery("#ValList_"+$attr[2]).html(data.Values);
            jQuery('#FLD_AttrValue_'+$attr[2]).val('');
            jQuery('#FLD_ValueID').val('NewValue');
            jQuery('.RgnTypeBtn').removeClass('SelectedAttr');
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
  jQuery('#DIV_Edit').on('click', '.AttrValEdit', function() {
      jQuery('.ValueInputDiv').hide();
      jQuery('.RgnTypeBtn').removeClass('SelectedAttr');
      var $val = jQuery(this).attr('id');
      $val = $val.split('_');
      var $name =  jQuery('#'+$val[1]).html();
      var $attr = jQuery('#ValAttr_'+$val[1]).val();
      jQuery('#FLD_AttrValue_'+$attr).val($name);
      jQuery('#FLD_ValueID').val($val[1]);
      jQuery('#ValueWrap_'+$val[1]).addClass('SelectedAttr');
      var $actv = jQuery('#ActiveAttribute').val();
      jQuery('#ValueInput_'+$actv).toggle('Slide');
  });
  jQuery('#DIV_Edit').on('click', '.AttrValDelete', function() {
    var answer = confirm('Are you sure you want to DELETE the Value?');
    if (answer === true) {
      var $val = jQuery(this).attr('id');
      $val = $val.split('_');
      var $attr =  jQuery('#ValAttr_'+$val[1]).val();
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action' : 'mensio_ajax_Products_Delete_Category_Attribute_Value',
          'Attribute' : $attr,
          'Value' : $val[1]
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('.ValueInputDiv').hide();
            jQuery('#FLD_ValueID').val('NewValue');
            jQuery("#ValList_"+$attr).html(data.Values);
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
  jQuery('.Modal_Wrapper').on('click', '#BTN_CatTransSave', function() {
    var $Cat = jQuery('#FLD_Category').val();
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
          'action': 'mensio_ajax_Products_Update_Category_Translations',
          'Category': $Cat,
          'Data' : $Data
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          Mensio_Append_New_PopUp(data.Message);
          jQuery('#TransList').html(data.Translations);
          jQuery('#MnsModal').toggle( "slide" );
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
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
          'action': 'mensio_ajax_Products_Update_Attribute_Translations',
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
  jQuery('#DIV_Edit').on('keyup', '#FLD_Name', function() {
    var $val = jQuery(this).val();
    $val = $val.toLowerCase();
    $val = $val.replace(new RegExp(' ', 'g'),'-');
    jQuery('#FLD_Slug').val($val);
  });
  jQuery('#DIV_Edit').on('blur', '#FLD_Name', function() {
    var $Name = jQuery('#FLD_Name').val();
    var $Cat = jQuery('#FLD_Category').val();
    if ($Name !== '') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Check_If_Category_Name_Exists',
          'Category' : $Cat,
          'Name' : $Name
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR !== 'FALSE') { jQuery('#CodeMsg').toggle("slide"); }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
});