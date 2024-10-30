<?php
add_action('wp_ajax_mensio_ajax_Table_Products', 'mensio_ajax_Table_Products');
add_action('wp_ajax_mensio_ajax_Load_Product_Type_Selector', 'mensio_ajax_Load_Product_Type_Selector');
add_action('wp_ajax_mensio_ajax_Load_Product_Data', 'mensio_ajax_Load_Product_Data');
add_action('wp_ajax_mensio_ajax_Load_Product_Translations', 'mensio_ajax_Load_Product_Translations');
add_action('wp_ajax_mensio_ajax_Load_Product_New_Status', 'mensio_ajax_Load_Product_New_Status');
add_action('wp_ajax_mensio_ajax_Add_Product_Status', 'mensio_ajax_Add_Product_Status');
add_action('wp_ajax_mensio_ajax_Remove_Product_Status', 'mensio_ajax_Remove_Product_Status');
add_action('wp_ajax_mensio_ajax_Save_Product_Data', 'mensio_ajax_Save_Product_Data');
add_action('wp_ajax_mensio_ajax_Load_Modal_Product_Category_Selector', 'mensio_ajax_Load_Modal_Product_Category_Selector');
add_action('wp_ajax_mensio_ajax_Product_Add_Category', 'mensio_ajax_Product_Add_Category');
add_action('wp_ajax_mensio_ajax_Product_Add_Value', 'mensio_ajax_Product_Add_Value');
add_action('wp_ajax_mensio_ajax_Product_Remove_Category', 'mensio_ajax_Product_Remove_Category');
add_action('wp_ajax_mensio_ajax_Product_Update_Value_Visibility', 'mensio_ajax_Product_Update_Value_Visibility');
add_action('wp_ajax_mensio_ajax_Product_Remove_Value', 'mensio_ajax_Product_Remove_Value');
add_action('wp_ajax_mensio_ajax_Update_Product_Image_List', 'mensio_ajax_Update_Product_Image_List');
add_action('wp_ajax_mensio_ajax_Product_Update_Main_Image', 'mensio_ajax_Product_Update_Main_Image');
add_action('wp_ajax_mensio_ajax_Product_Remove_Image', 'mensio_ajax_Product_Remove_Image');
add_action('wp_ajax_mensio_ajax_Modal_Product_Advanteges_Form', 'mensio_ajax_Modal_Product_Advanteges_Form');
add_action('wp_ajax_mensio_ajax_Load_Advanteges_Translations', 'mensio_ajax_Load_Advanteges_Translations');
add_action('wp_ajax_mensio_ajax_Add_Product_Advantages', 'mensio_ajax_Add_Product_Advantages');
add_action('wp_ajax_mensio_ajax_Remove_Product_Advantage', 'mensio_ajax_Remove_Product_Advantage');
add_action('wp_ajax_mensio_ajax_Modal_Product_Tags_Form', 'mensio_ajax_Modal_Product_Tags_Form');
add_action('wp_ajax_mensio_ajax_Update_Product_Tags', 'mensio_ajax_Update_Product_Tags');
add_action('wp_ajax_mensio_ajax_Remove_Product_Tags', 'mensio_ajax_Remove_Product_Tags');
add_action('wp_ajax_mensio_ajax_Modal_Product_Barcodes_Form', 'mensio_ajax_Modal_Product_Barcodes_Form');
add_action('wp_ajax_mensio_ajax_Product_Add_Barcode_Type', 'mensio_ajax_Product_Add_Barcode_Type');
add_action('wp_ajax_mensio_ajax_Product_Remove_Barcode_Type', 'mensio_ajax_Product_Remove_Barcode_Type');
add_action('wp_ajax_mensio_ajax_Product_Set_Public_Barcode', 'mensio_ajax_Product_Set_Public_Barcode');
add_action('wp_ajax_mensio_ajax_Add_Product_Barcode', 'mensio_ajax_Add_Product_Barcode');
add_action('wp_ajax_mensio_ajax_Remove_Product_Barcode', 'mensio_ajax_Remove_Product_Barcode');
add_action('wp_ajax_mensio_ajax_Update_Product_File_List', 'mensio_ajax_Update_Product_File_List');
add_action('wp_ajax_mensio_ajax_Remove_Product_File', 'mensio_ajax_Remove_Product_File');
add_action('wp_ajax_mensio_ajax_Load_Product_FIle_Expiration', 'mensio_ajax_Load_Product_FIle_Expiration');
add_action('wp_ajax_mensio_ajax_Update_Product_File_Expiration', 'mensio_ajax_Update_Product_File_Expiration');
add_action('wp_ajax_mensio_ajax_Load_Add_To_Bundle_Form', 'mensio_ajax_Load_Add_To_Bundle_Form');
add_action('wp_ajax_mensio_ajax_Search_Product_For_Bundle', 'mensio_ajax_Search_Product_For_Bundle');
add_action('wp_ajax_mensio_ajax_Add_Product_To_Bundle', 'mensio_ajax_Add_Product_To_Bundle');
add_action('wp_ajax_mensio_ajax_Remove_Product_From_Bundle', 'mensio_ajax_Remove_Product_From_Bundle');
add_action('wp_ajax_mensio_ajax_Update_Product_Visibility', 'mensio_ajax_Update_Product_Visibility');
add_action('wp_ajax_mensio_ajax_Delete_Product_Data', 'mensio_ajax_Delete_Product_Data');
add_action('wp_ajax_mensio_ajax_Check_If_Product_Code_Exists', 'mensio_ajax_Check_If_Product_Code_Exists');
add_action('wp_ajax_mensio_ajax_Product_Filter_Category_Attribute', 'mensio_ajax_Product_Filter_Category_Attribute');
add_action('wp_ajax_mensio_ajax_Product_ShowAll_Attribute_Values', 'mensio_ajax_Product_ShowAll_Attribute_Values');
add_action('wp_ajax_mensio_ajax_Product_Load_Category_Attribute_Values', 'mensio_ajax_Product_Load_Category_Attribute_Values');
add_action('wp_ajax_mensio_ajax_Product_Search_Category_Attribute_Value', 'mensio_ajax_Product_Search_Category_Attribute_Value');
add_action('wp_ajax_mensio_Show_Product_Reviews_Modal', 'mensio_Show_Product_Reviews_Modal');
add_action('wp_ajax_mensio_ajax_Product_Load_Status_Translations', 'mensio_ajax_Product_Load_Status_Translations');
add_action('wp_ajax_mensio_ajax_Products_Update_Status_Translations', 'mensio_ajax_Products_Update_Status_Translations');
add_action('wp_ajax_mensio_ajax_Load_Variation_Translation', 'mensio_ajax_Load_Variation_Translation');
add_action('wp_ajax_mensio_ajax_Products_Update_Variation_Translations', 'mensio_ajax_Products_Update_Variation_Translations');
add_action('wp_ajax_mensio_ajax_Load_Stock_Status_Modal', 'mensio_ajax_Load_Stock_Status_Modal');
add_action('wp_ajax_mensio_ajax_Refresh_Product_Stock_Status_Table', 'mensio_ajax_Refresh_Product_Stock_Status_Table');
add_action('wp_ajax_mensio_ajax_Remove_Product_Stock_Status', 'mensio_ajax_Remove_Product_Stock_Status');
add_action('wp_ajax_mensio_ajax_Product_Load_Stock_Status_Translations', 'mensio_ajax_Product_Load_Stock_Status_Translations');
add_action('wp_ajax_mensio_ajax_Product_Load_Stock_Status_Copy_Form', 'mensio_ajax_Product_Load_Stock_Status_Copy_Form');
add_action('wp_ajax_mensio_ajax_Product_Search_Stock_Status', 'mensio_ajax_Product_Search_Stock_Status');
add_action('wp_ajax_mensio_ajax_Product_Copy_Stock_Status', 'mensio_ajax_Product_Copy_Stock_Status');
function Mensio_Admin_Products() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Products_Form();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Products'));
    $Page->Set_CustomMenuItems('
              <div class="menu_button_row">
                <span id="HdBarBtnWrap">
                  <button class="button BtnRed BTN_Delete" title="Delete">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                  </button>
                  <button class="button BtnGreen BTN_Save" title="Save">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                  </button>
                </span>
                <button class="button BTN_Back" title="Back">
                  <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </button>
              </div>') ;
    ob_start();
    wp_editor('','FLD_ProNotes');
    $FLDNotes = ob_get_clean();  
    $BrandsOptions = str_replace('<option value="0" selected>All Brands</option>', '', $Page->LoadBrandOptions());
    $StatusOptions = str_replace('<option value="0" selected>All Status</option>', '', $Page->LoadStatusOptions());
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">Product Catalogue<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      <div id="ButtonArea">
        <button id="BTN_AddNew" title="Add New Product">
          <i class="fa fa-plus action-icon" aria-hidden="true"></i>
          Add New
        </button>
      </div>
      '.wp_nonce_field('Active_Page_Products').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_Catalogue.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Table">
        <div id="TBL_Products_Wrapper" class="TBL_DataTable_Wrapper">
          '.$Page->GetDataTable().'
        </div>
      <div class="DivResizer"></div>
      </div>
      <div id="DIV_Edit">
        <div class="Mns_Edit_Wrapper">
          <div id="tabs">
            <ul>
              <li><a href="#InfoDiv">
                <i class="fa fa-info" aria-hidden="true"></i>
                General
              </a></li>
              <li><a href="#PricesDiv">
                <i class="fa fa-dollar" aria-hidden="true"></i>
                Prices
              </a></li>
              <li><a href="#InventoryDiv">
                <i class="fa fa-archive" aria-hidden="true"></i>
                Inventory
              </a></li>
              <li><a href="#CategoriesDiv">
                <i class="fa fa-th-list" aria-hidden="true"></i>
                Properties
              </a></li>
              <li><a href="#ImagesDiv">
                <i class="fa fa-picture-o" aria-hidden="true"></i>
                Images
              </a></li>
              <li><a href="#TagsDiv">
                <i class="fa fa-tags" aria-hidden="true"></i>
                Tags
              </a></li>
              <li><a href="#FilesDiv">
                <i class="fa fa-database" aria-hidden="true"></i>
                Files
              </a></li>
            </ul>
            <!--  INFO DIV  -->
            <div id="InfoDiv" class="ProductTab">
              <label class="label_symbol">
                <i class="fa fa-archive fa-lg" aria-hidden="true"></i>
                Product Data
              </label>
              <div class="DnldAlertWrapper"></div>
              <hr>
              <input type="hidden" id="MainLang" value="'.$Page->LoadMainLang().'">
              <input type="hidden" id="EnLang" value="'.$Page->LoadEnglishLang().'">
              <input type="hidden" id="FLD_Language" value="">
              <input type="hidden" id="FLD_Product" value="" class="form-control">
              <label class="label_symbol">Code</label>
              <input type="text" id="FLD_Code" value="" class="form-control">
              <div id="CodeMsg">
                <p>Code is allready in use. Please change the product code!</p>
              </div>
              <label class="label_symbol">Brand</label>
              <select id="FLD_Brand" class="form-control">
                '.$BrandsOptions.'
              </select>
              <div class="DivResizer"></div>
              <div class="ProdExtraWrapper">
                <div class="ProdSubSelection">
                  <label class="label_symbol">Visible</label>
                  <input type="checkbox" id="FLD_Visible" value="" class="form-control form-check">
                </div>
                <div class="ProdSubSelection">
                  <label class="label_symbol">Reviewable</label>
                  <input type="checkbox" id="FLD_Reviewable" value="" class="form-control form-check">
                </div>
              </div>
              <div class="DivResizer"></div>
              <br>
              <div id="StatusController" class="SwitchArea">
                <div class="SwitchAreaCtrl">
                  <label class="label_symbol">Availablility Status</label>
                  <label class="switch">
                    <input id="FLD_StockRelated" type="checkbox" value="0">
                    <span id="SLD_StockRelated" class="slider"></span>
                  </label>
                  <span class="Stocklbl">Stock Related</span>
                <div class="DivResizer"></div>
                </div> 
                <div id="GenericStatusTab" class="StatusTag">
                  <div class="BtnDivWrapper">
                    <div id="BTN_AddNewStatus" class="BtnSwMdl" title="Add New Status">
                      <i class="fa fa-thermometer-full" aria-hidden="true"></i>
                    </div>
                  </div>
                  <div class="InputDiv">
                    <select id="FLD_Status" class="form-control">
                      '.$StatusOptions.'
                    </select>
                  </div>
                </div>
                <div id="StockStatusTab" class="StatusTag">
                  <input type="hidden" id="FLD_StockStatus" value="" class="form-control">
                  <table id="Tbl_StockStatus" class="ProductSubTable">
                    <thead>
                      <tr>
                        <th class="ModalStatusCtrlColBig">
                          <div id="BTN_AddNewStockStatus" class="StatusHeaderCol" title="Add New Stock Status">
                            <i class="fa fa-plus fa-lg" aria-hidden="true"></i>
                          </div>
                          <div id="BTN_CopyStockStatus" class="StatusHeaderCol" title="Copy Existing Stock Status">
                            <i class="fa fa-clone" aria-hidden="true"></i>
                          </div>
                          <div class="DivResizer"></div>
                        </th>
                        <th class="ModalStatusIcon">Icon</th>
                        <th class="ModalStatusIcon">Color</th>
                        <th class="ModalStatusIcon">Stock</th>
                        <th>Description</th>
                      </tr>
                    </thead>
                    <tbody id="Tbl_StockStatus_Body"></tbody>
                  </table>
                </div>
                <div class="DivResizer"></div>
              </div>
              <div class="DivResizer"></div>
              <div class="DivSep"></div>
              <label class="label_symbol">
                <i class="fa fa-info fa-lg" aria-hidden="true"></i>
                Description
              </label>
              <hr>        
              <div class="InfoLangSelectDiv">
                <div class="InfoLangWrapper">
                  '.$Page->LoadLanguageButtons('Info').'
                </div>
              <div class="DivResizer"></div>
              </div>
              <div class="InfoLangEditDiv">
                <label class="label_symbol">Name</label>
                <input type="text" id="FLD_Name" value="" class="ex-form-control">
                <label class="label_symbol">Slug</label>
                <input type="text" id="FLD_Slug" value="" class="form-control">
                <label class="label_symbol">Short Description</label>
                <textarea id="FLD_Description" class="ex-form-control"></textarea>
                '.$FLDNotes.'
              <div class="DivResizer"></div>
              </div>
              <div class="button_row">
                <button class="button BtnRed BTN_Delete" title="Delete">
                  <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
                <button class="button BtnGreen BTN_Save" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
                <button class="button BTN_Back" title="Back">
                  <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </button>
              </div>
            <div class="DivResizer"></div>
            </div>
            <!--  PRICES DIV  -->
            <div id="PricesDiv" class="ProductTab">
              <div class="BDWrapper">
                <label class="label_symbol">
                  <i class="fa fa-dollar fa-lg" aria-hidden="true"></i>
                  Prices
                </label>
                <hr>
                <label class="label_symbol">Business Price</label>
                <input type="text" id="FLD_BtBPrice" value="" class="form-control">
                <label class="label_symbol">Business Tax (%)</label>
                <input type="text" id="FLD_BtBTax" value="" class="form-control">
                <label class="label_symbol">Retail Price</label>
                <input type="text" id="FLD_Price" value="" class="form-control">
                <label class="label_symbol">Retail Tax (%)</label>
                <input type="text" id="FLD_Tax" value="" class="form-control">
                <label class="label_symbol">Discount (%)</label>
                <input type="text" id="FLD_Discount" value="" class="form-control">
              </div>
              <div class="button_row">
                <button class="button BtnRed BTN_Delete" title="Delete">
                  <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
                <button class="button BtnGreen BTN_Save" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
                <button class="button BTN_Back" title="Back">
                  <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </button>
              </div>
            <div class="DivResizer"></div>
            </div>
            <!--  INVENTORY DIV  -->
            <div id="InventoryDiv" class="ProductTab">
              <div class="BDWrapper">
                <label class="label_symbol">
                  <i class="fa fa-shopping-cart fa-lg" aria-hidden="true"></i>
                  Inventory
                </label>
                <hr>
                <label class="label_symbol">Stock</label>
                <input type="text" id="FLD_Stock" value="" class="form-control">
                <label class="label_symbol">Minimum Stock</label>
                <input type="text" id="FLD_MinStock" value="" class="form-control">
                <label class="label_symbol">Accept Orders When No Stock Available</label>
                <select id="FLD_Overstock" class="form-control">
                  <option value="0">No</option>
                  <option value="1">Yes</option>
                </select>
                <div class="DivResizer"></div>
                <div class="DivSep"></div>
                <label class="label_symbol">
                  <i class="fa fa-calendar fa-lg" aria-hidden="true"></i>
                  Availability
                </label>
                <hr>
                <label class="label_symbol">Available</label>
                <input type="text" id="FLD_Available" value="" class="form-control">
              </div>
              <div class="button_row">
                <button class="button BtnRed BTN_Delete" title="Delete">
                  <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
                <button class="button BtnGreen BTN_Save" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
                <button class="button BTN_Back" title="Back">
                  <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </button>
              </div>
            <div class="DivResizer"></div>
            </div>
            <!--  CATEGORIES DIV  -->
            <div id="CategoriesDiv" class="ProductTab">
              <div id="ProductCategoriesWrap"></div>
              <div class="button_row">
                <button class="button BTN_Back" title="Back">
                  <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </button>
              </div>
            <div class="DivResizer"></div>
            </div>
            <!--  IMAGES DIV  -->
            <div id="ImagesDiv" class="ProductTab">
              <label class="label_symbol">
                <i class="fa fa-picture-o fa-lg" aria-hidden="true"></i>
                Images
              </label>
              <div class="ExtraSelectionsDiv">
                <div id="BTN_OpenMediaModal" class="ESBtnsDivs" title="Open Image Selector">
                  <div class="ESBtns">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
              <hr>
              <div class="BDIImages"></div>
              <div class="button_row">
                <button class="button BTN_Back" title="Back">
                  <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </button>
              </div>
            <div class="DivResizer"></div>
            </div>
            <!--  TAGS DIV  -->
            <div id="TagsDiv" class="ProductTab">
              <label class="label_symbol">
                <i class="fa fa-tags fa-lg" aria-hidden="true"></i>
                Tags
              </label>
              <div class="ExtraSelectionsDiv">
                <div id="BTN_TagsModal" class="ESBtnsDivs" title="Add Product Tag">
                  <div class="ESBtns">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
              <hr>
              <div id="InfoTagsList" class="">
              </div>
              <div class="button_row">
                <button class="button BTN_Back" title="Back">
                  <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </button>
              </div>
            <div class="DivResizer"></div>
            </div>
            <!--  FILES DIV  -->
            <div id="FilesDiv" class="ProductTab">
              <label class="label_symbol">
                <i class="fa fa-database fa-lg" aria-hidden="true"></i>
                Files
              </label>
              <div class="ExtraSelectionsDiv">
                <div id="application" class="ESBtnsDivs PrdExtraUploads" title="Add Product File">
                  <div class="ESBtns">
                    <i class="fa fa-file-text" aria-hidden="true"></i>
                  </div>
                </div>
                <div id="video" class="ESBtnsDivs PrdExtraUploads" title="Add Product Video">
                  <div class="ESBtns">
                    <i class="fa fa-video-camera" aria-hidden="true"></i>
                  </div>
                </div>
                <div id="audio" class="ESBtnsDivs PrdExtraUploads" title="Add Product Audio">
                  <div class="ESBtns">
                    <i class="fa fa-headphones" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
              <hr>
              <div id="ProductFilesList"></div>
              <div class="button_row">
                <button class="button BTN_Back" title="Back">
                  <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </button>
              </div>
            <div class="DivResizer"></div>
            </div>
          </div>
        <div class="DivResizer"></div>
        </div>
      <div class="DivResizer"></div>
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Products','Catalogue');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Products() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $JSONData = $_REQUEST['ExtraActions'];
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch,$JSONData);
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Load_Product_Type_Selector() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductTypeSelectorForm();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Product_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductData($Product);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Product_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Language = filter_var($_REQUEST['Language'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductTranslations($Product,$Language);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Product_New_Status() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductAddStatusForm();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Load_Status_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Status = filter_var($_REQUEST['Status'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadModalStatusTranslations($Status);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Add_Product_Status() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Status = filter_var($_REQUEST['Status'],FILTER_SANITIZE_STRING);
    $Name = filter_var($_REQUEST['Name'],FILTER_SANITIZE_STRING);
    $Icon = filter_var($_REQUEST['Icon'],FILTER_SANITIZE_STRING);
    $Color = filter_var($_REQUEST['Color'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->AddProductStatus($Status,$Name,$Icon,$Color);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Remove_Product_Status() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Status = filter_var($_REQUEST['Status'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RemoveProductStatus($Status);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Save_Product_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Data = $_REQUEST['Data'];
    $Trans = $_REQUEST['Translations'];
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateProductData($Data,$Trans);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Modal_Product_Category_Selector() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductCategorySelectorForm($Product);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Add_Category() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Category = filter_var($_REQUEST['Category'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->AddCategoryToProduct($Product,$Category);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Remove_Category() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Category = filter_var($_REQUEST['Category'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RemoveCategoryFromProduct($Product,$Category);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Add_Value() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Value = filter_var($_REQUEST['Value'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->AddValueToProduct($Product,$Value);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Update_Value_Visibility() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Value = filter_var($_REQUEST['Value'],FILTER_SANITIZE_STRING);
    $Visibility = filter_var($_REQUEST['Visibility'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateValueVisibility($Product,$Value,$Visibility);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Remove_Value() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Value = filter_var($_REQUEST['Value'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->ProductRemoveValue($Product,$Value);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Product_Image_List() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $ImgList = $_REQUEST['DataList'];
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateProductImageList($Product,$ImgList);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Update_Main_Image() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Image = filter_var($_REQUEST['Image'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->ProductUpdateMainImage($Product,$Image);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Remove_Image() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Image = filter_var($_REQUEST['Image'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->ProductRemoveImage($Product,$Image);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Modal_Product_Advanteges_Form() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductAdvantagesForm();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Advanteges_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Language = filter_var($_REQUEST['Language'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadAdvantagesList($Product,$Language);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Add_Product_Advantages() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Language = filter_var($_REQUEST['Language'],FILTER_SANITIZE_STRING);
    $Advantage = filter_var($_REQUEST['Advantage'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->AddProductAdvantages($Product,$Language,$Advantage);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Remove_Product_Advantage() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Language = filter_var($_REQUEST['Language'],FILTER_SANITIZE_STRING);
    $Advantage = filter_var($_REQUEST['Advantage'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RemoveProductAdvantage($Product,$Language,$Advantage);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Modal_Product_Tags_Form() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Tag = filter_var($_REQUEST['Tag'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductTagsForm($Tag);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Product_Tags() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Tag = filter_var($_REQUEST['Tag'],FILTER_SANITIZE_STRING);
    $Text = filter_var($_REQUEST['Text'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateProductTags($Product,$Tag,$Text);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Remove_Product_Tags() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Tag = filter_var($_REQUEST['Tag'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RemoveProductTag($Product,$Tag);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Modal_Product_Barcodes_Form() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductBarcodesForm();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Add_Barcode_Type() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $BCType = filter_var($_REQUEST['BCType'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->AddBarcodeType($BCType);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Remove_Barcode_Type() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $BCType = filter_var($_REQUEST['BCType'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RemoveBarcodeType($BCType);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Set_Public_Barcode() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $BCType = filter_var($_REQUEST['BCType'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SetProductPublicBarcode($BCType);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Add_Product_Barcode() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $Barcode = filter_var($_REQUEST['Barcode'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->AddProductBarcode($Product,$Type,$Barcode);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Remove_Product_Barcode() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Barcode = filter_var($_REQUEST['Barcode'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RemoveProductBarcode($Product,$Barcode);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Product_File_List() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $FileList = $_REQUEST['DataList'];
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateProductFileList($Product,$Type,$FileList);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Product_FIle_Expiration() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $File = filter_var($_REQUEST['File'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductFIleExpirationForm($File);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Remove_Product_File() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $File = filter_var($_REQUEST['File'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RemoveProductFile($Product,$File);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Product_File_Expiration() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $File = filter_var($_REQUEST['File'],FILTER_SANITIZE_STRING);
    $Expiration = filter_var($_REQUEST['Expiration'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateProductFileExpiration($Product,$File,$Expiration);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Add_To_Bundle_Form() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductBundleForm();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Search_Product_For_Bundle() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Search = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SearchProductForBundle($Product,$Search);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Add_Product_To_Bundle() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Bundle = filter_var($_REQUEST['Bundle'],FILTER_SANITIZE_STRING);
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Amount = filter_var($_REQUEST['Amount'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->AddProductToBundle($Bundle,$Product,$Amount);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Remove_Product_From_Bundle() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Bundle = filter_var($_REQUEST['Bundle'],FILTER_SANITIZE_STRING);
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RemoveProductFromBundle($Bundle,$Product);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Product_Visibility() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Check = filter_var($_REQUEST['Check'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateProductVisibility($Product,$Check);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Delete_Product_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->DeleteProductData($Product);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Check_If_Product_Code_Exists() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Code = filter_var($_REQUEST['Code'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->CheckIfProductCodeExists($Product,$Code);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Filter_Category_Attribute() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Category = filter_var($_REQUEST['Category'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadCategoriesAttributes($Product,$Category);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Load_Category_Attribute_Values() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Attribute = filter_var($_REQUEST['Attribute'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadAttributeValueList($Attribute);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_ShowAll_Attribute_Values() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductCategories($Product,true);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Search_Category_Attribute_Value() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Attribute = filter_var($_REQUEST['Attribute'],FILTER_SANITIZE_STRING);
    $Search = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadAttributeValueList($Attribute,$Search);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_Show_Product_Reviews_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Reviews_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductReviews($Product);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_Load_Variation_Product_Form_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Variation = filter_var($_REQUEST['Variation'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadVariationProductForm($Product,$Variation);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_Refresh_Products_Variations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->SearchForVariableProduct($Product);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Variable_Product_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Main = filter_var($_REQUEST['Main'],FILTER_SANITIZE_STRING);
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Data = $_REQUEST['Data'];
    $Attributes = $_REQUEST['Attributes'];
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateVariableProduct($Main,$Product,$Data,$Attributes);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Change_Product_Main() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Variation = filter_var($_REQUEST['Variation'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateProductMain($Product,$Variation);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Product_Variation_For_Bundle() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadProductVariationForBundle($Product);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Update_Status_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Status = filter_var($_REQUEST['Status'],FILTER_SANITIZE_STRING);
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateStatusTranslations($Product,$Status,$Type,$Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Variation_Translation() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Variation = filter_var($_REQUEST['Variation'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadVariationTranslations($Variation);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Products_Update_Variation_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Variation = filter_var($_REQUEST['Variation'],FILTER_SANITIZE_STRING);
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateVariationTranslations($Product,$Variation,$Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Stock_Status_Modal() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Status = filter_var($_REQUEST['Status'],FILTER_SANITIZE_STRING);
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadStockStatusModal($Status,$Type);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Refresh_Product_Stock_Status_Table() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $List = filter_var($_REQUEST['List'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RefreshProductStockStatusTable($List);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Remove_Product_Stock_Status() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $Status = filter_var($_REQUEST['Status'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->RemoveProductStockStatus($Product,$Status);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Load_Stock_Status_Translations() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $Status = filter_var($_REQUEST['Status'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadModalStockStatusTranslations($Type,$Status);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Load_Stock_Status_Copy_Form() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Type = filter_var($_REQUEST['Type'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadStockStatusCopyForm($Type);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Search_Stock_Status() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Search = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadSearchStockStatusList($Search);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Product_Copy_Stock_Status() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Product = filter_var($_REQUEST['Product'],FILTER_SANITIZE_STRING);
    $CopyFrom = filter_var($_REQUEST['CopyFrom'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Products_Form();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->CopyStockStatusFromProduct($Product,$CopyFrom);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}