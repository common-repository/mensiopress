<?php
class Mensio_Admin_Settings_Continents extends mensio_core_form {
	private $DataSet;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->ActivePage = 'Continents';
    $Continent = new mensio_continents();
    $this->DataSet = $Continent->LoadContinentsCodes();
    unset($Continent);
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-currencies',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-admin-settings-continents.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-currencies',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-admin-settings-continents.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function LoadContinentsTranslations() {
    $TableData[] = array();
    $i = 0;
    $Languages = new mensio_languages();
    $LangDataSet = $Languages->LoadLanguagesData();
    unset($Languages);
    $Continents = new mensio_continents();
    foreach ($LangDataSet as $Lang) {
      if ($Lang->active == 1) {
        if ($Continents->Set_Language($Lang->uuid)) {
          $ContData = $Continents->LoadContinentsData();
          if (!empty($ContData)) {
            $TableData[$i]['Language'] = $Lang->name;
            foreach ($ContData as $Row) {
              $TableData[$i][$Row->code] = $Row->name;
            }
          } else {
            $TableData[$i]['Language'] = $Lang->name;
            foreach ($this->DataSet as $Row) {
              $TableData[$i][$Row->code] = '';
            }
          }
          ++$i;
        }
      }
    }
    unset($Continents);
    return $TableData;
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='') {
    $RtrnTable = $InSorter;
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_Editable(false);
    $tbl->Set_MultiSelect(false);
    $tbl->Set_Searchable(false);
    $tbl->Set_Columns(array(
      'Language:Language:plain-text',
      'AF:AF:plain-text',
      'AN:AN:plain-text',
      'AS:AS:plain-text',
      'EU:EU:plain-text',
      'NA:NA:plain-text',
      'OC:OC:plain-text',
      'SA:SA:plain-text'
    ));
    $RtrnTable = $tbl->CreateTable(
      'Continents',
      $this->LoadContinentsTranslations()
    );
    unset($tbl);
    return $RtrnTable;
  }
  public function GetLanguageButtons() {
    $Buttons = '<div class="Mns_NoActiveLang">!!! No Active Language !!!</div>';
    $Languages = new mensio_languages();
    $LangDataSet = $Languages->LoadLanguagesData();
    unset($Languages);
    if (!empty($LangDataSet)) {
      $Buttons = '';
      foreach ($LangDataSet as $Lang) {
        if ($Lang->active == 1) {
          $Buttons .= '
          <div id="'.$Lang->uuid.'" class="Mns_Language_Selector_Btn">
            '.$Lang->name.'
          </div>';
        }
      }
    }
    return $Buttons;
  }
  public function GetEditFields($Language='') {
    $Fields = '';
    $Empty = true;
    if ($Language !== '') {
      $Continents = new mensio_continents();
      if ($Continents->Set_Language($Language)) {
        $ContData = $Continents->LoadContinentsData();
        if (!empty($ContData)) {
          foreach ($ContData as $Row) {
            $Fields .= '
            <label class="label_symbol">'.$Row->code.'</label><br>
            <input type="text" id="'.$Row->uuid.'"
              class="form-control" value="'.$Row->name.'">';
          }
          $Empty= false;
        }
      }
      unset($Continents);
    }
    if ($Empty) {
      if (!empty($this->DataSet)) {
        foreach ($this->DataSet as $Row) {
          $Fields .= '
              <label class="label_symbol">'.$Row->code.'</label><br>
              <input type="text" id="'.$Row->uuid.'"
                class="form-control" value="">';
        }
      }
    }
    return $Fields;
  }
  public function UpdateContinentsData($Language,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Data = stripslashes($Data);
    $Data = json_decode($Data, true);
    if (is_array($Data)) {
      $Continent = new mensio_continents();
      if (!$Continent->Set_Language($Language)) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Problem with Language';
      } else {
        foreach ($Data as $DataRow) {
          if (!$Continent->Set_UUID($DataRow['Field'])) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Problem with Continent';
          }
          if (!$Continent->Set_Name($DataRow['Value']) ) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Problem with Continent Name';
          }
          if ($RtrnData['ERROR'] === 'FALSE') {
            if (!$Continent->UpdateContinentTranslation()) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Problem with Continent name : '.$DataRow['Value'].' Update';
            }
          }
        }
      }
      unset($Continent);
    } else {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with the Data';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Continent Translations were updated successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
}