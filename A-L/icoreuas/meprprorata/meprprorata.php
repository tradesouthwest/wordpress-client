<?php
/**
 * Plugin Name: MeprProrata
 * Plugin URI: https://themes.tradesouthwest.com/plugins
 * Description: Add math for prorata and expired 30 days fee Requires MemberPress. Options under Tools > MeprProrata.
 * Version: 1.0.2
 * Author: Larry Judd Tradesouthwest
 * Author URI: https://tradesouthwest.com
 *
 * Text Domain: meprprorata
 * Domain Path: /languages/
 *
 * Requires at least: 4.5
 * Tested up to: 4.9
 *
 * License: GPL v3 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-3.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Important constants
 *
 * @since   1.0.1
 *
 * @version - reserved
 * @plugin_url
 * @text_domain - reserved
 *
 */
/* if (!defined('MEPRPRORATA_VER')) { define('MEPRPRORATA_VER', '1.0.0'); } 
if (!defined('MEPRPRORATA_URL')) { define( 'MEPRPRORATA_URL', 
                                  plugin_dir_url(__FILE__) ); }
*/
//activate/deactivate hooks
function meprprorata_plugin_activation() {

/*  register_uninstall_hook( __FILE__, 'woonumdays_uninstall' );
  */
}

function meprprorata_plugin_deactivation() {

    //_deregister_shortcode();
        return false;
}
//if( is_plugin_active('memberpress/memberpress.php') ) 
//activate and deactivate registered
register_activation_hook( __FILE__, 'meprprorata_plugin_activation');
register_deactivation_hook( __FILE__, 'meprprorata_plugin_deactivation');

//enqueue script
function meprprorata_addtosite_scripts() {
    // Register Scripts
    wp_register_script( 'meprprorata-plugin', 
       plugins_url( 'lib/meprprorata-plugin.js', __FILE__ ), 
       array( 'jquery' ), true );
    
    //wp_enqueue_script( 'meprprorata-plugin' );
     
}
//add_action( 'wp_enqueue_scripts', 'meprprorata_addtosite_scripts' );
//load admin scripts as well
//add_action( 'admin_init', 'meprprorata_addtosite_scripts' );

//include admin and public views
require ( plugin_dir_path( __FILE__ ) . 'inc/meprprorata-adminpage.php' ); 
require ( plugin_dir_path( __FILE__ ) . 'inc/meprprorata-prorate.php' );
require ( plugin_dir_path( __FILE__ ) . 'inc/meprprorata-functions.php' ); 
?>