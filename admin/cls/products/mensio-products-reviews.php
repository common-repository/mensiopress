<?php
class mensio_products_reviews extends mensio_core_db {
  private $Product;
  private $Sorter;
  private $SearchString;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Product;
    $this->Sorter = 'ProdName';
    $this->SearchString = '';
  }
  final public function Set_Product($Value) {
		$SetOk = false;
    if ($Value !== '') {
      $ClrVal = $this->ClearUUID($Value);
      if ($ClrVal != false) {
        $this->Product = $ClrVal;
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
  public function LoadProductReviewsDataSet($ForAdmin=true) {
    $DataSet = array();
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Product !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = '
        SELECT '.$prfx.'reviews.*, '.$prfx.'products_descriptions.name AS ProdName,
          '.$prfx.'credentials.username
        FROM '.$prfx.'reviews, '.$prfx.'credentials,'.$prfx.'products_descriptions,
          '.$prfx.'store
        WHERE '.$prfx.'reviews.customer = '.$prfx.'credentials.uuid
        AND '.$prfx.'reviews.product = '.$prfx.'products_descriptions.product
        AND '.$prfx.'products_descriptions.language = '.$prfx.'store.'.$lang.'
        AND '.$prfx.'reviews.product = "'.$this->Product.'"
        ORDER BY '.$prfx.'reviews.created DESC';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadReviewsData() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'reviews.*, '.$prfx.'credentials.username
        FROM '.$prfx.'reviews, '.$prfx.'credentials
        WHERE '.$prfx.'reviews.customer = '.$prfx.'credentials.uuid
        AND '.$prfx.'reviews.uuid = "'.$this->Get_UUID().'"';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadReviewReplies() {
    $DataSet = array();
    if ($this->Get_UUID() !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'reviews_replies.*, '.$prfx.'credentials.username
        FROM '.$prfx.'reviews_replies, '.$prfx.'credentials
        WHERE '.$prfx.'reviews_replies.customer = '.$prfx.'credentials.uuid
        AND '.$prfx.'reviews_replies.review = "'.$this->Get_UUID().'"
        ORDER BY changed';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
  public function LoadReviewProductStats($ForAdmin=true) {
    $DataSet = array ('Name'=>'','NoReviews'=>'','AvgRating'=>'');
    if ($ForAdmin) { $lang = 'adminlang'; }
      else { $lang = 'themelang'; }
    if ($this->Product !== '') {
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = 'SELECT '.$prfx.'products_descriptions.name
        FROM '.$prfx.'products_descriptions, '.$prfx.'store
        WHERE '.$prfx.'products_descriptions.language = '.$prfx.'store.'.$lang.'
        AND '.$prfx.'products_descriptions.product = "'.$this->Product.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $DataSet['Name'] = $Row->name; }
      }
      $Query = 'SELECT COUNT(*) AS NoReviews FROM '.$prfx.'reviews
        WHERE product = "'.$this->Product.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $DataSet['NoReviews'] = $Row->NoReviews; }
      }
      $Query = 'SELECT AVG(rvalue) AS AvgRating FROM '.$prfx.'reviews
        WHERE product = "'.$this->Product.'"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) { $DataSet['AvgRating'] = $Row->AvgRating; }
      }
    }
    return $DataSet;
  }
  public function DeleteReviewRecord() {
    $JobDone = false;
    if ($this->Get_UUID() !== '') { 
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';    
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'reviews_replies WHERE review = %s',
        $this->Get_UUID()
      );
      $wpdb->query($Query);
      $Query = $wpdb->prepare(
        'DELETE FROM '.$prfx.'reviews WHERE uuid = %s',
        $this->Get_UUID()
      );
      if (false !== $wpdb->query($Query)) { $JobDone = true; }
    }
    return $JobDone;
  }
}