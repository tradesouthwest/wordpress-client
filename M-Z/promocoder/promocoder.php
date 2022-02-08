<?php
/**
 * Plugin Name: Promocoder
 * Plugin URI: http://themes.tradesouthwest.com/wordpress/plugins/
 * Description: Save Coupon or Pormotional codes and manage with ease.
 * Version: 1.0.3
 * Author: tradesouthwest
 * Author URI: http://tradesouthwest.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 4.8
 * Tested up to:      5.9
 * Requires PHP:      5.4
 * Text Domain:       promocoder
 * Domain Path:       /languages
*/
if ( ! defined( 'ABSPATH' ) ) exit;
/* Important constants */
define( 'PROMOCODER_VERSION', '1.0.3' );
define( 'PROMOCODER_FORMS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Upon plugin activation, always make sure init hook for a CPT 
 * has ran first or you will have to run `flush_rewrite()`.
*/
function promocoder_custom_post_type() 
{
    require_once ( plugin_dir_path( __FILE__ ) . 'promocoder-register.php' );
}
add_action( 'init', 'promocoder_custom_post_type' );

// Register taxonomies for CPT
function promocoder_register_custom_taxonomies()
{
    require_once ( plugin_dir_path( __FILE__ ) . 'promocoder-taxonomy.php' );
    register_taxonomy( 'promocoder_categories', 'promocoder', $args );
    register_taxonomy( 'promocoder_tags', 'promocoder', $params );
    flush_rewrite_rules();
}
add_action( 'init', 'promocoder_register_custom_taxonomies' ); 

/** 
 * Custom post type onlist_post.
 * Outputs for meta box fields
 * @since 1.0.0
 */
if( !function_exists( 'promocoder_get_custom_field' ) ) : 
    // Function to return a custom field value
    function promocoder_get_custom_field( $value )
    {
        global $post;
        $custom_field = get_post_meta( $post->ID, $value, true );
        if ( !empty( $custom_field ) )
            return is_array( $custom_field ) ? 
                stripslashes_deep( $custom_field ) :
                stripslashes( wp_kses_decode_entities( $custom_field ) );
                return false;
    }
endif;   

//activate/deactivate hooks
function promocoder_plugin_activation() 
{
    flush_rewrite_rules(); 
    return false;
}

function promocoder_plugin_deactivation() 
{
    flush_rewrite_rules();
    return false;
}

/**
 * reactivate plugin
*/
function promocoder_plugin_reactivate() 
{ 
    // clean up any CPT cache
    flush_rewrite_rules();    
    return false;      
}

/**
 * Include loadable plugin files
 */
// Initialise - load in translations
function promocoder_loadtranslations () {
    $plugin_dir = basename(dirname(__FILE__)).'/languages';
        load_plugin_textdomain( 'promocoder', false, $plugin_dir );
}
add_action('plugins_loaded', 'promocoder_loadtranslations');

// hook the plugin activation
register_activation_hook(   __FILE__, 'promocoder_plugin_activation');
register_deactivation_hook( __FILE__, 'promocoder_plugin_deactivation');
//register_uninstall_hook(__FILE__,     'promocoder_plugin_uninstall');
add_action( 'after_switch_theme',     'promocoder_plugin_reactivate' );	

/**
 * Plugin Scripts
 *
 * Register and Enqueues plugin scripts
 *
 * @since 0.0.1
 */
if( is_admin() ) : 
function promocoder_admin_enqueue_scripts()
{
    
    wp_enqueue_style( 'promocoder-admin', PROMOCODER_FORMS_URL 
                    . 'css/promocoder-admin.css', PROMOCODER_VERSION, false );
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script('promocoder-plugin', 
        plugins_url(  'js/promocoder-plugin.js', __FILE__ ), 
               array( 'wp-color-picker' ), false, true );
    if ( ! did_action('wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
}
add_action( 'admin_enqueue_scripts', 'promocoder_admin_enqueue_scripts' );  
endif;

/**
 * Register Scripts  
 * Register Styles
 * 
 */
function promocoder_enqueue_scripts() 
{
    wp_register_script( 'promocoder-public', plugins_url(
                        'js/promocoder-public.js', __FILE__ ), 
                        array( 'jquery' ), true ); 
    wp_register_style( 'promocoder-style', 
        PROMOCODER_FORMS_URL . 'css/promocoder-style.css' );

    wp_enqueue_style( 'promocoder-style' );
    wp_enqueue_script( 'promocoder-public' );
}
add_action( 'wp_enqueue_scripts', 'promocoder_enqueue_scripts' );

//include admin and public views
require_once ( plugin_dir_path( __FILE__ ) . 'inc/promocoder-editor.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'inc/promocoder-adminpage.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'inc/promocoder-functions.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'public/promocoder-templater.php' );    
require_once ( plugin_dir_path( __FILE__ ) . 'inc/promocoder-formpage.php' );   

// TP1
add_filter( 'template_include', 'promocoder_attach_single_template_to_page' );

// Register and load the widget
function promocoder_load_widgets() 
{ 
    require_once ( plugin_dir_path( __FILE__ ) . 'inc/Promocoder_Cat_Widget.php' );
        register_widget( 'Promocoder_Cat_Widget' );
}
//add_action( 'widgets_init', 'promocoder_load_widgets' );

// Register and load the shortcodes
function promocoder_register_plugin_shortcodes() 
{   
        add_shortcode( 'promocoder_submit_post', 'promocoder_front_post_creation' );
        add_shortcode( 'promocoder_list',        'promocoder_template_public_list' );
        add_shortcode( 'promocoder_entry',       'promocoder_front_promocoder_entry' );
} 
add_action( 'init', 'promocoder_register_plugin_shortcodes' ); 
?>