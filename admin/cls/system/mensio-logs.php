<?php
class mensio_logs extends mensio_core_db {
  private $Code;
  private $Sorter;  
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Code='';
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
  final public function LoadLogs() {
		$RtrnData = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($this->Sorter == '') { $this->Sorter = 'log_date DESC'; }
    $Query = 'SELECT * FROM '.$prfx.'mensiologs';
    $RtrnData = $wpdb->get_results($Query);
    return $RtrnData;
  }
  final public function DeleteLogs() {
		$RtrnData = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'DELETE FROM '.$prfx.'mensiologs'; 
    $RtrnData = $wpdb->get_results($Query);
    return $RtrnData;
  }
  final public function DeleteMultiLogs() {
		$RtrnData = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'DELETE FROM '.$prfx.'mensiologs
              WHERE '.$prfx.'mensiologs.code = "'.$this->Code.'"
             ';
    $RtrnData = $wpdb->get_results($Query);
    return $RtrnData;
  }
}
