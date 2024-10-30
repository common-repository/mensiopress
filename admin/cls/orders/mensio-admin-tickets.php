<?php
class mensio_tickets extends mensio_core_db {
  private $ReplyAuthor;
  private $ReplyText;
  private $AuthorIsAdmin;
  private $Sorter;
  private $SearchString;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->ReplyAuthor = '';
    $this->ReplyText = '';
    $this->AuthorIsAdmin = false;
    $this->Sorter = 'dateadded DESC';
    $this->SearchString = '';
  }
  final public function Set_ReplyAuthor($Value) {
    $SetOk = false;
    $this->AuthorIsAdmin = false;
    if (substr($Value,0,10) === 'ADMINUSER-') {
      $this->ReplyAuthor = str_replace('ADMINUSER-','',$Value);
      $this->AuthorIsAdmin = true;
      $SetOk = true;
    } else {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->ReplyAuthor = $ClrVal;
        $SetOk = true;
      }
		}
		return $SetOk;
	}
  final public function Set_ReplyText($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $this->ReplyText = wp_kses_post($Value);
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Sorter($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN',' ');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Sorter = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_SearchString($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN',' ');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->SearchString = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  public function GetNewID() {
    return $this->GetNewUUID();
  }
  public function LoadTicketsDataSet() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    switch ($this->Sorter) {
      case 'customer':
      case 'customer DESC':
        $this->Sorter = str_replace('customer', $prfx.'credentials.lastname', $this->Sorter);
        break;
      case 'ticket':
      case 'ticket DESC':
        $this->Sorter = str_replace('ticket', 'ticket_code', $this->Sorter);
      default:
        $this->Sorter = $prfx.'customers_tickets.'.$this->Sorter;
        break;
    }
    $Searcher = '';
    if ($this->SearchString !== '') {
      $Searcher = 'AND (ticket_code LIKE "%'.$this->SearchString.'%"
        OR dateadded LIKE "%'.$this->SearchString.'%"
        OR title LIKE "%'.$this->SearchString.'%"
        OR content LIKE "%'.$this->SearchString.'%")';
    }
    $Query = 'SELECT '.$prfx.'customers_tickets.*, '.$prfx.'credentials.lastname, '.$prfx.'credentials.firstname
      FROM '.$prfx.'customers_tickets, '.$prfx.'credentials
      WHERE '.$prfx.'customers_tickets.customer = '.$prfx.'credentials.uuid
      '.$Searcher.'
      ORDER BY '.$this->Sorter;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadTicketData() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'SELECT '.$prfx.'customers_tickets.*, '.$prfx.'credentials.lastname,
          '.$prfx.'credentials.firstname, '.$prfx.'credentials.username
          FROM '.$prfx.'customers_tickets, '.$prfx.'credentials
          WHERE '.$prfx.'customers_tickets.customer = '.$prfx.'credentials.uuid
          AND '.$prfx.'customers_tickets.uuid = %s',
        $this->Get_UUID()
      );
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadTicketHistory() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'customers_tickets_history
          WHERE ticket = %s ORDER BY replydate DESC',
        $this->Get_UUID()
      );
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function FindReplyAuthorName() {
    $Name = '';
    if ($this->AuthorIsAdmin) {
      $DataSet = $this->FindWPUserData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Name = $Row->user_nicename.'<span class="usrnmspan">('.$Row->user_email.')</span>';
        }
      }
    } else {
      $DataSet = $this->FindTicketCustomerData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Name = $Row->lastname.' '.$Row->firstname.'<span class="usrnmspan">('.$Row->username.')</span>';
        }
      }
      if ($Name === '') { $Name = 'TEST';}
    }
    return $Name;
  }
  private function FindWPUserData() {
    $DataSet = array();
    if ($this->ReplyAuthor !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix;
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'users WHERE ID = %s',
        $this->ReplyAuthor
      );
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  private function FindTicketCustomerData() {
    $DataSet = array();
    if ($this->ReplyAuthor !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'credentials WHERE uuid = %s',
        $this->ReplyAuthor
      );
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadTicketRelOrder() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'SELECT '.$prfx.'orders.*
          FROM '.$prfx.'customers_tickets_orders, '.$prfx.'orders
          WHERE '.$prfx.'customers_tickets_orders.orderid = '.$prfx.'orders.uuid
          AND '.$prfx.'customers_tickets_orders.ticket = %s',
        $this->Get_UUID()
      );
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function UpdateTicketHistory() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->ReplyAuthor === '') { $Error = true; }
    if ($this->ReplyText === '') { $Error = true; }
    if (!$Error) {
      $RDate = date("Y-m-d H:i:s");
      if ($this->AuthorIsAdmin) {$this->ReplyAuthor = 'ADMINUSER-'.$this->ReplyAuthor; }
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'customers_tickets_history (ticket,replyauthor,replydate,replytext) VALUES (%s,%s,%s,%s)',
        $this->Get_UUID(),
        $this->ReplyAuthor,
        $RDate,
        $this->ReplyText
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateTicketToClosed() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'customers_tickets SET dateclosed = %s, closed = TRUE WHERE uuid = %s',
        date("Y-m-d H:i:s"),
        $this->Get_UUID()
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function LoadTicketMailTemplate() {
    return $this->LoadMailTemplate('Ticket');
  }
}