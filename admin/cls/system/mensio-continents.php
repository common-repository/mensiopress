<?php
class mensio_continents extends mensio_core_db {  
  private $ShortCode;
  private $Language;
  private $Name;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->ShortCode = '';
    $this->Language = '';
    $this->Name = '';
  }
  final public function Set_ShortCode($Value) {
    $SetOk = false;
    $ClrVal = strtoupper($Value);
    $ClrVal = $this->ClearValue($ClrVal,'TX');
    $ClrVal = $this->ClearValue($ClrVal,'EN');
		if ((mb_strlen($ClrVal) === 2) || (mb_strlen($ClrVal) === 3)) {
      $this->ShortCode = $ClrVal;
      $SetOk = true;
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
		$ClrVal = $this->ClearValue($Value,'TX',' ');
		if (mb_strlen($ClrVal) === mb_strlen($Value)) {
			$this->Name = $ClrVal;
			$SetOk = true;
		}
		return $SetOk;
	}
  final public function Get_Value($Var) {
		$Value = '';
		switch ($Var) {
			case 'UUID':
        $Value = $this->Get_UUID();
        break;
			case 'Code':
        $Value = $this->ShortCode;
        break;
		}
		return $Value;
	}
  final public function LoadContinentsCodes() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'continents_codes';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;    
  }
  final public function LoadContinentsData($ForAdmin=true) {
    $DataSet = false;
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($this->Language === '') {
      $Query = 'SELECT '.$prfx.'continents_codes.*, '.$prfx.'continents_names.name
        FROM '.$prfx.'continents_codes, '.$prfx.'continents_names, '.$prfx.'store
        WHERE '.$prfx.'continents_codes.uuid = '.$prfx.'continents_names.continent
        AND '.$prfx.'continents_names.language = '.$prfx.'store.'.$lang.'
        ORDER BY '.$prfx.'continents_codes.code';
    } else {
      $Query = 'SELECT '.$prfx.'continents_codes.*, '.$prfx.'continents_names.name
        FROM '.$prfx.'continents_codes, '.$prfx.'continents_names
        WHERE '.$prfx.'continents_codes.uuid = '.$prfx.'continents_names.continent
        AND '.$prfx.'continents_names.language = "'.$this->Language.'"
        ORDER BY '.$prfx.'continents_codes.code';
    }
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;    
  }
  final public function UpdateContinentTranslation() {
		$Error = false;
		$JobDone = false;
    $UUID = $this->Get_Value('UUID');
		if ($UUID == '') { $Error = true; }
		if ($this->Language == '') { $Error = true; }
		if ($this->Name == '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'continents_names
          WHERE continent = %s AND language = %s',
        $UUID,
        $this->Language
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'continents_names
          (continent, language, name) VALUES (%s,%s,%s)',
        $UUID,
        $this->Language,
        $this->Name
      );      
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
}