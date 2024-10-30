'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_Tickets_SubPager($SubPage) {
  switch ($SubPage) {
    case 'Edit':
      jQuery('#DIV_Table').hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Edit Mode');
      jQuery('#DIV_Edit').show(800);
      jQuery('.menu_button_row').show();
      break;
    case 'Table':
      jQuery('.menu_button_row').hide();
      jQuery('#DIV_Edit').hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Table Mode');
      jQuery('#DIV_Table').show(800);
      break;
  }
}
function Mensio_Tickets_BackToTable() {
  var $Page = jQuery('#Tickets_PageSelector_Header').val();
  var $Rows = jQuery('#Tickets_RowSelector_Header').val();
  var $Search = jQuery('#Tickets_SearchFld').val();
  var $Sorter = jQuery('#Tickets_SorterCol').val();
  Mensio_CallAjaxTableLoader('Tickets',$Page,$Rows,$Search,$Sorter,$sec);
  Mensio_Tickets_SubPager('Table');
}
function Mensio_Edit_Ticket($Ticket) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Load_Ticket_Data',
      'Ticket': $Ticket
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#TicketHistoryWrap').html(data.TicketForm);
        Mensio_Tickets_SubPager('Edit');
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_Load_Reply_Ticket_Form() {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Load_Ticket_Reply_Form'
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
function Mensio_Close_Ticket() {
  var $Ticket = jQuery('#FLD_Ticket').val();
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Close_Ticket_Data',
      'Ticket': $Ticket
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        Mensio_Tickets_BackToTable();
        jQuery('#TicketHistoryWrap').html('');
      } else {
        Mensio_Append_New_PopUp(data.Message);
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
jQuery(document).ready(function() {
  jQuery('#DIV_Edit').on('click', '#BTN_Back', function() {
    Mensio_Tickets_BackToTable();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Back_Header', function() {
    Mensio_Tickets_BackToTable();
  });
  jQuery('.TBL_DataTable_Wrapper').on('click', '.Mns_Subline_EditOption',function() {
    var $EditOption = jQuery(this).attr('id');
    $EditOption = $EditOption.split('_');
    switch ($EditOption[2]) {
      case 'Edit':
        Mensio_Edit_Ticket($EditOption[3]);
        break;
    }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_ShowRelOrder', function() {
    var $OrderID = jQuery('#RelOrder').val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Ticket_Order_View',
        'OrderID' : $OrderID
      },
      success:function(data) {
        jQuery('#MnsModal').html(data);
        jQuery('#MnsModal').toggle('slide');
        jQuery('#tabs').tabs(); // Creating tabs
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Reply', function() {
    Mensio_Load_Reply_Ticket_Form();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Reply_Header', function() {
    Mensio_Load_Reply_Ticket_Form();
  });
  jQuery('#MnsModal').on('click', '#BTN_Save', function() {
    var $Ticket = jQuery('#FLD_Ticket').val();
    var $Customer = jQuery('#FLD_Customer').val();
    var $Reply = jQuery('#MDL_ReplyText').val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Send_Ticket_Reply',
        'Ticket': $Ticket,
        'Customer': $Customer,
        'Reply': $Reply
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#TicketHistoryData').html(data.TicketHistory);
          jQuery('#MnsModal').toggle('slide');
        }
        Mensio_Append_New_PopUp(data.Message);
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Close', function() {
    Mensio_Close_Ticket();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Close_Header', function() {
    Mensio_Close_Ticket();
  });
});
