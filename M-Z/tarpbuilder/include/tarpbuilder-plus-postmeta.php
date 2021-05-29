<?php 
/**
 * @package    tarpbuilder-plus
 * @subpackage include/tarpbuilder-postmeta.php
 * @since      2.0.1
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * *****************************************
 * Post meta and Cart meta
 * 
 * **************************************** */
// PM-1 Add Number Field to admin General tab
add_action( 'woocommerce_product_options_general_product_data', 'tarpbuilder_plus_general_product_data_field', 12 );
add_action('woocommerce_product_options_sku', 'tarpbuilder_plus_general_product_data_field', 12 );
// PM-2 Save meta 
add_action( 'woocommerce_process_product_meta', 'tarpbuilder_plus_save_tarpbuilder_fee' );

// OM-1 order meta
add_filter( 'woocommerce_order_item_product', 'tarpbuilder_plus_order_item_product', 10, 2 ); 
// OM-2 order item meta
//add_filter( 'woocommerce_order_item_display_meta_key', 'tarpbuilder_plus_change_order_item_meta_title', 20, 3 );
// OM-3 currently using Woo Additional Notes
//add_action('woocommerce_after_order_notes', 'tarpbuilder_plus_notes_checkout_field');
/** PM-1
 * Add fee field to product editor
 * 
 * @param         array
 * @param string $options
 */
function tarpbuilder_plus_general_product_data_field() 
{   
    $currency_symbol = get_woocommerce_currency_symbol();
    $q_symbol        = "&#33;";
    echo '<div class="tarpbuilder-postmeta" style="visibility: hidden;">';
    woocommerce_wp_text_input( array( 
        'id'    => '_tarpbuilder_plus_fee', 
        'name'    => '_tarpbuilder_plus_fee', 
        'class'    => 'untzr-fee',
        'placeholder' => '', 
        'label'     => __( 'Incremental Cost ', 'tarpbuilder' ) . $currency_symbol, 
        'description'   => __( ' Price per grommet spacing. Leave blank if Custom Tarp!', 'tarpbuilder' ), 
        'type'            => 'text'
    ) );
    woocommerce_wp_text_input( array( 
        'id'    => 'attribute_pa_custom-tarp-width', 
        'name'    => '_attribute_pa_custom-tarp-width', 
        'class'    => 'untzr-fee',
        'placeholder' => '', 
        'label'     => __( 'Width: ', 'tarpbuilder' ) . esc_attr($q_symbol), 
        'description'   => __( ' Leave Blank if Custom Tarp', 'tarpbuilder' ), 
        'type'            => 'text'
    ) );
    woocommerce_wp_text_input( array( 
        'id'    => 'attribute_pa_custom-tarp-length', 
        'name'    => '_attribute_pa_custom-tarp-length', 
        'class'    => 'untzr-fee',
        'placeholder' => '', 
        'label'     => __( 'Length: ', 'tarpbuilder' ) . esc_attr($q_symbol), 
        'description'   => __( ' Leave Blank if Custom Tarp', 'tarpbuilder' ), 
        'type'            => 'text'
    ) );
    echo '</div>';
}

/** PM-2
 * Save the whales... I mean save post meta
 * Hook callback functions to save custom fields 
 *
 * @param meta_id[int] 
 * @param post_id[int] 
 * @param meta_key[_tarpbuilder_plus_fee] 
 * @param meta_value[int]
 * @since 2.0.1
 */

function tarpbuilder_plus_save_tarpbuilder_fee( $post_id ) 
{
    //global $product;

    $custom_field_value = isset( $_POST['_tarpbuilder_plus_fee'] ) 
                               ? $_POST['_tarpbuilder_plus_fee'] : '';
    $custom_field_clean = sanitize_text_field( $custom_field_value );
    // width
    $custom_field_width = isset( $_POST['_attribute_pa_custom-tarp-width'] ) 
                               ? $_POST['_attribute_pa_custom-tarp-width'] : '';
    $custom_width_clean = sanitize_text_field( $custom_field_width );
    // length
    $custom_field_length = isset( $_POST['_attribute_pa_custom-tarp-length'] ) 
                               ? $_POST['_attribute_pa_custom-tarp-length'] : '';
    $custom_length_clean = sanitize_text_field( $custom_field_length );
    // added notes
    
    $product = wc_get_product( $post_id );

    if ( ''!= $custom_field_value ){
    $product->update_meta_data( '_tarpbuilder_plus_fee', $custom_field_clean );
    $product->save();
    }
    if ( ''!= $custom_field_width ){
    $product->update_meta_data( '_attribute_pa_custom-tarp-width', $custom_width_clean );
    $product->save();
    }
    if ( ''!= $custom_field_length ){
    $product->update_meta_data( '_attribute_pa_custom-tarp-length', $custom_length_clean );
    $product->save();
    }
   
}

/** OM-1
 * Show custom field in order overview
 * filter 'woocommerce_order_item_product'
 * @param array $cart_item
 * @param array $order_item
 * @return array
 */ 

function tarpbuilder_plus_order_item_product( $cart_item, $order_item )
{
    if( isset( $order_item['attribute_pa_custom-tarp'] ) ){ 
        $cart_item_meta['attribute_pa_custom-tarp'] = 
        $order_item['attribute_pa_custom-tarp']; 
    }
    if( isset( $order_item['tarpbuilder_plus_begin_date'] ) ){ 
        $cart_item_meta['tarpbuilder_plus_begin_date'] = 
        $order_item['tarpbuilder_plus_begin_date']; 
    }
    if( isset( $order_item['_attribute_pa_custom-tarp-width'] ) ){ 
        $cart_item_meta['_attribute_pa_custom-tarp-width'] = 
        $order_item['_attribute_pa_custom-tarp-width']; 
    }
    if( isset( $order_item['_attribute_pa_custom-tarp-length'] ) ){ 
        $cart_item_meta['_attribute_pa_custom-tarp-length'] = 
        $order_item['_attribute_pa_custom-tarp-length']; 
    }  
        return $cart_item;
}

/** Static
 * Filterting the meta data of an order item.
 * @param  array         $meta_data Meta data array
 * @param  WC_Order_Item $item      Item object
 * @return array                    The formatted meta
 */
function tarpbuilder_plus_change_formatted_meta_data( $meta_data, $item ) 
{
    $new_meta = array();
    foreach ( $meta_data as $id => $meta_array ) {
        // We are removing the meta with the key 'tarpbuilder' from the whole array.
        if ( 'tarpbuilder_' === $meta_array->key ) { continue; }
        $new_meta[ $id ] = $meta_array;
    }
    return $new_meta;
}
/** OM-2
 * Changing a meta title
 * @param  string        $key  The meta key
 * @param  WC_Meta_Data  $meta The meta object
 * @param  WC_Order_Item $item The order item object
 * @return string        The title
 */
function tarpbuilder_plus_change_order_item_meta_title( $key, $meta, $item ) 
{
    $wnddays = $wndbegin = '';
    $wnddays = get_option('tarpbuilder_plus_options')['tarpbuilder_plus_csdescription_field'];
    $wndbegin = get_option('tarpbuilder_plus_options')['tarpbuilder_plus_begin_date'];
    // By using $meta-key we are sure we have the correct one.
    if ( 'attribute_pa_custom-tarp' === $meta->key ) { $key = esc_attr($wnddays); }
    if ( 'tarpbuilder_plus_begin_date' === $meta->key ) { $key = $wndbegin; }
    if ( 'tarpuilder_plus_product_notes' === $meta_key) { $key = esc_html__('Customer Notes', 'woocommerce'); }
    if ( 'tarpuilder_width_needed' === $meta_key) { $key = esc_html__('Width: ', 'woocommerce'); }
    if ( 'tarpuilder_length_needed' === $meta_key) { $key = esc_html__('Lendth: ', 'woocommerce'); }

    return $key;
} 


/**
 * Add the field to order emails
 **/
add_filter('woocommerce_email_order_meta_keys', 'arpbuilder_plus_notes_field_order_meta_keys');

function arpbuilder_plus_notes_field_order_meta_keys( $keys ) 
{
 $wnddays = $wndbegin = '';
    $wnddays = get_option('tarpbuilder_plus_options')['tarpbuilder_plus_csdescription_field'];
    $wndbegin = get_option('tarpbuilder_plus_options')['tarpbuilder_plus_begin_date'];

	$keys[] = $wnddays;
    $keys[] = 'Width';
        $keys[] = 'Length';
	return $keys;
}
