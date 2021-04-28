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

    echo '<div class="bppx-container">';

    echo '<header class="bppx-header"><h3>' . esc_html( 'Location Information' ) . '</h3></header>';
    echo '<div class="bppx-fields">
         <table class="bppx-overview-table second-table"><thead>
        <tr><th>Location</th><th>Requests</th></tr></thead>
        <tbody>';
        
    /* need to check if array of activity_ids belong to logged in user id
    * then only fetch meta_value of those locations matching
    */
    $location = bppx_profilex_get_location_data();
    if( $location ) {
    foreach ( $location as $row ) {
    $matches = array();
    if (preg_match('#(\d+)$#', $row->meta_value, $matches)) { $memberid = $matches[1]; }
        else { $memberid = '554'; }
    if ( $memberid == $user_id ) 
    echo '<tr>
            <td>' . $row->meta_value . '</td>
            <td>' . $memberid . '</td>
        </tr>';
    }//ends foreach
     } else {
        echo '<tr>
                <td>' . esc_html__( 'No visits yet', 'bppx-profilex' ) . '</td>
              </tr>';
        }
    echo '</tbody></table><small style="color:#8aa;margin-right:1em;">*How many active post you wrote in the stream.</small> <small style="color:#8aa">**How many times you updated activity of your profile.</small>';
    echo '</div>
            <div class="bppx-clearfix"></div>
            <hr>';


    echo '<header class="bppx-header">' . bp_profilex_profile_tab_content_end() . '</header>';
    echo '<div class="bppx-fields">
        <table class="bppx-overview-table second-table"><thead>
        <tr><th>Location</th><th>Age</th><th>Name</th><th>Profile</th><th>visitor_logged</th></tr></thead>
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
            //$author_id = ( empty( get_user_meta( $user->ID, 'user_id',true ) ) ) ? '' : '';
            $user_city = ( empty( $user->billing_city )) ? 'unspecified' : $user->billing_city;

            $statez    = empty( bp_get_profile_field_data('field=State or Province&user_id=' .$user->ID) ) 
                 ? $user_city : bp_get_profile_field_data('field=State or Province&user_id=' .$user->ID);
            $user_age  = empty( bp_get_profile_field_data('field=Age&user_id=' . $user->ID) ) 
                        ? '?' : bp_get_profile_field_data('field=Age&user_id=' . $user->ID);        
            $value     = get_user_meta($user->ID, 'user_visitor_logged', true);
            echo '<tr>
                    <td>' . esc_html($statez) . '</td>
                    <td>' . esc_html($user_age) . '</td>
                    <td>' . esc_html($user->nickname) . '</td>
                    <td>' . absint($user->ID) . '</td>
                    <td>' . $value .'</td>
                    </tr>';
        } // ends foreach
    } else {
        echo '<tr>
                <td colspan=5>' . esc_html__( 'No visits yet', 'bppx-profilex' ) . '</td>
              </tr>';
        }
    echo '</tbody></table>';
    echo '</div>';
echo    '</div>';

    $htm = ob_get_clean();
    echo $htm;
} 
/**
 * Get meta value from bp_activity_meta table.
 *
 * @return string | objects
 */
function bppx_profilex_get_location_data()
{
    global $wpdb;
    
    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}bp_activity_meta 
                                    WHERE meta_key = 'bppx_visitor_logged'" );
            
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
