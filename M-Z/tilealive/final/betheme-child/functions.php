<?php
/**
 * Theme Functions
 *
 * @package Betheme Child
 * @author Muffin group, tradesouthwestgmailcom
 * @link https://muffingroup.com
 */
/**
 * Enqueue child theme style
 */
add_action( 'wp_enqueue_scripts', 'betheme_child_enqueue_styles' );
function betheme_child_enqueue_styles() 
{
    $parent_style = 'style';
	$parent_version = '21.7.4';
    $vc             = date('Ymdhi');
    wp_dequeue_style( $parent_style );
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css', 
                    array(), $parent_version );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(), 
                    array( $parent_style ), $vc, 'all' );
} 
/**
 * Script to add selector in span
 * wp_register_script( 'betheme-add-dcnt' );
 * @uses wp_footer() 
 */
function betheme_hook_javascript_footer() 
{

    if( is_cart() || is_checkout() ) { 
    ?>    
    <script id="betheme-add-crtcnt" type="text/javascript">
    jQuery(document).ready(function($){
    'use strict';
        $(".wcpa_cart_val span").each(function(){
        var $this = $(this);
        $this.attr("data-content", $this.text());
        });
        })(jQuery); 
    </script>
        <?php  
    }
    return false;
}
add_action( 'wp_footer', 'betheme_hook_javascript_footer', 5 );

/**
 * Clean woocommerce_cart_item_name. Works on review-orders AND cart templates
 * $product_get_name = apply_filters( 'woocommerce_cart_item_name', 
 * $product_get_name, $cart_item, $cart_item_key ); 
 * also wc-block-grid__product-title
 */
add_filter( 'the_title', 'betheme_child_custom_the_title', 15, 2 );
function betheme_child_custom_the_title( $title, $post_id ) 
{

    $post_type = get_post_field( 'post_type', $post_id, true );
    if( $post_type == 'product' || $post_type == 'product_variation' ) {
    $needle = array('| ');
    //$haystack = $title;
    $replace = array( '<br />');

        $title = str_replace( $needle, $replace, $title); 
        }
    return $title;
}
 
// Removes the order again button in checkout.
remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );
/**
 * Will change the minutes it takes an In Cart booking to expire.
 * This example reduces the number from 60 to 30.
 * 
 * @param  int $minutes 60 is the default passed
 * @return int          The amount of minutes you'd like to have In Cart bookings expire on. 
 */
function change_incart_bookings_expiry_minutes_20170825( $minutes ) {
	return 30;
}
add_filter( 'woocommerce_bookings_remove_inactive_cart_time', 'change_incart_bookings_expiry_minutes_20170825' );

