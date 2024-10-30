<?php
if (!defined('WPINC')) { die(); }
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/core/tfpdf.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/core/mensio-datatable.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/core/mensio-core-db.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/core/mensio-core-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/core/mensio-core-functions.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/settings/mensio-store.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/settings/mensio-store-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/settings/mensio-default-attributes.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/settings/mensio-default-attributes-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/settings/mensio-settings-shipping.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/settings/mensio-settings-shipping-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/settings/mensio-products-ratings.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/settings/mensio-products-ratings-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/settings/mensio-payment-methods.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/settings/mensio-payment-methods-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/customers/mensio-customers.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/customers/mensio-customers-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/customers/mensio-deleted-customers-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/customers/mensio-multiaccount-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/products/mensio-products.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/products/mensio-products-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/products/mensio-products-brands.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/products/mensio-products-brands-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/products/mensio-products-categories.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/products/mensio-products-categories-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/products/mensio-products-categories-tree.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/products/mensio-products-reviews.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/products/mensio-products-reviews-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/orders/mensio-orders-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/orders/mensio-orders.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/orders/mensio-orders-returns-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/orders/mensio-orders-returns.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/orders/mensio-admin-tickets-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/orders/mensio-admin-tickets.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/dashboard/mensio-dashboard-form-'.strtolower(MENSIO_FLAVOR).'.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/dashboard/mensio-dashboard.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-languages.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-languages-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-currencies.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-currencies-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-continents.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-continents-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-countries.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-countries-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-regions.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-regions-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-sectors.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-sectors-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-notifications.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-logs.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-notifications-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-logs-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-system.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-system-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-status.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/system/mensio-status-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/settings/mensio-admin-settings-store.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/settings/mensio-admin-settings-uisettings.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/settings/mensio-admin-settings-default-languages.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/settings/mensio-admin-settings-sales.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/settings/mensio-admin-settings-mail.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/settings/mensio-admin-settings-permissions.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/settings/mensio-admin-settings-terms.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/settings/mensio-admin-settings-default-attributes.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/settings/mensio-admin-settings-shipping.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/settings/mensio-admin-settings-ratings.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/settings/mensio-admin-settings-payment-methods.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/customers/mensio-admin-customers.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/customers/mensio-admin-multiaccount.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/customers/mensio-admin-deleted-customers.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/products/mensio-admin-products.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/products/mensio-admin-products-categories.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/products/mensio-admin-products-categories-tree.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/products/mensio-admin-products-brands.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/products/mensio-admin-products-reviews.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/orders/mensio-admin-orders.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/orders/mensio-admin-orders-returns.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/orders/mensio-admin-orders-tickets.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/dashboard/mensio-admin-dashboard.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/system/mensio-admin-languages.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/system/mensio-admin-currencies.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/system/mensio-admin-continents.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/system/mensio-admin-countries.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/system/mensio-admin-regions.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/system/mensio-admin-sectors.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/system/mensio-admin-system.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/system/mensio-admin-status.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/system/mensio-admin-system-notifications.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/system/mensio-admin-system-logs.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/css/skins.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/pages/mensio-design-pages-form-flavored.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/pages/mensio-design-pages.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cls/pages/mensio-design-pages-form.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/forms/pages/mensio-design-pages.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/cls/mensio-eurobank-page.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/cls/mensio_seller.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/cls/mensio_seller_gateways.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/shopping-cart.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/comparison-list.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/favorites.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/language-selection.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/brand-selection.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/category-selection.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/widgets/user.php';
function MensioEncodeUUID($ID){
    if(!$ID){
        return false;
    }
    if(is_array($ID)){
        $IDS=array();
        foreach($ID as $id){
            $Get=explode("-",$id);
            $IDS=$Get[2].$Get[1].$Get[0].$Get[3].$Get[4];
        }
        return $IDS;
    }
    $Get=explode("-",$ID);
    $ID=$Get[2].$Get[1].$Get[0].$Get[3].$Get[4];
    return $ID;
}
function MensioDecodeUUID($ID){
    if(is_array($ID)){
        $IDS=array();
        foreach($ID as $id){
            $fourth=substr($id,16,4);
            $fifth=substr($id,20);
            $third=substr($id,0,4);
            $second=substr($id,4,4);
            $first=substr($id,8,8);
            $IDS[]=$first."-".$second."-".$third."-".$fourth."-".$fifth;
        }
        return $IDS;
    }
    $fourth=substr($ID,16,4);
    $fifth=substr($ID,20);
    $third=substr($ID,0,4);
    $second=substr($ID,4,4);
    $first=substr($ID,8,8);
    return $first."-".$second."-".$third."-".$fourth."-".$fifth;
}
