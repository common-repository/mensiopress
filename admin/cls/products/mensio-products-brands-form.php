<?php
class Mensio_Admin_Products_Brands_Form extends mensio_core_form {
  public function __construct() {
    $this->ActivePage = 'Brands';
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
  }
  public function Load_Page_CSS() {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-productbrands',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-products-brands.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-productbrands',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-products-brands.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function LoadBrandsDataSet($InSearch,$InSorter) {
    $Error = false;
    $DataSet = array();
    $Brands = new mensio_products_brands();
    if ($InSearch !== '') {
      if (!$Brands->Set_SearchString($InSearch)) { $Error = true; }
    }
    if (!$Brands->Set_Sorter($InSorter)) { $Error = true; }
    if (!$Error) {
      $DataSet = $Brands->LoadProductBrandsDataSet();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if ($Row->logo !== 'No Image') { $Row->logo = get_site_url().'/'.$Row->logo; }
            else { $Row->logo = MENSIO_PATH.'/admin/icons/default/noimage.png'; }
          $Row->color = '<div class="TblClrBox" style="background:'.$Row->color.';"></div>';
        }
      }
    }
    unset($Brands);
    return $DataSet;
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='name',$InSearch='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    $tbl->Set_Sorter($InSorter);
    $DataSet = $this->LoadBrandsDataSet($InSearch,$InSorter);
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array(
     'VSBL'=>'Visible',
     'HDN'=>'Hidden',
     'DEL'=>'Delete'
    ));
    $tbl->Set_EditColumn('name');
    $tbl->Set_EditOptionsSubline(array(
      'Edit','Delete'
    ));
    $tbl->Set_Columns(array(
      'uuid:uuid:plain-text',
      'logo:Logo:img',
      'name:Brand:plain-text',
      'webpage:Web Page:plain-text',
      'color:Color:small',
      'visible:Visible:input-checkbox'
    ));
    $RtrnTable = $tbl->CreateTable(
      'Brands',
      $DataSet,
      array('uuid','logo','color','name','webpage','visible')
    );
    unset($tbl);    
    return $RtrnTable;
  }
  public function LoadMainLang() {
    $Languages = new mensio_languages();
    $Main = $Languages->ReturnMainLanguages('Admin');
    unset($Languages);
    return $Main;
  }
  public function LoadProductBrandData($BrandID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Brand'=>'','Name'=>'',
        'Logo'=>'','WebPage'=>'','Notes'=>'','Trans'=>'');
    $Trans = array();
    $NoteType = '';
    $Brands = new mensio_products_brands();
    if (!$Brands->Set_UUID($BrandID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] = 'Brand Code was not correct<br>';
    } else {
      $DataSet = $Brands->GetBrandData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData['Brand'] = $Row->uuid;
          $RtrnData['Name'] = $Row->name;
          $RtrnData['Slug'] = $Row->slug;
          $RtrnData['Logo'] = get_site_url().'/'.$Row->logo;
          $RtrnData['WebPage'] = $Row->webpage;
          $RtrnData['Color'] = $Row->color;
          $RtrnData['Visible'] = $Row->visible;
        }
      }
      $DataSet = $Brands->GetBrandTranslations();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $i = 0;
        foreach ($DataSet as $Row) {
          if ($Row->language === $this->LoadMainLang()) {
            $RtrnData['Notes'] = $Row->notes;
          }
          $Trans[$i]['language'] = $Row->language;
          $Trans[$i]['notes'] = $Row->notes;
          ++$i;
        }
      }
      $RtrnData['Trans'] = json_encode($Trans);
    }
    unset($Brands);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadLanguageButtons() {
    $LangBtns = '';
    $Languages = new mensio_languages();
    $Data = $Languages->LoadLanguagesData();
    $Main = $Languages->ReturnMainLanguages('Admin');
    unset($Languages);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        if ($Row->active) {
          $LangBtns .= '<div id="'.$Row->uuid.'" class=" TranslButtons">
              <img src="'.MENSIO_PATH.'/admin/icons/flags/'.$Row->icon.'.png" alt="'.$Row->icon.'">
              <input type="hidden" id="Note_'.$Row->uuid.'" class="TransFlds" value="">
            </div>';
        }
      }
      $LangBtns = str_replace(
        'id="'.$Main.'" class="',
        'id="'.$Main.'" class="TranslSelected',
        $LangBtns
      );
    }
    return $LangBtns;
  }
  public function UpdateProductBrandData($BrandID,$Name,$Slug,$WebPage,$Color,$Vsbl,$Logo,$Notes) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Brand'=>'');
    $NewBrand = false;
    $NoteType = '';
    $Brands = new mensio_products_brands();
    if ($BrandID === 'NewBrand') {
      $BrandID = $Brands->GetNewBrandID();
      $NewBrand = true;
    }
    if (!$Brands->Set_UUID($BrandID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Brand ID<br>';
    }
    if (!$Brands->Set_Name($Name)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Invalid name given "'.$Name.'"<br>';
    }
    if (!$Brands->Set_Slug($Slug) ) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Slug name not acceptable or in use<br>';
    }
    if (!$Brands->Set_WebPage($WebPage) ) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Invalid Web Page given<br>';
    }
    if (!$Brands->Set_Color($Color) ) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Invalid Color given<br>';
    }
    if (!$Brands->Set_Logo($Logo) ) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Invalid path for the logo : "'.$Logo.'"was given<br>';
    }
    $Brands->Set_Visible($Vsbl);
    if ($RtrnData['ERROR'] === 'FALSE') {
      if ($NewBrand) {
        if (!$Brands->InsertNewBrand()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Brand could not be saved<br>';
        }
      } else {
        if (!$Brands->UpdateBrandRecord()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Brand could not be updated<br>';
        }
      }
      $Data = json_decode(stripslashes($Notes), true);
      if (is_array($Data)) {
        foreach ($Data as $Row) {
          if (($Brands->Set_Language($Row['language'])) && ($Brands->Set_Notes($Row['note']))) {
            if (!$Brands->UpdateBrandTranslation()) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Translation [[ '.$Row['notes'].' ]] failed<br>';
            }
          }
        }
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Brands->UpdateBrandSlug()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Slug could not be updated<br>';
      }
    }
    unset($Brands);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data Saved Successfully';
      $RtrnData['Brand'] = $BrandID;
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  } 
  public function DeleteProductBrandRecord($BrandID,$Notif=true) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Brands = new mensio_products_brands();
    if ($Brands->Set_UUID($BrandID)) {
      if ($Brands->BrandIsNiUse()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Info';
        $RtrnData['Message'] .= 'The brand system is in use. Can not delete<br>';
      } else {
        if (!$Brands->DeleteBrandRecord()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Brand could not be deleted<br>';
        }
      }
    } else {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Brand ID<br>';
    }
    unset($Brands);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data deleted Successfully';
    }
    if ($Notif) {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function DeleteProductBrandSelections($Selections) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');    
    $NoteType = 'Success';
    $Selections = explode(';',$Selections);
    if (is_array($Selections)) {
      foreach ($Selections as $Row) {
        if ($Row !== '') {
          $Data = $this->LoadProductBrandData($Row);
          $Answer = $this->DeleteProductBrandRecord($Row,false);
          $RtrnData['ERROR'] = $Answer['ERROR'];
          $RtrnData['Message'] .= $Data['Name'].' '.$Answer['Message'].'<br>';
        }
      }
    }
    if ($RtrnData['ERROR'] !== 'FALSE') { $NoteType = 'Info'; }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function CheckIfBrandNameExist($BrandID,$Name) {
    $RtrnData = array('ERROR'=>'FALSE');
    $Brands = new mensio_products_brands();
    if ($ProductID !== 'NewBrand') {
      $Brands->Set_UUID($BrandID);
    }
    if ($Brands->Set_Name($Name)) {
      if ($Brands->CheckIfBrandNameExist()) { $RtrnData['ERROR'] = 'TRUE'; }
    }
    unset($Brands);
    return $RtrnData;
  }
  public function UpdateProductBrandVisibility($Selections,$Visible=true) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');    
    $NoteType = 'Success';
    if ($Selections !== '') {
      $Selections = explode(';',$Selections);
      if (is_array($Selections)) {
        $Brands = new mensio_products_brands();
        foreach ($Selections as $Row) {
          if ($Row !== '') {
            if ($Brands->Set_UUID($Row)) {
              if (!$Brands->UpdateBrandVisibility($Visible)) {
                $RtrnData['ERROR'] = 'TRUE';
                $RtrnData['Message'] .= 'Visibility could not be done for Brand with id "'.$Row.'"<br>';
              }
            }
          }
        }
        unset($Brands);
      }
    }
    if ($RtrnData['ERROR'] !== 'FALSE') { $NoteType = 'Info'; }
      else { $RtrnData['Message'] = 'Visibility Updated Successfully<br>'; }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
}