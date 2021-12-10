<?php 
/**
 * @since:     1.0.0
 * Author:     Tradesouthwest
 * Author URI: http://tradesouthwest.com
 * @package    delivery-validation-tsw
 * @subpackage inc/delivery-validation-tsw-public
*/
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Add data to WooCommerce data 
 * @since 1.0.1
 *
 * @param array Values of text field in product meta
 */
function delivery_validation_tsw_add_text_input() 
{
    woocommerce_wp_text_input([
        'id'        => 'delivery_validation_tsw', 
        'label'      => 'Delivery Date Validation', 
        'placeholder' => 'Date MUST be formatted', 
        'wrapper_class' => 'show_if_simple',
        'class'         => 'short',
        'type'          => 'text',
        'desc_tip'      => false,
        'description' => __('Only the first date (Friday) required. Example 23/24 July would be entered as 23/07/2021. Leave empty for not dated products.','woocommerce'),
    ]);
}
add_action( 'woocommerce_product_options_general_product_data', 'delivery_validation_tsw_add_text_input' );

/**
 * Add data to WooCommerce data 
 * @since 1.0.1
 *
 * @param string $text Value of text field in product meta
 */
function delivery_validation_tsw_save_validated_text( $post_id ) 
{
    global $post;

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
    
    if ( ! current_user_can( 'edit_post', $post_id ) )
        return;
    
    $post_id = $post->ID;
    $product = wc_get_product( $post_id );
    $text    = isset( $_POST['delivery_validation_tsw'] ) 
                    ? $_POST['delivery_validation_tsw'] : '';
    $product->update_meta_data( 'delivery_validation_tsw', sanitize_text_field($text));
    $product->save();
}
add_action( 'woocommerce_process_product_meta', 'delivery_validation_tsw_save_validated_text');


/**
 * Add validation to WooCommerce postcode data 
 * @since 1.0.1
 *
 * @param string $text Value of text field in product meta
 */

//add_filter( 'woocommerce_checkout_fields', 'delivery_validation_tsw_postalcodes_validation' );
function delivery_validation_tsw_postalcodes_validation( $woo_checkout_fields_array ) {
	unset( $woo_checkout_fields_array['shipping']['shipping_postcode']['validate'] );
	return $woo_checkout_fields_array;
}

add_action('woocommerce_checkout_process', 'delivery_validation_tsw_postalcodes_validate');
function delivery_validation_tsw_postalcodes_validate() 
{
    $shipping_postcode = filter_input( INPUT_POST, 'shipping_postcode' );
    $exception_values  = ( empty( get_option('postcode_exclusion_values') ) )
    				       ? '' : get_option('postcode_exclusion_values');
    $arr_values = explode(',', $exception_values );
	
    if( is_array( $arr_values ) ) {

        // find anything before a whitespace
        $substring = substr($shipping_postcode, 0, strpos($shipping_postcode, ' '));

        if ( $substring ) { 
            $errmsg = ( empty( get_option('postcode_exclusion_verify') ) )
    		? 'This Postcode is outside of our delivery zones. Please call us if you have any questions.' 
            : get_option('postcode_exclusion_verify');
        
            if ( in_array( $substring, $arr_values ) ) {

                return wc_add_notice( $errmsg . $substring, 'error' );

                } else {

                    return false;
            } 
        }
    } else {

            return false;
    } 
        
        //return false;
}

add_filter( 'woocommerce_checkout_fields' , 'delivery_validation_tsw_postalcodes_placeholders', 9999 );
function delivery_validation_tsw_postalcodes_placeholders( $f ) {

	// first name can be changed with woocommerce_default_address_fields as well
	$f['shipping']['shipping_postcode']['placeholder'] = __( 'Certain postal codes can not be delivered','delivery-validation-tsw');
    $f['billing']['billing_postcode']['placeholder'] = __( 'Certain postal codes can not be delivered','delivery-validation-tsw');
	
        return $f;

}

/**
 * Add data to WooCommerce data 
 * @since 1.0.1
 *
 * @param string $text Value of text field in product meta
 */
function delivery_validation_tsw_add_admin_scripts() 
{

    $content = '';
    $content .= '.delivery_validation_tsw_field .description{float:left;line-height:1.485;}.iconic-wds-order-delivery-details{background:#ffe4e1;padding: 0px 2px;}'; 
    wp_enqueue_style(  'delivery-validation-admin-style' );
    wp_register_style( 'delivery-validation-admin-set', false );
    wp_enqueue_style(  'delivery-validation-admin-set' );
    wp_add_inline_style( 'delivery-validation-admin-set', $content );
}
add_action( 'admin_enqueue_scripts', 'delivery_validation_tsw_add_admin_scripts' ); 
