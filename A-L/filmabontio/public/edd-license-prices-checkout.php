<?php 
/**
 * Upgrade functions
 *
 * @package     edd-license-prices/checkout
 * @since       1.0.2
 *  
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the meta for download.
 *
 * @param integer $download_id
 * @return void
 */
//add_action('wp_head', 'edd_license_prices_output_all_postmeta' );
function edd_license_prices_output_all_postmeta() {
	if( is_single('download-a1')) {
		$postmetas = get_post_meta(get_the_ID());

		foreach($postmetas as $meta_key=>$meta_value) {
			echo $meta_key . ' : ' . $meta_value[0] . '<br/>';
		}
	}
}
/**
 * Set the cookie for license.
 * page that receives the AJAX request, set $_SESSION
 * 
 * @return void
 */


/**
 * Get the cookie when lisensed chosen.
 *
 * @param integer $download_id
 * @return void
 */
function edd_license_prices_get_cookie()
{
	if(isset($_COOKIE['edd_license_upgraded'])) {
	return true;
	}
}
function edd_license_prices_after_purchase_or_removed_item()
{
	unset($_COOKIE['edd_license_upgraded']);
}

/**
* Filter cart item price name (similar to above)
*/
function edd_license_prices_get_cart_item_price_name( $name, $item_id, $price_id, $item ) 
{ 
	global $edd_options;
	// Throw default text if not single priced item_id
	$default_license_text = esc_html__(' (with Standard License)', 'edd_cp');
	//$price_id = isset( $options['price_id'] ) ? $options['price_id'] : false;

	if( edd_license_prices_has_license_pricing( $item_id ) 
		&& isset( $item['options']['price_id'] ) ) {
	
		if ( edd_single_price_option_mode( $item_id ) ) {
			// && isset( $item['options']['custom_price'][ $price_id ]) rmvd
			return $name . __( ' (Upgraded License)', 'edd_cp' );
		} else {
			
			return $name . __( ' (Upgraded License Options)', 'edd_cp' );
		}

	}
	return $name . ' ' . $default_license_text;
}
add_filter( 'edd_get_cart_item_price_name', 'edd_license_prices_get_cart_item_price_name', 10, 4 );
