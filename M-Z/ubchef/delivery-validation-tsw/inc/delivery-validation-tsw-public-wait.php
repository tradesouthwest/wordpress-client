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
 */

function delivery_validation_tsw_footer_scripts() 
{

ob_start();    
?><script type="text/javascript" id="delivery-validation-tsw-footer">
jQuery( document ).ready(function($) {

	jQuery(".fdoe_menuitem a").click( function(){
		//e.preventDefault();
		var fdoe_title = $.trim(jQuery(this).attr("title"));
	
	jQuery("#fdoe_mini_cart_id").html( $('<p class=\"tswSuccess\"><input type=\"text\" id=\"delivery_validate_tsw\" name=\"delivery_validate_tsw\" class=\"delivery-validate-tsw\" value=\"'+ fdoe_title + '\"></p>'));
	console.log( 'fdot= '+ fdoe_title  );
	});

	//var $ = jQuery;
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
});
	/* jQuery(function($) { 
		$( '.add_to_cart_button:not(.product_type_variable, .product_type_grouped)' ).on( 
		'click', function() { }); }); */
	
});</script> 
<?php 
$js = ob_get_clean();
echo $js;
}

add_action('woocommerce_after_checkout_billing_form','delivery_validation_tsw_checkout_html' ); 
function delivery_validation_tsw_checkout_html()
{
	global $post;

	ob_start();
    $show   = 'block';
	
	$tswget = $post->ID;
    echo '<div style="display:block;position:relative;bottom:-4em;color:maroon;width:100%;height:4em;">
		<p class="delivery-validate-msg" style="display:' . $show . ';">' . esc_html__('Your delivery date must match the menu date', 'delivery-validate-tsw' ) . '</p>';
	echo '<span></span>
	<input type="hidden" id="delivery_validate_tsw" name="delivery_validate_tsw" class="delivery-validate-tsw" value="' . esc_attr($tswget) . '"/>';
	echo '</div>';
	
    $output = ob_get_clean();

    	echo $output; 
}

