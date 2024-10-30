<?php
class Mensio_Admin_Products_Categories_Form extends mensio_core_form {
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->ActivePage = 'Categories';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-productbrands',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-products-categories.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-productbrands',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-products-categories.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function LoadCategoriesDataSet($InSearch,$InSorter,$JSONData) {
    $Error = false;
    $DataSet = array();
    $Categories = new mensio_products_categories();
    if ($InSearch !== '') {
      if (!$Categories->Set_SearchString($InSearch)) { $Error = true; }
    }
    if (!$Categories->Set_Sorter($InSorter)) { $Error = true; }
    $Categories->Set_ExtraFilters($JSONData);
    if (!$Error) {
      $DataSet = $Categories->LoadProductCategoriesDataSet();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Path = $Categories->LoadProductCategoryPath($Row->uuid);
          if ($Path !== '') { $Path .='/'; }
          $Row->name = $Path.$Row->name;
          if ($Row->image !== 'No Image') { $Row->image = get_site_url().'/'.$Row->image; }
            else { $Row->image = MENSIO_PATH.'/admin/icons/default/noimage.png'; }
        }
      }
    }
    unset($Categories);
    return $DataSet;
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='name',$InSearch='',$JSONData='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $ExtraOption = $this->CreateExtraOptions($JSONData);
    $tbl = new mensio_datatable();
    $tbl->Set_Sorter($InSorter);
    $DataSet = $this->LoadCategoriesDataSet($InSearch,$InSorter,$JSONData);
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array(
     'VIS'=>'Toggle Visible'
    ));
    $tbl->Set_ExtraActions($ExtraOption);
    $tbl->Set_EditColumn('name');
    $tbl->Set_EditOptionsSubline(array(
      'Visible','Edit','Delete'
    ));
    $tbl->Set_Columns(array(
      'uuid:uuid:plain-text',
      'name:Category:plain-text',
      'visibility:Visible:input-checkbox',
      'image:Icon:img'
    ));
    $RtrnTable = $tbl->CreateTable(
      'Categories',
      $DataSet,
      array('uuid','image','name','visibility')
    );
    unset($tbl);    
    return $RtrnTable;
  }
  private function CreateExtraOptions($JSONData) {
    $Selected = '0';
    $ExtraActions[0]['name'] = 'Parent';
    $ExtraActions[0]['options'] = '<option value="0">All Catergories</option>';
    $Categories = new mensio_products_categories();
    $DataSet = $Categories->LoadProductCategoriesDataSet();
    unset($Categories);
    $tst = stripslashes($JSONData);
    $JSONData = json_decode(stripslashes($JSONData),true);
    if ($JSONData !== '') {
      if (is_array($JSONData)) {
        foreach ($JSONData as $Row) { $Selected = $Row['Value']; }
      }
    }
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $ExtraActions[0]['options'] .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
      }
    }
    $ExtraActions[0]['options'] = str_replace('value="'.$Selected.'"','value="'.$Selected.'" selected',$ExtraActions[0]['options']);
    return $ExtraActions;
  }
  public function ToggleCategoriesVisibility($DataString) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    if (strlen($DataString) === 36) { $DataString = $DataString.';'; }
    $DataString = explode(';',$DataString);
    $Categories = new mensio_products_categories();
    foreach ($DataString as $Row) {
      if ($Row !== '') {
        if (!$Categories->Set_UUID($Row)) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Problem with Category ID<br>';
        } else {
          $RecData = $Categories->GetCategoryData();
          if (!empty($RecData)) {
            $Categories->Set_Visibility(false);
            if (!$RecData['visibility']) {
              $Categories->Set_Visibility(true);
            }
            if (!$Categories->UpdateCategoryRecord()) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Category '.$RecData['name'].' could not be updated<br>';
            }
          }
        }
      }
    }
    unset($Categories);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Category(ies) were updated successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  public function LoadCategoryData($CategoryID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Category'=>'','Name'=>'',
        'Visible'=>'', 'Image'=>'', 'Attributes'=>'','Values'=>'','Translations'=>'');
    $NoteType = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_UUID($CategoryID)) {
      $BsData = $Categories->GetCategoryData();
      if (!empty($BsData)) {
        $RtrnData['Category'] = $BsData['uuid'];
        $RtrnData['Name'] = $BsData['name'];
        $RtrnData['Slug'] = $Categories->GetCategorySlug();
        $RtrnData['Visible'] = $BsData['visibility'];
        $RtrnData['Image'] = get_site_url().'/'.$BsData['image'];
        $AttrData =  $this->LoadCategoryAttributes($CategoryID);
        $RtrnData['Attributes'] = $AttrData['Attributes'];
        $RtrnData['Values'] = $AttrData['Values'];
        $RtrnData['Translations'] = $this->LoadCategoryTranslations($CategoryID);
      } else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Category data not found !?!<br>';
      }
    } else {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Category id not correct<br>';
    }
    unset($Categories);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadCategoryTranslations($CategoryID) {
    $Btn = '<button id="BTN_CatTrans" class="button" title="Category Name Translations">
                  <i class="fa fa-comment" aria-hidden="true"></i>
                </button>';
    $RtrnData = '<table id="CatTransTbl"><tr><th class="LangCol">Language</th><th>Translation'.$Btn.'</th></tr>';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_UUID($CategoryID)) {
      $TransData = $Categories->GetCategoryTranslationList();
      if ((is_array($TransData)) && (!empty($TransData[0]))) {
        foreach ($TransData as $Row) {
          $RtrnData .= '<tr><td class="LangCol">'.$Row->langname.'</td><td>'.$Row->name.'</td></tr>';
        }
      }
    }
    $RtrnData .= '</table>';
    return $RtrnData;
  }
  public function LoadCategoryAttributes($CategoryID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'', 'Attributes'=>'', 'Values'=>'');
    $NoteType = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_UUID($CategoryID)) {
      $AttrData = $Categories->GetCategoryAttributes();
      if ((is_array($AttrData)) && (!empty($AttrData[0]))) {
        $RtrnData['Attributes'] = '';
        $RtrnData['Values'] = '';
        foreach ($AttrData as $Row) {
          $AttributeValues = $this->LoadCategoryAttributeValues($Row->uuid);
          $Check = '';
          $VisIcon = '<i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i>';
          if ($Row->visibility) {
            $Check = 'checked';
            $VisIcon = '<i class="fa fa-eye fa-lg" aria-hidden="true"></i>';
          }          
          $RtrnData['Attributes'] .= '
            <div class="NewAttrDiv">
              <div id="AttrID_'.$Row->uuid.'" class="AttrHeaderDiv">
                <span class="AttrNameSpan">
                  '.$VisIcon.'
                  '.$Row->name.'
                </span>
                <div id="Delete_'.$Row->uuid.'" class="AttrCtrlBtn BTN_RemoveAttr" title="Remove">
                  <i class="fa fa-times" aria-hidden="true"></i>
                </div>
                <div id="Trans_'.$Row->uuid.'" class="AttrCtrlBtn BTN_TransAttr" title="Translate">
                  <i class="fa fa-comment" aria-hidden="true"></i>
                </div>
                <div id="Edit_'.$Row->uuid.'" class="AttrCtrlBtn BTN_EditAttr" title="Edit">
                  <i class="fa fa-pencil" aria-hidden="true"></i>
                </div>
              <div class="DivResizer"></div>
              </div>
              <div id="DataDiv_'.$Row->uuid.'" class="AttrDataDiv">
                <input type="hidden" id="'.$Row->uuid.'" value="" class="form-control FldAttribute">
                <label class="label_symbol">Name</label>
                <input type="text" id="ATNM_'.$Row->uuid.'" value="'.$Row->name.'" class="form-control">
                <label class="label_symbol">Visible</label>
                <input type="checkbox" id="ATVS_'.$Row->uuid.'" value="" '.$Check.'>
              </div>
            <div class="DivResizer"></div>
            </div>';
            $RtrnData['Values'] .= '
            <div id="ValueEdit_'.$Row->uuid.'" class="AttrDataDiv">
              <input type="hidden" id="FLD_ValueID" value="NewValue">
              <div class="AttrValDiv">
                <div id="ValueInput_'.$Row->uuid.'" class="ValueInputDiv">
                  <input type="text" id="FLD_AttrValue_'.$Row->uuid.'" value="" class="form-control FLD_EditAttrVal">
                  <button id="BTN_AV_'.$Row->uuid.'" class="BTN_EditAttrVal" title="Update Attribute Value">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                  </button>
                <div class="DivResizer"></div>
                </div>
                <div id="ValList_'.$Row->uuid.'" class="AttrValListDiv">
                  '.$AttributeValues['Values'].'
                <div class="DivResizer"></div>
                </div>
              </div>
            <div class="DivResizer"></div>
            </div>';
        }
        $RtrnData['Attributes'] = '<div class="AttrListDivWrap">'.$RtrnData['Attributes'].'</div>';
        $RtrnData['Values'] = '<div class="AttrValueWrap">'.$RtrnData['Values'].'</div>';
      }
    } else {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Category id not correct<br>';
    }
    unset($Categories);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadCategoryAttributeValues($Attribute) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'', 'Values'=>'');
    $NoteType = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_Attribute($Attribute)) {
      $Data = $Categories->GetCategoryAttributeValues();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $RtrnData['Values'] .= '
                <div id="ValueWrap_'.$Row->uuid.'" class="RgnTypeBtn">
                  <span id="'.$Row->uuid.'">'.$Row->value.'</span>
                  <input type="hidden" id="ValAttr_'.$Row->uuid.'" value="'.$Row->attribute.'">
                  <div id="DEL_'.$Row->uuid.'" class="RgTpBtns AttrValDelete" title="Remove">
                    <i class="fa fa-times" aria-hidden="true"></i>
                  </div>
                  <div id="EDT_'.$Row->uuid.'" class="RgTpBtns AttrValEdit" title="Edit">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                  </div>
                <div class="DivResizer"></div>
                </div>';
        }
      }
    } else {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Attribute id not correct<br>';
    }
    unset($Categories);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function UpdateCategoryData($CategoryID,$Name,$Slug,$Image,$Visible) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'', 'Category'=>'','Translations'=>'');
    $NewEntry = false;
    $NoteType = '';
    $Categories = new mensio_products_categories();
    if ($CategoryID === 'NewCategory') {
      $CategoryID = $Categories->GetNewID();
      $NewEntry = true;
    }
    if (!$Categories->Set_UUID($CategoryID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Category id not correct<br>';
    }
    if (!$Categories->Set_Name($Name)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Category name not correct<br>';
    }
    if (!$Categories->Set_Slug($Slug) ) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Slug name not acceptable or in use<br>';
    }
    if (!$Categories->Set_Image($Image)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Category image path not correct<br>';
    }
    $Categories->Set_Visibility($Visible);
    if ($RtrnData['ERROR'] === 'FALSE') {
      if ($Categories->CheckCategoryNameExists()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Category Name: "'.$Name.'" allready in use<br>';
      } else {
        if ($NewEntry) {
          if (!$Categories->InsertNewCategory()) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Category could not be saved "'.$Name.'"<br>';
          }
        } else {
          if (!$Categories->UpdateCategoryRecord()) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Category could not be updated<br>';
          }
        }
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Categories->UpdateCategorySlug()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Slug could not be updated<br>';
      }
    }
    unset($Categories);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Category data were updated successfully<br>';
      $RtrnData['Category'] = $CategoryID;
      $RtrnData['Translations'] = $this->LoadCategoryTranslations($CategoryID);
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  public function DeleteCategoryData($CategoryID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_UUID($CategoryID)) {
      if (!$Categories->CheckCategoryUsage()) {
        if (!$Categories->RemoveCategoryData()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Category could not be deleted '.$CategoryID.'<br>';
        }
      } else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Info';
        $RtrnData['Message'] .= 'Category is in use can not be deleted<br>';
      }
    } else {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Category id not correct<br>';
    }
    unset($Categories);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Category was deleted successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  public function LoadModalTranslations($CategoryID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Modal'=>'');
    $NoteType = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_UUID($CategoryID)) {
      $Languages = new mensio_languages();
      $LangData = $Languages->LoadLanguagesData();
      unset($Languages);
      $TransFlds ='';
      if ((is_array($LangData)) && (!empty($LangData[0]))) {
        foreach ($LangData as $Row) {
          if ($Row->active) {
            if ($Categories->Set_Language($Row->uuid)) {
              $TransName = $Categories->GetCategoryTranslation();
              $TransFlds .= '<label class="label_symbol">'.$Row->name.'</label>
                <input type="text" id="'.$Row->uuid.'" class="form-control TransFlds" value="'.$TransName.'">';
            }
          }
        }
      }
      $ModalBody = $TransFlds.'
          <button id="BTN_CatTransSave" class="button" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>';
      $RtrnData['Modal'] = $this->CreateModalWindow('Category Translations',$ModalBody);
    }
    unset($Categories);
    if ( $RtrnData['ERROR'] === 'TRUE' ) {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function UpdateCategoryTranslations($CategoryID,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Modal'=>'','Translations'=>'');
    $NoteType = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_UUID($CategoryID)) {
      $Data = stripslashes($Data);
      $Data = json_decode($Data, true);
      if (is_array($Data)) {
        foreach ($Data as $Row) {
          if (!$Categories->Set_Language($Row['Field'])) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Language id not correct<br>';
          }
          if (!$Categories->Set_Name($Row['Value'])) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Language translation not correct<br>';
          }
          if ($RtrnData['ERROR'] === 'FALSE') {
            if (!$Categories->UpdateCategoryTranslation()) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Translation could not be updated<br>';
            }
          }
        }
      }
    }
    unset($Categories);
    if ($RtrnData['ERROR'] === 'FALSE' ) {
      $NoteType = 'Success';
      $RtrnData['Message'] .= 'Translations updated Successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    $RtrnData['Translations'] = $this->LoadCategoryTranslations($CategoryID);
    return $RtrnData;
  }
  public function UpdateAttributeTranslations($AttributeID,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Modal'=>'','Translations'=>'');
    $NoteType = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_Attribute($AttributeID)) {
      $Data = stripslashes($Data);
      $Data = json_decode($Data, true);
      if (is_array($Data)) {
        foreach ($Data as $Row) {
          if (!$Categories->Set_Language($Row['Field'])) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Language id not correct<br>';
          }
          if (!$Categories->Set_Name($Row['Value'])) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Language translation not correct<br>';
          }
          if ($RtrnData['ERROR'] === 'FALSE') {
            if (!$Categories->UpdateAttributeTranslation()) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Translation could not be updated<br>';
            }
          }
        }
      }
    }
    unset($Categories);
    if ($RtrnData['ERROR'] === 'FALSE' ) {
      $NoteType = 'Success';
      $RtrnData['Message'] .= 'Translations updated Successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    $RtrnData['Translations'] = $this->LoadCategoryTranslations($CategoryID);
    return $RtrnData;
  }
  public function AddCategoryAttribute($CategoryID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Attributes'=>'','Values'=>'');
    $NoteType = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_UUID($CategoryID)) {
      if (!$Categories->InsertNewCategoryAttribute()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Attribute could not be created<br>';
      } else {
        $RtrnData = $this->LoadCategoryAttributes($CategoryID);
      }
    } else {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Category id not correct<br>';
    }
    unset($Categories);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function DeleteCategoryAttribute($CategoryID,$Attribute) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Attributes'=>'');
    $NoteType = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_Attribute($Attribute)) {
      if ($Categories->CheckAttributeUsage()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Info';
        $RtrnData['Message'] .= 'Attribute is been used. Can not be deleted!<br>';
      } else {
        if (!$Categories->RemoveCategoryAttribute()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Attribute could not be deleted!<br>';
        } else {
          $NoteType = 'Success';
          $RtrnData = $this->LoadCategoryAttributes($CategoryID);
          $RtrnData['Message'] = 'Attribute deleted Successfully<br>'.$RtrnData['Message'];
        }
      }
    } else {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Attribute id not correct<br>';
    }
    unset($Categories);
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  public function UpdateCategoryAttributes($CategoryID,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Attributes'=>'','Values'=>'');
    $NoteType = 'Success';
    $Categories = new mensio_products_categories();
    $Data = stripslashes($Data);
    $Data = json_decode($Data, true);
    if (is_array($Data)) {
      foreach ($Data as $Row) {
        if (!$Categories->Set_Attribute($Row['Attribute'])) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Attribute id not correct<br>';
        }
        if (!$Categories->Set_Name($Row['Name'])) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Attribute Name '.$Row['Name'].' is not correct<br>';
        }
        $Categories->Set_Visibility($Row['Visible']);
        if ($RtrnData['ERROR'] === 'FALSE') {
          if (!$Categories->UpdateCategoryAttribute()) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Attribute '.$Row['Name'].' cound not be updated<br>';
          } else {
            $RtrnData['Message'] .= 'Attribute '.$Row['Name'].' updated successfully<br>';
          }
        }
      }
    }
    unset($Categories);
    $AttrFlds = $this->LoadCategoryAttributes($CategoryID);
    if (($NoteType === 'Success') && ($RtrnData['Message'] === '')) {
      $RtrnData['Message'] = 'No Attributes Found';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    $RtrnData['Attributes'] = $AttrFlds['Attributes'];
    $RtrnData['Values'] = $AttrFlds['Values'];
    return $RtrnData;
  }
  public function AddCategoryAttributeValue($Attribute,$ValueID,$Name) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Values'=>'');
    $NoteType = '';
    $NewEntry = false;
    $Categories = new mensio_products_categories();
    if (!$Categories->Set_Attribute($Attribute)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Attribute id not correct<br>';
    }
    if ($ValueID === 'NewValue') {
      $NewEntry = true;
      $ValueID = $Categories->GetNewID();
    }
    if (!$Categories->Set_Value($ValueID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Value id not correct "'.$ValueID.'"<br>';
    }
    if (!$Categories->Set_Name($Name)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Name "'.$Name.'" given not correct<br>';
    } 
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NumFnd = $Categories->FindNumberOfValuesExists();
      if ($NewEntry) {
        $Name = $Name.(++$NumFnd);
        $Categories->Set_Name($Name);
        if (!$Categories->InsertNewCategoryAttributeValue()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Attribute Value could not be created<br>';
        }
      } else {
        if (($NumFnd === 0) || ($NumFnd === '0')) {
          if (!$Categories->UpdateCategoryAttributeValue()) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Attribute Value could not be updated<br>';
          }
        } else {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Attribute Value given allready exists '.$NumFnd.' time(s)<br>';
        }
      }
    }
    unset($Categories);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    } else {
      $RtrnData = $this->LoadCategoryAttributeValues($Attribute);
    }
    return $RtrnData;
  }
  public function DeleteCategoryAttributeValue($Attribute,$Value) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Values'=>'');
    $NoteType = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_Value($Value)) {
      if (!$Categories->CheckAttributeValueUsage()) {
        if (!$Categories->RemoveAttributeValue()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Value could not be deleted<br>';
        }
      } else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Info';
        $RtrnData['Message'] .= 'Value is used in products. Can not be deleted<br>';
      }
    } else {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Value '.$Value.' not correct<br>';
    }
    unset($Categories);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    } else {
      $RtrnData = $this->LoadCategoryAttributeValues($Attribute);
    }
    return $RtrnData;
  }
  public function CheckIfCategoryNameExists($CategoryID,$Name) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $FoundName = false;
    $Categories = new mensio_products_categories();
    if ($CategoryID === 'NewCategory') {
      $CategoryID = $Categories->GetNewID();
    }
    if (!$Categories->Set_UUID($CategoryID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Category code '.$CategoryID.' was not correct<br>';
    }
    if (!$Categories->Set_Name($Name)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Name : "'.$Name.'" was not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') { $FoundName = $Categories->CheckCategoryNameExists(); }
    unset($Categories);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    } else {
      if ($FoundName) { $RtrnData['ERROR'] = 'TRUE'; }
    }
    return $RtrnData;
  }
  public function LoadAttributeTranslation($AttributeID) {
    $RtrnData = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_Attribute($AttributeID) ) {
      $Languages = new mensio_languages();
      $LangData = $Languages->LoadLanguagesData();
      unset($Languages);
      $TransFlds ='';
      if ((is_array($LangData)) && (!empty($LangData[0]))) {
        foreach ($LangData as $Row) {
          if ($Row->active) {
            if ($Categories->Set_Language($Row->uuid)) {
              $TransName = $Categories->GetAttributeTranslation();
              $TransFlds .= '<label class="label_symbol">'.$Row->name.'</label>
                <input type="text" id="'.$Row->uuid.'" class="form-control TransFlds" value="'.$TransName.'">';
            }
          }
        }
      }
      $ModalBody = $TransFlds.'
          <input type="hidden" id="MDL_TransAttribute" value="'.$AttributeID.'">
          <button id="BTN_AttrTransSave" class="button" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>';
      $RtrnData = $this->CreateModalWindow('Attribute Translations',$ModalBody);
    }
    unset($Categories);
    return $RtrnData;
  }
}