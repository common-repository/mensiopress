<?php
class mensio_products extends mensio_core_db {
  private $Code;
  private $Brand;
  private $BtBPrice;
  private $BtBTax;
  private $Price;
  private $Tax;
  private $Discount;
  private $Available;
  private $Changed;
  private $Status;
  private $Color;
  private $Stock;
  private $StockStatus;
  private $MinStock;
  private $Overstock;
  private $Visibility;
  private $Downloadable;
  private $Reviewable;
  private $IsBundle;
  private $Name;
  private $Description;
  private $Notes;
  private $Language;
  private $GlobalCategory;
  private $Category;
  private $AttributeValue;
  private $ImageID;
  private $Image;
  private $IsMain;
  private $Advantage;
  private $AdvantageID;
  private $Tags;
  private $TagsID;
  private $BarcodeType;
  private $BarcodeID;
  private $Barcode;
  private $FileID;
  private $FileType;
  private $File;
  private $Bundle;
  private $Amount;
  private $Slug;
  private $Variation;
  private $Sorter;
  private $SearchString;
  private $ExtraFilters;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Code = '';
    $this->Brand = '';
    $this->BtBPrice = 0;
    $this->BtBTax = 0;
    $this->Price = 0;
    $this->Tax = 0;
    $this->Discount = 0;
    $this->Available = '';
    $this->Changed = '';
    $this->Status = '';
    $this->StockStatus = '';
    $this->Color = '';
    $this->Stock = 0;
    $this->MinStock = 0;
    $this->Overstock = 0;
    $this->Visibility = '';
    $this->Downloadable = 0;
    $this->IsBundle = '';
    $this->Reviewable = '';
    $this->Name = '';
    $this->Description = '';
    $this->Notes = '';
    $this->Language = '';
    $this->GlobalCategory = $this->GetGlobalCategory();
    $this->Category = '';
    $this->AttributeValue = '';
    $this->ImageID = '';
    $this->Image = '';
    $this->IsMain = '';
    $this->Advantage = '';
    $this->AdvantageID = '';
    $this->Tags = '';
    $this->TagsID = '';
    $this->BarcodeType = '';
    $this->BarcodeID = '';
    $this->Barcode = '';
    $this->FileID = '';
    $this->FileType = '';
    $this->File = '';
    $this->Bundle = '';
    $this->Amount = 0;
    $this->Slug = '';
    $this->Variation = '';
    $this->Sorter = 'created';
    $this->SearchString = '';
    $this->ExtraFilters = '';
  }
  final public function Set_Code($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN','-');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Code = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Brand($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Brand = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_BtBPrice($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM',',.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->BtBPrice = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_BtBTax($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        if (($ClrVal >= 0) && ($ClrVal <= 100)) {
          $this->BtBTax = $ClrVal;
          $SetOk = true;
        }
      }
    }
		return $SetOk;
	}
  final public function Set_Price($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM',',.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Price = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Tax($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        if (($ClrVal >= 0) && ($ClrVal <= 100)) {
          $this->Tax = $ClrVal;
          $SetOk = true;
        }
      }
    }
		return $SetOk;
	}
  final public function Set_Discount($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        if (($ClrVal >= 0) && ($ClrVal <= 100)) {
          $this->Discount = $ClrVal;
          $SetOk = true;
        }
      }
    }
		return $SetOk;
	}
  final public function Set_Available($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $Value = str_replace('/', '-', $Value);
      $Value = str_replace('\\', '-', $Value);
      $ClrVal = $this->ClearValue($Value,'NM',':-');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Available = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Changed($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $Value = str_replace('/', '-', $Value);
      $Value = str_replace('\\', '-', $Value);
      $ClrVal = $this->ClearValue($Value,'NM',':-');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Changed = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Status($Value) {
		$SetOk = false;
    if ($Value !== '') {
      if ($Value === 'StockRelated') {
        $this->Status = 'StockRelated';
        $SetOk = true;
      } else {
        $ClrVal = $this->ClearUUID($Value);
        if ($ClrVal != false) {
          $this->Status = $ClrVal;
          $SetOk = true;
        }
      }
    }
		return $SetOk;
	}
  final public function Set_StockStatus($value) {
    $this->StockStatus = $value;
    $SetOk = true;
		return $SetOk;
  }
  final public function Set_Color($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN','#');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Color = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Stock($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM',',.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        if ($ClrVal >= 0) {
          $this->Stock = $ClrVal;
          $SetOk = true;
        }
      }
    }
		return $SetOk;
	}
  final public function Set_MinStock($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM',',.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        if ($ClrVal >= 0) {
          $this->MinStock = $ClrVal;
          $SetOk = true;
        }
      }
    }
		return $SetOk;
	}
  final public function Set_Overstock($Value) {
		$SetOk = false;
    if (($Value === 1) || ($Value === '1') || ($Value === true)) {
        $this->Overstock = '1';
        $SetOk = true;
    } else {
        $this->Overstock = '0';
        $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Visibility($Value) {
		$SetOk = false;
    if (($Value === 1) || ($Value === '1') || ($Value === true)) {
        $this->Visibility = '1';
        $SetOk = true;
    } else {
        $this->Visibility = '0';
        $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Downloadable($Value) {
		$SetOk = false;
    if (($Value === 1) || ($Value === '1') || ($Value === true)) {
        $this->Downloadable = '1';
        $SetOk = true;
    } else {
        $this->Downloadable = '0';
        $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_IsBundle($Value) {
		$SetOk = false;
    if (($Value === 1) || ($Value === '1') || ($Value === true)) {
        $this->IsBundle = '1';
        $SetOk = true;
    } else {
        $this->IsBundle = '0';
        $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Reviewable($Value) {
		$SetOk = false;
    if (($Value === 1) || ($Value === '1') || ($Value === true)) {
        $this->Reviewable = '1';
        $SetOk = true;
    } else {
        $this->Reviewable = '0';
        $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Name($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $this->Name = wp_kses_post($Value);
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Description($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $this->Description = wp_kses_post($Value);
      $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Notes($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $this->Notes = wp_kses_post($Value);
      $SetOk = true;
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
  final public function Set_Category($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Category = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_AttributeValue($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->AttributeValue = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_ImageID($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->ImageID = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Image($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN','-:_./');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $ClrVal = str_replace(get_site_url().'/','',$ClrVal);
        $this->Image = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_IsMain($Value) {
		$SetOk = false;
    if (($Value === 1) || ($Value === '1') || ($Value === true)) {
        $this->IsMain = '1';
        $SetOk = true;
    } else {
        $this->IsMain = '0';
        $SetOk = true;
    }
		return $SetOk;
	}
  final public function Set_Advantage($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',' ,.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Advantage = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_AdvantageID($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->AdvantageID = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Tags($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN',',');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Tags = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_TagsID($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->TagsID = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_BarcodeID($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->BarcodeID = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Barcode($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'EN', ' ');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Barcode = addslashes($ClrVal);
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_BarcodeType($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->BarcodeType = $ClrVal;
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
  final public function Set_FileID($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->FileID = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Bundle($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Bundle = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_Amount($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'NM',',.');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->Amount = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_FileType($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->FileType = $ClrVal;
        $SetOk = true;
      }
    }
		return $SetOk;
	}
  final public function Set_File($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearValue($Value,'AN','-:_./');
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $ClrVal = str_replace(get_site_url().'/','',$ClrVal);
        $this->File = $ClrVal;
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
  final public function Set_Variation($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Variation = $ClrVal;
        $SetOk = true;
      }
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
  public function GetGlobalCategory() {
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
  public function LoadProductCategoriesFilter($IsBundle=false) {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Searcher = '';
    if ($this->Brand !== '') {
      $Searcher .= ' AND uuid IN (SELECT category FROM '.$prfx.'products_categories, '.$prfx.'products
        WHERE '.$prfx.'products_categories.product = '.$prfx.'products.uuid
        AND '.$prfx.'products.brand = "'.$this->Brand.'")';
    }
    if ($this->Status !== '') {
      $Searcher .= ' AND uuid IN (SELECT category FROM '.$prfx.'products_categories, '.$prfx.'products
        WHERE '.$prfx.'products_categories.product = '.$prfx.'products.uuid
        AND '.$prfx.'products.status = "'.$this->Status.'")';
    }
    if ($IsBundle) {
      $Searcher .= ' AND uuid IN (SELECT category FROM '.$prfx.'products_categories, '.$prfx.'products
        WHERE '.$prfx.'products_categories.product = '.$prfx.'products.uuid
        AND '.$prfx.'products.isbundle = "1")';
    }
    $Query = 'SELECT * FROM '.$prfx.'categories_codes WHERE name != "GLOBAL"'.$Searcher.' ORDER BY name';
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
  public function LoadProductBrandFilter($IsBundle=false) {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Searcher = '';
    if ($this->Category !== '') {
      if ($Searcher === '') { $Searcher .= 'WHERE'; }
        else { $Searcher .= 'AND'; }
      $Searcher .= ' uuid IN (SELECT brand FROM '.$prfx.'products, '.$prfx.'products_categories
        WHERE '.$prfx.'products.uuid = '.$prfx.'products_categories.product
        AND '.$prfx.'products_categories.category = "'.$this->Category.'")';
    }
    if ($this->Status !== '') {
      if ($Searcher === '') { $Searcher .= 'WHERE'; }
        else { $Searcher .= 'AND'; }
      $Searcher .= ' uuid IN (SELECT brand FROM '.$prfx.'products WHERE '.$prfx.'products.status = "'.$this->Status.'")';
    }
    if ($IsBundle) {
      if ($Searcher === '') { $Searcher .= 'WHERE'; }
        else { $Searcher .= 'AND'; }
      $Searcher .= ' uuid IN (SELECT brand FROM '.$prfx.'products WHERE '.$prfx.'products.isbundle = "1")';
    }
    $Query = 'SELECT * FROM '.$prfx.'brands '.$Searcher;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadProductStatusFilter($IsBundle=false) {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Searcher = '';
    if ($this->Category !== '') {
      if ($Searcher === '') { $Searcher .= 'WHERE'; }
        else { $Searcher .= 'AND'; }
      $Searcher .= ' uuid IN (SELECT status FROM '.$prfx.'products, '.$prfx.'products_categories
        WHERE '.$prfx.'products.uuid = '.$prfx.'products_categories.product
        AND '.$prfx.'products_categories.category = "'.$this->Category.'")';
    }
    if ($this->Brand !== '') {
      if ($Searcher === '') { $Searcher .= 'WHERE'; }
        else { $Searcher .= 'AND'; }
      $Searcher .= ' uuid IN (SELECT status FROM '.$prfx.'products WHERE '.$prfx.'products.brand = "'.$this->Brand.'")';
    }
    if ($IsBundle) {
      if ($Searcher === '') { $Searcher .= 'WHERE'; }
        else { $Searcher .= 'AND'; }
      $Searcher .= ' uuid IN (SELECT status FROM '.$prfx.'products WHERE '.$prfx.'products.isbundle = "1")';
    }
    $Query = ' SELECT * FROM '.$prfx.'products_status '.$Searcher;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadStatusDataSet() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = ' SELECT * FROM '.$prfx.'products_status';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadStatusName($UUID) {
    $Name = '';
    if ($this->ClearUUID($UUID)) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = ' SELECT * FROM '.$prfx.'products_status WHERE uuid = "'.$UUID.'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) { $Name = $Row->name; }
      }
    }
    return $Name;
  }
  public function LoadProductsDataSet($ForAdmin=true) {
    $DataSet = array();
    $CompTbl = '';
    $Searcher = '';
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($this->ExtraFilters !== '') {
      if ((is_array($this->ExtraFilters)) && (!empty($this->ExtraFilters[0]))) {
        foreach ($this->ExtraFilters as $Row) {
          switch($Row['Field']) {
            case 'Categories':
              if (($Row['Value'] !== '') && ($Row['Value'] !== '0')) {
                $CompTbl = ','.$prfx.'products_categories';
                $Searcher .= '
                AND '.$prfx.'products.uuid = '.$prfx.'products_categories.product
                AND '.$prfx.'products_categories.category = "'.$Row['Value'].'"';
              }
              break;
            case 'Brands':
              if (($Row['Value'] !== '') && ($Row['Value'] !== '0')) {
                $Searcher .= 'AND '.$prfx.'products.brand = "'.$Row['Value'].'"';
              }
              break;
            case 'Status':
              if (($Row['Value'] !== '') && ($Row['Value'] !== '0')) {
                $Searcher .= 'AND '.$prfx.'products.status = "'.$Row['Value'].'"';
              }
              break;
            case 'Bundle':
              if (($Row['Value'] !== '') && ($Row['Value'] !== '0')) {
                $Searcher .= 'AND '.$prfx.'products.isbundle = "'.$Row['Value'].'"';
              }
              break;
          }
        }
      }
    }
    if ($this->SearchString !== '') {
      $Searcher .= 'AND (
          '.$prfx.'products_descriptions.description LIKE "%'.$this->SearchString.'%"
          OR '.$prfx.'products_descriptions.name LIKE "%'.$this->SearchString.'%"
          OR '.$prfx.'products_descriptions.notes LIKE "%'.$this->SearchString.'%"
          OR '.$prfx.'brands.name LIKE "%'.$this->SearchString.'%"
          OR '.$prfx.'products.code LIKE "%'.$this->SearchString.'%"
          OR '.$prfx.'products.price LIKE "%'.$this->SearchString.'%"
          OR '.$prfx.'products.btbprice LIKE "%'.$this->SearchString.'%"
          OR '.$prfx.'products.stock LIKE "%'.$this->SearchString.'%"
          OR '.$prfx.'products.minstock LIKE "%'.$this->SearchString.'%"
          OR '.$prfx.'products.created LIKE "%'.$this->SearchString.'%"
          OR '.$prfx.'products.available LIKE "%'.$this->SearchString.'%"
          OR '.$prfx.'products.changed LIKE "%'.$this->SearchString.'%")';
    }
    $NotVar = '';
    if (MENSIO_FLAVOR !== 'FREE') {
      $NotVar = 'AND '.$prfx.'products.uuid NOT IN (SELECT variation FROM '.$prfx.'products_variations)';
    }
    $Query = '
      SELECT '.$prfx.'products.*, '.$prfx.'products_descriptions.description,
        '.$prfx.'products_descriptions.name, '.$prfx.'products_descriptions.notes,
        '.$prfx.'products_images.file, '.$prfx.'brands.name as brandname
      FROM '.$prfx.'products, '.$prfx.'products_descriptions, '.$prfx.'brands,
        '.$prfx.'products_images, '.$prfx.'store'.$CompTbl.'
      WHERE '.$prfx.'products.uuid = '.$prfx.'products_descriptions.product
      AND '.$prfx.'products.uuid = '.$prfx.'products_images.product
      AND '.$prfx.'products.brand = '.$prfx.'brands.uuid
      AND  '.$prfx.'products_images.main = TRUE
      AND '.$prfx.'products_descriptions.language = '.$prfx.'store.'.$lang.'
      '.$Searcher.'
      '.$NotVar.'
      ORDER BY '.$this->Sorter;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadCategoryProductsList($ForAdmin=true) {
    $DataSet = array();
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Category !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'products.*, '.$prfx.'products_descriptions.description,
          '.$prfx.'products_descriptions.name, '.$prfx.'products_descriptions.notes,
          '.$prfx.'products_images.file, '.$prfx.'brands.name as brandname
        FROM '.$prfx.'products, '.$prfx.'products_categories, '.$prfx.'brands,
          '.$prfx.'products_images, '.$prfx.'products_descriptions, '.$prfx.'store
        WHERE '.$prfx.'products.uuid = '.$prfx.'products_descriptions.product
        AND '.$prfx.'products.uuid = '.$prfx.'products_images.product
        AND '.$prfx.'products.brand = '.$prfx.'brands.uuid
        AND  '.$prfx.'products_images.main = TRUE
        AND '.$prfx.'products_descriptions.language = '.$prfx.'store.'.$lang.'
        AND '.$prfx.'products.uuid = '.$prfx.'products_categories.product
        AND '.$prfx.'products_categories.category = "'.$this->Category.'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadBrandsProductsList($ForAdmin=true) {
    $DataSet = array();
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Brand !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'products.*, '.$prfx.'products_descriptions.description,
          '.$prfx.'products_descriptions.name, '.$prfx.'products_descriptions.notes,
          '.$prfx.'products_images.file, '.$prfx.'brands.name as brandname
        FROM '.$prfx.'products, '.$prfx.'products_descriptions, '.$prfx.'brands,
          '.$prfx.'products_images, '.$prfx.'store
        WHERE '.$prfx.'products.uuid = '.$prfx.'products_descriptions.product
        AND '.$prfx.'products.uuid = '.$prfx.'products_images.product
        AND '.$prfx.'products.brand = '.$prfx.'brands.uuid
        AND  '.$prfx.'products_images.main = TRUE
        AND '.$prfx.'products_descriptions.language = '.$prfx.'store.'.$lang.'
        AND '.$prfx.'products.brand = "'.$this->Brand.'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  function LoadProductRecordData($ForAdmin=true) {
    $DataSet = array();
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'products.*, '.$prfx.'products_descriptions.description,
          '.$prfx.'products_descriptions.name, '.$prfx.'products_descriptions.notes
        FROM '.$prfx.'products, '.$prfx.'products_descriptions, '.$prfx.'store
        WHERE '.$prfx.'products.uuid = '.$prfx.'products_descriptions.product
        AND '.$prfx.'products_descriptions.language = '.$prfx.'store.'.$lang.'
        AND '.$prfx.'products.uuid = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadVariationProductRecordData($simple=true) {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      if ($simple) {
        $Query = 'SELECT * FROM '.$prfx.'products WHERE uuid = "'.$this->Get_UUID().'"';
      } else {
        $Query = 'SELECT '.$prfx.'products.*, '.$prfx.'products_descriptions.description,
            '.$prfx.'products_descriptions.name, '.$prfx.'products_descriptions.notes
          FROM '.$prfx.'products, '.$prfx.'products_descriptions,
            '.$prfx.'products_variations, '.$prfx.'store
          WHERE '.$prfx.'products.uuid = '.$prfx.'products_variations.variation
          AND '.$prfx.'products_variations.product = '.$prfx.'products_descriptions.product
          AND '.$prfx.'products_descriptions.language = '.$prfx.'store.adminlang
          AND '.$prfx.'products.uuid = "'.$this->Get_UUID().'"';
      }
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadVariationAttributes() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'attributes.*, '.$prfx.'categories_codes.name as catname
        FROM '.$prfx.'attributes, '.$prfx.'categories_codes
        WHERE '.$prfx.'attributes.category = '.$prfx.'categories_codes.uuid
        AND '.$prfx.'attributes.category IN (SELECT category
          FROM '.$prfx.'products_categories WHERE product = "'.$this->Get_UUID().'"
        )
        AND '.$prfx.'attributes.uuid IN (SELECT '.$prfx.'attributes_values.attribute
          FROM '.$prfx.'attributes_values, '.$prfx.'products_attributes
          WHERE '.$prfx.'attributes_values.uuid = '.$prfx.'products_attributes.attribute_value
          AND '.$prfx.'products_attributes.product = "'.$this->Get_UUID().'"
        )';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function GetProductSlug() {
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
  public function LoadProductRecordTranslations() {
    $DataSet = array();
    if (($this->Get_UUID() !== '') && ($this->Language !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'products_descriptions
        WHERE product = "'.$this->Get_UUID().'"
        AND language = "'.$this->Language.'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadProductCategories() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'products_categories.*, '.$prfx.'categories_codes.name
        FROM '.$prfx.'products_categories, '.$prfx.'categories_codes
        WHERE '.$prfx.'products_categories.category = '.$prfx.'categories_codes.uuid
        AND '.$prfx.'products_categories.product = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadProductAttributeValues() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'products_attributes.*, '.$prfx.'attributes_values.uuid AS value_uuid,
        '.$prfx.'attributes_values.value, '.$prfx.'attributes.name
        FROM '.$prfx.'products_attributes, '.$prfx.'attributes_values, '.$prfx.'attributes
        WHERE '.$prfx.'products_attributes.attribute_value = '.$prfx.'attributes_values.uuid
        AND '.$prfx.'attributes_values.attribute = '.$prfx.'attributes.uuid
        AND '.$prfx.'products_attributes.product = "'.$this->Get_UUID().'"
        ORDER BY '.$prfx.'attributes.name';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadProductRecordImages($Main=true) {
    $DataSet = array();
    $Product = '';
    if ($Main) { $Product = $this->Get_UUID(); }
      else { $Product = $this->Variation; }
    if ($Product !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'products_images WHERE product = "'.$Product.'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadSpecificProductRecordImage() {
    $Image = '';
    if ($this->ImageID !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'products_images WHERE uuid = "'.$this->ImageID.'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Image = $Row->file;
        }
      }
    }
    return $Image;
  }
  public function LoadProductRecordMainImage($UUID) {
    $MainImage = plugins_url().'/mensiopress/admin/icons/default/noimage.png';
    if ($this->ClearUUID($UUID)) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'products_images WHERE product = "'.$UUID.'" AND main = TRUE';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) { $MainImage = get_site_url().'/'.$Row->file; }
      }
    }
    return $MainImage;
  }
  public function LoadProductAdvantages() {
    $DataSet = array();
    if (($this->Get_UUID() !== '') && ($this->Language !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'products_advantages
        WHERE product = "'.$this->Get_UUID().'"
        AND language = "'.$this->Language.'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadProductTags() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($this->Get_UUID() !== '') {
      $Query = 'SELECT * FROM '.$prfx.'products_tags
        WHERE product = "'.$this->Get_UUID().'"';
    }
    if ($this->TagsID !== '') {
      $Query = 'SELECT * FROM '.$prfx.'products_tags
        WHERE uuid = "'.$this->TagsID.'"';
    }
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadProductFileList() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($this->Get_UUID() !== '') {
      $Query = 'SELECT '.$prfx.'products_files.*, '.$prfx.'files_types.name
        FROM '.$prfx.'products_files, '.$prfx.'files_types
        WHERE '.$prfx.'products_files.filetype = '.$prfx.'files_types.uuid
        AND '.$prfx.'products_files.product = "'.$this->Get_UUID().'"
        ORDER BY '.$prfx.'products_files.filetype';
        $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadProductBarcodesTypes() {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'barcodes';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function LoadProductBarcodeList() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'products_barcodes.*, '.$prfx.'barcodes.name
        FROM '.$prfx.'products_barcodes, '.$prfx.'barcodes
        WHERE '.$prfx.'products_barcodes.type = '.$prfx.'barcodes.uuid
        AND '.$prfx.'products_barcodes.product = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadBundleProductList($ForAdmin=true) {
    $DataSet = array();
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
      SELECT DISTINCT '.$prfx.'products.*, '.$prfx.'products_descriptions.description,
        '.$prfx.'products_descriptions.name, '.$prfx.'products_descriptions.notes,
        '.$prfx.'products_images.file, '.$prfx.'products_bundles.amount
      FROM '.$prfx.'products, '.$prfx.'products_descriptions, '.$prfx.'store,
        '.$prfx.'products_images, '.$prfx.'products_bundles
      WHERE (
            '.$prfx.'products.uuid = '.$prfx.'products_descriptions.product
        AND '.$prfx.'products_descriptions.language = '.$prfx.'store.adminlang
        AND '.$prfx.'products.uuid = '.$prfx.'products_images.product
        AND '.$prfx.'products_images.main = TRUE
        AND '.$prfx.'products.uuid = '.$prfx.'products_bundles.product
        AND '.$prfx.'products_bundles.bundle = "'.$this->Get_UUID().'")
      OR (
            '.$prfx.'products.uuid = '.$prfx.'products_images.product
        AND '.$prfx.'products_images.main = TRUE
        AND '.$prfx.'products_descriptions.language = '.$prfx.'store.adminlang
        AND '.$prfx.'products_descriptions.product IN (
          SELECT product FROM '.$prfx.'products_variations WHERE variation IN (
            SELECT product FROM '.$prfx.'products_bundles
            WHERE bundle = "'.$this->Get_UUID().'"
          )
        )
        AND '.$prfx.'products.uuid IN (
          SELECT variation FROM '.$prfx.'products_variations
          WHERE variation IN (
            SELECT product FROM '.$prfx.'products_bundles
            WHERE bundle = "'.$this->Get_UUID().'"
          )
        )
      )';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function InsertNewProductStatus() {
    $JobDone = false;
    if ($this->Name !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'products_status (uuid,name,icon,color) VALUES (%s,%s,%s,%s)',
        $this->GetNewUUID(),
        $this->Name,
        $this->Image,
        $this->Color
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateProductStatus() {
    $JobDone = false;
    $Error = false;
    if ($this->Status === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Image === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_status SET name = %s, icon = %s, color = %s WHERE uuid = %s',
        $this->Name,
        $this->Image,
        $this->Color,
        $this->Status
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function CheckStatusUse() {
    $Answer = false;
    $Count = 0;
    if ($this->Status !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'products
        WHERE status = "'.$this->Status.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Count = $Row->count; }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  public function CheckStatusNameExists() {
    $Answer = false;
    $Count = 0;
    if ($this->Status !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'products_status
        WHERE name = "'.$this->Name.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Count = $Row->count; }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  public function RemoveProductStatusRecord() {
    $JobDone = false;
    if ($this->Status !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_status WHERE uuid = %s',
        $this->Status
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function Get_NewProductCode() {
    return $this->GetNewUUID();    
  }
  public function InsertNewProductMainData() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Code === '') { $Error = true; }
    if ($this->Brand === '') { $Error = true; }
    if ($this->BtBPrice === '') { $Error = true; }
    if ($this->BtBTax === '') { $Error = true; }
    if ($this->Price === '') { $Error = true; }
    if ($this->Tax === '') { $Error = true; }
    if ($this->Discount === '') { $Error = true; }
    if ($this->Available === '') { $Error = true; }
    if ($this->Status === '') { $Error = true; }
    if ($this->Stock === '') { $Error = true; }
    if ($this->Overstock === '') { $Error = true; }
    if ($this->MinStock === '') { $Error = true; }
    if ($this->Visibility === '') { $Error = true; }
    if ($this->Downloadable === '') { $Error = true; }
    if ($this->Reviewable === '') { $Error = true; }
    if (!$Error) {
      $RDate = date("Y-m-d H:i:s");
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'products (uuid,guuid,code,brand,btbprice,btbtax,price,tax,
         discount,created,available,changed,status,stock,minstock,overstock,visibility,
         downloadable,isbundle,reviewable) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)',
        $this->Get_UUID(),
        $this->GetNewUUID(),
        $this->Code,
        $this->Brand,
        $this->BtBPrice,
        $this->BtBTax,
        $this->Price,
        $this->Tax,
        $this->Discount,
        $RDate,
        $this->Available,
        $RDate,
        $this->Status,
        $this->Stock,
        $this->MinStock,
        $this->Overstock,
        $this->Visibility,
        $this->Downloadable,
        $this->IsBundle,
        $this->Reviewable
      );
      if (false !== $wpdb->query($Query)) {
        $BasicImage = MENSIO_SHORTPATH.'/admin/icons/default/noimage.png';
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'products_images
            (uuid,product,file,main) VALUES (%s,%s,%s,1)',
          $this->GetNewUUID(),
          $this->Get_UUID(),
          $BasicImage
        );
        if (false !== $wpdb->query($Query)) {
          $Query = $wpdb->prepare(
            'INSERT INTO '.$prfx.'products_categories (product,category) VALUES (%s,%s)',
            $this->Get_UUID(),
            $this->GlobalCategory
          );
          if (false !== $wpdb->query($Query)) { $JobDone = true; }
        }
      }
    }
    return $JobDone;
  }
  public function UpdateProductMainData() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Code === '') { $Error = true; }
    if ($this->Brand === '') { $Error = true; }
    if ($this->BtBPrice === '') { $Error = true; }
    if ($this->BtBTax === '') { $Error = true; }
    if ($this->Price === '') { $Error = true; }
    if ($this->Tax === '') { $Error = true; }
    if ($this->Discount === '') { $Error = true; }
    if ($this->Available === '') { $Error = true; }
    if ($this->Status === '') { $Error = true; }
    if ($this->Stock === '') { $Error = true; }
    if ($this->MinStock === '') { $Error = true; }
    if ($this->Overstock === '') { $Error = true; }
    if ($this->Visibility === '') { $Error = true; }
    if ($this->Downloadable === '') { $Error = true; }
    if ($this->Reviewable === '') { $Error = true; }
    if (!$Error) {
      $RDate = date("Y-m-d H:i:s");
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products SET code = %s, brand = %s, btbprice = %s, btbtax = %s,
          price = %s, tax=%s, discount = %s, available = %s, changed = %s,
          status = %s, stock = %s, minstock = %s, overstock = %s, visibility = %s,
          downloadable = %s, reviewable = %s
         WHERE uuid = %s',
        $this->Code,
        $this->Brand,
        $this->BtBPrice,
        $this->BtBTax,
        $this->Price,
        $this->Tax,
        $this->Discount,
        $this->Available,
        $RDate,
        $this->Status,
        $this->Stock,
        $this->MinStock,
        $this->Overstock,
        $this->Visibility,
        $this->Downloadable,
        $this->Reviewable,
        $this->Get_UUID()
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateProductVisibility() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products SET visibility = %s WHERE uuid = %s',
        $this->Visibility,
        $this->Get_UUID()
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateProductTranslations() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Language === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if ($this->Description === '') { $Error = true; }
    if ($this->Notes === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_descriptions WHERE product = %s AND language = %s',
        $this->Get_UUID(),
        $this->Language
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'products_descriptions
          (product,language,description,name,notes) VALUES (%s,%s,%s,%s,%s)',
        $this->Get_UUID(),
        $this->Language,
        $this->Description,
        $this->Name,
        $this->Notes
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function InsertProductToCategory() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Category === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'products_categories
          (product,category) VALUES (%s,%s)',
        $this->Get_UUID(),
        $this->Category
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RemoveProductCategory() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Category === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_categories WHERE product = %s AND category = %s',
        $this->Get_UUID(),
        $this->Category
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function InsertProductAttributeValue() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->AttributeValue === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'attributes_values WHERE uuid = "'.$this->AttributeValue.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Attribute = $Row->attribute; }
        $Query = $wpdb->prepare(
          'DELETE FROM '.$prfx.'products_attributes
            WHERE product = %s
            AND attribute_value IN 
            (SELECT uuid FROM '.$prfx.'attributes_values WHERE attribute = %s)',
          $this->Get_UUID(),
          $Attribute
        );
        $wpdb->query($Query);      
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'products_attributes
            (product,attribute_value,visibility) VALUES (%s,%s,1)',
          $this->Get_UUID(),
          $this->AttributeValue
        );
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
  public function RemoveProductAttributeValue() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->AttributeValue === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_attributes
          WHERE product = %s AND attribute_value = %s',
        $this->Get_UUID(),
        $this->AttributeValue
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function CheckIfProductCodeExists($main=true) {
    $Answer = false;
    $Count = 0;
    $AndSql = '';
    if ($this->Code !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      if ($main) {
        if ($this->Get_UUID() !== '') {
          $AndSql = 'AND uuid != "'.$this->Get_UUID().'"';
        }
      } else {
        if ($this->Variation !== '') {
          $AndSql = 'AND uuid != "'.$this->Variation.'"';
        }
      }
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'products
            WHERE code = "'.$this->Code.'" '.$AndSql;
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Count = $Row->count; }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  public function CheckIfCategoryInUse() {
    $Answer = false;
    $Count = 0;
    if (($this->Get_UUID() !== '') && ($this->Category !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'products_attributes
        WHERE '.$prfx.'products_attributes.product = "'.$this->Get_UUID().'"
        AND '.$prfx.'products_attributes.attribute_value IN (
            SELECT '.$prfx.'attributes_values.uuid
            FROM '.$prfx.'attributes_values, '.$prfx.'attributes
            WHERE '.$prfx.'attributes_values.attribute = '.$prfx.'attributes.uuid
            AND '.$prfx.'attributes.category = "'.$this->Category.'"
        )';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Count = $Row->count; }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  public function UpdateValueVisibility() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->AttributeValue === '') { $Error = true; }
    if ($this->Visibility === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_attributes
          SET visibility = %s
          WHERE product = %s AND attribute_value = %s',
        $this->Visibility,
        $this->Get_UUID(),
        $this->AttributeValue
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function ProductImageFound() {
    $Answer = false;
    $Count = 0;
    if (($this->Get_UUID() !== '') && ($this->Image !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'products_images
        WHERE product = "'.$this->Get_UUID().'"
        AND file = "'.$this->Image.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Count = $Row->count; }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  public function ProductFileFound() {
    $Answer = false;
    $Count = 0;
    if (($this->Get_UUID() !== '') && ($this->File !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'products_files
        WHERE product = "'.$this->Get_UUID().'"
        AND file = "'.$this->File.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Count = $Row->count; }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  public function VariableProductImageExists() {
    $Found = false;
    $Error = false;
    if ($this->Variation === '') { $Error = true; }
    if ($this->Image === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'products_images
        WHERE product = "'.$this->Variation.'" AND file = "'.$this->Image.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Count = $Row->count; }
      }
      if ($Count > 0) { $Found = true; }
    }
    return $Found;
  }
  public function InsertProductImage($Main=true) {
    $JobDone = false;
    $Error = false;
    $Product = '';
    if ($Main) { $Product = $this->Get_UUID(); }
      else { $Product = $this->Variation; }
    if ($Product === '') { $Error = true; }
    if ($this->Image === '') { $Error = true; }
    if (!$Error) {
      $NewImgID = $this->GetNewUUID();
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'products_images (uuid,product,file,main) VALUES (%s,%s,%s,0)',
        $NewImgID,
        $Product,
        $this->Image
      );
      if (false !== $wpdb->query($Query)) {
        $JobDone = true;
        $this->ImageID = $NewImgID;
      }
    }
    return $JobDone;
  }
  public function UpdateMainImage() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->ImageID === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_images Set main = FALSE WHERE product = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_images Set main = TRUE WHERE uuid = %s',
        $this->ImageID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RemoveProductImage() {
    $JobDone = false;
    if (($this->Get_UUID() !== '') && ($this->ImageID !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_images WHERE uuid = %s',
        $this->ImageID
      );
      if (false !== $wpdb->query($Query)) {
        $BasicImage = MENSIO_SHORTPATH.'/admin/icons/default/noimage.png';
        $Query = $wpdb->prepare(
          'UPDATE '.$prfx.'products_images Set main = 1
            WHERE product = %s AND file = %s',
          $this->Get_UUID(),
          $BasicImage
        );
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
  public function InsertNewProductAdvantage() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Language === '') { $Error = true; }
    if ($this->Advantage === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'products_advantages
          (uuid,product,language,advantage) VALUES (%s,%s,%s,%s)',
        $this->GetNewUUID(),
        $this->Get_UUID(),
        $this->Language,
        $this->Advantage
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RemoveProductAdvantage() {
    $JobDone = false;
    if ($this->AdvantageID !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_advantages WHERE uuid = %s',
        $this->AdvantageID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateProductTagList() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->TagsID === '') { $Error = true; }
    if ($this->Tags === '') { $Error = true; }
    if (!$Error) {
      if ($this->RemoveProductTag()) {
        global $wpdb;
        $prfx = $wpdb->prefix.'mns_';
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'products_tags
            (uuid,product,tags) VALUES (%s,%s,%s)',
          $this->TagsID,
          $this->Get_UUID(),
          $this->Tags
        );
        if (false !== $wpdb->query($Query)) {
          $post = get_page_by_path(
            $this->GetProductSlug(),
            OBJECT,
            'mensio_product'
          );
          $id = $post->ID;
          $TagArray = explode(',',stripslashes($this->Tags));
          wp_set_post_tags($id,$TagArray);
          $JobDone = true;
        }
      }
    }
    return $JobDone;
  }
  public function RemoveProductTag() {
    $JobDone = false;
    if ($this->TagsID !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_tags WHERE uuid = %s',
        $this->TagsID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function InsertNewBarcodeType() {
    $JobDone = false;
    if ($this->Name !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'barcodes (uuid,name,public) VALUES (%s,%s,FALSE)',
        $this->GetNewUUID(),
        $this->Name
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdatePublicBarcode() {
    $JobDone = false;
    if ($this->BarcodeType !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'UPDATE '.$prfx.'barcodes SET public = FALSE';
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'barcodes SET public = TRUE WHERE uuid = %s',
        $this->BarcodeType
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function CheckIfBCTypeInUse() {
    $Answer = false;
    $Count = 0;
    if ($this->BarcodeType !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'products_barcodes
        WHERE type = "'.$this->BarcodeType.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Count = $Row->count; }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  public function RemoveBarcodeType() {
    $JobDone = false;
    if ($this->BarcodeType !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'barcodes WHERE uuid = %s',
        $this->BarcodeType
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function InsertNewProductBarcode() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->BarcodeType === '') { $Error = true; }
    if ($this->Barcode === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'products_barcodes
          (uuid,product,type,barcode) VALUES (%s,%s,%s,%s)',
        $this->GetNewUUID(),
        $this->Get_UUID(),
        $this->BarcodeType,
        $this->Barcode
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RemoveProductBarcode() {
    $JobDone = false;
    if ($this->BarcodeID !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_barcodes WHERE uuid = %s',
        $this->BarcodeID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function FindTypeID($Type) {
    $TypeID = '';
    $ClrType = $this->ClearValue($Type,'EN');
    if (mb_strlen($ClrType) === mb_strlen($Type)) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'files_types WHERE name = "'.$ClrType.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $TypeID = $Row->uuid; }
      }
    }    
    return $TypeID;
  }
  public function InsertProductFile() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->FileType === '') { $Error = true; }
    if ($this->File === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'products_files
          (uuid,product,filetype,file,dnldtimes,expiration)
          VALUES (%s,%s,%s,%s,0,"2100-12-31 00:00:00")',
        $this->GetNewUUID(),
        $this->Get_UUID(),
        $this->FileType,
        $this->File
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateFileExpiration() {
    $JobDone = false;
    if (($this->FileID !== '') && ($this->Available !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_files SET expiration = %s WHERE uuid = %s',
        $this->Available,
        $this->FileID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RemoveProductFile() {
    $JobDone = false;
    if ($this->FileID !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_files WHERE uuid = %s',
        $this->FileID
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function CheckIfProductInBundle() {
    $Answer = false;
    $Count = 0;
    if (($this->Get_UUID() !== '') && ($this->Bundle !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'products_bundles
        WHERE bundle = "'.$this->Bundle.'" AND product = "'.$this->Get_UUID().'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Count = $Row->count; }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  public function InsertProductToBundle() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Bundle === '') { $Error = true; }
    if ($this->Amount === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'products_bundles
          (bundle,product,amount) VALUES (%s,%s,%s)',
        $this->Bundle,
        $this->Get_UUID(),
        $this->Amount
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function RemoveProductFromBundle() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Bundle === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_bundles WHERE bundle = %s AND product = %s',
        $this->Bundle,
        $this->Get_UUID()
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function CheckIfProductInOrders() {
    $Answer = false;
    $Count = 0;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'orders_products
        WHERE product = "'.$this->Get_UUID().'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Count = $Row->count; }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  public function CheckIfProductHasVariations() {
    $Answer = false;
    $Count = 0;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'products_variations
        WHERE product = "'.$this->Get_UUID().'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Count = $Row->count; }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  public function DeleteProductRecord() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_bundles WHERE product = %s OR bundle = %s',
        $this->Get_UUID(),
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_files WHERE product = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_descriptions WHERE product = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_tags WHERE product = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_attributes WHERE product = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_images WHERE product = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_categories WHERE product = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_relations WHERE product = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_variations WHERE variation = %s OR product = %s',
        $this->Get_UUID(),
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_barcodes WHERE product = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_advantages WHERE product = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_stock_status_descriptions
         WHERE stock_status IN
         (SELECT uuid FROM '.$prfx.'products_stock WHERE product = %s)',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_stock_status WHERE product = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products WHERE uuid = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'store_slugs WHERE type = "Product" AND uuid = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = 'DELETE FROM '.$wpdb->prefix.'posts WHERE post_content = "[mensioobject uuid=\"'.$this->Get_UUID().'\"]"';
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function CheckDownloableFIles() {
    $Answer = false;
    $Count = 0;
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT COUNT(*) AS count FROM '.$prfx.'products_files
        WHERE product = "'.$this->Get_UUID().'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Count = $Row->count; }
      }
      if ($Count > 0) { $Answer = true; }
    }
    return $Answer;
  }
  public function UpdateProductSlug() {
    $JobDone = true;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if (($this->Slug !== '') && ($this->Get_UUID() !== '')) {
      $Query = 'SELECT '.$prfx.'products_descriptions.*, '.$prfx.'languages_codes.code
        FROM '.$prfx.'products_descriptions, '.$prfx.'languages_codes
        WHERE '.$prfx.'products_descriptions.language = '.$prfx.'languages_codes.uuid
        AND '.$prfx.'products_descriptions.product = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $check = $this->AddSlug('Product',$this->Slug,$this->Get_UUID(),($Row->code.'::'.$Row->name));
          if (!$check) { $JobDone = false; }
        }
      }
    }
    return $JobDone;
  }
  public function LoadProductVariationList($simple=true) {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      if ($simple) {
        $Query = 'SELECT * FROM '.$prfx.'products WHERE uuid IN (
        SELECT variation FROM '.$prfx.'products_variations
          WHERE product = "'.$this->Get_UUID().'" )
        ORDER BY '.$prfx.'products.code DESC';
      } else {
        $Query = 'SELECT '.$prfx.'products.*, '.$prfx.'products_descriptions.name,
          '.$prfx.'products_images.file
        FROM '.$prfx.'products, '.$prfx.'products_descriptions, '.$prfx.'products_images, '.$prfx.'store
        WHERE '.$prfx.'products.uuid = '.$prfx.'products_images.product
        AND  '.$prfx.'products_images.main = TRUE
        AND '.$prfx.'products_descriptions.product = "'.$this->Get_UUID().'"
        AND '.$prfx.'products_descriptions.language = '.$prfx.'store.adminlang
        AND '.$prfx.'products.uuid IN (SELECT variation FROM '.$prfx.'products_variations
          WHERE product = "'.$this->Get_UUID().'")
        ORDER BY '.$prfx.'products.code DESC';
      }
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function GetCodeVars($VarCode) {
    $Found = 0;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT COUNT(*) as found FROM '.$prfx.'products WHERE code LIKE "'.$VarCode.'-var%"';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) { $Found = $Row->found; }
    }
    return $Found;
  }
  public function InsertNewProductVariation() {
    $NewProduct = false;
    $Error = false;
    $NewID = $this->AddNewVariationBasicData();
    if (false !== $NewID) {
      if (!$this->AddNewVariationCategories($NewID)) { $Error = true; }
      if (!$this->AddNewVariationAttributeValues($NewID)) { $Error = true; }
      if (!$Error) {
        global $wpdb;
        $prfx = $wpdb->prefix.'mns_';
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'products_variations (product,variation) VALUES (%s,%s)',
          $this->Get_UUID(),
          $NewID
        );
        if (false !== $wpdb->query($Query)) { $NewProduct = $NewID; }
      }
    }
    return $NewProduct;
  }
  public function SetVariableMainImage() {
    if ($this->ImageID !== '') {
      $RecDate = date("Y-m-d H:i:s");
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'products_images WHERE uuid = "'.$this->ImageID.'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $this->Set_Image($Row->file);
        }
      }
    }
  }
  public function AddNewVariationBasicData() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      $RecDate = date("Y-m-d H:i:s");
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'products WHERE uuid = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Query = $wpdb->prepare(
            'INSERT INTO '.$prfx.'products (uuid,guuid,code,brand,btbprice,btbtax,price,tax,
             discount,created,available,changed,status,stock,minstock,overstock,visibility,
             downloadable,isbundle,reviewable) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)',
            $this->Variation,
            $this->GetNewUUID(),
            $this->Code,
            $Row->brand,
            $this->BtBPrice,
            $this->BtBTax,
            $this->Price,
            $this->Tax,
            $this->Discount,
            $RecDate,
            $this->Available,
            $RecDate,
            $this->Status,
            $this->Stock,
            $this->MinStock,
            $Row->overstock,
            $this->Visibility,
            $Row->downloadable,
            $Row->isbundle,
            $Row->reviewable
          );
          if (false !== $wpdb->query($Query)) {
            $BasicImage = MENSIO_SHORTPATH.'/admin/icons/default/noimage.png';
            $Query = $wpdb->prepare(
              'INSERT INTO '.$prfx.'products_images
                (uuid,product,file,main) VALUES (%s,%s,%s,0)',
              $this->GetNewUUID(),
              $this->Variation,
              $BasicImage
            );
            if (false !== $wpdb->query($Query)) { $JobDone = true; }
          }
        }
      }
    }
    return $JobDone;
  }
   public function UpdateVariableMainImage() {
    $JobDone = false;
    if (($this->Variation !== '') && ($this->Image !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_images SET main = "0" WHERE product = %s',
        $this->Variation
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_images SET main = "1" WHERE product = %s AND file = %s',
        $this->Variation,
        $this->Image
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
   }
  public function UpdateVariationMainData() {
    $JobDone = false;
    $Error = false;
    if ($this->Variation === '') { $Error = true; }
    if ($this->Code === '') { $Error = true; }
    if ($this->BtBPrice === '') { $Error = true; }
    if ($this->BtBTax === '') { $Error = true; }
    if ($this->Price === '') { $Error = true; }
    if ($this->Tax === '') { $Error = true; }
    if ($this->Discount === '') { $Error = true; }
    if ($this->Available === '') { $Error = true; }
    if ($this->Status === '') { $Error = true; }
    if ($this->Stock === '') { $Error = true; }
    if ($this->MinStock === '') { $Error = true; }
    if ($this->Visibility === '') { $Error = true; }
    if (!$Error) {
      $RDate = date("Y-m-d H:i:s");
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products SET code = %s, btbprice = %s, btbtax = %s,
          price = %s, tax=%s, discount = %s, available = %s, changed = %s,
          status = %s, stock = %s, minstock = %s, visibility = %s
         WHERE uuid = %s',
        $this->Code,
        $this->BtBPrice,
        $this->BtBTax,
        $this->Price,
        $this->Tax,
        $this->Discount,
        $this->Available,
        $RDate,
        $this->Status,
        $this->Stock,
        $this->MinStock,
        $this->Visibility,
        $this->Variation
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function AddNewVariationCategories() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Variation === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'DELETE FROM '.$prfx.'products_categories WHERE product = "'.$this->Variation.'"';
      $wpdb->query($Query);
      $Query = 'DELETE FROM '.$prfx.'products_attributes WHERE product = "'.$this->Variation.'"';
      $wpdb->query($Query);
      $Query = 'INSERT INTO '.$prfx.'products_categories (product,category)
        SELECT "'.$this->Variation.'", category
        FROM '.$prfx.'products_categories WHERE product = "'.$this->Get_UUID().'"';
      $wpdb->query($Query);
      $Query = 'INSERT INTO '.$prfx.'products_attributes (product,attribute_value,visibility)
        SELECT "'.$this->Variation.'", attribute_value, visibility
        FROM '.$prfx.'products_attributes WHERE product = "'.$this->Get_UUID().'"';
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateVariableAttributeValue() {
    $JobDone = false;
    $Error = false;
    if ($this->Variation === '') { $Error = true; }
    if ($this->AttributeValue === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT * FROM '.$prfx.'attributes_values WHERE uuid = "'.$this->AttributeValue.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $Attribute = $Row->attribute; }
        $Query = $wpdb->prepare(
          'DELETE FROM '.$prfx.'products_attributes
            WHERE product = %s
            AND attribute_value IN 
            (SELECT uuid FROM '.$prfx.'attributes_values WHERE attribute = %s)',
          $this->Variation,
          $Attribute
        );
        $wpdb->query($Query);      
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'products_attributes
            (product,attribute_value,visibility) VALUES (%s,%s,1)',
          $this->Variation,
          $this->AttributeValue
        );
        if (false !== $wpdb->query($Query)) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
  public function UpdateVariableIndex() {
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Variation === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'products_variations (product,variation) VALUES (%s,%s)',
        $this->Get_UUID(),
        $this->Variation
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
  public function UpdateMainProductRecord() {
    $JobDone = false;
    $Error = false;
    if (($this->Get_UUID() !== '') && ($this->Variation !== '')) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_bundles SET product = %s WHERE product = %s',
        $this->Variation,
        $this->Get_UUID()
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_files SET product = %s WHERE product = %s',
        $this->Variation,
        $this->Get_UUID()
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_descriptions SET product = %s WHERE product = %s',
        $this->Variation,
        $this->Get_UUID()
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_tags SET product = %s WHERE product = %s',
        $this->Variation,
        $this->Get_UUID()
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_barcodes SET product = %s WHERE product = %s',
        $this->Variation,
        $this->Get_UUID()
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_advantages SET product = %s WHERE product = %s',
        $this->Variation,
        $this->Get_UUID()
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_relations SET product = %s WHERE product = %s',
        $this->Variation,
        $this->Get_UUID()
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'store_slugs SET uuid = %s WHERE type = "Product" AND uuid = %s',
        $this->Variation,
        $this->Get_UUID()
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'UPDATE '.$wpdb->prefix.'posts SET post_content = "[mensioobject uuid=\"'.$this->Variation.'\"]"
        WHERE post_content = "[mensioobject uuid=\"'.$this->Get_UUID().'\"]"';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_images SET product = %s WHERE product = %s',
        'REPLACE',
        $this->Variation
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_images SET product = %s WHERE product = %s',
        $this->Variation,
        $this->Get_UUID()
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_images SET product = %s WHERE product = %s',
        $this->Get_UUID(),
        'REPLACE'
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_variations SET product = %s WHERE product = %s',
        $this->Variation,
        $this->Get_UUID()
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'products_variations SET variation = %s WHERE variation = %s',
        $this->Get_UUID(),
        $this->Variation
      );
      if (false === $wpdb->query($Query)) { $Error = true; }
      if (!$Error) { $JobDone = true; }
    }
    return $JobDone;
  }
  final public function GetStatusTranslation($Type='default') {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Name = '';
    $Error = false;
    if ($this->Status === '') { $Error = true; }
    if ($this->Language === '') { $Error = true; }
    if (!$Error) {
      if ($Type !== 'Stock') {
        $Query = 'SELECT * FROM '.$prfx.'products_status_descriptions
        WHERE status = "'.$this->Status.'"
        AND language = "'.$this->Language.'"';
      } else {
        $Query = 'SELECT * FROM '.$prfx.'products_stock_status_descriptions
        WHERE stock_status = "'.$this->Status.'"
        AND language = "'.$this->Language.'"';
      }
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Name = $Row->name;
        }
      }
    }
    return $Name;
  }
  public function UpdateStatusTranslations($Type) {
    $JobDone = false;
    $Error = false;
    if ($this->Status === '') { $Error = true; }
    if ($this->Language === '') { $Error = true; }
    if ($this->Name === '') { $Error = true; }
    if (!$Error) {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      if ($Type !== 'Stock') {
        $Query = $wpdb->prepare(
          'DELETE FROM '.$prfx.'products_status_descriptions
            WHERE status = %s AND language = %s',
          $this->Status,
          $this->Language
        );      
        $wpdb->query($Query);
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'products_status_descriptions (status,language,name)
            VALUES (%s,%s,%s)',
          $this->Status,
          $this->Language,
          $this->Name
        );      
        if ($wpdb->query($Query) !== false) { $JobDone = true; }
      } else {
        $Query = $wpdb->prepare(
          'DELETE FROM '.$prfx.'products_stock_status_descriptions
            WHERE stock_status = %s AND language = %s',
          $this->Status,
          $this->Language
        );      
        $wpdb->query($Query);
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'products_stock_status_descriptions (stock_status,language,name)
            VALUES (%s,%s,%s)',
          $this->Status,
          $this->Language,
          $this->Name
        );      
        if ($wpdb->query($Query) !== false) { $JobDone = true; }
      }
    }
    return $JobDone;
  }
  final public function LoadVariationName() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Name = '';
    $Error = false;
    if ($this->Variation === '') { $Error = true; }
    if ($this->Language === '') { $Error = true; }
    if (!$Error) {
      $Query = 'SELECT * FROM '.$prfx.'products_descriptions
        WHERE product = "'.$this->Variation.'" AND language = "'.$this->Language.'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Name = $Row->name;
        }
      }
      if ($Name === '') {
        $Query = 'SELECT * FROM '.$prfx.'products_descriptions
          WHERE language = "'.$this->Language.'" AND product IN (
            SELECT product FROM '.$prfx.'products_variations WHERE variation = "'.$this->Variation.'"
          )';
        $DataSet = $wpdb->get_results($Query);
        if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
          foreach ($DataSet as $Row) {
            $Name = $Row->name;
          }
        }
      }
    }
    return $Name;
  }
  public function UpdateVariationTranslation() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $JobDone = false;
    $Error = false;
    if ($this->Get_UUID() === '') { $Error = true; }
    if ($this->Variation === '') { $Error = true; }
    if ($this->Language === '') { $Error = true; }
    if (!$Error) {
      $Query = 'SELECT * FROM '.$prfx.'products_descriptions
        WHERE product = "'.$this->Get_UUID().'" AND language = "'.$this->Language.'"';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $Descr = $Row->description;
          $Notes = $Row->notes;
        }
      }      
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_descriptions WHERE product = %s AND language = %s',
        $this->Variation,
        $this->Language
      );      
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'products_descriptions (product,language,description,name,notes)
          VALUES (%s,%s,%s,%s,%s)',
        $this->Variation,
        $this->Language,
        $Descr,
        $this->Name,
        $Notes
      );      
      if ($wpdb->query($Query) !== false) { $JobDone = true; }      
    }
    return $JobDone;
  }
  public function LoadStockStatus() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'products_stock_status.*, '.$prfx.'products_stock_status_descriptions.name
      FROM '.$prfx.'products_stock_status, '.$prfx.'products_stock_status_descriptions, '.$prfx.'store
      WHERE '.$prfx.'products_stock_status.product = "'.$this->Get_UUID().'"
      AND '.$prfx.'products_stock_status.uuid = '.$prfx.'products_stock_status_descriptions.stock_status
      AND '.$prfx.'products_stock_status_descriptions.language = '.$prfx.'store.adminlang
      ORDER BY '.$prfx.'products_stock_status.stock DESC';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadStockStatusData() {
    $DataSet = array();
    if ($this->Status !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'products_stock_status.*, '.$prfx.'products_stock_status_descriptions.name
      FROM '.$prfx.'products_stock_status, '.$prfx.'products_stock_status_descriptions, '.$prfx.'store
      WHERE '.$prfx.'products_stock_status.uuid = "'.$this->Status.'"
      AND '.$prfx.'products_stock_status.uuid = '.$prfx.'products_stock_status_descriptions.stock_status
      AND '.$prfx.'products_stock_status_descriptions.language = '.$prfx.'store.adminlang
      ORDER BY '.$prfx.'products_stock_status.stock DESC';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function UpdateProductStockStatus() {
    $JobDone = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($this->StockStatus === 'empty') { $JobDone = true; }
    else {
      $StatusList = explode(';;',$this->StockStatus);
      foreach ($StatusList as $Row) {
        $Error = false;
        $NewEntry = false;
        $Values = explode('::',$Row);
        if ($Values[0] === 'NewStockStatus') {
          $Values[0] = $this->GetNewUUID();
          $NewEntry = true;
        }
        $ClrVal = $this->ClearUUID($Values[0]);
        if ($ClrVal !== false) { $uuid = $ClrVal; }
          else { $Error = true; }
        $ClrVal = $this->ClearValue($Values[1],'AN',' ');
        if (mb_strlen($ClrVal) === mb_strlen($Values[1])) {
          $name = $ClrVal;
        } else {
          $Error = true;
        }
        $ClrVal = $this->ClearValue($Values[2],'AN','-:_./');
        if (mb_strlen($ClrVal) === mb_strlen($Values[2])) {
          $Icon = str_replace(get_site_url().'/','',$ClrVal);
        } else {
          $Error = true;
        }
        $ClrVal = $this->ClearValue($Values[3],'EN','#');
        if (mb_strlen($ClrVal) === mb_strlen($Values[3])) {
          $Color = $ClrVal;
        } else {
          $Error = true;
        }
        $ClrVal = $this->ClearValue($Values[4],'NM',',.');
        if (mb_strlen($ClrVal) === mb_strlen($Values[4])) {
          if ($ClrVal > -1) {$Stock = $ClrVal; }
        } else {
          $Error = true;
        }
        if (!$Error) {
          if ($NewEntry) {
            $Query = $wpdb->prepare(
              'INSERT INTO '.$prfx.'products_stock_status (uuid,product,icon,color,stock)
                VALUES (%s,%s,%s,%s,%s)',
              $uuid,
              $this->Get_UUID(),
              $Icon,
              $Color,
              $Stock
            );
            if ($wpdb->query($Query) !== false) {
              if (sizeof($Values) === 6) {
                if (!$this->CopyStockStatusTranslations($Values[0],$Values[5])) { $Error = true; }
              } else {
                $Query = $wpdb->prepare(
                  'INSERT INTO '.$prfx.'products_stock_status_descriptions (stock_status,language,name)
                    VALUES (%s,(SELECT adminlang FROM '.$prfx.'store),%s)',
                  $uuid,
                  $name
                );
                if ($wpdb->query($Query) === false) { $Error = true; }
              }
            }
          } else {
            $Query = $wpdb->prepare(
              'UPDATE '.$prfx.'products_stock_status SET icon = %s, color = %s, stock = %s
               WHERE uuid = %s',
              $Icon,
              $Color,
              $Stock,
              $uuid
            );
            if (false !== $wpdb->query($Query)) {
              $Query = $wpdb->prepare(
                'UPDATE '.$prfx.'products_stock_status_descriptions SET name = %s
                 WHERE stock_status = %s AND language IN (SELECT adminlang FROM '.$prfx.'store)',
                $name,
                $uuid
              );
              if (false === $wpdb->query($Query)) { $Error = true; }
            }
          }
          if (!$Error) { $JobDone = true; }
        }
      }
    }
    return $JobDone;
  }
  private function CopyStockStatusTranslations($Status,$CopyFrom) {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'products_stock_status_descriptions WHERE stock_status = "'.$CopyFrom.'"';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'products_stock_status_descriptions (stock_status,language,name)
            VALUES (%s,%s,%s)',
          $Status,
          $Row->language,
          $Row->name
        );
        if ($wpdb->query($Query) === false) { $Error = true; }
      }
    }
    if (!$Error) { $JobDone = true; }
    return $JobDone;
  }
  public function DeleteProductStockStatusData() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $JobDone = false;
    if ($this->Status !== '') {
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_stock_status_descriptions WHERE stock_status = %s',
        $this->Status
      );      
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_stock_status WHERE uuid = %s',
        $this->Status
      );      
      if ($wpdb->query($Query) !== false) { $JobDone = true; }      
    }
    return $JobDone;
  }
  public function LoadStockStatusSearch() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $DataSet = array();
    $Searcher = '';
    $Searcher .= 'AND (
        '.$prfx.'products_descriptions.description LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'products_descriptions.name LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'products_descriptions.notes LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'brands.name LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'products.code LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'products.price LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'products.btbprice LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'products.stock LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'products.minstock LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'products.created LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'products.available LIKE "%'.$this->SearchString.'%"
        OR '.$prfx.'products.changed LIKE "%'.$this->SearchString.'%")';
    $NotVar = '';
    if (MENSIO_FLAVOR !== 'FREE') {
      $NotVar = 'AND '.$prfx.'products.uuid NOT IN (SELECT variation FROM '.$prfx.'products_variations)';
    }
    $Query = '
      SELECT '.$prfx.'products.*, '.$prfx.'products_descriptions.description,
        '.$prfx.'products_descriptions.name, '.$prfx.'products_descriptions.notes,
        '.$prfx.'products_images.file, '.$prfx.'brands.name as brandname
      FROM '.$prfx.'products, '.$prfx.'products_descriptions, '.$prfx.'brands,
        '.$prfx.'products_images, '.$prfx.'store
      WHERE '.$prfx.'products.uuid = '.$prfx.'products_descriptions.product
      AND '.$prfx.'products.status = "StockRelated"
      AND '.$prfx.'products.uuid = '.$prfx.'products_images.product
      AND '.$prfx.'products.brand = '.$prfx.'brands.uuid
      AND  '.$prfx.'products_images.main = TRUE
      AND '.$prfx.'products_descriptions.language = '.$prfx.'store.adminlang
      '.$Searcher.'
      '.$NotVar.'
      ORDER BY '.$this->Sorter;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  public function ClearProductsStockStatus() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $JobDone = false;
    if ($this->Get_UUID() !== '') {
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_stock_status_descriptions WHERE stock_status IN (
          SELECT uuid FROM '.$prfx.'products_stock_status WHERE product = %s
         )',
        $this->Get_UUID()
      );      
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'products_stock_status WHERE product = %s',
        $this->Get_UUID()
      );      
      if ($wpdb->query($Query) !== false) { $JobDone = true; }      
    }
    return $JobDone;
  }
  public function LoadLatestProductsList($Limit='',$BrandID='',$LangID='') {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($Limit === '') { $Limit = '10'; }
    $BrandFilter = '';
    if ($BrandID !== '') {
      $BrandFilter = 'AND '.$prfx.'products.brand = "'.$BrandID.'"';
    }
    $LangFIlter = 'AND '.$prfx.'products_descriptions.language = '.$prfx.'store.themelang';
    if ($LangID !== '') {
      $LangFIlter = 'AND '.$prfx.'products_descriptions.language = "'.$LangID.'"';
    }
    $Query = 'SELECT '.$prfx.'products.*, '.$prfx.'products_descriptions.name, '.$prfx.'brands.color, '
        .$prfx.'products_descriptions.description, '.$prfx.'products_descriptions.notes,'
        .$prfx.'products_images.file
      FROM '.$prfx.'products, '.$prfx.'products_descriptions, '.$prfx.'brands, '
        .$prfx.'products_images, '.$prfx.'store
      WHERE '.$prfx.'products.uuid = '.$prfx.'products_descriptions.product
      '.$LangFIlter.'
      AND '.$prfx.'products.uuid = '.$prfx.'products_images.product
      AND '.$prfx.'products_images.main = TRUE
      AND '.$prfx.'products.available <= CURDATE()
      AND '.$prfx.'products.visibility = TRUE
      AND '.$prfx.'products.brand = '.$prfx.'brands.uuid
      '.$BrandFilter.'
      ORDER BY available DESC LIMIT '.$Limit;
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      $i = 0;
      foreach ($Data as $Row) {
        $DataSet[$i]['uuid'] = $Row->uuid;
        $DataSet[$i]['guuid'] = $Row->guuid;
        $DataSet[$i]['code'] = $Row->code;
        $DataSet[$i]['brand'] = $Row->brand;
        $DataSet[$i]['border_color'] = $Row->color;
        $DataSet[$i]['btbprice'] = $Row->btbprice;
        $DataSet[$i]['btbtax'] = $Row->btbtax;
        $DataSet[$i]['price'] = $Row->price;
        $DataSet[$i]['tax'] = $Row->tax;
        $DataSet[$i]['discount'] = $Row->discount;
        $DataSet[$i]['name'] = $Row->name;
        $DataSet[$i]['description'] = $Row->description;
        $DataSet[$i]['notes'] = $Row->notes;
        $DataSet[$i]['image'] = $Row->file;
        $DataSet[$i]['barcodes'] = '';
        if (MENSIO_FLAVOR !== 'FREE') {
          $this->Set_UUID($Row->uuid);
          $BrcdData = $this->LoadProductBarcodeList();
          $BrcdString = '';
          if ((is_array($BrcdData)) && (!empty($BrcdData[0]))) {
            foreach ($BrcdData as $BrcdRow) {
              if ($BrcdString === '') { $BrcdString .= $BrcdRow->barcode; }
                else { $BrcdString .= '::'.$BrcdRow->barcode; }
            }
          }
          $DataSet[$i]['barcodes'] = $BrcdString;
        }
        $i++;
      }
    }
    return $DataSet;
  }
}