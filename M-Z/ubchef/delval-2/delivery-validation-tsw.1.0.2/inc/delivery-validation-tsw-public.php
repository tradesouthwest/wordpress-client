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
// remove_filter( 'woocommerce_add_to_cart_form_action', '__return_empty_string' );
/**
 * @see http://www.example.com/?wc-ajax=get_refreshed_fragments
 */
//add_action( 'cart_custom_item_data', 'display_cart_item_custom_meta_data', 10, 2 );
function display_cart_item_custom_meta_data($p_id) 
{
    if (is_admin() && defined('DOING_AJAX') && DOING_AJAX) return;
	
	foreach ( WC()->cart->get_cart() as $cart_item ) { 
		if( in_array( $p_id, array( $cart_item['product_id'], 
										   $cart_item['variation_id']) )){
			$quantity =  $cart_item['quantity'];
			break; // stop the loop if product is found
		}
	}
	// Displaying the quantity if targeted product is in cart
	if( isset( $quantity ) && $quantity > 0 ) {
		return $quantity;
	} else {
		return '';
	}
}

/** 
 * @see https://njengah.com/custom-add-to-cart-button-woocommerce/
 * Override loop template and show quantities next to add to cart buttons
 * @param  array $args Args for the input 
 * @param  WC_Product|null $product 
 * @param  boolean $echo Whether to return or echo|string 
 */
function youbechef_hook_into_fdoe_quantity_field() 
{
	global $product;
	$html = '';
    if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() 
 		&& $product->is_in_stock() && ! $product->is_sold_individually() ) {

$qtv  = display_cart_item_custom_meta_data($product->get_id());
$html = '<div class="qtyfdoe-input">';
$html .= woocommerce_quantity_input( array( 
						'input_value' => esc_attr( isset( $qtv ) ? $qtv : '' )
						), 
						$product, 
						false 
					);
/*$html .= '<span><a href="" rel="nofollow" 
			data-product_id="'. esc_attr( $product->get_id() ) .'" 
			data-product_sku="'. esc_attr( $product->get_sku() ) .'"
			data-quantity ="" class="qtyfdoe-button add_to_cart_button 
			fdoe_simple_add_to_cart_button product_type_simple"><span>+</span></a></span>';
			*/
$html .= '<p class="msgx-wrap"><span class="msgx-cart"></span></p></div>';
	}
    return $html;
	
}
//add_filter( 'woocommerce_quantity_input_args', 'delval_quantity_input_args', 10, 2 );
/**
 * Add footer scripts for anchor links title attribute.
 * @since 1.0.1
 * //"change .qty": "add_simple",
 * attr( 'title', $('.fdoe_menuitem a').text() );
 * @see https://stackoverflow.com/questions/5525071/how-to-wait-until-an-element-exists
 */
add_action('wp_footer', 'delivery_validation_tsw_footer_scripts' );
function delivery_validation_tsw_footer_scripts() 
{
if( is_checkout() ) return; 

ob_start();    
?>
<style id="delivery-validation-footer-css">
.fdoe_price_and_add_onecol{padding-right:20px}
.qtyfdoe-input input.qty::-webkit-inner-spin-button, 
.qtyfdoe-input input.qty::-webkit-outer-spin-button, 
.qtyfdoe-input input[type=number]::-webkit-inner-spin-button, ::-webkit-outer-spin-button{ transform:scale(1.5) }
div.quantity{background:#f5f2df}
.thb-quantity-style2 div.quantity{font-size: 1em;padding:0 15px;}
.thb-quantity-style2 div.quantity .qty{position:relative;height:36px;width:52px;}
a.add_to_cart_button.fdoe_simple_add_to_cart_button.product_type_simple:not(.qtyfdoe-button){
font-size: 1.444em;float: right;position: relative;top: -1.95em;right: -1.122em;}
a.add_to_cart_button.fdoe_simple_add_to_cart_button.product_type_simple .fa-2x{font-size:1em;}
.msgx-wrap{position:relative;top:1em;left:0;color:inherit;font-weight:normal;}
.amount:not(.mini-cart-quantity .amount) {
    display: block;
    position: relative;
    top: -1em;
}
/*.thb-quantity-style2 div.quantity .qty {
  -webkit-appearance: textfield;
  -moz-appearance: textfield;
  appearance: textfield;
}

.thb-quantity-style2 div.quantity .qty::-webkit-inner-spin-button,
.thb-quantity-style2 div.quantity .qty::-webkit-outer-spin-button {
  -webkit-appearance: none;
}

.thb-quantity-style2 div.quantity  {
  border: 2px solid #ddd;
  display: inline-flex;
}

.thb-quantity-style2 div.quantity,
.thb-quantity-style2 div.quantity * {
  box-sizing: border-box;
}

.thb-quantity-style2 div.quantity button {
  outline:none;
  -webkit-appearance: none;
  background-color: transparent;
  border: none;
  align-items: center;
  justify-content: center;
  width: 1em;
  height: 1em;
  cursor: pointer;
  margin: 0;
  position: relative;
  padding:0 8px;
}

.thb-quantity-style2 div.quantity button:before,
.thb-quantity-style2 div.quantity button:after {
  display: inline-block;
  position: absolute;
  content: '';
  display:block;
  width: 1rem;
  height: 1px;
  background-color: #212121;
  margin-top: 64%;
  transform: translate(-50%, -50%);
}
.thb-quantity-style2 div.quantity button.aplus:after {
  transform: translate(-50%, -50%) rotate(90deg);
  margin-right:-12%;
}
*/
.thb-quantity-style2 div.quantity input[type=number] {
 
  text-align: center;
}</style>
<script>/* (function($){
	$(document).ready(function(){
	
	$(function(){
	
$("div.quantity:not(.btn_added)").addClass("btn_added").append('<button class="aplus"></button>').prepend('<button class="aminus"></button>');

	});
	//$(document).off("click", ".qty").on( "click", ".qty", function() {

});
})(jQuery); */
</script>
<script id="delivery-validation-tsw-footer">
jQuery( document ).ready(function($) {
	const waitUntilElementExists = (selector, callback) => {
	const el = document.querySelector(selector);
		if (el){
			return callback(el);
		}
	setTimeout(() => waitUntilElementExists(selector, callback), 500);
	}
	var waitUntil = waitUntilElementExists('.fdoe_menuitem a', (el) => {
	//console.log(el));

		jQuery(".fdoe_menuitem a").each(function() {
			jQuery(this).attr("title", $.trim( jQuery(this).text() ) );
		});
			console.log('set 1');
		jQuery('.fdoe_menuitem a[data-toggle="arocollapse"]').click( function(){
	    	var fdoe_title = jQuery(this).attr("title");
	    	jQuery(".fdoe_simple_add_to_cart_button").attr("data-product_menu", fdoe_title );
			console.log( 'fdot= '+ fdoe_title  );
			// add to fragments?
		}); 
	});
/*	var waitUntil = waitUntilElementExists('.fdoe_minicart_item', (el) => {
		jQuery(".fdoe_simple_add_to_cart_button").click(function(){
			var product_menu = jQuery(".fdoe_simple_add_to_cart_button").attr("data-product_menu");
			console.log( 'fdom= '+ product_menu );
jQuery("li.fdoe_minicart_item").text(jQuery('value="'+product_menu+'" '));
			
		}); 
	}); */
});</script> 
<?php 
	$js = ob_get_clean();
		
		echo $js;
}

/**
 * Checkout field Validation
 * @since 1.0.1
 *
 * @param string $field Value of text field in product meta
*/
add_action('woocommerce_after_checkout_billing_form','delivery_validation_tsw_checkout_html' );
function delivery_validation_tsw_checkout_html()
{

    $p_id = $field = $is_match = $next_day = $dayb = '';
    $p_id     = delivery_validation_tsw_parse_validation_to_cart_items();
	$field    = get_post_meta( $p_id, 'delivery_validation_tsw', true);
	$show     = 'block';
	$pattern  = '/([0-9])+/';  
	$is_match = preg_match($pattern, $field, $match);
	$next_day = $match[0] + 1; $same_day = $match[0] + 0;
	$field_splt = preg_split("/[\/,]+/", $field);
	$dayb     = sprintf("%02d", $next_day ) . '/'. $field_splt[1] . '/'. $field_splt[2]; 
	// christmas product menu exception for one day
	if (is_product('10712') || is_product('10693') ) { 
		$dayb = sprintf("%02d", $same_day ) . '/'. $field_splt[1] . '/'. $field_splt[2]; 
	}
	if( '' != $field ) : 
	ob_start();
    echo '<table class="delval-notices" style="line-height:1;padding:0;position:relative;bottom:-6.65em;width:100%;display:' . $show . ';">
	<tbody style="border-color:#e0e0e0;"><tr><td class="delivery-validate-msg" style="line-height:1;padding: 5px;color:darkcyan;">' 
	. esc_html__('Your delivery date must match the menu date.', 'delivery-validate-tsw' ) 
	. '</td><td style="line-height:1;padding: 5px"><span class="tswSuccess" style="line-height:1;color:green; "></span>
	<span class="tswError" style="color:red;text-shadow: 0 1px 1px #fff; "></span>
	</td></tr></tbody></table>
	<div class="inline_script">

	<script type="text/javascript" id="delivery-validate-days">
	jQuery( document ).ready(function() {
    jQuery("#jckwds-delivery-date").change(function(){
		var prddat1 = "' . esc_attr( $field ) . '";
		var prddat2 = "' . esc_attr( $dayb ) . '";
		var dlvrdate =  jQuery("#jckwds-delivery-date").val();
 
		if ( dlvrdate == prddat1 ) {
			jQuery(".tswSuccess").text("Confirmed, Thanks!");
			jQuery(".tswSuccess").css("display", "block");
			jQuery(".tswError").css("display", "none");
		} else if (dlvrdate == prddat2) {
			jQuery(".tswSuccess").text("Confirmed, Thanks!");
			jQuery(".tswSuccess").css("display", "block");
			jQuery(".tswError").css("display", "none");
		} else {
			jQuery(".tswError").text( "Date does not match Menu date!" );	
			jQuery(".tswError").css("display", "block");
			jQuery(".tswSuccess").css("display", "none");
		}	
	}); });</script></div><div class="clearfix"></div>';
	
    $output = ob_get_clean();

    	echo $output; 
	endif;
		$p_id = $field = $is_match = $next_day = $dayb = null;
}

/**
 * Pass delivery_validation to add to cart button 
 * @since 1.0.1
 *
 * @see https://gist.github.com/dr5hn/10b241076ac3c8ac1e4d84f7ec48866e
 * @param string $del_val Value of text field in product meta
*/
function delivery_validation_tsw_parse_validation_to_cart_items()
{
	global $woocommerce;
	if( is_product('10693') ) return; 
	$cnt = $woocommerce->cart->cart_contents_count;
	if ( $cnt > 0 ) : 
	foreach ( WC()->cart->get_cart() as $cart_item ) {
        $item = $cart_item['data'];
        //print_r($item);
        if(!empty($item)){
            $product = new WC_product($item->id);
                        
            $p_id = $product->get_id();
			// comment in to get only top item
			// break;
        }
    }
	endif;
	return absint($p_id);
}
