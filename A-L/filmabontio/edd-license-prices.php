<?php
/*
Plugin Name: EDD License Prices 
Plugin URL: http://themes.tradesouthwest.com/plugins
Description: Allow customers to enter a custom price for a product, based on a minimum price set in the admin.
Version: 1.0.5
Author: Easy Digital Downloads
Author URI: https://tradesouthwest.com
EDD Version Required: 1.9
PHP version required: 5.6 
*/

/* ------------------------------------------------------------------------*
 * Constants
 * ------------------------------------------------------------------------*/

// Plugin version
if( ! defined( 'EDD_LICENSE_PRICES' ) ) {
	define( 'EDD_LICENSE_PRICES', '1.0.5' );
}

// Plugin Folder URL
if( ! defined( 'EDD_LICENSE_PRICES_URL' ) ) {
	define( 'EDD_LICENSE_PRICES_URL', plugin_dir_url( __FILE__ ) );
}

// Plugin Folder Path
if( ! defined( 'EDD_LICENSE_PRICES_DIR' ) ) {
	define( 'EDD_LICENSE_PRICES_DIR', plugin_dir_path( __FILE__ ) );
}

register_activation_hook(   __FILE__, 'edd_license_prices_activation' );
register_deactivation_hook( __FILE__, 'edd_license_prices_deactivation' );

/**
 * Activate/deactivate hooks
 * 
 */
function edd_license_prices_activation() 
{
    // Create transient data 
    //set_transient( 'edd-cp-admin-notice', true, 5 );
    //flush_rewrite_rules();
	return false;
}
function edd_license_prices_deactivation() 
{
	return false;
}

/*
* Show notice if EDD is disabled, and deactive Custom Prices
*/
function edd_license_prices_admin_notice() {
	if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		deactivate_plugins( 'edd-custom-prices/edd-custom-prices.php' ); ?>
		<div class="error"><p><?php _e( '<strong>Error:</strong> Easy Digital Downloads must be activated to use the Custom Prices extension.', 'edd-lp' ); ?></p></div>
	<?php }
}
add_action( 'admin_notices', 'edd_license_prices_admin_notice' );

/**
 * Internationalization
 *
 * @access      public
 * @since       1.5.5
 * @return      void
 */
function edd_license_prices_load_textdomain() {
	// Set filter for language directory
	$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	$lang_dir = apply_filters( 'edd_license_prices_language_directory', $lang_dir );

	// Traditional WordPress plugin locale filter
	$locale = apply_filters( 'plugin_locale', get_locale(), '' );
	$mofile = sprintf( '%1$s-%2$s.mo', 'edd-lp', $locale );

	// Setup paths to current locale file
	$mofile_local   = $lang_dir . $mofile;
	$mofile_global  = WP_LANG_DIR . '/edd-license-prices/' . $mofile;

	if ( file_exists( $mofile_global ) ) {
		// Look in global /wp-content/languages/edd-license-prices/ folder
		load_textdomain( 'edd-lp', $mofile_global );
	} elseif ( file_exists( $mofile_local ) ) {
		// Look in local /wp-content/plugins/edd-license-prices/ folder
		load_textdomain( 'edd-lp', $mofile_local );
	} else {
		// Load the default language files
		load_plugin_textdomain( 'edd-lp', false, $lang_dir );
	}
}
add_action( 'plugins_loaded', 'edd_license_prices_load_textdomain' );

/*
* Fix download files when using variable pricing.
* For now it will only return the files for the first variable option.
*/

function edd_license_prices_download_files( $files, $id, $variable_price_id ) {
	if ( ! edd_license_prices_has_custom_pricing( $id ) || $variable_price_id != -1 ) {

		return $files;
	}
	remove_filter( 'edd_download_files', 'edd_license_prices_download_files' );	
	$files = edd_get_download_files( $id, 1 );
	return $files;
}
add_filter( 'edd_download_files', 'edd_license_prices_download_files', 10, 3 );

/*
* Enqueue scripts
*/

function edd_license_prices_load_scripts() 
{
	//EDD_LICENSE_PRICES
	wp_register_script( 'edd-license-prices-form', 
						EDD_LICENSE_PRICES_URL . 'js/edd-license-prices-form.js', 	
						array( 'jquery' ), time(), true );
	wp_enqueue_script( 'edd-license-prices-form' );
	// js cookie script
	wp_register_script( 'js-cookie', 
						EDD_LICENSE_PRICES_URL . 'js/js-cookie.js', 	
						array(), time(), true );
	wp_enqueue_script( 'js-cookie' );

	wp_register_script( 'edd-license-prices', 
						EDD_LICENSE_PRICES_URL . 'js/edd-license-prices.js', 
						array( 'js-cookie' ), time(), false ); 

	//wp_enqueue_script( 'edd-license-prices-ajax' );
	wp_localize_script('edd-license-prices-ajax', 'license_upgraded_vars', 
							array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),
								   'ajaxnonce' => wp_create_nonce('elp-upgraded-nonce') ) 
							);
	
}
add_action( 'wp_enqueue_scripts', 'edd_license_prices_load_scripts' );
//add_action( 'wp_ajax_license_upgraded', 'edd_license_prices_upgraded' );
//add_action( 'wp_ajax_nopriv_license_upgraded', 'edd_license_prices_upgraded' );

/*
* Enqueue admin scripts
*/
if ( is_admin() ) : 
function edd_license_prices_load_admin_scripts($hook) {
	global $post;

	if ( is_object( $post ) && $post->post_type != 'download' ) {
	    return;
	}

	wp_enqueue_script( 'edd-license-prices-admin', 
						EDD_LICENSE_PRICES_URL . 'js/edd-license-prices-admin.js', 
						array( 'jquery' ), EDD_LICENSE_PRICES );
}
add_action( 'admin_enqueue_scripts', 'edd_license_prices_load_admin_scripts' );

endif;

/**
 * Cookie  COOKIEPATH, COOKIE_DOMAIN );
 *
 * @see https://www.sitepoint.com/how-to-set-get-and-delete-cookies-in-wordpress/
 */
add_action( 'init', 'edd_license_prices_cookie_setter' );
function edd_license_prices_cookie_setter()
{
	//remove_action('init', 'edd_license_prices_cookie_handler');
	$value = sanitize_text_field( '0' );
	if ( !isset( $_COOKIE['elp_license_price'] ) ) { 
    setcookie('elp_license_price', $value, time() + ( 60 * 60 ), COOKIEPATH, COOKIE_DOMAIN );
	} else {
	setcookie('elp_license_price', 0, time() - ( 60 * 60 ), COOKIEPATH, COOKIE_DOMAIN ); 	
	} 
	//add_action('init', 'edd_license_prices_cookie_handler');
}
/*
* Check if product has custom pricing enabled
*/
function edd_license_prices_has_license_pricing( $download_id ) 
{
    global $post;
	
	$post_id = is_object( $post ) ? $post->ID : 0;

	if ( empty( $download_id ) ) { $download_id = $post_id; }

	$edd_license_pricing = get_post_meta( $download_id, '_edd_license_prices_pricing', true );

	if( $edd_license_pricing > 0 ) {
		return true;
	} else {
		return false;
	}
}
/**
 * Position of currency symbol
 */
function edd_license_prices_set_currency_symbol()
{   
	global $edd_options;

	$position     = false;

	if ( ! isset( $edd_options['currency_position'] ) 
		|| $edd_options['currency_position'] == 'before' ) : 
		$position = true;
	endif;

		return $position;
}
require_once ( EDD_LICENSE_PRICES_DIR . 'admin/edd-license-prices-admin.php');  
//require_once ( EDD_LICENSE_PRICES_DIR . 'js/Cookie.php'); 
require_once ( EDD_LICENSE_PRICES_DIR . 'public/edd-license-prices-public.php'); 
require_once ( EDD_LICENSE_PRICES_DIR . 'public/edd-license-prices-checkout.php');

?>