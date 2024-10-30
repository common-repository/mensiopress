<?php
class mensio_core_form {
  protected $ActivePage;
  private $MainPageTemplate;
  private $MainMenuItems;
  private $CustomMenuItems;
  private $MainPlaceHolder;
  protected $TimeZone;
  public function Set_MainMenuItems($Value) {
    $this->MainMenuItems = $Value;
  }
  public function Set_CustomMenuItems($Value) {
    $this->CustomMenuItems = $Value;
  }
  public function Set_MainPlaceHolder($Value) {
    $this->MainPlaceHolder = $Value;
  }
  final public function SetTimerCallPage() {
    $this->ActivePage = 'TimerCall';
  }
  protected function GetStoreActiveTimezone() {
    $this->TimeZone = 'UTC';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'store';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $this->TimeZone = $Row->tzone;
      }
    }
  }
  protected function Set_MainTemplate() {
    add_filter( 'admin_footer_text', '__return_empty_string', 11 );
    add_filter( 'update_footer',     '__return_empty_string', 11 );
    add_filter('admin_footer_text', 'Add_Mensio_Footer');
    $this->GetStoreActiveTimezone();
    $this->MainPageTemplate = '
    <div id="MENSIOLoader">
      <p>
        <img src="'.plugins_url('mensiopress/admin/icons/default/loading.gif').'" alt="loading_image">
      </p>
    </div>
    <div id="MENSIO">
      <!-- Header Bar Area Start -->
      <div id="Mensio_HeadBar" class="Mns_InfoBar Mns_HeaderBar">
          <!-- Menu Area Start -->
          <div id="Mns_HeaderBar_Menu" class="Mns_HeadBar_Menu_Ctrl">
            <div id="actions-div">
              [---MENUITEMSPLACEHOLDER---]
              [---BUTTONPLACEHOLDER---]
              <div class="DivResizer"></div>
            </div>
            <div class="DivResizer"></div>
          </div>
          <!-- Menu Area End -->
          <!-- Notification PopUp Start -->
          <div id="Mns_PopUp_Wrapper"></div>
          <!-- Notification PopUp End -->
      <div class="DivResizer"></div>
      </div>
      <!-- Header Bar Area End -->
      <!-- Warning Bar End -->
      <div id="NOSAVEWARN" class="warning-div"></div>
      <!-- Warning Bar End -->
      <!-- Modal Area Start -->
      <div id="MnsModal" class="Modal_Wrapper"></div>
      <!-- Modal Area End -->
      <!-- Main Container Start -->
      <div class="wrap">
        '.$this->CreateMainPagesDivision().'
        <div class="Mns_MainBody">
          [---MAINPLACEHOLDER---]
        <div class="DivResizer"></div>
        </div>
      <div class="DivResizer"></div>
      </div>
      <!-- Main Container End -->
    <div class="DivResizer"></div>
    </div>
    <div id="MENSIONO">
      <div class="MNSNO_MessageDiv">
        <div class="MNSNO_MessageWrap">
          <h3>HEADS UP !</h3>
          <i class="fa fa-exclamation-triangle fa-3x" aria-hidden="true"></i>
          <p>A larger browser window is required in order to access the backend of this plugin.</p>
          <br>
          <p>Thank you.</p>
        </div>
      </div>
    </div>';
  }
  private function CreateMainPagesDivision() {
    $RtrnData = '<div class="Mns_MainMenuNav Mns_InfoBar">';
    $DataSet = $this->GetUserPermissions();
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        if ($Row->products) {
          $RtrnData .= '<a id="PG_Products" class=" action-item" href="admin.php?page=Mensio_Admin_Main_Products" title="'.MENSIO_PAGEINFO_Catalogue.'">
            <img src="'.plugins_url('mensiopress/admin/icons/menu/main_catalog.png').'" alt="main_catalog">
            <span class="action-txt">Catalog</span>
          </a>';
        }
        if ($Row->customers) {
          $RtrnData .= '<a id="PG_Customers" class=" action-item" href="admin.php?page=Mensio_Admin_Main_Customers" title="'.MENSIO_PAGEINFO_Customers.'">
            <img src="'.plugins_url('mensiopress/admin/icons/menu/main_customers.png').'" alt="main_customers">
            <span class="action-txt">Customers</span>
          </a>';
        }
        if ($Row->orders) {
          $RtrnData .= '<a id="PG_Orders" class=" action-item" href="admin.php?page=Mensio_Admin_Main_Orders" title="'.MENSIO_PAGEINFO_Orders.'">
            <img src="'.plugins_url('mensiopress/admin/icons/menu/main_commerce.png').'" alt="main_commerce">
            <span class="action-txt">Commerce</span>
          </a>';
        }          
        if ($Row->design) {
          $RtrnData .= '<a id="PG_Design" class=" action-item" href="admin.php?page=mnsObjPrintAllPages" title="'.MENSIO_PAGEINFO_Design.'">
            <img src="'.plugins_url('mensiopress/admin/icons/menu/main_design.png').'" alt="main_design">
            <span class="action-txt">Pages</span>
          </a>';
        }
        if ($Row->settings) {
          $RtrnData .= '<a id="PG_Settings" class=" action-item" href="admin.php?page=Mensio_Admin_Main_Settings_Store" title="'.MENSIO_PAGEINFO_Settings.'">
            <img src="'.plugins_url('mensiopress/admin/icons/menu/main_settings.png').'" alt="main_settings">
            <span class="action-txt">Store</span>
          </a>';
        }
        if ($Row->system) {
          $RtrnData .= '<a id="PG_System" class=" action-item" href="admin.php?page=Mensio_Admin_System" title="'.MENSIO_PAGEINFO_System.'">
            <img src="'.plugins_url('mensiopress/admin/icons/menu/main_system.png').'" alt="main_system">
            <span class="action-txt">System</span>
          </a>';
        }
      }
    }
    $RtrnData .= '</div>';
    return $RtrnData;
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
  protected function ConvertDateToTimezone($Date,$Format='Y-m-d H:i:s',$TZone=''){
    if ($TZone === '') { $TZone = $this->TimeZone; }
    $Date = new DateTime($Date,new DateTimeZone('UTC'));
    $Date->setTimezone( new DateTimeZone($TZone) );
    return $Date->format($Format);
  }
  public function GetPageSubPages($Part) {
    $Buttons = '<a id="PG_DashBoard" class=" action-item" href="admin.php?page=Mensio_Admin_Main_DashBoard" title="'.MENSIO_PAGEINFO_DashBoard.'">
                  <div id="PG_DashBoard_Main_Image">
                    <img src="'.plugins_url('mensiopress/admin/icons/menu/mensio.png').'" alt="mensiopress_logo">
                  </div>
                  <span class="action-txt">Main</span>
                </a>';
    switch ($Part) {
      case 'DashBoard':
        $Buttons .= '';
        break;
      case 'Products':
        $Buttons .= '
                <a id="PG_Catalogue" class=" action-item" href="admin.php?page=Mensio_Admin_Products" title="'.MENSIO_PAGEINFO_Catalogue.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/catalog_products.png').'" alt="catalog_products">
                  <span class="action-txt">Products</span>
                </a>
                <a id="PG_Brands" class=" action-item" href="admin.php?page=Mensio_Admin_Products_Brands" title="'.MENSIO_PAGEINFO_Brands.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/catalog_brands.png').'" alt="catalog_brands">
                  <span class="action-txt">Brands</span>
                </a>
                <a id="PG_Categories" class=" action-item" href="admin.php?page=Mensio_Admin_Products_Categories" title="'.MENSIO_PAGEINFO_Categories.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/catalog_categories.png').'" alt="catalog_categories">
                  <span class="action-txt">Categories</span>
                </a>
                <a id="PG_CategoriesTree" class=" action-item" href="admin.php?page=Mensio_Admin_Products_Categories_Tree" title="'.MENSIO_PAGEINFO_CategoriesTree.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/catalog_hierarchy.png').'" alt="catalog_hierarchy">
                  <span class="action-txt">Hierarchy</span>
                </a>
';
        break;
      case 'Customers':
        $Buttons .= '
                <a id="PG_Accounts" class=" action-item" href="admin.php?page=Mensio_Admin_Customers" title="'.MENSIO_PAGEINFO_Customers.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/customers_accounts.png').'" alt="customers_accounts">
                  <span class="action-txt">Accounts</span>
                </a>
                <a id="PG_Deleted" class=" action-item" href="admin.php?page=Mensio_Admin_Deleted_Customers" title="'.MENSIO_PAGEINFO_Deleted.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/customers_inactive.png').'" alt="customers_inactive">
                  <span class="action-txt">Inactive</span>
                </a>';
        break;
      case 'Orders':
        $Buttons .= '
                <a id="PG_Sales" class=" action-item" href="admin.php?page=Mensio_Admin_Orders" title="'.MENSIO_PAGEINFO_Orders.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/commerce_orders.png').'" alt="commerce_orders">
                  <span class="action-txt">Orders</span>
                </a>
                <a id="PG_Tickets" class=" action-item" href="admin.php?page=Mensio_Admin_Orders_Tickets" title="'.MENSIO_PAGEINFO_Tickets.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/commerce_tickets.png').'" alt="commerce_tickets">
                  <span class="action-txt">Tickets</span>
                </a>';
        break;
      case 'Settings':
        $Buttons .= '
                <a id="PG_Store" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_Store" title="'.MENSIO_PAGEINFO_Settings.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/settings_store.png').'" alt="settings store general">
                  <span class="action-txt">General</span>
                </a>
                <a id="PG_UISettings" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_UISettings" title="'.MENSIO_PAGEINFO_UISettings.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/mensiopress-horizontal-button-store-uisettings.png').'" alt="settings store ui">
                  <span class="action-txt">UI Settings</span>
                </a>
                <a id="PG_DefaultLanguages" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_DefaultLanguages" title="'.MENSIO_PAGEINFO_DefaultLanguages.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/mensiopress-horizontal-button-store-languages.png').'" alt="settings store languages">
                  <span class="action-txt">Languages</span>
                </a>
                <a id="PG_SalesSettings" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_SalesSettings" title="'.MENSIO_PAGEINFO_SalesSettings.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/mensiopress-horizontal-button-store-commerce.png').'" alt="settings store sales">
                  <span class="action-txt">Commerce</span>
                </a>
                <a id="PG_MailSettings" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_MailSettings" title="'.MENSIO_PAGEINFO_MailSettings.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/mensiopress-horizontal-button-store-mail.png').'" alt="settings store mails">
                  <span class="action-txt">Mail</span>
                </a>
                <a id="PG_UserPermissions" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_UserPermissions" title="'.MENSIO_PAGEINFO_UserPermissions.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/mensiopress-horizontal-button-store-permissions.png').'" alt="settings store user permissions">
                  <span class="action-txt">Permissions</span>
                </a>
                <a id="PG_TermsOfService" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_TermsOfService" title="'.MENSIO_PAGEINFO_TermsOfService.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/mensiopress-horizontal-button-store-terms.png').'" alt="settings store terms of service">
                  <span class="action-txt">Terms</span>
                </a>
                <a id="PG_PaymentMethods" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_Payments" title="'.MENSIO_PAGEINFO_Shipping.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/settings_payments.png').'" alt="settings_payments">
                  <span class="action-txt">Payments</span>
                </a>
                <a id="PG_Shipping" class=" action-item" href="admin.php?page=Mensio_Admin_Orders_Shipping" title="'.MENSIO_PAGEINFO_Shipping.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/settings_shipping.png').'" alt="settings_shipping">
                  <span class="action-txt">Shipping</span>
                </a>
                <a id="PG_GlobalAttributes" class=" action-item" href="admin.php?page=Mensio_Admin_Products_Default_Attributes" title="'.MENSIO_PAGEINFO_GlobalAttributes.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/settings_attributes.png').'" alt="settings_attributes">
                  <span class="action-txt">Attributes</span>
                </a>
                <a id="PG_Ratings" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_Ratings" title="'.MENSIO_PAGEINFO_Ratings.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/settings_ratings.png').'" alt="settings_ratings">
                  <span class="action-txt">Ratings</span>
                </a>';
        break;
      case 'System':
        $Buttons .= '
                <a id="PG_About" class=" action-item" href="admin.php?page=Mensio_Admin_System" title="'.MENSIO_PAGEINFO_SysAbout.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/system_about.png').'" alt="system_about">
                  <span class="action-txt">About</span>
                </a>
                <a id="PG_Continent" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_Continents" title="'.MENSIO_PAGEINFO_Continent.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/settings_continents.png').'" alt="settings_continents">
                  <span class="action-txt">Continents</span>
                </a>
                <a id="PG_Countries" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_Countries" title="'.MENSIO_PAGEINFO_Countries.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/settings_countries.png').'" alt="settings_countries">
                  <span class="action-txt">Countries</span>
                </a>
                <a id="PG_Regions" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_Regions" title="'.MENSIO_PAGEINFO_Regions.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/settings_regions.png').'" alt="settings_regions">
                  <span class="action-txt">Regions</span>
                </a>
                <a id="PG_Languages" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_Languages" title="'.MENSIO_PAGEINFO_Currency.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/settings_languages.png').'" alt="settings_languages">
                  <span class="action-txt">Languages</span>
                </a>
                <a id="PG_Currency" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_Currencies" title="'.MENSIO_PAGEINFO_Languages.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/settings_currency.png').'" alt="settings_currency">
                  <span class="action-txt">Currencies</span>
                </a>
                <a id="PG_BusinessSectors" class=" action-item" href="admin.php?page=Mensio_Admin_Settings_Sectors" title="'.MENSIO_PAGEINFO_BusinessSectors.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/settings_industries.png').'" alt="settings_industries">
                  <span class="action-txt">Industries</span>
                </a>
                <a id="PG_Status" class=" action-item" href="admin.php?page=Mensio_Admin_System_Status" title="'.MENSIO_PAGEINFO_SysStatus.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/system_status.png').'" alt="system_status">
                  <span class="action-txt">Status</span>
                </a>
                <a id="PG_Notifications" class=" action-item" href="admin.php?page=Mensio_Admin_System_Notifications" title="'.MENSIO_PAGEINFO_SysLogs.'">
                  <img src="'.plugins_url('mensiopress/admin/icons/menu/system_logs.png').'" alt="system_logs">
                  <span class="action-txt">Logs</span>
                </a>';
        break;
    }
    return $Buttons;
  } 
  public function UpdatePage() {
    $this->MainPageTemplate = str_replace(
      '[---MENUITEMSPLACEHOLDER---]',
      $this->MainMenuItems,
      $this->MainPageTemplate
    );
    $this->MainPageTemplate = str_replace(
      '[---BUTTONPLACEHOLDER---]',
      $this->CustomMenuItems,
      $this->MainPageTemplate
    );
    $this->MainPageTemplate = str_replace(
      '[---MAINPLACEHOLDER---]',
      $this->MainPlaceHolder,
      $this->MainPageTemplate
    );
  }
  public function SetActiveSubPage($PageID,$SubPageID='') {
    $this->MainPageTemplate = str_replace(
      '<a id="PG_'.$PageID.'" class="',
      '<a id="PG_'.$PageID.'" class="item-selected ',
      $this->MainPageTemplate
    );
    $this->MainPageTemplate = str_replace(
      '<a id="PG_'.$SubPageID.'" class="',
      '<a id="PG_'.$SubPageID.'" class="item-selected ',
      $this->MainPageTemplate
    );
  }
  public function GetPage() {
      return $this->MainPageTemplate;
  }
  final public function VerifyPageIntegrity($Passphrase,$ref) {
    $IsCorrect = false;
    if (wp_verify_nonce( $Passphrase, 'Active_Page_'.$this->ActivePage )) {
      $ref = explode('page=',$ref);
      $ref = str_replace('Main','',str_replace('MensioAdmin', '', str_replace('_','',$ref[1])));
      switch($this->ActivePage) {
        case 'TimerCall':
          $IsCorrect = true;
          break;
        case 'DashBoard':
          if ($ref === 'DashBoard') { $IsCorrect = true; }
          break;
        case 'Products':
          if ($ref === 'Products') { $IsCorrect = true; }
          break;
        case 'Brands':
          if ($ref === 'ProductsBrands') { $IsCorrect = true; }
          break;
        case 'Categories':
          if ($ref === 'ProductsCategories') { $IsCorrect = true; }
          break;
        case 'Categories_Tree':
          if ($ref === 'ProductsCategoriesTree') { $IsCorrect = true; }
          break;
        case 'Relations':
          if ($ref === 'ProductsRelations') { $IsCorrect = true; }
          break;
        case 'Accounts':
          if ($ref === 'Customers') { $IsCorrect = true; }
          break;
        case 'MultiAccounts':
          if ($ref === 'Multiaccount') { $IsCorrect = true; }
          break;
        case 'Groups':
          if ($ref === 'GroupsCustomers') { $IsCorrect = true; }
          break;
        case 'Deleted':
          if ($ref === 'DeletedCustomers') { $IsCorrect = true; }
          break;
        case 'Orders':
          if ($ref === 'Orders') { $IsCorrect = true; }
          break;
        case 'Tickets':
          if ($ref === 'OrdersTickets') { $IsCorrect = true; }
          break;
        case 'Coupons':
          if ($ref === 'MarketingCoupons') { $IsCorrect = true; }
          break;
        case 'Discounts':
          if ($ref === 'OrdersDiscounts') { $IsCorrect = true; }
          break;
        case 'Report_Commerce':
          if ($ref === 'ReportsCommerce') { $IsCorrect = true; }
          break;
        case 'Report_Customers':
          if ($ref === 'ReportsCustomers') { $IsCorrect = true; }
          break;
        case 'Report_Activity':
          if ($ref === 'ReportsActivity') { $IsCorrect = true; }
          break;
        case 'Store_Settings':
          switch ($ref) {
            case 'SettingsStore':
            case 'SettingsUISettings':
            case 'SettingsDefaultLanguages':
            case 'SettingsSalesSettings':
            case 'SettingsMailSettings':
            case 'SettingsUserPermissions':
            case 'SettingsTermsOfService':
              $IsCorrect = true;
              break;
          }
          break;
        case 'Payment_Methods':
          if ($ref === 'SettingsPayments') { $IsCorrect = true; }
          break;
        case 'Product_Shipping':
          if ($ref === 'OrdersShipping') { $IsCorrect = true; }
          break;
        case 'Global_Attributes':
          if ($ref === 'ProductsDefaultAttributes') { $IsCorrect = true; }
          break;
        case 'Review_Ratings':
          if ($ref === 'SettingsRatings') { $IsCorrect = true; }
          break;
        case 'Continents':
          if ($ref === 'SettingsContinents') { $IsCorrect = true; }
          break;
        case 'Countries':
          if ($ref === 'SettingsCountries') { $IsCorrect = true; }
          break;
        case 'Countries_Regions':
          if ($ref === 'SettingsRegions') { $IsCorrect = true; }
          break;
        case 'Languages':
          if ($ref === 'SettingsLanguages') { $IsCorrect = true; }
          break;
        case 'Currency':
          if ($ref === 'SettingsCurrencies') { $IsCorrect = true; }
          break;
        case 'Business_Sectors':
          if ($ref === 'SettingsSectors') { $IsCorrect = true; }
          break;
        case 'Mensio_Logs':
          if ($ref === 'SystemLogs') { $IsCorrect = true; }
          break;
        case 'Mensio_Notifications':
          if ($ref === 'SystemNotifications') { $IsCorrect = true; }
          break;
        default:
          $IsCorrect = false;
          break;
      }
    }
    return $IsCorrect;
  }
  private function Get_Active_Store() {
    $ActiveStore = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'store';
    $Data = $wpdb->get_results($Query);
    foreach ($Data as $Row) {
      $ActiveStore = $Row->uuid;
    }
    return $ActiveStore;
  }
  protected function CreateModalWindow($ModalTitle, $ModalBody,$id='MnsModal') {
    $ModalForm = '
        <div class="ModalForm">
          <div class="modal-header">
            <div class="Mdl_Lbl_Title">
              '.$ModalTitle.'
            </div>
            <div id="CLS_'.$id.'" class="Mdl_Btn_Close">
              <i class="fa fa-times fa-2x" aria-hidden="true"></i>
            </div>
            <div class="DivResizer"></div>
          </div>
          <div class="modal-body">
            '.$ModalBody.'
            <div class="DivResizer"></div>
          </div>
          <div class="modal-footer">
            <div class="DivResizer"></div>
          </div>
        </div>';
    return $ModalForm;
  }
  protected function CreateNotificationDiv($Message) {
    $PopUp = '<div id="DIV_PopUp" class="Mns_PopUp">
              <div class="Mns_PopUp_Ctrl">
                <div id="Btn_PopUp_Close" class="button">
                  <i class="fa fa-times action-icon" aria-hidden="true"></i>
                </div>
              </div>
              <div class="Mns_PopUp_Info">
                '.$Message.'
              </div>
            </div>';
    return $PopUp;
  }
  public function CheckNotification() {
    $NumNotif = $this->LoadMensioLogs();
    $NotDiv = '<!--Top Right Menu-Text Alerts-->
        <div id="NTF_Orders" class="AlertIcon LeftIcon selected_alert_item" title="Orders">
          <div id="Mns_Orders_Notices" class="Notification_Number">'.$NumNotif['Orders'].'</div>
          <i class="fa fa-shopping-cart" aria-hidden="true"></i>
          <!-- <img src="'.plugins_url('mensiopress/admin/icons/ordersalert.png').'">
          <br> <span class="action-txt">Orders</span> --> 
        </div>
        <div id="NTF_Customers" class="AlertIcon LeftIcon selected_alert_item" title="Customers">
          <div id="Mns_Customers_Notices" class="Notification_Number">'.$NumNotif['Customers'].'</div>
          <i class="fa fa-user" aria-hidden="true"></i>
          <!-- <img src="'.plugins_url('mensiopress/admin/icons/custalert.png').'">
          <br> <span class="action-txt">Customers</span> --> 
        </div>
        <div id="NTF_Tickets" class="AlertIcon RightIcon selected_alert_item" title="Tickets">
          <div id="Mns_Tickets_Notices" class="Notification_Number">'.$NumNotif['Tickets'].'</div>
          <i class="fa fa-ticket" aria-hidden="true"></i>
          <!-- <img src="'.plugins_url('mensiopress/admin/icons/ticketsalert.png').'">
          <br> <span class="action-txt">Tickets</span> --> 
        </div>
        <div id="NTF_Info" class="AlertIcon RightIcon selected_alert_item" title="Info">
          <div id="Mns_Info_Notices" class="Notification_Number">'.$NumNotif['Info'].'</div>
          <i class="fa fa-info" aria-hidden="true"></i>
          <!-- <img src="'.plugins_url('mensiopress/admin/icons/infoalert.png').'">
          <br> <span class="action-txt">Info</span> --> 
        </div>
        <!--End of Top Right Menu Alerts-->';
    return $NotDiv;
  }
  private function LoadMensioLogs() {
    $Store = $this->Get_Active_Store();
    $NumNotif = array ('Orders'=>'','Customers'=>'','Tickets'=>'','Info'=>'','Mails'=>'');
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $MailNum = 0;
    if (MENSIO_FLAVOR !== 'FREE') {
      $Query = 'SELECT COUNT(*) as MailNum FROM '.$prfx.'customers_coupons WHERE informed = FALSE';
      $DataSet = $wpdb->get_results($Query);
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $MailNum = $Row->MailNum;
        }
      }
    }
    if ($MailNum > 0 ) {
      $SendOK = $this->SendCouponsToCustomers();
      if ($SendOK) {
        if ($MailNum > 10) { $NumNotif['Info'] += 10; }
          else { $NumNotif['Info'] += $MailNum; }
      }
    }
    return $NumNotif;
  }
  public function LoadOrdersLogs() {
    $PnlNot = '';
    $DataSet = $this->LoadMesioLogDataSet('Orders');
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $PnlNot .= '<div id="'.$Row->code.'" class="AlertItem">'.$Row->log.'</div>';
      }
    }
    $Panel = '
    <div id="OrdersPanel" class="SidePanelContent">
      <input type="hidden" id="SB_ActiveSideBar" value="Orders">
      <div class="NotifTitle">Orders</div>
      <div class="SPC_AlertsDiv">
        '.$PnlNot.'
      </div>
    </div>';
    return $Panel;
  }
  public function LoadCustomersLogs() {
    $PnlNot = '';
    $DataSet = $this->LoadMesioLogDataSet('Customers');
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $PnlNot .= '<div id="'.$Row->code.'" class="AlertItem">'.$Row->log.'</div>';
      }
    }
    $Panel = '
    <div id="CustomersPanel" class="SidePanelContent">
      <input type="hidden" id="SB_ActiveSideBar" value="Customers">
      <div class="NotifTitle">Customers</div>
      <div class="SPC_AlertsDiv">
        '.$PnlNot.'
      </div>
    </div>';
    return $Panel;
  }
  public function LoadTicketsLogs() {
    $PnlNot = '';
    $DataSet = $this->LoadMesioLogDataSet('Tickets');
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $PnlNot .= '<div id="'.$Row->code.'" class="AlertItem">'.$Row->log.'</div>';
      }
    }
    $Panel = '
    <div id="TicketsPanel" class="SidePanelContent">
      <input type="hidden" id="SB_ActiveSideBar" value="Tickets">
      <div class="NotifTitle">Tickets</div>
      <div class="SPC_AlertsDiv">
        '.$PnlNot.'
      </div>
    </div>';
    return $Panel;
  }
  public function LoadNotifications() {
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Store = $this->Get_Active_Store();
    $User = get_current_user_id();
    $Query = 'SELECT * FROM '.$prfx.'notifications WHERE note_store = "'.$Store.'"
      AND note_user = "'.$User.'" AND informed = FALSE';
    $DataSet = $wpdb->get_results($Query);
    $PnlNot = '';
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $PnlNot .= '<div id="'.$Row->code.'" class="AlertItem">'.$Row->notification.'</div>';
      }
    }
    $Panel = '
    <div id="OrdersPanel" class="SidePanelContent">
      <input type="hidden" id="SB_ActiveSideBar" value="Info">
      <div class="NotifTitle">Notifications</div>
      <div class="SPC_AlertsDiv Notif">
        '.$PnlNot.'
      </div>
    </div>';
    return $Panel;
  }
  protected function SetNotification($Type,$Message) {
    $Error = false;
    if ($Type === '') { $Error = true; }
    if ($Message === '') { $Error = true; }
    if (!$Error) {
      $Store = $this->Get_Active_Store();
      $User = get_current_user_id();
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      $Query = $wpdb->prepare(
        'INSERT INTO '.$prfx.'notifications
          (note_store, note_type, note_user,notification) VALUES (%s,%s,%s,%s)',
        $Store, $Type, $User, $Message
      );
      $wpdb->query($Query);
    }
  }
  public function UpdateNotification($NotCode) {
    $Check = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    if ($NotCode === 'ALL') {
      $Store = $this->Get_Active_Store();
      $User = get_current_user_id();     
      $Query = $wpdb->prepare(
        'UPDATE '.$prfx.'notifications SET informed = 1
          WHERE note_store = %s AND note_user = %s',
        $Store,
        $User
      );
      $Check = true;
    } else {
      $NotCode = intval($NotCode);
      if ($NotCode > 0) {
        $Check = true;
        $Query = $wpdb->prepare(
          'UPDATE '.$prfx.'notifications SET informed = 1 WHERE code = %d',
          $NotCode
        );
      }
    }
    if ($Check) { $wpdb->query($Query); }
    if ($NotCode !== 'ALL') {
      $Query = 'SELECT * FROM '.$prfx.'notifications WHERE code = "'.$NotCode.'"';
      $DataSet = $wpdb->get_results($Query);
      if (is_array($DataSet) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData = $this->CreateModalWindow('Notification', $Row->notification);
        }
      }
    }
    return $RtrnData;
  }
  public function UpdateLogs($Active,$Code) {
    $Error = true;
    $RtrnData = '';
    switch ($Active) {
      case 'Orders': case 'Tickets': case 'Customers':
      case 'Info':
        $Error = false;
        break;
    }
    if (!$Error) {
      $Check = false;
      global $wpdb;
      $prfx = $wpdb->prefix.'mns_';
      if ($Code === 'ALL') {
        $Check = true;
        $Store = $this->Get_Active_Store();
        $Query = $wpdb->prepare(
          'UPDATE '.$prfx.'mensiologs SET informed = 1 WHERE log_store = %s AND log_type = %s',
          $Store,
          $Active
        );
      } else {
        $Code = intval($Code);
        if ($Code > 0) {
          $Check = true;
          $Query = $wpdb->prepare(
            'UPDATE '.$prfx.'mensiologs SET informed = 1 WHERE code = %d',
            $Code
          );
        }
      }
      if ($Check) { $wpdb->query($Query); }
      if ($Code !== 'ALL') {
        $Query = 'SELECT * FROM '.$prfx.'mensiologs WHERE code = "'.$Code.'"';
        $DataSet = $wpdb->get_results($Query);
        if (is_array($DataSet) && (!empty($DataSet[0]))) {
          foreach ($DataSet as $Row) {
            $RtrnData = $this->CreateModalWindow('Log Info', $Row->log);
          }
        }
      }
    }
    return $RtrnData;
  }
  private function LoadMesioLogDataSet($Type) {
    $DataSet = array();
    switch ($Type) {
      case 'Orders': case 'Returns': case 'Customers': case 'Tickets': case 'Support':
        $Store = $this->Get_Active_Store();
        global $wpdb;
        $prfx = $wpdb->prefix.'mns_';
        $Query = 'SELECT * FROM '.$prfx.'mensiologs
          WHERE log_store = "'.$Store.'" AND log_type = "'.$Type.'"
          AND informed = FALSE ORDER BY log_date DESC';
        $DataSet = $wpdb->get_results($Query);
        break;
    }
    return $DataSet;
  }
  protected function LoadTableDefaultRows() {
    $Rows = 10;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'store';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) { $Rows = $Row->tblrows; }
    }
    return $Rows;
  }
  protected function LoadCurIcon() {
    $Icon = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT '.$prfx.'currencies_codes.*
      FROM '.$prfx.'currencies_codes, '.$prfx.'store
      WHERE '.$prfx.'currencies_codes.uuid = '.$prfx.'store.currency';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) { $Icon = $Row->symbol; }
    }
    return $Icon;
  }
  protected function LoadMetricIcon($Type) {
    $Metrics = '';
    $Icon = '';
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'store';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) { $Metrics = $Row->metrics; }
    }
    unset($DataSet);
    $Metrics = explode(';',$Metrics);
    switch ($Type) {
      case 'Color':
        $Metrics = explode(':',$Metrics[0]);
        break;
      case 'Height':
        $Metrics = explode(':',$Metrics[1]);
        break;
      case 'Length':
        $Metrics = explode(':',$Metrics[2]);
        break;
      case 'Size':
        $Metrics = explode(':',$Metrics[3]);
        break;
      case 'Volume':
        $Metrics = explode(':',$Metrics[4]);
        break;
      case 'Weight':
        $Metrics = explode(':',$Metrics[5]);
        break;
      case 'Width':
        $Metrics = explode(':',$Metrics[6]);
        break;
    }
    switch ($Metrics[1]) {
      case 'TXT':
      case 'NUM':
        $Icon = '';
        break;
      case 'HEX':
        $Icon = '(#)';
        break;
      case 'RGB':
        $Icon = '(rgb)';
        break;
      case 'MMT':
        $Icon = 'mm';
        break;
      case 'CMT':
        $Icon = 'cm';
        break;
      case 'MTR':
        $Icon = 'm';
        break;
      case 'INC':
        $Icon = 'in';
        break;
      case 'FOT':
        $Icon = 'ft';
        break;
      case 'YRD':
        $Icon = 'yd';
        break;
      case 'GAL':
        $Icon = 'gal';
        break;
      case 'LTR':
        $Icon = 'lt';
        break;
      case 'CFT':
        $Icon = 'cuft';
        break;
      case 'KLG':
        $Icon = 'kg';
        break;
      case 'GRM':
        $Icon = 'gr';
        break;
    }
    return $Icon;
  }
  public function LoadPopUpTimeOut() {
    $PopUp = 0;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT notiftime FROM '.$prfx.'store';
    $DataSet = $wpdb->get_results($Query);
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) { $PopUp = $Row->notiftime; }
    }
    return $PopUp;
  }
  private function SendCouponsToCustomers() {
    $MailsSend = false;
    $MailSettings = $this->LoadMailSettings();
    if (!is_numeric($MailSettings)) {
      $MailSettings = explode(';;',$MailSettings);
      $MailsSend = $this->SendSMTPCouponsMails($MailSettings);
    } else {
      $MailsSend = $this->SendCouponsMails($MailSettings);
    }
    return $MailsSend;
  }
  protected function LoadMailSettings() {
    $MailSettings = 1;
    $Settings = new mensio_store();
    $DataSet = $Settings->LoadStoreData();
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $MailSettings = $Row->mailsettings;
      }
    }
    return $MailSettings;
  }
  private function LoadCustomersMailingList($RecNo) {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT '.$prfx.'customers_coupons.*, '.$prfx.'coupons.title,
                '.$prfx.'coupons.message, '.$prfx.'contacts.value AS email,
                '.$prfx.'credentials.firstname, '.$prfx.'credentials.lastname
              FROM '.$prfx.'customers_coupons, '.$prfx.'coupons,
                '.$prfx.'credentials, '.$prfx.'contacts, '.$prfx.'contacts_type
              WHERE '.$prfx.'customers_coupons.coupon = '.$prfx.'coupons.uuid
              AND '.$prfx.'customers_coupons.customer = '.$prfx.'credentials.uuid
              AND '.$prfx.'credentials.uuid = '.$prfx.'contacts.credential
              AND '.$prfx.'contacts.type = '.$prfx.'contacts_type.uuid
              AND '.$prfx.'contacts_type.name = "E-Mail"
              AND '.$prfx.'customers_coupons.informed = FALSE LIMIT '.$RecNo;
    $DataSet = $wpdb->get_results($Query);
    return $DataSet;
  }
  private function UpdateInformedCustomer($RecID) {
    $JobDone = false;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'UPDATE '.$prfx.'customers_coupons SET informed = TRUE WHERE uuid = "'.$RecID.'"';
    if (false !== $wpdb->query($Query)) { $JobDone = true; }
    return $JobDone;
  }
  private function SendCouponsMails($RecNo) {
    $MailsSend = false;
    $Message = '';
    $Customers = $this->LoadCustomersMailingList($RecNo);
    if ((is_array($Customers)) && (!empty($Customers[0]))) {
      foreach ($Customers as $Row) {
        $body = str_replace('[%LASTNAME%]', $Row->lastname, stripslashes($Row->message));
        $body = str_replace('[%FIRSTNAME%]', $Row->firstname, $body);
        $body = str_replace('[%CPNKEY%]', $Row->cpnkey, $body);
        $headers = array('Content-Type: text/html; charset=UTF-8');
        if (wp_mail( $Row->email, $Row->title, $body, $headers)) {
          if ($this->UpdateInformedCustomer($Row->uuid)) {
            $MailsSend = true;
            $Message .= 'E-Mail was send at '.$Row->email.' for user '.$Row->lastname.' '.$Row->firstname.'<br>';
          }
        } else {
          $Message .= 'E-Mail could not be send at '.$Row->email.' for user '.$Row->lastname.' '.$Row->firstname.'<br>';
        }
      }
    }
    unset($Customers);
    $this->SetNotification('Info',$Message);
    return $MailsSend;
  }
  private function SendSMTPCouponsMails($SMTPSettings) {
    $MailsSend = false;
    $Message = '';
    $From = '';
    $FromName = '';
    $RecNo = 1;
    global $phpmailer; // define the global variable
    if ( !is_object( $phpmailer ) || !is_a( $phpmailer, 'PHPMailer' ) ) { // check if $phpmailer object of class PHPMailer exists
      require_once ABSPATH . WPINC . '/class-phpmailer.php';
      require_once ABSPATH . WPINC . '/class-smtp.php';
      $phpmailer = new PHPMailer( true );
    }
    $phpmailer->CharSet = 'UTF-8';
    $phpmailer->isSMTP(); // Set mailer to use SMTP
    foreach ($SMTPSettings as $Setting) {
      $Setting = explode(':',$Setting);
      switch ($Setting[0]) {
        case 'Host': // Specify main and backup SMTP servers
          $phpmailer->Host = $Setting[1];
          break;
        case 'SMTPAuth': // Enable SMTP authentication
          if ($Setting[1] === '1') { $phpmailer->SMTPAuth = true; }
            else { $phpmailer->SMTPAuth = false; }
          break;
        case 'SMTPSecure':  // Enable encryption, `ssl` and 'tls' also accepted
          $phpmailer->SMTPSecure = $Setting[1];
          break;
        case 'Port': // TCP port to connect to
          $phpmailer->Port = $Setting[1];
          break;
        case 'Username': // SMTP username
          $phpmailer->Username = $Setting[1];
          break;
        case 'Password': // SMTP password
          $phpmailer->Password = $Setting[1];
          break;
        case 'From':
          $From = $Setting[1];
          break;
        case 'FromName':
          $FromName = $Setting[1];
          break;
        case 'MailsPerMinute':
         $RecNo = $Setting[1];
         break;
      }
    }
    if (($From !== '') && ($FromName !== '')) {
      $phpmailer->setFrom($From,$FromName);
    } else {
      $Usr = wp_get_current_user();
      $phpmailer->setFrom($Usr->user_email,$Usr->user_nicename);
    }
    $Customers = $this->LoadCustomersMailingList($RecNo);
    if ((is_array($Customers)) && (!empty($Customers[0]))) {
      foreach ($Customers as $Row) {
        $body = str_replace('[%LASTNAME%]', $Row->lastname, stripslashes($Row->message));
        $body = str_replace('[%FIRSTNAME%]', $Row->firstname, $body);
        $body = str_replace('[%CPNKEY%]', $Row->cpnkey, $body);
        $phpmailer->ClearAllRecipients( ); // clear all
        $phpmailer->addAddress($Row->email, $Row->lastname.' '.$Row->firstname);
        $phpmailer->isHTML(true);
        $phpmailer->Subject = $Row->title;
        $phpmailer->Body    = $body;
        $phpmailer->AltBody = wp_strip_all_tags($body);
        if ($phpmailer->send()) {
          if ($this->UpdateInformedCustomer($Row->uuid)) {
            $MailsSend = true;
            $Message .= 'E-Mail was send at '.$Row->email.' for user '.$Row->lastname.' '.$Row->firstname.'<br>';
          }
        } else {
          $Message .= 'E-Mail could not be send '.$Row->email.' for user '.$Row->lastname.' '.$Row->firstname.'<br>';
        }
      }
    }
    unset($Customers);
    $this->SetNotification('Info',$Message);
    return $MailsSend;
  }
  protected function DefaultMailTemplate($Name) {
    $Template = array(
      'GeneralMail' => '<html>
            <head>
            </head>
            <body>
              [%STORELOGO%]
              <span style="width: 50px; font-weight: bold;">From: </span> [%STORENAME%] [%STOREMAIL%]<br>
              [%GENERALMAIL%]
            </body>
            </html>',
      'Sales'=>'<html>
            <head>
            </head>
            <body>
              [%STORELOGO%]
              <span style="width: 50px; font-weight: bold;">From: </span> [%STORENAME%] [%STOREMAIL%]<br>
              <span style="width: 50px; font-weight: bold;">Invoice for order: </span> [%ORDERNUMBER%]<br>
              [%ORDERLIST%]<br>
            </body>
            </html>',
      'Status'=>'<html>
          <head>
          </head>
          <body>
            [%STORELOGO%]
            <span style="display: block;width: 50px;">To:</span> [%TITLE%] [%LASTNAME%] [%FIRSTNAME%]
            <br>
              <p>We inform you that your order with number [%ORDERNUMBER%] was change status [%STATUSNAME%]</p>
              [%STATUSTEXT%]
              <p>For more information please visit our page and login with your credentials</p>
            <br>
          </body>
          </html>',
      'Ticket'=>'<html>
          <head>
          </head>
          <body>
            [%STORELOGO%]
            <span style="width: 50px; font-weight: bold;">From: </span> [%STORENAME%] [%STOREMAIL%]<br>
            <span style="width: 50px; font-weight: bold;">Update Ticket: </span> [%TICKETCODE%]<br>
            <br>
            [%REPLYTEXT%]
          </body>
          </html>',
      'Register'=>'<html>
          <head>
          </head>
          <body>
            [%STORELOGO%]
            <span style="width: 50px; font-weight: bold;">From: </span> [%STORENAME%] [%STOREMAIL%]<br>
            <span style="width: 50px; font-weight: bold;">Registration confirmation: </span> <a href="[%REGISTERCONFIRM%]"><br>
          </body>
          </html>',
        'PswdConfirm'=>'<html>
          <head>
          </head>
          <body>
            [%STORELOGO%]
            <span style="width: 50px; font-weight: bold;">From: </span> [%STORENAME%] [%STOREMAIL%]<br>
            <p>Your Password was changed Please confirm the new password with the codes below</p>
            <span style="width: 50px; font-weight: bold;">Code: </span> [%FORGOTPASSWORDCODE%]<br>
            <span style="width: 50px; font-weight: bold;">Password: </span> [%FORGOTPASSWORDPASS%]<br>
            <span style="width: 50px; font-weight: bold;">Link: </span> <a href="[%FORGOTPASSWORDLINK%]"><br>
          </body>
          </html>'
    );
    return $Template[$Name];
  }
}
