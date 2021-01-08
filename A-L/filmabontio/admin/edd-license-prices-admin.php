<?php 
/**
 * Upgrade functions
 *
 * @package     edd-license-prices/admin
 * @since       1.0.2
 *  
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
* Add custom price fields in metabox
*/
function edd_license_prices_render_custom_price_field( $post_id ) 
{
	global $edd_options;
	
	$license_pricing = get_post_meta( $post_id, '_edd_license_prices_pricing', true );
	$default_price   = get_post_meta( $post_id, 'edd_license_prices_default_price', true );
	$second_price    = get_post_meta( $post_id, 'edd_license_prices_second_price', true );
	$default_text    = get_post_meta( $post_id, 'edd_license_prices_default_text', true ); 
	$second_text     = get_post_meta( $post_id, 'edd_license_prices_second_text', true );
	?>
	<p>
		<strong><?php esc_html_e( 'License Pricing:', 'edd-lp' ); ?></strong>
	</p>

    <p>
		<label for="edd_license_prices_pricing">
			<input type="checkbox" name="_edd_license_prices_pricing" 
					id="edd_license_prices_pricing" 
					value="1" <?php checked( 1, $license_pricing ); ?> />
			<?php esc_html_e( 'Enable license pricing for this product', 'edd-lp' ); ?>
		</label>
	</p>

   	<div id="edd_license_prices_container" <?php echo $custom_pricing ? '' : 'style="display: none;"'; ?>>
<table class="wide-fat"><tbody>
		<tr><td>
        	<label for="edd_license_prices_default_price"><?php _e( 'Upgrade License Type: ', 'edd-lp' ); ?>
            <?php if ( ! isset( $edd_options['currency_position'] ) 
					|| $edd_options['currency_position'] == 'before' ) : ?>
                <?php echo edd_currency_filter( '' ); ?>
			<input type="text" name="edd_license_prices_default_price" 
					id="edd_license_prices_default_price" 
					value="<?php echo isset( $default_price ) ? esc_attr( $default_price ) : ''; ?>" 
					style="width: 140px;" placeholder="0.00">
                <?php else : 
				// try edd_format_amount ?>
                    <input type="text" name="edd_license_prices_default_price" 
						id="edd_license_prices_default_price" 
						value="<?php echo isset( $default_price ) ? esc_attr( $default_price ) : ''; ?>" 
						style="width: 140px;" placeholder="0.00"/>
						<?php echo edd_currency_filter( '' ); ?>
            <?php endif; ?>
            <?php esc_html_e( 'Leave empty for none', 'edd-lp' ); ?>
	        </label>
        </td>
		<td>
        	<label for="edd_license_prices_default_text" style=""><?php _e( 'Label text: ', 'edd-lp' ); ?></label>
            <input type="text" name="edd_license_prices_default_text" 
					id="edd_license_prices_default_text" 
					value="<?php echo isset( $default_text ) 
								? esc_attr( $default_text ) : 'Business License'; ?>" 
					style="width: 140px;">
            <?php esc_html_e( 'First button text', 'edd-lp' ); ?>
        </td>
		</tr>
		<tr>
		<td>
        	<label for="edd_license_prices_second_price">
					<?php _e( 'Second License Type: ', 'edd-lp' ); ?>
            <?php if ( ! isset( $edd_options['currency_position'] ) 
					|| $edd_options['currency_position'] == 'before' ) : ?>
                <?php echo edd_currency_filter( '' ); ?>
			<input type="text" name="edd_license_prices_second_price" 
					id="edd_license_prices_second_price" 
					value="<?php echo isset( $second_price ) 
								? esc_attr( $second_price ) : ''; ?>" 
					style="width: 140px;" placeholder="0.00">
                <?php else : ?>
            <input type="text" name="edd_license_prices_second_price" 
					id="edd_license_prices_second_price" 
					value="<?php echo isset( $second_price ) 
								? esc_attr( $second_price ) : ''; ?>" 
					style="width: 140px;" placeholder="0.00">
							<?php echo edd_currency_filter( '' ); ?>
            <?php endif; ?>
            <?php esc_html_e( 'Leave empty for none', 'edd-lp' ); ?>
        	</label>
        </td>
        <td>
        	<label for="edd_license_prices_second_text">
					<?php _e( 'Label text: ', 'edd-lp' ); ?>
            <input type="text" name="edd_license_prices_second_text" 
					id="edd_license_prices_second_text" 
					value="<?php echo isset( $second_text ) 
								? esc_attr( $second_text ) : 'Commercial License'; ?>" 
					style="width: 140px;">
            <?php esc_html_e( 'Second button text', 'edd-lp' ); ?>
        </td>
		</tr></tbody></table>
  	</div>
<?php
}
add_filter( 'edd_after_price_field', 'edd_license_prices_render_custom_price_field' );

/*
* Add fields to be saved
*/

function edd_license_prices_metabox_fields_save( $fields ) 
{
	$fields[] = '_edd_license_prices_pricing';
	$fields[] = 'edd_license_prices_default_price';
	$fields[] = 'edd_license_prices_second_price';
	$fields[] = 'edd_license_prices_default_text';
	$fields[] = 'edd_license_prices_second_text';
	
		return $fields;
}
add_filter( 'edd_metabox_fields_save', 'edd_license_prices_metabox_fields_save' );
