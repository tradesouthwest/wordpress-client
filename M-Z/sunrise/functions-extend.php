<?php
/**
 * Add MIME type to Wordpress uploader
 * `define('ALLOW_UNFILTERED_UPLOADS', true)` Can leave vulnerabilities.
 * @param array $t Mime types keyed by the file extension regex corresponding to those types.
 */
// multiple types
function my_myme_types($mime_types){
    $mime_types['svg'] = 'image/svg+xml'; //Adding svg extension
    $mime_types['psd'] = 'image/vnd.adobe.photoshop'; //Adding photoshop files
    $mime_types['epub'] = 'application/epub+zip';
    $mime_types['mobi'] = 'application/x-mobipocket-ebook';
    
      return $mime_types;
}
add_filter('upload_mimes', 'my_myme_types', 1, 1);

// single type
function my_theme_custom_upload_mimes( $existing_mimes ) {
    // Add webm to the list of mime types.
    $existing_mimes['webm'] = 'video/webm';

    // Return the array back to the function with our added mime type.
    return $existing_mimes;
}
add_filter( 'mime_types', 'my_theme_custom_upload_mimes' );

// alternate and disallow
function edit_upload_types($existing_mimes = array()) {
    // allow .woff
    $existing_mimes['woff'] = 'font/woff';
 
    // add as many as you want with the same syntax
 
    // disallow .jpg files
    unset( $existing_mimes['jpg'] );
 
    return $existing_mimes;
}
add_filter('upload_mimes', 'edit_upload_types');

/**
 * Output Mime types on a page
 */
function my_theme_output_upload_mimes() {
    var_dump( wp_get_mime_types() );
}
add_action( 'template_redirect', 'my_theme_output_upload_mimes' );
