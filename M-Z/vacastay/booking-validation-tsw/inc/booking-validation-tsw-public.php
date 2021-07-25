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

// Display html for costs
add_action('tsw_get_additional_costs_html', 'booking_validation_tsw_get_additional_costs_html' );
function booking_validation_tsw_get_additional_costs_html($order) 
{
    global $woocommerce;
    $add_depsoit = $add_cleaning = 0;
    $listing_id  = get_post_meta( $order->get_id(), 'listing_id', true ); 
	$add_deposit = get_post_meta( $listing_id, '_security_deposit', true ); 
	$add_clean   = get_post_meta( $listing_id, '_cleaning_fee', true ); 
	$_feez       = $add_deposit + $add_clean;
    $add_feez    = round($_feez, 2);            
	if ( $add_feez > 0 )
	echo '<span> Security and Cleaning Fee ' . get_woocommerce_currency_symbol() . '' . $add_feez . '</span>';
}
add_filter( 'woocommerce_add_cart_item_data', 'tsw_display_deposit_data_in_cart',2,4);
/** 
 * @_cart_item_price only returns data, does not total
 * display ppd next to price
 * @param  string $title
 * @param  array $cart_item
 * @param  array $cart_item_key
 * @return array $subtotal, $this, $item, $inc_tax, $round
 */ 
add_action('tsw_get_additional_costs_items', 'booking_validation_tsw_get_additional_costs_items' );
function booking_validation_tsw_get_additional_costs_items($order) 
{
    global $woocommerce;
    //$order_subtotal = $order->get_subtotal(); 
    $add_deposit = $add_cleaning = 0;
    $listing_id  = get_post_meta( $order->get_id(), 'listing_id', true ); 
	$add_deposit = get_post_meta( $listing_id, '_security_deposit', true ); 
	$add_clean   = get_post_meta( $listing_id, '_cleaning_fee', true ); 
	
	$add_feez       = $add_deposit + $add_clean;
    
        return round($add_feez, 2);
} 
/**
 * Checkout field Validation
 * @since 1.0.1
 * https://rudrastyh.com/woocommerce/order-items.html
 * @param string $field Value of text field in product meta
*/
//add_action( 'woocommerce_single_product_summary', 'booking_validation_tsw_checkout_html' );
add_action('booking_validation_after_booking_summary','booking_validation_tsw_checkout_html' );
function booking_validation_tsw_checkout_html($data)
{
    $deposit_value = get_post_meta($data->listing_id,"_security_deposit",true);  
	$cleaning_value = get_post_meta($data->listing_id,"_cleaning_fee",true); 
	$currency_symbol = get_woocommerce_currency_symbol();
	
	if( '' != $deposit_value ) : 
	ob_start();
    echo '<li id="booking-confirmation-security-deposit" style="width: 100%;color: #888;margin: 2px 0;transition: 0.2s;cursor: default;overflow: hidden;">
			<h5 style="font-weight:600">Security Deposit <span style="float: right;font-weight: 400;text-align: right;"> ' .$currency_symbol. '' .$deposit_value . '</span></h5>
			<input id="_security_deposit" type="hidden" name="_security_deposit" value="' .$deposit_value . '">
		</li>';
	else: 
		    echo '<li>&nbsp;</li>';
		    endif;
	if( '' != $cleaning_value ) : 	
	echo '<li id="booking-confirmation-cleaning-fee" style="width: 100%;color: #888;margin: 2px 0;transition: 0.2s;cursor: default;overflow: hidden;">
			<h5 style="font-weight:600">Cleaning Fee <span style="float: right;font-weight: 400;text-align: right;"> ' .$currency_symbol. '' .$cleaning_value . '</span></h5>
			<input id="_cleaning_fee" type="hidden" name="_cleaning_fee" value="' .$cleaning_value . '">
		</li>';
	else: 
		    echo '<li>&nbsp;</li>';
		    endif;
	
    $output = ob_get_clean();
    	echo $output;
		return false;
}

/**
 * add post meta (fees) to line item (_order_items)
 * 
 */ 
add_filter( 'woocommerce_add_cart_item_data', 'tsw_display_deposit_data_in_cart',2,4); 
function tsw_display_deposit_data_in_cart($cart_item_data,$product_id,$variation_id,$quantity)
{

    if( ! empty( $_POST['_security_deposit']))
    {
        $cart_item_data['_security_deposit'] = $_POST['_security_deposit'];
	}
	if( ! empty( $_POST['_cleaning_fee']))
    {
        $cart_item_data['_cleaning_fee'] = $_POST['_cleaning_fee'];
	}
		return $cart_item_data;
}

add_action( 'woocommerce_add_order_item_meta', 'misha_order_item_meta', 10, 2 );

// $item_id – order item ID
// $cart_item[ 'product_id' ] – associated product ID
function misha_order_item_meta( $item_id, $cart_item ) {

	// get product meta
	$security_deposit = get_post_meta( $cart_item[ 'product_id' ], '_security_deposit', true );
	$cleaning_fee = get_post_meta( $cart_item[ 'product_id' ], '_cleaning_fee', true );

	// if not empty, update order item meta
 	if( ! empty( $security_deposit ) ) {
		wc_update_order_item_meta( $item_id, '_security_deposit', $security_deposit );
	}
	if( ! empty( $cleaning_fee ) ) {
		wc_update_order_item_meta( $item_id, '_cleaning_fee', $cleaning_fee );
	}
	
}

/**
 * For debug only
 */
//add_action('wp_footer', 'booking_validation_tsw_meta_html');
function booking_validation_tsw_metavalues($meta)
{
	$meta = (empty ( $meta ) ) ? '' : $meta;

	$meta_key = get_post_meta( get_the_ID(), $meta, true );

		return $meta_key;
}
