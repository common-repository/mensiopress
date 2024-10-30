<?php
add_action('wp_ajax_mensio_ajax_Table_Customers', 'mensio_ajax_Table_Customers');
add_action('wp_ajax_mensio_ajax_Update_Customers_Activate', 'mensio_ajax_Update_Customers_Activate');
add_action('wp_ajax_mensio_ajax_Update_Customers_Deactivate', 'mensio_ajax_Update_Customers_Deactivate');
add_action('wp_ajax_mensio_ajax_Modal_View_Customer', 'mensio_ajax_Modal_View_Customer');
add_action('wp_ajax_mensio_ajax_Modal_Customer_Address', 'mensio_ajax_Modal_Customer_Address');
add_action('wp_ajax_mensio_ajax_Modal_Customer_Contact', 'mensio_ajax_Modal_Customer_Contact');
add_action('wp_ajax_mensio_ajax_Customers_Load_Region_Options', 'mensio_ajax_Customers_Load_Region_Options');
add_action('wp_ajax_mensio_ajax_Customer_Update_Modal_Data', 'mensio_ajax_Customer_Update_Modal_Data');
add_action('wp_ajax_mensio_ajax_Customer_Delete_Modal_Data', 'mensio_ajax_Customer_Delete_Modal_Data');
add_action('wp_ajax_mensio_ajax_New_Customer', 'mensio_ajax_New_Customer');
add_action('wp_ajax_mensio_ajax_Edit_Customer', 'mensio_ajax_Edit_Customer');
add_action('wp_ajax_mensio_ajax_Save_Customer_Data', 'mensio_ajax_Save_Customer_Data');
add_action('wp_ajax_mensio_ajax_Delete_Customer_Data', 'mensio_ajax_Delete_Customer_Data');
add_action('wp_ajax_mensio_ajax_Customers_Type_Changed', 'mensio_ajax_Customers_Type_Changed');
add_action('wp_ajax_mensio_ajax_Customers_Company_Changed', 'mensio_ajax_Customers_Company_Changed');
add_action('wp_ajax_mensio_ajax_Load_Customer_NewPswd_Modal', 'mensio_ajax_Load_Customer_NewPswd_Modal');
add_action('wp_ajax_mensio_ajax_Check_Customers_New_Password_Strength', 'mensio_ajax_Check_Customers_New_Password_Strength');
add_action('wp_ajax_mensio_ajax_Customers_New_Password', 'mensio_ajax_Customers_New_Password');
add_action('wp_ajax_mensio_ajax_Customer_Modal_View_Order_Details', 'mensio_ajax_Customer_Modal_View_Order_Details');
add_action('wp_ajax_mensio_ajax_Customer_Modal_View_Order_Status_History', 'mensio_ajax_Customer_Modal_View_Order_Status_History');
function Mensio_Admin_Customers() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Customers_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Customers'));
    $Page->Set_CustomMenuItems('
              <div class="menu_button_row">
                <span id="HdBarBtnWrap">
                  <button id="BTN_Save_Header" class="button BtnGreen" title="Save">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                  </button>
                  <button id="BTN_Delete_Header" class="button BtnRed" title="Delete">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                  </button>
                </span>
                <button id="BTN_Back_Header" class="button" title="Back">
                  <i class="fa fa-arrow-left"></i>
                </button>
              </div>') ;
    $Page->Set_MainPlaceHolder(
     '<h1 class="Mns_Page_HeadLine">Customers<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      <div id="ButtonArea">
        <button id="BTN_AddNew" title="Add New Customer">
          <i class="fa fa-plus action-icon" aria-hidden="true"></i>
          Add New
        </button>
      </div>
      '.wp_nonce_field('Active_Page_Accounts').' 
      <div class="PageInfo">'.MENSIO_PAGEINFO_Customers.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Table">
        <div id="TBL_Customers_Wrapper" class="TBL_DataTable_Wrapper">
          '.$Page->GetDataTable().'
        </div>
      <div class="DivResizer"></div>
      </div>
      <div id="DIV_Edit"></div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Customers','Accounts');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Customers() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $JSONData = stripslashes($_REQUEST['ExtraActions']);
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch,$JSONData);
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Update_Customers_Activate() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $JSONData = stripslashes($_REQUEST['ExtraActions']);
    $InData = filter_var($_REQUEST['Data'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateActivation($InData,true);
      $RtrnData['Table'] = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch,$JSONData);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Customers_Deactivate() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $JSONData = stripslashes($_REQUEST['ExtraActions']);
    $InData = filter_var($_REQUEST['Data'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateActivation($InData,false);
      $RtrnData['Table'] = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch,$JSONData);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Modal_View_Customer() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Customer = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->ShowCustomerModalInfo($Customer);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_New_Customer() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadCustomerEditing();
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Edit_Customer() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Customer = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadCustomerEditing($Customer);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Delete_Customer_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Customer = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RemoveCustomerData($Customer);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Save_Customer_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Customer = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateCustomerData($Customer,$Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Customers_Type_Changed() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->CheckCustomersType($Type);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Customers_Company_Changed() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadNewMultiFields();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Customer_NewPswd_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadNewPasswordModalForm();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Customers_New_Password() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->CreateNewPassword();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Check_Customers_New_Password_Strength() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Value = $_REQUEST['Value'];
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData= $Page->CheckCustomersNewPasswordStrength($Value);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Modal_Customer_Address() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Credential = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $Address = filter_var($_REQUEST['Address'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadAddressModal($Credential,$Address);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Modal_Customer_Contact() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Credential = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $Contact = filter_var($_REQUEST['Contact'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadContactModal($Credential,$Contact);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Customers_Load_Region_Options() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Country = filter_var($_REQUEST['Country'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadRegionOptions($Country);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Customer_Update_Modal_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Tab = filter_var($_REQUEST['Tab'],FILTER_SANITIZE_STRING);
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateModalData($Tab,$Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Customer_Delete_Modal_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Customer = filter_var($_REQUEST['Customer'],FILTER_SANITIZE_STRING);
    $Tab = filter_var($_REQUEST['Tab'],FILTER_SANITIZE_STRING);
    $Data = filter_var($_REQUEST['Data'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteModalData($Customer,$Tab,$Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Customer_Modal_View_Order_Details() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->ModalViewOrderDetails($Order);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Customer_Modal_View_Order_Status_History() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Order = filter_var($_REQUEST['Order'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Customers_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->ModalViewOrderStatusHistory($Order);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}