/* delivery-validation-tsw-script 
 * @maybe_use $( 'body' ).trigger( 'update_checkout' );
 */
(function($){
	$(document).ready(function(){
	$('.product-menu-cart').on('change keyup paste',function(){
	$('.cart_totals').block({
	message: null,
	overlayCSS: {
	background: '#fff',
	opacity: 0.6
	}
	});
	var cart_id = $(this).data('cart-id');
	$.ajax(
	{
	type: 'POST',
	url: tswajax_vars.ajaxurl,
	data: {
	action: 'delivery_validation_tsw_update_cart_notes',
	security: $('#woocommerce-cart-nonce').val(),
	notes: $('#product_menu_' + cart_id).val(),
	cart_id: cart_id
	},
	success: function( response ) {
	$('.cart_totals').unblock();
	}
	}
	)
	});
	});
})(jQuery);