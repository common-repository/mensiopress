<?php
class Mensio_Admin_Settings_Countries extends mensio_core_form {
	private $DataSet;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->DataSet = $this->LoadCountriesDataSet();
    $this->ActivePage = 'Countries';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-countries',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-admin-settings-countries.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-countries',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-admin-settings-countries.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
   private function LoadCountriesDataSet($InSorter='',$InSearch='') {
    $Data = '';
    $TblData = array();
    $Country = new mensio_countries();
    if ($InSorter !== '') { $Country->Set_Sorter($InSorter); }
    if ($InSearch !== '') { $Country->Set_SearchString($InSearch); }
    $Data = $Country->GetCountriesDataSet();
    unset($Country);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      $i = 0;
      foreach ($Data as &$Row) {
        $TblData[$i]['uuid'] = $Row->uuid;
        $TblData[$i]['country'] = $Row->country;
        $TblData[$i]['currency'] = $Row->symbol.' '.$Row->curname;
        $TblData[$i]['iso'] = $Row->iso;
        $TblData[$i]['domain'] = $Row->domain;
        $TblData[$i]['idp'] = $Row->idp;
        ++$i;
      }
    }
    return $TblData;
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='',$InSearch='') {
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $RtrnTable = '';
    $TableData = $this->DataSet;
    if ($TableData == '') {
      $TableData = 'No Data Found';
    } else {
      $tbl = new mensio_datatable();
      if ($InSorter != '') { $tbl->Set_Sorter($InSorter); }
      $TableData = $this->LoadCountriesDataSet($InSorter,$InSearch);
      $tbl->Set_ActivePage($InPage);
      $tbl->Set_ActiveRows($InRows);
      $tbl->Set_BulkActions(array(
       'QED'=>'Quick Edit',
       'DEL'=>'Delete'
      ));
      $tbl->Set_EditColumn('country');
      $tbl->Set_EditOptionsSubline(array(
          'Edit',
          'Delete'
      ));
      $tbl->Set_Columns(array(
          'uuid:uuid:plain-text',
          'country:Country:plain-text',
          'currency:Currency:small',
          'iso:Iso:small',
          'domain:Domain:small',
          'idp:Idp:small'
      ));
      $RtrnTable = $tbl->CreateTable(
        'Countries', 
        $TableData,
        array('uuid','country','currency','iso','domain','idp')
      );
      unset($tbl,$Data);
    }
    return $RtrnTable;
  }
  public function GetOptions($Table) {
    $Data = '';
    $Options = '';
    switch ($Table) {
      case 'Continent':
        $Tbl = new mensio_continents();
        $Data = $Tbl->LoadContinentsData();
        unset($Tbl);
        break;
      case 'Currency':
        $Tbl = new mensio_currencies();
        $Data = $Tbl->LoadCurrencies();
        unset($Tbl);
        break;
    }
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as &$Row) {
        $Options .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
      }
    }
    return $Options;
  }
  public function GetTranslationFields() {
    $Fields = '';
    $Language = new mensio_languages();
    $Data = $Language->LoadLanguagesData();
    unset($Language);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        if ($Row->active) {
          $Fields .= '
          <label class="label_symbol">'.$Row->name.'</label><br>
          <input type="text" id="'.$Row->uuid.'" class="form-control" value="">';
        }
      }
    }
    return $Fields;
  }
  public function LoadCountryData($CntrCode) {
    $RtrnData = array(
        'ERROR'=>'FALSE','Message'=>'','Country'=>'',
        'Continent'=>'','iso2'=>'','iso3'=>'','domain'=>'',
        'idp'=>'','currency'=>'','Translations'=>''
    );
    $NoteType = '';
    $Countries = new mensio_countries();
    if (!$Countries->Set_UUID($CntrCode)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Not correct country code';
    } else {
      $Country = $Countries->GetCountryBasicData();
      foreach ($Country as &$Row) {
        $RtrnData['Country'] = $Row->uuid;
        $iso = explode('-',$Row->iso);
        $RtrnData['Continent'] = $Row->continent;
        $RtrnData['iso2'] = $iso[0];
        $RtrnData['iso3'] = $iso[1];
        $RtrnData['domain'] = $Row->domain;
        $RtrnData['idp'] = $Row->idp;
        $RtrnData['currency'] = $Row->currency;
      }
      $Data = $Countries->GetCountryTranslations();
      foreach ($Data as $Row) {
        if ($RtrnData['Translations'] != '') {
          $RtrnData['Translations'] .= '??';
        }
        $RtrnData['Translations'] .= $Row->language.'::'.$Row->name;
      }
    }
    if ($NoteType === 'Alert') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function SaveCountryData($Code,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NewCountry = false;
    $NoteType = '';
    $Countries = new mensio_countries();
    if ($Code === 'NewCountry') {
      $Code = $Countries->GetNewCountryID();
      $NewCountry = true;
    }
    if (!$Countries->Set_UUID($Code)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Country code : '.$Code.'<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Data = stripslashes($Data);
      $Data = json_decode($Data, true);
      if (is_array($Data)) {
        foreach ($Data as $DataRow) {
          if (substr($DataRow['Field'],0,4) === 'FLD_') {
            $SetValue = $this->FindSetFun($DataRow['Field']);
            if (!$Countries->$SetValue($DataRow['Value'])) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $Lbl = $this->GetFldLbl($DataRow['Field']);
              $RtrnData['Message'] .= 'Value "'.$DataRow['Value'].'" of the field '.$Lbl.' is not correct<br>';
            }
          } else {
            $ActLang[$DataRow['Field']] = $DataRow['Value'];
          }
        }
        if ($RtrnData['ERROR'] === 'FALSE') {
          $Mode = 'Edit';
          if ($NewCountry) { $Mode = 'New'; }
          if (!$Countries->UpdateCountryData($Mode)) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Problem Saving Country data<br>';
          } else {
            foreach ($ActLang as $lang => $value) {
              if (!$Countries->Set_Language($lang)) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Problem with Country Language : <br>';
              }
              if (!$Countries->Set_Name($value)) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Problem with Country Translation : '
                        .$DataRow['Value'].'<br>';
              }
              if ($RtrnData['ERROR'] === 'FALSE') {
                if (!$Countries->UpdateCountryTranslations()) {
                  $RtrnData['ERROR'] = 'TRUE';
                  $NoteType = 'Alert';
                  $RtrnData['Message'] .= 'Problem Saving Country Translations<br>';
                }
              }
            }
          }
        }        
      } else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Problem with the Data';
      }
    }
    unset($Countries);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Country Data Saved Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  private function FindSetFun($Field) {
    $SetFun = false;
    switch ($Field) {
      case 'FLD_Country':
        $SetFun = 'Set_UUID';
        break;
      case 'FLD_Continent':
        $SetFun = 'Set_Continent';
        break;
      case 'FLD_iso2':
        $SetFun = 'Set_ISO2';
        break;
      case 'FLD_iso3':
        $SetFun = 'Set_ISO3';
        break;
      case 'FLD_domain':
        $SetFun = 'Set_Domain';
        break;
      case 'FLD_idp':
        $SetFun = 'Set_IDP';
        break;
      case 'FLD_Currency':
        $SetFun = 'Set_Currency';
        break;
    }
    return $SetFun;
  }
  private function GetFldLbl($Field) {
    $Label = '';
    switch ($Field) {
      case 'FLD_Country':
        $Label = 'Country Code';
        break;
      case 'FLD_Continent':
        $Label = 'Continent';
        break;
      case 'FLD_iso2':
        $Label = 'ISO 2';
        break;
      case 'FLD_iso3':
        $Label = 'ISO 3';
        break;
      case 'FLD_domain':
        $Label = 'Domain';
        break;
      case 'FLD_idp':
        $Label = 'IDP';
        break;
      case 'FLD_Currency':
        $Label = 'Currency';
        break;
    }
    return $Label;
  }
  public function Load_ModalQuickEdit() {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Modal'=>'');
    $ModalBody = '
          <div id="mdltabs">
            <ul>
              <li><a href="#mdltabs-1">Continent</a></li>
              <li><a href="#mdltabs-2">Currency</a></li>
            </ul>
            <div id="mdltabs-1" class="Mns_Tab_Container">
              <label class="label_symbol">Continent</label>
              <select id="FLD_MDL_Continent" class="form-control Mdl-form-control" value="">
                '.$this->GetOptions('Continent').'
              </select>
            </div>
            <div id="mdltabs-2" class="Mns_Tab_Container">
              <label class="label_symbol">Currency</label>
              <select id="FLD_MDL_Currency" class="form-control Mdl-form-control" value="">
                '.$this->GetOptions('Currency').'
              </select>
            </div>
          </div>
            <button id="BTN_ModalSave" class="button">
              <i class="fa fa-floppy-o" aria-hidden="true"></i>
            </button>';
    $RtrnData['Modal'] = $this->CreateModalWindow('Quick Edit',$ModalBody);
    return $RtrnData;
  }
  public function UpdateBulkCountriesData($Selected,$Field,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Countries = new mensio_countries();
    if (($Field !== 'FLD_MDL_Continent') && ($Field !== 'FLD_MDL_Currency')) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Not Correct Field<br>';
    } else {
      if ($Field === 'FLD_MDL_Continent') {
        if (!$Countries->Set_Continent($Data)) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Not Correct Continent<br>';
        }
      } else {
        if (!$Countries->Set_Currency($Data)) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Not Correct Currency<br>';
        }
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Selected = explode(';',$Selected);
      if (is_array($Selected)) {
        foreach ($Selected as $Country) {
          if ($Country !== '') {
            if (!$Countries->Set_UUID($Country)) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Problem with Country code<br>';
            } else {
              if (!$Countries->UpdateCountryData('Edit')) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Country '.$Countries->GetCountryName().' could not be updated<br>';
              }
            }
          }
        }
      }
    }
    unset($Countries);
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function DeleteCountryData($CntrCode) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Countries = new mensio_countries();
    if (!$Countries->Set_UUID($CntrCode)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Country code<br>';
    } else {
      if ($Countries->CountryInUse()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Info';
        $RtrnData['Message'] .= 'Country '.$Countries->GetCountryName().' can not be deleted because is in use<br>';
      } else {
        if (!$Countries->DeleteCountry()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Country '.$Countries->GetCountryName().' could not be deleted<br>';
        } 
      }
    }
    unset($Countries);
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function DeleteBulkCountriesData($Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $Data = explode(';',$Data);
    if (is_array($Data)) {
      foreach ($Data as $Country) {
        if ($Country !== '') {
          $Answer = $this->DeleteCountryData($Country);
          $RtrnData['ERROR'] = $Answer['ERROR'];
          $RtrnData['Message'] .= $Answer['Message'];
        }
      }
    }
    return $RtrnData;
  }
}