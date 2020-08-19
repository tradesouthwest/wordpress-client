<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function readlimit_is_restricted_content($post_id){

    if(function_exists('rcp_is_restricted_content')) : 
        if(rcp_is_restricted_content( $post_id )){
        return true;
        }
    endif;
}
/**
 * Last Login time and date 
 * @uses get_user_meta on wp_login
 * @param string $new_expiration Rest expire to Sunday
 * @param int    $user_id        ID of the user to login.
 */
function readlimit_user_update_last_login( $user_login, $user ) {
    
    $user_id              = $user->ID;
    $login_count          = 1;
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
add_action( 'wp_login', 'readlimit_user_update_last_login', 10, 2 );
add_action( 'personal_options_update', 'readlimit_user_update_last_login', 10, 2 );


/**
 * Check if user logged in and is subscriber
 * @param string $user WP core get_user_by string $field, int|string $value
 * https://codex.wordpress.org/Class_Reference/WP_User_Query
 * human_time_diff($user->last_login) = 2mins
 */
function readlimit_access_check_subscriber_status()
{ 

    //$user_id = get_current_user_id(); 
    global $wpdb;
    $args = array(
    'role'    => 'Subscriber',
    'orderby' => 'registered', 
    'order'   => 'ASC'
    );

    // The Query
    $user_query = new WP_User_Query( $args );

    //ob_start();
    // User Loop
    ?>
    <table class="widefat fixed" cellspacing="0">
    <thead><tr>
    
        <th>display name</th>
        <th>date user registered</th>
        <th>date last logged in + count</th>
        <th>subscription level</th>  
        <th>plan</th>
        <th>expiration</th>  
    </tr></thead>
    <tbody>
    <?php
    if ( ! empty( $user_query->get_results() ) ) { 
    
        // user->last_login is in epoch time
        foreach ( $user_query->get_results() as $user ) {
            $last_login = ( $user->last_login == '' ) ? 'never logged in' 
                            : date( 'Y-m-d H:i:s', $user->last_login );
            $login_count = ( $user->login_count < 1 ) ? 1 : absint($user->login_count);
        
        echo '<tr>';
        echo '<td>' . $user->display_name . '</td>
              <td>' . $user->user_registered . '</td>
              <td>' . $last_login . ' + ' . $login_count . '</td>
              <td>' . $user->rcp_subscription_level . '</td>
              <td>' . $user->rcp_status . '</td>
              <td>' . $user->rcp_expiration . '</td>';
        echo '</tr>';
        }
    
    } else { 

        echo '<tr><td colspan="4">No users found.</td></tr>';
    
    } ?> 
    
    </tbody></table>
    
    <?php 

}
//add_action('wp_login', 'readlimit_access_check_subscriber_status', 0, 2);
/**
 * Start session timer for reading
 * @param string $readlimit_login_time     Last login when read 
 * @param int    $readlimit_user_id        user_id
 * @param int    $readlimit_article_read   post_id
 
 */
function readlimit_pageview_content_restriction() {
    if( !is_user_logged_in() && !is_front_page() ) { 

    $readlimit_rtdtitle = (empty( get_option('readlimit_options' )['readlimit_rtdtitle'] )) 
	        ? 'expired' : esc_attr(get_option('readlimit_options')['readlimit_rtdtitle']);
    $redirect = site_url('/');
    $maxpageviews = false; 
        // define default max pageviews
        if(!$maxpageviews || !is_int($maxpageviews)) { $maxpageviews = 9;  }
        // get / increment current pageviews
        $current_pageviews = readlimit_pageview_session_counter($maxpageviews);
        // this is just debugging information which could also be stored in the next if()
        echo "<!-- Pageviews : $current_pageviews -->";

            if($current_pageviews > $maxpageviews) {
                if(empty($redirect)) {
                    $redirect = get_bloginfo('url')."/wp-login.php";
                }
                $status = "302";
                wp_redirect( $redirect, $status );
                exit;
            }
        ob_flush();
    }
}
//add_action('get_header', 'readlimit_pageview_content_restriction');

/*
 * this is needed for the redirection
 */
function readlimit_pageview_content_restriction_output_buffer() {
    ob_start();
}
add_action('init', 'readlimit_pageview_content_restriction_output_buffer');

/*
 * Provide session handling
 * from http://wordpress.org/plugins/simple-session-support/
 */
add_action('init', 'readlimit_pageview_session_start', 1);

/**
 * start the session, after this call the PHP $_SESSION super global is available
 */
function readlimit_pageview_session_start() {
    if(!session_id()) {
        // how long does the session cookie last
        session_set_cookie_params(86400, "/"); // 3600 seconds = 1h
        session_start();
    }
	ob_start();
}


/**
 * destroy the session, this removes any data saved in the session over logout-login
 */
function readlimit_pageview_session_destroy() {
    session_destroy ();
}
/**
 * get a value from the session array
 * @param type $key the key in the array
 * @param type $default the value to use if the key is not present. empty string if not present
 * @return type the value found or the default if not found
 */
function readlimit_pageview_session_get($key, $default='') {
    if(isset($_SESSION[$key])) {
        return $_SESSION[$key];
    }
}
/**
 * set a value in the session array
 * @param type $key the key in the array
 * @param type $value the value to set
 */
function readlimit_pageview_session_set($key, $value) {
    $_SESSION[$key] = $value;
}
/*
 * get unique browser ID
 */
function readlimit_get_unique_browser_identifier() {
    if(strpos($_SERVER['HTTP_USER_AGENT'], "Googlebot") !== FALSE || strpos($_SERVER['HTTP_USER_AGENT'], "Yahoo! Slurp") !== FALSE || strpos($_SERVER['HTTP_USER_AGENT'], "msnbot") !== FALSE) {
        return "donotblock";
    } else {
        // credit for this idea to identify visitors goes to Emmanuel Revah / manurevah.com
        return md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_IP']);
    }
}
function readlimit_pageview_session_counter($maxpageviews) {
    global $pageview_sessionfolder_path;
    if (readlimit_get_unique_browser_identifier() == "donotblock") {
        // major bots are not blocked.
        return 0;
    } else {
        /*
        * in any case
        * increment the pageview counting session cookie
        * or set the session cookie if it does not exist yet
        */
        $pageview_counter = readlimit_pageview_session_get('pageview_counter');
        if(!$pageview_counter) { $pageview_counter = 0; }
        $pageview_counter++;

        /*
        * verify also if there is a session file for the unique browser id
        * if so, verify if this file has already a pageview count and increment it, too
        * if not, use the session cookie's value and write this to the file
        */
        if(is_dir($pageview_sessionfolder_path)) {
            $file = $pageview_sessionfolder_path.'/'.readlimit_get_unique_browser_identifier();
            if(file_exists($file)) {
                $stored_pageviews = file_get_contents($file);
                // verify how many pages this person has already seen
                if($stored_pageviews < $maxpageviews) {
                    $stored_pageviews++;
                    file_put_contents($file, $stored_pageviews);
                } else {
                    // maximum already reached
                    // don't modify the file, that is unnecessary
                }
            } else {
                // 1st time write file using the session cookie pagecount
                // sanitize the session cookie, so no strange data is written to the file
                file_put_contents($file, esc_attr(intval($pageview_counter)));
                chmod($file, 0777);
            }
        }
       /*
        * in any case, write pageview count to session
        * use the value of the file if it's higher than that of the cookie.
        */
        if($stored_pageviews > $pageview_counter) $pageview_counter = $stored_pageviews;
            readlimit_pageview_session_set("pageview_counter", $pageview_counter);
        return $pageview_counter;
    }
}

/* if somebody logs in at some point, we need to shall his/her unique identifier file */
function readlimit_delete_pageview_session_counter() {
    // unset the session cookie
    readlimit_pageview_session_destroy();
    global $pageview_sessionfolder_path;

    $file = $pageview_sessionfolder_path.'/'.readlimit_get_unique_browser_identifier();
    if(file_exists($file)) {
        unlink($file);
    }
}
add_action('wp_login', 'readlimit_delete_pageview_session_counter');

/**
 * Restrict post content if users viewing limit is reached
 * 
 * @param string $content 
 * @return string
 */
function rcp_filter_content_restriction( $content ) {
	global $rcp_options;

	$post_type         = get_post_type_object( get_post_type() );
	$registration_page = isset( $rcp_options['registration_page'] ) 
                         ? get_permalink( $rcp_options['registration_page'] ) : home_url();

	if( rcp_user_is_restricted() ) {
		$content  = "<p>" . "\n";
		$content .= "<span>" . sprintf( __( 'You have reached your limit of free %s. ', 'rcp' ), 
                    strtolower( $post_type->labels->name ) ) . "</span>";
		$content .= "<a href=" . $registration_page . " class=\"button subscribe-more\">" 
                    . __( 'Subscribe Today', 'rcp' ) . "</a>" . "\n";
		$content .= "</p>" . "\n";

		return rcp_format_teaser( apply_filters( 'rcp_cl_limit_reached_text', 
                                    $content, $post_type, $registration_page ) );
	}

	return $content;
}