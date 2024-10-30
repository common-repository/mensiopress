<?php
class Mensio_Admin_Settings_Store extends mensio_core_form {
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->ActivePage = 'Store_Settings';
  }
  function Load_Page_CSS($name) {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-Store',
     plugin_dir_url( __FILE__ ) . '../../css/'.$name.'.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  function Load_Page_JS($name) {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-Store',
     plugin_dir_url( __FILE__ ) . '../../js/'.$name.'.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  public function LoadStoreSettingsData() {
    $DataSet = array();
    $Store = new mensio_store();
    $Data = $Store->LoadStoreData();
    unset($Store);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $DataSet['uuid'] = $Row->uuid;
        $DataSet['ThemeLang'] = $Row->themelang;
        $DataSet['ThmActiveLang'] = $Row->thmactivelang;
        $DataSet['AdminLang'] = $Row->adminlang;
        $DataSet['Name'] = $Row->name;
        $DataSet['Country'] = $Row->country;
        $DataSet['TZone'] = $Row->tzone;
        $DataSet['City'] = $Row->city;
        $DataSet['Street'] = $Row->street;
        $DataSet['Number'] = $Row->number;
        $DataSet['Phone'] = $Row->phone;
        $DataSet['Fax'] = $Row->fax;
        $DataSet['EMail'] = $Row->email;
        $DataSet['GglAnalytics'] = stripslashes($Row->gglstats);
        $DataSet['GglMap'] = stripslashes($Row->gglmap);
        if ($Row->logo !== 'NotSet') { $DataSet['Logo'] = get_site_url().'/'.$Row->logo; }
          else {$DataSet['Logo'] = plugins_url('mensiopress/admin/icons/default/empty.png'); }
        $DataSet['Currency'] = $Row->currency;
        $DataSet['CurrUpdate'] = $Row->update_currency;
        $DataSet['Barcode'] = $Row->barcode;
        $DataSet['OrderSerial'] = $Row->orderserial;
        $DataSet['TblRows'] = $Row->tblrows;
        $DataSet['NotifTime'] = $Row->notiftime;
        $Data = $this->LoadLastTerm($Row->uuid);
        $DataSet['TermID'] = $Data['uuid'];
        $DataSet['Term'] = $Data['Text'];
        $Metrics = explode(';',$Row->metrics);
        foreach ($Metrics as $Setting) {
          $Setting = explode(':',$Setting);
          $DataSet[$Setting[0]] = $Setting[1];
        }
        if (is_numeric($Row->mailsettings)) {
          $DataSet['MailSettings'] = 'sendmail';
          $DataSet['MailsPerMinute'] = $Row->mailsettings;
        } else{
          $MailSettings = explode(';;',$Row->mailsettings);
          foreach ($MailSettings as $Setting) {
            $Setting = explode(':',$Setting);
            $DataSet[$Setting[0]] = $Setting[1];
          }
          $DataSet['MailSettings'] = 'smtp';
        }
      }
    }
    return $DataSet;
  }
  public function LoadCountryOptions($CountryID) {
    $Options = '';
    $Countries = new mensio_countries();
    $DataSet = $Countries->GetCountriesDataSet();
    unset($Countries);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        if ($CountryID === $Row->uuid) {
          $Options .= '<option value="'.$Row->uuid.'" selected>'.$Row->country.'</option>';
        } else {
          $Options .= '<option value="'.$Row->uuid.'">'.$Row->country.'</option>';
        }
      }
    }
    return $Options;
  }
  public function LoadTimezonesOptions($TZone) {
    $Options = '<option value="UTC">Default UTC</option>';
    $DataSet = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    foreach ($DataSet as $Row) {
      if ($TZone === $Row) {
        $Options .= '<option value="'.$Row.'" selected>'.$Row.'</option>';
      } else {
        $Options .= '<option value="'.$Row.'">'.$Row.'</option>';
      }
    }
    if ($TZone === 'DEFAULT') {
      $Options = str_replace('value="DEFAULT"', 'value="DEFAULT" selected', $Options);
    }
    return $Options;
  }
  public function LoadLanguageOptions($LangID) {
    $Options = '';
    $Languages = new mensio_languages();
    $DataSet = $Languages->LoadLanguagesData();
    unset($Languages);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        if ($Row->active) {
          if ($LangID === $Row->uuid) {
            $Options .= '<option value="'.$Row->uuid.'" selected>'.$Row->name.'</option>';
          } else {
            $Options .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
          }
        }
      }
    }
    return $Options;
  }
  public function LoadActiveThemeLanguages($ActiveLang) {
    $LangList = '';
    $ActiveLang = explode(';',$ActiveLang);
    $Languages = new mensio_languages();
    $DataSet = $Languages->LoadLanguagesData();
    unset($Languages);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        if ($Row->active) {
          $Checked = '';
          $Value = '0';
          if (in_array($Row->uuid, $ActiveLang)) {
            $Checked = 'checked';
            $Value = '1';
          }
          $LangList .='
                <div class="ListRow">
                  <div class="LangName">
                    '.$Row->name.'
                  </div>
                  <div class="LangCheck">
                    <input type="checkbox" id="'.$Row->uuid.'" class="ChkLang" value="'.$Value.'" '.$Checked.'>
                  </div>
                  <div class="DivResizer"></div>
                </div>';
        }
      }
    }
    return $LangList;
  }
  public function LoadCurrencyOptions($CurrID) {
    $Options = '';
    $Currencies = new mensio_currencies();
    $DataSet = $Currencies->LoadCurrencies();
    unset($Currencies);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        if ($CurrID === $Row->uuid) {
          $Options .= '<option value="'.$Row->uuid.'" selected>'.$Row->name.'</option>';
        } else {
          $Options .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
        }
      }
    }
    return $Options;
  }
  public function UpdateStoreBasicData($StoreID,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Store = new mensio_store();
    if (!$Store->Set_UUID($StoreID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Store ID<br>';
    } else {
      $Data = stripslashes($Data);
      $Data = json_decode($Data, true);
      if (is_array($Data)) {
        foreach ($Data as $Row) {
          $SetFun = str_replace('FLD_', 'Set_', $Row['Field']);
          $FieldName = str_replace('FLD_', '', $Row['Field']);
          if (!$Store->$SetFun($Row['Value'])) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Problem with Field '.$FieldName.'<br>'.$Row['Value'].'<br>';
          }
        }
        if ($RtrnData['ERROR'] === 'FALSE') {
          if (!$Store->UpdateStoreData()) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Problem while updating store setting fields<br>';
          }
        }
      }
    }
    unset($Store);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data Saved Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function UpdateStoreSupportLanguages($StoreID,$Admin,$Theme,$Active) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Store = new mensio_store();
    if (!$Store->Set_UUID($StoreID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Store ID<br>';
    }
    if (!$Store->Set_AdminLang($Admin)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Admin selected language<br>';
    }
    if (!$Store->Set_ThemeLang($Theme)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Theme selected language<br>';
    }
    if ($Active !== '') {
      $Active = explode(';', $Active);
      foreach ($Active as $Row) {
        if ($Row !== '') {
          if (!$Store->Set_ThmActiveLang($Row)) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Problem with selected Active language IDs<br>';
          }
        }
      }
    } else {
      if (!$Store->Set_ThmActiveLang($Theme)) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Problem with Active Theme language<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Store->UpdateStoreData()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Problem while updating store setting fields<br>';
      }
    }
    unset($Store);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data Saved Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function UpdateStoreDefaultCurrency($StoreID,$Currency) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Store = new mensio_store();
    if (!$Store->Set_UUID($StoreID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Store ID<br>';
    }
    if (!$Store->Set_Currency($Currency)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with selected Currency<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Store->UpdateStoreData()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Problem while updating store setting fields<br>';
      }
    }
    unset($Store);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data Saved Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function UpdateStoreTableView($StoreID,$TblRows,$NotifTime) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Store = new mensio_store();
    if (!$Store->Set_UUID($StoreID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Store ID<br>';
    }
    if (!$Store->Set_TblRows($TblRows)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Not correct Currency value<br>';
    } else {
      $Check = $Store->CheckTblRows();
      if ($Check !== 'OK') {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Info';
        $RtrnData['Message'] .= $Check;
      }
    }
    if (!$Store->Set_NotifTime($NotifTime)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Not correct Notification display time value<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Store->UpdateStoreData()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Problem while updating store setting fields<br>';
      }
    }
    unset($Store);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data Saved Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function UpdateStoreOrderSettings($StoreID,$OrderSerial) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Store = new mensio_store();
    if (!$Store->Set_UUID($StoreID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Store ID<br>';
    }
    if (!$Store->Set_OrderSerial($OrderSerial)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Not correct Order Serial value<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Store->UpdateStoreData()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Problem while updating store setting fields<br>';
      }
    }
    unset($Store);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data Saved Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function UpdateStoreProductMetrics($StoreID,$Color,$Height,$Length,$Size,$Volume,$Weight,$Width) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Metrics = '';
    $Store = new mensio_store();
    if (!$Store->Set_UUID($StoreID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Store ID<br>';
    } else {
      if ($this->CheckMetric('Color',$Color)) { $Metrics .= 'Color:'.$Color.';';}
      else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Color value not correct<br>';
      }
      if ($this->CheckMetric('Height',$Height)) { $Metrics .= 'Height:'.$Height.';';}
      else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Height value not correct<br>';
      } 
      if ($this->CheckMetric('Length',$Length)) { $Metrics .= 'Length:'.$Length.';';}
      else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Length value not correct<br>';
      } 
      if ($this->CheckMetric('Size',$Size)) { $Metrics .= 'Size:'.$Size.';';}
      else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Size value not correct<br>';
      }
      if ($this->CheckMetric('Volume',$Volume)) { $Metrics .= 'Volume:'.$Volume.';';}
      else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Volume value not correct<br>';
      }
      if ($this->CheckMetric('Weight',$Weight)) { $Metrics .= 'Weight:'.$Weight.';';}
      else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Weight value not correct<br>';
      }
      if ($this->CheckMetric('Width',$Width)) { $Metrics .= 'Width:'.$Width;}
      else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Width value not correct<br>';
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        if ($Store->Set_Metrics($Metrics)) {
          if (!$Store->UpdateStoreData()) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Metrics could not be updated<br>';
          }
        }
      }
    }
    unset($Store);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data Saved Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  private function CheckMetric($Metric,$Value) {
    $Correct = false;
    switch ($Metric) {
      case 'Color':
        if (($Value ==='TXT') || ($Value ==='HEX') || ($Value ==='RGB') || ($Value ==='IMG')) {
          $Correct = true;
        }
        break;
      case 'Size':
        if (($Value ==='TXT') || ($Value ==='NUM')) { $Correct = true; }
        break;
      case 'Volume':
        if (($Value ==='TXT') || ($Value ==='GAL') || ($Value ==='LTR') ||
            ($Value ==='CMT') || ($Value ==='CFT')) { $Correct = true; }
        break;
      case 'Length':
      case 'Height':
      case 'Width':
        if (($Value ==='TXT') || ($Value ==='MMT') || ($Value ==='CMT') ||
            ($Value ==='MTR') || ($Value ==='INC') || ($Value ==='FOT') ||
            ($Value ==='YRD')) { $Correct = true; }
        break;
      case 'Weight':
        if (($Value ==='TXT') || ($Value ==='KLG') || ($Value ==='GRM') ||
            ($Value ==='GAL') || ($Value ==='LTR')) { $Correct = true; }
        break;
    }
    return $Correct;
  }
  public function LoadLastTerm($StoreID) {
    $RtrnData = array('uuid'=>'NewTerm','Text'=>'');
    $Store = new mensio_store();
    if ($Store->Set_UUID($StoreID)) {
      $DataSet = $Store->LoadStoreTermsOfUse(true);
    }
    unset($Store);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        if (!$Row->published) { $RtrnData['uuid'] = $Row->uuid; }
        $RtrnData['Text'] = $Row->useterms;
      }
    }
    return $RtrnData;
  }
  public function LoadTermsOfUseList($StoreID,$SelectedID="") {
    $RtrnData = '';
    $Store = new mensio_store();
    if ($Store->Set_UUID($StoreID)) {
      $DataSet = $Store->LoadStoreTermsOfUse();
    }
    unset($Store);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $Active = '';
        $Selected = '';
        if ($Row->active) { $Active = 'ActiveTerm'; }
        if ($SelectedID === $Row->uuid) { $Selected = 'SelectedTerm'; }
        if ($Row->published) {
          $id = 'id="VW_'.$Row->uuid.'"';
          $Buttons = '<div class="TagBtn" title="Vew Terms of Use">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                  </div>';
        } else {
          $id = 'id="EDT_'.$Row->uuid.'"';
          $Buttons = '<div class="TagBtn" title="Edit Terms of Use">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                  </div>';
        }
        $RtrnData .= '
              <div '.$id.' class="TermSelector '.$Active.' '.$Selected.'">
                <div class="TermSelectorDate">'.$Row->editdate.'</div>
                <div class="TermsTagBtns">
                  '.$Buttons.'
                </div>
              </div>';
      }
    }
    return $RtrnData;
  }
  public function UpdateStoreUseTerms($StoreID,$TermID,$TermsOfUse) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Term'=>'','Date'=>'','List'=>'');
    $NoteType = '';
    $NewEntry = false;
    $Store = new mensio_store();
    if (!$Store->Set_UUID($StoreID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Store ID<br>';
    }
    if ($TermID === 'NewTerm') {
      $TermID = $Store->GetNewID();
      $NewEntry = true;
    }
    if (!$Store->Set_TermID($TermID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Term ID<br>';
    }
    if (!$Store->Set_Terms($TermsOfUse)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Terms of Use text has errors or unaccepted characters<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if ($NewEntry) {
        $Date = $Store->AddNewStoreTerms();
        if (!$Date) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Terms of Use text could not be saved<br>';
        }
      } else {
        $Date = $Store->UpdateStoreTerms();
        if (!$Date) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Terms of Use text could not be updated<br>';
        }
      }
    }
    unset($Store);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data Saved Successfully';
      $RtrnData['Term'] = $TermID;
      $RtrnData['Date'] = $Date;
      $RtrnData['List'] = $this->LoadTermsOfUseList($StoreID);
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function UpdatePublishTerms($StoreID,$TermID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Date'=>'','List'=>'');
    $NoteType = '';
    $Store = new mensio_store();
    if (!$Store->Set_TermID($TermID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Term ID<br>';
    } else {
      $RtrnData['Date'] = $Store->UpdatePublishedTermsOfUse();
      if (!$RtrnData['Date']) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Terms of Use could not be published<br>';
      }
    }
    unset($Store);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Terms of Use Published Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    $RtrnData['List'] = $this->LoadTermsOfUseList($StoreID);
    return $RtrnData;
  }
  public function LoadTermsViewModal($TermID) {
    $Store = new mensio_store();
    if ($Store->Set_TermID($TermID)) {
      $DataSet = $Store->LoadStoreTermsOfUseData();
    }
    unset($Store);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $Title = 'Terms Of Use '.$Row->editdate;
        $MdlForm = '<div class="ModalTermsView">'.stripslashes($Row->useterms).'</div>';
      }
    }
    return $this->CreateModalWindow($Title, $MdlForm);
  }
  public function LoadTermsViewData($TermID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Term'=>'','Date'=>'','Notes'=>'');
    $Store = new mensio_store();
    if ($Store->Set_TermID($TermID)) {
      $DataSet = $Store->LoadStoreTermsOfUseData();
    }
    unset($Store);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        if (!$Row->published) {
          $RtrnData['Term'] = $Row->uuid;
          $RtrnData['Date'] = $Row->editdate;
          $RtrnData['Notes'] = stripslashes($Row->useterms);
        } else {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] = 'Terms of Use Published Successfully';
        }
      }
    }
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function RemoveTermsViewData($StoreID,$TermID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','List'=>'');
    $Store = new mensio_store();
    if (!$Store->Set_TermID($TermID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'Terms of Use id not correct<br>';
    } else {
      if (!$Store->DeleteTermOfUse()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] = 'Terms of Use could not be removed<br>';
      } else {
        $NoteType = 'Success';
        $RtrnData['Message'] = 'Terms of Use removed successfully<br>';
      }
    }
    unset($Store);
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $RtrnData['List'] = $this->LoadTermsOfUseList($StoreID);
    }
    return $RtrnData;
  }
  public function LoadUserPerTable() {
    $RtrnData = '';
    $Select = '
      <select id="" class="form-control ChPerm">
        <option value="0">Not Allowed</option>
        <option value="1">Allowed</option>
      </select>';
    $Store = new mensio_store();
    $DataSet = $Store->LoadUserPermissions();
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      $RtrnData = '<table class="PermissionsTbl">
          <thead>
            <tr>
              <th>User</th>
              <th>Catalog</th>
              <th>Customers</th>
              <th>Commerce</th>
              <th>design</th>
              <th>Settings</th>
              <th>System</th>
            </tr>
          </thead>
          <tbody>';
      foreach ($DataSet as $Row) {
        $Products = str_replace('id=""','id="'.$Row->user_login.'_products"',$Select);
        $Products = str_replace('value="'.$Row->products.'"','value="'.$Row->products.'" selected',$Products);
        $Customers = str_replace('id=""','id="'.$Row->user_login.'_customers"',$Select);
        $Customers = str_replace('value="'.$Row->customers.'"','value="'.$Row->customers.'" selected',$Customers);
        $Orders = str_replace('id=""','id="'.$Row->user_login.'_orders"',$Select);
        $Orders = str_replace('value="'.$Row->orders.'"','value="'.$Row->orders.'" selected',$Orders);
        $Reports = str_replace('id=""','id="'.$Row->user_login.'_reports"',$Select);
        $Reports = str_replace('value="'.$Row->reports.'"','value="'.$Row->reports.'" selected',$Reports);
        $Design = str_replace('id=""','id="'.$Row->user_login.'_design"',$Select);
        $Design = str_replace('value="'.$Row->design.'"','value="'.$Row->design.'" selected',$Design);
        $Settings = str_replace('id=""','id="'.$Row->user_login.'_settings"',$Select);
        $Settings = str_replace('value="'.$Row->settings.'"','value="'.$Row->settings.'" selected',$Settings);
        $System = str_replace('id=""','id="'.$Row->user_login.'_system"',$Select);
        $System = str_replace('value="'.$Row->system.'"','value="'.$Row->system.'" selected',$System);
        $RtrnData .= '
            <tr>
              <td class="usrspn">'.$Row->user_login.'</td>
              <td>'.$Products.'</td>
              <td>'.$Customers.'</td>
              <td>'.$Orders.'</td>
              <td>'.$Design.'</td>
              <td>'.$Settings.'</td>
              <td>'.$System.'</td>
            </tr>';
      }
      $RtrnData .= '</tbody></table>';
    }
    unset($Store);
    return $RtrnData;
  }
  public function UpdateUserPermissionList($Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Store = new mensio_store();
    $Data = stripslashes($Data);
    $Data = json_decode($Data, true);
    if (is_array($Data)) {
      foreach ($Data as $Row) {
        $Option = explode('_',$Row['Field']);
        $User = $Option[0];
        if (!$Store->Set_WPUser($User)) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'User "'.$Option[0].'" not found<br>';
        }
        if (!$Store->Set_Page($Option[1])) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Page "'.$Option[1].'" not found<br>';
        }
        $Store->Set_Access($Row['Value']);
        if ($RtrnData['ERROR'] === 'FALSE') {
          if (!$Store->UpdateUserPermissions()) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Permission for User "'.$Option[0].'" and Page "'.$Option[1].'" could not be updated<br>';
          }
        }
      }
    }
    unset($Store);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Permissions updated successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function LoadUserPermissionModal() {
    $MdlForm = '
    <div id="ModalUserSelector">
      '.$this->CreateUserSelectionTable().'
    </div>';
    return $this->CreateModalWindow('User Selection', $MdlForm);
  }
  private function CreateUserSelectionTable() {
    $RtrnData = '<table class="PermissionsTbl">
          <thead>
            <tr>
              <th>User</th>
              <th>E-Mail</th>
              <th>Nicename</th>
              <th class="CtrlCol"></th>
            </tr>
          </thhead>
          <tbody>';
    $Store = new mensio_store();
    $DataSet = $Store->LoadUnselectedUsers();
    unset($Store);
    if (is_array($DataSet)) {
      foreach ($DataSet as $Row) {
        $RtrnData .= '<tr id="'.$Row->user_login.'">
              <td>'.$Row->user_login.'</td>
              <td>'.$Row->user_email.'</td>
              <td>'.$Row->user_nicename.'</td>
              <td class="CtrlCol">
                <div id="Btn_'.$Row->user_login.'" class="UsrPmnBtn" title="Add user '.$Row->user_login.'">
                  <i class="fa fa-plus" aria-hidden="true"></i>
                </div>
              </td>
            </tr>';
      }
    }
    $RtrnData .= '</tbody></table>';
    return $RtrnData;
  }
  public function AddNewUserToPermissions($User) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Store = new mensio_store();
    if (!$Store->Set_WPUser($User)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'User "'.$User.'" was not correct<br>';
    } else {
      if (!$Store->AddNewUserPermissions()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'User "'.$User.'" could not be added<br>';
      }
    }
    unset($Store);
    if ($RtrnData['ERROR'] !== 'FALSE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function UpdateStoreMailSettings($StoreID,$Mailer,$Host,$SMTPAuth,$SMTPSecure,$Port,$Username,$Password,$From,$FromName,$MailsPerMinute) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Error = false;
    $Store = new mensio_store();
    if (!$Store->Set_UUID($StoreID)) {
      $Error = true;
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Store code was not correct<br>';
    }
    if ($Mailer !== 'sendmail') {
      if (!$Store->Set_Host($Host)) {
        $Error = true;
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Host data was not correct<br>';
      }
      if (!$Store->Set_SMTPAuth($SMTPAuth)) {
        $Error = true;
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'SMTP Auth values must "0" or "1"<br>';
      }
      if (!$Store->Set_SMTPAuth($SMTPAuth)) {
        $Error = true;
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'SMTP Auth values must "0" or "1"<br>';
      }
      if (!$Store->Set_SMTPSecure($SMTPSecure)) {
        $Error = true;
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'SMTP Security protocol not correct<br>'.$SMTPSecure;
      }
      if (!$Store->Set_Port($Port)) {
        $Error = true;
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Port not correct<br>';
      }
      if (!$Store->Set_Username($Username)) {
        $Error = true;
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Username not correct<br>';
      }
      if (!$Store->Set_Password($Password)) {
        $Error = true;
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Password not correct<br>';
      }
      if (!$Store->Set_From($From)) {
        $Error = true;
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'From Address not correct<br>';
      }
      if (!$Store->Set_FromName($FromName)) {
        $Error = true;
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'From Name not correct<br>';
      }
    }
    if (!$Store->Set_MailsPerMinute($MailsPerMinute)) {
      $Error = true;
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Mails per minute value is not correct<br>';
    }
    if (!$Error) {
      if (!$Store->UpdateMailSettings($Mailer)) {
        $Error = true;
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Store Mail settings could not be updated<br>';
      }
    }
    unset($Store);
    if ($RtrnData['ERROR'] !== 'FALSE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function SendTestMail($Address,$Template='',$store='') {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $MailSettings = $this->LoadMailSettings();
    if (!is_numeric($MailSettings)) { $RtrnData = $this->SendSMTPTestMails($MailSettings,$Address,$Template,$store); }
      else { $RtrnData = $this->SendTestMails($Address,$Template,$store); }
    return $RtrnData;
  }
  public function SendTestMails($Address,$Template,$store) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $current_user = wp_get_current_user();
    $Address = str_replace(' ', '', $Address);
    $Address = preg_replace('/\s+/', '', $Address);
    if ($Address === '') { $Address = $current_user->user_email; }
    $headers = array('Content-Type: text/html; charset=UTF-8');
    if ($Template === '') {
      $body = '<html>
        <head>
        </head>
        <body>
          <span style="display: block;width: 50px;">To: </span>'.$current_user->user_lastname.' '.$current_user->user_firstname.'
          <br>
          This is a test message send to you from Mensiopress (Local Server)
        </body>
      </html>';
    } else {
      $body = $this->CreateTestTemplate($store,$Template);
    }
    if (wp_mail( $current_user->user_email, 'Mensiopress Test Mail', $body, $headers)) {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'E-Mail was send at '.$Address.' for user '.$current_user->display_name.'<br>';
    } else {
      $NoteType = 'Info';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'E-Mail could not be send at '.$Address.' for user '.$current_user->display_name.'<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function SendSMTPTestMails($MailSettings,$Address,$Template,$store) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    global $phpmailer; // define the global variable
    if ( !is_object( $phpmailer ) || !is_a( $phpmailer, 'PHPMailer' ) ) { // check if $phpmailer object of class PHPMailer exists
      require_once ABSPATH . WPINC . '/class-phpmailer.php';
      require_once ABSPATH . WPINC . '/class-smtp.php';
      $phpmailer = new PHPMailer( true );
    }
    $phpmailer->CharSet = 'UTF-8';
    $phpmailer->isSMTP(); // Set mailer to use SMTP
    $MailSettings = explode(';;',$MailSettings);
    foreach ($MailSettings as $Setting) {
      $Setting = explode(':',$Setting);
      switch ($Setting[0]) {
        case 'Host': // Specify main and backup SMTP servers
          $phpmailer->Host = $Setting[1];
          break;
        case 'SMTPAuth': // Enable SMTP authentication
          if ($Setting[1] === '1') { $phpmailer->SMTPAuth = true; }
            else { $phpmailer->SMTPAuth = false; }
          break;
        case 'SMTPSecure':  // Enable encryption, `ssl` and 'tls' also accepted
          $phpmailer->SMTPSecure = $Setting[1];
          break;
        case 'Port': // TCP port to connect to
          $phpmailer->Port = $Setting[1];
          break;
        case 'Username': // SMTP username
          $phpmailer->Username = $Setting[1];
          break;
        case 'Password': // SMTP password
          $phpmailer->Password = $Setting[1];
          break;
        case 'From':
          $From = $Setting[1];
          break;
        case 'FromName':
          $FromName = $Setting[1];
          break;
        case 'MailsPerMinute':
         $RecNo = $Setting[1];
         break;
      }
    }
    $phpmailer->setFrom($From,$FromName);
    $current_user = wp_get_current_user();
    $Address = str_replace(' ', '', $Address);
    $Address = preg_replace('/\s+/', '', $Address);
    if ($Address === '') { $Address = $current_user->user_email; }
    if ($Template === '') {
      $body = '<html><head></head><body>This is a test message send to you from Mensiopress</body></html>';
    } else {
      $body = $this->CreateTestTemplate($store,$Template);
    }
    $phpmailer->ClearAllRecipients( ); // clear all
    $phpmailer->addAddress($Address, $current_user->user_lastname.' '.$current_user->user_firstname);
    $phpmailer->isHTML();
    $phpmailer->Subject = 'Mensiopress Test Mail';
    $phpmailer->Body    = $body;
    $phpmailer->AltBody = wp_strip_all_tags($body);
    if ($phpmailer->send()) {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'E-Mail was send at '.$Address.' from '.$From.'<br>';
    } else {
      $NoteType = 'Info';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = 'E-Mail could not be send at '.$Address.' from '.$From.'<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  private function CreateTestTemplate($Store,$Template) {
    $RtrnData = $this->LoadTemplateForEditing($Store,$Template);
    if ($RtrnData['ERROR'] === 'FALSE') {    
      $tmplt = $RtrnData['Template'];
      $tmplt = str_replace('[%STORELOGO%]', 'Image logo of the Store', $tmplt);
      $tmplt = str_replace('[%STORENAME%]', 'Demo Mail Store', $tmplt);
      $tmplt = str_replace('[%STOREMAIL%]', 'demo@store', $tmplt);
      $tmplt = str_replace('[%ORDERNUMBER%]', 'DemoOrderCodeHere', $tmplt);
      $text = 'DEMO TABE FOLLOWS (Not Real Data)<br><table cellpadding="20"><tbody><tr><td colspan="6" align="center"><img src="IMAGE HERE" moz-do-not-send="true"></td></tr><tr><td><a href="#" moz-do-not-send="true"><img src="IMAGE HERE" moz-do-not-send="true" width="150" height="150"></a></td><td><a href="#" moz-do-not-send="true">Nike Zoom Cage 3 Women"s Tennis Shoe</a></td><td><span class="mensioPrice">173.60</span></td><td><br></td><td>x1</td><td><span class="mensioPrice">173.60</span></td></tr><tr><td><a href="#" moz-do-not-send="true"><img src="IMAGE HERE" moz-do-not-send="true" width="150" height="150"></a></td><td><a href="#" moz-do-not-send="true">Nike Zoom Cage 3 Women"s Tennis Shoe</a></td><td><span class="mensioPrice">173.60</span></td><td><br></td><td>x1</td><td><span class="mensioPrice">173.60</span></td></tr><tr><td colspan="2">Shipping Cost</td><td colspan="3"><br></td><td class="mensioPrice">100.00</td></tr><tr><td colspan="6"><hr></td></tr><tr><td colspan="5" align="right"><br></td><td><span class="mensioPrice">447.20</span></td></tr><tr><td colspan="6"><table><tbody><tr><td>Name:</td><td>John</td></tr><tr><td>LastName:</td><td>Diver</td></tr><tr><td>Country:</td><td>Greece</td></tr><tr><td>City:</td><td>Pefki</td></tr><tr><td>Address:</td><td>Irinis 46</td></tr></tbody></table></td></tr></tbody></table><style>.mensioPrice:before{content:"â‚¬";}</style>';
      $tmplt = str_replace('[%ORDERLIST%]', $text, $tmplt);
      $tmplt = str_replace('[%STATUSNAME%]', 'Status Description Here', $tmplt);
      $tmplt = str_replace('[%STATUSTEXT%]', 'Status Text Here. Propably a few linews more', $tmplt);
      $tmplt = str_replace('[%TITLE%]', 'Mr. Miss or empty Will Be Inserted Here', $tmplt);
      $tmplt = str_replace('[%LASTNAME%]', 'Lastname', $tmplt);
      $tmplt = str_replace('[%FIRSTNAME%]', 'Firstname', $tmplt);
      $tmplt = str_replace('[%TICKETCODE%]', 'Ticket Code Here', $tmplt);
      $text = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras semper volutpat malesuada. Phasellus mauris tellus, pretium a ullamcorper quis, ultrices eget dui. Aenean et imperdiet mi. Aliquam erat volutpat. Aenean at pretium eros. Sed non enim libero. Vestibulum congue suscipit elit ac semper. Integer vestibulum, est vel facilisis consequat, magna metus dignissim nulla, sed rutrum magna sem sed ex. Suspendisse tempus egestas lobortis. Nam vestibulum bibendum urna, at imperdiet nisi rutrum ac. Proin ac sem facilisis metus facilisis ultricies. Pellentesque eget massa nisl. Fusce iaculis non eros non consectetur. Praesent eleifend ultrices massa sagittis imperdiet. Nullam in tellus metus.</p><p>Maecenas elementum a magna ut efficitur. Aliquam vel turpis sapien. Vivamus ultrices sem odio, vitae blandit lectus euismod non. Nullam mattis rutrum libero, semper posuere turpis dictum vitae. Donec gravida nec dolor a eleifend. Aliquam posuere bibendum eros id tempor. Praesent imperdiet ligula at neque ultrices ultrices. Integer in erat eleifend, tincidunt leo a, ornare tortor.</p>';
      $tmplt = str_replace('[%REPLYTEXT%]', $text, $tmplt);
      $tmplt = str_replace('[%CPNKEY%]', '#12345dfg678ghjk90', $tmplt);
      $RtrnData = str_replace('[%%REGISTERCONFIRM%]', '#', $tmplt);
    } else {
      $RtrnData = $RtrnData['Message'];
    }
    return $RtrnData;
  }
  public function LoadTemplateForEditing($StoreID,$Template) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Template'=>'');
    $NoteType = '';
    $Error = false;
    $Store = new mensio_store();
    if (!$Store->Set_UUID($StoreID)) {
      $Error = true;
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Store code was not correct<br>';
    }
    if (!$Store->Set_Template($Template)) {
      $Error = true;
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Template code was not correct<br>';
    }
    if (!$Error) {
      $RtrnData['Template'] = $Store->LoadStoreMailTemplate();
      if ($RtrnData['Template'] === '') { $RtrnData['Template'] = $this->DefaultMailTemplate($Template);
      }
    }
    unset($Store);
    if ($RtrnData['ERROR'] !== 'FALSE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function UpdateMailTemplate($Name,$Template) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Template'=>'');
    $NoteType = '';
    if ($Template === 'EMPTY') {
      $Template = addslashes($this->DefaultMailTemplate($Name));
    } else {
      $Template = str_replace( '<script>', '', $Template );
      $Template = str_replace( '</script>', '', $Template );
    }
    $Store = new mensio_store();
    if ($Store->Set_Template($Name)) {
      if (!$Store->UpdateMailTemplatePost($Template)) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Template could not be updated<br>';
      }
    } else {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Template code was not correct<br>';
    }
    unset($Store);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] .= 'Template was updated successfully<br>';
      $RtrnData['Template'] = stripslashes($Template);
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  final public function LoadMailTemplateInfo() {
   $InfoPopUp = '
    <div id="HelpInfoPopUp" class="HelpPopUp">
      <p><span class="VarName">[%GENERALMAIL%]</span> We use this variable to set any number defferent mail texts</p>
      <p><span class="VarName">[%STORELOGO%]</span> We use this variable to set the logo image of the store</p>
      <p><span class="VarName">[%STORENAME%]</span> We use this variable to set the name of the store in the E-Mail</p>
      <p><span class="VarName">[%STOREMAIL%]</span> It is used for setting the E-Mail of the store</p>
      <p><span class="VarName">[%ORDERNUMBER%]</span> The unique number of the order</p>
      <p><span class="VarName">[%ORDERLIST%]</span> The list of the products that are part of the order</p>
      <p><span class="VarName">[%STATUSNAME%]</span> The name of the status of the order (Active, Canceled, etc)</p>
      <p><span class="VarName">[%STATUSTEXT%]</span> The informatin text send from the system to the customer</p>
      <p><span class="VarName">[%TITLE%]</span> Mr. Miss or empty depending on what the customer selected during registration</p>
      <p><span class="VarName">[%LASTNAME%]</span> The customers last name</p>
      <p><span class="VarName">[%FIRSTNAME%]</span> The customers first name</p>
      <p><span class="VarName">[%TICKETCODE%]</span> The unique code for the hellp or question request of the customer</p>
      <p><span class="VarName">[%REPLYTEXT%]</span> the tickets reply text</p>
      <p><span class="VarName">[%CPNKEY%]</span> The unique key for each customer. It is created dynamically. IT CAN NOT be omitted</p>
      <p><span class="VarName">[%REGISTERCONFIRM%]</span> The confirmation area selection of the E-Mail we send after the customer has been register. IT CAN NOT be omitted</p>
      <p><span class="VarName">[%FORGOTPASSWORDLINK%]</span> The link for the new password for the forgotten password option</p>
      <p><span class="VarName">[%FORGOTPASSWORDCODE%]</span> Confirmation code for the new password for the forgotten password option</p>
      <p><span class="VarName">[%FORGOTPASSWORDPASS%]</span> The new password for the forgotten password option</p>
    </div>';
   return $this->CreateModalWindow('Mail Variables Info', $InfoPopUp);
  }
  public function LoadStoreGoogleAnalyticsHelp() {
    $MdlForm = '<div class="code_snipset">
      (function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,"script","https://www.google-analytics.com/analytics.js","ga");<br>
			  ga("create", "<span class="BoldVar">[---GGLID---]</span>", "auto");<br>
			  ga("set","dynx_itemid","<span class="BoldVar">[---itemid---]</span>");<br>
			  ga("set","dynx_pagetype","<span class="BoldVar">[---itemtype---]</span>");<br>
			  ga("set","dynx_totalvalue","<span class="BoldVar">[---itemprice---]</span>");<br>
			  ga("send", "pageview")<br>
      </div>
      <p>The code snippet above is an example of a Google analytics code. You’ll notice the bold values of in specific parts of the code (note that your Google Analytics might be a bit different from the one above).</p><p>The <span class="BoldVar">[---GGLID---]</span> is the only part, you need to change it with your own Google ID code. The <span class="BoldVar">[---itemid---]</span> is the id of each different product of your store and each time the page will load a different product you need this variable to be changed into different product codes. The <span class="BoldVar">[---itemtype---]</span> is the same for the category of the product and the <span class="BoldVar">[---itemprice---]</span> has to do with the price of the product.</p><p>Just copy those three phrases to your Google Analytics code and you are ready to go.</p>';
    return $this->CreateModalWindow('Google Analytics Variables', $MdlForm);
  }
}