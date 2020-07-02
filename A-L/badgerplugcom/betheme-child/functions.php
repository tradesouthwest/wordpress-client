<?php
// Child theme file added named extended-functions.php included at bottom of this file.
/* ---------------------------------------------------------------------------
 * Child Theme URI | DO NOT CHANGE
 * --------------------------------------------------------------------------- */
define( 'CHILD_THEME_URI', get_stylesheet_directory_uri() );


/* ---------------------------------------------------------------------------
 * Define | YOU CAN CHANGE THESE
 * --------------------------------------------------------------------------- */

// White Label --------------------------------------------
define( 'WHITE_LABEL', false );

// Static CSS is placed in Child Theme directory ----------
define( 'STATIC_IN_CHILD', false );


/* ---------------------------------------------------------------------------
 * Enqueue Style
 * --------------------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', 'mfnch_enqueue_styles', 101 );
function mfnch_enqueue_styles() {
	
	// Enqueue the parent stylesheet
// 	wp_enqueue_style( 'parent-style', get_template_directory_uri() .'/style.css' );		//we don't need this if it's empty
	
	// Enqueue the parent rtl stylesheet
	if ( is_rtl() ) {
		wp_enqueue_style( 'mfn-rtl', get_template_directory_uri() . '/rtl.css' );
	}
	
	// Enqueue the child stylesheet
	wp_dequeue_style( 'style' );
	wp_enqueue_style( 'style', get_stylesheet_directory_uri() .'/style.css' );
	
}


/* ---------------------------------------------------------------------------
 * Load Textdomain
 * --------------------------------------------------------------------------- */
add_action( 'after_setup_theme', 'mfnch_textdomain' );
function mfnch_textdomain() {
    load_child_theme_textdomain( 'betheme',  get_stylesheet_directory() . '/languages' );
    load_child_theme_textdomain( 'mfn-opts', get_stylesheet_directory() . '/languages' );
}

add_filter( 'wc_product_table_open_products_in_new_tab', '__return_true' );


add_filter( 'woocommerce_cart_item_name', 'customizing_cart_item_data', 10, 3);
function customizing_cart_item_data( $item_name, $cart_item, $cart_item_key ) {
    $term_names = array();

    // Get product categories
    $terms = wp_get_post_terms( $cart_item['product_id'], 'product_cat' );

    if( count($terms) > 0 ){
        foreach( $terms as $term ) $term_names[] = $term->name;

        $item_name .= '<p class="item-category" style="margin:12px 0 0; font-size: .875em;">
            <strong class="label">' . _n( 'Category', 'Categories', count($terms), 'woocommerce' ) . ': </strong>
            <span class="values">' . implode( ', ', $term_names ) . '</span>
        </p>';
    }
    return $item_name;
}

if ( defined('YITH_YWRAQ_PREMIUM') && function_exists('YITH_YWRAQ_Frontend') && class_exists('WC_Product_Table_Plugin') ) {
 add_filter('wc_product_table_data_short_description','add_to_quote_button_in_table',99,2);
 function add_to_quote_button_in_table($short_description, $pid) {
 ob_start();
 YITH_YWRAQ_Frontend()->print_button( $pid );
 return ob_get_clean();
 }
}

// include extended functions @by codeable
include 'extended-functions.php';