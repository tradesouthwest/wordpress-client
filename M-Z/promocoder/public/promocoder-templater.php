<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/** TP1
 * Attaches the specified template to the page identified by the specified name.
 *
 * @params    $page_name        The name of the page to attach the template.
 * @params    $template_path    The template's filename (assumes .php' is specified)
 *
 * @returns   false if the page does not exist; otherwise, the ID of the page.
 */
function promocoder_attach_single_template_to_page( $template ) {
// Post ID
    $post_id = get_the_ID();
    
    // For all other CPT
    if ( get_post_type( $post_id ) != 'promocoder' ) {
        return $template;
    }
    
    // Else use custom template
    if ( is_single() || is_tax() || is_search() ) {
        return promocoder_single_template_hierarchy( 'single-promocoder' );
    }
} 

/** TP2
    * Get the custom template if is set
    *
    * @since 1.0
    */
function promocoder_single_template_hierarchy( $template ) {
    
    // Get the template slug
    $template_slug = rtrim( $template, '.php' );
    $template      = $template_slug . '.php';
    
    // Check if a custom template exists in the theme folder, if not, load the plugin template file
    if ( $theme_file = locate_template( array( 'plugin_template/' . $template ) ) ) {
        $file = $theme_file;
    }
    else {
        $file = plugin_dir_path( __FILE__ ) . 'templates/' . $template;
    }
    
    return apply_filters( 'promocoder_single_template_' . $template, $file );
}
