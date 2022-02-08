<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * custom post type to handle form retrieval
 * register_post_type( $post_type, $args )
 *
 * Link: http://codex.wordpress.org/Function_Reference/register_post_type
 */
register_post_type( 'promocoder', 
array( 
    'labels' => array(
        'name'               => _x( 'Promocodes', 'post type general name', 'promocoder' ),
        'singular_name'      => _x( 'Promocoder', 'post type singular name', 'promocoder' ),
        'menu_name'          => _x( 'Promo Codes', 'admin menu',       'promocoder' ),
        'name_admin_bar'     => _x( 'Promo Code', 'add new on admin bar',    'promocoder' ),
        'add_new'            => _x( 'Add New', 'promocoder_client', 'promocoder' ),
        'add_new_item'       => __( 'Add New Promo Code', 'promocoder' ),
        'new_item'           => __( 'New Promo Code', 'promocoder' ),
        'edit_item'          => __( 'Edit Promo Code', 'promocoder' ),
        'view_item'          => __( 'View Promo Code', 'promocoder' ),
        'all_items'          => __( 'All Promocodes', 'promocoder' ),
        'search_items'       => __( 'Search Promo Code', 'promocoder' ),
        'parent_item_colon'  => __( 'Parent Promo Code:', 'promocoder' ),
        'not_found'          => __( 'No Promocode found.', 'promocoder' ),
        'not_found_in_trash' => __( 'No Promocode found in Trash.', 'promocoder' )
        ),

        'description'        => __( 'Promocoder List', 'promocoder' ),
        'supports'           => array( 'title', 'revisions' ),
        'capability_type'    => 'post',
        'capabilities'       => array(
                            'edit_post'   => 'edit_promocoder_post',
                            'delete_post' => 'delete_promocoder_post',
				            'read_post'   => 'read_promocoder_post',
        ),
        'map_meta_cap'  => true,
        'taxonomies'    => array( 
                            'post_tag' 
        ), 
        'public'        => true,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_position' => 45,
        'menu_icon'     => 'dashicons-admin-post',
        'show_in_nav_menus'   => true,
        'publicly_queryable'  => true,
        'exclude_from_search' => false,
        'has_archive'         => true,
	    'hierarchical'        => true,
        'rewrite'             => array(
                                    'slug' => 'promocode',
                                    'with_front' => false 
        ),
        'query_var'           => true,
	    'delete_with_user'    => true,
        'can_export'          => true,
    )
); 
