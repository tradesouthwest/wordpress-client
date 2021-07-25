<?php
/**
 * Plugin Name:       Booking Validation TSW
 * Plugin URI:        http://themes.tradesouthwest.com/wordpress/plugins/
 * Description:       Set validation for checkout module specific
 * Version:           1.0.0
 * Author:            Larry Judd
 * Author URI:        https://codeable.io/developers/larry-judd/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       booking-validation-tsw
 * Domain Path:       /languages
 *
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//activate/deactivate hooks
function booking_validation_tsw_plugin_activation() {
  // Check for WooCommerce
  if (!class_exists('WooCommerce')) {
	echo('<div class="error">
	<p>This plugin requires that WooCommerce is installed and activated.</p>
	</div></div>');
	return;
  }
}
function booking_validation_tsw_plugin_deactivation() {
        return false;
}
/**
 * InitialiZe - load in translations
 * @since 1.0.0
 */
function booking_validation_tsw_loadtranslations () {
    $plugin_dir = basename(dirname(__FILE__)).'/languages';
    load_plugin_textdomain( 'booking-validation-tsw', false, $plugin_dir );
}
add_action('plugins_loaded', 'booking_validation_tsw_loadtranslations');

//activate and deactivate registered
register_activation_hook( __FILE__, 'booking_validation_tsw_plugin_activation');
register_deactivation_hook( __FILE__, 'booking_validation_tsw_plugin_deactivation');

/**
 * Plugin Scripts
 *
 * Register and Enqueues plugin helper scripts
 *
 * @since 1.0.0 
 */
if ( !function_exists('booking_validation_tsw_addplugin_scripts') ) :  
function booking_validation_tsw_addplugin_scripts() 
{
    $ver      = "1.0.0";
    // Register Scripts
    wp_register_script( 'booking-update-cart-items', 
					   plugins_url('js/booking-update-cart-items-ajax.js', __FILE__ ), 
					   array('jquery'), $ver, true );
   
    wp_enqueue_script( 'booking-update-cart-items' );
     
}
endif;
/** 
 * Admin side specific
 *
 * Enqueue admin only scripts 
 */ 

function booking_validation_tsw_load_admin_scripts() 
{
    /*
     * Enqueue styles */
    wp_enqueue_style( 'booking-validation-tsw-admin', 
                         plugins_url('css/plugin-admin.css', __FILE__), 
                        array(), '1.0.0', false 
                        );
}

include( plugin_dir_path( __FILE__ ) . 'inc/booking-validation-tsw-public.php' ); 
//include( plugin_dir_path( __FILE__ ) . 'admin/booking-validation-tsw-admin.php' ); 
// initiate hooks
add_action( 'wp_enqueue_scripts', 'booking_validation_tsw_addplugin_scripts');
//add_action( 'admin_enqueue_scripts', 'booking_validation_tsw_load_admin_scripts' );   
?>
