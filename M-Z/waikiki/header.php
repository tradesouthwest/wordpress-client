<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> <?php omega_attr( 'body' ); ?>>
<?php do_action( 'omega_before' ); ?>
<div class="<?php echo omega_apply_atomic( 'site_container_class', 'site-container' );?>">
	<?php 
	do_action( 'omega_before_header' );
	do_action( 'omega_header' );
	do_action( 'omega_after_header' ); 
	?>
	<?php if ( function_exists( 'mobile_child_banner_render' ) ) : ?>
	<div class="banner-top">
		<?php do_action( 'mobile_child_banner' ); ?>
	</div>
	
	<!-- 21.2793° N, 157.8292° W -->
	<div class="weather-widget-holder" style="display:block">
	
	<div id="ml_a073d126"><div style="padding:0;margin:0;" id="ml_a073d126_i" v='1.1' a='{"t":"g7bs","lang":"en","ids":["6494"],"a_br_c":"#039BD5","a_bg":"#038BD5","a_c":"rgba(255,255,255,1)","p_cr":0,"a_t_mr_lf":10}'></div><div id="ml_a073d126_c" style="padding:0;margin:0;padding:7px 5px;"><img src="https://sharpweather.com/assets/img/logo_z_w.svg" style=";vertical-align:baseline;padding:0;margin:0;width:15px;opacity:0.7;margin-right:5px;position:relative;top:50%;transform:translateY(-6px);vertical-align:top;display:inline-block;"><a href="https://sharpweather.com/weather_honolulu/" style=";vertical-align:baseline;padding:0;margin:0;color:inherit;text-decoration:none;display:inline-block;box-sizing: border-box;padding:0 5px;font-size:14px;line-height:14px;position:relative;top:50%;transform:translateY(-7px);vertical-align:top;" target="_blank" id="ml_a073d126_u">Weather in Honolulu</a></div></div><script async src="https://app.sharpweather.com/js/?id=ml_a073d126"></script>
	
	</div>
	
	<?php endif; ?>
	
	<div class="site-inner">

		<?php do_action( 'omega_before_main' ); ?>
