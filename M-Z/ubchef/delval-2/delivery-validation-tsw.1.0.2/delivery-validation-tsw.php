<?php
/**
 * Plugin Name:       Delivery Validation TSW
 * Plugin URI:        http://themes.tradesouthwest.com/wordpress/plugins/
 * Description:       Set validation for checkout module specific dates
 * Version:           1.0.21
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
if (!defined('DELVALTSW_URL')) { define( 'DELVALTSW_URL', plugin_dir_url(__FILE__)); }
//activate/deactivate hooks
function delivery_validation_tsw_plugin_activation() {
  // Check for WooCommerce
  if (!class_exists('WooCommerce')) {
	echo('<div class="error">
	<p>This plugin requires that WooCommerce is installed and activated.</p>
	</div>');
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
add_action( 'wp_enqueue_scripts', 'delivery_validation_tsw_addplugin_scripts', 20);
function delivery_validation_tsw_addplugin_scripts() 
{
    $ver      = time();
    // Register Scripts
    wp_register_script( 'delval-update-cart-items', 
					   plugins_url('js/delval-update-cart-items.js', __FILE__ ), 
					   array('jquery'), $ver, false );
   
    wp_enqueue_script( 'delval-update-cart-items' );
  /* wp_localize_script( 
        'delval-update-cart-items', 
        'delval-update-cart-items', 
        array( 
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'action' => 'delval_ajaxfdoe_add' 
        )
    ); */
    //wp_enqueue_script('wc-add-to-cart-variation');
     /* 
    wp_register_script( 'delvaltsw-xmas', 
					   DELVALTSW_URL . 'js/delvaltsw-xmas.js', 
					   array('jquery'), $ver, false ); */
    //wp_enqueue_script( 'delvaltsw-xmas' ); 

}
//add_action( 'wp_ajax_delval_ajaxfdoe_add', 'delval_ajaxfdoe_add' );
//add_action( 'wp_ajax_nopriv_delval_ajaxfdoe_add', 'delval_ajaxfdoe_add' );
// initiate hooks
require ( plugin_dir_path( __FILE__ ) . 'admin/delivery-validation-tsw-admin.php' ); 
//require ( plugin_dir_path( __FILE__ ) . 'inc/delval-ajax.php' ); 
require ( plugin_dir_path( __FILE__ ) . 'inc/delivery-validation-tsw-metadata.php' ); 
require ( plugin_dir_path( __FILE__ ) . 'inc/delivery-validation-tsw-public.php' ); 
add_action( 'wp_footer', 'delivery_validation_tsw_footer_scripts' ); 
?>