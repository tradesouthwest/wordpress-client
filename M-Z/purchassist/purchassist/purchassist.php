<?php
/**
 * Plugin Name:       Purchassist
 * Plugin URI:        http://themes.tradesouthwest.com/wordpress/plugins/
 * Description:       Add text fields to purchasing assist product
 * Version:           1.0.1
 * Author:            Larry Judd
 * Author URI:        https://codeable.io/developers/larry-judd/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       purchassist
 * Domain Path:       /languages
 *
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if (!defined('PURCHASSIST_VER')) { define('PURCHASSIST_VER', "1.0.1"); }

if (!defined('PURCHASSIST_URL')) { define( 'PURCHASSIST_URL', plugin_dir_url(__FILE__)); }

//activate/deactivate hooks
function purchase_assist_tsw_plugin_activation() {
  // Check for WooCommerce
  if (!class_exists('WooCommerce')) {
	echo('<div class="error">
	<p>This plugin requires that WooCommerce is installed and activated.</p>
	</div></div>');
	return;
  }
}
function purchase_assist_tsw_plugin_deactivation() {
        return false;
}
/**
 * InitialiZe - load in translations
 * @since 1.0.0
 */
function purchase_assist_tsw_loadtranslations () {
    $plugin_dir = basename(dirname(__FILE__)).'/languages';
    load_plugin_textdomain( 'purchassist', false, $plugin_dir );
}
add_action('plugins_loaded', 'purchase_assist_tsw_loadtranslations');

//activate and deactivate registered
register_activation_hook( __FILE__, 'purchase_assist_tsw_plugin_activation');
register_deactivation_hook( __FILE__, 'purchase_assist_tsw_plugin_deactivation');

/**
 * Plugin Scripts
 *
 * Register and Enqueues plugin helper scripts
 *
 * @since 1.0.0 
 */
//if ( !function_exists('purchase_assist_tsw_addplugin_scripts') ) :  
function purchase_assist_tsw_addplugin_scripts() 
{
    $ver      = "1.0.1";
    wp_enqueue_style( 'purchassist-style',  PURCHASSIST_URL
                      . 'css/purchassist-style.css', array(), $ver, false );
    // Register Scripts
    wp_register_script( 'purchassist-fields', 
					   plugins_url('js/purchassist-fields.js', __FILE__ ), 
					   array('jquery'), $ver, true );
   
    wp_enqueue_script( 'purchassist-fields' );
     
}
//endif;
// initiate hooks
add_action( 'wp_enqueue_scripts', 'purchase_assist_tsw_addplugin_scripts');
 
/**
 * @since 1.0.1
 * Add an input field to products - WooCommerce
 * Show purchase input field above Add to Cart
 *
 * @return HTML
 */
add_action( 'woocommerce_before_add_to_cart_button', 'purchase_assist_tsw_product_add_on', 9 );
function purchase_assist_tsw_product_add_on() {

    $value = isset( $_POST['purchase_text_add_on'] ) 
                ? sanitize_text_field( $_POST['_purchase_text_add_on'] ) : '';

    echo '<div id="purchase_assist_fields">
     <h5>Purchase Item URL <abbr class="required" title="Link to your product *required">*</abbr></h5>
    <div class="patsw-line-items">
        
        <div id="purchasefield_1" class="patsw-form-field">
            <p><input id="purchaseitem_1" type="text" value="" 
            class="patsw-item" data-fieldup="1"><span><input type="button" class="but_rmv" 
            name="purchasefield_1" value="X"></p>
            
        </div>

    </div>
        <div>
            <p><input type="button" id="but_add" value="Add new"></p>
        </div>
    </div>';

}

// Throw error if purchase input field empty

add_filter( 'woocommerce_add_to_cart_validation', 
    'purchase_assist_tsw_product_add_on_validation', 10, 3 );

function purchase_assist_tsw_product_add_on_validation( $passed, $product_id, $qty ){

   if( isset( $_POST['purchase_text_add_on'] ) 
        && sanitize_text_field( $_POST['purchase_text_add_on'] ) == '' ) {

      wc_add_notice( 'Purchase URL is a required field', 'error' );

      $passed = false;

   }

   return $passed;

}
// Save purchase input field value into cart item data

add_filter( 'woocommerce_add_cart_item_data', 'purchase_assist_tsw_product_add_on_cart_item_data', 10, 2 );

function purchase_assist_tsw_product_add_on_cart_item_data( $cart_item, $product_id ){

    if( isset( $_POST['purchase_text_add_on'] ) ) {

        $cart_item['purchase_text_add_on'] = sanitize_text_field( $_POST['purchase_text_add_on'] );

    }

    return $cart_item;

}

// Display purchase input field value @ Cart

add_filter( 'woocommerce_get_item_data', 'purchase_assist_tsw_product_add_on_display_cart', 10, 2 );

function purchase_assist_tsw_product_add_on_display_cart( $data, $cart_item ) {

    if ( isset( $cart_item['purchase_text_add_on'] ) ){

        $data[] = array(

            'name' => 'Purchase URL Add-On',

            'value' => sanitize_text_field( $cart_item['purchase_text_add_on'] )

        );

    }

    return $data;

}

// Save purchase input field value into order item meta

add_action( 'woocommerce_add_order_item_meta', 'purchase_assist_tsw_product_add_on_order_item_meta', 10, 2 );

function purchase_assist_tsw_product_add_on_order_item_meta( $item_id, $values ) {

    if ( ! empty( $values['purchase_text_add_on'] ) ) {

        wc_add_order_item_meta( $item_id, 'Purchase URL Added', $values['purchase_text_add_on'], true );

    }

}

// Display purchase input field value into order table

add_filter( 'woocommerce_order_item_product', 'purchase_assist_tsw_product_add_on_display_order', 10, 2 );

function purchase_assist_tsw_product_add_on_display_order( $cart_item, $order_item ){

    if( isset( $order_item['purchase_text_add_on'] ) ){

        $cart_item['purchase_text_add_on'] = $order_item['purchase_text_add_on'];

    }

    return $cart_item;

}

// Display purchase input field value into order emails

add_filter( 'woocommerce_email_order_meta_fields', 'purchase_assist_tsw_product_add_on_display_emails' );

function purchase_assist_tsw_product_add_on_display_emails( $fields ) {

    $fields['purchase_text_add_on'] = 'Purchase URL Added';

    return $fields;

}

?>