<?php
class Mensio_Admin_Settings_Languages extends mensio_core_form {
	private $AdminLang;
	private $ThemeLang;
  private $DataSet;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $MainLang = $this->Set_MainLanguages();
    $this->AdminLang = $MainLang['Admin'];
    $this->ThemeLang = $MainLang['Theme'];
    $this->DataSet = $this->LoadMainDataSet();
    $this->ActivePage = 'Languages';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-Languages',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-admin-settings-languages.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-Languages',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-admin-settings-languages.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function Set_MainLanguages() {
    $Data = array('Admin'=>'','Theme'=>'');
    $ClsLang = new mensio_languages();
    $Data['Admin'] = $ClsLang->Get_Value('AdminMain');
    $Data['Theme'] = $ClsLang->Get_Value('ThemeMain');
    unset ($ClsLang);
    return $Data;
  }
  public function Get_MainLanguages($Area) {
    $Main = '';
    if ($Area === 'Admin') { $Main = $this->AdminLang; }
    if ($Area === 'Theme') { $Main = $this->ThemeLang; }
    return $Main;
  }
  private function LoadMainDataSet($InSorter='') {
    $DataSet = '';
    $ClsLang = new mensio_languages();
    if (!$ClsLang->Set_Sorter($InSorter)) {
      $ClsLang->Set_Sorter('');
    }
    $DataSet = $ClsLang->LoadLanguagesData();
    unset ($ClsLang);
    return $DataSet;
  }
  private function AddExtraColumns($DataRecords) {
     foreach( $DataRecords as &$row) {
      if ($row->uuid === $this->AdminLang) { $row->admin = 1; }
        else { $row->admin = 0; }
      if ($row->uuid === $this->ThemeLang) { $row->theme = 1; }
        else { $row->theme = 0; }
    }
    return $DataRecords;
  }
  public function GetLanguageDataTable($InPage=1,$InRows=0,$InSorter='') {
    $RtrnTable = '';
    $TableData = $this->DataSet;
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    if ($TableData == '') {
      $TableData = 'No Data Found';
    } else {
      $tbl = new mensio_datatable();
      $tbl->Set_ActivePage($InPage);
      $tbl->Set_ActiveRows($InRows);
      $tbl->Set_BulkActions(array(
       '0'=>'Deactivate Selected',
       '1'=>'Activate Selected'
      ));
      if ($InSorter != '') {
        if (($InSorter != 'admin') && ($InSorter != 'theme')) {
          $tbl->Set_Sorter($InSorter);
          $TableData = $this->LoadMainDataSet($InSorter);
        }
      }
      $tbl->Set_EditColumn('name');
      $tbl->Set_EditOptionsSubline(array(
          'Edit',
          'Active'
      ));
      $TableData = $this->AddExtraColumns($TableData);
      $tbl->Set_Columns(array(
          'uuid:uuid:plain-text',
          'code:Code:small',
          'icon:Icon:hidden',
          'active:Active:input-checkbox',
          'name:Name:plain-text',
          'admin:Admin:hidden',
          'theme:Theme:hidden'
      ));
      $RtrnTable = $tbl->CreateTable(
              'Languages',
              $TableData,
              array('uuid','icon','name','code','active','admin','theme')
      );
      unset($tbl,$Data);
    }
    return $RtrnTable;
  }
  public function GetLanguageOptions() {
    $Options = '<option value="0">New Language</option>';
    $OptionData = $this->DataSet;
    foreach ($OptionData as &$row) {
      $Options .= '<option value="'.$row->uuid.'">'.$row->name.'</option>';
    }
    return $Options;
  }
  public function LoadTranslationFields($Language,$Type) {
    $CLSLang = new mensio_languages();
    $Data = $this->DataSet;
    $TransRows = '';
    foreach( $Data  as &$row) {
      if ($Type === 'From') {
        $CLSLang->Set_ToLanguage($row->uuid);
        $CLSLang->Set_Language($Language);
      } else {
        $CLSLang->Set_ToLanguage($Language);
        $CLSLang->Set_Language($row->uuid);
      }
      $LangTrans = $CLSLang->ReturnLanguageTranslations();
      foreach ($LangTrans as $Trans) {
        $check = true;
        if (($Type === 'To') && ($Language === $row->uuid)) {
          $check = false;
        }
        if ($check) {
          $TransRows .= '<div class = "main_translation"> 
              <label class="label_symbol">'.$row->name.':</label>
              <div class="DivResizer"></div>
              <input type="text" id="FLD_'.$Type.'_'.$row->uuid.'"
                class="form-control '.$Type.'" value="'.$Trans->name.'">
              <div class="DivResizer"></div>
            </div>';
        }
      }
  }
    unset($CLSLang);
    return $TransRows;
  }
  public function LoadSearchResults($InPage,$InRows,$InSearch,$InSorter) {
    $RtrnTable = '';
    if (($InSorter === 'admin') || ($InSorter === 'theme')) {
      $InSorter = '';
    }
    $Lang = new mensio_languages();
    if (!$Lang->Set_SearchString($InSearch)) {
      $Lang->Set_SearchString('');
    }
    if (!$Lang->Set_Sorter($InSorter)) {
      $Lang->Set_Sorter('');
    }
    $TableData = $Lang->SearchLanguages();
    unset($Lang);
    $tbl = new mensio_datatable();
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_Sorter($InSorter);
    $tbl->Set_BulkActions(array(
     '0'=>'Deactivate Selected',
     '1'=>'Activate Selected'
    ));
    $tbl->Set_EditColumn('name');
    $tbl->Set_EditOptionsSubline(array(
        'Edit',
        'Active'
    ));
    $TableData = $this->AddExtraColumns($TableData);
    $tbl->Set_Columns(array(
        'uuid:uuid:plain-text',
        'code:Code:small',
        'icon:Icon:hidden',
        'active:Active:input-checkbox',
        'name:Name:plain-text',
        'admin:Admin:hidden',
        'theme:Theme:hidden'
    ));
    $RtrnTable = $tbl->CreateTable(
            'Languages',
            $TableData,
            array('uuid','code','icon','name','active','admin','theme')
    );
    unset($tbl,$Data);
    return $RtrnTable;
  }
  public function UpdateActiveLanguage($Active,$Language) {
    $RtrnData = array('ERROR'=>'FALSE', 'Message'=>'');
    $NoteType = '';
    $ClsLang = new mensio_languages();
    $ClsLang->Set_Active($Active);
    if (!$ClsLang->Set_UUID($Language)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Language was not correct <br>';
    }
    $CheckLang = $ClsLang->Get_Value('AdminMain');
    if ($ClsLang->Get_Value('UUID') === $CheckLang) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Info';
      $RtrnData['Message'] .= 'Language Is used as Main In Dashboard <br>';
    }
    $CheckLang = $ClsLang->Get_Value('ThemeMain');
    if ($ClsLang->Get_Value('UUID') === $CheckLang) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Info';
      $RtrnData['Message'] .= 'Language Is used as Main In Theme <br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Data = $ClsLang->ReturnLanguageData();
      foreach ($Data as $Row) {
        $ClsLang->Set_ShortCode($Row->code);
      }
      if ($ClsLang->UpdateLanguageData()) {
        $NoteType = 'Success';
        if ($Active == 0) { $RtrnData['Message'] = 'Language Deactivated Successfully'; }
          else { $RtrnData['Message'] = 'Language Activated Successfully'; }
      } else {
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Problem while updating Language Active Column<br>';
      }
    }
    unset ($ClsLang);
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function UpdateMainLanguages($Type,$Language) {
    $RtrnData = array('ERROR'=>'FALSE', 'Message'=>'');
    $NoteType = '';
    $Error = false;
    $ClsLang = new mensio_languages();
    if (!$ClsLang->Set_UUID($Language)) {
      $Error = true;
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Language was not correct <br>';
    }
    if (!$Error) {
      if ($ClsLang->UpdateMainLanguages($Type)) {
        $NoteType = 'Success';
        $RtrnData['Message'] = $Type.' main language updated successfully';
      } else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Problem while updating Language Column<br>';
      }
    }
    unset ($ClsLang);
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function GetLanguageDetails($Language) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','KeyCode'=>'',
        'ShortCode'=>'','Active'=>'','Icon'=> '','TransFrom'=>'','TransTo'=>'',
        'IsAdminMain'=>'','IsThemeMain'=>'');
    $Error = false;
    $NoteType = '';
    $ClsLang = new mensio_languages();
    if (!$ClsLang->Set_UUID($Language)) {
      $Error = true;
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Language was not correct<br>';
    }
    if (!$Error) {
      $Data = $ClsLang->ReturnLanguageData();
      if (!$Data) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'No Language Data Found!!<br>';
      } else {
        foreach ($Data as &$Row) {
          $RtrnData['KeyCode'] = $Row->uuid;
          $RtrnData['ShortCode'] = $Row->code;
          $RtrnData['Active'] = $Row->active;
          $RtrnData['Icon'] = $Row->icon;
          $RtrnData['IconImage'] = MENSIO_PATH.'/admin/icons/flags/'.$Row->icon.'.png';
        }
        $RtrnData['TransFrom'] = $this->LoadTranslationFields($RtrnData['KeyCode'],'From');
        $RtrnData['TransTo'] = $this->LoadTranslationFields($RtrnData['KeyCode'],'To');
        $RtrnData['IsAdminMain'] = '0';
        if ($RtrnData['KeyCode'] === $ClsLang->Get_Value('AdminMain')) {
          $RtrnData['IsAdminMain'] = '1';
        }
        $RtrnData['IsThemeMain'] = '0';
        if ($RtrnData['KeyCode'] === $ClsLang->Get_Value('ThemeMain')) {
          $RtrnData['IsThemeMain'] = '1';
        }
      }
    }
    unset ($ClsLang);
    if ($NoteType === 'Alert') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);     
    }
    return $RtrnData;
  }
  public function CreateNewLanguage($ShortCode) {
    $Error = false;
    $NoteType = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Options'=>'');
    $CLSLang = new mensio_languages();
    if ( ! $CLSLang->Set_ShortCode($ShortCode) ) {
      $Error = true;
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Language Code was not correct<br>';
    } else { 
      if ($CLSLang->ShortCodeFound()) {
        $Error = true;
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Language Code is allready in use<br>';
      }
    }
    if (!$Error) {
      if ( ! $CLSLang->InsertNewLanguage()) {
        $Error = true;
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Language Code allready in use<br>';
      } else {
        $RtrnData = $this->GetLanguageDetails( $CLSLang->Get_Value('Language') );
        $this->DataSet = $this->LoadMainDataSet();
        $NoteType = 'Success';
        $RtrnData['Message'] .= 'New Language saved successfully<br>';
        $RtrnData['Options'] = $this->GetLanguageOptions();
      }
    }
    unset($CLSLang);
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;    
  }
  public function UpdateLanguage($Language,$ShortCode,$Active,$Icon) {
    $Error = false;
    $NoteType = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $CLSLang = new mensio_languages();
    if ( ! $CLSLang->Set_UUID($Language) ) {
      $Error = true;
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Language was not correct<br>';
    }
    if ( ! $CLSLang->Set_ShortCode($ShortCode) ) {
      $Error = true;
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Language Code was not correct<br>';
    } else { 
      if ($CLSLang->ShortCodeFound( $CLSLang->Get_Value('UUID') )) {
        $Error = true;
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Language Code is allready in use<br>';
      }
    }
    if ( ! $CLSLang->Set_Icon($Icon) ) {
      $Error = true;
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Icon value was not correct<br>'.$Icon;
    }
    if ( ! $CLSLang->Set_Active($Active) ) {
      $Error = true;
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Active value was not correct<br>';
    }
    if (!$Error) {
      if ( ! $CLSLang->UpdateLanguageData() ) {
        $Error = true;
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Problem while updating Language';
      } else {
        $NoteType = 'Success';
        $RtrnData['Message'] = 'Language Updated Successfully';
      }
    }
    unset($CLSLang);
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function UpdateTranslation($Language,$ToLanguage,$Name) {
    $Error = false;
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Options'=>'');
    $NoteType = '';
    $CLSLang = new mensio_languages();
    if ( ! $CLSLang->Set_Language($Language) ) {
      $Error = true;
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Language was not correct <br>';
    }    
    if ( ! $CLSLang->Set_ToLanguage($ToLanguage) ) {
      $Error = true;
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'To Language was not correct <br>';
    }    
    if ( ! $CLSLang->Set_Name($Name) ) {
      $Error = true;
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Name was not correct : '.$Name.'<br>';
    }    
    if (!$Error) {
      if ( ! $CLSLang->UpdateLanguageTranslation() ) {
        $Error = true;
        $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
        $RtrnData['Message'] = 'Problem while updating Language Translation';
      } else {
        $this->DataSet = $this->LoadMainDataSet();
        $NoteType = 'Success';
        $RtrnData['Message'] = 'Language name Updated to '.$Name.' Successfully<br>';
        $RtrnData['Options'] = $this->GetLanguageOptions();
      }
    }
    unset($CLSLang);
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function LoadFlagIconForm($Image) {
    $Icons = '';
    $dir = plugins_url().'/mensiopress/admin/icons/flags/';
    $Image = str_replace($dir, '', $Image);
    $IconList = scandir('../'.MENSIO_SHORTPATH.'/admin/icons/flags/');
    foreach ($IconList as $Row) {
      $SlctCls = '';
      if ($Row === $Image) {$SlctCls = 'SelectedIcon';}
      if (substr($Row, -4) === '.png') {
        $RowID = str_replace('.png', '', $Row);
        $Icons .= '
        <div id="'.$RowID.'" class="IconElementWrap '.$SlctCls.'">
          <img class="IconElement" src="'.$dir.$Row.'">
          <div class="DivResizer"></div>
        </div>';
      }
    }
    $ModalBody = '<div class="IconListWrap">'.$Icons.'</div>';
    return $this->CreateModalWindow('Flag Icon Selection',$ModalBody);
  }
}