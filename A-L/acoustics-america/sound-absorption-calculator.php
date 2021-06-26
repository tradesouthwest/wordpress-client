<?php
/**
 * Plugin Name:       Sound Absorption Calculator
 * Plugin URI:        http://themes.tradesouthwest.com/wordpress/plugins/mixmat
 * Description:       A NRC is an average rating of how much sound an acoustic product can absorb.
 * Version:           1.0.0
 * Author:            Larry Judd
 * Author URI:        http://tradesouthwest.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sound-absorption-calc
 * Domain Path:       na using woocommerce i18n
 * @wordpress-plugin  wpdb = 
 * @link              http://tradesouthwest.com
 * @package           Sound_Absorption_Calc
 *
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** 
 * Important constants
 *
 * @since   1.0.1
 * @constant plugin_url
 *
 */
if (!defined('SOUND_ABSORPTION_CALC_URL')) { 
    define( 'SOUND_ABSORPTION_CALC_URL', plugin_dir_url(__FILE__)); 
}
//activate/deactivate hooks
function sound_absorption_calc_plugin_activation() {
  // Check for WooCommerce
  if (!class_exists('WooCommerce')) {
	echo('<div class="error">
	<p>This plugin requires that WooCommerce is installed and activated.</p>
	</div></div>');
	return;
  }
}
function sound_absorption_calc_plugin_deactivation() {
        return false;
}
/**
 * InitialiZe - load in translations
 * @since 1.0.0
 */
function sound_absorption_calc_loadtranslations () {
    $plugin_dir = basename(dirname(__FILE__)).'/languages';
    load_plugin_textdomain( 'sound-absorption-calc', false, $plugin_dir );
}
add_action('plugins_loaded', 'sound_absorption_calc_loadtranslations');
/**
 * Plugin Scripts
 *
 * Register and Enqueues plugin scripts
 *
 * @since 1.0.0
 */
//activate and deactivate registered
register_activation_hook( __FILE__, 'sound_absorption_calc_plugin_activation');
register_deactivation_hook( __FILE__, 'sound_absorption_calc_plugin_deactivation');

//include admin and public views
require ( plugin_dir_path( __FILE__ ) . 'admin/sound-absorption-calc-admin.php' ); 
require ( plugin_dir_path( __FILE__ ) . 'inc/sound-absorption-calc-calculate.php' ); 
require ( plugin_dir_path( __FILE__ ) . 'inc/sound-absorption-helpers.php' ); 
// initiate hooks
add_action( 'wp_enqueue_scripts', 'sound_absorption_calc_addtosite_scripts' );
?>