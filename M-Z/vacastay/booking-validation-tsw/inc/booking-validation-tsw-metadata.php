<?php 
/**
 * @since:     1.0.0
 * Author:     Tradesouthwest
 * Author URI: http://tradesouthwest.com
 * @package    booking-validation-tsw
 * @subpackage inc/booking-validation-tsw-public
*/
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add data to WooCommerce data 
 * @since 1.0.1
 * 
 * @param string $text Value of text field in product meta
 */
//add_filter('woocommerce_checkout_cart_item_quantity','booking_valtsw_add_custom_option_from_session_into_cart',1,3);  
//add_filter('woocommerce_cart_item_price','booking_valtsw_add_custom_option_from_session_into_cart',1,3);
if(!function_exists('booking_valtsw_add_custom_option_from_session_into_cart'))
{
 function booking_valtsw_add_custom_option_from_session_into_cart($product_name, $values, $cart_item_key )
    {
        /*code to add custom data on Cart & checkout Page*/    
        if(count($values['_security_deposit']) > 0)
        {
            $return_string = $product_name . "</a><dl class='variation'>";
            $return_string .= "<table class='additional_fees_table' id='additional_fees_" . $values['product_id'] . "'>";
            $return_string .= "<tr><td>" . $values['_security_deposit'] . "</td></tr>";
            $return_string .= "</table></dl>"; 
            return $return_string;
        }
        
        else
        {
            return $product_name;
        }
    }
}
//add_action('woocommerce_add_order_item_meta','booking_valtsw_add_values_to_order_item_meta',1,2);
if(!function_exists('booking_valtsw_add_values_to_order_item_meta'))
{
  function booking_valtsw_add_values_to_order_item_meta($item_id, $values)
  {
        global $woocommerce,$wpdb;
        
        $security_deposit = $values['_security_deposit'];
        if(!empty($security_deposit))
        {
            wc_add_order_item_meta($item_id, '_security_deposit', $security_deposit);  
        }
        $cleaning_fee = $values['_cleaning_fee'];
        if(!empty($cleaning_fee))
        {
            wc_add_order_item_meta($item_id,'_security_deposit', $cleaning_fee);  
        }
  }
}

//add_action('woocommerce_before_cart_item_quantity_zero','booking_valtsw_remove_user_custom_data_options_from_cart',1,1);
if(!function_exists('booking_valtsw_remove_user_custom_data_options_from_cart'))
{
    function booking_valtsw_remove_user_custom_data_options_from_cart($cart_item_key)
    {
        global $woocommerce;
        // Get cart
        $cart = $woocommerce->cart->get_cart();
        // For each item in cart, if item is upsell of deleted product, delete it
        foreach( $cart as $key => $values)
        {
        if ( $values['_security_deposit'] == $cart_item_key )
            unset( $woocommerce->cart->cart_contents[ $key ] );
        if ( $values['_cleaning_fee'] == $cart_item_key )
            unset( $woocommerce->cart->cart_contents[ $key ] );
        }
    }
}

/**
 * WooCommerce Extra Feature
 * --------------------------
 *
 * Display product meta field in a shortcode
 * ex: [woo_custom_field id="my-custom-field"]
 *
 */
function tswwoo_custom_field_shortcode( $atts, $content = null ) {

  global $post;
	
	extract(shortcode_atts(array(
		"id" => ''
	), $atts));
	
	$fields = get_post_meta( $post->ID, $id);
	
	foreach( $fields as $field ) {
		return $field;
	}
}

add_shortcode("woo_custom_field", "tswwoo_custom_field_shortcode");
