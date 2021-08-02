<?php
/**
 * @since ver: 1.0.0
 * Author:     Tradesouthwest
 * Author      URI: http://tradesouthwest.com
 * @package    sound_absorption_calc
 * @subpackage admin/booking-validation-tsw-admin
*/
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Changing a meta title
 * @param  string        $key  The meta key
 * @param  WC_Meta_Data  $meta The meta object
 * @param  WC_Order_Item $item The order item object
 * @return string        The title
 */
//add_action( 'woocommerce_after_order_itemmeta', 'booking_validation_tsw_admin_order_item_custom', 10, 3 );
function booking_validation_tsw_admin_order_item_custom( $item_id, $item, $product ){
    // Only "line" items and backend order pages
    if( ! ( is_admin() && $item->is_type('line_item') ) )
        return;

    $security_deposit = $item->get_meta('_security_deposit'); // Get custom item meta data (array)
    $cleaning_fee     = $item->get_meta('_cleaning_fee'); 
    if( ! empty($security_deposit) ) {
        // Display a custom download button using custom meta for the link
        echo '<table><tbody><tr><td>Security Deposit ' . $security_deposit . '</td></tbody></table>';
    }
    if( ! empty($security_deposit) ) {
        // Display a custom download button using custom meta for the link
        echo '<table><tbody><tr><td>Cleaning Fee ' . $cleaning_fee . '</td></tbody></table>';
    }
    
} 

//add_action( 'woocommerce_after_order_itemmeta', 'booking_validation_tsw_order_item_custom_fields', 10, 2 );
function booking_validation_tsw_order_item_custom_fields( $item_id, $item ) {
    // Targeting line items type only
    if( $item->get_type() !== 'line_item' ) return;
     woocommerce_wp_text_input( array(
        'id'            => "_security_deposit[$item_id]",
        'label'         => __( 'Security Deposit', 'cfwc' ),
        'description'   => __( 'Change deposit', 'ctwc' ),
        'desc_tip'      => true,
        'class'         => 'order-item-field',
        'style'         => 'width:10em;padding:0px',
        'value'         => wc_get_order_item_meta( $item_id, '_security_deposit' ),
    ) );
     woocommerce_wp_text_input( array(
        'id'            => "_cleaning_fee[$item_id]",
        'label'         => __( 'Cleaning Fee', 'cfwc' ),
        'description'   => __( 'Change fee', 'ctwc' ),
        'desc_tip'      => true,
        'class'         => 'order-item-field',
        'style'         => 'width:10em;padding:0px',
        'value'         => wc_get_order_item_meta( $item_id, '_cleaning_fee' ),
    ) );
}

// Save the custom field value
//add_action('save_post', 'booking_validation_tsw_order_item_custom_fields_save', 100, 2 );
function booking_validation_tsw_order_item_custom_fields_save( $post_id, $post ){
    if ( 'shop_order' !== $post->post_type )
        return $post_id;

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    if ( ! current_user_can( 'edit_shop_order', $post_id ) )
        return $post_id;

    $order = wc_get_order( $post_id );
    foreach ( $order->get_items() as $item_id => $item ) {
        if( isset( $_POST['_security_deposit[$item_id]'] ) ) {
            $item->update_meta_data( '_security_deposit', sanitize_text_field( $_POST['_security_deposit[$item_id]'] ) );
            $item->save();
        }
        if( isset( $_POST['_cleaning_fee[$item_id]'] ) ) {
            $item->update_meta_data( '_cleaning_fee', sanitize_text_field( $_POST['cleaning_fee[$item_id]'] ) );
            $item->save();
        }
    }
    $order->save();
}

