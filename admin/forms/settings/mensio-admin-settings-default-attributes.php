<?php
add_action('wp_ajax_mensio_ajax_Table_DefaultAttributes', 'mensio_ajax_Table_DefaultAttributes');
add_action('wp_ajax_mensio_ajax_Products_Edit_Attribute_Data', 'mensio_ajax_Products_Edit_Attribute_Data');
add_action('wp_ajax_mensio_ajax_Products_Save_Attribute_Data', 'mensio_ajax_Products_Save_Attribute_Data');
add_action('wp_ajax_mensio_ajax_Products_Save_Global_Attribute_Value', 'mensio_ajax_Products_Save_Global_Attribute_Value');
add_action('wp_ajax_mensio_ajax_Products_Remove_Global_Attribute_Value', 'mensio_ajax_Products_Remove_Global_Attribute_Value');
add_action('wp_ajax_mensio_ajax_Products_Toggle_Global_Attribute_Visiblity', 'mensio_ajax_Products_Toggle_Global_Attribute_Visiblity');
add_action('wp_ajax_mensio_ajax_Products_Toggle_Global_Attribute_Visiblity_Bulk', 'mensio_ajax_Products_Toggle_Global_Attribute_Visiblity_Bulk');
add_action('wp_ajax_mensio_ajax_Products_Convert_Hex_To_RGB', 'mensio_ajax_Products_Convert_Hex_To_RGB');
add_action('wp_ajax_mensio_ajax_Global_Attribute_Load_Translation', 'mensio_ajax_Global_Attribute_Load_Translation');
add_action('wp_ajax_mensio_ajax_Update_Global_Attribute_Translations', 'mensio_ajax_Update_Global_Attribute_Translations');
add_action('wp_ajax_mensio_ajax_Global_Attribute_Load_AttrType', 'mensio_ajax_Global_Attribute_Load_AttrType');
add_action('wp_ajax_mensio_ajax_Update_Store_Product_Metrics', 'mensio_ajax_Update_Store_Product_Metrics');
function Mensio_Admin_Products_Default_Attributes() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Products_Default_Attributes_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Settings'));
    $Page->Set_CustomMenuItems('
              <div class="menu_button_row"> 
                <button id="BTN_Translate_Header" class="button" title="Translate">
                  <i class="fa fa-comment" aria-hidden="true"></i>
                </button>
                <button id="BTN_Back_Header" class="button" title="Back">
                  <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </button>
              </div>');
    $Page->Set_MainPlaceHolder('
      <h1 class="Mns_Page_HeadLine">Global Attributes<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      <div id="ButtonArea">
        <button id="BTN_EditAttrType" title="Edit Attribute Type">
          <i class="fa fa-pencil action-icon" aria-hidden="true"></i>
          Edit Attribute Type
        </button>
      </div>
      '.wp_nonce_field('Active_Page_Global_Attributes').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_GlobalAttributes.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Table">
        <div id="TBL_DefaultAttributes_Wrapper" class="TBL_DataTable_Wrapper">
          '.$Page->GetDataTable().'
        </div>
      <div class="DivResizer"></div>
      </div>
      <div id="DIV_Edit">
        <div id="EditAttrWrapper">
          <div class="AttrStdDiv">
            <input type="hidden" id="FLD_Attribute" value="">
            <div class="namelbl">
              <label class="label_symbol">
                Attribute Name:
                <span id="FLD_Name"></span>
              </label>
            </div>
            <div class="namelbl">
              <label class="label_symbol">
                Attribute Type:
                <span id="FLD_Type"></span>
              </label>
            </div>
            <div class="namelbl">
              <label class="label_symbol ChckLbl">Visible
                <input type="checkbox" id="FLD_Visibility" value="">
              </label>
            </div>
            <div class="DivResizer"></div>
            <div id="ValCtrlDiv" class="ValueInputDiv">
              <input type="hidden" id="FLD_ValueID" value="NewValue">
              <div id="InputBtnDiv">
                <button id="BTN_AddValue" class="BTN_AddAttrVal" title="Add Attribute Value">
                  <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
                <div class="DivResizer"></div>
              </div>
              <div id="InputDiv"></div>
            </div>
            <div id="AttrValList" class="AttrValListDiv"></div>
            <div class="button_row">
              <button id="BTN_Translate" class="button" title="Translate">
                <i class="fa fa-comment" aria-hidden="true"></i>
              </button>
              <button id="BTN_Back" class="button" title="Back">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
              </button>
            </div>
            <div class="DivResizer"></div>
          </div>
        </div>
      </div>');
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Settings','GlobalAttributes');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_DefaultAttributes() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Default_Attributes_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch);
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Products_Edit_Attribute_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Attribute = filter_var($_REQUEST['Attribute'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Default_Attributes_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {    
      $RtrnData = $Page->LoadAttributeData($Attribute);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Save_Attribute_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Attribute = filter_var($_REQUEST['Attribute'],FILTER_SANITIZE_STRING);
    $Visibility = filter_var($_REQUEST['Visibility'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Default_Attributes_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SaveAttributeData($Attribute,$Visibility);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Save_Global_Attribute_Value() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Attribute = filter_var($_REQUEST['Attribute'],FILTER_SANITIZE_STRING);
    $Name = filter_var($_REQUEST['Name'],FILTER_SANITIZE_STRING);
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $ValID = filter_var($_REQUEST['ValID'],FILTER_SANITIZE_STRING);
    $Value = filter_var($_REQUEST['Value'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Default_Attributes_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SaveGlobalAttributeValue($Attribute,$Name,$Type,$ValID,$Value);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Remove_Global_Attribute_Value() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Attribute = filter_var($_REQUEST['Attribute'],FILTER_SANITIZE_STRING);
    $Value = filter_var($_REQUEST['Value'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Default_Attributes_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RemoveGlobalAttributeValue($Attribute,$Value);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Toggle_Global_Attribute_Visiblity() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Attribute = filter_var($_REQUEST['Attribute'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Default_Attributes_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->ToggleGlobalAttributeVisiblity($Attribute);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Toggle_Global_Attribute_Visiblity_Bulk() {
  $RtrnData = '';
  $Msg = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Selections = filter_var($_REQUEST['Selections'],FILTER_SANITIZE_STRING);
    $Selections = explode(';', $Selections);
    $Page = new Mensio_Admin_Products_Default_Attributes_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      foreach ($Selections as $Attribute) {
        if ($Attribute !== '') {
          $RtrnData = $Page->ToggleGlobalAttributeVisiblity($Attribute);
          $Msg .= $RtrnData['Message'];
        }
      }
    }
    unset($Page);
    $RtrnData['Message'] = $Msg;
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Convert_Hex_To_RGB() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Value = filter_var($_REQUEST['Value'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Default_Attributes_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->HexToRGB($Value);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Global_Attribute_Load_Translation() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Attribute = filter_var($_REQUEST['Attribute'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Default_Attributes_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadGlobalAttributeTranslation($Attribute);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Global_Attribute_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Attribute = filter_var($_REQUEST['Attribute'],FILTER_SANITIZE_STRING);
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Products_Default_Attributes_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateGlobalAttributeTranslations($Attribute,$Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Global_Attribute_Load_AttrType() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Products_Default_Attributes_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadGlobalAttributeTypeForm();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Store_Product_Metrics() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Store = filter_var($_REQUEST['Store'],FILTER_SANITIZE_STRING);
    $Color = filter_var($_REQUEST['Color'],FILTER_SANITIZE_STRING);
    $Height= filter_var($_REQUEST['Height'],FILTER_SANITIZE_STRING);
    $Length = filter_var($_REQUEST['Length'],FILTER_SANITIZE_STRING);
    $Size = filter_var($_REQUEST['Size'],FILTER_SANITIZE_STRING);
    $Volume = filter_var($_REQUEST['Volume'],FILTER_SANITIZE_STRING);
    $Weight = filter_var($_REQUEST['Weight'],FILTER_SANITIZE_STRING);
    $Width = filter_var($_REQUEST['Width'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Default_Attributes_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateStoreProductMetrics($Store,$Color,$Height,$Length,$Size,$Volume,$Weight,$Width);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}