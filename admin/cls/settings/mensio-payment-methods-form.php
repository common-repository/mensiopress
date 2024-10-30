<?php
class Mensio_Admin_Payment_Methods_Form extends mensio_core_form {
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->ActivePage = 'Payment_Methods';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-dashboard',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-admin-paymentmethods.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-dashboard',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-admin-paymentmethods.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  public function CreatePaymentTab() {
    $RtrnData = '';
    $PayMethods = new mensio_payment_methods();
    $DataSet = $PayMethods->LoadStorePayments();
    unset($PayMethods);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      $li = '';
      $div = '';
      foreach ($DataSet as $Row) {
        $id = str_replace(' ', '', $Row->name);
        $li .= '<li><a href="#'.$id.'Div" data-selector="'.$id.'">'.$Row->name.'</a></li>';
        switch ($id) {
          case 'OnDelivery':
            $div .= '<div id="'.$id.'Div" class="ProductTab">'.$this->GetDeliveryForm().'</div>';
            break;
          case 'BankDeposit':
            $div .= '<div id="'.$id.'Div" class="ProductTab">'.$this->GetBankForm().'</div>';
            break;
          default:
            $div .= '<div id="'.$id.'Div" class="ProductTab">'.$this->GetGateWayForm($Row->name).'</div>';
            break;
        }
      }
      $RtrnData = '<ul>'.$li.'</ul>'.$div;
    }
    return $RtrnData;
  }
  public function GetDeliveryForm() {
    $UUID = '';
    $Active = '';
    $Checked = '';
    $LangID = '';
    $Desc = '';
    $Inst = '';
    $Payment = '';
    $PayMethods = new mensio_payment_methods();
    $DataSet = $PayMethods->LoadPayOnDeliveryData();
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        if ($Row->active) { $Checked = 'checked'; }
        $UUID = $Row->uuid;
        $Active = $Row->active;
        $LangID = $Row->language;
        $Desc = $Row->description;
        $Inst = $Row->instructions;
        $Payment = $Row->payment;
      }
    }
    unset($PayMethods,$DataSet);
    $RtrnForm = '<table id="TBL_DeliveryPay" class="OptionsTable">
          <tr>
            <td class="LblCol">Active</td>
            <td class="FldCol">
              <input type="hidden" id="FLD_PoD" value="'.$UUID.'">
              <input type="checkbox" id="FLD_DlvrActive" value="'.$Active.'" class=" ActCheckbox" '.$Checked.'>
            </td>
          </tr>
          <tr>
            <td class="LblCol">Description<br><span class="ColInfoSpan">(Main Language)</span></td>
            <td class="FldCol">
              <input type="hidden" id="FLD_lang_Delivery" value="'.$LangID.'">
              <input type="text" id="FLD_DlvrDesc" class="form-control" value="'.$Desc.'">
            </td>
          </tr>
          <tr>
            <td class="LblCol">Instructions<br><span class="ColInfoSpan">(Main Language)</span></td>
            <td class="FldCol">
              <textarea id="FLD_DlvrNotes" class="form-control">'.$Inst.'</textarea>
              <button class="button BTN_Translations" title="Add Translations">
                <i class="fa fa-comment" aria-hidden="true"></i>
              </button>
            </td>
          </tr>
          <tr>
            <td class="LblCol">Shipping Methods</td>
            <td class="FldCol">
              <div id="ShipOptListDiv" class="">
                '.$this->GetDeliveryShippingOptions($Payment).'
              </div>
            </td>
          </tr>
        </table>';    
    return $RtrnForm;
  }
  private function GetDeliveryShippingOptions($PaymentID) {
    $DataSet = array();
    $RtrnData = '';
    $ShipOptions = new mensio_shipping();
    $DataSet = $ShipOptions->LoadShippingDataSet();
    unset($ShipOptions);
    $PayMethods = new mensio_payment_methods();
    if ($PayMethods->Set_UUID($PaymentID)) {
      $ActiveShippings = $PayMethods->LoadPayOnDeliveryShippingOptions();
    }
    unset($PayMethods);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $Checked = '';
        $value = '0';
        if (in_array($Row->uuid,$ActiveShippings)) {
          $Checked = 'checked';
          $value = '1';
        }
        $RtrnData .= '<div class="ShipOptnElmnt">
                   <label class="label_symbol">'.$Row->name.'</label>
                   <input type="checkbox" id="'.$Row->uuid.'" class="FLD_ShipOptn" value="'.$value.'" '.$Checked.'>
                 </div>';
      }
    }
    return $RtrnData;
  }
  public function UpdatePayOnDeliveryData($PoD,$Active,$LangID,$Description,$Notes,$ShipOpt) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $PayMethods = new mensio_payment_methods();
    if (!$PayMethods->Set_UUID($PoD)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Pay On Delivery Main Code !!!!!<br>';
    }
    $PayMethods->Set_Active($Active);
    if (!$PayMethods->Set_Language($LangID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Main Language Code !!!!<br>';
    }
    if (!$PayMethods->Set_Description($Description)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Description text not acceptable<br>';
    }
    if (!$PayMethods->Set_Instructions($Notes)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Instructions text not acceptable<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$PayMethods->UpdatePaymentActivation()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Unable to Update Pay On Delivery<br>';
      } else {
        if (!$PayMethods->UpdatePaymentTranslation()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Unable to Update Pay On Delivery translation<br>';
        }
        $ShipOpt = explode(';', $ShipOpt);
        foreach ($ShipOpt as $Row) {
          $Option = explode(':', $Row);
          if ($PayMethods->Set_ShippingOption($Option[0])) {
            if (($Option[1] === '1') || ($Option[1] === 1)) {
              if (!$PayMethods->ShippingOptionFound()) {
                if (!$PayMethods->AddPayOnDeliveryShippingOption()) {
                  $NoteType = 'Alert';
                  $RtrnData['ERROR'] = 'TRUE';
                  $RtrnData['Message'] .= 'Unable to Add Pay On Delivery Shipping Option<br>';
                }
              }
            } else {
              if (!$PayMethods->RemovePayOnDeliveryShippingOption()) {
                $NoteType = 'Alert';
                $RtrnData['ERROR'] = 'TRUE';
                $RtrnData['Message'] .= 'Unable to Remove Pay On Delivery Shipping Option<br>';
              }
            }
          } else {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Problem with Shipping Option Code: '.$Option[0].'<br>';
          }
        }
      }
    }
    unset($PayMethods);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Pay On Delivery Data Saved Successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function LoadPaymentTranslationsModal($PoP) {
    $LangBtns = '';
    $Languages = new mensio_languages();
    $DataSet = $Languages->LoadLanguagesData();
    $MainLang = $Languages->ReturnMainLanguages('adminlang');
    unset($Languages);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        if ($Row->active) {
          $Slctd = '';
          if ($Row->uuid === $MainLang) { $Slctd = 'LangSelected'; }
          $LangBtns .= '
            <div id="'.$Row->uuid.'" class="MDL_LangSelector '.$Slctd.'" title="'.$Row->name.'">
              <img src="'.MENSIO_PATH.'/admin/icons/flags/'.$Row->icon.'.png" alt="language_flag">
            </div>';
        }
      }
    }
    $PayMethods = new mensio_payment_methods();
    if ($PayMethods->Set_UUID($PoP)) {
      $PayName = $PayMethods->GetPaymentMethodType();
      if ($PayMethods->Set_Language($MainLang)) {
        $Translation = $PayMethods->GetPaymentMethodTranslation();
      }
    }
    unset($PayMethods);
    $MdlForm = '
    <div class="ModalPaymentMethod">
      <div class="TransSlctrsDiv">
        <div class="TransSlctrsWrap">
          '.$LangBtns.'
        </div>
      </div>
      <div class="TransFldsDiv">
        <div id="MyMdlAlertBar" class="MdlAlertBar"></div>
        <input type="hidden" id="MDL_Changes" value="0">
        <input type="hidden" id="MDL_FLD_lang" value="'.$MainLang.'">
        <label class="label_symbol">Description</label>
        <input type="hidden" id="MDL_OLD_DlvrDesc" value="'.$Translation['Description'].'">
        <input type="text" id="MDL_FLD_DlvrDesc" class="form-control ModalFld" value="'.$Translation['Description'].'">
        <label class="label_symbol">Instructions</label>
        <input type="hidden" id="MDL_OLD_DlvrNotes" value="'.$Translation['Instructions'].'">
        <textarea id="MDL_FLD_DlvrNotes" class="form-control ModalFld">'.$Translation['Instructions'].'</textarea>
        <div class="button_row">
          <button id="BTN_ModalRestore" class="button" title="Restore field values">
            <i class="fa fa-undo" aria-hidden="true"></i>
          </button>
          <button id="BTN_ModalTransSave" class="button BtnGreen" title="Update Translation">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>
        </div>
      </div>
    </div>';
    return $this->CreateModalWindow($PayName.' Translations', $MdlForm);
  }
  public function LoadPaymentTranslations($PayID,$LangID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Language'=>'','Description'=>'','Instructions'=>'');
    $NoteType = '';
    $PayMethods = new mensio_payment_methods();
    if (!$PayMethods->Set_UUID($PayID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Payment Code !!!!!<br>';
    }
    if (!$PayMethods->Set_Language($LangID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Main Language Code !!!!<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Translation = $PayMethods->GetPaymentMethodTranslation();
      $RtrnData['Language'] = $LangID;
      $RtrnData['Description'] = $Translation['Description'];
      $RtrnData['Instructions'] = $Translation['Instructions'];
    }
    unset($PayMethods);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function UpdatePaymentTranslations($PayID,$LangID,$Description,$Notes) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $PayMethods = new mensio_payment_methods();
    if (!$PayMethods->Set_UUID($PayID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Pay On Delivery Main Code !!!!!<br>';
    }
    if (!$PayMethods->Set_Language($LangID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Main Language Code !!!!<br>';
    }
    if (!$PayMethods->Set_Description($Description)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Description text not acceptable<br>';
    }
    if (!$PayMethods->Set_Instructions($Notes)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Instructions text not acceptable<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$PayMethods->UpdatePaymentTranslation()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Unable to Update Pay On Delivery translation<br>';
      }
    }
    unset($PayMethods);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Payment Translation Saved Successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;    
  }
  public function GetBankForm() {
    $RtrnForm = '';
    $UUID = '';
    $Active = '';
    $Checked = '';
    $LangID = '';
    $Desc = '';
    $Inst = '';
    $PayMethods = new mensio_payment_methods();
    $DataSet = $PayMethods->LoadBankDepositData();
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        if ($Row->active) { $Checked = 'checked'; }
        $UUID = $Row->uuid;
        $Active = $Row->active;
        $LangID = $Row->language;
        $Desc = $Row->description;
        $Inst = $Row->instructions;
      }
    }
    unset($PayMethods,$DataSet);
    $RtrnForm = '
            <table id="TBL_BankPay" class="OptionsTable">
              <tr>
                <td class="LblCol">Active</td>
                <td class="FldCol">
                  <input type="hidden" id="FLD_BankDep" value="'.$UUID.'">
                  <input type="checkbox" id="FLD_BankActive" value="'.$Active.'" class=" ActCheckbox" '.$Checked.'>
                </td>
              </tr>
              <tr>
                <td class="LblCol">Description</td>
                <td class="FldCol">
                  <input type="hidden" id="FLD_lang_Bank" value="'.$LangID.'">
                  <input type="text" id="FLD_BankDesc" class="form-control" value="'.$Desc.'">
                </td>
              </tr>
              <tr>
                <td class="LblCol">Instructions</td>
                <td class="FldCol">
                  <textarea id="FLD_BankNotes" class="form-control">'.$Inst.'</textarea>
                  <button class="button BTN_Translations" title="Add Translations">
                    <i class="fa fa-comment" aria-hidden="true"></i>
                  </button>                
                </td>
              </tr>
              <tr>
                <td class="LblCol">Bank Account Info</td>
                <td id="BankAccountTable" class="FldCol">
                  '.$this->LoadBankAccountTable($UUID).'
                </td>
              </tr>
            </table>';
    return $RtrnForm;
  }
  public function LoadBankAccountTable($PaymentID) {
    $line = '';
    $RtrnData = '<table id="TBL_BankAccounts" class="AccountTable">
                    <tr>
                      <th class="CtrlCol">
                        <div id="BTN_AddBankAccount" class="ESBtns" title="Add Bank Account">
                          <i class="fa fa-plus" aria-hidden="true"></i>
                        </div>
                      </th>
                      <th class="IconCol">Icon</th>
                      <th>Bank</th>
                      <th>Name</th>
                      <th>Number</th>
                      <th>Routing</th>
                      <th>IBAN</th>
                      <th>BIC / Swift</th>
                    </tr>';
    $PayMethods = new mensio_payment_methods();
    if ($PayMethods->Set_UUID($PaymentID)) {
      $DataSet = $PayMethods->LoadBankAccountList();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $line .= '<tr class="OddTblLine">
                      <td class="CtrlCol">
                        <div id="DelAcc_'.$Row->uuid.'" class="ESBtns DelAccBtn" title="Delete Account">
                          <i class="fa fa-times" aria-hidden="true"></i>
                        </div>
                        <div id="EditAcc_'.$Row->uuid.'" class="ESBtns EditAccBtn" title="Edit Account">
                          <i class="fa fa-pencil" aria-hidden="true"></i>
                        </div>
                      </td>
                      <td class="IconCol"><img src="'.get_site_url().'/'.$Row->account_icon.'" alt="account_icon"></td>
                      <td>'.$Row->account_bank.'</td>
                      <td>'.$Row->account_name.'</td>
                      <td>'.$Row->account_number.'</td>
                      <td>'.$Row->account_routing.'</td>
                      <td>'.$Row->account_iban.'</td>
                      <td>'.$Row->account_swift.'</td>
                    </tr>';      }
      }
    }
    unset($PayMethods);
    $RtrnData .= $line.'</table>';
    return $RtrnData;
  }
  public function UpdateBankDepositData($PayID,$Active,$LangID,$Description,$Notes) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $PayMethods = new mensio_payment_methods();
    if (!$PayMethods->Set_UUID($PayID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Bank Deposit Main Code !!!!!<br>';
    }
    $PayMethods->Set_Active($Active);
    if (!$PayMethods->Set_Language($LangID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Main Language Code !!!!<br>';
    }
    if (!$PayMethods->Set_Description($Description)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Description text not acceptable<br>';
    }
    if (!$PayMethods->Set_Instructions($Notes)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Instructions text not acceptable<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$PayMethods->UpdatePaymentActivation()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Unable to Update Bank Deposit<br>';
      } else {
        if (!$PayMethods->UpdatePaymentTranslation()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Unable to Update Bank Deposit translation<br>';
        }
      }
    }
    unset($PayMethods);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Bank Deposit Data Saved Successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function LoadBankAccountModal($BnkAccntID) {
    $Account = 'NewEntry';
    $Icon = plugin_dir_url( __FILE__ ).'../../icons/default/empty.png';
    $Bank = '';
    $Name = '';
    $Number = '';
    $Routing = '';
    $IBAN = '';
    $Swift = '';
    if ($BnkAccntID !== 'NewEntry') {
      $PayMethods = new mensio_payment_methods();
      if ($PayMethods->Set_BankAccount($BnkAccntID)) {
        $DataSet = $PayMethods->LoadBankAccountData();
        if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
          foreach ($DataSet as $Row) {
            $Account = $Row->uuid;
            $Bank = $Row->account_bank;
            $Name = $Row->account_name;
            $Number = $Row->account_number;
            $Routing = $Row->account_routing;
            $IBAN = $Row->account_iban;
            $Swift = $Row->account_swift;
            $Icon = get_site_url().'/'.$Row->account_icon;
          }
        }
      }
      unset($PayMethods);
    }
    $MdlForm = '<div id="BnkAcntMdl" class="ModalPaymentMethod">
      <div id="MyMdlAlertBar" class="MdlAlertBar"></div>
      <input type="hidden" id="MDL_Changes" value="0">
      <input type="hidden" id="MDL_FLD_Account" value="'.$Account.'">
      <label class="label_symbol">Bank Name</label>
      <input type="hidden" id="MDL_OLD_Bank" value="'.$Bank.'">
      <input type="text" id="MDL_FLD_Bank" class="form-control ModalFld" value="'.$Bank.'">
      <label class="label_symbol">Account Name</label>
      <input type="hidden" id="MDL_OLD_Name" value="'.$Name.'">
      <input type="text" id="MDL_FLD_Name" class="form-control ModalFld" value="'.$Name.'">
      <label class="label_symbol">Number</label>
      <input type="hidden" id="MDL_OLD_Number" value="'.$Number.'">
      <input type="text" id="MDL_FLD_Number" class="form-control ModalFld" value="'.$Number.'">
      <label class="label_symbol">Routing</label>
      <input type="hidden" id="MDL_OLD_Routing" value="'.$Routing.'">
      <input type="text" id="MDL_FLD_Routing" class="form-control ModalFld" value="'.$Routing.'">
      <label class="label_symbol">IBAN</label>
      <input type="hidden" id="MDL_OLD_IBAN" value="'.$IBAN.'">
      <input type="text" id="MDL_FLD_IBAN" class="form-control ModalFld" value="'.$IBAN.'">
      <label class="label_symbol">BIC/Swift</label>
      <input type="hidden" id="MDL_OLD_Swift" value="'.$Swift.'">
      <input type="text" id="MDL_FLD_Swift" class="form-control ModalFld" value="'.$Swift.'">
      <div class="Mns_ImgDiv">
        <label class="label_symbol">Bank Icon</label>
        <div class="Mns_Img_Container">
          <img id="DispImg" class="selectIm" src="'.$Icon.'" alt="bank_icon"> 
        </div>
        <div class="Mns_ImgBtnDiv">
          <button id="Mns_Bank_Icon" class="button Mns_Img_Btn Mns_OpenMediaModal" title="Open Image Selector">
            <i class="fa fa-picture-o" aria-hidden="true"></i>
          </button>
          <button id="Mns_ClearImg" class="button Mns_Img_Btn" title="Clear Image">
            <i class="fa fa-trash" aria-hidden="true"></i>
          </button>
          <input type="hidden" id="MDL_OLD_Icon" value="'.$Icon.'">
          <input type="hidden" id="MDL_FLD_Icon" class="ModalFld" value="'.$Icon.'"/>
          <input type="hidden" id="DefImg" value="'.plugin_dir_url( __FILE__ ).'../../icons/default/empty.png"/>
        </div>
      </div>
      <div class="button_row">
        <button id="BTN_ModalRestore" class="button" title="Restore field values">
          <i class="fa fa-undo" aria-hidden="true"></i>
        </button>
        <button id="BTN_ModalAccountSave" class="button BtnGreen" title="Save Bank Account">
          <i class="fa fa-floppy-o" aria-hidden="true"></i>
        </button>
      </div>
    </div>';
    return $this->CreateModalWindow('Bank Account', $MdlForm);
  }
  public function UpdateBankDepositAccountData($PayID,$Account,$Icon,$Bank,$Name,$Number,$Routing,$IBAN,$Swift) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','AccountsTable'=>'');
    $NoteType = '';
    $NewEntry = false;
    $PayMethods = new mensio_payment_methods();
    if (!$PayMethods->Set_UUID($PayID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Bank Deposit Main Code !!!!!<br>';
    }
    if ($Account === 'NewEntry') {
      $Account = $PayMethods->GetNewCode();
      $NewEntry = true;
    }
    if (!$PayMethods->Set_BankAccount($Account)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Bank Account Code !!!!!<br>';
    }
    if (!$PayMethods->Set_Icon($Icon)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Icon path not acceptable<br>';
    }
    if (!$PayMethods->Set_Bank($Bank)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Bank name not acceptable<br>';
    }
    if (!$PayMethods->Set_Name($Name)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Account name not acceptable<br>';
    }
    if (!$PayMethods->Set_Number($Number)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Account Number not acceptable<br>';
    }
    if (!$PayMethods->Set_Routing($Routing)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Account Routing not acceptable<br>';
    }
    if (!$PayMethods->Set_IBAN($IBAN)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Account IBAN not acceptable<br>';
    }    
    if (!$PayMethods->Set_Swift($Swift)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Account Swift not acceptable<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if ($NewEntry) {
        if (!$PayMethods->AddNewBankAccountData()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Account data could not be saved<br>';
        }
      } else {
        if (!$PayMethods->UpdateBankAccountData()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Account could not be updated<br>';
        }
      }
    }
    unset($PayMethods);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Bank Deposit Data Saved Successfully<br>';
      $RtrnData['AccountsTable'] = $this->LoadBankAccountTable($PayID);
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function RemoveBankDepositAccountData($PayID,$Account) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','AccountsTable'=>'');
    $NoteType = '';
    $PayMethods = new mensio_payment_methods();
    if (!$PayMethods->Set_BankAccount($Account)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Bank Account Code !!!!!<br>';
    } else {
      if (!$PayMethods->DeleteBankAccountData()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Account data could not be removed<br>';
      }
    }
    unset($PayMethods);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Bank Deposit Data Saved Successfully<br>';
      $RtrnData['AccountsTable'] = $this->LoadBankAccountTable($PayID);
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function GetGateWayForm($Gateway) {
    $RtrnForm = '';
    $UUID = '';
    $Active = '';
    $Checked = '';
    $LangID = '';
    $Desc = '';
    $Inst = '';
    $PayMethods = new mensio_payment_methods();
    $DataSet = $PayMethods->LoadGatewayData($Gateway);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        if ($Row->active) { $Checked = 'checked'; }
        $UUID = $Row->uuid;
        $Active = $Row->active;
        $LangID = $Row->language;
        $Desc = $Row->description;
        $Inst = $Row->instructions;
      }
    }
    unset($PayMethods,$DataSet);
    $RtrnForm = '
            <table id="TBL_Bank'.$Gateway.'" class="OptionsTable">
              <tr>
                <td class="LblCol">Active</td>
                <td class="FldCol">
                  <input type="hidden" id="FLD_'.$Gateway.'Dep" value="'.$UUID.'">
                  <input type="checkbox" id="FLD_'.$Gateway.'Active" value="'.$Active.'" class=" ActCheckbox" '.$Checked.'>
                </td>
              </tr>
              <tr>
                <td class="LblCol">Description</td>
                <td class="FldCol">
                  <input type="hidden" id="FLD_lang_'.$Gateway.'" value="'.$LangID.'">
                  <input type="text" id="FLD_'.$Gateway.'Desc" class="form-control" value="'.$Desc.'">
                </td>
              </tr>
              <tr>
                <td class="LblCol">Instructions</td>
                <td class="FldCol">
                  <textarea id="FLD_'.$Gateway.'Notes" class="form-control">'.$Inst.'</textarea>
                  <button class="button BTN_Translations" title="Add Translations">
                    <i class="fa fa-comment" aria-hidden="true"></i>
                  </button>                
                </td>
              </tr>
              '.$this->CreateGatewayParameters($UUID,$Gateway).'
            </table>';
    return $RtrnForm;
  }
  private function CreateGatewayParameters($UUID,$Type) {
    $ParamRows = '';
    $PayMethods = new mensio_payment_methods();
    if ($PayMethods->Set_UUID($UUID)) {
      $Parameters = $PayMethods->LoadGatewayParameters();
      if ((is_array($Parameters)) && (!empty($Parameters[0]))) {
        $IconRow = '';
        $ActSndboxRow = '';
        foreach ($Parameters as $Row) {
          $Name = str_replace(' ', '_', $Row->parameter);
          switch ($Name) {
            case '00_Active_Sandbox_Mode':
            case '05_Reject_3ds_U':
              $ActSndboxRow = '
              <tr>
                <td class="LblCol">'.$Row->parameter.'</td>
                <td class="FldCol">
                  <select id="FLD_'.$Type.'_'.$Name.'" class="form-control '.$Type.'Flds">
                    <option value="Y">Yes</option>
                    <option value="N">No</option>
                  </select>
                </td>
              </tr>';
              $ParamRows .= str_replace('"'.$Row->value.'"', '"'.$Row->value.'" selected', $ActSndboxRow);
              break;
            case 'Icon':
              $IconRow = '
              <tr>
                <td class="LblCol">'.$Row->parameter.'</td>
                <td class="FldCol">
                  <div class="Mns_ImgDiv">
                    <div class="Mns_Img_Container">
                      <img id="'.$Type.'DsplIcon" class="selectIm" src="'.get_site_url().'/'.$Row->value.'" alt="gateway_icon"> 
                    </div>
                    <div class="Mns_ImgBtnDiv">
                      <button class="button Mns_Img_Btn Mns_OpenMediaModal" title="Open Image Selector">
                        <i class="fa fa-picture-o" aria-hidden="true"></i>
                      </button>
                      <button class="button Mns_Img_Btn" title="Clear Image">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                      </button>
                      <input type="hidden" id="FLD_'.$Type.'Icon" class="ModalFld '.$Type.'Flds" value="'.get_site_url().'/'.$Row->value.'"/>
                      <input type="hidden" id="DefImg_'.$Type.'" value="'.plugin_dir_url( __FILE__ ).'../../icons/default/empty.png"/>
                    </div>
                  </div>
                </td>
              </tr>';
              break;
            default:
              $ParamRows .= '
              <tr>
                <td class="LblCol">'.$Row->parameter.'</td>
                <td class="FldCol">
                  <input type="text" id="FLD_'.$Type.'_'.$Name.'" class="form-control '.$Type.'Flds" value="'.$Row->value.'">
                </td>
              </tr>';
              break;
          }
        }
        $ParamRows = $ParamRows.$IconRow; 
      }
    }
    unset($PayMethods,$DataSet);
    return $ParamRows;
  }
  public function UpdateGateWayData($Gateway,$PayID,$Active,$LangID,$Description,$Notes,$Params) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $PayMethods = new mensio_payment_methods();
    if (!$PayMethods->Set_UUID($PayID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Pay Gateway Main Code !!!!!<br>';
    }
    $PayMethods->Set_Active($Active);
    if (!$PayMethods->Set_Language($LangID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with Main Language Code !!!!<br>';
    }
    if (!$PayMethods->Set_Description($Description)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Description text not acceptable<br>';
    }
    if (!$PayMethods->Set_Instructions($Notes)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Instructions text not acceptable<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$PayMethods->UpdatePaymentActivation()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Unable to Update Pay Gateway Active option<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$PayMethods->UpdatePaymentTranslation()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Unable to Update Pay Gateway translation<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Params = stripslashes($Params);
      $Params = json_decode($Params, true);
      if (is_array($Params)) {
        foreach ($Params as $Row) {
          $Row['Param'] = str_replace('_'.$Gateway.'_', '', $Row['Param']);
          if (!$PayMethods->Set_Parameter(str_replace('_', ' ', $Row['Param']))) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Parameter given '.$Row['Param'].' not acceptable<br>';
          }
          if (!$PayMethods->Set_Value($Row['Value'])) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Parameter value given '.$Row['Value'].' not acceptable<br>';
          }
          if ($RtrnData['ERROR'] === 'FALSE') {
            if (!$PayMethods->UpdateGatewayParameter()) {
              $NoteType = 'Alert';
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Parameter '.$Row['Param'].' could not be updated<br>';
            }
          }
        }
      }
    }
    unset($PayMethods);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Pay Gateway Data Saved Successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;    
  }
  public function LoadDefaultLandingPagesModal() {
    $PayMethods = new mensio_payment_methods();
    $DataSet = $PayMethods->LoadSalesDefaultLandingPages();
    unset($PayMethods);
    $MdlForm = '
    <div class="LandingFormWrapper">
      <label class="label_symbol">Success Transaction Landing Page</label>
      <input type="hidden" id="MDL_OLD_Success" value="'.$DataSet['Success'].'">
      <input type="text" id="MDL_FLD_Success" class="form-control ModalFld" value="'.$DataSet['Success'].'">
      <label class="label_symbol">Failed Transaction Landing Page</label>
      <input type="hidden" id="MDL_OLD_Failed" value="'.$DataSet['Failed'].'">
      <input type="text" id="MDL_FLD_Failed" class="form-control ModalFld" value="'.$DataSet['Failed'].'">
      <div class="button_row">
        <button id="BTN_UpdateLanding" class="button BtnGreen" title="Save">
          <i class="fa fa-floppy-o" aria-hidden="true"></i>
        </button>
      </div>
    </div>';
    return $this->CreateModalWindow('Default Landing Pages', $MdlForm);
  }
  public function UpdateLandingPagesData($OldSuccess,$Success,$OldFailed,$Failed) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $PayMethods = new mensio_payment_methods();
    if (!$PayMethods->Set_Value($OldSuccess)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Old Success Page not acceptable<br>';
    }
    if (!$PayMethods->Set_Value($Success)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Success Page not acceptable<br>';
    } else {
      if (!$PayMethods->UpdateSalesDefaultLandingPages('success',$OldSuccess)) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Success Page could not be updated<br>';
      }
    }
    if (!$PayMethods->Set_Value($OldFailed)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Old Failed Page not acceptable<br>';
    }
    if (!$PayMethods->Set_Value($Failed)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Failed Page not acceptable<br>';
    } else {
      if (!$PayMethods->UpdateSalesDefaultLandingPages('failed',$OldFailed)) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Failed Page could not be updated<br>';
      }
    }
    unset($PayMethods);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Page address updates successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
}