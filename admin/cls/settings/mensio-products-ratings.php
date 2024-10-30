<?php
class mensio_products_ratings extends mensio_core_db {
  private $Name;
  private $Min;
  private $Max;
  private $Step;
  private $Start;
  private $Icon;
  private $Active;
  private $Sorter;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Name = '';
    $this->Min = 0;
    $this->Max = 100;
    $this->Step = 1;
    $this->Start = 0;
    $this->Icon = '';
    $this->Active = 0;
    $this->Sorter = 'name';
  }
  final public function Set_Name($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' ');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Name = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Min($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Min = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Max($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Max = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Step($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Step = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Start($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Start = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Icon($Value) {
		$SetOk = false;
    if ($Value === 'No Image') {
      $this->Icon = $Value;
      $SetOk = true;
    } else {
      $ClrVal = $this->ClearValue($Value,'EN','-_/.:');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $ClrVal = str_replace(get_site_url().'/','', $ClrVal);
        $this->Icon = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Active($Value) {
		$SetOk = false;
    if (($Value === 1) || ($Value === '1') || ($Value === true)) {
      $this->Active = '1';
      $SetOk = true;
    } else {
      $this->Active = '0';
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Sorter($Value) {
		$SetOk = false;
		$ClrVal = $this->ClearValue($Value,'EN',' ');
		if (mb_strlen($ClrVal) === mb_strlen($Value)) {
			$this->Sorter = $ClrVal;
			$SetOk = true;
		}
		return $SetOk;
	}
  public function GetNewRatingID() {
    return $this->GetNewUUID();
  }
  public function LoadProductRatingsDataSet() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Searcher = '';
    if ($this->Name !== '') { $Searcher = 'WHERE name LIKE "%'.$this->Name.'%"'; }
    $Query = 'SELECT * FROM '.$prfx.'ratings_types '.$Searcher.' ORDER BY '.$this->Sorter;
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet))) {
      foreach ($DataSet as $Row) {
        $Row->icon = get_site_url().'/'.$Row->icon;
      }
    }
    return $DataSet;
  }
  public function GetRatingSystemData() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'ratings_types WHERE uuid = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function CheckMinMaxValues() {
    $Check = false;
    if ($this->Min < $this->Max) { $Check = true; }
    return $Check;
  }
  public function InsertRatingSystem() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Min === '') { $Error = true; }
    if ($this->Max === '') { $Error = true; }
    if ($this->Step === '') { $Error = true; }
    if ($this->Start === '') { $Error = true; }
    if ($this->Icon === '') { $Error = true; }
    if ($this->Active === '') { $Error = true; }
    if (!$Error) {
      if ($this->Active) { $this->ResetActive(); }
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'ratings_types (uuid,name,min,max,step,start,icon,active)
          VALUES (%s,%s,%s,%s,%s,%s,%s,%s)',
        $this->Get_UUID(),
        $this->Name,
        $this->Min,
        $this->Max,
        $this->Step,
        $this->Start,
        $this->Icon,
        $this->Active
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateRatingSystem() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Min === '') { $Error = true; }
    if ($this->Max === '') { $Error = true; }
    if ($this->Step === '') { $Error = true; }
    if ($this->Start === '') { $Error = true; }
    if ($this->Icon === '') { $Error = true; }
    if ($this->Active === '') { $Error = true; }
    if (!$Error) {
      if ($this->Active) { $this->ResetActive(); }
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'ratings_types
          SET name = %s,min = %s,max = %s,step = %s,start = %s,icon = %s,active = %s
          WHERE uuid = %s',
        $this->Name,
        $this->Min,
        $this->Max,
        $this->Step,
        $this->Start,
        $this->Icon,
        $this->Active,
        $this->Get_UUID()
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }    
    return $JobDone;
  }
  private function ResetActive() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = 'UPDATE '.$prfx.'ratings_types SET active = FALSE';
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateActiveRating() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      $this->ResetActive(); // Reseting ALL rating records to false
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'ratings_types SET active = %s WHERE uuid = %s',
        $this->Active,
        $this->Get_UUID()
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RatingIsNiUse() {
    $Answer = false;
    $Count = 0;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'reviews
        WHERE rtype = "'.$this->Get_UUID().'"';
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
  public function DeleteRatingSystem() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'ratings_types WHERE uuid = %s',
        $this->Get_UUID()
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }    
    return $JobDone;
  }
}