<?php
/**
 * Plugin Name:       Views Addon
 * Plugin URI:        http://themes.tradesouthwest.com/wordpress/plugins/
 * Description:       TSW assigns judges custom plugin. Opens in Settings > TSW Views Addon
 * Author:            tradesouthwestgmailcom
 * Author URI:        https://tradesouthwest.com
 * Version:           1.0.4
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 4.5
 * Tested up to:      5.3.1
 * Requires PHP:      5.4
 * Text Domain:       views-addon
 * Domain Path:       /languages
*/

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {	exit; }
/** 
 * Constants
 * 
 * @param VIEWS_ADDON_VER         Using bumped ver.
 * @param VIEWS_ADDON_URL         Base path
 * @since 1.0.0 
 */
if( !defined( 'VIEWS_ADDON_VER' )) { define( 'VIEWS_ADDON_VER', '1.0.3' ); }
if( !defined( 'VIEWS_ADDON_URL' )) { define( 'VIEWS_ADDON_URL', 
    plugin_dir_url(__FILE__)); }

    // Start the plugin when it is loaded.
    register_activation_hook(   __FILE__, 'views_addon_plugin_activation' );
    register_deactivation_hook( __FILE__, 'views_addon_plugin_deactivation' );
  
/**
 * Activate/deactivate hooks
 * 
 */
function views_addon_plugin_activation() 
{

    return false;
}
function views_addon_plugin_deactivation() 
{
    return false;
}
/**
 * Define the locale for this plugin for internationalization.
 * Set the domain and register the hook with WordPress.
 *
 * @uses slug `swedest`
 */
add_action( 'plugins_loaded', 'views_addon_load_plugin_textdomain' );

function views_addon_load_plugin_textdomain() 
{

    $plugin_dir = basename( dirname(__FILE__) ) .'/languages';
                  load_plugin_textdomain( 'views-addon', false, $plugin_dir );
}

/** 
 * Admin side specific
 *
 * Enqueue admin only scripts 
 */ 
if( is_admin() ) :  
add_action( 'admin_enqueue_scripts', 'views_addon_load_admin_scripts' );   
function views_addon_load_admin_scripts() 
{
    /*
     * Enqueue styles */
    wp_enqueue_style( 'views-addon-admin', 
                        VIEWS_ADDON_URL . 'css/views-addon-admin.css', 
                        array(), VIEWS_ADDON_VER, false 
                        );
}
endif;

require_once ( plugin_dir_path(__FILE__) . 'inc/views-addon-theme-admin.php' );
require_once ( plugin_dir_path(__FILE__) . 'inc/views-addon-helpers.php' );
require_once ( plugin_dir_path(__FILE__) . 'inc/views-addon-functions.php' );
/**
 * register and add shortcodes [request_link] 
 */
add_action( 'init', 'views_addon_register_shortcodes_init');
function views_addon_register_shortcodes_init(){
    //add_shortcode( 'getdata_view',  'view_addon_shortcode_params' );
    add_shortcode( 'request_link',  'views_addon_shortcode_linkout' );
   // return false;
}

/** 
 * Enqueue or localise scripts conditionally
 */
    $enqu = (empty( get_option('views_addon_options')['views_addon_checkbox_allow']))
          ? false : get_option('views_addon_options')['views_addon_checkbox_allow'];

if ( isset( $enqu ) && $enqu != '1' ) : 
add_action( 'wp_enqueue_scripts', 'views_addon_public_style' );

function views_addon_public_style() {
    wp_enqueue_style( 'views-addon-style',  VIEWS_ADDON_URL
                      . 'css/views-addon-theme.css',array(), null, false );
}
endif; 
/**
 * Check to add body_class to privatized listings
 * @since 1.0.3
 * @deprecated 1.0.4
 */
// #a3 in inc/views-addon-helpers
//add_action('init', 'views_addon_inclusive_protected_post');
?>