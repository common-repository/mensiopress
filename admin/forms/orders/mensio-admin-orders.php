<?php
add_action('wp_ajax_mensio_ajax_Table_Orders', 'mensio_ajax_Table_Orders');
add_action('wp_ajax_mensio_ajax_Load_Order_Data', 'mensio_ajax_Load_Order_Data');
add_action('wp_ajax_mensio_ajax_Load_Customer_Orders_Selection_Modal','mensio_ajax_Load_Customer_Orders_Selection_Modal');
add_action('wp_ajax_mensio_ajax_Search_Customer_For_Orders','mensio_ajax_Search_Customer_For_Orders');
add_action('wp_ajax_mensio_ajax_Load_Customer_Data_For_Orders','mensio_ajax_Load_Customer_Data_For_Orders');
add_action('wp_ajax_mensio_ajax_Load_Address_Orders_Selection_Modal','mensio_ajax_Load_Address_Orders_Selection_Modal');
add_action('wp_ajax_mensio_ajax_Set_Customer_Order_Address','mensio_ajax_Set_Customer_Order_Address');
add_action('wp_ajax_mensio_ajax_Load_Product_Orders_Selection_Modal','mensio_ajax_Load_Product_Orders_Selection_Modal');
add_action('wp_ajax_mensio_ajax_Search_Product_For_Orders','mensio_ajax_Search_Product_For_Orders');
add_action('wp_ajax_mensio_ajax_Add_Product_To_Order','mensio_ajax_Add_Product_To_Order');
add_action('wp_ajax_mensio_ajax_Load_Orders_View_Product_Modal','mensio_ajax_Load_Orders_View_Product_Modal');
add_action('wp_ajax_mensio_ajax_Orders_Remove_Product_From_Order','mensio_ajax_Orders_Remove_Product_From_Order');
add_action('wp_ajax_mensio_ajax_Refresh_Product_Table_Data','mensio_ajax_Refresh_Product_Table_Data');
add_action('wp_ajax_mensio_ajax_Load_Orders_ShippingType_Modal','mensio_ajax_Load_Orders_ShippingType_Modal');
add_action('wp_ajax_mensio_ajax_Load_Orders_Status_Modal','mensio_ajax_Load_Orders_Status_Modal');
add_action('wp_ajax_mensio_ajax_Orders_Save_Main_Data','mensio_ajax_Orders_Save_Main_Data');
add_action('wp_ajax_mensio_ajax_Add_Order_Status','mensio_ajax_Add_Order_Status');
add_action('wp_ajax_mensio_ajax_Load_Order_Invoice','mensio_ajax_Load_Order_Invoice');
add_action('wp_ajax_mensio_ajax_Send_Invoice_Modal','mensio_ajax_Send_Invoice_Modal');
add_action('wp_ajax_mensio_ajax_Load_Split_Order_Form_Modal','mensio_ajax_Load_Split_Order_Form_Modal');
add_action('wp_ajax_mensio_ajax_Orders_Save_Split_Orders_Data','mensio_ajax_Orders_Save_Split_Orders_Data');
add_action('wp_ajax_mensio_ajax_Orders_Load_Discounts_Modal','mensio_ajax_Orders_Load_Discounts_Modal');
add_action('wp_ajax_mensio_ajax_Orders_Save_Orders_Discounts','mensio_ajax_Orders_Save_Orders_Discounts');
add_action('wp_ajax_mensio_ajax_Orders_Load_Payment_Data','mensio_ajax_Orders_Load_Payment_Data');
add_action('wp_ajax_mensio_ajax_Orders_Update_Payment_Status','mensio_ajax_Orders_Update_Payment_Status');
add_action('wp_ajax_mensio_ajax_Orders_Send_Status_Info_Mail','mensio_ajax_Orders_Send_Status_Info_Mail');
add_action('wp_ajax_mensio_ajax_Orders_Load_Payments_Modal','mensio_ajax_Orders_Load_Payments_Modal');
add_action('wp_ajax_mensio_ajax_Orders_Update_Order_Payment_Type','mensio_ajax_Orders_Update_Order_Payment_Type');
function Mensio_Admin_Orders() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Orders_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Orders'));
    $Page->Set_CustomMenuItems('
              <div class="menu_button_row"> 
                <button id="BTN_Save_Header" class="button BtnGreen BtnSwitchable btnsv" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
                <button id="BTN_SetPayment_Header" class="button BtnSwitchable" title="Select Payment Type">
                  <i class="fa fa-credit-card-alt" aria-hidden="true"></i>
                </button>
                <button id="BTN_EditOrderDiscounts_Header" class="button BtnSwitchable" title="Discounts">
                  <i class="fa fa-tags" aria-hidden="true"></i>
                </button>
                <button id="BTN_SplitOrder_Header" class="button BtnRed BtnSwitchable" title="Split">
                    <i class="fa fa-scissors fa-lg"></i>
                </button>
                <button id="BTN_Invoice_Header" class="button" title="Export Invoice To PDF">
                  <i class="fa fa-print" aria-hidden="true"></i>
                </button>
                <button id="BTN_SendMail_Header" class="button" title="Send Invoice Mail">
                  <i class="fa fa-envelope" aria-hidden="true"></i>
                </button>
                <button id="BTN_Back_Header" class="button" title="Back">
                  <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </button>
              </div>');
    $Page->Set_MainPlaceHolder(
     '<h1 class="Mns_Page_HeadLine">Sales<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      <div id="ButtonArea">
        <button id="BTN_AddNew" title="Add New Order">
          <i class="fa fa-plus" aria-hidden="true"></i>
          Add New
        </button>
      </div>
      '.wp_nonce_field('Active_Page_Orders').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_Orders.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Table">
        <div id="TBL_Orders_Wrapper" class="TBL_DataTable_Wrapper">
          '.$Page->GetDataTable().'
        </div>
      <div class="DivResizer"></div>
      </div>
      <div id="DIV_Edit" class="mensio-panel">
        <div id="tabs">
          <ul>
            <li><a href="#OrderInfoDiv">
              <i class="fa fa-dollar" aria-hidden="true"></i>
              Order
            </a></li>
            <li><a href="#OrderStatusDiv">
              <i class="fa fa-history" aria-hidden="true"></i>
              Status
            </a></li>
          </ul>
          <div id="OrderInfoDiv" class="ProductTab">
            <div class="mensio-main-panel-header">
              <label class="header-txt">
                <i id="left-header-icon" class="fa fa-info fa-fw header-icon"></i>
                Order information:
              </label>
              <i id="right-header-icon" class="fa fa-shopping-cart fa-fw header-icon"></i>
            </div>
            <div id="DIV_OrderWrapper">
              <div class="OrderDefault">
                <input type="hidden" id="FLD_EDOrder" value="">
                <input type="hidden" id="FLD_EDCustomer" value="">
                <input type="hidden" id="FLD_EDBlAddress" value="">
                <input type="hidden" id="FLD_EDSndAddress" value="">
                <input type="hidden" id="FLD_Products" value="">
                <input type="hidden" id="FLD_ShippimgType" value="">
                <div class="OrderBasicData">
                  <div class="mensio-panel-header">
                    <label class="label_symbol">
                     <i class="fa fa-bookmark"></i>
                     Serial number:
                    </label>
                    <label id="LblActStat" class="label_symbol">
                     <i class="fa fa-history"></i>
                     Active Status:<span id="OrderStatus"></span>
                    </label>
                  </div>
                  <div class="DfltInfoDiv">
                    <span id="OrderSerial"></span>
                  </div>
                  <div id="PayDetails" class="PayDetailsDiv"></div>
                  <div class="DivResizer"></div>
                </div>
                <div class="DivResizer"></div>
                <div class="OrderCustomer">
                  <div class="mensio-panel-header">
                    <label class="label_symbol">
                      <i class="fa fa-user fa-lg" aria-hidden="true"></i>
                      Customer:
                    </label>
                  </div>
                  <div class="OrderActionBtnDiv">
                    <div id="BTN_EditCustomer" class="ESBtnsDivs" title="Select Customer">
                      <div class="ESBtns">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                      </div>
                  </div>
                </div>
                <div id="CustomerDataDiv"></div>
                <div class="DivResizer"></div>
                </div>
                <div class="OrderAddress">
                  <div class="mensio-panel-header">
                    <label class="label_symbol">
                      <i class="fa fa-address-book fa-lg" aria-hidden="true"></i>
                      Billing Address:
                    </label>
                  </div>
                  <div class="OrderActionBtnDiv">
                    <div id="BTN_EditBillingAddress" class="ESBtnsDivs" title="Select Billing Address">
                      <div class="ESBtns">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                      </div>
                    </div>
                  </div>
                  <div id="BillingAddressDiv"></div>
                  <div class="DivResizer"></div>
                </div>
                <div class="OrderAddress">
                  <div class="mensio-panel-header">
                    <label class="label_symbol">
                      <i class="fa fa-address-book fa-lg" aria-hidden="true"></i>
                      Shipping Address:
                    </label>
                  </div>
                  <div class="OrderActionBtnDiv">
                    <div id="BTN_EditShippingAddress" class="ESBtnsDivs" title="Select Shipping Address">
                      <div class="ESBtns">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                      </div>
                    </div>
                  </div>
                  <div id="SendingAddressDiv"></div>
                  <div class="DivResizer"></div>
                </div>
                <div class="DivResizer"></div>
                <div class="OrderCustomer">
                 <div class="mensio-panel-header"><label class="label_symbol">
                    <i class="fa fa-archive fa-lg" aria-hidden="true"></i>
                    Products:
                  </label></div>
                  <div class="OrderActionBtnDiv">
                    <div id="BTN_EditProducts" class="ESBtnsDivs" title="Select Products">
                      <div class="ESBtns">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                      </div>
                    </div>
                  </div>
                  <div class="table-container">
                  <table class="ProductTable">
                    <thead>
                      <tr>
                        <th class="BtnCol"></th>
                        <th>Name</th>
                        <th class="SmlCol">Amount</th>
                        <th class="SmlCol">Price</th>
                        <th class="SmlCol">Discount</th>
                        <th class="SmlCol">Tax</th>
                        <th class="SmlCol">Full Price</th>
                      </tr>
                    </thead>
                    <tbody id="OrdersProductTable"></tbody>
                    <tfoot id="OrdersProductTableFooter"></tfoot>
                  </table>
                  </div>
                </div>
                <div class="DivResizer"></div>
                <div class="button_row">
                  <button id="BTN_Save" class="button BtnGreen BtnSwitchable btnsv" title="Save">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                  </button>
                  <button id="BTN_SetPayment" class="button BtnSwitchable" title="Select Payment Type">
                    <i class="fa fa-credit-card-alt" aria-hidden="true"></i>
                  </button>
                  <button id="BTN_SplitOrder" class="button BtnRed BtnSwitchable" title="Split">
                      <i class="fa fa-scissors fa-lg"></i>
                  </button>
                  <button id="BTN_Invoice" class="button" title="Export Invoice To PDF">
                    <i class="fa fa-print" aria-hidden="true"></i>
                  </button>
                  <button id="BTN_SendMail" class="button" title="Send Invoice Mail">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                  </button>
                  <button id="BTN_Back" class="button" title="Back">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                  </button>
                </div>
                <div class="DivResizer"></div>
              </div>
            </div>
          </div>
          <div id="OrderStatusDiv" class="ProductTab">
            <div class="OrderCustomer">
              <div class="mensio-panel-header">
                <label class="label_symbol">
                  <i class="fa fa-history fa-lg" aria-hidden="true"></i>
                  Status History
                </label>
              </div>
              <div class="OrderActionBtnDiv">
                <div class="ESBtnsDivs">
                  <div id="BTN_ChangeStatus" class="ESBtns" title="Change Order Status">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
              <table class="ProductTable">
                <thead>
                  <tr>
                    <th>Status</th>
                    <th class="SmlCol">Date</th>
                    <th class="SmlCol">Time</th>
                    <th class="SmlCol infocol">Active</th>
                  </tr>
                </thead>
                <tbody id="OrderStatusHistory"></tbody>
              </table>
            </div>
            <div class="DivResizer"></div>
          </div>
        </div>
      </div>');
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Orders','Sales');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Orders() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $JSONData = stripcslashes($_REQUEST['ExtraActions']);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch,$JSONData);
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Load_Order_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadOrderData($Order);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Customer_Orders_Selection_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadCustomerSelectionModal();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Search_Customer_For_Orders() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Search = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SearchOrdersCustomer($Search);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Customer_Data_For_Orders() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Customer = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadOrdersCustomerData($Customer);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Address_Orders_Selection_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $Customer = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadAddressSelectionModal($Type,$Customer);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Set_Customer_Order_Address() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $Customer = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $Address = filter_var($_REQUEST['Address'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadCustomerOrderAddressData($Type,$Customer,$Address);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Product_Orders_Selection_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductOrdersSelection();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Search_Product_For_Orders() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $OrderPrds = filter_var($_REQUEST['OrderPrds'],FILTER_SANITIZE_STRING);
    $SrchPrd = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadSearchProductsForOrders($OrderPrds,$SrchPrd);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Add_Product_To_Order() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $OrderPrds = $_REQUEST['OrderPrds'];
    $NewPrds = filter_var($_REQUEST['NewProd'],FILTER_SANITIZE_STRING);
    $Amount = filter_var($_REQUEST['Amount'],FILTER_SANITIZE_STRING);
    $Shipping = filter_var($_REQUEST['Shipping'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->AddProductToOrderTable($Order,$OrderPrds,$NewPrds,$Amount,$Shipping);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Orders_View_Product_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadViewProductModal($Product);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Orders_Remove_Product_From_Order() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $OrderPrds = $_REQUEST['OrderPrds'];
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Amount = filter_var($_REQUEST['Amount'],FILTER_SANITIZE_STRING);
    $Shipping = filter_var($_REQUEST['Shipping'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RemoveProductFromOrderTable($Order,$OrderPrds,$Product,$Shipping);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Refresh_Product_Table_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $OrderPrds = $_REQUEST['OrderPrds'];
    $Shipping = filter_var($_REQUEST['Shipping'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RefreshProductTableData($Order,$OrderPrds,$Shipping);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Orders_ShippingType_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $ShipType = filter_var($_REQUEST['ShipType'],FILTER_SANITIZE_STRING);
    $ShipAddress = filter_var($_REQUEST['ShipAddress'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadOrdersShippingTypeModal($ShipType,$ShipAddress);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Orders_Status_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadOrderStatusModal($Order);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Orders_Save_Main_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Customer = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $BlngAddress = filter_var($_REQUEST['BlngAddress'],FILTER_SANITIZE_STRING);
    $SendAddress = filter_var($_REQUEST['SendAddress'],FILTER_SANITIZE_STRING);
    $OrderPrds = $_REQUEST['OrderPrds'];
    $ShipType = filter_var($_REQUEST['ShipType'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SaveOrdersData($Order,$Customer,$BlngAddress,$SendAddress,$OrderPrds,$ShipType);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Add_Order_Status() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Status = filter_var($_REQUEST['Status'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SaveOrdersStatus($Order,$Status);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Order_Invoice() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadOrderInvoice($Order);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Split_Order_Form_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadSplitOrderFormModal($Order);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Orders_Save_Split_Orders_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $SubOrder1 = filter_var($_REQUEST['SubOrder1'],FILTER_SANITIZE_STRING);
    $SubOrder2 = filter_var($_REQUEST['SubOrder2'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SaveSplitOrdersData($Order,$SubOrder1,$SubOrder2);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Orders_Load_Discounts_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadOrderDiscountModal($Order);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Orders_Save_Orders_Discounts() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Discount = filter_var($_REQUEST['Discount'],FILTER_SANITIZE_STRING);
    $Active = filter_var($_REQUEST['Active'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateOrdersDiscounts($Order,$Discount,$Active);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Orders_Load_Payment_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadOrderPaymentData($Order);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Orders_Update_Payment_Status() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateOrderPaymentStatus($Order);
      if ($RtrnData['ERROR'] === 'FALSE') {
        $RtrnData = $Page->LoadOrderPaymentData($Order);
      }
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Orders_Send_Status_Info_Mail() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SendOrderStatusInfoMail($Order);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Send_Invoice_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SendOrderInvoiceMail($Order);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Orders_Load_Payments_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadOrderPaymentsModal($Order);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Orders_Update_Order_Payment_Type() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Payment = filter_var($_REQUEST['Payment'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateOrderPaymentTypeValues($Order,$Payment);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}