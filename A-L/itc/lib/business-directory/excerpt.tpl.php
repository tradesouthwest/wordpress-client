<?php
/**
 * Template listing excerpt view.
 * @since views_addon plugin https://businessdirectoryplugin.com/knowledge-base/general-customization-guide/
 * @package BDP/Templates/Excerpt
 */

$__template__ = array( 'blocks' => array( 'before', 'after' ) );
?>
<div id="<?php echo esc_attr( $listing_css_id ); ?>" class="<?php echo esc_attr( $listing_css_class ); ?>" data-breakpoints='{"medium": [560,780], "large": [780,999999]}' data-breakpoints-class-prefix="wpbdp-listing-excerpt">
	<?php 
	if ( function_exists( 'views_addon_exclude_assigned_listings_archive' ) ) {
		$addonviews_class = views_addon_exclude_assigned_listings_archive(); 
	} else { 
		$addonviews_class = ''; 
	}
	echo '<div style="display: '. esc_attr( $addonviews_class ) .'">'; 
	echo $blocks['before'];
	if ( in_array( 'excerpt', wpbdp_get_option( 'display-sticky-badge' ) ) ) {
		echo $sticky_tag;
	}
echo '<h5 style="margin: 0 auto 0 0">Play by: ' . get_the_author() . '</h5>';
	wpbdp_x_part( 'excerpt_content' );
	echo $blocks['after'];
	
	echo wpbdp_the_listing_actions();
	echo '</div>';
	?>
</div>
