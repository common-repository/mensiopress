<?php
class Mensio_Admin_Settings_Sectors extends mensio_core_form {
	private $DataSet;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->LoadSectorsDataSet();
    $this->ActivePage = 'Business_Sectors';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-sectors',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-admin-settings-sectors.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-sectors',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-admin-settings-sectors.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function LoadSubSectors($Parent,$Path) {
    $Sectors = new mensio_sectors();
    if ($Sectors->Set_Parent($Parent)) {
      $SubLevel = $Sectors->LoadSectorsList();
      foreach ($SubLevel as $Row) {
        $Span = $Path.'<i class="fa fa-arrow-right td-fa" aria-hidden="true"></i>';
        $Row->name = $Span.$Row->name;
        $this->DataSet[] = $Row;
        $this->LoadSubSectors($Row->uuid,$Row->name);
      }
    }
    unset($Sectors);
  }
  public function LoadSectorsDataSet($InSorter='') {
    $this->DataSet = array();
    $Sectors = new mensio_sectors();
    if ($InSorter != '') {
      $Sectors->Set_Sorter($InSorter);
    }
    $TopLevel = $Sectors->LoadSectorsList();
    foreach ($TopLevel as $Row) {
      $this->DataSet[] = $Row;
      $this->LoadSubSectors($Row->uuid,$Row->name);
    }
    if ($this->DataSet === '') { $this->DataSet[] = array(); }
    unset($Sectors);
  }
  private function SearchSectorsDataSet($InSearch,$InSorter) {
    $this->DataSet = array();
    $Sectors = new mensio_sectors();
    if ($InSorter != '') {
      $Sectors->Set_Sorter($InSorter);
    }
    if ($InSearch != '') {
      $Sectors->Set_SearchString($InSearch);
    }
    $TopLevel = $Sectors->SearchSectorsList();
    foreach ($TopLevel as $Row) {
      $this->DataSet[] = $Row;
      $this->LoadSubSectors($Row->uuid,$Row->name);
    }
    if ($this->DataSet === '') { $this->DataSet[] = array(); }
    unset($Sectors);
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='',$InSearch='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    if ($InSearch != '') {
      if ($InSorter != '') { $tbl->Set_Sorter($InSorter); }
      $this->SearchSectorsDataSet($InSearch,$InSorter);
    } else {
      if ($InSorter != '') {
        $tbl->Set_Sorter($InSorter);
        $this->LoadSectorsDataSet($InSorter);
      }
    }
    $TableData = $this->DataSet;
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array(
     'CHPR'=>'Change Parent'
    ));
    $tbl->Set_EditColumn('name');
    $tbl->Set_EditOptionsSubline(array(
        'Edit'
    ));
    $tbl->Set_Columns(array(
        'uuid:uuid:plain-text',
        'parent:parent:hidden',
        'name:Name:plain-text'
    ));
    $RtrnTable = $tbl->CreateTable(
            'Sectors',
            $TableData,
            array('uuid','parent','name')
    );
    unset($tbl,$Data);    
    return $RtrnTable;
  }
  public function GetSectorOptions() {
    $SectOptions = '<option value="TopLevel">No Parent Sector</option>';
    if (!empty($this->DataSet[0])) {
      foreach ($this->DataSet as $Row) {
        $Name = '';
        $Depth = explode('<i class="fa fa-arrow-right td-fa" aria-hidden="true"></i>', $Row->name);
        $DCount = count($Depth);
        $Name = $Depth[$DCount - 1];
        for ($i = 1; $i < $DCount; ++$i) {
          $Name = '--'.$Name; 
        }
        $SectOptions .= '<option value="'.$Row->uuid.'">'.$Name.'</option>';
      }
    }
    return $SectOptions;
  }
  public function GetSectorTransFields() {
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
  public function LoadSectorData($SctrCode) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Sector'=>'',
        'ParentSector'=>'','Translations'=>'');
    $NoteType = '';
    $Sector = new mensio_sectors();
    if (!$Sector->Set_UUID($SctrCode)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] = 'Problem with Sector code<br>';
    } else {
      $Data = $Sector->GetSectorData();
      foreach ($Data as $Row) {
        $RtrnData['Sector'] = $Row->uuid;
        $RtrnData['ParentSector'] = $Row->parent;
      }
      $Data = $Sector->GetSectorTranslationData();
      foreach ($Data as $Row) {
        if ($RtrnData['Translations'] != '') {
          $RtrnData['Translations'] .= '??';
        }
        $RtrnData['Translations'] .= $Row->language.'::'.$Row->name;
      }
    }
    unset($Sector);
    if ( $RtrnData['ERROR'] === 'TRUE' ) {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function UpdateSectorData($SctrCode,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NewSector = false;
    $NoteType = '';
    $Sector = new mensio_sectors();
    if ($SctrCode === 'NewSector') {
      $SctrCode = $Sector->GetNewSectorID();
      $NewSector = true;
    }
    if (!$Sector->Set_UUID($SctrCode)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Sector code<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Data = stripslashes($Data);
      $Data = json_decode($Data, true);
      if (is_array($Data)) {
        foreach ($Data as $DataRow) {
          if ($DataRow['Field'] === 'FLD_ParentSector') {
            $Sector->Set_Parent($DataRow['Value']);
            if ($NewSector) {
              if (!$Sector->InsertNewSector()) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Problem Saving New Sector<br>';
              }
            } else {
              if (!$Sector->UpdateSectorData()) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Problem Saving Sector data<br>';
              }
            }
          } else {
            if (!$Sector->Set_Language($DataRow['Field'])) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Problem with Sector Language : <br>';
            }
            if (!$Sector->Set_Name($DataRow['Value'])) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Problem with Sector Translation : '
                      .$DataRow['Value'].'<br>';
            }
            if ($RtrnData['ERROR'] === 'FALSE') {
              if (!$Sector->UpdateSectorTranslations()) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Problem Saving Sector Translations<br>';
              }
            }
          }
        }
      }
    }
    unset($Sector);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data Saved Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  private function GetLanguageName($language) {
    $Name = 'Not Found';
    $Lang = new mensio_languages();
    if ($Lang->Set_UUID($language)) {
      $Name = $Lang->Get_LanguageMainName();
    }
    unset($Lang);
    return $Name;
  }
  public function DeleteSectorData($SctrCode) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Sector = new mensio_sectors();
    if (!$Sector->Set_UUID($SctrCode)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] = 'Problem with code';
    } else {
      if ($Sector->CheckIfSectorInUse()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] = 'Sector has Sub Sectors or is used in other tables. Can not delete';
      } else {
        if (!$Sector->DeleteSector()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] = 'Sector could not be deleted';
        }
      }
    }
    unset($Sector);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Sector deleted successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function LoadModalParent($Selected) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Modal'=>'');
    $NoteType = '';
    $Selected = explode(';',$Selected);
    if (!is_array($Selected)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] = 'Problem with sector values<br>';
    } else {
      $SectOptions = '<option value="TopLevel">No Parent Sector</option>';
      if (!empty($this->DataSet[0])) {
        foreach ($this->DataSet as $Row) {
          $found = false;
          foreach ($Selected as $Sector) {
            if ($Sector === $Row->uuid) { $found = true; }
          }
          if (!$found) {
            $Name = '';
            $Depth = explode('<i class="fa fa-arrow-right td-fa" aria-hidden="true"></i>', $Row->name);
            $DCount = count($Depth);
            $Name = $Depth[$DCount - 1];
            for ($i = 1; $i < $DCount; ++$i) {
              $Name = '--'.$Name; 
            }            
            $SectOptions .= '<option value="'.$Row->uuid.'">'.$Name.'</option>';
          }
        }
      } 
      $ModalBody = '
          <label class="label_symbol">Select Parent Sector</label>
          <select id="MDL_ParentSector" class="form-control">
            '.$SectOptions.'
          </select>            
          <button id="BTN_ModalSave" class="button" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>';
      $RtrnData['Modal'] = $this->CreateModalWindow('Update Parent Sector',$ModalBody);
    }
    if ( $RtrnData['ERROR'] === 'TRUE' ) {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function UpdateModalParent($Selected,$Parent) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Modal'=>'');
    $NoteType = '';
    $Sector = new mensio_sectors();
    if (!$Sector->Set_Parent($Parent)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] = 'Problem with parent code';
    } else {
      $Selected = explode(';',$Selected);
      if (is_array($Selected)) {
        foreach ($Selected as $SctCode) {
          if ($SctCode !== '') {
            if (!$Sector->Set_UUID($SctCode)) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Problem with sector code !!';
            } else {
              if (!$Sector->UpdateSectorData()) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Sector code : '.$SctCode.' could not be updated';
              }
            }
          }
        }
      }
    }
    unset($Sector);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Sectors updated successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);    
    return $RtrnData;
  }
}