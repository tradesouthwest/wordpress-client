<?php
/*
@since ver: 1.0.0
Author: Tradesouthwest
Author URI: http://tradesouthwest.com
@package sound_absorption_calc
@subpackage admin/sound-absorption-calc-metabox
*/
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Add a meta box to the product editing screen
 */
function sound_absorption_calc_product_metabox() {
    
    add_meta_box( 'sound_absorption_calc_meta', 
                __( 'NRC Values', 'sound-absorption-calc' ), 
                'sound_absorption_calc_metabox_callback', 
                'product' , 
                'side' 
            );
}

function sound_absorption_calc_metabox_callback()
{
global $post;
//$sac_meta = soundAbsoptionCalc_get_nrc_value($opt='');
$nrca = get_post_meta($post->ID, 'sac_meta_nrca', true );
$nrcb = get_post_meta($post->ID, 'sac_meta_nrcb', true );
$nrcc = get_post_meta($post->ID, 'sac_meta_nrcc', true );
echo
    '<label for="sac_meta_nrca" style="margin-top:.5em;margin-bottom:0">' . esc_html__( 'NRC Value A', 'woocommerce' ) . ' 
    <small>' . esc_html__( 'decimal/number value', 'woocommerce' ) . '</small></label>
    <input type="text" name="sac_meta_nrca" id="sac_meta_nrca" value="'. esc_attr($nrca) .'" class="form-input"/>

    <label for="sac_meta_nrcb" style="margin-top:.5em;margin-bottom:0">' . esc_html__( 'NRC Value B', 'woocommerce' ) . ' 
    <small></small></label>
    <input type="text" name="sac_meta_nrcb" id="sac_meta_nrcb" value="'. esc_attr($nrcb) .'" class="form-input" />
    
    <label for="sac_meta_nrcc" style="margin-top:.5em;margin-bottom:0">' . esc_html__( 'NRC Value C', 'woocommerce' ) . ' 
    <small>' . esc_html__( 'optional', 'woocommerce' ) . '</small></label>
    <input type="text" name="sac_meta_nrcc" id="sac_meta_nrcc" value="'. esc_attr($nrcc) .'" class="form-input" />';        
        
        echo wp_nonce_field( 'sac_meta_nrc_meta_box_nonce', 'sac_meta_nrc_nonce' );
}
/**
 * Save meta data
 * @param int $post_id Global
 */
function sound_absorption_calc_productmeta_save( )
{
    global $post;
    //if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
     
    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['sac_meta_nrc_nonce'] ) 
        || !wp_verify_nonce( $_POST['sac_meta_nrc_nonce'], 
        'sac_meta_nrc_meta_box_nonce' ) ) return;
    
    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) ) return;
     
    if( isset( $_POST['sac_meta_nrca'] ) ) :
        update_post_meta( $post->ID, 'sac_meta_nrca', sanitize_text_field( $_POST['sac_meta_nrca']) );
    endif;
    if( isset( $_POST['sac_meta_nrcb'] ) ) :
        update_post_meta( $post->ID, 'sac_meta_nrcb', sanitize_text_field( $_POST['sac_meta_nrcb']) );
    endif;
    if( isset( $_POST['sac_meta_nrcc'] ) ) :
        update_post_meta( $post->ID, 'sac_meta_nrcc', sanitize_text_field( $_POST['sac_meta_nrcc']) );
    endif;
}
