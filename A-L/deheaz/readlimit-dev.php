<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Restrict access to all posts, but grant access to users in subscription level ID 2.
 *
 * @param bool $can_access
 * @param int $member_id
 * @param int $post_id
 * @param RCP_Member $member
 */
function readlimit_rcp_member_can_access( $can_access, $member_id, $post_id, $member ) {
 
	// Change if this is a `!post`.
	if ( 'post' == get_post_type( $post_id ) ) {
 
		// Grant access if they're on sub level #2 and their account isn't expired.
		if ( 1 == $member->get_subscription_id() && ! $member->is_expired() ) {
			$can_access = true;
		} else {
			$can_access = false;
		}
 
		// Always grant access to admins.
		if ( user_can( $member_id, 'manage_options' ) ) {
			$can_access = true;
		}
 
	}
 
	return $can_access;
 
}
 
add_filter( 'rcp_member_can_access', 'readlimit_rcp_member_can_access', 10, 4 );
 
function user_last_login( $user_login, $user ) {
    update_user_meta( $user->ID, 'last_login', time() );
}
add_action( 'wp_login', 'user_last_login', 10, 2 );
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

    
    // User Loop
    ?>
    <table class="widefat fixed" cellspacing="0">
    <thead>
    <tr>
        <th>display name</th>
        <th>date user registered</th>
        <th>date last logged in</th><th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ( ! empty( $user_query->get_results() ) ) { 
    $last_login = ( $user->last_login == '' ) ? 'never logged in' 
                        : date( 'Y-m-d H:i:s', $user->last_login );
        // user->last_login is in epoch time
        foreach ( $user_query->get_results() as $user ) {

        
        echo '<tr>';
        echo '<td>' . $user->display_name . '</td>
              <td>' . $user->user_registered . '<td>
              <td>' . $last_login . '</td>';
        echo '</tr>';
        }
    
    } else { 

        echo '<tr><td colspan="3">No users found.</td></tr>';
    
    } ?> 
    
    </tbody></table>
    
    <?php 
    
        $user_query = null;
}
//add_action('wp_login', 'readlimit_access_check_subscriber_status', 0, 2);
/**
 * Start session timer for reading
 * @param string $readlimit_login_time     Last login when read 
 * @param int    $readlimit_user_id        user_id
 * @param int    $readlimit_article_read   post_id
 
 */
function readlimit_access_session_handler()
{

return false;
} 

/**
 * Determine time limits to read and apply if conditions met
 *
 * @param int readlimit_readlimits        Epoch time limits [plugin option]
 * @uses readlimit_access_session_handler login time plus readlimit_readlimits.
 */
function readlimit_access_check_timelimit()
{

$ageunix = get_the_time('U');

$days_old_in_seconds = ((time() - $ageunix));

$days_old = (($days_old_in_seconds/86400));

if ($days_old > 365) { 


return 'yes';;
}
}

/**
 * If timer met or ended, change user_access to 0 for this post_id
 * 
 */
function readlimit_access_user_access_updated()
{

return false;

} 

/**
 * Send report to admin
 */
function readlimit_access_admin_display_log()
{
/*
$query = get_users(
'&offset='.$offset.'&orderby=meta_value&meta_key=user_last_login&order=DESC&number='.$number); 
*/
return false;

}
