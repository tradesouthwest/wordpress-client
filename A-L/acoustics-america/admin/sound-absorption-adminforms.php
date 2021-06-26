<?php
/*
@since ver: 1.0.0
Author: Tradesouthwest
Author URI: http://tradesouthwest.com
@package sound_absorption_calc
@subpackage admin/sound-absorption-calc-adminform
*/
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** 
 * name for 'title' field
 * @since 1.0.0
 */
function sound_absorption_calc_title_field()
{
    $options = get_option('sound_absorption_calc'); 
    $sound_absorption_calc_title = (empty($options['sound_absorption_calc_title'] )) 
                ? "" : $options['sound_absorption_calc_title']; ?>
    <label class="olmin" for="sound_absorption_calc_title"><?php esc_html_e( 
'Set title for &#39;Public&#39; field.', 'sound-absorption-calc' ); ?></label>
    <input type="text" name="sound_absorption_calc[sound_absorption_calc_title]" 
           value="<?php echo esc_attr( $options['sound_absorption_calc_title'] ); ?>" 
           size="35"/>
    <?php 
}
/** 
 * Material field
 * @since 1.0.0
 */
function sound_absorption_calc_mat_a1_field()
{
    $options = get_option('sound_absorption_calc'); 
    $sound_absorption_calc_val = (empty($options['sound_absorption_calc_mat_a1'] )) 
                ? "" : $options['sound_absorption_calc_mat_a1']; ?>
    <label class="olmin" for="sound_absorption_calc_mat_a1"><?php esc_html_e( 
'Set value for F 1.', 'sound-absorption-calc' ); ?></label>
    <input type="text" name="sound_absorption_calc[sound_absorption_calc_mat_a1]" 
           value="<?php echo esc_attr( $options['sound_absorption_calc_mat_a1'] ); ?>" 
           size="5"/>
    <?php 
}
/** 
 * Material field
 * @since 1.0.0
 */
function sound_absorption_calc_mat_a2_field()
{
    $options = get_option('sound_absorption_calc'); 
    $sound_absorption_calc_val = (empty($options['sound_absorption_calc_mat_a2'] )) 
                ? "" : $options['sound_absorption_calc_mat_a2']; ?>
    <label class="olmin" for="sound_absorption_calc_mat_a2"><?php esc_html_e( 
'Set value for F 2.', 'sound-absorption-calc' ); ?></label>
    <input type="text" name="sound_absorption_calc[sound_absorption_calc_mat_a2]" 
           value="<?php echo esc_attr( $options['sound_absorption_calc_mat_a2'] ); ?>" 
           size="5"/>
    <?php 
}
/** 
 * Material field
 * @since 1.0.0
 */
function sound_absorption_calc_mat_b1_field()
{
    $options = get_option('sound_absorption_calc'); 
    $sound_absorption_calc_val = (empty($options['sound_absorption_calc_mat_b1'] )) 
                ? "" : $options['sound_absorption_calc_mat_b1']; ?>
    <label class="olmin" for="sound_absorption_calc_mat_b1"><?php esc_html_e( 
'Set value for B 1.', 'sound-absorption-calc' ); ?></label>
    <input type="text" name="sound_absorption_calc[sound_absorption_calc_mat_b1]" 
           value="<?php echo esc_attr( $options['sound_absorption_calc_mat_b1'] ); ?>" 
           size="5"/>
    <?php 
}
/** 
 * Material field
 * @since 1.0.0
 */
function sound_absorption_calc_mat_b2_field()
{
    $options = get_option('sound_absorption_calc'); 
    $sound_absorption_calc_val = (empty($options['sound_absorption_calc_mat_b2'] )) 
                ? "" : $options['sound_absorption_calc_mat_b2']; ?>
    <label class="olmin" for="sound_absorption_calc_mat_b2"><?php esc_html_e( 
'Set value for B 2.', 'sound-absorption-calc' ); ?></label>
    <input type="text" name="sound_absorption_calc[sound_absorption_calc_mat_b2]" 
           value="<?php echo esc_attr( $options['sound_absorption_calc_mat_b2'] ); ?>" 
           size="5"/>
    <?php 
}
