<?php
 /**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.mensiopress.org
 * @since             1.0.0
 * @package           mensiopress
 *
 * @wordpress-plugin
 * Plugin Name:       Mensiopress
 * Plugin URI:        http://www.mensiopress.org
 * Description:       E-Commerce System for WordPress. Please read the README file for more information.
 * Version:           1.0.0
 * Author:            Protocol Digital Marketing, Athens-Greece
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mensiopress
 * Domain Path:       /languages
 */
if ( ! defined( 'WPINC' ) ) {	die; }
class TCPDF_Loader {
	public static function init() {
		add_action( 'activated_plugin', array( __CLASS__, 'load_tcpdf_first' ) );
		if ( ! class_exists( 'TCPDF' ) ) {
			define( 'TCPDF_PLUGIN_ACTIVE', true );
			define( 'TCPDF_VERSION', '6.2.11' );
			require( dirname( __FILE__ ) . '/includes/tcpdf/tcpdf.php' );
		}
	}
	public static function load_tcpdf_first() {
		$path            = __FILE__;
		$path            = str_replace( trailingslashit( WP_PLUGIN_DIR ), '', $path );
		$path            = str_replace( WP_CONTENT_DIR . '/mu-plugins/', '', $path );
		$active_plugins  = get_option( 'active_plugins' );
		$this_plugin_key = array_search( $path, $active_plugins );
		if ( $this_plugin_key ) { // if it's 0 it's the first plugin already, no need to continue
			array_splice( $active_plugins, $this_plugin_key, 1 );
			array_unshift( $active_plugins, $path );
			update_option( 'active_plugins', $active_plugins );
		}
	}
}
TCPDF_Loader::init();
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');
define('MENSIO_PLGTITLE','MENSIOPRESS');
define('MENSIO_FLAVOR','FREE');
define('MENSIO_VERSION','1.0.0');
define('MENSIO_PATH',plugins_url().'/mensiopress');
define('MENSIO_SHORTPATH', str_replace(home_url().'/','',MENSIO_PATH));
$UploadDir = wp_upload_dir(); //basedir
define('MENSIO_UPLOAD_DIR',$UploadDir['baseurl'].'/mensiopress');
function Add_Mensio_Footer() {
  global $wp_version;
  echo '</p>
<div id="MENSIOFootBar" class="">
    <div class="Mns_FooterBar_Version">
      <span>WP: '.$wp_version.'</span> / <span>MP: '.MENSIO_VERSION.'</span>
    </div>
</div><p>';
}
require plugin_dir_path( __FILE__ ) . 'includes/class-mensio.php';
function run_mensio() {
	$mensio = new mensio();
	$mensio->run();
}
run_mensio();
