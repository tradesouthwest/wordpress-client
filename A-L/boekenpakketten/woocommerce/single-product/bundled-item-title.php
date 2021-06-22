<?php
/**
 * Bundled Item Title template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/bundled-item-title.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * Note: Bundled product properties are accessible via '$bundled_item->product'.
 *
 * @version 6.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $title === '' ) {
	return;
}

?><h4 class="bundled_product_title product_title"><span class="bundled_product_title-link"><?php 
	$label_title = $bundled_item->get_title();
	$link        = $permalink ? '<a class="bundled_product-permalink" href="' . $permalink . '" 
		 target="_self" aria-label="' . __( 'View product', 'woocommerce-product-bundles' ) . '">'
		. $label_title . '</a></span>' : '</span>';

		echo $link;
?></h4>
