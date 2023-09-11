<?php
/**
 * Prevent direct access to the file.
 * @subpackage extend-quickcab/inc/extend-quickcab-admin.php
 * @since 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extend Quickcab Options Page
 *
 * Add options page for the plugin.
 *
 * @since 1.0
 */
function extend_quickcab_plugin_page() {

	add_options_page(
		__( 'Extend Quickcab Options', 'extend-quickcab' ),
		__( 'Extend Quickcab Settings', 'extend-quickcab' ),
		'manage_options',
		'extend-quickcab',
		'extend_quickcab_render_admin_page'
	);

}
add_action( 'admin_menu', 'extend_quickcab_plugin_page' );
add_action( 'admin_init', 'extend_quickcab_register_admin_options' ); 
/**
 * Register settings for options page
 *
 * @since    1.0.0
 * 
 * a.) register all settings groups
 * Register Settings $option_group, $option_name, $sanitize_callback 
 */
function extend_quickcab_register_admin_options() 
{
    
    register_setting( 'extend_quickcab_options', 'extend_quickcab_options' );
        
    //add a section to admin page
    add_settings_section(
        'extend_quickcab_options_settings_section',
        '',
        'extend_quickcab_options_settings_section_callback',
        'extend_quickcab_options'
    );    
    // settings text field
    add_settings_field(
        'extend_quickcab_message_one',
        __('Checkout Messages', 'extend-quickcab'),
        'extend_quickcab_message_one_cb',
        'extend_quickcab_options',
        'extend_quickcab_options_settings_section',
        array( 
            'type'        => 'text',
            'option_name' => 'extend_quickcab_options', 
            'name'        => 'extend_quickcab_message_one',
            'default'     => '',
            'value'       => (!isset( get_option('extend_quickcab_options')['extend_quickcab_message_one']))
                               ? '' : get_option('extend_quickcab_options')['extend_quickcab_message_one'],
            'description' => esc_html__( 'Cancellation message. ', 'extend-quickcab' ),
            'tip'         => esc_attr__( 'Leave blank for none.', 'extend-quickcab' )  
        )
    );
    // settings text field
    add_settings_field(
        'extend_quickcab_message_two',
        __('Booking Notice', 'extend-quickcab'),
        'extend_quickcab_message_two_cb',
        'extend_quickcab_options',
        'extend_quickcab_options_settings_section',
        array( 
            'type'        => 'text',
            'option_name' => 'extend_quickcab_options', 
            'name'        => 'extend_quickcab_message_two',
            'default'     => '',
            'value'       => (!isset( get_option('extend_quickcab_options')['extend_quickcab_message_two']))
                               ? '' : get_option('extend_quickcab_options')['extend_quickcab_message_two'],
            'description' => esc_html__( 'First booking form message. ', 'extend-quickcab' ),
            'tip'         => esc_attr__( 'Leave blank for none.', 'extend-quickcab' )  
        )
    );
    // settings text field
    add_settings_field(
        'extend_quickcab_message_three',
        __('Booking Warning', 'extend-quickcab'),
        'extend_quickcab_message_three_cb',
        'extend_quickcab_options',
        'extend_quickcab_options_settings_section',
        array( 
            'type'        => 'text',
            'option_name' => 'extend_quickcab_options', 
            'name'        => 'extend_quickcab_message_three',
            'default'     => '',
            'value'       => (!isset( get_option('extend_quickcab_options')['extend_quickcab_message_three']))
                               ? '' : get_option('extend_quickcab_options')['extend_quickcab_message_three'],
            'description' => esc_html__( 'Warning select message. ', 'extend-quickcab' ),
            'tip'         => esc_attr__( 'Se non si tratta di un viaggio di sola andata, seleziona Ritorna adesso.', 'extend-quickcab' )  
        )
    ); 
    // settings checkbox 
    /* add_settings_field(
        'extend_quickcab_debug_radio',
        __('Activate Debug', 'extend_quickcab'),
        'extend_quickcab_debug_radio_cb',
        'extend_quickcab_options',
        'extend_quickcab_options_settings_section',
        array( 
            'type'        => 'checkbox',
            'option_name' => 'extend_quickcab_options', 
            'name'        => 'extend_quickcab_debug_radio',
            'value'       => (!isset( get_option('extend_quickcab_options')['extend_quickcab_debug_radio']))
                                ? 0 : get_option('extend_quickcab_options')['extend_quickcab_debug_radio'],
            'checked'     => ( get_option('extend_quickcab_options')['extend_quickcab_debug_radio'] == 0) 
                                ? '' : 'checked',
            'description' => esc_html__( 'Check to use debug. Uncheck to disable.', 'extend_quickcab' ),
            'tip'         => esc_attr__( 'Default is OFF. Used to start new debug functions.', 'extend_quickcab' )  
        )
    ); */ 
}

/** 
 * message field
 * @since 1.0.1
 * @input type 
 */
function extend_quickcab_message_one_cb($args)
{ 
    printf(
        '<fieldset>
        <p><span class="vmarg">%4$s </span></p>
        <input id="%1$s" class="text-field" name="%2$s[%1$s]" type="%6$s" value="%3$s" size="65"/>
        <sup><em class="grctip" title="%5$s">?</em></sup>
        </fieldset>',
            $args['name'],
            $args['option_name'],
            $args['value'],
            $args['description'],
            $args['tip'],
            $args['type']
    );
}   
/** 
 * message field
 * @since 1.0.1
 * @input type 
 */
function extend_quickcab_message_two_cb($args)
{ 
    printf(
        '<fieldset>
        <p><span class="vmarg">%4$s </span></p>
        <input id="%1$s" class="text-field" name="%2$s[%1$s]" type="%6$s" value="%3$s" size="65"/>
        <sup><em class="grctip" title="%5$s">?</em></sup>
        </fieldset>',
            $args['name'],
            $args['option_name'],
            $args['value'],
            $args['description'],
            $args['tip'],
            $args['type']
    );
}   
/** 
 * message field
 * @since 1.0.1
 * @input type text
 */
function extend_quickcab_message_three_cb($args)
{ 
    printf(
        '<fieldset>
        <p><span class="vmarg">%4$s </span></p>
        <input id="%1$s" class="text-field" name="%2$s[%1$s]" type="%6$s" value="%3$s" size="65"/>
        <sup><em class="grctip" title="%5$s">?</em></sup>
        </fieldset>',
            $args['name'],
            $args['option_name'],
            $args['value'],
            $args['description'],
            $args['tip'],
            $args['type']
    );
}   

/** 
 * switch for 'allow debug' field
 * @since 1.0.1
 * @input type checkbox
 */
function extend_quickcab_debug_radio_cb($args)
{ 
     printf(
        '<fieldset><b class="grctip" data-title="%6$s">?</b><sup></sup>
        <input type="hidden" name="%3$s[%1$s]" value="0">
        <input id="%1$s" type="%2$s" name="%3$s[%1$s]" value="1"  
        class="regular-checkbox" %7$s /><br>
        <span class="vmarg">%5$s </span> v=%4$s</fieldset>',
            $args['name'],
            $args['type'],
            $args['option_name'],
            $args['value'],
            $args['description'],
            $args['tip'],
            $args['checked']
        );
}   

//callback for description of options section
function extend_quickcab_options_settings_section_callback() 
{
	echo '<h2>' . esc_html__( 'Extend Quickcab', 'extend-quickcab' ) . '</h2>';
}
// display the plugin settings page
function extend_quickcab_render_admin_page()
{
	// check if user is allowed access
    if ( ! current_user_can( 'manage_options' ) ) return;
    
	echo '<form action="options.php" method="post">';

	// output security fields
	settings_fields( 'extend_quickcab_options' );

	// output setting sections
	do_settings_sections( 'extend_quickcab_options' );
	submit_button();

    echo '</form>';
    echo '<h3>tips and notes</h3>
    <dl><dt>Checkout fields</dt>
    <dd>To add to array of disabled fields use `extend_quickcab_woocommerce_billing_fields` function in extend-quickcab-users.php file. (in inc folder of this plugin)</dd>
    <dd>Note that there is a second file named "extend-quickcab-checkout" which is for custom fields. Do not confuse with the above file which only handles user functions of checkout page.</dd>
    <dd>Also check the javascript file in the "js" folder for the select dropdown disabling of select dropdown checkout fields.</dd>
    <dt></dt>
    <dd></dd>
    </dl>'; 
} 
