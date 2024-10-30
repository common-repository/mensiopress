<?php
class mensio_core_db {
	private $UUID;
	protected function ClearValue($Value,$Type='AN',$SpCh='NONE') {
    switch($Type) {
      case 'TX':
        $Patern = '[^\p{L}]';
        break;
      case 'EN':
        $Patern = '[^A-Za-z0-9]';
        break;
      case 'NM':
        $Patern = '[^0-9]';
        break;
      default:
        $Patern = '[^\p{L}\p{N}]';
        break;
    }
    if ($SpCh != 'NONE') {
      $Patern = str_replace(']','\\'.$SpCh.']', $Patern);
    }
    $Value = mb_ereg_replace($Patern, '', $Value);
    return $Value;
	}
	protected function ClearUUID($Value) {
		$RtrnVal = false;
		if (mb_strlen($Value) == 36) {
			$ClrVal = $this->ClearValue($Value,'EN','-');
			if (mb_strlen($ClrVal) == 36) {
				$ValArray = explode('-',$ClrVal);
				if ((is_array($ValArray)) && (count($ValArray) == 5)) {
					$RtrnVal = $ClrVal;
				}
			}
		}
		return $RtrnVal;
	}
	protected function GetNewUUID() {
    $NewUUID = '';
    global $wpdb;
    $Query = 'SELECT uuid() AS uuid';
    $DataRows = $wpdb->get_results($Query);
    foreach ( $DataRows as $Data) {
    	$NewUUID = $Data->uuid;
    }
    unset($DataRows);
    return $NewUUID;
	}
	final public function Set_UUID($Value) {
		$SetOK = false;
		$ClrVal = $this->ClearUUID($Value);
		if ($ClrVal != false) {
			$this->UUID = $ClrVal;
			$SetOK = true;
		}
		return $SetOK;
	}
	protected function Get_UUID() {
    return $this->UUID;
	}
  final public function LoadAdminLang() {
    $AdminLang = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'store';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $AdminLang = $Row->adminlang;
      }
    }
    return $AdminLang;
  }
  protected function SlugExists($Slug,$Code) {
    $Found = false;
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT * FROM '.$prfx.'mns_store_slugs WHERE slug = "'.$Slug.'" AND uuid != "'.$Code.'"';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $Found = true;
      }
    } else {
      $Query = 'SELECT * FROM '.$prfx.'posts WHERE post_name = "'.$Slug.'" AND post_content != "[mensioobject uuid=\"'.$Code.'\"]"';
      $Data = $wpdb->get_results($Query);
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Found = true;
        }
      }      
    }
    return $Found;
  }
  protected function AddSlug($Type,$Slug,$Code,$Name='') {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $JobDone = false;
    $Lang = '';
    if ($Name === '') { $Name = $Slug; }
    else {
      if (strpos($Name, '::') !== false) {
        $Chk = explode('::',$Name);
        if (is_array($Chk) && (strlen($Chk[0]) === 2)) {
          $Lang = 'AND post_title LIKE "'.$Chk[0].'::%"';
        }
      }
    }
    Switch ($Type) {
      case 'Brand':
      case 'Category':
      case 'Product':
        $Query = 'DELETE FROM '.$prfx.'store_slugs WHERE uuid = "'.$Code.'"';
        $wpdb->query($Query);
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'store_slugs (uuid,type,slug) VALUES (%s,%s,%s)',
          $Code,
          $Type,
          $Slug
        );
        if (false !== $wpdb->query($Query)) {
          $Query = 'SELECT * FROM '.$wpdb->prefix.'posts WHERE post_content = "[mensioobject uuid=\"'.$Code.'\"]"';
          $ChkData = $wpdb->get_results($Query);
          $PostID = '';
          if ((is_array($ChkData)) && (!empty($ChkData[0]))) {
            foreach ($ChkData as $Row) {
              $PostID = $Row->ID;
            }
          }
          if ($PostID === '') {
            $my_post = array(
              'post_title'    => $Name,
              'post_content'  => '[mensioobject uuid=\"'.$Code.'\"]',
              'post_status'   => 'publish',
              'post_name'     => $Slug,
              'post_type'     => 'mensio_'.strtolower($Type)
            );
            wp_insert_post( $my_post );
          } else {
            $my_post = array(
              'ID'            => $PostID,
              'post_title'    => $Name,
              'post_content'  => '[mensioobject uuid=\"'.$Code.'\"]',
              'post_status'   => 'publish',
              'post_name'     => $Slug,
              'post_type'     => 'mensio_'.strtolower($Type)
            );
            wp_update_post( $my_post );            
          }
          $JobDone = true;
        }
        break;
    }
    return $JobDone;    
  }
  protected function LoadMailTemplate($Name) {
    $Template = '';
    switch ($Name) {
      case 'Sales': case 'Status': case 'Ticket':
      case 'Register': case 'PswdConfirm': case 'GeneralMail':
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Query = 'SELECT * FROM '.$prfx.'posts WHERE post_type = "mensio_mailtemplate" AND post_name = "'.strtolower($Name).'"';
        $DataSet = $wpdb->get_results($Query);
        if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
          foreach ($DataSet as $Row) { $Template = stripslashes( $Row->post_content ); }
        }
        break;
    }
    return $Template;
  }
}