'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
var $NeedSave = false;
function Mensio_Orders_SubPager($SubPage) {
  switch ($SubPage) {
    case 'Edit':
      jQuery('#DIV_Table').hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Edit Mode');
      jQuery('#DIV_Edit').show(800);
      jQuery('.menu_button_row').show();
      $NeedSave = false;
      break;
    case 'Table':
      jQuery('#PayDetails').hide();
      jQuery('.menu_button_row').hide();
      jQuery('#DIV_Edit').hide(800);
      jQuery('.Mns_Page_Breadcrumb').html('Table Mode');
      jQuery('#DIV_Table').show(800);
      $NeedSave = false;
      break;
  }
}
function Mensio_Orders_BackToTable() {
  var $Page = jQuery('#Orders_PageSelector_Header').val();
  var $Rows = jQuery('#Orders_RowSelector_Header').val();
  var $Search = jQuery('#Orders_SearchFld').val();
  var $Sorter = jQuery('#Orders_SorterCol').val();
  Mensio_CallAjaxTableLoader('Orders',$Page,$Rows,$Search,$Sorter,$sec);
  Mensio_Orders_SubPager('Table');
}
function Mensio_Switch_Buttons($OnOff) {
  if ($OnOff === 'off') {
    jQuery('#BTN_EditOrderDiscounts').attr('disabled','disabled');
    jQuery('.BtnSwitchable').attr('disabled','disabled');
  } else {
    jQuery('#BTN_EditOrderDiscounts').removeAttr('disabled');
    jQuery('.BtnSwitchable').removeAttr('disabled');
  }
}
function Mensio_Edit_Order($Order) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Load_Order_Data',
      'Order': $Order
    },
    success:function(data) {
      data = jQuery.parseJSON(data);
      if (data.ERROR === 'FALSE') {
        jQuery('#FLD_EDOrder').val(data.Order);
        jQuery('#FLD_EDCustomer').val(data.Customer);
        jQuery('#FLD_EDBlAddress').val(data.BillingAddr);
        jQuery('#FLD_EDSndAddress').val(data.SendingAddr);
        jQuery('#FLD_Products').val(data.Products);
        jQuery('#FLD_ShippimgType').val(data.ShippingType);
        jQuery('#OrderSerial').html(data.Serial);
        jQuery('#OrderStatus').html(data.ActiveStatus);
        jQuery('#CustomerDataDiv').html(data.CstData);
        jQuery('#BillingAddressDiv').html(data.BAData);
        jQuery('#SendingAddressDiv').html(data.SAData);
        jQuery('#OrdersProductTable').html(data.PrdTable);
        jQuery('#OrdersProductTableFooter').html(data.Totals);
        jQuery('#OrderStatusHistory').html(data.Status);
        jQuery('.OrderAddress').slideDown();
        jQuery('#BTN_EditProducts').show();
        jQuery('#BTN_ChangeStatus').show();
        Mensio_Switch_Buttons('on');
        if (data.Complete === 'TRUE') {
          jQuery('.ESBtnsDivs').hide();
          Mensio_Switch_Buttons('off');
        }
        Mensio_Orders_SubPager('Edit');
      }
    },
    error: function(errorThrown){
      alert(errorThrown);
    }
  });
}
function Mensio_GetOrderProducts() {
  var $PrdIDs = jQuery('#FLD_Products').val();
  var $PrdData = '';
  var $ID = '';
  var $name = '';
  var $amount = '';
  var $max = '';
  var $price = '';
  var $tax = '';
  var $discount = '';
  if ($PrdIDs !== '') {
    $PrdData = Array();
    $PrdIDs = $PrdIDs.split(';');
    for (var $i=0; $i < $PrdIDs.length; ++$i) {
      $ID = $PrdIDs[$i];
      $name = jQuery('#name_'+$ID).html();
      $amount = jQuery('#amount_'+$ID).val();
      $max = jQuery('#amount_'+$ID).attr('max');
      $price = jQuery('#price_'+$ID).val();
      $price = $price.replace(/[^0-9\.]+/g,'');
      $tax = jQuery('#prdtax_'+$ID).html();
      $tax = $tax.replace(/[^0-9\.]+/g,'');
      $discount = jQuery('#discount_'+$ID).val();
      $discount = $discount.replace(/[^0-9\.]+/g,'');
      $PrdData.push({"ID":$ID,"Name":$name,"Amount":$amount,'Max':$max,"Price":$price,'Taxes':$tax,"Discount":$discount});
    }
    $PrdData = JSON.stringify($PrdData);
  }
  return $PrdData;
}
function Mensio_DisplayOrderInvoice($Order) {
  if ($NeedSave) {
    alert('Please save order first');
  } else {  
    jQuery.ajax({
      type: 'post',
      url: ajaxurl ,
      dataType: 'text',
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Order_Invoice',
        'Order': $Order
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          window.open(data.Name);
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
function Mensio_SendInvoiceMail($Order) {
  if ($NeedSave) {
    alert('Please save order first');
  } else {  
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Send_Invoice_Modal',
        'Order': $Order
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
function Mensio_SplitOrder($Order) {
  jQuery.ajax({
    type: 'post',
    url: ajaxurl,
    data: { 'Security': $sec,
      'action': 'mensio_ajax_Load_Split_Order_Form_Modal',
      'Order': $Order
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
function Mensio_Save_Order() {
  var $Err = '';
  var $check = jQuery('#CompletePrice').val();
  var $order = jQuery('#FLD_EDOrder').val();
  var $cstmr = jQuery('#FLD_EDCustomer').val();
  var $bladdr = jQuery('#FLD_EDBlAddress').val();
  var $sndaddr = jQuery('#FLD_EDSndAddress').val();
  var $OrderPrds = Mensio_GetOrderProducts();
  var $ShipType = jQuery('#FLD_ShippimgType').val();
  if (!jQuery.isNumeric($check)) { $Err = $Err+'Price Not Correct'+$check+'\r\n'; }
  if ($cstmr === '') { $Err = $Err+'Customer Not Found\r\n'; }
  if ($bladdr === '') { $Err = $Err+'Billing Address Empty\r\n'; }
  if ($sndaddr === '') { $Err = $Err+'Shipping Address Empty\r\n'; }
  if ($OrderPrds === '') { $Err = $Err+'No Products\r\n'; }
  if ($ShipType === '') { $Err = $Err+'Shippimg Type Not selected\r\n'; }
  if ($Err === '') {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Orders_Save_Main_Data',
        'Order' : $order,
        'Customer' : $cstmr,
        'BlngAddress' : $bladdr,
        'SendAddress' : $sndaddr,
        'OrderPrds' : $OrderPrds,
        'ShipType' : $ShipType
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#FLD_EDOrder').val(data.Order);
          jQuery('#OrderStatusHistory').html(data.Status);
          jQuery('#BTN_ChangeStatus').show();
          Mensio_Append_New_PopUp(data.Message);
          Mensio_Switch_Buttons('On');
          $NeedSave = false;
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  } else {
    alert('Problems with empty field selections\r\n' + $Err);
  }
}
function Mensio_Add_Order_Discounts() {
  if ($NeedSave) {
    alert('Please save order first');
  } else {
    var $order = jQuery('#FLD_EDOrder').val();
    if ($order === 'NewOrder') {
      alert('Please Save Order First');
    } else {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Orders_Load_Discounts_Modal',
          'Order' : $order
        },
        success:function(data) {
          if (data !== '') {
            jQuery('#MnsModal').html(data);
            jQuery('#MnsModal').toggle('slide');
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  }
}
function Mensio_SetOrderPayment() {
  if ($NeedSave) {
    alert('Please save order first');
  } else {
    var $order = jQuery('#FLD_EDOrder').val();
    if ($order === 'NewOrder') {
      alert('Please Save Order First');
    } else {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Orders_Load_Payments_Modal',
          'Order' : $order
        },
        success:function(data) {
          if (data !== '') {
            jQuery('#MnsModal').html(data);
            jQuery('#MnsModal').toggle('slide');
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
  jQuery('#DIV_Edit').on('click', '#BTN_Back', function() {
    Mensio_Orders_BackToTable();
    jQuery('.ESBtnsDivs').show();
    Mensio_Switch_Buttons('off');
    jQuery('.btnsv').removeAttr('disabled');
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Back_Header', function() {
    Mensio_Orders_BackToTable();
    jQuery('.ESBtnsDivs').show();
    Mensio_Switch_Buttons('off');
    jQuery('.btnsv').removeAttr('disabled');
  });
  jQuery('.TBL_DataTable_Wrapper').on('click', '.Mns_Subline_EditOption',function() {
    var $EditOption = jQuery(this).attr('id');
    $EditOption = $EditOption.split('_');
    switch ($EditOption[2]) {
      case 'Edit':
        Mensio_Edit_Order($EditOption[3]);
        break;
      case 'Invoice':
        Mensio_DisplayOrderInvoice($EditOption[3]);
        break;
    }
  });
  jQuery('#ButtonArea').on('click', '#BTN_AddNew', function() {
    jQuery('#FLD_EDOrder').val('NewOrder');
    jQuery('#FLD_EDCustomer').val('');
    jQuery('#FLD_EDBlAddress').val('');
    jQuery('#FLD_EDSndAddress').val('');
    jQuery('#FLD_Products').val('');
    jQuery('#FLD_ShippimgType').val('');
    jQuery('#OrderSerial').html('New Order');
    jQuery('#OrderStatus').html('');
    jQuery('#CustomerDataDiv').html('');
    jQuery('#BillingAddressDiv').html('');
    jQuery('#SendingAddressDiv').html('');
    jQuery('#OrdersProductTable').html('');
    jQuery('#OrdersProductTableFooter').html('');
    jQuery('#OrderStatusHistory').html('');
    jQuery('.OrderAddress').slideUp();
    jQuery('#BTN_EditProducts').hide();
    jQuery('#BTN_ChangeStatus').hide();
    Mensio_Switch_Buttons('off');
    jQuery('.btnsv').removeAttr('disabled');
    Mensio_Orders_SubPager('Edit');
  });
  jQuery('#DIV_Edit').on('click', '#BTN_EditCustomer', function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Customer_Orders_Selection_Modal'
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
  jQuery('.Modal_Wrapper').on('keyup', '#MDL_SrchCstmr', function() {
    var $SrchCstmr = jQuery('#MDL_SrchCstmr').val();
    if ($SrchCstmr !== '') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Search_Customer_For_Orders',
          'Search' : $SrchCstmr
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#MDL_CstmrLst').html(data.Results);
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
  jQuery('.Modal_Wrapper').on('click', '.CstmrSlctr', function() {
    var $id = jQuery(this).attr('id');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Customer_Data_For_Orders',
        'Customer' : $id
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#MnsModal').toggle('slide');
          jQuery('#FLD_EDCustomer').val(data.Customer);
          jQuery('#FLD_EDBlAddress').val(data.BillingAddr);
          jQuery('#FLD_EDSndAddress').val(data.SendingAddr);
          jQuery('#CustomerDataDiv').html(data.CstData);
          jQuery('#BillingAddressDiv').html(data.BAData);
          jQuery('#SendingAddressDiv').html(data.SAData);
          jQuery('.ESBtnsDivs').show();
          jQuery('.OrderAddress').slideDown();
          jQuery('#NOSAVEWARN').show();
          $NeedSave = true;
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#BTN_EditBillingAddress', function() {
    var $Cstmr = jQuery('#FLD_EDCustomer').val();
    if ($Cstmr !== '') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Load_Address_Orders_Selection_Modal',
          'Type': 'Billing',
          'Customer': $Cstmr
        },
        success:function(data) {
          if (data !== '') {
            jQuery('#MnsModal').html(data);
            jQuery('#MnsModal').toggle('slide');
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_EditShippingAddress', function() {
    var $Cstmr = jQuery('#FLD_EDCustomer').val();
    if ($Cstmr !== '') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Load_Address_Orders_Selection_Modal',
          'Type': 'Shipping',
          'Customer': $Cstmr
        },
        success:function(data) {
          if (data !== '') {
            jQuery('#MnsModal').html(data);
            jQuery('#MnsModal').toggle('slide');
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('.Modal_Wrapper').on('click', '.CstmrAddrSlctr', function() {
    var $addr = jQuery(this).attr('id');
    var $type = jQuery('#MDL_AddrType').val();
    var $cstmr = jQuery('#FLD_EDCustomer').val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Set_Customer_Order_Address',
        'Type' : $type,
        'Customer' : $cstmr,
        'Address' : $addr
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          if (data.Type === 'Billing') {
            jQuery('#FLD_EDBlAddress').val(data.BillingAddr);
            jQuery('#BillingAddressDiv').html(data.BAData);
          }
          if (data.Type === 'Shipping') {
            jQuery('#FLD_EDSndAddress').val(data.SendingAddr);
            jQuery('#SendingAddressDiv').html(data.SAData);
          }
          jQuery('#MnsModal').toggle('slide');
          jQuery('#NOSAVEWARN').show();
          $NeedSave = true;
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#BTN_EditProducts', function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Product_Orders_Selection_Modal'
      },
      success:function(data) {
        if (data !== '') {
          jQuery('#MnsModal').html(data);
          jQuery('#MnsModal').toggle('slide');
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('keyup', '#MDL_SrchPrd', function() {
    var $SrchPrd = jQuery('#MDL_SrchPrd').val();
    if ($SrchPrd !== '') {
      var $OrderPrds = jQuery('#FLD_Products').val();
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Search_Product_For_Orders',
          'OrderPrds' : $OrderPrds,
          'Search' : $SrchPrd
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#MDL_ProdLst').html(data.Results);
          } else {
            Mensio_Append_New_PopUp(data.Message);
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    } else {
      jQuery('#MDL_ProdLst').html('');
    }
  });
  jQuery('.Modal_Wrapper').on('click', '.MDL_BTN_AddProduct', function() {
    var $Order = jQuery('#FLD_EDOrder').val();
    var $OrderPrds = Mensio_GetOrderProducts();
    var $Prd = jQuery(this).attr('id');
    var $Amnt = jQuery('#MDL_PrdAmount_'+$Prd).val();
    var $Shpng = 0;
    if (jQuery('#Shipping').length) {
      $Shpng = jQuery('#Shipping').html();
      $Shpng = $Shpng.replace(/[^0-9\.]+/g,'');
    }
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Add_Product_To_Order',
        'Order' : $Order,
        'OrderPrds' : $OrderPrds,
        'NewProd' : $Prd,
        'Amount' : $Amnt,
        'Shipping' : $Shpng
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#FLD_Products').val(data.Products);
          jQuery('#OrdersProductTable').html(data.PrdTable);
          jQuery('#OrdersProductTableFooter').html(data.Totals);
          jQuery('#Product_'+$Prd).toggle('slide');
          jQuery('#NOSAVEWARN').show();
          $NeedSave = true;
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '.ViewProductInfo', function() {
    var $id = jQuery(this).attr('id');
    $id = $id.replace('PrdVW_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Orders_View_Product_Modal',
        'Product': $id
      },
      success:function(data) {
        if (data !== '') {
          jQuery('#MnsModal').html(data);
          jQuery('#MnsModal').toggle('slide');
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '.RemoveProduct', function() {
    var $Order = jQuery('#FLD_EDOrder').val();
    var $id = jQuery(this).attr('id');
    $id = $id.replace('PrdDL_','');
    var $OrderPrds = Mensio_GetOrderProducts();
    var $Shpng = 0;
    if (jQuery('#Shipping').length) {
      $Shpng = jQuery('#Shipping').html();
      $Shpng = $Shpng.replace(/[^0-9\.]+/g,'');
    }
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Orders_Remove_Product_From_Order',
        'Order': $Order,
        'OrderPrds': $OrderPrds,
        'Product': $id,
        'Shipping': $Shpng
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#FLD_Products').val(data.Products);
          jQuery('#OrdersProductTable').html(data.PrdTable);
          jQuery('#OrdersProductTableFooter').html(data.Totals);
          jQuery('#NOSAVEWARN').show();
          $NeedSave = true;
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('blur', '.TblInput', function() {
    var $Order = jQuery('#FLD_EDOrder').val();
    var $OrderPrds = Mensio_GetOrderProducts();
    var $Shpng = 0;
    if (jQuery('#Shipping').length) {
      $Shpng = jQuery('#Shipping').html();
      $Shpng = $Shpng.replace(/[^0-9\.]+/g,'');
    }
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Refresh_Product_Table_Data',
        'Order': $Order,
        'OrderPrds' : $OrderPrds,
        'Shipping' : $Shpng
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#FLD_Products').val(data.Products);
          jQuery('#OrdersProductTable').html(data.PrdTable);
          jQuery('#OrdersProductTableFooter').html(data.Totals);
          jQuery('#NOSAVEWARN').show();
          $NeedSave = true;
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#BTN_EditShippingType', function() {
    var $SType = jQuery('#FLD_ShippimgType').val();
    var $SAddr = jQuery('#FLD_EDSndAddress').val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Orders_ShippingType_Modal',
        'ShipType': $SType,
        'ShipAddress': $SAddr
      },
      success:function(data) {
        if (data !== '') {
          jQuery('#MnsModal').html(data);
          jQuery('#MnsModal').toggle('slide');
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('click', '#MDL_BTN_SetShipType', function() {
    var $Order = jQuery('#FLD_EDOrder').val();
    var $ShipType = jQuery('#MDL_FLD_ShippingType').val();
    var $ShipPrice = jQuery('#MDL_FLD_ShippingType option:selected').text();
    $ShipPrice = $ShipPrice.split(':');
    $ShipPrice = $ShipPrice[2].replace(/[^0-9\.]+/g,'');
    jQuery('#FLD_ShippimgType').val($ShipType);
    jQuery('#Shipping').html($ShipPrice);
    jQuery('#MnsModal').toggle('slide');
    var $OrderPrds = Mensio_GetOrderProducts();
    var $Shpng = 0;
    if (jQuery('#Shipping').length) {
      $Shpng = jQuery('#Shipping').html();
      $Shpng = $Shpng.replace(/[^0-9\.]+/g,'');
    }
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Refresh_Product_Table_Data',
        'Order': $Order,
        'OrderPrds' : $OrderPrds,
        'Shipping' : $Shpng
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#FLD_Products').val(data.Products);
          jQuery('#OrdersProductTable').html(data.PrdTable);
          jQuery('#OrdersProductTableFooter').html(data.Totals);
          jQuery('#NOSAVEWARN').show();
          $NeedSave = true;
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#BTN_ChangeStatus', function() {
    var $Order = jQuery('#FLD_EDOrder').val();
    if ($Order === 'NewOrder') {
      alert('Save Order First');
    } else {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Load_Orders_Status_Modal',
          'Order': $Order
        },
        success:function(data) {
          if (data !== '') {
            jQuery('#MnsModal').html(data);
            jQuery('#MnsModal').toggle('slide');
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('.Modal_Wrapper').on('click', '.MDLStatusElement', function() {
    var $Status = jQuery(this).attr('id');
    var $Order = jQuery('#FLD_EDOrder').val();
    if ($Order !== 'NewOrder') {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Add_Order_Status',
          'Order' : $Order,
          'Status' : $Status
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#'+$Status).toggle('slide');
            jQuery('#OrderStatusHistory').html(data.Status);
            jQuery('#OrderStatus').html(data.Name);
            if (data.Complete === 'TRUE') {
              jQuery('.ESBtnsDivs').hide();
              Mensio_Switch_Buttons('off');
              jQuery('#MnsModal').toggle('slide');
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
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    Mensio_Save_Order();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    Mensio_Save_Order();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_SetPayment', function() {
    var $id = jQuery('#FLD_EDOrder').val();
    Mensio_SetOrderPayment();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_SetPayment_Header', function() {
    var $id = jQuery('#FLD_EDOrder').val();
    Mensio_SetOrderPayment();
  });
  jQuery('#DIV_Edit').on('click', '#BTN_Invoice', function() {
    var $id = jQuery('#FLD_EDOrder').val();
    Mensio_DisplayOrderInvoice($id);
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Invoice_Header', function() {
    var $id = jQuery('#FLD_EDOrder').val();
    Mensio_DisplayOrderInvoice($id);
  });
  jQuery('#DIV_Edit').on('click', '#BTN_SendMail', function() {
    var $id = jQuery('#FLD_EDOrder').val();
    Mensio_SendInvoiceMail($id);
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_SendMail_Header', function() {
    var $id = jQuery('#FLD_EDOrder').val();
    Mensio_SendInvoiceMail($id);
  });
  jQuery('#DIV_Edit').on('click', '#BTN_SplitOrder', function() {
    var $id = jQuery('#FLD_EDOrder').val();
    Mensio_SplitOrder($id);
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_SplitOrder_Header', function() {
    var $id = jQuery('#FLD_EDOrder').val();
    Mensio_SplitOrder($id);
  });
  jQuery('#DIV_Edit').on('click', '#BTN_EditOrderDiscounts', function() {
    Mensio_Add_Order_Discounts();
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_EditOrderDiscounts_Header', function() {
    Mensio_Add_Order_Discounts();
  });
  jQuery('#DIV_Edit').on('click', '#PayInfoIcon', function() {
    if (jQuery('#PayDetails:visible').length === 0) {
      var $order = jQuery('#FLD_EDOrder').val();
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Orders_Load_Payment_Data',
          'Order' : $order
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'FALSE') {
            jQuery('#PayDetails').html(data.Payment);
            jQuery('#PayDetails').fadeIn(800);
          } else {
            Mensio_Append_New_PopUp(data.Message);
            jQuery('#MnsModal').toggle('slide');
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    } else {
      jQuery('#PayDetails').fadeOut(800, function() {
        jQuery('#PayDetails').html('');
      });
    }
  });
  jQuery('#DIV_Edit').on('click', '#BTN_UpdatePayment', function() {
    var $order = jQuery('#FLD_EDOrder').val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Orders_Update_Payment_Status',
        'Order' : $order
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#PayDetails').html(data.Payment);
          Mensio_Edit_Order($order);
        } else {
          Mensio_Append_New_PopUp(data.Message);
          jQuery('#MnsModal').toggle('slide');
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('click', '#MDL_BTN_Cancel', function() {
    jQuery('#MnsModal').toggle('slide', function() {
      jQuery('#MnsModal').html('');
    });
  });
  jQuery('.Modal_Wrapper').on('click', '#MDL_BTN_Split', function() {
    jQuery('#MDL_Split_Info').toggle('slide', function() {
      jQuery('#MDL_Split_Form').toggle('slide');
    });
  });
  jQuery('.Modal_Wrapper').on('click', '.MDL_BTN_SetActive', function() {
    jQuery('.MDL_BTN_SetActive').html('<i class="fa fa-square-o fa-lg" aria-hidden="true"></i>');
    jQuery(this).html('<i class="fa fa-check-square-o fa-lg" aria-hidden="true"></i>');
    var $id = jQuery(this).attr('id');
    $id = $id.replace('_Active','');
    jQuery('.SubOrderList').removeClass('SOActive');
    jQuery('#'+$id).addClass('SOActive');
    jQuery('#FLD_ActiveSubOrder').val($id);
  });
  jQuery('.Modal_Wrapper').on('click', '.MDL_BTN_Clear', function() {
    var $id = jQuery(this).attr('id');
    $id = $id.replace('_Clear','');
    jQuery('#FLD_'+$id).val('');
    var $prds = jQuery('#'+$id).html();
    jQuery('#MDL_Split_Prime').append($prds);
    jQuery('#'+$id).html('');
  });
  jQuery('.Modal_Wrapper').on('click', '.MdlSplitPrimePrd', function() {
    var $Active = jQuery('#FLD_ActiveSubOrder').val();
    var $id = jQuery(this).attr('id');
    var $prddiv = jQuery(this);
    jQuery('#'+$Active).append($prddiv);
    var $prd = jQuery('#FLD_'+$Active).val();
    if ($prd === '') { $prd = $id; }
      else { $prd = $prd+';'+$id; }
    jQuery('#FLD_'+$Active).val($prd);
  });
  jQuery('.Modal_Wrapper').on('click', '#MDL_BTN_SaveSplitOrders', function() {
    var $prd1 = jQuery('#FLD_SubOrder1').val();
    var $prd2 = jQuery('#FLD_SubOrder2').val();
    if (($prd1 !== '') && ($prd2 !== '')) {
      var $order = jQuery('#FLD_PrimeOrder').val();
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Orders_Save_Split_Orders_Data',
          'Order' : $order,
          'SubOrder1' : $prd1,
          'SubOrder2' : $prd2
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          Mensio_Append_New_PopUp(data.Message);
          jQuery('#MnsModal').toggle('slide');
          Mensio_Orders_BackToTable();
          jQuery('.ESBtnsDivs').show();
          Mensio_Switch_Buttons('on');
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    } else {
      alert('One or more orders seems empty !?!');
    }
  });
  jQuery('.Modal_Wrapper').on('click', '.MDL_DiscCheck', function() {
    var $order = jQuery('#FLD_EDOrder').val();
    var $id = jQuery(this).attr('id');
    var $val = jQuery(this).val();
    if ($val === '0') { $val = '1'; }
      else { $val = '0'; }
    jQuery(this).val($val);
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Orders_Save_Orders_Discounts',
        'Order' : $order,
        'Discount' : $id,
        'Active' : $val
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') {
          jQuery('#FLD_Products').val(data.Products);
          jQuery('#OrdersProductTable').html(data.PrdTable);
          jQuery('#OrdersProductTableFooter').html(data.Totals);
        } else {
          Mensio_Append_New_PopUp(data.Message);
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#Btn_MlStsInfo', function() {
    var $order = jQuery('#FLD_EDOrder').val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Orders_Send_Status_Info_Mail',
        'Order' : $order
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'FALSE') { jQuery('#Btn_MlStsInfo').hide(); }
        Mensio_Append_New_PopUp(data.Message);
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('.Modal_Wrapper').on('click', '#MDL_BTN_SetPaymentType', function() {
    var $order = jQuery('#FLD_EDOrder').val();
    var $payment = jQuery('#MDL_PaymentSelect').val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Orders_Update_Order_Payment_Type',
        'Order' : $order,
        'Payment' : $payment
      },
      success:function(data) {
        jQuery('#MnsModal').toggle('slide');
        data = jQuery.parseJSON(data);
        Mensio_Append_New_PopUp(data.Message);
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
});