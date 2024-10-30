<?php
class mensio_languages extends mensio_core_db {
  private $ShorCode;
  private $Name;
  private $Language;
	private $ToLanguage;
	private $AdminMain;
	private $ThemeMain;
	private $Icon;
	private $Active;
  private $SearchString;
  private $Sorter;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->ShorCode = '';
    $this->Name = '';
    $this->Icon = 'No Image';
    $this->Active = '';
    $this->Language = '';
    $this->ToLanguage = '';
    $this->AdminMain = $this->ReturnMainLanguages('Admin');
    $this->ThemeMain = $this->ReturnMainLanguages('Theme');
  }
  public function ShortCodeFound($UUID='') {
    $CodeFound = false;
    if ($this->ShortCode != '') {
      global $wpdb;
      $Query = 'SELECT * FROM '.$wpdb->prefix.'mns_languages_codes
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
  final public function Set_ShortCode($Value) {
    $Error = false;
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'TX','-');
    if (mb_strlen($ClrVal) != mb_strlen($Value)) { $Error = true; }
    if ((mb_strlen($ClrVal) < 2 ) || (mb_strlen($ClrVal) > 7 )) { $Error = true; }
    if (!$Error) {
      $this->ShortCode = $ClrVal;
      $SetOK = true;
    }
    return $SetOK;
  }
  final public function Set_Name($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearValue($Value,'TX',' ');
    if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $SetOK = true;
      $this->Name = $ClrVal;
    }
    return $SetOK;
  }
  final public function Set_Icon($Value) {
    $SetOK = false;
    if ($Value === 'No Image') {
      $SetOK = true;
      $this->Icon = $Value;
    } else {
      $ClrVal = $this->ClearValue($Value,'EN','-_./:=?&');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $ClrVal = str_replace(get_site_url().'/', '', $ClrVal);
        $SetOK = true;
        $this->Icon = $ClrVal;
      }
    }
    return $SetOK;
  }
  final public function Set_Language($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearUUID($Value);
    if ($ClrVal) {
      $this->Language = $ClrVal;
      $SetOK = true;
    }
    return $SetOK;
  }
  final public function Set_ToLanguage($Value) {
    $SetOK = false;
    $ClrVal = $this->ClearUUID($Value);
    if ($ClrVal) {
      $this->ToLanguage = $ClrVal;
      $SetOK = true;
    }
    return $SetOK;
  }
	final public function Set_Active($Value) {
		$SetOK = false;
    if ($Value == 1) {
      $this->Active = '1';
      $SetOK = true;
    } else {
      $this->Active = '0';
      $SetOK = true;
    }
		return $SetOK;
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
  final public function Get_Value($ValName) {
		$Value = '';
		switch ($ValName) {
			case 'UUID':
				$Value = $this->Get_UUID();
				break;
			case 'ShortCode':
				$Value = $this->ShortCode;
				break;
			case 'Name':
				$Value = $this->Name;
				break;
      case 'Language':
				$Value = $this->Language;
				break;
      case 'ToLanguage':
				$Value = $this->ToLanguage;
				break;
      case 'AdminMain':
				$Value = $this->AdminMain;
				break;
      case 'ThemeMain':
				$Value = $this->ThemeMain;
				break;
      case 'Active':
				$Value = $this->Active;
				break;
		}
		return $Value;
	}
  final public function Get_LanguageMainName() {
		$RtrnData = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
		$Query = '
    SELECT * FROM '.$prfx.'languages_names
      WHERE language = "'.$this->Get_Value('UUID').'"
      AND tolanguage = "'.$this->AdminMain.'"';
    $Data = $wpdb->get_results($Query);
    foreach ($Data as $Row) {
  		$RtrnData = $Row->name;
    }
    return $RtrnData;
  }
	public function LoadLanguagesData() {
		$RtrnData = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($this->Sorter == '') { $this->Sorter = $prfx.'languages_names.name'; }
		$Query = '
    SELECT '.$prfx.'languages_codes.*, '.$prfx.'languages_names.name
      FROM '.$prfx.'languages_codes, '.$prfx.'languages_names, '.$prfx.'store
      WHERE '.$prfx.'languages_codes.uuid = '.$prfx.'languages_names.language
      AND '.$prfx.'languages_names.tolanguage =  '.$prfx.'store.adminlang
      ORDER BY '.$this->Sorter;
    $RtrnData = $wpdb->get_results($Query);
    return $RtrnData;
	}
  public function SearchLanguages() {
		$RtrnData = '';
    if ($this->SearchString != '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      if ($this->Sorter == '') { $this->Sorter = $prfx.'languages_codes.uuid'; }
      if (mb_strlen($this->SearchString) === 2) {
        $Query = 'SELECT '.$prfx.'languages_codes.*, '.$prfx.'languages_names.name
          FROM '.$prfx.'languages_codes, '.$prfx.'languages_names, '.$prfx.'store
          WHERE '.$prfx.'languages_codes.uuid = '.$prfx.'languages_names.language
          AND '.$prfx.'languages_names.tolanguage =  '.$prfx.'store.adminlang
          AND '.$prfx.'languages_codes.code LIKE "%'.$this->SearchString.'%"
          ORDER BY '.$this->Sorter;
      } else {
        $Query = 'SELECT '.$prfx.'languages_codes.*, '.$prfx.'languages_names.name
          FROM '.$prfx.'languages_codes, '.$prfx.'languages_names, '.$prfx.'store
          WHERE '.$prfx.'languages_codes.uuid = '.$prfx.'languages_names.language
          AND '.$prfx.'languages_names.tolanguage =  '.$prfx.'store.adminlang
          AND '.$prfx.'languages_codes.uuid IN (
              SELECT '.$prfx.'languages_names.language
              FROM '.$prfx.'languages_names
              WHERE '.$prfx.'languages_names.name LIKE "%'.$this->SearchString.'%"
              OR '.$prfx.'languages_names.language IN (
                  SELECT '.$prfx.'languages_codes.uuid
                  FROM '.$prfx.'languages_codes
                  WHERE '.$prfx.'languages_codes.code LIKE "%'.$this->SearchString.'%"
                  OR '.$prfx.'languages_codes.active LIKE "%'.$this->SearchString.'%"
              )
          ) ORDER BY '.$this->Sorter;
      }
      $RtrnData = $wpdb->get_results($Query);
    }
    return $RtrnData;
  }
  public function ReturnLanguageData($SerchCol='uuid') {
		$RtrnData = false;
    $Error = false;
    if ($SerchCol !== 'code') {
      $Col = 'uuid';
      $LangKey = $this->Get_Value('UUID');
    } else {
      $Col = $SerchCol;
      $LangKey = $this->Get_Value('ShortCode');
    }
    if ($LangKey == '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'languages_codes
        WHERE '.$Col.' = "'.$LangKey.'"';
      $RtrnData = $wpdb->get_results($Query);
    }
    return $RtrnData;
   }
  public function ReturnLanguageTranslations() {
		$RtrnData = false;
    $Error = false;
    if ($this->Get_Value('Language') == '') { $Error = true; }
    if ($this->Get_Value('ToLanguage') == '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'languages_names
        WHERE language = "'.$this->Get_Value('Language').'"
        AND tolanguage = "'.$this->Get_Value('ToLanguage').'"';
      $RtrnData = $wpdb->get_results($Query);
    }
    return $RtrnData;
  }
  public function ReturnMainLanguages($Type) {
		$RtrnData = false;
    $Error = false;
    if ($Type !== 'Theme') { $Col = 'adminlang'; }
      else {$Col = 'themelang'; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'store';
      $lang = $wpdb->get_results($Query);
      foreach ($lang as $ROW) {
        $RtrnData = $ROW->$Col;
      }
    }
    return $RtrnData;
  }
  public function ReturnEnglishLanguage() {
		$RtrnData = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'languages_codes WHERE code = "en"';
    $DataSet = $wpdb->get_results($Query);
    foreach ($DataSet as $Row) {
      $RtrnData = $Row->uuid;
    }
    return $RtrnData;
  }
  public function InsertNewLanguage() {
		$JobDone = false;
		if ($this->ShortCode != '') {
      if ( ! $this->ShortCodeFound() ) {
        $NewUUID = $this->GetNewUUID();
        global $wpdb;
        $prfx = $wpdb->prefix.'mns_';
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'languages_codes
            (uuid, code, icon, active) VALUES (%s,%s,%s,0)',
          $NewUUID,
          $this->ShortCode,
          'No Image'
        );
        $wpdb->query($Query);
        $Lang = $this->LoadLanguagesData();
        foreach ($Lang as $Language) {
          $this->Language = $NewUUID;
          $this->ToLanguage = $Language->uuid;
          $this->Name = $this->ShortCode;
          $this->UpdateLanguageTranslation();
          $this->Language = $Language->uuid;
          $this->ToLanguage = $NewUUID;
          $this->Name = $this->ShortCode;
          $this->UpdateLanguageTranslation();
        }
        $this->Language = $NewUUID;
        $this->ToLanguage = $NewUUID;
        $this->Name = $this->ShortCode;
        $this->UpdateLanguageTranslation();
        $JobDone = true;
      }
		}
    return $JobDone;
  }
  public function UpdateLanguageData() {
		$JobDone = false;
    $Error = false;
    $Lang = $this->Get_Value('UUID');
    if ($Lang === '') { $Error = true; }
    if ($this->ShortCode === '') { $Error = true; }
    if ($this->Icon === '') { $Error = true; }
    if ($this->Active === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      if ($this->Icon !== 'No Image') {
        $Query = $wpdb->prepare(
          'UPDATE '.$prfx.'languages_codes SET code = %s, icon=%s, active = %s
            WHERE uuid = %s',
          $this->ShortCode,
          $this->Icon,
          $this->Active,
          $Lang
        );
      } else {
        $Query = $wpdb->prepare(
          'UPDATE '.$prfx.'languages_codes SET code = %s, active = %s
            WHERE uuid = %s',
          $this->ShortCode,
          $this->Active,
          $Lang
        );
      }
      if ($wpdb->query($Query) !== false) { $JobDone = true;
        if ($this->Active === '0') {
          $Query = 'SELECT * FROM '.$prfx.'store';
          $DataSet = $wpdb->get_results($Query);
          $ActiveLang = '';
          foreach ($DataSet as $Row) {
            $ActiveLang = $Row->thmactivelang;
          }
          if ($ActiveLang !== '') {
            $ActiveLang = str_replace (';;',';',str_replace ($Lang,'',$ActiveLang));
            $Query = 'UPDATE '.$prfx.'store SET thmactivelang = "'.$ActiveLang.'"';
            $wpdb->query($Query);
          }
        }
      }
    }
    return $JobDone;
  }
  public function UpdateLanguageTranslation () {
		$Error = false;
		$JobDone = false;
		if ($this->Language == '') { $Error = true; }
		if ($this->ToLanguage == '') { $Error = true; }
		if ($this->Name == '') { $Error = true; }
    if (!$Error) {
        global $wpdb;
        $prfx = $wpdb->prefix.'mns_';
        $Query = $wpdb->prepare(
          'DELETE FROM '.$prfx.'languages_names
            WHERE language = %s AND tolanguage = %s',
          $this->Language,
          $this->ToLanguage
        );
        $wpdb->query($Query);
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'languages_names
            (language, tolanguage, name) VALUES (%s,%s,%s)',
          $this->Language,
          $this->ToLanguage,
          $this->Name
        );
        if ($wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateMainLanguages($Type) {
    $JobDone = true;
    switch ($Type) {
      case 'Admin':
        $Col = 'adminlang';
        break;
      case 'Theme':
        $Col = 'themelang';
        break;
      default:
        $JobDone = false;
        break;
    }
    $Lang = $this->Get_Value('UUID');
    if ($Lang == '') { $JobDone = false; }
    if ($JobDone) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $OldLang = $this->ReturnMainLanguages($Type);
      $Query = 'UPDATE '.$prfx.'store
        SET '.$Col.' = "'.$Lang.'"
          WHERE  '.$Col.' = "'.$OldLang.'"';
      if ( ! $wpdb->query($Query) ) { $JobDone = false; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'languages_codes
          SET active = 1
          WHERE uuid = %s',
        $Lang
      );
      if ($wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function GetActiveLanguages() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'languages_codes WHERE active = TRUE';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
}
