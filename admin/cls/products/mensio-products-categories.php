<?php
class mensio_products_categories extends mensio_core_db {
  private $Name;
  private $Slug;
  private $Language;
  private $Image;
  private $Visibility;
  private $Parent;
  private $COrder;
  private $Attribute;
  private $Value;
  private $Sorter;
  private $SearchString;
  private $ExtraFilters;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Name = '';
    $this->Slug = '';
    $this->Language = '';
    $this->Image = '';
    $this->Visibility = false;
    $this->Parent = '';
    $this->COrder = '';
    $this->Attribute = '';
    $this->Value = '';
    $this->Sorter = 'name';
    $this->SearchString = '';
    $this->ExtraFilters = '';
  }
  final public function Set_Name($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' .,/"');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Name = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Slug($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $Value = mb_ereg_replace('[^\p{L}\p{N}]','-',$Value);
      $ClrVal = $this->ClearValue($Value,'AN','-');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        if (!$this->SlugExists($ClrVal,$this->Get_UUID())) {
          $this->Slug = $ClrVal;
          $SetOk = true;
        }
      }
    }
		return $SetOk;
	}
  final public function Set_Language($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Language = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Image($Value) {
		$SetOk = false;
    if ($Value === 'No Image') {
      $this->Image = $Value;
      $SetOk = true;
    } else {
      $ClrVal = $this->ClearValue($Value,'EN','-_/.:');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $ClrVal = str_replace(get_site_url().'/','',$ClrVal);
        $this->Image = addslashes($ClrVal);
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
  final public function Set_COrder($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->COrder = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Attribute($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Attribute = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Value($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Value = $ClrVal;
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
  final public function Set_ExtraFilters($Value) {
		$SetOk = false;
    $this->ExtraFilters = '';
    $JSONData = json_decode(stripslashes($Value),true);
    if (json_last_error() === JSON_ERROR_NONE) {
      $this->ExtraFilters = $JSONData;
			$SetOk = true;
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
  public function GetNewID() {
    return $this->GetNewUUID();
  }
  public function LoadProductCategoriesDataSet() {
    $DataSet = array();
    $Searcher = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($this->SearchString !== '') {
      $Searcher = 'AND name LIKE "%'.$this->SearchString.'%"
        OR uuid IN ( SELECT category FROM '.$prfx.'categories_names
          WHERE name LIKE "%'.$this->SearchString.'%" )';
    }
    if ($this->ExtraFilters !== '') {
      if ((is_array($this->ExtraFilters)) && (!empty($this->ExtraFilters[0]))) {
        foreach ($this->ExtraFilters as $Row) {
          switch($Row['Field']) {
            case 'Parent':
              if (($Row['Value'] !== '') && ($Row['Value'] !== '0')) {
                $Searcher .= ' AND uuid IN (SELECT category FROM '.$prfx.'categories_tree WHERE parent = "'.$Row['Value'].'")';
              }
              break;
          }
        }
      }
    }
    $Query = 'SELECT * FROM '.$prfx.'categories_codes WHERE name != "GLOBAL" '.$Searcher.' ORDER BY '.$this->Sorter;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadProductCategoriesTreeDataSet($ForAdmin=true) {
    $DataSet = array();
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT '.$prfx.'categories_tree.*, '.$prfx.'categories_codes.name AS name,
        '.$prfx.'categories_names.name AS translation
      FROM '.$prfx.'categories_tree, '.$prfx.'categories_names,
        '.$prfx.'categories_codes,'.$prfx.'store
      WHERE '.$prfx.'categories_tree.category = '.$prfx.'categories_codes.uuid
      AND '.$prfx.'categories_codes.name != "GLOBAL"
      AND '.$prfx.'categories_tree.category = '.$prfx.'categories_names.category
      AND '.$prfx.'categories_names.language = '.$prfx.'store.'.$lang.'
      AND '.$prfx.'categories_tree.parent = "'.$this->Parent.'"
      ORDER BY corder';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadProductCategoryPath($CategoryID) {
    $Name = '';
    if ($this->ClearUUID($CategoryID)) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'categories_codes.*
      FROM '.$prfx.'categories_tree, '.$prfx.'categories_codes
      WHERE '.$prfx.'categories_tree.parent = '.$prfx.'categories_codes.uuid
      AND '.$prfx.'categories_tree.category = "'.$CategoryID.'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Name .= $this->LoadProductCategoryPath($Row->uuid);
          if ($Name === '') { $Name .= $Row->name; }
            else { $Name .= '/'.$Row->name; }
        }
      }
    }
    return $Name;
  }
  public function GetCategoryData() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'categories_codes
        WHERE '.$prfx.'categories_codes.uuid = "'.$this->Get_UUID().'"
        ORDER BY '.$this->Sorter;
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $DataSet['uuid'] = $Row->uuid;
          $DataSet['name'] = $Row->name;
          $DataSet['image'] = $Row->image;
          $DataSet['visibility'] = $Row->visibility;
        }
      }
    }
    return $DataSet;
  }
  public function GetGLOBALCategoryID() {
    $GlblID = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'categories_codes WHERE name = "GLOBAL"';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) { $GlblID = $Row->uuid; }
    }
    return $GlblID;
  }
  public function GetCategorySlug() {
    $Slug = '';
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'store_slugs WHERE uuid = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Slug = $Row->slug;
        }
      }
    }
    return $Slug;
  }
  public function GetCategoryAttributes() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'attributes WHERE category = "'.$this->Get_UUID().'" ORDER BY name';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function GetCategoryAttributeValues() {
    $DataSet = array();
    if ($this->Attribute !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'attributes_values WHERE attribute = "'.$this->Attribute.'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function GetCategoryTranslation() {
    $TransName = '';
    if (($this->Get_UUID() !== '') && ($this->Language !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'categories_names
        WHERE category = "'.$this->Get_UUID().'"
        AND language = "'.$this->Language.'"';
      $Data = $wpdb->get_results($Query);    
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $TransName = $Row->name;
        }
      }
    }
    return $TransName;
  }
  public function GetAttributeTranslation() {
    $TransName = '';
    if (($this->Attribute !== '') && ($this->Language !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'attributes_names
        WHERE attribute = "'.$this->Attribute.'"
        AND language = "'.$this->Language.'"';
      $Data = $wpdb->get_results($Query);    
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $TransName = $Row->name;
        }
      }
    }
    return $TransName;
  }
  public function GetCategoryTranslationList($ForAdmin=true) {
    $DataSet = '';
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'categories_names.*, '.$prfx.'languages_names.name AS langname
        FROM '.$prfx.'categories_names, '.$prfx.'languages_names, '.$prfx.'store
        WHERE '.$prfx.'categories_names.language = '.$prfx.'languages_names.language
        AND '.$prfx.'languages_names.tolanguage = '.$prfx.'store.'.$lang.'
        AND  '.$prfx.'categories_names.category = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);    
    }
    return $DataSet;
  }
  private function FindMaxCOrder() {
    $MaxCOrder = 0;
    $ParentID = 'TopLevel';
    if ($this->Parent !== '') { $ParentID = $this->Parent; }
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'categories_tree
      WHERE parent = "'.$ParentID.'"
      ORDER BY corder DESC LIMIT 1';
    $Data = $wpdb->get_results($Query);    
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $MaxCOrder = $Row->corder;
      }
    }
    return $MaxCOrder;
  }
  public function InsertNewCategory() {
    $JobDone = false;
    $Error = false;
    $this->Language = $this->LoadAdminLang();
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Image === '') { $Error = true; }
    if ($this->Visibility === '') { $Error = true; }
    if (!$Error) {
      $MaxOrder = $this->FindMaxCOrder();
      ++$MaxOrder;
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'categories_codes (uuid,name,image,visibility)
          VALUES (%s,%s,%s,%s)',
        $this->Get_UUID(),
        $this->Name,
        $this->Image,
        $this->Visibility
      );
      if ($wpdb->query($Query) !== false) {
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'categories_names (category,language,name)
           SELECT %s,uuid,%s FROM '.$prfx.'languages_codes WHERE active = TRUE',
          $this->Get_UUID(),
          $this->Name
        );
        if ($wpdb->query($Query) !== false) {
          $Query = $wpdb->prepare(
            'INSERT INTO '.$prfx.'categories_tree (category,parent,corder)
              VALUES (%s,"TopLevel",%s)',
            $this->Get_UUID(),
            $MaxOrder
          );
          if ($wpdb->query($Query) !== false) { $JobDone = true; }
        }
      }
    }
    return $JobDone;
  }
  public function UpdateCategoryRecord() {
    $JobDone = false;
    $SetStr = '';
    $Sep = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'UPDATE '.$prfx.'categories_codes SET ';
    if ($this->Name !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'name = "'.$this->Name.'"';
    }
    if ($this->Image !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'image = "'.$this->Image.'"';
    }
    if ($this->Visibility !== '') {
      if ($SetStr !== '') { $Sep = ', '; }
      $SetStr .= $Sep.'visibility = "'.$this->Visibility.'"';
    }
    if ($SetStr != '') {
      $Query .= $SetStr.' WHERE uuid = "'.$this->Get_UUID().'"';
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateCategoryTranslation() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Language === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'categories_names
          WHERE category = %s AND language = %s',
        $this->Get_UUID(),
        $this->Language
      );      
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'categories_names (category,language,name)
          VALUES (%s,%s,%s)',
        $this->Get_UUID(),
        $this->Language,
        $this->Name
      );      
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateAttributeTranslation() {
    $JobDone = false;
    $Error = false;
    if ($this->Attribute === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Language === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'attributes_names
          WHERE attribute = %s AND language = %s',
        $this->Attribute,
        $this->Language
      );      
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'attributes_names (attribute,language,name)
          VALUES (%s,%s,%s)',
        $this->Attribute,
        $this->Language,
        $this->Name
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RemoveCategoryData() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'categories_tree SET parent = "TopLevel" WHERE parent = %s',
        $this->Get_UUID()
      );      
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'attributes_values WHERE attribute IN
          (SELECT uuid FROM '.$prfx.'attributes WHERE category = %s)',
        $this->Get_UUID()
      );      
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'attributes WHERE category = %s',
        $this->Get_UUID()
      );      
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'categories_names WHERE category = %s',
        $this->Get_UUID()
      );      
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'categories_codes WHERE uuid = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = 'DELETE FROM '.$prfx.'categories_tree
          WHERE category NOT IN (SELECT uuid FROM '.$prfx.'categories_codes)';
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'store_slugs WHERE uuid = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = 'DELETE FROM '.$wpdb->prefix.'posts WHERE post_content = "[mensioobject uuid=\"'.$this->Get_UUID().'\"]"';
      if (false !== $wpdb->query($Query)) { $JobDone = true; }      
    }
    return $JobDone;
  }
  public function CheckCategoryUsage() {
    $IsUsed = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'products_attributes.*
        FROM '.$prfx.'products_attributes, '.$prfx.'attributes_values, '.$prfx.'attributes
        WHERE '.$prfx.'products_attributes.attribute_value = '.$prfx.'attributes_values.uuid
        AND '.$prfx.'attributes_values.attribute = '.$prfx.'attributes.uuid 
        AND '.$prfx.'attributes.category = "'.$this->Get_UUID().'"';
      $Data = $wpdb->get_results($Query);    
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $IsUsed = true; }
      }
    }
    return $IsUsed;
  }
  public function CheckAttributeUsage() {
    $IsUsed = false;
    if ($this->Attribute !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'products_attributes.*
        FROM '.$prfx.'products_attributes, '.$prfx.'attributes_values
        WHERE '.$prfx.'products_attributes.attribute_value = '.$prfx.'attributes_values.uuid
        AND '.$prfx.'attributes_values.attribute = "'.$this->Attribute.'"';
      $Data = $wpdb->get_results($Query);    
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $IsUsed = true; }
      }
    }
    return $IsUsed;
  }
  public function CheckAttributeValueUsage() {
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
  public function InsertNewCategoryAttribute() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      $NewAttribute = $this->GetNewUUID();
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'attributes (uuid,category,name,visibility)
          VALUES (%s,%s,%s,"1")',
        $NewAttribute,
        $this->Get_UUID(),
        'New Attribute'
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function InsertTransferedAttribute() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Attribute === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'attributes (uuid,category,name,visibility)
          VALUES (%s,%s,%s,"0")',
        $this->Attribute,
        $this->Get_UUID(),
        $this->Name
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateCategoryAttribute() {
    $JobDone = false;
    $Error = false;
    if ($this->Attribute === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Visibility === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'attributes SET name = %s, visibility = %s
          WHERE uuid = %s',
        $this->Name,
        $this->Visibility,
        $this->Attribute
      );
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RemoveCategoryAttribute() {
    $JobDone = false;
    if ($this->Attribute !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'attributes_values WHERE attribute = %s',
        $this->Attribute
      );      
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'attributes WHERE uuid = %s',
        $this->Attribute
      );      
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function FindNumberOfValuesExists() {
    $Found = 0;
    if (($this->Attribute !== '') && ($this->Value !== '') && ($this->Name !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS NumFound
        FROM '.$prfx.'attributes_values
        WHERE uuid != "'.$this->Value.'"
        AND attribute = "'.$this->Attribute.'"
        AND value = "'.$this->Name.'"';
      $Data = $wpdb->get_results($Query);    
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Found = $Row->NumFound; }
      }
    }
    return $Found;
  }
  public function InsertNewCategoryAttributeValue() {
    $JobDone = false;
    if (($this->Attribute !== '') && ($this->Name !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'attributes_values (uuid,attribute,value)
          VALUES (%s,%s,%s)',
        $this->GetNewUUID(),
        $this->Attribute,
        $this->Name
      );      
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateCategoryAttributeValue() {
    $JobDone = false;
    $Error = false;
    if ($this->Value === '')  { $Error = true; }
    if ($this->Name === '')  { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'attributes_values SET value = %s WHERE uuid = %s',
        $this->Name,
        $this->Value
      );      
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RemoveAttributeValue() {
    $JobDone = false;
    if ($this->Value !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'attributes_values WHERE uuid = %s',
        $this->Value
      );      
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function ClearCategoriesTree() {
    $JobDone = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'DELETE FROM '.$prfx.'categories_tree';
    if ($wpdb->query($Query) !== false) { $JobDone = true; }
    return $JobDone;
  }
  public function UpdateCategoryTree() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Parent === '') { $Error = true; }
    if ($this->COrder === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'categories_tree (category,parent,corder)
          VALUES (%s,%s,%s)',
        $this->Get_UUID(),
        $this->Parent,
        $this->COrder
      );      
      if ($wpdb->query($Query) !== false) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function CheckCategoryNameExists() {
    $Answer = false;
    $Count = 0;
    if (($this->Get_UUID() !== '') && ($this->Name !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'categories_codes
        WHERE name = "'.$this->Name.'" AND uuid != "'.$this->Get_UUID().'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Count = $Row->count; }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  public function UpdateCategorySlug() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Slug === '') { $Error = true; }
    if (!$Error) {
      $JobDone = $this->AddSlug('Category',$this->Slug,$this->Get_UUID(),$this->Name);
    }
    return $JobDone;
  }
}