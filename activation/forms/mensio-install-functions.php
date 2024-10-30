<?php
class mensio_Install_Functions {
	private $DB_Name;
	private $DB_User;
	private $DB_Password;
	private $DB_Host;
	private $DB_Prefix;
	private $StoreUUID;
	public function __construct() {
		$this->DB_Name = '';
		$this->DB_User = '';
		$this->DB_Password = '';
		$this->DB_Host = '';
		$this->DB_Prefix = '';
    $this->StoreUUID = $this->GetNewUUID();
	}
	private function ClearValue($Value,$Type='AN',$SpCh='NONE') {
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
	private function GetNewUUID() {
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
	private function Set_DB_Name($Value) {
   $SetOk = false;
	 $ClrVal = $this->ClearValue($Value,'EN','_-');
	 if (mb_strlen($ClrVal) === mb_strlen($Value)) {
    $this->DB_Name = $ClrVal;
		$SetOk = true;
   }
	 return $SetOk;
	}
	private function Set_DB_User($Value) {
		$SetOk = false;
		$ClrVal = $this->ClearValue($Value,'EN','-_@');
		if (mb_strlen($ClrVal) < 33) {
      if (mb_strlen($ClrVal) === mb_strlen($Value)) {
        $this->DB_User = $ClrVal;
    		$SetOk = true;
      }
    }
		return $SetOk;
	}
	private function Set_DB_Password($Value) {
		$this->DB_Password = $Value;
	}
	 private function Set_DB_Host($Value) {
    $SetOk = false;
    $Error = false;
    $ClrVal = $this->ClearValue($Value,'EN','-.');
    if (mb_strlen($ClrVal) > 253) { $Error = true; }
    $ChkHost = explode('.',$ClrVal);
    foreach ($ChkHost as $Part) {
     if (substr($Part,0,1) == '-') { $Error = true; }
    }
    if (!$Error) {
      $this->DB_Host = $Value;
      $SetOk = true;
    }
    return $SetOk;
	 }
	 private function Set_DB_Prefix($Value) {
		 $SetOk = false;
		 $ClrVal = $this->ClearValue($Value,'EN','_');
		 if (mb_strlen($ClrVal) === mb_strlen($Value)) {
      $this->DB_Prefix = $ClrVal;
      $SetOk = true;
     }
		 return $SetOk;
	 }
  final public function CheckDataStringValues($DataString) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $Data = json_decode(stripslashes($DataString),true);
    if (json_last_error() === JSON_ERROR_NONE) {
        if (!$this->Set_DB_Name($Data['dbname'])) {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Data Base Name not correct: '.$Data['dbname'].'<br>';
        }
        if (!$this->Set_DB_User($Data['username'])) {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'User Name not correct: '.$Data['username'].'<br>';
        }
        $this->Set_DB_Name($Data['password']);
        if (!$this->Set_DB_Host($Data['host'])) {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Host not correct: '.$Data['password'].'<br>';
        }
        if (!$this->Set_DB_Prefix($Data['prefix']) ) {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Host not correct: '.$Data['prefix'].'<br>';
        }
    } else {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Data Entered not correct: '.JSON_ERROR_NONE;
    }
    return $RtrnData;
  }
  final public function CompleteInstallation() {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $UploadDir = wp_upload_dir(); //basedir
    $path = $UploadDir['basedir'];
    if (!file_exists($path)) {
	    if (!wp_mkdir_p($path)) {
	      $RtrnData['ERROR'] = 'TRUE';
	      $RtrnData['Message'] .= 'Failed to create folder at '.$path;
      } else {
        $IndexFile = fopen($path.'/index.php', 'w');
        $txt = "<?php // Silence is golden";
        fwrite($IndexFile, $txt);
        fclose($IndexFile);
      }
    }
    if ($RtrnData['ERROR'] !== 'TRUE') {
      $path = $path.'/mensiopress';
      if (!file_exists($path)) {
        if (!wp_mkdir_p($path)) {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Failed to create folder mensiopress at '.$path;
        } else {
          $IndexFile = fopen($path.'/index.php', 'w');
          $txt = "<?php // Silence is golden";
          fwrite($IndexFile, $txt);
          fclose($IndexFile);
        }
      }
    }
		if (!$this->UpdateWPOptionsEntry()) {
			$RtrnData['ERROR'] = 'TRUE';
			$RtrnData['Message'] .= 'Complete Installation could not finished';
		}
    return $RtrnData;
  }
  private function UpdateWPOptionsEntry() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'DELETE FROM '.$prfx.'options WHERE option_name LIKE "mensio_installed%"';
    if (false === $wpdb->query($Query)) { $Error = true; }
    if (!$Error) {
      $Query = 'INSERT INTO '.$prfx.'options (option_name, option_value,autoload)
        VALUES ("mensio_installed","Active","no")';
      if (false !== $wpdb->query($Query)) {
        $Query = 'INSERT INTO '.$prfx.'options`
          (option_name, option_value,autoload)
          VALUES ("mensio_installed_Date","'.date("Y-m-d H:i:s").'","no")';
        $wpdb->query($Query);
        $Query = 'INSERT INTO '.$prfx.'options
          (option_name, option_value,autoload)
          VALUES ("mensio_installed_NewSetupMsg","Show","no")';
        $wpdb->query($Query);
        $JobDone = true;
      }
    }
    return $JobDone;
  }
  final public function StartMnsInstallSettings() {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    if (!$this->InstallSettingsTables()) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Settings Tables could NOT be Installed !!!!<br>';
    }
    return $RtrnData;
  }
  private function InstallSettingsTables() {
    $Error = false;
    $JobDone = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'store';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'store` (
        `uuid` char(36) NOT NULL,
        `themelang` char(36) NOT NULL,
        `thmactivelang` text NOT NULL,
        `adminlang` char(36) NOT NULL,
        `name` varchar(255) NOT NULL,
        `country` char(36) NOT NULL,
        `tzone` varchar(255) NOT NULL,
        `city` varchar(255) NOT NULL,
        `street` varchar(255) NOT NULL,
        `number` varchar(10) NOT NULL,
        `phone` varchar(20) NOT NULL,
        `fax` varchar(20) NOT NULL,
        `email` varchar(255) NOT NULL,
        `gglstats` text NOT NULL,
        `gglmap` text NOT NULL,
        `logo` varchar(255) NOT NULL,
        `currency` char(36) NOT NULL,
        `update_currency` tinyint(1) NOT NULL,
        `barcode` varchar(255) NOT NULL,
        `orderserial` varchar(255) NOT NULL,
        `tblrows` smallint(6) NOT NULL,
        `notiftime` int(11) NOT NULL,
        `metrics` text NOT NULL,
        `mailsettings` text NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'notifications';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'notifications` (
      `code` bigint(20) NOT NULL,
      `note_store` char(36) NOT NULL,
      `note_type` varchar(255) NOT NULL,
      `note_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `note_user` bigint(20) NOT NULL,
      `notification` text NOT NULL,
      `informed` tinyint(1) NOT NULL DEFAULT "0"
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'mensiologs';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'mensiologs` (
        `code` bigint(20) NOT NULL,
        `log_store` char(36) NOT NULL,
        `log_type` varchar(255) NOT NULL,
        `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `log` text NOT NULL,
        `informed` tinyint(1) NOT NULL DEFAULT "0"
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_users_permissions';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'store_users_permissions` (
        `userID` bigint(20) NOT NULL,
        `store` char(36) NOT NULL,
        `products` tinyint(1) NOT NULL,
        `customers` tinyint(1) NOT NULL,
        `orders` tinyint(1) NOT NULL,
        `marketing` tinyint(1) NOT NULL,
        `reports` tinyint(1) NOT NULL,
        `design` tinyint(1) NOT NULL,
        `settings` tinyint(1) NOT NULL,
        `system` tinyint(1) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_slugs';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'store_slugs` (
        `uuid` char(36) NOT NULL,
        `type` varchar(20) NOT NULL,
        `slug` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_mails';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'store_mails` (
        `uuid` char(36) NOT NULL,
        `name` varchar(255) NOT NULL,
        `template` text NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_terms';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'store_terms` (
        `uuid` char(36) NOT NULL,
        `store` char(36) NOT NULL,
        `useterms` text NOT NULL,
        `editdate` datetime NOT NULL,
        `published` tinyint(1) NOT NULL,
        `active` tinyint(1) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_backups';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'store_backups` (
        `store` char(36) NOT NULL,
        `user` varchar(255) NOT NULL,
        `created` datetime NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'languages_codes';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'languages_codes` (
        `uuid` char(36) NOT NULL,
        `code` varchar(10) NOT NULL,
        `icon` varchar(255) NOT NULL,
        `active` tinyint(1) NOT NULL DEFAULT "0"
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'languages_names';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'languages_names` (
        `language` char(36) NOT NULL,
        `tolanguage` char(36) NOT NULL,
        `name` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'continents_codes';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'continents_codes` (
        `uuid` char(36) NOT NULL,
        `code` varchar(4) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'continents_names';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'continents_names` (
        `continent` char(36) NOT NULL,
        `language` char(36) NOT NULL,
        `name` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'countries_codes';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'countries_codes` (
        `uuid` char(36) NOT NULL,
        `continent` char(36) NOT NULL,
        `iso` varchar(20) NOT NULL,
        `domain` varchar(4) NOT NULL,
        `idp` varchar(10) NOT NULL,
        `currency` char(36) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'countries_names';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'countries_names` (
        `country` char(36) NOT NULL,
        `language` char(36) NOT NULL,
        `name` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'currencies_codes';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'currencies_codes` (
        `uuid` char(36) NOT NULL,
        `code` varchar(10) NOT NULL,
        `symbol` varchar(10) NOT NULL,
        `icon` varchar(20) NOT NULL,
        `leftpos` tinyint(1) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'currencies_names';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'currencies_names` (
        `currency` char(36) NOT NULL,
        `language` char(36) NOT NULL,
        `name` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'sectors_codes';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'sectors_codes` (
        `uuid` char(36) NOT NULL,
        `parent` char(36) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'sectors_names';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'sectors_names` (
        `sector` char(36) NOT NULL,
        `language` char(36) NOT NULL,
        `name` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'regions_types';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'regions_types` (
        `uuid` char(36) NOT NULL,
        `country` char(36) NOT NULL,
        `name` varchar(255) NOT NULL,
        `level` smallint(6) NOT NULL,
        `inhouse` tinyint(1) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'regions_codes';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'regions_codes` (
        `uuid` char(36) NOT NULL,
        `country` char(36) NOT NULL,
        `type` char(36) NOT NULL,
        `parent` char(36) NOT NULL,
        `inhouse` tinyint(1) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'regions_names';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'regions_names` (
        `region` char(36) NOT NULL,
        `language` char(36) NOT NULL,
        `name` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_payment_type';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'orders_payment_type` (
        `uuid` char(36) NOT NULL,
        `name` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_payment';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'store_payment` (
        `uuid` char(36) NOT NULL,
        `store` char(36) NOT NULL,
        `type` char(36) NOT NULL,
        `success_page` varchar(255) NOT NULL,
        `failed_page` varchar(255) NOT NULL,
        `active` tinyint(1) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_payment_descriptions';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'store_payment_descriptions` (
        `payment` char(36) NOT NULL,
        `language` char(36) NOT NULL,
        `description` varchar(255) NOT NULL,
        `instructions` text NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_payment_delivery';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'store_payment_delivery` (
        `payment` char(36) NOT NULL,
        `shipping` char(36) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_payment_gateways';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'store_payment_gateways` (
        `payment` char(36) NOT NULL,
        `parameter` varchar(255) NOT NULL,
        `value` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_payment_bank';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'store_payment_bank` (
        `uuid` char(36) NOT NULL,
        `payment` char(36) NOT NULL,
        `account_bank` varchar(255) NOT NULL,
        `account_icon` varchar(255) NOT NULL,
        `account_name` varchar(255) NOT NULL,
        `account_number` varchar(100) NOT NULL,
        `account_routing` varchar(100) NOT NULL,
        `account_iban` varchar(100) NOT NULL,
        `account_swift` varchar(100) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'ALTER TABLE `'.$prfx.'store` ADD PRIMARY KEY (`uuid`), ADD KEY `language_theme` (`themelang`), ADD KEY `language_admin` (`adminlang`), ADD KEY `country` (`country`), ADD KEY `currency` (`currency`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'notifications` ADD PRIMARY KEY (`code`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'notifications` MODIFY `code` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'mensiologs` ADD PRIMARY KEY (`code`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'mensiologs` MODIFY `code` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'store_users_permissions` ADD PRIMARY KEY (`userID`), ADD KEY `store` (`store`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'store_slugs` ADD PRIMARY KEY (`uuid`), ADD UNIQUE KEY `slag` (`slug`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'store_mails` ADD PRIMARY KEY (`uuid`), ADD KEY `store` (`store`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'store_terms` ADD PRIMARY KEY (`uuid`), ADD KEY `store` (`store`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'store_backups` ADD PRIMARY KEY (`aa`), ADD KEY `store` (`store`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'languages_codes` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'languages_names` ADD KEY `From_Language` (`language`), ADD KEY `To_Language` (`tolanguage`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'continents_codes` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'continents_names` ADD KEY `continent` (`continent`), ADD KEY `language` (`language`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'countries_codes` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'countries_names` ADD KEY `country` (`country`), ADD KEY `language` (`language`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'currencies_codes` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'currencies_names` ADD KEY `currency` (`currency`), ADD KEY `language` (`language`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'sectors_codes` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'sectors_names` ADD KEY `sector` (`sector`), ADD KEY `language` (`language`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'regions_types` ADD PRIMARY KEY (`uuid`), ADD KEY `country` (`country`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'regions_codes` ADD PRIMARY KEY (`uuid`), ADD KEY `country` (`country`), ADD KEY `type` (`type`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'regions_names` ADD KEY `region` (`region`), ADD KEY `language` (`language`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'orders_payment_type` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'store_payment` ADD PRIMARY KEY (`uuid`), ADD KEY `store` (`store`), ADD KEY `type` (`type`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'store_payment_delivery` ADD KEY `payment` (`payment`), ADD KEY `shipping` (`shipping`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'store_payment_gateways` ADD KEY `payment` (`payment`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'store_payment_bank` ADD PRIMARY KEY (`uuid`), ADD KEY `payment` (`payment`);';
      $wpdb->query($Query);
    }
    if (!$Error) { $JobDone = true; }
    return $JobDone;
  }
  final public function InstallSettingsBasicValues() {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    if (!$this->InsertLanguageValues()) {
      $RtrnData['ERROR'] === 'TRUE';
      $RtrnData['Message'] .= 'Language values could not be installed<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$this->InsertCurrencyValues()) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= $Error.'<br>Currency values could not be installed<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$this->InsertSectorsValues()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Sectors values could not be installed<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$this->InsertContinentValues()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Continent values could not be installed<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$this->InsertCountryValues()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Country values could not be installed<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$this->InsertUserPermissionsValues()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'User Permissions could not be installed<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$this->InsertMainStoreTableData()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Store default values could not be installed<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$this->InsertOrdersPaymentTypeValues()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Orders Payment Type values could not be installed<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$this->InsertOrdersPaymentValues()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Orders Payments attribute values could not be installed<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$this->InsertPaymentDescriptions()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Payment Descriptions could not be installed<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$this->InsertGateWayPayPalValues()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'PayPal Attributes could not be installed<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$this->InsertDefaultPagesTemplates()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Default pages templates could not be installed<br>';
      }
    }
    if (MENSIO_FLAVOR !== 'FREE') {
      if ($RtrnData['ERROR'] === 'FALSE') {
        if (!$this->InsertGateWayBanksValues('Eurobank')) {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Eurobank Attributes could not be installed<br>';
        }
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        if (!$this->InsertGateWayBanksValues('AlphaBank')) {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'AlphaBank Attributes could not be installed<br>';
        }
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        if (!$this->InsertGateWayBanksValues('VivaWallet')) {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Viva Wallet Attributes could not be installed<br>';
        }
      }
    }
    return $RtrnData;
  }
  private function InsertLanguageValues() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'INSERT INTO '.$prfx.'languages_codes (uuid, code, icon, active) VALUES ("'.$this->GetNewUUID().'", "en", "usa", 1)';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'languages_codes (uuid, code, icon, active) VALUES ("'.$this->GetNewUUID().'", "el", "No Image", 0)';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'languages_codes (uuid, code, icon, active) VALUES ("'.$this->GetNewUUID().'", "de", "No Image", 0)';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'languages_codes (uuid, code, icon, active) VALUES ("'.$this->GetNewUUID().'", "es", "No Image", 0)';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'languages_codes (uuid, code, icon, active) VALUES ("'.$this->GetNewUUID().'", "rs", "No Image", 0)';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'languages_codes (uuid, code, icon, active) VALUES ("'.$this->GetNewUUID().'", "fr", "no Image", 0)';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'languages_codes (uuid, code, icon, active) VALUES ("'.$this->GetNewUUID().'", "it", "No Image", 0)';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'languages_names (language, tolanguage, name)
      VALUES (
        (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
        (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
        "English"
      )';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'languages_names (language, tolanguage, name)
      VALUES (
        (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"),
        (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
        "Greek"
      )';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'languages_names (language, tolanguage, name)
      VALUES (
        (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"),
        (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
        "German"
      )';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'languages_names (language, tolanguage, name)
      VALUES (
        (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"),
        (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
        "Spanish"
      )';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'languages_names (language, tolanguage, name)
      VALUES (
        (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "rs"),
        (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
        "Russian"
      )';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'languages_names (language, tolanguage, name)
      VALUES (
        (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"),
        (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
        "French"
      )';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'languages_names (language, tolanguage, name)
      VALUES (
        (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"),
        (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
        "Italian"
      )';
    if (false === $wpdb->query($Query)) { $Error = true; }
    if (!$Error) {
      $JobDone = true;
    } else {
      $Query = 'DELETE FROM '.$prfx.'languages_codes';
      $wpdb->query($Query);
      $Query = 'DELETE FROM '.$prfx.'languages_names';
      $wpdb->query($Query);
    }
    return $JobDone;
  }
  private function InsertCurrencyValues() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'INSERT INTO '.$prfx.'currencies_codes (uuid, code, symbol, icon, leftpos) VALUES ("'.$this->GetNewUUID().'", "EUR", "€", "No Icon", 0)';
    if (false === $wpdb->query($Query)) { $Error = true; }
    if (!$Error) {
      $Query = 'INSERT INTO '.$prfx.'currencies_codes (uuid, code, symbol, icon, leftpos) VALUES ("'.$this->GetNewUUID().'", "USD", "$", "No Icon", 0)';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'INSERT INTO '.$prfx.'currencies_codes (uuid, code, symbol, icon, leftpos) VALUES ("'.$this->GetNewUUID().'", "PD", "£", "No Icon", 0)';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'INSERT INTO '.$prfx.'currencies_names (currency, language, name)
        VALUES (
          (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"),
          (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
          "Euro"
        )';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'INSERT INTO '.$prfx.'currencies_names (currency, language, name)
        VALUES (
          (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"),
          (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
          "United States Dollar"
        )';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'INSERT INTO '.$prfx.'currencies_names (currency, language, name)
        VALUES (
          (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "PD"),
          (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
          "English Pound"
        )';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $JobDone = true;
    } else {
      $Query = 'DELETE FROM '.$prfx.'currencies_codes';
      $wpdb->query($Query);
      $Query = 'DELETE FROM '.$prfx.'currencies_names';
      $wpdb->query($Query);
    }
    return $JobDone;
  }
  private function InsertSectorsValues() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Values = '("c572515b-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c57259c5-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c5725dfc-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c5726468-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c5726aa1-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572703c-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c5727a2c-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c57280b0-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c57284e1-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c57288b9-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c5728cc2-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572968e-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c5729b22-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c5729f79-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572a773-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572ab64-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572b168-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572b6da-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572baeb-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572c0a7-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572c752-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572cdb7-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572d343-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572ddea-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572e3db-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572e92e-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572efb9-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("c572f3c2-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("ca175bd4-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("d290031e-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("d2900bdf-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("d2901251-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("d2901756-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("d2901c2c-be12-11e6-b932-4ccc6a4aa826", "TopLevel")::
("91f946c0-be1b-11e6-b932-4ccc6a4aa826", "c572515b-be12-11e6-b932-4ccc6a4aa826")::
("91f954bc-be1b-11e6-b932-4ccc6a4aa826", "c572515b-be12-11e6-b932-4ccc6a4aa826")::
("91f96372-be1b-11e6-b932-4ccc6a4aa826", "c572515b-be12-11e6-b932-4ccc6a4aa826")::
("91f9702f-be1b-11e6-b932-4ccc6a4aa826", "c572515b-be12-11e6-b932-4ccc6a4aa826")::
("91f97dff-be1b-11e6-b932-4ccc6a4aa826", "c572515b-be12-11e6-b932-4ccc6a4aa826")::
("91f98f07-be1b-11e6-b932-4ccc6a4aa826", "c572515b-be12-11e6-b932-4ccc6a4aa826")::
("c0db9c3b-be17-11e6-b932-4ccc6a4aa826", "c572515b-be12-11e6-b932-4ccc6a4aa826")::
("f287889e-be1d-11e6-b932-4ccc6a4aa826", "c572515b-be12-11e6-b932-4ccc6a4aa826")::
("f287a2f7-be1d-11e6-b932-4ccc6a4aa826", "c572515b-be12-11e6-b932-4ccc6a4aa826")::
("f28d2351-be1d-11e6-b932-4ccc6a4aa826", "c572515b-be12-11e6-b932-4ccc6a4aa826")::
("f28d2d03-be1d-11e6-b932-4ccc6a4aa826", "c572515b-be12-11e6-b932-4ccc6a4aa826")::
("f287bb5b-be1d-11e6-b932-4ccc6a4aa826", "c57259c5-be12-11e6-b932-4ccc6a4aa826")::
("f287d3eb-be1d-11e6-b932-4ccc6a4aa826", "c57259c5-be12-11e6-b932-4ccc6a4aa826")::
("f287ec9d-be1d-11e6-b932-4ccc6a4aa826", "c57259c5-be12-11e6-b932-4ccc6a4aa826")::
("f287fa7f-be1d-11e6-b932-4ccc6a4aa826", "c57259c5-be12-11e6-b932-4ccc6a4aa826")::
("f288070b-be1d-11e6-b932-4ccc6a4aa826", "c57259c5-be12-11e6-b932-4ccc6a4aa826")::
("f2881319-be1d-11e6-b932-4ccc6a4aa826", "c57259c5-be12-11e6-b932-4ccc6a4aa826")::
("f288201a-be1d-11e6-b932-4ccc6a4aa826", "c57259c5-be12-11e6-b932-4ccc6a4aa826")::
("f2882b81-be1d-11e6-b932-4ccc6a4aa826", "c57259c5-be12-11e6-b932-4ccc6a4aa826")::
("f2883827-be1d-11e6-b932-4ccc6a4aa826", "c57259c5-be12-11e6-b932-4ccc6a4aa826")::
("f288436a-be1d-11e6-b932-4ccc6a4aa826", "c57259c5-be12-11e6-b932-4ccc6a4aa826")::
("f2885a95-be1d-11e6-b932-4ccc6a4aa826", "c57259c5-be12-11e6-b932-4ccc6a4aa826")::
("f2886d78-be1d-11e6-b932-4ccc6a4aa826", "c5725dfc-be12-11e6-b932-4ccc6a4aa826")::
("f2887cd3-be1d-11e6-b932-4ccc6a4aa826", "c5725dfc-be12-11e6-b932-4ccc6a4aa826")::
("f2888951-be1d-11e6-b932-4ccc6a4aa826", "c5725dfc-be12-11e6-b932-4ccc6a4aa826")::
("f288967c-be1d-11e6-b932-4ccc6a4aa826", "c5725dfc-be12-11e6-b932-4ccc6a4aa826")::
("f288a1ef-be1d-11e6-b932-4ccc6a4aa826", "c5726aa1-be12-11e6-b932-4ccc6a4aa826")::
("f288aed7-be1d-11e6-b932-4ccc6a4aa826", "c5726aa1-be12-11e6-b932-4ccc6a4aa826")::
("f288ba7b-be1d-11e6-b932-4ccc6a4aa826", "c5726aa1-be12-11e6-b932-4ccc6a4aa826")::
("f288c75f-be1d-11e6-b932-4ccc6a4aa826", "c5726aa1-be12-11e6-b932-4ccc6a4aa826")::
("f288d251-be1d-11e6-b932-4ccc6a4aa826", "c5726aa1-be12-11e6-b932-4ccc6a4aa826")::
("f288dc05-be1d-11e6-b932-4ccc6a4aa826", "c5726aa1-be12-11e6-b932-4ccc6a4aa826")::
("f288e458-be1d-11e6-b932-4ccc6a4aa826", "c5726aa1-be12-11e6-b932-4ccc6a4aa826")::
("f288ee1f-be1d-11e6-b932-4ccc6a4aa826", "c5726aa1-be12-11e6-b932-4ccc6a4aa826")::
("f288f7ad-be1d-11e6-b932-4ccc6a4aa826", "c5726aa1-be12-11e6-b932-4ccc6a4aa826")::
("f2890251-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f2890bcd-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f2891640-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f2891f5c-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f28929b3-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f28932c5-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f2893d0e-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f2894636-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f2895090-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f28959ab-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f289641e-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f2896db6-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f2897831-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f2898167-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f2898b39-be1d-11e6-b932-4ccc6a4aa826", "c572703c-be12-11e6-b932-4ccc6a4aa826")::
("f28994d3-be1d-11e6-b932-4ccc6a4aa826", "c5727a2c-be12-11e6-b932-4ccc6a4aa826")::
("f2899f8f-be1d-11e6-b932-4ccc6a4aa826", "c5727a2c-be12-11e6-b932-4ccc6a4aa826")::
("f289a8d6-be1d-11e6-b932-4ccc6a4aa826", "c5727a2c-be12-11e6-b932-4ccc6a4aa826")::
("f289b334-be1d-11e6-b932-4ccc6a4aa826", "c5727a2c-be12-11e6-b932-4ccc6a4aa826")::
("f289bc4e-be1d-11e6-b932-4ccc6a4aa826", "c5727a2c-be12-11e6-b932-4ccc6a4aa826")::
("f289c6c6-be1d-11e6-b932-4ccc6a4aa826", "c5727a2c-be12-11e6-b932-4ccc6a4aa826")::
("f289cfe2-be1d-11e6-b932-4ccc6a4aa826", "c5727a2c-be12-11e6-b932-4ccc6a4aa826")::
("f289da45-be1d-11e6-b932-4ccc6a4aa826", "c57280b0-be12-11e6-b932-4ccc6a4aa826")::
("f289e38e-be1d-11e6-b932-4ccc6a4aa826", "c57280b0-be12-11e6-b932-4ccc6a4aa826")::
("f289ee03-be1d-11e6-b932-4ccc6a4aa826", "c57280b0-be12-11e6-b932-4ccc6a4aa826")::
("f289f7b3-be1d-11e6-b932-4ccc6a4aa826", "c57280b0-be12-11e6-b932-4ccc6a4aa826")::
("f28a023e-be1d-11e6-b932-4ccc6a4aa826", "c57280b0-be12-11e6-b932-4ccc6a4aa826")::
("f28a0acb-be1d-11e6-b932-4ccc6a4aa826", "c57280b0-be12-11e6-b932-4ccc6a4aa826")::
("f28a1555-be1d-11e6-b932-4ccc6a4aa826", "c57288b9-be12-11e6-b932-4ccc6a4aa826")::
("f28a1ea1-be1d-11e6-b932-4ccc6a4aa826", "c57288b9-be12-11e6-b932-4ccc6a4aa826")::
("f28a302f-be1d-11e6-b932-4ccc6a4aa826", "c57288b9-be12-11e6-b932-4ccc6a4aa826")::
("f28a40e3-be1d-11e6-b932-4ccc6a4aa826", "c57288b9-be12-11e6-b932-4ccc6a4aa826")::
("f28a4d65-be1d-11e6-b932-4ccc6a4aa826", "c57288b9-be12-11e6-b932-4ccc6a4aa826")::
("f28a5fb9-be1d-11e6-b932-4ccc6a4aa826", "c57288b9-be12-11e6-b932-4ccc6a4aa826")::
("f28a6c73-be1d-11e6-b932-4ccc6a4aa826", "c572968e-be12-11e6-b932-4ccc6a4aa826")::
("f28a76eb-be1d-11e6-b932-4ccc6a4aa826", "c572968e-be12-11e6-b932-4ccc6a4aa826")::
("f28a823b-be1d-11e6-b932-4ccc6a4aa826", "c572968e-be12-11e6-b932-4ccc6a4aa826")::
("f28a8c09-be1d-11e6-b932-4ccc6a4aa826", "c572968e-be12-11e6-b932-4ccc6a4aa826")::
("f28a9851-be1d-11e6-b932-4ccc6a4aa826", "c572968e-be12-11e6-b932-4ccc6a4aa826")::
("f28aa191-be1d-11e6-b932-4ccc6a4aa826", "c5729b22-be12-11e6-b932-4ccc6a4aa826")::
("f28aac3a-be1d-11e6-b932-4ccc6a4aa826", "c5729b22-be12-11e6-b932-4ccc6a4aa826")::
("f28ab602-be1d-11e6-b932-4ccc6a4aa826", "c5729b22-be12-11e6-b932-4ccc6a4aa826")::
("f28ac1f7-be1d-11e6-b932-4ccc6a4aa826", "c5729b22-be12-11e6-b932-4ccc6a4aa826")::
("f28ad108-be1d-11e6-b932-4ccc6a4aa826", "c5729b22-be12-11e6-b932-4ccc6a4aa826")::
("f28adddd-be1d-11e6-b932-4ccc6a4aa826", "c5729f79-be12-11e6-b932-4ccc6a4aa826")::
("f28ae71d-be1d-11e6-b932-4ccc6a4aa826", "c5729f79-be12-11e6-b932-4ccc6a4aa826")::
("f28af298-be1d-11e6-b932-4ccc6a4aa826", "c5729f79-be12-11e6-b932-4ccc6a4aa826")::
("f28afb97-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28b0674-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28b0f8d-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28b1a77-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28b24dd-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28b306b-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28b38f9-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28b42f0-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28b4b24-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28b5fb2-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28b6c71-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28b7857-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28b820c-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28b8dc8-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28b973a-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28ba245-be1d-11e6-b932-4ccc6a4aa826", "c572a773-be12-11e6-b932-4ccc6a4aa826")::
("f28bab90-be1d-11e6-b932-4ccc6a4aa826", "c572ab64-be12-11e6-b932-4ccc6a4aa826")::
("f28bb65e-be1d-11e6-b932-4ccc6a4aa826", "c572ab64-be12-11e6-b932-4ccc6a4aa826")::
("f28bbf90-be1d-11e6-b932-4ccc6a4aa826", "c572ab64-be12-11e6-b932-4ccc6a4aa826")::
("f28bca94-be1d-11e6-b932-4ccc6a4aa826", "c572ab64-be12-11e6-b932-4ccc6a4aa826")::
("f28bd3fc-be1d-11e6-b932-4ccc6a4aa826", "c572ab64-be12-11e6-b932-4ccc6a4aa826")::
("f28bded2-be1d-11e6-b932-4ccc6a4aa826", "c572ab64-be12-11e6-b932-4ccc6a4aa826")::
("f28be81c-be1d-11e6-b932-4ccc6a4aa826", "c572b168-be12-11e6-b932-4ccc6a4aa826")::
("f28bf3b9-be1d-11e6-b932-4ccc6a4aa826", "c572b168-be12-11e6-b932-4ccc6a4aa826")::
("f28c0956-be1d-11e6-b932-4ccc6a4aa826", "c572b168-be12-11e6-b932-4ccc6a4aa826")::
("f28c1d93-be1d-11e6-b932-4ccc6a4aa826", "c572b168-be12-11e6-b932-4ccc6a4aa826")::
("f28c2d57-be1d-11e6-b932-4ccc6a4aa826", "c572b168-be12-11e6-b932-4ccc6a4aa826")::
("f28c3928-be1d-11e6-b932-4ccc6a4aa826", "c572b6da-be12-11e6-b932-4ccc6a4aa826")::
("f28c42cd-be1d-11e6-b932-4ccc6a4aa826", "c572b6da-be12-11e6-b932-4ccc6a4aa826")::
("f28c4cc5-be1d-11e6-b932-4ccc6a4aa826", "c572baeb-be12-11e6-b932-4ccc6a4aa826")::
("f28c553d-be1d-11e6-b932-4ccc6a4aa826", "c572baeb-be12-11e6-b932-4ccc6a4aa826")::
("f28c5e9a-be1d-11e6-b932-4ccc6a4aa826", "c572baeb-be12-11e6-b932-4ccc6a4aa826")::
("f28c68f4-be1d-11e6-b932-4ccc6a4aa826", "c572c0a7-be12-11e6-b932-4ccc6a4aa826")::
("f28c753d-be1d-11e6-b932-4ccc6a4aa826", "c572c0a7-be12-11e6-b932-4ccc6a4aa826")::
("f28c7e24-be1d-11e6-b932-4ccc6a4aa826", "c572c0a7-be12-11e6-b932-4ccc6a4aa826")::
("f28c87bd-be1d-11e6-b932-4ccc6a4aa826", "c572c0a7-be12-11e6-b932-4ccc6a4aa826")::
("f28c9010-be1d-11e6-b932-4ccc6a4aa826", "c572c752-be12-11e6-b932-4ccc6a4aa826")::
("f28c9b67-be1d-11e6-b932-4ccc6a4aa826", "c572c752-be12-11e6-b932-4ccc6a4aa826")::
("f28ca6d4-be1d-11e6-b932-4ccc6a4aa826", "c572c752-be12-11e6-b932-4ccc6a4aa826")::
("f28cb3d8-be1d-11e6-b932-4ccc6a4aa826", "c572c752-be12-11e6-b932-4ccc6a4aa826")::
("f28cbd73-be1d-11e6-b932-4ccc6a4aa826", "c572c752-be12-11e6-b932-4ccc6a4aa826")::
("f28cc75a-be1d-11e6-b932-4ccc6a4aa826", "c572c752-be12-11e6-b932-4ccc6a4aa826")::
("f28cd11f-be1d-11e6-b932-4ccc6a4aa826", "c572cdb7-be12-11e6-b932-4ccc6a4aa826")::
("f28cdb92-be1d-11e6-b932-4ccc6a4aa826", "c572cdb7-be12-11e6-b932-4ccc6a4aa826")::
("f28ce4ef-be1d-11e6-b932-4ccc6a4aa826", "c572cdb7-be12-11e6-b932-4ccc6a4aa826")::
("f28cf2df-be1d-11e6-b932-4ccc6a4aa826", "c572cdb7-be12-11e6-b932-4ccc6a4aa826")::
("f28cfd94-be1d-11e6-b932-4ccc6a4aa826", "c572cdb7-be12-11e6-b932-4ccc6a4aa826")::
("f28d0846-be1d-11e6-b932-4ccc6a4aa826", "c572cdb7-be12-11e6-b932-4ccc6a4aa826")::
("f28d1138-be1d-11e6-b932-4ccc6a4aa826", "c572cdb7-be12-11e6-b932-4ccc6a4aa826")::
("f28d1ade-be1d-11e6-b932-4ccc6a4aa826", "c572cdb7-be12-11e6-b932-4ccc6a4aa826")::
("f28d3759-be1d-11e6-b932-4ccc6a4aa826", "c572ddea-be12-11e6-b932-4ccc6a4aa826")::
("f28d4252-be1d-11e6-b932-4ccc6a4aa826", "c572ddea-be12-11e6-b932-4ccc6a4aa826")::
("f28d4bb8-be1d-11e6-b932-4ccc6a4aa826", "c572ddea-be12-11e6-b932-4ccc6a4aa826")::
("f28d5b7a-be1d-11e6-b932-4ccc6a4aa826", "c572ddea-be12-11e6-b932-4ccc6a4aa826")::
("f28d68be-be1d-11e6-b932-4ccc6a4aa826", "c572e3db-be12-11e6-b932-4ccc6a4aa826")::
("f28d7461-be1d-11e6-b932-4ccc6a4aa826", "c572e3db-be12-11e6-b932-4ccc6a4aa826")::
("f28d7e13-be1d-11e6-b932-4ccc6a4aa826", "c572e3db-be12-11e6-b932-4ccc6a4aa826")::
("f28d892b-be1d-11e6-b932-4ccc6a4aa826", "c572e3db-be12-11e6-b932-4ccc6a4aa826")::
("f28d9307-be1d-11e6-b932-4ccc6a4aa826", "c572e3db-be12-11e6-b932-4ccc6a4aa826")::
("f28d9dda-be1d-11e6-b932-4ccc6a4aa826", "c572e3db-be12-11e6-b932-4ccc6a4aa826")::
("f28da739-be1d-11e6-b932-4ccc6a4aa826", "c572e3db-be12-11e6-b932-4ccc6a4aa826")::
("f28db199-be1d-11e6-b932-4ccc6a4aa826", "c572e3db-be12-11e6-b932-4ccc6a4aa826")::
("f28dbae2-be1d-11e6-b932-4ccc6a4aa826", "c572e92e-be12-11e6-b932-4ccc6a4aa826")::
("f28dc558-be1d-11e6-b932-4ccc6a4aa826", "c572e92e-be12-11e6-b932-4ccc6a4aa826")::
("f28dcf56-be1d-11e6-b932-4ccc6a4aa826", "c572e92e-be12-11e6-b932-4ccc6a4aa826")::
("f28ddd5f-be1d-11e6-b932-4ccc6a4aa826", "c572e92e-be12-11e6-b932-4ccc6a4aa826")::
("f28de73d-be1d-11e6-b932-4ccc6a4aa826", "c572e92e-be12-11e6-b932-4ccc6a4aa826")::
("f28df426-be1d-11e6-b932-4ccc6a4aa826", "c572e92e-be12-11e6-b932-4ccc6a4aa826")::
("f28dfd4f-be1d-11e6-b932-4ccc6a4aa826", "c572e92e-be12-11e6-b932-4ccc6a4aa826")::
("f28e0702-be1d-11e6-b932-4ccc6a4aa826", "c572e92e-be12-11e6-b932-4ccc6a4aa826")::
("f28e10ec-be1d-11e6-b932-4ccc6a4aa826", "c572e92e-be12-11e6-b932-4ccc6a4aa826")::
("f28e1bc7-be1d-11e6-b932-4ccc6a4aa826", "c572efb9-be12-11e6-b932-4ccc6a4aa826")::
("f28e250a-be1d-11e6-b932-4ccc6a4aa826", "c572efb9-be12-11e6-b932-4ccc6a4aa826")::
("f28e2feb-be1d-11e6-b932-4ccc6a4aa826", "c572efb9-be12-11e6-b932-4ccc6a4aa826")::
("f28e39b0-be1d-11e6-b932-4ccc6a4aa826", "c572efb9-be12-11e6-b932-4ccc6a4aa826")::
("f28e4415-be1d-11e6-b932-4ccc6a4aa826", "c572efb9-be12-11e6-b932-4ccc6a4aa826")::
("f28e4d42-be1d-11e6-b932-4ccc6a4aa826", "c572efb9-be12-11e6-b932-4ccc6a4aa826")::
("f28e57ad-be1d-11e6-b932-4ccc6a4aa826", "c572efb9-be12-11e6-b932-4ccc6a4aa826")::
("f28e6135-be1d-11e6-b932-4ccc6a4aa826", "c572efb9-be12-11e6-b932-4ccc6a4aa826")::
("f28e6c14-be1d-11e6-b932-4ccc6a4aa826", "c572efb9-be12-11e6-b932-4ccc6a4aa826")::
("f28e75f3-be1d-11e6-b932-4ccc6a4aa826", "c572efb9-be12-11e6-b932-4ccc6a4aa826")::
("f28e80b1-be1d-11e6-b932-4ccc6a4aa826", "ca175bd4-be12-11e6-b932-4ccc6a4aa826")::
("f28e8a5a-be1d-11e6-b932-4ccc6a4aa826", "ca175bd4-be12-11e6-b932-4ccc6a4aa826")::
("f28e955b-be1d-11e6-b932-4ccc6a4aa826", "ca175bd4-be12-11e6-b932-4ccc6a4aa826")::
("f28ea45d-be1d-11e6-b932-4ccc6a4aa826", "ca175bd4-be12-11e6-b932-4ccc6a4aa826")::
("f28eb4dd-be1d-11e6-b932-4ccc6a4aa826", "ca175bd4-be12-11e6-b932-4ccc6a4aa826")::
("f28ebea4-be1d-11e6-b932-4ccc6a4aa826", "ca175bd4-be12-11e6-b932-4ccc6a4aa826")::
("f28ec8c4-be1d-11e6-b932-4ccc6a4aa826", "ca175bd4-be12-11e6-b932-4ccc6a4aa826")::
("f28ed17c-be1d-11e6-b932-4ccc6a4aa826", "ca175bd4-be12-11e6-b932-4ccc6a4aa826")::
("f28edb21-be1d-11e6-b932-4ccc6a4aa826", "d290031e-be12-11e6-b932-4ccc6a4aa826")::
("f28ee43a-be1d-11e6-b932-4ccc6a4aa826", "d290031e-be12-11e6-b932-4ccc6a4aa826")::
("f28eef01-be1d-11e6-b932-4ccc6a4aa826", "d290031e-be12-11e6-b932-4ccc6a4aa826")::
("f28ef885-be1d-11e6-b932-4ccc6a4aa826", "d290031e-be12-11e6-b932-4ccc6a4aa826")::
("f28f03c7-be1d-11e6-b932-4ccc6a4aa826", "d290031e-be12-11e6-b932-4ccc6a4aa826")::
("f28f0d90-be1d-11e6-b932-4ccc6a4aa826", "d290031e-be12-11e6-b932-4ccc6a4aa826")::
("f28f179e-be1d-11e6-b932-4ccc6a4aa826", "d290031e-be12-11e6-b932-4ccc6a4aa826")::
("f28f217a-be1d-11e6-b932-4ccc6a4aa826", "d290031e-be12-11e6-b932-4ccc6a4aa826")::
("f28f2c38-be1d-11e6-b932-4ccc6a4aa826", "d2900bdf-be12-11e6-b932-4ccc6a4aa826")::
("f28f35f4-be1d-11e6-b932-4ccc6a4aa826", "d2900bdf-be12-11e6-b932-4ccc6a4aa826")::
("f28f45f5-be1d-11e6-b932-4ccc6a4aa826", "d2900bdf-be12-11e6-b932-4ccc6a4aa826")::
("f28f585d-be1d-11e6-b932-4ccc6a4aa826", "d2900bdf-be12-11e6-b932-4ccc6a4aa826")::
("f28f6306-be1d-11e6-b932-4ccc6a4aa826", "d2900bdf-be12-11e6-b932-4ccc6a4aa826")::
("f28f6e9c-be1d-11e6-b932-4ccc6a4aa826", "d2900bdf-be12-11e6-b932-4ccc6a4aa826")::
("f28f7ab1-be1d-11e6-b932-4ccc6a4aa826", "d2900bdf-be12-11e6-b932-4ccc6a4aa826")::
("f28f8515-be1d-11e6-b932-4ccc6a4aa826", "d2901251-be12-11e6-b932-4ccc6a4aa826")::
("f28f92bf-be1d-11e6-b932-4ccc6a4aa826", "d2901251-be12-11e6-b932-4ccc6a4aa826")::
("f28fa0b1-be1d-11e6-b932-4ccc6a4aa826", "d2901251-be12-11e6-b932-4ccc6a4aa826")::
("f28fad15-be1d-11e6-b932-4ccc6a4aa826", "d2901251-be12-11e6-b932-4ccc6a4aa826")::
("f28fb6f6-be1d-11e6-b932-4ccc6a4aa826", "d2901251-be12-11e6-b932-4ccc6a4aa826")::
("f28fc212-be1d-11e6-b932-4ccc6a4aa826", "d2901251-be12-11e6-b932-4ccc6a4aa826")::
("f28fcbc3-be1d-11e6-b932-4ccc6a4aa826", "d2901251-be12-11e6-b932-4ccc6a4aa826")::
("f28fd6d8-be1d-11e6-b932-4ccc6a4aa826", "d2901251-be12-11e6-b932-4ccc6a4aa826")::
("f28fe08a-be1d-11e6-b932-4ccc6a4aa826", "d2901251-be12-11e6-b932-4ccc6a4aa826")::
("f28fec79-be1d-11e6-b932-4ccc6a4aa826", "d2901756-be12-11e6-b932-4ccc6a4aa826")::
("f28ff731-be1d-11e6-b932-4ccc6a4aa826", "d2901756-be12-11e6-b932-4ccc6a4aa826")::
("f29001f9-be1d-11e6-b932-4ccc6a4aa826", "d2901756-be12-11e6-b932-4ccc6a4aa826")::
("f2900b91-be1d-11e6-b932-4ccc6a4aa826", "d2901756-be12-11e6-b932-4ccc6a4aa826")::
("f2901671-be1d-11e6-b932-4ccc6a4aa826", "d2901c2c-be12-11e6-b932-4ccc6a4aa826")::
("f2902047-be1d-11e6-b932-4ccc6a4aa826", "d2901c2c-be12-11e6-b932-4ccc6a4aa826")::
("f2902b79-be1d-11e6-b932-4ccc6a4aa826", "d2901c2c-be12-11e6-b932-4ccc6a4aa826")::
("f290352e-be1d-11e6-b932-4ccc6a4aa826", "d2901c2c-be12-11e6-b932-4ccc6a4aa826")::
("f2903f5d-be1d-11e6-b932-4ccc6a4aa826", "d2901c2c-be12-11e6-b932-4ccc6a4aa826")::
("f290496e-be1d-11e6-b932-4ccc6a4aa826", "d2901c2c-be12-11e6-b932-4ccc6a4aa826")::
("f2905441-be1d-11e6-b932-4ccc6a4aa826", "d2901c2c-be12-11e6-b932-4ccc6a4aa826")::
("f2905df4-be1d-11e6-b932-4ccc6a4aa826", "d2901c2c-be12-11e6-b932-4ccc6a4aa826")::
("f29068d4-be1d-11e6-b932-4ccc6a4aa826", "d2901c2c-be12-11e6-b932-4ccc6a4aa826")::
("f2907303-be1d-11e6-b932-4ccc6a4aa826", "d2901c2c-be12-11e6-b932-4ccc6a4aa826")::
("f2907e22-be1d-11e6-b932-4ccc6a4aa826", "d2901c2c-be12-11e6-b932-4ccc6a4aa826")::
("7d535222-f918-11e6-a9f3-901b0ebdf399", "f28f217a-be1d-11e6-b932-4ccc6a4aa826")';
    $Values = explode('::',$Values);
    foreach ($Values as $Row) {
      $Query = 'INSERT INTO '.$prfx.'sectors_codes (uuid, parent) VALUES '.$Row;
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    $Values = '("91f946c0-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Regional Mid-Atlantic Banks")::
("f2891f5c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Grocery Stores")::
("f28c3928-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Farm & Construction Machinery")::
("c572b6da-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Agriculture")::
("c572b6da-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Landwirtschaft")::
("c572b6da-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "agricoltura")::
("c572b6da-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Agricultura")::
("f28c42cd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Produits agricoles")::
("f28c42cd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Landwirtschaftliche Produkte")::
("f28c42cd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Prodotti agricoli")::
("f28c42cd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Productos agrícolas")::
("c572515b-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Banking")::
("c572515b-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Bancaire")::
("c572515b-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "münchen")::
("c572515b-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "ΤΡΑΠΕΖΙΚΕΣ ΕΡΓΑΣΙΕΣ")::
("c572515b-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Bancario")::
("c572515b-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bancario")::
("f28d2d03-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Credit Services")::
("f28d2d03-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services de crédit")::
("f28d2d03-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Kreditdienstleistungen")::
("f28d2d03-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Πιστωτικές Υπηρεσίες")::
("f28d2d03-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Credit Services")::
("f28d2d03-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de Crédito")::
("f287a2f7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Foreign Money Center Banks")::
("f287a2f7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Banques étrangères")::
("f287a2f7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Fremdwährungszentrum Banken")::
("f287a2f7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ξένες τράπεζες Κεντρικές τράπεζες")::
("f287a2f7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Banche Denaro Centro Estero")::
("f287a2f7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bancos del centro del dinero extranjero")::
("91f96372-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Foreign Regional Banks")::
("91f96372-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Banques régionales étrangères")::
("91f96372-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Ausländische Regionalbanken")::
("91f96372-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Εξωτερικών Περιφερειακών Τραπεζών")::
("91f96372-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Banche regionali esteri")::
("91f96372-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bancos regionales extranjeros")::
("91f9702f-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Money Center Banks")::
("91f9702f-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Centre d argent Banques")::
("91f9702f-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Geld Center Banken")::
("91f9702f-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κέντρο χρημάτων των τραπεζών")::
("91f9702f-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Banche Money Center")::
("91f9702f-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bancos del centro del dinero")::
("91f97dff-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Regional Midwest Banks")::
("91f97dff-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Banques régionales du Midwest")::
("91f97dff-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Regionale Mittlere Westen Banken")::
("91f97dff-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Περιφερειακή Midwest τράπεζα")::
("91f97dff-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Midwest Regional Banks")::
("91f97dff-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bancos Regionales del Medio Oeste")::
("c0db9c3b-be17-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Regional Northeast Banks")::
("c0db9c3b-be17-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Banques régionales du Nord Est")::
("c0db9c3b-be17-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Regionale Nordostbanken")::
("c0db9c3b-be17-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Περιφερειακών Τραπεζών Βορειοανατολικά")::
("c0db9c3b-be17-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Banche Northeast Regional")::
("c0db9c3b-be17-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bancos Noreste Regionales")::
("91f98f07-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Regional Pacific Banks")::
("91f98f07-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Banques régionales du Pacifique")::
("91f98f07-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Regionale Pazifische Banken")::
("91f98f07-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τράπεζες Περιφερειακής Ειρηνικού")::
("91f98f07-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Banche Pacific Regional")::
("91f98f07-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bancos regionales del Pacífico")::
("91f954bc-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Regional Southeast Banks")::
("91f954bc-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Banques régionales du Sud Est")::
("91f954bc-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Regionale Südostbanken")::
("91f954bc-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Περιφερειακών Τραπεζών της Νοτιοανατολικής")::
("91f954bc-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Le banche del sud est regionali")::
("91f954bc-be1b-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bancos Sureste Regionales")::
("f287889e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Regional Southwest Banks")::
("f287889e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Régional Southwest Banks")::
("f287889e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Regionale Südwestbanken")::
("f287889e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Περιφερειακών Τραπεζών Southwest")::
("f287889e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Banche Southwest Regional")::
("f287889e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Regional Southwest Banks")::
("f28d2351-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Savings Loans")::
("f28d2351-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Épargne et prêts")::
("f28d2351-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Ersparnisse und Darlehen")::
("f28d2351-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ταμιευτηρίου Δανείων")::
("f28d2351-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Risparmi e prestiti")::
("f28d2351-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Ahorros y Préstamos")::
("c572baeb-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Basic Materials")::
("c572baeb-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Matériaux de base")::
("c572baeb-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Grundmaterialien")::
("c572baeb-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "βασικά Υλικά")::
("c572baeb-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Materiali di base")::
("c572baeb-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Materiales basicos")::
("f28c4cc5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Basic Materials Wholesale")::
("f28c4cc5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Matériaux de base")::
("f28c4cc5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Grundlegende Materialien Großhandel")::
("f28c4cc5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βασικά υλικά Χονδρική")::
("f28c4cc5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Materiali di base all ingrosso")::
("f28c4cc5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Materiales básicos al por mayor")::
("f28c553d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Packaging Containers")::
("f28c553d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Emballages et conteneurs")::
("f28c553d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Verpackung Behälter")::
("f28c553d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Συσκευασία εμπορευματοκιβώτια")::
("f28c553d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Packaging Containers")::
("f28c553d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Envases y Contenedores")::
("f28c5e9a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Rubber Plastics")::
("f28c5e9a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Caoutchouc et matières plastiques")::
("f28c5e9a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Gummi und Kunststoffe")::
("f28c5e9a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καουτσούκ και πλαστικές ύλες")::
("f28c5e9a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Gomma e materie plastiche")::
("f28c5e9a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Caucho y plásticos")::
("c57259c5-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Capital Goods")::
("c57259c5-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Biens d équipement")::
("c57259c5-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Kapitalgüter")::
("c57259c5-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "κεφαλαιουχικά αγαθά")::
("c57259c5-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Beni strumentali")::
("c57259c5-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bienes de equipo")::
("f287ec9d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Aerospace Defense Major Diversified")::
("f287ec9d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Aérospatiale Défense Major Diversifié")::
("f287ec9d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Luft und Raumfahrt  Verteidigung stark diversifiziert")::
("f287ec9d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Aerospace Defense Major Diversified")::
("f287ec9d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Aerospaziale Difesa Major Diversified")::
("f287ec9d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Aeroespacial Defensa Major Diversified")::
("f2882b81-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Aerospace Defense Products  Services")::
("f2882b81-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Aérospatiale  Produits et services de défense")::
("f2882b81-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Luft und Raumfahrt  Verteidigung Produkte  Dienstleistungen")::
("f2882b81-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Aerospace  Defense Προϊόντα  Υπηρεσίες")::
("f2882b81-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Aerospaziale  Difesa Prodotti e servizi")::
("f2882b81-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Productos y servicios aeroespaciales  defensa")::
("f288201a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Business Equipment")::
("f288201a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Équipement d entreprise")::
("f288201a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Geschäftsausstattung")::
("f288201a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Επαγγελματικού εξοπλισμού")::
("f288201a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "business Equipment")::
("f288201a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Equipo de negocios")::
("f2883827-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Diversified Machinery")::
("f2883827-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Machines diversifiées")::
("f2883827-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Verschiedene Maschinen")::
("f2883827-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "διαφοροποιημένα Μηχανήματα")::
("f2883827-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Diversified Machinery")::
("f2883827-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Maquinaria Diversificada")::
("f288070b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Industrial Electrical Equipment")::
("f288070b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Équipement électrique industriel")::
("f288070b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Industrielle elektrische Ausrüstung")::
("f288070b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βιομηχανική Ηλεκτρολογία")::
("f288070b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Industrial Electrical Equipment")::
("f288070b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Equipo eléctrico industrial")::
("f2885a95-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Industrial Equipment  Components")::
("f2885a95-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Composants de l équipement industriel")::
("f2885a95-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "ndustrieausrüstungskomponenten")::
("f2885a95-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βιομηχανικός Εξοπλισμός Εξαρτήματα")::
("f2885a95-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Componenti per le attrezzature industriali")::
("f2885a95-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Componentes de equipos industriales")::
("f287d3eb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Industrial Equipment Wholesale")::
("f287d3eb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Équipement industriel")::
("f287d3eb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Industrieanlagen Großhandel")::
("f287d3eb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βιομηχανική Χονδρικός Εξοπλισμός")::
("f287d3eb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Commercio all ingrosso attrezzature industriali")::
("f287d3eb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Equipos Industriales")::
("f287bb5b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Machine Tools  Accessories")::
("f287bb5b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Accessoires de machines outils")::
("f287bb5b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Werkzeugmaschinen Zubehör")::
("f287bb5b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Εργαλειομηχανών Αξεσουάρ")::
("f287bb5b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Macchine utensili Accessori")::
("f287bb5b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Accesorios de máquinas herramientas")::
("f287fa7f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Metal Fabrication")::
("f287fa7f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Fabrication de métaux")::
("f287fa7f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Metallverarbeitung")::
("f287fa7f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κατασκευή μεταλλικών")::
("f287fa7f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "metal Fabrication")::
("f287fa7f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Fabricación de metal")::
("f2881319-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Pollution  Treatment Controls")::
("f2881319-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Contrôles du traitement de la pollution")::
("f2881319-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Verschmutzungsbehandlung")::
("f2881319-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Έλεγχοι της ρύπανσης")::
("f2881319-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Controlli trattamento dell inquinamento")::
("f2881319-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Controles de tratamiento de la contaminación")::
("f288436a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Small Tools accessories")::
("f288436a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Accessoires pour petits outils")::
("f288436a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Kleine Werkzeuge Zubehör")::
("f288436a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μικρά Εργαλεία αξεσουάρ")::
("f288436a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "accessori piccoli utensili")::
("f288436a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Accesorios para Herramientas Pequeñas")::
("c572c0a7-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Chemicals")::
("c572c0a7-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Produits chimiques")::
("c572c0a7-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Chemikalien")::
("c572c0a7-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Χημικές ουσίες")::
("c572c0a7-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Sostanze chimiche")::
("c572c0a7-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Productos químicos")::
("f28c7e24-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Agricultural Chemicals")::
("f28c7e24-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Produits chimiques agricoles")::
("f28c7e24-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Landwirtschaftliche Chemikalien")::
("f28c7e24-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "γεωργικά χημικά προϊόντα")::
("f28c7e24-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Agricultural Chemicals")::
("f28c7e24-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Químicos agriculturales")::
("f28c68f4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Chemicals  Major Diversified")::
("f28c68f4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Produits chimiques diversifiés")::
("f28c68f4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Chemikalien stark diversifiziert")::
("f28c68f4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Χημικές ουσίες Σημαντικές Διαφοροποιημένη")::
("f28c68f4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Sostanze chimiche Major Diversified")::
("f28c68f4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Principales Productos Químicos Diversificados")::
("f28c753d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Specialty Chemicals")::
("f28c753d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Produits chimiques spécialisés")::
("f28c753d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Spezialchemikalien")::
("f28c753d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Specialty Chemicals")::
("f28c753d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Specialty Chemicals")::
("f28c753d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Productos Químicos Especiales")::
("f28c87bd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Synthetics")::
("f28c87bd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Synthétique")::
("f28c87bd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Synthetik")::
("f28c87bd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "συνθετικά")::
("f28c87bd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Sintetici")::
("f28c87bd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Sintéticos")::
("c5725dfc-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Clothing")::
("c5725dfc-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Vêtements")::
("c5725dfc-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Kleidung")::
("c5725dfc-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "είδη ένδυσης")::
("c5725dfc-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Capi di abbigliamento")::
("c5725dfc-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Ropa")::
("f2888951-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Apparel Stores")::
("f2888951-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Magasins de vêtements")::
("f2888951-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Bekleidungsgeschäfte")::
("f2888951-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καταστήματα ένδυσης")::
("f2888951-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Abbigliamento Negozi")::
("f2888951-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Tiendas de ropa")::
("f2886d78-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Textile Apparel Clothing")::
("f2886d78-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Textile Vêtements ")::
("f2886d78-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Textil Bekleidung ")::
("f2886d78-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κλωστοϋφαντουργίας ένδυσης ")::
("f2886d78-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Tessile Abbigliamento ")::
("f2886d78-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Ropa Textil ")::
("f2887cd3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Textile Apparel Footwear  Accessories")::
("f2887cd3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Textile")::
("f2887cd3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Textilbekleidung Schuhe Zubehör")::
("f2887cd3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κλωστοϋφαντουργίας Ένδυσης Αξεσουάρ Υπόδηση")::
("f2887cd3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Tessile Abbigliamento Calzature Accessori")::
("f2887cd3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Textil Ropa Calzado Accesorios")::
("f288967c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Textile Industrial")::
("f288967c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Textile Industriel")::
("f288967c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Textilindustrie")::
("f288967c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Textile Industrial")::
("f288967c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Textile Industrial")::
("f288967c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Textil Industrial")::
("c572c752-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Communications")::
("c572c752-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Communications")::
("c572c752-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Kommunikation")::
("c572c752-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Διαβιβάσεις")::
("c572c752-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "comunicazioni")::
("c572c752-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Comunicaciones")::
("f28cbd73-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Communication Equipment")::
("f28cbd73-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Équipement de communication")::
("f28cbd73-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Kommunikationsausrüstung")::
("f28cbd73-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Εξοπλισμός επικοινωνίας")::
("f28cbd73-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Communication Equipment")::
("f28cbd73-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Equipos de comunicacion")::
("f28cb3d8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Diversified Communication Services")::
("f28cb3d8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services de communication diversifiés")::
("f28cb3d8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Diverse Kommunikationsdienste")::
("f28cb3d8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Διαφοροποιημένη Υπηρεσίες Επικοινωνίας")::
("f28cb3d8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Diversified Communication Services")::
("f28cb3d8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de Comunicación Diversificada")::
("f28c9010-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Long Distance Carriers")::
("f28c9010-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Compagnies d interurbains")::
("f28c9010-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Langstreckträger")::
("f28c9010-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Οι μεταφορείς Long Distance")::
("f28c9010-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Vettori a lunga distanza")::
("f28c9010-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Portadores de larga distancia")::
("f28cc75a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Telecom Services Domestic")::
("f28cc75a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services de télécommunications domestiques")::
("f28cc75a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Telekommunikationsdienstleistungen Inland")::
("f28cc75a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τηλεπικοινωνιακές Υπηρεσίες Εσωτερικού")::
("f28cc75a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Telecom Servizi Nazionali")::
("f28cc75a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de telecomunicaciones nacionales")::
("f28c9b67-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Telecom Services  Foreign")::
("f28c9b67-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services Télécoms")::
("f28c9b67-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Telecom Services Foreign")::
("f28c9b67-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τηλεπικοινωνιακές Υπηρεσίες Εξωτερικού")::
("f28c9b67-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Telecom Servizi Esteri")::
("f28c9b67-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de telecomunicaciones")::
("f28ca6d4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Wireless Communications")::
("f28ca6d4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Communications sans fil")::
("f28ca6d4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Drahtlose Kommunikation")::
("f28ca6d4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ασύρματες Επικοινωνίες")::
("f28ca6d4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "wireless Communications")::
("f28ca6d4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Comunicaciones inalámbricas")::
("c5726468-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Conglomerates")::
("c5726468-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Conglomérats")::
("c5726468-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Konglomerate")::
("c5726468-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "ομίλων")::
("c5726468-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "conglomerati")::
("c5726468-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Conglomerados")::
("c572cdb7-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Construction")::
("c572cdb7-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Construction")::
("c572cdb7-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Bau")::
("c572cdb7-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κατασκευή")::
("c572cdb7-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Costruzione")::
("c572cdb7-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Construcción")::
("f28cdb92-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Building Materials Wholesale")::
("f28cdb92-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Matériaux de construction")::
("f28cdb92-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Baustoffe Großhandel")::
("f28cdb92-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Οικοδομικά Υλικά Χονδρική")::
("f28cdb92-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Commercio all ingrosso materiali da costruzione")::
("f28cdb92-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Materiales de Construcción")::
("f28d1138-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Cement")::
("f28d1138-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Ciment")::
("f28d1138-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Zement")::
("f28d1138-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τσιμέντο")::
("f28d1138-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Cemento")::
("f28d1138-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Cemento")::
("f28ce4ef-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "General Building Materials")::
("f28ce4ef-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Matériaux de construction générale")::
("f28ce4ef-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Allgemeine Baustoffe")::
("f28ce4ef-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γενικά Οικοδομικά Υλικά")::
("f28ce4ef-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "General Building Materials")::
("f28ce4ef-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Materiales de construcción generales")::
("f28cd11f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "General Contractors")::
("f28cd11f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Entrepreneurs généraux")::
("f28cd11f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Generalunternehmer")::
("f28cd11f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "γενικές Εργασίες")::
("f28cd11f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "General Contractors")::
("f28cd11f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Contratistas Generales")::
("f28cfd94-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Heavy Construction")::
("f28cfd94-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Construction Lourde")::
("f28cfd94-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Schwerer Bau")::
("f28cfd94-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "βαριά κατασκευή")::
("f28cfd94-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Costruzioni pesanti")::
("f28cfd94-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Construcción pesada")::
("f28d1ade-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Lumber  Wood Production")::
("f28d1ade-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Bois de construction")::
("f28d1ade-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Holz Holzproduktion")::
("f28d1ade-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ξύλο Ξύλο Παραγωγής")::
("f28d1ade-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Legname Legno Produzione")::
("f28d1ade-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Madera Madera Producción")::
("f28cf2df-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Manufactured Housing")::
("f28cf2df-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Boîtier manufacturé")::
("f28cf2df-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Hergestelltes Gehäuse")::
("f28cf2df-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "κατασκευάζεται Στέγαση")::
("f28cf2df-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Housing Prodotto")::
("f28cf2df-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Vivienda manufacturada")::
("f28d0846-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Residential Construction")::
("f28d0846-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Construction résidentielle")::
("f28d0846-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Wohnbau")::
("f28d0846-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κατοικίες Κατασκευή")::
("f28d0846-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Edilizia residenziale")::
("f28d0846-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Construcción residencial")::
("c5726aa1-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Consumer Durables")::
("c5726aa1-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Biens de consommation durables")::
("c5726aa1-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Gebrauchsgüter")::
("c5726aa1-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "διαρκή καταναλωτικά αγαθά")::
("c5726aa1-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Beni di consumo durevoli")::
("c5726aa1-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bienes de consumo duraderos")::
("f288a1ef-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Appliances")::
("f288a1ef-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "appareils électroménagers")::
("f288a1ef-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Geräte")::
("f288a1ef-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "συσκευές")::
("f288a1ef-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Appliances")::
("f288a1ef-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Accesorios")::
("f288ee1f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Electronic Equipment")::
("f288ee1f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Équipement électronique")::
("f288ee1f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Elektronische Geräte")::
("f288ee1f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ηλεκτρονικός εξοπλισμός")::
("f288ee1f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Equipaggiamento elettronico")::
("f288ee1f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Equipo electronico")::
("f288ba7b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Home Furnishing  Fixtures")::
("f288ba7b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Équipement d ameublement")::
("f288ba7b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Home Einrichtungsgegenstände")::
("f288ba7b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Οικιακός Εξοπλισμός Έπιπλα")::
("f288ba7b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Home Furnishing Infissi")::
("f288ba7b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Muebles para el hogar")::
("f288dc05-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Home Furnishing Stores")::
("f288dc05-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Magasins d ameublement")::
("f288dc05-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Einrichtungsgegenstände")::
("f288dc05-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Οικιακός Εξοπλισμός Καταστήματα")::
("f288dc05-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Home Furnishing Stores")::
("f288dc05-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Tiendas de muebles para el hogar")::
("f288aed7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Home Improvement Stores")::
("f288aed7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Magasins d ameublement")::
("f288aed7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Home Improvement Stores")::
("f288aed7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καταστήματα Αρχική Βελτίωση")::
("f288aed7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Negozi Home Improvement")::
("f288aed7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Tiendas De Mejoras Para El Hogar")::
("f288f7ad-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Housewares  Accessories")::
("f288f7ad-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Accessoires de ménage")::
("f288f7ad-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Haushaltswaren Zubehör")::
("f288f7ad-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "οικιακών ειδών Αξεσουάρ")::
("f288f7ad-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Accessori casalinghi")::
("f288f7ad-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Accesorios para el hogar")::
("f288d251-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Jewelry Stores")::
("f288d251-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Bijouterie et Joaillerie")::
("f288d251-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Schmuckgeschäfte")::
("f288d251-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "κόσμημα")::
("f288d251-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "negozi di gioielli")::
("f288d251-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Joyerías")::
("f288e458-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Photographic Equipment  Supplies")::
("f288e458-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Fournitures photographiques")::
("f288e458-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Fotoausrüstung Zubehör")::
("f288e458-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Φωτογραφικά Υλικά Είδη Μηχανές")::
("f288e458-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Forniture Attrezzature fotografiche")::
("f288e458-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Suministros para equipos fotográficos")::
("f288c75f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Recreational Vehicles")::
("f288c75f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Véhicules récréatifs")::
("f288c75f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Freizeitfahrzeuge")::
("f288c75f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Οχήματα αναψυχής")::
("f288c75f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Veicoli ricreazionali")::
("f288c75f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Vehículos recreacionales")::
("c572d343-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Consumer Goods")::
("c572d343-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Biens de consommation")::
("c572d343-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Konsumgüter")::
("c572d343-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καταναλωτικά αγαθά")::
("c572d343-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "generi di consumo")::
("c572d343-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bienes de consumo")::
("c572703c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Consumer Non Durables")::
("c572703c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Consommateur Non Durables")::
("c572703c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Konsumenten Non Durables")::
("c572703c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καταναλωτή για μη διαρκή αγαθά")::
("c572703c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Di consumo non durevoli")::
("c572703c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Consumidor No Durables")::
("f2894636-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Cigarettes")::
("f2894636-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Cigarettes")::
("f2894636-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Zigaretten")::
("f2894636-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "τσιγάρα")::
("f2894636-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Le sigarette")::
("f2894636-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Cigarrillos")::
("f2898b39-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Cleaning Products")::
("f2898b39-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Produits de nettoyage")::
("f2898b39-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Reinigungsmittel")::
("f2898b39-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Προιόντα καθαρισμού")::
("f2898b39-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Prodotti per la pulizia")::
("f2898b39-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Productos de limpieza")::
("f28929b3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Department Stores")::
("f28929b3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Grands magasins")::
("f28929b3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Warenhäuser")::
("f28929b3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Πολυκαταστήματα")::
("f28929b3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Grandi magazzini")::
("f28929b3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Grandes almacenes")::
("f2896db6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Discount Variety Stores")::
("f2896db6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Magasins Discount")::
("f2896db6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Discount Variety Stores")::
("f2896db6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καταστήματα Έκπτωση Ποικιλία")::
("f2896db6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Discount Varietà")::
("f2896db6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Tiendas De Variedades")::
("f2897831-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Food  Major Diversified")::
("f2897831-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Alimentaire Diversifié")::
("f2897831-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Essen Major Diversified")::
("f2897831-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Food  Major Diversified")::
("f2897831-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Il cibo Major Diversified")::
("f2897831-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Alimentación Mayor Diversificada")::
("f2898167-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Office Supplies")::
("f2898167-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Fournitures de bureau")::
("f2898167-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Bürobedarf")::
("f2898167-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Προμήθειες γραφείου")::
("f2898167-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Attrezzature da ufficio")::
("f2898167-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Material de oficina")::
("f2895090-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Paper  Paper Products")::
("f2895090-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Produits de papier")::
("f2895090-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Papier Papierprodukte")::
("f2895090-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Προϊόντα Χαρτί")::
("f2895090-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Di carta prodotti cartacei")::
("f2895090-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Productos de papel")::
("f2893d0e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Personal Products")::
("f2893d0e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Produits personnels")::
("f2893d0e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Persönliche Produkte")::
("f2893d0e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Προϊόντα Προσωπικής Περιποίησης")::
("f2893d0e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "personal Products")::
("f2893d0e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Productos personales")::
("f28932c5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Recreational Goods Other")::
("f28932c5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Autre")::
("f28932c5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Freizeitartikel Andere")::
("f28932c5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αναψυχής ΠΡΟΪΟΝΤΑ Άλλα")::
("f28932c5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Recreational merci diverse")::
("f28932c5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Otros")::
("f2891640-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Speciality Retail Other")::
("f2891640-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Commerce de détail spécialisé Autre")::
("f2891640-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Spezialität Einzelhandel")::
("f2891640-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Εξειδικευμένο Λιανικό Άλλα")::
("f2891640-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Specialità al dettaglio Altro")::
("f2891640-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Venta al por menor especializada")::
("f2890251-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Sporting Goods")::
("f2890251-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Articles de sport")::
("f2890251-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Sportwaren")::
("f2890251-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αθλητικά είδη")::
("f2890251-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Sporting Goods")::
("f2890251-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Artículos deportivos")::
("f289641e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Sporting Goods Stores")::
("f289641e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Magasins de sport")::
("f289641e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Sportartikel")::
("f289641e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καταστήματα Αθλητικών Ειδών")::
("f289641e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Negozi sportivi")::
("f289641e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Tiendas de artículos deportivos")::
("f2890bcd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Toys Games")::
("f2890bcd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Jouets Jeux")::
("f2890bcd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Spielzeug Spiele")::
("f2890bcd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Παιχνίδια Παιχνίδια")::
("f2890bcd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "giocattoli Giochi")::
("f2890bcd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Juguetes Juegos")::
("f28959ab-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Wholesale Other")::
("f28959ab-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Vente en gros Autre")::
("f28959ab-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Großhandel Andere")::
("f28959ab-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Χονδρικό Άλλα")::
("f28959ab-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Commercio all ingrosso Altro")::
("f28959ab-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Venta al por mayor Otros")::
("c5727a2c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Drugs")::
("c5727a2c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Drogues")::
("c5727a2c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Drogen")::
("c5727a2c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Φάρμακα")::
("c5727a2c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "farmaci")::
("c5727a2c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Drogas")::
("f28994d3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Drug Delivery")::
("f28994d3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "L administration de médicaments")::
("f28994d3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Drogenlieferung")::
("f28994d3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Παράδοση φαρμάκων")::
("f28994d3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Consegna farmaci")::
("f28994d3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Entrega de fármacos")::
("f289a8d6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Drug Manufacturers  Major")::
("f289a8d6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Principaux fabricants de médicaments")::
("f289a8d6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Drug Hersteller Major")::
("f289a8d6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Drug Manufacturers Major")::
("f289a8d6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Drug Manufacturers Maggiore")::
("f289a8d6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Principales fabricantes de drogas")::
("f289cfe2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Drug Manufactures Other")::
("f289cfe2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Fabricants de drogues Autres")::
("f289cfe2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Arzneimittelherstellung Andere")::
("f289cfe2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Φάρμακο Κατασκευές Άλλα")::
("f289cfe2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Drug produce Altro")::
("f289cfe2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Manufacturas de drogas")::
("f289b334-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Drug Related Products")::
("f289b334-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Produits Pharmaceutiques")::
("f289b334-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Arzneimittel Verwandte Produkte")::
("f289b334-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Φάρμακο Σχετικά προϊόντα")::
("f289b334-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Drug Related Products")::
("f289b334-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Productos Relacionados con las Drogas")::
("f289bc4e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Drug Stores")::
("f289bc4e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Magasin de vêtements")::
("f289bc4e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Drogerien")::
("f289bc4e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Drug Stores")::
("f289bc4e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Drug Stores")::
("f289bc4e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Tiendas de drogas")::
("f289c6c6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Drug Wholesale")::
("f289c6c6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Vente de produits pharmaceutiques")::
("f289c6c6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Droge Großhandel")::
("f289c6c6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Drug Wholesale")::
("f289c6c6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Commercio all ingrosso della droga")::
("f289c6c6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Venta al por mayor de drogas")::
("f2899f8f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Drugs Generic")::
("f2899f8f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Médicaments Génériques")::
("f2899f8f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Drogen Generika")::
("f2899f8f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Drugs Generic")::
("f2899f8f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Farmaci generici ")::
("f2899f8f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Drogas Genérico")::
("c572ddea-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Electronics")::
("c572ddea-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Électronique")::
("c572ddea-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Elektronik")::
("c572ddea-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "ΗΛΕΚΤΡΟΝΙΚΑ ΕΙΔΗ")::
("c572ddea-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Elettronica")::
("c572ddea-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Electrónica")::
("f28d4252-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Diversified Electronics")::
("f28d4252-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Électronique diversifiée")::
("f28d4252-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Vielfältige Elektronik")::
("f28d4252-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "διαφοροποιημένη Ηλεκτρονικά")::
("f28d4252-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Diversified Electronics")::
("f28d4252-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Electrónica Diversificada")::
("f28d4bb8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Electronic Stores")::
("f28d4bb8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Magasins d électronique")::
("f28d4bb8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Elektronische Stores")::
("f28d4bb8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "ηλεκτρονικά καταστήματα")::
("f28d4bb8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "negozi di elettronica")::
("f28d4bb8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Tiendas Electrónicas")::
("f28d3759-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Electronic Wholesale")::
("f28d3759-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Vente en gros")::
("f28d3759-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Elektronischer Großhandel")::
("f28d3759-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "ηλεκτρονική Χονδρική")::
("f28d3759-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Commercio all ingrosso elettronico")::
("f28d3759-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Venta al por mayor electrónica")::
("f28d5b7a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Processing Systems Products")::
("f28d5b7a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Systèmes de traitement")::
("f28d5b7a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Verarbeitungssysteme Produkte")::
("f28d5b7a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Συστήματα Επεξεργασίας Προϊόντα")::
("f28d5b7a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Sistemi di elaborazione Prodotti")::
("f28d5b7a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Sistemas de procesamiento de productos")::
("c57280b0-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Energy")::
("c57280b0-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Énergie")::
("c57280b0-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Energie")::
("c57280b0-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ενέργεια")::
("c57280b0-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Energia")::
("c57280b0-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Energía")::
("f289ee03-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Independent Oil Gas")::
("f289ee03-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Gaz de pétrole indépendant")::
("f289ee03-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Unabhängiges Ölgas")::
("f289ee03-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ανεξάρτητη αερίου πετρελαίου")::
("f289ee03-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Independent Oil Gas")::
("f289ee03-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Gas de petróleo independiente")::
("f28a023e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Major Integrated Oil Gas")::
("f28a023e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Principaux produits pétroliers intégrés")::
("f28a023e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Großes integriertes Ölgas")::
("f28a023e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Major Integrated Oil Gas")::
("f28a023e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Maggiore Integrated Oil Gas")::
("f28a023e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Principales aceites de petróleo integrados")::
("f289e38e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Oil Gas Drilling Exploration")::
("f289e38e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Exploration de forage de gaz pétrolier")::
("f289e38e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Öl Gasbohrung Erforschung")::
("f289e38e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Πετρέλαιο Αέριο γεωτρήσεις")::
("f289e38e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Oil Gas Drilling Esplorazione")::
("f289e38e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Perforación y Exploración de Gas Petrolífero")::
("f289da45-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Oil Gas Equipment Services")::
("f289da45-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services")::
("f289da45-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Öl Gasanlagen Dienstleistungen")::
("f289da45-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Πετρέλαιο Υπηρεσίες Εξοπλισμός αερίου")::
("f289da45-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Oil Gas Equipment Servizi")::
("f289da45-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de equipos de gas y petróleo")::
("f289f7b3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Oil Gas Pipelines")::
("f289f7b3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Gazoducs")::
("f289f7b3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Öl Gas Pipelines")::
("f289f7b3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Πετρέλαιο αγωγοί φυσικού αερίου")::
("f289f7b3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Oleodotti gas")::
("f289f7b3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Gasoductos")::
("f28a0acb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Oil Gas Refining  Marketing")::
("f28a0acb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Pétrole Gaz Raffinage Marketing")::
("f28a0acb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Öl Gas Raffinerie Marketing")::
("f28a0acb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Oil Gas Refining  Marketing")::
("f28a0acb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Oil Gas Refining Marketing")::
("f28a0acb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Petróleo y Gas Refinación Marketing")::
("c572e3db-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Entertainment")::
("c572e3db-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Divertissement")::
("c572e3db-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Unterhaltung")::
("c572e3db-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ψυχαγωγία")::
("c572e3db-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Divertimento")::
("c572e3db-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Entretenimiento")::
("f28da739-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Entertainment  Diversified")::
("f28da739-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Divertissement Diversified")::
("f28da739-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Unterhaltung diversifiziert")::
("f28da739-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Διασκέδαση Διαφοροποιημένη")::
("f28da739-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "intrattenimento Diversified")::
("f28da739-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Entretenimiento Diversified")::
("f28d68be-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Gaming Activities")::
("f28d68be-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Activités de jeu")::
("f28d68be-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Spielaktivitäten")::
("f28d68be-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Δραστηριότητες Gaming")::
("f28d68be-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "gaming Attività")::
("f28d68be-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Actividades de juego")::
("f28d9dda-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "General Entertainment")::
("f28d9dda-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Général Entertainment")::
("f28d9dda-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Allgemeine Unterhaltung")::
("f28d9dda-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γενικά Ψυχαγωγία")::
("f28d9dda-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Generale intrattenimento")::
("f28d9dda-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Entretenimiento General")::
("f28d7e13-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Movie Production Theaters")::
("f28d7e13-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Production de films")::
("f28d7e13-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Film Produktionstheater")::
("f28d7e13-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κινηματογράφοι Παραγωγής")::
("f28d7e13-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Cinema Produzione")::
("f28d7e13-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Teatros de producción cinematográfica")::
("f28db199-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Music Video Stores")::
("f28db199-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Magasins de musique")::
("f28db199-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Musik Video Shops")::
("f28db199-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καταστήματα Music Video")::
("f28db199-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Negozi Music Video")::
("f28db199-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Tiendas de Video Musical")::
("f28d9307-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Resorts Casinos")::
("f28d9307-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Casinos de villégiature")::
("f28d9307-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Resorts Casinos")::
("f28d9307-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Resorts Καζίνο")::
("f28d9307-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Resorts Casinò")::
("f28d9307-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Resorts Casinos")::
("f28d892b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Restaurants")::
("f28d892b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Restaurants")::
("f28d892b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Restaurants")::
("f28d892b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "εστιατόρια")::
("f28d892b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Ristoranti")::
("f28d892b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Restaurantes y Bares")::
("f28d7461-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Toy Hobby Stores")::
("f28d7461-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Magasins de jouets")::
("f28d7461-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Spielzeug Hobby Stores")::
("f28d7461-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καταστήματα Παιχνιδιών Χόμπι")::
("f28d7461-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Negozi di giocattoli Hobby")::
("f28d7461-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Tiendas de juguetes")::
("c57284e1-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Financial")::
("c57284e1-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Financier")::
("c57284e1-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Finanziell")::
("c57284e1-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Χρηματοοικονομική")::
("c57284e1-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Finanziario")::
("c57284e1-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Financiero")::
("c572e92e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Food and Beverages")::
("c572e92e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Aliments et boissons")::
("c572e92e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Essen und Getränke")::
("c572e92e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Φαγητό και αναψυκτικά")::
("c572e92e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Cibo e bevande")::
("c572e92e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Comida y bebidas")::
("f28dfd4f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Beverage Soft Drinks")::
("f28dfd4f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Boissons sans alcool")::
("f28dfd4f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Getränke alkoholfreie Getränke")::
("f28dfd4f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ποτά Αναψυκτικά")::
("f28dfd4f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Beverage Soft Drinks")::
("f28dfd4f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bebidas Refrescos")::
("f28dbae2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Beverages Wineries Distillers")::
("f28dbae2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Boissons")::
("f28dbae2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Getränke Weingüter Distillers")::
("f28dbae2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ποτά Οινοποιεία Distillers")::
("f28dbae2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Bevande Cantine Distillers")::
("f28dbae2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bebidas Bodegas Destilerías")::
("f28e0702-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Confectioners")::
("f28e0702-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Confiseurs")::
("f28e0702-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Konditoren")::
("f28e0702-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "ζαχαροπλάστες")::
("f28e0702-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "pasticceri")::
("f28e0702-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Confiteros")::
("f28ddd5f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Dairy Products")::
("f28ddd5f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Les produits laitiers")::
("f28ddd5f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Milchprodukte")::
("f28ddd5f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "γαλακτοκομικά προϊόντα")::
("f28ddd5f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "latticini")::
("f28ddd5f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Productos lácteos")::
("f28dcf56-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Food Brewers")::
("f28dcf56-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Brasseries")::
("f28dcf56-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Lebensmittelbrauer")::
("f28dcf56-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Brewers τροφίμων")::
("f28dcf56-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Brewers alimentari")::
("f28dcf56-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Cerveceros de comida")::
("f28e10ec-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Meat Products")::
("f28e10ec-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Produits carnés")::
("f28e10ec-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Fleischprodukte")::
("f28e10ec-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Προϊόντα κρέατος")::
("f28e10ec-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Prodotti a base di carne")::
("f28e10ec-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Productos de carne")::
("f28de73d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Processed Packaged Goods")::
("f28de73d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Produits conditionnés traités")::
("f28de73d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Verarbeitete verpackte Waren")::
("f28de73d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Επεξεργασμένα Συσκευασμένα Αγαθά")::
("f28de73d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Packaged Goods trasformati")::
("f28de73d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Productos envasados procesados")::
("f28df426-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Speciality Eateries")::
("f28df426-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Spécialité Eateries")::
("f28df426-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Spezialitäten")::
("f28df426-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Speciality Eateries")::
("f28df426-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Punti di ristoro specializzati")::
("f28df426-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Especialidad de Eateries")::
("f28dc558-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Tobacco Products Other")::
("f28dc558-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Produits du tabac Autres")::
("f28dc558-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Tabakwaren Andere")::
("f28dc558-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καπνός Προϊόντα Άλλα")::
("f28dc558-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Tabacco Prodotti Altro")::
("f28dc558-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Productos de Tabaco")::
("c57288b9-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Hardware")::
("c57288b9-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Hardware")::
("c57288b9-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Hardware")::
("c57288b9-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Hardware")::
("c57288b9-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Hardware")::
("c57288b9-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Hardware")::
("f28a1ea1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Computer Based Systems")::
("f28a1ea1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Systèmes informatiques")::
("f28a1ea1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Computerbasierte Systeme")::
("f28a1ea1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Συστήματα που βασίζεται σε υπολογιστή")::
("f28a1ea1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Sistemi basati su computer")::
("f28a1ea1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Sistemas basados en computadora")::
("f28a1555-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Computer Peripherals")::
("f28a1555-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Périphériques d ordinateur")::
("f28a1555-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Computer Peripheriegeräte")::
("f28a1555-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Περιφερειακά υπολογιστών")::
("f28a1555-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Periferiche del computer")::
("f28a1555-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Periféricos de la computadora")::
("f28a302f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Data Storage Devices")::
("f28a302f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Périphériques de stockage de données")::
("f28a302f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Datenspeichergeräte")::
("f28a302f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Συσκευές αποθήκευσης δεδομένων")::
("f28a302f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Dispositivi di archiviazione dati")::
("f28a302f-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Dispositivos de almacenamiento de datos")::
("f28a5fb9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Electronic Equipment")::
("f28a5fb9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Équipement électronique")::
("f28a5fb9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Elektronische Geräte")::
("f28a5fb9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ηλεκτρονικός εξοπλισμός")::
("f28a5fb9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Equipaggiamento elettronico")::
("f28a5fb9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Equipo electronico")::
("f28a40e3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Networking Communication Devices")::
("f28a40e3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Dispositifs de communication réseau")::
("f28a40e3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Netzwerkkommunikationsgeräte")::
("f28a40e3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Δικτύωση συσκευών επικοινωνίας")::
("f28a40e3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Dispositivi di comunicazione di rete")::
("f28a40e3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Dispositivos de comunicación en red")::
("f28a4d65-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Printed Circuit Boards")::
("f28a4d65-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Cartes de circuits imprimés")::
("f28a4d65-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Leiterplatten")::
("f28a4d65-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τυπωμένα κυκλώματα")::
("f28a4d65-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Circuiti stampati")::
("f28a4d65-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Placas de circuito impreso")::
("c572efb9-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Healthcare")::
("c572efb9-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Soins de santé")::
("c572efb9-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Gesundheitswesen")::
("c572efb9-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Φροντίδα υγείας")::
("c572efb9-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Assistenza sanitaria")::
("c572efb9-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Cuidado de la salud")::
("f28e4d42-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Biotechnology")::
("f28e4d42-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Biotechnologie")::
("f28e4d42-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Biotechnologie")::
("f28e4d42-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "βιοτεχνολογία")::
("f28e4d42-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Biotecnologia")::
("f28e4d42-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Biotecnología")::
("f28e4415-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Diagnostic Substances")::
("f28e4415-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Substances diagnostiques")::
("f28e4415-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Diagnostische Substanzen")::
("f28e4415-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "διαγνωστικές ουσίες")::
("f28e4415-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Diagnostic Substances")::
("f28e4415-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Sustancias Diagnósticas")::
("f28e2feb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Health Care Plans")::
("f28e2feb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Soins de santé")::
("f28e2feb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Gesundheitspflegepläne")::
("f28e2feb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σχέδια Φροντίδας Υγείας")::
("f28e2feb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Piani di assistenza sanitaria")::
("f28e2feb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Planes de salud")::
("f28e6135-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Healthcare Information Services")::
("f28e6135-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services d information sur la santé")::
("f28e6135-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Gesundheitswesen Informationsdienste")::
("f28e6135-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Υπηρεσίες πληροφοριών υγείας")::
("f28e6135-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Healthcare Information Services")::
("f28e6135-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de Información Médica")::
("f28e1bc7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Home Health Care")::
("f28e1bc7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Soin à domicile")::
("f28e1bc7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Häusliche Krankenpflege")::
("f28e1bc7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αρχική Φροντίδα Υγείας")::
("f28e1bc7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Assistenza domiciliare")::
("f28e1bc7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Cuidado de la Salud en el Hogar")::
("f28e75f3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Medical Appliances  Equipment")::
("f28e75f3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Appareils médicaux")::
("f28e75f3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Medizinische Geräte Ausrüstung")::
("f28e75f3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ιατρικά Μηχανήματα Συσκευές")::
("f28e75f3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Medical Appliances Attrezzature")::
("f28e75f3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Equipos Médicos Equipos")::
("f28e39b0-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Medical Equipment Wholesale")::
("f28e39b0-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Équipement médical")::
("f28e39b0-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Medizinische Geräte Großhandel")::
("f28e39b0-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Χονδρικό Ιατρικός Εξοπλισμός")::
("f28e39b0-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Attrezzature mediche all ingrosso")::
("f28e39b0-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Equipos médicos al por mayor")::
("f28e6c14-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Medical Instruments  supplies")::
("f28e6c14-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Fournitures pour instruments médicaux")::
("f28e6c14-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Medizinische Instrumente liefert")::
("f28e6c14-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "προμηθειών Ιατρικών Οργάνων")::
("f28e6c14-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "forniture Medical Instruments")::
("f28e6c14-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Instrumentos médicos")::
("f28e57ad-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Medical Practitioners")::
("f28e57ad-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Médecins")::
("f28e57ad-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Ärzte")::
("f28e57ad-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "ιατρών")::
("f28e57ad-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "I medici")::
("f28e57ad-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Médicos practicantes")::
("f28e250a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Specialized Health Services")::
("f28e250a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services de santé spécialisés")::
("f28e250a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Spezialisierte Gesundheitsdienste")::
("f28e250a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Εξειδικευμένες Υπηρεσίες Υγείας")::
("f28e250a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Specialized Servizi sanitari")::
("f28e250a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios Especializados de Salud")::
("c5728cc2-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Industrial Goods")::
("c5728cc2-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Produits industriels")::
("c5728cc2-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Industrieprodukte")::
("c5728cc2-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "βιομηχανικά Προϊόντα")::
("c5728cc2-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Beni industriali")::
("c5728cc2-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bienes industriales")::
("c572f3c2-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Information Technology ")::
("c572f3c2-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Technologie de l information ")::
("c572f3c2-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Informationstechnologie ")::
("c572f3c2-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τεχνολογία των πληροφοριών ")::
("c572f3c2-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Information Technology ")::
("c572f3c2-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Tecnología de la Información ")::
("c572968e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Insurance")::
("c572968e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Assurance")::
("c572968e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Versicherung")::
("c572968e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "ΑΣΦΑΛΙΣΗ")::
("c572968e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Assicurazione")::
("c572968e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Seguro")::
("f28a76eb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Accident Health Insurance")::
("f28a76eb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Assurance maladie")::
("f28a76eb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Unfall Krankenversicherung")::
("f28a76eb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ατύχημα Ασφάλισης Υγείας")::
("f28a76eb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Assicurazione contro gli infortuni Salute")::
("f28a76eb-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Seguro de Salud por Accidentes")::
("f28a6c73-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Insurance Brokers")::
("f28a6c73-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Courtiers d assurance")::
("f28a6c73-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Versicherungsmakler")::
("f28a6c73-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μεσίτες Ασφαλίσεων")::
("f28a6c73-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Brokers di assicurazione")::
("f28a6c73-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Corredores de seguros")::
("f28a823b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Life Insurance")::
("f28a823b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Assurance vie")::
("f28a823b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Lebensversicherung")::
("f28a823b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ασφάλεια ζωής")::
("f28a823b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Assicurazione sulla vita")::
("f28a823b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Seguro de vida")::
("f28a8c09-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Property & Casualty Insurance")::
("f28a8c09-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Assurance dommages")::
("f28a8c09-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Schaden und Unfallversicherung")::
("f28a8c09-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ακίνητα & Ζημιών")::
("f28a8c09-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Property & Casualty Insurance")::
("f28a8c09-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Seguro de Propiedad y Accidentes")::
("f28a9851-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Surety & Title Insurance")::
("f28a9851-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Assurance de la caution et du titre")::
("f28a9851-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Surety & Title Versicherung")::
("f28a9851-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Εγγύηση & Τίτλος Ασφάλειες")::
("f28a9851-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Surety & titolo di assicurazione")::
("f28a9851-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Seguro de Responsabilidad Civil y Título")::
("ca175bd4-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Investing")::
("ca175bd4-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Investissement")::
("ca175bd4-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Investieren")::
("ca175bd4-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Επένδυση")::
("ca175bd4-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Investire")::
("ca175bd4-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Invertir")::
("f28e80b1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Asset Management")::
("f28e80b1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "La gestion d actifs")::
("f28e80b1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Anlagenmanagement")::
("f28e80b1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Διαχείριση περιουσιακών στοιχείων")::
("f28e80b1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Gestione delle risorse")::
("f28e80b1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Gestión de activos")::
("f28e955b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Closed - End - Fund - Debt")::
("f28e955b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Fonds Clôturé - Dette")::
("f28e955b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Closed-End Fund - Schulden")::
("f28e955b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Closed-End Fund - Χρέος")::
("f28e955b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Fondo chiuso - Debito")::
("f28e955b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Fondo cerrado - Deuda")::
("f28ec8c4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Closed-End Fund - Equity")::
("f28ec8c4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Fonds à capital fixe - Capitaux propres")::
("f28ec8c4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Closed-End Fund - Eigenkapital")::
("f28ec8c4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Closed-End Fund - Equity")::
("f28ec8c4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Fondo chiuso - Patrimonio")::
("f28ec8c4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Fondo cerrado - Patrimonio neto")::
("f28e8a5a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Closed-End Fund - Foreign")::
("f28e8a5a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Fonds à capital fixe - Étranger")::
("f28e8a5a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Closed-End Fund - Ausländisch")::
("f28e8a5a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Closed-End Fund - Εξωτερικού")::
("f28e8a5a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Fondo Chiuso - Esteri")::
("f28e8a5a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Fondo cerrado - Extranjero")::
("f28eb4dd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Diversified Investments")::
("f28eb4dd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Placements diversifiés")::
("f28eb4dd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Diversifizierte Investitionen")::
("f28eb4dd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "διαφοροποιημένες επενδύσεις")::
("f28eb4dd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Diversified Investments")::
("f28eb4dd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Inversiones Diversificadas")::
("f28ea45d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Investment Brokerage - National")::
("f28ea45d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Courtage en Investissement - National")::
("f28ea45d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Investment Brokerage - National")::
("f28ea45d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Επενδυτικής Διαμεσολάβησης - Εθνική")::
("f28ea45d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Investment Brokerage - National")::
("f28ea45d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Corretaje de Inversiones - Nacional")::
("f28ed17c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Investments Brokerage - Regional")::
("f28ed17c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Investissements Courtage - Régional")::
("f28ed17c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Investments Brokerage - Regional")::
("f28ed17c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Επενδύσεις Χρηματιστηριακές - Περιφερειακή")::
("f28ed17c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Investimenti Brokerage - Regional")::
("f28ed17c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Intermediación en Inversiones - Regional")::
("f28ebea4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Mortgage Investment")::
("f28ebea4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Investissement hypothécaire")::
("f28ebea4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Hypothekeninvestitionen")::
("f28ebea4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Δανείου Επενδύσεων")::
("f28ebea4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Mortgage Investment")::
("f28ebea4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Inversión Hipotecaria")::
("c5729b22-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "It Services")::
("c5729b22-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services informatiques")::
("c5729b22-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "IT-Service")::
("c5729b22-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "είναι Υπηρεσίες")::
("c5729b22-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "It Services")::
("c5729b22-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios")::
("f28aac3a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Information & Delivery Services")::
("f28aac3a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services d information et de livraison")::
("f28aac3a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Informations und Lieferdienste")::
("f28aac3a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Υπηρεσίες Πληροφορικής & Παράδοσης")::
("f28aac3a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Informazioni e consegna a domicilio")::
("f28aac3a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Información y servicios de entrega")::
("f28ad108-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Information Technology Services")::
("f28ad108-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services de technologies de l information")::
("f28ad108-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Informationstechnologie Dienstleistungen")::
("f28ad108-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Υπηρεσιών Πληροφορικής")::
("f28ad108-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Information Technology Services")::
("f28ad108-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de Tecnología de la Información")::
("f28ac1f7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Internet Information Providers")::
("f28ac1f7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Fournisseurs d informations Internet")::
("f28ac1f7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Internet-Informationsanbieter")::
("f28ac1f7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Παροχής Πληροφοριών στο Internet")::
("f28ac1f7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Internet Information Providers")::
("f28ac1f7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Proveedores de información de Internet")::
("f28aa191-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Internet Service Providers")::
("f28aa191-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Les fournisseurs de services internet")::
("f28aa191-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Internetanbieter")::
("f28aa191-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Παροχείς Υπηρεσιών Διαδικτύου")::
("f28aa191-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Internet Service Provider")::
("f28aa191-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Proveedores de servicio de Internet")::
("f28ab602-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Internet Software & Services")::
("f28ab602-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Logiciels et services Internet")::
("f28ab602-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Internet Software & Dienstleistungen")::
("f28ab602-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Λογισμικό Internet & Υπηρεσίες")::
("f28ab602-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Software e servizi internet")::
("f28ab602-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Software y servicios de Internet")::
("d290031e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Media")::
("d290031e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Médias")::
("d290031e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Medien")::
("d290031e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μεσο ΜΑΖΙΚΗΣ ΕΝΗΜΕΡΩΣΗΣ")::
("d290031e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "media")::
("d290031e-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Medios de comunicación")::
("f28f03c7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Advertising Agencies")::
("f28f03c7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Agences de publicité")::
("f28f03c7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Werbeagenturen")::
("f28f03c7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Διαφημιστικές Εταιρίες & Εργαστήρια")::
("f28f03c7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Agenzie pubblicitarie")::
("f28f03c7-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Agencias de publicidad")::
("f28eef01-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Broadcasting - Radio")::
("f28eef01-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Radiodiffusion - Radio")::
("f28eef01-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Rundfunk - Rundfunk")::
("f28eef01-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Broadcasting - Ραδιόφωνο")::
("f28eef01-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Broadcasting - Radio")::
("f28eef01-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Radiodifusión - Radio")::
("f28f0d90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Broadcasting - TV")::
("f28f0d90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Diffusion - TV")::
("f28f0d90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Rundfunk - Fernsehen")::
("f28f0d90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Broadcasting - Τηλεόραση")::
("f28f0d90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Broadcasting - TV")::
("f28f0d90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Radiodifusión - TV")::
("f28ee43a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "CATV Systems")::
("f28ee43a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Systèmes CATV")::
("f28ee43a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "CATV-Systeme")::
("f28ee43a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "CATV Συστήματα")::
("f28ee43a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Sistemi CATV")::
("f28ee43a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Sistemas CATV")::
("f28edb21-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Marketing Services")::
("f28edb21-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services de marketing")::
("f28edb21-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Marketing-Dienstleistungen")::
("f28edb21-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "μάρκετινγκ υπηρεσιών")::
("f28edb21-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Marketing Services")::
("f28edb21-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de marketing")::
("f28f179e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Publishing - Books")::
("f28f179e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Edition - Livres")::
("f28f179e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Verlag - Bücher")::
("f28f179e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Εκδόσεις - Βιβλία")::
("f28f179e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Editoria - Libri")::
("f28f179e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Editorial - Libros")::
("f28f217a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Publishing - Newspapers")::
("f28f217a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Edition - Journaux")::
("f28f217a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Veröffentlichen - Zeitungen")::
("f28f217a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Εκδόσεις - Εφημερίδες")::
("f28f217a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Editoria - Giornali")::
("f28f217a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Publicaciones - Periódicos")::
("7d535222-f918-11e6-a9f3-901b0ebdf399", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Online Media")::
("7d535222-f918-11e6-a9f3-901b0ebdf399", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Médias en ligne")::
("7d535222-f918-11e6-a9f3-901b0ebdf399", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Onlinemedien")::
("7d535222-f918-11e6-a9f3-901b0ebdf399", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Online Media")::
("7d535222-f918-11e6-a9f3-901b0ebdf399", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "online media")::
("7d535222-f918-11e6-a9f3-901b0ebdf399", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Medios Online")::
("f28ef885-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Publishing - Periodicals")::
("f28ef885-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Edition - Périodiques")::
("f28ef885-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Verlagswesen - Zeitschriften")::
("f28ef885-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Εκδόσεις - Περιοδικά")::
("f28ef885-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Editoria - Periodici")::
("f28ef885-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Publicaciones - Periódicos")::
("c5729f79-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Medical Facilities")::
("c5729f79-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Établissements médicaux")::
("c5729f79-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Medizinische Einrichtung")::
("c5729f79-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ιατρικές εγκαταστάσεις")::
("c5729f79-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Strutture mediche")::
("c5729f79-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Instalaciones medicas")::
("f28ae71d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Hospitals")::
("f28ae71d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Hôpitaux")::
("f28ae71d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Krankenhäuser")::
("f28ae71d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "νοσοκομεία")::
("f28ae71d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "ospedali")::
("f28ae71d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Hospitales")::
("f28adddd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Long-Term Care Facilities")::
("f28adddd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Soins de longue durée")::
("f28adddd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Langzeitpflegeeinrichtungen")::
("f28adddd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Long-Term Εγκαταστάσεις Φροντίδας")::
("f28adddd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Long-Term Care Facilities")::
("f28adddd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Instalaciones de cuidado a largo plazo")::
("f28af298-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Medical Laboratories & Research")::
("f28af298-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Laboratoires médicaux et recherche")::
("f28af298-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Medizinische Laboratorien & Forschung")::
("f28af298-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ιατρικών Εργαστηρίων & Έρευνας")::
("f28af298-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Medical Laboratories & Research")::
("f28af298-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Laboratorios e Investigaciones Médicas")::
("d2900bdf-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Metals and Mining")::
("d2900bdf-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Métaux et mines")::
("d2900bdf-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Metalle und Bergbau")::
("d2900bdf-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μέταλλα και Μεταλλευτικών")::
("d2900bdf-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Metalli e Minerario")::
("d2900bdf-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Metales y minería")::
("f28f7ab1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Aluminum")::
("f28f7ab1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Aluminium")::
("f28f7ab1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Aluminium")::
("f28f7ab1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αλουμίνιο")::
("f28f7ab1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Alluminio")::
("f28f7ab1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Aluminio")::
("f28f2c38-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Copper")::
("f28f2c38-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Cuivre")::
("f28f2c38-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Kupfer")::
("f28f2c38-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Χαλκός")::
("f28f2c38-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Rame")::
("f28f2c38-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Cobre")::
("f28f6306-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Gold")::
("f28f6306-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Or")::
("f28f6306-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Gold")::
("f28f6306-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Χρυσός")::
("f28f6306-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Oro")::
("f28f6306-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Oro")::
("f28f35f4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Industrial Metals & Minerals")::
("f28f35f4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Métaux et minéraux industriels")::
("f28f35f4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Industriemetalle & Mineralien")::
("f28f35f4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βιομηχανικά Μέταλλα & Minerals")::
("f28f35f4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Metals & Industrial Minerals")::
("f28f35f4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Metales y minerales industriales")::
("f28f45f5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Nonmetallic Mineral Mining")::
("f28f45f5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Mines minérales non métalliques")::
("f28f45f5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Nichtmetallischer Mineralbergbau")::
("f28f45f5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μη μεταλλικά ορυκτά Μεταλλευτική")::
("f28f45f5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Nonmetallic Mineral Mining")::
("f28f45f5-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Minería Mineral No Metálica")::
("f28f6e9c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Silver")::
("f28f6e9c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "argent")::
("f28f6e9c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Silber")::
("f28f6e9c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ασήμι")::
("f28f6e9c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Argento")::
("f28f6e9c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Plata")::
("f28f585d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Steel & Iron")::
("f28f585d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Acier & Fer")::
("f28f585d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Stahl Eisen")::
("f28f585d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Χάλυβα και σιδήρου")::
("f28f585d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Steel & Iron")::
("f28f585d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Acero hierro")::
("d2901251-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Real Estate")::
("d2901251-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Immobilier")::
("d2901251-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Grundeigentum")::
("d2901251-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ακίνητα")::
("d2901251-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Immobiliare")::
("d2901251-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Bienes raíces")::
("f28fe08a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Property Management")::
("f28fe08a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Gestion de la propriété")::
("f28fe08a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Immobilienverwaltung")::
("f28fe08a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Διαχείριση Ακινήτων")::
("f28fe08a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Gestione della proprietà")::
("f28fe08a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Propiedad administrativa")::
("f28fb6f6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Real Estate Development")::
("f28fb6f6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Développement immobilier")::
("f28fb6f6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Immobilien-Entwicklung")::
("f28fb6f6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ανάπτυξη ακινήτων")::
("f28fb6f6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Sviluppo immobiliare")::
("f28fb6f6-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Desarrollo inmobiliario")::
("f28fc212-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "REIT - Diversified")::
("f28fc212-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "REIT - Diversifié")::
("f28fc212-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "REIT - Verschiedene")::
("f28fc212-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "REIT - Διαφοροποιημένη")::
("f28fc212-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "REIT - Diversified")::
("f28fc212-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "REIT - Diversificado")::
("f28fa0b1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "REIT - Hotel Motel")::
("f28fa0b1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "REIT - Hôtel Motel")::
("f28fa0b1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "REIT - Hotel Motel")::
("f28fa0b1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "REIT - Hotel Motel")::
("f28fa0b1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "REIT - Hotel Motel")::
("f28fa0b1-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "REIT - Hotel Motel")::
("f28f92bf-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "REIT - Industrial")::
("f28f92bf-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "REIT - Βιομηχανικά")::
("f28f92bf-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "REIT - Industrie")::
("f28f92bf-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "REIT - Βιομηχανικά")::
("f28f92bf-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "REIT - Industrial")::
("f28f92bf-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "REIT - Industrial")::
("f28fcbc3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "REIT - Office")::
("f28fcbc3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "REIT - Bureau")::
("f28fcbc3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "REIT - Büro")::
("f28fcbc3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "REIT - Γραφείο")::
("f28fcbc3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "REIT - Ufficio")::
("f28fcbc3-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "REIT - Oficina")::
("f28f8515-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "REIT - Residential")::
("f28f8515-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "REIT - Résidentiel")::
("f28f8515-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "REIT - Wohngebäude")::
("f28f8515-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "REIT - Κατοικίες")::
("f28f8515-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "REIT - Residential")::
("f28f8515-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "REIT - Residencial")::
("f28fd6d8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "REIT - Retail")::
("f28fd6d8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "REIT - Commerce de détail")::
("f28fd6d8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "REIT - Einzelhandel")::
("f28fd6d8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "REIT - Λιανική")::
("f28fd6d8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "REIT - vendita al dettaglio")::
("f28fd6d8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "REIT - Venta al por menor")::
("f28fad15-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "TREIT - Healthcare Facilities")::
("f28fad15-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "TREIT - Établissements de santé")::
("f28fad15-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "TREIT - Gesundheitseinrichtungen")::
("f28fad15-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Treit - Υγεία Εγκαταστάσεις")::
("f28fad15-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "TREIT - Strutture sanitarie")::
("f28fad15-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "TREIT - Servicios de Salud")::
("c572a773-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Services")::
("c572a773-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Prestations de service")::
("c572a773-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Dienstleistungen")::
("c572a773-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Υπηρεσίες")::
("c572a773-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Servizi")::
("c572a773-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios")::
("f28b973a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Air Delivery & Freight Services")::
("f28b973a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services de transport aérien")::
("f28b973a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Air Delivery & Frachtdienste")::
("f28b973a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Air Παράδοση & Freight Services")::
("f28b973a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Air Delivery & Freight Services")::
("f28b973a-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Air Delivery & Freight Services")::
("f28b38f9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Air Services, Other")::
("f28b38f9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services aériens, Autre")::
("f28b38f9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Luftverkehr, Andere")::
("f28b38f9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Air Services, Άλλα")::
("f28b38f9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Air Services, Other")::
("f28b38f9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios aéreos, Otro")::
("f28afb97-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Business Services")::
("f28afb97-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Les services aux entreprises")::
("f28afb97-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Geschäftsdienstleistungen")::
("f28afb97-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Επιχειρηματικές υπηρεσίες")::
("f28afb97-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Servizi per gli affari")::
("f28afb97-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de negocios")::
("f28b306b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Catalog & Mail Order Houses")::
("f28b306b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Catalogue et vente par correspondance")::
("f28b306b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Katalog & Versandhaus")::
("f28b306b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κατάλογος & Mail Παραγγελία Σπίτια")::
("f28b306b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Catalogo & Mail Order Case")::
("f28b306b-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Catálogo y Venta por Correo")::
("f28b5fb2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Consumer Services")::
("f28b5fb2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services aux consommateurs")::
("f28b5fb2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Verbraucherdienste")::
("f28b5fb2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Υπηρεσίες προς Καταναλωτές")::
("f28b5fb2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Servizio consumatori")::
("f28b5fb2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicio al consumidor")::
("f28b0674-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Education & Training Services")::
("f28b0674-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services de formation et de formation")::
("f28b0674-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Bildung & Training Dienstleistungen")::
("f28b0674-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Εκπαίδευση & Κατάρτιση Υπηρεσίες")::
("f28b0674-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Istruzione e formazione personale Servizi")::
("f28b0674-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de Educación y Capacitación")::
("f28b6c71-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Lodging")::
("f28b6c71-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Hébergement")::
("f28b6c71-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Unterkunft")::
("f28b6c71-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κατάλυμα")::
("f28b6c71-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Alloggio")::
("f28b6c71-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Alojamiento")::
("f28b7857-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Management Services")::
("f28b7857-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services de management")::
("f28b7857-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Management-Dienstleistungen")::
("f28b7857-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Υπηρεσίες διαχείρισης")::
("f28b7857-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Management Services")::
("f28b7857-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de administración")::
("f28b1a77-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Personal Services")::
("f28b1a77-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services personnels")::
("f28b1a77-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Persönliche Dienstleistungen")::
("f28b1a77-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "προσωπικές Υπηρεσίες")::
("f28b1a77-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "servizi alla persona")::
("f28b1a77-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios personales")::
("f28ba245-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Research Services")::
("f28ba245-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services de recherche")::
("f28ba245-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Forschungsdienstleistungen")::
("f28ba245-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "έρευνα Υπηρεσίες")::
("f28ba245-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Servizi di ricerca")::
("f28ba245-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de Investigación")::
("f28b4b24-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Security & Protection Services")::
("f28b4b24-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services de sécurité et de protection")::
("f28b4b24-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Sicherheit & Schutz Dienstleistungen")::
("f28b4b24-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ασφάλεια & Προστασία Υπηρεσίες")::
("f28b4b24-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Sicurezza e protezione Servizi")::
("f28b4b24-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de Seguridad y Protección")::
("f28b0f8d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Shipping")::
("f28b0f8d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "livraison")::
("f28b0f8d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Versand")::
("f28b0f8d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αποστολή")::
("f28b0f8d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "spedizione")::
("f28b0f8d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Envío")::
("f28b24dd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Sporting Activities")::
("f28b24dd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Activités sportives")::
("f28b24dd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Sportliche Aktivitäten")::
("f28b24dd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "αθλητικές Δραστηριότητες")::
("f28b24dd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Attività sportive")::
("f28b24dd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Actividades deportivas")::
("f28b820c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Staffing & Outsourcing Services")::
("f28b820c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services de dotation et d impartition")::
("f28b820c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Personal- & Outsourcing-Dienstleistungen")::
("f28b820c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Στελέχωση & Outsourcing Services")::
("f28b820c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Staffing & Outsourcing Services")::
("f28b820c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de personal y outsourcing")::
("f28b8dc8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Technical Services")::
("f28b8dc8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services techniques")::
("f28b8dc8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Technische Dienstleistungen")::
("f28b8dc8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "ΤΕΧΝΙΚΕΣ ΥΠΗΡΕΣΙΕΣ")::
("f28b8dc8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Servizi tecnici")::
("f28b8dc8-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios técnicos")::
("f28b42f0-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Waste Management")::
("f28b42f0-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "La gestion des déchets")::
("f28b42f0-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Abfallwirtschaft")::
("f28b42f0-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Διαχείριση των αποβλήτων")::
("f28b42f0-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Gestione dei rifiuti")::
("f28b42f0-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Gestión de residuos")::
("d2901756-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Software")::
("d2901756-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Logiciel")::
("d2901756-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Software")::
("d2901756-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Λογισμικό")::
("d2901756-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Software")::
("d2901756-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Software")::
("f29001f9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Application Software")::
("f29001f9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Logiciel d application")::
("f29001f9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Anwendungssoftware")::
("f29001f9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Λογισμικό εφαρμογής")::
("f29001f9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Software applicativo")::
("f29001f9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Software de la aplicacion")::
("f28ff731-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Business Software & Services")::
("f28ff731-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Logiciels et services commerciaux")::
("f28ff731-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Business Software & Dienstleistungen")::
("f28ff731-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Business Software & Services")::
("f28ff731-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Business Software & Servizi")::
("f28ff731-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Software y servicios empresariales")::
("f28fec79-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Multimedia & Graphics Software")::
("f28fec79-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Logiciel multimédia et graphique")::
("f28fec79-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Multimedia & Grafik Software")::
("f28fec79-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Πολυμέσα & Γραφικά Λογισμικό")::
("f28fec79-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Multimedia & Graphics")::
("f28fec79-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Software Multimedia y Gráficos")::
("f2900b91-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Technical & System Software")::
("f2900b91-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Logiciel technique et système")::
("f2900b91-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Technische & Systemsoftware")::
("f2900b91-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τεχνικά & Λογισμικό συστήματος")::
("f2900b91-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Software Tecnico e del sistema")::
("f2900b91-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Software Técnico y de Sistema")::
("c572ab64-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Technology")::
("c572ab64-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "La technologie")::
("c572ab64-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Technologie")::
("c572ab64-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τεχνολογία")::
("c572ab64-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Tecnologia")::
("c572ab64-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Tecnología")::
("f28bca94-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Scientific & Technical Instruments")::
("f28bca94-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Instruments scientifiques et techniques")::
("f28bca94-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Wissenschaftliche und technische Instrumente")::
("f28bca94-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Επιστημονική & Τεχνική Όργανα")::
("f28bca94-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Scientific & Technical Instruments")::
("f28bca94-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Instrumentos científicos y técnicos")::
("f28bb65e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Semiconductor Equipment & Materials")::
("f28bb65e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Équipement et matériaux semiconducteurs")::
("f28bb65e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Halbleiterausrüstung & Werkstoffe")::
("f28bb65e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ημιαγωγών Εξοπλισμός & Υλικά")::
("f28bb65e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Semiconductor Equipment & Materials")::
("f28bb65e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Equipos y Materiales Semiconductores")::
("f28bbf90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Semiconductor - Broad Line")::
("f28bbf90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Semiconducteur - Broad Line")::
("f28bbf90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Halbleiter - Broad Line")::
("f28bbf90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "ημιαγωγών - Ευρεία Γραμμή")::
("f28bbf90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Semiconductor - Broad Linea")::
("f28bbf90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Semiconductor - Línea amplia")::
("f28bd3fc-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Semiconductor - Integrated Circuits")::
("f28bd3fc-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Semi-conducteurs - Circuits intégrés")::
("f28bd3fc-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Halbleiter - Integrierte Schaltungen")::
("f28bd3fc-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ημιαγωγών - Ολοκληρωμένα Κυκλώματα")::
("f28bd3fc-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Circuiti integrati - Semiconductor")::
("f28bd3fc-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Semiconductor - Circuitos integrados")::
("f28bded2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Semiconductor - Memory Chips")::
("f28bded2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Semi-conducteur")::
("f28bded2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Halbleiter - Speicherchips")::
("f28bded2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τσιπ μνήμης - ημιαγωγών")::
("f28bded2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Chip memoria - Semiconductor")::
("f28bded2-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Semiconductor - chips de memoria")::
("f28bab90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Semiconductor - Specialized")::
("f28bab90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Semiconducteur - Spécialisé")::
("f28bab90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Halbleiter - spezialisiert")::
("f28bab90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ημιαγωγών - Εξειδίκευση")::
("f28bab90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Semiconductor - Specialized")::
("f28bab90-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Semiconductor - Especializado")::
("d2901c2c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Transportation")::
("d2901c2c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Transport")::
("d2901c2c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Transport")::
("d2901c2c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μεταφορά")::
("d2901c2c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Trasporti")::
("d2901c2c-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Transporte")::
("f2907e22-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Auto arts")::
("f2907e22-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Arts automobiles")::
("f2907e22-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Auto-Künste")::
("f2907e22-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "τέχνες Auto")::
("f2907e22-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "arti auto")::
("f2907e22-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Artes del automóvil")::
("f29068d4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Auto Dealerships")::
("f29068d4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Concessionnaires automobiles")::
("f29068d4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Autohäuser")::
("f29068d4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "αντιπροσωπείες αυτοκινήτων")::
("f29068d4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "concessionarie auto")::
("f29068d4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Auto concesionarios")::
("f290496e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Auto Manufactures - Major")::
("f290496e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Fabricants d automobiles - Major")::
("f290496e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Auto Hersteller - Major")::
("f290496e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Auto Κατασκευαστές - Major")::
("f290496e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Auto Manufacturers - Principali")::
("f290496e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Fabricantes de automóviles - mayor")::
("f2902b79-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Auto Parts Stores")::
("f2902b79-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Magasins de pièces automobiles")::
("f2902b79-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Auto Ersatzteile")::
("f2902b79-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καταστήματα Auto Parts")::
("f2902b79-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Negozi Auto Parts")::
("f2902b79-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Tiendas de Autopartes")::
("f290352e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Auto Parts Wholesale")::
("f290352e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Pièces auto")::
("f290352e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Autoteile Großhandel")::
("f290352e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ανταλλακτικά αυτοκινήτων Χονδρική")::
("f290352e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Commercio all ingrosso ricambi auto")::
("f290352e-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Venta al por mayor")::
("f2907303-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Major Airlines")::
("f2907303-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Principales compagnies aériennes")::
("f2907303-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Major Airlines")::
("f2907303-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "μεγάλες αεροπορικές εταιρείες")::
("f2907303-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Le principali compagnie aeree")::
("f2907303-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Principales aerolíneas")::
("f2902047-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Railroads")::
("f2902047-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Chemins de fer")::
("f2902047-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Eisenbahnen")::
("f2902047-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "πιέζει")::
("f2902047-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Ferrovia")::
("f2902047-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Ferrocarriles")::
("f2905441-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Regional Airlines")::
("f2905441-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Compagnies aériennes régionales")::
("f2905441-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Regional Airlines")::
("f2905441-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Regional Airlines")::
("f2905441-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Regional Airlines")::
("f2905441-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Líneas aéreas regionales")::
("f2905df4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Rental & Leasing Services")::
("f2905df4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services de location et de location")::
("f2905df4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Vermietung & Leasing Dienstleistungen")::
("f2905df4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ενοικιάσεις & Leasing Υπηρεσίες")::
("f2905df4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Rental & Leasing Services")::
("f2905df4-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de Alquiler y Arrendamiento")::
("f2901671-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Trucking")::
("f2901671-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Camionnage")::
("f2901671-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Trucking")::
("f2901671-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "φορτηγά")::
("f2901671-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Autotrasporti")::
("f2901671-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Camionaje")::
("f2903f5d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Trucks & Other Vehicles")::
("f2903f5d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Camions et autres véhicules")::
("f2903f5d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Trucks & andere Fahrzeuge")::
("f2903f5d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Φορτηγά & Άλλα οχήματα")::
("f2903f5d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Ed altri veicoli")::
("f2903f5d-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Camiones y otros vehículos")::
("c572b168-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Utilities")::
("c572b168-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Utilitaires")::
("c572b168-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Dienstprogramme")::
("c572b168-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Utilities")::
("c572b168-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Utilità")::
("c572b168-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Utilidades")::
("f28c1d93-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Diversifies Utilities")::
("f28c1d93-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Diversifie les services publics")::
("f28c1d93-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Diversifiziert Dienstprogramme")::
("f28c1d93-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "διαφοροποιεί Utilities")::
("f28c1d93-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "diversifica Utilità")::
("f28c1d93-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Diversifica las utilidades")::
("f28bf3b9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Electric Utilities")::
("f28bf3b9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services publics")::
("f28bf3b9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Stromversorgungsunternehmen")::
("f28bf3b9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "ηλεκτρικό Utilities")::
("f28bf3b9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Electric Utilities")::
("f28bf3b9-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Utilidades electricas")::
("f28c0956-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Foreign Utilities")::
("f28c0956-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Services publics étrangers")::
("f28c0956-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Ausländische Versorgungsunternehmen")::
("f28c0956-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Εξωτερικών Utilities")::
("f28c0956-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "Utilità esteri")::
("f28c0956-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Utilidades extranjeras")::
("f28be81c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Gas Utilities")::
("f28be81c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Gaz Utilitaires")::
("f28be81c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Gasversorger")::
("f28be81c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "αερίου Utilities")::
("f28be81c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "gas Utilities")::
("f28be81c-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Gas Utilidades")::
("f28c2d57-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Water Utilities")::
("f28c2d57-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "fr"), "Utilitaires d eau")::
("f28c2d57-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "de"), "Wasserversorgung")::
("f28c2d57-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Water Utilities")::
("f28c2d57-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "it"), "servizi idrici")::
("f28c2d57-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "es"), "Servicios de agua")::
("c572b6da-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Agriculture")::
("c572b6da-be12-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γεωργία")::
("f28c42cd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Farm Products")::
("f28c42cd-be1d-11e6-b932-4ccc6a4aa826", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αγροτική εκμετάλλευση Προϊόντα")';
    $Values = explode('::',$Values);
    foreach ($Values as $Row) {
      $Query = 'INSERT INTO '.$prfx.'sectors_names (sector, language, name) VALUES '.$Row;
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $JobDone = true;
    } else {
      $Query = 'DELETE FROM '.$prfx.'sectors_codes';
      $wpdb->query($Query);
      $Query = 'DELETE FROM '.$prfx.'sectors_names';
      $wpdb->query($Query);
    }
    return $JobDone;
  }
  private function InsertContinentValues() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'INSERT INTO '.$prfx.'continents_codes (uuid, code) VALUES ("'.$this->GetNewUUID().'", "AF")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'continents_codes (uuid, code) VALUES ("'.$this->GetNewUUID().'", "AN")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'continents_codes (uuid, code) VALUES ("'.$this->GetNewUUID().'", "AS")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'continents_codes (uuid, code) VALUES ("'.$this->GetNewUUID().'", "EU")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'continents_codes (uuid, code) VALUES ("'.$this->GetNewUUID().'", "NA")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'continents_codes (uuid, code) VALUES ("'.$this->GetNewUUID().'", "OC")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'continents_codes (uuid, code) VALUES ("'.$this->GetNewUUID().'", "SA")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'continents_names (continent, language, name) VALUES (
      (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"),
      (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
      "Africa"
    )';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'continents_names (continent, language, name) VALUES (
      (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AN"),
      (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
      "Antarctica"
    )';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'continents_names (continent, language, name) VALUES (
      (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"),
      (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
      "Asia"
    )';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'continents_names (continent, language, name) VALUES (
      (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"),
      (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
      "Europe"
    )';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'continents_names (continent, language, name) VALUES (
      (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "NA"),
      (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
      "North Αmerica"
    )';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'continents_names (continent, language, name) VALUES (
      (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"),
      (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
      "Oceania"
    )';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'continents_names (continent, language, name) VALUES (
      (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"),
      (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
      "South America"
    )';
    if (false === $wpdb->query($Query)) { $Error = true; }
    if (!$Error) {
      $JobDone = true;
    } else {
      $Query = 'DELETE FROM '.$prfx.'continents_codes';
      $wpdb->query($Query);
      $Query = 'DELETE FROM '.$prfx.'continents_names';
      $wpdb->query($Query);
    }
    return $JobDone;
  }
  private function InsertCountryValues() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Values = '
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SZ-SWZ", "sz", "268", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "IR-IRN", "ir", "98", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "CR-CRI", "cr", "506", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "LK-LKA", "lk", "94", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "AC-ASC", "ac", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "PA-PAN", "pa", "507", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "AO-AGO", "ao", "244", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "MZ-MOZ", "mz", "258", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "EG-EGY", "eg", "20", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "PL-POL", "pl", "48", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "BJ-BEN", "bj", "229", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "PD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "TG-TGO", "tg", "228", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "LI-LIE", "li", "423", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "SK-SVK", "sk", "421", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "KE-KEN", "ke", "254", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SH-SHN", "SHN", "290", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "HK-HKG", "hk", "852", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "SM-SMR", "sm", "378", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "BY-BLR", "by", "375", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "NU-NIU", "nu", "683", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "MX-MEX", "mx", "52", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "KR-KOR", "kr", "82", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "DJ-DJI", "dj", "253", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "PD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "CC-CCK", "cc", "618", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "TK-TKL", "tk", "690", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "GN-GIN", "gn", "224", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "BG-BGR", "bg", "673", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "TC-TCA", "tc", "1649", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "VC-VCT", "vc", "1784", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "AF-AFG", "af", "93", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "CW-CUW", "cw", "599", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "AE-ARE", "ae", "971", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "DZ-DZA", "dz", "213", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "SE-SWE", "se", "46", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "FI-FIN", "fi", "358", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "RO-ROM", "ro", "40", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "IQ-IRQ", "iq", "964", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SH-SHN", "SHN", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "VE-VEN", "ve", "58", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "NA"), "GE-GEO", "ge", "995", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "CV-CPV", "cv", "238", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "SV-SLV", "sv", "503", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "NA"), "BM-BMU", "bm", "1441", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "LT-LTU", "lt", "370", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SH-SHN", "SHN", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "ST-STP", "st", "239", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "SI-SVN", "si", "386", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "KW-KWT", "kw", "965", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "XK-UNK", "xk", "383", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "NF-NFK", "nf", "672", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SH-SHN", "SHN", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "PG-PNG", "pg", "675", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "ML-MLI", "ml", "223", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "MM-MMR", "mm", "95", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SH-SHN", "SHN", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "DM-DMA", "dm", "1737", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "FM-FSM", "fm", "691", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "CO-COL", "co", "57", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "AI-AIA", "AI", "1264", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "TV-TUV", "tv", "688", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "TO-TON", "to", "676", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "ZM-ZMB", "zm", "260", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "BF-BFA", "bf", "226", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "HU-HUN", "hu", "36", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "GB-GBR", "uk", "44", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SH-SHN", "SHN", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "PS-PSE", "ps", "970", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "CI-CIV", "ci", "225", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "GQ-GNQ", "gq", "240", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "VG-VGB", "vg", "1284", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "LU-LUX", "lu", "352", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "VN-VNM", "vn", "84", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "IE-IRL", "ie", "353", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "KY-CYM", "ky", "1345", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "SA-SAU", "sa", "966", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "IM-IMN", "im", "44", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "PF-PYF", "pf", "689", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "CH-CHE", "ch", "41", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "MT-MLT", "mt", "356", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "AM-ARM", "am", "374", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "DE-DEU", "de", "49", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "KG-KGZ", "kg", "996", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "BT-BTN", "bt", "975", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "UG-UGA", "ug", "256", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "DO-DOM", "do", "1809", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "BO-BOL", "bo", "591", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "PD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "MD-MDA", "md", "373", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "TT-TTO", "tt", "1868", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "NA-NAM", "na", "264", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "KM-COM", "km", "269", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "PT-PRT", "pt", "351", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "MO-MAC", "mo", "853", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "BI-BDI", "bi", "257", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "GW-GNB", "gw", "245", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "BS-BHS", "bs", "1242", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "AN-ANT", "an", "599", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "ZW-ZWE", "zw", "263", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "MP-MNP", "mp", "1670", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "NA"), "US-USA", "us", "1", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "IS-ISL", "is", "354", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "SB-SLB", "sb", "677", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "HR-HRV", "hr", "385", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "IL-ISR", "il", "972", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "CF-CAF", "cf", "236", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "SN-SEN", "sn", "221", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AN"), "AQ-ATA", "aq", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "GH-GHA", "gh", "233", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "SY-SYR", "sy", "963", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SH-SHN", "SHN", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "MH-MHL", "mh", "692", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "VI-VIR", "vi", "1340", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "FR-FRA", "fr", "33", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SS-SSD", "ss", "211", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "AS-ASM", "as", "1684", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "AW-ABW", "aw", "297", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "UA-UKR", "ua", "380", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "CG-COG", "cg", "242", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "PD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "NA"), "PM-SPM", "pm", "508", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AN"), "TF-ATF", "tf", "262", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "GY-GUY", "gy", "592", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "LA-LAO", "la", "856", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "NR-NRU", "nr", "674", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "TN-TUN", "tn", "216", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "BA-BIH", "ba", "387", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "ER-ERI", "er", "291", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SO-SOM", "so", "252", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "CU-CUB", "cu", "53", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "BH-BHR", "bh", "973", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "PD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "NC-NCL", "nc", "687", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "IT-ITA", "it", "39", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "PY-PRY", "py", "595", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "NO-NOR", "no", "47", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "NA"), "UM-UMI", "us", "1", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "MK-MKD", "mk", "359", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "MQ-MTQ", "mq", "596", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "GI-GIB", "gi", "350", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "TD-TCD", "td", "235", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "BL-BLM", "bl", "590", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "CK-COK", "ck", "682", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "WF-WLF", "wf", "681", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "AG-ATG", "ag", "1268", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "EE-EST", "ee", "372", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "LV-LVA", "lv", "371", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "GA-GAB", "ga", "241", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "TW-TWN", "tw", "886", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SD-SDN", "sd", "249", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "HT-HTI", "ht", "509", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "TA-SHN", "ta", "290", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "GF-GUF", "gf", "594", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "TR-TUR", "tr", "90", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "OM-OMN", "om", "968", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "MC-MCO", "mc", "377", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "JM-JAM", "jm", "1876", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "IN-IND", "in", "91", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "BW-BWA", "bw", "267", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "PD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "PR-PRI", "pr", "1787", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "RU-RUS", "ru", "7", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "NP-NPL", "np", "977", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "ZA-ZAF", "za", "27", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SH-SHN", "SHN", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "CY-CYP", "cy", "357", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "AU-AUS", "au", "61", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "AD-AND", "ad", "376", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "NZ-NZL", "nz", "64", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "BD-BGD", "bd", "880", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "GD-GRD", "gd", "1473", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "MR-MRT", "mr", "222", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "MG-MDG", "mg", "261", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "ME-MNE", "me", "382", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SH-SHN", "SHN", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "LB-LBN", "lb", "961", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SC-SYC", "sc", "248", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "UY-URY", "uy", "598", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "PE-PER", "pe", "51", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "ET-ETH", "et", "251", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "TJ-TJK", "tj", "992", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "GR-GRC", "gr", "30", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "GG-GGY", "gg", "44", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AN"), "HM-HMD", "hm", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "GM-GMB", "gm", "220", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "MF-MAF", "mf", "721", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "MN-MNG", "mn", "976", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "EH-ESH", "eh", "212", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "SR-SUR", "sr", "597", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "CL-CHL", "cl", "56", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "AL-ALB", "al", "355", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "JP-JPN", "jp", "81", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "ID-IDN", "id", "62", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "BE-BEL", "be", "32", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "NL-NLD", "nl", "31", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "AR-ARG", "ar", "54", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "NI-NIC", "ni", "505", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "RW-RWA", "rw", "250", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "BV-BVT", "bv", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "MW-MWI", "mw", "265", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "RS-SRB", "rs", "381", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "GP-GLP", "gp", "590", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "PK-PAK", "pk", "92", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "BB-BRB", "bb", "1246", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "KH-KHM", "kh", "855", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "LS-LSO", "ls", "266", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "IO-IOT", "io", "246", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "JE-JEY", "je", "44", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AN"), "GS-SGS", "gs", "500", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "NA"), "GL-GRL", "gl", "299", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "FK-FLK", "fk", "500", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "UZ-UZB", "uz", "998", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "CN-CHN", "cn", "86", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SL-SLE", "sl", "232", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "CZ-CZE", "cz", "420", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "TZ-TZA", "tz", "255", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "PH-PHL", "ph", "63", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "MS-MSR", "ms", "1664", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SH-SHN", "SHN", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "JO-JOR", "jo", "962", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "QA-QAT", "qa", "974", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "YE-YEM", "ye", "967", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "HN-HND", "hn", "504", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "AT-AUT", "at", "43", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "TL-TLS", "tl", "670", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "BZ-BLZ", "bz", "501", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "BR-BRA", "br", "55", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "NE-NER", "ne", "227", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "LR-LBR", "lr", "231", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "MY-MYS", "my", "60", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "KN-KNA", "kn", "1869", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "AX-ALA", "ax", "358", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "CM-CMR", "cm", "237", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "KI-KIR", "ki", "686", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "GU-GUM", "gu", "1671", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "PW-PLW", "pw", "680", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "MU-MUS", "mu", "230", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SH-SHN", "SHN", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "VU-VUT", "vu", "678", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "ES-ESP", "es", "34", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "FO-FRO", "fo", "298", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "IC-ICA", "ic", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "SJ-SJM", "sj", "47", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "CX-CXR", "ck", "618", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "MA-MAR", "ma", "212", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "NG-NGA", "ng", "234", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "RE-REU", "re", "262", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "PN-PCN", "pn", "870", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "SG-SGP", "sg", "65", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "EC-ECU", "ec", "593", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "AZ-AZE", "az", "994", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "DK-DNK", "dk", "45", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "KZ-KAZ", "kz", "7", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "WS-WSM", "ws", "685", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "SH-SHN", "SHN", "00", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "TH-THA", "th", "66", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "MV-MDV", "mv", "960", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "LY-LBY", "ly", "218", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "KP-PRK", "kp", "850", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "BN-BRN", "bn", "673", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AF"), "YT-MYT", "yt", "262", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "LC-LCA", "lc", "1758", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "GT-GTM", "gt", "502", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "AS"), "TM-TKM", "tm", "993", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "SA"), "BQ-BES", "bq", "599", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "OC"), "FJ-FJI", "fj", "679", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "NA"), "CA-CAN", "ca", "1", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"))::
("'.$this->GetNewUUID().'", (SELECT uuid FROM '.$prfx.'continents_codes WHERE code = "EU"), "VA-VAT", "va", "379", (SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "EUR"))';
    $Values = explode('::',$Values);
    foreach ($Values as $Row) {
      $Query = 'INSERT INTO '.$prfx.'countries_codes (uuid, continent, iso, domain, idp, currency) VALUES '.$Row;
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    $Values = '
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "US-USA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "usa")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "US-USA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "ΗΠΑ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AF-AFG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Afghanistan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AF-AFG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αφγανιστάν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AL-ALB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Albania")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AL-ALB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αλβανία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "DZ-DZA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Algeria")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "DZ-DZA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αλγερία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AS-ASM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "American Samoa")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AS-ASM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αμερικάνικη Σαμόα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AD-AND"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Andorra")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AD-AND"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ανδόρα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AO-AGO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Angola")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AO-AGO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αγκόλα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AI-AIA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Anguilla")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AI-AIA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ανγκουίλα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AQ-ATA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Antarctica")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AQ-ATA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ανταρκτική")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AG-ATG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Antigua and Barbuda")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AG-ATG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αντίγκουα και Μπαρμπούντα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AR-ARG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Argentina")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AR-ARG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αργεντίνη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AM-ARM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Armenia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AM-ARM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αρμενία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AW-ABW"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Aruba")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AW-ABW"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αρούμπα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AU-AUS"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Australia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AU-AUS"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αυστραλία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AT-AUT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Austria")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AT-AUT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αυστρία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AZ-AZE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Azerbaijan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AZ-AZE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αζερμπαϊτζάν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BS-BHS"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Bahamas")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BS-BHS"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μπαχάμες")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BH-BHR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Bahrain")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BH-BHR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μπαχρέιν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BD-BGD"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Bangladesh")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BD-BGD"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μπαγκλαντές")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BB-BRB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Barbados")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BB-BRB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μπαρμπάντος")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BY-BLR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Belarus")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BY-BLR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Λευκορωσία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BE-BEL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Belgium")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BE-BEL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βέλγιο")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BZ-BLZ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Belize")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BZ-BLZ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μπελίζ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BJ-BEN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Benin")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BJ-BEN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μπενίν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BM-BMU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Bermuda")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BM-BMU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βερμούδα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BT-BTN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Bhutan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BT-BTN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μπουτάν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BO-BOL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Bolivia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BO-BOL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βολιβία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BA-BIH"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Bosnia and Herzegovina")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BA-BIH"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βοσνία και Ερζεγοβίνη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BW-BWA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Botswana")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BW-BWA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μποτσουάνα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BV-BVT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Bouvet Island")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BV-BVT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νήσος Μπουβέ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BR-BRA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Brazil")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BR-BRA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βραζιλία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IO-IOT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "British Indian Ocean Territory")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IO-IOT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βρετανικό Έδαφος Ινδικού Ωκεανού")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BN-BRN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Brunei Darussalam")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BN-BRN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μπρουνάι Νταρουσαλάμ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BG-BGR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Bulgaria")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BG-BGR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βουλγαρία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BF-BFA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Burkina Faso")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BF-BFA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μπουρκίνα Φάσο")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BI-BDI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Burundi")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BI-BDI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μπουρούντι")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KH-KHM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Cambodia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KH-KHM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καμπότζη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CM-CMR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Cameroon")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CM-CMR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καμερούν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CA-CAN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Canada")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CA-CAN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καναδάς")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CV-CPV"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Cape Verde")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CV-CPV"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Πράσινο Ακρωτήριο")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KY-CYM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Cayman Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KY-CYM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νησιά Καϊμάν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CF-CAF"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Central African Republic")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CF-CAF"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Δημοκρατία Κεντρικής Αφρικής")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TD-TCD"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Chad")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TD-TCD"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τσαντ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CL-CHL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Chile")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CL-CHL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Χιλή")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CN-CHN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "China")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CN-CHN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κίνα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CX-CXR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Christmas Island")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CX-CXR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νησί Χριστουγέννων")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CC-CCK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Cocos Keeling Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CC-CCK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νησιά Κόκος Κίλινγκ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CO-COL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Colombia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CO-COL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κολομβία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KM-COM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Comoros")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KM-COM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κομόρες")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CG-COG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Congo")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CG-COG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κογκό")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CK-COK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Cook Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CK-COK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νησιά Κουκ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CR-CRI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Costa Rica")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CR-CRI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κόστα Ρίκα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CI-CIV"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Cote D Ivoire")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CI-CIV"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ακτή Ελεφαντοστού")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "HR-HRV"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Croatia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "HR-HRV"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κροατία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CU-CUB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Cuba")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CU-CUB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κούβα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CY-CYP"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Cyprus")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CY-CYP"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κύπρος")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CZ-CZE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Czech Republic")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CZ-CZE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Δημοκρατία της Τσεχίας")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "DK-DNK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Denmark")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "DK-DNK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Δανία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "DJ-DJI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Djibouti")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "DJ-DJI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τζιμπουτί")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "DM-DMA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Dominica")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "DM-DMA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ντομίνικα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "DO-DOM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Dominican Republic")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "DO-DOM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Δομινικανή Δημοκρατία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TL-TLS"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "East Timor")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TL-TLS"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ανατολικό Τιμόρ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "EC-ECU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Ecuador")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "EC-ECU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Εκουαδόρ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "EG-EGY"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Egypt")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "EG-EGY"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αίγυπτος")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SV-SLV"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "El Salvador")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SV-SLV"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ελ Σαλβαδόρ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GQ-GNQ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Equatorial Guinea")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GQ-GNQ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ισημερινή Γουινέα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ER-ERI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Eritrea")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ER-ERI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ερυθραία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "EE-EST"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Estonia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "EE-EST"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Εσθονία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ET-ETH"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Ethiopia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ET-ETH"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αιθιοπία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "FK-FLK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Falkland Islands Malvinas")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "FK-FLK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νησιά Φώκλαντ Μαλβίνες")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "FO-FRO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Faroe Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "FO-FRO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νήσοι Φερόες")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "FJ-FJI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Fiji")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "FJ-FJI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Φίτζι")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "FI-FIN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Finland")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "FI-FIN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Φινλανδία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "FR-FRA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "France Metropolitan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "FR-FRA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γαλλία Μητροπολιτική")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GF-GUF"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "French Guiana")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GF-GUF"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γαλλική Γουιάνα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PF-PYF"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "French Polynesia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PF-PYF"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γαλλική Πολυνησία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TF-ATF"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "French Southern Territories")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TF-ATF"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γαλλικά Νότια Εδάφη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GA-GAB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Gabon")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GA-GAB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γκαμπόν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GM-GMB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Gambia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GM-GMB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γκάμπια")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GE-GEO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Georgia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GE-GEO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γεωργία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "DE-DEU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Germany")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "DE-DEU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γερμανία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GH-GHA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Ghana")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GH-GHA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γκάνα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GI-GIB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Gibraltar")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GI-GIB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γιβραλτάρ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GL-GRL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Greenland")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GL-GRL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γροιλανδία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GD-GRD"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Grenada")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GD-GRD"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γρενάδα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GP-GLP"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Guadeloupe")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GP-GLP"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γουαδελούπη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GU-GUM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Guam")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GU-GUM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γκουάμ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GT-GTM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Guatemala")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GT-GTM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γουατεμάλα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GN-GIN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Guinea")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GN-GIN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γκινέα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GW-GNB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Guinea Bissau")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GW-GNB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γουινέα Μπισάου")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GY-GUY"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Guyana")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GY-GUY"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γουιάνα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "HT-HTI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Haiti")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "HT-HTI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αΐτη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "HM-HMD"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Heard Island and McDonald Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "HM-HMD"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νήσοι Χερντ και ΜακΝτόναλντ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "HN-HND"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Honduras")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "HN-HND"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ονδούρα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "HK-HKG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Hong Kong")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "HK-HKG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Χονγκ Κονγκ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "HU-HUN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Hungary")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "HU-HUN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ουγγαρία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IS-ISL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Iceland")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IS-ISL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ισλανδία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IN-IND"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "India")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IN-IND"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ινδία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ID-IDN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Indonesia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ID-IDN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ινδονησία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IR-IRN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Iran Islamic Republic of")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IR-IRN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ιράν Ισλαμική Δημοκρατία του")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IQ-IRQ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Iraq")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IQ-IRQ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ιράκ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IE-IRL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Ireland")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IE-IRL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ιρλανδία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IL-ISR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Israel")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IL-ISR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ισραήλ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IT-ITA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Italy")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IT-ITA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ιταλία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "JM-JAM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Jamaica")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "JM-JAM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ιαμαϊκή Τζαμάϊκα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "JP-JPN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Japan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "JP-JPN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ιαπωνία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "JO-JOR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Jordan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "JO-JOR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ιορδανία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KZ-KAZ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Kazakhstan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KZ-KAZ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Καζακστάν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KE-KEN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Kenya")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KE-KEN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κενύα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KI-KIR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Kiribati")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KI-KIR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κιριμπάτι")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KP-PRK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "North Korea")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KP-PRK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βόρεια Κορέα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KR-KOR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "South Korea")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KR-KOR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νότια Κορέα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KW-KWT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Kuwait")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KW-KWT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κουβέιτ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KG-KGZ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Kyrgyzstan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KG-KGZ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κιργιστάν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LA-LAO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Lao People s Democratic Republic")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LA-LAO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Λαϊκή Δημοκρατία του Λάος")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LV-LVA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Latvia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LV-LVA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Λατβία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LB-LBN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Lebanon")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LB-LBN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Λίβανος")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LS-LSO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Lesotho")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LS-LSO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Λεσόθο")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LR-LBR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Liberia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LR-LBR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Λιβερία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LY-LBY"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Libyan Arab Jamahiriya")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LY-LBY"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Λιβυκή Αραβική Τζαμαχιρία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LI-LIE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Liechtenstein")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LI-LIE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Λιχτενστάιν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LT-LTU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Lithuania")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LT-LTU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Λιθουανία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LU-LUX"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Luxembourg")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LU-LUX"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Λουξεμβούργο")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MO-MAC"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Macau")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MO-MAC"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μακάου")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MK-MKD"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "FYROM")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MK-MKD"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σκόπια FYROM")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MG-MDG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Madagascar")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MG-MDG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μαδαγασκάρη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MW-MWI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Malawi")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MW-MWI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μαλάουι")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MY-MYS"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Malaysia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MY-MYS"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μαλαισία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MV-MDV"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Maldives")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MV-MDV"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μαλδίβες")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ML-MLI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Mali")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ML-MLI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μάλι")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MT-MLT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Malta")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MT-MLT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μάλτα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MH-MHL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Marshall Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MH-MHL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νησια Μαρσαλ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MQ-MTQ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Martinique")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MQ-MTQ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μαρτινίκα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MR-MRT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Mauritania")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MR-MRT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μαυριτανία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MU-MUS"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Mauritius")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MU-MUS"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μαυρίκιος")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "YT-MYT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Mayotte")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "YT-MYT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μαγιότ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MX-MEX"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Mexico")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MX-MEX"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μεξικό")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "FM-FSM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Micronesia Federated States of")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "FM-FSM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ομόσπονδες Πολιτείες της Μικρονησίας")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MD-MDA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Moldova Republic of")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MD-MDA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Δημοκρατία της Μολδαβίας")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MC-MCO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Monaco")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MC-MCO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μονακό")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MN-MNG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Mongolia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MN-MNG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μογγολία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MS-MSR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Montserrat")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MS-MSR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μοντσεράτ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MA-MAR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Morocco")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MA-MAR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μαρόκο")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MZ-MOZ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Mozambique")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MZ-MOZ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μοζαμβίκη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MM-MMR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Myanmar")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MM-MMR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μιανμάρ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NA-NAM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Namibia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NA-NAM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ναμίμπια")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NR-NRU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Nauru")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NR-NRU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ναουρού")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NP-NPL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Nepal")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NP-NPL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νεπάλ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NL-NLD"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Netherlands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NL-NLD"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ολλανδία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AN-ANT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Netherlands Antilles")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AN-ANT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ολλανδικές Αντίλλες")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NC-NCL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "New Caledonia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NC-NCL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νέα Καληδονία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NZ-NZL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "New Zealand")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NZ-NZL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νέα Ζηλανδία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NI-NIC"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Nicaragua")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NI-NIC"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νικαράγουα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NE-NER"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Niger")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NE-NER"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νίγηρας")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NG-NGA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Nigeria")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NG-NGA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νιγηρία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NU-NIU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Niue")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NU-NIU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νιούε")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NF-NFK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Norfolk Island")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NF-NFK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νησί Νόρφολκ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MP-MNP"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Northern Mariana Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MP-MNP"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νησιά Βόρειες Μαριάννες")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NO-NOR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Norway")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "NO-NOR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νορβηγία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "OM-OMN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Oman")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "OM-OMN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ομάν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PK-PAK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Pakistan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PK-PAK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Πακιστάν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PW-PLW"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Palau")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PW-PLW"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Παλάου")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PA-PAN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Panama")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PA-PAN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Παναμάς")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PG-PNG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Papua New Guinea")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PG-PNG"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Παπούα Νέα Γουινέα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PY-PRY"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Paraguay")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PY-PRY"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Παραγουάη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PE-PER"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Peru")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PE-PER"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Περού")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PH-PHL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Philippines")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PH-PHL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Φιλιππίνες")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PN-PCN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Pitcairn")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PN-PCN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Πίτκερν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PL-POL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Poland")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PL-POL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Πολωνία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PT-PRT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Portugal")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PT-PRT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Πορτογαλία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PR-PRI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Puerto Rico")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PR-PRI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Πουέρτο Ρίκο")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "QA-QAT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Qatar")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "QA-QAT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κατάρ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "RE-REU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Reunion")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "RE-REU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ρεγιουνιόν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "RO-ROM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Romania")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "RO-ROM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ρουμανία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "RU-RUS"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Russian Federation")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "RU-RUS"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ρωσική Ομοσπονδία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "RW-RWA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Rwanda")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "RW-RWA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ρουάντα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KN-KNA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Saint Kitts and Nevis")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "KN-KNA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Άγιος Χριστόφορος και Νέβις")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LC-LCA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Saint Lucia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LC-LCA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αγία Λουκία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "VC-VCT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Saint Vincent and the Grenadines")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "VC-VCT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Άγιος Βικέντιος και Γρεναδίνες")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "WS-WSM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Samoa")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "WS-WSM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σαμόα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SM-SMR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "San Marino")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SM-SMR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σαν Μαρίνο")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ST-STP"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Sao Tome and Principe")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ST-STP"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σάο Τομέ και Πρίνσιπε")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SA-SAU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Saudi Arabia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SA-SAU"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σαουδική Αραβία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SN-SEN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Senegal")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SN-SEN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σενεγάλη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SC-SYC"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Seychelles")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SC-SYC"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σεϋχέλλες")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SL-SLE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Sierra Leone")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SL-SLE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σιέρρα Λεόνε")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SG-SGP"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Singapore")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SG-SGP"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σιγκαπούρη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SK-SVK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Slovak Republic")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SK-SVK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Δημοκρατία της Σλοβακίας")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SI-SVN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Slovenia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SI-SVN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σλοβενία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SB-SLB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Solomon Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SB-SLB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νησιά Σολομώντα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SO-SOM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Somalia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SO-SOM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σομαλία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ZA-ZAF"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "South Africa")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ZA-ZAF"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νότια Αφρική")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GS-SGS"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "South Sandwich Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GS-SGS"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νότια Γεωργία και Νότια Νησιά Σάντουιτς")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ES-ESP"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Spain")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ES-ESP"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ισπανία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LK-LKA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Sri Lanka")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "LK-LKA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σρι Λάνκα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SH-SHN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "St Helena")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SH-SHN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Άγια Ελένη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PM-SPM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "St Pierre and Miquelon")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PM-SPM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σαιν Πιερ και Μικελόν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SD-SDN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Sudan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SD-SDN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σουδάν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SR-SUR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Suriname")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SR-SUR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σουρινάμ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SJ-SJM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Svalbard and Jan Mayen Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SJ-SJM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σβάλμπαρντ και Γιαν Μαγιέν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SZ-SWZ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Swaziland")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SZ-SWZ"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σουαζιλάνδη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SE-SWE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Sweden")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SE-SWE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σουηδία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CH-CHE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Switzerland")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CH-CHE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ελβετία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SY-SYR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Syrian Arab Republic")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SY-SYR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αραβική Δημοκρατία της Συρίας")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TW-TWN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Taiwan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TW-TWN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ταϊβάν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TJ-TJK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Tajikistan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TJ-TJK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τατζικιστάν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TZ-TZA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Tanzania United Republic of")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TZ-TZA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ενωμένη Δημοκρατία της Τανζανίας")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TH-THA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Thailand")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TH-THA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σιάμ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TG-TGO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Togo")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TG-TGO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τόγκο")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TK-TKL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Tokelau")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TK-TKL"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τοκελάου")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TO-TON"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Tonga")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TO-TON"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τόνγκα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TT-TTO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Trinidad and Tobago")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TT-TTO"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τρινιντάντ και Τομπάγκο")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TN-TUN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Tunisia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TN-TUN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τυνησία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TR-TUR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Turkey")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TR-TUR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τουρκία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TM-TKM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Turkmenistan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TM-TKM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τουρκμενιστάν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TC-TCA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Turks and Caicos Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TC-TCA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νήσοι Τερκς και Κάικος")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TV-TUV"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Tuvalu")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TV-TUV"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τουβαλού")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "UG-UGA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Uganda")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "UG-UGA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ουγκάντα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "UA-UKR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Ukraine")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "UA-UKR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ουκρανία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AE-ARE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "United Arab Emirates")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AE-ARE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ηνωμένα Αραβικά Εμιράτα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GB-GBR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "United Kingdom")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GB-GBR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ηνωμένο Βασίλειο")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "UM-UMI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "United States Minor Outlying Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "UM-UMI"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Απομακρυσμένες Νησίδες των Ηνωμένων Πολιτειών")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "UY-URY"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Uruguay")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "UY-URY"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ουρουγουάη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "UZ-UZB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Uzbekistan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "UZ-UZB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ουζμπεκιστάν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "VU-VUT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Vanuatu")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "VU-VUT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βανουάτου")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "VA-VAT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Vatican City State")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "VA-VAT"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Πόλη του Βατικανού")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "VE-VEN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Venezuela")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "VE-VEN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βενεζουέλα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "VN-VNM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Viet Nam")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "VN-VNM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Βιετνάμ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "VG-VGB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Virgin Islands British")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "VG-VGB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Παρθένα νησιά Βρετανικά")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "VI-VIR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Virgin Islands US")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "VI-VIR"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Παρθένοι Νήσοι ΗΠΑ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "WF-WLF"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Wallis and Futuna Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "WF-WLF"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ουόλις και Φουτούνα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "EH-ESH"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Western Sahara")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "EH-ESH"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Δυτική Σαχάρα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "YE-YEM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Yemen")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "YE-YEM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γέμενη")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ZM-ZMB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Zambia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ZM-ZMB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ζάμπια")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ZW-ZWE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Zimbabwe")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ZW-ZWE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ζιμπάμπουε")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ME-MNE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Montenegro")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "ME-MNE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μαυροβούνιο")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "RS-SRB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Serbia")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "RS-SRB"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Σερβία")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AX-ALA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Aaland Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AX-ALA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Aaland νησιά")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BQ-BES"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Bonaire Sint Eustatius and Saba")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BQ-BES"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Μπονέρ Άγιος Ευστάθιος και Σάμπα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CW-CUW"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Curacao")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "CW-CUW"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κουράσω")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PS-PSE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Palestinian Territory Occupied")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "PS-PSE"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Παλαιστινιακά Εδάφη Κατεχόμενα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SS-SSD"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "South Sudan")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "SS-SSD"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νότιο Σουδάν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BL-BLM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "St Barthelemy")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "BL-BLM"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Αγίου Βαρθολομαίος")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MF-MAF"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "St Martin French part")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "MF-MAF"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Άγιος Μαρτίνος γαλλικό τμήμα")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IC-ICA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Canary Islands")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IC-ICA"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Κανάριοι Νήσοι")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AC-ASC"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Ascension Island British")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "AC-ASC"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νήσος της Αναλήψεως Βρετανικά")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "XK-UNK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Kosovo Republic of")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "XK-UNK"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Δημοκρατία του Κοσσυφοπέδιου")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IM-IMN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Isle of Man")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "IM-IMN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Νήσος του Μαν")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TA-SHN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Tristan da Cunha")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "TA-SHN"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τριστάν ντα Κούνια")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GG-GGY"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Guernsey")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GG-GGY"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Γκέρνσεϊ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "JE-JEY"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Jersey")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "JE-JEY"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Τζέρσεϊ")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GR-GRC"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), "Greece")::
((SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "GR-GRC"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "el"), "Ελλάδα")';
    $Values = explode('::',$Values);
    foreach ($Values as $Row) {
      $Query = 'INSERT INTO '.$prfx.'countries_names (country, language, name) VALUES '.$Row;
      $wpdb->query($Query);
    }
    if (!$Error) {
      $JobDone = true;
    } else {
      $Query = 'DELETE FROM '.$prfx.'countries_codes';
      $wpdb->query($Query);
      $Query = 'DELETE FROM '.$prfx.'countries_names';
      $wpdb->query($Query);
    }
    return $JobDone;
  }
  private function InsertUserPermissionsValues() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = $wpdb->prepare(
      'INSERT INTO '.$prfx.'store_users_permissions (userID, store, products, customers, orders, marketing, reports, design, settings, system)
      VALUES (%s,%s,"1","1","1","1","1","1","1","1")',
      get_current_user_id(),
      $this->StoreUUID
    );
    if (false !== $wpdb->query($Query)) { $JobDone = true; }
    return $JobDone;
  }
  private function InsertMainStoreTableData() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'INSERT INTO '.$prfx.'store (uuid, themelang, thmactivelang, adminlang, name, country, tzone, city, street, number, phone, fax, email, gglstats, gglmap, logo, currency, update_currency, barcode, orderserial, tblrows, notiftime, metrics, mailsettings) VALUES ("'.$this->StoreUUID.'", (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),(SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"), (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),"New E-Shop",(SELECT uuid FROM '.$prfx.'countries_codes WHERE iso = "US-USA"), "UTC", "--", "--", "--", "--", "--", "--","NOANALYTICS","NOMAP","'.MENSIO_SHORTPATH.'/admin/icons/default/empty.png",(SELECT uuid FROM '.$prfx.'currencies_codes WHERE code = "USD"),"1","NotSet", "Mensio-Order", "10", "1500","Color:TXT;Height:TXT;Length:TXT;Size:TXT;Volume:TXT;Weight:TXT;Width:TXT","2")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    if (!$Error) {
      $JobDone = true;
    } else {
      $Query = 'DELETE FROM '.$prfx.'store';
      $wpdb->query($Query);
    }
    return $JobDone;
  }
  private function InsertOrdersPaymentTypeValues() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'INSERT INTO '.$prfx.'orders_payment_type (uuid, name) VALUES ("'.$this->GetNewUUID().'", "On Delivery")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'orders_payment_type (uuid, name) VALUES ("'.$this->GetNewUUID().'", "Bank Deposit")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'orders_payment_type (uuid, name) VALUES ("'.$this->GetNewUUID().'", "PayPal")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    if (MENSIO_FLAVOR !== 'FREE') {
      $Query = 'INSERT INTO '.$prfx.'orders_payment_type (uuid, name) VALUES ("'.$this->GetNewUUID().'", "Eurobank")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'orders_payment_type (uuid, name) VALUES ("'.$this->GetNewUUID().'", "AlphaBank")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'orders_payment_type (uuid, name) VALUES ("'.$this->GetNewUUID().'", "VivaWallet")';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $JobDone = true;
    } else {
      $Query = 'DELETE FROM '.$prfx.'orders_payment_type';
      $wpdb->query($Query);
    }
    return $JobDone;
  }
  private function InsertOrdersPaymentValues() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'orders_payment_type';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $Query = $wpdb->prepare(
          'INSERT INTO '.$prfx.'store_payment (uuid, store, type, success_page, failed_page, active) VALUES (%s,%s,%s,%s,%s,"0")',
          $this->GetNewUUID(),
          $this->StoreUUID,
          $Row->uuid,
          '--',
          '--'
        );
        if (false === $wpdb->query($Query)) { $Error = true; }
      }
    }
    if (!$Error) { $JobDone = true; }
    return $JobDone;
  }
  private function InsertPaymentDescriptions() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'store_payment';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $Query = 'INSERT INTO '.$prfx.'store_payment_descriptions
          (payment, language, description, instructions)
          VALUES ( "'.$Row->uuid.'",
            (SELECT uuid FROM '.$prfx.'languages_codes WHERE code = "en"),
            "description",
            "instructions")';
        if (false === $wpdb->query($Query)) { $Error = true; }
      }
    }
    if (!$Error) {
      $JobDone = true;
    } else {
      $Query = 'DELETE FROM '.$prfx.'store_payment_gateways';
      $wpdb->query($Query);
    }
    return $JobDone;
  }
  private function InsertGateWayPayPalValues() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $PayPal = '';
    $Query = 'SELECT '.$prfx.'store_payment.uuid FROM '.$prfx.'store_payment, '.$prfx.'orders_payment_type
      WHERE '.$prfx.'store_payment.type = '.$prfx.'orders_payment_type.uuid
      AND '.$prfx.'orders_payment_type.name = "PayPal"';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $PayPal = $Row->uuid;
      }
    }
    if ($PayPal === '') {
      $Error = true;
    } else {
      $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$PayPal.'", "00 Active Sandbox Mode", "Y")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$PayPal.'", "Icon", "'.MENSIO_SHORTPATH.'/admin/icons/default/empty.png")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$PayPal.'", "01 Client ID Live", "--")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$PayPal.'", "02 Client ID Sandbox", "--")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$PayPal.'", "03 Client Secret", "--")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$PayPal.'", "04 Receiver E-Mail", "--")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$PayPal.'", "05 Return Success Page", "--")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$PayPal.'", "06 Return Failed Page", "--")';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $JobDone = true;
    } else {
      $Query = 'DELETE FROM '.$prfx.'store_payment_gateways';
      $wpdb->query($Query);
    }
    return $JobDone;
  }
  private function InsertDefaultPagesTemplates() {
    $JobDone = true;
    $CrUser = wp_get_current_user();
    $my_post = array(
      'post_title'    => 'User Page',
      'post_content'  => '<div class="mns-html-content"><div class="mns-block mns-user">[mns_user]</div></div>',
      'post_status'   => 'publish',
      'post_author'   => $CrUser->ID,
      'post_name'     => 'User Page',
      'post_type'     => 'mensio_page'
    );
    $postId = wp_insert_post($my_post);
    add_post_meta($postId, 'mensio_page_function', 'user_page');
    $my_post = array(
      'post_title'    => 'Product Page',
      'post_content'  => '<div class="mns-html-content"><div class="mns-block mns-product">[mns_product]</div></div>',
      'post_status'   => 'publish',
      'post_author'   => $CrUser->ID,
      'post_name'     => 'Product Page',
      'post_type'     => 'mensio_page'
    );
    $postId = wp_insert_post($my_post);
    add_post_meta($postId, 'mensio_page_function', 'product_page');
    $my_post = array(
      'post_title'    => 'Categories Page',
      'post_content'  => '<div class="mns-html-content"><div class="mns-block mns-categories">[mns_categories]</div></div>',
      'post_status'   => 'publish',
      'post_author'   => $CrUser->ID,
      'post_name'     => 'Categories Page',
      'post_type'     => 'mensio_page'
    );
    $postId = wp_insert_post($my_post);
    add_post_meta($postId, 'mensio_page_function', 'categories_page');
    $my_post = array(
      'post_title'    => 'Products Page',
      'post_content'  => '<div class="mns-html-content"><div class="mns-block mns-category">[mns_category]</div></div>',
      'post_status'   => 'publish',
      'post_author'   => $CrUser->ID,
      'post_name'     => 'Products Page',
      'post_type'     => 'mensio_page'
    );
    $postId = wp_insert_post($my_post);
    add_post_meta($postId, 'mensio_page_function', 'category_page');
    $my_post = array(
      'post_title'    => 'Terms Of Service',
      'post_content'  => '<div class="mns-html-content"><div class="mns-block mns-tos">[mns_tos]</div></div>',
      'post_status'   => 'publish',
      'post_author'   => $CrUser->ID,
      'post_name'     => 'Terms Of Service',
      'post_type'     => 'mensio_page'
    );
    $postId = wp_insert_post($my_post);
    add_post_meta($postId, 'mensio_page_function', 'tos_page');
    $my_post = array(
      'post_title'    => 'Cart Page',
      'post_content'  => '<div class="mns-html-content"><div class="mns-block mns-cart">[mns_cart]</div></div>',
      'post_status'   => 'publish',
      'post_author'   => $CrUser->ID,
      'post_name'     => 'Cart Page',
      'post_type'     => 'mensio_page'
    );
    $postId = wp_insert_post($my_post);
    add_post_meta($postId, 'mensio_page_function', 'cart_page');
    $my_post = array(
      'post_title'    => 'Checkout',
      'post_content'  => '<div class="mns-html-content"><div class="mns-block mns-checkout">[mns_checkout]</div></div>',
      'post_status'   => 'publish',
      'post_author'   => $CrUser->ID,
      'post_name'     => 'Checkout',
      'post_type'     => 'mensio_page'
    );
    $postId = wp_insert_post($my_post);
    add_post_meta($postId, 'mensio_page_function', 'checkout_page');
    $my_post = array(
      'post_title'    => 'Login',
      'post_content'  => '<div class="mns-html-content"><div class="mns-block mns-login">[mns_login]</div></div>',
      'post_status'   => 'publish',
      'post_author'   => $CrUser->ID,
      'post_name'     => 'Login',
      'post_type'     => 'mensio_page'
    );
    $postId = wp_insert_post($my_post);
    add_post_meta($postId, 'mensio_page_function', 'login_page');
    $my_post = array(
      'post_title'    => 'Signup',
      'post_content'  => '<div class="mns-html-content"><div class="mns-block mns-signup">[mns_signup]</div></div>',
      'post_status'   => 'publish',
      'post_author'   => $CrUser->ID,
      'post_name'     => 'Signup',
      'post_type'     => 'mensio_page'
    );
    $postId = wp_insert_post($my_post);
    add_post_meta($postId, 'mensio_page_function', 'signup_page');
    $my_post = array(
      'post_title'    => 'Contact',
      'post_content'  => '<div class="mns-html-content"><div class="mns-block mns-contact">[mns_contact]</div></div>',
      'post_status'   => 'publish',
      'post_author'   => $CrUser->ID,
      'post_name'     => 'Contact',
      'post_type'     => 'mensio_page'
    );
    $postId = wp_insert_post($my_post);
    add_post_meta($postId, 'mensio_page_function', 'contact_page');
    $my_post = array(
      'post_title'    => 'Search',
      'post_content'  => '<div class="mns-html-content"><div class="mns-block mns-search">[mns_search]</div></div>',
      'post_status'   => 'publish',
      'post_author'   => $CrUser->ID,
      'post_name'     => 'Search',
      'post_type'     => 'mensio_page'
    );
    $postId = wp_insert_post($my_post);
    add_post_meta($postId, 'mensio_page_function', 'search_results_page');
    if (MENSIO_FLAVOR === 'STD') {
      $my_post = array(
        'post_title'    => 'Favorite Products',
        'post_content'  => '<div class="mns-html-content"><div class="mns-block mns-favorites">[mns_favorites]</div></div>',
        'post_status'   => 'publish',
        'post_author'   => $CrUser->ID,
        'post_name'     => 'Favorite Products',
        'post_type'     => 'mensio_page'
      );
      $postId = wp_insert_post($my_post);
      add_post_meta($postId, 'mensio_page_function', 'product_favorites_page');
      $my_post = array(
        'post_title'    => 'Comparison',
        'post_content'  => '<div class="mns-html-content"><div class="mns-block mns-product_comparison">[mns_product_comparison]</div></div>',
        'post_status'   => 'publish',
        'post_author'   => $CrUser->ID,
        'post_name'     => 'Comparison',
        'post_type'     => 'mensio_page'
      );
      $postId = wp_insert_post($my_post);
      add_post_meta($postId, 'mensio_page_function', 'product_comparison_page');
    }
    return $JobDone;
  }
  private function InsertGateWayBanksValues($Name) {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Bank = '';
    $Query = 'SELECT '.$prfx.'store_payment.uuid FROM '.$prfx.'store_payment, '.$prfx.'orders_payment_type
      WHERE '.$prfx.'store_payment.type = '.$prfx.'orders_payment_type.uuid
      AND '.$prfx.'orders_payment_type.name = "'.$Name.'"';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $Bank = $Row->uuid;
      }
    }
    if ($Bank === '') {
      $Error = true;
    } else {
      switch ($Name){
        case 'Eurobank';
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "01 Action url", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "02 Merchant ID", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "03 Digest Key", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "04 Max Pay Retries", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "05 Reject 3ds U", "Y")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "06 Return Success Page", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "07 Return Failed Page", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "08 CSS url", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "09a Installment Offset", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "09b Installment Period", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "09c Installment frequency", "28")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "09d Recurring End Date", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "Icon", "'.MENSIO_SHORTPATH.'/admin/icons/default/empty.png")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          break;
        case 'AlphaBank';
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "01 Action url", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "02 Merchant ID", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "03 Digest Key", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "04 Return Success Page", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "05 Return Failed Page", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "06 CSS url", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "07a Installment Offset", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "07b Installment Period", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "07c Installment frequency", "28")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "07d Recurring End Date", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "Icon", "'.MENSIO_SHORTPATH.'/admin/icons/default/empty.png")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          break;
        case 'VivaWallet':
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "01 Public Key", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "02 Merchant ID", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "03 API Key", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "04 Address URL", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "05 Native Checkout Source Code", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "06 Return Success Page", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "07 Return Failed Page", "--")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          $Query = 'INSERT INTO '.$prfx.'store_payment_gateways (payment, parameter, value) VALUES ("'.$Bank.'", "Icon", "'.MENSIO_SHORTPATH.'/admin/icons/default/empty.png")';
          if (false === $wpdb->query($Query)) { $Error = true; }
          break;
      }
    }
    if (!$Error) {
      $JobDone = true;
    } else {
      $Query = 'DELETE FROM '.$prfx.'store_payment_gateways';
      $wpdb->query($Query);
    }
    return $JobDone;
  }
  final public function StartMnsInstallCustomer() {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    if (!$this->InstallCustomerTables()) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Settings Tables could NOT be Installed !!!!<br>';
    }
    return $RtrnData;
  }
  private function InstallCustomerTables() {
    $Error = false;
    $JobDone = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'addresses_type';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'addresses_type` (
      `uuid` char(36) NOT NULL,
      `name` varchar(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'addresses';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'addresses` (
        `uuid` char(36) NOT NULL,
        `customer` char(36) NOT NULL,
        `credential` char(36) NOT NULL,
        `type` char(36) NOT NULL,
        `fullname` varchar(255) NOT NULL,
        `country` char(36) NOT NULL,
        `city` varchar(255) NOT NULL,
        `region` char(36) NOT NULL,
        `street` varchar(255) NOT NULL,
        `zipcode` varchar(10) NOT NULL,
        `phone` varchar(10) NOT NULL,
        `notes` text NOT NULL,
        `deleted` tinyint(1) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'contacts_type';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'contacts_type` (
        `uuid` char(36) NOT NULL,
        `name` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'contacts';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'contacts` (
        `uuid` char(36) NOT NULL,
        `credential` char(36) NOT NULL,
        `type` char(36) NOT NULL,
        `value` varchar(255) NOT NULL,
        `validated` tinyint(1) NOT NULL,
        `deleted` tinyint(1) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_types';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'customers_types` (
        `uuid` char(36) NOT NULL,
        `name` varchar(255) NOT NULL,
        `multcred` tinyint(1) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'customers` (
        `uuid` char(36) NOT NULL,
        `type` char(36) NOT NULL,
        `created` datetime NOT NULL,
        `source` char(1) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL,
        `ipaddress` varchar(100) NOT NULL,
        `main` char(36) NOT NULL,
        `deleted` tinyint(1) NOT NULL DEFAULT "0"
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'credentials';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'credentials` (
        `uuid` char(36) NOT NULL,
        `customer` char(36) NOT NULL,
        `guuid` char(36) NOT NULL,
        `username` varchar(255) NOT NULL,
        `password` varchar(255) NOT NULL,
        `encryption` varchar(100) NOT NULL,
        `hashkey` varchar(255) NOT NULL,
        `title` varchar(20) NOT NULL,
        `firstname` varchar(255) NOT NULL,
        `lastname` varchar(255) NOT NULL,
        `active` tinyint(1) NOT NULL DEFAULT "1",
        `lastlogin` datetime NOT NULL,
        `loginip` varchar(100) NOT NULL,
        `termsnotice` datetime NOT NULL,
        `deleted` tinyint(1) NOT NULL DEFAULT "0"
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'companies';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'companies` (
        `customer` char(36) NOT NULL,
        `sector` char(36) NOT NULL,
        `name` varchar(255) NOT NULL,
        `tin` varchar(10) NOT NULL,
        `website` varchar(255) NOT NULL,
        `email` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_verification';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'customers_verification` (
        `customer` char(36) NOT NULL,
        `verification` varchar(255) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_history';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'customers_history` (
        `uuid` char(36) NOT NULL,
        `customer` char(36) NOT NULL,
        `addressip` varchar(50) NOT NULL,
        `opsystem` varchar(255) NOT NULL,
        `browser` varchar(255) NOT NULL,
        `screensize` varchar(50) NOT NULL,
        `visitdate` datetime NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_history_pages';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'customers_history_pages` (
        `historyid` char(36) NOT NULL,
        `product` char(36) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_lists';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'customers_lists` (
        `uuid` char(36) NOT NULL,
        `customer` char(36) NOT NULL,
        `listtype` varchar(50) NOT NULL,
        `product` char(36) NOT NULL,
        `Date_added` datetime NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_tickets';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'customers_tickets` (
        `uuid` char(36) NOT NULL,
        `customer` char(36) NOT NULL,
        `ticket_code` varchar(255) NOT NULL,
        `dateadded` datetime NOT NULL,
        `dateclosed` datetime NOT NULL,
        `title` varchar(255) NOT NULL,
        `content` text NOT NULL,
        `closed` tinyint(1) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_tickets_history';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'customers_tickets_history` (
        `ticket` char(36) NOT NULL,
        `replyauthor` char(36) NOT NULL,
        `replydate` datetime NOT NULL,
        `replytext` text NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_tickets_orders';
      $wpdb->query($Query);
      $Query = 'CREATE TABLE `'.$prfx.'customers_tickets_orders` (
        `ticket` char(36) NOT NULL,
        `orderid` char(36) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if (!$Error) {
      $Query = 'ALTER TABLE `'.$prfx.'addresses_type` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'addresses` ADD PRIMARY KEY (`uuid`),
        ADD KEY `customer` (`customer`),
        ADD KEY `credential` (`credential`),
        ADD KEY `type` (`type`),
        ADD KEY `country` (`country`),
        ADD KEY `region` (`region`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'contacts_type` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'contacts` ADD PRIMARY KEY (`uuid`), ADD KEY `credential` (`credential`), ADD KEY `type` (`type`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'customers_types` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'customers` ADD PRIMARY KEY (`uuid`), ADD KEY `type` (`type`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'credentials` ADD PRIMARY KEY (`uuid`), ADD KEY `customer` (`customer`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'companies` ADD PRIMARY KEY (`uuid`), ADD KEY `sector` (`sector`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'customers_verification` ADD KEY (`customer`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'customers_history` ADD PRIMARY KEY (`uuid`), ADD KEY `customer` (`customer`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'customers_history_pages` ADD KEY `historyid` (`historyid`), ADD KEY `product` (`product`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'customers_lists` ADD PRIMARY KEY (`uuid`), ADD KEY `customer` (`customer`), ADD KEY `product` (`product`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'customers_tickets` ADD PRIMARY KEY (`uuid`), ADD KEY `customer` (`customer`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'customers_tickets_history` ADD KEY `ticket` (`ticket`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'customers_tickets_orders` ADD KEY `ticket` (`ticket`), ADD KEY `orderid` (`orderid`);';
      $wpdb->query($Query);
    }
    if (!$Error) { $JobDone = true; }
    return $JobDone;
  }
  final public function InstallCustomerBasicValues() {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    if (!$this->InsertAddressTypesValues()) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Address types default values could not be installed<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$this->InsertContactTypesValues()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Contact types default values could not be installed<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$this->InsertCustomerTypesValues()) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Customer types default values could not be installed<br>';
      }
    }
    return $RtrnData;
  }
  private function InsertAddressTypesValues() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'INSERT INTO '.$prfx.'addresses_type (uuid, name) VALUES ("'.$this->GetNewUUID().'", "Shipping")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'addresses_type (uuid, name) VALUES ("'.$this->GetNewUUID().'", "Billing")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'addresses_type (uuid, name) VALUES ("'.$this->GetNewUUID().'", "Billing / Shipping")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    if (!$Error) { $JobDone = true; }
    return $JobDone;
  }
  private function InsertContactTypesValues() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'INSERT INTO '.$prfx.'contacts_type (uuid, name) VALUES ("'.$this->GetNewUUID().'", "Phone")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'contacts_type (uuid, name) VALUES ("'.$this->GetNewUUID().'", "Mobile")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'contacts_type (uuid, name) VALUES ("'.$this->GetNewUUID().'", "E-Mail")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'contacts_type (uuid, name) VALUES ("'.$this->GetNewUUID().'", "Fax")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    if (!$Error) { $JobDone = true; }
    return $JobDone;
  }
  private function InsertCustomerTypesValues() {
    $JobDone = false;
    $Error = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'INSERT INTO '.$prfx.'customers_types (uuid, name, multcred) VALUES ("'.$this->GetNewUUID().'", "Guest", "0")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'customers_types (uuid, name, multcred) VALUES ("'.$this->GetNewUUID().'", "Individual", "0")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'customers_types (uuid, name, multcred) VALUES ("'.$this->GetNewUUID().'", "Company", "1")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'customers_types (uuid, name, multcred) VALUES ("'.$this->GetNewUUID().'", "Government", "1")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'INSERT INTO '.$prfx.'customers_types (uuid, name, multcred) VALUES ("'.$this->GetNewUUID().'", "Organization", "1")';
    if (false === $wpdb->query($Query)) { $Error = true; }
    if (!$Error) { $JobDone = true; }
    return $JobDone;
  }
  final public function StartMnsInstallProduct() {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    if (!$this->InstallProductTables()) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Product Tables could NOT be Installed !!!!<br>';
    }
    return $RtrnData;
  }
  private function InstallProductTables() {
    $Error = false;
    $JobDone = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'attributes';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'attributes` (
      `uuid` char(36) NOT NULL,
      `category` char(36) NOT NULL,
      `name` varchar(255) NOT NULL,
      `visibility` tinyint(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'attributes_names';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'attributes_names` (
      `attribute` char(36) NOT NULL,
      `language` char(36) NOT NULL,
      `name` varchar(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'attributes_values';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'attributes_values` (
      `uuid` char(36) NOT NULL,
      `attribute` char(36) NOT NULL,
      `value` varchar(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'brands';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'brands` (
      `uuid` char(36) NOT NULL,
      `name` varchar(255) NOT NULL,
      `logo` varchar(255) NOT NULL,
      `webpage` varchar(255) NOT NULL,
      `color` varchar(20) NOT NULL,
      `visible` tinyint(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'brands_names';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'brands_names` (
      `brand` char(36) NOT NULL,
      `language` char(36) NOT NULL,
      `notes` text NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'categories_codes';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'categories_codes` (
      `uuid` char(36) NOT NULL,
      `name` varchar(255) NOT NULL,
      `image` varchar(255) NOT NULL,
      `visibility` tinyint(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'categories_names';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'categories_names` (
      `category` char(36) NOT NULL,
      `language` char(36) NOT NULL,
      `name` varchar(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'categories_tree';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'categories_tree` (
      `category` char(36) NOT NULL,
      `parent` char(36) NOT NULL,
      `corder` smallint(6) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'files_types';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'files_types` (
      `uuid` char(36) NOT NULL,
      `name` varchar(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_stock_status';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'products_stock_status` (
      `uuid` char(36) NOT NULL,
      `product` char(36) NOT NULL,
      `icon` varchar(255) NOT NULL,
      `color` varchar(50) NOT NULL,
      `stock` decimal(10,0) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_stock_status_descriptions';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'products_stock_status_descriptions` (
      `stock_status` char(36) NOT NULL,
      `language` char(36) NOT NULL,
      `name` varchar(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'products` (
      `uuid` char(36) NOT NULL,
      `guuid` char(36) NOT NULL,
      `code` varchar(255) NOT NULL,
      `brand` char(36) NOT NULL,
      `btbprice` decimal(10,6) NOT NULL,
      `btbtax` smallint(6) NOT NULL,
      `price` decimal(10,6) NOT NULL,
      `tax` smallint(6) NOT NULL,
      `discount` smallint(6) NOT NULL DEFAULT "0",
      `created` datetime NOT NULL,
      `available` datetime NOT NULL,
      `changed` datetime NOT NULL,
      `status` char(36) NOT NULL,
      `stock` decimal(10,4) NOT NULL,
      `minstock` decimal(10,4) NOT NULL,
      `overstock` tinyint(1) NOT NULL,
      `visibility` tinyint(1) NOT NULL,
      `downloadable` tinyint(1) NOT NULL,
      `isbundle` tinyint(1) NOT NULL DEFAULT "0",
      `reviewable` tinyint(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_attributes';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'products_attributes` (
      `product` char(36) NOT NULL,
      `attribute_value` char(36) NOT NULL,
      `visibility` tinyint(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_categories';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'products_categories` (
      `product` char(36) NOT NULL,
      `category` char(36) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_descriptions';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'products_descriptions` (
      `product` char(36) NOT NULL,
      `language` char(36) NOT NULL,
      `description` text NOT NULL,
      `name` varchar(255) NOT NULL,
      `notes` text NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_files';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'products_files` (
      `uuid` char(36) NOT NULL,
      `product` char(36) NOT NULL,
      `filetype` char(36) NOT NULL,
      `file` varchar(255) NOT NULL,
      `dnldtimes` smallint(6) NOT NULL,
      `expiration` datetime NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_images';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'products_images` (
      `uuid` char(36) NOT NULL,
      `product` char(36) NOT NULL,
      `file` varchar(255) NOT NULL,
      `main` tinyint(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_status';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'products_status` (
      `uuid` char(36) NOT NULL,
      `name` varchar(255) NOT NULL,
      `icon` varchar(255) NOT NULL,
      `color` varchar(50) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_status_descriptions';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'products_status_descriptions` (
      `status` char(36) NOT NULL,
      `language` char(36) NOT NULL,
      `name` varchar(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_tags';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'products_tags` (
      `uuid` char(36) NOT NULL,
      `product` char(36) NOT NULL,
      `tags` text NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'ratings_types';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'ratings_types` (
      `uuid` char(36) NOT NULL,
      `name` varchar(255) NOT NULL,
      `min` smallint(6) NOT NULL,
      `max` smallint(6) NOT NULL,
      `step` smallint(6) NOT NULL DEFAULT "1",
      `start` smallint(6) NOT NULL,
      `icon` varchar(255) NOT NULL,
      `active` tinyint(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'reviews';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'reviews` (
      `uuid` char(36) NOT NULL,
      `product` char(36) NOT NULL,
      `customer` char(36) NOT NULL,
      `rtype` char(36) NOT NULL,
      `rvalue` smallint(6) NOT NULL,
      `title` varchar(255) NOT NULL,
      `notes` text NOT NULL,
      `created` datetime NOT NULL,
      `changed` datetime NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'reviews_replies';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'reviews_replies` (
      `uuid` char(36) NOT NULL,
      `review` char(36) NOT NULL,
      `customer` char(36) NOT NULL,
      `notes` text NOT NULL,
      `created` datetime NOT NULL,
      `changed` datetime NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    if (!$Error) {
      $Query = 'ALTER TABLE `'.$prfx.'attributes` ADD PRIMARY KEY (`uuid`), ADD KEY `category` (`category`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'attributes_values` ADD PRIMARY KEY (`uuid`), ADD KEY `attribute` (`attribute`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'brands` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'brands_names` ADD KEY `brand` (`brand`), ADD KEY `language` (`language`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'categories_codes` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'categories_names` ADD KEY `category` (`category`), ADD KEY `language` (`language`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'categories_tree` ADD KEY `category` (`category`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'files_types` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'products` ADD PRIMARY KEY (`uuid`), ADD KEY `brand` (`status`), ADD KEY `brand` (`status`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'products_attributes` ADD KEY `product` (`product`), ADD KEY `attribute_value` (`attribute_value`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'products_categories` ADD KEY `product` (`product`), ADD KEY `category` (`category`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'products_descriptions` ADD KEY `product` (`product`), ADD KEY `language` (`language`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'products_files` ADD PRIMARY KEY (`uuid`), ADD KEY `product` (`product`), ADD KEY `filetype` (`filetype`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'products_files` ADD PRIMARY KEY (`uuid`), ADD KEY `product` (`product`), ADD KEY `filetype` (`filetype`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'products_images` ADD PRIMARY KEY (`uuid`), ADD KEY `product` (`product`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'products_status` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'products_tags` ADD PRIMARY KEY (`uuid`), ADD KEY `product` (`product`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'ratings_types` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'reviews` ADD PRIMARY KEY (`uuid`), ADD KEY `product` (`product`), ADD KEY `customer` (`customer`), ADD KEY `rtype` (`rtype`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'reviews_replies` ADD PRIMARY KEY (`uuid`), ADD KEY `customer` (`customer`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'products_stock_status` ADD PRIMARY KEY (`uuid`), ADD KEY `product` (`product`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'products_stock_status_descriptions` ADD KEY `stock_status` (`stock_status`), ADD KEY `language` (`language`);';
      $wpdb->query($Query);
    }
    if (!$Error) { $JobDone = true; }
    return $JobDone;
  }
  final public function InstallProductBasicValues() {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Global = $this->GetNewUUID();
    $Query = 'INSERT INTO '.$prfx.'categories_codes (uuid, name, image, visibility) VALUES ("'.$Global.'", "GLOBAL", "NOIMAGE", "0")';
    if (false === $wpdb->query($Query)) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Categories default values could not be installed<br>';
    } else {
      $Error = false;
      $Query = 'INSERT INTO '.$prfx.'attributes (uuid, category, name, visibility) VALUES ("'.$this->GetNewUUID().'", "'.$Global.'", "Weight", "1")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'attributes (uuid, category, name, visibility) VALUES ("'.$this->GetNewUUID().'", "'.$Global.'", "Color", "1")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'attributes (uuid, category, name, visibility) VALUES ("'.$this->GetNewUUID().'", "'.$Global.'", "Height", "1")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'attributes (uuid, category, name, visibility) VALUES ("'.$this->GetNewUUID().'", "'.$Global.'", "Length", "1")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'attributes (uuid, category, name, visibility) VALUES ("'.$this->GetNewUUID().'", "'.$Global.'", "Size", "1")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'attributes (uuid, category, name, visibility) VALUES ("'.$this->GetNewUUID().'", "'.$Global.'", "Volume", "1")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'attributes (uuid, category, name, visibility) VALUES ("'.$this->GetNewUUID().'", "'.$Global.'", "Width", "1")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      if ($Error) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Attributes default values could not be installed<br>';
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $Error = false;
      $Query = 'INSERT INTO '.$prfx.'files_types (uuid, name) VALUES ("'.$this->GetNewUUID().'", "application")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'files_types (uuid, name) VALUES ("'.$this->GetNewUUID().'", "video")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      $Query = 'INSERT INTO '.$prfx.'files_types (uuid, name) VALUES ("'.$this->GetNewUUID().'", "audio")';
      if (false === $wpdb->query($Query)) { $Error = true; }
      if ($Error) {
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'File type default values could not be installed<br>';
      }
    }
    return $RtrnData;
  }
  final public function StartMnsInstallSales() {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    if (!$this->InstallSalesTables()) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Sales Tables could NOT be Installed !!!!<br>';
    }
    return $RtrnData;
  }
  private function InstallSalesTables() {
    $Error = false;
    $JobDone = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'couriers_type';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'couriers_type` (
      `uuid` char(36) NOT NULL,
      `name` varchar(255) NOT NULL,
      `delivery_speed` varchar(50) NOT NULL,
      `billing_type` varchar(50) NOT NULL,
      `active` tinyint(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'orders` (
      `uuid` char(36) NOT NULL,
      `serial` varchar(255) NOT NULL,
      `refnumber` varchar(30) NOT NULL,
      `created` datetime NOT NULL,
      `customer` char(36) NOT NULL,
      `billingaddr` char(36) NOT NULL,
      `sendingaddr` char(36) NOT NULL,
      `shipping` char(36) NOT NULL,
      `orderip` varchar(100) NOT NULL,
      `complete` tinyint(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_payment';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'orders_payment` (
      `orders` char(36) NOT NULL,
      `payment` char(36) NOT NULL,
      `answer` text NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_products';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'orders_products` (
      `orders` char(36) NOT NULL,
      `product` char(36) NOT NULL,
      `amount` decimal(10,4) NOT NULL,
      `price` decimal(10,6) NOT NULL,
      `discount` decimal(10,6) NOT NULL,
      `taxes` decimal(10,6) NOT NULL,
      `fullprice` decimal(10,6) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_shipping';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'orders_shipping` (
      `uuid` char(36) NOT NULL,
      `courier` char(36) NOT NULL,
      `country` char(36) NOT NULL,
      `price` decimal(10,6) NOT NULL,
      `weight` decimal(10,6) NOT NULL,
      `disabled` tinyint(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_split_relations';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'orders_split_relations` (
      `orders` char(36) NOT NULL,
      `split_order` char(36) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_status';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'orders_status` (
      `orders` char(36) NOT NULL,
      `status` char(36) NOT NULL,
      `changed` datetime NOT NULL,
      `active` tinyint(1) NOT NULL DEFAULT "0"
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_status_type';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'orders_status_type` (
      `uuid` char(36) NOT NULL,
      `name` varchar(255) NOT NULL,
      `final` tinyint(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'returns_products';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'returns_products` (
      `returns` char(36) NOT NULL,
      `product` char(36) NOT NULL,
      `samount` decimal(10,4) NOT NULL,
      `sprice` decimal(10,6) NOT NULL,
      `ramount` decimal(10,4) NOT NULL,
      `rprice` decimal(10,6) NOT NULL,
      `notes` text NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_returns';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'orders_returns` (
      `uuid` char(36) NOT NULL,
      `orders` char(36) NOT NULL,
      `created` datetime NOT NULL,
      `notes` text NOT NULL,
      `complete` tinyint(1) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'returns_status';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'returns_status` (
      `returns` char(36) NOT NULL,
      `status` char(36) NOT NULL,
      `changed` datetime NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'returns_status_type';
    $wpdb->query($Query);
    $Query = 'CREATE TABLE `'.$prfx.'returns_status_type` (
      `uuid` char(36) NOT NULL,
      `name` varchar(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    if (false === $wpdb->query($Query)) { $Error = true; }
    if (!$Error) {
      $Query = 'ALTER TABLE `'.$prfx.'couriers_type` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'orders` ADD PRIMARY KEY (`uuid`), ADD KEY `customer` (`customer`), ADD KEY `billingaddr` (`billingaddr`), ADD KEY `sendingaddr` (`sendingaddr`), ADD KEY `shipping` (`shipping`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'orders_payment` ADD KEY `orders` (`orders`), ADD KEY `payment` (`payment`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'orders_products` ADD KEY `orders` (`orders`), ADD KEY `product` (`product`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'orders_shipping` ADD PRIMARY KEY (`uuid`), ADD KEY `courier` (`courier`), ADD KEY `country` (`country`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'orders_split_relations` ADD KEY `orders` (`orders`), ADD KEY `split_order` (`split_order`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'orders_status` ADD KEY `orders` (`orders`), ADD KEY `status` (`status`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'orders_status_type` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'returns_products` ADD KEY `returns` (`returns`), ADD KEY `product` (`product`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'orders_returns` ADD PRIMARY KEY (`uuid`), ADD KEY `orders` (`orders`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'returns_status` ADD KEY `returns` (`returns`), ADD KEY `status` (`status`);';
      $wpdb->query($Query);
      $Query = 'ALTER TABLE `'.$prfx.'returns_status_type` ADD PRIMARY KEY (`uuid`);';
      $wpdb->query($Query);
    }
    if (!$Error) { $JobDone = true; }
    return $JobDone;
  }
  final public function InstallSalesBasicValues() {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Values = '("'.$this->GetNewUUID().'", "Split Order", 1)::
("'.$this->GetNewUUID().'", "Active", 0)::
("'.$this->GetNewUUID().'", "Submitted", 0)::
("'.$this->GetNewUUID().'", "Complete", 1)::
("'.$this->GetNewUUID().'", "Canceled", 1)::
("'.$this->GetNewUUID().'", "No Products", 0)::
("'.$this->GetNewUUID().'", "Pending", 0)::
("'.$this->GetNewUUID().'", "Denied", 1)::
("'.$this->GetNewUUID().'", "Cancel Reversal", 0)::
("'.$this->GetNewUUID().'", "Chargeback", 1)::
("'.$this->GetNewUUID().'", "Expired", 1)::
("'.$this->GetNewUUID().'", "Failed", 1)::
("'.$this->GetNewUUID().'", "Processed", 0)::
("'.$this->GetNewUUID().'", "Processing", 0)::
("'.$this->GetNewUUID().'", "Refunded", 1)::
("'.$this->GetNewUUID().'", "Reversed", 0)::
("'.$this->GetNewUUID().'", "Shipped", 0)';
    $Values = explode('::',$Values);
    foreach ($Values as $Row) {
      $Query = 'INSERT INTO '.$prfx.'orders_status_type (uuid, name, final) VALUES '.$Row;
      if (false === $wpdb->query($Query)) { $Error = true; }
    }
    if ($Error) {
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Order status types default values could not be installed<br>';
    }
    return $RtrnData;
  }
}
