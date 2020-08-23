<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * load plugin components only if set to 1
 * @since 1.0.3
 */
$readlimit_pluginoff = (empty( get_option('readlimit_options')['readlimit_pluginoff'] ))
				  ? 0 : absint(get_option('readlimit_options')['readlimit_pluginoff']);
// @uses 0 for plugin ON
if ( 0 == $readlimit_pluginoff ) : 

	// A1
	add_action( 'wp_login', 'readlimit_user_update_last_login', 10, 2 );
	// A2
	add_action( 'wp_footer', 'readlimit_session_timer_infooter', 10, 1 );
	

	//add_action ( 'readlimit_retrieve_logintime', 'readlimit_retrieve_logintime_ofuser' );

// A3
//add_action('get_header', 'readlimit_pageview_content_restriction');
// A4 only used in admin screen
//add_action('wp_login', 'readlimit_access_check_subscriber_status', 0, 2);
// A5 cookie buffer init
//add_action('init', 'readlimit_pageview_content_restriction_output_buffer');

endif;


/** A1
 * Last Login time and date 
 * @uses get_user_meta on wp_login
 * @param string $new_expiration Rest expire to Sunday
 * @param string $last_login     Object of RCP > User_Meta
 * @param int    $user_id        ID of the user to login.
 */
function readlimit_user_update_last_login( $user_login, $user ) {
    
    $user_id              = $user->ID;
    $login_count          = 0;
    $last_login           = get_user_meta($user_id, 'last_login', true);
    $readlimit_login_time = time();
    $login_count          = get_user_meta($user_id, 'login_count', true);
    /*
	 * By default this expiration date value is empty. This means the expiration date will be
	 * auto calculated based on the membership level you've chosen. If you want to use a different
	 * date you can set one in this value. You can use "none" for the membership to never expire,
	 * or you can specify a MySQL-formatted date like so: "2020-12-31 23:59:59"
	 */
    // TODO make reset override to default 'none'
    $readlimit_weekly_reset = date( "Y-m-d 23:59:59", strtotime('next Sunday') );
    $login_count            = absint( $login_count + 1 );

    update_user_meta( $user_id, 'last_login',   $readlimit_login_time );
    update_user_meta( $user_id, 'login_count',   $login_count );
    update_user_meta( $user_id, 'rcp_expiration', $readlimit_weekly_reset );

}

/** A2
 * Start timer
 * @param string $countdown   Option value from plugin converted to epoch time.
 * @param string $last_login  Option value from plugin.
 * @param string $rlrls       Option default.

 */
function readlimit_session_timer_infooter($user_id)
{
	global $rcp_options;
	if ( !is_singular('newsletter') ) return; 
	
	$rlrls = $last_login = $rlcountdown = '';
	$readlimit_redir_page = ('' == ( get_option('readlimit_options')['readlimit_redir_page'] ))
                             ? "#" : get_option('readlimit_options')['readlimit_redir_page'];
	$rlredir_page = esc_url( site_url('/') . $readlimit_redir_page );
	$last_login  = get_user_meta( $user_id, 'last_login', true );
	$rlstarts    = ( $last_login == '' ) ? time() : $last_login;
	$rlrls       = (empty(get_option( 'readlimit_options' )['readlimit_readlimits'])) 
					? 86399 : get_option( 'readlimit_options' )['readlimit_readlimits'];
	$rlcountdown = absint( ( time() + $rlrls )); 
	$rlmessage   = ( '' == ( get_option('readlimit_options' )['readlimit_rtdtitle'] )) 
					? 'Proceed to next article' 
					: get_option('readlimit_options')['readlimit_rtdtitle'];
	$rlrcp_message = $rcp_options['restriction_message']; 
	?>
	<div class="rlhidden-wrap" style=""> 

		<div class="flhidden-inner-position" style="">
			<div id="rlcount" class="rlcounter"></div>
		
				<div id="rlnext" class="rlhidden" style="visibility: hidden; ">
					<div class="rlhidden-box">
						<p><a href="<?php print($rlredir_page); ?>" 
							class="link" title="proceed" data-link="rlredir-page">
					<?php echo esc_html($rlrcp_message); ?></a></p>
					<?php //echo esc_html($rlmessage); ?>
					</div>
				</div>

			</div>
		</div>
	<script id="readlimit-session-timer" type="text/javascript">
	window.onload = function(){
		(function(){
		var counter = <?php echo absint( $rlrls ); ?>;
			setInterval(function() {
				counter--;
				if (counter >= 0) {
					span = document.getElementById("rlcount");
					span.innerHTML = counter;
				}
				if (counter === 0) {
					//confirm('this is where it happens'); 
					document.getElementById("rlnext").style.visibility = "visible"; 
					clearInterval(counter);
				}
			}, 1000);
		})();
	}
	</script>

<?php

}
/**
 * Finished timer read time routine
 *
 */

/** A3
 * Get login time and compare to readlimit settings
 *
 * @return Bool
 */
function readlimit_retrieve_login_andstart_timer($user)
{
	$cutoffday = $readlimit = $last_login = $new_expr_date = '';
    if ( is_single( 'newsletter' ) ) : 

		$redir_page = (empty( get_option('readlimit_options' )['readlimit_redir_page'] )) 
					? 'newsletter-members-area/' 
					: sanitize_text_field(get_option('readlimit_options')['readlimit_redir_page']);
		$user_id    = $user->ID; 
		$last_login = get_user_meta( $user_id, 'last_login', true );
		$cutoffday  = get_option( 'readlimit_options' )['readlimit_cutoffday'];
		$readlimit  = get_option( 'readlimit_options' )['readlimit_readlimits'];
		// will need cutoff day and 24 hr timer both, to determine redirect
		$new_expr_date = date( "Y-m-d 23:59:59", strtotime( 'next ' . $cutoffday ));

		if( time() >= $new_expr_date || $readlimit > $last_login )
		{
		
			wp_safe_redirect( apply_filters( 'rcp_login_redirect_url', 
            						        site_url('/') . $redir_page, $user )); 
                    exit; 
		}
		
	endif;
	return false;
}
