<?php
add_action('wp_ajax_mensio_ajax_Table_Categories', 'mensio_ajax_Table_Categories');
add_action('wp_ajax_mensio_ajax_Products_Categories_Toggle_Visibility', 'mensio_ajax_Products_Categories_Toggle_Visibility');
add_action('wp_ajax_mensio_ajax_Products_Load_Categories_Data', 'mensio_ajax_Products_Load_Categories_Data');
add_action('wp_ajax_mensio_ajax_Products_Update_Categories_Data', 'mensio_ajax_Products_Update_Categories_Data');
add_action('wp_ajax_mensio_ajax_Products_Load_Category_Translations', 'mensio_ajax_Products_Load_Category_Translations');
add_action('wp_ajax_mensio_ajax_Products_Update_Category_Translations', 'mensio_ajax_Products_Update_Category_Translations');
add_action('wp_ajax_mensio_ajax_Products_Delete_Category_Data', 'mensio_ajax_Products_Delete_Category_Data');
add_action('wp_ajax_mensio_ajax_Products_Add_Category_Attribute', 'mensio_ajax_Products_Add_Category_Attribute');
add_action('wp_ajax_mensio_ajax_Products_Add_Category_Attribute_Value', 'mensio_ajax_Products_Add_Category_Attribute_Value');
add_action('wp_ajax_mensio_ajax_Products_Delete_Category_Attribute', 'mensio_ajax_Products_Delete_Category_Attribute');
add_action('wp_ajax_mensio_ajax_Update_Category_Attributes', 'mensio_ajax_Update_Category_Attributes');
add_action('wp_ajax_mensio_ajax_Products_Delete_Category_Attribute_Value', 'mensio_ajax_Products_Delete_Category_Attribute_Value');
add_action('wp_ajax_mensio_ajax_Check_If_Category_Name_Exists', 'mensio_ajax_Check_If_Category_Name_Exists');
add_action('wp_ajax_mensio_ajax_Attribute_Load_Translation', 'mensio_ajax_Attribute_Load_Translation');
add_action('wp_ajax_mensio_ajax_Products_Update_Attribute_Translations', 'mensio_ajax_Products_Update_Attribute_Translations');
function Mensio_Admin_Products_Categories() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Products_Categories_Form();
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
              </div>');
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">Product Categories<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      <div id="ButtonArea">
        <button id="BTN_AddNew" title="Add New Category">
          <i class="fa fa-plus action-icon" aria-hidden="true"></i>
          Add New
        </button>
      </div>
      '.wp_nonce_field('Active_Page_Categories').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_Categories.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Table">
        <div id="TBL_Categories_Wrapper" class="TBL_DataTable_Wrapper">
          '.$Page->GetDataTable().'
        </div>
      <div class="DivResizer"></div>
      </div>
      <div id="DIV_Edit">
        <div class="Mns_Edit_Wrapper">
          <div id="CatImgDiv">
            <label class="label_symbol">Category Image</label><br><br>
            <div class="DIV_Cur_Img">
              <div class="Mns_Img_Container">
                <img id="DispImg" class="selectIm" src="'.MENSIO_PATH.'/admin/icons/default/empty.png" alt="image">
              </div>
              <div class="ImgBtnDiv">
                <button id="Mns_OpenMediaModal" class="button Mns_Img_Btn" title="Open Image Selector">
                  <i class="fa fa-picture-o" aria-hidden="true"></i>
                </button>
                <button id="Mns_ClearImg" class="button Mns_Img_Btn" title="Clear Image">
                  <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
                <input id="FLD_Image" class="form-control" type="hidden" value="">
                <input type="hidden" id="DefaultImage" value="'.MENSIO_PATH.'/admin/icons/default/noimage.png">
              </div>
            </div>
          <div class="DivResizer"></div>
          </div>
          <div id="CatInputDiv">
            <input type="hidden" id="FLD_Category" value="" class="">
            <div id="CatNameDiv">
              <label class="label_symbol">Internal Name</label><br>
              <input type="text" id="FLD_Name" value="" class="form-control">
              <label class="label_symbol">Slug</label><br>
              <input type="text" id="FLD_Slug" value="" class="form-control">
              <div class="DivResizer"></div>
              <div id="CodeMsg">
                <p>Given name is allready in use. Please change the given internal name!</p>
              </div>
              <div id="TransList"></div>
              <div class="VisibileWrap">
                <label class="label_symbol">Visible</label>
                <input type="checkbox" id="FLD_Visibility" value="">
                <div class="DivResizer"></div>
              </div>
            <div class="DivResizer"></div>
            </div>
            <div class="CatAttrWrap">
              <div class="AttrListWrap">
                <div class="AttrLblDiv">
                  <input type="hidden" id="ActiveAttribute" value="">
                  <label class="label_symbol ChckLbl">Attributes</label>
                  <button id="BTN_AddAttr" class="button" title="Add New Attribute">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                  </button>
                <div class="DivResizer"></div>
                </div>
                <div class="DivResizer"></div>
                <div id="AttrListDiv"></div>
              <div class="DivResizer"></div>
              </div>
              <div id="AttrListDataWrap">
                <div class="AttrLblDiv">
                  <label class="label_symbol ChckLbl">Attribute Values</label>
                  <button id="BTN_AddAttrVal" class="button" title="Add New Attribute Value">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                  </button>
                <div class="DivResizer"></div>
                </div>
                <div class="DivResizer"></div>
                <div id="ValuesListDiv"></div>
              <div class="DivResizer"></div>
              </div>
            <div class="DivResizer"></div>
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
      </div>');
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Products','Categories');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Categories() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $JSONData = $_REQUEST['ExtraActions'];
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch,$JSONData);
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Products_Categories_Toggle_Visibility() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Data = filter_var($_REQUEST['Data'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->ToggleCategoriesVisibility($Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Load_Categories_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Category = filter_var($_REQUEST['Category'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadCategoryData($Category);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Update_Categories_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Category = filter_var($_REQUEST['Category'],FILTER_SANITIZE_STRING);
    $Name = filter_var($_REQUEST['Name'],FILTER_SANITIZE_STRING);
    $Slug = filter_var($_REQUEST['Slug'],FILTER_SANITIZE_STRING);
    $Image = filter_var($_REQUEST['Image'],FILTER_SANITIZE_STRING);
    $Visible = filter_var($_REQUEST['Visible'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateCategoryData($Category,$Name,$Slug,$Image,$Visible);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Load_Category_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Category = filter_var($_REQUEST['Category'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadModalTranslations($Category);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Update_Category_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Category = filter_var($_REQUEST['Category'],FILTER_SANITIZE_STRING);
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateCategoryTranslations($Category,$Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Delete_Category_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Category = filter_var($_REQUEST['Category'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteCategoryData($Category);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Add_Category_Attribute() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Category = filter_var($_REQUEST['Category'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->AddCategoryAttribute($Category);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Add_Category_Attribute_Value() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Attribute = filter_var($_REQUEST['Attribute'],FILTER_SANITIZE_STRING);
    $ValueID = filter_var($_REQUEST['ValueID'],FILTER_SANITIZE_STRING);
    $Name = filter_var($_REQUEST['Name'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->AddCategoryAttributeValue($Attribute,$ValueID,$Name);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Delete_Category_Attribute() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Category = filter_var($_REQUEST['Category'],FILTER_SANITIZE_STRING);
    $Attribute = filter_var($_REQUEST['Attribute'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteCategoryAttribute($Category,$Attribute);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Category_Attributes() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Category = filter_var($_REQUEST['Category'],FILTER_SANITIZE_STRING);
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateCategoryAttributes($Category,$Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Delete_Category_Attribute_Value() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Attribute = filter_var($_REQUEST['Attribute'],FILTER_SANITIZE_STRING);
    $Value = filter_var($_REQUEST['Value'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteCategoryAttributeValue($Attribute,$Value);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Check_If_Category_Name_Exists() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Category = filter_var($_REQUEST['Category'],FILTER_SANITIZE_STRING);
    $Name = filter_var($_REQUEST['Name'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->CheckIfCategoryNameExists($Category,$Name);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Attribute_Load_Translation() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Attribute = filter_var($_REQUEST['Attribute'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadAttributeTranslation($Attribute);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Update_Attribute_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Attribute = filter_var($_REQUEST['Attribute'],FILTER_SANITIZE_STRING);
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Products_Categories_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateAttributeTranslations($Attribute,$Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}