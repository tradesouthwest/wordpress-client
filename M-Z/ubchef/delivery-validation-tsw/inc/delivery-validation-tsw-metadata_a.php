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
add_action('wp_ajax_delivery_validation_tsw_data_options', 'delivery_validation_tsw_data_options');
add_action('wp_ajax_nopriv_delivery_validation_tsw_data_options', 'delivery_validation_tsw_data_options');

function delivery_validation_tsw_data_options()
{
    global $woocommerce;
    $product_id = $_POST['id']; //Product ID
    $tsw_menu_data_values = $_POST['data-menu'] ? $_POST['data-menu'] : 'no-data-menu'; //This is custom value sent via AJAX
    session_start();
    $_SESSION['tsw_menu_date_datas'] = $tsw_menu_data_values;

    //Add product to WooCommerce cart.
    $product_id = $_GET['product_id'];
    $quantity = 1; //Or it can be some userinputted quantity
    if( WC()->cart->add_to_cart( $product_id, $quantity )) {

        $cart_url = $woocommerce->cart->get_cart_url();

        $output = array('success' => 1, 
                        'msg' =>'Added the product to your cart', 
                        'cart_url' => $cart_url );
    } else {
        $output = array('success' => 0, 
                        'msg' => 
                        'Something went wrong, please try again');
    }
    wp_die(json_encode($output));
}
/**
 * Add data to WooCommerce data 
 */
add_filter('woocommerce_add_cart_item_data','delivery_validation_tsw_data_to_woocommerce_session',1,2);

function delivery_validation_tsw_data_to_woocommerce_session( $cart_item_data, $product_id )
{

    global $woocommerce;
    session_start();

    if(empty($_SESSION['tsw_menu_date_datas']))
        return $cart_item_data;
    else { 
        $options = $_SESSION['tsw_menu_date_datas'];

        //Unset our custom session variable
        unset($_SESSION['tsw_menu_date_datas']);

        if(empty($cart_item_data))
            return $options;
        else
            return array_merge($cart_item_data, $options);
    }

}
add_filter('woocommerce_get_cart_item_from_session', 'delivery_validation_tsw_get_data_session', 1, 3 );

function delivery_validation_tsw_get_data_session( $item, $values, $key ) {

    //Check if the key exist and add it to item variable.
    if (array_key_exists( 'tsw_menu_date_datas', $values ) )
    {
        $item['tsw_menu_date_datas'] = $values['tsw_menu_date_datas'];
    }
    return $item;
}

add_filter('woocommerce_checkout_cart_item_quantity','delivery_validation_tsw_data_in_cart',1,3); 
add_filter('woocommerce_cart_item_price', 'delivery_validation_tsw_data_in_cart',1,3);
 
function delivery_validation_tsw_data_in_cart( $product_name, $values, $cart_item_key )
{
    global $wpdb;
 
    if(!empty($values['tsw_menu_date_datas']))
    {
        $return_string = "<table>
            <tr>
                <th>" . ucfirst($values['tsw_menu_date_datas']['label']) . "</th>"
                ."<td>" . $values['tsw_menu_date_datas']['value'] . "</td>
            </tr>
            </table>";
        return $return_string;
 
    }
}

add_action('woocommerce_before_cart_item_quantity_zero', 'delivery_validation_tsw_remove_item_meta',1,1);
 
function delivery_validation_tsw_remove_item_meta( $cart_item_key ) {
    global $woocommerce;

    // Get cart
    $cart = $woocommerce->cart->get_cart();
 
    // For each item in cart, if item is upsell of deleted product, delete it
    foreach( $cart as $key => $values)
    {
        if ( $values['tsw_menu_date_datas'] == $cart_item_key )
            unset( $woocommerce->cart->cart_contents[ $key ] );
    }
} 

add_filter( 'woocommerce_hidden_order_itemmeta', 'delivery_validation_tsw_admin_meta_field' );

function delivery_validation_tsw_admin_meta_field( $fields ) {
    $fields[] = 'tsw_menu_date_datas'; //Add all the custom fields here in this array and it will not be displayed.
    return $fields;
} 
