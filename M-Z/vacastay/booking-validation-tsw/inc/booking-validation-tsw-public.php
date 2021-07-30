<?php 
/**
 * @since:     1.0.0
 * Author:     Tradesouthwest
 * Author URI: http://tradesouthwest.com
 * @package    booking-validation-tsw
 * @subpackage inc/booking-validation-tsw-public
*/
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Simple validate to check if post_meta exists
 * @return Boolean
 */
function bkvaltsw_is_widget_ready()
{
    global $post;
    $deposit  = get_post_meta($post->ID,'_security_deposit',true);
    $cleaning = get_post_meta($post->ID,'_cleaning_fee',true);
    $rtn = false;
    if ( !empty( $deposit || $cleaning ) ) { $rtn = true; }
    
        return $rtn;
    
}
// Display html for costs
add_action('tsw_get_additional_costs_html', 'booking_validation_tsw_get_additional_costs_html' );
function booking_validation_tsw_get_additional_costs_html($order) 
{
    global $woocommerce;
    $add_depsoit = $add_cleaning = 0;
    $listing_id  = get_post_meta( $order->get_id(), 'listing_id', true ); 
	$add_deposit = get_post_meta( $listing_id, '_security_deposit', true ); 
	$add_clean   = get_post_meta( $listing_id, '_cleaning_fee', true ); 
	$_feez       = $add_deposit + $add_clean;
    $add_feez    = round($_feez, 2);            
	if ( $add_feez > 0 )
	//echo '<span> Security and Cleaning Fee ' . get_woocommerce_currency_symbol() . '' . $add_feez . '</span>';
	echo $add_feez;
}

/** 
 * @_cart_item_price only returns data, does not total
 * display ppd next to price
 * @param  string $title
 * @param  array $cart_item
 * @param  array $cart_item_key
 * @return array $subtotal, $this, $item, $inc_tax, $round
 */ 
add_action('tsw_get_additional_costs_items', 'booking_validation_tsw_get_additional_costs_items' );
function booking_validation_tsw_get_additional_costs_items($post_inf) 
{
    $post_inf = $post_info->ID;
    //$order_subtotal = $order->get_subtotal(); 
    $add_deposit = $add_cleaning = 0;
	$add_deposit = get_post_meta( $post_inf, '_security_deposit', true ); 
	$add_clean   = get_post_meta( $post_inf, '_cleaning_fee', true ); 
	$add_feez       = $add_deposit + $add_clean;
    if( $add_feez > 0 ):
        return round($add_feez, 2);
        else: 
            return false;
        endif;
} 
/**
 * Checkout field Validation
 * @since 1.0.1
 * @subpackage listeo/woocommerce/checkout/pay-form.php
 * @param string $field Value of text field in product meta
*/
//add_action( 'woocommerce_single_product_summary', 'booking_validation_tsw_checkout_html' );
add_action('booking_validation_after_booking_summary','booking_validation_tsw_checkout_html' );
function booking_validation_tsw_checkout_html($data)
{
    $deposit_value   = get_post_meta($data->listing_id,"_security_deposit",true);  
	$cleaning_value  = get_post_meta($data->listing_id,"_cleaning_fee",true); 
	$currency_symbol = get_woocommerce_currency_symbol();
	$totalfees       = $cleaning_value+$deposit_value;
    if( '' != $deposit_value ) : 	
	ob_start();
	
    echo '<li id="booking-confirmation-security-deposit" style="width: 100%;color: #888;margin: 2px 0;transition: 0.2s;cursor: default;overflow: hidden;">
			<h5 style="font-weight:600">Security Deposit <span style="float: right;font-weight: 400;text-align: right;"> ' .$currency_symbol. '' .$deposit_value . '</span></h5>
			<input id="_security_deposit" type="hidden" name="_security_deposit" value="' .$deposit_value . '">
		</li>';
	
	echo '<li id="booking-confirmation-cleaning-fee" style="width: 100%;color: #888;margin: 2px 0;transition: 0.2s;cursor: default;overflow: hidden;">
			<h5 style="font-weight:600">Cleaning Fee <span style="float: right;font-weight: 400;text-align: right;"> ' .$currency_symbol. '' .$cleaning_value . '</span></h5>
			<input id="_cleaning_fee" type="hidden" name="_cleaning_fee" value="' .$cleaning_value . '">
		</li>';
	echo '<li style="visibility:hidden"><input type="hidden" id="totalfees" name="totalfees" value="' . esc_attr($totalfees) . '"></li>';	
	echo '<li id="booking-confirmation-agreeto"><label for="tsw-agreeto"><span style="float:left;">I agree to additional fees above: </span>
	        <input type="checkbox" id="tsw-agreeto" value="" required="required" style="height: 1.4em;position: relative;left: 45%;top: -21px;"></label></li>
	        <li class="tsw-error"><span class="tsw-valid">This field is required</span></li>'; 
	        
	$output = ob_get_clean();
	
    	echo $output;
    	else: echo '<li>&nbsp;</li>';
    endif;
}

/**
 * Single `cart` field Validation
 * @since 1.0.1
 * @subpackage listeo-core/include/class-listeo-core-widget.php
 * @param string $field Value of text field in product meta
*/
add_action('booking_validation_tsw_render_listeo_booking_widget','booking_validation_tsw_extend_listeo_booking_widget');
function booking_validation_tsw_extend_listeo_booking_widget($post_id)
{
    $post_id = (empty($post_id)) ? get_the_ID() : $post_id;
    $additional_fees = $deposit_value = $cleaning_value = 0; 
    $deposit_value   = get_post_meta($post_id, "_security_deposit",true);  
	$cleaning_value  = get_post_meta($post_id, "_cleaning_fee",true); 
	$additional_fees = ($deposit_value + $cleaning_value) * 1; 
    //$currency_symbol = get_woocommerce_currency_symbol();
	if( '' != $deposit_value ) : 
	ob_start();
	?>

    <div class="booking-additional-cost" style="display: block;margin-top: 15px;margin-bottom: -5px;padding-top: 15px;border-top: 1px solid #e8e8e8;">
		<strong style="font-weight:600"><?php esc_html_e('Additional Fees','listeo_core'); ?></strong>
		<span data-price="<?php echo esc_attr($additional_fees); ?>" style="text-align: right;float: right;font-weight:600;font-size: 16px;position: relative;">
		<?php echo esc_html(booking_validation_formatted_price($additional_fees)); ?>
        </span>
        <input id="_cleaning_fee" type="hidden" name="_cleaning_fee" value="<?php echo esc_attr($cleaning_value); ?>">
        <input id="_security_deposit" type="hidden" name="_security_deposit" value="<?php echo esc_attr($deposit_value); ?>">
        <input id="additional_rental_fees" type="hidden" name="additional_rental_fees" value="<?php echo esc_attr($additional_fees); ?>"><?php /* unique_id maybe */ ?>
    </div>

    <?php 
    else: echo '<small>' . esc_html("Additional Fees may apply.") . '</small>'; ?><?php 
    endif;
	$tswout = ob_get_clean();
	
	    echo $tswout;
}



/**
 * Render field in checkout
 * AKA checkout @subpackage form-pay in theme
 */ 
add_action('tsw_add_deposit_data_to_cart', 'booking_validation_tsw_display_deposit_data_in_cart' );
function booking_validation_tsw_display_deposit_data_in_cart($order) {
	
	$additional_fees = 0; //$htmlout = ''; 
	$listing_id      = get_post_meta($order->get_id(),'listing_id', true );
    //$deposit_value  = get_post_meta( $listing_id, '_security_deposit', true );  
    //$cleaning_value = get_post_meta( $listing_id, '_cleaning_fee', true ); 
    $additional_fees = $deposit_value+$cleaning_value; 
    ob_start();
    if( '' != $deposit_value ) : ?>	
        <tr>
			<th scope="row" colspan="2"><?php echo esc_html('Security Deposit'); ?></th><?php // @codingStandardsIgnoreLine ?>
			<td class="additional-fees"><?php echo booking_validation_formatted_price( $deposit_value); ?><?php // @codingStandardsIgnoreLine ?>
			<input id="_security_deposit" type="hidden" name="_security_deposit" value="<?php echo esc_attr($deposit_value); ?>"></td>
		    
		</tr>
	<?php endif; ?>	
	<?php if( '' != $cleaning_value ) : ?>
		<tr>
			<th scope="row" colspan="2"><?php echo esc_html('Cleaning Fee'); ?></th><?php // @codingStandardsIgnoreLine ?>
			<td class="additional-fees"><?php echo booking_validation_formatted_price($cleaning_value); ?><?php // @codingStandardsIgnoreLine ?>
		    <input id="_cleaning_fee" type="hidden" name="_cleaning_fee" value="<?php echo esc_attr($cleaning_value); ?>"></td>
		</tr>
	<?php  //maybe_save_meta_data()
	endif; 
	$htmlout = ob_get_clean();
	
	    echo $htmlout;

}

function booking_validation_formatted_price($tswprice)
{
    $unit    = intval( $tswprice );
    $sep     = '.';
    $decimal = sprintf( '%02d', ( $tswprice-$unit ) * 100 );
    $dol     = "$";
        return sprintf( '%s%d%s%s', $dol, $unit, $sep, $decimal );
}

add_action('booking_valtsw_extra_fees_html', 'booking_validation_tsw_render_extra_fees');
function booking_validation_tsw_render_extra_fees($listing_id){
    
    $deposit  = get_post_meta( $listing_id, '_security_deposit', true );
	$cleaning = get_post_meta( $listing_id, '_cleaning_fee', true );
	$currency_abbr = get_option( 'listeo_currency' );
	$currency_postion = get_option( 'listeo_currency_postion' );
	$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);

    ob_start(); 
	echo
	'<div class="inner-booking-list">'; ?>
        <?php if( '' != $deposit ) : ?>
        <?php echo '<h5>' . esc_html__("Security Deposit:", "listeo") . '</h5>
    	<ul class="listeo_booked_fees_list">
    	    <li class="line-item">' . esc_html($currency_symbol) . '<span>' . esc_attr($deposit) . '</span>
    	    <input id="_security_deposit" type="hidden" name="_security_deposit" value="'. esc_attr($deposit) .'"></li>
    	    </ul>'; ?>
    	<?php endif; ?>
    <?php echo '</div>
    
    <div class="inner-booking-list">'; ?>
        <?php if( '' != $cleaning ) : ?>
        <?php echo '<h5>' . esc_html__("Cleaning Fee:", "listeo") . '</h5>
    	<ul class="listeo_booked_fees_list">
    	    <li class="line-item">' . esc_html($currency_symbol) . '<span>' . esc_attr($cleaning) . '</span>
    	    <input id="_cleaning_fee" type="hidden" name="_cleaning_fee" value="'. esc_attr($cleaning) .'"></li>
    	    </ul>'; ?>
    	<?php endif; ?>
    <?php echo '</div>';	
 
    $output = ob_get_clean();
    
        echo $output;
    
}


add_action( 'woocommerce_before_calculate_totals', 'booking_validation_tsw_subtotal');
function booking_validation_tsw_subtotal() {
    global $woocommerce;
    //Get the custom field value
    $addes_cost = 0;
    $deposit_value   = get_post_meta($post_id, "_security_deposit",true);  
	$cleaning_value  = get_post_meta($post_id, "_cleaning_fee",true); 
	$additional_fees = absint($deposit_value + $cleaning_value); 

    //Check if we have a custom shipping cost, if so, display it below the item price
    if ($additional_fees) {
        $added_cost = $additional_fees;
    } else {
        $added_cost = 0;   
    }
    $woocommerce->cart->add_fee( __('Rental Fees', 'woocommerce'), $added_cost );
    
}

/**
 * add post meta (fees) to line item (_order_items)
 * @uses woocommerce_checkout_create_order_line_item <not working
 * 
 */ 
//add_filter( 'woocommerce_checkout_create_order_line_item', 'booking_validation_tsw_addfees_data_to_cart',10,4); 
function booking_validation_tsw_addfees_data_to_cart( $item, $cart_item_key, $values, $order){
	$security_deposit = get_post_meta( $item->get_product_id(), '_security_deposit', true );
	$cleaning_fee = get_post_meta( $item->get_product_id(), '_cleaning_fee', true );
	
	// Update order item meta
    if ( ! empty( $security_deposit ) ){
        $item->update_meta_data( '_security_deposit', $security_deposit ); 
        
    }
    if ( ! empty( $cleaning_fee ) ){
        $item->update_meta_data( '_cleaning_fee', $cleaning_fee ); 
        
    }
}

add_action( 'woocommerce_checkout_update_order_meta', 'codeablex_validate_referral_id_now', 12, 1 );
function codeablex_validate_referral_id_now( $order ) 
{
    $order     = wc_get_order( $order_id ); 

    $deposit_value = get_post_meta( $order_id, '_security_deposit', true ); 
    // Update customer source.
    if( $deposit_value ) : 
        $deposit_cost = ( empty( $deposit_value )) ? '0' : sanitize_text_field( $deposit_value );
        $order->add_meta( $order_id, '_security_deposit', $deposit_cost );            
        
    endif;
    $cleaning_value = get_post_meta( $order_id, '_cleaning_fee', true ); 
    // Update customer source.
    if( $cleaning_value ) : 
        $cleaning_cost = ( empty( $cleaning_value )) ? '0' : sanitize_text_field( $cleaning_value );
        $order->add_meta( $order_id, '_cleaning_fee', $cleaning_cost );            
        
    endif;
}

/**
 * For debug only
 */
//add_action('wp_footer', 'booking_validation_tsw_metavalues');
function booking_validation_tsw_metavalues() {
  
	//$meta = (empty ( $meta ) ) ? '' : $meta;
 $postmetas = get_post_meta(get_the_ID());

	foreach($postmetas as $meta_key=>$meta_value) {
		echo $meta_key . ' : ' . $meta_value[0] . '<br/>';
	} 

}
