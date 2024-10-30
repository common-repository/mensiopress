
'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
var $SrchPrs = false;
function Mensio_GetExtraSelectors() {
  var $Data = '';
  if (jQuery('.Extra_Selector').length > 0) {
    var $XtrSl = jQuery('.Extra_Selector');
    var $FldID = '';
    var $val = '';
    var Selectors = Array();
    for (var $i=0; $i < $XtrSl.length; ++$i) {
      $FldID = $XtrSl[$i].id;
      $val = jQuery('#'+$FldID).val();
      $FldID = $FldID.split('_');
      var $Found = false;
      for (var $j=0; $j < Selectors.length; ++$j) {
        if ($FldID[4] === Selectors[$j].Field) {
          $Found = true;
        }
      }
      if (!$Found) {
        Selectors.push({ "Field": $FldID[4], "Value": $val});
      }
    }
    $Data = Selectors;
  }
  return $Data;
}
function Mensio_CallAjaxTableLoader($Table,$Page,$Rows,$Search,$Sorter,$sec) {
  var $Data = Mensio_GetExtraSelectors();
  var $JSONData = JSON.stringify($Data);
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: {
      'action':'mensio_ajax_Table_'+$Table, // Here we write the php function
      'Security': $sec,
      'Table' : $Table, // this is where we send the data
      'Page' : $Page,
      'Rows' : $Rows,
      'Search' : $Search,
      'Sorter' : $Sorter,
      'ExtraActions' : $JSONData
    },
    success:function(data) { // IF Correct this outputs the result of the ajax request
      jQuery('#TBL_'+$Table+'_Wrapper').html(data);
      if ($Search !== '') {
        jQuery('#'+$Table+'_SearchFld').val($Search);
        jQuery('#'+$Table+'_SearchFld').focus();
      }
    },
    error: function(errorThrown){ // IF Error this outputs the result of the ajax request
      alert(errorThrown);
    }
  });
}
jQuery(document).ready(function() {
  jQuery('.TBL_DataTable_Wrapper').on('change','.Mns_Tbl_Footer_Pagination_PageSelector', function() {
    var $Table = jQuery(this).attr('id');
    var $Page = jQuery(this).val();
    jQuery('.Mns_Tbl_Footer_Pagination_PageSelector').val($Page);
    $Table = $Table.split('_');
    var $Rows = jQuery('#'+$Table[0]+'_RowSelector_'+$Table[2]).val();
    var $Search = '';
    if ($SrchPrs) {
      $Search = jQuery('#'+$Table[0]+'_SearchFld').val();
    }
   var $Sorter = jQuery('#'+$Table[0]+'_SorterCol').val();
    Mensio_CallAjaxTableLoader($Table[0],$Page,$Rows,$Search,$Sorter,$sec);
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.Mns_Tbl_Footer_Pagination_Previous', function() {
    var $Table = jQuery(this).attr('id');
    $Table = $Table.split('_');
    var $Page = jQuery('#'+$Table[0]+'_PageSelector_'+$Table[2]).val();
    var $Rows = jQuery('#'+$Table[0]+'_RowSelector_'+$Table[2]).val();
    var $Max = jQuery('#'+$Table[0]+'_PageSelector_'+$Table[2]+' option:last-child').val();
    var $Search = '';
    if ($SrchPrs) {
      $Search = jQuery('#'+$Table[0]+'_SearchFld').val();
    }
   var $Sorter = jQuery('#'+$Table[0]+'_SorterCol').val();
    $Page = parseInt($Page) - 1;
    if ($Page === 'NaN') { $Page = 1; }
    if  ($Page === 0) { $Page = $Max; }
    var $Sorter = jQuery('#'+$Table[0]+'_SorterCol').val();
    Mensio_CallAjaxTableLoader($Table[0],$Page,$Rows,$Search,$Sorter,$sec);
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.Mns_Tbl_Footer_Pagination_Next', function() {
    var $Table = jQuery(this).attr('id');
    $Table = $Table.split('_');
    var $Page = jQuery('#'+$Table[0]+'_PageSelector_'+$Table[2]).val();
    var $Rows = jQuery('#'+$Table[0]+'_RowSelector_'+$Table[2]).val();
    var $Max = jQuery('#'+$Table[0]+'_PageSelector_'+$Table[2]+' option:last-child').val();
    var $Search = '';
    if ($SrchPrs) {
      $Search = jQuery('#'+$Table[0]+'_SearchFld').val();
    }
    var $Sorter = jQuery('#'+$Table[0]+'_SorterCol').val();
    $Page = parseInt($Page) + 1;
    if ($Page === 'NaN') { $Page = 1; }
    if  ($Page > $Max) { $Page = 1; }
    var $Sorter = jQuery('#'+$Table[0]+'_SorterCol').val();
    Mensio_CallAjaxTableLoader($Table[0],$Page,$Rows,$Search,$Sorter,$sec);
  });
  jQuery('.TBL_DataTable_Wrapper').on('change','.Mns_Row_Selector', function() {
    var $Table = jQuery(this).attr('id');
    var $Rows = jQuery(this).val();
    jQuery('.Mns_Row_Selector').val();
    $Table = $Table.split('_');
    var $Page = 1;
    var $Search = '';
    if ($SrchPrs) {
      $Search = jQuery('#'+$Table[0]+'_SearchFld').val();
    }
    var $Sorter = jQuery('#'+$Table[0]+'_SorterCol').val();
    Mensio_CallAjaxTableLoader($Table[0],$Page,$Rows,$Search,$Sorter,$sec);
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.Mns_Btn_Search', function() {
    var $Table = jQuery(this).attr('id');
    $Table = $Table.split('_');
    var $Page = 1;
    var $Rows = jQuery('#'+$Table[0]+'_RowSelector_Header').val();
    var $Search = jQuery('#'+$Table[0]+'_SearchFld').val();
    $SrchPrs = true;
    var $Sorter = jQuery('#'+$Table[0]+'_SorterCol').val();
    Mensio_CallAjaxTableLoader($Table[0],$Page,$Rows,$Search,$Sorter,$sec);
  });
  jQuery('.TBL_DataTable_Wrapper').on('keyup','.Mns_Fld_Search', function() {
    var $Table = jQuery(this).attr('id');
    $Table = $Table.split('_');
    var $Search = jQuery('#'+$Table[0]+'_SearchFld').val();
    var $Sorter = jQuery('#'+$Table[0]+'_SorterCol').val();
    if ($Search === '') {
      var $Page = 1;
      var $Rows = jQuery('#'+$Table[0]+'_RowSelector_Header').val();
      $SrchPrs = false;
      Mensio_CallAjaxTableLoader($Table[0],$Page,$Rows,$Search,$Sorter,$sec);
    }
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.Mns_Tbl_Head_Sorter', function() {
    var $Table = jQuery(this).attr('id');
    $Table = $Table.split('_');
    var $OldSort = jQuery('#'+$Table[0]+'_SorterCol').val();
    var $Sorter = $Table[1];
    jQuery('#'+$Table[0]+'_SorterCol').val($Table[1]);
    if ($OldSort === $Sorter) { $Sorter = $Sorter+' DESC'; }
    var $Page = 1;
    var $Rows = jQuery('#'+$Table[0]+'_RowSelector_'+$Table[2]).val();
    var $Search = '';
    if ($SrchPrs) {
      $Search = jQuery('#'+$Table[0]+'_SearchFld').val();
    }
    Mensio_CallAjaxTableLoader($Table[0],$Page,$Rows,$Search,$Sorter,$sec);
  });
  jQuery('.TBL_DataTable_Wrapper').on('change','.Mns_Tbl_Body_Table_Ctrl_Check', function() {
    var $ClrMltSlct = '';
    var $Table = jQuery(this).attr('id');
    $Table = $Table.split('_');
    var $MltSlct = jQuery('#'+$Table[0]+'_MultiSelectTblIDs').val();
    if (this.checked) {
      $ClrMltSlct = $MltSlct+$Table[1]+';';
    } else {
      $ClrMltSlct = '';
      $MltSlct = $MltSlct.split(';');
      for (var $i=0; $i<$MltSlct.length; $i++) {
        if (($Table[1] !== $MltSlct[$i]) && ($MltSlct[$i] !== '')) {
          $ClrMltSlct = $ClrMltSlct+$MltSlct[$i]+';';
        }
      }
    }
    jQuery('#'+$Table[0]+'_MultiSelectTblIDs').val($ClrMltSlct);
  });
  jQuery('.TBL_DataTable_Wrapper').on('change','.Mns_Tbl_Head_Check', function() {
    var $Value = '';
    var $ChkVal = '';
    var $TblName = jQuery(this).attr('id');
    $TblName = $TblName.split('_');
    $TblName = $TblName[1];
    if (jQuery(this).is(':checked')) {
      jQuery('.Mns_Tbl_Head_Check').attr('checked', true);
      jQuery('.Mns_Tbl_Body_Table_Ctrl_Check').attr('checked', true);
      var $ChBoxes = jQuery('.Mns_Tbl_Body_Table_Ctrl_Check');
      for (var i=0; i<$ChBoxes.length ;++i) {
        $ChkVal = jQuery($ChBoxes[i]).attr('id');
        $ChkVal = $ChkVal.split('_');
        $ChkVal = $ChkVal[1];
        $Value = $Value+$ChkVal+';';
      }
      jQuery('#'+$TblName+'_MultiSelectTblIDs').val($Value);
    } else {
      jQuery('.Mns_Tbl_Head_Check').attr('checked', false);
      jQuery('.Mns_Tbl_Body_Table_Ctrl_Check').attr('checked', false);
      jQuery('#'+$TblName+'_MultiSelectTblIDs').val('');
    }
  });
  jQuery('.TBL_DataTable_Wrapper').on('mouseenter','.Mns_Editable_Column', function() {
    var $Column = jQuery(this).attr('id');
    $Column = $Column.split('_');
    jQuery('#EdOpt_'+$Column[0]+'_'+$Column[2]).show();
  });
  jQuery('.TBL_DataTable_Wrapper').on('mouseleave','.Mns_Editable_Column', function() {
    jQuery('.Mns_Subline').hide();
  });
  jQuery('.TBL_DataTable_Wrapper').on('change', '.Extra_Selector',function() {
    var $SlctrID = jQuery(this).attr('id');
    var $Value = jQuery('#'+$SlctrID).val();
    $SlctrID = $SlctrID.split('_');
    var $Area = 'Header';
    if ($SlctrID[3] === $Area ) { $Area = 'Footer'; }
    var $id = '#'+$SlctrID[0]+'_'+$SlctrID[1]+'_'+$SlctrID[2]+'_'+$Area+'_'+$SlctrID[4];
    jQuery($id).val($Value);
    var $Table = jQuery(this).attr('id');
    $Table = $Table.split('_');
    var $Page = 1;
    var $Rows = jQuery('#'+$Table[1]+'_RowSelector_'+$Table[3]).val();
    var $Search = jQuery('#'+$Table[1]+'_SearchFld').val();
    var $Sorter = jQuery('#'+$Table[1]+'_SorterCol').val();
    Mensio_CallAjaxTableLoader($Table[1],$Page,$Rows,$Search,$Sorter,$sec);
  });
});
