<?php
/*
Plugin Name: Coupon Editor
Plugin URI: https://plugins.tradesouthwest.com
Description: Ad coupons to site. Use shortcode [coupon_editor title="Your title"]Coupon text here[/coupon_editor]
Author: Tradesouthwest
Version: 1.0
Domain Path: coupon-editor
Author URI: https:/tradesouthwest.com
*/

function coupon_editor_plugin_activation() 
{

    // Create transient data 
    //set_transient( 'coupon_editor-admin-notice-startup', true, 5 );
   
    return false;
    
}
function coupon_editor_plugin_deactivation() 
{
    return false;
}

/**
 * Define the locale for this plugin for internationalization.
 * Set the domain and register the hook with WordPress.
 *
 * @uses slug `coupon-editor`
 */
//add_action('plugins_loaded', 'coupon_editor_load_plugin_textdomain');

function coupon_editor_load_plugin_textdomain() 
{

    $plugin_dir = basename( dirname(__FILE__) ) .'/languages';
	              load_plugin_textdomain( 'coupon-editor', false, $plugin_dir );
}

function coupon_editor_shortcode_render(  $atts = [], $content = null, $tag = '' ) 
{
    $coed_atts = shortcode_atts(
        array(
            'title' => 'Coupon',
        ), $atts, $tag
    );
     // start box
    $o = 
	'<div id="couponBox">
		<div class="coupon-container" style="border: 4px dashed #b22;width:310px;background:#f1f1f1;">
			<div class="coupon-box" style="padding-left:0px;padding-right:0px;min-height:148px;margin: 3px;">';

    $o .= 
		    '<div class="coupon-header" style="">
			   <p class="coupon-title" style="text-align:center;font-size:larger;color: #fff;margin-bottom: 0;padding-bottom: 10px;padding-top: 7px;">
			   <strong>' . esc_html__( $coed_atts['title'], 'coupon-editor' ) . '</strong></p></div>
				<div class="coupon-content" style="text-align:center;">';
      
    if ( ! is_null( $content ) ) {
        // secure output by executing the_content filter hook on $content
        //$o .= apply_filters( 'the_content', $content );
 
        // run shortcode parser recursively
        $o .= do_shortcode( $content );
    }

 // end box
    $o .= '</div>
			</div>
		</div>
		<div class="coupon-print" style="text-align:center;">
			<span class="hidden-print"><input style="text-transform:uppercase;background:red;color:white;padding: 6px 10px;border-radius: 5px;margin: .5em auto;font-weight:600;border:1px solid #c00;" type="button" value="print coupon" onclick="printDiv();"></p></span>
		</div>
	
	</div>
		<script id="coupon-editor-onpage">
		function printDiv(){
			var printContents = document.getElementById("couponBox").innerHTML;
			var originalContents = document.body.innerHTML;

			document.body.innerHTML = printContents;

			window.print();

			document.body.innerHTML = originalContents;

		}
	</script>';
 
    // return output
    return $o;
	
	
}

function coupon_editor_shortcode_register()
{
     
  add_shortcode( 'coupon_editor', 'coupon_editor_shortcode_render' );
}
add_action( 'init', 'coupon_editor_shortcode_register');
?>
