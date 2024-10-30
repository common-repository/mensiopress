<?php
add_action('wp_ajax_mensio_ajax_Table_Regions', 'mensio_ajax_Table_Regions');
add_action('wp_ajax_mensio_ajax_Load_Country_Regions', 'mensio_ajax_Load_Country_Regions');
add_action('wp_ajax_mensio_ajax_Load_Regions_Parent_Options', 'mensio_ajax_Load_Regions_Parent_Options');
add_action('wp_ajax_mensio_ajax_Edit_Regions_Data', 'mensio_ajax_Edit_Regions_Data');
add_action('wp_ajax_mensio_ajax_Update_Region_Data', 'mensio_ajax_Update_Region_Data');
add_action('wp_ajax_mensio_ajax_Edit_Region_Type_Data', 'mensio_ajax_Edit_Region_Type_Data');
add_action('wp_ajax_mensio_ajax_Delete_Region_Type_Data', 'mensio_ajax_Delete_Region_Type_Data');
add_action('wp_ajax_mensio_ajax_Update_Region_Type', 'mensio_ajax_Update_Region_Type');
add_action('wp_ajax_mensio_ajax_Delete_Region_Data', 'mensio_ajax_Delete_Region_Data');
add_action('wp_ajax_mensio_ajax_Modal_Region_Translations', 'mensio_ajax_Modal_Region_Translations');
add_action('wp_ajax_mensio_ajax_Update_Region_Translations', 'mensio_ajax_Update_Region_Translations');
function Mensio_Admin_Settings_Regions() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Regions();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('System'));
    $Page->Set_CustomMenuItems('') ;
    $Page->Set_MainPlaceHolder('
        <h1 class="Mns_Page_HeadLine">Country Regions<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
        '.wp_nonce_field('Active_Page_Countries_Regions').'
        <div class="PageInfo">'.MENSIO_PAGEINFO_Regions.'</div>
        <div class="DivResizer"></div>
        <hr>
        <div id="DIV_Table">
          <div id="TBL_Regions_Wrapper" class="TBL_DataTable_Wrapper">
            '.$Page->GetDataTable().'
          </div>
        <div class="DivResizer"></div>
        </div>
        <div id="DIV_Edit">
          <div class="Mns_Edit_Wrapper">
            <div id="RegionEditDiv">
              <div id="RegionEdit">
                <h3>Region</h3>
                <div class="AccTab">
                  <div class="edit_row">
                    <input type="hidden" id="Country" value="">
                    <input type="hidden" id="Region" value="">
                    <input type="hidden" id="Type" value="">
                    <label class="label_symbol">Region Types</label>
                    <select id="FLD_RegionType" class="form-control">
                    </select>
                    <label class="label_symbol">Region Name</label>
                    <input type="text" id="FLD_RegionName" value="" class="form-control">
                    <label class="label_symbol">Region Parent</label>
                    <select id="FLD_RegionParent" class="form-control">
                      <option value="TopLevel">No Parent</option>
                    </select>
                  </div>
                  <div class="button_row">
                    <button id="BTN_RegionSave" class="button BtnGreen" title="Region Save">
                      <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    </button>
                    <button id="BTN_AddRegion" class="button" title="Add New Region">
                      <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                    <button id="BTN_RegionTrans" class="button" title="Translate Region Name">
                      <i class="fa fa-comment" aria-hidden="true"></i>
                    </button>
                    <button id="BTN_RegionDel" class="button BtnRed" title="Delete Region">
                      <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                    <button id="BTN_Back" class="button" title="Back">
                      <i class="fa fa-arrow-left"></i>
                    </button>                   
                  </div>
                </div>
                <h3>Region Type</h3>
                <div class="AccTab">
                  <div class="edit_row">
                    <label class="label_symbol">Type Name</label>
                    <input type="text" id="FLD_TypeName" value="" class="form-control">
                    <div id="RegionTypeList"></div>
                  </div>
                  <div class="button_row">
                    <button id="BTN_TypeSave" class="button BtnGreen" title="Save Region Type">
                      <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    </button>
                    <button id="BTN_AddType" class="button" title="Add New Region Type">
                      <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                  </div>
                </div> 
              </div>
            </div>
            <div id="RegionListDiv">
              <div class="RegionListTitle">
                <h2>Region List</h2>
              <div class="DivResizer"></div>
              </div>
              <div id="RegionList"></div>
            <div class="DivResizer"></div>
            </div>
          <div class="DivResizer"></div>
          </div>
        <div class="DivResizer"></div>
        </div>'
    );  
    $Page->UpdatePage();
    $Page->SetActiveSubPage('System','Regions');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Regions() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Regions();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch);
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Load_Country_Regions() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Country = filter_var($_REQUEST['Country'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Regions();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadCountryRegions($Country);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Region_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Country = filter_var($_REQUEST['Country'],FILTER_SANITIZE_STRING);
    $Region = filter_var($_REQUEST['Region'],FILTER_SANITIZE_STRING);
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $Name = filter_var($_REQUEST['Name'],FILTER_SANITIZE_STRING);
    $Parent = filter_var($_REQUEST['Parent'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Regions();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateRegionsList($Country,$Region,$Type,$Name,$Parent);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Regions_Parent_Options() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Country = filter_var($_REQUEST['Country'],FILTER_SANITIZE_STRING);
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Regions();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadRegionsParentOptions($Country,$Type);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Edit_Regions_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Region = filter_var($_REQUEST['Region'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Regions();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->EditRegionsData($Region);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Region_Type() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Country = filter_var($_REQUEST['Country'],FILTER_SANITIZE_STRING);
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $TypeName = filter_var($_REQUEST['TypeName'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Regions();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateRegionType($Country,$Type,$TypeName);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Edit_Region_Type_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Regions();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->EditRegionTypeData($Type);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Delete_Region_Type_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Country = filter_var($_REQUEST['Country'],FILTER_SANITIZE_STRING);
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Regions();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteRegionTypeData($Country,$Type);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Delete_Region_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Country = filter_var($_REQUEST['Country'],FILTER_SANITIZE_STRING);
    $Region = filter_var($_REQUEST['Region'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Regions();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteRegionData($Country,$Region);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Modal_Region_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Region = filter_var($_REQUEST['Region'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Regions();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadModalTranslations($Region);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Region_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Region = filter_var($_REQUEST['Region'],FILTER_SANITIZE_STRING);
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Settings_Regions();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateRegionTranslations($Region,$Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}