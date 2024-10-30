<?php
class Mensio_Admin_System_Form extends mensio_core_form {
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-about',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-system-about.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-about',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-system-about.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
}