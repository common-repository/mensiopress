<?php
class Mensio_Admin_Settings_Currencies extends mensio_core_form {
	private $DataSet;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->DataSet = $this->LoadCurrencyDataSet();
    $this->ActivePage = 'Currency';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-currencies',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-admin-settings-currencies.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-currencies',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-admin-settings-currencies.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function LoadCurrencyDataSet($InSorter='') {
    $Data = '';
    $Currency = new mensio_currencies();
    if ($Currency->Set_Sorter($InSorter)) {
      $Data = $Currency->LoadCurrencies();
    }
    unset($Currency);
    return $Data;
  }
  public function GetCurrencyDataTable($InPage=1,$InRows=0,$InSorter='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $TableData = $this->DataSet;
    if (empty($TableData[0])) {
      $TableData = 'No Data Found';
    } else {
      $tbl = new mensio_datatable();
      if ($InSorter != '') {
        $tbl->Set_Sorter($InSorter);
        $TableData = $this->LoadCurrencyDataSet($InSorter);
      }
      $tbl->Set_ActivePage($InPage);
      $tbl->Set_ActiveRows($InRows);
      $tbl->Set_BulkActions(array(
       '0'=>'Position Right',
       '1'=>'Position Left'
      ));
      $tbl->Set_EditColumn('name');
      $tbl->Set_EditOptionsSubline(array(
        'Edit'
      ));
      $tbl->Set_Columns(array(
        'uuid:uuid:plain-text',
        'code:Code:small',
        'symbol:Symbol:small',
        'icon:Icon:hidden',
        'leftpos:Left:input-checkbox',
        'name:Name:plain-text'
      ));
      $RtrnTable = $tbl->CreateTable(
        'Currencies',
        $TableData,
        array('uuid','name','code','symbol','icon','leftpos')
      );
      unset($tbl,$Data);
    }
    return $RtrnTable;
  }
  public function LoadSearchResults($InPage, $InRows, $InSearch, $InSorter) {
    $RtrnTable = '';
    $Currency = new mensio_currencies();
    if (!$Currency->Set_SearchString($InSearch)) {
      $Currency->Set_SearchString('');
    }
    if (!$Currency->Set_Sorter($InSorter)) {
      $Currency->Set_Sorter('');
      $InSorter = '';
    }
    $TableData = $Currency->SearchCurrencies();
    unset($Currency);
    $tbl = new mensio_datatable();
    $tbl->Set_Sorter($InSorter);
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array(
     '0'=>'Position Right',
     '1'=>'Position Left'
    ));
    $tbl->Set_EditColumn('name');
    $tbl->Set_EditOptionsSubline(array(
        'Edit'
    ));
    $tbl->Set_Columns(array(
        'uuid:uuid:plain-text',
        'code:Code:small',
        'symbol:Symbol:small',
        'icon:Icon:hidden',
        'leftpos:Left:input-checkbox',
        'name:Name:plain-text'
    ));
    $RtrnTable = $tbl->CreateTable(
            'Currencies',
            $TableData,
            array('uuid','name','code','symbol','icon','leftpos')
    );
    unset($tbl,$Data);
    return $RtrnTable;
  }
  public function LoadCurrencyData($CurrCode) {
    $Error = false;
    $NoteType = '';
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','KeyCode'=>'','ShortCode'=>'',
        'Symbol'=>'','Left'=>'','Icon'=>'','Translations'=>'');
    $Currency = new mensio_currencies();
    if (!$Currency->Set_UUID($CurrCode)) {
      $Error = false;
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] = 'Problem with currency';
    }
    if (!$Error) {
      $Data = $Currency->LoadCurrencyMainData();
      if (!$Data) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'No Language Data Found!!<br>';
      } else {
        foreach ($Data as &$Row) {
          $RtrnData['KeyCode'] = $Row->uuid;
          $RtrnData['ShortCode'] = $Row->code;
          $RtrnData['Symbol'] = $Row->symbol;
          $RtrnData['Icon'] = $Row->icon;
          $RtrnData['Left'] = $Row->leftpos;
        }
        $Trans = $Currency->LoadCurrencyTranslations();
        foreach ($Trans as &$Row) {
          $RtrnData['Translations'] .= '
            <label class="label_symbol">
              '.$this->GetLanguageName($Row->language).'
            </label>
            <br>
            <input type="text" id="'.$Row->language.'"
              class="form-control" value="'.$Row->name.'">';
        }
        $Trans = $Currency->LoadCurrencyTranslations('New');
        foreach ($Trans as &$Row) {
          $RtrnData['Translations'] .= '
            <label class="label_symbol">
              '.$this->GetLanguageName($Row->uuid).'
            </label>
            <br>
            <input type="text" id="'.$Row->uuid.'"
              class="form-control" value="">';
        }
      }      
    }
    unset($Currency);
    if ($NoteType === 'Alert') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  private function GetLanguageName($language) {
    $Name = '';
    $Lang = new mensio_languages();
    if ($Lang->Set_UUID($language)) {
      $Name = $Lang->Get_LanguageMainName();
    }
    unset($Lang);
    return $Name;
  }
  public function UpdateCurrencyLeftPos($CurCode,$LeftPos) {
    $RtrnData = 'OK';
    $Error = false;
    $Currency = new mensio_currencies();
    if (!$Currency->Set_UUID($CurCode)) {
      $Error = true;
      $RtrnData = 'Problem with the currency';
    }
    $Currency->Set_LeftPos($LeftPos);
    if (!$Error) {
      if (!$Currency->UpdateCurrency()) {
        $RtrnData = 'No records were updated';
      }
    }
    unset($Currency);
    return $RtrnData;
  }
  public function UpdateCurrencyData($Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $ActLang = array();
    $Data = stripslashes($Data);
    $Data = json_decode($Data, true);
    if (is_array($Data)) {
      $Currency = new mensio_currencies();
      foreach ($Data as $DataRow) {
        if (substr($DataRow['Field'],0,4) === 'FLD_') {
          $SetValue = $this->FindSetFun($DataRow['Field']);
          if ($SetValue != false) {
            if (!$Currency->$SetValue($DataRow['Value'])) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Problem with the value of '.$DataRow['Value'].'<br>';
            }
          } else {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Problem with the Field '.$DataRow['Field'].'<br>';
          }
        } else {
          $LangName = $this->GetLanguageName($DataRow['Field']);
          if ($LangName !== '') {
            $ActLang[$DataRow['Field']] = $DataRow['Value'];
          }
        }
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        if (!$Currency->UpdateCurrency()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Problem while updating currency data<br>';
        }
        foreach ($ActLang as $lang => $value) {
          if (!$Currency->Set_Language(trim($lang))) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Problem with the translation language '.$lang.'<br>';          
          }
          if (!$Currency->Set_Name(trim($value))) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Problem with the translation name '.$value.'<br>';          
          }
          if ($RtrnData['ERROR'] === 'FALSE') {
           if (!$Currency->UpdateCurrencyTranslations()) {
             $RtrnData['ERROR'] = 'TRUE';
             $NoteType = 'Alert';
             $RtrnData['Message'] .= 'Problem while updating currency translations<br>';
           }         
          }
        }
      }
      unset($Currency);
    } else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Problem with the Data';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Currency Data Saved';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  private function FindSetFun($Field) {
    $SetFun = false;
    switch ($Field) {
      case 'FLD_Currency':
        $SetFun = 'Set_UUID';
        break;
      case 'FLD_Code':
        $SetFun = 'Set_ShortCode';
        break;
      case 'FLD_Symbol':
        $SetFun = 'Set_Symbol';
        break;
      case 'FLD_Left':
        $SetFun = 'Set_LeftPos';
        break;
      case 'FLD_Icon':
        $SetFun = 'Set_Icon';
        break;
    }
    return $SetFun;
  }
  public function GetNewTranslations() {
    $Trans = '';
    $Lang = new mensio_languages();
    $Data = $Lang->LoadLanguagesData();
    foreach ($Data as &$Row) {
      if ($Row->active) {
        $Trans .= '
        <label class="label_symbol">'.$Row->name.'</label>
        <br>
        <input type="text" id="'.$Row->uuid.'" class="form-control" value="">';
      }
    }
    unset($Data);
    return $Trans;
  }
  public function AddNewCurrencyData($Code) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'', 'Currency'=>'');
    $NoteType = '';
    $Currency = new mensio_currencies();
    if (!$Currency->Set_ShortCode($Code)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with the currency code '.$Code;
    } else {
      if ($Currency->ShortCodeFound()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Code is in use';
      } else {
        $RtrnData['Currency'] = $Currency->AddNewCurrency();
        if (!$RtrnData['Currency']) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Problem Adding New Currency';
          $RtrnData['Currency'] = '';
        }
      }
    }
    unset($Currency);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Currency Added Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
}