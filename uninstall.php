<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
class mensio_uninstaller {
	public function __construct() {
    if ((!defined('WPINC')) || (!current_user_can('manage_options'))) { die(); }
	}
  final public function MensioUnistallTables() {
    $dir = MENSIO_UPLOAD_DIR;
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()){
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
    rmdir($dir);
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'DELETE FROM '.$prfx.'posts WHERE post_type LIKE "%mensio%"';
    $wpdb->query($Query);
    $Query = 'DELETE FROM '.$prfx.'options WHERE meta_key LIKE "%mensio%"';
    $wpdb->query($Query);
    $Query = 'DELETE FROM '.$prfx.'options WHERE option_name LIKE "%mensio%"';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'store';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'notifications';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'mensiologs';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_users_permissions';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_slugs';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_mails';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_terms';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_backups';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'languages_codes';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'languages_names';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'continents_codes';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'continents_names';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'countries_codes';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'countries_names';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'currencies_codes';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'currencies_names';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'sectors_codes';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'sectors_names';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'regions_types';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'regions_codes';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'regions_names';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_payment_type';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_payment';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_payment_descriptions';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_payment_delivery';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_payment_gateways';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'store_payment_bank';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'addresses_type';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'addresses';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'contacts_type';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'contacts';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_types';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'credentials';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'companies';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_verification';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'coupons';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_coupons';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'groups';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'groups_customers';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_history';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_history_pages';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_lists';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_tickets';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_tickets_history';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'customers_tickets_orders';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'attributes';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'attributes_names';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'attributes_values';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'barcodes';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'brands';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'brands_names';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'categories_codes';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'categories_names';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'categories_tree';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'files_types';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_advantages';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_attributes';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_barcodes';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_bundles';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_categories';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_descriptions';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_files';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_images';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'relations_types';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_relations';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_status';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_status_descriptions';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_stock_status';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_stock_status_descriptions';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_tags';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'products_variations';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'ratings_types';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'reviews';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'reviews_replies';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'couriers_type';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'discounts';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'discounts_categories';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'discounts_customers';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_discounts';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_payment';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_products';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_shipping';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_split_relations';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_status';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_status_type';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'returns_products';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'orders_returns';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'returns_status';
    $wpdb->query($Query);
    $Query = 'DROP TABLE IF EXISTS '.$prfx.'returns_status_type';
    $wpdb->query($Query);
  }
}
$Uninstaller = new mensio_uninstaller();
$Uninstaller->MensioUnistallTables();
unset($Uninstaller);