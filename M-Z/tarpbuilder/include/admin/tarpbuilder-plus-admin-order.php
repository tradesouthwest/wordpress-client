<?php 
/**
 * @package    tarpbuilder
 * @subpackage includes/tarpbuilder-quantity
 * @since      1.0.2
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Add order item meta.
add_action( 'woocommerce_add_order_item_meta', 
                                'tarpbuilder_plus_update_order_item_meta',10, 3);
function tarpbuilder_plus_update_order_item_meta ( $item_id, $item_values, $item_key ) 
{
   if( ! empty( $item_values['pa_custom-tarp'] ) ){
        wc_update_order_item_meta( $item_id, 'Custom Tarp', sanitize_text_field( 
                                   $item_values['pa_custom-tarp'] ) ); }
    if( ! empty( $item_values['pa_custom-tarp-width'] ) ) {
        wc_update_order_item_meta( $item_id, 'Width', sanitize_text_field( 
                                   $item_values['pa_custom-tarp-width'] ) ); }
    if( ! empty( $item_values['pa_custom-tarp-length'] ) ) {
        wc_update_order_item_meta( $item_id, 'Length', sanitize_text_field( 
                                   $item_values['pa_custom-tarp-length'] ) ); }

}
// Display the extra data in the order admin panel
add_action( 'woocommerce_admin_order_data_after_order_details', 'tarpbuilder_display_order_data_in_admin', 10, 1 );
function tarpbuilder_display_order_data_in_admin( $order ){
if( $value = $order->get_meta( 'attribute_pa_custom-tarp' ) ) {
        echo '<div class="order_data_column">
        <p><strong>' . __( 'Custom Tarp', "woocommerce" ) . ':</strong> ' . $value . '</p>
        </div>';
    }
    if( $value = $order->get_meta( 'attribute_pa_custom-tarp-width' ) ) {
        echo '<div class="order_data_column">
        <p><strong>' . __( 'Width', "woocommerce" ) . ':</strong> ' . $value . '</p>
        </div>';
    }
    if( $value = $order->get_meta( 'attribute_pa_custom-tarp-length' ) ) {
        echo '<div class="order_data_column";>
        <p><strong>' . __( 'Length', "woocommerce" ) . ':</strong> ' . $value . '</p>
        </div>';
    }
    if( $value = $order->get_meta( 'attribute_pa_approx-spacing' ) ) {
        echo '<div class="order_data_column">
        <p><strong>' . __( 'Tarp Cost $', "woocommerce" ) . ':</strong> ' . $value . '</p>
        </div>';
    }
}
/**
 * e.)
 * Add the field to order emails
 * 
 * @since 1.0.1
 */
add_filter('woocommerce_email_order_meta_keys','tarpbuilder_custom_checkout_field_order_meta_keys');
      
function tarpbuilder_custom_checkout_field_order_meta_keys( $keys ) {
$keys['Custom Tarp']          = 'attribute_pa_custom-tarp';
    $keys['Custom Tarp Width']          = 'attribute_pa_custom-tarp-width';
    $keys['Custom Tarp Length']         = 'attribute_pa_custom-tarp-length';
    $keys['Approx Spacing'] = 'attribute_pa_approx-spacing';
    return $keys;
} 