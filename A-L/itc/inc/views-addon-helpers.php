<?php 
/**
 * @subpackage Views Addon/inc
 * @since 1.0.1
 */
// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) 	exit;

// #f1
add_filter( 'body_class', 'views_addon_assigned_protected_body_class' );
// #f2
add_filter( 'body_class', 'views_addon_inclusive_protected_post_type' );
// #a2 
add_action('views_addon_inclusive_protected_post', 'views_addon_inclusive_protected_post_type');
// #a3
//add_action('init', 'views_addon_validate_key_accesses' );

/**
 * Gets the request parameter.
 *
 * @param      string  $key      The query parameter
 * @param      string  $default  The default value to return if not found
 *
 * @return     string  The request parameter.
 */

function views_addon_get_request_parameter( $key, $default = '' ) {
    // If not request set
    if ( ! isset( $_REQUEST[ $key ] ) || empty( $_REQUEST[ $key ] ) ) {
        return $default;
    }

    // Set so process it
    return strip_tags( (string) wp_unslash( $_REQUEST[ $key ] ) );
}

/** id= a3
 * ?validated_key= add_filter( 'query_vars',
 * @return Boolean
 */
function views_addon_validate_key_accesses(){

    $key  = sanitize_text_field('validated_key');
    $str  = views_addon_get_request_parameter( $key, '' );
    $para = absint($_REQUEST['listing_id']);    

    if ( '' != $str ) { $valid = true; } else { $valid = false; }
    
        if ( $valid ) {
        $url  = home_url('/') . 'wp-admin/post.php?post=';
        $link = $url.'/'.$para.'&action=edit'; 
        } else {
        $link = '';
        }

        return $link;

} 

/**
 * render link to view files of play
 * @uses template lib/controllers/pages/class-listing-contact.php
 * @since 1.0.4
 */
function views_addon_render_judges_access_link(){
return true;
}
/**
 * Selected
 * Admin helper only
 */
function views_addon_tsw_selected( $opt, $val )
{
    $sel = '';
    $opt = ('' != $opt ) ? $opt : 'not_assigned';
    if( $opt == $val ) $sel = true;
    if ( $sel ) {
    return 'selected="selected"'; } else { return ''; }
} 

/**
 * Must have post_id
 * Admin helper only 
 */
function views_addon_find_judge_assigned($post_id){
    
    if ( ! is_admin() || get_post_type( $post_id) != 'wpbdp_listing' ) {
        return;
    }
    $judges_field  = '_wpbdp[fields][23]'; 
    $current_judge = get_post_custom( $post_id );

        return esc_attr( $current_judge );
}

/**
 * Find user permissions to pass to _check_logged_in
 * 
 * @uses maybe name="wpbdp_settings[enable-key-access]" to get access key
 */
function views_addon_find_judges_email(){
	$current_user = wp_get_current_user();
	$em = esc_html( $current_user->user_email );

    	return $em;
}

/**
 * For wpbdp-listing-attachments attachments
 * @since 1.0.1
 */
function views_addon_check_logged_in() {
	$current_user = wp_get_current_user();
	if ( 0 == $current_user->ID ) {
	    $bln = false;
	} else {
	    $bln = true;
	}
	return $bln;
}

/**
 * @return boolean Looks for class in body_class tags
 */
function viewsaddon_isBodyClass($classToSearchFor) {
    $classes = get_body_class();

    return in_array($classToSearchFor, $classes);
}

/** id=f1
 * Add body class to protected pages
 * For WPBDP singular posts
 * @param string $userObject->user_email Uses user email to text for access.
 * @since 1.1.0
 * @global string $classes Body class added.
 * @return string $classes.
 */
function views_addon_assigned_protected_body_class( $classes ) {
    global $post;
    if ( get_post_type( $post->ID ) != 'wpbdp_listing' ) return;
    
    $user               = wp_get_current_user();
    $custom_field        = '_wpbdp[fields][23]'; 
    $assigned_judge_email = get_post_meta( $post->ID, $custom_field, true ); 

	if( 'not_assigned' == $assigned_judge_email ){
        $classes[] = 'unassigned-judge';
    } elseif( $user->user_email == $assigned_judge_email ) {
        $classes[] = 'assigned-tojudge';
    } else {
        $classes[] = 'viewsaddon-restricted';
    }
        return $classes;
} 

/** hook-id=f2
 *  hook-id=a2 Only works on WPBDP singular posts
 *  hook-id=a3
 * @since 1.0.3
 */
function views_addon_inclusive_protected_post_type($classes){
    global $post, $user;

    if ( get_post_type( $post->ID) != 'wpbdp_listing' ) return;
    $user = wp_get_current_user();
    $allowed_roles = array( 'administrator', 'author', 'editor', 'contributor' );

    if ( array_intersect( $allowed_roles, $user->roles ) ) {
    
    $classes[] = 'viewsaddon-restricted-toprivate';
    } else {
    $classes[] = 'viewsaddon-restricted-frompublic';
    }

        return $classes;
}

/**
 * Get user/judge assigned to post and compare with current user.
 * @param string $assigned_judge Email of assigned judge
 * @return boolean
 * @since 1.0.2
 */
function views_addon_check_for_assigned_judge(){
    global $post;
	$user               = wp_get_current_user();
    $custom_field        = '_wpbdp[fields][23]'; 
    $assigned_judge_email = get_post_meta( $post->ID, $custom_field, true ); 
	 
	if ( $user->user_email == sanitize_text_field( $assigned_judge_email ) ){
		return true;
    } else {
        return false;
    }
}