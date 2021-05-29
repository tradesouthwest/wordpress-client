=== TarpBuilder ===
Contributors:     tradesouthwestgmailcom
Donate link:      https://paypal.me/tradesouthwest
Tags:             woocommerce, custom, product, addition, increments, fees
Requires at least: 4.2
Tested up to:      5.2.4
Requies:           Woocommerce plugin
Stable tag:        1.0.5
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-3.0.html
Plugin URI:        http://themes.tradesouthwest.com/plugins/

TarpBuilder Plus add custom cart item data to category custom-tarp for Woocommerce products.

== Description ==

TarpBuilder Plus add custom cart item data to category custom-tarp for Woocommerce products.
This plugin is pretty much just the opposite of a Discount or Price Reduction plugin: it will add and multiply the increment to the product depending on how many increments are selected. You can order a Green Widget with 7 days rental and at $10 a day you will be paying $70 dollars on top of the product cost. This additional fee will show in the cart totals and in each cart item product field just next to the product price.

== Features ==

* Set value of increment in every product separately
* Auto sense your currency symbol
* Custom name your custom item's duration 
* Can be use for number of days, hours, any given duration
* Option to turn off requirement of adding duration to cart
* No strings attached simplicity setup and configure
* Adds data to admin order panel
* Sends additional text with email invoices
* Allow for tax rate separate from the product tax rate
* Help and Instructions built into admin page
* Alter text in cart, checkout, email and admin to accomodate
* Adjust positions of text on cart and product page
* Add HTML before or after TarpBuilder sections
* Change color of Woocommerce buttons!
* Full instructions in admin panel


== Installation ==

This section describes how to install the plugin and get it working.
1. Upload `tarpbuilder` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Only products with the Category of "custom-tarps" will display this plugin options.

== Instructions ==

- build product using category "custom tarps"
- add attributes Material, Color, Hemmed, Custom Tarp Dimensions, Trison Custom Tarps
- only use for variations Material, Color and Hemmed


== Frequently Asked Questions ==

Q.: Does TarpBuilder work for all WooCommerce product types?
A.: Not for all product types but it has been tested to work on Simple Product and Variable Products. So please ask our support first, before you purchase TarpBuilder-Plus, if you have special needs.
Q.: Can I change the background colors or text colors of any fields?
A.: There is a setting in your admin panel to set default usage of TarpBuilder styles. You can also turn off default styles if it interferes with your theme. We also provide a samll color Picker tool to change the WooCommerce button colors if you so decide to change those purple buttons.

== Screenshots ==

== Upgrade Notice ==
== TODO ==
Max number of increments per product
Allow increments in tarpbuilder_plus_qnty (min max steps)
activate/deactivate PRODUCT quantity tarpbuilder_plus_product_quantity
use tarpbuilder_plus_numberof for 'set price/number' not publicly changable (flat fee)
fixed start date (today or selected date)
change text of tarpbuilder_plus_fee per product
turn on/off datepicker, per product.
subtract/add/mutiply tarpbuilder_plus_qunty, per product

== Changelog ==
= 1.0.2 =
* modified slugs
* removed transient notice
* changed some admin descriptions
* modified for trisontarps
= 1.0.5 =
* add grommet text -include/postmeta & -include/quantity
* change names of length and width inputs -include/postmeta
* removed date postmeta
* revised wc_get template part in single https://timberwebdesign.com/editing-woocommerce-product-page-layout-right-way-2018/
* changed select dropdown text
* added content after variation price summary
= 1.0.6 =
* added admin-order.php
* included admin-order from admin-page.php
* added order item admin order_data_after_order_details
= 1.0.8 =
* moved attr from .quantity to .attribute.php
* conditional to enqueue script
* changed name grommets.js to tarpbuilder.js
