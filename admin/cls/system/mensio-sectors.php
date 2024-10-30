<?php
class mensio_sectors extends mensio_core_db {  
  private $Parent;
  private $Language;
  private $Name;
  private $SearchString;
  private $Sorter;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Parent = '';
    $this->Language = '';
    $this->Name = '';
    $this->SearchString = '';
    $this->Sorter = '';
  }
  final public function Set_Parent($Value) {
		$SetOk = false;
    if ($Value === 'TopLevel') {
        $this->Parent = $Value;
        $SetOk = true;
    } else {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Parent = $ClrVal;
        $SetOk = true;
      }
		}
		return $SetOk;
  }
  final public function Set_Language($Value) {
		$SetOk = false;
		$ClrVal = $this->ClearUUID($Value);
		if ($ClrVal != false) {
			$this->Language = $ClrVal;
			$SetOk = true;
		}
		return $SetOk;
	}
  final public function Set_Name($Value) {
		$SetOk = false;
		$ClrVal = $this->ClearValue($Value,'TX',',&\-\' ');
		if (mb_strlen($ClrVal) === mb_strlen($Value)) {
			$this->Name = addslashes($ClrVal);
			$SetOk = true;
		}
		return $SetOk;
	}
  final public function Set_SearchString($Value) {
		$SetOk = false;
    $Value = mb_ereg_replace('[^\p{L}\p{N}]', '%', $Value);
    $ClrVal = $this->ClearValue($Value,'AN','%');
		if (mb_strlen($ClrVal) === mb_strlen($Value)) {
			$this->SearchString = $ClrVal;
			$SetOk = true;
		}
		return $SetOk;
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
  public function LoadSectorsList($ForAdmin=true) {
    $SortBy = 'name';
    if ($this->Parent === '') { $this->Parent = 'TopLevel'; }
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Sorter === 'name DESC') { $SortBy = 'name DESC';}
      else { $SortBy = 'name'; }
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT '.$prfx.'sectors_codes.*, '.$prfx.'sectors_names.name
      FROM '.$prfx.'sectors_codes, '.$prfx.'sectors_names, '.$prfx.'store
      WHERE '.$prfx.'sectors_codes.uuid = '.$prfx.'sectors_names.sector
      AND '.$prfx.'sectors_names.language = '.$prfx.'store.'.$lang.'
      AND '.$prfx.'sectors_codes.parent = "'.$this->Parent.'"
      ORDER BY '.$prfx.'sectors_names.'.$SortBy;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function SearchSectorsList($ForAdmin=true) {
    $SortBy = 'name';
    if ($this->Parent === '') { $this->Parent = 'TopLevel'; }
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Sorter === 'name DESC') { $SortBy = 'name DESC';}
      else { $SortBy = 'name'; }
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT '.$prfx.'sectors_codes.*, '.$prfx.'sectors_names.name
      FROM '.$prfx.'sectors_codes, '.$prfx.'sectors_names, '.$prfx.'store
      WHERE '.$prfx.'sectors_codes.uuid = '.$prfx.'sectors_names.sector
      AND '.$prfx.'sectors_names.language = '.$prfx.'store.'.$lang.'
      AND '.$prfx.'sectors_names.name LIKE "%'.$this->SearchString.'%"
      ORDER BY '.$prfx.'sectors_names.'.$SortBy;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function GetSectorData() {
    $RtrnData = false;
    $uuid = $this->Get_UUID();
    if ($uuid !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'sectors_codes WHERE uuid = "'.$uuid.'"';
      $RtrnData = $wpdb->get_results($Query);
    }
    return $RtrnData;
  }
  public function GetSectorTranslationData() {
    $RtrnData = false;
    $uuid = $this->Get_UUID();
    if ($uuid !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'sectors_names WHERE sector = "'.$uuid.'"';
      $RtrnData = $wpdb->get_results($Query);
    }
    return $RtrnData;
  }
  public function GetNewSectorID() {
    return $this->GetNewUUID();
  }
  public function InsertNewSector() {
    $JobDone = false;
    $Error = false;
    $uuid = $this->Get_UUID();
    if ($uuid === '') { $Error = true; }
    if ($this->Parent === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'sectors_codes
          (uuid, parent) VALUES (%s,%s)',
        $uuid,
        $this->Parent
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateSectorData() {
    $JobDone = false;
    $Error = false;
    $uuid = $this->Get_UUID();
    if ($uuid === '') { $Error = true; }
    if ($this->Parent === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'sectors_codes SET parent = %s WHERE uuid = %s',
        $this->Parent,
        $uuid
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateSectorTranslations() {
		$JobDone = false;
		$Error = false;
    $UUID = $this->Get_UUID();
		if ($UUID == '') { $Error = true; }
		if ($this->Language == '') { $Error = true; }
		if ($this->Name == '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'sectors_names
          WHERE sector = %s AND language = %s',
        $UUID,
        $this->Language
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'sectors_names
          (sector, language, name) VALUES (%s,%s,%s)',
        $UUID,
        $this->Language,
        $this->Name
      );      
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function CheckIfSectorInUse() {
    $IsInUse = false;
    $uuid = $this->Get_UUID();
		if ($uuid !== '') { 
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'sectors_codes WHERE parent = "'.$uuid.'"';
      $RtrnData = $wpdb->get_results($Query);
      if (!empty($RtrnData)) { $IsInUse = true; }
      $Query = 'SELECT * FROM '.$prfx.'companies WHERE sector = "'.$uuid.'"';
      $RtrnData = $wpdb->get_results($Query);
      if (!empty($RtrnData)) { $IsInUse = true; }
    }
    return $IsInUse;
  }
  public function DeleteSector() {
		$JobDone = false;
		$Error = false;
    $UUID = $this->Get_UUID();
		if ($UUID == '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'sectors_names WHERE sector = %s',
        $UUID
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'sectors_codes WHERE uuid = %s',
        $UUID
      );
      $wpdb->query($Query);
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
}