<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Add meta box to editor
 * 
 * @strings $id, $title, $callback, $screen, $context, $priority, $args  
 * function's action_added in register cpt 
 */
function promocoder_link_meta_box() 
{
    add_meta_box(
        'promocoder_link_meta', 
        __( 'Note', 'promocoder' ), 
        'promocoder_link_meta_box_cb',    //callback
        'promocoder',                    // post type screen 
        'advanced',                 
        'default' 
    );
}
//'register_meta_box_cb' => 'promocoder_link_meta_box',
add_action( 'add_meta_boxes', 'promocoder_link_meta_box' );
/**
 * Output the HTML for the metabox.
 */
function promocoder_link_meta_box_cb($post) {
    global $post;
    $promocoder_link = ''; $promocoder_thumb = '';
	
    // Output the field
	$promocoder_link = get_post_meta( $post->ID, 'promocoder_link', true );    
    $html = '';
    $html .= '<input id="promocoder_link" type="text" name="promocoder_link" 
    value="' . esc_attr($promocoder_link) . '" class="widefat">';

    $html .= wp_nonce_field( 'promocoder_field', 'promocoder_field' );
    echo $html;
}

/**
 * Save meta box content.
 * https://metabox.io/how-to-create-custom-meta-boxes-custom-fields-in-wordpress/
 * @param int $post_id Post ID
 */
add_action( 'save_post', 'promocoder_update_link_meta', 10, 2 );
function promocoder_update_link_meta( $post_id ) 
{
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( $parent_id = wp_is_post_revision( $post_id ) ) {
        $post_id = $parent_id;
    }

    $fields = [
        'promocoder_link',
    ];
    foreach ( $fields as $field ) {
        if ( array_key_exists( $field, $_POST ) ) {
            update_post_meta( $post_id, $field, 
            sanitize_text_field( $_POST[$field] ) );
        }
     }
} 


// add custom post messages in the admin for our post type
add_filter( 'post_updated_messages', function($messages) {
    global $post, $post_ID;
    $link = esc_url( get_permalink($post_ID) );

    $messages['promocoder'] = array(
        0 => '',
        1 => sprintf( __('Promocode Updated. <a href="%s">View list</a>'), $link ),
    );
    return $messages;
}); 