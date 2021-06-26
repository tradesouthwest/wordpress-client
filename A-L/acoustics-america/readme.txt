=== Sound Absorption Calculator ===
Contributors:  tradesouthwest
Donate link: https://paypal.me/tradesouthwest
Tags: directory, classifieds, listings, ecommerce, business directory, listing, classified ads, responsive, forms, metabox, real estate, catalog
Requires at least: 3.8
Tested up to: 4.9.1
Requires PHP: 5.2
Stable tag: 1.0.0
License: Apache License
License URI: See File, LICENSE
Plugin URI: http://themes.tradesouthwest.com/wordpress/plugins/

Calculates sound absorbtion (NRC) level for Woocommerce products using category.

== Description ==
Calculates sound absorbtion (NRC) level for Woocommerce products using category. The NRC is calculated by averaging the various measurements and rounded off to the nearest 0.05.
Demo at http://tradesouthwest.com/onlist/

== Features == 
* Most useful settings to start a directory, with little to no configuration.
* Change listing field names to make OnList versatile.
* Custom meta data for every listing.
* Branding for your log in page.
* Branding of User dashboard features.
* Dashboard widgets to show user content.
* Before and After Content widgets included.
* Category Widget included for sidebars, plus category shortcode.
* Supports comments which can be used for service requests or reviews.
* Supports Featured Image.
* Supports Excerpts.
* Turn on or off comments.
* Set number of days to display a listing.
* Set featured listing to top of page.
* Spam protection assets to safe guard email and phone numbers.
* Certain fields can be hidden to paid or subscribed users only.
* Page view counter shows on listing.
* Rating system 1 to 5 auto-calculate.
* Has auto detect features to keep from clashing with other plugins and themes.
* USA States are included but can be excluded as categories.
* Sub-Categories can be added.
* Only two fields: Address and Postal/Zip Code is all that is needed to show map.
* Set size of map box on page.
* Add any label to "Other" field (Specialist In, UPC, Certification Number, etc.)
* Plugin is responsive layout.
* Uses seven shortcodes and Google Maps API key to display all settings on page.
* Custom templater functions help display listings on most any theme.

== Screenshots ==
1. Listing fields
2. Admin Page with settings
3. Admin Page Three
4. Editor meta boxes
5. Basic listing page
6. Single listing page
7. Single listing with images

== Installation ==
This section describes how to install the plugin and get it working.
1. Upload `onlist.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings and configure OnList settings.
4. Add shortcode to a page to display listsings [onlist-listings]
5. Optional, add shortcode to a page or use widget to display categories [onlist-categories]
6. Optional, add shortcode to a page to display login form [onlist-loginform]
7. Go through all options in OnList Settings to activate what you want to use.
8. Suggested to set Permalinks to `post name.`

== Frequently Asked Questions ==
Q.: How do I let other people add a listing?
A.: This plugin uses normal WordPress login and user functions. So just use it like you would a blog. Have People register and then they will have their own listing admin area without the administrative rights. (They can not change the plugin settings.)

Q.: Where are the controls for this plugin?
A.: You will find the setup under menu page "OnList Settings." 

Q.: Can I change the listing style?
A.: If you know CSS then you can use any CSS editor to add a selctor in your Customizer or and other stylesheet. Using the Inspector on your browser it is easy to look up the selector name. Most all of OnList selectors start with onlist-.

Q.: How do I get an API key to use the maps?
A.: To get an API Key, Visit: https://developers.google.com/maps/documentation/javascript/get-api-key. API will be required to have a Google account in order to create the key but the proccess is simpler than pie. No programming experience or coding required. Just copy the number and paste it into the plugin field.

Q.: I just loaded a new theme and can not see the listings?
A.: Go to Settings > Permalinks and hit "Save Changes." This refreshes your posts to recognize the OnList post. It is also possible that you have changed the permalink settings. Try to keep settings as "Post name" permalink.

== Upgrade Notice ==
n/a

== Changelog ==
1.0.0
* initial release