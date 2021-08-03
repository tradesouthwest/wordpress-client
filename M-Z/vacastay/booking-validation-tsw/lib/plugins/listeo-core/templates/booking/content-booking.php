listeo-core/templates/booking/content-booking.php line 264
						<?php endif; ?><?php if(function_exists('bkvaltsw_is_widget_ready_price')) { ?><?php $cutoff = get_option('booking_valtsw_field')['booking_valtsw_cutoff']; ?>
						<li><small style="color:maroon">Refund can only be refunded before <?php echo esc_attr( $cutoff ); ?> hours.</small></li><?php } ?>
