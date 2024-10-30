<?php
class Mensio_Admin_Orders_Form extends mensio_core_form {
  private $CurIcon;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->CurIcon = $this->LoadCurIcon();
    $this->Set_MainTemplate();
    $this->ActivePage = 'Orders';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-orders',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-orders.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-orders',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-orders.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function LoadOrdersListDataSet($InSearch,$InSorter,$JSONData) {
    $Error = false;
    $DataSet = array();
    $Orders = new mensio_Orders();
    if ($JSONData !== '') { $Orders->Set_ExtraFilters($JSONData); }
    if ($InSearch !== '') {
      if (!$Orders->Set_SearchString($InSearch)) { $Error = true; }
    }
    if ($InSorter !== '') {
      if (!$Orders->Set_Sorter($InSorter)) { $Error = true; }
    }
    if (!$Error) {
      $DataSet = $Orders->LoadOrdersListDataSet();
    }
    unset($Orders);
    return $DataSet;
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='created DESC',$InSearch='',$JSONData='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    $tbl->Set_Sorter($InSorter);
    $DataSet = $this->LoadOrdersListDataSet($InSearch,$InSorter,$JSONData);
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array());
    $ExtraActions = $this->LoadExtraActions($JSONData);
    $tbl->Set_ExtraActions($ExtraActions);
    $tbl->Set_EditColumn('serial');
    $tbl->Set_EditOptionsSubline(array(
      'Edit','Invoice'
    ));
    $tbl->Set_Columns(array(
      'uuid:uuid:plain-text',
      'serial:Order:plain-text',
      'created:Created:small',
      'orderip:Order IP:small',
      'complete:Complete:hidden',
      'status:Status:small'
    ));
    $RtrnTable = $tbl->CreateTable(
      'Orders',
      $DataSet,
      array('uuid','serial','created','orderip','complete','status')
    );
    unset($tbl);    
    return $RtrnTable;
  }
  private function LoadExtraActions($JSONData='') {
    $ExtraActions = array();
    $SlctStatus = '';
    $SlctCustomers = '';
    $SlctPayment = '';
    $SlctDate = '';
    $JSONData = stripslashes($JSONData);
    $Data = json_decode($JSONData,true);
    if (json_last_error() === JSON_ERROR_NONE) {
      if (is_array($Data)) {
        foreach ($Data as $Row) {
          switch ($Row['Field']) {
            case 'Status':
              $SlctStatus = $Row['Value'];
              break;
            case 'Customers':
              $SlctCustomers = $Row['Value'];
              break;
            case 'Payment':
              $SlctPayment = $Row['Value'];
              break;
            case 'Date':
              $SlctDate = $Row['Value'];
              break;
          }
        }
      }
    }
    $Option['name'] = 'Status';
    $Option['options'] = $this->LoadOrdersStatusOptions($SlctStatus,$SlctCustomers,$SlctPayment,$SlctDate);
    $ExtraActions[0] = $Option;
    $Option['name'] = 'Customers';
    $Option['options'] = $this->LoadOrdersCustomersOptions($SlctStatus,$SlctCustomers,$SlctPayment,$SlctDate);
    $ExtraActions[1] = $Option;
    $Option['name'] = 'Payment';
    $Option['options'] = $this->LoadOrdersPaymentOptions($SlctStatus,$SlctCustomers,$SlctPayment,$SlctDate);
    $ExtraActions[2] = $Option;
    $Option['name'] = 'Date';
    $Option['options'] = $this->LoadOrdersDateOptions($SlctStatus,$SlctCustomers,$SlctPayment,$SlctDate);
    $ExtraActions[3] = $Option;
    return $ExtraActions;
  }
  private function LoadOrdersStatusOptions($SlctStatus,$SlctCustomers,$SlctPayment,$SlctDate) {
    $Error = false;
    $Options = '<option value="0">Select Status</option>';
    $Orders = new mensio_orders();
    if (($SlctCustomers !== '') && ($SlctCustomers !== '0')) {
      if (!$Orders->Set_Customer($SlctCustomers)) { $Error = true; }
    }
    if (($SlctPayment !== '') && ($SlctPayment !== '0')) {
      if (!$Orders->Set_PaymentType($SlctPayment)) { $Error = true; }
    }
    if (($SlctDate !== '') && ($SlctDate !== '0')) {
      if (!$Orders->Set_OrderDate($SlctDate)) { $Error = true; }
    }
    if (!$Error) {
      $Data = $Orders->LoadOrdersStatusfilter();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Options .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
        }
      }
      if ($SlctStatus === '') {
        $Options = str_replace('value="0"', 'value="0" selected', $Options);
      } else {
        $Options = str_replace('value="'.$SlctStatus.'"', 'value="'.$SlctStatus.'" selected', $Options);
      }
    }
    unset($Orders);
    return $Options;
  }
  private function LoadOrdersCustomersOptions($SlctStatus,$SlctCustomers,$SlctPayment,$SlctDate) {
    $Error = false;
    $Options = '<option value="0">Select Customer</option>';
    $Orders = new mensio_orders();
    if (($SlctStatus !== '') && ($SlctStatus !== '0')) {
      if (!$Orders->Set_Status($SlctStatus)) { $Error = true; }
    }
    if (($SlctPayment !== '') && ($SlctPayment !== '0')) {
      if (!$Orders->Set_PaymentType($SlctPayment)) { $Error = true; }
    }
    if (($SlctDate !== '') && ($SlctDate !== '0')) {
      if (!$Orders->Set_OrderDate($SlctDate)) { $Error = true; }
    }
    if (!$Error) {
      $Data = $Orders->LoadOrdersCustomersFilter();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Options .= '<option value="'.$Row->uuid.'">'.$Row->lastname.' '.$Row->firstname.'</option>';
        }
      }
      if ($SlctCustomers === '') {
        $Options = str_replace('value="0"', 'value="0" selected', $Options);
      } else {
        $Options = str_replace('value="'.$SlctCustomers.'"', 'value="'.$SlctCustomers.'" selected', $Options);
      }
    }
    unset($Orders);
    return $Options;
  }
  private function LoadOrdersPaymentOptions($SlctStatus,$SlctCustomers,$SlctPayment,$SlctDate) {
    $Error = false;
    $Options = '<option value="0">Select Payment Type</option>';
    $Orders = new mensio_orders();
    if (($SlctStatus !== '') && ($SlctStatus !== '0')) {
      if (!$Orders->Set_Status($SlctStatus)) { $Error = true; }
    }
    if (($SlctCustomers !== '') && ($SlctCustomers !== '0')) {
      if (!$Orders->Set_Customer($SlctCustomers)) { $Error = true; }
    }
    if (($SlctDate !== '') && ($SlctDate !== '0')) {
      if (!$Orders->Set_OrderDate($SlctDate)) { $Error = true; }
    }
    if (!$Error) {
      $Data = $Orders->LoadOrdersPaymentFilter();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Options .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
        }
      }
      if ($SlctPayment === '') {
        $Options = str_replace('value="0"', 'value="0" selected', $Options);
      } else {
        $Options = str_replace('value="'.$SlctPayment.'"', 'value="'.$SlctPayment.'" selected', $Options);
      }
    }
    unset($Orders);
    return $Options;
  }
  private function LoadOrdersDateOptions($SlctStatus,$SlctCustomers,$SlctPayment,$SlctDate) {
    $Error = false;
    $Options = '<option value="0">Select Date</option>';
    $Orders = new mensio_orders();
    if (($SlctStatus !== '') && ($SlctStatus !== '0')) {
      if (!$Orders->Set_Status($SlctStatus)) { $Error = true; }
    }
    if (($SlctCustomers !== '') && ($SlctCustomers !== '0')) {
      if (!$Orders->Set_Customer($SlctCustomers)) { $Error = true; }
    }
    if (($SlctPayment !== '') && ($SlctPayment !== '0')) {
      if (!$Orders->Set_PaymentType($SlctPayment)) { $Error = true; }
    }
    if (!$Error) {
      $DataSet = $Orders->LoadOrdersDatesFilter();
      $Month = '';
      foreach ($DataSet as $Row) {
        if ($Month !== substr($Row->created, 0, 7)) {
          $Month = substr($Row->created, 0, 7);
          $Data = explode('-',$Month);
          $Name = date('F', mktime(0, 0, 0, intval($Data[1]), 1, intval($Data[0])));
          $Options .= '<option value="'.$Month.'">'.$Name.' '.$Data[0].'</option>';
        }
      }
      if ($SlctDate === '') {
        $Options = str_replace('value="0"', 'value="0" selected', $Options);
      } else {
        $Options = str_replace('value="'.$SlctDate.'"', 'value="'.$SlctDate.'" selected', $Options);
      }    
    }    
    unset($Orders);
    return $Options;
  }
  public function LoadOrderData($OrderID) {
    $RtrnData = array(
      'ERROR'=>'FALSE','Message'=>'','Order'=>'','Customer'=>'','BillingAddr'=>'',
      'SendingAddr'=>'','Payment'=>'','Serial'=>'','CstData'=>'','BAData'=>'',
      'SAData'=>'','Products'=>'','Totals'=>'','PayData'=>'','Status'=>'',
      'ActiveStatus'=>'','PrdTable'=>'','ShippingType'=>'','Complete'=>'FALSE');
    $NoteType = '';
    $Orders = new mensio_orders();
    if (!$Orders->Set_UUID($OrderID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order id not correct<br>';
    } else {
      $DataSet = $Orders->GetOrderData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData['Order'] = $Row->uuid;
          $RtrnData['Customer'] = $Row->customer;
          $RtrnData['BillingAddr'] = $Row->billingaddr;
          $RtrnData['SendingAddr'] = $Row->sendingaddr;
          $RtrnData['ShippingType'] = $Row->shipping;
          $RtrnData['Serial'] = $Row->serial.' ---- #'.$Row->refnumber;
          if ($Row->complete) {
            $RtrnData['Complete'] = 'TRUE';
            $RtrnData['Serial'] .= '<i class="fa fa-lock fa-2x" aria-hidden="true"></i>';
          }
        }
        $RtrnData['CstData'] = $this->LoadCustomerTag($RtrnData['Customer']);
        $RtrnData['BAData'] = $this->LoadAddressTag($RtrnData['BillingAddr']);
        $RtrnData['SAData'] = $this->LoadAddressTag($RtrnData['SendingAddr']);
        $Products = $this->LoadOrderProducts($RtrnData['Order']);
        $RtrnData['Products'] = $Products['Products'];
        $RtrnData['PrdTable'] = $Products['PrdTable'];
        $RtrnData['Totals'] = $Products['Totals'];
        $Data = $this->LoadStatusTag($OrderID);
        $RtrnData['Status'] = $Data['StatusTag'];
        $RtrnData['ActiveStatus'] = $Data['ActiveStatus'];
      }
    }
    unset($Orders);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  private function LoadCustomerTag($CustomerID) {
    $RtrnData = '';
    $Customers = new mensio_customers();
    if ($Customers->Set_UUID($CustomerID)) {
      $DataSet = $Customers->LoadCustomerData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData = '<div class="OrderDataDiv">
            <label class="label_symbol label_txt">Title:</label>
            <label class="label_response_txt">'.$Row->title.'</label>
          </div>
          <div class="OrderDataDiv">
            <label class="label_symbol label_txt">First Name:</label>
            <label class="label_response_txt">'.$Row->firstname.'</label>
          </div>
          <div class="OrderDataDiv">
            <label class="label_symbol label_txt">Last Name:</label>
            <label class="label_response_txt">'.$Row->lastname.'</label>
          </div>
          <div class="OrderDataDiv">
            <label class="label_symbol label_txt">User Name:</label>
            <label class="label_response_txt">'.$Row->username.'</label>
          </div>
          <div class="OrderDataDiv">
            <label class="label_symbol label_txt">Created:</label>
            <label class="label_response_txt">'.$Row->created.'</label>
          </div>';
        }
      }
    }
    unset($Customers); 
    return $RtrnData;
  }
  public function LoadAddressTag($Address) {
    $RtrnData = '';
    $Customers = new mensio_customers();
    if ($Customers->Set_Address($Address)) {
      $DataSet = $Customers->LoadAddressData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $CountryName = '';
          $Countries = new mensio_countries();
          if ($Countries->Set_UUID($Row->country)) {
            $CountryName = $Countries->GetCountryName();
          }
          unset($Countries);
          $RtrnData = '<div class="OrderDataDiv">
              <label class="label_symbol label_txt">Name:</label>
              <label class="label_response_txt">'.$Row->fullname.'</label>
            </div>
            <div class="OrderDataDiv">
              <label class="label_symbol label_txt">Country:</label>
              <label class="label_response_txt">'.$CountryName.'</label>
            </div>
            <div class="OrderDataDiv">
              <label class="label_symbol label_txt">City:</label>
              <label class="label_response_txt">'.$Row->city.'</label>
            </div>
            <div class="OrderDataDiv">
              <label class="label_symbol label_txt">Street:</label>
              <label class="label_response_txt">'.$Row->street.'</label>
            </div>
            <div class="OrderDataDiv">
              <label class="label_symbol label_txt">Zip Code:</label>
              <label class="label_response_txt">'.$Row->zipcode.'</label>
            </div>
            <div class="OrderDataDiv">
              <label class="label_symbol label_txt">Phone:</label>
              <label class="label_response_txt">'.$Row->phone.'</label>
            </div>
            <div class="OrderDataDiv">
              <label class="label_symbol label_txt">Notes:</label>
              <label class="label_response_txt">'.$Row->notes.'</label>
            </div>';
        }
      }
    }
    unset($Customers); 
    return $RtrnData;
  }
  public function LoadOrderProducts($OrderID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Products'=>'','PrdTable'=>'','Totals'=>'');
    $NoteType = '';
    $Orders = new mensio_orders();
    if (!$Orders->Set_UUID($OrderID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order id not correct<br>';
    } else {
      $DataSet = $Orders->LoadOrderProducts();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $Discounts = $this->LoadOrderDiscounts($OrderID);
        $Shipping = $Orders->LoadOrderShipping();
        $FullPrice = 0;
        $RowClass = 'OddTblLine';
        foreach ($DataSet as $Row) {
          if ($RtrnData['Products'] === '') { $RtrnData['Products'] = $Row->product; }
            else { $RtrnData['Products'] .= ';'.$Row->product; }
          $RtrnData['PrdTable'] .= '
              <tr class="'.$RowClass.'">
                <td class="BtnCol">
                  <div class="ESBtnsDivs">
                    <div id="PrdVW_'.$Row->product.'" class="ESBtns ViewProductInfo" title="View Product Info">
                      <i class="fa fa-eye" aria-hidden="true"></i>
                    </div>
                  </div>
                  <div class="ESBtnsDivs">
                    <div id="PrdDL_'.$Row->product.'" class="ESBtns RemoveProduct" title="Remove Product">
                      <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                  </div>
                </td>
                <td id="name_'.$Row->product.'">'.$Row->code.' - '.$Row->name.'</td>
                <td class="SmlCol">
                  <input class="TblInput" type="number" id="amount_'.$Row->product.'" value="'.($Row->amount + 0).'" min="0" max="'.($Row->stock + 0).'">
                </td>
                <td class="SmlCol">
                  <input class="TblInput" type="text" id="price_'.$Row->product.'" value="'.($Row->price + 0).' '.$this->CurIcon.'">
                </td>
                <td class="SmlCol">
                  <input class="TblInput" type="text" id="discount_'.$Row->product.'" value="'.($Row->discount + 0).' '.$this->CurIcon.'">
                </td>
                <td class="SmlCol"id="prdtax_'.$Row->product.'">'.($Row->taxes + 0).'</span> '.$this->CurIcon.'</td>
                <td class="SmlCol">'.($Row->fullprice + 0).' '.$this->CurIcon.'</td>
              </tr> ';
          $FullPrice = $FullPrice + $Row->fullprice;
          if ($RowClass === 'OddTblLine') { $RowClass = 'EvenTblLine'; }
            else { $RowClass = 'OddTblLine'; }
        }
        if ($Discounts['Percent'] !== 0) {
          $Discounts['Totals'] += $FullPrice * ($Discounts['Percent'] / 100);
        }
        $OrderPrice = ($FullPrice - $Discounts['Totals']) + $Shipping;
        if ($OrderPrice < 0 ) {
          $OrderPrice = '!!!! ORDER PRICE LESS THAN ZERO !!!!';
        } else {
          $OrderPrice = ($OrderPrice + 0);
        }
        $RtrnData['Totals'] = '
              <tr>
                <td class="BtnCol"></td>
                <td></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"><span class="LblCol">Total:</span></td>
                <td class="SmlCol">'.($FullPrice + 0).' '.$this->CurIcon.'</td>
              </tr>
              <tr>
                <td class="BtnCol"></td>
                <td></td>                                                         <!-- INSERT SHIPPING INFO HERE -->
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td id="MngShpngCol" class="SmlCol">
                  <div class="ESBtnsDivs">
                    <div id="BTN_EditShippingType" class="ESBtns" title="Select Shipping Method">
                      <i class="fa fa-refresh" aria-hidden="true"></i>
                    </div>
                  </div>
                </td>
                <td class="SmlCol"><span class="LblCol">Shipping:</span></td>
                <td class="SmlCol" id="Shipping">'.($Shipping + 0).' '.$this->CurIcon.'</td>
              </tr>
              <tr>
                <td class="BtnCol"></td>
                <td>'.$Discounts['Info'].'</td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td id="MngShpngCol" class="SmlCol"></td>
                <td class="SmlCol"><span class="LblCol">Discounts:</span></td>
                <td class="SmlCol" id="OrderDiscounts">'.($Discounts['Totals'] + 0).' '.$this->CurIcon.'</td>
              </tr>
              <tr>
                <td class="BtnCol"></td>
                <td></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"><span class="LblCol">Complete:</span></td>
                <td class="SmlCol">
                  <input type="hidden" id="CompletePrice" value="'.$OrderPrice.'">
                  '.$OrderPrice.' '.$this->CurIcon.'
                 </td>
              </tr>';
      }
    }
    unset($Orders);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  private function LoadOrderDiscounts($OrderID) {
    $Discounts = array('Info'=> '','Percent'=> 0,'Totals'=> 0);
    $Orders = new mensio_orders();
    if ($Orders->Set_UUID($OrderID)) {
      $DataSet = $Orders->LoadOrderDiscounts();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if ($Discounts['Info'] === '') { $Discounts['Info'] = $Row->name; }
            else  { $Discounts['Info'] .= '<br>'.$Row->name; }
          if ($Row->flatdisc) { $Discounts['Totals'] += $Row->discount; }
            else { $Discounts['Percent'] += $Row->discount; }
        }
      }
    }
    unset($Orders);
    return $Discounts;
  }
  public function LoadStatusTag($OrderID) {
    $RtrnData = array('StatusTag'=>'','ActiveStatus'=>'');
    $Orders = new mensio_orders();
    if ($Orders->Set_UUID($OrderID)) {
      $DataSet = $Orders->LoadOrderStatusHistory();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $RowClass = 'OddTblLine';
        foreach ($DataSet as $Row) {
          $ActiveIcon = '<i class="fa fa-times fa-lg" aria-hidden="true"></i>';
          $MailBtn = '';
          if ($Row->active) {
            $ActiveIcon = '<i class="fa fa-check fa-lg" aria-hidden="true"></i>';
            $MailBtn = '<div id="Btn_MlStsInfo" class="MailESBtns" title="Send Inform Mail">
                <i class="fa fa-envelope" aria-hidden="true"></i>
              </div>';
            $RtrnData['ActiveStatus'] = $Row->name;
          }
          $RtrnData['StatusTag'] .='
                <tr class="'.$RowClass.'">
                  <td>'.$MailBtn.' '.$Row->name.'</td>
                  <td class="SmlCol">'.date("d/m/Y", strtotime($Row->changed)).'</td>
                  <td class="SmlCol">'.date("H:i:s", strtotime($Row->changed)).'</td>
                  <td class="SmlCol ActStat">
                    '.$ActiveIcon.'
                  </td>
                </tr>';
          if ($RowClass === 'OddTblLine') { $RowClass = 'EvenTblLine'; }
            else { $RowClass = 'OddTblLine'; }
        }
      }
    }
    unset($Orders);
    return $RtrnData;
  }
  public function LoadCustomerSelectionModal() {
    $MdlForm = '
    <div class="ModalCustomerSelector">
      <div class="CstmrSrchFldDiv">
        <label class="label_symbol">
          <i class="fa fa-search fa-lg" aria-hidden="true"></i>
          Search .....
        </label>
        <input type="text" id="MDL_SrchCstmr" value="" class="form-control">
      </div>
      <div id="MDL_CstmrLst" class="CstmrLst"></div>
    </div>';
    return $this->CreateModalWindow('Customer Selection', $MdlForm);
  }
  public function SearchOrdersCustomer($Search) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Results'=>'');
    $NoteType = '';
    $DataSet = '';
    $Customers = new mensio_customers();
    if (!$Customers->Set_SearchString($Search)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with the search string '.$Search.'<br>';
    } else {
      $DataSet = $Customers->LoadTableList();
    }
    unset($Customers);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $RtrnData['Results'] .= '
        <div id="'.$Row['uuid'].'" class="CstmrSlctr">
          <label class="SlctLbl">Name : </label>'.$Row['name'].'<br>
          <label class="SlctLbl">Type : </label>'.$Row['type'].'<br>
          <label class="SlctLbl">Created : </label>'.$Row['created'].'
        </div>';
      }
    }
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadOrdersCustomerData($CustomerID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Customer'=>'',
      'BillingAddr'=>'','SendingAddr'=>'','CstData'=>'','BAData'=>'',
      'SAData'=>'');
    $Customers = new mensio_customers();
    if ($Customers->Set_UUID($CustomerID)) {
      $RtrnData['Customer'] = $CustomerID;
      $RtrnData['CstData'] = $this->LoadCustomerTag($CustomerID);
      $AddrData = $Customers->LoadCustomerAddress();
      if ((is_array($AddrData)) && (!empty($AddrData[0]))) {
        $RecCount = count($AddrData);
        switch ($RecCount) {
        case 0:
          $RtrnData['BAData'] = 'Please Insert Billing Address';
          $RtrnData['SAData'] = 'Please Insert Sending Address';
          break;
        case 1:
          $RtrnData['BillingAddr'] = $AddrData[0]->uuid;
          $RtrnData['SendingAddr'] = $AddrData[0]->uuid;
          $RtrnData['BAData'] = $this->LoadAddressTag($AddrData[0]->uuid);
          $RtrnData['SAData'] = $RtrnData['BAData'];
          break;
        default:
          foreach ($AddrData as $Row) {
            if (($Row->name === 'Billing') || ($Row->name === 'Both')) {
              if ($RtrnData['BillingAddr'] === '') {
                $RtrnData['BillingAddr'] = $Row->uuid;
                $RtrnData['BAData'] = $this->LoadAddressTag($Row->uuid);
              }
            }
            if (($Row->name === 'Shipping') || ($Row->name === 'Both')) {
              if ($RtrnData['SendingAddr'] === '') {
                $RtrnData['SendingAddr'] = $Row->uuid;
                $RtrnData['SAData'] = $this->LoadAddressTag($Row->uuid);
              }
            }
          }
          break;
        }
      }
    }
    unset($Customers);
    return $RtrnData;
  }
  public function LoadAddressSelectionModal($Type,$CustomerID) {
    $MdlForm = '';
    $AddrDiv = '';
    if (($Type === 'Billing') || ($Type === 'Shipping')) {
      $Customers = new mensio_customers();
      if ($Customers->Set_UUID($CustomerID)) {
        $AddrData = $Customers->LoadCustomerAddress();
        if ((is_array($AddrData)) && (!empty($AddrData[0]))) {
          foreach ($AddrData as $Row) {
            if (($Row->name === $Type) || ($Row->name === 'Billing / Shipping')) {
              $CountryName = '';
              $Countries = new mensio_countries();
              if ($Countries->Set_UUID($Row->country)) {
                $CountryName = $Countries->GetCountryName();
              }
              unset($Countries);
              $AddrDiv .= '
                <div id="'.$Row->uuid.'" class="CstmrAddrSlctr">
                  <label class="SlctLbl">Type : </label>'.$Row->name.'<br>
                  <label class="SlctLbl">Name : </label>'.$Row->fullname.'<br>
                  <label class="SlctLbl">Country : </label>'.$CountryName.'<br>
                  <label class="SlctLbl">City : </label>'.$Row->city.'<br>
                  <label class="SlctLbl">Street : </label>'.$Row->street.'<br>
                  <label class="SlctLbl">Street : </label>'.$Row->street.'<br>
                  <label class="SlctLbl">Zip Code : </label>'.$Row->zipcode.'<br>
                  <label class="SlctLbl">Phone : </label>'.$Row->phone.'<br>
                </div>';
            }
          }
        }
      }
      unset($Customers);
      if ($AddrDiv === '') { $AddrDiv = 'No Address Found!!!'; }
      $MdlForm = '
      <div class="ModalCustomerSelector">
        <input type="hidden" id="MDL_AddrType" value="'.$Type.'">
        <div id="MDL_AddrLst" class="CstmrLst">
          '.$AddrDiv.'
        </div>
      </div>';
      $MdlForm = $this->CreateModalWindow($Type.' Address Selection', $MdlForm);
    }
    return $MdlForm;
  }
  public function LoadCustomerOrderAddressData($Type,$CustomerID,$AddressID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','BillingAddr'=>'',
        'SendingAddr'=>'','BAData'=>'','SAData'=>'','Type'=>'');
    if (($Type === 'Billing') || ($Type === 'Shipping')) {
      $Customers = new mensio_customers();
      if (($Customers->Set_UUID($CustomerID)) && ($Customers->Set_Address($AddressID))) {
        $RtrnData['Type'] = $Type;
        if ($Type === 'Billing') {
          $RtrnData['BillingAddr'] = $AddressID;
          $RtrnData['BAData'] = $this->LoadAddressTag($AddressID);
        } else {
          $RtrnData['SendingAddr'] = $AddressID;
          $RtrnData['SAData'] = $this->LoadAddressTag($AddressID);
        }
      }
      unset($Customers);
    }
    return $RtrnData;
  }
  public function LoadProductOrdersSelection() {
    $MdlForm = '
    <div class="ModalCustomerSelector">
      <div class="CstmrSrchFldDiv">
        <label class="label_symbol">
          <i class="fa fa-search fa-lg" aria-hidden="true"></i>
          Search .....
        </label>
        <input type="text" id="MDL_SrchPrd" value="" class="form-control">
      </div>    
      <div id="MDL_ProdLst" class="CstmrLst"></div>
    </div>';
    $MdlForm = $this->CreateModalWindow('Product Selection', $MdlForm);
    return $MdlForm;
  }
  public function LoadSearchProductsForOrders($OrderPrds,$SrchPrd) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Results'=>'');
    $NoteType = '';
    $OrderPrds = $this->CheckOrderProductIDs($OrderPrds);
    $Orders = new mensio_orders();
    if (!$Orders->Set_SearchString($SrchPrd)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Search value not correct "'.$SrchPrd.'"<br>';
    } else {
      $DataSet = $Orders->LoadNonOrderProducts($OrderPrds);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData['Results'] .= '
          <div id="Product_'.$Row['uuid'].'" class="ProdSlctr">
            <div class="ProdSlctrImgDiv">
              <img src="'.get_site_url().'/'.$Row['Image'].'">
            </div>
            <div class="ProdSlctrInfoDiv">
              <label class="SlctLbl">Code : </label>'.$Row['Code'].'<br>
              <label class="SlctLbl">Brand : </label>'.$Row['BrandName'].'<br>
              <label class="SlctLbl">Name : </label>'.$Row['Name'].'<br>
            </div>
            <div class="ProdSlctrAmountDiv">
              <input type="number" id="MDL_PrdAmount_'.$Row['uuid'].'" class="form-control" min="0" max="'.$Row['Stock'].'" value="1">
              <div id="'.$Row['uuid'].'" class="button AddPrdBtn MDL_BTN_AddProduct" title="Add Product">
                <i class="fa fa-plus fa-2x" aria-hidden="true"></i>
              </div>
            </div>
            <div class="DivResizer"></div>
          </div>';
        }
      }
    }
    unset($Orders);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  private function CheckOrderProductIDs($OrderPrds) {
    $RtrnData = '';
    if ($OrderPrds !== '') {
      $OrderPrds = explode (';',$OrderPrds);
      if (is_array($OrderPrds)) {
        $Orders = new mensio_orders();
        foreach ($OrderPrds as $Row) {
          if (!$Orders->Set_Product($Row)) { $RtrnData = false; }
        }
        unset($Orders);
        if ($RtrnData === '') { $RtrnData = $OrderPrds; }
      }
    }
    return $RtrnData;
  }
  public function AddProductToOrderTable($OrderID,$OrderPrds,$NewPrds,$Amount,$Shipping) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Products'=>'','Totals'=>'','PrdTable'=>'');
    $NoteType = '';
    $Data = $this->CreateProductLines($OrderPrds);
    $RtrnData['Products'] = $Data['Products'];
    $RtrnData['PrdTable'] = $Data['PrdTable'];
    $RtrnData['Totals'] = $Data['Totals'];
    $Data = $this->AddNewProductLine($NewPrds,$Amount);
    if ($RtrnData['Products'] === '') { $RtrnData['Products'] = $Data['Product']; }
      else { $RtrnData['Products'] .= ';'.$Data['Product']; }
    $RtrnData['PrdTable'] .= $Data['PrdLine'];
    $RtrnData['Totals'] += $Data['Price'];
    $Discounts = $this->LoadOrderDiscounts($OrderID);
    if ($Discounts['Percent'] !== 0) {
      $Discounts['Totals'] += $RtrnData['Totals'] * ($Discounts['Percent'] / 100);
    }
    $OrderPrice = ($RtrnData['Totals'] - $Discounts['Totals']) + $Shipping;
    if ($OrderPrice < 0 ) {
      $OrderPrice = '!!!! ORDER PRICE LESS THAN ZERO !!!!';
    } else {
      $OrderPrice = ($OrderPrice + 0);
    }
    $RtrnData['Totals'] = '
              <tr>
                <td class="BtnCol"></td>
                <td></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"><span class="LblCol">Total:</span></td>
                <td class="SmlCol">'.($RtrnData['Totals'] + 0).' '.$this->CurIcon.'</td>
              </tr>
              <tr>
                <td class="BtnCol"></td>
                <td></td>                                                         <!-- INSERT SHIPPING INFO HERE -->
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td id="MngShpngCol" class="SmlCol">
                  <div class="ESBtnsDivs">
                    <div id="BTN_EditShippingType" class="ESBtns" title="Select Shipping Method">
                      <i class="fa fa-refresh" aria-hidden="true"></i>
                    </div>
                  </div>
                </td>
                <td class="SmlCol"><span class="LblCol">Shipping:</span></td>
                <td class="SmlCol" id="Shipping">'.($Shipping + 0).' '.$this->CurIcon.'</td>
              </tr>
              <tr>
                <td class="BtnCol"></td>
                <td>'.$Discounts['Info'].'</td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td id="MngShpngCol" class="SmlCol"></td>
                <td class="SmlCol"><span class="LblCol">Discounts:</span></td>
                <td class="SmlCol" id="OrderDiscounts">'.($Discounts['Totals'] + 0).' '.$this->CurIcon.'</td>
              </tr>
              <tr>
                <td class="BtnCol"></td>
                <td></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"><span class="LblCol">Complete:</span></td>
                <td class="SmlCol">
                  <input type="hidden" id="CompletePrice" value="'.$OrderPrice.'">
                  '.$OrderPrice.' '.$this->CurIcon.'
                 </td>
              </tr>';
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function RemoveProductFromOrderTable($OrderID,$OrderPrds,$Product,$Shipping) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Products'=>'','Totals'=>'','PrdTable'=>'');
    $NoteType = '';
    $Data = $this->CreateProductLines($OrderPrds,$Product);
    $RtrnData['Products'] = $Data['Products'];
    $RtrnData['PrdTable'] = $Data['PrdTable'];
    $RtrnData['Totals'] = $Data['Totals'];    
    $Discounts = $this->LoadOrderDiscounts($OrderID);
    if ($Discounts['Percent'] !== 0) {
      $Discounts['Totals'] += $RtrnData['Totals'] * ($Discounts['Percent'] / 100);
    }
    $OrderPrice = ($RtrnData['Totals'] - $Discounts['Totals']) + $Shipping;
    if ($OrderPrice < 0 ) {
      $OrderPrice = '!!!! ORDER PRICE LESS THAN ZERO !!!!';
    } else {
      $OrderPrice = ($OrderPrice + 0);
    }
    $RtrnData['Totals'] = '
              <tr>
                <td class="BtnCol"></td>
                <td></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"><span class="LblCol">Total:</span></td>
                <td class="SmlCol">'.($RtrnData['Totals'] + 0).' '.$this->CurIcon.'</td>
              </tr>
              <tr>
                <td class="BtnCol"></td>
                <td></td>                                                         <!-- INSERT SHIPPING INFO HERE -->
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td id="MngShpngCol" class="SmlCol">
                  <div class="ESBtnsDivs">
                    <div id="BTN_EditShippingType" class="ESBtns" title="Select Shipping Method">
                      <i class="fa fa-refresh" aria-hidden="true"></i>
                    </div>
                  </div>
                </td>
                <td class="SmlCol"><span class="LblCol">Shipping:</span></td>
                <td class="SmlCol" id="Shipping">'.($Shipping + 0).' '.$this->CurIcon.'</td>
              </tr>
              <tr>
                <td class="BtnCol"></td>
                <td>'.$Discounts['Info'].'</td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td id="MngShpngCol" class="SmlCol"></td>
                <td class="SmlCol"><span class="LblCol">Discounts:</span></td>
                <td class="SmlCol" id="OrderDiscounts">'.($Discounts['Totals'] + 0).' '.$this->CurIcon.'</td>
              </tr>
              <tr>
                <td class="BtnCol"></td>
                <td></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"><span class="LblCol">Complete:</span></td>
                <td class="SmlCol">
                  <input type="hidden" id="CompletePrice" value="'.$OrderPrice.'">
                  '.$OrderPrice.' '.$this->CurIcon.'
                 </td>
              </tr>';
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  private function CreateProductLines($OrderPrds,$Product='') {
    $RtrnData = array('Products'=>'','Totals'=>0,'PrdTable'=>'');
    $OrderPrds = stripslashes($OrderPrds);
    $OrderPrds = json_decode($OrderPrds, true);
    if ((is_array($OrderPrds)) && (!empty($OrderPrds[0]))) {
      $FullPrice = 0;
      $RowClass = 'OddTblLine';
      $Prds = new mensio_products();
      foreach ($OrderPrds as $Row) {
        if ($Product !== $Row['ID']) {
          if ($Prds->Set_UUID($Row['ID'])) {
            $DataSet = $Prds->LoadProductRecordData();
            if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
              foreach ($DataSet as $DataRow) {
                $Row['Taxes'] = (($Row['Amount'] * $Row['Price']) * ($DataRow->tax / 100));
              }
            }
          }
          $ProdPrice = (($Row['Amount'] * $Row['Price']) + $Row['Taxes']) - $Row['Discount'];
          if ($RtrnData['Products'] === '') { $RtrnData['Products'] = $Row['ID']; }
            else { $RtrnData['Products'] .= ';'.$Row['ID']; }
          $RtrnData['PrdTable'] .= '
                <tr class="'.$RowClass.'">
                  <td class="BtnCol">
                    <div class="ESBtnsDivs">
                      <div id="PrdVW_'.$Row['ID'].'" class="ESBtns ViewProductInfo" title="View Product Info">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                      </div>
                    </div>
                    <div class="ESBtnsDivs">
                      <div id="PrdDL_'.$Row['ID'].'" class="ESBtns RemoveProduct" title="Remove Product">
                        <i class="fa fa-times" aria-hidden="true"></i>
                      </div>
                    </div>
                  </td>
                  <td id="name_'.$Row['ID'].'">'.$Row['Name'].'</td>
                  <td class="SmlCol">
                    <input class="TblInput" type="number" id="amount_'.$Row['ID'].'" value="'.($Row['Amount'] + 0).'" min="0" max="'.($Row['Max'] + 0).'">
                  </td>
                  <td class="SmlCol">
                    <input class="TblInput" type="text" id="price_'.$Row['ID'].'" value="'.($Row['Price'] + 0).' '.$this->CurIcon.'">
                  </td>
                  <td class="SmlCol">
                    <input class="TblInput" type="text" id="discount_'.$Row['ID'].'" value="'.($Row['Discount'] + 0).' '.$this->CurIcon.'">
                  </td>
                  <td class="SmlCol" id="prdtax_'.$Row['ID'].'">'.($Row['Taxes'] + 0).'</span> '.$this->CurIcon.'</td>
                  <td class="SmlCol">'.($ProdPrice + 0).' '.$this->CurIcon.'</td>
                </tr> ';
          $FullPrice = $FullPrice + $ProdPrice;
          if ($RowClass === 'OddTblLine') { $RowClass = 'EvenTblLine'; }
            else { $RowClass = 'OddTblLine'; }
        }
      }
      unset($Prds);
      $RtrnData['Totals'] = $FullPrice;
    }
    return $RtrnData;
  }
  private function AddNewProductLine($NewPrds,$Amount) {
    $RtrnData = array('Product'=>'','Price'=>0,'PrdLine'=>'');
    $Products = new mensio_products();
    if ($Products->Set_UUID($NewPrds)) {
      $DataSet = $Products->LoadProductRecordData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData['Product'] = $NewPrds;
          $Tax = (($Amount * $Row->price) * ($Row->tax / 100));
          $Discount = (($Amount * $Row->price) * ($Row->discount / 100));
          $RtrnData['Price'] = (($Amount * $Row->price) + $Tax) - $Discount;
          $RtrnData['PrdLine'] .= '
            <tr class="'.$RowClass.'">
              <td class="BtnCol">
                <div class="ESBtnsDivs">
                  <div id="PrdVW_'.$NewPrds.'" class="ESBtns ViewProductInfo" title="View Product Info">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                  </div>
                </div>
                <div class="ESBtnsDivs">
                  <div id="PrdDL_'.$NewPrds.'" class="ESBtns RemoveProduct" title="Remove Product">
                    <i class="fa fa-times" aria-hidden="true"></i>
                  </div>
                </div>
              </td>
              <td id="name_'.$NewPrds.'">'.$Row->code.' - '.$Row->name.'</td>
              <td class="SmlCol">
                <input class="TblInput" type="number" id="amount_'.$NewPrds.'" value="'.($Amount + 0).'" min="0" max="'.($Row->stock + 0).'">
              </td>
              <td class="SmlCol">
                <input class="TblInput" type="text" id="price_'.$NewPrds.'" value="'.($Row->price + 0).' '.$this->CurIcon.'">
              </td>
              <td class="SmlCol">
                <input class="TblInput" type="text" id="discount_'.$NewPrds.'" value="'.($Discount + 0).' '.$this->CurIcon.'">
              </td>
              <td class="SmlCol" id="prdtax_'.$NewPrds.'">'.($Tax + 0).'</span> '.$this->CurIcon.'</td>
              <td class="SmlCol">'.($RtrnData['Price'] + 0).' '.$this->CurIcon.'</td>
            </tr> ';
        }
      }
    }
    unset($Products);
    return $RtrnData;
  }
  public function LoadViewProductModal($Product) {
    $PrdFrm = '';
    $Img = '';
    $Products = new mensio_products();
    if ($Products->Set_UUID($Product)) {
      $ImgSet = $Products->LoadProductRecordImages();
      if ((is_array($ImgSet)) && (!empty($ImgSet[0]))) {
        foreach ($ImgSet as $Row) {
          if ($Row->main) { $Img = get_site_url().'/'.$Row->file; }
        }
      }
      $DataSet = $Products->LoadProductRecordData();
      if ((!is_array($DataSet)) || (empty($DataSet[0]))) {
        $DataSet = $Products->LoadVariationProductRecordData(false);
      }
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Brand = $this->GatBrandName($Row->brand);
          $Status = $this->GatStatusName($Row->status);
          $PrdFrm = '
          <div class="MdlPrdImgDiv">
            <img src="'.$Img.'">
          </div>
          <div class="MdlPrdIbfoDiv">
            <h3>'.$Row->name.'</h3>
            <p><label class="Mdl_LblPrdInfo">Code :</label> '.$Row->code.'</p>
            <p><label class="Mdl_LblPrdInfo">Brand :</label> '.$Brand.'</p>
            <p><label class="Mdl_LblPrdInfo">BtB Price :</label> '.($Row->btbprice + 0).' '.$this->CurIcon.'</p>
            <p><label class="Mdl_LblPrdInfo">Price :</label> '.($Row->price + 0).' '.$this->CurIcon.'</p>
            <p><label class="Mdl_LblPrdInfo">Tax :</label> '.$Row->tax.'%</p>
            <p><label class="Mdl_LblPrdInfo">Discount :</label> '.$Row->discount.'%</p>
            <p><label class="Mdl_LblPrdInfo">Stock :</label> '.($Row->stock + 0).'</p>
            <p><label class="Mdl_LblPrdInfo">Status :</label> '.$Status.'</p>
            <label class="Mdl_LblPrdInfo">Description :</label><br>'.$Row->description.'<p></p>
            <label class="Mdl_LblPrdInfo">Info :</label>'.$Row->notes.'
          </div>';
        }
        unset($Orders);
      }
    }
    unset($Products);
    $MdlForm = '
    <div class="ModalProductInfo">
      '.$PrdFrm.'
      <div class="DivResizer"></div>
    </div>';
    $MdlForm = $this->CreateModalWindow('Product Info', $MdlForm);
    return $MdlForm;
  }
  private function GatBrandName($BrandID) {
    $Name = '';
    $Brands = new mensio_products_brands();
    if ($Brands->Set_UUID($BrandID)) {
      $DataSet = $Brands->GetBrandData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) { $Name = $Row->name; }
      }
    }
    unset($Brands);
    return $Name;
  }
  private function GatStatusName($StatusID) {
    $Name = '';
    $Products = new mensio_products();
    $DataSet = $Products->LoadStatusDataSet();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if ($Row->uuid === $StatusID) { $Name = $Row->name; }
        }
      }
    unset($Brands);
    return $Name;
  }
  public function RefreshProductTableData($OrderID,$OrderPrds,$Shipping) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Products'=>'','Totals'=>'','PrdTable'=>'');
    $NoteType = '';
    $CheckFlds = $this->CheckPrdValues($OrderPrds);
    if ($CheckFlds['Error']) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] = $CheckFlds['Message'];
    } else {
      $Data = $this->CreateProductLines($OrderPrds);
      $RtrnData['Products'] = $Data['Products'];
      $RtrnData['PrdTable'] = $Data['PrdTable'];
      $RtrnData['Totals'] = $Data['Totals'];
      $Discounts = $this->LoadOrderDiscounts($OrderID);
      if ($Discounts['Percent'] !== 0) {
        $Discounts['Totals'] = $RtrnData['Totals'] * ($Discounts['Percent'] / 100);
      }
      $OrderPrice = ($RtrnData['Totals'] - $Discounts['Totals']) + $Shipping;
      if ($OrderPrice < 0 ) {
        $OrderPrice = '!!!! ORDER PRICE LESS THAN ZERO !!!!';
      } else {
        $OrderPrice = ($OrderPrice + 0);
      }
      $RtrnData['Totals'] = '
              <tr>
                <td class="BtnCol"></td>
                <td></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"><span class="LblCol">Total:</span></td>
                <td class="SmlCol">'.($RtrnData['Totals'] + 0).' '.$this->CurIcon.'</td>
              </tr>
              <tr>
                <td class="BtnCol"></td>
                <td></td>                                                         <!-- INSERT SHIPPING INFO HERE -->
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td id="MngShpngCol" class="SmlCol">
                  <div class="ESBtnsDivs">
                    <div id="BTN_EditShippingType" class="ESBtns" title="Select Shipping Method">
                      <i class="fa fa-refresh" aria-hidden="true"></i>
                    </div>
                  </div>
                </td>
                <td class="SmlCol"><span class="LblCol">Shipping:</span></td>
                <td class="SmlCol" id="Shipping">'.($Shipping + 0).' '.$this->CurIcon.'</td>
              </tr>
              <tr>
                <td class="BtnCol"></td>
                <td>'.$Discounts['Info'].'</td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td id="MngShpngCol" class="SmlCol"></td>
                <td class="SmlCol"><span class="LblCol">Discounts:</span></td>
                <td class="SmlCol" id="OrderDiscounts">'.($Discounts['Totals'] + 0).' '.$this->CurIcon.'</td>
              </tr>
              <tr>
                <td class="BtnCol"></td>
                <td></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"></td>
                <td class="SmlCol"><span class="LblCol">Complete:</span></td>
                <td class="SmlCol">
                  <input type="hidden" id="CompletePrice" value="'.$OrderPrice.'">
                  '.$OrderPrice.' '.$this->CurIcon.'
                 </td>
              </tr>';
    }
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  private function CheckPrdValues($OrderPrds) {
    $RtrnData = array('Error'=>false,'Message'=>'');
    $OrderPrds = stripslashes($OrderPrds);
    $OrderPrds = json_decode($OrderPrds, true);
    if ((is_array($OrderPrds)) && (!empty($OrderPrds[0]))) {
      $Orders = new mensio_orders();
      foreach ($OrderPrds as $Row) {
        if (!$Orders->Set_Product($Row['ID'])) {
          $RtrnData['Error'] = true;
          $RtrnData['Message'] .= 'Id of product '.$Row['Name'].' not correct<br>';
        } else {
          $Stock = $Orders->GetProductStock();
          if ($Stock < $Row['Amount']) {
            $RtrnData['Error'] = true;
            $RtrnData['Message'] .= 'Amount value given ('.($Row['Amount'] + 0).') for '.$Row['Name'].' greater than stock ('.($Stock + 0).')<br>';
          }
          if ($Row['Price'] <= 0) {
            $RtrnData['Error'] = true;
            $RtrnData['Message'] .= 'Price given ('.($Row['Price'] + 0).') for '.$Row['Name'].' is not correct<br>';
          }
          $ProdPrice = (($Row['Amount'] * $Row['Price']) + $Row['Taxes']) - $Row['Discount'];
          if ($ProdPrice < 0) {
            $RtrnData['Error'] = true;
            $RtrnData['Message'] .= 'Total value for '.$Row['Name'].' is less than 0<br>';
          }
        }
      }
      unset($Orders);
    }
    return $RtrnData;
  }
  public function LoadOrdersShippingTypeModal($ShipType,$ShipAddress) {
    $OptShipping = '';
    $WT = $this->LoadMetricIcon('Weight');
    $Orders = new mensio_orders();
    if ($Orders->Set_SendAddress($ShipAddress)) {
      $DataSet = $Orders->LoadShippingOptions();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $Main = '';
        foreach ($DataSet as $Row) {
          $OptShipping .= '<option value="'.$Row->uuid.'">['.$Row->name.'] Weight: '.($Row->weight + 0).' '.$WT.' Price: '.($Row->price + 0).' '.$this->CurIcon.'</option>';
          if ($Row->main) { $Main = $Row->uuid; }
        }
        if ($ShipType === '') {
          $OptShipping = str_replace(
            'value="'.$Main.'"',
            'value="'.$Main.'" selected',
            $OptShipping
          );
        } else {
          $OptShipping = str_replace(
            'value="'.$ShipType.'"',
            'value="'.$ShipType.'" selected',
            $OptShipping
          );
        }
      }
    }
    unset($Orders);
    $MdlForm = '
    <div class="ModalCustomerSelector">
      <select id="MDL_FLD_ShippingType" class="form-control">
        '.$OptShipping.'
      </select>
      <button id="MDL_BTN_SetShipType" class="button" title="Save">
        <i class="fa fa-floppy-o" aria-hidden="true"></i>
      </button>
    </div>';
    $MdlForm = $this->CreateModalWindow('Shipping Type Selection', $MdlForm);
    return $MdlForm;
  }
  public function SaveOrdersData($OrderID,$Customer,$BlngAddress,$SendAddress,$OrderPrds,$ShipType) {
    $NewOrder = false;
    if ($OrderID === 'NewOrder') { $NewOrder = true; }
    $RtrnData = $this->SaveOrderMainData($OrderID,$Customer,$BlngAddress,$SendAddress,$ShipType);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $OrderID = $RtrnData['Order'];
      $RtrnData = $this->SaveOrderProducts($OrderID,$OrderPrds);
      if ($RtrnData['ERROR'] === 'FALSE') {
        if ($NewOrder) {
          $RtrnData = $this->SaveNewOrderStatus($OrderID);
          if ($RtrnData['ERROR'] === 'FALSE') {
            $Data = $this->LoadStatusTag($OrderID);
            $RtrnData['Status'] = $Data['StatusTag'];
            $RtrnData['ActiveStatus'] = $Data['ActiveStatus'];
          }
        }
        $RtrnData['Order'] = $OrderID;
      }
    }
    if ($RtrnData['ERROR'] === 'TRUE') {
      $NoteType = 'Alert';
    } else {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Order Saved Successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  private function SaveOrderMainData($OrderID,$Customer,$BlngAddress,$SendAddress,$Shipping) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Order'=>'');
    $NewOrder = false;
    $Orders = new mensio_orders();
    if ($OrderID === 'NewOrder') {
      $OrderID = $Orders->GetNewID();
      $NewOrder = true;
    }
    if (!$Orders->Set_UUID($OrderID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order Id is not correct --- '.$OrderID.'<br>';
    }
    if (!$Orders->Set_Serial()) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order Serial could not be Created<br>';
    }
    if (!$Orders->Set_Customer($Customer)) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Customer Id is not correct<br>';
    }
    if (!$Orders->Set_BlngAddress($BlngAddress)) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Billing Address id not correct<br>';
    }
    if (!$Orders->Set_SendAddress($SendAddress)) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Shipping Address id not correct<br>';
    }
    if (!$Orders->Set_Shipping($Shipping)) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Shipping Type id not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $RtrnData['Order'] = $OrderID;
      if ($NewOrder) {
        if (!$Orders->InsertNewOrderData()) {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Order could not be Added<br>';
        }
      } else {
        if (!$Orders->UpdateOrderData()) {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Order cound not be updated<br>';
        }
      }
    }
    unset($Orders);
    return $RtrnData;
  }
  private function SaveOrderProducts($OrderID,$OrderPrds) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $Orders = new mensio_orders();
    if (!$Orders->Set_UUID($OrderID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order Id is not correct '.$OrderID.'<br>';
    } else {
      $OrderPrds = stripslashes($OrderPrds);
      $OrderPrds = json_decode($OrderPrds, true);
      if ((is_array($OrderPrds)) && (!empty($OrderPrds[0]))) {
        if ($Orders->ClearOrderProducts()) {
          foreach ($OrderPrds as $Row) {
            if (!$Orders->Set_Product($Row['ID'])) {
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Order Product id for "'.$Row['Name'].'" is not correct<br>';
            }
            if (!$Orders->Set_Amount($Row['Amount'])) {
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Amount for "'.$Row['Name'].'" is not correct<br>';
            }
            if (!$Orders->Set_Price($Row['Price'])) {
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Price for "'.$Row['Name'].'" is not correct<br>';
            }
            if (!$Orders->Set_Tax($Row['Taxes'])) {
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Taxes for "'.$Row['Name'].'" is not correct<br>';
            }
            if (!$Orders->Set_Discount($Row['Discount'])) {
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Discount for "'.$Row['Name'].'" is not correct<br>';
            }          
            if (!$RtrnData['Error']) {
              $Max = $Orders->GetProductStock();
              if (($Row['Amount'] > $Max) || ($Row['Amount'] < 1)) {
                $RtrnData['ERROR'] = 'TRUE';
                $RtrnData['Message'] .= 'Amount given for "'.$Row['Name'].'" not correct<br>';
              }
              $Total = (($Row['Amount'] * $Row['Price']) + $Row['Taxes']) - $Row['Discount'];
              if ($Total < 0) {
                $RtrnData['ERROR'] = 'TRUE';
                $RtrnData['Message'] .= 'Total Price Less than zero for "'.$Row['Name'].'"<br>';
              }
              if (!$RtrnData['Error']) {
                if (!$Orders->AddOrderProduct()) {
                  $RtrnData['ERROR'] = 'TRUE';
                  $RtrnData['Message'] .= 'Total Price Less than zero for "'.$Row['Name'].'"<br>';
                }
              }
            }
          }
        }
      }
    }
    unset($Orders);
    return $RtrnData;
  }
  private function SaveNewOrderStatus($OrderID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $Orders = new mensio_orders();
    if (!$Orders->Set_UUID($OrderID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order Id is not correct <br>';
    } else {
      if (!$Orders->InsertNewOrderStatus()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'New Order Status could not be updated<br>';
      }
    }
    unset($Orders);
    return $RtrnData;
  }
  public function LoadOrderStatusModal($OrderID) {
    $StatusList = '';
    $Orders = new mensio_orders();
    if ($Orders->Set_UUID($OrderID)) {
      $DataSet = $Orders->LoadStatusList();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach($DataSet as $Row) {
          $Final = '';
          if ($Row->final) {
            $Final = '<i class="fa fa-stop-circle-o" aria-hidden="true"></i>';
          }
          $StatusList .= '
            <div id="'.$Row->uuid.'" class="MDLStatusElement">
              '.$Row->name.' '.$Final.'
            </div>';
        }
      }
    }
    unset($Orders);
    $MdlForm = '
    <div class="ModalStatusSelector">
      <div class="ModalStatusList">
        '.$StatusList.'
      </div>
      <span class="ModalStatusDescription">Selecting a status automatically updates the order. If the status has the <i class="fa fa-stop-circle-o" aria-hidden="true"></i> icon it means the order will be locked disallowing any further editing</span>
    </div>';
    $MdlForm = $this->CreateModalWindow('Order Status', $MdlForm);
    return $MdlForm;
  }
  public function SaveOrdersStatus($OrderID,$StatusID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Name'=>'','Status'=>'','Complete'=>'FALSE');
    $NoteType = '';
    $Orders = new mensio_orders();
    if (!$Orders->Set_UUID($OrderID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order id is not correct <br>';
    }
    if (!$Orders->Set_Status($StatusID) ) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order Status id is not correct <br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Orders->AddOrderNewStatus()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Order Status could not be added<br>';
      } else {
        $DataSet = $Orders->LoadOrderStatusHistory();
        if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
          $RowClass = 'OddTblLine';
          foreach ($DataSet as $Row) {
            $ActiveIcon = '<i class="fa fa-times fa-lg" aria-hidden="true"></i>';
            if ($Row->active) {
              $ActiveIcon = '<i class="fa fa-check fa-lg" aria-hidden="true"></i>';
              $RtrnData['Name'] = $Row->name;
            }
            $RtrnData['Status'] .= '
                  <tr class="'.$RowClass.'">
                    <td>'.$Row->name.'</td>
                    <td class="SmlCol">'.date("d/m/Y", strtotime($Row->changed)).'</td>
                    <td class="SmlCol">'.$this->ConvertDateToTimezone($Row->changed, 'H:i:s').'</td>
                    <td class="SmlCol ActStat">
                      '.$ActiveIcon.'
                    </td>
                  </tr>';
            if ($RowClass === 'OddTblLine') { $RowClass = 'EvenTblLine'; }
              else { $RowClass = 'OddTblLine'; }
            if (($Row->active) && ($Row->final)) { $RtrnData['Complete'] = 'TRUE'; }
          }
        }
        if ($RtrnData['Complete'] === 'TRUE') {
          if (!$Orders->UpdateOrderToComplete()) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Order could not be updated to complete<br>';
          }
        }
      }
    }
    unset($Orders);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadOrderInvoice($OrderID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=> '','Name'=> '');
    $NoteType = '';
    $current_user = wp_get_current_user();
    $RtrnData['Name'] = $current_user->display_name;
    $Orders = new mensio_orders();
    if (!$Orders->Set_UUID($OrderID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order id is not correct';
    } else {
      $pdf = new TCPDF();
      $pdf->SetTitle('Invoice');
      $pdf->setPrintHeader(false);
      $pdf->setPrintFooter(false);
      $pdf->AddPage();
      $StData = $this->LoadStoreInfoForPDF();
      $pdf->SetFont('freeserif', '', 10); // set font
      $pdf->SetCellPadding(2);
      $pdf->SetFillColor(221,221,221); // Grey
      $pdf->Multicell(50, 17, '', 0, 'C', 1, 1, 10, 15,true);
      $pdf->Multicell(70, 17, '', 0, 'C', 1, 1, 68, 15,true);
      $pdf->SetFillColor(255,255,255); // white
      $pdf->SetXY(4, 6);
      $ImgType = substr($StData['Logo'], -3);
      $img = plugin_dir_path( __DIR__ ).'/orders/../../../../../../'.$StData['Logo'];
      $pdf->Image($img, 9, 14, 0, 14, $ImgType, '', 'T', false, 300, '', false, false, 0, false, false, false);
      $pdf->Multicell(50, 0, $StData['Info'], 1, 'R', 1, 1, 8, 13,true);
      $pdf->Multicell(70, 0, $StData['Contact'], 1, 'L', 1, 1, 66, 13,true);
      $OrderData = array();
      $DataSet = $Orders->GetOrderData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $OrderData['Serial'] = '#'.$Row->refnumber;
          $OrderData['Billing'] = "BILLING ADDRESS\n\n".$this->LoadAddressForPDF($Row->billingaddr);
          $OrderData['Shipping'] = "SHIPPING ADDRESS\n\n".$this->LoadAddressForPDF($Row->sendingaddr);
          $OrderData['ShippingType'] = $Row->shipping;
        }
      }
      $pdf->SetCellPadding(0);
      $pdf->SetFont('freeserif', 'B', 20); // set font
      $pdf->SetFillColor(221,221,221); // grey background
      $pdf->Multicell(50, 0, '', 0, 'C', 1, 1, 150, 15,true);
      $pdf->SetFillColor(0,0,0); // black background
      $pdf->SetTextColor(255,255,255); // white text
      $pdf->Multicell(50, 0, "INVOICE", 1, 'C', 1, 1, 148, 13,true);
      $pdf->SetFont('freeserif', '', 10); // set font
      $pdf->SetFillColor(255,255,255); // white background
      $pdf->SetTextColor(0,0,0); // black text
      $pdf->Multicell(0, 0, "Order: \nDate: ", 0, 'L', 1, 1, 150, 25,true);
      $pdf->Multicell(0, 0, $OrderData['Serial']."\n".date('Y-m-d'), 0, 'L', 1, 1, 160, 25,true);
      $OrderSerial = $OrderData['Serial'];
      $pdf->SetCellPadding(2);
      $pdf->SetFont('freeserif', '', 10); // set font
      $pdf->SetFillColor(221,221,221); // grey background
      $pdf->Multicell(69, 35, '', 0, 'C', 1, 1, 11, 40,true);
      $pdf->Multicell(69, 35, '', 0, 'C', 1, 1, 91, 40,true);
      $pdf->SetFillColor(255,255,255); // white background
      $pdf->SetTextColor(0,0,0); // black text
      $pdf->Multicell(70, 0, $OrderData['Billing'], 1, 'L', 1, 1, 8, 38,true);
      $pdf->Multicell(70, 0, $OrderData['Shipping'], 1, 'L', 1, 1, 88, 38,true);
      $pdf->SetCellPadding(0);
      $pdf->SetFillColor(221,221,221); // grey background
      $pdf->Multicell(190, 152, '', 0, 'C', 1, 1, 11, 82,true);
      $pdf->SetFillColor(255,255,255); // white background
      $pdf->SetTextColor(0,0,0); // black text
      $pdf->Multicell(191, 152, '', 1, 'L', 1, 1, 8, 80,true);
      $pdf->SetCellPadding(2);
      $pdf->SetFillColor(0,0,0); // black background
      $pdf->SetTextColor(255,255,255); // white text
      $pdf->SetFont('freeserif', 'B', 10); // set font
      $pdf->Multicell(20, 0, 'Code', 1, 'L', 1, 1, 8, 80,true);
      $pdf->Multicell(96, 0, 'Description', 1, 'L', 1, 1, 28, 80,true);
      $pdf->Multicell(15, 0, 'Qty', 1, 'C', 1, 1, 124, 80,true);
      $pdf->Multicell(15, 0, 'Price', 1, 'R', 1, 1, 139, 80,true);
      $pdf->Multicell(15, 0, 'Tax', 1, 'R', 1, 1, 154, 80,true);
      $pdf->Multicell(15, 0, 'Disc', 1, 'R', 1, 1, 169, 80,true);
      $pdf->Multicell(15, 0, 'Total', 1, 'R', 1, 1, 184, 80,true);
      $pdf->SetFillColor(255,255,255); // white background
      $pdf->SetTextColor(0,0,0); // black text
      $pdf->SetFont('freeserif', '', 8); // set font
      $DataSet = $Orders->LoadOrderProducts();
      $SubTotal = 0;
      $TotalTaxes = 0;
      $TotalDiscount = 0;
      $Payment = 0;
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $line = 88;
        foreach ($DataSet as $Row) {
          $SubTotal = $SubTotal + $Row->price;
          $TotalTaxes = $TotalTaxes + $Row->taxes;
          $TotalDiscount = $TotalDiscount + $Row->discount;
          $Payment = $Payment + $Row->fullprice;
          $pdf->Multicell(20, 12, $Row->code, 1, 'L', 0, 1, 8, $line,true);
          $pdf->Multicell(96, 12, $Row->name, 1, 'L', 0, 1, 28, $line,true);
          $pdf->Multicell(15, 12, ($Row->amount + 0), 1, 'C', 0, 1, 124, $line,true);
          $pdf->Multicell(15, 12, ($Row->price + 0).' '.$this->CurIcon, 1, 'R', 0, 1, 139, $line,true);
          $pdf->Multicell(15, 12, ($Row->taxes + 0).' '.$this->CurIcon, 1, 'R', 0, 1, 154, $line,true);
          $pdf->Multicell(15, 12, ($Row->discount + 0).' '.$this->CurIcon, 1, 'R', 0, 1, 169, $line,true);
          $pdf->Multicell(15, 12, ($Row->fullprice + 0).' '.$this->CurIcon, 1, 'R', 0, 1, 184, $line,true);
          $line = $line + 12;
        }
      }
      $Shipping = ($Orders->LoadOrderShipping() + 0);
      $Payment = $Payment + $Shipping;
      $pdf->SetCellPadding(0);
      $pdf->SetFont('freeserif', 'B', 10); // set font
      $PDFBody = "Sub Totals:\nTaxes:\nShipping:\nDiscount:\nPayment:\n";
      $pdf->Multicell(25, 0, $PDFBody, 0, 'R', 0, 1, 144, 240,true);
      $pdf->SetFont('freeserif', '', 10); // set font
      $PDFBody = "$SubTotal $this->CurIcon\n$TotalTaxes $this->CurIcon\n$Shipping $this->CurIcon\n$TotalDiscount $this->CurIcon\n$Payment $this->CurIcon\n";
      $pdf->Multicell(25, 0, $PDFBody, 0, 'R', 0, 1, 170, 240,true);
      $OrderSerial = str_replace('#', '', $OrderSerial);
      $file = fopen(MENSIO_UPLOAD_DIR.'/'.$OrderSerial.'.pdf', 'w');
      fclose($file);
      $UploadDir = wp_upload_dir();
      $pdf->Output($UploadDir['basedir'].'/mensiopress/'.$OrderSerial.'.pdf', 'F');
      unset($pdf);
    }
    unset($Orders);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    } else {
      $RtrnData['Name'] = MENSIO_UPLOAD_DIR.'/'.$OrderSerial.'.pdf';
    }
    return $RtrnData;
  }
  private function LoadStoreInfoForPDF() {
    $RtrnData = array('Logo'=>'','Info'=>'','Contact'=>'');
    $Store = new mensio_store();
    $DataSet = $Store->LoadStoreData();
    unset($Store);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $RtrnData['Logo'] = $Row->logo;
        $RtrnData['Info'] = "$Row->street $Row->number\n\n$Row->city";
        $RtrnData['Contact'] = "Phone: $Row->phone\nFax: $Row->fax\nE-Mail: $Row->email";
      }
    }
    return $RtrnData;
  }
  private function LoadAddressForPDF($Address) {
    $RtrnData = '';
    $Customers = new mensio_customers();
    if ($Customers->Set_Address($Address)) {
      $DataSet = $Customers->LoadAddressData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $CountryName = '';
          $Countries = new mensio_countries();
          if ($Countries->Set_UUID($Row->country)) {
            $CountryName = $Countries->GetCountryName();
          }
          unset($Countries);          
          $RtrnData = "Name: $Row->fullname\nCountry: $CountryName\nCity: $Row->city\nStreet: $Row->street $Row->zipcode\nPhone: $Row->phone";
        }
      }
    }
    unset($Customers); 
    return $RtrnData;
  }
  public function LoadSplitOrderFormModal($OrderID) {
    $MdlForm = '';
    $Serial = '';
    $Complete = false;
    $Orders = new mensio_orders();
    if ($Orders->Set_UUID($OrderID)) {
      $DataSet = $Orders->GetOrderData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Serial = $Row->serial;
          $Complete = $Row->complete;
        }
      }
      if (!$Complete) {
        $DataSet = $Orders->LoadOrderProducts();
        if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
          $PrimeProducts = '';
          foreach ($DataSet as $Row) {
            $PrimeProducts .= '
              <div id="'.$Row->product.'" class="MdlSplitPrimePrd">
                '.$Row->code.' --- '.$Row->name.'
              </div>';
          }
        }
        $EditForm = '
        <div class="mensio-panel-header">
          <label class="">Order '.$Serial.' products</label>
        </div>
        <input type="hidden" id="FLD_PrimeOrder" value="'.$OrderID.'">
        <input type="hidden" id="FLD_ActiveSubOrder" value="SubOrder1">
        <input type="hidden" id="FLD_SubOrder1" value="">
        <input type="hidden" id="FLD_SubOrder2" value="">
        <div id="MDL_Split_Prime" class="MldSpFrm">
          '.$PrimeProducts.'
        </div>
        <div class="mensio-panel-header">
          <label class="">New Orders</label>
        </div>
        <div id="MDL_Split_Sub" class="MldSpFrm">
          <div id="SubOrder1_Active" class="MDL_BTN_SetActive" title="Sub Order 1 Active">
            <i class="fa fa-check-square-o fa-lg" aria-hidden="true"></i>
          </div>
          <div id="SubOrder1_Clear" class="MDL_BTN_Clear" title="Clear">
            <i class="fa fa-eraser fa-lg" aria-hidden="true"></i>
          </div>
          <div id="SubOrder1" class="SubOrderList SOActive"></div>
          <div class="DivResizer"></div>
          <div id="SubOrder2_Active" class="MDL_BTN_SetActive" title="Sub Order 2 Active">
            <i class="fa fa-square-o fa-lg" aria-hidden="true"></i>
          </div>
          <div id="SubOrder2_Clear" class="MDL_BTN_Clear" title="Clear">
            <i class="fa fa-eraser fa-lg" aria-hidden="true"></i>
          </div>
          <div id="SubOrder2" class="SubOrderList"></div>
        </div>
        <div class="DivResizer"></div>
        <div class="button_row">
          <div id="MDL_BTN_SaveSplitOrders" class="MdlSplitInfoButton" title="Save">
            <i class="fa fa-floppy-o fa-2x" aria-hidden="true"></i>
          </div>
        </div>';
      } else {
          $EditForm = '<div class="SplitedOrder">Order '.$Serial.' is locked and can not be split</div>';
      }
      $MdlForm = '
      <div id="MDL_Split_Info" class="MDLSplitWrapper">
        <h2>Attention</h2>
        <p>By splitting an order you create two new orders with the products of the prime.</p>
        <p>That means the prime order will be locked with the status of "SPLIT ORDER" and the two new orders will be active</p>
        <p>The new orders will have all the basic data of the prime order (customer, billing address, shipping address etc) but the products will be split between them.
        After creating the new orders you can edit them as you may from the edit option of the orders table</p>
        <p class="BoldP">ARE YOU SURE YOU WANT TO SPLIT THE ORDER?</p>
        <div class="button_row">
          <div id="MDL_BTN_Split" class="MdlSplitInfoButton">
            <i class="fa fa-scissors" aria-hidden="true"></i>
            SPLIT ORDER
          </div>
          <div id="MDL_BTN_Cancel" class="MdlSplitInfoButton">
            <i class="fa fa-ban" aria-hidden="true"></i>
            CANCEL
          </div>
        </div>
      </div>
      <div id="MDL_Split_Form" class="MDLSplitWrapper">
        '.$EditForm.'
      </div>';
    }
    $MdlForm = $this->CreateModalWindow('Split Order', $MdlForm);
    return $MdlForm;
  }
  public function SaveSplitOrdersData($OrderID,$SOPrds1,$SOPrds2) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=> '');
    $NoteType = 'Success';
    $Orders = new mensio_orders();
    if (!$Orders->Set_UUID($OrderID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order id is not correct <br>';
    } else {
      $DataSet = $Orders->GetOrderData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Orders->Set_Customer($Row->customer);
          $Orders->Set_BlngAddress($Row->billingaddr);
          $Orders->Set_SendAddress($Row->sendingaddr);
          $Orders->Set_Shipping($Row->shipping);
        }
      }
      $tst = $Orders->AddNewSplitOrder($Row->refnumber.'-P1');
      if ($Orders->Set_SplitOrder($tst)) {
        $SOPrds1 = explode(';',$SOPrds1);
        foreach ($SOPrds1 as $ProductID) {
          if ($Orders->Set_Product($ProductID)) {
            if (!$Orders->AddSplitOrderProduct()) {
              $NoteType = 'Alert';
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Order Product cound not be moved from main order to split<br>';
            }
          } else {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Order Product ID not correct<br>';
          }
        }
      } else {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Split Order 1 ID not correct<br>'.$tst;
      }
      $tst = $Orders->AddNewSplitOrder($Row->refnumber.'-P2');
      if ($Orders->Set_SplitOrder($tst)) {
        $SOPrds2 = explode(';',$SOPrds2);
        foreach ($SOPrds2 as $ProductID) {
          if ($Orders->Set_Product($ProductID)) {
            if (!$Orders->AddSplitOrderProduct()) {
              $NoteType = 'Alert';
              $RtrnData['ERROR'] = 'TRUE';
              $RtrnData['Message'] .= 'Order Product cound not be moved from main order to split<br>';
            }
          } else {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Order Product ID not correct<br>';
          }
        }
      } else {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Split Order 2 ID not correct<br>'.$tst;
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        $Orders->SetSplitOrderStatus();
      }
    }
    unset($Orders);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $RtrnData['Message'] = 'Order was split successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;    
  }
  public function LoadOrderDiscountModal($OrderID) {
    $DiscRows = '';
    $DataSet = array();
    $Discounts = new mensio_orders_discounts();
    if ($Discounts->Set_Order($OrderID)) {
      $DataSet = $Discounts->LoadOrderDiscounts();
    }
    unset($Discounts);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      $Orders = new mensio_orders();
      if ($Orders->Set_UUID($OrderID)) {
        $Class = 'OddTblLine';
        foreach ($DataSet as $Row) {
          if ($Orders->Set_DiscountID($Row->uuid)) {
            if ($Orders->CheckOrdersDiscount()) {
              $Selectable = '<input type="checkbox" id="'.$Row->uuid.'" class="MDL_DiscCheck" value="1" checked>';
            } else {
              $Selectable = '<input type="checkbox" id="'.$Row->uuid.'" class="MDL_DiscCheck" value="0">';
            }
            $DiscRows .= '
              <tr class="'.$Class.'">
                <td>'.$Row->name.'</td>
                <td class="SmlCol">'.date("d/m/Y", strtotime($Row->dateend)).'</td>
                <td class="SmlCol ActStat">
                  '.$Selectable.'
                </td>
              </tr>';
            if ($Class === 'OddTblLine') { $Class = 'EvenTblLine'; }
              else { $Class = 'OddTblLine'; }
          }
        }
      }
      unset($Orders);
    }
    $MdlForm = '
    <div class="ModalCustomerSelector">
      <table class="ProductTable">
        <thead>
          <tr>
            <th>Discount</th>
            <th class="SmlCol">Expiration</th>
            <th class="SmlCol">Selected</th>
          </tr>
        </thead>
        <tbody id="OrderStatusHistory">
          '.$DiscRows.'
        </tbody>
      </table>
    </div>';
    return $this->CreateModalWindow('Discount Selection', $MdlForm);
  }
  public function UpdateOrdersDiscounts($OrderID,$DiscountID,$Active) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=> '');
    $NoteType = '';
    if (($Active === '0') || ($Active === '1')) {
      $Orders = new mensio_orders();
      if (!$Orders->Set_UUID($OrderID)) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Order id not correct<br>';
      }
      if (!$Orders->Set_DiscountID($DiscountID)) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Discount id not correct<br>';
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        if ($Active === '1') {
          if (!$Orders->AddOrderDiscount()) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Discount could NOT be added to the order<br>';
          }
        } else {
          if (!$Orders->RemoveOrderDiscount()) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Discount could NOT be removed from the order<br>';
          }
        }
      }
      unset($orders);
    } else {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Discount activation error<br>';
    }
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    } else {
      $RtrnData = $this->LoadOrderProducts($OrderID);
    }
    return $RtrnData;
  }
  public function LoadOrderPaymentData($OrderID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Payment'=>'');
    $NoteType = '';
    $Orders = new mensio_orders();
    if (!$Orders->Set_UUID($OrderID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order id not correct<br>';
    } else {
      $DataSet = $Orders->GetOrderPaymentData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Button = '';
          $SplitInfo = '';
          switch ($Row->answer) {
            case 'Pending':
              if (!$Row->complete) {
                $Button = '<div class="button_row">
                    <button id="BTN_UpdatePayment" class="button BtnGreen" title="Update Payment">
                      <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    </button>
                    </div';
              }
              break;
            case 'Split':
              $SplitData = $Orders->GetSplitOrderChildsData();
              if ((is_array($SplitData)) && (!empty($SplitData[0]))) {
                foreach ($SplitData as $Rec) {
                  $SplitInfo .= $Rec->serial.'<br>';
                }
                $SplitInfo = '<br><label class="label_symbol">Splited into the Orders</label><hr>'.$SplitInfo;
              }
              break;
          }
          $RtrnData['Payment'] = '<div class="PaymentInfoDiv">
              <label class="label_symbol">Payment Info</label>
              <hr>
              <span class="label_symbol PayInfoLbl">Type: </span>'.$Row->name.'<br>
              <span class="label_symbol PayInfoLbl">Status: </span>'.$Row->answer.'<br>
              '.$SplitInfo.'
            </div>'.$Button;
        }
      }
    }
    unset($Orders);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function UpdateOrderPaymentStatus($OrderID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Orders = new mensio_orders();
    if (!$Orders->Set_UUID($OrderID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order id not correct<br>';
    } else {
      if (!$Orders->UpdatePaymentStatus()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Order payment status cound not be updates<br>';
      }
    }
    unset($Orders);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function SendOrderStatusInfoMail($OrderID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Orders = new mensio_orders();
    if (!$Orders->Set_UUID($OrderID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order id not correct<br>';
    } else {    
      $RtrnData = $this->CreateStatusInfoMail($OrderID);
    }
    unset($Orders);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] .= 'Mail Send Successfully<br>';
    } else {
      $NoteType = 'Alert';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  private function CreateStatusInfoMail($OrderID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $From = '';
    $FromName = '';
    $RecNo = 1;
    $orders = new mensio_orders();
    if ($orders->Set_UUID($OrderID)) {
      $CstmrData = $orders->GetCustomerMailData();
      $StName = $orders->LoadActiveStatusName();
      $OrderNum = $orders->LoadOrderReferenceNumber();
      $SMTPSettings = explode(';;',$this->LoadMailSettings());
      global $phpmailer; // define the global variable
      if ( !is_object( $phpmailer ) || !is_a( $phpmailer, 'PHPMailer' ) ) { // check if $phpmailer object of class PHPMailer exists
        require_once ABSPATH . WPINC . '/class-phpmailer.php';
        require_once ABSPATH . WPINC . '/class-smtp.php';
        $phpmailer = new PHPMailer( true );
      }
      $phpmailer->CharSet = 'UTF-8';
      $phpmailer->isSMTP(); // Set mailer to use SMTP
      foreach ($SMTPSettings as $Setting) {
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
      if (($From !== '') && ($FromName !== '')) { $phpmailer->setFrom($From,$FromName); }
      $tbl = $this->LoadOrdersMailTemplate('Status');
      if ((is_array($CstmrData)) && (!empty($CstmrData[0]))) {
        foreach ($CstmrData as $Row) {
          $phpmailer->ClearAllRecipients( ); // clear all
          $phpmailer->addAddress($Row['Mail'], $Row['LName'].' '.$Row['FName']);
          $phpmailer->isHTML(true);
          $phpmailer->Subject = 'Update Status for Order :'.$OrderData['Number'];
          if ($Row['Title'] === 'NoTitle') { $Row['Title'] = ''; }
          $body = str_replace('[%TITLE%]',$Row['Title'],$tbl);
          $body = str_replace('[%LASTNAME%]',$Row['LName'],$body);
          $body = str_replace('[%FIRSTNAME%]',$Row['FName'],$body);
          $body = str_replace('[%ORDERNUMBER%]',$OrderNum,$body);
          $body = str_replace('[%STATUSNAME%]',$StName,$body);
          $phpmailer->Body = $body;
          $phpmailer->AltBody = wp_strip_all_tags($body);
          if ($phpmailer->send()) { $MailsSend = true; } else { $MailsSend = false; }
        }
      }
      if (!$MailsSend) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Status E-Mail could not be send<br>';
      }
    }
    unset($orders);
    return $RtrnData;
  }
  public function SendOrderInvoiceMail($OrderID) {
    $MailsSend = false;
    $NoteType = '';
    $From = '';
    $FromName = '';
    $RecNo = 1;
    $RtrnData = $this->LoadOrderInvoice($OrderID);
    $OrderData = $this->LoadOrderBascData($OrderID);
    if (($RtrnData['ERROR'] === 'FALSE') && ($OrderData['ERROR'] === 'FALSE')) {
      $SMTPSettings = explode(';;',$this->LoadMailSettings());
      $Orders = new mensio_orders();
      if ($Orders->Set_UUID($OrderID)) { $CstmrData = $Orders->GetCustomerMailData(); }
      unset($Orders);
      $mail = new MNS_PHPMailer(); // Passing `true` enables exceptions
      $phpmailer->CharSet = 'UTF-8';
      $phpmailer->isSMTP(); // Set mailer to use SMTP
      foreach ($SMTPSettings as $Setting) {
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
      if (($From !== '') && ($FromName !== '')) { $phpmailer->setFrom($From,$FromName); }      
      $tbl = $this->LoadOrdersMailTemplate('Sales');
      if ((is_array($CstmrData)) && (!empty($CstmrData[0]))) {
        $Crntusr = wp_get_current_user();
        foreach ($CstmrData as $Row) {
          $phpmailer->ClearAllRecipients( ); // clear all
          $phpmailer->addAddress($Row['Mail'], $Row['LName'].' '.$Row['FName']);
          $phpmailer->isHTML(true);
          $phpmailer->Subject = 'Invoice for Order :'.$OrderData['Number'];
          $Path = MENSIO_UPLOAD_DIR.'/'.$Crntusr->display_name.'display.pdf';
          $body = str_replace('[%STORENAME%]',$FromName,$tbl);
          $body = str_replace('[%STOREMAIL%]',$From,$body);
          $body = str_replace('[%ORDERNUMBER%]',$OrderData['Number'],$body);
          $phpmailer->Body = $body;
          $phpmailer->AltBody = wp_strip_all_tags($body);
          $phpmailer->AddAttachment($Path, '', 'base64', 'application/pdf');
          if ($phpmailer->send()) { $MailsSend = true; } else { $MailsSend = false; }
        }
      }
    }
    if (!$MailsSend) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'E-Mail could not be send<br>';
    } else {
      $NoteType = 'Success';
      $RtrnData['Message'] .= 'E-Mail was send successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  private function LoadOrdersMailTemplate($Name) {
    $Template = '';
    $Orders = new mensio_orders();
    $Template = $Orders->LoadOrdersMailTemplate($Name);
    unset($Orders);
    if ($Template === '') { $Template = $this->DefaultMailTemplate($Name); }
    return $Template;
  }
  private function LoadOrderBascData($OrderID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Order'=>'','Customer'=>'','Number'=>'');
    $NoteType = '';
    $Orders = new mensio_orders();
    if (!$Orders->Set_UUID($OrderID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order id not correct<br>';
    } else {
      $DataSet = $Orders->GetOrderData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData['Order'] = $Row->uuid;
          $RtrnData['Customer'] = $Row->customer;
          $RtrnData['Number'] = $Row->refnumber;
        }
      }
    }
    unset($Orders);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadOrderPaymentsModal($OrderID) {
    $Payment = '';
    $Options = '';
    $DataSet = array();
    $Orders = new mensio_orders();
    if ($Orders->Set_UUID($OrderID)) {
      $DataSet = $Orders->GetOrderPaymentData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Payment = $Row->payment;
        }
      }
      $DataSet = $Orders->GetActivePaymentMethods();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Options .= '<option value="'.$Row->uuid.'">'.$Row->name.'</option>';
        }
      }
    }
    unset($Orders);
    if ($Payment !== '') {
      $Options = str_replace('value="'.$Payment.'"','value="'.$Payment.'" selected',$Options);
    }
    $MdlForm = '
    <div class="ModalCustomerSelector">
      <label class="label_symbol">Select Payment Type</label>
      <select id="MDL_PaymentSelect" class="form-control">
        '.$Options.'
      </select>
      <p class="paymentinfo">We give you the ability to change the type of payment of the order but please be very carefull while doing so.</p>
      <div class="button_row">
        <button id="MDL_BTN_SetPaymentType" class="button" title="Save">
          <i class="fa fa-floppy-o" aria-hidden="true"></i>
        </button>
      </div>
    </div>';
    return $this->CreateModalWindow('Payment Type Selection', $MdlForm);
  }
  public function UpdateOrderPaymentTypeValues($OrderID,$PaymentID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Orders = new mensio_orders();
    if (!$Orders->Set_UUID($OrderID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order id not correct<br>';
    }
    if (!$Orders->Set_Payment($PaymentID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Payment type not correct<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Orders->UpdateOrderPayment()) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Order payment could not be updated<br>';
      } else {
        $NoteType = 'Success';
        $RtrnData['Message'] .= 'Order Payment Updated successfully<br>';
      }
    }
    unset($Orders);
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
}
