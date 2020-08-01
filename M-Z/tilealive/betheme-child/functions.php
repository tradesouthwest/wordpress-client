<?php
/**
 * Theme Functions
 *
 * @package Betheme Child
 * @author Muffin group
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
    wp_dequeue_style( $parent_style );
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css', 
                    array(), $parent_version );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(), 
                    array( $parent_style ), '21.7.41', 'all' );
} 

function betheme_add_divider_fields()
{

 echo '<div class="single-top-divider"><hr></div>';

}
add_action( 'woocommerce_single_product_summary', 'betheme_add_divider_fields', 21 );