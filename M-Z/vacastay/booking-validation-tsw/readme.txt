=== Booking Validation TSW ===
Contributors:      tradesouthwest
Donate link:       https://paypal.me/tradesouthwest
Tags:              menu, orders, booking, date
Requires at least: 3.8
Tested up to:      4.9.1
Requires PHP:      5.2
Stable tag:        1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Plugin URI: http://themes.tradesouthwest.com/wordpress/plugins/

Set validation for checkout module

== Description ==
Set validation for checkout module

== Features == 
* Add text above cart 

== Screenshots ==

== Installation ==
This section describes how to install the plugin and get it working.
1. Upload `.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings and configure OnList settings.
&gt;?php if ( function_exists('booking_validation_tsw_checkout_html') ) { do_action('booking_validation_after_booking_summary'); } ?&gt;
== Frequently Asked Questions ==

== Upgrade Notice ==
n/a

== Changelog ==
1.0.0
* initial release