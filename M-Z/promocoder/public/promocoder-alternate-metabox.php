<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// MB1    
add_action( 'add_meta_boxes', 'promocoder_editor_meta_box' );
// MB3    
add_action( 'save_post', 'promocoder_editor_meta_save_meta_box' );  
/** MB1
 * Register meta box(es).
 */    
function promocoder_editor_meta_box()
{                                           // --- Parameters: ---
    add_meta_box( 'promocoder-meta-box',   // ID attribute of metabox
                  'Privatize Page',       // Title of metabox visible to user
                  'promocoder_meta_box_cb', // Function that prints box in wp-admin
                  'page',               // Show box for posts, pages, custom, etc.
                  'side',            // Where on the page to show the box
                  'low'              // Priority of box in display order
                    
    );
}
/** MB2
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function promocoder_meta_box_cb( $post ) 
{
    global $post;
    $meta_val = 'no';
    $meta_val = get_post_meta( $post->ID, 'promocoder_meta_box', true );
    $metaval = ( 'yes' == $meta_val ) ? $meta_val : sanitize_text_field('no');
    $checked = '';
				if( $metaval && 'yes' == $metaval ){
					$checked = 'checked="checked"';
				} 
    ?>
    <span class="components-checkbox-control__input-container">
        <input id="promocoder-meta-box" type="checkbox" 
        name="promocoder_meta_box" 
        class="components-checkbox-control__input-custom" 
        value="<?php echo esc_attr($metaval); ?>" <?php echo $checked; ?>>
    </span>
    <label for="promocoder_meta_box">
    <?php echo esc_html__('Make this Page Promotional Only', 'promocoder'); ?></label>
     
    <p>&nbsp;Set to: <?php echo esc_attr( $metaval . '-' . $meta_val ); ?></p>
    <?php wp_nonce_field( 'nonce_promocoder', 'promocoder_nonce' ); ?>       
    <?php
}
 
/** MB3
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function promocoder_editor_meta_save_meta_box( $post_id ) {
    if ( ! isset( $_POST['promocoder_nonce'] ) ) {
        return;
    }
 
    if ( ! wp_verify_nonce( $_POST['promocoder_nonce'], 'nonce_promocoder' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
 
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
 
    if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
        if( !isset( $_POST[ 'promocoder_meta_box' ] ) ) {

            update_post_meta( $post_id, 'promocoder_meta_box', 
                array_map( 'sanitize_text_field', 'no' ) 
                );
        } else {
            update_post_meta( $post_id, 'promocoder_meta_box', 
                array_map( 'sanitize_text_field', 'yes' ) 
                );
        }
 
    }
}
