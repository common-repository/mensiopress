'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
var $VariationProperties = new Array();
var $ActiveVarProperty = '';
function Mensio_Products_SubPager($SubPage) {
  switch ($SubPage) {
    case 'Edit':
      jQuery('#DIV_Table').hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Edit Mode');
      jQuery('#DIV_Edit').show(800);
      jQuery('.menu_button_row').show();
      jQuery("#HdBarBtnWrap").show();
      break;
    case 'Table':
      jQuery('.menu_button_row').hide();
      jQuery('#DIV_Edit').hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Table Mode');
      jQuery('#DIV_Table').show(800);
  }
}
function Mensio_Products_BackToTable() {
  jQuery('.form-control').val('');
  var $Page = jQuery('#Products_PageSelector_Header').val();
  var $Rows = jQuery('#Products_RowSelector_Header').val();
  var $Search = jQuery('#Products_SearchFld').val();
  var $Sorter = jQuery('#Products_SorterCol').val();
  Mensio_CallAjaxTableLoader('Products',$Page,$Rows,$Search,$Sorter,$sec);
  Mensio_Products_SubPager('Table');
}
function Mensio_Edit_Product($Product,$VarProd) {
  var $Variable = 'FALSE';
  if ($VarProd) { $Variable = 'TRUE'; }
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Load_Product_Data',
      'Product' : $Product,
      'Variable' : $Variable
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#FLD_Product').val(data.Product);
        jQuery('#FLD_Code').val(data.Code);
        jQuery('#FLD_Brand').val(data.Brand);
        var $Lang = jQuery('#MainLang').val();
        jQuery('#Name_'+$Lang).val(data.Name);
        jQuery('#Desc_'+$Lang).val(data.Description);
        jQuery('#Note_'+$Lang).val(data.Notes);         
        jQuery('#FLD_Name').val(data.Name);
        jQuery('#FLD_Slug').val(data.Slug);
        jQuery('#FLD_Description').val(data.Description);
        Mensio_tmce_setContent(data.Notes,'FLD_ProNotes'); // var $Notes = Mensio_tmce_getContent('FLD_ProNotes');
        jQuery('#FLD_Visible').val(data.Visibility);
        jQuery('#FLD_Reviewable').val(data.Reviewable);
        if (data.Visibility === '0') { jQuery('#FLD_Visible').prop('checked', false); }
          else { jQuery('#FLD_Visible').prop('checked', true); }
        if (data.Reviewable === '0') { jQuery('#FLD_Reviewable').prop('checked', false); }
          else { jQuery('#FLD_Reviewable').prop('checked', true); }
        jQuery('#FLD_BtBPrice').val(data.BtBPrice);
        jQuery('#FLD_BtBTax').val(data.BtBTax);
        jQuery('#FLD_Price').val(data.Price);
        jQuery('#FLD_Tax').val(data.Tax);
        jQuery('#FLD_Discount').val(data.Discount);
        jQuery('#FLD_Stock').val(data.Stock);
        jQuery('#FLD_MinStock').val(data.MinStock);
        jQuery('#FLD_Overstock').val(data.Overstock);
        jQuery('#FLD_Available').val(data.Available);
        jQuery('#FLD_Status').val(data.Status);
        if (data.Status === 'StockRelated') {
          jQuery('#FLD_StockRelated').val('1');
          jQuery('#FLD_StockRelated').prop('checked', true);
          jQuery('#GenericStatusTab').hide();
          jQuery('#StockStatusTab').show();
          jQuery('.Stocklbl').show();
        } else {
          jQuery('#FLD_StockRelated').val('0');
          jQuery('#FLD_StockRelated').prop('checked', false);
          jQuery('#StockStatusTab').hide();
          jQuery('#GenericStatusTab').show();
          jQuery('.Stocklbl').hide();
        }
        jQuery('#FLD_StockStatus').val(data.StockStatus);
        jQuery('#Tbl_StockStatus_Body').html(data.StockStatusTable);
        jQuery('#ProductCategoriesWrap').html(data.Categories);
        jQuery('#ProductVariationWrap').html(data.Variations);
        jQuery('.BDIImages').html(data.ImageList);
        jQuery('#InfoAdvantagesList').html(data.Advantages);
        jQuery('#InfoTagsList').html(data.Tags);
        jQuery('#ProductFilesList').html(data.FileList);
        jQuery('#ProductBarcodesList').html(data.BarcodesList);
        if (data.DnldAlert !== '') {
          jQuery('.DnldAlertWrapper').html(data.DnldAlert);
        }
        jQuery('#tabs').tabs('enable', 3);
        jQuery('#tabs').tabs('enable', 4);
        jQuery('#tabs').tabs('enable', 5);
        jQuery('#tabs').tabs('enable', 6);
        jQuery('#tabs').tabs('enable', 7);
        jQuery('#tabs').tabs('enable', 8);
        jQuery('#tabs').tabs('enable', 9);
        if (data.IsBundle !== 'FALSE') {
          jQuery('#BundleListDiv').html(data.BundleList);
          jQuery('.BandleTab').show();
          jQuery('#tabs').tabs('enable', 10);
        } else {
          jQuery('#tabs').tabs( "option", "disabled",[10]);
          jQuery('.BandleTab').hide();
        }
        jQuery('#tabs').tabs({ active: 0 });
        Mensio_Products_SubPager('Edit');
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_SaveProductData() {
  var $Err = false;
  var $FldID = '';
  var $val = '';
  var $DtFlds = Array();
  var $Trans = Array();
  var $EmptyFlds = '';
  var $FrmCtrl = jQuery('.form-control');
  for (var $i=0; $i < $FrmCtrl.length; ++$i) {
    $FldID = $FrmCtrl[$i].id;
    $val = jQuery('#'+$FldID).val();
    if ($FldID === 'FLD_Status') {
      var $StockRelated = jQuery('#FLD_StockRelated').val();
      if ($StockRelated === '1') { $val = 'StockRelated'; }
    }
    if ($FldID === 'FLD_StockStatus') {
      var $StockRelated = jQuery('#FLD_StockRelated').val();
      if ($StockRelated === '0') { $val = 'empty'; }
    }
    if ($val === '') {
      $Err = true;
      $EmptyFlds = $EmptyFlds+' -- '+$FldID;
    } else {
      $DtFlds.push({"Field":$FldID,"Value":$val});
    }
  }
  var $TrID = jQuery('.InfoTranslButtons.TranslSelected').attr('id');
  if ($TrID === undefined || $TrID === null) {
     var $TrID = jQuery('#MainLang').val();
  }
  $TrID = $TrID.replace('INF_','');
  var $Name = jQuery('#FLD_Name').val();
  var $Desc = jQuery('#FLD_Description').val();
  var $Note = Mensio_tmce_getContent('FLD_ProNotes');
  jQuery('#Name_'+$TrID).val($Name);
  jQuery('#Desc_'+$TrID).val($Desc);
  jQuery('#Note_'+$TrID).val($Note);
  var $Langs = jQuery('.InfoTranslButtons');
  for (var $i=0; $i < $Langs.length; ++$i) {
    $FldID = $Langs[$i].id;
    $FldID = $FldID.replace('INF_','');
    $Name = jQuery('#Name_'+$FldID).val();
    $Desc = jQuery('#Desc_'+$FldID).val();
    $Note = jQuery('#Note_'+$FldID).val();
    $Trans.push({"Field":$FldID,"Value":$Name+'|::|'+$Desc+'|::|'+$Note});
  }
  if ($Err) {
    alert('One or more fields were empty: '+$EmptyFlds);
  } else {
    var $DtFlds = JSON.stringify($DtFlds);
    var $Trans = JSON.stringify($Trans);
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Save_Product_Data',
          'Data': $DtFlds,
          'Translations': $Trans
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            var $id = jQuery('#FLD_Product').val();
            jQuery('#FLD_Product').val(data.Product);
            jQuery('#ProductCategoriesWrap').html(data.Categories);
            var tbl = data.StockStatusTable;
            jQuery('#FLD_StockStatus').val(data.StockStatus);
            jQuery('#Tbl_StockStatus_Body').html(tbl.replace(/\\/g, ""));
            jQuery('#tabs').tabs('enable', 3);
            jQuery('#tabs').tabs('enable', 4);
            jQuery('#tabs').tabs('enable', 5);
            jQuery('#tabs').tabs('enable', 6);
            jQuery('#tabs').tabs('enable', 7);
            jQuery('#tabs').tabs('enable', 8);
            jQuery('#tabs').tabs('enable', 9);
            if ($id === 'NewBundle') { jQuery('#tabs').tabs('enable', 10); }
            jQuery('#NOSAVEWARN').hide();
          }
          Mensio_Append_New_PopUp(data.Message);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
    });
  }
}
function Mensio_UpdateFileList($Type,$Data) {
  var $Action = '';
  if ($Type === 'image') { $Action = 'mensio_ajax_Update_Product_Image_List'; }
    else { $Action = 'mensio_ajax_Update_Product_File_List'; }
  var $prd = jQuery('#FLD_Product').val();
  if (($prd !== 'NewProduct') && ($prd !=='NewBundle')) {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': $Action,
        'Product' : $prd,
        'Type' : $Type,
        'DataList' : $Data
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          if ($Type === 'image') { jQuery('.BDIImages').html(data.ImageList); }
            else { jQuery('#ProductFilesList').html(data.FileList); }
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  }
}
function Mensio_Delete_Product($Product,$main=true) {
  var answer = confirm('Are you sure you want to DELETE the product?');
  if (answer === true) {
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Delete_Product_Data',
          'Product' : $Product
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            if ($main) { Mensio_Products_BackToTable(); }
              else { Mensio_Refresh_Products_Variations();}
          }
          Mensio_Append_New_PopUp(data.Message);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
    });
  }
}
function Mensio_Show_Product_Reviews_Modal($Product) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_Show_Product_Reviews_Modal',
      'Product' : $Product
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
function Mensio_CheckProductCode($Prd,$val,$alertMsg) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Check_If_Product_Code_Exists',
      'Product' : $Prd,
      'Code' : $val
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR !== 'FALSE') {
        jQuery('#'+$alertMsg).toggle("slide");
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Refresh_Products_Variations() {
  var prd = jQuery('#FLD_Product').val();
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_Refresh_Products_Variations',
      'Product' : prd
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#ProductVariationWrap').html(data.Variations);
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Load_Variation_Form(id) {
  var prd = jQuery('#FLD_Product').val();
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_Load_Variation_Product_Form_Modal',
      'Product' : prd,
      'Variation' : id
    },
    success:function(data) {
      jQuery('#MnsModal').html(data);
      jQuery('#MnsModal').toggle('slide');
      jQuery('#Mdltabs').tabs(); // Creating tabs
      var $Switch = jQuery('#FLD_StockRelatedVariation').val();
      if ($Switch === '1') {
        jQuery('#GenericVariationStatusTab').hide();
        jQuery('#VariationStockStatusTab').show();
      } else {
        jQuery('#VariationStockStatusTab').hide();
        jQuery('#GenericVariationStatusTab').show();
      }
      var av = jQuery('#MDL_VarAvailable').val();
      jQuery('#MDL_VarAvailable').datepicker();
      jQuery('#MDL_VarAvailable').datepicker( 'option', 'dateFormat', 'yy-mm-dd');
      jQuery('#MDL_VarAvailable').datepicker("setDate", av);
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Remove_StockStatus($Type,$Status) {
  var answer = confirm('Are you sure you want to DELETE the Status ?');
  if (answer === true) {
    var $NewCheck = $Status.split('_');
    if ($NewCheck[0] === 'NewStockStatus') {
      jQuery('#Tbl_Ln_'+$Status).hide();
      var $Fld = '#FLD_StockStatus';
      if ($Type !== 'Default') { $Fld = '#FLD_VarStockStatus'; }
      var $Lst = jQuery($Fld).val();
      var $rec = '';
      var $NLst = '';
      $Lst = $Lst.split(';;');
      for (var i = 0; i < $Lst.length; i++) { 
        $rec = $Lst[i].split('::');
        if ($rec[4] !== $NewCheck[1]) {
          if ($NLst === '') {
            $NLst = $rec[0]+'::'+$rec[1]+'::'+$rec[2]+'::'+$rec[3]+'::'+$rec[4];
          } else {
            $NLst = $NLst+';;'+$rec[0]+'::'+$rec[1]+'::'+$rec[2]+'::'+$rec[3]+'::'+$rec[4];
          }
        }
      }
      jQuery($Fld).val($NLst);
    } else {
      var $Product = '';
      if ($Type === 'Default') { $Product = jQuery('#FLD_Product').val(); }
        else { $Product = jQuery('#MDL_VarProductID').val(); }
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Remove_Product_Stock_Status',
          'Product' : $Product,
          'Status' : $Status
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            if ($Type === 'Default') { 
              jQuery('#FLD_StockStatus').val(data.StockStatus);
              var tbl = data.StockStatusTable;
              jQuery('#Tbl_StockStatus_Body').html(tbl.replace(/\\/g, ""));
            } else {
              jQuery('#FLD_VarStockStatus').val(data.StockStatus);
              var tbl = data.StockStatusTable;
              jQuery('#Tbl_VarStockStatus_Body').html(tbl.replace(/\\/g, ""));
            }
          } else {
            Mensio_Append_New_PopUp(data.Message);
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  }
}
jQuery(document).ready(function() {
  jQuery('#tabs').tabs(); // Creating tabs
  jQuery('#FLD_Available').datepicker();
  jQuery('#FLD_Available').datepicker( 'option', 'dateFormat', 'yy-mm-dd');
  jQuery('.ui-tabs-anchor').click(function(){
    var tab = jQuery(this).attr('id');
    tab = parseInt(tab.replace('ui-id-',''));
    if (tab > 3) { jQuery('#HdBarBtnWrap').hide(); }
      else { jQuery('#HdBarBtnWrap').show(); }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_OpenMediaModal', function() {
    if (this.window === undefined) {
    this.window = wp.media({
      title: 'Select Image',
      library: {type: 'image'},
      multiple: true,
      button: {text: 'Select'}
    });
    var self = this; // Needed to retrieve our variable in the anonymous function below
    this.window.on('select', function() {
      var urls = Array();
      var selection = self.window.state().get('selection').models;
      for (var i=0; i<selection.length; i++) {
        urls.push({"Image": selection[i].attributes.url});
      }
      if (urls.length > 0) {
        var $Data = JSON.stringify(urls);
        Mensio_UpdateFileList('image',$Data);
      }
    });
    }
    this.window.open();
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_OpenModalMediaModal', function() {
    if (this.window === undefined) {
    this.window = wp.media({
      title: 'Select Image',
      library: {type: 'image'},
      multiple: true,
      button: {text: 'Select'}
    });
    var self = this; // Needed to retrieve our variable in the anonymous function below
    this.window.on('select', function() {
      var urls = Array();
      var selection = self.window.state().get('selection').models;
      for (var i=0; i<selection.length; i++) {
        urls.push({"Image": selection[i].attributes.url});
      }
      if (urls.length > 0) {
        var $btns = '';
        var $i = 0;
        var $imgs = jQuery('#MDL_NewImages').val();
        var $Data = JSON.stringify(urls);
        var obj = jQuery.parseJSON($Data);
        jQuery.each(obj, function(key,value) {
          $btns = $btns +'<div id="VarPrdImg_'+$i+'" class="ProdImgWrapper SlctImg"><img src="'+value.Image+'"';
          $btns = $btns +' alt="file_image"><div class="ImgLstOverlay"><div class="text">';
          $btns = $btns +'<div id="SM_'+$i+'" class="ImgBtn BTN_SetMain">Main</div>';
          $btns = $btns +'<div id="DL_'+$i+'" class="ImgBtn BTN_DelImg">Remove</div>';
          $btns = $btns +'</div></div><div class="DivResizer"></div></div>';
          $i = $i+1;
          if ($imgs === 'Empty') { $imgs = value.Image; }
            else { $imgs = $imgs+'::'+value.Image; }
        });
        jQuery('#MDL_NewImages').val($imgs);
        jQuery('#VariationImageList').append($btns);
      }
    });
    }
    this.window.open();
  });
  jQuery('#DIV_Edit').on('click', '.BTN_Back', function() {
    jQuery('#FLD_Name').val('');
    jQuery('#FLD_Slug').val('');
    jQuery('#FLD_Description').val('');
    Mensio_tmce_setContent('','FLD_ProNotes');
    jQuery('.TransFlds').val('');
    jQuery('.InfoTranslButtons').removeClass('TranslSelected');
    var ML = jQuery('#MainLang').val();
    jQuery('#INF_'+ML).addClass('TranslSelected');
    jQuery('.TrAdvBtns').removeClass('TranslSelected');
    jQuery('#ADV_'+ML).addClass('TranslSelected');
    jQuery('#NOSAVEWARN').hide();
    jQuery('#AttrValSearcherDiv').hide();
    Mensio_Products_BackToTable();
  });
  jQuery('#Mensio_HeadBar').on('click', '.BTN_Back', function() {
    jQuery('#FLD_Name').val('');
    jQuery('#FLD_Slug').val('');
    jQuery('#FLD_Description').val('');
    Mensio_tmce_setContent('','FLD_ProNotes');
    jQuery('.TransFlds').val('');
    jQuery('.InfoTranslButtons').removeClass('TranslSelected');
    var ML = jQuery('#MainLang').val();
    jQuery('#INF_'+ML).addClass('TranslSelected');
    jQuery('.TrAdvBtns').removeClass('TranslSelected');
    jQuery('#ADV_'+ML).addClass('TranslSelected');
    jQuery('#NOSAVEWARN').hide();
    jQuery('#AttrValSearcherDiv').hide();
    Mensio_Products_BackToTable();
  });
  jQuery('#ButtonArea').on('click', '#BTN_AddNew', function() {
    jQuery('.form-control').val('');
    jQuery('#FLD_Name').val('');
    jQuery('#FLD_Slug').val('');
    jQuery('#FLD_Description').val('');
    Mensio_tmce_setContent('','FLD_ProNotes');
    jQuery('.TransFlds').val('');
    jQuery('#FLD_Product').val('NewProduct');
    jQuery('#FLD_Brand').val('0');
    jQuery('#FLD_Status').val('0');
    jQuery('.InfoExtraListDiv').html('');
    jQuery('#FLD_Visible').attr('checked', false);
    jQuery('#FLD_Visible').val(0);
    jQuery('#FLD_Reviewable').attr('checked', false);
    jQuery('#FLD_Reviewable').val(0);
    jQuery('.ImageListWrapper').html('');
    jQuery('#ProductCategoriesWrap').html('');
    jQuery('.BDIImages').html('');
    jQuery('#ProductFilesList').html('');
    jQuery('#ProductBarcodesList').html('');
    jQuery('#tabs').tabs( "option", "disabled",[3,4,5,6,7,8,9,10]);
    jQuery('#tabs').tabs({ active: 0 });
    jQuery('#FLD_StockRelated').val('0');
    jQuery('#FLD_StockRelated').prop('checked', false);
    jQuery('#StockStatusTab').hide();
    jQuery('#GenericStatusTab').show();
    jQuery('.Stocklbl').hide();
    jQuery('#FLD_Stock').prop('disabled', false);
    jQuery('#FLD_MinStock').prop('disabled', false);
    jQuery('#FLD_Overstock').prop('disabled', false);
    jQuery('#FLD_Overstock').val(0);
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Product_Type_Selector'
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
  jQuery('#DIV_Edit').on('click', '.BTN_Delete', function() {
    var $Prd = jQuery('#FLD_Product').val();
    Mensio_Delete_Product($Prd);
  });
  jQuery('#Mensio_HeadBar').on('click', '.BTN_Delete', function() {
    var $Prd = jQuery('#FLD_Product').val();
    Mensio_Delete_Product($Prd);
  });
  jQuery('.TBL_DataTable_Wrapper').on('click', '.Mns_Subline_EditOption',function() {
    var $EditOption = jQuery(this).attr('id');
    $EditOption = $EditOption.split('_');
    switch ($EditOption[2]) {
      case 'Reviews':
        Mensio_Show_Product_Reviews_Modal($EditOption[3]);
        break;
      case 'Edit':
        jQuery('#tabs').tabs( "option", "disabled",[3,4,5,6,7,8,9,10]);
        Mensio_Edit_Product($EditOption[3],false);
        break;
      case 'Delete':
        Mensio_Delete_Product($EditOption[3]);
        break;
    }
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.BTN_BulkActions', function() {
    var $check = '';
    var $Action = jQuery('.Bulk_Selector').val();
    var $Selections = jQuery('#Products_MultiSelectTblIDs').val();
    switch ($Action) {
      case 'VIS':
        $check = 'Checked';
        break;
      case 'INV':
        $check = 'Unchecked';
        break;
    }
    if ($Selections !== '') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Update_Product_Visibility',
          'Product' : $Selections,
          'Check' : $check
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR !== 'FALSE') {
            Mensio_Append_New_PopUp(data.Message);
          } else {
            $Selections = $Selections.split(';');
            for (var i=0; i<$Selections.length; ++i) {
              if ($Selections[i] !== '') {
                if ($check === 'Checked') {
                  jQuery('#Products_visibility_'+$Selections[i]).attr('checked', true);
                } else {
                  jQuery('#Products_visibility_'+$Selections[i]).attr('checked', false);
                }
              }
            }
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.Mns_Tbl_Body_Table_Fld_Check', function() {
    var $check = '';
    var $id = jQuery(this).attr('id');
    var $val = jQuery('#'+$id).val();
    if ($val === '0') {
      jQuery('#'+$id).attr('checked', true);
      jQuery('#'+$id).val('1');
      $check = 'Checked';
    } else {
      jQuery('#'+$id).attr('checked', false);
      jQuery('#'+$id).val('1');
      $check = 'Unchecked';
    }
    $id = $id.split('_');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Update_Product_Visibility',
        'Product' : $id[2],
        'Check' : $check
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
  jQuery('.Modal_Wrapper').on('click', '#NewStdProd', function() {
    jQuery('.ModalListCol').hide();
    jQuery('.ModalBtnCol').animate({ width: "100%"}, 800);
    jQuery('#MnsModal').toggle('slide');
    jQuery('#FLD_Product').val('NewProduct');
    var $val = jQuery('#MainLang').val();
    jQuery('#FLD_Language').val($val);
    jQuery('.InfoTranslButtons').removeClass('TranslSelected');
    jQuery('#INF_'+$val).addClass('TranslSelected');
    Mensio_Products_SubPager('Edit');
    jQuery('.BandleTab').hide();
  });
  jQuery('.Modal_Wrapper').on('click', '#NewBundle', function() {
    jQuery('.ModalListCol').hide();
    jQuery('.ModalBtnCol').animate({ width: "100%"}, 800);
    jQuery('#MnsModal').toggle('slide');
    jQuery('#FLD_Product').val('NewBundle');
    var $val = jQuery('#MainLang').val();
    jQuery('#FLD_Language').val($val);
    jQuery('.InfoTranslButtons').removeClass('TranslSelected');
    jQuery('#INF_'+$val).addClass('TranslSelected');
    Mensio_Products_SubPager('Edit');
    jQuery('.BandleTab').show();
  });
  jQuery('.Modal_Wrapper').on('click', '#Mns_OpenMediaModal', function() {
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
    });
    }
    this.window.open();
  });
  jQuery('.Modal_Wrapper').on('click', '#Mns_ClearImg', function() {
    jQuery('#FLD_Logo').val('No Image');
    jQuery("#DispImg").attr("src",'');
  });
  jQuery('.Modal_Wrapper').on('click', '#MDL_BtnAddStatus', function() {
    var $Status = jQuery('#MDL_Status_ID').val();
    var $Name = jQuery('#MDL_Status').val();
    var $Icon = jQuery('#FLD_Logo').val();
    var $Color = jQuery('#FLD_Color').val();
    var $Test = $Name.replace(/\s+/g, '');
    if ($Test !== '') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Add_Product_Status',
          'Status': $Status,
          'Name': $Name,
          'Icon': $Icon,
          'Color': $Color
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#MDL_StatusListWrapper').html(data.Table);
            jQuery('#FLD_Status').html(data.Options);
            jQuery('#MDL_Status').val('');
            jQuery('#MDL_Status_ID').val('NewEntry');
            jQuery('#FLD_Logo').val(data.DfltImg);
            jQuery("#DispImg").attr("src",data.DfltImg);
            jQuery('#FLD_Color').val('');
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
  jQuery('.Modal_Wrapper').on('click', '.MdlStatusRemoveBtn', function() {
    var answer = confirm('Are you sure you want to DELETE the Status ?');
    if (answer === true) {
      var $Status = jQuery(this).attr('id');
      $Status = $Status.replace('Dlt_','');
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Remove_Product_Status',
          'Status' : $Status
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#MDL_StatusListWrapper').html(data.Table);
            jQuery('#FLD_Status').html(data.Options);
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
  jQuery('.Modal_Wrapper').on('click', '.MdlStatusEditBtn', function() {
    var $StatusID = jQuery(this).attr('id');
    $StatusID = $StatusID.replace('Edt_','');
    var $Icon = jQuery('#Img_'+$StatusID).attr('src');
    var $Name = jQuery('#Name_'+$StatusID).html();
    var $Color = jQuery('#Color_'+$StatusID).attr('attr-bckgrnd');
    jQuery('#MDL_Status_ID').val($StatusID);
    jQuery('#MDL_Status').val($Name);
    jQuery('#FLD_Logo').val($Icon);
    jQuery("#DispImg").attr("src",$Icon);  
    jQuery('#FLD_Color').val($Color);
  });
  jQuery('.Modal_Wrapper').on('click', '.MdlStatusTransBtn', function() {
    var $StatusID = jQuery(this).attr('id');
    $StatusID = $StatusID.replace('Trs_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Product_Load_Status_Translations',
        'Status' : $StatusID
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#StatusTransModal').html(data.Modal);
          jQuery('#StatusTransModal').toggle( "slide" );
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_StatusTransSave', function() {
    var $Product = jQuery('#FLD_Product').val();
    var $Status = jQuery('#StatusTransID').val();
    var $Type = jQuery('#StatusType').val();
    var $Err = false;
    var $FldID = '';
    var $msg = '';
    var $val = '';
    var $ValPckg = Array();
    var $FrmCtrl = jQuery('.StatusTransFlds');
    for (var $i=0; $i < $FrmCtrl.length; ++$i) {
      $FldID = $FrmCtrl[$i].id;
      $val = jQuery('#'+$FldID).val();
      if ($val === '') {
        $Err = true;
        $msg = $msg+' '+$FldID;
      } else {
        $ValPckg.push({ "lang": $FldID, "Value": $val});
      }
    }
    if ($Err) {
      alert('One or more fields were empty '+$msg);
    } else {
      var $Data = JSON.stringify($ValPckg);
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Products_Update_Status_Translations',
          'Product': $Product,
          'Type': $Type,
          'Status': $Status,
          'Data' : $Data
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          Mensio_Append_New_PopUp(data.Message);
          if ($Type === 'Stock') {
            var $Lst = jQuery('#FLD_StockStatus').val();
            var $rec = '';
            var $NLst = '';
            $Lst = $Lst.split(';;');
            for (var i = 0; i < $Lst.length; i++) { 
              $rec = $Lst[i].split('::');
              if ($rec[0] === 'NewStockStatus') {
                if ($NLst === '') {
                  $NLst = $rec[0]+'::'+$rec[1]+'::'+$rec[2]+'::'+$rec[3]+'::'+$rec[4];
                } else {
                  $NLst = $NLst+';;'+$rec[0]+'::'+$rec[1]+'::'+$rec[2]+'::'+$rec[3]+'::'+$rec[4];
                }
              }
            }
            var tbl = data.StockStatus;
            if ($NLst !== '') { $NLst = $NLst +';;'+ tbl.replace(/\\/g, ""); }
              else {$NLst = tbl.replace(/\\/g, ""); }
            jQuery.ajax({
              type: 'post',
              url: ajaxurl,
              data: { 'Security': $sec,
                'action': 'mensio_ajax_Refresh_Product_Stock_Status_Table',
                'List' : $NLst
              },
              success:function(data) {
                jQuery('#FLD_StockStatus').val($NLst);
                jQuery('#Tbl_StockStatus_Body').html(data);
                jQuery('#MnsModal').toggle('slide');
              },
              error: function(errorThrown){
                alert(errorThrown);
              }
            });              
          } else {
            jQuery('#StatusTransModal').html('');
            jQuery('#StatusTransModal').toggle( "slide" );
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('.Modal_Wrapper').on('click', '.CatSelctor', function() {
    var $prd = jQuery('#FLD_Product').val();
    if (($prd !== 'NewProduct') && ($prd !== 'NewBundle')) {
      if (!jQuery( this ).hasClass( 'disabled' ) ) {
        var $Cat = jQuery(this).attr('id');
        jQuery.ajax({
          type: 'post',
          url: ajaxurl,
          data: { 'Security': $sec,
            'action': 'mensio_ajax_Product_Add_Category',
            'Product' : $prd,
            'Category' : $Cat
          },
          success:function(data) {
            data = jQuery.parseJSON(data);
            if (data.ERROR === 'FALSE') {
              jQuery('#ProductCategoriesWrap').html(data.Categories);
              jQuery('#'+$Cat).addClass('disabled');
            } else {
              Mensio_Append_New_PopUp(data.Message);
            }
          },
          error: function(errorThrown){
            alert(errorThrown);
          }
        });
      }
    }
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_ModalSaveAdv', function() {
    var $NewAdv = jQuery('#FLD_Advantage').val();
    if (($NewAdv !== '') && ($NewAdv !== ' ')) {
      var $Prd = jQuery('#FLD_Product').val();
      var $Lang = jQuery('.TrAdvBtns.TranslSelected').attr('id');
      $Lang = $Lang.replace('ADV_','');
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Add_Product_Advantages',
          'Product' : $Prd,
          'Language' : $Lang,
          'Advantage' : $NewAdv
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#InfoAdvantagesList').html(data.Advantages);
            jQuery('#FLD_Advantage').val('');
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
  jQuery('.Modal_Wrapper').on('click', '#BTN_ModalClearTags', function() {
    jQuery('#FLD_TagID').val('NewTag');
    jQuery('#FLD_TagText').val('');
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_ModalSaveTags', function() {
    var $TagText = jQuery('#FLD_TagText').val();
    if (($TagText !== '') && ($TagText !== ' ')) {
      var $Prd = jQuery('#FLD_Product').val();
      var $TagID = jQuery('#FLD_TagID').val();
      $TagID = $TagID.replace('DT_','');
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Update_Product_Tags',
          'Product' : $Prd,
          'Tag' : $TagID,
          'Text' : $TagText
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#InfoTagsList').html(data.Tags);
            jQuery('#FLD_TagID').val(data.TagID);
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
  jQuery('.Modal_Wrapper').on('click', '#BTN_ModalSaveExpiration', function() {
    var $err = false;
    var $File = jQuery('#FLD_File').val();
    var $Expr = jQuery('#FLD_Expiration').val();
    if ($Expr !== '') {
      var $Prd = jQuery('#FLD_Product').val();
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Update_Product_File_Expiration',
          'Product' : $Prd,
          'File' : $File,
          'Expiration' : $Expr
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#ProductFilesList').html(data.FileList);
            jQuery('#MnsModal').toggle('slide');
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
  jQuery('.Modal_Wrapper').on('keyup', '#FLD_SearchProduct', function() {
    var $val = jQuery(this).val();
    jQuery('#MDL_SearchResults').html('');
    var $Prd = jQuery('#FLD_Product').val();
    if (($val !== '') && ($val !== ' ')) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Search_Product_For_Bundle',
          'Product' : $Prd,
          'Search' : $val
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#MDL_SearchResults').html(data.Results);
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
  jQuery('.Modal_Wrapper').on('click', '.SelectProduct', function() {
    var id = jQuery(this).attr('id');
    id = id.replace('BTN_Slct_','');
    jQuery('#FLD_SlctdPrdct').val(id);
    jQuery('#MDL_SearchDiv').toggle('slide', function() {
      jQuery('#VariationListDiv').hide();
      jQuery('#MDL_AmountDiv').toggle('slide');
    });
  });
  jQuery('.Modal_Wrapper').on('click', '#Btn_CloseVarBundleSlctr', function() {
    jQuery('#VariationListDiv').hide();
  });
  jQuery('.Modal_Wrapper').on('click', '.SelectVariation', function() {
    var id = jQuery(this).attr('id');
    id = id.replace('BTN_Vrtn_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Product_Variation_For_Bundle',
        'Product' : id
      },
      success:function(data) {
        jQuery('#VariationList').html(data);
        jQuery('#VariationListDiv').show();
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });    
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_ModalSaveBundle', function() {
    var $Bndl = jQuery('#FLD_Product').val();
    var $Prd = jQuery('#FLD_SlctdPrdct').val();
    var $Amount = jQuery('#FLD_SetAmount').val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Add_Product_To_Bundle',
        'Bundle' : $Bndl,
        'Product' : $Prd,
        'Amount' : $Amount
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#BundleListDiv').html(data.BundleList);
          jQuery('#FLD_SlctdPrdct').val('');
          jQuery('#FLD_SetAmount').val('');
          jQuery('#FLD_SearchProduct').val('');
          jQuery('#MDL_SearchResults').html('');
          jQuery('#MDL_AmountDiv').toggle('slide', function() {
            jQuery('#MDL_SearchDiv').toggle('slide');
          });
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '.InfoTranslButtons',function() {
    var $Lang = jQuery(this).attr('id');
    $Lang = $Lang.replace('INF_','');
    var $Prdt = jQuery('#FLD_Product').val();
    jQuery('#FLD_Language').val($Lang);
    var $BName = jQuery('#Name_'+$Lang).val();
    var $BDesc = jQuery('#Desc_'+$Lang).val();
    var $BNote = jQuery('#Note_'+$Lang).val();
    var $TrID = jQuery('.InfoTranslButtons.TranslSelected').attr('id');
    $TrID = $TrID.replace('INF_','');
    var $Name = jQuery('#FLD_Name').val();
    var $Desc = jQuery('#FLD_Description').val();
    var $Note = Mensio_tmce_getContent('FLD_ProNotes');
    if ($Name !== '') {
      jQuery('#Name_'+$TrID).val($Name);
      jQuery('#Desc_'+$TrID).val($Desc);
      jQuery('#Note_'+$TrID).val($Note);
    }
    jQuery('#FLD_Name').val($BName);
    jQuery('#FLD_Description').val($BDesc);
    Mensio_tmce_setContent($BNote,'FLD_ProNotes');
    if ($BName === '') {
      if (($Prdt !== 'NewProduct') && ($Prdt !== 'NewBundle')) {
        jQuery.ajax({
          type: 'post',
          url: ajaxurl,
          data: { 'Security': $sec,
            'action': 'mensio_ajax_Load_Product_Translations',
            'Product' : $Prdt,
            'Language' : $Lang
          },
          success:function(data) {
            data = jQuery.parseJSON(data);
            if (data.ERROR === 'FALSE') {
              jQuery('#FLD_Name').val(data.Name);
              jQuery('#FLD_Description').val(data.Description);
              Mensio_tmce_setContent(data.Notes,'FLD_ProNotes');
              jQuery('#Name_'+$Lang).val(data.Name);
              jQuery('#Desc_'+$Lang).val(data.Description);
              jQuery('#Note_'+$Lang).val(data.Notes);
            } else {
              Mensio_Append_New_PopUp(data.Message);
            }
          },
          error: function(errorThrown){
            alert(errorThrown);
          }
        });
      }
    }
    jQuery('.InfoTranslButtons').removeClass('TranslSelected');
    jQuery(this).addClass('TranslSelected');
  });
  jQuery('#DIV_Edit').on('click', '.form-check',function() {
    var $Chkid = jQuery(this).attr('id');
    if (jQuery('#'+$Chkid).is(':checked')) { jQuery('#'+$Chkid).val('1'); }
      else { jQuery('#'+$Chkid).val('0'); }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_AddNewStatus',function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Product_New_Status'
      },
      success:function(data) {
        jQuery('#MnsModal').html(data);
        jQuery('#MnsModal').toggle('slide');
        jQuery('#FLD_Color').iris({
          change: function(event, ui) {
            $("#FLD_Color").css( 'color', ui.color.toString());
            $("#FLD_Color").css( 'background', ui.color.toString());
          }
        });
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('click', function() {
    if (jQuery(':focus').hasClass('.iris-picker') || (jQuery(':focus').attr('id') === 'FLD_Color')) {
      jQuery('#FLD_Color').iris('show');
    } else {
      jQuery('#FLD_Color').iris('hide');
    }
  });
  jQuery('#DIV_Edit').on('click', '.BTN_Save', function() {
    Mensio_SaveProductData();
  });
  jQuery('#Mensio_HeadBar').on('click', '.BTN_Save', function() {
    Mensio_SaveProductData();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_AddProdCat', function() {
    var $Product = jQuery('#FLD_Product').val();
    if (($Product !== 'NewProduct') && ($Product !=='NewBundle')) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Load_Modal_Product_Category_Selector',
          'Product' : $Product
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
  jQuery('#DIV_Edit').on('click', '.ValueSelector', function() {
    var $prd = jQuery('#FLD_Product').val();
    if (($prd !== 'NewProduct') && ($prd !== 'NewBundle')) {
      var $Val = jQuery(this).attr('id');
      var $attr = jQuery(this).attr('attr-id');
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Product_Add_Value',
          'Product' : $prd,
          'Value' : $Val
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#AttValLst').html(data.ValueList);
            jQuery('.ValueSelector').removeClass('RgnTypeBtnSelected');
            jQuery('#'+$Val).addClass('RgnTypeBtnSelected');
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
  jQuery('#DIV_Edit').on('click', '.VWCategory', function() {
    var $prd = jQuery('#FLD_Product').val();
    var $Cat = jQuery(this).attr('id');
    $Cat = $Cat.replace('VW_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Product_Filter_Category_Attribute',
        'Product' : $prd,
        'Category' : $Cat
      },
      success:function(data) {
        jQuery('#AttrBtnWrapper').html(data);
        jQuery('#CatAttrValueListWrapper').html('<div class="WrapperEmpty"><div class="">Select Value</div></div>');
        jQuery('.CatBtnName').removeClass('ActiveCat');
        jQuery('#CAT_'+$Cat).addClass('ActiveCat');
        jQuery('#AttrValSearcherDiv').hide();
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '.CategoryAttribute', function() {
    var $id = jQuery(this).attr('id');
    jQuery('.CategoryAttribute').removeClass('AttrSelected');
    jQuery(this).addClass('AttrSelected');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Product_Load_Category_Attribute_Values',
        'Attribute' : $id
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#CatAttrValueListWrapper').html(data.ValueList);
          jQuery('#AttrValSearcherDiv').show();
      } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#ShowAllValues', function() {
    var $prd = jQuery('#FLD_Product').val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Product_ShowAll_Attribute_Values',
        'Product' : $prd
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#ProductCategoriesWrap').html(data.Categories);
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '.RMCategory', function() {
    var answer = confirm('Are you sure you want to remove the Category?');
    if (answer === true) {
      var $prd = jQuery('#FLD_Product').val();
      var $Cat = jQuery(this).attr('id');
      $Cat = $Cat.replace('DEL_','');
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Product_Remove_Category',
          'Product' : $prd,
          'Category' : $Cat
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#ProductCategoriesWrap').html(data.Categories);
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
  jQuery('#DIV_Edit').on('keyup', '#FLD_SearchAttrVal', function() {
    if (jQuery('.CategoryAttribute').hasClass('AttrSelected')) {
      var $Attr = jQuery('.AttrSelected').attr('id');
      var $Srch = jQuery(this).val();
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Product_Search_Category_Attribute_Value',
          'Attribute' : $Attr,
          'Search' : $Srch
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#CatAttrValueListWrapper').html(data.ValueList);
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
  jQuery('#DIV_Edit').on('click', '.ProductValue', function() {
    var $prd = jQuery('#FLD_Product').val();
    var $val = jQuery(this).attr('id');
    var $vsbl = jQuery(this).val();
    if ($vsbl === '1') {
      jQuery(this).prop('checked', false);
      jQuery(this).val('0');
      $vsbl = '0';
    } else {
      jQuery(this).prop('checked', true);
      jQuery(this).val('1');
      $vsbl = '1';
    }
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Product_Update_Value_Visibility',
        'Product' : $prd,
        'Value' : $val,
        'Visibility' : $vsbl
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR !== 'FALSE') { Mensio_Append_New_PopUp(data.Message); }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '.AttrValDelete', function() {
    var answer = confirm('Are you sure you want to remove the value?');
    if (answer === true) {
      var $prd = jQuery('#FLD_Product').val();
      var $Val = jQuery(this).attr('id');
      $Val = $Val.replace('BTN_', '');
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Product_Remove_Value',
          'Product' : $prd,
          'Value' : $Val
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#AttValLst').html(data.ValueList);
            jQuery('#'+$Val).removeClass('SelectedValues');
            jQuery('#'+$Val).addClass('ValueSelector');
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
  jQuery('#DIV_Edit').on('click', '.BTN_SetMain', function() {
    var $prd = jQuery('#FLD_Product').val();
    var $img = jQuery(this).attr('id');
    $img = $img.replace('SM_', '');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Product_Update_Main_Image',
        'Product' : $prd,
        'Image' : $img
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#MainImgWrap').html(data.Image);
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '.BTN_DelImg', function() {
    var $prd = jQuery('#FLD_Product').val();
    var $img = jQuery(this).attr('id');
    $img = $img.replace('DL_', '');
    if (($prd !== 'NewProduct') && ($prd !=='NewBundle')) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Product_Remove_Image',
          'Product' : $prd,
          'Image' : $img
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('.BDIImages').html(data.ImageList);
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
  jQuery('#DIV_Edit').on('click', '#BTN_TagsModal', function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Modal_Product_Tags_Form',
        'Tag': 'NewTag'
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
  jQuery('#DIV_Edit').on('click', '.AttrTagsEdit', function() {
    var $Tag = jQuery(this).attr('id');
    $Tag = $Tag.replace('DT_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Modal_Product_Tags_Form',
        'Tag': $Tag
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
  jQuery('#DIV_Edit').on('click', '.AttrTagsDelete', function() {
    var $Prd = jQuery('#FLD_Product').val();
    var $TagID = jQuery(this).attr('id');
    $TagID = $TagID.replace('DL_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Remove_Product_Tags',
        'Product' : $Prd,
        'Tag' : $TagID
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#InfoTagsList').html(data.Tags);
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#BTN_AddBarcodes', function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Modal_Product_Barcodes_Form'
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
  jQuery('#DIV_Edit').on('click', '.BCDel', function() {
    var $Prd = jQuery('#FLD_Product').val();
    var $Barcode = jQuery(this).attr('id');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Remove_Product_Barcode',
        'Product' : $Prd,
        'Barcode' : $Barcode
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#ProductBarcodesList').html(data.BarcodesList);
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });    
  });
  jQuery('#DIV_Edit').on('click', '.PrdExtraUploads', function() {
    var $type = jQuery(this).attr('id');
    if (this.window === undefined) {
      var $title = jQuery(this).attr('title');
    this.window = wp.media({
      title: $title,
      library: {type: $type},
      multiple: true,
      button: {text: 'Select'}
    });
    var self = this; // Needed to retrieve our variable in the anonymous function below
    this.window.on('select', function() {
      var urls = Array();
      var selection = self.window.state().get('selection').models;
      for (var i=0; i<selection.length; i++) {
        urls.push({"Image": selection[i].attributes.url});
      }
      if (urls.length > 0) {
        var $Data = JSON.stringify(urls);
        Mensio_UpdateFileList($type,$Data);
      }
    });
    }
    this.window.open();
  });
  jQuery('#DIV_Edit').on('click', '.BtnEditFile', function() {
    var $id = jQuery(this).attr('id');
    $id = $id.replace('DT_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Product_FIle_Expiration',
        'File': $id
      },
      success:function(data) {
        jQuery('#MnsModal').html(data);
        jQuery('#FLD_Expiration').datepicker();
        jQuery('#FLD_Expiration').datepicker( 'option', 'dateFormat', 'yy-mm-dd');
        jQuery('#MnsModal').toggle('slide');
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '.BtnRemoveFile', function() {
    var $Prd = jQuery('#FLD_Product').val();
    var $id = jQuery(this).attr('id');
    $id = $id.replace('DL_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Remove_Product_File',
        'Product' : $Prd,
        'File' : $id
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#ProductFilesList').html(data.FileList);
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });    
  });
  jQuery('#DIV_Edit').on('click', '#BTN_AddBdlPrd', function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Add_To_Bundle_Form'
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
  jQuery('#DIV_Edit').on('click', '.BTN_BndlDel', function() {
    var $Bndl = jQuery('#FLD_Product').val();
    var $Prd = jQuery(this).attr('id');
    $Prd = $Prd.replace('DL_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Remove_Product_From_Bundle',
        'Bundle' : $Bndl,
        'Product' : $Prd
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#BundleListDiv').html(data.BundleList);
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('blur', '#FLD_Code', function() {
    var $Prd = jQuery('#FLD_Product').val();
    var $val = jQuery(this).val();
    if ($val !== '') { Mensio_CheckProductCode($Prd,$val,'CodeMsg'); }
  });
  jQuery('#DIV_Edit').on('focus', '#FLD_Code', function() {
    jQuery('#CodeMsg').hide();
  });
  jQuery('#DIV_Edit').on('keyup', '#FLD_Name', function() {
    var en = jQuery('#EnLang').val();
    var actv = jQuery('.TranslSelected').attr('id');
    actv = actv.replace('INF_','');
    if (en === actv) {
      var $val = jQuery(this).val();
      $val = $val.toLowerCase();
      $val = $val.replace(new RegExp(' ', 'g'),'-');
      jQuery('#FLD_Slug').val($val);
    }
  });
  jQuery('#DIV_Edit').on('mouseenter', '.VarElCodeSelection', function() {
    var id = jQuery(this).attr('id');
    id = id.replace('VarElHvr_','');
    jQuery('#VarElInfo_'+id).show();
  });
  jQuery('#DIV_Edit').on('mouseleave', '.VarElCodeSelection', function() {
    jQuery('.ProductVariationInfo').hide();
  });
  jQuery('#DIV_Edit').on('click', '.VarDel', function() {
    var main = jQuery('#FLD_Product').val();
    var id = jQuery(this).attr('id');
    id = id.replace('VarDel_','');
    Mensio_Delete_Product(id,false);
  });
  jQuery('#DIV_Edit').on('click', '.VarEdit', function() {
    var id = jQuery(this).attr('id');
    id = id.replace('VarEdit_','');
    Mensio_Load_Variation_Form(id);
  });
  jQuery('#DIV_Edit').on('click', '.VarSetMain', function() {
    var $prd = jQuery('#FLD_Product').val();
    var $var = jQuery(this).attr('id');
    $var = $var.replace('SetMain_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Change_Product_Main',
        'Product' : $prd,
        'Variation' : $var
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          Mensio_Products_BackToTable();
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#BTN_AddVarProd', function() {
    $VariationProperties = [];
    Mensio_Load_Variation_Form('NewProduct');
  });
  jQuery('.Modal_Wrapper').on('blur', '#MDL_VarCode', function() {
    var $Prd = jQuery('#MDL_VarProductID').val();
    var $val = jQuery(this).val();
    if ($val !== '') { Mensio_CheckProductCode($Prd,$val,'MDL_VarCodeMsg'); }
  });
  jQuery('.Modal_Wrapper').on('click', '.BTN_SetMain', function() {
    var $img = jQuery(this).attr('id');
    jQuery('.SlctImg').removeClass('VarMainImg');
    $img = $img.replace('SM_', '');
    jQuery('#MDL_MainImageID').val($img);
    jQuery(this).parents().eq(2).addClass('VarMainImg');
  });
  jQuery('.Modal_Wrapper').on('click', '.BTN_DelImg', function() {
    var $prd = jQuery('#MDL_VarProductID').val();
    var $img = jQuery(this).attr('id');
    var $lst = jQuery('#MDL_ImageListID').val();
    $img = $img.replace('DL_', '');
    jQuery('#VarPrdImg_'+$img).hide();
    $lst = $lst.replace($img,'');
    $lst = $lst.replace('::::','::');
    jQuery('#MDL_ImageListID').val($lst);
    if (($prd !== 'NewProduct') && ($prd !=='NewBundle')) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Product_Remove_Image',
          'Product' : $prd,
          'Image' : $img
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
    }
  });
  jQuery('.Modal_Wrapper').on('click', '.VarCategoryAttribute', function() {
    var $id = jQuery(this).attr('id');
    jQuery('.VarCategoryAttribute').removeClass('AttrSelected');
    jQuery(this).addClass('AttrSelected');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Product_Load_Category_Attribute_Values',
        'Attribute' : $id
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#VarAttrValSelWrapper').html(data.ValueList);
          jQuery('#VarAttrValSearcherDiv').show();
          $ActiveVarProperty = $id;
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('click', '.ValueSelector', function() {
    var $tbl = '';
    var $Attr = jQuery('#'+$ActiveVarProperty).html();
    var $AttrVal = jQuery(this).html();
    var $id = jQuery(this).attr('id');
    var $found = false;
    for (var $i = 0; $i < $VariationProperties.length; $i++) {
      if ($VariationProperties[$i].Attr === $ActiveVarProperty) {
        $VariationProperties[$i].Value = $id;
        $VariationProperties[$i].ValName = $AttrVal;
        $found = true;
      }
    }
    if (!$found) {
      $VariationProperties.push({"Attr":$ActiveVarProperty, 'AttrName':$Attr,'Value':$id, 'ValName':$AttrVal});
    }
    for (var $i = 0; $i < $VariationProperties.length; $i++) {
      $tbl = $tbl + '<tr><td class="AttrCol">'+$VariationProperties[$i].AttrName+'</td><td>'+$VariationProperties[$i].ValName+'</td></tr>';
    }
    jQuery('#ValAttValLst').html($tbl);
  });
  jQuery('.Modal_Wrapper').on('keyup', '#MDL_VarSearchAttrVal', function() {
    var $Attr = jQuery('#'+$ActiveVarProperty).attr('id');
    var $Srch = jQuery(this).val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Product_Search_Category_Attribute_Value',
        'Attribute' : $Attr,
        'Search' : $Srch
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#VarAttrValSelWrapper').html(data.ValueList);
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('change', '#MDL_VarVisible', function() {
    var $val = jQuery(this).val();
    if ($val === '0') { jQuery('#MDL_VarVisible').val('1'); }
      else { jQuery('#MDL_VarVisible').val('0'); }
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_VariationSave', function() {
    var $Main = jQuery('#FLD_Product').val();
    var $Prd = jQuery('#MDL_VarProductID').val();
    var $StType = jQuery('#FLD_StockRelatedVariation').val();
    var $Err = false;
    var $FldID = '';
    var $val = '';
    var $DtFlds = Array();
    var $Name = '';
    var $Desc = '';
    var $Note = '';
    var $Trans = Array();
    var $FrmCtrl = jQuery('.VarPrdField');
    for (var $i=0; $i < $FrmCtrl.length; ++$i) {
      $FldID = $FrmCtrl[$i].id;
      $val = jQuery('#'+$FldID).val();
      if ($FldID === 'MDL_VarStatus') {
        if ($StType === '1') { $val = 'StockRelated'; }
      }
      if ($FldID === 'FLD_VarStockStatus') {
        if ($StType === '0') { $val = 'empty'; }
      }
      if ($val === '') { $Err = true; }
        else { $DtFlds.push({"Field":$FldID,"Value":$val}); }
    }
    $DtFlds = JSON.stringify($DtFlds);
    var $Attrs = JSON.stringify($VariationProperties);
    if ($Attrs === '[]') {
      var str = jQuery('#ValAttValLst').html();
      str = str.replace(/\s+/g, '');
      if (str === '') {$Err = true; }
    }
    if (!$Err) {
      jQuery.ajax({
          type: 'post',
          url: ajaxurl,
          data: { 'Security': $sec,
            'action': 'mensio_ajax_Update_Variable_Product_Data',
            'Main' : $Main,
            'Product' : $Prd,
            'Data': $DtFlds,
            'Attributes': $Attrs
          },
          success:function(data) {
            data = jQuery.parseJSON(data);
            if (data.ERROR === 'FALSE') {
              jQuery('#NOSAVEWARN').hide();
              jQuery('#ProductVariationWrap').html(data.List);
              jQuery('#FLD_VarStockStatus').val(data.StockStatus);
              var tbl = data.StockStatusTable;
              jQuery('#Tbl_VarStockStatus_Body').html(tbl.replace(/\\/g, ""));
            }
            Mensio_Append_New_PopUp(data.Message);
          },
          error: function(errorThrown){
            alert(errorThrown);
          }
      });
    } else {
      alert('Fields are empty or main porduct properties empty');
    }
  });
  jQuery('#DIV_Edit').on('click', '.VarTrans', function() {
    var $id = jQuery(this).attr('id');
    $id = $id.replace('VarTrans_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Variation_Translation',
        'Variation': $id
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
  jQuery('.Modal_Wrapper').on('click', '#BTN_VarTransSave', function() {
    var $Main = jQuery('#FLD_Product').val();
    var $id = jQuery('#MDL_TransVariation').val();
    var $Err = false;
    var $FldID = '';
    var $val = '';
    var $ValPckg = Array();
    var $FrmCtrl = jQuery('.VarTransFlds');
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
          'action': 'mensio_ajax_Products_Update_Variation_Translations',
          'Product': $Main,
          'Variation': $id,
          'Data' : $Data
        },
        success:function(data) {
            data = jQuery.parseJSON(data);
            if (data.ERROR === 'FALSE') {
              jQuery('#NOSAVEWARN').hide();
              jQuery('#ProductVariationWrap').html(data.Variations);
              jQuery('#MnsModal').toggle('slide');
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
  jQuery('#DIV_Edit').on('click', '#SLD_StockRelated', function() {
    var $val = jQuery('#FLD_StockRelated').val();
    if ($val === '0') {
      $val = '1';
      jQuery('#StockStatusTab').slideDown(function() {
        jQuery('#GenericStatusTab').slideUp();
        jQuery('.Stocklbl').show();
      });
    } else {
      $val = '0';
      jQuery('#GenericStatusTab').slideDown(function() {
        jQuery('#StockStatusTab').slideUp();
        jQuery('.Stocklbl').hide();
      });
    }
    jQuery('#FLD_StockRelated').val($val);
  });
  jQuery('#DIV_Edit').on('click', '#BTN_AddNewStockStatus', function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Stock_Status_Modal',
        'Type': 'Default',
        'Status': 'NewStockStatus'
      },
      success:function(data) {
        jQuery('#MnsModal').html(data);
        jQuery('#MnsModal').toggle('slide');
        jQuery('#FLD_Color').iris({
          change: function(event, ui) {
            $("#FLD_Color").css( 'color', ui.color.toString());
            $("#FLD_Color").css( 'background', ui.color.toString());
          }
        });
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '.StockStatusEditBtn', function() {
    var $Status = jQuery(this).attr('id');
    $Status = $Status.replace('Edt_Stk_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Stock_Status_Modal',
        'Type': 'Default',
        'Status': $Status
      },
      success:function(data) {
        jQuery('#MnsModal').html(data);
        jQuery('#MnsModal').toggle('slide');
        jQuery('#FLD_Color').iris({
          change: function(event, ui) {
            $("#FLD_Color").css( 'color', ui.color.toString());
            $("#FLD_Color").css( 'background', ui.color.toString());
          }
        });
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_StockStatusSave', function() {
    var $Status = jQuery('#MDL_Status_ID').val();
    var $Name = jQuery('#MDL_Status').val();
    var $Icon = jQuery('#FLD_Logo').val();
    var $Color = jQuery('#FLD_Color').val();
    if ($Color === '') { $Color = '#ffffff'; }
    var $Stock = jQuery('#MDL_Stock').val();
    var $Test = $Name.replace(/\s+/g, '');
    if ($Test !== '') {
      var $Lst = jQuery('#FLD_StockStatus').val();
      if ($Status !== 'NewStockStatus') {
        var $rec = '';
        var $NLst = '';
        $Lst = $Lst.split(';;');
        for (var i = 0; i < $Lst.length; i++) { 
          $rec = $Lst[i].split('::');
          if ($rec[0] === $Status) {
            $rec[1] = $Name;
            $rec[2] = $Icon;
            $rec[3] = $Color;
            $rec[4] = $Stock;
          }
          if ($NLst === '') {
            $NLst = $rec[0]+'::'+$rec[1]+'::'+$rec[2]+'::'+$rec[3]+'::'+$rec[4];
          } else {
            $NLst = $NLst+';;'+$rec[0]+'::'+$rec[1]+'::'+$rec[2]+'::'+$rec[3]+'::'+$rec[4];
          }
        }
        $Lst = $NLst;
      } else {
        var $rec = '';
        var $NLst = '';
        var $Found = false;
        $Lst = $Lst.split(';;');
        for (var i = 0; i < $Lst.length; i++) { 
          $rec = $Lst[i].split('::');
          if ($rec[4] === $Stock) {
            $rec[1] = $Name;
            $rec[2] = $Icon;
            $rec[3] = $Color;
            $Found = true;
          }
          if ($NLst === '') {
            $NLst = $rec[0]+'::'+$rec[1]+'::'+$rec[2]+'::'+$rec[3]+'::'+$rec[4];
          } else {
            $NLst = $NLst+';;'+$rec[0]+'::'+$rec[1]+'::'+$rec[2]+'::'+$rec[3]+'::'+$rec[4];
          }
        }
        $Lst = $NLst;
        if (!$Found) {
          if ($Lst === '') { $Lst = $Status+'::'+$Name+'::'+$Icon+'::'+$Color+'::'+$Stock; }
            else { $Lst = $Lst+';;'+$Status+'::'+$Name+'::'+$Icon+'::'+$Color+'::'+$Stock; }
        }
      }
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Refresh_Product_Stock_Status_Table',
          'List' : $Lst
        },
        success:function(data) {
          jQuery('#FLD_StockStatus').val($Lst);
          jQuery('#Tbl_StockStatus_Body').html(data);
          jQuery('#MnsModal').toggle('slide');
          jQuery('#NOSAVEWARN').show();
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    } 
  });
  jQuery('#DIV_Edit').on('click', '.StockStatusRemoveBtn', function() {
    var $Status = jQuery(this).attr('id');
    $Status = $Status.replace('Dlt_Stk_','');
    Mensio_Remove_StockStatus('Default',$Status);
  });
  jQuery('.Modal_Wrapper').on('click', '.StockStatusRemoveBtn', function() {
    var $Status = jQuery(this).attr('id');
    $Status = $Status.replace('Dlt_Stk_','');
    Mensio_Remove_StockStatus('Variation',$Status);
  });
  jQuery('#DIV_Edit').on('click', '.StockStatusTransBtn', function() {
    var $Status = jQuery(this).attr('id');
    $Status = $Status.replace('Trs_Stk_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Product_Load_Stock_Status_Translations',
        'Type': 'Default',
        'Status' : $Status
      },
      success:function(data) {
        jQuery('#MnsModal').html(data);
        jQuery('#MnsModal').toggle( "slide" );
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#BTN_CopyStockStatus', function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Product_Load_Stock_Status_Copy_Form',
        'Type': 'Default'
      },
      success:function(data) {
        jQuery('#MnsModal').html(data);
        jQuery('#MnsModal').toggle( "slide" );
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('keyup', '#MDL_SearchProduct', function() {
    var $val = jQuery(this).val();
    var $Test = $val.replace(/\s+/g, '');
    if ($Test !== '') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Product_Search_Stock_Status',
          'Search': $val
        },
        success:function(data) {
          jQuery('#MDL_SearchResults').html(data);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('.Modal_Wrapper').on('click', '.StockStatusSelection', function() {
    var $type = jQuery('#MDL_SearchFormType').val();
    var $Product = '';
    if ($type === 'Default') { $Product = jQuery('#FLD_Product').val(); }
      else { $Product = jQuery('#MDL_VarProductID').val(); }
    var $CopyFrom = jQuery(this).attr('id');
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Product_Copy_Stock_Status',
          'Product': $Product,
          'CopyFrom': $CopyFrom
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            if ($type === 'Default') {
              jQuery('#FLD_StockStatus').val(data.StockStatus);
              var tbl = data.StockStatusTable;
              jQuery('#Tbl_StockStatus_Body').html(tbl.replace(/\\/g, ""));
              jQuery('#MnsModal').toggle( "slide" );
              jQuery('#MnsModal').html('');
            } else {
              jQuery('#FLD_VarStockStatus').val(data.StockStatus);
              var tbl = data.StockStatusTable;
              jQuery('#Tbl_VarStockStatus_Body').html(tbl.replace(/\\/g, ""));
              jQuery('#VariationMnsModal').toggle( "slide" );
              jQuery('#VariationMnsModal').html('');
            }
          } else {
            Mensio_Append_New_PopUp(data.Message);
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
  });
  jQuery('.Modal_Wrapper').on('click', '#SLD_StockRelatedVariation', function() {
    var $val = jQuery('#FLD_StockRelatedVariation').val();
    if ($val === '0') {
      $val = '1';
      jQuery('#VariationStockStatusTab').slideDown(function() {
        jQuery('#GenericVariationStatusTab').slideUp();
        jQuery('.Stocklbl').show();
      });
    } else {
      $val = '0';
      jQuery('#GenericVariationStatusTab').slideDown(function() {
        jQuery('#VariationStockStatusTab').slideUp();
        jQuery('.Stocklbl').hide();
      });
    }
    jQuery('#FLD_StockRelatedVariation').val($val);
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_VarAddNewStockStatus', function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Stock_Status_Modal',
        'Type': 'Variation',
        'Status': 'NewStockStatus'
      },
      success:function(data) {
        jQuery('#VariationMnsModal').html(data);
        jQuery('#VariationMnsModal').toggle('slide');
        jQuery('#FLD_Color').iris({
          change: function(event, ui) {
            $("#FLD_Color").css( 'color', ui.color.toString());
            $("#FLD_Color").css( 'background', ui.color.toString());
          }
        });
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_VarStockStatusSave', function() {
    var $Status = jQuery('#MDL_Status_ID').val();
    var $Name = jQuery('#MDL_Status').val();
    var $Icon = jQuery('#FLD_Logo').val();
    var $Color = jQuery('#FLD_Color').val();
    if ($Color === '') { $Color = '#ffffff'; }
    var $Stock = jQuery('#MDL_Stock').val();
    var $Test = $Name.replace(/\s+/g, '');
    if ($Test !== '') {
      var $Lst = jQuery('#FLD_VarStockStatus').val();
      if ($Status !== 'NewStockStatus') {
        var $rec = '';
        var $NLst = '';
        if ($Lst !== '') {
          $Lst = $Lst.split(';;');
          for (var i = 0; i < $Lst.length; i++) {
            $rec = $Lst[i].split('::');
            if ($rec[0] === $Status) {
              $rec[1] = $Name;
              $rec[2] = $Icon;
              $rec[3] = $Color;
              $rec[4] = $Stock;
            }
            if ($NLst === '') {
              $NLst = $rec[0]+'::'+$rec[1]+'::'+$rec[2]+'::'+$rec[3]+'::'+$rec[4];
            } else {
              $NLst = $NLst+';;'+$rec[0]+'::'+$rec[1]+'::'+$rec[2]+'::'+$rec[3]+'::'+$rec[4];
            }
          }
        }
        $Lst = $NLst;
      } else {
        var $rec = '';
        var $NLst = '';
        var $Found = false;
        if ($Lst !== '') {
          $Lst = $Lst.split(';;');
          for (var i = 0; i < $Lst.length; i++) { 
            $rec = $Lst[i].split('::');
            if ($rec[4] === $Stock) {
              $rec[1] = $Name;
              $rec[2] = $Icon;
              $rec[3] = $Color;
              $Found = true;
            }
            if ($NLst === '') {
              $NLst = $rec[0]+'::'+$rec[1]+'::'+$rec[2]+'::'+$rec[3]+'::'+$rec[4];
            } else {
              $NLst = $NLst+';;'+$rec[0]+'::'+$rec[1]+'::'+$rec[2]+'::'+$rec[3]+'::'+$rec[4];
            }
          }
        }
        $Lst = $NLst;
        if (!$Found) {
          if ($Lst === '') { $Lst = $Status+'::'+$Name+'::'+$Icon+'::'+$Color+'::'+$Stock; }
            else { $Lst = $Lst+';;'+$Status+'::'+$Name+'::'+$Icon+'::'+$Color+'::'+$Stock; }
        }
      }
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Refresh_Product_Stock_Status_Table',
          'List' : $Lst
        },
        success:function(data) {
          jQuery('#FLD_VarStockStatus').val($Lst);
          jQuery('#Tbl_VarStockStatus_Body').html(data);
          jQuery('#VariationMnsModal').toggle('slide');
          jQuery('#NOSAVEWARN').show();
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_VarCopyStockStatus', function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Product_Load_Stock_Status_Copy_Form',
        'Type': 'Variation'
      },
      success:function(data) {
        jQuery('#VariationMnsModal').html(data);
        jQuery('#VariationMnsModal').toggle('slide');
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('click', '.StockStatusEditBtn', function() {
    var $Status = jQuery(this).attr('id');
    $Status = $Status.replace('Edt_Stk_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Stock_Status_Modal',
        'Type': 'Variation',
        'Status': $Status
      },
      success:function(data) {
        jQuery('#VariationMnsModal').html(data);
        jQuery('#VariationMnsModal').toggle('slide');
        jQuery('#FLD_Color').iris({
          change: function(event, ui) {
            $("#FLD_Color").css( 'color', ui.color.toString());
            $("#FLD_Color").css( 'background', ui.color.toString());
          }
        });
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('click', '.StockStatusTransBtn', function() {
    var $Status = jQuery(this).attr('id');
    $Status = $Status.replace('Trs_Stk_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Product_Load_Stock_Status_Translations',
        'Type': 'Variation',
        'Status' : $Status
      },
      success:function(data) {
        jQuery('#VariationMnsModal').html(data);
        jQuery('#VariationMnsModal').toggle( "slide" );
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_VarStatusTransSave', function() {
    var $Product = jQuery('#MDL_VarProductID').val();
    var $Status = jQuery('#StatusTransID').val();
    var $Type = jQuery('#StatusType').val();
    var $Err = false;
    var $FldID = '';
    var $msg = '';
    var $val = '';
    var $ValPckg = Array();
    var $FrmCtrl = jQuery('.StatusTransFlds');
    for (var $i=0; $i < $FrmCtrl.length; ++$i) {
      $FldID = $FrmCtrl[$i].id;
      $val = jQuery('#'+$FldID).val();
      if ($val === '') {
        $Err = true;
        $msg = $msg+' '+$FldID;
      } else {
        $ValPckg.push({ "lang": $FldID, "Value": $val});
      }
    }
    if ($Err) {
      alert('One or more fields were empty '+$msg);
    } else {
      var $Data = JSON.stringify($ValPckg);
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Products_Update_Status_Translations',
          'Product': $Product,
          'Type': $Type,
          'Status': $Status,
          'Data' : $Data
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          Mensio_Append_New_PopUp(data.Message);
          if ($Type === 'Stock') {
            var $Lst = jQuery('#FLD_VarStockStatus').val();
            var $rec = '';
            var $NLst = '';
            $Lst = $Lst.split(';;');
            for (var i = 0; i < $Lst.length; i++) { 
              $rec = $Lst[i].split('::');
              if ($rec[0] === 'NewStockStatus') {
                if ($NLst === '') {
                  $NLst = $rec[0]+'::'+$rec[1]+'::'+$rec[2]+'::'+$rec[3]+'::'+$rec[4];
                } else {
                  $NLst = $NLst+';;'+$rec[0]+'::'+$rec[1]+'::'+$rec[2]+'::'+$rec[3]+'::'+$rec[4];
                }
              }
            }
            var tbl = data.StockStatus;
            if ($NLst !== '') { $NLst = $NLst +';;'+ tbl.replace(/\\/g, ""); }
              else {$NLst = tbl.replace(/\\/g, ""); }
            jQuery.ajax({
              type: 'post',
              url: ajaxurl,
              data: { 'Security': $sec,
                'action': 'mensio_ajax_Refresh_Product_Stock_Status_Table',
                'List' : $NLst
              },
              success:function(data) {
                jQuery('#FLD_VarStockStatus').val($NLst);
                jQuery('#Tbl_VarStockStatus_Body').html(data);
                jQuery('#VariationMnsModal').toggle('slide');
              },
              error: function(errorThrown){
                alert(errorThrown);
              }
            });              
          } else {
            jQuery('#StatusTransModal').html('');
            jQuery('#StatusTransModal').toggle( "slide" );
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
});