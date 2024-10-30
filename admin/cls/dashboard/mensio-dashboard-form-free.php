<?php
class Mensio_Admin_DashBoard_Form extends mensio_core_form {
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->ActivePage = 'DashBoard';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-dashboard',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-admin-dashboard.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-dashboard',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-admin-dashboard.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  public function GetStdDashboardElements() {
    $Page = new mensio_dashboard();
    $SalesData = $this->GetNewOrdersList();
    $CstmrsData = $this->GetNewCustomerList();
    $TicketsData = $this->GetNewTicketsList();
    $StatData = $this->GetStatData();
    $RtrnData = '<div id="InfoDiv" class="ProductTab">
                  <div class="InfoPanel">
                    <div class="InfoPanelHead">
                      <div class="InfoPanelIcon">
                        <i class="fa fa-shopping-cart fa-lg" aria-hidden="true"></i>
                      </div>
                      Latest Sales
                    </div>
                    <div id="SalesList" class="InfoPanelBody">
                      '.$SalesData['Elements'].'
                    </div>
                    <div id="InfoSalesFooter" class="InfoPanelFoot">
                      <span class="InfoSalesFooterLabel">Total Entries :</span> '.$SalesData['Totals'].'
                    </div>
                  </div>
                  <div class="InfoPanel">
                    <div class="InfoPanelHead">
                      <div class="InfoPanelIcon">
                        <i class="fa fa-users fa-lg" aria-hidden="true"></i>
                      </div>
                      Latest Registrations
                    </div>
                    <div id="CstmrList" class="InfoPanelBody">
                      '.$CstmrsData['Elements'].'
                    </div>
                    <div id="InfoCustomersFooter" class="InfoPanelFoot">
                      <span class="InfoSalesFooterLabel">Total Entries :</span> '.$CstmrsData['Totals'].'
                    </div>
                  </div>
                  <div class="InfoPanel">
                    <div class="InfoPanelHead">
                      <div class="InfoPanelIcon">
                        <i class="fa fa-envelope-open fa-lg" aria-hidden="true"></i>
                      </div>
                      Latest Tickets
                    </div>
                    <div id="TicketsList" class="InfoPanelBody">
                      '.$TicketsData['Elements'].'
                    </div>
                    <div id="InfoTicketsFooter" class="InfoPanelFoot">
                      <span class="InfoSalesFooterLabel">Total Entries :</span> '.$TicketsData['Totals'].'
                    </div>
                  </div>
                  <div class="InfoPanel">
                    <div class="InfoPanelHead">
                      <div class="InfoPanelIcon">
                        <i class="fa fa-line-chart fa-lg" aria-hidden="true"></i>
                      </div>
                      Statistics [Last 7 Days]
                    </div>
                    <div id="ChartsList" class="InfoPanelBody">
                      '.$StatData.'
                    </div>
                    <div id="InfoChartsFooter" class="InfoPanelFoot">
                    </div>
                  </div>
                  <div class="DivResizer"></div>
                </div>';
    unset($Page);
    return $RtrnData;
  }
  private function GetNewOrdersList() {
    $RtrnData = array('Elements'=>'
        <div class="infobubble">
          <div class="BubbleWrapper">
            <div class="BubbleIconDiv">
              <i class="fa fa-shopping-cart fa-3x" aria-hidden="true"></i>
            </div>
            <div class="BubbleTextDiv">
              <p>Here you will see all new sales requests</p>
              <p>No new sales found until '.$this->ConvertDateToTimezone(date('H:i:s'), 'H:i:s').'</p>
            </div>
          </div>
        </div>',
        'Totals'=>0
    );
    $Dashboard = new mensio_dashboard();
    $DataSet = $Dashboard->GetNewMensioLogs('Orders');
    unset($Dashboard);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      $RtrnData['Elements'] = '';
      foreach ($DataSet as $Row) {
        $log = str_replace('id="XXXX"', 'id="SalesCode_'.$RtrnData['Totals'].'"', $Row->log);
        $RtrnData['Elements'] .= '
                      <div id="Sales_'.$Row->code.'_Element" class="InfoElement">
                        <div class="InfoElementLog">'.$log.'</div>
                        <div class="InfoElementCtrl">
                          <div id="Sales_'.$RtrnData['Totals'].'_Info" class="InfoElementButton InfoElementButtonMore" title="More">
                            <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
                          </div>
                          <div id="Sales_'.$Row->code.'_Close" class="InfoElementButton InfoElementButtonClose" title="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                          </div>
                        </div>
                        <div class="DivResizer"></div>
                      </div>';
        ++$RtrnData['Totals'];
      }
    }
    return $RtrnData;
  }
  private function GetNewCustomerList() {
    $RtrnData = array('Elements'=>'
        <div class="infobubble">
          <div class="BubbleWrapper">
            <div class="BubbleIconDiv">
              <i class="fa fa-users fa-3x" aria-hidden="true"></i>
            </div>
            <div class="BubbleTextDiv">
              <p>New customer registrations will be displayed here</p>
              <p>No new registrations found until '.$this->ConvertDateToTimezone(date('H:i:s'), 'H:i:s').'</p>
            </div>
          </div>
        </div>',
        'Totals'=>0
    );
    $Dashboard = new mensio_dashboard();
    $DataSet = $Dashboard->GetNewMensioLogs('Customers');
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      $RtrnData['Elements'] = '';
      foreach ($DataSet as $Row) {
        $log = explode(' ', $Row->log);
        $Customer = $Dashboard->GetCredentialData($log[0]);
        $RtrnData['Elements'] .= '
                      <div id="Customers_'.$Row->code.'_Element" class="InfoElement">
                        <div class="InfoElementLog">
                          <span class="InfoElementLabel">Customer :</span> <span id="CustomersCode_'.$RtrnData['Totals'].'">'.$Customer['Username'].'</span><br>
                          <span class="InfoElementLabel">Registered :</span> '.$Customer['Date'].'<br>
                          <span class="InfoElementLabel">Name :</span> '.$Customer['Name'].'<br>
                        </div>
                        <div class="InfoElementCtrl">
                          <div id="Customers_'.$RtrnData['Totals'].'_Info" class="InfoElementButton InfoElementButtonMore" title="More">
                            <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
                          </div>
                          <div id="Customers_'.$Row->code.'_Close" class="InfoElementButton InfoElementButtonClose" title="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                          </div>
                        </div>
                        <div class="DivResizer"></div>
                      </div>';
        ++$RtrnData['Totals'];
      }
    }
    unset($Dashboard);
    return $RtrnData;
  }
  private function GetNewTicketsList() {
    $RtrnData = array('Elements'=>'
        <div class="infobubble">
          <div class="BubbleWrapper">
            <div class="BubbleIconDiv">
              <i class="fa fa-envelope-open fa-3x" aria-hidden="true"></i>
            </div>
            <div class="BubbleTextDiv">
              <p>This area will display all support request from customers</p>
              <p>No new support requests until '.$this->ConvertDateToTimezone(date('H:i:s'), 'H:i:s').'</p>
            </div>
          </div>
        </div>',
        'Totals'=>0
    );
    $Dashboard = new mensio_dashboard();
    $DataSet = $Dashboard->GetNewMensioLogs('Tickets');
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      $RtrnData['Elements'] = '';
      foreach ($DataSet as $Row) {
        $log = str_replace('id="XXXX"', 'id="TicketsCode_'.$RtrnData['Totals'].'"', $Row->log);
        $RtrnData['Elements'] .= '
                      <div id="Tickets_'.$Row->code.'_Element" class="InfoElement">
                        <div class="InfoElementLog">'.$log.'</div>
                        <div class="InfoElementCtrl">
                          <div id="Tickets_'.$RtrnData['Totals'].'_Info" class="InfoElementButton InfoElementButtonMore" title="More">
                            <i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
                          </div>
                          <div id="Tickets_'.$Row->code.'_Close" class="InfoElementButton InfoElementButtonClose" title="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                          </div>
                        </div>
                        <div class="DivResizer"></div>
                      </div>';
        ++$RtrnData['Totals'];
      }
    }
    unset($Dashboard);
    return $RtrnData;
  }
  private function GetStatData() {
    $Dashboard = new mensio_dashboard();
    $DataSet = $Dashboard->GetVisitsToSalesData();
    $StatData['Guests'] = 0;
    $StatData['Customers'] = 0;
    $StatData['SalesRatio'] = 0;
    $StatData['Sales'] = 0;
    $StatData['SVRatio'] = 0;
    $StatData['VisitsRatio'] = 0;
    if ($DataSet['Visits'] > 0) {
      $StatData['Visits'] = $DataSet['Visits'];
      $StatData['SalesRatio'] = round((($DataSet['Visits'] / $DataSet['TotalVisits']) * 100),2);
      $StatData['Sales'] = $DataSet['Sales'];
      $StatData['Visits'] = $DataSet['Visits'];
      $diff = $DataSet['Visits'] - $DataSet['Sales'];
      if ($diff > 0) { $StatData['SVRatio'] = round((($DataSet['Sales'] / $DataSet['Visits']) * 100),2); }
        else { $StatData['SVRatio'] = 0; }
      $StatData['VisitsRatio'] = 100 - $StatData['SVRatio'];
      $DataSet = $Dashboard->GetGuestsToCustomersData();
      $StatData['Guests'] = round((($DataSet['Guests'] / $StatData['Visits']) * 100),2);
      $StatData['Customers'] = round((($DataSet['Customers'] / $StatData['Visits']) * 100),2);
    }
    unset($Dashboard);
    $VR = 'width: '.$StatData['VisitsRatio'].'%;';
    if ($StatData['VisitsRatio'] === 0) { $VR .= 'background: white;'; }
    $VtS = 'width: '.$StatData['SVRatio'].'%;';
    if ($StatData['SVRatio'] === 0) { $VtS .= 'background: white;'; }
    $GRt = 'width: '.$StatData['Guests'].'%;';
    if ($StatData['Guests'] === 0) { $GRt .= 'background: white;'; }
    $CstRt = 'width: '.$StatData['Customers'].'%;';
    if ($StatData['Customers'] === 0) { $CstRt .= 'background: white;'; }
    $RtrnData = '<label class="label_symbol">Visits Ratio [ '.$StatData['VisitsRatio'].'% ]</label>
                      <div class="StaticChartbar">
                        <div class="StaticChartbarFiller FillerRed" style="'.$VR.'"></div>
                      </div>
                      <label class="label_symbol">Visits To Sales Ratio [ '.$StatData['SVRatio'].'% ]</label>
                      <div class="StaticChartbar">
                        <div class="StaticChartbarFiller FillerGreen" style="'.$VtS.'"></div>
                      </div>
                      <label class="label_symbol">Guests Ratio [ '.$StatData['Guests'].'% ]</label>
                      <div class="StaticChartbar">
                        <div class="StaticChartbarFiller FillerBlue" style="'.$GRt.'"></div>
                      </div>
                      <label class="label_symbol">Customers Ratio [ '.$StatData['Customers'].'% ]</label>
                      <div class="StaticChartbar">
                        <div class="StaticChartbarFiller FillerYellow" style="'.$CstRt.'"></div>
                      </div>';
    return $RtrnData;
  }
  public function LoadDashboardInfoModal($Type,$ID) {
    $Title = '';
    $ModalBody = '';
    switch($Type) {
      case 'Sales':
        $Title = 'Order '.$ID.' Details';
        $ModalBody = $this->LoadSalesModal($ID);
        break;
      case 'Customers':
        $Title = 'Customer '.$ID.' Details';
        $ModalBody = $this->LoadCustomersModal($ID);
        break;
      case 'Tickets':
        $Title = 'Ticket '.$ID.' Details';
        $ModalBody = $this->LoadTicketsModal($ID);
        break;
      default:
        $Title = 'ERROR';
        $ModalBody = 'No Data Found';
        break;
    }
    return $this->CreateModalWindow($Title,$ModalBody);
  }
  private function LoadSalesModal($ID) {
    $RtrnForm = '';
    $Dashboard = new mensio_dashboard();
    $DataSet = $Dashboard->GetSalesData($ID);
    unset($Dashboard);
    if (is_array($DataSet)) {
      $ProdData = explode('||', $DataSet['Products']);
      $Products = '<table class="InfoTable"><thead><tr><th>Product</th><th>Amount</th></tr></thead><tbody>';
      foreach ($ProdData as $Row) {
        $Prds = explode('::', $Row);
        $Products .= '<tr><td>'.$Prds[0].'</td><td>'.$Prds[1].'</td></tr>';
      }
      $Products .= '</tbody></table>';
      $RtrnForm = '
        <span class="InfoElementLabel">Ref. ID:</span> '.$DataSet['refnumber'].'<br>
        <span class="InfoElementLabel">Created:</span> '.$DataSet['created'].'<br>
        <span class="InfoElementLabel">Customer:</span> '.$DataSet['customer'].'<br>
        <span class="InfoElementLabel">Status:</span> '.$DataSet['status'].'<br>
        '.$Products;
    }
    return $RtrnForm;
  }
  private function LoadCustomersModal($ID) {
    $RtrnForm = '';
    $Dashboard = new mensio_dashboard();
    $DataSet = $Dashboard->GetCustomerData($ID);
    unset($Dashboard);
    if (is_array($DataSet)) {
      $RtrnForm = '
        <span class="InfoElementLabel">Username:</span> '.$DataSet['username'].'<br>
        <span class="InfoElementLabel">Last Name:</span> '.$DataSet['lastname'].'<br>
        <span class="InfoElementLabel">First Name:</span> '.$DataSet['firstname'].'<br>
        <span class="InfoElementLabel">Country:</span> '.$DataSet['country'].'<br>
        <span class="InfoElementLabel">City:</span> '.$DataSet['city'].'<br>
        <span class="InfoElementLabel">Street:</span> '.$DataSet['street'].'<br>
        <span class="InfoElementLabel">Zip Code:</span> '.$DataSet['zipcode'].'<br>
        <span class="InfoElementLabel">Phone:</span> '.$DataSet['phone'].'<br>';
    }
    return $RtrnForm;
  }
  private function LoadTicketsModal($ID) {
    $RtrnForm = '';
    $Dashboard = new mensio_dashboard();
    $DataSet = $Dashboard->GetTicketData($ID);
    unset($Dashboard);
    if (is_array($DataSet)) {
      $RtrnForm = '
        <span class="InfoElementLabel">Username:</span> '.$DataSet['username'].'<br>
        <span class="InfoElementLabel">Ticket:</span> '.$DataSet['ticket_code'].'<br>
        <span class="InfoElementLabel">Date:</span> '.$DataSet['date'].'<br>
        <span class="InfoElementLabel">Title:</span> '.$DataSet['title'].'<br>
        <div class="TicketText">'.$DataSet['content'].'</div>';
    }
    return $RtrnForm;
  }
  public function UpdateDashboardInformedInfo($ID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    if (is_integer(intval($ID))) {
      $Dashboard = new mensio_dashboard();
      if (!$Dashboard->UpdateMensioLogsInformed($ID)) {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Problem with updating info table<br>';
      }
      unset($Dashboard);
    } else {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Problem with id given<br>';
    }
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
}