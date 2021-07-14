<?php
/**
 * Plugin Name:       Delivery Validation TSW
 * Plugin URI:        http://themes.tradesouthwest.com/wordpress/plugins/
 * Description:       Set validation for checkout module specific dates
 * Version:           1.0.2
 * Author:            Larry Judd
 * Author URI:        https://codeable.io/developers/larry-judd/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       delivery-validation-tsw
 * Domain Path:       /languages
 *
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//activate/deactivate hooks
function delivery_validation_tsw_plugin_activation() {
  // Check for WooCommerce
  if (!class_exists('WooCommerce')) {
	echo('<div class="error">
	<p>This plugin requires that WooCommerce is installed and activated.</p>
	</div></div>');
	return;
  }
}
function delivery_validation_tsw_plugin_deactivation() {
        return false;
}
/**
 * InitialiZe - load in translations
 * @since 1.0.0
 */
function delivery_validation_tsw_loadtranslations () {
    $plugin_dir = basename(dirname(__FILE__)).'/languages';
    load_plugin_textdomain( 'delivery-validation-tsw', false, $plugin_dir );
}
add_action('plugins_loaded', 'delivery_validation_tsw_loadtranslations');

//activate and deactivate registered
register_activation_hook( __FILE__, 'delivery_validation_tsw_plugin_activation');
register_deactivation_hook( __FILE__, 'delivery_validation_tsw_plugin_deactivation');

/**
 * Plugin Scripts
 *
 * Register and Enqueues plugin helper scripts
 *
 * @since 1.0.0 
 */
//if ( !function_exists('delivery_validation_tsw_addplugin_scripts') ) :  
function delivery_validation_tsw_addplugin_scripts() 
{
    $ver      = "1.0.2";
    // Register Scripts
    wp_register_script( 'delivery-update-cart-items', 
					   plugins_url('js/delivery-update-cart-items-ajax.js', __FILE__ ), 
					   array('jquery'), $ver, true );
   
    wp_enqueue_script( 'delivery-update-cart-items' );
     
}
//endif;
// initiate hooks
//add_action( 'wp_enqueue_scripts', 'delivery_validation_tsw_addplugin_scripts');
require ( plugin_dir_path( __FILE__ ) . 'admin/delivery-validation-tsw-admin.php' ); 
require ( plugin_dir_path( __FILE__ ) . 'inc/delivery-validation-tsw-metadata.php' ); 
require ( plugin_dir_path( __FILE__ ) . 'inc/delivery-validation-tsw-public.php' ); 
add_action( 'wp_footer', 'delivery_validation_tsw_footer_scripts' );
?>