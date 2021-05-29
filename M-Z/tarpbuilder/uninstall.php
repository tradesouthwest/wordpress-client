<?php
/**
 * Delete our options when this plugin is deleted
 * @since 2.0.1 Deprecated but still useful.
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
/*
global $wpdb;

$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'tarpbuilder\_%';" );
$options = array(
	'tarpbuilder_plus_begin_label',
	'tarpbuilder_plus_content_after_html',
	'tarpbuilder_plus_content_before_html',
	'tarpbuilder_plus_csdescription_field',
	'tarpbuilder_plus_cstitle_field',
	'tarpbuilder_plus_inwoo_background',
	'tarpbuilder_plus_inwoo_color',
	'tarpbuilder_plus_priority_order',
	'tarpbuilder_plus_print_styles',
	'tarpbuilder_plus_removestyles_field',
	'tarpbuilder_plus_styles_radio',
	'tarpbuilder_plus_wndinwidth_field',
	'tarpbuilder_plus_wndinppd_field',
	'tarpbuilder_plus_wndmatch_field',
	'tarpbuilder_plus_wndnada_field',
	'tarpbuilder_plus_wndproduct_field',
	'tarpbuilder_plus_wndtaxbase_field',
);
foreach( $options as $option ) {
	delete_option( $option );
}
*/