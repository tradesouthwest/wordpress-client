=== Delivery Validation TSW ===
Contributors:      tradesouthwest
Donate link:       https://paypal.me/tradesouthwest
Tags:              menu, orders, delivery, date
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
4. Around about line 254 in food-order-premium/classes/class-fdoe-product.php plugin, 
    add function into file; just in front of <span><a add to cart:
    `if(function_exists('youbechef_hook_into_fdoe_quantity_field')){
        $qtyfdoe = youbechef_hook_into_fdoe_quantity_field(); } else { $qtyfdoe = ''; }

			$do_add_url = '<span class="qtyfdoe-field">'.$qtyfdoe.'</span>....`

== Frequently Asked Questions ==

== Upgrade Notice ==
n/a

== Changelog ==
1.0.1
* initial release
1.0.2
* added functionality for Food Menu plugin