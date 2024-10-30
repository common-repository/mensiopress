'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_Products_Brands_SubPager($SubPage) {
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
function Mensio_Products_Brands_BackToTable() {
  jQuery('.form-control').val('');
  var $Page = jQuery('#Brands_PageSelector_Header').val();
  var $Rows = jQuery('#Brands_RowSelector_Header').val();
  var $Search = jQuery('#Brands_SearchFld').val();
  var $Sorter = jQuery('#Brands_SorterCol').val();
  Mensio_CallAjaxTableLoader('Brands',$Page,$Rows,$Search,$Sorter,$sec);
  Mensio_Products_Brands_SubPager('Table');
}
function Mensio_Products_Brands_Edit($Brand) {
  jQuery('#FLD_Logo').val('No Image');
  jQuery("#DispImg").attr("src",'');
  var $Langs = jQuery('.TranslButtons');
  for (var $i=0; $i < $Langs.length; ++$i) {
    jQuery('#Note_'+$Langs[$i].id).val('');
  }
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: {
      'action': 'mensio_ajax_Load_Products_Brands_Data',
      'Security': $sec,
      'Brand' : $Brand
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      var trans = jQuery.parseJSON(data.Trans);
      if (data.ERROR === 'FALSE') {
        jQuery('#FLD_Brand').val(data.Brand);
        jQuery('#FLD_Name').val(data.Name);
        jQuery('#FLD_Slug').val(data.Slug);
        jQuery('#FLD_WebPage').val(data.WebPage);
        jQuery('#FLD_Visible').val(data.Visible);
        jQuery('#FLD_Logo').val(data.Logo);
        jQuery('#FLD_Color').val(data.Color);
        jQuery("#DispImg").attr("src",data.Logo);
        Mensio_tmce_setContent(data.Notes,'FLD_Notes');
        for (var i = 0; i < trans.length; i++) {
          jQuery('#Note_'+trans[i]['language']).val(trans[i]['notes']);
        }
        Mensio_Products_Brands_SubPager('Edit');
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Products_Brands_SaveData() {
  jQuery('#Note_'+jQuery('.TranslSelected').attr('id')).val(Mensio_tmce_getContent('FLD_Notes'));
  var $Err = false;
  var $id = '';
  var $Note = '';
  var $Trans = Array();
  var $Brand = jQuery('#FLD_Brand').val();
  if ($Brand === '') { $Err = true; }
  var $Name = jQuery('#FLD_Name').val();
  if ($Name === '') { $Err = true; }
  var $Slug = jQuery('#FLD_Slug').val();
  if ($Slug === '') { $Err = true; }
  var $WebPage = jQuery('#FLD_WebPage').val();
  if ($WebPage === '') { $Err = true; }
  var $Logo = jQuery('#FLD_Logo').val();
  if ($Logo === '') { $Err = true; }
  var $Vsbl = jQuery('#FLD_Visible').val();
  var $Color = jQuery('#FLD_Color').val();
  if ($Color === '') { $Err = true; }
  var $Langs = jQuery('.TranslButtons');
  for (var $i=0; $i < $Langs.length; ++$i) {
    $id = $Langs[$i].id;
    $Note = jQuery('#Note_'+$id).val();
    $Trans.push({"language":$id,"note":$Note});
  }
  if ($Err) {
    alert('One or more fields were empty');
  } else {
    $Trans = JSON.stringify($Trans);
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: {
        'action': 'mensio_ajax_Updating_Brand_System_Data',
        'Security': $sec,
        'Brand' : $Brand,
        'Name' : $Name,
        'Slug' : $Slug,
        'WebPage': $WebPage,
        'Logo': $Logo,
        'Color': $Color,
        'Visible': $Vsbl,
        'Notes': $Trans
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') { jQuery('#FLD_Brand').val(data.Brand); }
        Mensio_Append_New_PopUp(data.Message);
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  }
}
function Mensio_Products_Brands_Delete($Brand) {
  if ($Brand !== 'NewBrand') {
    var answer = confirm('Are you sure you want to DELETE the Brand ?');
    if (answer === true) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
          'action': 'mensio_ajax_Delete_Products_Brands_Data',
          'Security': $sec,
          'Brand' : $Brand
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') { Mensio_Products_Brands_BackToTable(); }
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
  jQuery('#FLD_Color').iris({
    change: function(event, ui) {
      $("#FLD_Color").css( 'color', ui.color.toString());
      $("#FLD_Color").css( 'background', ui.color.toString());
    }
  });
  jQuery('#DIV_Edit').on('click', function() {
    if (jQuery(':focus').hasClass('.iris-picker') || (jQuery(':focus').attr('id') === 'FLD_Color')) {
      jQuery('#FLD_Color').iris('show');
    } else {
      jQuery('#FLD_Color').iris('hide');
    }
  });
  jQuery('#ButtonArea').on('click', '#BTN_AddNew', function() {
    jQuery('#FLD_Brand').val('NewBrand');
    jQuery('#FLD_Name').val('');
    jQuery('#FLD_Slug').val('');
    jQuery('#FLD_WebPage').val('');
    jQuery('#FLD_Color').val('#FFFFFF');
    jQuery('#FLD_Visible').val(0);
    var $dfltimg = jQuery('#DefaultImage').val();
    jQuery('#FLD_Logo').val($dfltimg);
    jQuery("#DispImg").attr("src",$dfltimg);
    Mensio_tmce_setContent('','FLD_Notes');
    var $Langs = jQuery('.TranslButtons');
    for (var $i=0; $i < $Langs.length; ++$i) {
      jQuery('#Note_'+$Langs[$i].id).val('');
    }
    Mensio_Products_Brands_SubPager('Edit');
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Back', function() {
    Mensio_Products_Brands_BackToTable();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Back_Header', function() {
    Mensio_Products_Brands_BackToTable();
  });
  jQuery('.TBL_DataTable_Wrapper').on('click','.BTN_BulkActions', function() {
    var $Action = jQuery('.Bulk_Selector').val();
    var $Selections = jQuery('#Brands_MultiSelectTblIDs').val();
    var $Check = false;
    switch ($Action) {
      case 'VSBL':
        $Action = 'mensio_ajax_Products_Brands_Visble';
        $Check = true;
        break;
      case 'HDN':
        $Action = 'mensio_ajax_Products_Brands_Hidden';
        $Check = true;
        break;
      case 'DEL':
        $Action = 'mensio_ajax_Delete_Products_Brands_Selections';
        $Check = true;
        break;
    }
    if ($Check) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
          'action': $Action,
          'Security': $sec,
          'Selections' : $Selections
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          Mensio_Products_Brands_BackToTable();
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
        Mensio_Products_Brands_Edit($EditOption[3]);
        break;
      case 'Delete':
        Mensio_Products_Brands_Delete($EditOption[3]);
        break;
    }
  });
  jQuery('.TBL_DataTable_Wrapper').on('change', '.Mns_Tbl_Body_Table_Fld_Check',function() {
    var $Action = '';
    var $id = jQuery(this).attr('id');
    $id = $id.replace('Brands_visible_','');
    var $Value = jQuery(this).val();
    if ($Value === '0') {
      $Value = '1';
      jQuery(this).prop('checked', true);
      $Action = 'mensio_ajax_Products_Brands_Visble';
    } else {
      $Value = '0';
      jQuery(this).prop('checked', false);
      $Action = 'mensio_ajax_Products_Brands_Hidden';
    }
    jQuery(this).val($Value);
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: {
        'action': $Action,
        'Security': $sec,
        'Selections' : $id+';'
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'TRUE') {
          Mensio_Products_Brands_BackToTable();
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
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
        jQuery('#FLD_Logo').val($Image.url);
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
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_Products_Brands_SaveData();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_Products_Brands_SaveData();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Delete', function() {
    var $Brand = jQuery('#FLD_Brand').val();
    Mensio_Products_Brands_Delete($Brand);
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Delete_Header', function() {
    var $Brand = jQuery('#FLD_Brand').val();
    Mensio_Products_Brands_Delete($Brand);
  });
  jQuery('#DIV_Edit').on('focus', '#FLD_Name', function() {
    jQuery('#NameMsg').hide();
  });
  jQuery('#DIV_Edit').on('keyup', '#FLD_Name', function() {
    var $val = jQuery(this).val();
    $val = $val.toLowerCase();
    $val = $val.replace(new RegExp(' ', 'g'),'-');
    jQuery('#FLD_Slug').val($val);
  });
  jQuery('#DIV_Edit').on('blur', '#FLD_Name', function() {
    var $Brand = jQuery('#FLD_Brand').val();
    var $Name = jQuery('#FLD_Name').val();
    if ($Name !== '') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
          'action': 'mensio_ajax_Check_If_Brand_Name_Exist',
          'Brand' : $Brand,
          'Security': $sec,
          'Name' : $Name
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR !== 'FALSE') { jQuery('#NameMsg').toggle("slide"); }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('#DIV_Edit').on('click', '.TranslButtons', function() {
    var notes = Mensio_tmce_getContent('FLD_Notes');
    var id = jQuery('.TranslSelected').attr('id');
    jQuery('#Note_'+id).val(notes);
    jQuery('.TranslButtons').removeClass('TranslSelected');
    jQuery(this).addClass('TranslSelected');
    id = jQuery(this).attr('id');
    notes = jQuery('#Note_'+id).val();
    Mensio_tmce_setContent(notes,'FLD_Notes');
  });
});