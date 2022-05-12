<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the class=site-inner div and all content after
 *
 * @package Omega
 */
?>
		<?php do_action( 'omega_after_main' ); ?>
		<?php /* if( function_exists('mobile_child_register_widget_areas'))
		dynamic_sidebar( 'footer_area_one' ); */ ?>
	</div><!-- .site-inner -->
	<?php 
	do_action( 'omega_before_footer' ); 
	do_action( 'omega_footer' ); 
	do_action( 'omega_after_footer' ); 
	?>
</div><!-- .site-container -->
<?php do_action( 'omega_after' ); ?>
<?php wp_footer(); ?>
</body>
</html>
