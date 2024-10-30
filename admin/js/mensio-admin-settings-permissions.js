'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_Settings_UpdateUserPermissions() {
  var $FldID = '';
  var $Val = '';
  var $ValPckg = Array();
  var $Fields = jQuery('.ChPerm');
  for (var $i=0; $i < $Fields.length; ++$i) {
    $FldID = $Fields[$i].id;
    $Val = jQuery('#'+$FldID).val();
    $ValPckg.push({ "Field": $FldID, "Value": $Val});
  }
  var $Data = JSON.stringify($ValPckg);
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    dataType: 'text',
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Update_Permission_Pages',
      'Data' : $Data
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'TRUE') { Mensio_Append_New_PopUp(data.Message); }
        else { location.reload(); }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Add_Permissions_User() {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Load_User_Permission_Modal'
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
  jQuery('#DIV_Edit').on('click', '.BTN_Save', function() {
    Mensio_Settings_UpdateUserPermissions();
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#Mensio_HeadBar').on('click', '.BTN_Save', function() {
    Mensio_Settings_UpdateUserPermissions();
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_AddPermUser', function() {
    Mensio_Add_Permissions_User();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_AddPermUser_Header', function() {
    Mensio_Add_Permissions_User();
  });
  jQuery('.Modal_Wrapper').on('click', '.UsrPmnBtn', function() {
    var $id = jQuery(this).attr('id');
    $id = $id.replace('Btn_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      dataType: 'text',
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Update_Permission_User_List',
        'User' : $id
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#'+$id).hide();
          jQuery('#PermissionsTblDiv').html(data.Message);
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
});