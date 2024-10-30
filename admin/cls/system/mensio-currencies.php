<?php
class mensio_currencies extends mensio_core_db {  
  private $ShortCode;
  private $Symbol;
  private $Icon;
  private $LeftPos;
  private $Language;
  private $Name;
  private $SearchString;
  private $Sorter;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->ShortCode = '';
    $this->Symbol = '';
    $this->Icon = '';
    $this->LeftPos = '';
    $this->Language = '';
    $this->Name = '';
    $this->SearchString = '';
    $this->Sorter = '';
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
  final public function Set_Symbol($Value) {
    $SetOk = false;
    switch ($Value) {
      case 'Lek': case '؋': case '$': case 'ƒ': case 'ман': case 'Br':
      case 'BZ$': case '$b': case 'KM': case 'P': case 'лв': case 'R$':
      case '៛': case '¥': case '₡': case 'kn': case '₱': case 'Kč':
      case 'kr': case 'RD$': case '£': case '€': case '¢': case 'Q':
      case 'L': case 'Ft': case 'INR': case 'Rp': case '﷼': case '₪':
      case 'J$': case '₩': case '₭': case 'ден': case 'RM': case '₨':
      case '₮': case 'MT': case 'C$': case '₦': case 'B/.': case 'Gs':
      case 'S/.': case 'zł': case 'lei': case 'руб': case 'Дин': case 'S':
      case 'R': case 'CHF': case 'NT$': case '฿': case 'TT$': case 'TRY':
      case '₴': case '$U': case 'Bs': case '₫': case 'Z$':
        $this->Symbol = $Value;
        $SetOk = true;
        break;
      default:
        $SetOk = false;
    }
    return $SetOk;
  }
  final public function Set_Icon($Value) {
    $SetOk = false;
    if (($Value === 'No Icon') || ($Value === '')) {
      $this->Icon = addslashes('No Icon');
      $SetOk = true;
    } else {
      $ClrVal = $this->ClearValue($Value,'EN','-_ ');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $IconType = explode('-',$ClrVal);
        if (is_array($IconType)) {
          if (($IconType[0] === 'dashicons') || ($IconType[0] === 'fa')) {
            $this->Icon = addslashes($ClrVal);
            $SetOk = true;
          }
        }
      }
    }
    return $SetOk;
  }
	final public function Set_LeftPos($Value) {
    if (($Value == 'true') || ($Value == '1')) {
      $this->LeftPos = '1';
    } else {
      $this->LeftPos = '0';
    }
    return true;
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
  final public function Get_Value($Var) {
		$Value = '';
		switch ($Var) {
			case 'UUID':
				$Value = $this->Get_UUID();
				break;
			case 'Code':
        $Value = $this->ShortCode;
        break;
      case 'Symbol':
        $Value = $this->Symbol;
        break;
      case 'Icon':
				$Value = $this->Icon;
				break;
      case 'LeftPos':
				$Value = $this->LeftPos;
				break;
      case 'Language':
				$Value = $this->Language;
				break;
      case 'Name':
				$Value = $this->Name;
				break;
      case 'CurrencySymbols':
				$Value = $this->CurrencySymbols;
				break;
		}
		return $Value;
	}
  public function ShortCodeFound() {
    $UUID = $this->Get_Value('UUID');
    $CodeFound = false;
    if ($this->ShortCode != '') {
      global $wpdb;
      $Query = 'SELECT * FROM '.$wpdb->prefix.'currencies_codes
        WHERE code = "'.$this->ShortCode.'"';
      $Data = $wpdb->get_results($Query);
      if ($UUID != '') {
        foreach ($Data as $Row) {
          if ($UUID !== $Row->uuid) { $CodeFound = true; }
        }
      } else {
        if (!empty($Data)) { $CodeFound = true; }
      }
      unset($DataRows);
    }
    return $CodeFound;
  }
  final public function LoadCurrencies($ForAdmin=true) {
		$RtrnData = '';
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($this->Sorter == '') { $this->Sorter = $prfx.'currencies_names.name'; }
    $Query = 'SELECT '.$prfx.'currencies_codes.*, '.$prfx.'currencies_names.name
      FROM '.$prfx.'currencies_codes, '.$prfx.'currencies_names, '.$prfx.'store
      WHERE '.$prfx.'currencies_codes.uuid = '.$prfx.'currencies_names.currency
      AND '.$prfx.'currencies_names.language  =  '.$prfx.'store.'.$lang.'
      ORDER BY '.$this->Sorter;
    $RtrnData = $wpdb->get_results($Query);
    return $RtrnData;
  }
  final public function LoadCurrencyMainData() {
		$RtrnData = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'currencies_codes WHERE uuid = "'.$this->Get_Value('UUID').'"';
    $RtrnData = $wpdb->get_results($Query);
    return $RtrnData;
  }
  final public function LoadCurrencyTranslations($Type='',$ForAdmin=true) {
		$RtrnData = '';
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($Type === '') {
      $Query = 'SELECT * FROM '.$prfx.'currencies_names
        WHERE currency = "'.$this->Get_Value('UUID').'"';
    } else {
      $Query = 'SELECT '.$prfx.'languages_codes.*, '.$prfx.'languages_names.name
        FROM '.$prfx.'languages_codes, '.$prfx.'languages_names, '.$prfx.'store
        WHERE '.$prfx.'languages_codes.uuid = '.$prfx.'languages_names.language
        AND '.$prfx.'languages_names.tolanguage = '.$prfx.'store.'.$lang.'
        AND '.$prfx.'languages_codes.active = 1
        AND '.$prfx.'languages_codes.uuid NOT IN (
          SELECT language FROM '.$prfx.'currencies_names
          WHERE currency = "'.$this->Get_Value('UUID').'"
        )';
    }
    $RtrnData = $wpdb->get_results($Query);
    return $RtrnData;
  }
  public function SearchCurrencies($ForAdmin=true) {
		$RtrnData = '';
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($this->Sorter == '') { $this->Sorter = $prfx.'currencies_names.name'; }
    $Query = 'SELECT '.$prfx.'currencies_codes.*, '.$prfx.'currencies_names.name
      FROM '.$prfx.'currencies_codes, '.$prfx.'currencies_names, '.$prfx.'store
      WHERE '.$prfx.'currencies_codes.uuid = '.$prfx.'currencies_names.currency
      AND '.$prfx.'currencies_names.language =  '.$prfx.'store.'.$lang.'
      AND '.$prfx.'currencies_codes.uuid IN (
          SELECT '.$prfx.'currencies_names.currency
          FROM '.$prfx.'currencies_names
          WHERE '.$prfx.'currencies_names.name LIKE "%'.$this->SearchString.'%"
          OR '.$prfx.'currencies_names.currency IN (
              SELECT '.$prfx.'currencies_codes.uuid
              FROM '.$prfx.'currencies_codes
              WHERE '.$prfx.'currencies_codes.code LIKE "%'.$this->SearchString.'%"
          )
      ) ORDER BY '.$this->Sorter;
    $RtrnData = $wpdb->get_results($Query);
    return $RtrnData;
  }
  public function UpdateCurrency() {
    $JobDone = false;
    $SetStr = '';
    $Sep = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'UPDATE '.$prfx.'currencies_codes SET ';
    if ($this->ShortCode !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'code = "'.$this->ShortCode.'"';
    }
    if ($this->Symbol !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'symbol = "'.$this->Symbol.'"';
    }
    if ($this->Icon !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'icon = "'.$this->Icon.'"';
    }
    if ($this->LeftPos !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'leftpos = "'.$this->LeftPos.'"';
    }
    if ($SetStr != '') {
      $Query .= $SetStr.' WHERE uuid = "'.$this->Get_Value('UUID').'"';
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateCurrencyTranslations() {
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
        'DELETE FROM '.$prfx.'currencies_names
          WHERE currency = %s AND language = %s',
        $UUID,
        $this->Language
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'currencies_names
          (currency, language, name) VALUES (%s,%s,%s)',
        $UUID,
        $this->Language,
        $this->Name
      );      
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function AddNewCurrency() {
    $JobDone = false;
    if ($this->ShortCode !== '') {
      $NewUUID = $this->GetNewUUID();
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'currencies_codes
          (uuid, code, symbol, icon, leftpos)
          VALUES (%s,%s,"$","No Icon","0")',
        $NewUUID,
        $this->ShortCode
      );
      if (false !== $wpdb->query($Query)) { $JobDone = $NewUUID; }
    }
    return $JobDone;
  }
}