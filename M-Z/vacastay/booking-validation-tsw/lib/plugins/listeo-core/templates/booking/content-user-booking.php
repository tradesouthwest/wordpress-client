line 259	<?php if(function_exists('bkvaltsw_is_widget_ready_price')) { ?><?php $cutoff = get_option('booking_valtsw_field')['booking_valtsw_cutoff']; $cutoff_text = get_option('booking_valtsw_field')['booking_valtsw_cutoff_text']; ?>
                        <li><small style="color:maroon"><?php echo esc_attr( $cutoff_text ); ?><?php echo esc_attr( $cutoff ); ?> hours.</small></li><?php } ?>
					</ul>
