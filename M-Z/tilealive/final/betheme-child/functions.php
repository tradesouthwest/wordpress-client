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
 *
 */
add_filter( 'the_title', 'betheme_child_custom_the_title', 10, 2 );
function betheme_child_custom_the_title( $title, $post_id ) 
{

    $post_type = get_post_field( 'post_type', $post_id, true );
    if( $post_type == 'product' || $post_type == 'product_variation' )
        $title = str_replace( '|', '<br/>', $title ); 
    return $title;
}
