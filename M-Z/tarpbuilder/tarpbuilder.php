<?php
/**
 * Plugin Name:       TarpBuilder Plus
 * Plugin URI:        https://themes.tradesouthwest.com/wordpress/plugins/
 * Description:       TarpBuilder Plus add custom cart item data to category custom-tarp for Woocommerce products.
 * Version:           1.0.8
 * Author:            Larry Judd
 * Author URI:        http://tradesouthwest.com
 * @package           TarpBuilder Plus
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Requires Package:  WooCommerce 3.0+
 * Tested Package:    WooCommerce 4.8.0
 * Requires at least: 4.5
 * Tested up to:      5.6
 * Requires PHP:      5.4
 * Text Domain:       tarpbuilder
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { exit; }
if (!defined('TARPBUILDER_PLUS_URL') )  { define('TARPBUILDER_PLUS_URL', plugins_url() ); } 
if (!defined('TARPBUILDER_PLUS_PATH') ) { define('TARPBUILDER_PLUS_PATH', plugin_dir_url(__FILE__) ); } 
if (!defined('TARPBUILDER_PLUS_VER') )  { define('TARPBUILDER_PLUS_VER', time() ); } 

//activate/deactivate hooks
function tarpbuilder_plus_plugin_activation() 
{

        return false;
}

function tarpbuilder_plus_plugin_deactivation() 
{
    //tarpbuilder_deregister_shortcode() 
        return false;
}

if( is_admin() ) : 
//enqueue scripts 
function tarpbuilder_plus_addtoplugin_scripts() 
{

    wp_enqueue_style( 'tarpbuilder-admin-style',  
                        plugin_dir_url(__FILE__) . 'lib/tarpbuilder-admin-style.css',
                        array(), TARPBUILDER_PLUS_VER, false );
    
    wp_enqueue_style ( 'tarpbuilder-admin-style' ); 
    //wp_enqueue_script( 'tarpbuilder-plus-plugin' );
}
//load admin scripts 
add_action( 'admin_enqueue_scripts',  'tarpbuilder_plus_addtoplugin_scripts' ); 
endif;

//public scripts
function tarpbuilder_plus_addtosite_scripts()
{
    wp_enqueue_style( 'jquery-ui',  plugin_dir_url(__FILE__) 
                    . 'lib/jquery-ui.css',
                    array(), '', false );
    
    wp_enqueue_style( 'tarpbuilder-plus-public',  
                    plugin_dir_url(__FILE__) . 'lib/tarpbuilder-public-style.css',
                    array(), TARPBUILDER_PLUS_VER, false );
    
    wp_register_script( 'trisontarps-math', 
                    plugins_url( 'lib/trisontarps-math.js', __FILE__ ), 
                    array( 'jquery' ), TARPBUILDER_PLUS_VER, true );
    wp_register_script( 'tarpbuilder-grommets', 
                    plugins_url( 'lib/tarpbuilder-grommets.js', __FILE__ ), 
                    array( 'jquery' ), TARPBUILDER_PLUS_VER, true );
    
    if (function_exists('is_product_category()') && is_product_category('custom-tarps')){  

        wp_register_script('tarpbuilder',
                    plugins_url( 'lib/tarpbuilder.js', __FILE__ ), 
                    array( 'jquery' ), TARPBUILDER_PLUS_VER, true );
        wp_enqueue_script('tarpbuilder');
    }
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('trisontarps-math');
    wp_enqueue_script('tarpbuilder-grommets');
    
} 
add_action( 'wp_enqueue_scripts', 'tarpbuilder_plus_addtosite_scripts' );

//load language scripts     
function tarpbuilder_plus_load_text_domain() 
{
    load_plugin_textdomain( 'tarpbuilder', false, 
    basename( dirname( __FILE__ ) ) . '/languages' ); 
}
/**
 * Init premium features
 * @since 2.0.1
 */

//activate and deactivate registered
register_activation_hook(   __FILE__, 'tarpbuilder_plus_plugin_activation');
register_deactivation_hook( __FILE__, 'tarpbuilder_plus_plugin_deactivation');

//include admin and public views
require_once ( plugin_dir_path( __FILE__ ) 
                . 'include/admin/tarpbuilder-plus-admin-page.php' ); 
/* include the admin forms section */
include_once ( plugin_dir_path( __FILE__ ) 
                . 'include/admin/tarpbuilder-plus-admin-forms.php' );

/* include the admin help- section */
include_once ( plugin_dir_path( __FILE__ ) 
                . 'include/admin/tarpbuilder-plus-admin-section-help.php' );

/* Include render admin page section */
require_once ( plugin_dir_path( __FILE__ ) 
                . 'include/admin/tarpbuilder-plus-admin-render.php' ); 

/* Include render admin page section */
require_once ( plugin_dir_path( __FILE__ ) 
                . 'include/admin/tarpbuilder-plus-admin-order.php' ); 

require_once ( plugin_dir_path( __FILE__ ) . 'include/tarpbuilder-plus-functions.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'include/tarpbuilder-plus-postmeta.php' ); 
require_once ( plugin_dir_path( __FILE__ ) . 'include/tarpbuilder-plus-quantity.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'include/tarpbuilder-plus-attributes.php' );

?>