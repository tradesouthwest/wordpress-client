<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * @link       http://tradesouthwest.com
 * @since      1.0.0
 *
 * @package    Hordes
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$option_name = 'hordes_settings';
delete_option($option_name);

function hordes_delete_post_type(){
    unregister_post_type( 'hordes' );
}
add_action('init','hordes_delete_post_type');

// for site options in Multisite
delete_site_option($option_name);
?>