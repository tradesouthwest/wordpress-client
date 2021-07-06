/* delivery-validation-tsw-script 
 * @maybe_use $( 'body' ).trigger( 'update_checkout' );
 */
jQuery( document ).ready(function() {
    jQuery('#jckwds-delivery-date').blur(function(e){
		
		var prdslug = "16/07/2021";
		var dlvrdate =  jQuery('#jckwds-delivery-date').val();
		
		if ( dlvrdate == prdslug ) {
			jQuery('.tswSuccess').text("Confirmed");
		} else {
			jQuery('.tswSuccess').text( "does not match "+ prdslug);	
		}	
	});
	//e.preventDefault();

});