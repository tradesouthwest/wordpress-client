<?php
/*
 * Plugin Name: Readlimit
 * Description:  Reset subscriptions or restricted content by date/time.
 * Version:           1.0.2
 * Author: Larry Judd 
 * Author URI: https://codeable.io/developers/larry-judd/
 * @wordpress-plugin  wpdb =
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 4.8
 * Tested up to:      5.5
 * Requires PHP:      5.4
 * Text Domain:       readlimit
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!defined('READLIMIT_VER')) { define('READLIMIT_VER', '1.0.2'); }

if (!defined('READLIMIT_URL')) { define( 'READLIMIT_URL', plugin_dir_url(__FILE__)); }

/**
 * Upon plugin activation, always make sure init hook for a CPT 
 * has ran first or you will have to run `flush_rewrite()`.
*/

    include ( plugin_dir_path( __FILE__ ) . 'readlimit-cpts.php' );
    
//load language scripts     
function readlimit_load_text_domain() 
{
    load_plugin_textdomain( 'readlimit', false, 
    basename( dirname( __FILE__ ) ) . '/languages' ); 
}

//enqueue or localise scripts
function readlimit_public_style() 
{
    wp_enqueue_style( 'readlimit-style', READLIMIT_URL 
                      . '/assets/readlimit-style.css',array(), READLIMIT_VER, false );
}
    //add_action( 'wp_enqueue_scripts', 'readlimit_public_style' );

if( is_admin() ) : 
//enqueue scripts
function readlimit_addtoplugin_scripts() {
    $dev = time();
    wp_enqueue_style( 'readlimit-admin',  
        plugin_dir_url(__FILE__) . 'assets/readlimit-admin.css',
                      array(), $dev, false );
    // Register Scripts
    wp_register_script( 'readlimit-plugin', 
       plugins_url( 'assets/readlimit-admin.js', __FILE__ ), 
                    array( 'jquery' ), true );
                    
    wp_enqueue_style ( 'readlimit-admin' ); 
    wp_enqueue_script( 'readlimit-plugin' );
    
}
//load admin scripts as well
add_action( 'admin_enqueue_scripts',  'readlimit_addtoplugin_scripts' );
endif;
//activate plugin
function readlimit_plugin_activate() 
{  
    $t=time();
    $time = date("Y-m-d",$t);
    add_option( 'readlimit_date_plugin_activated' );
    update_option( 'readlimit_date_plugin_activated', $time );
    flush_rewrite_rules();      
}
//activate plugin
function readlimit_plugin_reactivate() 
{ 
    flush_rewrite_rules();          
}
//deactivation settings
function readlimit_plugin_deactivate() 
{
    delete_option( 'readlimit_date_plugin_activated' );
    /* Flush rewrite rules for custom post types. */
    flush_rewrite_rules();
        return false;
} 
    //ready, set, go
    register_activation_hook(__FILE__,   'readlimit_plugin_activate');
    register_deactivation_hook(__FILE__, 'readlimit_plugin_deactivate');
	add_action( 'after_switch_theme',    'readlimit_plugin_reactivate' );

// load files
include ( plugin_dir_path( __FILE__ ) . 'functions/readlimit-functions-access.php' );
include ( plugin_dir_path( __FILE__ ) . 'functions/readlimit-functions-profile.php' );
include ( plugin_dir_path( __FILE__ ) . 'readlimit-admin.php' );
