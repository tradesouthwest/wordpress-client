<?php
/**
 * Plugin Name: BP Profilex
 * Description: Adds fields to BuddyPress Profile pages
 * Author: Tradesouthwest
 * Author URI: http://tradesouthwest.com
 * Version: 1.0.2
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Ambiguous user for non logged in views. unknown unknown@ma3stro.com 64jbQnBMjE2
 *
 */  

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! defined('BP_PROFILEX_VER')) { define('BP_PROFILEX_VER', time() ); }

if (!defined('BP_PROFILEX_URL')) { define( 'BP_PROFILEX_URL', plugin_dir_url(__FILE__) ); }	
/**
 * @activate plugin
 * It is safe to activate plugin, now that CPT is hooked
 * @string $time, creates activation time to display in admin.
 * @uses get_option(i18n time string)
 */
function bp_profilex_profile_tab_plugin_activate() 
{  
   return false;
    //flush_rewrite_rules();      
}
/**
 * reactivate plugin
*/
function bp_profilex_profile_tab_plugin_reactivate() 
{ 
    // clean up any CPT cache
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
          
}
/**
 * deactivation settings
 */
function bp_profilex_profile_tab_plugin_deactivate() 
{

    /* Flush rewrite rules for custom post types. */
    flush_rewrite_rules();
        return false;
} 
    //ready, set, go
    register_activation_hook(__FILE__,   'bp_profilex_profile_tab_plugin_activate');
    register_deactivation_hook(__FILE__, 'bp_profilex_profile_tab_plugin_deactivate');
	add_action( 'after_switch_theme',    'bp_profilex_profile_tab_plugin_reactivate' );

/**
 * This function just loads language files
 */
function bppx_profilex_load_textdomain() {
    load_plugin_textdomain('bppx-profilex', false, BP_PROFILEX_URL  . 'languages/');
}

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
    wp_register_script( 'bp-profilex-ajax', 
                        plugins_url( 'css-js/bp-profilex-ajax.js', __FILE__ ), 
                        array( 'jquery' ), BP_PROFILEX_VER, false );

    wp_enqueue_script( 'bp-profilex-ajax' );
    wp_localize_script( 'bp-profilex-ajax', 'ajax_object', 
                    array( 'ajaxurl' => admin_url( 'admin_ajax.php' ) ) );

}
add_action( 'wp_enqueue_scripts', 'bppx_profilex_profile_tab_public_style' );
add_action( 'wp_ajax_bppx_filter_setvisits', 'bppx_filter_setvisits' );
add_action( 'wp_ajax_nopriv_bppx_filter_setvisits', 'bppx_filter_setvisits');
/**
 * ***************************************
 * Include specific files to run plugin
 * @since 1.0.1
 * ***************************************
 */
include_once 'inc/bp-profilex-meta.php';
include_once 'inc/bp-profilex-overview.php';
//include_once 'inc/bp-profilex-public.php';

require_once('vendor/geoplugin.class.php');

//register shortcodes R1
//add_action( 'init', 'bppx_profilex_register_shortcodes' );

function bppx_profilex_register_shortcodes() 
{   
add_shortcode( 'bppx_profilex_location_form', 'bppx_profilex_visitor_form_location_tag' );
}    
/*
add_action( 'bp_before_directory_activity', function () {
	echo do_shortcode('[bppx_profilex_location_form]');
} );
*/

/**
 * Returns total comments count for the user
 *
 * @param int $user_id user id.
 * @param string $type activity type.
 *
 * @return int
 */
function bp_profilex_get_total_user_comments_count( $user_id, $type = '' ) {
	global $wpdb;

	$table = buddypress()->activity->table_name;

	$where_conditions = array();

	$where_conditions[] = $wpdb->prepare( 'user_id = %d', $user_id );
	if ( $type ) {
		$where_conditions[] = $wpdb->prepare( "type = %s", $type );
	}

	$whe_sql = join( ' AND ', $where_conditions );

	return absint( $wpdb->get_var( "SELECT count(DISTINCT id) FROM {$table} WHERE {$whe_sql}" ) );
}

/**
 * Returns total activities count for the user
 *
 * @param int $user_id user id.
 * @param string $type activity type.
 *
 * @return int
 */
function bp_profilex_get_total_activity_activities_count( $user_id, $type = '' ) {
	global $wpdb;

    $table = buddypress()->activity->table_name;
    
	$where_conditions = array();

	$where_conditions[] = $wpdb->prepare( 'user_id = %d', $user_id );
	if ( $type ) {
		$where_conditions[] = $wpdb->prepare( "type = %s", $type );
	}

	$whe_sql = join( ' AND ', $where_conditions );

	return absint( $wpdb->get_var( "SELECT count(DISTINCT id) FROM {$table} WHERE {$whe_sql}" ) );
}
/**
 * Returns total profile updates count for the user
 *
 * @param int $user_id user id.
 * @param string $type activity type.
 *
 * @return int
 */
function bp_profilex_get_total_user_updates_count( $user_id, $type = '' ) {
	global $wpdb;

	$table = buddypress()->activity->table_name;

	$where_conditions = array();

	$where_conditions[] = $wpdb->prepare( 'user_id = %d', $user_id );
	if ( $type ) {
		$where_conditions[] = $wpdb->prepare( "type = %s", $type );
	}

	$whe_sql = join( ' AND ', $where_conditions );

	return absint( $wpdb->get_var( "SELECT count(DISTINCT id) FROM {$table} WHERE {$whe_sql}" ) );
} 
?>