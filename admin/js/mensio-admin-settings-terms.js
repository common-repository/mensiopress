'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_Settings_UpdateStoreTermsOfUse() {
  var $Store = jQuery('#FLD_Store').val();
  var $Term = jQuery('#FLD_TermsCode').val();
  var $TermsOfUse = Mensio_tmce_getContent('FLD_TermsOfUse');
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Update_Store_Use_Terms',
      'Store': $Store,
      'Term': $Term,
      'TermsOfUse' : $TermsOfUse
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#FLD_TermsCode').val(data.Term);
        jQuery('#PublishedWrap').html(data.Date);
        jQuery('#TermsListWraper').html(data.List);
      }
      Mensio_Append_New_PopUp(data.Message);
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_NewTermOfUse() {
  jQuery('#FLD_TermsCode').val('NewTerm');
  Mensio_tmce_setContent('','FLD_TermsOfUse');
}
function Mensio_View_Older_Terms($Term) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_Show_Terms_View_Modal',
      'Term' : $Term
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
function Mensio_Edit_Terms($Term) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_Edit_Terms_Of_Use',
      'Term' : $Term
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#FLD_TermsCode').val(data.Term);
        jQuery('#PublishedWrap').html(data.Date);
        Mensio_tmce_setContent(data.Notes,'FLD_TermsOfUse');
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Remove_Terms() {
  var $Term = jQuery('#FLD_TermsCode').val();
  if ($Term !== 'NewTerm') {
    var answer = confirm('Are you sure you want to DELETE the Terms of Use ?');
    if (answer === true) {
      var $Store = jQuery('#FLD_Store').val();
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_Remove_Terms_Of_Use',
          'Store': $Store,
          'Term' : $Term
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#TermsListWraper').html(data.List);
          }
          Mensio_Append_New_PopUp(data.Message);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  }
}
function Mensio_Publish_Store_Terms() {
  var $Store = jQuery('#FLD_Store').val();
  var $Term = jQuery('#FLD_TermsCode').val();
  var $TermsOfUse = Mensio_tmce_getContent('FLD_TermsOfUse');
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    dataType: 'text',
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Update_Publish_Terms',
      'Store': $Store,
      'Term': $Term,
      'TermsOfUse' : $TermsOfUse
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#PublishedWrap').html(data.Date);
        jQuery('#TermsListWraper').html(data.List);
        Mensio_NewTermOfUse();
      }
      Mensio_Append_New_PopUp(data.Message);
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
jQuery(document).ready(function() {
  jQuery('#DIV_Edit').on('click', '.BTN_Save', function() {
    Mensio_Settings_UpdateStoreTermsOfUse();
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#Mensio_HeadBar').on('click', '.BTN_Save', function() {
    Mensio_Settings_UpdateStoreTermsOfUse();
    jQuery('#NOSAVEWARN').hide();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Publish', function() {
    Mensio_Publish_Store_Terms();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Publish_Header', function() {
    Mensio_Publish_Store_Terms();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_NewTerms', function() {
    Mensio_NewTermOfUse();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_NewTerms_Header', function() {
    Mensio_NewTermOfUse();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_DelTerm', function() {
    Mensio_Remove_Terms();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_DelTerm_Header', function() {
    Mensio_Remove_Terms();
  });
  jQuery('#DIV_Edit').on('click', '.TermSelector', function() {
    Mensio_NewTermOfUse();
    jQuery('.TermSelector').removeClass('SelectedTerm');
    jQuery(this).addClass('SelectedTerm');
    var $id = jQuery(this).attr('id');
    $id = $id.split('_');
    var $test = '';
    switch ($id[0]) {
      case 'VW':
        Mensio_View_Older_Terms($id[1]);
        break;
      case 'EDT':
        Mensio_Edit_Terms($id[1]);
        break;
    }
  });
});