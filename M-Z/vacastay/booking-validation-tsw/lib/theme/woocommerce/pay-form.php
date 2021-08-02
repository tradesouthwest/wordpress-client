<?php
/**
 * Pay for order form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-pay.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 5.2.0
 */
// Added to line 159 
<?php /* ************ added by larry@codeable ************* */ if( function_exists( 'booking_validation_tsw_display_deposit_data_in_cart' ) ) { ?> <div class="inner-booking-list"> <?php do_action( 'tsw_add_deposit_data_to_cart', $listing_id ); ?> </div><?php /* ends added by larry */ } ?>
