<?php 
/**
 * @subpackage Views Addon/inc
 */
// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) 	exit;
// #a1
add_action( 'views_addon_send_request', 'views_addon_send_request_formid' );
// #a2
//add_action( 'wpbdp_contact_form_extra_fields', 'views_addon_add_custom_notation_field' );
// #a3
add_action( 'post_submitbox_misc_actions', 'views_addon_submitbox_assign_judge_dropdown');
// #a4
add_action('updated_post_meta', 'views_addon_process_updated_assigns', 10, 3 );
/* =========== Filter Hooks =========== */
// #f1
add_filter( 'wpbdp_contact_form_output', 'views_addon_extend_emailaddress_contact_form', 10, 2 );
// #f2
//add_filter( 'wpbdp_contact_form_validation_errors', 'views_addon_validate_custom_notation_field' );
apply_filters( 'wpbdp_listing_link', 'views_addon_add_excerpt_judge' );
function views_addon_add_excerpt_judge(){
global $post;
$find_judge = ( empty ( views_addon_find_judge_assigned($post->ID) )) ? 'not found' 
                : views_addon_find_judge_assigned($post->ID);

return '<p>' . esc_attr($find_judge) . '<p>';
}
/**
 * Add custom link to WPBDP Settings tab
 * @since 1.0.2
 */
add_action( 'wpbdp_settings_subtab_general/main', 'views_addon_wpbd_add_content', 9 );
function views_addon_wpbd_add_content() {
    echo '<br><a class="cursor-friendly" href="https://internationaltheatercompetition.com/wp-admin/options-general.php?page=views-addon" 
    title="Find Views Addon TSW Settings Here">Find <span>Views Addon TSW</span> Settings Here</a>';
}
add_action( 'wpbdp_settings_subtab_general/main', 'views_addon_wpbd_add_content_statistics', 10 );

function views_addon_wpbd_add_content_statistics(){

    if ( ! is_admin() ) return;
    $current_judge = '';
    $wp_query = '';
    echo '<div class="wpbdp-setting-row">
    <div class="wpbdp-setting-content">
    <div id="viewsaddon-table">
    <table class="viewsaddon-table">
    <thead><tr>
    <th>Judge</th>
    <th>title</th>
    <th>id</th>
    </tr></thead>
    <tbody>';
    //metadata of posts    
    global $wp_query;    
    global $paged;
    $paged = ( get_query_var('page') ) ? get_query_var('page') : 1;
    $wp_query = new WP_Query( array( 
                    'post_type'      => 'wpbdp_listing',
                    'posts_per_page' => 35,
                    'paged'          => $paged                  
                    ) );
    if ( $wp_query->have_posts() ) :
        while ( $wp_query->have_posts() ) : $wp_query->the_post(); 
            $judges_field  = '_wpbdp[fields][23]'; 
            $current_judge = get_post_custom( get_the_ID() );
    echo '<tr>
    <td>' . esc_attr( $current_judge["$judges_field"][0] ) .'</td>
    <td>' . get_the_title() . '</td>
    <td class="aligncenter">' . get_the_ID() . '</td>
    </tr>';
    endwhile; 
    endif;
    echo '<tr>
    <td></td>
    <td></td>
    <td><a href="#viewsaddon-table" title="top">top</a></td>
    </tr>';
    echo '</tbody>
    </table></div></div>
    <div id="onlist-pagination" class="pagination">
        <nav>';
    /**
    * get_prev/next_posts_link ($label, $max_page)
    * https://docs.pluginize.com/article/81-cleanly-done-pagination-with-custom-wpquery-objects
    */
    $prevpost = get_previous_posts_link( 'Newer Listings', $wp_query->max_num_pages );
    $nextpost = get_next_posts_link( 'Older Listings', $wp_query->max_num_pages );
    echo '<div class="alignleft">' . $prevpost . '</div>';
    echo '<div class="alignright">' . $nextpost .'</div>';
    echo ' 
        </nav>
    </div> 
    </div>';
/** @phpstan-ignore-next-line */
    wp_reset_postdata();
    wp_reset_query();
	$wp_query = null;
}
/**
 * shortcode `[request_link]` Gets params from emailed action link.
 * @since 1.0.0
 * @return HTML
 */

function views_addon_shortcode_linkout($atts='', $content=null){

    $getformid = (!isset ( $_GET['form_id'] ) ) ? 1 : absint($_GET['form_id']);
    $getjudge  = (!isset ( $_GET['viewsaddon_name'] ) ) ? 'not valid - use email' 
                : sanitize_text_field($_GET['viewsaddon_name']);
    $getemail  = (!isset ( $_GET['viewsaddon_email'] ) ) ? 1 
                : sanitize_email($_GET['viewsaddon_email']);
    $getlink   = (!isset ( $_GET['viewsaddon_playlink'] ) ) ? 1 
                : sanitize_text_field( $_GET['viewsaddon_playlink'] );
    $linkout   = (empty( get_option('views_addon_options')['views_addon_linkout'])) 
                  ? '' : get_option('views_addon_options')['views_addon_linkout'];
    $href_url  = 'https://internationaltheatercompetition.com/' . sanitize_text_field($linkout);
    //$logredir  = wp_login_url( site_url( add_query_arg( array(), $wp->request ) ) );
    /* atts become stringafied *//*
    extract( shortcode_atts( array(
                "href" => $href_url
                ), 
                $atts 
                )
            ); */

    $output = '<div class="viewsaddon-container">';
    $output .= '<div class="form-message">';

    if ( isset ( $_POST['viewsaddon_submit'] ) ) {
    
            do_action( 'views_addon_send_request' );    
    }
	
    $output .= '</div>';
    $output .= '<div class="viewsaddon-container">';
    $output .= '<form name="viewsaddon_mailto" method="post" action="">
	<h5>' . esc_html__( 'Authorize Judge to this Play', 'views-addon' ) . '</h5>
        <fieldset>
        <label for="viewsaddon_name">' . esc_html( 'Judge Full Name', 'views-addon' ) . '</label>
        <input type="text" name="viewsaddon_name" value="' . esc_attr($getjudge) . '" class="text-field">
        </fieldset>
        <fieldset>
        <label for="viewsaddon_email">' . esc_html( 'Email to Send To', 'views-addon' ) . '</label>
        <input type="email" name="viewsaddon_email" value="' . esc_attr($getemail) . '" class="text-field">
        </fieldset>
        <fieldset>
        <label for="viewsaddon_editlink">' . esc_html( 'Additional Information', 'views-addon' ) . '</label>
        <input type="text" readonly name="viewsaddon_editlink" value="'. esc_attr($getlink) .'" class="text-field">
        </fieldset>
        <fieldset>
        <input type="hidden" name="viewsaddon_playlink" value="'. absint($getformid) .'">
        </fieldset>
        <fieldset>
        ' . wp_nonce_field( 'views_addon_validurl' ) . '
        <input type="submit" name="viewsaddon_submit" class="button submit"
            value="' . __('Authorize Play Invite', 'views-addon') . '">
        </fieldset>

        </form>';
    $output .= '</div>
        </div>';
    $output .= '<div class="viewsaddon-container">';
    if ( ! is_user_logged_in() ) { 
    $output .= '<h5>Login required</h5><p>If you are not logged in as per capability role, 
            please login redirected back to this page but will need to open page from email again.</p>
            </div>';
    }
        ob_start();
        echo $output;
        return ob_get_clean();
}


/**
 * Determine which posts are not not_assigned
 *
 * @since 1.0.3
 */
//add_action( 'pre_get_posts', 'views_addon_excluding_assigned');
//add_action( 'views_addon_excluding_assigned', 'views_addon_exclude_assigned_listings_archive');
function views_addon_exclude_assigned_listings_archive(){
    global $post;
    if ( get_post_type( $post->ID ) != 'wpbdp_listing' ) return;
    $custom_field        = '_wpbdp[fields][23]'; 
    $assigned_judge_email = get_post_meta( $post->ID, $custom_field, true ); 
    if ( 'not_assigned' == $assigned_judge_email ){
        $assigned = 'block';
    } else {
        $assigned = 'none';
    }
	return esc_attr($assigned);
}

/**
 * Ajax call to change post status via email link to button
 * @see https://stackoverflow.com/questions/53123800/wordpress-update-custom-post-status-by-clicking-custom-link-in-admin
 * @feedback May not be capable of emailing password!
 */
add_action('wp_footer', 'views_addon_post_updated_message');
function views_addon_post_updated_message(){
    global $post;
    if ( 'wpbdp_listing' !== $post->post_type ) {
		return;
	}

    echo '<style id="views-addon-inliner">
    .archive .wpbdp-listing.excerpt .wpbdp-field-assigned_judge .value{
position: relative; top: 0; font-size: smaller;}
.archive .wpbdp-listing.excerpt .wpbdp-field-assigned_judge:before{
content:"Assigned Judge "; font-weight:bold; color: gold;}</style>';

}

/** TODO
 * be sure that add_action is called before the action is triggered
 * object(WP_Post)->post_password to change cat to assigned-judge
 */

/** @id=a1
 * Action hook to send form back to admin to filter post updates
 * 
 * @return boolean or redirect url if admin
 */
function views_addon_send_request_formid(){
    if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'views_addon_validurl' ) ) {
    wp_die( 'Are you an admin?' );
    } else {

    $user          = wp_get_current_user();
    $allowed_roles = array('editor', 'administrator', 'author');
    if( array_intersect($allowed_roles, $user->roles ) ){

        //$getformid = views_addon_get_request_parameter($_POST['form_id']);
        $editlink  = (!isset ($_POST['viewsaddon_editlink'] ) ) ? 1 
                    : absint($_POST['viewsaddon_editlink']);
        $edit_url  = esc_url( admin_url() . "post.php?post=$editlink&action=edit" );
        
        echo '<div class="viewsaddon-container">
        <h4>Validation Complete! Please Authorize by Clicking on Link</h4>
        <p>a.) Visit the listing via link below -step d.- and when you get there:</p>
        <p>b.) Scroll to <strong>Publish</strong> then set <strong>Assign Judge</strong> to protect listing.</p>
        <p>c.) After assigning be sure to <strong>Update</strong> to send link to judgeink to Judge.</p>';
        printf( '<p>d.) <a href="%s" title="$s">authorize: %s</a></p>',
        esc_url($edit_url),
        esc_attr($editlink),
        esc_url($editlink)
        );
        echo '</div>';
        }
    }
        return false;

}

/** @id=f1
 * @uses FileRequestITC id from cf7 form 22847
 * @param FileRequestITC  Element Loads $listing_id.
 * @param EmailRequestITC Element Autoloads formfield email of user.
 *
 * @since 1.1.0(1)
 */

function views_addon_extend_emailaddress_contact_form($form, $atts=''){

    $listing_id = $atts['listing_id'];
    $text    = (empty( get_option('views_addon_options')['views_addon_heading_h2'])) 
                 ? '' : get_option('views_addon_options')['views_addon_heading_h2'];
    $attc    = ( false == views_addon_check_logged_in() ) ? 'none' : 'block';
    $valc    = 'block';
    $output  = '<div class="itc-filerequest">'; 
    $output .= '<h4>' . esc_html($text) .'</h4>';
    $output .= $form;
    $output .= '</div>'; //debug $listing_id;

	    return $output;

}


/** id=a3
 * Fires after the post time/date setting in the Publish meta box.
 * @param string $judges_field Option from _wpbdp[fields][23] as metadata
 * @uses `_action( 'post_submitbox_misc_actions', WP_Post $post )` 
 * @uses select id="authors" name="post_author" wp_dropdown_users list
 */
function views_addon_submitbox_assign_judge_dropdown(){  
    global $post;
    /**
     * return if not on admin page or
     * post type to compare is null
     */
    if ( ! is_admin() || $post->post_type != 'wpbdp_listing' ) {
        return;
    }
    // field is email of judge assigned to this listing
    $judges_field  = '_wpbdp[fields][23]'; 
    $current_judge = get_post_custom( $post->ID );

    echo '<div class="misc-pub-section misc-assignjudge" id="assignjudge">';
    echo '<p><strong>' . esc_html( 'Assign Judge', 'views-addon') .'</strong></p>';

        /* Create user list outside of post to avoid infinite loop */
        if( function_exists('views_addon_dropdown_select_assign_judges')) {
            do_action('views_addon_dropdown_judged');
        }
	echo 'Currently assigned: ' . $current_judge["$judges_field"][0];
    echo '</div>';
} 

/** id=a4
 * Genericized update function default <wordpress@internationaltheatercompetition.com>
 */
function views_addon_process_updated_assigns($post_id, $meta_key, $meta_value=''){
    global $post;
    if ( ! is_admin() || $post->post_type != 'wpbdp_listing' ) {
        return;
    }
    $post_id        = absint($post->ID);
    $emails_to      = ( empty( get_option('views_addon_options')['views_addon_request_emails'] )) 
                        ? get_bloginfo('admin_email')   
                        : get_option('views_addon_options')['views_addon_request_emails'];
    //$judges_field  = '_wpbdp[fields][23]'; 
    //$current_judge = get_post_custom( $post_id );
    $meta_key       = '_wpbdp[fields][23]'; 
    $meta_value     = get_post_custom( $post->ID );
    $old_val        = $meta_value["$meta_key"][0];
    $pick_send      = ( isset ( $_POST['pick_send_judge'] ) ) 
                    ? sanitize_text_field( $_POST['pick_send_judge'] ) 
                    : sanitize_text_field('not assigned yet');
    $assigned_judge = ( isset ( $_POST['assign_judges'] ) ) 
                    ? sanitize_text_field( $_POST['assign_judges'] ) : '';
    
    //meta= set
    if ( $old_val == 'not_assigned' && $assigned_judge == 'not_assigned'  ) { 
        return;
    }
    elseif( ''!= $assigned_judge ) { 

        $update = update_post_meta( $post_id, $meta_key, $assigned_judge);
    
        $subscribers = array( $emails_to );
	    $subject     = 'A new Judge has beed assigned!';
	    $message     = sprintf( 'Judge %s has been assigned to, %s. 
                        Click <a href="%s">here</a> to see the play. Assigned at %s', 
                        esc_attr( $assigned_judge ),
                        get_the_title( $post ), 
                        get_permalink( $post ),
                        esc_attr( $pick_send ) 
                    );
        wp_mail( $subscribers, $subject, $message );

        return $update;
    } else {
        return;
    }
}


/** id=a5
 * Generates dropdown of all available judges from WP_Users object
 *
 * ONLY displays select dropdown in submitbox Publish metabox. Does not process.
 * @param string $judges_field  Field of metadata from post meta of cpt
 * @param string $current_judge Value of $judges_field
 */
// #a5 
add_action('views_addon_dropdown_judged', 'views_addon_dropdown_select_assign_judges');
function views_addon_dropdown_select_assign_judges(){
    global $post;
    $judges_field  = '_wpbdp[fields][23]'; 
    $current_judge = get_post_custom( $post->ID );
    // prepare arguments
    $args  = array(
        // search only for Authors role
        //'role' => 'Author',
        'orderby' => 'display_name',
    );
    // Create the WP_User_Query object
    $wp_user_query = new WP_User_Query($args);
    $authors = $wp_user_query->get_results();
    // Check for results
    echo '<fieldset>';
    echo '<label class="screen-reader-text" for="assign_judges">Assign Judge</label>';
    
    if (!empty($authors))
    {
    $old_val        = views_addon_find_judge_assigned($post->ID);
    
    if( $current_judge["$judges_field"][0] == 'not_assigned' ) { 
        $maybe_selected = 'selected="selected"'; 
        } else {
        $maybe_selected = 'data-nolo="not-set"';
    }
    echo '<select id="assign_judges" name="assign_judges" class="wpbdp-user-selector" 
            style="max-width:76%;"  tabindex="-1" aria-hidden="true">
            <option value="not_assigned" '. $maybe_selected .'>Assign | Unassign Judge</option>';
        foreach ($authors as $author) {
            $author_info = get_userdata($author->ID);
            $selected    = views_addon_tsw_selected( 
                                        $author->user_email, 
                                        $current_judge["$judges_field"][0] );
            
        echo '<option value="' . esc_html($author->user_email) . '" '. $selected .'>' 
                . esc_html($author->user_email .' '. $author->display_name) . '</option>';
            }
    echo'</select>';
    }
    echo '<input type="hidden" id="pick_send_judge" name="pick_send_judge" 
            value="'. date('Y/m/d h:i:s') .'" />
        </fieldset>';

} 
