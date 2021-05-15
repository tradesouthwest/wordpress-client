<?php
/**
 * Plugin Name: BP Profilex
 * Description: Adds fields to BuddyPress Profile pages
 * Author: Tradesouthwest
 * Author URI: http://tradesouthwest.com
 * Version: 1.0.21
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Ambiguous user for non logged in views. unknown unknown@ma3stro.com 64jbQnBMjE2
 *
 */  

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! defined('BP_PROFILEX_VER')) { define('BP_PROFILEX_VER', time() ); }

if ( !defined('BP_PROFILEX_URL' )) { define( 'BP_PROFILEX_URL', plugin_dir_url(__FILE__) ); }	

/**
 * deactivation settings
 */
function bp_profilex_profile_tab_plugin_deactivate() 
{
    return false;
} 

/**
 * This function just loads language files
 */
function bppx_profilex_load_textdomain() 
{
    load_plugin_textdomain('bppx-profilex', false, BP_PROFILEX_URL  . 'languages/');
    return false;
}
/**
 * @activate plugin
 * It is safe to activate plugin, now that CPT is hooked
 * @string $time, creates activation time to display in admin.
 * @uses get_option(i18n time string)
 */
function bp_profilex_database_build_plugin_activate() 
{  
/*
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $table_name = $wpdb->prefix . "bppx_location_meta";
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        location_key varchar(220) DEFAULT NULL,
        location_value varchar(220) DEFAULT NULL,
        location_count varchar(220) DEFAULT '1',
        visit_time varchar(220) DEFAULT NULL,
        member_id int(11) DEFAULT '1',
        activity_id int(11) DEFAULT '',
        PRIMARY KEY(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    
        dbDelta( $sql );
  */  
    return false;     
}
//ready, set, go
register_activation_hook(__FILE__,   'bp_profilex_database_build_plugin_activate');
register_deactivation_hook(__FILE__, 'bp_profilex_profile_tab_plugin_deactivate');

/** 
 * Admin side specific
 * @since 1.0.0 
 *
 */
if( is_admin() ) : 
function bp_profilex_profile_tab_admin_style() 
{
    wp_enqueue_style( 'bp-profilex-admin', BP_PROFILEX_URL 
                      . 'css-js/bp-profilex-admin.css',array(), BP_PROFILEX_VER, false );
    
}
    //add_action( 'admin_enqueue_scripts', 'bp_profilex_profile_tab_admin_style' );
    //run admin settings
    require_once 'inc/bp-profilex-admin.php';
endif; 

// public styles
function bppx_profilex_profile_tab_public_style() 
{
    wp_enqueue_style( 'bppx-public',  plugin_dir_url(__FILE__) 
                      . 'css-js/bp-profilex-public.css',
                      array(), BP_PROFILEX_VER, false );
    wp_register_script( 'bp-profilex-forms', 
                        plugins_url( 'css-js/bp-profilex-forms.js', __FILE__ ), 
                        array( 'jquery' ), BP_PROFILEX_VER, true );

    wp_enqueue_script( 'bp-profilex-forms' );
    /* $params = array( 'ajaxurl'    => admin_url( 'admin-ajax.php' ),
                     'ajaxaction' => 'bppx_profilex_success' 
                    );  */
   // wp_localize_script( 'bp-profilex-forms', 'params', $params );

}
add_action( 'wp_enqueue_scripts', 'bppx_profilex_profile_tab_public_style' );
//add_action( 'wp_ajax_bppx_profilex_success', 'bppx_profilex_success_messages' );
//add_action( 'wp_ajax_nopriv_bppx_profilex_success', 'bppx_profilex_success_messages');

/**
 * ***************************************
 * Include specific files to run plugin
 * @since 1.0.1
 * ***************************************
 */
include 'inc/bp-profilex-meta.php';
include 'inc/bp-profilex-public.php';
include 'inc/bp-profilex-overview.php';


require_once('vendor/geoplugin.class.php');

//register shortcodes R1
/* add_action( 'init', 'bppx_profilex_register_shortcodes' );

function bppx_profilex_register_shortcodes() 
{   
add_shortcode( 'bppx_profilex_location_form', 'bppx_profilex_visitor_form_location_tag' );
}    
add_action( 'bp_before_directory_activity', function () {
	echo do_shortcode('[bppx_profilex_location_form]');
} );
*/

?>