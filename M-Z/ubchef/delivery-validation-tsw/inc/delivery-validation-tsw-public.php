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
global $post;
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
	var delivery_validation = '<?php echo $delivery_validation; ?>';
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
	var waitUntil = waitUntilElementExists('.fdoe_minicart_item', (el) => {
		jQuery(".fdoe_simple_add_to_cart_button").click(function(){
			var product_menu = jQuery(".fdoe_simple_add_to_cart_button").attr("data-product_menu");
			console.log( 'fdom= '+ product_menu );
jQuery("li.fdoe_minicart_item").text(jQuery('value="'+product_menu+'" '));
			
		}); 
	}); 
});</script> 

<?php 
	$js = ob_get_clean();
		
		echo $js;
}
/**
 * Checkout field
 * @since 1.0.1
 *
 * @param string $tswget Value of text field in product meta
*/
add_action('woocommerce_after_checkout_billing_form','delivery_validation_tsw_checkout_html' );
function delivery_validation_tsw_checkout_html()
{
    $p_id = '';
    $p_id = delivery_validation_tsw_parse_validation_to_cart_items();
	$field = get_post_meta( $p_id, 'delivery_validation_tsw', true);

	$tswget = ('' != $_REQUEST['delivery_validate_tsw']) ? $_REQUEST['delivery_validate_tsw'] : '';
    $show   = 'block';
	
	ob_start();
    echo '<div class="delval-notices" style="display:block;position:relative;bottom:-6.67em;width:100%;height:3em;">
	<p class="delivery-validate-msg" style="color:blue;display:' . $show . ';">' 
	. esc_html__('Your delivery date must match the menu date', 'delivery-validate-tsw' ) 
	. ' <span class="tswSuccess"></span>
	<input type="text" id="delivery_validate_tsw" name="delivery_validate_tsw" 
	       class="delivery-validate-tsw" value="' . esc_attr($field) . '" 
		   style="width:65px;float:right;padding:0;margin:0"></p>
	</div>
	<div class="inline_script">
	<script type="text/javascript" id="delivery-validate-checkout">
	jQuery( document ).ready(function() {
    jQuery("#jckwds-delivery-date").change(function(){
		var prdslug = "' . esc_attr( $field ) . '";
		var dlvrdate =  jQuery("#jckwds-delivery-date").val();
		if ( dlvrdate == prdslug ) {
			jQuery(".tswSuccess").text("Confirmed");
			jQuery(".tswSuccess").css("color", "green");
		} else {
			jQuery(".tswSuccess").text( "does not match "+ prdslug);	
			jQuery(".tswSuccess").css("color", "marron");
		}	
	}); });</script></div><div class="clearfix"></div>';
	
    $output = ob_get_clean();

    	echo $output; 
}

/**
 * Pass delivery_validation to add to cart button 
 * @since 1.0.1
 *
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
            // $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->ID ), 'single-post-thumbnail' );
            
            $p_id = $product->get_id();
			break; // get only top item
        }
    }
	endif;
	return absint($p_id);
}

/**
 * For debug only
 */
//add_action('wp_footer', 'delivery_validation_tsw_meta_html');
function delivery_validation_tsw_meta_html(){

echo '<div style="display:block;width:100%;height:100%;overflow:auto;">Info: ';
$meta_keys = get_post_meta( get_the_ID() );

foreach( $meta_keys as $meta => $value ) : 
    if( ( '_' != $meta[0] ) && ( '' != $meta ) ) : 
        echo '<span><strong>' . $meta . ':</strong> 
		echo ' . $value[0] . '</span>';
    endif;
endforeach; 
 
echo '</div>';
}
