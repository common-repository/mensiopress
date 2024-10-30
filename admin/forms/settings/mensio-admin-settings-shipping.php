<?php
add_action('wp_ajax_mensio_ajax_Table_Shipping', 'mensio_ajax_Table_Shipping');
add_action('wp_ajax_mensio_ajax_Shipping_Load_Courier_Data', 'mensio_ajax_Shipping_Load_Courier_Data');
add_action('wp_ajax_mensio_ajax_Shipping_Update_Courier_Active_Status', 'mensio_ajax_Shipping_Update_Courier_Active_Status');
add_action('wp_ajax_mensio_ajax_Shipping_Update_Courier_Data', 'mensio_ajax_Shipping_Update_Courier_Data');
add_action('wp_ajax_mensio_ajax_Shipping_Load_Option_Modal', 'mensio_ajax_Shipping_Load_Option_Modal');
add_action('wp_ajax_mensio_ajax_Shipping_Add_Shipping_Option', 'mensio_ajax_Shipping_Add_Shipping_Option');
add_action('wp_ajax_mensio_ajax_Shipping_Disable_Shipping_Country', 'mensio_ajax_Shipping_Disable_Shipping_Country');
add_action('wp_ajax_mensio_ajax_Shipping_Disable_Shipping_Option', 'mensio_ajax_Shipping_Disable_Shipping_Option');
function Mensio_Admin_Orders_Shipping() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Orders_Shipping();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Settings'));
    $Page->Set_CustomMenuItems('
              <div class="menu_button_row"> 
                <button id="BTN_Save_Header" class="button BtnGreen" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
                <button id="BTN_Back_Header" class="button" title="Back">
                  <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </button>
              </div>');
    $Page->Set_MainPlaceHolder(
       '<h1 class="Mns_Page_HeadLine">Shipping Options<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
        <div id="ButtonArea">
          <button id="BTN_AddNew" title="Add New Shipping Option">
            <i class="fa fa-plus action-icon" aria-hidden="true"></i>
            Add New
          </button>
        </div>
        '.wp_nonce_field('Active_Page_Product_Shipping').'
        <div class="PageInfo">'.MENSIO_PAGEINFO_Shipping.'</div>
        <div class="DivResizer"></div>
        <hr>
        <div id="DIV_Table">
          <div id="TBL_Shipping_Wrapper" class="TBL_DataTable_Wrapper">
            '.$Page->GetDataTable().'
          </div>
        <div class="DivResizer"></div>
        </div>
        <div id="DIV_Edit">
          <div id="CourierDiv" class="EditSubForm">
            <input type="hidden" id="FLD_Courier" value="" class="form-control">
            <label class="label_symbol">Courier Data</label>
            <hr>
            <label class="label_symbol">Name</label>
            <input type="text" id="FLD_Name" value="" class="form-control">
            <label class="label_symbol">Delivery Speed (in days)</label>
            <input type="text" id="FLD_DeliverySpeed" value="" class="form-control">
            <label class="label_symbol">Billing Type</label>
            <select id="FLD_BillingType" class="form-control">
              <option value="WEIGHT">Weight</option>
              <option value="PRICE">Price</option>
            </select>
            <label class="label_symbol">Option Is Active
              <input type="checkbox" id="FLD_Active" value="">
            </label>
            <div class="DivResizer"></div>
            <div class="button_row">
              <button id="BTN_Save" class="button BtnGreen" title="Save">
                <i class="fa fa-floppy-o" aria-hidden="true"></i>
              </button>
              <button id="BTN_Back" class="button" title="Back">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
              </button>
            </div>
          <div class="DivResizer"></div>
          </div>
          <div class="DivResizer"></div>
          <div id="ShippingDiv" class="EditSubForm">
            <div class="CountryList">
              <div class="CountryListCtrl">
                <label class="label_symbol">Shipping Countries</label>
                <div class="OrderActionBtnDiv">
                  <div id="BTN_AddShippingOption" class="ESBtnsDivs">
                    <div class="ESBtns" title="Add Shipping Option">
                      <i class="fa fa-plus" aria-hidden="true"></i>
                    </div>
                  </div>
                </div>
              </div>
              <hr>
              <input type="hidden" id="ActiveCountry" value="">
              <div id="ShippingList" class="ListBox"></div>
              <div class="DivResizer"></div>
            </div>
          </div>
          <div class="DivResizer"></div>
        </div>'
    );  
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Settings','Shipping');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Shipping() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Shipping();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch);
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Shipping_Load_Courier_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Courier = filter_var($_REQUEST['Courier'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Shipping();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadCourierData($Courier);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Shipping_Update_Courier_Active_Status() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Courier = filter_var($_REQUEST['Courier'],FILTER_SANITIZE_STRING);
    $Active = filter_var($_REQUEST['Active'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Shipping();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateCourierActiveStatus($Courier,$Active);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Shipping_Update_Courier_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Courier = filter_var($_REQUEST['Courier'],FILTER_SANITIZE_STRING);
    $Name = filter_var($_REQUEST['Name'],FILTER_SANITIZE_STRING);
    $DlSpeed = filter_var($_REQUEST['DeliverySpeed'],FILTER_SANITIZE_STRING);
    $BlngType = filter_var($_REQUEST['BillingType'],FILTER_SANITIZE_STRING);
    $Active = filter_var($_REQUEST['Active'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Shipping();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateCourierData($Courier,$Name,$DlSpeed,$BlngType,$Active);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Shipping_Load_Option_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Courier = filter_var($_REQUEST['Courier'],FILTER_SANITIZE_STRING);
    $Country = filter_var($_REQUEST['Country'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Shipping();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadShippingOptionModal($Courier,$Country);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Shipping_Add_Shipping_Option() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Courier = filter_var($_REQUEST['Courier'],FILTER_SANITIZE_STRING);
    $Country = filter_var($_REQUEST['Country'],FILTER_SANITIZE_STRING);
    $Weight = filter_var($_REQUEST['Weight'],FILTER_SANITIZE_STRING);
    $Price = filter_var($_REQUEST['Price'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Shipping();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->AddCourierShippingOption($Courier,$Country,$Weight,$Price);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Shipping_Disable_Shipping_Country() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Courier = filter_var($_REQUEST['Courier'],FILTER_SANITIZE_STRING);
    $Country = filter_var($_REQUEST['Country'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Shipping();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DisableCourierShippingCountry($Courier,$Country);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Shipping_Disable_Shipping_Option() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Option = filter_var($_REQUEST['Option'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Orders_Shipping();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DisableCourierShippingOption($Option);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}