<?php 
/**
 * BP Profilex 
 * @since ver. 1.0.1
 * @param array to get all address from front page
 */

/**
 * Find user id regardless.
 * 
 * @param string $user_id
 * @return int 
 */
function bppx_profilex_get_userid()
{
    $user_id = get_current_user_id();
    if ( !isset( $user_id ) ) { $user_id = bbp_get_user_id( 0, true, false ); }
    if ( $user_id == '' ) { $user_id = '554'; }
    
        return $user_id;
}

/**
 * Obtain geolocation
 */
add_action('bppx_current_geolocation', 'bppx_profilex_obtain_current_geolocation' ); 
function bppx_profilex_obtain_current_geolocation()
{
    $rtrn = '';
    if( !class_exists('geoPlugin') ) { 
    require_once( plugin_dir_url(__FILE__) . 'vendor/geoplugin.class.php' );
    } 
    $geoplugin = new geoPlugin(); 
    $geoplugin->locate(); 
    
        $rtrn = $geoplugin->city; 
        return $rtrn;
}

/**
 * Find user location regardless.
 * 
 * @param string $user_id
 * @return int 
 */
function bppx_profilex_get_location($userid)
{
    global $post;
    $location = $user_city = '';
    $userid   = bppx_profilex_get_userid();
    $statez   = empty(bp_get_profile_field_data('field=State or Province&user_id=' .$userid) ) 
       ? $user_city : bp_get_profile_field_data('field=State or Province&user_id=' .$userid); 
      
    $bppx_location_meta = empty(get_post_meta($post->ID, 'bppx_location_tag', true)) ? $statez  
                              : get_post_meta($post->ID, 'bppx_location_tag', true); 
    $bppx_location      = ( ''!= $bppx_location_meta ) ? $bppx_location_meta : $statez; 
    $location           = ( ''!= $bppx_location ) ? $bppx_location : 'unspecified';
        
        return $location;
}

/**
 * bp_activity_update_meta
 * @see https://buddydev.com/snippets/tag/activity/
 * @param int $activity_id, string
 * @param $meta_key,
 * @param mixed $meta_value,
 * @param mixed $prev_value = ''
 */ 
add_action('bppx_filter_setvisits', 'bppx_filter_setUserVisits' );
function bppx_filter_setUserVisits() 
{
    $result = null;
        $wpdb = '';
    global $wpdb;
    
    if ( ! isset( $_POST['bppx_location_tag_fields'] ) 
            || ! wp_verify_nonce( $_POST['bppx_location_tag_fields'], 'bppx_location_tag_action' ) ) 
        {
            echo '<div class="bppx-message error">'
                    . esc_html__( 'Error... You can only submit a valid form item.', 'bppx-profilex') 
                    . '</div>';
            return false;
        }
    
    else{ 

        if ( isset ( $_POST['bppx_location_tag'] ) ) 
        {
        //add_action( 'wp_ajax_bppx_filter_setvisits', 'bppx_filter_setvisits' );
        //add_action( 'wp_ajax_nopriv_bppx_filter_setvisits', 'bppx_filter_setvisits');

            $location_city = sanitize_text_field($_POST['bppx_location_city']);
            $location      = sanitize_text_field($_POST['bppx_location_tag']);
            $new_activity  = sanitize_text_field($_POST['bppx_new_activity']);
            $member_id     = sanitize_text_field($_POST['bppx_member_id']);
            
            $lcount        = absint(1);
            $visit         = time();

            /*
            $meta_values = array( 'location_value' => $location,
                                'location_count' => absint(1),
                                'visit_time'     => time(),
                                'member_id'      => $member_id,
                                ); */
            $table_name = $wpdb->prefix . 'bppx_location_meta';
        
            $sql = $wpdb->prepare( 
                "INSERT INTO $table_name  
                ( location_key, location_value, location_count, 
                visit_time, member_id, activity_id ) 
                VALUES ( %s, %s, %d, %d, %d, %d )",
                $location_city, $location, $lcount, 
                $visit, $member_id, $new_activity 
                );
            $result = $wpdb->query( $sql );
            

            if( $result === false ) {
                echo '<div class="bppx-message error">'
                    . esc_html__( 'Error... You can only submit a valid form item.', 'bppx-profilex') 
                    . '</div>';
            $result = '';
            $wpdb = null;
            
            } else {
                $result = '';
                $wpdb = null;
                remove_filter( 'nocache_headers', 'bppx_nocache_headers' );
                $url = site_url('/');

                if (!headers_sent())
                {    
                    header('Location: '.$url);
                    exit;
                
                    } else {  
                    add_filter( 'nocache_headers', 'bppx_nocache_headers' ); 
                    echo '<script type="text/javascript">';
                    echo 'window.location.href="'.$url.'";';
                    echo '</script>';
                    exit; 
                }
                    //$new_activity = $userid = $location = $memberid = '';
                    // return false;
                    //do_action('bppx_profilex_success');         
                
            }
        }        

        return false;
    }             
} 
function bppx_nocache_headers() {
                    return array(
                        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                        'Pragma'        => 'no-cache',
                        'Expires'       => gmdate( 'D, d M Y H:i:s \G\M\T', time() )
                    );
                    apply_filters( 'nocache_headers', 'bppx_nocache_headers' ); 
                }
/**
 * Print hidden data in each activity post
 * <form id="bppxLocationForm" method="post" action="" name="bppx_location_form">
 * @since 1.0.1
 * @see https://codex.buddypress.org/plugindev/post-types-activities/
 * maybe use bp_is_anonymous_activity()
 */
add_action( 'bp_activity_entry_content', 'bppx_profilex_activity_loop_counters_front' );
function bppx_profilex_activity_loop_counters_front()
{   
    
    if ( !function_exists('bppx_filter_setUserVisits') ) {
        return false;
    } 

    ob_start(); 
    $userid = '';

    if (is_user_logged_in()) {
        $userid   = bppx_profilex_get_userid();
        $location = bppx_profilex_get_location($userid);

        } else {
        $location = bppx_profilex_obtain_current_geolocation();
        $location = (empty($location)) ? 'unspecified' : sanitize_text_field($location);
        
    }

    $new_activity = bp_get_activity_id();    
    $memberid     = bp_get_activity_user_id();
    $unique       = time() . '-' . rand(10000, 99999);

    echo '<div class="bppx-invisible">
    <form class="bppxLocationForm" method="post" action="" name="bppx_location_form">
    <ul class="bppxLocation_' . $new_activity . '"></dt>
        <li>
        <input id="bppx_location_tag_' . $new_activity . '" 
           class="bppx-location" 
           name="bppx_location_tag" 
           type="hidden" 
           value="' . $location . '-' . $unique . '_' . $memberid . '">
        <input type="hidden" name="bppx_location_city" value="' . $location . '">
        <input type="hidden" name="bppx_member_id" value="' . $memberid . '">   
        <input type="hidden" name="bppx_new_activity" value="' . $new_activity . '">';
           
    
    echo '<input type="hidden" name="action" value="bppx_location_form">';	
    echo wp_nonce_field( 'bppx_location_tag_action', 'bppx_location_tag_fields' );
    echo '<input class="bppx-submit-button" id="bppxLocationSubmit" type="submit" 
                  name="bppxLocationSubmit" value="Fan Vote">
        </li>
    </ul>
    </form>
    </div><div class="bppx-clearfix"></div>';

    if( isset($_POST['bppxLocationSubmit'] )) { 
        do_action('bppx_filter_setvisits'); 
        //return false;
    }
    

        // clean content
        $html = ob_get_clean();
        
            echo $html;
} 

/**
 * do_action('bppx_profilex_success')
 */
add_action('bppx_profilex_success', 'bppx_profilex_success_messages' );
function bppx_profilex_success_messages()
{

    echo '<div id="bppxMessage" class="msg-disabled">
    <p class="bppx-message-thanks">' . esc_html__('Thanks, Hope to see you soon', 'buddypress') . '</p>
    </div><script id="bppx-autorefresh-once">javascript:document.location.reload();return false;</script>';
}



// Using user_id 554 as "unknown" visitor.
function bppx_profilex_get_userid_from_activityid($activity_id)
{
global $wpdb;

    $activity_id = ( !$activity_id ) ? '173' : $activity_id;
    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}bp_activity 
                                    WHERE 'id' = {$activity_id}
                                    LIMIT 1 " );
    
        return $result['user_id'];
}

/**
 * Strip all but digits from avatar to get id
 *
 */
function bppx_profilex_strip_alpha_leave_beta($res)
{

$res = preg_replace("/[^0-9]/", "", "' . $string . '" );
}
