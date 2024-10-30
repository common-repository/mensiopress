<?php
class Mensio_Admin_Products_Form extends mensio_core_form {
	private $DataSet;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->ActivePage = 'Products';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-products',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-products.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-productreviews',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-products-reviews.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-products',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-products.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-productreviews',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-products-reviews.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='created DESC',$InSearch='',$JSONData='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    $DataSet = $this->LoadProductsDataSet($InSearch,$InSorter,$JSONData);
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_Sorter($InSorter);
    $tbl->Set_BulkActions(array(
     'VIS'=>'Visible ON',
     'INV'=>'Visible OFF'
    ));
    $tbl->Set_ExtraActions(
      $this->LoadExtraActions($JSONData)
    );
    $tbl->Set_EditColumn('name');
    $tbl->Set_EditOptionsSubline(array(
      'Reviews','Edit','Delete'
    ));
    $tbl->Set_Columns(array(
      'uuid:uuid:plain-text',
      'code:Code:small',
      'file:Image:img',
      'name:Name:plain-text',
      'stock:Stock:small',
      'created:Created:small',
      'visibility:Visible:input-checkbox'
    ));
    $RtrnTable = $tbl->CreateTable(
      'Products',
      $DataSet,
      array(
        'uuid',
        'file',
        'name',
        'code',
        'stock',
        'created',
        'visibility'
      )
    );
    unset($tbl);
    return $RtrnTable;
  }
  private function LoadProductsDataSet($InSearch,$InSorter,$JSONData) {
    $DataSet = array();
    $Products = new mensio_products();
    $Products->Set_SearchString($InSearch);
    $Products->Set_Sorter($InSorter);
    $Products->Set_ExtraFilters($JSONData);
    $Data = $Products->LoadProductsDataSet();
    if ((is_array($Data)) && (!empty($Data[0]))) {
      $i = 0;
      foreach ($Data as $Row) {
        $DnldAlert = '';
        $DataSet[$i]['uuid'] = $Row->uuid;
        $DataSet[$i]['code'] = $Row->code;
        $DataSet[$i]['file'] = get_site_url().'/'.$Row->file; 
        $DataSet[$i]['name'] = $Row->name.$DnldAlert;
        $DataSet[$i]['stock'] = ($Row->stock + 0);
        $DataSet[$i]['created'] =  date("d/m/Y", strtotime($Row->created));
        $DataSet[$i]['visibility'] = $Row->visibility;
        ++$i;
      }
    } else {
      $DataSet = $Data;
    }
    unset($Products);
    return $DataSet;
  }
  public function LoadMainLang() {
    $Languages = new mensio_languages();
    $Main = $Languages->ReturnMainLanguages('Admin');
    unset($Languages);
    return $Main;
  }
  public function LoadEnglishLang() {
    $Languages = new mensio_languages();
    $EngID = $Languages->ReturnEnglishLanguage();
    unset($Languages);
    return $EngID;
  }
  public function LoadLanguageButtons($Tab) {
    $LangBtns = '';
    $Languages = new mensio_languages();
    $Data = $Languages->LoadLanguagesData();
    $Main = $Languages->ReturnMainLanguages('Admin');
    unset($Languages);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        if ($Row->active) {
          switch ($Tab) {
            case 'Info':
              $id = 'INF_';
              $LangBtns .= '
                <div id="'.$id.$Row->uuid.'" class=" TranslButtons InfoTranslButtons">
                  <img src="'.MENSIO_PATH.'/admin/icons/flags/'.$Row->icon.'.png" alt="'.$Row->icon.'">
                  <input type="hidden" id="Name_'.$Row->uuid.'" class="TransFlds" value="">
                  <input type="hidden" id="Desc_'.$Row->uuid.'" class="TransFlds" value="">
                  <input type="hidden" id="Note_'.$Row->uuid.'" class="TransFlds" value="">
                    <!-- '.$id.$Main.' -->
                </div>';
              break;
            case 'Advantages':
              $id = 'ADV_';
              $LangBtns .= '
                <div id="'.$id.$Row->uuid.'" class=" TranslButtons TrAdvBtns">
                  <img src="'.MENSIO_PATH.'/admin/icons/flags/'.$Row->icon.'.png" alt="'.$Row->icon.'">
                </div>';
              break;
          }
        }
      }
      $LangBtns = str_replace(
        'id="'.$id.$Main.'" class="',
        'id="'.$id.$Main.'" class="TranslSelected',
        $LangBtns
      );
    }
    return $LangBtns;
  }
  private function LoadExtraActions($JSONData='') {
    $ExtraActions = array();
    $SlctdCat = '';
    $SlctdBrand = '';
    $SlctdStatus = '';
    $SlctdBundle = '';
    $JSONData = stripslashes($JSONData);
    $Data = json_decode($JSONData,true);
    if (json_last_error() === JSON_ERROR_NONE) {
      if (is_array($Data)) {
        foreach ($Data as $Row) {
          switch ($Row['Field']) {
            case 'Categories':
              $SlctdCat = $Row['Value'];
              break;
            case 'Brands':
              $SlctdBrand = $Row['Value'];
              break;
            case 'Status':
              $SlctdStatus = $Row['Value'];
              break;
            case 'Bundle':
              $SlctdBundle = $Row['Value'];
              break;
          }
        }
      }
    }
    $Option['name'] = 'Categories';
    $Option['options'] = $this->LoadCategoriesOptions($SlctdCat,$SlctdBrand,$SlctdStatus,$SlctdBundle);
    $ExtraActions[0] = $Option;
    $Option['name'] = 'Brands';
    $Option['options'] = $this->LoadBrandOptions($SlctdCat,$SlctdBrand,$SlctdStatus,$SlctdBundle);
    $ExtraActions[1] = $Option;
    $Option['name'] = 'Status';
    $Option['options'] = $this->LoadStatusOptions($SlctdCat,$SlctdBrand,$SlctdStatus,$SlctdBundle);
    $ExtraActions[2] = $Option;
    $Option['name'] = 'Bundle';
    $Option['options'] = $this->LoadBundleOptions($SlctdBundle);
    $ExtraActions[3] = $Option;
    return $ExtraActions;
  }
  private function LoadCategoriesOptions($Category,$Brand,$Status,$Bundle) {
    $Error = false;
    $Options = '<option value="0">All Categories</option>';
    $Products = new mensio_products();
    if (($Brand !== '0') && ($Brand !== '')) {
      if (!$Products->Set_Brand($Brand)) { $Error = true; }
    }
    if (($Status !== '0') && ($Status !== '')) {
      if (!$Products->Set_Status($Status)) { $Error = true; }
    }
    if (($Bundle === '0') || ($Bundle === '')) { $Bundle = false; }
      else { $Bundle = true; }
    if (!$Error) {
      $Data = $Products->LoadProductCategoriesFilter($Bundle);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Name = $Products->LoadProductCategoryPath($Row->uuid);
          if ($Name !== '') { $Name .= '/';}
          $Options .= '<option value="'.$Row->uuid.'">'.$Name.$Row->name.'</option>';
        }
      }
      if ($Category === '') {
        $Options = str_replace('value="0"', 'value="0" selected', $Options);
      } else {
        $Options = str_replace('value="'.$Category.'"', 'value="'.$Category.'" selected', $Options);
      }
    }
    unset($Products);
    return $Options;
  }
  public function LoadBrandOptions($Category='0',$Brand='0',$Status='0',$Bundle='0') {
    $Error = false;
    $Options = '<option value="0">All Brands</option>';
    $Products = new mensio_products();
    if (($Category !== '0') && ($Category !== '')) {
      if (!$Products->Set_Category($Category)) { $Error = true; }
    }
    if (($Status !== '0') && ($Status !== '')) {
      if (!$Products->Set_Status($Status)) { $Error = true; }
    }
    if (($Bundle === '0') || ($Bundle === '')) { $Bundle = false; }
      else { $Bundle = true; }
    if (!$Error) {
      $Data = $Products->LoadProductBrandFilter($Bundle);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Options .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
        }
      }
      if ($Brand === '') {
        $Options = str_replace('value="0"', 'value="0" selected', $Options);
      } else {
        $Options = str_replace('value="'.$Brand.'"', 'value="'.$Brand.'" selected', $Options);
      }
    }
    return $Options;
  }
  public function LoadStatusOptions($Category='0',$Brand='0',$Status='0',$Bundle='0') {
    $Error = false;
    $Options = '<option value="0">All Status</option>';
    $Products = new mensio_products();
    if (($Category !== '0') && ($Category !== '')) {
      if (!$Products->Set_Category($Category)) { $Error = true; }
    }
    if (($Brand !== '0') && ($Brand !== '')) {
      if (!$Products->Set_Brand($Brand)) { $Error = true; }
    }
    if (($Bundle === '0') || ($Bundle === '')) { $Bundle = false; }
      else { $Bundle = true; }
    if (!$Error) {
      $Data = $Products->LoadProductStatusFilter($Bundle);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Options .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
        }
      }
      if ($Status === '0') {
        $Options = str_replace('value="0"', 'value="0" selected', $Options);
      } else {
        $Options = str_replace('value="'.$Status.'"', 'value="'.$Status.'" selected', $Options);
      }
    }
    return $Options;
  }
  private function LoadBundleOptions($Selected='') {
    $Options = '<option value="0">All Products</option><option value="1">Bundles Only</option>';
    if ($Selected === '') {
      $Options = str_replace('value="0"', 'value="0" selected', $Options);
    } else {
      $Options = str_replace('value="'.$Selected.'"', 'value="'.$Selected.'" selected', $Options);
    }
    return $Options;
  }
  public function LoadProductTypeSelectorForm() {
    $MdlForm = '
    <div class="ModalProductTypeSelector">
      <div class="ModalBtnCol">
        <button id="NewStdProd">
          <i class="fa fa-archive" aria-hidden="true"></i>
          Standard Product
        </button>
      </div>
      <div class="ModalListCol">
        <label class="label_symbol">Search Product by Name or Code</label>
        <input type="text" id="MDL_Product" value="" class="mdl-form-control">
        <div class="DivResizer"></div>
        <div id="MDL_SrchRslts" class="ModalListRslts"></div>
        <div class="DivResizer"></div>
      </div>
    </div>';
    return $this->CreateModalWindow('Product Type Selection', $MdlForm);
  }
  public function LoadProductData($ProductID) {
    $NoteType = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Product'=>'','Name'=>'',
      'Code'=>'', 'Brand'=>'', 'BtBPrice'=>'', 'BtBTax'=>'', 'Price'=>'', 'Tax'=>'',
      'Available'=>'', 'Status'=>'', 'Stock'=>'', 'MinStock'=>'', 'Overstock'=>'',
      'Visibility'=>'','Downloadable'=>'', 'IsBundle'=>'','Description'=>'',
      'Notes'=>'', 'Discount'=>'', 'ImageList'=>'', 'Advantages'=>'','Tags'=>'',
      'Categories'=>'', 'Variations'=>'', 'DnldAlert'=>'', 'AttrValueList'=>'',
      'FileList'=>'', 'BarcodesList'=>'','BundleList'=>'','StockStatus'=>'',
      'StockStatusTable'=>'');
    $MainLang = $this->LoadMainLang();
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product value not acceptable';
    } else {
      $DataSet = $Products->LoadProductRecordData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData['Product'] = $Row->uuid;
          $RtrnData['Code'] = $Row->code;
          $RtrnData['Brand'] = $Row->brand;
          $RtrnData['BtBPrice'] = $Row->btbprice+0;
          $RtrnData['BtBTax'] = $Row->btbtax;
          $RtrnData['Price'] = $Row->price+0;
          $RtrnData['Tax'] = $Row->tax;
          $RtrnData['Discount'] = $Row->discount;
          $RtrnData['Available'] = date('Y-m-d', strtotime($Row->available));
          $RtrnData['Status'] = $Row->status;
          $RtrnData['Stock'] = $Row->stock+0;
          $RtrnData['MinStock'] = $Row->minstock+0;
          $RtrnData['Overstock'] = $Row->overstock;
          $RtrnData['Visibility'] = $Row->visibility;
          $RtrnData['Downloadable'] = $Row->downloadable;
          $RtrnData['Reviewable'] = $Row->reviewable;
          $RtrnData['DnldAlert'] = '';
          if ($Row->isbundle) { $RtrnData['IsBundle'] = 'TRUE'; }
            else { $RtrnData['IsBundle'] = 'FALSE'; }
          $RtrnData['Name'] = $Row->name;
          $RtrnData['Slug'] = $Products->GetProductSlug();
          $RtrnData['Description'] = $Row->description;
          $RtrnData['Notes'] = $Row->notes;
        }
        $ExtraData = $this->LoadProductStockStatus($Row->uuid);
        $RtrnData['StockStatus'] = $ExtraData['Values'];
        $RtrnData['StockStatusTable'] = $ExtraData['Table'];
        $ExtraData = $this->LoadProductCategories($RtrnData['Product'],false,true);
        $RtrnData['Categories'] = $ExtraData['Categories'];
        $ExtraData = $this->LoadBarcodesList($RtrnData['Product']);
        $RtrnData['BarcodesList'] = $ExtraData['BarcodesList'];
        $ExtraData = $this->LoadProductImageList($RtrnData['Product']);
        $RtrnData['ImageList'] = $ExtraData['ImageList'];
        $ExtraData = $this->LoadAdvantagesList($RtrnData['Product'],$MainLang);
        $RtrnData['Advantages'] = $ExtraData['Advantages'];
        $ExtraData = $this->LoadProductTagsList($RtrnData['Product']);
        $RtrnData['Tags'] = $ExtraData['Tags'];
        $ExtraData = $this->LoadProductFileList($RtrnData['Product']);
        $RtrnData['FileList'] = $ExtraData['FileList'];
        $ExtraData = $this->SearchForVariableProduct($RtrnData['Product']);
        $RtrnData['Variations'] = $ExtraData['Variations'];
        $ExtraData = $this->LoadProductBundleList($RtrnData['Product']);
        $RtrnData['BundleList'] = $ExtraData['BundleList'];
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadProductImageList($ProductID) {
    $NoteType = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','ImageList'=>'');
    $MainImage = '';
    $ImageList = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product value not acceptable';
    } else {
      $DataSet = $Products->LoadProductRecordImages();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $BasicImage = MENSIO_SHORTPATH.'/admin/icons/default/noimage.png';
        foreach ($DataSet as $Row) {
          if ($Row->file !== $BasicImage) {
            if ($Row->main) {
              $MainImage = '
                <div class="ProdImgWrapper">
                  <img src="'.get_site_url().'/'.$Row->file.'" alt="image">
                  <div class="DivResizer"></div>
                </div>';
            }
            $ImageList .= '
                <div class="ProdImgWrapper SlctImg">
                  <img src="'.get_site_url().'/'.$Row->file.'" alt="image">
                  <div class="ImgLstOverlay">
                    <div class="text">
                      <div id="SM_'.$Row->uuid.'" class="ImgBtn BTN_SetMain">Main</div>
                      <div id="DL_'.$Row->uuid.'" class="ImgBtn BTN_DelImg">Remove</div>
                    </div>
                  </div>                  
                  <div class="DivResizer"></div>
                </div>';
          }
        }
      }
      $RtrnData['ImageList'] = '
            <div class="MainImageDiv">
              <label class="label_symbol">Main</label>
              <div class="DivResizer"></div>
              <div id="MainImgWrap">
                '.$MainImage.'
              </div>
            </div>
            <div class="ImageListDiv">
              <label class="label_symbol">List</label>
              <div class="DivResizer"></div>
              <div class="ImageList">
                '.$ImageList.'
              </div>
            </div>';
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadProductTranslations($ProductID,$LangID) {
    $NoteType = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Product'=>'','Name'=>'',
        'Description'=>'', 'Notes'=>'');
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product value not acceptable';
    }
    if (!$Products->Set_Language($LangID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Language value not acceptable';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $DataSet = $Products->LoadProductRecordTranslations();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData['Name'] = $Row->name;
          $RtrnData['Description'] = $Row->description;
          $RtrnData['Notes'] = $Row->notes;
        }
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadAdvantagesList($ProductID,$LangID) {
    $NoteType = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Advantages'=>'');
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product value not acceptable';
    }
    if (!$Products->Set_Language($LangID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Language value not acceptable';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $DataSet = $Products->LoadProductAdvantages();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData['Advantages'] .= '<div class="TagDiv">
                   <div class="TagText">
                    '.$Row->advantage.'
                   </div>
                   <div class="TagBtns">
                     <div id="'.$Row->uuid.'" class="RgTpBtns AttrAdvDelete">
                       <i class="fa fa-times" aria-hidden="true"></i>
                     </div>
                   </div>
                <div class="DivResizer"></div>
                </div>';
        }
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadProductTagsList($ProductID) {
    $NoteType = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Tags'=>'');
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product value not acceptable';
    } else {
      $DataSet = $Products->LoadProductTags();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData['Tags'] .= '<div class="TagDiv">
                 <div class="TagText">
                  '.$Row->tags.'
                 <div class="DivResizer"></div>
                 </div>
                 <div class="TagBtns">
                   <div id="DT_'.$Row->uuid.'" class="RgTpBtnsBL AttrTagsEdit" title="Edit">
                     <i class="fa fa-pencil" aria-hidden="true"></i>
                   </div>
                   <div id="DL_'.$Row->uuid.'" class="RgTpBtns AttrTagsDelete" title="Delete">
                     <i class="fa fa-times" aria-hidden="true"></i>
                   </div>
                 </div>
              <div class="DivResizer"></div>
              </div>';
        }
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadProductFileList($ProductID) {
    $NoteType = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','FileList'=>'');
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product value not acceptable';
    } else {
      $DataSet = $Products->LoadProductFileList();
      $RtrnData['FileList'] = '<table class="ProductSubTable">
                <thead>
                  <tr>
                    <th>File</th>
                    <th class="SubTblSmallCol">Type</th>
                    <th class="SubTblSmallCol">Downladed</th>
                    <th class="SubTblSmallCol">Expiration</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>';
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Filename = explode('/',$Row->file);
          $Pos = count($Filename);
          $Pos = $Pos - 1;
          $Filename = $Filename[$Pos];
          $date1 = new DateTime(date('Y-m-d H:i:s'));
          $date2 = new DateTime(date('Y-m-d H:i:s', strtotime($Row->expiration)));
          $interval = $date1->diff($date2);
          if ($interval->y > 2) {
            $diff = 'No Expiration Set';
          } else {
            $diff = $interval->y . " years, " . $interval->m." months, ".$interval->d." days";
          }
          $RtrnData['FileList'] .= '<tr>
                    <td>'.$Filename.'</td>
                    <td class="SubTblSmallCol">'.$Row->name.'</td>
                    <td class="SubTblSmallCol">'.$Row->dnldtimes.'</td>
                    <td class="SubTblSmallCol">'.$diff.'</td>
                    <td class="SubTblBtnCol">
                      <div id="DT_'.$Row->uuid.'" class="BtnEditFile BtnFileMng" title="Edit">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                      </div>
                      <div id="DL_'.$Row->uuid.'" class="BtnRemoveFile BtnFileMng" title="Delete">
                        <i class="fa fa-times" aria-hidden="true"></i>
                      </div>
                    </td>
                  </tr>';
        }
      }
      $RtrnData['FileList'] .= '
                </tbody>
              </table>';
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadProductBundleList($ProductID) {
    $NoteType = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','BundleList'=>'');
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product value not acceptable';
    } else {
      $DataSet = $Products->LoadBundleProductList();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $RtrnData['BundleList'] = '
              <table class="ProductSubTable">
                <thead>
                  <tr>
                    <th class="BndlImgCol">Image</th>
                    <th class="BndlNmCol">Product</th>
                    <th class="BndlSmlCol">Amount</th>
                    <th class="BndlCtrlBtnCol"></th>
                  </tr>
                </thead>
                <tbody>';
        foreach ($DataSet as $Row) {
          $RtrnData['BundleList'] .= '<tr>
                    <td class="BndlImgCol">
                      <img src="'.get_site_url().'/'.$Row->file.'" alt="file_image">
                      <div class="DivResizer"></div>
                    </td>
                    <td class="BndlNmCol">
                      '.$Row->name.'
                      <div class="DivResizer"></div>
                    </td>
                    <td class="BndlSmlCol">
                      '.($Row->amount + 0).'
                      <div class="DivResizer"></div>
                    </td>
                    <td class="BndlCtrlBtnCol">
                      <div id="DL_'.$Row->uuid.'" class="BndlCtrlBtn BTN_BndlDel" title="Delete">
                        <i class="fa fa-times" aria-hidden="true"></i>
                      </div>
                      <div class="DivResizer"></div>
                    </td>
                  </tr>';
        }
        $RtrnData['BundleList'] .= '</tbody>
              </table>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadBarcodesList($ProductID) { 
    $NoteType = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','BarcodesList'=>'');
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product value not acceptable';
    } else {
      $DataSet = $Products->LoadProductBarcodeList();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $RtrnData['BarcodesList'] = '<table class="ProductSubTable">
                <thead>
                  <tr>
                    <th class="SubTblSmallCol">Type</th>
                    <th>Barcode</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>';
        foreach ($DataSet as $Row) {
          $RtrnData['BarcodesList'] .= '<tr>
                    <td class="SubTblSmallCol">'.$Row->name.'</td>
                    <td>'.$Row->barcode.'</td>
                    <td class="SubTblBtnCol">
                      <div id="'.$Row->uuid.'" class="RgTpBtns BCDel" title="Delete">
                        <i class="fa fa-times" aria-hidden="true"></i>
                      </div>
                    </td>
                  </tr>';
        }
        $RtrnData['BarcodesList'] .= '</tbody>
              </table>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadStatusList() {
    $Products = new mensio_products();
    $DataSet = $Products->LoadStatusDataSet();
    unset($Products);
    $StatusTbl = '<table class="ProductSubTable">
                <thead>
                  <tr>
                    <th></th>
                    <th>Icon</th>
                    <th>Color</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>';
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $StatusTbl .= '<tr>
                    <td class="ModalStatusCtrlCol">
                      <div id="Dlt_'.$Row->uuid.'" class="MdlStatusRemoveBtn" title="Delete">
                        <i class="fa fa-times" aria-hidden="true"></i>
                      </div>
                      <div id="Edt_'.$Row->uuid.'" class="MdlStatusEditBtn" title="Edit">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                      </div>
                      <div id="Trs_'.$Row->uuid.'" class="MdlStatusTransBtn" title="Translations">
                        <i class="fa fa-comments" aria-hidden="true"></i>
                      </div>
                    </td>
                    <td class="ModalStatusIcon">
                      <img id="Img_'.$Row->uuid.'" src="'.get_site_url().'/'.$Row->icon.'" alt="'.$Row->name.'">
                    </td>
                    <td class="ModalStatusIcon">
                      <div id="Color_'.$Row->uuid.'" class="StatusColorBox" attr-bckgrnd="'.$Row->color.'" style="background:'.$Row->color.';"></div>
                    </td>
                    <td id="Name_'.$Row->uuid.'">'.$Row->name.'</td>
                  </tr>';
      }
    }
    $StatusTbl .= '</tbody>
              </table>';
    return $StatusTbl;
  }
  public function LoadProductAddStatusForm() {
    $StatusTbl = $this->LoadStatusList();
    $MdlForm = '
    <div class="ModalProductTypeSelector">
      <div id="StatusTransModal"></div>
      <label class="label_symbol">Status Name</label>
      <div class="StatusInputDiv">
        <div class="StatusInput">
          <input type="hidden" id="MDL_Status_ID" value="NewEntry">
          <input type="text" id="MDL_Status" value="" class="mdl-form-control">
        </div>
        <div class="StatusBtn">
          <div id="MDL_BtnAddStatus" class="AddStatusBtn" title="Add Status">
            <i class="fa fa-save" aria-hidden="true"></i>
          </div>
        </div>
        <div class="DivResizer"></div>
        <div id="BrandsLogoDiv" class="IconWrapper">
          <label class="label_symbol">Status Icon</label>
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
              <input id="FLD_Logo" class="form-control" type="hidden" value="'.MENSIO_PATH.'/admin/icons/default/noimage.png">
              <input type="hidden" id="DefaultImage" value="'.MENSIO_PATH.'/admin/icons/default/noimage.png">
            </div>
          </div>
        </div>
        <div class="ColorWraper">
          <label class="label_symbol">Brand Color</label>
          <input type="text" id="FLD_Color" class="form-control">
        </div>
        <div class="DivResizer"></div>
        <div id="MDL_StatusListWrapper" class="StatusList">
          '.$StatusTbl.'
        </div>
      </div>
    </div>';
    return $this->CreateModalWindow('Status List', $MdlForm);
  }
  public function LoadModalStatusTranslations($StatusID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Modal'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if ($Products->Set_Status($StatusID)) {
      $Languages = new mensio_languages();
      $LangData = $Languages->LoadLanguagesData();
      unset($Languages);
      $TransFlds ='';
      if ((is_array($LangData)) && (!empty($LangData[0]))) {
        foreach ($LangData as $Row) {
          if ($Row->active) {
            if ($Products->Set_Language($Row->uuid)) {
              $TransName = $Products->GetStatusTranslation();
              $TransFlds .= '<label class="label_symbol">'.$Row->name.'</label>
                <input type="text" id="'.$Row->uuid.'" class="form-control StatusTransFlds" value="'.$TransName.'">';
            }
          }
        }
      }
      $ModalBody = $TransFlds.'
          <input type="hidden" id="StatusType" value="default">
          <input type="hidden" id="StatusTransID" value="'.$StatusID.'">
          <button id="BTN_StatusTransSave" class="button" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>';
      $RtrnData['Modal'] = $this->CreateModalWindow('Status Translations',$ModalBody,'StatusTransModal');
    }
    unset($Products);
    if ( $RtrnData['ERROR'] === 'TRUE' ) {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function AddProductStatus($Status,$Name,$Icon,$Color) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Table'=>'','Options'=>'',
      'DfltImg'=>MENSIO_PATH.'/admin/icons/default/empty.png'
    );
    $NoteType = '';
    $NewEntry = false;
    $Products = new mensio_products();
    if ($Status === 'NewEntry') { $NewEntry = true; }
    if (!$Products->Set_Name($Name)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Status name value not acceptable';
      if ($NewEntry) {
        if ($Products->CheckStatusNameExists()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] = 'Status name given allready exists';
        }
      }
    }
    if (!$Products->Set_Image($Icon)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Icon selected not acceptable';
    }
    if (!$Products->Set_Color($Color)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Color code not acceptable';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if ($NewEntry) {
        if (!$Products->InsertNewProductStatus()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] = 'Status name could not be saved';
        }
      } else {
        if ($Products->Set_Status($Status)) {
          if (!$Products->UpdateProductStatus()) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] = 'Status could not be saved';
          }
        }
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData['Table'] = $this->LoadStatusList();
      $RtrnData['Options'] = $this->LoadStatusOptions();
    }
    return $RtrnData;
  }
  public function RemoveProductStatus($Status) {
    $NoteType = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Table'=>'');
    $Products = new mensio_products();
    if (!$Products->Set_Status($Status)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Status name value not acceptable';
    } else {
      if ($Products->CheckStatusUse()) {
        $NoteType = 'Info';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] = 'Status is in use can not be deleted';
      } else {
        if (!$Products->RemoveProductStatusRecord()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] = 'Status could not be deleted';
        } else {
          $RtrnData['Table'] = $this->LoadStatusList();
          $RtrnData['Options'] = $this->LoadStatusOptions();
        }
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function UpdateStatusTranslations($ProductID,$StatusID,$Type,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','StockStatus'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if ($Products->Set_Status($StatusID)) {
      $Data = json_decode(stripslashes($Data), true);
      if (is_array($Data)) {
        foreach ($Data as $Row) {
          if (!$Products->Set_Language($Row['lang'])) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Language id not correct<br>';
          }
          if (!$Products->Set_Name($Row['Value'])) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Language translation not correct<br>';
          }
          if ($RtrnData['ERROR'] === 'FALSE') {
            if (!$Products->UpdateStatusTranslations($Type)) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Translation could not be updated<br>';
            }
          }
        }
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'FALSE' ) {
      $NoteType = 'Success';
      $RtrnData['Message'] .= 'Status Translations updated Successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    if ($Type === 'Stock') {
      $Data = $this->LoadProductStockStatus($ProductID);
      $RtrnData['StockStatus'] = $Data['Values'];
    }
    return $RtrnData;
  }
  public function UpdateProductData($Data,$Trans) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Product'=>'',
    'Categories'=>'','StockStatus'=>'','StockStatusTable'=>'');
    $RMain = $this->UpdateProductMainData($Data);
    $RtrnData['ERROR'] = $RMain['ERROR'];
    $RtrnData['Message'] .= $RMain['Message'].'<br>';
    $RtrnData['Product'] = $RMain['Product'];
    $ExtraData = $this->LoadProductCategories($RtrnData['Product'],false,true);
    $RtrnData['Categories'] = $ExtraData['Categories'];
    if ($RtrnData['ERROR'] === 'FALSE') {
      if ($RMain['StockRelated']) {
        $Data = $this->LoadProductStockStatus($RMain['Product']);
        $RtrnData['StockStatus'] = $Data['Values'];
        $RtrnData['StockStatusTable'] = $Data['Table'];
      }
      $RTrans = $this->UpdateProductTranslations($RMain['Product'],$Trans);
      if ($RTrans['ERROR'] === 'TRUE') {
        $RtrnData = $this->DeleteProductData($RtrnData['Product']);
        $RtrnData['ERROR'] = $RTrans['ERROR'];
        $RtrnData['Message'] .= $RTrans['Message'];
      } else {
        $RtrnData['Message'] .= $RTrans['Message'];
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$this->UpdateSlug($RMain['Product'],$RMain['Slug'])) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Slug could not be updated<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'TRUE') { $NoteType = 'Alert'; }
      else { $NoteType = 'Success'; }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  private function UpdateProductMainData($Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Product'=>'','StockRelated'=>false, 'Slug'=>'');
    $NewEntry = false;
    $Data = json_decode(stripslashes($Data), true);
    if (is_array($Data)) {
      $Products = new mensio_products();
      foreach ($Data as $Row) {
        if ($Row['Field'] === 'FLD_Product') {
          if (($Row['Value'] === 'NewProduct') || ($Row['Value'] === 'NewBundle')) {
            if ($Row['Value'] === 'NewBundle') { $Products->Set_IsBundle(true); }
            $NewEntry = true;
            $Row['Value'] = $Products->Get_NewProductCode();
          }
          if (!$Products->Set_UUID($Row['Value'])) {
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] = 'Product code not correct';
          } else {
            $RtrnData['Product'] = $Row['Value'];
          }
        } else if (substr($Row['Field'],0,4) === 'FLD_') {
          if (($Row['Field'] === 'FLD_Status') && ($Row['Value'] === 'StockRelated')) {
            $RtrnData['StockRelated'] = true;
          }
          if ($Row['Field'] === 'FLD_Slug') {
            $RtrnData['Slug'] = $Row['Value'];
          }
          $SetValue = $this->FindSetFun($Row['Field']);
          if ($SetValue !== '') {
            if (!$Products->$SetValue($Row['Value'])) {
              $RtrnData['ERROR'] = 'TRUE';
              $Lbl = str_replace('FLD_','',$Row['Field']);
              $RtrnData['Message'] .= 'Value "'.$Row['Value'].'" of the field '.$Lbl.' is not correct<br>';
            }
          }
        }
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        if (!$Products->CheckIfProductCodeExists()) {
          if ($NewEntry) {
            if (!$Products->InsertNewProductMainData()) {
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Problem while inserting new product<br>';
            }
          } else {
            if (!$Products->UpdateProductMainData()) {
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Problem while updating product<br>';
            }
          }
          if ($RtrnData['ERROR'] === 'FALSE') {
            if (!$Products->UpdateProductStockStatus()) {
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Stock Status list could not be updated<br>';
            }
          }
        } else {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Product code is allready in use<br>';
        }
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        $RtrnData['Message'] .= 'Product data saved successfully<br>';
      }
      unset($Products);
    }
    return $RtrnData;
  }
  private function UpdateSlug($ProductID,$Slug) {
    $RtrnData = true;
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) { $RtrnData = false; }
    if (!$Products->Set_Slug($Slug)) { $RtrnData = false; }
    if ($RtrnData) { $RtrnData = $Products->UpdateProductSlug(); }
    unset($Products);
    return $RtrnData;
  }
  private function FindSetFun($Field) {
    $SetFun = '';
    switch ($Field) {
      case 'FLD_Code':
        $SetFun = 'Set_Code';
        break;
      case 'FLD_Brand':
        $SetFun = 'Set_Brand';
        break;
      case 'FLD_Visible':
        $SetFun = 'Set_Visibility';
        break;
      case 'FLD_Downloadable':
        $SetFun = 'Set_Downloadable';
        break;
      case 'FLD_Downloadable':
        $SetFun = 'Set_Downloadable';
        break;
      case 'FLD_Reviewable':
        $SetFun = 'Set_Reviewable';
        break;
      case 'FLD_BtBPrice':
        $SetFun = 'Set_BtBPrice';
        break;
      case 'FLD_BtBTax':
        $SetFun = 'Set_BtBTax';
        break;
      case 'FLD_Price':
        $SetFun = 'Set_Price';
        break;
      case 'FLD_Tax':
        $SetFun = 'Set_Tax';
        break;
      case 'FLD_Discount':
        $SetFun = 'Set_Discount';
        break;
      case 'FLD_Stock':
        $SetFun = 'Set_Stock';
        break;
      case 'FLD_StockStatus':
        $SetFun = 'Set_StockStatus';
        break;
      case 'FLD_MinStock':
        $SetFun = 'Set_MinStock';
        break;
      case 'FLD_Overstock':
        $SetFun = 'Set_Overstock';
        break;
      case 'FLD_Available':
        $SetFun = 'Set_Available';
        break;
      case 'FLD_Status':
        $SetFun = 'Set_Status';
        break;
      case 'FLD_Slug':
        $SetFun = 'Set_Slug';
        break;
    }
    return $SetFun;
  }
  public function CheckIfProductCodeExists($ProductID,$Code) {
    $RtrnData = array('ERROR'=>'FALSE');
    $Products = new mensio_products();
    if ($ProductID !== 'NewProduct') {
      $Products->Set_UUID($ProductID);
    }
    if ($Products->Set_Code($Code)) {
      if ($Products->CheckIfProductCodeExists()) { $RtrnData['ERROR'] = 'TRUE'; }
    }
    unset($Products);
    return $RtrnData;
  }
  private function UpdateProductTranslations($ProductID,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $Data = stripslashes($Data);
    $Data = json_decode($Data, true);
    if (is_array($Data)) {
      $Products = new mensio_products();
      if (!$Products->Set_UUID($ProductID)) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] = 'Product code not correct';
      } else {
        foreach ($Data as $Row) {
          if (!$Products->Set_Language($Row['Field'])) {
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] = 'Language code not correct <br>';
          } else {
            $Trans = explode('|::|',$Row['Value']);
            if ($Trans[0] === '') { $Trans[0] = 'NO TRANSLATION AVAILABLE'; }
            if ($Trans[1] === '') { $Trans[1] = 'NO TRANSLATION AVAILABLE'; }
            if ($Trans[2] === '') { $Trans[2] = 'NO TRANSLATION AVAILABLE'; }
            if (!$Products->Set_Name($Trans[0])) {
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Name given "'.$Trans[0].'" not correct<br>';
            }
            if (!$Products->Set_Description($Trans[1])) {
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Description given not correct<br>';
            }
            if (!$Products->Set_Notes($Trans[2])) {
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Notes given not correct<br>';
            }
            if ($RtrnData['ERROR'] === 'FALSE') {
              if (!$Products->UpdateProductTranslations()) {
                $RtrnData['ERROR'] = 'TRUE';
                $RtrnData['Message'] .= 'Translation could not be updated<br>';
              }
            }
          }
        }
        if ($RtrnData['ERROR'] === 'FALSE') {
          $RtrnData['Message'] = 'Translations updated successfully<br>';
        }
      }
      unset($Products);
    }
    return $RtrnData;
  }
  public function LoadProductCategories($ProductID,$ShowAll=false,$Dflt=false) {
    $NoteType = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Categories'=>'');
    $CatBtns = $this->LoadCategoryColumn($ProductID,$ShowAll,$Dflt);
    $Attr = '<div class="WrapperEmpty"><div class="">Select Category</div></div>';
    if ($ShowAll) { $Attr = $CatBtns['Attributes']; }
    if ($Dflt) { $Attr = $this->LoadGLOBALAttributes($ProductID); }
    $RtrnData['Categories'] = '
            <div class="CategoryList">
              <label class="label_symbol">
                Categories
              </label>
              <div class="ExtraSelectionsDiv">
                <div id="BTN_AddProdCat" class="ESBtnsDivs" title="Add Category">
                  <div class="ESBtns">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
              <hr>
              <div class="CategoryListWrapper">
                '.$CatBtns['Categories'].'
              </div>
              <div class="CategorButtonWrapper">
                <button id="ShowAllValues" class="CatLstBtn">View All Attributes</button>
              </div>
            </div>
            <div class="AttributeList">
              <div id="AttributeValueSelector">
                <div id="AttributeSelectorList" class="">
                  <div class="AttrLbl">
                    <label class="label_symbol">Attributes</label><hr>
                  </div>
                  <div id="AttrBtnWrapper" class="">
                    '.$Attr.'
                  </div>
                </div>
                <div id="CatAttrValueList" class="">
                  <div class="AttrLbl">
                    <label class="label_symbol">Values</label><hr>
                  </div>
                  <div id="CatAttrValueListWrapper">
                    <div class="WrapperEmpty"><div class="">Select Attribute</div></div>
                  </div>
                  <div id="AttrValSearcherDiv" class="">
                    <input type="text" id="FLD_SearchAttrVal" class="Srchcontrol" value="" placeholder="Search ...">
                  </div>
                </div>
                <div id="ValueTableDisplay" class="AttributeValueList">
                  <div class="AttrLbl">
                    <label class="label_symbol">Active Attribute Values</label><hr>
                  </div>
                  <table id="TBL_AttributeValues">
                    <thead>
                      <tr>
                        <th class="AttrCol">Attribute</th>
                        <th>Value</th>
                        <th>Visible</th>
                        <th class="thempty"></th>
                      </tr>
                    </thead>
                    <tbody id="AttValLst">
                     '.$this->LoadProductAttributeValueList($ProductID).'
                    </tbody>
                  </table>
                </div>
              </div>
            </div>';
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  private function LoadGLOBALAttributes($ProductID) {
    $Categories = new mensio_products_categories();
    $GlblID = $Categories->GetGLOBALCategoryID();
    unset($Categories);
    $Attr = $this->LoadCategoriesAttributes($ProductID,$GlblID);
    return $Attr;
  }
  public function LoadCategoryColumn($ProductID,$ShowAll,$Dflt=false) {
    $CatBtns = array('Categories' => '', 'Attributes' => '');
    $NoteType = '';
    $Products = new mensio_products();
    $Global = $Products->GetGlobalCategory();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product value not acceptable';
    } else {
      $DataSet = $Products->LoadProductCategories();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Class = '';
          if ($ShowAll) { $Class = ' ActiveCat'; }
          else {
            if (($Dflt) && ($Global === $Row->category)) { $Class = ' ActiveCat'; }
          }
          $DelBtn = '';
          if ($Global !== $Row->category) {
          $DelBtn = '<div id="DEL_'.$Row->category.'" class="RgTpBtns RMCategory" title="Remove">
                    <i class="fa fa-times" aria-hidden="true"></i>
                  </div>';
          }
          $CatBtns['Categories'] .= '
              <div id="CAT_'.$Row->category.'" class="CatBtnName'.$Class.'">
                <div class="CatName">'.$Row->name.'<br></div>
                <div class="CatBtn">
                  <div id="VW_'.$Row->category.'" class="RgTpBtns VWCategory" title="Filter">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                  </div>
                  '.$DelBtn.'
                </div>
                <div class="DivResizer"></div>
              </div>';
          $CatBtns['Attributes'] .= $this->LoadCategoriesAttributes($ProductID,$Row->category);
        }
      }
    }
    unset($Products);
    return $CatBtns;
  }
  public function LoadProductCategorySelectorForm($ProductID) {
    $CatList = $this->LoadCategoriesTree($ProductID);
    $MdlForm = '
    <div class="ModalProductCategories">
      '.$CatList.'
    <div class="DivResizer"></div>
    </div>';
    return $this->CreateModalWindow('Category Selection', $MdlForm);
  }
  private function LoadCategoriesTree($ProductID,$Parent='TopLevel') {
    $CatTree = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_Parent($Parent)) {
      $DataSet = $Categories->LoadProductCategoriesTreeDataSet();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $CatTree .= '<ol class="dd-list">';
        foreach ($DataSet as $Row) {
          $Disabled = '';
          if ($this->CheckProductCategories($ProductID,$Row->category)) {
            $Disabled = 'disabled';
          }
          $CatTree .= '<li class="dd-item '.$Disabled.'" data-id="'.$Row->category.'">
                  <div class="dd-handle CatSelctor '.$Disabled.'" id="'.$Row->category.'">'.$Row->name.' ('.$Row->translation.') </div>';
          $CatTree .= $this->LoadCategoriesTree($ProductID,$Row->category);
          $CatTree .= '</li>';
        }
        $CatTree .= '</ol>';
      }
    }
    unset($Categories);
    return $CatTree;
  }  
  public function LoadCategoriesAttributes($ProductID,$CategoryID) {
    $Attributes = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_UUID($CategoryID)) {
      $AttrData = $Categories->GetCategoryAttributes();
      if ((is_array($AttrData)) && (!empty($AttrData[0]))) {
        foreach ($AttrData as $Attr) {
          $Attributes .= '
            <div id="'.$Attr->uuid.'" class="CategoryAttribute">
              '.$Attr->name.'
            </div>';
        }
      }
    }
    unset($Categories);
    return $Attributes;
  }
  public function LoadAttributeValueList($AttributeID,$Search='') {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'', 'ValueList'=>'');
    $NoteType = '';
    $Attributes = new mensio_products_default_attributes();
    if ($Attributes->Set_UUID($AttributeID)) {
      if ($Search !== '') { $Attributes->Set_SearchString($Search); }
      $Data = $Attributes->GetAttributeValues();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        $Text = '';
        $Hex = '';
        $Img = '';
        foreach ($Data as $Row) {
          if (substr($Row->value, 0, 5) === 'Name:') {
            $ClrData = explode(';',$Row->value);
            switch (count($ClrData)) {
              case 5:
                $ClrData[0] = str_replace('Name:', '', $ClrData[0]);
                $ClrData[1] = str_replace('Hex:', '', $ClrData[1]);
                $ClrData[2] = str_replace('R:', '', $ClrData[2]);
                $ClrData[3] = str_replace('G:', '', $ClrData[3]);
                $ClrData[4] = str_replace('B:', '', $ClrData[4]);
                $Hex .= '
                <div id="'.$Row->uuid.'" class="RgnTypeBtn ClrBtn ValueSelector">
                  <div class="ColorDspl" style="background:'.$ClrData[1].';"></div>
                  <div class="ColorData">
                    <span>'.$ClrData[0].'</span><br>
                    Hex: '.$ClrData[1].'<br>
                    RGB: '.$ClrData[2].','.$ClrData[3].','.$ClrData[4].'
                  </div>
                <div class="DivResizer"></div>
                </div>';
                break;
              case 2:
                $ClrData[0] = str_replace('Name:', '', $ClrData[0]);
                $ClrData[1] = str_replace('Img:', '', $ClrData[1]);
                $Img .= '
                <div id="'.$Row->uuid.'" class="RgnTypeBtn imgRgnTypeBtn ValueSelector">
                  <div class="ColorImgDspl">
                    <img src="'.get_site_url().'/'.$ClrData[1].'" alt="file_image">
                  </div>
                  <span>'.$ClrData[0].'</span>
                <div class="DivResizer"></div>
                </div>';
                break;
            }
          } else {
            $Text .= '
                <div id="'.$Row->uuid.'" class="RgnTypeBtn TxtBtn ValueSelector">
                  <span>'.$Row->value.'</span>
                <div class="DivResizer"></div>
                </div>';
          }
        }
        $Type = $Attributes->GetGlobalAttributeType();
        switch($Type) {
          case 'HEX': case 'RGB':
            $RtrnData['ValueList'] = $Hex.$Text.$Img;
            break;
          case 'IMG':
            $RtrnData['ValueList'] = $Img.$Text.$Hex;
            break;
          default:
            $RtrnData['ValueList'] = $Text.$Hex.$Img;
            break;
        }
      }
    } else {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Attribute id not correct<br>';
    }
    unset($Attributes);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  private function CheckProductCategories($ProductID,$Category) {
    $Found = false;
    $Products = new mensio_products();
    if ($Products->Set_UUID($ProductID)) {
      $Data = $Products->LoadProductCategories();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          if ($Row->category === $Category) { $Found = true; }
        }
      }
    }
    unset($Products);
    return $Found;
  }
  private function ProductValueFound($ProductID,$AttrVal) {
    $Found = false;
    $Products = new mensio_products();
    if ($Products->Set_UUID($ProductID)) {
      $Data = $Products->LoadProductAttributeValues();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          if ($Row->attribute_value === $AttrVal) { $Found = true; }
        }
      }
    }
    unset($Products);
    return $Found;
  }
  public function LoadProductAttributeValueList($ProductID,$IsVarTbl=false) {
    $AttrList = '';
    $Products = new mensio_products();
    if ($Products->Set_UUID($ProductID)) {
      $Data = $Products->LoadProductAttributeValues();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Checked = '';
          if ($Row->visibility) { $Checked = 'checked'; }
          if (substr($Row->value,0,5) === 'Name:') {
            $ClrData = explode(';',$Row->value);
            switch(count($ClrData)) {
              case 5:
                $ClrData[0] = str_replace('Name:', '', $ClrData[0]);
                $ClrData[1] = str_replace('Hex:', '', $ClrData[1]);
                $ClrData[2] = str_replace('R:', '', $ClrData[2]);
                $ClrData[3] = str_replace('G:', '', $ClrData[3]);
                $ClrData[4] = str_replace('B:', '', $ClrData[4]);
                $Btns = '';
                if (!$IsVarTbl) { // if it is a variable product
                  $Btns = '<td class="VisCol">
                         <input id="CHK_'.$Row->attribute_value.'" type="checkbox" class="ProductValue" value="'.$Row->visibility.'" '.$Checked.'>
                       </td>
                       <td class="VisCol">
                         <div id="BTN_'.$Row->attribute_value.'" class="RgTpBtns AttrValDelete" title="Remove">
                           <i class="fa fa-times" aria-hidden="true"></i>
                         </div>
                       </td>';
                }
                $AttrList .= '<tr>
                       <td class="AttrCol">'.$Row->name.'</td>
                       <td><div class="RowClrDiv" style="background:'.$ClrData[1].'"></div>'.$ClrData[0].'</td>
                       '.$Btns.'
                     </tr>';
                break;
              case 2:
                $ClrData[0] = str_replace('Name:', '', $ClrData[0]);
                $ClrData[1] = str_replace('Img:', '', $ClrData[1]);
                $Btns = '';
                if (!$IsVarTbl) { // if it is a variable product
                  $Btns = '<td class="VisCol">
                         <input id="CHK_'.$Row->attribute_value.'" type="checkbox" class="ProductValue" value="'.$Row->visibility.'" '.$Checked.'>
                       </td>
                       <td class="VisCol">
                         <div id="BTN_'.$Row->attribute_value.'" class="RgTpBtns AttrValDelete" title="Remove">
                           <i class="fa fa-times" aria-hidden="true"></i>
                         </div>
                       </td>';
                }
                $AttrList .= '<tr>
                       <td class="AttrCol">'.$Row->name.'</td>
                       <td><div class="RowImgDiv"><img src="'.get_site_url().'/'.$ClrData[1].'" alt="file_image"></div>'.$ClrData[0].'</td>
                       '.$Btns.'
                     </tr>';
                break;
            }
          } else {
            $Btns = '';
            if (!$IsVarTbl) { // if it is a variable product
              $Btns = '<td class="VisCol">
                         <input id="CHK_'.$Row->attribute_value.'" type="checkbox" class="ProductValue" value="'.$Row->visibility.'" '.$Checked.'>
                       </td>
                       <td class="VisCol">
                         <div id="BTN_'.$Row->attribute_value.'" class="RgTpBtns AttrValDelete" title="Remove">
                           <i class="fa fa-times" aria-hidden="true"></i>
                         </div>
                       </td>';
            }            
            $AttrList .= '<tr>
                       <td class="AttrCol">'.$Row->name.'</td>
                       <td>'.$Row->value.'</td>
                       '.$Btns.'
                     </tr>';
          }
        }
      }
    }
    unset($Products);
    return $AttrList;
  }
  public function AddCategoryToProduct($ProductID,$CategoryID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    if (!$this->CheckProductCategories($ProductID,$CategoryID)) {
      $Products = new mensio_products();
      if (!$Products->Set_UUID($ProductID)) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Product code not correct';
      }
      if (!$Products->Set_Category($CategoryID)) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Category code not correct';
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        if (!$Products->InsertProductToCategory()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Category could not be added to product';
        }
      }
      unset($Products);
    }
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData = $this->LoadProductCategories($ProductID);
    }
    return $RtrnData;
  }
  public function RemoveCategoryFromProduct($ProductID,$CategoryID)  {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    if ($this->CheckProductCategories($ProductID,$CategoryID)) {
      $Products = new mensio_products();
      if (!$Products->Set_UUID($ProductID)) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Product code not correct<br>';
      }
      if (!$Products->Set_Category($CategoryID)) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Category code not correct<br>';
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        if ($Products->CheckIfCategoryInUse()) {
          $NoteType = 'Info';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'One or more category values are been used<br>Category could not be removed<br>';
        } else {
          if (!$Products->RemoveProductCategory()) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Category could not be removed<br>';
          }
        }
      }
      unset($Products);
    }
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData = $this->LoadProductCategories($ProductID);
    }
    return $RtrnData;
  }
  public function AddValueToProduct($ProductID,$ValueID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','ValueList'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    }
    if (!$Products->Set_AttributeValue($ValueID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Value code not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->InsertProductAttributeValue()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] = 'Value could not be added to product<br>';
      } else {
        $this->UpdateVariableProductValue($ProductID,$ValueID,'add');
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData['ValueList'] = $this->LoadProductAttributeValueList($ProductID);
    }
    return $RtrnData;
  }
  public function UpdateValueVisibility($ProductID,$ValueID,$Visibility) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    }
    $ValueID = str_replace('CHK_', '', $ValueID);
    if (!$Products->Set_AttributeValue($ValueID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Value code not correct<br>';
    }
    $Products->Set_Visibility($Visibility);
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->UpdateValueVisibility()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Value could not be updated<br>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function ProductRemoveValue($ProductID,$ValueID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','ValueList'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    }
    if (!$Products->Set_AttributeValue($ValueID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Value code not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->RemoveProductAttributeValue()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Value could not be removeed from product<br>';
      } else {
        $RtrnData = $this->UpdateVariableProductValue($ProductID,$ValueID,'remove');
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData['ValueList'] = $this->LoadProductAttributeValueList($ProductID);
    }
    return $RtrnData;
  }
  private function UpdateVariableProductValue($ProductID,$ValueID,$action) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','ValueList'=>'');
    $DataSet = array();
    $Products = new mensio_products();
    if ($Products->Set_UUID($ProductID)) {
      $DataSet = $Products->LoadProductVariationList();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if (!$Products->Set_UUID($Row->uuid)) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Product code not correct<br>';
          }
          if (!$Products->Set_AttributeValue($ValueID)) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Value code not correct<br>';
          }
          if ($RtrnData['ERROR'] === 'FALSE') {
            switch($action) {
              case 'add':
                if (!$Products->InsertProductAttributeValue()) {
                  $NoteType = 'Alert';
                  $RtrnData['ERROR'] = 'TRUE';
                  $RtrnData['Message'] .= 'Value could not be added to product<br>';
                }
                break;
              case 'remove':
                if (!$Products->RemoveProductAttributeValue()) {
                  $NoteType = 'Alert';
                  $RtrnData['ERROR'] = 'TRUE';
                  $RtrnData['Message'] .= 'Value could not be removeed from product<br>';
                }
                break;
            }
          }
        }
      }
    }
    unset($Products);
    return $RtrnData;
  }
  public function UpdateProductImageList($ProductID,$ImgList) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','ImgList'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    } else {
      $ImgList = stripslashes($ImgList);
      $ImgList = json_decode($ImgList,true);
      if (is_array($ImgList)) {
        foreach ($ImgList as $Row) {
          if ($Products->Set_Image($Row['Image']) ) {
            if (!$Products->ProductImageFound()) {
              if (!$Products->InsertProductImage()) {
                $NoteType = 'Alert';
                $RtrnData['ERROR'] = 'TRUE';
                $RtrnData['Message'] .= 'Image "'.$Row['Image'].'" could not be saved<br>';
              }
            }
          } else {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Image "'.$Row['Image'].'" not correct<br>';
          }
        }
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData= $this->LoadProductImageList($ProductID);
    }
    return $RtrnData;
  }
  public function ProductUpdateMainImage($ProductID,$Image) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Image'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    }
    if ($Products->Set_ImageID($Image) ) {
      if (!$Products->UpdateMainImage()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Image could not be set as main<br>';
      } else {
        $RtrnData['Message'] .= 'Image '.$Image.' for product '.$ProductID.'WAS set as main<br>';
        $DataSet = $Products->LoadProductRecordImages();
        if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
          foreach ($DataSet as $Row) {
            if ($Row->main) {
              $RtrnData['Image'] = '
                <div class="ProdImgWrapper">
                  <img src="'.get_site_url().'/'.$Row->file.'" alt="file_image">
                  <div class="DivResizer"></div>
                </div>';
            }
          }
        }
      }
    } else {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Image code not correct<br>';
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function ProductRemoveImage($ProductID,$Image) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    }
    if (!$Products->Set_ImageID($Image) ) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Image code not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->RemoveProductImage()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Image could not be removed<br>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData= $this->LoadProductImageList($ProductID);
    }
    return $RtrnData;
  }
  public function  LoadProductAdvantagesForm() {
    $MdlForm = '
    <div class="ModalProductTypeSelector">
      <label class="label_symbol">New Advantage</label>
      <input type="text" id="FLD_Advantage" class="mdl-form-control" value="">
      <button id="BTN_ModalSaveAdv" class="button" title="Save">
        <i class="fa fa-floppy-o" aria-hidden="true"></i>
      </button>
    </div>';
    return $this->CreateModalWindow('Product Advantages', $MdlForm);
  }
  public function AddProductAdvantages($ProductID,$LangID,$Advantage) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    }
    if (!$Products->Set_Language($LangID) ) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Language code not correct<br>';
    }
    if (!$Products->Set_Advantage($Advantage) ) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Advantage text not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->InsertNewProductAdvantage()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Advantage could not be added<br>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData= $this->LoadAdvantagesList($ProductID,$LangID);
    }
    return $RtrnData;
  }
  public function RemoveProductAdvantage($ProductID,$LangID,$Advantage) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_AdvantageID($Advantage) ) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Advantage code not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->RemoveProductAdvantage()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Advantage could not be removed<br>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData= $this->LoadAdvantagesList($ProductID,$LangID);
    }
    return $RtrnData;
  }
  public function LoadProductTagsForm($TagID) {
    $TagText = '';
    if ($TagID !== 'NewTag') {
      $Products = new mensio_products();
      if ($Products->Set_TagsID($TagID)) {
        $DataSet = $Products->LoadProductTags();
        if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
          foreach ($DataSet as $Row) {
            $TagText = $Row->tags;
          }
        }
      }
      unset($Products);
    }
    $MdlForm = '
    <div class="ModalProductTypeSelector">
      <label class="label_symbol">Tags</label>
      <input type="hidden" id="FLD_TagID" value="'.$TagID.'">
      <textarea type="text" id="FLD_TagText" class="mdl-form-control">'.$TagText.'</textarea>
      <button id="BTN_ModalSaveTags" class="button" title="Save">
        <i class="fa fa-floppy-o" aria-hidden="true"></i>
      </button>
      <button id="BTN_ModalClearTags" class="button" title="New">
        <i class="fa fa-plus" aria-hidden="true"></i>
      </button>
    </div>';
    return $this->CreateModalWindow('Product Tags', $MdlForm);    
  }
  public function UpdateProductTags($ProductID,$Tag,$Text) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','TagID'=>'','Tags'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    }
    if ($Tag === 'NewTag') { $Tag = $Products->Get_NewProductCode(); }
    if (!$Products->Set_TagsID($Tag)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Tag code not correct<br>';
    }
    if (!$Products->Set_Tags($Text)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Text not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->UpdateProductTagList()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Tag could not be updated<br>';
      }else {
        $RtrnData['TagID'] = $Tag;
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $Tags = $this->LoadProductTagsList($ProductID);
      $RtrnData['Tags'] = $Tags['Tags'];
    }
    return $RtrnData;
  }
  public function RemoveProductTag($ProductID,$Tag) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if ($Tag !== 'NewTag') {
      if (!$Products->Set_TagsID($Tag)) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Tag code not correct<br>';
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        if (!$Products->RemoveProductTag()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Tag could not be removed<br>';
        }
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData = $this->LoadProductTagsList($ProductID);
    }
    return $RtrnData;
  }
  private function CreateBarcodeTypeSelections() {
    $BCType = array ('Options'=>'','Selections'=>'');
    $Products = new mensio_products();
    $DataSet = $Products->LoadProductBarcodesTypes();
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      $BCType['Options'] = '<option value="0">Barcode Type</option>';
      foreach ($DataSet as $Row) {
        $Main = '<div id="Pbl_'.$Row->uuid.'" class="RgTpBtnsGreen Brcbtns BCTypePublic" title="Select as public">
                <i class="fa fa-check-square" aria-hidden="true"></i>
              </div>';
        if ($Row->public) { $Main = ''; }
        $BCType['Options'] .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
        $BCType['Selections'] .= '<div class="TagDiv">
            <div class="TagText">
             '.$Row->name.'
            <div class="DivResizer"></div>
            </div>
            <div class="TagBtns">
              '.$Main.'
              <div id="Del_'.$Row->uuid.'" class="RgTpBtns Brcbtns BCTypeDelete" title="Delete">
                <i class="fa fa-times" aria-hidden="true"></i>
              </div>
            </div>
           <div class="DivResizer"></div>
           </div>';
      }
    }
    unset($Products);
    return $BCType;
  }
  public function LoadProductBarcodesForm() {
    $BCType = $this->CreateBarcodeTypeSelections();
    $MdlForm = '
    <div class="ModalProductTypeSelector">
      <div id="MDL_BarcodesDIV">
        <label class="label_symbol">Barcode Type</label>
        <div class="InputDivWrapper">
          <div class="BtnDivWrapper">
            <div id="BTN_AddNewBCType" class="BtnSwMdl" title="Add New Barcode Type">
              <i class="fa fa-plus" aria-hidden="true"></i>
            </div>              
          </div>
          <div class="InputDiv">
            <select id="FLD_BCTypeOptions" class="mdl-form-control">
              '.$BCType['Options'].'
            </select>
          </div>
        </div>
        <input type="text" id="FLD_Barcode" class="mdl-form-control" value="">
        <button id="BTN_ModalSaveBarCodes" class="button" title="Save">
          <i class="fa fa-floppy-o" aria-hidden="true"></i>
        </button>
      </div>
      <div id="MDL_BarcodeTypesDIV">
        <label class="label_symbol">Insert Barcode Type</label>
        <div class="InputDivWrapper">
          <div class="InputDiv">
            <input type="text" id="FLD_BCType" class="mdl-form-control" value="">
          </div>
          <div class="BtnDivWrapper">
            <div id="BTN_SaveBCType" class="BtnSwMdl" title="Save">
              <i class="fa fa-plus" aria-hidden="true"></i>
            </div>
          </div>
        </div>
        <label class="label_symbol">Barcode Type List</label>
        <div id="MDL_BCTypeList">
          '.$BCType['Selections'].'
        </div>
        <button id="BTN_ModalBackToBCode" class="button" title="Back">
          <i class="fa fa-arrow-left" aria-hidden="true"></i>
        </button>
      </div>
    </div>';
    return $this->CreateModalWindow('Product Barcodes', $MdlForm);
  }
  public function AddBarcodeType($BCType) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Options'=>'','Selections'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_Name($BCType)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Barcode type name not correct<br>';
    } else {
      if (!$Products->InsertNewBarcodeType()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Barcode type could not be saved<br>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $BCType = $this->CreateBarcodeTypeSelections();
      $RtrnData['Options'] = $BCType['Options'];
      $RtrnData['Selections'] = $BCType['Selections'];
    }
    return $RtrnData;
  }
  public function SetProductPublicBarcode($BCType) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Options'=>'','Selections'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_BarcodeType($BCType)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Barcode type not correct<br>';
    } else {
      if (!$Products->UpdatePublicBarcode()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Barcode type could not be set as public<br>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $BCType = $this->CreateBarcodeTypeSelections();
      $RtrnData['Options'] = $BCType['Options'];
      $RtrnData['Selections'] = $BCType['Selections'];
    }
    return $RtrnData;
  }
  public function RemoveBarcodeType($BCType) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Options'=>'','Selections'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_BarcodeType($BCType)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Barcode type not correct<br>';
    } else {
      if ($Products->CheckIfBCTypeInUse()) {
        $NoteType = 'Info';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Barcode type is in use. Can not be deleted<br>';
      } else {
        if (!$Products->RemoveBarcodeType()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Barcode type could not be deleted<br>';
        }
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $BCType = $this->CreateBarcodeTypeSelections();
      $RtrnData['Options'] = $BCType['Options'];
      $RtrnData['Selections'] = $BCType['Selections'];
    }
    return $RtrnData;
  }
  public function AddProductBarcode($ProductID,$Type,$Barcode) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    }
    if (!$Products->Set_BarcodeType($Type)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Barcode type not correct<br>';
    }
    if (!$Products->Set_Barcode($Barcode)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Barcode not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->InsertNewProductBarcode()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Barcode could not be saved<br>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData = $this->LoadBarcodesList($ProductID);
    }
    return $RtrnData;
  }
  public function RemoveProductBarcode($ProductID,$BarcodeID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    }
    if (!$Products->Set_BarcodeID($BarcodeID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Barcode code not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->RemoveProductBarcode()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Barcode could not be deleted<br>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData = $this->LoadBarcodesList($ProductID);
    }
    return $RtrnData;
  }
  public function UpdateProductFileList($ProductID,$Type,$FileList) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    } 
    $TypeID = $Products->FindTypeID($Type);
    if (!$Products->Set_FileType($TypeID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product type code not correct<br>';
    } 
    if ($RtrnData['ERROR'] === 'FALSE') {
      $FileList = stripslashes($FileList);
      $FileList = str_replace('"Image"','"File"',$FileList);
      $FileList = json_decode($FileList,true);
      if (is_array($FileList)) {
        foreach ($FileList as $Row) {
          if ($Products->Set_File($Row['File']) ) {
            if (!$Products->ProductFileFound()) {
              if (!$Products->InsertProductFile()) {
                $NoteType = 'Alert';
                $RtrnData['ERROR'] = 'TRUE';
                $RtrnData['Message'] .= 'Image "'.$Row['File'].'" could not be saved<br>';
              }
            }
          } else {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Image "'.$Row['File'].'" not correct<br>';
          }
        }
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData= $this->LoadProductFileList($ProductID);
    }
    return $RtrnData;
  }
  public function RemoveProductFile($ProductID,$FileID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    }
    if (!$Products->Set_FileID($FileID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'File code not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->RemoveProductFile()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'File could not be removed<br>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData = $this->LoadProductFileList($ProductID);
    }
    return $RtrnData;
  }
  public function LoadProductFIleExpirationForm($File) {
    $MdlForm = '
    <div class="ModalProductTypeSelector">
        <input type="hidden" id="FLD_File" value="'.$File.'">
        <label class="label_symbol">Expiration Date</label>
        <input type="text" id="FLD_Expiration" class="mdl-form-control" value="">
        <button id="BTN_ModalSaveExpiration" class="button" title="Save">
          <i class="fa fa-floppy-o" aria-hidden="true"></i>
        </button>
    </div>';
    return $this->CreateModalWindow('File Expiration Date', $MdlForm);
  }
  public function UpdateProductFileExpiration($ProductID,$FileID,$Expiration) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_FileID($FileID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'File code not correct<br>';
    }
    if (!$Products->Set_Available($Expiration)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Date given not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->UpdateFileExpiration()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Expiration date could not be updated<br>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData = $this->LoadProductFileList($ProductID);
    }
    return $RtrnData;
  }
  public function LoadProductBundleForm() {
    $MdlForm = '
    <div class="ModalProductTypeSelector">
        <div id="MDL_SearchDiv">
          <label class="label_symbol">Search Product</label>
          <input type="text" id="FLD_SearchProduct" class="mdl-form-control" value="">
          <div id="MDL_SearchResults"></div>
          <div id="VariationListDiv">
            <div id="VariationList"></div>
            <div class="button_row">
              <button id="Btn_CloseVarBundleSlctr" class="button BTN_Back" title="Close">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
              </button>
            </div>
            <div class="DivResizer"></div>
          </div>
        </div>
        <div id="MDL_AmountDiv">
          <input type="hidden" id="FLD_SlctdPrdct" class="mdl-form-control" value="">
          <label class="label_symbol">Insert Amount</label>
          <input type="text" id="FLD_SetAmount" class="mdl-form-control" value="">
          <button id="BTN_ModalSaveBundle" class="button" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>
        </div>
    </div>';
    return $this->CreateModalWindow('Product Selection', $MdlForm);
  }
  public function SearchProductForBundle($ProductID,$Search) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Results'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    }
    if (!$Products->Set_SearchString($Search)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with the search string '.$Search.'<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $DataSet = $Products->LoadProductsDataSet();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $RtrnData['Results'] = '<table id="" class="InfoTable">
                <thead>
                  <tr>
                    <th class="ImgCol"></th>
                    <th>Product</th>
                    <th class="BtnCol"></th>
                  </tr>
                </thead>
                <tbody>';
        foreach ($DataSet as $Row) {
          if (!$Row->isbundle) {
            $VarBtn = '';
            $Products->Set_UUID($Row->uuid);
            if ($Products->CheckIfProductHasVariations()) {
              $VarBtn = '<div id="BTN_Vrtn_'.$Row->uuid.'" class="BundleBtns SelectVariation" title="Select Variation">
                        <i class="fa fa-files-o" aria-hidden="true"></i>
                      </div>';
            }
            $RtrnData['Results'] .= '<tr>
                <td class="ImgCol"><img src="'.get_site_url().'/'.$Row->file.'" alt="file_image"></td>
                <td><span class="RelProdCode">'.$Row->code.'</span>'.$Row->name.'</td>
                <td class="BtnCol">
                  <div id="BTN_Slct_'.$Row->uuid.'" class="BundleBtns SelectProduct" title="Add Product">
                    <i class="fa fa-check" aria-hidden="true"></i>
                  </div>
                  '.$VarBtn.'
                </td>
              </tr>';
          }
        }
        $RtrnData['Results'] .= '</tbody></table>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadProductVariationForBundle($ProductID) {
    $VarTbl = '<table id="" class="InfoTable">
                <thead>
                  <tr>
                    <th class="ImgCol"></th>
                    <th>Product</th>
                    <th class="BtnCol"></th>
                  </tr>
                </thead>
                <tbody>';
    $Products = new mensio_products();
    if ($Products->Set_UUID($ProductID)) {
      $DataSet = $Products->LoadProductVariationList(false);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $VarTbl .= '<tr>
                <td class="ImgCol"><img src="'.get_site_url().'/'.$Row->file.'" alt="file_image"></td>
                <td><span class="RelProdCode">'.$Row->code.'</span>'.$Row->name.'</td>
                <td class="BtnCol">
                  <div id="BTN_Slct_'.$Row->uuid.'" class="BundleBtns SelectProduct" title="Add Product">
                    <i class="fa fa-check" aria-hidden="true"></i>
                  </div>
                </td>
              </tr>';
        }
      }
    }
    $VarTbl .= '</tbody></table>';
    return $VarTbl;
  }
  function AddProductToBundle($BundleID,$ProductID,$Amount) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_Bundle($BundleID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Bundle code not correct<br>';
    }
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    }
    if (!$Products->Set_Amount($Amount)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Amount given was not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->CheckIfProductInBundle()) {
        if (!$Products->InsertProductToBundle()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Product could not be added to bundle<br>';
        }
      } else {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Product allready in bundle<br>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData= $this->LoadProductBundleList($BundleID);
    }
    return $RtrnData;
  }
  public function RemoveProductFromBundle($BundleID,$ProductID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_Bundle($BundleID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Bundle code not correct<br>';
    }
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->RemoveProductFromBundle()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Product could not be removed from bundle<br>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData= $this->LoadProductBundleList($BundleID);
    }
    return $RtrnData;
  }
  public function UpdateProductVisibility($ProductList,$Check) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    if ($Check === 'Checked') { $Check = '1'; } else { $Check = '0'; }
    $Products = new mensio_products();
    $IDs = explode(';',$ProductList);
    foreach ($IDs as $ProductID) {
      if ($ProductID !== '') {
        if (!$Products->Set_UUID($ProductID)) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Product code not correct<br>'.$ProductID;
        } else {
          $Products->Set_Visibility($Check);
          if (!$Products->UpdateProductVisibility()) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Product visibility could not be updated<br>'.$ProductID;
          }
        }
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function DeleteProductData($ProductID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product code not correct<br>';
    } else {
      if ($Products->CheckIfProductInOrders()) {
        $NoteType = 'Info';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Product is in use. Can not be deleted<br>';
      }
      if (MENSIO_FLAVOR === 'STD') {
        if ($Products->CheckIfProductHasVariations()) {
          $NoteType = 'Info';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Product has variations. Can not be deleted<br>';
        }
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        if (!$Products->DeleteProductRecord()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Product could not be deleted<br>';
        } else {
          $NoteType = 'Success';
          $RtrnData['ERROR'] = 'FALSE';
          $RtrnData['Message'] .= 'Product deleted successfully<br>';
        }
      }
    }
    unset($Products);
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  public function SearchForVariableProduct($ProductID) {
    $NoteType = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Variations'=>'');
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product code not correct';
    } else {
      $DataSet = $Products->LoadProductVariationList();
      $Products->Set_Language($Products->LoadAdminLang());
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $LftClr = 'VarElActive';
          $LblActive = 'Yes';
          if (!$Row->visibility) {
            $LftClr = 'VarElInactive';
            $LblActive = 'No';
          }
          $Products->Set_Variation($Row->uuid);
          $VarName = $Products->LoadVariationName();
          $VarMainImage = $Products->LoadProductRecordMainImage($Row->uuid);
          if ($Row->status === 'StockRelated') { $StatusName = 'Stock Related'; }
            else { $StatusName = $Products->LoadStatusName($Row->status); }
          $RtrnData['Variations'] .= '<div class="PrdVarElement">
            <div class="VarElColor '.$LftClr.'"></div>
            <div class="VarElImg">
              <img src="'.$VarMainImage.'" alt="'.$VarMainImage.'" alt="file_image">
            </div>
            <div class="VarElCode">
              <span id="VarElHvr_'.$Row->uuid.'" class="VarElCodeSelection">
                '.$Row->code.' '.$VarName.'
              </span>
            </div>
            <div class="VarElBtns">
              <div id="SetMain_'.$Row->uuid.'" class="RgTpBtns VarSetMain" title="Set as Main Product">
                <i class="fa fa-home" aria-hidden="true"></i>
              </div>
              <div id="VarEdit_'.$Row->uuid.'" class="RgTpBtns VarEdit" title="Edit Variable Product Attributes">
                <i class="fa fa-pencil" aria-hidden="true"></i>
              </div>
              <div id="VarTrans_'.$Row->uuid.'" class="RgTpBtns VarTrans" title="Edit Variable Product Name">
                <i class="fa fa-comment" aria-hidden="true"></i>
              </div>
              <div id="VarDel_'.$Row->uuid.'" class="RgTpBtns VarDel" title="Delete Variable Product">
                <i class="fa fa-times" aria-hidden="true"></i>
              </div>
            </div>
            <div id="VarElInfo_'.$Row->uuid.'" class="ProductVariationInfo">
              <h3>'.$VarName.'</h3>
              <div class="VarPrdDfltVals">
                <table class="VarElInfoTbl">
                  <tr><td class="VarLblCol">Code</td><td>'.$Row->code.'</td></tr>
                  <tr><td class="VarLblCol">Status</td><td>'.$StatusName.'</td></tr>
                  <tr><td class="VarLblCol">Available</td><td>'.date("d/m/Y", strtotime($Row->available)).'</td></tr>
                  <tr><td class="VarLblCol">Bus. Price</td><td>'.($Row->btbprice + 0).'</td></tr>
                  <tr><td class="VarLblCol">Bus. Tax(%)</td><td>'.($Row->btbtax + 0).'</td></tr>
                  <tr><td class="VarLblCol">Price</td><td>'.($Row->price + 0).'</td></tr>
                  <tr><td class="VarLblCol">Tax(%)</td><td>'.($Row->tax + 0).'</td></tr>
                  <tr><td class="VarLblCol">Discount(%)</td><td>'.($Row->discount + 0).'</td></tr>
                  <tr><td class="VarLblCol">Stock</td><td>'.($Row->stock + 0).'</td></tr>
                  <tr><td class="VarLblCol">Min Stock</td><td>'.($Row->minstock + 0).'</td></tr>
                </table>
              </div>
              <div class="VarPrdDfltAttrs">
                <table id="TBL_AttributeValues">
                  <thead>
                    <tr><th class="AttrCol">Attribute</th><th>Value</th></tr>
                  </thead>
                  <tbody id="AttValLst">
                    '.$this-> LoadProductAttributeValueList($Row->uuid,true).'
                  </tbody>
                </table>
              </div>
            </div>
            <div class="DivResizer"></div>
          </div>';
        }
      }
    }
    unset($DataSet,$Products);
    return $RtrnData;
  }
  public function LoadVariationProductForm($ProductID,$VariationID) {
    $NewEntry = false;
    $MdlForm = '';
    $VarCode = '';
    $VarBtBPrice = '';
    $VarBtBTax = '';
    $VarPrice = '';
    $VarTax = '';
    $VarDiscount = '';
    $StatusSwitch = 'value="0"';
    $VarStatus = '';
    $StockStatus = 'empty';
    $StockStatusTable = '';
    $VarAvailable = '';
    $VarStock = '';
    $VarMinStock = '';
    $ID = $VariationID;
    if ($ID === 'NewProduct') {
      $ID = $ProductID;
      $NewEntry = true;
    }
    $Products = new mensio_products();
    if ($Products->Set_UUID($ID)) {
      $DataSet = $Products->LoadVariationProductRecordData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $VarCode = $Row->code;
          if ($NewEntry) { $VarCode .= '-Var-'.($Products->GetCodeVars($VarCode) + 1); }
          $VarBtBPrice = $Row->btbprice+0;
          $VarBtBTax = $Row->btbtax;
          $VarPrice = $Row->price+0;
          $VarTax = $Row->tax;
          $VarDiscount = $Row->discount;
          $VarAvailable = date('Y-m-d', strtotime($Row->available));
          $VarStatus = $Row->status;
          if ($VarStatus === 'StockRelated') {
            $StatusSwitch = 'value="1" checked';
            $ExtraData = $this->LoadProductStockStatus($Row->uuid);
            $StockStatus = $ExtraData['Values'];
            $StockStatusTable = $ExtraData['Table'];
          }
          $VarStock = $Row->stock+0;
          $VarMinStock = $Row->minstock+0;
          if ($Row->visibility) {
            $VarVisibility = '<input type="checkbox" id="MDL_VarVisible" value="1" class="form-control form-check VarPrdField" checked>';
          } else {
            $VarVisibility = '<input type="checkbox" id="MDL_VarVisible" value="0" class="form-control form-check VarPrdField">';
          }
        }
      }
    }
    unset($Products);
    $Images = $this->LoadVariationImageList($ProductID,$VariationID);
    $MdlForm = '<div class="MdlFormWraper">
              <div id="VariationMnsModal" class="Modal_Wrapper"></div>
              <div id="Mdltabs">
                <ul>
                  <li><a href="#MDLInfoDiv">
                    <i class="fa fa-info" aria-hidden="true"></i>
                    General
                  </a></li>
                  <li><a href="#MDLPricesDiv">
                    <i class="fa fa-dollar" aria-hidden="true"></i>
                    Prices
                  </a></li>
                  <li><a href="#MDLAttrDiv">
                    <i class="fa fa-th-list" aria-hidden="true"></i>
                    Properties
                  </a></li>
                  <li><a href="#MDLImagesDiv">
                    <i class="fa fa-picture-o" aria-hidden="true"></i>
                    Images
                  </a></li>
                </ul>
                <!-- -------------------- INFO DIV -------------------- -->
                <div id="MDLInfoDiv" class="VarPrdTab">
                  <input type="hidden" id="MDL_VarProductID" value="'.$VariationID.'" class="">
                  <input type="hidden" id="MDL_MainImageID" value="'.$Images['MainImage'].'" class="VarPrdField">
                  <input type="hidden" id="MDL_ImageListID" value="'.$Images['ListIDs'].'" class="VarPrdField">
                  <input type="hidden" id="MDL_NewImages" value="Empty" class="VarPrdField">
                  <label class="label_symbol">Code</label>
                  <input type="text" id="MDL_VarCode" value="'.$VarCode.'" class="form-control VarPrdField">
                  <div id="MDL_VarCodeMsg">
                    <p>Code is allready in use. Please change the product code!</p>
                  </div>
                  <div class="SwitchAreaCtrl">
                    <label class="label_symbol">Availablility Status</label>
                    <label class="switch">
                      <input id="FLD_StockRelatedVariation" type="checkbox" '.$StatusSwitch.'>
                      <span id="SLD_StockRelatedVariation" class="slider"></span>
                    </label>
                    <span class="Stocklbl">Stock Related</span>
                  <div class="DivResizer"></div>
                  </div>
                  <div id="GenericVariationStatusTab" class="StatusTag">
                    <select id="MDL_VarStatus" class="form-control VarPrdField">
                      '.str_replace(
                       'value="'.$VarStatus.'"',
                       'value="'.$VarStatus.'" selected',
                       (str_replace(
                          '<option value="0" selected>All Status</option>',
                          '',
                          $this->LoadStatusOptions())
                       )).'
                    </select>
                  </div>
                  <div id="VariationStockStatusTab" class="StatusTag">
                    <input type="hidden" id="FLD_VarStockStatus" value="'.$StockStatus.'" class="VarPrdField">
                    <table id="Tbl_VarStockStatus" class="ProductSubTable">
                      <thead>
                        <tr>
                          <th class="ModalStatusCtrlColBig">
                            <div id="BTN_VarAddNewStockStatus" class="StatusHeaderCol" title="Add New Stock Status">
                              <i class="fa fa-plus fa-lg" aria-hidden="true"></i>
                            </div>
                            <div id="BTN_VarCopyStockStatus" class="StatusHeaderCol" title="Copy Existing Stock Status">
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
                      <tbody id="Tbl_VarStockStatus_Body">'.$StockStatusTable.'</tbody>
                    </table>
                  </div>
                  <label class="label_symbol">Available</label>
                  <input type="text" id="MDL_VarAvailable" value="'.$VarAvailable.'" class="form-control VarPrdField">
                  <label class="label_symbol">Stock</label>
                  <input type="text" id="MDL_VarStock" value="'.$VarStock.'" class="form-control VarPrdField">
                  <label class="label_symbol">Minimum Stock</label>
                  <input type="text" id="MDL_VarMinStock" value="'.$VarMinStock.'" class="form-control VarPrdField">
                  <div class="ProdSubSelection">
                    <label class="label_symbol">Visible</label>
                    '.$VarVisibility.'
                  </div>
                </div>
                <!-- ------------------- PRICES DIV ------------------- -->
                <div id="MDLPricesDiv" class="VarPrdTab">
                  <label class="label_symbol">Business Price</label>
                  <input type="text" id="MDL_VarBtBPrice" value="'.$VarBtBPrice.'" class="form-control VarPrdField">
                  <label class="label_symbol">Business Tax (%)</label>
                  <input type="text" id="MDL_VarBtBTax" value="'.$VarBtBTax.'" class="form-control VarPrdField">
                  <label class="label_symbol">Retail Price</label>
                  <input type="text" id="MDL_VarPrice" value="'.$VarPrice.'" class="form-control VarPrdField">
                  <label class="label_symbol">Retail Tax (%)</label>
                  <input type="text" id="MDL_VarTax" value="'.$VarTax.'" class="form-control VarPrdField">
                  <label class="label_symbol">Discount (%)</label>
                  <input type="text" id="MDL_VarDiscount" value="'.$VarDiscount.'" class="form-control VarPrdField">
                </div>
                <!-- ----------------- ATTRIBUTES DIV ----------------- -->
                <div id="MDLAttrDiv" class="VarPrdTab">
                  '.$this->LoadVariationProperties($ID).'
                </div>
                <!-- ------------------- IMAGES DIV ------------------- -->
                <div id="MDLImagesDiv" class="VarPrdTab">
                  '.$Images['ImageList'].'
                </div>
              </div>
              <div class="DivResizer"></div>
              <div class="button_row">
                <button id="BTN_VariationSave" class="button BtnGreen" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
              </div>
              <div class="DivResizer"></div>
        </div>';
    return $this->CreateModalWindow('Product Variation', $MdlForm);
  }
  public function LoadVariationImageList($ProductID,$VariationID) {
    $NoteType = '';
    $ImageList = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','MainImage'=>'','ImageList'=>'','ListIDs'=>'');
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product value not acceptable';
    }
    if ($VariationID !== 'NewProduct') {
      if (!$Products->Set_Variation($VariationID)) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] = 'Variant Product value not acceptable';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $MainImage = $Products->LoadProductRecordMainImage($VariationID);
      if ($VariationID !== 'NewProduct') { $DataSet = $Products->LoadProductRecordImages(false); }
        else { $DataSet = $Products->LoadProductRecordImages(); }
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $BasicImage = MENSIO_SHORTPATH.'/admin/icons/default/noimage.png';
        $RtrnData['ListIDs'] = '';
        foreach ($DataSet as $Row) {
          $img = get_site_url().'/'.$Row->file;
          if ($Row->file !== $BasicImage) {
            $Main = '';
            if ($img === $MainImage) {
              $Main = 'VarMainImg';
              $RtrnData['MainImage'] = $Row->uuid;
            }
            if ( $RtrnData['ListIDs'] === '') { $RtrnData['ListIDs'] .= $Row->uuid; }
              else { $RtrnData['ListIDs'] .= '::'.$Row->uuid; }
            $ImageList .= '<div id="VarPrdImg_'.$Row->uuid.'" class="ProdImgWrapper SlctImg '.$Main.'">
                  <img src="'.$img.'" alt="file_image">
                  <div class="ImgLstOverlay">
                    <div class="text">
                      <div id="SM_'.$Row->uuid.'" class="ImgBtn BTN_SetMain">Main</div>
                      <div id="DL_'.$Row->uuid.'" class="ImgBtn BTN_DelImg">Remove</div>
                    </div>
                  </div>
                  <div class="DivResizer"></div>
                </div>';
          }
        }
      }
      $RtrnData['ImageList'] = '
            <div class="ExtraSelectionsDiv">
              <div id="BTN_OpenModalMediaModal" class="ESBtnsDivs" title="Open Image Selector">
                <div class="ESBtns">
                  <i class="fa fa-plus" aria-hidden="true"></i>
                </div>
              </div>
            </div>
            <div class="ImageListDiv">
              <div id="VariationImageList" class="ImageList">
                '.$ImageList.'
              </div>
            </div>';
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadVariationProperties($ProductID) {
    $Attr = '';
    $Products = new mensio_products();
    if ($Products->Set_UUID($ProductID)) {
      $DataSet = $Products->LoadVariationAttributes();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Attr .= '<div id="'.$Row->uuid.'" class="VarCategoryAttribute ">
            '.$Row->catname.' / '.$Row->name.'</div>';
        }
      }
    }
    $PropertyTab = '
      <div class="VarAttrSelectDiv">
        <div id="VarAttrSelDiv" class="MDLCtrlDivs">
          <label class="label_symbol">Attributes</label>
          <hr>
          <div id="VarAttrSelDivWrapper">
            '.$Attr.'
          </div>
        </div>
        <div id="VarAttrValSelDiv" class="MDLCtrlDivs">
          <label class="label_symbol">Attribute Values</label>
          <hr>
          <div id="VarAttrValSelWrapper">
            <div class="WrapperEmpty"><div class="">Select Attribute</div></div>
          </div>
          <div id="VarAttrValSearcherDiv" class="">
            <input id="MDL_VarSearchAttrVal" class="Srchcontrol" value="" placeholder="Search ..." type="text">
          </div>
        </div>
      </div>
      <label class="label_symbol">Active Attribute Values</label>
      <hr>
      <div id="VarValueTableDiv" class="VarValueDiv">
        <table id="TBL_AttributeValues">
          <thead>
            <tr>
              <th class="AttrCol">Attribute</th>
              <th>Value</th>
            </tr>
          </thead>
          <tbody id="ValAttValLst">
           '.$this->LoadProductAttributeValueList($ProductID,true).'
          </tbody>
        </table>
      </div>';
    return $PropertyTab;
  }
  public function UpdateVariableProduct($Main,$ProductID,$Data,$Attributes) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','List'=>'','StockStatus'=>'','StockStatusTable'=>'');
    $NoteType = '';
    $ChkData = $this->UpdateMainVarData($Main,$ProductID,$Data);
    $Varid = $ChkData['ID'];
    if ($ChkData['ERROR'] === 'FALSE') {
      $ChkData = $this->UpdateVarAttrData($Main,$Varid,$Attributes);
    } else { 
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $this->SetNotification($NoteType,$ChkData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($ChkData['Message']);
    }
    if ($ChkData['ERROR'] === 'FALSE') {
      $ChkData = $this->LoadProductStockStatus($Varid);
      $RtrnData['StockStatus'] = $ChkData['Values'];
      $RtrnData['StockStatusTable'] = $ChkData['Table'];
      $ChkData = $this->SearchForVariableProduct($Main);
      $RtrnData['List'] = $ChkData['Variations'];
    } else { 
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $this->SetNotification($NoteType,$ChkData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($ChkData['Message']);
    }
    return $RtrnData;
  }
  private function UpdateMainVarData($Main,$ProductID,$JSONData) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','ID'=>'','StockRelated'=>false);
    $NewEntry = false;
    $ImgList = array();
    $NewImg = array();
    $MainImg = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($Main)) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product code not correct';
    } else {
      if ($ProductID === 'NewProduct') {
        $ProductID = $Products->Get_NewProductCode();
        $NewEntry = true;
      }
      if ($Products->Set_Variation($ProductID)) {
        $JSONData = str_replace('MDL_Var','FLD_',stripslashes($JSONData));
        $JSONData = str_replace('FLD_Var','FLD_',stripslashes($JSONData)); // for the variable FLDs
        $Data = json_decode($JSONData,true);
        if (is_array($Data)) {
          foreach ($Data as $Row) {
            if (substr($Row['Field'],0,4) === 'FLD_') {
              $SetValue = $this->FindSetFun($Row['Field']);
              if ($SetValue !== '') {
                if (!$Products->$SetValue($Row['Value'])) {
                  $RtrnData['ERROR'] = 'TRUE';
                  $RtrnData['Message'] .= 'Value "'.$Row['Value'].'" of the field '.str_replace('FLD_','',$Row['Field']).' is not correct<br>';
                }
              }
            } else {
              switch ($Row['Field']) {
                case 'MDL_ImageListID':
                  $ImgList = explode('::',$Row['Value']);
                  break;
                case 'MDL_MainImageID':
                  $MainImg = $Row['Value'];
                  break;
                case 'MDL_NewImages':
                  if ($Row['Value'] !== 'Empty') { $NewImg = explode('::',$Row['Value']); }
                  break;
                case 'MDL_VarStatus':
                  if ($Row['Value'] === 'StockRelated') { $RtrnData['StockRelated'] = true; }
                  break;
              }
            }
          }
        }
      }
    }
    if ($Products->CheckIfProductCodeExists(false)) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Code was found<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if ($NewEntry) {
        if (!$Products->AddNewVariationBasicData()) {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Variation could not be added<br>';
        } else {
          $Products->UpdateVariableIndex();
        }
      } else {
        if (!$Products->UpdateVariationMainData()) {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Variation could not be updated<br>';
        }
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        foreach ($ImgList as $Row) {
          if ($Row !== '') {
            if ($Products->Set_ImageID($Row)) {
              $Products->SetVariableMainImage();
              if (!$Products->VariableProductImageExists()) {
                $Products->InsertProductImage(false);
              }
            }
          }
        }
        foreach ($NewImg as $Row) {
          if ($Row !== '') {
            if ($Products->Set_Image($Row)) {
              $Products->InsertProductImage(false);
            }
          }
        }
        if ($Products->Set_ImageID($MainImg)) {
          $Products->SetVariableMainImage();
          if (!$Products->UpdateVariableMainImage()) {
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Main Image could not be set<br>';
          }
        }
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Products->Set_UUID($ProductID);
      if (!$Products->UpdateProductStockStatus()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Stock Status list could not be updated<br>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'FALSE') { $RtrnData['ID'] = $ProductID; }
    return $RtrnData;
  }
  private function UpdateVarAttrData($Main,$ProductID,$JSONData) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $Products = new mensio_products();
    if (!$Products->Set_UUID($Main)) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product code not correct';
    }
    if (!$Products->Set_Variation($ProductID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product code not correct';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->AddNewVariationCategories()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] = 'Default values failed to register';
      } else {
        $Data = json_decode(stripslashes($JSONData),true);
        if (is_array($Data)) {
          foreach ($Data as $Row) {
            if ($Products->Set_AttributeValue($Row['Value'])) {
              if (!$Products->UpdateVariableAttributeValue()) {
                $RtrnData['ERROR'] = 'TRUE';
                $RtrnData['Message'] .= 'Property Value could not be updated';
              }
            }
          }
        }
      }
    }
    unset($Products);
    return $RtrnData;
  }
  public function UpdateProductMain($ProductID,$Variation) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (!$Products->Set_UUID($ProductID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Product code not correct';
    }
    if (!$Products->Set_Variation($Variation)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Variation Product code not correct';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Products->UpdateMainProductRecord()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] = 'Changing the main product was Unsuccessful';
      } else {
        $NoteType = 'Success';
        $RtrnData['Message'] = 'Changing the main product was Successful';
      }
    }
    unset($Products);
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function LoadVariationTranslations($VariationID) {
    $RtrnData = '';
    $Products = new mensio_products();
    if ($Products->Set_Variation($VariationID)) {
      $Languages = new mensio_languages();
      $LangData = $Languages->LoadLanguagesData();
      unset($Languages);
      $TransFlds ='';
      if ((is_array($LangData)) && (!empty($LangData[0]))) {
        foreach ($LangData as $Row) {
          if ($Row->active) {
            if ($Products->Set_Language($Row->uuid)) {
              $TransName = $Products->LoadVariationName();
              $TransFlds .= '<label class="label_symbol">'.$Row->name.'</label>
                <input type="text" id="'.$Row->uuid.'" class="form-control VarTransFlds" value="'.$TransName.'">';
            }
          }
        }
      }
      $ModalBody = $TransFlds.'
          <input type="hidden" id="MDL_TransVariation" value="'.$VariationID.'">
          <button id="BTN_VarTransSave" class="button" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>';
      $RtrnData = $this->CreateModalWindow('Variation Product Name Translations',$ModalBody);
    }
    unset($Products);
    return $RtrnData;
  }
  public function UpdateVariationTranslations($ProductID,$VariationID,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if (($Products->Set_UUID($ProductID)) && ($Products->Set_Variation($VariationID))) {
      $RtrnData = array('ERROR'=>'FALSE','Message'=> $ProductID.' '.$VariationID);
      $Data = stripslashes($Data);
      $Data = json_decode($Data, true);
      if (is_array($Data)) {
        foreach ($Data as $Row) {
          if (!$Products->Set_Language($Row['Field'])) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Language id not correct<br>';
          }
          if (!$Products->Set_Name($Row['Value'])) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Language translation not correct<br>';
          }
          if ($RtrnData['ERROR'] === 'FALSE') {
            if (!$Products->UpdateVariationTranslation()) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Translation could not be updated<br>';
            }
          }
        }
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'FALSE' ) {
      $RtrnData = $this->SearchForVariableProduct($ProductID);
    }
    return $RtrnData;
  }
  public function LoadProductStockStatus($ProductID) {
    $RtrnData = array('Values'=>'','Table'=>'');
    $Products = new mensio_products();
    if ($Products->Set_UUID($ProductID)) {
      $DataSet = $Products->LoadStockStatus();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if ($RtrnData['Values'] === '') {
            $RtrnData['Values'] .= $Row->uuid.'::'.$Row->name.'::'.$Row->icon.'::'.$Row->color.'::'.$Row->stock;
          } else {
            $RtrnData['Values'] .= ';;'.$Row->uuid.'::'.$Row->name.'::'.$Row->icon.'::'.$Row->color.'::'.$Row->stock;
          }
          $RtrnData['Table'] .= '
          <tr id="Tbl_Ln_'.$Row->uuid.'">
            <td class="ModalStatusCtrlColBig">
              <div class="StockCtrlWrap">
                <div id="Dlt_Stk_'.$Row->uuid.'" class="StockStatusRemoveBtn" title="Delete">
                  <i class="fa fa-times" aria-hidden="true"></i>
                </div>
                <div id="Edt_Stk_'.$Row->uuid.'" class="StockStatusEditBtn" title="Edit">
                  <i class="fa fa-pencil" aria-hidden="true"></i>
                </div>
                <div id="Trs_Stk_'.$Row->uuid.'" class="StockStatusTransBtn" title="Translations">
                  <i class="fa fa-comments" aria-hidden="true"></i>
                </div>
                <div class="DivResizer"></div>
              </div>
            </td>
            <td class="ModalStatusIcon">
              <img src="'.get_site_url().'/'.$Row->icon.'" alt="Available">
            </td>
            <td class="ModalStatusIcon">
              <div class="StatusColorBox" attr-bckgrnd="'.$Row->color.'" style="background:'.$Row->color.';"></div>
            </td>
            <td class="ModalStatusIcon">'.$Row->stock.'</td>
            <td>'.$Row->name.'</td>
          </tr>';
        }
      }
    }
    return $RtrnData;
  }
  public function RefreshProductStockStatusTable($List) {
    $TableRows = '';
    $List = explode(';;',$List);
    foreach ($List as $Entry) {
        $tst = str_replace('::', '', $Entry);
        $tst = str_replace('undefined', '', $tst);
        if ($tst !== '') {
        $Row = explode('::',$Entry);
        if ($Row[0] === 'NewStockStatus') {
          $Row[0] .= '_'.$Row[4];
          $Ctrl = '<div id="Dlt_Stk_'.$Row[0].'" class="StockStatusRemoveBtn" title="Delete">
                    <i class="fa fa-times" aria-hidden="true"></i>
                  </div>';
        } else {
          $Ctrl = '<div id="Dlt_Stk_'.$Row[0].'" class="StockStatusRemoveBtn" title="Delete">
                    <i class="fa fa-times" aria-hidden="true"></i>
                  </div>
                  <div id="Edt_Stk_'.$Row[0].'" class="StockStatusEditBtn" title="Edit">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                  </div>
                  <div id="Trs_Stk_'.$Row[0].'" class="StockStatusTransBtn" title="Translations">
                    <i class="fa fa-comments" aria-hidden="true"></i>
                  </div>';
        }
        $Row[2] = str_replace(get_site_url().'/', '', $Row[2]);
        $TableRows .= '
          <tr id="Tbl_Ln_'.$Row[0].'">
            <td class="ModalStatusCtrlColBig">
              <div class="StockCtrlWrap">
                '.$Ctrl.'
                <div class="DivResizer"></div>
              </div>
            </td>
            <td class="ModalStatusIcon">
              <img src="'.get_site_url().'/'.$Row[2].'" alt="Available">
            </td>
            <td class="ModalStatusIcon">
              <div class="StatusColorBox" attr-bckgrnd="'.$Row[3].'" style="background:'.$Row[3].';"></div>
            </td>
            <td class="ModalStatusIcon">'.$Row[4].'</td>
            <td>'.$Row[1].'</td>
          </tr>';
      }
    }
    return $TableRows;
  }
  public function LoadStockStatusModal($StatusID,$Type) {
    $ID = 'NewStockStatus';
    $Icon = MENSIO_PATH.'/admin/icons/default/empty.png';
    $Color = '';
    $Stock = '';
    $Description = '';
    if ($StatusID !== 'NewStockStatus') {
      $Products = new mensio_products();
      if ($Products->Set_Status($StatusID)) { $DataSet = $Products->LoadStockStatusData(); }
      unset($Products);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $ID = $Row->uuid;
          $Icon = get_site_url().'/'.$Row->icon;
          $Color = $Row->color;
          $Stock = $Row->stock;
          $Description = $Row->name;
        }
      }
    }
    if ($Type === 'Default') { $Btn = 'BTN_StockStatusSave'; }
      else { $Btn = 'BTN_VarStockStatusSave'; }
    $MdlForm = '
    <div class="ModalProductTypeSelector">
      <div id="StatusTransModal"></div>
      <div class="StatusInputDiv">
        <input type="hidden" id="MDL_Status_ID" value="'.$ID.'">
        <label class="label_symbol">Status Description (default language)</label>
        <input type="text" id="MDL_Status" value="'.$Description.'" class="mdl-form-control">
        <label class="label_symbol">Minimum stock to be active status</label>
        <input type="text" id="MDL_Stock" value="'.$Stock.'" class="mdl-form-control">
        <div class="DivResizer"></div>
        <div id="BrandsLogoDiv" class="IconWrapper">
          <label class="label_symbol">Status Icon</label>
          <div class="DIV_Cur_Img">
            <div class="Mns_Img_Container">
              <img id="DispImg" class="selectIm" src="'.$Icon.'" alt="image">
            </div>
            <div class="">
              <button id="Mns_OpenMediaModal" class="button Mns_Img_Btn" title="Open Image Selector">
                <i class="fa fa-picture-o" aria-hidden="true"></i>
              </button>
              <button id="Mns_ClearImg" class="button Mns_Img_Btn" title="Clear Image">
                <i class="fa fa-trash" aria-hidden="true"></i>
              </button>
              <input id="FLD_Logo" class="form-control" type="hidden" value="'.$Icon.'">
              <input type="hidden" id="DefaultImage" value="'.MENSIO_PATH.'/admin/icons/default/noimage.png">
            </div>
          </div>
        </div>
        <div class="ColorWraper">
          <label class="label_symbol">Brand Color</label>
          <input type="text" id="FLD_Color" class="" style="background: '.$Color.';color: '.$Color.';" value="'.$Color.'">
        </div>
        <div class="DivResizer"></div>
        <div class="button_row StStatusBtn">
          <button id="'.$Btn.'" class="button BtnGreen" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>
        </div>
        <div class="DivResizer"></div>
      </div>
    </div>';
    if ($Type === 'Default') { $RtrnData = $this->CreateModalWindow('Stock Related Status Form',$MdlForm); }
      else { $RtrnData = $this->CreateModalWindow('Variation Stock Related Status Form',$MdlForm, 'VariationMnsModal'); }
    return $RtrnData;
  }
  public function RemoveProductStockStatus($ProductID,$StatusID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','StockStatus'=>'','StockStatusTable'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if ($Products->Set_Status($StatusID)) {
      if (!$Products->DeleteProductStockStatusData()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Stock Status could not be deleted<br>';
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);    
    } else {
      $Data = $this->LoadProductStockStatus($ProductID);
      $RtrnData['StockStatus'] = $Data['Values'];
      $RtrnData['StockStatusTable'] = $Data['Table'];
    }
    return $RtrnData;
  }
  public function LoadModalStockStatusTranslations($Type,$StatusID) {
    $RtrnData = '';
    $Products = new mensio_products();
    if ($Products->Set_Status($StatusID)) {
      $Languages = new mensio_languages();
      $LangData = $Languages->LoadLanguagesData();
      unset($Languages);
      $TransFlds ='';
      if ((is_array($LangData)) && (!empty($LangData[0]))) {
        foreach ($LangData as $Row) {
          if ($Row->active) {
            if ($Products->Set_Language($Row->uuid)) {
              $TransName = $Products->GetStatusTranslation('Stock');
              $TransFlds .= '<label class="label_symbol">'.$Row->name.'</label>
                <input type="text" id="'.$Row->uuid.'" class="form-control StatusTransFlds" value="'.$TransName.'">';
            }
          }
        }
      }
      if ($Type === 'Default') { $btn = 'BTN_StatusTransSave'; }
        else { $btn = 'BTN_VarStatusTransSave'; }
      $ModalBody = $TransFlds.'
          <input type="hidden" id="StatusType" value="Stock">
          <input type="hidden" id="StatusTransID" value="'.$StatusID.'">
          <button id="'.$btn.'" class="button" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>';
      if ($Type === 'Default') { $RtrnData = $this->CreateModalWindow('Status Translations',$ModalBody); }
        else { $RtrnData = $this->CreateModalWindow('Stock Status Translations',$ModalBody, 'VariationMnsModal'); }
    }
    unset($Products);
    return $RtrnData;
  }
  public function LoadStockStatusCopyForm($Type) {
    $MdlForm = '
    <div class="CopyFormWrapper">
      <label class="label_symbol">Search Product</label>
      <input type="hidden" id="MDL_SearchFormType" value="'.$Type.'">
      <input id="MDL_SearchProduct" class="form-control" value="" type="text">
      <span class="infotext">Note: When you select to copy a product stock status related values it will remove all existing values. There will be no turning back</span>
      <div id="MDL_SearchResults"></div>
      <div class="DivResizer"></div>
    </div>';
    if ($Type === 'Default') { $RtrnData = $this->CreateModalWindow('Stock Status Copy Form',$MdlForm); }
      else { $RtrnData = $this->CreateModalWindow('Stock Status Copy Form',$MdlForm, 'VariationMnsModal'); }
    return $RtrnData;
  }
  public function LoadSearchStockStatusList($Search) {
    $RtrnData = '';
    $Products = new mensio_products();
    if ($Products->Set_SearchString($Search)) {
      $DataSet = $Products->LoadStockStatusSearch();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData .= '
            <div id="'.$Row->uuid.'" class="StockStatusSelection">
              <div class="PrdImg">
                <img src="'.get_site_url().'/'.$Row->file.'" alt="'.$Row->name.'">
              </div>
              <span class="PrdctCode">'.$Row->code.'</span>
              '.$Row->name.'
            </div>';
        }
      }
    }
    unset($Products);
    return $RtrnData;
  }
  public function CopyStockStatusFromProduct($ProductID,$CopyFromID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','StockStatus'=>'','StockStatusTable'=>'');
    $NoteType = '';
    $Products = new mensio_products();
    if ($ProductID !== 'NewProduct') {
      if (!$Products->Set_UUID($ProductID)) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Product id not correct<br>';
      } else {
        if (!$Products->ClearProductsStockStatus()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Product statuses could not be cleared<br>';
        }
      }
    }
    if (!$Products->Set_UUID($CopyFromID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Product id to copy from not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Products->Set_UUID($CopyFromID);
      $DataSet = $Products->LoadStockStatus();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if ($RtrnData['StockStatus'] === '') {
            $RtrnData['StockStatus'] .= 'NewStockStatus::'.$Row->name.'::'.$Row->icon.'::'.$Row->color.'::'.$Row->stock.'::'.$Row->uuid;
          } else {
            $RtrnData['StockStatus'] .= ';;NewStockStatus::'.$Row->name.'::'.$Row->icon.'::'.$Row->color.'::'.$Row->stock.'::'.$Row->uuid;
          }
        }
        $RtrnData['StockStatusTable'] = $this->RefreshProductStockStatusTable($RtrnData['StockStatus']);
      }
    }
    unset($Products);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);    
    }
    return $RtrnData;
  }
}