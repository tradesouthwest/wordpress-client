<?php 
/**
 * Custom nav menu adding to profile tab.
 * @param array   settings.
 */ 
function bppx_profilex_setup_overview_navigation() 
{
   bp_core_new_subnav_item( array(
	'name'                    => 'Analytics Overview',
	'slug'                    => 'overview',
	'parent_url'              => bp_loggedin_user_domain() . 'settings/',
	'parent_slug'             => 'profile',
	'screen_function'         => 'bp_profilex_profile_tab_screen',
	'show_for_displayed_user' => false
   ) );
}
add_action( 'bp_setup_nav',   'bppx_profilex_setup_overview_navigation' );

/** 
 * Add title and content then call the members plugin.php template.
 *
 */
function bp_profilex_profile_tab_screen() 
{
    if ( is_user_logged_in() && bp_is_my_profile() ) : 

    add_action( 'bp_template_title', 'bp_profilex_profile_tab_title' );
    add_action( 'bp_template_content', 'bp_profilex_profile_tab_content' );
    //add_action( 'bp_profilex_profile_tab_start', 'bp_profilex_profile_tab_content_start');
    bp_core_load_template( 'members/single' );
    
    endif;
}

// header to profile tab page
function bp_profilex_profile_tab_title() {
    echo '<div class="options-section-title">
    <h2><i class="fas fa-user"></i> ' . __('Analytics', 'buddypress' ) . '</h2>
    </div>';
}

function bp_profilex_profile_tab_content_start(){

    echo '<h3 style=""color:#8aa;margin-bottom:.444em">' . __('Insights of Activities', 'bppx-profilex' ) . '</h3>';
}

function bp_profilex_profile_tab_content_end(){

    echo '<h3 style=""color:#8aa;margin-bottom:.444em">' . __('Engagements', 'bppx-profilex' ) . '</h3>';
}

/** 
 * Count the views when someone refreshes or views page.
 * Remove comment from 'issues with prefetching' to kill refresh counts.
*/
// Remove issues with prefetching adding extra views
//remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0); 

/**
 * **************
 * Getters
 * **************
 */
function bppx_getPostViews($postID)
{
    $count_key = 'post_views_count';
    $count     = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "";
    }
    return $count. __( ' Views', 'buddypress' );
}
function bppx_getProfileViews($postID)
{
    $count_key = 'profile_views_count';
    $count     = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "";
    }
    return $count. __( ' Views', 'buddypress' );
}
function bppx_getUserVisits($postID)
{
    $count_key = 'user_visited_pages';
    $count     = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "";
    }
    return $count;
}
/**
 * **************
 * Setters
 * **************
 */
// maybe inc single-item groups
function bppx_setPostViews($postID) 
{
    $count_key = 'post_views_count';
    $count     = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
} 

function bppx_setProfileViews($postID) 
{
    $count_key = 'profile_views_count';
    $count     = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

// Using user_id 554 as "unknown" visitor.

function bppx_filter_setUserVisits($post) 
{
    global $post;
    
    if ( !isset( $user_id ) ) {
       $user_id = '554';
    }else{
        $user_id = get_current_user_id();
    }
    $user_visit = get_user_meta($user_id, 'user_visitor_logged', true);
    $meta_key   = 'user_visitor_logged';
    $meta_value = $user_id; 
    //$prev_value = get_user_meta( $user_id, $meta_key, true );
    if( $meta_key !='' ) : 
        update_post_meta( $beta_value, $meta_key, $meta_value );
        endif;
    
} 
function bppx_set_userdata_logged()
{

return true;
}
/**
 * Print hidden data in each activity post
 *
 * @since 1.0.1
 * @see https://codex.buddypress.org/plugindev/post-types-activities/
 * maybe use bp_is_anonymous_activity()
 */
add_action( 'bp_activity_entry_content', 'bppx_profilex_activity_loop_counters_front' );
function bppx_profilex_activity_loop_counters_front()
{   
    if ( !function_exists('bppx_set_userdata_logged') ) { return false; }
    $activity_id = $userid = $location = '';
    $activity_id = bp_get_activity_id();    
    $userid      = bppx_profilex_get_userid();
    $location    = bppx_profilex_get_location($userid);
     
    // <!-- UserVisits Marker Set -->
    print '<div class="bppx-invisible"><div class="bppxLocation_' . $activity_id . '">
    <input id="bppx_location_tag_' . $activity_id . '" 
           class="bppx-location" 
           name="bppx_location_tag" type="text" 
           data-location="' . $location . '"  
           value="' . $activity_id . '_' . $userid . '_' . $location . '" 
           readonly style="padding:0;border:none;">
    </div></div>';
        // clean content
        $activity_id = $userid = $location = null;    
} 


/**
 * Custom fields adding to profile tab.
 * @param string|int $bppx_age     Age of user 
 * @param string|int $bppx_nclicks how many profile clicks,
 * @param array                    what posts were clicked on,
 * @param string|int $bppx_nposts  how many clicks per post
 * @param array                    location of follower.
 */ 
function bp_profilex_profile_tab_content($user_id) 
{ 
    global $bp, $post;
    if ( ! bp_is_user_profile() ) {
        return;
    }
    $user_id = bppx_profilex_get_userid();
    /*if ( isset( $user_id ) ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }
    } */
    $postid  = '';
    //if($user_id == ''){ $user_id = bbp_get_user_id( 0, true, false ); }
    $vistor  = absint($user_id);     
    $ages    = empty(bp_get_profile_field_data('field=Age') ) ? '21' 
             : bp_get_profile_field_data('field=Age');
    $states  = empty(bp_get_profile_field_data('field=State or Province') ) ? 'Unkown' 
             : bp_get_profile_field_data('field=State or Province');
    $names   = empty(bp_get_profile_field_data('field=Name') ) ? 'Member' 
             : bp_get_profile_field_data('field=Name');
    $read_id = 'Title of Action or Id Link';
    
    $pviews  = empty( bppx_getPostViews($user_id) ) ? '1' 
             : bppx_getPostViews($user_id);
    $qviews  = empty( bppx_getProfileViews($user_id) ) ? '1' 
             : bppx_getProfileViews($user_id);
    
    // types = 'activity_status', 'updated_profile', 'activity_comment', ('last_activity')
    $updates_count  = bp_profilex_get_total_user_updates_count( $user_id, 'updated_profile' );
    $comments_count = bp_profilex_get_total_user_comments_count($user_id, 'activity_comment');
    $activity_count = bp_profilex_get_total_activity_activities_count($user_id, 'activity_status' );
    
    // clean buffer output
    ob_start();
    
    echo '<div class="bppx-container">';
    
    echo '<header class="bppx-header">' . bp_profilex_profile_tab_content_start() . '</header>';
    echo '<div class="bppx-fields">
        <table class="bppx-overview-table" style="margin-bottom: 0"><thead>
        <tr><th>Active posts</th><th>Name</th><th>Profile ID</th><th>Profile views</th><th>Actions</th></tr></thead>
        <tbody>';
    echo '<tr><td>' . absint($activity_count) . '* <small style="color:#8aa;">(' . $comments_count . ' comments)</small></td>
            <td>' . esc_html($names) . '</td>
            <td>' . $user_id . '</td>
            <td>' . absint($qviews) . '</td>
            <td>' . esc_html($updates_count) . '**</td></tr>';
    echo '</tbody></table><small style="color:#8aa;margin-right:1em;">*How many active post you wrote in the stream.</small> <small style="color:#8aa">**How many times you updated activity of your profile.</small>';
    echo '</div>
            <div class="bppx-clearfix"></div>
            <hr>';

    echo '<header class="bppx-header">' . bp_profilex_profile_tab_content_end() . '</header>';
    echo '<div class="bppx-fields">
        <table class="bppx-overview-table second-table"><thead>
        <tr><th>Location</th><th>Age</th><th>Name</th><th>Profile</th><th></th></tr></thead>
        <tbody>';
// TODO orderby location
$uargs = array(
    'meta_query' => array(
        array(
            'key' => 'user_visitor_logged',
            'value' => '1',
            'compare' => '>='
        )
    )
);

$member_arr = get_users($uargs);
if ($member_arr) {
  foreach ($member_arr as $user) {
    $visited_activity = '';
    $user_city = ( empty( $user->billing_city )) ? 'unspecified' : $user->billing_city;

    $statez    = empty( bp_get_profile_field_data('field=State or Province&user_id=' .$user->ID) ) 
         ? $user_city : bp_get_profile_field_data('field=State or Province&user_id=' .$user->ID);
    /* $locatez   = empty( bppx_profilex_get_location($user->ID) ) ? $statez 
                      : bppx_profilex_get_location($user->ID); */
    $user_age  = empty( bp_get_profile_field_data('field=Age&user_id=' . $user->ID) ) 
                ? '?' : bp_get_profile_field_data('field=Age&user_id=' . $user->ID);        
    echo '<tr>
            <td>' . esc_html($statez) . '</td>
            <td>' . esc_html($user_age) . '</td>
            <td>' . esc_html($user->nickname) . '</td>
            <td>' . absint($user->ID) . '</td>
            <td>'. $visited_activity.'</td>
            </tr>';

    }
} else {
  echo '<tr>
            <td colspan=5>no users found</td></tr>';
}
    echo '</tbody></table>';
    echo '</div>';
echo    '</div>';

    $htm = ob_get_clean();
    echo $htm;
} 
