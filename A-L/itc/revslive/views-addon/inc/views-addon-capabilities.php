<?php 
/**
 * Get user's capabilities.
 *
 * @param  int|WP_User $user The user ID or object. Default is the current user.
 *
 * @return array             The user's capabilities or empty array if none or user doesn't exist.
 */
function views_addon_get_user_capabilities( $user = null ) {

	$user = $user ? new WP_User( $user ) : wp_get_current_user();

	return array_keys( $user->allcaps );
} 

/**
 * Author access granted
 * Somebody who can write and manage their own posts but cannot publish them.
 * @global string $current_user
 */
if ( function_exists('views_addon_get_user_capabilities') ) :
function views_addon_check_judges_capability($current_user){

	$current_user = wp_get_current_user();
	$author_caps  = views_addon_get_user_capabilities( $current_user->ID );
	
	if ( !empty ( $author_caps ) && in_array( 'pdtsw_mediator', $author_caps ) ) { 
	
		return true;
	} else {
		return false;
	}
}
endif;

/**
 * Get user email and compare with judge assigned to listing
 *
 */
function views_addon_determine_protected_owner(){

	if ( !views_addon_check_judges_capability($current_user) ) { 
        return;
    }

	// find judge by listing
	
}
