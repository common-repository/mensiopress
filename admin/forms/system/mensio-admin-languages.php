<?php
if ( ! defined( 'WPINC' ) ) { die; }
add_action('wp_ajax_mensio_ajax_Table_Languages', 'mensio_ajax_Table_Languages');
add_action('wp_ajax_mensio_ajax_Update_Language_Active', 'mensio_ajax_Update_Language_Active');
add_action('wp_ajax_mensio_ajax_Update_Language_Admin', 'mensio_ajax_Update_Language_Admin');
add_action('wp_ajax_mensio_ajax_Update_Language_Theme', 'mensio_ajax_Update_Language_Theme');
add_action('wp_ajax_mensio_ajax_Update_Language_Data', 'mensio_ajax_Update_Language_Data');
add_action('wp_ajax_mensio_ajax_Add_New_Language', 'mensio_ajax_Add_New_Language');
add_action('wp_ajax_mensio_ajax_Update_Language_Basic_Data', 'mensio_ajax_Update_Language_Basic_Data');
add_action('wp_ajax_mensio_ajax_Update_Language_Translations', 'mensio_ajax_Update_Language_Translations');
add_action('wp_ajax_mensio_ajax_Load_Flag_Icon_Form', 'mensio_ajax_Load_Flag_Icon_Form');
function Mensio_Admin_Settings_Languages(){
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Languages();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $RtrnTable = $Page->GetLanguageDataTable();
    $LangOptions = $Page->GetLanguageOptions();
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
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">Languages<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      <div id="ButtonArea">
        <button id="BTN_AddNew" title="Add New Language">
          <i class="fa fa-plus action-icon" aria-hidden="true"></i>
          Add New
        </button>
      </div>
      '.wp_nonce_field('Active_Page_Languages').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_Languages.'</div>
      <div class="DivResizer"></div>
      <hr>    
      <div id="DIV_LangTable" class="" data-toggle="tooltip" title="Add New Language">
        <div id="TBL_Languages_Wrapper" class="TBL_DataTable_Wrapper">
          '.$RtrnTable.'
        </div>
        <div class="DivResizer"></div>
      </div>
      <div id="DIV_Edit">
        <div id="DIV_LangEdit">
          <!-- ROW 1 START -->
          <div class="Mns_Languages_Main_Edit_Div">
            <div class="Mns_LangEditDiv">
              <label class="label_symbol">Select Language:</label>
              <select id="FLD_LangSelection" class="form-control">
               '.$LangOptions.'
              </select>
              <label class="label_symbol">Language code:</label>
              <input type="text" class="form-control" id="FLD_Code">
              <label class="label_symbol">Is Active:</label>
              <select id="FLD_Active" class="form-control FLD_YesNo_Selection">
                <option value="0">NO</option>
                <option value="1">YES</option>
              </select>
            </div>
            <div class="Mns_LangImgDiv">
              <label class="label_symbol">Icon</label>
              <div class="Mns_Img_Container">
                <img id="DispImg" class="selectIm" src="'.plugin_dir_url( __FILE__ ).'../../icons/default/empty.png" alt="language_icon">
              </div>
              <div class="Mns_ImgBtnDiv">
                <button id="Mns_OpenMediaModal" class="button Mns_Img_Btn" title="Open Image Selector">
                  <i class="fa fa-picture-o" aria-hidden="true"></i>
                </button>
                <button id="Mns_ClearImg" class="button Mns_Img_Btn" title="Clear Image">
                  <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
                <input type="hidden" id="DefImg" value="'.plugin_dir_url( __FILE__ ).'../../icons/default/empty.png"/>
                <input type="hidden" id="DefPath" value="'.plugin_dir_url( __FILE__ ).'../../icons/flags/"/>
                <input type="hidden" id="FLD_Icon" class="form-control" value=""/>
              </div>
            </div>
          <div class="DivResizer"></div>
          </div>
          <!-- ROW 1 END -->
          <!-- ROW 2 START -->
          <div class="Mns_Languages_Main_Edit_Div">
            <div class="Mns_ExtraFields">
              <label class="label_symbol">Translate to other Languages</label>
              <br>
              <div id="DIV_TransToOther" class="Mns_lang_forms"></div>
              <div class="DivResizer"></div>
            </div>
            <div class="Mns_ExtraFields">
              <label class="label_symbol">Translate other Languages to this</label>
              <br>
              <div id="DIV_TransToThis" class="Mns_lang_forms"></div>
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
          </div>
          <!-- ROW 2 END -->
        <div class="DivResizer"></div>
        </div>
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('System','Languages');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Languages() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Languages();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      if ($InSearch == '') {
        $RtrnTable = $Page->GetLanguageDataTable($InPage,$InRows,$InSorter);
      } else {
        $RtrnTable = $Page->LoadSearchResults($InPage, $InRows, $InSearch, $InSorter);
      }
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Update_Language_Active() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Language = filter_var($_REQUEST['Language'],FILTER_SANITIZE_STRING);
    $Active = filter_var($_REQUEST['Active'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Languages();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateActiveLanguage($Active,$Language);
    }
    unset ($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Language_Admin() {
  echo mensio_Update_Language('Admin');
  die();
}
function mensio_ajax_Update_Language_Theme() {
  echo mensio_Update_Language('Theme');
  die();
}
function mensio_Update_Language($Type) {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Language = filter_var($_REQUEST['Language'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Languages();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateMainLanguages($Type,$Language);
    }
    unset ($Page);
    $RtrnData = json_encode($RtrnData);
  }
  return $RtrnData;
}
function mensio_ajax_Update_Language_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Language = filter_var($_REQUEST['Language'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Languages();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->GetLanguageDetails($Language);
    }
    unset($Language);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Add_New_Language() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $ShortCode = filter_var($_REQUEST['ShortCode'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Languages();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->CreateNewLanguage($ShortCode);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Language_Basic_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Language = filter_var($_REQUEST['Language'],FILTER_SANITIZE_STRING);
    $ShortCode = filter_var($_REQUEST['ShortCode'],FILTER_SANITIZE_STRING);
    $Active = filter_var($_REQUEST['Active'],FILTER_SANITIZE_STRING);
    $Icon = filter_var($_REQUEST['Icon'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Languages();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateLanguage($Language,$ShortCode,$Active,$Icon);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Language_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Language = filter_var($_REQUEST['Language'],FILTER_SANITIZE_STRING);
    $ToLanguage = filter_var($_REQUEST['ToLanguage'],FILTER_SANITIZE_STRING);
    $Name = filter_var($_REQUEST['Name'],FILTER_SANITIZE_STRING);  
    $Page = new Mensio_Admin_Settings_Languages();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateTranslation($Language,$ToLanguage,$Name);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Flag_Icon_Form() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Image = filter_var($_REQUEST['Image'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Languages();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadFlagIconForm($Image);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}