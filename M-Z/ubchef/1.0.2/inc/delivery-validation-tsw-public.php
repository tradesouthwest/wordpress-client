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
 * Add footer scripts for anchor links title attribute.
 * @since 1.0.1
 *
 * attr( 'title', $('.fdoe_menuitem a').text() );
 * @see https://stackoverflow.com/questions/5525071/how-to-wait-until-an-element-exists
 */

function delivery_validation_tsw_footer_scripts() 
{
if( is_checkout() ) return; 

ob_start();    
?><script type="text/javascript" id="delivery-validation-tsw-footer">
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
/* removed from testing. goes in line 90 if needed.
<input type="hidden" id="delivery_validate" name="delivery_validate" 
	       class="delivery-validate-tsw" value="' . esc_attr( $field ) . '" 
		   style="width:65px;visibility:hidden">
 */
    $p_id = $field = $is_match = $next_day = $dayb = '';
    $p_id     = delivery_validation_tsw_parse_validation_to_cart_items();
	$field    = get_post_meta( $p_id, 'delivery_validation_tsw', true);
	/* deprecated 
	$pattern  = '/([0-9])+/';  
	$is_match = preg_match($pattern, $field, $dates);
	$next_day = absint($dates[0]) + 1;
	$dayb     = $next_day . '/'.date("m").'/'.date("Y"); 
    $show     = 'table'; */
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

/**
 * For christmas courses only
 */
add_filter( 'woocommerce_add_to_cart_validation', 
			'delvaltsw_add_to_cart_validationxm', 10, 5 );
function delvaltsw_add_to_cart_validationxm( $passed, $product_id, $quantity, 
				$variation_id, $variations  )
{ 
	global $product;
	if( !is_product('10693') ) return; 
	$passed = false;
	$product = wc_get_product( $product_id );
    $variations = $product->get_available_variations();
	if($variations ){
	$passed = true;
	}
	$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', 
										$passed, $product_id, $quantity, 
										$variation_id, $variations 
										);
	return $passed_validation;
    
}
add_action('wp_footer', 'delivery_validation_tsw_christmas_courses');
function delivery_validation_tsw_christmas_courses() 
{
	if( !is_product('10693') ) return;

	ob_start(); 

	?><script id="christmasCourse" type="text/javascript">
	(function( $ ) {
    "use strict";
 		$(document).ready( function(){

			$("form.variations_form.cart select").change(function (e) {
			
				var optsel = $(this).children("option:selected").val();
				
				var starter = $("#starter-two-courses").val();
				var mainc   = $("#main-course-two-courses").val();
				var dessert = $("#dessert-two-courses").val();

				if ( ( mainc && starter ) && dessert ) {
				e.preventDefault();
					alert( "Only Two courses per order. Please click the Clear button below and select again. Thanks." );
					return false;
				} else {
				e.preventDefault();
					console.log("OK4 " + optsel);
					
				}
			
			});	
    	}); 
	})(jQuery); </script><style id="suppXm">.thb-product-detail .variations_form .reset_variations{
	padding-top: 4px;padding-bottom: 4px;font-size: 1em;color: red;line-height: .2;}</style>
	
	<?php $jsxm = ob_get_clean();
	
		echo $jsxm;
} 
