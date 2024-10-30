'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_Products_Ratings_SubPager($SubPage) {
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
function Mensio_Products_Ratings_BackToTable() {
  jQuery('.form-control').val('');
  var $Page = jQuery('#Ratings_PageSelector_Header').val();
  var $Rows = jQuery('#Ratings_RowSelector_Header').val();
  var $Search = jQuery('#Ratings_SearchFld').val();
  var $Sorter = jQuery('#Ratings_SorterCol').val();
  Mensio_CallAjaxTableLoader('Ratings',$Page,$Rows,$Search,$Sorter,$sec);
  Mensio_Products_Ratings_SubPager('Table');
}
function Mensio_Products_Ratings_Edit($Rating) {
  jQuery('#FLD_Icon').val('No Image');
  jQuery("#DispImg").attr("src",'');        
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Load_Products_Ratings_Data',
      'Rating' : $Rating
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#FLD_Rating').val(data.Rating);
        jQuery('#FLD_Name').val(data.Name);
        jQuery('#FLD_Min').val(data.MinVal);
        jQuery('#FLD_Max').val(data.MaxVal);
        jQuery('#FLD_Step').val(data.Step);
        jQuery('#FLD_Start').val(data.Start);
        jQuery('#FLD_Icon').val(data.Icon);
        jQuery('#FLD_Active').val(data.Active);
        jQuery("#DispImg").attr("src",data.Icon);        
        Mensio_Products_Ratings_SubPager('Edit');
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Products_Ratings_SaveData() {
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
    if ($val === '') { $Err = true; }
      else { $ValPckg.push({ "Field": $FldID, "Value": $val}); }
  }
  if ($Err) {
    alert('One or more fields were empty');
  } else {
    var $Rating = jQuery('#FLD_Rating').val();
    var $Data = JSON.stringify($ValPckg);
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Updating_Rating_System_Data',
        'Rating' : $Rating,
        'Data' : $Data
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') { jQuery('#FLD_Rating').val(data.Rating); }
        Mensio_Append_New_PopUp(data.Message);
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  }
}
function Mensio_Products_Ratings_Delete($Rating) {
  if ($Rating !== 'NewRating') {
    var answer = confirm('Are you sure you want to DELETE the Rating System?');
    if (answer === true) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Delete_Products_Ratings_Data',
          'Rating' : $Rating
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') { Mensio_Products_Ratings_BackToTable(); }
          Mensio_Append_New_PopUp(data.Message);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  }
}
jQuery(document).ready(function() {
  jQuery('#ButtonArea').on('click', '#BTN_AddNew', function() {
    jQuery('#FLD_Rating').val('NewRating');
    jQuery('#FLD_Name').val('');
    jQuery('#FLD_Min').val('');
    jQuery('#FLD_Max').val('');
    jQuery('#FLD_Step').val('');
    jQuery('#FLD_Start').val('');
    jQuery('#FLD_Icon').val('No Image');
    jQuery('#FLD_Active').val(0);
    jQuery("#DispImg").attr("src",'');
    Mensio_Products_Ratings_SubPager('Edit');
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Back', function() {
    Mensio_Products_Ratings_BackToTable();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Back_Header', function() {
    Mensio_Products_Ratings_BackToTable();
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.BTN_BulkActions', function() {
    var $Action = jQuery('.Bulk_Selector').val();
    var $Selections = jQuery('#Ratings_MultiSelectTblIDs').val();
    if ($Action === 'DEL') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Delete_Products_Ratings_Selections',
          'Selections' : $Selections
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          Mensio_Products_Ratings_BackToTable();
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
      case 'Edit':
        Mensio_Products_Ratings_Edit($EditOption[3]);
        break;
      case 'Delete':
        Mensio_Products_Ratings_Delete($EditOption[3]);
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
        jQuery('#FLD_Icon').val($Image.url);
        jQuery("#DispImg").attr("src",$Image.url);
        jQuery('#NOSAVEWARN').show();
      });
    }
    this.window.open();
  });
  jQuery('#Mns_ClearImg').on('click', function() {
    jQuery('#FLD_Icon').val('No Image');
    jQuery("#DispImg").attr("src",'');
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_Products_Ratings_SaveData();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_Products_Ratings_SaveData();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Delete', function() {
    var $Rating = jQuery('#FLD_Rating').val();
    Mensio_Products_Ratings_Delete($Rating);
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Delete_Header', function() {
    var $Rating = jQuery('#FLD_Rating').val();
    Mensio_Products_Ratings_Delete($Rating);
  });
  jQuery('.TBL_DataTable_Wrapper').on('click', '.Mns_Tbl_Body_Table_Fld_Check',function() {
    var $id = jQuery(this).attr('id');
    var $id = $id.split('_');
    var $val = jQuery(this).val();
    if ($val === '0') {$val = '1'; }
      else { $val = '0'; }
    jQuery('.Mns_Tbl_Body_Table_Fld_Check').val(0);
    jQuery('.Mns_Tbl_Body_Table_Fld_Check').prop('checked', false);
    jQuery(this).val($val);
    jQuery(this).prop('checked', true);
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Update_Active_Rating_System',
        'Courier': $id[2],
        'Active': $val
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'TRUE') {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });  
});