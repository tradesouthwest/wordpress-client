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
 * attr( 'title', $('.fdoe_menuitem a').text() );
 * @see https://stackoverflow.com/questions/5525071/how-to-wait-until-an-element-exists
 */

function delivery_validation_tsw_footer_scripts() 
{
//$data_menu = urlencode($data_menu);
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
		}); 
	});
	var waitUntil = waitUntilElementExists('.fdoe_minicart_item', (el) => {
		//jQuery(".fdoe_simple_add_to_cart_button").click(function(){
			var product_menu = jQuery(".fdoe_simple_add_to_cart_button").attr("data-product_menu");
			console.log( 'fdom= '+ product_menu );
jQuery(".fdoe_minicart_item").append(jQuery('<input type="text" id="product_menu" name="product_menu" value="'+product_menu+'">'));
			
		//}); 
	}); 
});</script> 

<?php 
	$js = ob_get_clean();
		
		echo $js;
}

add_action('woocommerce_after_checkout_billing_form','delivery_validation_tsw_checkout_html' ); 
function delivery_validation_tsw_checkout_html()
{
	//global $post;
$tswget = ( '' != $_REQUEST['product_menu'] ) ? $_REQUEST['product_menu'] : 0;
    $show   = 'block';

	ob_start();
    echo '<div style="display:block;position:relative;bottom:-6.67em;color:maroon;width:100%;height:3em;">
	<p class="delivery-validate-msg" style="display:' . $show . ';">' 
	. esc_html__('Your delivery date must match the menu date', 'delivery-validate-tsw' ) 
	. '<span class="tswSuccess"></span><input type="text" id="delivery_validate_tsw" name="delivery_validate_tsw" 
	class="delivery-validate-tsw" value="' . esc_attr($tswget) . '" style="width:40px;float:right;padding:0;margin:0"></p>';
	echo '</div><div class="clearfix"></div>';
	
    $output = ob_get_clean();

    	echo $output; 
}
