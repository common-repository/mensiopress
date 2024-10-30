<?php
add_action('wp_ajax_mensio_ajax_Table_Sectors', 'mensio_ajax_Table_Sectors');
add_action('wp_ajax_mensio_ajax_Load_Sector_Edit_Data', 'mensio_ajax_Load_Sector_Edit_Data');
add_action('wp_ajax_mensio_ajax_Save_Sector_Edit_Data', 'mensio_ajax_Save_Sector_Edit_Data');
add_action('wp_ajax_mensio_ajax_Delete_Sector', 'mensio_ajax_Delete_Sector');
add_action('wp_ajax_mensio_ajax_Display_Sector_Modal_Parent', 'mensio_ajax_Display_Sector_Modal_Parent');
add_action('wp_ajax_mensio_ajax_Modal_Update_Sector_Parent', 'mensio_ajax_Modal_Update_Sector_Parent');
function Mensio_Admin_Settings_Sectors() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Sectors();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('System'));
    $Page->Set_CustomMenuItems('
              <div class="menu_button_row"> 
                <button id="BTN_Save_Header" class="button BtnGreen" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
                <button id="BTN_Delete_Header" class="button BtnRed" title="Delete">
                  <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
                <button id="BTN_Back_Header" class="button" title="Back">
                  <i class="fa fa-arrow-left"></i>
                </button>
              </div>');
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">Business Sectors<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      <div id="ButtonArea">
        <button id="BTN_AddNew" title="Add New Business Sector">
          <i class="fa fa-plus action-icon" aria-hidden="true"></i>
          Add New
        </button>
      </div>
      '.wp_nonce_field('Active_Page_Business_Sectors').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_BusinessSectors.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Table">
        <div id="TBL_Sectors_Wrapper" class="TBL_DataTable_Wrapper">
          '.$Page->GetDataTable().'
        </div>
      <div class="DivResizer"></div>
      </div>
      <div id="DIV_Edit">
        <div class="Mns_EditWrapper">
          <label class="label_symbol">Parent Sector</label><br>
          <input type="hidden" id="FLD_Sector" value="">
          <select id="FLD_ParentSector" class="form-control">
            '.$Page->GetSectorOptions().'
          </select>
        </div>
        <div class="Mns_EditWrapper">
          <label class="label_symbol TransLabel">Translations</label>
          '.$Page->GetSectorTransFields().'
        <div class="DivResizer"></div>
        </div>
        <div class="button_row">
          <button id="BTN_Save" class="button BtnGreen" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>
          <button id="BTN_Delete" class="button BtnRed" title="Delete">
            <i class="fa fa-trash" aria-hidden="true"></i>
          </button>
          <button id="BTN_Back" class="button" title="Back">
            <i class="fa fa-arrow-left"></i>
          </button> 
        </div>
      <div class="DivResizer"></div>
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('System','BusinessSectors');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Sectors() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Sectors();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch);
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Load_Sector_Edit_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Sector = filter_var($_REQUEST['Sector'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Sectors();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadSectorData($Sector);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Save_Sector_Edit_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Sector = filter_var($_REQUEST['Sector'],FILTER_SANITIZE_STRING);
    $FldData = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Settings_Sectors();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateSectorData($Sector,$FldData);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Delete_Sector() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Sector = filter_var($_REQUEST['Sector'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Sectors();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteSectorData($Sector);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Display_Sector_Modal_Parent() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Selected = filter_var($_REQUEST['Selected'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Sectors();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadModalParent($Selected);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Modal_Update_Sector_Parent() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Parent = filter_var($_REQUEST['Parent'],FILTER_SANITIZE_STRING);
    $Selected = filter_var($_REQUEST['Selected'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Sectors();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateModalParent($Selected,$Parent);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();  
}