<?php
/*
@since ver: 1.0.0
Author: Tradesouthwest
Author URI: http://tradesouthwest.com
@package sound_absorption_calc
@subpackage admin/sound-absorption-calc-adminpage
*/
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

    add_action( 'admin_menu', 'sound_absorption_calc_add_options_page' );  
    add_action( 'admin_init', 'sound_absorption_calc_register_admin_options' );

/**
 * add forms for settings
 */
    include 'sound-absorption-adminforms.php';     

/**
 * Add an options page under the Settings submenu
 * $page_title, $menu_title, $capability, $menu_slug, $function-to-render, $icon_url, $position
 * @since  1.0.0
 */
function sound_absorption_calc_add_options_page() 
{
    add_menu_page(
        __( 'Sound Absorption Calculator Plugin', 'sound-absorption-calc' ),
        __( 'Sound Absorption', 'sound-absorption-calc' ),
        'manage_options',
        'sound_absorption_calc',
        'sound_absorption_calc_options_page',
        'dashicons-admin-tools' 
    );
}
/** 
 * register a new sections and fields in the "sound_absorption_calc admin" page
 * option group, option name
 */
function sound_absorption_calc_register_admin_options() 
{
    register_setting( 'sound_absorption_calc_adminPg', 'sound_absorption_calc' ); //options pg

/**
 * listings section
 */        
    add_settings_section(
        'sound_absorption_calc_section',
        '',
        'sound_absorption_calc_section_cb',
        'sound_absorption_calc_adminPg'
    ); 
    //settings 
    add_settings_field(
        'sound_absorption_calc_title',
        __('Text to Display', 'sound-absorption-calc'),
        'sound_absorption_calc_title_field',
        'sound_absorption_calc_adminPg',
        'sound_absorption_calc_section'
    );
    add_settings_field(
        'sound_absorption_calc_mat_a1',
        __('Fiberglass 1 in.', 'sound-absorption-calc'),
        'sound_absorption_calc_mat_a1_field',
        'sound_absorption_calc_adminPg',
        'sound_absorption_calc_section'
    );
    add_settings_field(
        'sound_absorption_calc_mat_a2',
        __('Fiberglass 2 in.', 'sound-absorption-calc'),
        'sound_absorption_calc_mat_a2_field',
        'sound_absorption_calc_adminPg',
        'sound_absorption_calc_section'
    );
    add_settings_field(
        'sound_absorption_calc_mat_b1',
        __('Polyester 1', 'sound-absorption-calc'),
        'sound_absorption_calc_mat_b1_field',
        'sound_absorption_calc_adminPg',
        'sound_absorption_calc_section'
    );
    add_settings_field(
        'sound_absorption_calc_mat_b2',
        __('Polyester 2', 'sound-absorption-calc'),
        'sound_absorption_calc_mat_b2_field',
        'sound_absorption_calc_adminPg',
        'sound_absorption_calc_section'
    );
    // help
    add_settings_field(
        'sound_absorption_calc_help',
        __('Notations', 'sound-absorption-calc'),
        'sound_absorption_calc_help_field',
        'sound_absorption_calc_adminPg',
        'sound_absorption_calc_section'
    );
}

/** 
 * Help field
 * @since 1.0.0
 */
function sound_absorption_calc_help_field()
{
    //$options = get_option('sound_absorption_calc'); 
    ?><label class="olmin" for="sound_absorption_calc_help"><?php esc_html_e( 
'Information about usage:', 'sound-absorption-calc' ); ?></label>
<dl><dt><?php esc_html_e( 'NRC Values', 'sound-absorption-calc' ); ?></dt>
<dd><?php esc_html_e( 'Values MUST be a number, decimal of two places or single number OK.', 'sound-absorption-calc' ); ?> </dd>
<dt><?php esc_html_e( 'Adding fields', 'sound-absorption-calc' ); ?></dt>
<dd><?php esc_html_e( 'New NRC values for new Materials can use the existing tags. If your value is not one of the above, then you will need to add a new Materil tag and update this plugin settings options.', 'sound-absorption-calc' ); ?> </dd>
<dt><?php esc_html_e( 'Adding ', 'sound-absorption-calc' ); ?></dt>
<dd><?php esc_html_e( 'New settings options.', 'sound-absorption-calc' ); ?> </dd>

</dl>
    <?php 
}

// section content cb
function sound_absorption_calc_section_cb()
{    
    print( '<h3><span class="dashicons dashicons-admin-generic"></span> ' );
    esc_html_e( ' Sound Absorption Calculator Settings', 'sound-absorption-calc' ); 
    print( '</h3>' );
}

        
//render admin page
function sound_absorption_calc_options_page() 
{
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) return;
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
    // add settings saved message with the class of "updated"
    add_settings_error( 'sound_absorption_calc_messages', 'sound_absorption_calc_message', 
                        __( 'Settings Saved', 'sound-absorption-calc' ), 'updated' );
    }
    // show error/update messages
    settings_errors( 'sound_absorption_calc_messages' );
   
    echo esc_html( get_admin_page_title() ); ?>
    <div class="wrap wrap-sound-absorption-calc-admin">

    <form action="options.php" method="post"><?php 
        settings_fields( 'sound_absorption_calc_adminPg' );
        do_settings_sections( 'sound_absorption_calc_adminPg' ); 
        submit_button( 'Save Settings' ); ?>
    </form>
    
    </div>
    <?php 
}