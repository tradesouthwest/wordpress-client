<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 * Can change @param string $ver to: `wp_get_theme()->get('Version')`, after dev
 *
 * @return void
 */
function hello_elementor_child_enqueue_scripts() {

	$parent_style = 'hello-elementor-theme-style'; 
	$ver          = time();
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'hello-elementor-theme-child-style',
					  get_stylesheet_directory_uri() . '/style.css',
					  array( $parent_style ), $ver );
	wp_register_script( 'boekenpakket-topprice-script', 
						get_stylesheet_directory_uri() . '/js/boekenpakket-fragments-script.js', 
						array('jquery'), $ver, true );
	wp_enqueue_script( 'boekenpakket-topprice-script' );
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts', 20 );

/**
 * Add text before the  price
 *
 * @param  string $text Text to add before price
 * @return HTML         CSS
 */ 
add_action( 'wp_footer', 'boekenpakket_bundles_text_before_price' );
function boekenpakket_bundles_text_before_price()
{
	if ( is_singular() ) : 

	/* BEGIN change text below if needed: */ 
	$text = __('Kosten van dit pakket: ', 'woocommerce-product-bundles' );
	/* ENDS change text below if needed: */ 

	echo '<style type="text/css" id="beforebundlesprice">
		.single-product .woocommerce-Price-amount.amount:before{content: "' . $text . ' ";position: relative;}
		.bundle_button{width: auto;display: block;float: right;}
		.bundled_item_cart_details span.woocommerce-Price-amount.amount:before,
		.woocommerce-cart span.woocommerce-Price-amount.amount:before{content: "";}
		
		</style>';

	endif;
}

/**
 * Display bundle price above bundle items table
 *
 * @param string $product Product->ID
 * @return HTML
 */
function boekenpakket_bundles_top_bundle_price($product)
{

	$bndl_prc = $product->get_price_html();
	$text = __('Kosten van dit pakket: ', 'woocommerce-product-bundles' );

	ob_start();
	echo '<div class="bundle_price_top" style="float: right;width:auto;color: #0A4191;font-weight: 600;">';
	echo '<p style="padding-left: 1em;">' . esc_html__( $text ) . '<span class="bndl_prc" style="display:inline-block"></span>
	<span id="bpTop"></span></p></div>';

	$output = ob_get_clean();

		return $output; 
}
