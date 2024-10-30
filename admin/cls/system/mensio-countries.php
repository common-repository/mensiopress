<?php
class mensio_countries extends mensio_core_db {
  private $Continent;
  private $Iso2;
  private $Iso3;
  private $Domain;
  private $Idp;
  private $Currency;
  private $Language;
  private $Name;
  private $SearchString;
  private $Sorter;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Continent = '';
    $this->Iso2 = '';
    $this->Iso3 = '';
    $this->Domain = '';
    $this->Idp = '';
    $this->Currency = '';
    $this->Language = '';
    $this->Name = '';
    $this->SearchString = '';
    $this->Sorter = '';
  }
  final public function Set_Continent($Value) {
		$SetOk = false;
		$ClrVal = $this->ClearUUID($Value);
		if ($ClrVal != false) {
			$this->Continent = $ClrVal;
			$SetOk = true;
		}
		return $SetOk;
  }
  final public function Set_ISO2($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'EN');
    $ClrVal = $this->ClearValue($ClrVal,'TX');
    if (mb_strlen($ClrVal) === 2) {
      $SetOK = true;
      $this->Iso2 = $ClrVal;
    }
    return $SetOK;
  }
  final public function Set_ISO3($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'EN');
    $ClrVal = $this->ClearValue($ClrVal,'TX');
    if (mb_strlen($ClrVal) === 3) {
      $SetOK = true;
      $this->Iso3 = $ClrVal;
    }
    return $SetOK;
  }
  final public function Set_IDP($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'NM');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $SetOK = true;
      $this->Idp = $ClrVal;
    }
    return $SetOK;
  }
  final public function Set_Domain($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'TX');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $SetOK = true;
      $this->Domain = $ClrVal;
    }
    return $SetOK;
  }
  final public function Set_Currency($Value) {
		$SetOk = false;
		$ClrVal = $this->ClearUUID($Value);
		if ($ClrVal != false) {
			$this->Currency = $ClrVal;
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
  final public function Set_SearchString($Value) {
		$SetOk = false;
    $Value = mb_ereg_replace('[^\p{L}\p{N}]', '%', $Value);
    $ClrVal = $this->ClearValue($Value,'EN','%');
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
  private function CreateSorter() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($this->Sorter == '') {
      $this->Sorter = $prfx.'countries_names.name';
    } else {
      switch ($this->Sorter) {
        case 'country':
        case 'country DESC':
          if (strpos($this->Sorter, 'DESC') !== false) {
            $this->Sorter = $prfx.'countries_names.name DESC';
          } else {
            $this->Sorter = $prfx.'countries_names.name';
          }
          break;
        case 'currency':
        case 'currency DESC':
          if (strpos($this->Sorter, 'DESC') !== false) {
            $this->Sorter = $prfx.'currencies_names.name DESC';
          } else {
            $this->Sorter = $prfx.'currencies_names.name';
          }
          break;
        case 'iso':
        case 'iso DESC':
        case 'domain':
        case 'domain DESC':
        case 'idp':
        case 'idp DESC':
          $this->Sorter = $prfx.'countries_codes.'.$this->Sorter;
          break;
      }
    }
  }
  private function CreateQuery($prfx,$lang) {
    $this->CreateSorter();
    $Search = '';
    if ($this->SearchString !== '') {
      $Search = 'AND '.$prfx.'countries_codes.uuid IN (
            SELECT uuid FROM '.$prfx.'countries_codes 
            WHERE uuid IN (
                SELECT uuid FROM '.$prfx.'countries_codes
                WHERE iso LIKE "%'.$this->SearchString.'%"
                OR domain LIKE "%'.$this->SearchString.'%"
                OR idp LIKE "%'.$this->SearchString.'%"
            )
            OR uuid IN (
                SELECT country FROM '.$prfx.'countries_names
                WHERE name LIKE "%'.$this->SearchString.'%"
            )
          OR currency IN (
                SELECT currency
                FROM '.$prfx.'currencies_names
                WHERE name LIKE "%'.$this->SearchString.'%"
            )
        )';
    }
    $Query = 'SELECT '.$prfx.'countries_codes.*, '.$prfx.'countries_names.name AS country,
        '.$prfx.'currencies_codes.symbol, '.$prfx.'currencies_names.name AS curname
      FROM '.$prfx.'countries_codes, '.$prfx.'countries_names,
        '.$prfx.'currencies_codes, '.$prfx.'currencies_names, '.$prfx.'store
      WHERE '.$prfx.'countries_codes.uuid = '.$prfx.'countries_names.country    
      AND '.$prfx.'countries_names.language  =  '.$prfx.'store.'.$lang.'
      AND '.$prfx.'countries_codes.currency  =  '.$prfx.'currencies_codes.uuid
      AND '.$prfx.'currencies_codes.uuid  =  '.$prfx.'currencies_names.currency
      AND '.$prfx.'currencies_names.language  =  '.$prfx.'store.'.$lang.'
      '.$Search.' ORDER BY '.$this->Sorter;
    return $Query;
  }
  final public function GetCountriesDataSet($ForAdmin=true) {
    $CountryData = '';
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    global $wpdb;
    $Query = $this->CreateQuery($wpdb->prefix.'mns_',$lang);
    $CountryData = $wpdb->get_results($Query);
    return $CountryData;
  }
  final public function GetCountryBasicData() {
    $Error = false;
    $DataSet = false;
    $uuid = $this->Get_UUID();
    if ($uuid === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'countries_codes WHERE uuid = "'.$uuid.'"';
      $DataSet = $wpdb->get_results($Query);
      if (is_array($DataSet)) {
        if (empty($DataSet[0])) { $DataSet = false; }
      } else {
        $DataSet = false;
      }
    }
    return $DataSet;
  }
  final public function GetCountryTranslations() {
    $Error = false;
    $DataSet = false;
    $uuid = $this->Get_UUID();
    if ($uuid === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'countries_names WHERE country = "'.$uuid.'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function GetCountryName($ForAdmin=true) {
    $Error = false;
    $DataSet = false;
    $Name = '';
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    $uuid = $this->Get_UUID();
    if ($uuid === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'countries_names.*
        FROM '.$prfx.'countries_names, '.$prfx.'store
        WHERE '.$prfx.'countries_names.country = "'.$uuid.'"
        AND '.$prfx.'countries_names.language = '.$prfx.'store.'.$lang;
      $DataSet = $wpdb->get_results($Query);
      foreach ($DataSet as $Country) {
        $Name = $Country->name;
      }
    }
    return $Name;
  }
  public function GetNewCountryID() {
    return $this->GetNewUUID();
  }
  final public function UpdateCountryData($Mode) {
    $JobDone = false;
    $Error = false;
    $uuid = $this->Get_UUID();
    if ($uuid === '') { $Error = true; }
    if (($Mode !== 'New') && ($Mode !== 'Edit')) { $Error = true; }
    if (!$Error) {
      if ($Mode === 'New') {
        if ($this->Continent === '') { $Error = true; }
        if ($this->Iso2 === '') { $Error = true; }
        if ($this->Iso3 === '') { $Error = true; }
        if ($this->Domain === '') { $Error = true; }
        if ($this->Idp === '') { $Error = true; }
        if ($this->Currency === '') { $Error = true; }
        if (!$Error) {
          $JobDone = $this->InsertCountryRecord($uuid);
        }
      } else {
        $JobDone = $this->UpdateCountryRecord();
      }
    }
    return $JobDone;
  }
  private function InsertCountryRecord($uuid) {
    $JobDone = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = $wpdb->prepare(
      'INSERT INTO '.$prfx.'countries_codes
        (uuid,continent,iso,domain,idp,currency) VALUES (%s,%s,%s,%s,%s,%s)',
      $uuid,
      $this->Continent,
      $this->Iso2.'-'.$this->Iso3,
      $this->Domain,
      $this->Idp,
      $this->Currency
    );
    if (false !== $wpdb->query($Query)) { $JobDone = true; }
    return $JobDone;
  }
  private function UpdateCountryRecord() {
    $JobDone = false;
    $SetStr = '';
    $Sep = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'UPDATE '.$prfx.'countries_codes SET ';
    if ($this->Continent !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'continent = "'.$this->Continent.'"';
    }
    if (($this->Iso2 !== '') && ($this->Iso3 !== '')) {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'iso = "'.$this->Iso2.'-'.$this->Iso3.'"';
    }
    if ($this->Domain !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'domain = "'.$this->Domain.'"';
    }
    if ($this->Idp !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'idp = "'.$this->Idp.'"';
    }
    if ($this->Currency !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'currency = "'.$this->Currency.'"';
    }
    if ($SetStr != '') {
      $Query .= $SetStr.' WHERE uuid = "'.$this->Get_UUID().'"';
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  final public function UpdateCountryTranslations() {
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
        'DELETE FROM '.$prfx.'countries_names
          WHERE country = %s AND language = %s',
        $UUID,
        $this->Language
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'countries_names
          (country, language, name) VALUES (%s,%s,%s)',
        $UUID,
        $this->Language,
        $this->Name
      );      
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function CountryInUse() {
    $Found = false;
    $Error = false;
    $uuid = $this->Get_UUID();
    if ($uuid === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'store WHERE country = "'.$uuid.'"';
      $DataSet = $wpdb->get_results($Query);
      foreach ($DataSet as $Country) {
        $Found = true;
      }
      $Query = 'SELECT * FROM '.$prfx.'addresses WHERE country = "'.$uuid.'"';
      $DataSet = $wpdb->get_results($Query);
      foreach ($DataSet as $Country) {
        $Found = true;
      }
    }
    return $Found;
  }
  final public function DeleteCountry() {
		$JobDone = false;
		$Error = false;
    $UUID = $this->Get_UUID();
		if ($UUID == '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'countries_names WHERE country = %s',
        $UUID
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'countries_codes WHERE uuid = %s',
        $UUID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
}