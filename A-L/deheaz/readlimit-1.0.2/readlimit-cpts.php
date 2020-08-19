<?php 
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
function readlimit_register_cpts_newsletter() {

	/**
	 * Post Type: PDF Reports.
	 */

	$labels = [
		"name" => __( "Newsletters", "deheza-ltd" ),
		"singular_name" => __( "Newsletter", "deheza-ltd" ),
	];

	$args = [
		"label" => __( "Newsletters", "deheza-ltd" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "newsletter", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
	];

	register_post_type( "newsletter", $args );
}

add_action( 'init', 'readlimit_register_cpts_newsletter' );

function readlimit_register_taxies_newsletter_category() {

	/**
	 * Taxonomy: Newsletter Categories.
	 */

	$labels = [
		"name" => __( "Newsletter Categories", "deheza-ltd" ),
		"singular_name" => __( "Newsletter Category", "deheza-ltd" ),
	];

	$args = [
		"label" => __( "Newsletter Categories", "deheza-ltd" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'newsletter_category', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"rest_base" => "newsletter_category",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => false,
		];
	register_taxonomy( "newsletter_category", [ "newsletter" ], $args );
}
add_action( 'init', 'readlimit_register_taxies_newsletter_category' );
