<?php
add_action('wp_ajax_mensio_ajax_Table_Brands', 'mensio_ajax_Table_Brands');
add_action('wp_ajax_mensio_ajax_Load_Products_Brands_Data', 'mensio_ajax_Load_Products_Brands_Data');
add_action('wp_ajax_mensio_ajax_Updating_Brand_System_Data', 'mensio_ajax_Updating_Brand_System_Data');
add_action('wp_ajax_mensio_ajax_Delete_Products_Brands_Data', 'mensio_ajax_Delete_Products_Brands_Data');
add_action('wp_ajax_mensio_ajax_Delete_Products_Brands_Selections', 'mensio_ajax_Delete_Products_Brands_Selections');
add_action('wp_ajax_mensio_ajax_Check_If_Brand_Name_Exist', 'mensio_ajax_Check_If_Brand_Name_Exist');
add_action('wp_ajax_mensio_ajax_Products_Brands_Visble', 'mensio_ajax_Products_Brands_Visble');
add_action('wp_ajax_mensio_ajax_Products_Brands_Hidden', 'mensio_ajax_Products_Brands_Hidden');
function Mensio_Admin_Products_Brands() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Products_Brands_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Products'));
    $Page->Set_CustomMenuItems('
              <div class="menu_button_row">
                <button id="BTN_Delete_Header" class="button BtnRed" title="Delete">
                  <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
                <button id="BTN_Save_Header" class="button BtnGreen" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
                <button id="BTN_Back_Header" class="button" title="Back">
                  <i class="fa fa-arrow-left"></i>
                </button>
              </div>') ;
    ob_start();
    wp_editor('','FLD_Notes');
    $Editor = ob_get_clean();
    $Page->Set_MainPlaceHolder(
     '<h1 class="Mns_Page_HeadLine">Product Brands<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      <div id="ButtonArea">
        <button id="BTN_AddNew" title="Add New Brand">
          <i class="fa fa-plus action-icon" aria-hidden="true"></i>
          Add New
        </button>
      </div>
      '.wp_nonce_field('Active_Page_Brands').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_Brands.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Table">
        <div id="TBL_Brands_Wrapper" class="TBL_DataTable_Wrapper">
          '.$Page->GetDataTable().'
        </div>
      <div class="DivResizer"></div>
      </div>
      <div id="DIV_Edit">
        <div class="Mns_Edit_Wrapper">
          <div id="BrandsLogoDiv">
            <label class="label_symbol">Logo</label>
            <div class="DIV_Cur_Img">
              <div class="Mns_Img_Container">
                <img id="DispImg" class="selectIm" src="'.MENSIO_PATH.'/admin/icons/default/empty.png" alt="image">
              </div>
              <div class="">
                <button id="Mns_OpenMediaModal" class="button Mns_Img_Btn" title="Open Image Selector">
                  <i class="fa fa-picture-o" aria-hidden="true"></i>
                </button>
                <button id="Mns_ClearImg" class="button Mns_Img_Btn" title="Clear Image">
                  <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
                <input id="FLD_Logo" class="form-control" type="hidden" value="">
                <input type="hidden" id="DefaultImage" value="'.MENSIO_PATH.'/admin/icons/default/noimage.png">
              </div>
            </div>
          </div>
          <div id="BrandsInputsDiv">
            <input type="hidden" id="FLD_Brand" value="" class="">
            <label class="label_symbol">Name</label>
            <input type="text" id="FLD_Name" value="" class="form-control">
            <label class="label_symbol">Slug</label>
            <input type="text" id="FLD_Slug" value="" class="form-control">
            <div id="NameMsg">
                <p>Name is allready in use. Please change the Brand Name!</p>
            </div>
            <label class="label_symbol">Web Page</label>
            <input type="text" id="FLD_WebPage" value="" class="form-control">
            <label class="label_symbol">Visible</label>
            <select id="FLD_Visible" class="form-control">
              <option value="0">NO</option>
              <option value="1">YES</option>
            </select>
            <div class="DivResizer"></div>
            <div class="ColorWraper">
              <label class="label_symbol">Brand Color</label>
              <input type="text" id="FLD_Color" value="" class="form-control">
            </div>
          </div>
          <div id="BrandsNotes">
            <div id="NotesCtrl">
              '.$Page->LoadLanguageButtons().'
            </div>
            <div id="NotesEditor">
              '.$Editor.'
            </div>
            <div class="DivResizer"></div>
          </div>
          <div class="button_row">
            <button id="BTN_Delete" class="button BtnRed" title="Delete">
              <i class="fa fa-trash" aria-hidden="true"></i>
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
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Products','Brands');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Brands() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Brands_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch);
    }  
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Load_Products_Brands_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Page = new Mensio_Admin_Products_Brands_Form();
    $Brand = filter_var($_REQUEST['Brand'],FILTER_SANITIZE_STRING);
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductBrandData($Brand);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Updating_Brand_System_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Brand = filter_var($_REQUEST['Brand'],FILTER_SANITIZE_STRING);
    $Name = filter_var($_REQUEST['Name'],FILTER_SANITIZE_STRING);
    $Slug = filter_var($_REQUEST['Slug'],FILTER_SANITIZE_STRING);
    $WebPage = filter_var($_REQUEST['WebPage'],FILTER_SANITIZE_STRING);
    $Vsbl = filter_var($_REQUEST['Visible'],FILTER_SANITIZE_STRING);
    $Logo = filter_var($_REQUEST['Logo'],FILTER_SANITIZE_STRING);
    $Color = $_REQUEST['Color'];
    $Notes = $_REQUEST['Notes'];
    $Page = new Mensio_Admin_Products_Brands_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateProductBrandData($Brand,$Name,$Slug,$WebPage,$Color,$Vsbl,$Logo,$Notes);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Delete_Products_Brands_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Brand = filter_var($_REQUEST['Brand'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Brands_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteProductBrandRecord($Brand);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Delete_Products_Brands_Selections() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Selections = filter_var($_REQUEST['Selections'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Brands_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteProductBrandSelections($Selections);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Check_If_Brand_Name_Exist() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Brand = filter_var($_REQUEST['Brand'],FILTER_SANITIZE_STRING);
    $Name = filter_var($_REQUEST['Name'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Brands_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->CheckIfBrandNameExist($Brand,$Name);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Brands_Visble() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Selections = filter_var($_REQUEST['Selections'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Brands_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateProductBrandVisibility($Selections);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Brands_Hidden() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {  
    $Selections = filter_var($_REQUEST['Selections'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Brands_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateProductBrandVisibility($Selections,false);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}