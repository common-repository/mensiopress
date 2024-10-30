<?php
if ( ! defined( 'WPINC' ) ) { die(); }
add_action('wp_ajax_mensio_ajax_Table_Countries', 'mensio_ajax_Table_Countries');
add_action('wp_ajax_mensio_ajax_Load_Country_Data', 'mensio_ajax_Load_Country_Data');
add_action('wp_ajax_mensio_ajax_Save_Country_Edit_Data', 'mensio_ajax_Save_Country_Edit_Data');
add_action('wp_ajax_mensio_ajax_Countries_Quick_Update', 'mensio_ajax_Countries_Quick_Update');
add_action('wp_ajax_mensio_ajax_Save_Country_Quick_Edit', 'mensio_ajax_Save_Country_Quick_Edit');
add_action('wp_ajax_mensio_ajax_Delete_Country_Data', 'mensio_ajax_Delete_Country_Data');
add_action('wp_ajax_mensio_ajax_Countries_Quick_Delete', 'mensio_ajax_Countries_Quick_Delete');
function Mensio_Admin_Settings_Countries() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Countries();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('System'));
    $Page->Set_CustomMenuItems('
              <div class="menu_button_row"> 
                <button id="BTN_Save_Header" class="button BtnGreen" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
                <button id="BTN_Back_Header" class="button" title="Back">
                  <i class="fa fa-arrow-left"></i>
                </button>
              </div>');
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">Countries<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
        <div id="ButtonArea">
          <button id="BTN_AddNew" title="Add New Country">
            <i class="fa fa-plus action-icon" aria-hidden="true"></i>
            Add New
          </button>
        </div>
        '.wp_nonce_field('Active_Page_Countries').'
        <div class="PageInfo">'.MENSIO_PAGEINFO_Countries.'</div>
        <div class="DivResizer"></div>
        <hr>
        <div id="DIV_Table">
          <div id="TBL_Countries_Wrapper" class="TBL_DataTable_Wrapper">
            '.$Page->GetDataTable().'
          </div>
        <div class="DivResizer"></div>
        </div>
        <div id="DIV_Edit">
          <div class="Mns_Tab_Wrapper">
            <div id="tabs">
              <ul>
                <li><a href="#tabs-1">Country Info</a></li>
                <li><a href="#tabs-2">Translations</a></li>
              </ul>
              <!-- TAB INFO START -->
              <div id="tabs-1" class="Mns_Tab_Container">
                <input type="hidden" id="FLD_Country">
                <label class="label_symbol">Continent</label>
                <select id="FLD_Continent" class="form-control">
                  '.$Page->GetOptions('Continent').'
                </select>
                <label class="label_symbol">ISO 2</label>
                <input type="text" id="FLD_iso2" class="form-control" >
                <label class="label_symbol">ISO 3</label>
                <input type="text" id="FLD_iso3" class="form-control">
                <label class="label_symbol">Domain</label>
                <input type="text" id="FLD_domain" class="form-control">
                <label class="label_symbol">IDP</label>
                <input type="text" id="FLD_idp" class="form-control">
                <label class="label_symbol">Currency</label>
                <select id="FLD_Currency" class="form-control">
                  '.$Page->GetOptions('Currency').'
                </select>
              <div class="DivResizer"></div>
              </div>
              <!-- TAB INFO END -->
              <!-- TAB TRANSLATION START -->
              <div id="tabs-2" class="Mns_Tab_Container">
                '.$Page->GetTranslationFields().'
              <div class="DivResizer"></div>
              </div>
              <!-- TAB TRANSLATION END -->
            <div class="DivResizer"></div>
            </div>
          <div class="DivResizer"></div>
          </div>
          <!-- Button Row Start -->
          <div class="button_row">
            <button id="BTN_Save" class="button BtnGreen" title="Save">
              <i class="fa fa-floppy-o" aria-hidden="true"></i>
            </button>
            <button id="BTN_Back" class="button" title="Back">
              <i class="fa fa-arrow-left"></i>
            </button> 
          </div>
          <!-- Button Row End -->
        <div class="DivResizer"></div>
        </div>'
    );  
    $Page->UpdatePage();
    $Page->SetActiveSubPage('System','Countries');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Countries() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Countries();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch);
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Load_Country_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Country = filter_var($_REQUEST['Country'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Countries();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadCountryData($Country);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Save_Country_Edit_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Code = filter_var($_REQUEST['Country'],FILTER_SANITIZE_STRING);
    $DataSet = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Settings_Countries();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SaveCountryData($Code,$DataSet);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Countries_Quick_Update() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Countries();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->Load_ModalQuickEdit();
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Save_Country_Quick_Edit() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Selected = filter_var($_REQUEST['Selected'],FILTER_SANITIZE_STRING);
    $Field = filter_var($_REQUEST['Field'],FILTER_SANITIZE_STRING);
    $Data = filter_var($_REQUEST['Data'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Countries();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateBulkCountriesData($Selected,$Field,$Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Delete_Country_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Country = filter_var($_REQUEST['Country'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Countries();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteCountryData($Country);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Countries_Quick_Delete() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Data = filter_var($_REQUEST['Data'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Countries();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteBulkCountriesData($Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}