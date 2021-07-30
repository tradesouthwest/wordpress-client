// added at line 1191 . Just before `<div class="booking-estimated-cost" `	


	<?php /* **************** added by larry@codeable ******************* into class-listeo-core-widgets */
	if( function_exists('booking_validation_tsw_extend_listeo_booking_widget') ) : 
	    if ( bkvaltsw_is_widget_ready() ) {
	        do_action('booking_validation_tsw_render_listeo_booking_widget', $post_id );  
	    }
	endif; ?>
