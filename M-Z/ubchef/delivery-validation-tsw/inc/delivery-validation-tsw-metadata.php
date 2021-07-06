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
 * On Click of ‘Add To Cart’ button make an ajax call to store the custom entered data in session
 * @since 1.0.1
 */
//add_action('wp_ajax_delivery_validation_tsw_data_options', 'delivery_validation_tsw_data_options');
//add_action('wp_ajax_nopriv_delivery_validation_tsw_data_options', 'delivery_validation_tsw_data_options');
add_filter( 'woocommerce_add_cart_item_data', 'delivery_validation_tsw_text_to_cart_item', 10, 3 );
/**
 * Add engraving text to cart item.
 *
 * @param array $cart_item_data
 * @param int   $product_id
 * @param int   $variation_id
 *
 * @return array
 */
function delivery_validation_tsw_text_to_cart_item( $cart_item_data, $product_id, $variation_id=false ) {
	$product_menu = empty($_POST['product_menu']) ? 0 
                    : sanitize_text_field($_POST['product_menu']);

	if ( !$product_menu > 0 ) {
		return $cart_item_data;
	}

	$cart_item_data['product_menu'] = $product_menu;

	return $cart_item_data;
}

/**
 * Display engraving text in the cart.
 *
 * @param array $item_data
 * @param array $cart_item
 *
 * @return array
 */
function delivery_validation_tsw_display_engraving_text_cart( $item_data, $cart_item ) {
	if ( empty( $cart_item['product_menu'] ) ) {
		return $item_data;
	}

	$item_data[] = array(
		'key'     => __( 'Delivery date', 'delivery-validation-tsw' ),
		'value'   => wc_clean( $cart_item['product_menu'] ),
		'display' => '',
	);

	return $item_data;
}

add_filter( 'woocommerce_get_item_data', 'delivery_validation_tsw_display_engraving_text_cart', 10, 2 );

/**
 * Add engraving text to order.
 *
 * @param WC_Order_Item_Product $item
 * @param string                $cart_item_key
 * @param array                 $values
 * @param WC_Order              $order
 */
function delivery_validation_tsw_text_to_order_items( $item, $cart_item_key, $values, $order ) {
	if ( empty( $values['product_menu'] ) ) {
		return;
	}

	$item->add_meta_data( __( 'Delivery date', 'delivery-validation-tsw' ), $values['product_menu'] );
}

add_action( 'woocommerce_checkout_create_order_line_item', 'delivery_validation_tsw_text_to_order_items', 10, 4 ); 

//add_action('woocommerce_ajax_added_to_cart', 'delivery_validation_tsw_fdoe_ajax', $product_id);
//add_action('woocommerce_update_cart_action_cart_updated');
function delivery_validation_tsw_fdoe_ajax($product_id='')
{ 
$product_menu = empty($_POST['product_menu']) ? 0 
                    : sanitize_text_field($_POST['product_menu']);
ob_start();
if ($product_menu > 0) {

    $hash = WC()->cart->add_to_cart($product_id, $product_menu );
    $passed_vali = true;
    $product_quantity_new = absint(1);
    $status = false;
            if (false != $hash) {
                do_action('woocommerce_ajax_added_to_cart', $product_id);
                do_action('woocommerce_update_cart_action_cart_updated');

                $data = array(
                    'success' => true,
                    'overstock' => isset( $overstock) ? $overstock:false,
                    'is_sold_indi'=> isset($is_sold_indi ) ? $is_sold_indi :false,
                    'status' => $status,
                    'product_quantity' => $product_quantity_new,
                    'passed_vali' => true

                );
            }
            $alert = ob_get_clean();
    $data1 = array_merge($data, array('alert' => $alert));
    wp_send_json($data1);
    }
}
