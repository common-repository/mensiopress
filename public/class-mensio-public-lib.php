<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/cls/mensio-public-display.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/cls/mensio-template-page-objects.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/list.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/new-products.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/product.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/login.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/signup.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/search.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/offers.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/brand.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/categories.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/category.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/contact.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/cart.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/user.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/ratings.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/tos.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/objects/checkout.php';
add_shortcode( 'mns_blank', 'mensiopress_blank' );
add_shortcode( 'mns_top_brands', 'mensiopress_get_top_brands' );
add_shortcode( 'mns_product_categories', 'mensiopress_get_product_categories' );
add_shortcode( 'mns_product_recommendations', 'mensiopress_product_recommendations' );
add_shortcode( 'mns_category_products', 'mensiopress_category_products' );
add_shortcode( 'mns_text_block', 'mnsTextBlock' );
add_shortcode( 'mns_html_block', 'mnsCustomHTML' );
add_shortcode( 'mensioobject', 'MensioObject' );
add_shortcode( 'mensiopresshomepage', 'MensioHomepage' );