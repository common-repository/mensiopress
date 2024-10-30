<?php
class mensio_products_default_attributes extends mensio_core_db {
  private $Name;
  private $Visibility;
  private $GlobalCategory;
  private $ValueID;
  private $Value;
  private $Sorter;
  private $SearchString;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Name = '';
    $this->Visibility = 0;
    $this->GlobalCategory = $this->GetGlobalCategory();
    $this->ValueID = '';
    $this->Value = '';
    $this->Sorter = 'name';
    $this->SearchString = '';
  }
  final public function Set_Name($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' .,/"()-');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Name = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Visibility($Value) {
    if (($Value === 0) || ($Value === '0') || ($Value === false)) {
      $this->Visibility = '0';
    } else {
      $this->Visibility = '1';
    }
	}
  final public function Set_ValueID($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->ValueID = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Value($Val) {
		$SetOk = false;
    if ($Val !== '') {
      $ClrVal = $this->ClearValue($Val,'AN',' .,/"()-');
      if (mb_strlen($ClrVal) === mb_strlen($Val)) {
        $this->Value = addslashes($ClrVal);
        $SetOk = true;
      }
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
      $Value = mb_ereg_replace('[^\p{L}\p{N}]', '%', $Value);
      $ClrVal = $this->ClearValue($Value,'EN','%');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->SearchString = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_ColorValue($ColorVal) {
		$SetOk = false;
    $Check = 0;
    $this->Value = '';
    if ($ColorVal !== '') {
      $ClrData = explode(';',$ColorVal);
      if (count($ClrData) === 5) {
        if (substr($ClrData[0], 0, 4) === 'Name') {
          $ClrData[0] = str_replace('Name:', '', $ClrData[0]);
          $ClrVal = $this->ClearValue($ClrData[0],'AN',' ');
          if (mb_strlen($ClrVal) === mb_strlen($ClrData[0])) {
            $this->Value .= 'Name:'.$ClrVal.';';
            ++$Check;
          }
        }
        if (substr($ClrData[1], 0, 3) === 'Hex') {
          $ClrData[1] = str_replace('Hex:', '', $ClrData[1]);
          $ClrVal = $this->ClearValue($ClrData[1],'EN','#');
          if (mb_strlen($ClrVal) === mb_strlen($ClrData[1])) {
            $this->Value .= 'Hex:'.$ClrVal.';';
            ++$Check;
          }
        }
        if (substr($ClrData[2], 0, 1) === 'R') {
          $ClrData[2] = str_replace('R:', '', $ClrData[2]);
          $ClrVal = $this->ClearValue($ClrData[2],'NM');
          if (mb_strlen($ClrVal) === mb_strlen($ClrData[2])) {
            $this->Value .= 'R:'.$ClrVal.';';
            ++$Check;
          }
        }
        if (substr($ClrData[3], 0, 1) === 'G') {
          $ClrData[3] = str_replace('G:', '', $ClrData[3]);
          $ClrVal = $this->ClearValue($ClrData[3],'NM');
          if (mb_strlen($ClrVal) === mb_strlen($ClrData[3])) {
            $this->Value .= 'G:'.$ClrVal.';';
            ++$Check;
          }
        }
        if (substr($ClrData[4], 0, 1) === 'B') {
          $ClrData[4] = str_replace('B:', '', $ClrData[4]);
          $ClrVal = $this->ClearValue($ClrData[4],'NM');
          if (mb_strlen($ClrVal) === mb_strlen($ClrData[4])) {
            $this->Value .= 'B:'.$ClrVal;
            ++$Check;
          }
        }
        if ($Check === 5) { $SetOk = true; }
      } else {
        if (substr($ClrData[0], 0, 4) === 'Name') {
          $ClrData[0] = str_replace('Name:', '', $ClrData[0]);
          $ClrVal = $this->ClearValue($ClrData[0],'AN',' ');
          if (mb_strlen($ClrVal) === mb_strlen($ClrData[0])) {
            $this->Value .= 'Name:'.$ClrVal.';';
            ++$Check;
          }
        }
        if (substr($ClrData[1], 0, 4) === 'Img:') {
          $ClrVal = $this->ClearValue($ClrData[1],'AN','-:_./');
          if (mb_strlen($ClrVal) === mb_strlen($ClrData[1])) {
            $this->Value .= str_replace(get_site_url().'/','',$ClrVal);
            ++$Check;
          }
        }
        if ($Check === 2) { $SetOk = true; }
      }
    }
		return $SetOk;
  }
  final public function Set_NumericValue($Value) {
		$SetOk = false;
    if ($Value !== '') {
      if (substr($Value, 0, 4) === 'Sngl') {
        $Value = str_replace('Sngl:', '', $Value);
        $ClrVal = $this->ClearValue($Value,'NM','.,');
        if (mb_strlen($ClrVal) === mb_strlen($Value)) {
          $this->Value = $ClrVal;
          $SetOk = true;
        }
      } else {
        $this->Value = array();
        $Data = explode(';',$Value);
        $Min = str_replace('Min:', '', $Data[0]);
        $ClrVal = $this->ClearValue($Min,'NM','.,');
        if (mb_strlen($ClrVal) === mb_strlen($Min)) {
          $Step = str_replace('Step:', '', $Data[2]);
          $ClrVal = $this->ClearValue($Step,'NM','.,');
          if (mb_strlen($ClrVal) === mb_strlen($Step)) {
            $Step = $ClrVal;
            $Max = str_replace('Max:', '', $Data[1]);
            $ClrVal = $this->ClearValue($Max,'NM','.,');
            if (mb_strlen($ClrVal) === mb_strlen($Max)) {
              $Max = $ClrVal;
              $i = 0;
              for ($Num = $Min; $Num <= $Max; $Num = $Num + $Step) {
                $this->Value[$i] = $Num;
                ++$i;
              }
              $SetOk = true;
            }
          }
        }        
      }
    }
		return $SetOk;
  }
  public function GetNewAttributeID() {
    return $this->GetNewUUID();
  }
  private function GetGlobalCategory() {
    $GlblCat = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'categories_codes WHERE name = "GLOBAL"';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) { $GlblCat = $Row->uuid; }
    } else {
      $GlblCat = $this->GetNewUUID();
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'categories_codes (uuid,name,image,visibility)
          VALUES (%s,"GLOBAL","NOIMAGE",0)',
        $GlblCat
      );
      if ($wpdb->query($Query) === false) { $GlblCat = ''; }
    }
    return $GlblCat;
  }
  public function GetGlobalAttributeType() {
    $Name = '';
    $Metrics = '';
    $Type = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'attributes WHERE uuid = "'.$this->Get_UUID().'"';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $Name = $Row->name;
      }
    }
    $Query = 'SELECT * FROM '.$prfx.'store';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $Metrics = $Row->metrics;
      }
    }
    if ($Metrics !== '') {
      $Metrics = explode(';',$Metrics);
      foreach ($Metrics as $Attr) {
        $Attr = explode(':',$Attr);
        if ($Name === $Attr[0]) { $Type = $Attr[1]; }
      }
    }
    return $Type;
  }
  public function LoadDefaultAttributesDataSet() {
    $DataSet = array();
    $Search = '';
    if ($this->SearchString !== '') {
      $Search = 'AND name LIKE "%'.$this->SearchString.'%"';
    }
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'attributes
      WHERE category = "'.$this->GlobalCategory.'"
      '.$Search.' ORDER BY '.$this->Sorter;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadAttributeData() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'attributes WHERE uuid = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function GetAttributeValues() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'attributes_values WHERE attribute = "'.$this->Get_UUID().'"';
      if ($this->SearchString !== '') {
        $Query .= ' AND value LIKE "%'.$this->SearchString.'%"';
      }
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function AddNewGlobalAttribute() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Visibility === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'attributes (uuid,category,name,visibility)
          VALUES (%s,%s,%s,%s)',
        $this->Get_UUID(),
        $this->GlobalCategory,
        $this->Name,
        $this->Visibility
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateGlobalAttribute() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Visibility === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'attributes SET visibility = %s WHERE uuid = %s',
        $this->Visibility,
        $this->Get_UUID()
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateGlobalAttributeVisibility() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'attributes SET visibility =
         (CASE
            WHEN visibility = TRUE THEN FALSE ELSE TRUE
         END)
        WHERE uuid = %s',
        $this->Get_UUID()
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function AddNewValue() {
    $JobDone = false;
    $Error = false;
    $Check = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->ValueID === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      if (is_array($this->Value)) {
        foreach ($this->Value as $Row) {
          $Check = false;
          $Query = $wpdb->prepare(
            'INSERT INTO '.$prfx.'attributes_values (uuid,attribute,value)
              VALUES (%s,%s,%s)',
            $this->GetNewUUID(),
            $this->Get_UUID(),
            $Row
          );
          if ($wpdb->query($Query) !== false) { $Check = true; }
        }
        if ($Check) { $JobDone = true; }
      } else {
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'attributes_values (uuid,attribute,value)
            VALUES (%s,%s,%s)',
          $this->ValueID,
          $this->Get_UUID(),
          $this->Value
        );
        if ($wpdb->query($Query) !== false) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
  public function UpdateValue() {
    $JobDone = false;
    $Error = false;
    if ($this->ValueID === '') { $Error = true; }
    if ($this->Value === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'attributes_values SET value = %s WHERE uuid = %s',
        $this->Value,
        $this->ValueID
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RemoveGlobalValue() {
    $JobDone = false;
    if ($this->ValueID !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'attributes_values WHERE uuid = %s',
        $this->ValueID
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function CheckValueUsage() {
    $IsUsed = false;
    if ($this->Value !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'products_attributes
        WHERE attribute_value = "'.$this->Value.'"';
      $Data = $wpdb->get_results($Query);    
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $IsUsed = true; }
      }
    }
    return $IsUsed;
  }
  public function CheckAttributeUsage() {
    $IsUsed = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'products_attributes
        WHERE attribute_value IN (
          SELECT uuid FROM '.$prfx.'attributes_values
          WHERE attribute = "'.$this->Get_UUID().'")';
      $Data = $wpdb->get_results($Query);    
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { 
          if ($Row->count > 0 ) { $IsUsed = true; }
        }
      }
    }
    return $IsUsed;
  }
  public function RemoveGlobalAttribute() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_attributes WHERE attribute_value IN
          (SELECT uuid FROM '.$prfx.'attributes_values WHERE attribute = %s)',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'attributes_values WHERE attribute = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'attributes WHERE uuid = %s',
        $this->Get_UUID()
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
}