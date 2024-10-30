<?php
class mensio_Admin {
	private $plugin_name;
	private $version;
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	public function enqueue_styles() {
		 wp_enqueue_style(
			$this->plugin_name.'-jqui',
			plugin_dir_url( __FILE__ ) . 'css/jqui.min.css',
			array(), $this->version,
			'all'
	   );
		 wp_enqueue_style(
			$this->plugin_name.'-jquistr',
			plugin_dir_url( __FILE__ ) . 'css/jqui.str.css',
			array(), $this->version,
			'all'
	   );
		 wp_enqueue_style(
			$this->plugin_name.'-jquithm',
			plugin_dir_url( __FILE__ ) . 'css/jqui.thm.css',
			array(), $this->version,
			'all'
	   );
    wp_enqueue_style(
			$this->plugin_name.'-fontawesome',
			plugin_dir_url( __FILE__ ) . 'css/font-awesome.css',
			array(), $this->version,
			'all'
	   );
		 wp_enqueue_style(
		 	$this->plugin_name.'-mnsdt',
			plugin_dir_url( __FILE__ ) . 'css/mensio-datatable.css',
			array(),
			$this->version,
			'all'
		 );
		 wp_enqueue_style(
		 	$this->plugin_name.'-main',
			plugin_dir_url( __FILE__ ) . 'css/mensio-admin.css',
			array(),
			$this->version,
			'all'
		 );
	}
	public function enqueue_scripts() {
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-accordion' );
    wp_enqueue_script( 'jquery-ui-autocomplete' );
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_script( 'jquery-ui-dialog' );
    wp_enqueue_script( 'jquery-ui-draggable' );
    wp_enqueue_script( 'jquery-ui-droppable' );
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'jquery-ui-slider' );
    wp_enqueue_script( 'jquery-ui-spiner' );
    wp_enqueue_script( 'jquery-ui-tooltip' );
    wp_enqueue_script( 'jquery-ui-tabs' );
    wp_enqueue_script( 'jquery-effects-core' );
    wp_enqueue_script( 'jquery-effects-blind' );
    wp_enqueue_script( 'jquery-effects-bounce' );
    wp_enqueue_script( 'jquery-effects-drop' );
    wp_enqueue_script( 'jquery-effects-explode' );
    wp_enqueue_script( 'jquery-effects-fade' );
    wp_enqueue_script( 'jquery-effects-fold' );
    wp_enqueue_script( 'jquery-effects-scale' );
    wp_enqueue_script( 'jquery-effects-shake' );
    wp_enqueue_script( 'jquery-effects-slide' );
    wp_enqueue_media();
		wp_enqueue_script(
			$this->plugin_name.'-mnsdt',
			plugin_dir_url( __FILE__ ) . 'js/mensio-datatable.js',
			array( 'jquery' ),
			$this->version,
			false
		);
		wp_enqueue_script(
			$this->plugin_name.'-main',
			plugin_dir_url( __FILE__ ) . 'js/mensio-admin.js',
			array( 'jquery' ),
			$this->version,
			false
		);
	}
	public function mensio_main_admin_menu() {
    $this->Add_Main_Pages();
    $this->Add_Products_Pages();
    $this->Add_Customers_Pages();
    $this->Add_Orders_Pages();
    $this->Add_Settings_Pages();
    $this->Add_System_Pages();
	}
  private function GetUserPermissions() {
    $current_user = wp_get_current_user();
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'store_users_permissions WHERE userID = "'.$current_user->ID.'"';
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  private function Add_Main_Pages() {
		add_menu_page(
      'Mensiopress:Dashboard',
      'Mensiopress',
      'manage_options',
      'Mensio_Admin_Main_DashBoard',
      'Mensio_Admin_DashBoard',
      plugin_dir_url('mensiopress/admin/icons/menu/mensiopress-sidebar-emblem.png').'mensiopress-sidebar-emblem.png',
      2
    );
    $DataSet = $this->GetUserPermissions();
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        if ($Row->products) {    
          add_submenu_page('Mensio_Admin_Main_DashBoard','Mensiopress:Products','Catalog','manage_options','Mensio_Admin_Main_Products','Mensio_Admin_Products');
        }
        if ($Row->customers) {
          add_submenu_page('Mensio_Admin_Main_DashBoard','Mensiopress:Customers','Customers','manage_options','Mensio_Admin_Main_Customers','Mensio_Admin_Customers');
        }
        if ($Row->orders) {
          add_submenu_page('Mensio_Admin_Main_DashBoard','Mensiopress:Orders','Sales','manage_options','Mensio_Admin_Main_Orders','Mensio_Admin_Orders');
        }          
        if ($Row->design) {
          add_submenu_page('Mensio_Admin_Main_DashBoard','Mensiopress:Pages','Pages','manage_options','mnsObjPrintAllPages','Mensio_Admin_Pages');
        }
        if ($Row->settings) {
          add_submenu_page('Mensio_Admin_Main_DashBoard','Mensiopress:Settings','Settings','manage_options','Mensio_Admin_Main_Settings_Store','Mensio_Admin_Settings_Store');
        }
        if ($Row->system) {
          add_submenu_page('Mensio_Admin_Main_DashBoard','Mensiopress:System','System','manage_options','Mensio_Admin_Main_System','Mensio_Admin_System');
        }
      }
    }
  }
  private function Add_Products_Pages() {
		add_menu_page('Mensiopress:Products','Products','manage_options','Mensio_Admin_Products','Mensio_Admin_Products','dashicons-archive');
		add_submenu_page('Mensio_Admin_Products','Mensiopress:Categories','Categories','manage_options','Mensio_Admin_Products_Categories','Mensio_Admin_Products_Categories');
		add_submenu_page('Mensio_Admin_Products','Mensiopress:Hierarchy','Categories Tree','manage_options','Mensio_Admin_Products_Categories_Tree','Mensio_Admin_Products_Categories_Tree');
		add_submenu_page('Mensio_Admin_Products','Mensiopress:Brands','Brands','manage_options','Mensio_Admin_Products_Brands','Mensio_Admin_Products_Brands');
  }
  private function Add_Customers_Pages() {
		add_menu_page('Mensiopress:Customers','Customers','manage_options','Mensio_Admin_Customers','Mensio_Admin_Customers','dashicons-businessman');
		add_submenu_page('Mensio_Admin_Customers','Mensiopress:Inactive','Deleted','manage_options','Mensio_Admin_Deleted_Customers','Mensio_Admin_Deleted_Customers');
  }
  private function Add_Orders_Pages() {
		add_menu_page('Mensiopress:Orders','Orders','manage_options','Mensio_Admin_Orders','Mensio_Admin_Orders','dashicons-cart');
		add_submenu_page('Mensio_Admin_Orders','Mensiopress:Returns','Returns','manage_options','Mensio_Admin_Orders_Returns','Mensio_Admin_Orders_Returns');
		add_submenu_page('Mensio_Admin_Orders','Mensiopress:Tickets','Tickets','manage_options','Mensio_Admin_Orders_Tickets','Mensio_Admin_Orders_Tickets');
  }
  private function Add_Settings_Pages() {
		add_menu_page('Mensiopress:Store','Store','manage_options','Mensio_Admin_Settings_Store','Mensio_Admin_Settings_Store','dashicons-admin-settings');
		add_submenu_page('Mensio_Admin_Settings_Store','Mensiopress:UISettings','UISettings','manage_options','Mensio_Admin_Settings_UISettings','Mensio_Admin_Settings_UISettings');
		add_submenu_page('Mensio_Admin_Settings_Store','Mensiopress:Default Languages','DefaultLanguages','manage_options','Mensio_Admin_Settings_DefaultLanguages','Mensio_Admin_Settings_DefaultLanguages');
		add_submenu_page('Mensio_Admin_Settings_Store','Mensiopress:Sales Settings','SalesSettings','manage_options','Mensio_Admin_Settings_SalesSettings','Mensio_Admin_Settings_SalesSettings');
		add_submenu_page('Mensio_Admin_Settings_Store','Mensiopress:Mail Settings','MailSettings','manage_options','Mensio_Admin_Settings_MailSettings','Mensio_Admin_Settings_MailSettings');
		add_submenu_page('Mensio_Admin_Settings_Store','Mensiopress:Permissions','Permissions','manage_options','Mensio_Admin_Settings_UserPermissions','Mensio_Admin_Settings_UserPermissions');
		add_submenu_page('Mensio_Admin_Settings_Store','Mensiopress:Terms of Service','TermsofService','manage_options','Mensio_Admin_Settings_TermsOfService','Mensio_Admin_Settings_TermsOfService');
		add_submenu_page('Mensio_Admin_Settings_Store','Mensiopress:Payments','Payments','manage_options','Mensio_Admin_Settings_Payments','Mensio_Admin_Settings_Payments');
		add_submenu_page('Mensio_Admin_Settings_Store','Mensiopress:Shipping','Shipping','manage_options','Mensio_Admin_Orders_Shipping','Mensio_Admin_Orders_Shipping');
		add_submenu_page('Mensio_Admin_Settings_Store','Mensiopress:Default Attributes','Default Attributes','manage_options','Mensio_Admin_Products_Default_Attributes','Mensio_Admin_Products_Default_Attributes');
		add_submenu_page('Mensio_Admin_Settings_Store','Mensiopress:Ratings','Ratings','manage_options','Mensio_Admin_Settings_Ratings','Mensio_Admin_Settings_Ratings');
  }
  private function Add_System_Pages() {
		add_menu_page('Mensiopress:System','System','manage_options','Mensio_Admin_System','Mensio_Admin_System','dashicons-admin-tools');
		add_submenu_page('Mensio_Admin_System','Mensiopress:Regions','Regions','manage_options','Mensio_Admin_Settings_Regions','Mensio_Admin_Settings_Regions');
		add_submenu_page('Mensio_Admin_System','Mensiopress:Languages','Languages','manage_options','Mensio_Admin_Settings_Languages','Mensio_Admin_Settings_Languages');
		add_submenu_page('Mensio_Admin_System','Mensiopress:Currencies','Currencies','manage_options','Mensio_Admin_Settings_Currencies','Mensio_Admin_Settings_Currencies');
		add_submenu_page('Mensio_Admin_System','Mensiopress:Continents','Continents','manage_options','Mensio_Admin_Settings_Continents','Mensio_Admin_Settings_Continents');
		add_submenu_page('Mensio_Admin_System','Mensiopress:Countries','Countries','manage_options','Mensio_Admin_Settings_Countries','Mensio_Admin_Settings_Countries');
		add_submenu_page('Mensio_Admin_System','Mensiopress:Sectors','Sectors','manage_options','Mensio_Admin_Settings_Sectors','Mensio_Admin_Settings_Sectors');
		add_submenu_page('Mensio_Admin_System','Mensiopress:Status','Status','manage_options','Mensio_Admin_System_Status','Mensio_Admin_System_Status');
		add_submenu_page('Mensio_Admin_System','Mensiopress:Logs','Logs','manage_options','Mensio_Admin_System_Logs','Mensio_Admin_System_Logs');
		add_submenu_page('Mensio_Admin_System','Mensiopress:Logs','Notifications','manage_options','Mensio_Admin_System_Notifications','Mensio_Admin_System_Notifications');
  }
}