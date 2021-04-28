<?php 
/**
 * BP Profilex 
 * @since ver. 1.0.1
 * @param array to get all address from front page
 */
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
    global $bp;
    $activity_id = $userid = $location = $memberid ='';
    //$memberid = array();
    //if ( !function_exists('bppx_filter_setUserVisits') ) {return false;} 
    
    ob_start(); 
    if (!is_user_logged_in()) {

        $location = sanitize_text_field(bppx_profilex_obtain_current_geolocation());
        $location = (empty($location)) ? 'unspecified' : $location;
        } else {
        
            $userid = bppx_profilex_get_userid();
            $location = bppx_profilex_get_location($userid);
    }

    if( isset($_POST['bppxLocationSubmit'] )) do_action('bppx_filter_setvisits');
    
    $activity_id = bp_get_activity_id();    
    $memberid    = bp_get_activity_user_id();
    $unique      = time();

    echo '<div class="bppx-clearfix"></div>';

    echo '<div class="bppx-invisible">
    <ul class="bppxLocation_' . $activity_id . '"></dt>
        <li><form class="bppxLocationForm" method="post" action="" name="bppx_location_form">
        <input id="bppx_location_tag_' . $activity_id . '" 
           class="bppx-location" 
           name="bppx_location_tag" 
           type="hidden" 
           value="' . $location . '-' . $unique . '_' . $memberid . '">
           <input type="hidden" name="bppx_new_activity" value="' . $activity_id . '">
           <input type="hidden" name="bppx_member_id" value="' . $memberid . '">'
           . wp_nonce_field( 'bppx_location_tag_action', 'bppx_location_tag_fields' );	
    echo '<input class="bppx-submit-button" id="bppxLocationSubmit" type="submit" 
                  name="bppxLocationSubmit" value="Fan Vote"></form>
        </li>
    </ul>
           
           <p>' . $activity_id . ' ' . $memberid . '</p>
    </div>';

        // clean content
        $html = ob_get_clean();
        
            echo $html;
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
//remove_action( 'wp_ajax_bppx_filter_setvisits', 'bppx_filter_setvisits' );
//remove_action( 'wp_ajax_nopriv_bppx_filter_setvisits', 'bppx_filter_setvisits');
    //global $wpdb;
    $new_activity = $prev_value ='';
    if ( ! isset( $_POST['bppx_location_tag_fields'] ) 
            || ! wp_verify_nonce( $_POST['bppx_location_tag_fields'], 'bppx_location_tag_action' ) ) 
        {
            print 'Sorry, your can only submit a valid form item.';
            exit;
        }
    else{ 
if ( isset ( $_POST['bppx_location_tag'] ) ) {
//add_action( 'wp_ajax_bppx_filter_setvisits', 'bppx_filter_setvisits' );
//add_action( 'wp_ajax_nopriv_bppx_filter_setvisits', 'bppx_filter_setvisits');

        // actual post/activity ID
        $location     = $_POST['bppx_location_tag'];
        $new_activity = $_POST['bppx_new_activity'];
        $prev_value   = bp_activity_get_meta( $new_activity, 'bppx_visitor_logged' );
        // ? (int) $prev_value + 1 : 
        //$appending  = if($unserialized == $match) count($matched);
        bp_activity_update_meta( $new_activity, 'bppx_visitor_logged', $location );
                //header("Refresh: 2");
}        
        
        //return false;
    }             
} 

/**
 * do_action('bppx_profilex_success')
 */
add_action('bppx_profilex_success', 'bppx_profilex_success_messages' );
function bppx_profilex_success_messages()
{
    ob_start(); 

    echo '<div id="bppxMessage" class="msg-disabled">
    <p class="bppx-message-thanks">' . esc_html__('Thanks, Hope to see you soon', 'buddypress') . '</p>
    </div>';

    $htm = ob_get_clean();
    echo $htm;
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
