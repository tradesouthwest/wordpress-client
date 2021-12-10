/* delivery-validation-tsw-script 
 * @maybe_use $( 'body' ).trigger( 'update_checkout' );
 */
 /** delevery validation jq version: 1.0.21 */
(function($){
	$(document).ready(function(){

	$(function(){
	    $('input.qty').on('keyup', function() {
			var vl = $(this).val();
			$(this).next($('a.fdoe_simple_add_to_cart_button').attr('data-quantity', vl));
			console.log(vl ); 	
			           		
    	});
       /* 
		$('.qty').focus(function(){
			$(this).addClass("enabled");
			var id = $(this).attr('id');
			var vl = $(this).attr('value');
			console.log(id + vl);
				
		});
*/

	});
	});
})(jQuery);
