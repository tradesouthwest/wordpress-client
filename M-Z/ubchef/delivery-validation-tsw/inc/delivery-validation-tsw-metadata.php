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
        'description' => __('Example 23/24 July would be entered as 23/07/2021. Leave empty for weekly or not dated.','woocommerce'),
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
