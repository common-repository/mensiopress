'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_RegionssSubPager($SubPage) {
  switch ($SubPage) {
    case 'Edit':
      jQuery("#DIV_Table").hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Edit Mode');
      jQuery("#DIV_Edit").show(800);
      break;
    case 'Table':
      jQuery("#DIV_Edit").hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Table Mode');
      jQuery("#DIV_Table").show(800);
  }
}
function Mensio_EditRegionType(RegType) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Edit_Region_Type_Data',
      'Type': RegType
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#Type').val(data.Type);
        jQuery('#FLD_TypeName').val(data.TypeName);
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });  
}
function Mensio_DeleteRegionType(RegType) {
  var answer = confirm('Are you sure you want to DELETE the entry?');
  if (answer === true) {
    var Country = jQuery('#Country').val();
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Delete_Region_Type_Data',
          'Country': Country,
          'Type' : RegType
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            var TypeOptions = data.TypeOptions.replace(/\\/g, '');
            jQuery('#FLD_RegionType').html(TypeOptions);
            var TypeBtns = data.TypeBtns.replace(/\\/g, '');
            jQuery('#RegionTypeList').html(TypeBtns);
            jQuery('#Type').val('NewRegionType');
            jQuery('#FLD_TypeName').val('');
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
  jQuery('#DIV_Edit').on('click', '#BTN_Back', function() {
    Mensio_RegionssSubPager('Table');
  });
  jQuery('.TBL_DataTable_Wrapper').on('click', '.Mns_Subline_EditOption',function() {
    var $EditOption = jQuery(this).attr('id');
    $EditOption = $EditOption.split('_');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Country_Regions',
        'Country' : $EditOption[3]
      },
      success:function(data) {
        jQuery('#Country').val($EditOption[3]);
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          var Regions = data.Regions.replace(/\\/g, '');
          jQuery('#RegionList').html(Regions);
          var TypeOptions = data.TypeOptions.replace(/\\/g, '');
          jQuery('#FLD_RegionType').html(TypeOptions);
          var TypeBtns = data.TypeBtns.replace(/\\/g, '');
          jQuery('#RegionTypeList').html(TypeBtns);
          jQuery('#Region').val('NewRegion');
          jQuery('#Type').val('NewRegionType');
          Mensio_RegionssSubPager('Edit');
          jQuery('#RegionEdit').accordion();
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('change', '#FLD_RegionType',function() {
    var Country = jQuery('#Country').val();
    var RegType = jQuery(this).val();
    if (RegType !== '') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Load_Regions_Parent_Options',
          'Country': Country,
          'Type': RegType
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            var ParentOptions = data.ParentOptions.replace(/\\/g, '');
            jQuery('#FLD_RegionParent').html(ParentOptions);
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
  jQuery('#DIV_Edit').on('click', '#BTN_AddRegion',function() {
    jQuery('#Region').val('NewRegion');
    jQuery('#FLD_RegionName').val('');
  });
  jQuery('#DIV_Edit').on('click', '#BTN_AddType',function() {
    jQuery('#Type').val('NewRegionType');
    jQuery('#FLD_TypeName').val('');
  });
  jQuery('#DIV_Edit').on('click', '.RegionsDspl',function() {
    jQuery('.RegionsDspl').removeClass('SelectedRegion');
    jQuery(this).addClass('SelectedRegion');
    var id = jQuery(this).attr('id');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Edit_Regions_Data',
        'Region': id
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#FLD_RegionParent').html(data.ParentOptions);
          jQuery('#Region').val(data.Region);
          jQuery('#FLD_RegionType').val(data.Type);
          jQuery('#FLD_RegionName').val(data.Name);
          jQuery('#FLD_RegionParent').val(data.Parent);
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '.RgTpBtns',function() {
    var id = jQuery(this).attr('id');
    id = id.split('_');
    switch (id[0]) {
      case 'TPEdit':
        Mensio_EditRegionType(id[1]);
        break;
      case 'TPDel':
        Mensio_DeleteRegionType(id[1]);
        break;
    }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_TypeSave',function() {
    var Country = jQuery('#Country').val();
    var Type = jQuery('#Type').val();
    var TypeName = jQuery('#FLD_TypeName').val();
    if ((TypeName !== '') && (TypeName !== ' ')) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Update_Region_Type',
          'Country': Country,
          'Type': Type,
          'TypeName': TypeName
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            var TypeOptions = data.TypeOptions.replace(/\\/g, '');
            jQuery('#FLD_RegionType').html(TypeOptions);
            var TypeBtns = data.TypeBtns.replace(/\\/g, '');
            jQuery('#RegionTypeList').html(TypeBtns);
            jQuery('#Type').val('NewRegionType');
            jQuery('#FLD_TypeName').val('');
          }
          Mensio_Append_New_PopUp(data.Message);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_RegionSave',function() {
    var Country = jQuery('#Country').val();
    var Region = jQuery('#Region').val();
    var RegionType = jQuery('#FLD_RegionType').val();
    var Name = jQuery('#FLD_RegionName').val();
    var Parent = jQuery('#FLD_RegionParent').val();
    if ((Name !== '') && (Name !== ' ')) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Update_Region_Data',
          'Country': Country,
          'Region': Region,
          'Type': RegionType,
          'Name': Name,
          'Parent': Parent
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#Region').val('NewRegion');
            jQuery('#FLD_RegionName').val('');
            var Regions = data.Regions.replace(/\\/g, '');
            jQuery('#RegionList').html(Regions);
            jQuery('#NOSAVEWARN').hide();
          }
          Mensio_Append_New_PopUp(data.Message);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_RegionTrans',function() {
    var Region = jQuery('#Region').val();
    if (Region !== 'NewRegion') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action' : 'mensio_ajax_Modal_Region_Translations',
          'Region' : Region
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
      alert('Please select a region first');
    }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_RegionDel', function() {
    var $Country = jQuery('#Country').val();
    var $Region = jQuery('#Region').val();
    if ( $Region !== 'NewRegion') {
      var answer = confirm('Are you sure you want to DELETE the region?');
      if (answer === true) {
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: { 'Security': $sec,
              'action': 'mensio_ajax_Delete_Region_Data',
              'Country' : $Country,
              'Region' : $Region
            },
            success:function(data) {
              data = jQuery.parseJSON(data);
              if (data.ERROR === 'FALSE') {
                jQuery('#Region').val('NewRegion');
                jQuery('#FLD_RegionName').val('');
                var Regions = data.Regions.replace(/\\/g, '');
                jQuery('#RegionList').html(Regions);
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
  jQuery('.Modal_Wrapper').on('keyup', '.MainFLD', function() {
    var region = jQuery('#Region').val();
    var val = jQuery(this).val();
    jQuery('#FLD_RegionName').val(val);
    jQuery('#'+region).find('p').html(val);
  });
  jQuery('.Modal_Wrapper').on('click', '#BTN_TransSave', function() {
    var $Region = jQuery('#Region').val();
    var $Err = false;
    var $FldID = '';
    var $val = '';
    var $ValPckg = Array();
    var $TrFlds = jQuery('.Trn-Fields');
    for (var $i=0; $i < $TrFlds.length; ++$i) {
      $FldID = $TrFlds[$i].id;
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
            'action': 'mensio_ajax_Update_Region_Translations',
            'Region' : $Region,
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
  });
});
