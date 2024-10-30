<?php
class mensio_orders_returns extends mensio_core_db {
  private $Sorter;
  private $SearchString;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Sorter = 'name';
    $this->SearchString = '';
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
  public function LoadReturnsDataSet() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Searcher = '';
    if ($this->SearchString !== '') {
      $Searcher = 'WHERE serial LIKE "%'.$this->SearchString.'%"
        OR created  LIKE "%'.$this->SearchString.'%"';
    }
    $Query = 'SELECT * FROM '.$prfx.'returns '.$Searcher.' ORDER BY '.$this->Sorter;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function GetReturnData() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'returns WHERE uuid = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
}