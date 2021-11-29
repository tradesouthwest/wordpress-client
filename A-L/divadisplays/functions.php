<?php

add_action( 'wp_enqueue_scripts', 'porto_child_enqueue_styles', 9999 );

// Load CSS
function porto_child_enqueue_styles() {
	$upver = date('ymdhis');
	// porto child theme styles
	wp_deregister_style( 'styles-child' );
	wp_register_style( 'styles-child', esc_url( get_stylesheet_directory_uri() ) . '/style.css', array(), $upver);
	wp_enqueue_style( 'styles-child' );

	/*if ( is_rtl() ) {
		wp_deregister_style( 'styles-child-rtl' );
		wp_register_style( 'styles-child-rtl', esc_url( get_stylesheet_directory_uri() ) . '/style_rtl.css' );
		wp_enqueue_style( 'styles-child-rtl' );
	}*/
}
function porto_child_change_single_summary_order(){
	global $product;
	 if ( has_term( array(), 'product_tag', $product->get_id() ) ) :
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 15 );
		endif;
}
add_action( 'woocommerce_single_product_summary', 'porto_child_change_single_summary_order', 10 );
