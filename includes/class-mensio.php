<?php
class mensio {
	protected $loader;
	protected $plugin_name;
	protected $version;
	private $ActivePlugin;
	public function __construct() {
		$this->plugin_name = MENSIO_PLGTITLE;
		$this->version = MENSIO_VERSION;
		$this->ActivePlugin = $this->CheckIfActive();
		if ($this->ActivePlugin) {
			$this->load_dependencies();
			$this->set_locale(); // localisation
			$this->define_admin_hooks();
			$this->define_public_hooks();
		} else {
			$this->load_install_dependencies();
			$this->define_install_hooks();
		}
	}
	private function CheckIfActive() {
		$PgnState = false;
		global $wpdb;
		$name = 'mensio_installed';
		$query = 'SELECT * FROM '.$wpdb->prefix.'options WHERE option_name = %s';
		$DataRows = $wpdb->get_results($wpdb->prepare($query,$name));
		foreach ( $DataRows as $Data ) {
			if ($Data->option_value == 'Active') { $PgnState = true; }
		}
		return $PgnState;
	}
	private function load_install_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mensio-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mensio-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'activation/forms/mensio-install-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'activation/forms/mensio-install-form.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'activation/class-mensio-install.php';
		$this->loader = new mensio_Loader();
	}
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mensio-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mensio-i18n.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mensio-admin-definitions.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mensio-admin-lib.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mensio-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mensio-public-lib.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mensio-public.php';
		$this->loader = new mensio_Loader();
	}
	private function set_locale() {
		$plugin_i18n = new mensio_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}
	private function define_install_hooks() {
		$plugin_admin = new mensio_Install( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'mensio_main_install_menu' );
	}
	private function define_admin_hooks() {
		$plugin_admin = new mensio_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'mensio_main_admin_menu' );
	}
	private function define_public_hooks() {
		$plugin_public = new mensio_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'Mensio_Public_Functions', $plugin_public, 'Mensio_Public_Functions' );
	}
	public function run() {
		$this->loader->run();
	}
	public function get_plugin_name() {
		return $this->plugin_name;
	}
	public function get_loader() {
		return $this->loader;
	}
	public function get_version() {
		return $this->version;
	}
}