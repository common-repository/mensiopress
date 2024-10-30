<?php
add_action('wp_ajax_mensio_ajax_Table_Continents', 'mensio_ajax_Table_Continents');
add_action('wp_ajax_mensio_ajax_Load_Continent_Translations', 'mensio_ajax_Load_Continent_Translations');
add_action('wp_ajax_mensio_ajax_Update_Continents_Translations', 'mensio_ajax_Update_Continents_Translations');
function Mensio_Admin_Settings_Continents() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Continents();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('System'));
    $Page->Set_CustomMenuItems(' <!-- Mensio_HeadBar -->
              <div class="menu_button_row"> 
                <button id="BTN_Save_Header" class="button BtnGreen" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
                <button id="BTN_Back_Header" class="button" title="Back">
                  <i class="fa fa-arrow-left"></i>
                </button>
              </div>');
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">Continents<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      <div id="ButtonArea">
        <button id="BTN_Edit" title="Edit Translations">
          <i class="fa fa-pencil action-icon" aria-hidden="true"></i>
          Edit
        </button>
      </div>
      '.wp_nonce_field('Active_Page_Continents').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_Continent.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Table">
        <div id="TBL_Continents_Wrapper" class="TBL_DataTable_Wrapper">
          '.$Page->GetDataTable().'
        </div>
      <div class="DivResizer"></div>
      </div>
      <div id="DIV_Edit">
        <div id="DIV_LangSelect" class="Mns_Language_Selector_Div">
          <div class="Mns_Language_Selector_Label">
            Language Selection
          <div class="DivResizer"></div>
          </div>
          <div class="Mns_Language_Selector_Btn_Div">
            '.$Page->GetLanguageButtons().'
          <div class="DivResizer"></div>
          </div>
        <div class="DivResizer"></div>
        </div>
        <div class="Mns_Language_Fields_Div">
          <div class="Mns_Language_Selector_Label">
            Translations
          <div class="DivResizer"></div>
          </div>
          <div id="DIV_FLD_Translations" class="Mns_Language_Translations">
            '.$Page->GetEditFields().'
          <div class="DivResizer"></div>
          </div>
        <div class="DivResizer"></div>
        </div>
        <div class="button_row">
          <button id="BTN_Save" class="button" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>
          <button id="BTN_Back" class="button" title="Back">
            <i class="fa fa-arrow-left"></i>
          </button> 
        <div class="DivResizer"></div>
        </div>
      <div class="DivResizer"></div>
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('System','Continent');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Continents() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Continents();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      if ($InSearch == '') {
        $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter);
      } else {
        $RtrnTable = $Page->LoadSearchResults($InPage, $InRows, $InSearch, $InSorter);
      }
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Load_Continent_Translations() {
  $Flds = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Language = filter_var($_REQUEST['Language'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Continents();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $Flds = $Page->GetEditFields($Language);
    }
    unset($Page);
  }
  echo $Flds;
  die();
}
function mensio_ajax_Update_Continents_Translations() {
  $RtrnMsg = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Language = filter_var($_REQUEST['Language'],FILTER_SANITIZE_STRING);
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Settings_Continents();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnMsg = $Page->UpdateContinentsData($Language,$Data);
    }
    unset($Page);
    $RtrnMsg = json_encode($RtrnMsg);
  }
  echo $RtrnMsg;
  die();
}