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
    $postid  = '';
    $pviews  = empty( bppx_getPostViews($user_id) ) ? '1' 
             : bppx_getPostViews($user_id);
    $qviews  = empty( bppx_getProfileViews($user_id) ) ? '1' 
             : bppx_getProfileViews($user_id);
    $updates_count  = bp_profilex_get_total_user_updates_count( $user_id, 'updated_profile' );
    $comments_count = bp_profilex_get_total_user_comments_count($user_id, 'activity_comment');
    $activity_count = bp_profilex_get_total_activity_activities_count($user_id, 'activity_status' );
    $member_name    = empty(bp_get_profile_field_data('field=Name') ) ? 'Member' 
                    : bp_get_profile_field_data('field=Name');
    
    // clean buffer output
    ob_start();
    
    echo '<div class="bppx-container">';
    
    echo '<header class="bppx-header">' . bp_profilex_profile_tab_content_start() . '</header>';
    echo '<div class="bppx-fields">
        <table class="bppx-overview-table" style="margin-bottom: 0"><thead>
        <tr><th>Active posts</th><th>For</th><th>Profile ID</th><th>Profile views</th><th>Actions</th></tr></thead>
        <tbody>';
    echo '<tr><td>' . absint($activity_count) . '* <small style="color:#8aa;">(' . $comments_count . ' comments)</small></td>
            <td>' . esc_html($member_name) . '</td>
            <td>' . absint($user_id) . '</td>
            <td>' . absint($qviews) . '</td>
            <td>' . esc_html($updates_count) . '**</td></tr>';
    echo '</tbody></table><small style="color:#8aa;margin-right:1em;">*How many active post you wrote in the stream.</small> <small style="color:#8aa">**How many times you updated activity of your profile.</small>';
    echo '</div>
            <div class="bppx-clearfix"></div>';

    echo '<div class="bppx-container">';

    echo '<header class="bppx-header"><h3>' . esc_html( 'Location Information' ) . '</h3></header>';
    echo '<div class="bppx-fields">
         <table class="bppx-overview-table second-table"><thead>
        <tr><th>Location</th><th>Age</th><th>Requested</th><th>Stream</th></tr></thead>
        <tbody>';
      
    /* need to check if array of activity_ids belong to logged in user id
    * then only fetch meta_value of those locations matching
    */
    $location = bppx_profilex_get_location_data($user_id);
    
    
    if( $location ) {
    
        foreach ( $location as $row ) {
    $args           = array( 'field' => 'Age', 
                             'user_id' => $row->member_id 
                        );
    $ages           = empty(bp_get_profile_field_data($args) ) 
                    ? 'unspecified' : bp_get_profile_field_data($args);
    $visit_time     = intval($row->visit_time);
    $activity_link  = bp_activity_get_permalink(intval($row->activity_id));
    $location_count = ( empty( $ages ) ) ? 'unspecified' : $ages;
        //if ( $memberid == $user_id ) 
        echo '<tr>
                <td>' . $row->location_key . '</td>
                <td>' . esc_html( $location_count ) . '</td>
                <td>' . esc_attr( date( 'm/d/Y H:m', $visit_time )) . '</td>
                <td><a href="' . esc_url($activity_link)  . '" title="visit activity">' 
                . esc_html($row->activity_id) . '</a></td>
            </tr>';
        }//ends foreach
    } else {
        echo '<tr>
                <td>' . esc_html__( 'No visits yet', 'bppx-profilex' ) . '</td>
              </tr>';
        }
    echo '</tbody></table><small style="color:#8aa;margin-right:1em;">*How many active post you wrote in the stream.</small> <small style="color:#8aa">**How many times you updated activity of your profile.</small>';
    echo '</div>
            <div class="bppx-clearfix"></div>';

echo    '</div>';

    $htm = ob_get_clean();
    echo $htm;
} 
/**
 * Get meta value from bp_activity_meta table.
 *
 * @return string | objects
 */
function bppx_profilex_get_location_data($member_id)
{
    global $wpdb;
    $member_id = (empty($member_id)) ? '544' : $member_id;
    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}bppx_location_meta 
                                    WHERE member_id = $member_id 
                                    ORDER BY activity_id DESC" );
            
        return $results;
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
