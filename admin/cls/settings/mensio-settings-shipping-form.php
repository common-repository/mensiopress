<?php
class Mensio_Admin_Orders_Shipping extends mensio_core_form {
  private $CurIcon;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->CurIcon = $this->LoadCurIcon();
    $this->ActivePage = 'Product_Shipping';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-shipping',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-orders-shipping.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-shipping',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-orders-shipping.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='name',$InSearch='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    if ($InSorter != '') { $tbl->Set_Sorter($InSorter); }
    $TableData = $this->SearchShippingDataSet($InSearch,$InSorter);
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array(
      'ACTV'=>'Active',
      'DCTV'=>'Deactive'
    ));
    $tbl->Set_EditColumn('name');
    $tbl->Set_EditOptionsSubline(array(
      'Edit'
    ));
    $tbl->Set_Columns(array(
      'uuid:uuid:plain-text',
      'name:Carrier:plain-text',
      'billing:Billing:small',
      'active:Active:input-checkbox'
    ));
    $RtrnTable = $tbl->CreateTable(
      'Shipping',
      $TableData,
      array('uuid','name','billing','active')
    );
    unset($tbl,$Data);    
    return $RtrnTable;
  }
  private function SearchShippingDataSet($InSearch,$InSorter) {
    $RtrnData = array();
    $Shipping = new mensio_shipping();
    $Shipping->Set_SearchString($InSearch);
    $Shipping->Set_Sorter($InSorter);
    $Data = $Shipping->LoadShippingDataSet();
    unset($Shipping);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      $i = 0;
      foreach ($Data as $Row) {
        $RtrnData[$i]['uuid'] = $Row->uuid;
        $RtrnData[$i]['name'] = $Row->name;
        $RtrnData[$i]['billing'] = $Row->billing_type;
        $RtrnData[$i]['active'] = $Row->active;
        ++$i;
      }
    }
    return $RtrnData;
  }
  public function LoadCourierData($CourierID) {
    $RtrnData = array(
      'ERROR'=>'FALSE','Message'=>'','Courier'=>'','Name'=>'','DeliverySpeed'=>'',
      'BillingType'=>'','Active'=>'','ShippingList'=>'');
    $NoteType = '';
    $Shipping = new mensio_shipping ();
    if (!$Shipping->Set_UUID($CourierID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order id not correct<br>';
    } else {
      $DataSet = $Shipping->LoadCourierTypeData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData['Courier'] = $Row->uuid;
          $RtrnData['Name'] = $Row->name;
          $RtrnData['DeliverySpeed'] = $Row->delivery_speed;
          $RtrnData['BillingType'] = $Row->billing_type;
          $RtrnData['Active'] = $Row->active;
          $RtrnData['ShippingList'] = $this->LoadShippingList($Row->uuid);
        }
      }
    }
    unset($Shipping);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadShippingList($CourierID) {
    $RtrnData = '';
    $Country = '';
    $Shipping = new mensio_shipping ();
    if ($Shipping->Set_UUID($CourierID)) {
      $DataSet = $Shipping->LoadCourierShippingData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if ($Shipping->Set_Shipping($Row->uuid)) {
            if ($Country !== $Row->country) {
              $Country = $Row->country;
              if ($RtrnData !== '') {
                $RtrnData .= '
                      </tbody>
                    </table>
                  </div>
                  <div class="DivResizer"></div>
                </div>';
              }
              $RtrnData .= '
                <div id="'.$Row->country.'" class="ListCountrySelector">
                  <input type="hidden" id="CHK_'.$Row->country.'" value="0">
                  <div class="ListElementBtnDiv">
                    <div id="BTN_'.$Row->country.'" class="ESBtnsDivs BTN_RemoveCountry">
                      <div class="ESBtns RemoveEntry" title="Remove Country Shipping Option">
                        <i class="fa fa-times" aria-hidden="true"></i>
                      </div>
                    </div>
                  </div>
                  <div id="NM_'.$Row->country.'" class="ListElementNameDiv">'.$Row->name.'</div>
                  <div class="DivResizer"></div>
                  <div id="LST_'.$Row->country.'" class="ListElementDataDiv">
                    <table class="CountryShippingCosts">
                      <thead>
                        <tr>
                          <th class="BtnCol"></th>
                          <th class="SmallCol">Max Weight (Kgr)</th>
                          <th class="SmallCol">Price (&euro;)</th> 
                        </tr>
                      </thead>
                      <tbody>
                        <tr id="TR_'.$Row->uuid.'">
                          <td class="BtnCol">
                            <div id="'.$Row->uuid.'" class="ESBtnsDivs BTN_RemoveOption">
                              <div class="ESBtns RemoveEntry" title="Remove Country Shipping Option">
                                <i class="fa fa-times" aria-hidden="true"></i>
                              </div>
                            </div>
                          </td>
                          <td class="SmallCol">
                            '.($Row->weight + 0).'
                          </td>
                          <td class="SmallCol">
                            '.($Row->price + 0).'
                          </td> 
                        </tr>';
            } else {
              $RtrnData .= '
                        <tr id="TR_'.$Row->uuid.'">
                          <td class="BtnCol">
                            <div id="'.$Row->uuid.'" class="ESBtnsDivs BTN_RemoveOption">
                              <div class="ESBtns RemoveEntry" title="Remove Country Shipping Option">
                                <i class="fa fa-times" aria-hidden="true"></i>
                              </div>
                            </div>
                          </td>
                          <td class="SmallCol">
                            '.($Row->weight + 0).'
                          </td>
                          <td class="SmallCol">
                            '.($Row->price + 0).'
                          </td> 
                        </tr>';
            }
          }
        }
      }
    }
    unset($Shipping);
    return $RtrnData;
  }
  public function LoadShippingOptionModal($CourierID,$Country) {
    $MdlForm = '
    <div class="ModalCountrySelector">
      <div id="MDLCountrySlctrDiv">
        <label class="label_symbol">Country</label>
        <select id="MDL_FLD_Country" class="form-control">
          '.$this->LoadTableOptions('Country',$Country).'
        </select>
      </div>
      <div id="MDLCountryShippingDiv">
        <div class="MDLInputDivs">
          <label class="label_symbol">Max Weight</label>
          <input type="text" id="MDL_FLD_Weight" class="form-control" value="">
        </div>
        <div class="MDLInputDivs">
          <label class="label_symbol">Price</label>
          <input type="text" id="MDL_FLD_Price" class="form-control" value="">
        </div>
        <button id="MDL_AddNewShippingOption" class="button BtnBlue" title="Add New Country Shipping Option">
          <i class="fa fa-plus" aria-hidden="true"></i>
        </button>
      </div>
    </div>';
    return $this->CreateModalWindow('Country Selection', $MdlForm);
  }
  public function LoadTableOptions($DbTbl,$Selected='') {
    $Options = '';
    switch ($DbTbl) {
      case 'Courier':
        $Couriers = new mensio_shipping();
        $Data = $Couriers->LoadShippingDataSet();
        unset($Couriers);
        $OptVal = 'name';
        break;
      case 'Country':
        $Countries = new mensio_countries();
        $Data = $Countries->GetCountriesDataSet();
        unset($Countries);
        $OptVal = 'country';
        break;
    }
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $Options .= '<option value="'.$Row->uuid.'">'.$Row->$OptVal.'</option>';
      }
    }
    if ($Selected !== '') {
      $Options = str_replace('value="'.$Selected.'"', 'value="'.$Selected.'" selected', $Options);
    }
    return $Options;
  }
  public function UpdateCourierActiveStatus($CourierID,$Active) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Shipping = new mensio_shipping();
    if (!$Shipping->Set_UUID($CourierID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Courier id not correct<br>';
    } else {
      if ($Shipping->Set_Active($Active)) {
        if (!$Shipping->UpdateCourierData()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Courier Data could not be updated<br>';
        }
      }
    }
    unset($Shipping);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Courier Updated Successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  public function UpdateCourierData($CourierID,$Name,$DlSpeed,$BlngType,$Active) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Courier'=>$CourierID);
    $NoteType = '';
    $NewEntry = false;
    $Shipping = new mensio_shipping();
    if ($CourierID === 'NewEntry') {
      $NewEntry = true;
      $CourierID = $Shipping->GetNewCode();
    }
    if (!$Shipping->Set_UUID($CourierID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Courier id not correct<br>';
    }
    if (!$Shipping->Set_Name($Name)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Courier name not acceptable<br>';
    }
    if (!$Shipping->Set_DeliverySpeed($DlSpeed)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Delivery speed value not acceptable<br>';
    }
    if (!$Shipping->Set_BillingType($BlngType)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Billing type value not acceptable<br>';
    }
    if (!$Shipping->Set_Active($Active)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Active value not acceptable<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if ($NewEntry) {
        if (!$Shipping->InsertNewCourierData()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Courier Data could not be updated<br>';
        }
      } else {
        if (!$Shipping->UpdateCourierData()) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Courier Data could not be updated<br>';
        }
      }
    }
    unset($Shipping);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Courier Saved Successfully<br>';
      $RtrnData['Courier'] = $CourierID;
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  public function AddCourierShippingOption($CourierID,$Country,$Weight,$Price) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','ShippingList'=>'');
    $NoteType = '';
    $Shipping = new mensio_shipping();
    if (!$Shipping->Set_Courier($CourierID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Courier id not correct<br>';
    }
    if (!$Shipping->Set_Country($Country)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Country id not correct<br>';
    }
    if (!$Shipping->Set_Weight($Weight)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Weight value was not correct<br>';
    }
    if (!$Shipping->Set_Price($Price)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Price value was not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $FndEntry = $Shipping->FoundShippingEntry();
      switch ($FndEntry) {
        case 'Empty':
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Function parameters are empty<br>';
          break;
        case 'Enabled':
          $NoteType = 'Info';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Shipping option allready exists<br>';
          break;
        case 'Disabled':
          if (!$Shipping->ReEnabledShippingEntry()) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Shipping Entry cound not be re enabled<br>';
          }
          break;
        case 'NotFound':
          if (!$Shipping->InsertNewShippingEntry()) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Shipping Entry cound not be saved<br>';
          }
          break;
      }
    }
    unset($Shipping);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $RtrnData['ShippingList'] = $this->LoadShippingList($CourierID);
    } else {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function DisableCourierShippingCountry($CourierID,$Country) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','ShippingList'=>'');
    $NoteType = '';
    $Shipping = new mensio_shipping();
    if (!$Shipping->Set_Courier($CourierID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Courier id not correct<br>';
    }
    if (!$Shipping->Set_Country($Country)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Country id not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Shipping->DisableCourierCountry()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Country could not be disabled<br>';
      }
    }
    unset($Shipping);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $RtrnData['ShippingList'] = $this->LoadShippingList($CourierID);
    } else {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function DisableCourierShippingOption($Option) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','ShippingList'=>'');
    $NoteType = '';
    $Shipping = new mensio_shipping();
    if (!$Shipping->Set_Shipping($Option)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Option id not correct<br>';
    } else {
      if (!$Shipping->ReEnabledShippingEntry(false)) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Country option could not be disabled<br>';
      }
    }
    unset($Shipping);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
}