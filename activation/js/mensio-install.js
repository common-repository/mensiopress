'use strict';
(function( $ ) {
function Mensio_Install_Settings($DataString) {
  jQuery('#MessagesDisplay').html('<p>Installing System Tables ... <i class="fa fa-cog fa-spin fa-fw"></i></p>');
  $.ajax({
    type: 'POST',
    url: ajaxurl,
    data: {
      'action':'mensio_install_Settings',
      'DataString' : $DataString
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        $('#myBar').width('12.5%');
        Mensio_Install_Settings_Values();
      } else {
        $("#MsgWrap").html(data.Message);
        $('#DisplayDiv').hide(function() {
          $('#FormDiv').show();
        });
      }
    },
    error: function(errorThrown){
      $("#MsgWrap").html(errorThrown);
    }
  });
}
function Mensio_Install_Settings_Values() {
  var $msg = jQuery('#MessagesDisplay').html();
  $msg = $msg.replace(
    ' <i class="fa fa-cog fa-spin fa-fw"></i></p>',
    'OK</p><p>Installing System Data ... <i class="fa fa-cog fa-spin fa-fw"></i></p>'    
  );
  jQuery('#MessagesDisplay').html($msg);
  $.ajax({
    type: 'POST',
    url: ajaxurl,
    data: {
      'action':'mensio_install_settings_values'
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        $('#myBar').width('25%');
        Mensio_Install_Customer();
      } else {
        $("#MsgWrap").html(data.Message);
        $('#DisplayDiv').hide(function() {
          $('#FormDiv').show();
        });
      }
    },
    error: function(errorThrown){
      $("#MsgWrap").html(errorThrown);
    }
  });
}
function Mensio_Install_Customer() {
  var $msg = jQuery('#MessagesDisplay').html();
  $msg = $msg.replace(
    ' <i class="fa fa-cog fa-spin fa-fw"></i></p>',
    'OK</p><p>Installing Customer Tables ... <i class="fa fa-cog fa-spin fa-fw"></i></p>'    
  );
  jQuery('#MessagesDisplay').html($msg);
  $.ajax({
    type: 'POST',
    url: ajaxurl,
    data: {
      'action':'mensio_install_Customer'
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        $('#myBar').width('37.5%');
        Mensio_Install_Customer_Values();
      } else {
        $("#MsgWrap").html(data.Message);
        $('#DisplayDiv').hide(function() {
          $('#FormDiv').show();
        });
      }
    },
    error: function(errorThrown){
      $("#MsgWrap").html(errorThrown);
    }
  });
}
function Mensio_Install_Customer_Values() {
  var $msg = jQuery('#MessagesDisplay').html();
  $msg = $msg.replace(
    ' <i class="fa fa-cog fa-spin fa-fw"></i></p>',
    'OK</p><p>Installing Customer Data ... <i class="fa fa-cog fa-spin fa-fw"></i></p>'    
  );
  jQuery('#MessagesDisplay').html($msg);
  $.ajax({
    type: 'POST',
    url: ajaxurl,
    data: {
      'action':'mensio_install_customer_values'
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        $('#myBar').width('50%');
        Mensio_Install_Product();
      } else {
        $("#MsgWrap").html(data.Message);
        $('#DisplayDiv').hide(function() {
          $('#FormDiv').show();
        });
      }
    },
    error: function(errorThrown){
      $("#MsgWrap").html(errorThrown);
    }
  });
}
function Mensio_Install_Product() {
  var $msg = jQuery('#MessagesDisplay').html();
  $msg = $msg.replace(
    ' <i class="fa fa-cog fa-spin fa-fw"></i></p>',
    'OK</p><p>Installing Product Tables ... <i class="fa fa-cog fa-spin fa-fw"></i></p>'    
  );
  jQuery('#MessagesDisplay').html($msg);
  $.ajax({
    type: 'POST',
    url: ajaxurl,
    data: {
      'action':'mensio_install_product'
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        $('#myBar').width('62.5%');
        Mensio_Install_Product_Values();
      } else {
        $("#MsgWrap").html(data.Message);
        $('#DisplayDiv').hide(function() {
          $('#FormDiv').show();
        });
      }
    },
    error: function(errorThrown){
      $("#MsgWrap").html(errorThrown);
    }
  });
}
function Mensio_Install_Product_Values() {
  var $msg = jQuery('#MessagesDisplay').html();
  $msg = $msg.replace(
    ' <i class="fa fa-cog fa-spin fa-fw"></i></p>',
    'OK</p><p>Installing Product Data ... <i class="fa fa-cog fa-spin fa-fw"></i></p>'    
  );
  jQuery('#MessagesDisplay').html($msg);
  $.ajax({
    type: 'POST',
    url: ajaxurl,
    data: {
      'action':'mensio_install_product_values'
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        $('#myBar').width('75%');
        Mensio_Install_Sales();
      } else {
        $("#MsgWrap").html(data.Message);
        $('#DisplayDiv').hide(function() {
          $('#FormDiv').show();
        });
      }
    },
    error: function(errorThrown){
      $("#MsgWrap").html(errorThrown);
    }
  });
}
function Mensio_Install_Sales() {
  var $msg = jQuery('#MessagesDisplay').html();
  $msg = $msg.replace(
    ' <i class="fa fa-cog fa-spin fa-fw"></i></p>',
    'OK</p><p>Installing Sales Tables ... <i class="fa fa-cog fa-spin fa-fw"></i></p>'    
  );
  jQuery('#MessagesDisplay').html($msg);
  $.ajax({
    type: 'POST',
    url: ajaxurl,
    data: {
      'action':'mensio_install_sales'
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        $('#myBar').width('85%');
        Mensio_Install_Sales_Values();
      } else {
        $("#MsgWrap").html(data.Message);
        $('#DisplayDiv').hide(function() {
          $('#FormDiv').show();
        });
      }
    },
    error: function(errorThrown){
      $("#MsgWrap").html(errorThrown);
    }
  });
}
function Mensio_Install_Sales_Values() {
  var $msg = jQuery('#MessagesDisplay').html();
  $msg = $msg.replace(
    ' <i class="fa fa-cog fa-spin fa-fw"></i></p>',
    'OK</p><p>Installing Sales Data ... <i class="fa fa-cog fa-spin fa-fw"></i></p>'
  );
  jQuery('#MessagesDisplay').html($msg);
  $.ajax({
    type: 'POST',
    url: ajaxurl,
    data: {
      'action':'mensio_install_sales_values'
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        $('#myBar').width('100%');
        var $msg = jQuery('#MessagesDisplay').html();
        $msg = $msg.replace(' <i class="fa fa-cog fa-spin fa-fw"></i></p>','OK</p>');
        jQuery('#MessagesDisplay').html($msg);
        $('#RefBtnDiv').show();
      } else {
        $("#MsgWrap").html(data.Message);
        $('#DisplayDiv').hide(function() {
          $('#FormDiv').show();
        });
      }
    },
    error: function(errorThrown){
      $("#MsgWrap").html(errorThrown);
    }
  });
}
$(document).ready(function() {
  $('.InputFld').attr('disabled', 'disabled');
	$('#BtnSetDB').on('click', function(){
    $("#MsgWrap").html('');
		$('#FormDiv').hide(function() {
      $('#DisplayDiv').show();
      var $DataString = {
        dbname: $('#dbname').val(),
        username: $('#username').val(),
        password: $('#password').val(),
        host: $('#host').val(),
        prefix: $('#prefix').val()
      };
      $DataString = JSON.stringify($DataString);
      Mensio_Install_Settings($DataString);
    });
	});
});
$(document).ready(function() {
	$('#BtnReload').on('click', function(e){
		e.preventDefault(); // prevents the default actions if any
    $.ajax({
      type: 'POST',
      url: ajaxurl,
      data: {
        'action':'mensio_install_lock_complete'
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          window.location.href = 'admin.php?page=Mensio_Admin_Main_DashBoard';
        } else {
          $("#MsgWrap").html(data.Message);
        }
      },
      error: function(errorThrown){
        $("#MsgWrap").html(errorThrown);
      }
    });    
	});
});
})( jQuery );