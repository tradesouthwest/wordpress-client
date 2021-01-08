<?php 
/**
 * Upgrade functions
 *
 * @package     edd-license-prices/public
 * @since       1.0.2
 *  
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add fee for line items 
 * 
 * @see https://stackoverflow.com/questions/18304269/get-post-data-using-ajax/18309224
 * @param string $license_fee $_POST or localStorage value
 * @since Deprecated
 */
//add_action( 'init', 'edd_license_prices_upgrading' );
function edd_license_prices_upgrading($download_id, $price_id=0 )
{
	if( !edd_license_prices_has_license_pricing($download_id) ) {
		return;
	} 

	$dvalue      = '1'; // false;
	$license_fee = null;
	$cookieValue = ( '' != $_COOKIE['elp_license_price'] ) 
					? $_COOKIE['elp_license_price'] : $dvalue; 
	$license_fee = sanitize_text_field($cookieValue);

	if ( $license_fee > 0 ) : 
		EDD()->fees->add_fee( $license_fee, 'edd_license_upgraded', 'edd_license_upgraded' );
		
		setcookie('elp_license_price', 0, time() - ( 60 * 60 ), COOKIEPATH, COOKIE_DOMAIN ); 	
		
	endif;

		//return false;
}

/**
 * Add cookie value from form
 * @since Dev; not used
 */
//add_action( 'wp_head', 'edd_license_prices_store_cvalue' );
function edd_license_prices_store_cvalue()
{
    
	?>
	<script id="elp-license-upgraded" type="text/javascript">
	jQuery(document).ready(function($){
    'use strict';
        $(".wcpa_cart_val span").each(function(){
        var $this = $(this);
        $this.attr("data-content", $this.text());
        });
        })(jQuery); 
	</script>
	<?php
	
}
/* 
* Hook into add to cart post
* adds filter edd_purchase_link_top
* 
* @since 1.0.51
*/
add_filter( 'edd_purchase_link_top', 'edd_license_prices_purchase_link_top' );
function edd_license_prices_purchase_link_top( $download_id ) 
{
	if( !is_singular('download') ) {
		return;
	}
	if( !edd_license_prices_has_license_pricing($download_id) ) {
		return;
	} 

    // strings to default values
	//$data_nonce    = wp_create_nonce('edd-lp-nonce');
	$default_price = get_post_meta( $download_id, 'edd_license_prices_default_price', true );
	$second_price  = get_post_meta( $download_id, 'edd_license_prices_second_price', true );
	$edd_license_default = ('' != $_REQUEST['edd_license_price'] ) 
							? edd_sanitize_amount( $_REQUEST['edd_license_price'] )
							: 1;
	// isset from ajax
    $edd_license_price = isset( $_COOKIE['elp_license_price'] ) 
                     		? edd_sanitize_amount( $_COOKIE['elp_license_price'] ) 
                     		: edd_sanitize_amount( $_REQUEST['edd_license_price']);
	$position          = edd_license_prices_set_currency_symbol();
	?>

    <div class="edd-cp-container">
		<?php /* removed data-min="<?php echo $min_price; ?>" */ ?>
		<p><strong><?php esc_html_e( 'Business License', 'edd_cp' ); ?></strong> 
			<input type="radio" 
				name="edd_license_prices" 
				class="edd_license_prices" 
				id="edd_license_prices" 
				value="<?php echo esc_attr( $default_price ); ?>" 
                data-default-text="<?php esc_attr_e( 'Business License price', 'edd_cp' ); ?>" 
		 		style="" >
     			
			<?php 
			if ( $position === true ) : ?>
        	<span><?php echo edd_currency_filter( $default_price ); ?></span>
			<?php else: ?>
			<span><?php echo esc_html($default_price) . '' . edd_currency_filter(); ?></span>
			<?php endif; ?>
		</p>

		<p><strong><?php esc_html_e( 'Commercial License', 'edd_cp' ); ?></strong> 
			<label><input type="radio" 
				name="edd_license_prices" 
				class="edd_license_prices" 
				id="edd_license_prices" 
				value="<?php echo esc_attr( $second_price ); ?>" 
                data-default-text="<?php esc_attr_e( 'Commercial License price', 'edd_cp' ); ?>"
		 		style="">
     			
			<?php 
			if ( $position === true ) : ?>
        	<span><?php echo edd_currency_filter( $second_price ); ?></span>
			<?php else: ?>
			<span><?php echo esc_html($second_price) . '' . edd_currency_filter(); ?></span>
			<?php endif; ?></label>
		</p>
        <p><label><span class="lp-position" style="display: none">
						<?php if ( $position === true ) echo edd_currency_filter(); ?></span>
			<input type="text" 
				id="edd_license_price" 
				name="edd_license_price" 
				class="edd_license_price" 
				value="" 
				data-price="" 
		 		style="border:none;background:none;padding:0;width:5.67em">
				<span class="lp-position" style="display: none"><?php if ( $position === false ) echo edd_currency_filter(); ?></span> 
				 <em id="defaultText"></em>
        </label></p><span><?php print( $edd_license_price ); ?></span>
			<input type="hidden" name="action" value="license_upgraded">
			<span id="elp-ajax-error"></span>
  
    </div>
    <?php // add cart item of fees to cart session
	if( !empty( $edd_license_price ) ) : 
	EDD()->session->set( 'edd_cart_fees', null );
	$args = array(
			'amount' => $edd_license_price,
			'label' => 'Upgraded License',
			'download_id' => $_post->ID,
			'id' => 'edd_license_upgraded',
			'type' => 'item',
			'price_id' => 1
		);
	EDD()->fees->add_fee( $args ); 
		apply_filters( 'edd_fees_add_fee', $args, true );
	    setcookie('elp_license_price', 0, time() - ( 60 * 60 ), COOKIEPATH, COOKIE_DOMAIN ); 	
	endif;
		
} 
