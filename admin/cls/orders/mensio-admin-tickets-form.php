<?php
class Mensio_Admin_Tickets_Form extends mensio_core_form {
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->ActivePage = 'Tickets';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-tickets',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-admin-tickets.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-tickets',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-admin-tickets.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function LoadTicketsDataSet($InSearch,$InSorter) {
    $Error = false;
    $RtrnData = array();
    $Tickets = new mensio_tickets();
    if ($InSearch !== '') {
      if (!$Tickets->Set_SearchString($InSearch)) { $Error = true; }
    }
    if (!$Tickets->Set_Sorter($InSorter)) { $Error = true; }
    if (!$Error) {
      $DataSet = $Tickets->LoadTicketsDataSet();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $i = 0;
        foreach ($DataSet as $Row) {
          $RtrnData[$i]['uuid'] = $Row->uuid;
          $RtrnData[$i]['ticket_code'] = $Row->ticket_code;
          $RtrnData[$i]['customer'] = $Row->lastname.' '.$Row->firstname;
          $RtrnData[$i]['dateadded'] = $this->ConvertDateToTimezone($Row->dateadded);
          $RtrnData[$i]['title'] = $Row->title;
          $RtrnData[$i]['closed'] = '<i class="fa fa-envelope-open fa-lg" aria-hidden="true"></i>';
          if ($Row->closed) {
            $RtrnData[$i]['closed'] = '<i class="fa fa-envelope fa-lg" aria-hidden="true"></i>';
          }
          ++$i;
        }
      }
    }
    unset($Tickets);
    return $RtrnData;
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='dateadded DESC',$InSearch='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    $tbl->Set_Sorter($InSorter);
    $DataSet = $this->LoadTicketsDataSet($InSearch,$InSorter);
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array());
    $tbl->Set_EditColumn('title');
    $tbl->Set_EditOptionsSubline(array(
      'Edit'
    ));
    $tbl->Set_Columns(array(
      'uuid:uuid:plain-text',
      'ticket_code:Ticket:plain-text',
      'customer:Customer:plain-text',
      'dateadded:Committed:plain-text',
      'title:Title:plain-text',
      'closed:Closed:small'
    ));
    $RtrnTable = $tbl->CreateTable(
      'Tickets',
      $DataSet,
      array('uuid','ticket_code','title','customer','dateadded','closed')
    );
    unset($tbl);
    return $RtrnTable;
  }
  public function LoadTicketHistoryData($TicketID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','TicketForm'=>'');
    $NoteType = 'Success';
    $DataSet = array();
    $FLDTicket = '';
    $FLDCustomer = '';
    $FLDTicketCode = '';
    $FLDDateCreated = '';
    $FLDDateClose = '';
    $FLDTitle = '';
    $FLDText = '';
    $FLDStatus = '';
    $FLDName = '';
    $FLDUName = '';
    $Disabled =  '';
    $HstrData = '';
    $RelOrder = '';
    $Tickets = new mensio_tickets();
    if (!$Tickets->Set_UUID($TicketID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with ticket code<br>';
    } else {
      $DataSet = $Tickets->LoadTicketData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $FLDTicket = $Row->uuid;
          $FLDCustomer = $Row->customer;
          $FLDTicketCode = $Row->ticket_code;
          $FLDDateCreated = $this->ConvertDateToTimezone($Row->dateadded);
          $FLDDateClose = $this->ConvertDateToTimezone($Row->dateclosed);
          $FLDTitle = $Row->title;
          $FLDText = $Row->content;
          $FLDStatus = 'Open';
          if ($Row->closed) {
            $FLDStatus = 'Closed';
            $Disabled = 'disabled';
          }
          $FLDName = $Row->lastname.' '.$Row->firstname;
          $FLDUName = $Row->username;
        }
      }
      $DataSet = $this->LoadTicketHistory($TicketID);
      if ($DataSet['ERROR'] === 'FALSE') {
        $HstrData = $DataSet['TicketHistory'];
      } else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= $DataSet['Message'];
      }
      $DataSet = $Tickets->LoadTicketRelOrder();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RelOrder = '<div class="RelOrderLine">
            <input type="hidden" id="RelOrder" value="'.$Row->uuid.'">
            <button id="BTN_ShowRelOrder" class="button" title="Display Order Info">
              <i class="fa fa-eye" aria-hidden="true"></i>
            </button> '.$Row->serial.'</div>';
        }
      } else {
        $RelOrder = 'No Order Specific';
      }
    }
    unset($Tickets);
    $RtrnData['TicketForm'] = '
      <div class="TicketDataDiv">
        <input type="hidden" id="FLD_Ticket" value="'.$FLDTicket.'">
        <input type="hidden" id="FLD_Customer" value="'.$FLDCustomer.'">
        <div class="TicketStaticData">
          <table class="TicketDataTbl">
            <tr>
              <td class="LblCol">Customer</td>
              <td>'.$FLDName.'</td>
            </tr>
            <tr>
              <td class="LblCol">User Name</td>
              <td>'.$FLDUName.'</td>
            </tr>
          </table>
        </div>
        <div class="TicketStaticData">
          <table  class="TicketDataTbl">
            <tr>
              <td class="LblCol">Ticket Code</td>
              <td>'.$FLDTicketCode.'</td>
            </tr>
            <tr>
              <td class="LblCol">Committed</td>
              <td>'.$FLDDateCreated.'</td>
            </tr>
          </table>
        </div>
        <div class="TicketStaticData StcRelIrder">
          <table  class="TicketDataTbl">
            <tr>
              <td class="LblCol">Related Order</td>
              <td>'.$RelOrder.'</td>
            </tr>
            <tr>
              <td class="LblCol">Status</td>
              <td>'.$FLDStatus.'</td>
            </tr>
          </table>
        </div>
        <div class="TicketStaticDataBtns">
        </div>
        <div class="DivResizer"></div>
        <div class="TicketTextData">
          <span class="TicketTitleSpan">Ticket Title :</span> '.$FLDTitle.'<br>
          <hr>
          '.$FLDText.'
        </div>
        <div class="DivResizer"></div>
        <div id="TicketHistoryData">
          '.$HstrData.'
        </div>
      </div>
      <div class="button_row">
        <button id="BTN_Reply" class="button BtnGreen" title="Reply to the ticket" '.$Disabled.'>
          <i class="fa fa-reply" aria-hidden="true"></i>
        </button>
        <button id="BTN_Close" class="button BtnRed" title="Close the ticket" '.$Disabled.'>
          <i class="fa fa-lock" aria-hidden="true"></i>
        </button>
        <button id="BTN_Back" class="button" title="Back">
          <i class="fa fa-arrow-left" aria-hidden="true"></i>
        </button>
      </div>';
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadTicketHistory($TicketID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','TicketHistory'=>'');
    $NoteType = 'Success';
    $DataSet = array();
    $Tickets = new mensio_tickets();
    if (!$Tickets->Set_UUID($TicketID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with ticket code<br>';
    } else {
      $DataSet = $Tickets->LoadTicketHistory();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Author = '';
          if ($Tickets->Set_ReplyAuthor($Row->replyauthor)) { $Author = $Tickets->FindReplyAuthorName(); }
          $RtrnData['TicketHistory'] .= '
            <div class="TckHstrElem">
              <table  class="TicketDataTbl">
                <tr>
                  <td class="LblCol">Posted By</td>
                  <td class="DtCol">'.$Author.'</td>
                  <td class="LblCol">Posted On</td>
                  <td class="DtCol">'.$this->ConvertDateToTimezone($Row->replydate).'</td>
                </tr>
              </table>
              <hr>
              '.$Row->replytext.'
            </div>';
        }
        $RtrnData['TicketHistory'] .= '<div class="DivResizer"></div>';
      }
    }
    unset($Tickets);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function LoadTicketOrderViewModal($OrderID) {
    $ModalBody = '';
    $Orders = new Mensio_Admin_Orders_Form();
    $Data = $Orders->LoadOrderData($OrderID);
    unset($Orders);
    $Products = $this->LoadOrderProducts($OrderID);
    $Status = $this->ModalViewOrderStatusHistory($OrderID);
    $ModalBody = '
    <div id="tabs">
      <ul>
        <li><a href="#OrderInfoDiv">Address</a></li>
        <li><a href="#OrderProductsDiv">Products</a></li>
        <li><a href="#OrderStatusDiv">Status</a></li>
      </ul>
      <div id="OrderInfoDiv">
        <div class="AddrDiv AddrDivLeft">
          <label class="label_symbol">Billing Address</label>
          <hr>
          '.$Data['BAData'].'
        </div>
        <div class="AddrDiv AddrDivRight">
          <label class="label_symbol">Shipping Address</label>
          <hr>
          '.$Data['SAData'].'
        </div>
      </div>
      <div id="OrderProductsDiv">
        <div class="PrdDiv">
          <table class="ProductTable">
            <thead>
              <tr>
                <th class="PrdName">Product</th>
                <th class="SmlCol">Amount</th>
                <th class="SmlCol">Total Price</th>
              </tr>
            </thead>
            <tbody id="OrderStatusHistory">
              '.$Products.'
            </tbody>
          </table>
        </div>
      </div>
      <div id="OrderStatusDiv">
        '.$Status.'
      </div>
    </div>';
    return $this->CreateModalWindow('Order '.$Data['Serial'].' Data',$ModalBody);
  }
  public function LoadOrderProducts($OrderID) {
    $RtrnData = '';
    $Orders = new mensio_orders();
    if ($Orders->Set_UUID($OrderID)) {
      $DataSet = $Orders->LoadOrderProducts();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $RowClass = 'OddTblLine';
        foreach ($DataSet as $Row) {
          $RtrnData .= '
                <tr class="'.$RowClass.'">
                <td class="PrdName">
                  '.$Row->code.' - '.$Row->name.'</td>
                <td class="SmlCol">
                  '.($Row->amount + 0).'
                </td>
                <td class="SmlCol">
                  '.($Row->fullprice + 0).'
                </td>
              </tr> ';
        }
      }
    }
    unset($Orders);
    return $RtrnData;
  }
  public function ModalViewOrderStatusHistory($OrderID) {
    $OrderStatus = '';
    $Orders = new Mensio_Admin_Orders_Form();
    $Data = $Orders->LoadStatusTag($OrderID);
    unset($Orders);
    $OrderStatus = '<div>
      <table class="ProductTable">
        <thead>
          <tr>
            <th>Status</th>
            <th class="SmlCol">Date</th>
            <th class="SmlCol">Time</th>
            <th class="SmlCol">Active</th>
          </tr>
        </thead>
        <tbody id="OrderStatusHistory">
          '.$Data['StatusTag'].'
        </tbody>
      </table>
    </div>';
    return $OrderStatus;
  }
  public function LoadTicketReplyForm() {
    $ModalBody = '
      <div class="">
        <textarea id="MDL_ReplyText"></textarea>
        <div class="DivResizer"></div>
        <div class="button_row">
          <button id="BTN_Save" class="button BtnGreen" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>
        </div>
        <div class="DivResizer"></div>
      </div>';
    return $this->CreateModalWindow('Reply Form',$ModalBody);
  }
  public function SendTicketReply($TicketID,$CustomerID,$Reply) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','TicketHistory'=>'');
    $NoteType = 'Success';
    $CstmrMail = $this->LoadCustomerEMailList($CustomerID);
    if ($CstmrMail === '') {
      $NoteType = 'Info';
      $RtrnData['Message'] .= 'Customer Has No E-Mails or E-Mail is not validated<br>
        Reply Could not been send<br>
        It can be viewed from the user ticket info tab<br>';
    }
    $Tickets = new mensio_tickets();
    if (!$Tickets->Set_UUID($TicketID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with ticket code<br>';
    }
    if(!$Tickets->Set_ReplyAuthor('ADMINUSER-'.get_current_user_id())) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with reply text author given<br>';
    }
    if (!$Tickets->Set_ReplyText($Reply)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with reply text given<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $TcketCode = '';
      $Data = $Tickets->LoadTicketData();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $TcketCode = $Row->ticket_code;
          $Name = $Row->lastname.';;'.$Row->firstname.';;'.$Row->username;
        }
      }
      if (!$Tickets->UpdateTicketHistory()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Ticket Reply could not be Updated<br>';
      } else {
        if ($CstmrMail !== '') {
          if (!$this->SendCustomerReplyEMail($Name,$CstmrMail,$TcketCode,$Reply)) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] = 'Mail Could NOT be send at '.$CstmrMail.' for ticket '.$TcketCode;
          }
        }
      }
    }
    unset($Tickets);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Msg = $RtrnData['Message'] ;
      $RtrnData = $this->LoadTicketHistory($TicketID);
      $RtrnData['Message'] .= $Msg.'Reply Saved Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  private function LoadCustomerEMailList($CustomerID) {
    $CstmrMail = '';
    $Customers = new mensio_customers();
    if ($Customers->Set_UUID($CustomerID)) {
      $DataSet = $Customers->LoadCustomerContact();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          if (($Row->name ==='E-Mail') && ($Row->validated)) {
            if ($CstmrMail === '') { $CstmrMail = $Row->value; }
              else { $CstmrMail .= '::'.$Row->value; }
          }
        }
      }
    }
    unset($Customers,$DataSet);
    return $CstmrMail;
  }
  private function SendCustomerReplyEMail($CstmrName,$MailList,$TcketCode,$Reply) {
    $SMTPSettings = explode(';;',$this->LoadMailSettings());
    $CstmrName = explode(';;',$CstmrName);
    $MailsSend = false;
    $From = '';
    $FromName = '';
    $RecNo = 1;
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
    $tbl = $this->LoadTicketsMailTemplate();
    $MailList = explode('::',$MailList);
    if ((is_array($MailList)) && (!empty($MailList[0]))) {
      foreach ($MailList as $Mail) {
        $phpmailer->ClearAllRecipients( ); // clear all
        $phpmailer->addAddress($Mail, $CstmrName[0].' '.$CstmrName[1]);
        $phpmailer->isHTML(true);
        $phpmailer->Subject = 'Replay for ticket :'.$TcketCode;
        $body = str_replace('[%STORENAME%]',$FromName,$tbl);
        $body = str_replace('[%STOREMAIL%]',$From,$body);
        $body = str_replace('[%TICKETCODE%]',$TcketCode,$body);
        $body = str_replace('[%REPLYTEXT%]',$Reply,$body);
        $phpmailer->Body = $body;
        $phpmailer->AltBody = wp_strip_all_tags($body);
        if ($phpmailer->send()) { $MailsSend = true; } else { $MailsSend = false; }
      }
    }
    return $MailsSend;
  }
  private function LoadTicketsMailTemplate() {
    $Template = '';
    $Ticket = new mensio_tickets();
    $Template = $Ticket->LoadTicketMailTemplate();
    unset($Ticket);
    if ($Template === '') { $Template = $this->DefaultMailTemplate('Ticket'); }
    return $Template;
  }
  public function CloseTicketData($TicketID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = 'Success';
    $Tickets = new mensio_tickets();
    if (!$Tickets->Set_UUID($TicketID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with ticket code<br>';
    } else {
      if (!$Tickets->UpdateTicketToClosed()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Ticket Reply could not be closed<br>';
      }
    }
    unset($Tickets);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
}