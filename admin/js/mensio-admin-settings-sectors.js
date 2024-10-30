'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_SectorsSubPager($SubPage) {
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
function Mensio_Sectors_BackToTable() {
  jQuery('.form-control').val('');
  var $Page = jQuery('#Sectors_PageSelector_Header').val();
  var $Rows = jQuery('#Sectors_RowSelector_Header').val();
  var $Search = jQuery('#Sectors_SearchFld').val();
  var $Sorter = jQuery('#Sectors_SorterCol').val();
  Mensio_CallAjaxTableLoader('Sectors',$Page,$Rows,$Search,$Sorter,$sec);
  Mensio_SectorsSubPager('Table');            
}
function Mensio_SaveSectorData() {
  var $Err = false;
  var $FldID = '';
  var $val = '';
  var $ValPckg = Array();
  var $Sctr = jQuery('#FLD_Sector').val();
  if ($Sctr === '') { $Err = true; }
  var $FrmCtrl = jQuery('.form-control');
  for (var $i=0; $i < $FrmCtrl.length; ++$i) {
    $FldID = $FrmCtrl[$i].id;
    if ($FldID !== 'MDL_ParentSector') {
      $val = jQuery('#'+$FldID).val();
      if ($val === '') {
        $Err = true;
      } else {
        $ValPckg.push({ "Field": $FldID, "Value": $val});
      }
    }
  }
  if ($Err) {
    alert('One or more fields were empty');
  } else {
    var $Data = JSON.stringify($ValPckg);
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Save_Sector_Edit_Data',
          'Sector' : $Sctr,
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
function Mensio_DeleteSector() {
  var answer = confirm('Are you sure you want to DELETE the sector?');
  if (answer === true) {
    var $Sctr = jQuery('#FLD_Sector').val();
    if ($Sctr === '') {
      alert('Problem with Sector Code');
    } else {
      jQuery.ajax({
          type: 'post',
          url: ajaxurl,
          data: { 'Security': $sec,
            'action': 'mensio_ajax_Delete_Sector',
            'Sector' : $Sctr
          },
          success:function(data) {
            data = jQuery.parseJSON(data);
            Mensio_Append_New_PopUp(data.Message);
            if (data.ERROR === 'FALSE') { Mensio_Sectors_BackToTable(); }
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
    jQuery('.form-control').val('');
    jQuery('#FLD_Sector').val('NewSector');
    jQuery('#FLD_ParentSector').val('TopLevel');
    Mensio_SectorsSubPager('Edit');
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Back', function() {
    Mensio_Sectors_BackToTable();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Back_Header', function() {
    Mensio_Sectors_BackToTable();
  });
  jQuery('#TBL_Sectors_Wrapper').on('click', '.Mns_Subline_EditOption', function() {
    var $val = jQuery(this).attr('id');
    $val = $val.split('_');
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Load_Sector_Edit_Data',
          'Sector' : $val[3]
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#FLD_Sector').val(data.Sector);
            jQuery('#FLD_ParentSector').val(data.ParentSector);
            var $Trans = data.Translations.split('??');
            var $Translation = '';
            for (var $i=0; $i < $Trans.length; ++$i) {
              $Translation = $Trans[$i].split('::');
              jQuery('#'+$Translation[0]).val($Translation[1]);
            }
            Mensio_SectorsSubPager('Edit');
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
    });
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_SaveSectorData();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_SaveSectorData();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Delete', function() {
    Mensio_DeleteSector();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Delete_Header', function() {
    Mensio_DeleteSector();
  });
  jQuery('.Modal_Wrapper').on('click', '.Mdl_Btn_Close', function() {
    Mensio_Sectors_BackToTable();
  });  
  jQuery('.TBL_DataTable_Wrapper').on('click', '.BTN_BulkActions',function() {
    var $Slctrs = jQuery('#Sectors_MultiSelectTblIDs').val();
    if ($Slctrs !== '') {
      var $Action = '';
      var $BlkSlctr = jQuery('.Bulk_Selector').val();
      switch ($BlkSlctr) {
        case 'CHPR':
          $Action = 'mensio_ajax_Display_Sector_Modal_Parent';
          break;
      }
      if ($Action !== '') {
        jQuery.ajax({
          type: 'post',
          url: ajaxurl,
          data: { 'Security': $sec,
            'action' : $Action,
            'Selected' : $Slctrs
          },
          success:function(data) {
            data = jQuery.parseJSON(data);
            if (data.ERROR === 'FALSE') {
              jQuery('#MnsModal').html(data.Modal);
              jQuery('#MnsModal').toggle('slide');
            }
            Mensio_Append_New_PopUp(data.Message);
          },
          error: function(errorThrown){
            alert(errorThrown);
          }
        });
      }
    }
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_ModalSave',function() {
    var $Slctrs = jQuery('#Sectors_MultiSelectTblIDs').val();
    var $PrSct = jQuery('#MDL_ParentSector').val();
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action' : 'mensio_ajax_Modal_Update_Sector_Parent',
          'Parent' : $PrSct,
          'Selected' : $Slctrs
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
});