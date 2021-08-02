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
 * https://wisdmlabs.com/blog/add-custom-data-woocommerce-order/
 * @param string $text Value of text field in product meta
 */
//add_action('booking_valtsw_bookable_field', 'booking_valtsw_custom_checkout_field');

function booking_valtsw_custom_checkout_field($checkout='') {
    //global $woocommerce;
    
    ob_start();
    woocommerce_form_field('_security_deposit', 
        array(
        'type'        => 'text',
        'class'       => array('tsw-field-class form-row-wide'),
        'label'       => __('Security Deposit and Cleaning Fee'),
        'placeholder' => __('if apply'),
        'required'	  => false,
        ));
        
    WC()->checkout->get_value( '_security_deposit' );
    echo ob_get_clean();
}

//add_action( 'woocommerce_checkout_update_order_meta', 'customise_checkout_field_update_order_meta' );
function customise_checkout_field_update_order_meta( $order_id ) {
    $deposit = get_post_meta( $data->listing_id, '_security_deposit', true);
    if ($_POST['_security_deposit']) { 
        update_post_meta( $order_id, 'Security Deposit', esc_attr($_POST['_security_deposit']));
    } else {
		update_post_meta( $order_id, '_security_deposit', sanitize_text_field( $deposit ) );
	} 
}

function booking_valtsw_product_and_meta_keys(){
    $domain = 'woocommerce';
    return array(
    '_security_deposit' => __('Security Deposit', $domain),
    '_cleaning_fee' => __('Cleaning Fee', $domain),
    );
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
