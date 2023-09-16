<?php
/**
 * Plugin Name:       Extend QuickCab
 * Plugin URI:        http://themes.tradesouthwest.com/wordpress/plugins/
 * Description:       Extended functionality for QuickCab Booking plugin by Larry
 * Author:            tradesouthwestgmailcom
 * Author URI:        https://tradesouthwest.com
 * Version:           1.0.2
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 4.5
 * Tested up to:      5.3.1
 * Requires PHP:      5.4
 * Text Domain:       extend-quickcab
 * Domain Path:       /languages
*/

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {	exit; }
/** 
 * Constants
 * 
 * @param EXTEND_QUICKCAB_VER         Using bumped ver.
 * @param EXTEND_QUICKCAB_URL         Base path
 * @since 1.0.0 
 */
if( !defined( 'EXTEND_QUICKCAB_VER' )) { define( 'EXTEND_QUICKCAB_VER', time() ); }
if( !defined( 'EXTEND_QUICKCAB_URL' )) { define( 'EXTEND_QUICKCAB_URL', 
    plugin_dir_url(__FILE__)); }

    // Start the plugin when it is loaded.
    register_activation_hook(   __FILE__, 'extend_quickcab_plugin_activation' );
    register_deactivation_hook( __FILE__, 'extend_quickcab_plugin_deactivation' );
  
/**
 * Activate/deactivate hooks
 * 
 */
function extend_quickcab_plugin_activation() 
{

    return false;
}
function extend_quickcab_plugin_deactivation() 
{
    return false;
} 

/** 
 * Admin side specific
 *
 * Enqueue admin only scripts 
 */ 
add_action( 'admin_enqueue_scripts', 'extend_quickcab_load_admin_scripts' );   
function extend_quickcab_load_admin_scripts() 
{
    /*
     * Enqueue styles */
    wp_enqueue_style( 'extend-quickcab-admin',  
        plugin_dir_url(__FILE__) . 'css/quickcab-extend-admin.css',
        array(), 
        EXTEND_QUICKCAB_VER, 
        false 
    );

}

/**
 * Plugin Scripts
 *
 * Register and Enqueues plugin scripts
 *
 * @since 1.0.0
 */
function extend_quickcab_addtosite_scripts()
{
    wp_enqueue_style( 'extend-quickcab-public',  
        plugin_dir_url(__FILE__) . 'css/quickcab-extend-public.css',
        array(), 
        EXTEND_QUICKCAB_VER, 
        false 
    );
        
    wp_enqueue_script( 'extend-quickcab-front', 
        plugin_dir_url( __FILE__ ) . 'js/quickcab-extend-form.js', 
        array( ), 
        EXTEND_QUICKCAB_VER, 
        true 
    ); 
}
add_action( 'wp_enqueue_scripts', 'extend_quickcab_addtosite_scripts' );

/**
 * Define the locale for this plugin for internationalization.
 * Set the domain and register the hook with WordPress.
 *
 * @uses slug `extend-quickcab`
 */
//add_action( 'plugins_loaded', 'extend_quickcab_load_plugin_textdomain' );
     
function extend_quickcab_load_plugin_textdomain() 
{

    $plugin_dir = basename( dirname(__FILE__) ) .'/languages';
                  load_plugin_textdomain( 'extend-quiickcab', false, $plugin_dir );
}

require_once ( plugin_dir_path(__FILE__) . 'inc/extend-quickcab-functions.php' );
require_once ( plugin_dir_path(__FILE__) . 'inc/extend-quickcab-admin.php' );
