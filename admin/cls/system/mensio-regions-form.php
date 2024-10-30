<?php
class Mensio_Admin_Settings_Regions extends mensio_core_form {
	private $CountriesDS;
	private $RegionsDS;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->CountriesDS = $this->LoadCountriesDataSet();
    $this->RegionsDS = '';
    $this->ActivePage = 'Countries_Regions';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-Regions',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-admin-settings-regions.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-Regions',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-admin-settings-regions.js',
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
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $TableData = $this->CountriesDS;
    if ($TableData == '') {
      $TableData = 'No Data Found';
    } else {
      $tbl = new mensio_datatable();
      if ($InSorter != '') { $tbl->Set_Sorter($InSorter); }
      $TableData = $this->LoadCountriesDataSet($InSorter,$InSearch);
      $tbl->Set_ActivePage($InPage);
      $tbl->Set_ActiveRows($InRows);
      $tbl->Set_BulkActions(array(
       'CP'=>'Copy Region Types'
      ));
      $tbl->Set_EditColumn('country');
      $tbl->Set_EditOptionsSubline(array(
          'Edit'
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
              'Regions', 
              $TableData,
              array('uuid','country','currency','iso','domain','idp')
      );
      unset($tbl,$Data);
    }
    return $RtrnTable;
  }
  function LoadCountryRegions($Country) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'',
        'Regions'=>'','TypeOptions'=>'');
    $NoteType = '';    
    $Regions = new mensio_regions();
    if (!$Regions->Set_Country($Country)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with the country selected<br>';
    } else {
      $DataSet = $Regions->LoadCountryRegions();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if ($Row !== '') {
            $Span = '';
            for ($i = 1; $i < $Row['level']; ++$i) {
              $Span .= '<span class="SubRegion"></span>';
            }
            $RtrnData['Regions'] .= '
                <div class="RegionsItem">
                  '.$Span.'
                  <div id="'.$Row['uuid'].'" class="RegionsDspl">
                    <p>'.$Row['name'].'</p>
                  </div>
                </div>';
          }
        }
      }
    }
    unset($Regions);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    } else {
      $Options = $this->LoadRegionTypesOptions($Country);
      $RtrnData['TypeOptions'] = $Options['TypeOptions'];
      $RtrnData['TypeBtns'] = $Options['TypeBtns'];
    }
    return $RtrnData;
  }
  public function LoadRegionTypesOptions($Country) {
    $Options['TypeOptions'] = '<option value="">No Region Types</options>';
    $Options['TypeBtns'] = '';
    $Regions = new mensio_regions();
    if ($Regions->Set_Country($Country)) {
      $Data = $Regions->LoadCountryRegionTypes();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        $Options['TypeOptions'] = '';
        foreach ($Data as $Row) {
          $Options['TypeOptions'] .= '<option value="'.$Row->uuid.'">'.$Row->name.'</options>';
          $Options['TypeBtns'] .= '
            <div class="RgnTypeBtn">
              '.$Row->name.'
              <div id="TPDel_'.$Row->uuid.'" class="RgTpBtns" title="Delete Region Type">
                <i class="fa fa-times" aria-hidden="true"></i>
              </div>
              <div id="TPEdit_'.$Row->uuid.'" class="RgTpBtns" title="Edit Region Type">
                <i class="fa fa-pencil" aria-hidden="true"></i>
              </div>
            <div class="DivResizer"></div>
            </div>';
        }
      }
    }
    unset($Regions);
    return $Options;
  }
  public function LoadRegionsParentOptions($Country,$Type) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','ParentOptions'=>'');
    $NoteType = '';
    $Regions = new mensio_regions();
    if (!$Regions->Set_Country($Country)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with the country selected<br>';
    }
    if (!$Regions->Set_Type($Type) ) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with the region type<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Data = $Regions->LoadParentRegions();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $RtrnData['ParentOptions'] .= '<option value="'.$Row->uuid.'">'.$Row->name.'</options>';
        }
      } else {
          $RtrnData['ParentOptions'] = '<option value="TopLevel">No Parent Regions</options>';
      }
    }
    unset($Regions);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function EditRegionsData($RegCode) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','ParentOptions'=>'',
        'Region'=>'','Type'=>'','Name'=>'','Parent'=>'');
    $NoteType = '';
    $Regions = new mensio_regions();
    if (!$Regions->Set_UUID($RegCode)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with the country selected<br>';
    } else {
      $Data = $Regions->LoadRegionData();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $PR = $this->LoadRegionsParentOptions($Row->country,$Row->type);
          $RtrnData['ParentOptions'] = $PR['ParentOptions'];
          $RtrnData['Region'] = $Row->uuid;
          $RtrnData['Type'] = $Row->type;
          $RtrnData['Name'] = $Row->name;
          if ($Row->parent !== 'TopLevel') {
            $RtrnData['Parent'] = $Row->parent;
          }
        }
      }
    }
    unset($Regions);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function UpdateRegionsList($Country,$RegCode,$Type,$Name,$Parent) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Regions'=>'');
    $NoteType = '';
    $NewEntry = false;
    $Regions = new mensio_regions(); 
    if ($RegCode === 'NewRegion') {
      $RegCode = $Regions->GetNewCodeForRegions();
      $NewEntry = true;
    }
    if (!$Regions->Set_UUID($RegCode)) {
      $NoteType = 'Alert';    
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Region Code<br>';
    }
    if (!$Regions->Set_Country($Country)) {
      $NoteType = 'Alert';    
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Country Code<br>';
    }
    if (!$Regions->Set_Type($Type)) {
      $NoteType = 'Alert';    
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Region Type<br>';
    }
    if (!$Regions->Set_Name($Name)) {
      $NoteType = 'Alert';    
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Region Name<br>';
    }
    if (!$Regions->Set_Parent($Parent)) {
      $NoteType = 'Alert';    
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Parent Code<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if ($NewEntry) {
        if (!$Regions->AddNewRegionData()) {
          $NoteType = 'Alert';    
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Problem while updating parent<br>';
        }
      } else {
        if (!$Regions->UpdateRegionData()) {
          $NoteType = 'Alert';    
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Problem while updating parent<br>';
        }
      }
    }
    unset($Regions);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Lst = $this->LoadCountryRegions($Country);
      $RtrnData['Regions'] = $Lst['Regions'];
      $NoteType = 'Success';    
      $RtrnData['Message'] .= 'Record saved successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function UpdateRegionType($Country,$Type,$TypeName) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','TypeOptions'=>'','TypeBtns'=>'');
    $NoteType = '';
    $NewEntry = false;
    $Regions = new mensio_regions();
    if ($Type === 'NewRegionType') {
      $Type = $Regions->GetNewCodeForRegions();
      $NewEntry = true;
    }
    if (!$Regions->Set_UUID($Type)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Type code not correct<br>';
    }
    if (!$Regions->Set_Country($Country)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Country code not correct<br>';
    }
    if (!$Regions->Set_Name($TypeName)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Type name not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if ($NewEntry) {
        if (!$Regions->AddNewRegionType()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] = 'New type was NOT saved<br>';
        }
      } else {
        if (!$Regions->UpdateRegionType()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] = 'Type was NOT Updated<br>';
        }
      }
    }
    unset($Regions);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Region type saved successfully<br>';
      $Options = $this->LoadRegionTypesOptions($Country);
      $RtrnData['TypeOptions'] = $Options['TypeOptions'];
      $RtrnData['TypeBtns'] = $Options['TypeBtns'];
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function EditRegionTypeData($Type) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Type'=>'','TypeName'=>'');
    $NoteType = '';
    $Regions = new mensio_regions();
    if ($Regions->Set_Type($Type)) {
      $Data = $Regions->LoadRegionTypeData();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $RtrnData['Type'] = $Row->uuid;
          $RtrnData['TypeName'] = $Row->name;
        }
      }
    } else {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with region type code<br>';
    }
    unset($Regions);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function DeleteRegionTypeData($Country,$Type) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','TypeOptions'=>'','TypeBtns'=>'');
    $NoteType = '';
    $Regions = new mensio_regions();
    if ($Regions->Set_Type($Type)) {
      if ($Regions->CheckRegionType()) {
        $NoteType = 'Info';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Region type is in use<br>';
      } else {
        if (!$Regions->DeleteRegionType()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Region type could not be deleted<br>';
        }
      }
    } else {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with region type code<br>';
    }
    unset($Regions);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Region type deleted successfully<br>';
      $Options = $this->LoadRegionTypesOptions($Country);
      $RtrnData['TypeOptions'] = $Options['TypeOptions'];
      $RtrnData['TypeBtns'] = $Options['TypeBtns'];
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function DeleteRegionData($Country,$RegCode) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Regions'=>'');
    $NoteType = '';
    $Regions = new mensio_regions();
    if ($Regions->Set_UUID($RegCode)) {
      if ($Regions->CheckIfRegionHasSubs()) {
        $NoteType = 'Info';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Region has sub regions. Can not be deleted<br>';
      } else {
        if (!$Regions->RemoveRegionData()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Region could not be deleted<br>';
        }
      }
    }
    unset($Regions);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Region deleted successfully<br>';
      $Lst = $this->LoadCountryRegions($Country);
      $RtrnData['Regions'] = $Lst['Regions'];
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function LoadModalTranslations($RegCode) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Modal'=>'');
    $NoteType = '';
    $Regions = new mensio_regions();
    if (!$Regions->Set_UUID($RegCode)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with region code<br>';
    } else {
      $Language = new mensio_languages();
      $AdminLang = $Language->ReturnMainLanguages('Admin');
      $Data = $Language->LoadLanguagesData();
      unset($Language);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Name = '';
          if (!$Regions->Set_Language($Row->uuid)) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Problem with language code<br>';
          } else {
            $Name = $Regions->LoadRegionTranslations();
            if ($Row->active) {
              $MainCls = '';
              if ($AdminLang === $Row->uuid) { $MainCls = 'MainFLD'; }
              $RtrnData['Modal'] .= '
              <label class="label_symbol">'.$Row->name.'</label><br>
              <input type="text" id="'.$Row->uuid.'" class="form-control Trn-Fields '.$MainCls.'" value="'.$Name.'">';
            }
          }
        }
        $RtrnData['Modal'] .= '<div class="button_row">
                  <button id="BTN_TransSave" class="button" title="Save Translation">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                  </button>
                </div>';
      }      
      $RtrnData['Modal'] = $this->CreateModalWindow('Translations', $RtrnData['Modal']);
    }
    unset($Regions);
    if ($RtrnData['ERROR'] !== 'FALSE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function UpdateRegionTranslations($RegCode,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Modal'=>'');
    $NoteType = '';
    $Regions = new mensio_regions();
    if (!$Regions->Set_UUID($RegCode)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with region code<br>';
    } else {
      $Data = stripslashes($Data);
      $Data = json_decode($Data, true);
      if (is_array($Data)) {
        foreach ($Data as $DataRow) {
          if ($Regions->Set_Language($DataRow['Field'])) {
            if ($Regions->Set_Name($DataRow['Value'])) {
              if (!$Regions->UpdateRegionTranslations()) {
                $NoteType = 'Alert';
                $RtrnData['ERROR'] = 'TRUE';
                $RtrnData['Message'] .= 'Region translation cound not be updated<br>';
              }
            } else {
              $NoteType = 'Alert';
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Problem with name given : '.$DataRow['Value'].'<br>';
            }
          } else {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Problem with language code<br>';
          }
        }
      }
    }
    unset($Regions);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Region Translations updated successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
} 
