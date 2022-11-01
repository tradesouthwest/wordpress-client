<?php
/* -------------------------------------- 
 These function may be loaded independantly from TSW Views Addon plugin which handles
 all the customized aspects of the wpbdp plugin. If above plugin is not installed you
 can still use these functions to email protect wpbdp pages.
-------------------------------------- */

/**
 * Check if page is email protected
 * For WPBDP singular posts. 
 * @requires Views Addon TSW plugin If not, replace $user_judge='your@email.com'.
 * @since 1.1.0
 * @global is_singular
 * @return Boolean
 */
function itctheme_remove_public_content_onpass(){

	if( function_exists( 'views_addon_check_for_assigned_judge' ) ) {
		$user_judge = views_addon_check_for_assigned_judge();
		
		if ( is_singular() && $user_judge ){
			return true;
		}
	} 
		return false;
}

/**
 * Remove Genesis content from public views
 * For WPBDP singular posts
 * @see https://wpsites.net/wordpress-themes/new-genesis-2-0-loop-hooks-how-to-use-them/
 * @since 1.1.0
 * @hook genisis_entry_content
 * @return string Classes.
 */
add_action('genesis_entry_content', 'itctheme_public_content_limited_content');
function itctheme_public_content_limited_content(){

	if ( itctheme_remove_public_content_onpass() ) {
		//remove_action( genesis_entry_content maybe )
		do_action( 'itctheme_protected_entry_content', 'itctheme_entry_content_protected_display' );
	}
	return false;
}

/**
 * Check if page is password protected
 * For WPBDP singular posts
 * @since 1.1.0
 * @global is_singular
 * @return Boolean
 */
add_action('itctheme_protected_entry_content', 'itctheme_entry_content_protected_display');
function itctheme_entry_content_protected_display(){
	echo '<div class="itctheme-entry-heading">
			<h4>Content for this play is reserved by another judge at this time</h4>
		</div>';
}

/**
 * Removes the word Protected from title
 * Resevered - uncomment to remove title precursor.
 * @since 1.1.0
 * @global string $format Before pseudo class.
 * @return string blank or text
 */
//add_filter( 'protected_title_format', 'remove_private_protected_before_title' );
//add_filter( 'private_title_format', 'remove_private_protected_before_title'  );
function remove_private_protected_before_title( $format ) {
return '%s';
}
