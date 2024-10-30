<?php
class mensio_regions extends mensio_core_db {
  private $Parent;
  private $Language;
  private $Name;
  private $Country;
  private $Type;
  private $AdminLang;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Language = '';
    $this->Name = '';
    $this->Country = '';
    $this->Parent = '';
    $this->SearchString = '';
    $this->Sorter = '';
    $this->Type = '';
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
  final public function Set_Country($Value) {
    $SetOk = false;
    $ClrVal = $this->ClearUUID($Value);
    if ($ClrVal != false) {
      $this->Country = $ClrVal;
    	$SetOk = true;
	  }
		return $SetOk;
  }
  final public function Set_Type($Value) {
    $SetOk = false;
    $ClrVal = $this->ClearUUID($Value);
    if ($ClrVal != false) {
      $this->Type = $ClrVal;
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
  final public function Set_Parent($Value) {
    $SetOk = false;
    if ($Value === 'TopLevel') {
      $this->Parent = $Value;
      $SetOk = true;
    }else{
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Parent = $ClrVal;
        $SetOk = true;
      }
    }
    return $SetOk;
  }
  public function GetNewCodeForRegions() {
    return $this->GetNewUUID();
  }
  public function LoadCountryRegionTypes() {
    $Data = '';
    if ($this->Country !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'regions_types
        WHERE country = "'.$this->Country.'"
        ORDER BY level';
      $Data = $wpdb->get_results($Query);
    }
    return $Data;
  }
  private function LoadRegionDataSet($Parent='TopLevel',$ForAdmin=true) {
    $DataSet = '';
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Country !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
      SELECT '.$prfx.'regions_codes.*, '.$prfx.'regions_names.name,
        '.$prfx.'regions_types.level
      FROM '.$prfx.'regions_codes, '.$prfx.'regions_names, '.$prfx.'store,
        '.$prfx.'regions_types
      WHERE '.$prfx.'regions_codes.uuid = '.$prfx.'regions_names.region
      AND '.$prfx.'regions_codes.type = '.$prfx.'regions_types.uuid
      AND '.$prfx.'regions_names.language = '.$prfx.'store.'.$lang.'
      AND '.$prfx.'regions_codes.country = "'.$this->Country.'"
      AND '.$prfx.'regions_codes.parent = "'.$Parent.'"
      ORDER BY '.$prfx.'regions_names.name';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $DataSet .= $Row->uuid.'++'.$Row->type.'++'.$Row->level.'++'.$Row->name.'++'.$Row->parent.';;';
          $DataSet .= $this->LoadRegionDataSet($Row->uuid);
        }
      }
    }
    return $DataSet;
  }
  public function LoadCountryRegions() {
    $DataSet = array();
    if ($this->Country !== '') {
      $Data = $this->LoadRegionDataSet();
      if ($Data !== '') {
        $Data = explode(';;',$Data);
        $i = 0;
        foreach ($Data as $Row) {
          if ($Row !== '') {
            $RowData = explode('++',$Row);
            $DataSet[$i]['uuid'] = $RowData[0];
            $DataSet[$i]['type'] = $RowData[1];
            $DataSet[$i]['level'] = $RowData[2];
            $DataSet[$i]['name'] = $RowData[3];
            $DataSet[$i]['parent'] = $RowData[4];
          }
          ++$i;
        }
      }
    }
    return $DataSet;
  }
  public function LoadParentRegions($ForAdmin=true) {
    $Data = '';
    $Error = false;
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Country === '') { $Error = true; }
    if ($this->Type === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'regions_codes.*, '.$prfx.'regions_names.name as name
        FROM '.$prfx.'regions_codes, '.$prfx.'regions_names, '.$prfx.'store
        WHERE '.$prfx.'regions_codes.uuid = '.$prfx.'regions_names.region
        AND '.$prfx.'regions_names.language = '.$prfx.'store.'.$lang.'
        AND '.$prfx.'regions_codes.type = (
          SELECT '.$prfx.'regions_types.uuid FROM '.$prfx.'regions_types
          WHERE '.$prfx.'regions_types.country = "'.$this->Country.'"
          AND '.$prfx.'regions_types.level < (
              SELECT '.$prfx.'regions_types.level FROM '.$prfx.'regions_types
              WHERE '.$prfx.'regions_types.country = "'.$this->Country.'"
              AND '.$prfx.'regions_types.uuid = "'.$this->Type.'"
          ) ORDER BY '.$prfx.'regions_types.level DESC LIMIT 1
        )';
      $Data = $wpdb->get_results($Query);
    }
    return $Data;
  }
  public function LoadRegionData($ForAdmin=true) {
    $Data = '';
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'regions_codes.*, '.$prfx.'regions_names.name as name
        FROM '.$prfx.'regions_codes, '.$prfx.'regions_names, '.$prfx.'store
        WHERE '.$prfx.'regions_codes.uuid = '.$prfx.'regions_names.region
        AND '.$prfx.'regions_names.language = '.$prfx.'store.'.$lang.'
        AND '.$prfx.'regions_codes.uuid = "'.$this->Get_UUID().'"';
      $Data = $wpdb->get_results($Query);
    }
    return $Data;
  }
  public function LoadRegionName($ForAdmin=true) {
    $Data = '';
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'regions_names.name as name
        FROM '.$prfx.'regions_codes, '.$prfx.'regions_names, '.$prfx.'store
        WHERE '.$prfx.'regions_codes.uuid = '.$prfx.'regions_names.region
        AND '.$prfx.'regions_names.language = '.$prfx.'store.'.$lang.'
        AND '.$prfx.'regions_codes.uuid = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Data = $Row->name;
        }
      }
    }
    return $Data;
  }
  public function LoadRegionTypeData() {
    $Data = '';
    if ($this->Type !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT *  FROM '.$prfx.'regions_types
        WHERE uuid = "'.$this->Type.'"';
      $Data = $wpdb->get_results($Query);
    }
    return $Data;
  }
  public function CheckParentType() {
    $Answer = false;
    $Error = false;
    $RegLvl = '';
    $ParLvl = '';
    if ($this->Get_UUID() === '') { $Error = true;}
    if ($this->Parent === '') { $Error = true;}
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
      SELECT '.$prfx.'regions_codes.uuid, '.$prfx.'regions_types.level
      FROM '.$prfx.'regions_codes, '.$prfx.'regions_types
      WHERE '.$prfx.'regions_codes.type = '.$prfx.'regions_types.uuid
      AND '.$prfx.'regions_codes.uuid = "'.$this->Get_UUID().'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $RegLvl = $Row->level;
        }
      }
      $Query = '
      SELECT '.$prfx.'regions_codes.uuid, '.$prfx.'regions_types.level
      FROM '.$prfx.'regions_codes, '.$prfx.'regions_types
      WHERE '.$prfx.'regions_codes.type = '.$prfx.'regions_types.uuid
      AND '.$prfx.'regions_codes.uuid = "'.$this->Parent.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $ParLvl = $Row->level;
        }
      }
      if ($ParLvl < $RegLvl) { $Answer = true; }
    }
    $Answer = '$ParLvl : '.$ParLvl.' $RegLvl : '.$RegLvl.'<br>'.$Query;
    return $Answer;
  }
  public function AddNewRegionData() {
    $JobDone = false;
    $Error = false;
    $AdminLang = $this->LoadAdminLang();
    if ($this->Get_UUID() === '') { $Error = true;}
    if ($this->Country === '') { $Error = true;}
    if ($this->Type === '') { $Error = true;}
    if ($this->Name === '') { $Error = true;}
    if ($this->Parent === '') { $Error = true;}
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'regions_codes (uuid,country,type,parent,inhouse)
          VALUES (%s,%s,%s,%s,"1")',
        $this->Get_UUID(),
        $this->Country,
        $this->Type,
        $this->Parent
      );
      if ($wpdb->query($Query) !== false) {
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'regions_names (region,language,name)
            VALUES (%s,%s,%s)',
          $this->Get_UUID(),
          $AdminLang,
          $this->Name
        );
        if ($wpdb->query($Query) !== false) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
  public function UpdateRegionData() {
    $JobDone = false;
    $Error = false;
    $AdminLang = $this->LoadAdminLang();
    if ($this->Get_UUID() === '') { $Error = true;}
    if ($this->Type === '') { $Error = true;}
    if ($this->Name === '') { $Error = true;}
    if ($this->Parent === '') { $Error = true;}
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'regions_codes SET type = %s, parent = %s WHERE uuid = %s',
        $this->Type,
        $this->Parent,
        $this->Get_UUID()
      );
      $Error = true;
      if ($wpdb->query($Query) !== false) { $Error = false; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'regions_names SET name = %s
          WHERE region = %s
          AND language = %s',
        $this->Name,
        $this->Get_UUID(),
        $AdminLang
      );
      $Error = true;
      if ($wpdb->query($Query) !== false) { $Error = false; }
      if (!$Error) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function CheckRegionType() {
    $Answer = false;
    $Count = 0;
    if ($this->Type !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'regions_codes
        WHERE '.$prfx.'regions_codes.type = "'.$this->Type.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Count = $Row->count;
        }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  public function CheckIfRegionHasSubs() {
    $Answer = false;
    $Count = 0;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'regions_codes
        WHERE '.$prfx.'regions_codes.parent = "'.$this->Get_UUID().'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Count = $Row->count;
        }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  private function FindMaxTypeLevel() {
    $Level = 0;
    if ($this->Country !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'regions_types
        WHERE country = "'.$this->Country.'" ORDER BY level DESC LIMIT 1';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Level = intval($Row->level) + 1;
        }
      }
      if ($Level === 0) { $Level = 1; }
    }
    return $Level;
  }
  public function AddNewRegionType() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Country === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if (!$Error) {
      $Level = $this->FindMaxTypeLevel();
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'regions_types (uuid,country,name,level,inhouse)
          VALUES (%s,%s,%s,%s,"1")',
        $this->Get_UUID(),
        $this->Country,
        $this->Name,
        $Level
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;    
  }
  public function UpdateRegionType() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'regions_types SET name = %s WHERE uuid = %s',
        $this->Name,
        $this->Get_UUID()
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function DeleteRegionType() {
    $JobDone = false;
    if ($this->Type !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'regions_types WHERE uuid = %s',
        $this->Type
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RemoveRegionData() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'regions_names WHERE region = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'regions_codes WHERE uuid = %s',
        $this->Get_UUID()
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function LoadRegionTranslations() {
    $Name = '';
    if (($this->Get_UUID() !== '') && ($this->Language !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'SELECT * FROM '.$prfx.'regions_names
          WHERE region = %s
          AND language = %s',
        $this->Get_UUID(),
        $this->Language
      );
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Name = $Row->name;
        }
      }
    }
    return $Name;
  }
  public function UpdateRegionTranslations() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Language === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'regions_names WHERE region = %s AND language = %s',
        $this->Get_UUID(),
        $this->Language
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'regions_names (region,language,name)
          VALUES (%s,%s,%s)',
        $this->Get_UUID(),
        $this->Language,
        $this->Name
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
}
