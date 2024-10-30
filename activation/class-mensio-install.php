<?php
class mensio_Install {
	private $plugin_name;
	private $version;
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mensio-install.css', array(), $this->version, 'all' );
	}
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mensio-install.js', array( 'jquery' ), $this->version, false );
	}
	public function mensio_main_install_menu() {
		add_menu_page(
      'mensio_Install_Page',
      'mensio Installation',
      'manage_options',
      'mensio_Install_Page',
      'mensio_Install_Page',
      plugin_dir_url('mensiopress/admin/icons/menu/mensio.png').'mensio.png'
    );
	}
}
