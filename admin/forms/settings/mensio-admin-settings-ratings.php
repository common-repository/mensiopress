<?php
add_action('wp_ajax_mensio_ajax_Table_Ratings', 'mensio_ajax_Table_Ratings');
add_action('wp_ajax_mensio_ajax_Load_Products_Ratings_Data', 'mensio_ajax_Load_Products_Ratings_Data');
add_action('wp_ajax_mensio_ajax_Updating_Rating_System_Data', 'mensio_ajax_Updating_Rating_System_Data');
add_action('wp_ajax_mensio_ajax_Delete_Products_Ratings_Data', 'mensio_ajax_Delete_Products_Ratings_Data');
add_action('wp_ajax_mensio_ajax_Delete_Products_Ratings_Selections', 'mensio_ajax_Delete_Products_Ratings_Selections');
add_action('wp_ajax_mensio_ajax_Update_Active_Rating_System', 'mensio_ajax_Update_Active_Rating_System');
function Mensio_Admin_Settings_Ratings() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Products_Ratings_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Settings'));
    $Page->Set_CustomMenuItems('
              <div class="menu_button_row"> 
                <button id="BTN_Delete_Header" class="button BtnRed" title="Delete">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
                <button id="BTN_Save_Header" class="button BtnGreen" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
                <button id="BTN_Back_Header" class="button" title="Back">
                  <i class="fa fa-arrow-left"></i>
                </button>
              </div>');
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">Product Ratings<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      <div id="ButtonArea">
        <button id="BTN_AddNew" title="Add New Rating System">
          <i class="fa fa-plus action-icon" aria-hidden="true"></i>
          Add New
        </button>
      </div>
      '.wp_nonce_field('Active_Page_Review_Ratings').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_Ratings.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Table">
        <div id="TBL_Ratings_Wrapper" class="TBL_DataTable_Wrapper">
          '.$Page->GetDataTable().'
        </div>
      <div class="DivResizer"></div>
      </div>
      <div id="DIV_Edit">
        <div class="Mns_Edit_Wrapper">
          <input type="hidden" id="FLD_Rating" value="" class="">
          <label class="label_symbol">Name</label>
          <input type="text" id="FLD_Name" value="" class="form-control">
          <label class="label_symbol">Minimum Value</label>
          <input type="number" id="FLD_Min" value="" class="form-control">
          <label class="label_symbol">Maximum Value</label>
          <input type="number" id="FLD_Max" value="" class="form-control">
          <label class="label_symbol">Step</label>
          <input type="number" id="FLD_Step" value="" class="form-control">
          <label class="label_symbol">Starting Value</label>
          <input type="number" id="FLD_Start" value="" class="form-control">
          <label class="label_symbol">Set as Active</label>
          <select id="FLD_Active" class="form-control">
            <option value="0">No</option>
            <option value="1">Yes</option>
          </select>
          <div class="DIV_Cur_Img">
            <label class="label_symbol">Image Icon</label>
            <div class="Mns_Img_Container">
              <img id="DispImg" class="selectIm" src="'.plugins_url('mensiopress/admin/icons/default/empty.png').'" alt="rating_icon">
            </div>
            <div class="">
              <button id="Mns_OpenMediaModal" class="button Mns_Img_Btn" title="Open Image Selector">
                <i class="fa fa-picture-o" aria-hidden="true"></i>
              </button>
              <button id="Mns_ClearImg" class="button Mns_Img_Btn" title="Clear Image">
                <i class="fa fa-trash" aria-hidden="true"></i>
              </button>
              <input id="FLD_Icon" class="form-control" type="hidden" value="">
            </div>
          </div>
          <div class="button_row">
            <button id="BTN_Delete" class="button BtnRed" title="Delete">
              <i class="fa fa-floppy-o" aria-hidden="true"></i>
            </button>
            <button id="BTN_Save" class="button BtnGreen" title="Save">
              <i class="fa fa-floppy-o" aria-hidden="true"></i>
            </button>
            <button id="BTN_Back" class="button" title="Back">
              <i class="fa fa-arrow-left"></i>
            </button> 
          </div>
        <div class="DivResizer"></div>
        </div>
      </div>');
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Settings','Ratings');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Ratings() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Ratings_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch);
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Load_Products_Ratings_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Rating = filter_var($_REQUEST['Rating'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Ratings_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductRatingSystemData($Rating);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Updating_Rating_System_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Rating = filter_var($_REQUEST['Rating'],FILTER_SANITIZE_STRING);
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Products_Ratings_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateProductRatingSystemData($Rating,$Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Delete_Products_Ratings_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Rating = filter_var($_REQUEST['Rating'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Ratings_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteProductRatingSystemData($Rating);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Delete_Products_Ratings_Selections() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Selections = filter_var($_REQUEST['Selections'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Ratings_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteProductRatingSelections($Selections);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Active_Rating_System() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Courier = filter_var($_REQUEST['Courier'],FILTER_SANITIZE_STRING);
    $Active = filter_var($_REQUEST['Active'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Ratings_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateActiveRatingSystem($Courier,$Active);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}