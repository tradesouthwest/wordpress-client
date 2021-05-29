<?php 
/**
 * @package    tarpbuilder
 * @subpackage includes/tarpbuilder-quantity
 * @since      2.0.1
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// PB-2 $cart_item, $product_id
add_filter( 'woocommerce_add_cart_item_data', 'tarpbuilder_plus_add_cart_item_data', 10, 2 );
// PB-3 $cart_item, $values
add_filter( 'woocommerce_get_cart_item_from_session', 'tarpbuilder_plus_get_cart_item_from_session', 20, 2 );
// PB-5 $cart_item, $product_id
add_filter( 'woocommerce_get_item_data', 'tarpbuilder_plus_get_item_data', 10, 2 );
//PB-6 $item_id, $values
add_action( 'woocommerce_add_order_item_meta', 'tarpbuilder_plus_add_order_item_meta', 10, 2 );


/**
 * show price in single product page
 */
add_action( 'woocommerce_after_add_to_cart_button', 'tarpbuilder_plus_display_tarp_charged', 10 );
function tarpbuilder_plus_display_tarp_charged()
{
    global $post;
    $tpconting = tarpbuilder_plus_contingencies();
    if( !$tpconting ) 
        return;

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
            return;
    $product = wc_get_product( $post->ID );
    //'attributes'         => array(), $data
    $vari_price = $product->get_price(); //$_product->get_price();
    echo '<p>$' . esc_html($vari_price) . '</p>';

}
  add_action( 'wp_ajax_woocommerce_add_to_cart_variable_rc', 'woocommerce_add_to_cart_variable_rc_callback' ); 
  add_action( 'wp_ajax_nopriv_woocommerce_add_to_cart_variable_rc', 'woocommerce_add_to_cart_variable_rc_callback' ); 
   
function woocommerce_add_to_cart_variable_rc_callback() 
{ 
      return false;
} 

/**
 * woocommerce-variation-price price
 * name="variable_regular_price[0]" id="variable_regular_price_0" value="1.19"
 * @used for debuggin only.
 */
add_action( 'woocommerce_before_add_to_cart_button', 'tarpbuilder_plus_display_html_price' );
function tarpbuilder_plus_display_html_price()
{
    global $woocommerce;

    $tpconting = tarpbuilder_plus_contingencies();
    if( !$tpconting ) return;
$added = WC()->cart->get_cart_subtotal();
    echo '=<div id="tbselect-price">'. $added .'</div>
    <input type="number" id="tarpbuilder-variation-price" 
                class="tarpbuilder-variation-price" value="" 
                name="_tarpbuilder_variation_price"></p>';


}



//add_action( 'woocommerce_cart_calculate_fees','tarpbuilder_plus_custom_tarp_charge' );

/**
 * Add a charge to cart / checkout based on number of square feet.
 *
 * TODO decimal filters 
 */
function tarpbuilder_plus_custom_tarp_charge($cart) 
{     global $woocommerce, $product_id;  
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;
    $found = false;
    $square_feet = '';
    $cart_size = sizeof( WC()->cart->get_cart() );
    
    if ( $cart_size > 0 ) :  
    
        // option to allow prod price same as fee
    $optqnty = (!isset(get_option( 'tarpbuilder_plus_options' )['tarpbuilder_plus_wndmatch_field'] ))
                ? '' : get_option( 'tarpbuilder_plus_options' )['tarpbuilder_plus_wndmatch_field'];
    $wndtaxx = (!isset(get_option( 'tarpbuilder_plus_options' )['tarpbuilder_plus_wndtaxbase_field'] ))
                ? '' : get_option( 'tarpbuilder_plus_options' )['tarpbuilder_plus_wndtaxbase_field'];
        
        //label in cart totals
        $wndttltext = get_option( 'tarpbuilder_plus_options' )['tarpbuilder_plus_cstitle_field'];
        $tarp_fee   = '';        // clean string
        $adt_fee    = 1; 
        $spaced     = '.01';
        $found      = false;   // default
    
    // Loop thru items in cart
    foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) 
    {

        /* Check for the wnd_fee Line Item in Woocommerce Cart */
        $square_feet = ( !isset( $cart_item['attribute_pa_custom-tarp'] ) ) 
                        ? '' : $cart_item['attribute_pa_custom-tarp'];
            if( '' != $square_feet ) { 
                $found = true; 
            } 
           
            // Found cart item to match - Now do math
            if( $found === true ) 
            { 
                $approx_spacing = (!isset( $cart_item['attribute_pa_approx-spacing'])) 
                                   ? '' : $cart_item['attribute_pa_approx-spacing'];
                
                    switch($approx_spacing){
                        case "6":
                        $spaced = '17';
                        break;
                        case "12":
                        $spaced = '15';
                        break;
                        case "18":
                        $spaced = '13';
                        break;
                        case "24":
                        $spaced = '11';
                        break;
                        case "-1":
                        $spaced = '17';
                        break;
                        default:
                        $spaced = '0';
                        break;
                    }
                    
                 
                    $variation_price = ( !isset( $cart_item['variation'] )) 
                            ? '' : $cart_item['variation']; 
        
                    
                    if( $variation_price > intval( '.01' ) ) {
                        //$product_id = $cart_item['product_id'];
                        //$wnd_qnty   = $cart_item['attribute_pa_custom-tarp'];
                        $price_fees = $woocommerce->cart->cart_contents_total; //get_post_meta($product_id, '_price', true);
                        $adt_fee    = $square_feet + ($spaced * 1);
                        $_fees      = intval( $price_fees * $adt_fee );
                        $wnd_fee    = round($_fees, 2);             
                    } 
                //$fee_price = ( $woocommerce->cart->cart_contents_total * $square_feet );
                $woocommerce->cart->add_fee( $wndttltext .' '. $spaced, $wnd_fee, true, $wndtaxx );
            }
            
    } //ends foreach loop
        $wnd_fee = null;
        $adt_fee = null;
    endif;
    
}

// ************************************************* Custom Data Sets 
// PB-1 $passed, $product_id, $qty
//add_action('wp_ajax_tarpbuilder_plus_errors', 'tarpbuilder_plus_display_errors');
//add_action('wp_ajax_tarpbuilder_plus_errors', 'tarpbuilder_plus_display_errors');


//add_action( 'woocommerce_before_single_product', 'tarpbuilder_plus_display_errors', 9 );
function tarpbuilder_plus_display_errors()
{
    $htm = '';
    
    
    
        $htm .= '<div class="woocommerce-error" role="alert">' 
. esc_html__('You must add width and length to continue', 'woocommerce')
. '</div>';
    echo $htm;
}
/** PB-1
 * Validate when adding to cart 
 * 
 * @param bool $passed
 * @param int $product_id
 * @param int $quantity
 * @return bool
 */
/* add_filter( 'woocommerce_add_to_cart_validation',
            'tarpbuilder_width_cart_validation', 10, 3); */

function tarpbuilder_width_cart_validation( $passed, $product_id, $variation_id )
{
    $tpconting = tarpbuilder_plus_contingencies();
    if( !$tpconting ) return $passed;
    $passed = false; 
        //do_action( 'woocommerce_set_cart_cookies',  true );
        
        if( isset( $_POST['_attribute_pa_custom-tarp'] ) && 
         ( $_POST['_attribute_pa_custom-tarp'] > 0 ))
        {
        
        $passed = true;

        }
    if( !$passed ) { 
    
    add_action( 'woocommerce_before_single_product', 'tarpbuilder_plus_display_errors', 10 ); 
    
    } else {
    remove_action( 'woocommerce_before_single_product', 'tarpbuilder_plus_display_errors', 10 ); 
    }
        return $passed;
}

/** PB-2
 * Add custom data to the cart item
 * 
 * @uses woocommerce_add_cart_item_data
 * @param array $cart_item
 * @param int   $product_id
 * @return array
 */

function tarpbuilder_plus_add_cart_item_data( $cart_item, $product_id )
{

    if( isset( $_POST['_attribute_pa_custom-tarp'] ) ) {
            $cart_item['attribute_pa_custom-tarp'] = sanitize_text_field( 
               $_POST['_attribute_pa_custom-tarp'] );
    }
    if( isset( $_POST['_tarpbuilder_plus_begin_date'] ) ) {
            $cart_item['tarpbuilder_plus_begin_date'] = sanitize_text_field( 
               $_POST['_tarpbuilder_plus_begin_date'] );
    }
    // tarp_width_major
    if( isset( $_POST['_attribute_pa_custom-tarp-width'] ) ) {
            $cart_item['attribute_pa_custom-tarp-width'] = sanitize_text_field( 
               $_POST['_attribute_pa_custom-tarp-width'] );
    }
    // tarp_length_major
    if( isset( $_POST['_attribute_pa_custom-tarp-length'] ) ) {
            $cart_item['attribute_pa_custom-tarp-length'] = sanitize_text_field( 
               $_POST['_attribute_pa_custom-tarp-length'] );
    }
    // attribute_pa_approx-spacing
    if( isset( $_POST['attribute_pa_approx-spacing'] ) ) {
            $cart_item['attribute_pa_approx-spacing'] = sanitize_text_field( 
               $_POST['attribute_pa_approx-spacing'] );
    }
     // attribute_pa_approx-spacing
    if( isset( $_POST['spacing'] ) ) {
            $cart_item['spacing'] = sanitize_text_field( 
               $_POST['spacing'] );
    }
    // attribute_pa_custom-tarp
    if( isset( $_POST['attribute_pa_custom-tarp'] ) ) {
            $cart_item['attribute_pa_custom-tarp'] = sanitize_text_field( 
               $_POST['attribute_pa_custom-tarp'] );
    }
        return $cart_item;
}
/** PB-3
 * Load cart data from session
 * 
 * @uses woocommerce_get_cart_item_from_session
 * @param  array $cart_item
 * @param  array $other_data
 * @return array
 */
function tarpbuilder_plus_get_cart_item_from_session( $cart_item, $values ) 
{

    if ( isset( $values['attribute_pa_custom-tarp'] ) ){
        $cart_item['attribute_pa_custom-tarp'] = $values['attribute_pa_custom-tarp'];
    } 
    if ( isset( $values['tarpbuilder_plus_begin_date'] ) ){
        $cart_item['tarpbuilder_plus_begin_date'] = $values['tarpbuilder_plus_begin_date'];
    }
    // tarp_width_major
    if ( isset( $values['attribute_pa_custom-tarp-width'] ) ){
        $cart_item['attribute_pa_custom-tarp-width'] = $values['attribute_pa_custom-tarp-width'];
    }
    // tarp_length_major
    if ( isset( $values['attribute_pa_custom-tarp-length'] ) ){
        $cart_item['attribute_pa_custom-tarp-length'] = $values['attribute_pa_custom-tarp-length'];
    }
    // attribute_pa_approx-spacing
    if ( isset( $values['attribute_pa_approx-spacing'] ) ){
        $cart_item['attribute_pa_approx-spacing'] = $values['attribute_pa_approx-spacing'];
    }
    // attribute_pa_approx-spacing
    if ( isset( $values['spacing'] ) ){
        $cart_item['spacing'] = $values['spacing'];
    }
    // attribute_pa_custom-tarp
    if ( isset( $values['attribute_pa_custom-tarp'] ) ){
        $cart_item['attribute_pa_custom-tarp'] = $values['attribute_pa_custom-tarp'];
    }
        return $cart_item;
}

/** PB-5
 * Get item data to DISPLAY in cart
 * 
 * @uses         woocommerce_get_item_data
 * @param  array $other_data
 * @param  array $cart_item
 * @return array
 */
function tarpbuilder_plus_get_item_data( $other_data, $cart_item ) 
{

    $spctxt = "inches"; $feettxt = "feet"; $spcingtxt = '';
    
    $tarp_width_major  = ( !empty( $other_data['attribute_pa_custom-tarp-width'] ) ) 
                                 ? $other_data['attribute_pa_custom-tarp-width'] : '';
    $tarp_length_major = ( !empty( $other_data['attribute_pa_custom-tarp-length'] ) ) 
                                 ? $other_data['attribute_pa_custom-tarp-length'] : '';
    
    $approx_spacing = ( !empty( $other_data['attribute_pa_approx-spacing'] ) ) 
                                           ? $other_data['attribute_pa_approx-spacing'] : '';
     $spacing = ( !empty( $other_data['spacing'] ) ) 
                                           ? $other_data['spacing'] : ''; 
 if ( !empty( $cart_item['attribute_pa_custom-tarp'] ) )
    {
        $other_data[] = array(
            'key'     => __( 'Custom Tarp', 'woocommerce' ),
            'name'  => esc_attr( 'Square Feet' ),
            'value' => sanitize_text_field( 
                        $cart_item['attribute_pa_custom-tarp'] . ' square-' . $feettxt )
        ); 
    }  
    
    if ( !empty( $cart_item['attribute_pa_custom-tarp-width'] ) )
    {
        $other_data[] = array(
            'key'     => __( 'Width', 'woocommerce' ),
            'name'  => esc_attr( $tarp_width_major ),
            'value' => sanitize_text_field( 
                        $cart_item['attribute_pa_custom-tarp-width'] . ' ' . $feettxt )
        ); 
    }
    if ( !empty( $cart_item['attribute_pa_custom-tarp-length'] ) )
    {
        $other_data[] = array(
            'key'     => __( 'Length', 'woocommerce' ),
            'name'  => esc_attr( $tarp_length_major ),
            'value' => sanitize_text_field( 
                        $cart_item['attribute_pa_custom-tarp-length'] . ' ' . $feettxt )
        ); 
    }  
    // attribute_pa_approx-spacing
    if ( !empty( $cart_item['attribute_pa_approx-spacing'] ) )
    {
        $other_data[] = array(
            'key'     => __( 'Approx Spacing', 'woocommerce' ),
            'name'  => esc_attr( $approx_spacing ),
            'value' => sanitize_text_field( 
                        $cart_item['attribute_pa_approx-spacing'] . ' ' . $spctxt )
        ); 
    }
    // attribute_pa_approx-spacing
    if ( !empty( $cart_item['attribute_pa_approx-spacing'] ) )
    {
        $other_data[] = array(
            'key'     => __( 'Details: ', 'woocommerce' ),
            'name'  => esc_attr( $spacing ),
            'value' => sanitize_text_field( 
                        $cart_item['spacing'] . ' ' . $spcingtxt )
        ); 
    }
        return $other_data;
}
/** PB-6
 * Add meta to order item
 * 
 * @uses woocommerce_add_order_item_meta
 * @param  int   $item_id
 * @param  array $values attribute_pa_custom-tarp, tarpbuilder_plus_begin_date
 * @return void
 */
function tarpbuilder_plus_add_order_item_meta( $item_id, $values ) 
{
    //tarpbuiler_ post meta
    if ( ! empty( $values['attribute_pa_custom-tarp'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'attribute_pa_custom-tarp', 
                                         $values['attribute_pa_custom-tarp'] );           
    }
    if ( ! empty( $values['tarpbuilder_plus_begin_date'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'tarpbuilder_plus_begin_date', 
                                         $values['tarpbuilder_plus_begin_date'] );           
    } 
    // these are options
    if ( ! empty( $values['attribute_pa_custom-tarp-width'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'attribute_pa_custom-tarp-width', 
                                         $values['attribute_pa_custom-tarp-width'] );           
    } 
    if ( ! empty( $values['attribute_pa_custom-tarp-length'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'attribute_pa_custom-tarp-length', 
                                         $values['attribute_pa_custom-tarp-length'] );           
    } 
    // attribute_pa_approx-spacing
    if ( ! empty( $values['attribute_pa_approx-spacing'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'attribute_pa_approx-spacing', 
                                         $values['attribute_pa_approx-spacing'] );           
    } 
    // attribute_pa_approx-spacing
    if ( ! empty( $values['spacing'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'spacing', 
                                         $values['spacing'] );           
    } 
}
