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

/**
 * parseURL of iframe so it is accessable for styling
 *
 * @usage run do_action() in template
 */

add_action( 'waikiki_embed_calendar', 'waikiki_embed_styled_calendar' );
function waikiki_embed_styled_calendar(){
$your_google_calendar="https://calendar.google.com/calendar/embed?height=600&wkst=1&bgcolor=%23ffffff&ctz=Pacific%2FHonolulu&mode=AGENDA&showTabs=1&showCalendars=1&src=d2Fpa2lraWNhbGVuZGFyQGdtYWlsLmNvbQ&color=%23039BE5";

$url= parse_url($your_google_calendar);
$google_domain = $url['scheme'].'://'.$url['host'];

// Load and parse Google's raw calendar
$dom = new DOMDocument;
$dom->loadHTMLfile($your_google_calendar);

// Create a link to a new CSS file called schedule.min.css - Leaving out since I styled inline on template 
/*
$element = $dom->createElement('link');
$element->setAttribute('type', 'text/css');
$element->setAttribute('rel', 'stylesheet');
$element->setAttribute('href', '/css/schedule.min.css');
*/
// Change Google's JS file to use absolute URLs
$scripts = $dom->getElementsByTagName('script');

foreach ($scripts as $script) {
  $js_src = $script->getAttribute('src');
  
  if ($js_src) {
    $parsed_js = parse_url($js_src, PHP_URL_HOST);
    if (!$parsed_js) {
      $script->setAttribute('src', $google_domain . $js_src);      
    }
  }
}

 // Append this link at the end of the element
$head = $dom->getElementsByTagName('head')->item(0);
$head->appendChild($element);

// Remove old stylesheet
$oldcss = $dom->documentElement;
$link = $oldcss->getElementsByTagName('link')->item(0);
$head->removeChild($link);

// Export the HTML
echo $dom->saveHTML();
} 
