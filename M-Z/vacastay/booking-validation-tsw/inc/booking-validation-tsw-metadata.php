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

function booking_valtsw_get_author_meta_email()
{
    global $post;
    $author_id = $owner_email = '';
    
    $author_id = 3;
    
    if($author_id) {
        $owner_email = get_the_author_meta( 'user_email', $author_id );
    } else {
        $owner_email = 'admin@vacastays.com';
    }
        return esc_attr($owner_email);
}
add_action( 'woocommerce_after_order_itemmeta', 'booking_valtsw_custom_meta_customized_display',10, 3 );

function booking_valtsw_custom_meta_customized_display( $item_id, $item, $product ){

    global $post;
    $adata = $bdata = '1';
    $pid   = '';
    $adata = get_post_meta( $pid, '_security_deposit', true);
    $bdata = get_post_meta( $pid, '_cleaning_fee', true);
    $pid   = booking_valtsw_get_product_id($post->ID);
    if('' != $adata)
	{
	    $html_string = '<span>';
		$html_string .= '<strong style="color:#777;font-weight:600">'. __('Security Deposit') . ':</strong> $' . esc_attr($adata) . ' </span> ';
		
	        echo $html_string;			
	}
	if('' != $bdata )
	{
	    $htm_string = ' | <span> ';
	    $htm_string .= '<strong style="color:#777;font-weight:600">'. __('Cleaning Costs') . ':</strong> $' . esc_attr($bdata) . '</span>';
		
	        echo $htm_string;			
	}
	
	

}
function booking_valtsw_get_product_id($post)
{
    global $post;
    
    $order = wc_get_order($post);
    $order_id = $order->get_id();
    
        return absint($order_id - 3);
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
        'label'       => __('Security Deposit and Cleaning Cost'),
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
    '_cleaning_fee' => __('Cleaning Costs', $domain),
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
