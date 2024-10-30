<?php
class Mensio_Admin_Status_Form extends mensio_core_form {
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-status',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-system-status.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-status',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-system-status.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  public function GetDebugIcon() {
    $Icon = 'times';
    if ((defined('WP_DEBUG')) && (true === WP_DEBUG)) {
      $Icon = 'check';
    }
    $Icon = '<i class="fa fa-'.$Icon.'" aria-hidden="true"></i>';
    return $Icon;
  }
  public function GetMultisiteIcon() {
    $Icon = 'times';
    if (MULTISITE) { $Icon = 'check'; }
    $Icon = '<i class="fa fa-'.$Icon.'" aria-hidden="true"></i>';
    return $Icon;
  }
  public function GetMySQLInfo() {
    $Info = array();
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASSWORD);
    if (!mysqli_connect_errno()) {
      $Info['Version'] = $mysql->server_version;
      $Info['Info'] = $mysql->server_info;
      $mysql->close();    
    }
    return $Info;
  }
  public function GetPHPExtensions() {
    $List = '';
    $Ext = get_loaded_extensions();
    foreach ($Ext as $Row) {
      if ($List === '') { $List .= $Row; }
        else { $List .= ', '.$Row; }
    }
    return $List;
  }
}