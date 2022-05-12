<?php 
/**
 * @author Larry Judd @codeable.io
 */
add_action( 'mobile_child_banner', 'mobile_child_banner_render' );
function mobile_child_banner_render(){
	$img = "https://waikikicalendar.com/wp-content/uploads/2022/05/WaikikiCalendarFestivalsEventsNow-schetch_resized.jpg";

	echo '<figure class="mobile-banner-hero">
		<img src="' . esc_url( $img ) . '" title="waikiki calendar of events welcome to hawaii" 
		class="mobile-child-hero-image" alt="calendar for waikiki events" />
	</figure>';
} 

function mobile_child_register_widget_areas() {

  register_sidebar( array(
    'name'          => 'Footer area one',
    'id'            => 'footer_area_one',
    'description'   => 'This widget area discription',
    'before_widget' => '<section class="footer-area footer-area-one">',
    'after_widget'  => '</section>',
    'before_title'  => '<h4>',
    'after_title'   => '</h4>',
  ));
  
}
add_action( 'widgets_init', 'mobile_child_register_widget_areas' ); 
