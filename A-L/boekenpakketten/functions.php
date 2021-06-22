<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *   wp_get_theme()->get('Version')
 * @return void
 */
function hello_elementor_child_enqueue_scripts() {

	$parent_style = 'hello-elementor-theme-style'; 
	$ver          = time();
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'hello-elementor-theme-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( $parent_style ), $ver );
	/*
	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		'1.0.0'
	); */
	wp_register_script( 'boekenpakket-fragments-script', get_stylesheet_directory_uri() 
						. '/js/boekenpakket-fragments-script.js', array(), $ver, true  );


    wp_enqueue_script( 'boekenpakket-fragments-script' );

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts', 20 );

/**
 * Display bundle price above bundle items table
 *
 * @param string $product Product->ID
 * @return HTML
 */
function boekenpakket_bundles_top_bundle_price($product)
{
	$bndl_prc = $product->get_price_html();
	$text    = __( 'Vanaf: ', 'woocommerce-product-bundles' );

	$output  = '<div class="bundle_price_top" style="float: right;width:12%;color: #0A4191;font-weight: 600;">';
	$output .= '<span class="bndl_prc" style="display:block">' . $bndl_prc . '</span>';
	$output .= '<p><span id="bpTop"></span></p></div>';

		return $output; 
}