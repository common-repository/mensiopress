<?php
class mensio_notifications extends mensio_core_db {
  private $Code;
  private $Note_Store;    
  private $Note_Type;
  private $Note_Date;
  private $Note_User;
  private $Notification;
  private $Informed;
  private $Sorter;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Code = '';
    $this->Note_Store = '';
    $this->Note_Type = '';
    $this->Note_Date = '';
    $this->Note_User = '';
    $this->Notification = '';
    $this->Informed = '';
  }
  final public function Set_Code($Value) {
        $SetOK = false;
        $ClrVal = $this->ClearValue($Value,'NM');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $SetOK = true;
        $this->Code = $ClrVal;
    }
        return $SetOK;
  }
  final public function Set_Sorter($Value) {
		$SetOk = false;
		$ClrVal = $this->ClearValue($Value,'EN',' ');
		$ClrVal = $this->ClearValue($ClrVal,'TX',' ');
		if (mb_strlen($ClrVal) === mb_strlen($Value)) {
			$this->Sorter = $ClrVal;
			$SetOk = true;
		}
		return $SetOk;
	}
  final public function LoadNotifications() {
		$RtrnData = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
     if ($this->Sorter == '') { $this->Sorter = 'note_date DESC'; }
    $Query = 'SELECT '.$prfx.'notifications.*,'.$wpdb->prefix.'users.display_name
               FROM '.$prfx.'notifications,'.$wpdb->prefix.'users
               WHERE '.$prfx.'notifications.note_user = '.$wpdb->prefix.'users.ID     
              ORDER BY '.$prfx.'notifications.'.$this->Sorter; 
    $RtrnData = $wpdb->get_results($Query);
    return $RtrnData; 
  }
  final public function DeleteNotifications() {
		$RtrnData = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'DELETE FROM '.$prfx.'notifications';
    $RtrnData = $wpdb->get_results($Query);
    $Query = 'ALTER TABLE '.$prfx.'notifications AUTO_INCREMENT = 1';
    $wpdb->query($Query);
    return $RtrnData;
  }
  final public function DeleteMultiNotifications() {
		$RtrnData = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'DELETE FROM '.$prfx.'notifications
              WHERE '.$prfx.'notifications.code = "'.$this->Code.'"
             ';
    $RtrnData = $wpdb->get_results($Query);
    return $RtrnData;
  }
  }