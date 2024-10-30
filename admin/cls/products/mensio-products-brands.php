<?php
class mensio_products_brands extends mensio_core_db {
  private $Name;
  private $Slug;
  private $Logo;
  private $WebPage;
  private $Color;
  private $Visible;
  private $Language;
  private $Notes;
  private $Sorter;
  private $SearchString;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Name = '';
    $this->Slug = '';
    $this->Logo = '';
    $this->WebPage = '';
    $this->Color = '';
    $this->Visible = 0;
    $this->Language = '';
    $this->Notes = '';
    $this->Sorter = 'name';
    $this->SearchString = '';
  }
  final public function Set_Name($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' &.@()-');
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
  final public function Set_Logo($Value) {
		$SetOk = false;
    if ($Value === 'No Image') {
      $this->Logo = $Value;
      $SetOk = true;
    } else {
      $ClrVal = $this->ClearValue($Value,'AN','-_/.:');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $ClrVal = str_replace(get_site_url().'/','',$ClrVal);
        $this->Logo = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_WebPage($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN','-_/.:');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->WebPage = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Color($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN','#');
      if ((mb_strlen($ClrVal) === mb_strlen($Value)) && (mb_strlen($ClrVal) === 7)) {
        $this->Color = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Visible($Value) {
		$SetOk = false;
    if (($Value === true) || ($Value === '1') || ($Value === 1)) {
      $this->Visible = '1';
      $SetOk = true;
    } else {
      $this->Visible = '0';
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Notes($Value) {
		$SetOk = false;
    $this->Notes = addslashes($Value);
    $SetOk = true;
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
  final public function Set_Sorter($Value) {
		$SetOk = false;
		$ClrVal = $this->ClearValue($Value,'EN',' ');
		if (mb_strlen($ClrVal) === mb_strlen($Value)) {
			$this->Sorter = $ClrVal;
			$SetOk = true;
		}
		return $SetOk;
	}
  final public function Set_SearchString($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $Value = mb_ereg_replace('[^\p{L}\p{N}]', '%', $Value);
      $ClrVal = $this->ClearValue($Value,'AN','%');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->SearchString = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  public function GetNewBrandID() {
    return $this->GetNewUUID();
  }
  public function LoadProductBrandsDataSet($lang='store.adminlang') {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Searcher = '';
    if ($this->SearchString !== '') {
      $Searcher = 'WHERE name LIKE "%'.$this->SearchString.'%"
        OR webpage LIKE "%'.$this->SearchString.'%"';
    }
    $Query = 'SELECT * FROM '.$prfx.'brands '.$Searcher.' ORDER BY '.$this->Sorter;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadBrandsCategoriesList($ForAdmin=true) {
    $DataSet = array();
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'categories_codes.*, '.$prfx.'categories_names.name AS translation
        FROM '.$prfx.'categories_codes, '.$prfx.'categories_names
        WHERE '.$prfx.'categories_codes.uuid = '.$prfx.'categories_names.category
        AND '.$prfx.'categories_codes.uuid IN (
          SELECT '.$prfx.'categories_codes.uuid
          FROM '.$prfx.'categories_codes, '.$prfx.'categories_names, '.$prfx.'store,
            '.$prfx.'products_categories, '.$prfx.'products, '.$prfx.'brands
          WHERE '.$prfx.'categories_codes.uuid = '.$prfx.'categories_names.category
          AND '.$prfx.'categories_names.language = '.$prfx.'store.'.$lang.'
          AND '.$prfx.'categories_codes.uuid = '.$prfx.'products_categories.category
          AND '.$prfx.'products_categories.product = '.$prfx.'products.uuid
          AND '.$prfx.'products.brand = "'.$this->Get_UUID().'")
        ';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function GetBrandData() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'brands.*, '.$prfx.'store_slugs.slug
        FROM '.$prfx.'brands, '.$prfx.'store_slugs
        WHERE '.$prfx.'brands.uuid = '.$prfx.'store_slugs.uuid
        AND '.$prfx.'brands.uuid = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function GetBrandTranslations($lang='') {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      $Searcher = '';
      if ($lang !== '') { $Searcher = 'AND language = "'.$lang.'"'; }
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'brands_names WHERE brand = "'.$this->Get_UUID().'"
        '.$Searcher;
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function InsertNewBrand() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Logo === '') { $Error = true; }
    if ($this->WebPage === '') { $Error = true; }
    if ($this->Visible === '') { $this->Visible = '0'; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'brands (uuid,name,logo,webpage,color,visible)
          VALUES (%s,%s,%s,%s,%s,%s)',
        $this->Get_UUID(),
        $this->Name,
        $this->Logo,
        $this->WebPage,
        $this->Color,
        $this->Visible
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateBrandRecord() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Logo === '') { $Error = true; }
    if ($this->WebPage === '') { $Error = true; }
    if ($this->Color === '') { $Error = true; }
    if ($this->Visible === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'brands SET name = %s, logo = %s, webpage = %s, color= %s, visible = %s WHERE uuid = %s',
        $this->Name,
        $this->Logo,
        $this->WebPage,
        $this->Color,
        $this->Visible,
        $this->Get_UUID()
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateBrandTranslation() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Language === '') { $Error = true; }
    if ($this->Notes === '') { $this->Notes = 'none'; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'brands_names WHERE brand = %s AND language = %s',
        $this->Get_UUID(),
        $this->Language
      );
      $wpdb->query($Query);
      $Query = 'INSERT INTO '.$prfx.'brands_names (brand,language,notes)
      VALUES ("'.$this->Get_UUID().'","'.$this->Language.'","'.addslashes($this->Notes).'")';
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateBrandVisibility($Visible) {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'brands SET visible = %s WHERE uuid = %s',
        $Visible,
        $this->Get_UUID()
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function BrandIsNiUse() {
    $Answer = false;
    $Count = 0;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'products
        WHERE brand = "'.$this->Get_UUID().'"';
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
  public function DeleteBrandRecord() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'brands WHERE uuid = %s',
        $this->Get_UUID()
      );
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
  public function CheckIfBrandNameExist() {
    $Answer = false;
    $Count = 0;
    $AndSql = '';
    if ($this->Code !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      if ($this->Get_UUID() !== '') {
        $AndSql = 'AND uuid <> "'.$this->Get_UUID().'"';
      }
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'brands
            WHERE name = "'.$this->Name.'" '.$AndSql;
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Count = $Row->count; }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  public function UpdateBrandSlug() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Slug === '') { $Error = true; }
    if (!$Error) {
      $JobDone = $this->AddSlug('Brand',$this->Slug,$this->Get_UUID(),$this->Name);
    }
    return $JobDone;
  }
}