<?php
/**
 * Extend Quickcab Checkout fields
 * @since 1.0.0
 */

/**
 * Add the field to the checkout
 */
//add_action( 'woocommerce_after_order_notes', 'extend_quickcab_checkout_field' );
/*
function extend_quickcab_checkout_field( $checkout ) {
echo '<div id="extend_quickcab_district_message_checkout_field">';    
    woocommerce_form_field( 'district_message', 
        array(
            'type'          => 'text',
            'class'         => array('woo-field-class form-row-wide noborder'),
            'label'         => __('Avviso di districtlazione'),
            'placeholder'   => '',
            'custom_attributes' => array('readonly'=>'readonly'),
        ), $checkout->get_value( 'district_message' )
    );

    echo '</div>';
}
*/
/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'extend_quickcab_checkout_field_update_order_meta' );

function extend_quickcab_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['district_message'] ) ) {
    update_post_meta( $order_id, '', sanitize_text_field( $extquick_message_one ) );
    }
}
/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'extend_quickcab_checkout_field_display_admin_order_meta', 9);

function extend_quickcab_checkout_field_display_admin_order_meta($order){

    echo '<p><strong>'.__('Districts').':</strong> ' . get_post_meta( $order->id, 'district_message', true ) . '</p>';
}

/**
 * Add a custom field (in an order) to the emails
 */
add_filter( 'woocommerce_email_order_meta_fields', 'extend_quickcab_woocommerce_email_order_meta_fields', 10, 3 );

function extend_quickcab_woocommerce_email_order_meta_fields( $fields, $sent_to_admin, $order ) {
$fields['district_message'] = array(
        'label' => __( 'Avviso di districtlazione' ),
        'value' => get_post_meta( $order->id, 'district_message', true ),
    );
    return $fields;
} 