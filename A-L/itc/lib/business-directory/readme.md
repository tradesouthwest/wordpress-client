= Child theme files =

== added to functions .php of child ==
Helpful links [WPBDP docs](https://businessdirectoryplugin.com/knowledge-base/general-customization-guide/)

```
/* Functions added by Larry @codeable 
-------------------------------------- 
 These function will run independantly from TSW Views Addon plugin which handles
 all the customized aspects of the wpbdp plugin. If above plugin is not installed you
 can still use these functions to password protect wpbdp pages.
-------------------------------------- */

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

	//if ( itctheme_remove_public_content_onpass() ) {
		//remove_action( genesis_entry_content maybe )
		do_action( 'itctheme_protected_entry_content', 'itctheme_entry_content_protected_display' );
	//}
	//return false;
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

	// check for plugin addon first
	if ( !function_exists( 'views_addon_find_judge_assigned' ) ) {
		echo '<div class="itctheme-entry-heading">
			
		</div>';

	} else {
	$text = ( empty( get_option('views_addon_options')['views_addon_heading_h1'] )) 
			  ? '' : get_option('views_addon_options')['views_addon_heading_h1'];
	echo '<div class="itctheme-entry-heading">
			<h4>' . esc_html($text) . '</h4>
		</div>';
	}
}

/**
 * Removes the word Protected from title
 * Resevered - Uncomment to remove title precursor, if any pages are password proteced.
 * @since 1.1.0
 * @global string $format Before pseudo class.
 * @return string blank or text
 */
//add_filter( 'protected_title_format', 'remove_private_protected_before_title' );
//add_filter( 'private_title_format', 'remove_private_protected_before_title'  );
function remove_private_protected_before_title( $format ) {
return '%s';
}

/**
 * Add T&C agree button to contact form
 * For WPBDP singular posts
 * @since 1.1.0
 * @global is_singular
 * @return HTML
 */
add_action( 'wpbdp_contact_form_extra_fields', 'itctheme_contact_form_extra_fields', 15 );
function itctheme_contact_form_extra_fields(){
	if ( ! is_singular() ) return;
	echo 
	'<div class="wpbdp-form-field wpbdp-form-field-type-checkbox">
	<div class="wpbdp-form-field-label">
		<label for="wpbdp-contact-form-terms">Terms and Conditions
		<span class="wpbdp-form-field-required-indicator">*</span>
		<p>Judges must agree to Site Terms</p>
		
		<p><input id="wpbdp-contact-form-terms" type="checkbox" 
			name="wpbdp-contact-form-terms" value="yes" required>Agree</label></p>

        <p><a href="https://internationaltheatercompetition.com/terms-to-review-a-play/" 
        title="terms" target="_blank">View Terms Here</a>
    <small>Opens in new window/tab</small><p>
		</div>
	</div>';
} ```
