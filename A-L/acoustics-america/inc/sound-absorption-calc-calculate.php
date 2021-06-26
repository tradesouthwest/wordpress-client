<?php
/*
@since ver: 1.0.0
Author: Tradesouthwest
Author URI: http://tradesouthwest.com
@package sound_absorption_calc
@subpackage inc/sound-absorption-calc-calculate
*/
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** 
 * name for 'title' field
 * @since 1.0.0
 */
//add_action( 'sound_absorption_calc_calculate_nrc', 'sound_absorption_calc_calculate_nrc' ); 
function sound_absorption_calc_get_sac_thickness($sac_thickness )
{ 
//global $product;
    /*
    $a1    = soundAbsoptionCalc_get_nrc_value('a1');
    $a2    = soundAbsoptionCalc_get_nrc_value('a2');
    $b1    = soundAbsoptionCalc_get_nrc_value('b1');
    $b2    = soundAbsoptionCalc_get_nrc_value('b2'); */
    $nrc_multipler = ( '' != $sac_thickness ) ? $sac_thickness : '.85';
    
    //return sanitize_text_field( $nrc );
   //$rtrn = wc_get_product_terms( $product->id, 'pa_thickness', array( 'fields' => 'names' ) );
   return $nrc_multiplier;
}
/** 
 * get 'title' field
 * @since 1.0.0
 */
//add_filter( 'woocommerce_get_price_html', 'sound_absorption_calc_title_render' );
//add_filter( 'woocommerce_cart_item_price', 'sound_absorption_calc_title_render' ); 
add_filter( 'woocommerce_after_add_to_cart_quantity', 'sound_absorption_calc_title_render' );
function sound_absorption_calc_title_render()
{
    
    $options       = get_option('sound_absorption_calc'); 
    $sac_nrc_title = ( empty( $options['sound_absorption_calc_title'] ) ) 
                       ? "" : $options['sound_absorption_calc_title']; 
    //$aus  = sound_absorption_calc_calculate_nrc();

    ob_start();

    echo '<div class="row sac-block" style="">';
    echo '<p class="absorption-units-title"><button id="sac_check" type="button" class="sac_check" name="sac_check">Check AU</button>
        <strong>' . esc_attr($sac_nrc_title) . '</strong><input type="text" id="sac_nrc_units" name="sac_nrc_units" 
        value="" style="border:none;font-weight: bold;width:180px;margin-left:8px;"></p>
        <input id="nrc_multiplier" type="hidden" value="1" name="nrc_multiplier"></div>';

    $output = ob_get_clean();

        echo $output;
} 
/**
 * Add the text field as item data to the cart object
 * @since 1.0.0
 * @param Array $cart_item_data Cart item meta data.
 * @param Integer $product_id Product ID.
 * @param Integer $variation_id Variation ID.
 * @param Boolean $quantity Quantity
 */
function sound_absorption_calc_nrcunits_field_item_data($cart_item_data,$product_id,$variation_id,$quantity)
{
    if( ! empty( $_POST['sac_nrc_units'] ) ) {
    // Add the item data
    $cart_item_data['sac_nrc_units'] = $_POST['sac_nrc_units'];
    }
    
        return $cart_item_data;
}
//add_filter( 'woocommerce_add_cart_item_data', 'sound_absorption_calc_nrcunits_field_item_data', 10, 4 ); 
