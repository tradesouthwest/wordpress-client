<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    tarpbuilder
 * @subpackage /include
 * @author     Larry Judd <tradesouthwest@gmail.com>
 * TODO add a field in the order table (admin side)
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'tarpbuilder_plus_add_options_page' ); 
add_action( 'admin_init', 'tarpbuilder_plus_register_admin_options' ); 

/** 
 * Create an options page
 * $parent_slug, $page_title, $menu_slug, $menu_title, $cap,  $funx
 */ 
function tarpbuilder_plus_add_options_page() 
{
   add_submenu_page(
       'options-general.php',
        __( 'TarpBuilder Plus', 'tarpbuilder' ),
        __( 'Tarp Builder', 'tarpbuilder' ),
        'manage_options',
        'tarpbuilder-plus',
        'tarpbuilder_plus_options_page'
    );
}   

/** 
 ** a.) Register new settings
 *  $option_group (page), $option_name, $sanitize_callback
 *  --------
 ** b.) Add sections
 *  $id, $title, $callback, $page
 *  --------
 ** c.) Add fields 
 *  $id, $title, $callback, $page, $section, $args = array() 
 *  --------
 ** d.) Options Form Rendering. action="options.php"
 *
 */


/* 
 * a.) register all settings groups
 */ 
function tarpbuilder_plus_register_admin_options() 
{
//options pg
    register_setting( 'tarpbuilder_plus_options', 'tarpbuilder_plus_options' );
    register_setting( 'tarpbuilder_content_options', 'tarpbuilder_content_options' );
    register_setting( 'tarpbuilder_admin_help_content', 'tarpbuilder_admin_help_content' );
    
/*
 * b1.) options section
 */        
    add_settings_section(
        'tarpbuilder_plus_options_section',
        esc_html__( 'Configuration and Settings', 'tarpbuilder' ),
        'tarpbuilder_plus_options_section_cb',
        'tarpbuilder_plus_options'
    );
/*
 * b2.) help section
 */    
    add_settings_section(
        'tarpbuilder_content_options_section',
        esc_html__( 'TarpBuilder Content Helpers', 'tarpbuilder' ),
        'tarpbuilder_content_options_cb',
        'tarpbuilder_content_options'
    );
/*
 * b2.) help section
 */    
    add_settings_section(
        'tarpbuilder_admin_help_content_section',
        esc_html__( 'TarpBuilder Help', 'tarpbuilder' ),
        'tarpbuilder_admin_help_content_cb',
        'tarpbuilder_admin_help_content'
    );

    // c0.) settings 
    add_settings_field(
        'tarpbuilder_plus_cstitle_field',
        esc_attr__('Label for Checkout Sum', 'tarpbuilder'),
        'tarpbuilder_plus_cstitle_field_cb',
        'tarpbuilder_plus_options',
        'tarpbuilder_plus_options_section',
        array( 
            'type'         => 'text',
            'option_group' => 'tarpbuilder_plus_options', 
            'name'         => 'tarpbuilder_plus_cstitle_field',
            'value'        => 
            esc_attr( get_option( 'tarpbuilder_plus_options' )['tarpbuilder_plus_cstitle_field'] ),
            'description'  => esc_html__( 'Shows above Product price and in checkout and in email. 
                              Try: Accumulated Cost', 'tarpbuilder' ),
            'tip'          => esc_html__( 'Also used in orders in admin and in order emails', 'tarpbuilder' )
        )
    );
    // c1.) settings 
    add_settings_field(
        'tarpbuilder_plus_csdescription_field',
        esc_attr__('Cart Text', 'tarpbuilder'),
        'tarpbuilder_plus_csdescription_field_cb',
        'tarpbuilder_plus_options',
        'tarpbuilder_plus_options_section',
        array( 
            'type'         => 'text',
            'option_group' => 'tarpbuilder_plus_options', 
            'name'         => 'tarpbuilder_plus_csdescription_field',
            'value'        => get_option( 'tarpbuilder_plus_options' )['tarpbuilder_plus_csdescription_field'],
            'description'  => __( 'Shows below product name in cart. Try: Per Reqested', 'tarpbuilder' ),
             'tip'         => __( 'Also shows on line items in checkout and in order emails', 'tarpbuilder' )
        )
    );
    // c2.) settings 
    add_settings_field(
        'tarpbuilder_plus_wndproduct_field',
        esc_attr__('Product Page Title', 'tarpbuilder'),
        'tarpbuilder_plus_wndproduct_field_cb',
        'tarpbuilder_plus_options',
        'tarpbuilder_plus_options_section',
        array( 
            'type'         => 'text',
            'option_group' => 'tarpbuilder_plus_options', 
            'name'         => 'tarpbuilder_plus_wndproduct_field',
            'value'        => get_option( 'tarpbuilder_plus_options' )['tarpbuilder_plus_wndproduct_field'],
            'description'  => __( 'Text to display on the product page. Try: Select No. of', 'tarpbuilder' ),
            'tip'          => __( 'Text will show above the add-to-cart button to the left of the 
                              quantity field. Could be Days, Hours, People....', 'tarpbuilder' )
        )
    );    
    // c4.) settings 
    add_settings_field(
        'tarpbuilder_plus_wndinppd_field',
        esc_attr__('Style Price Title', 'tarpbuilder'),
        'tarpbuilder_plus_wndinppd_field_cb',
        'tarpbuilder_plus_options',
        'tarpbuilder_plus_options_section',
        array( 
            'type'         => 'text',
            'option_group' => 'tarpbuilder_plus_options', 
            'name'         => 'tarpbuilder_plus_wndinppd_field',
            'value'        => get_option( 'tarpbuilder_plus_options' )['tarpbuilder_plus_wndinppd_field'],
            'description'  => __( 'Add inline style just below the product price. 
                              Try: font-size:90%;margin-bottom:.5em', 'tarpbuilder' ),
            'tip'          => __( 'You my add a attribute like margin-left etc. 
                              Just write like it is inline style="". ', 'tarpbuilder' )
        )
    );    
    // c5.) settings 
    add_settings_field(
        'tarpbuilder_plus_wndinwidth_field',
        esc_attr__('Input Fields Style', 'tarpbuilder'),
        'tarpbuilder_plus_wndinwidth_field_cb',
        'tarpbuilder_plus_options',
        'tarpbuilder_plus_options_section',
        array( 
            'type'         => 'text',
            'option_group' => 'tarpbuilder_plus_options', 
            'name'         => 'tarpbuilder_plus_wndinwidth_field',
            'value'        => 
            esc_attr( ( empty( get_option( 'tarpbuilder_plus_options' )['tarpbuilder_plus_wndinwidth_field'] ) ) )
                        ? '' : get_option( 'tarpbuilder_plus_options' )['tarpbuilder_plus_wndinwidth_field'],
            'description'  => esc_html__( 'Style Quantity and Datepicker fields on product page. Try: width:40%;border-radius:5px', 'tarpbuilder' ),
            'tip'          => esc_attr__( 'This helps to match the input field to your theme input fields so they all look the same. You my add a attribute like margin-left etc. 
                                Just write like it is inside of style="". ', 'tarpbuilder' )
        )
    );    
    // c6.) settings 
    add_settings_field(
        'tarpbuilder_plus_wndtaxbase_field',
        esc_attr__('Tax Options', 'tarpbuilder'),
        'tarpbuilder_plus_wndtaxbase_field_cb',
        'tarpbuilder_plus_options',
        'tarpbuilder_plus_options_section',
         array(
            'type'         => 'select',
            'option_group' => 'tarpbuilder_plus_options', 
            'name'         => 'tarpbuilder_plus_wndtaxbase_field',
            'value'        => esc_attr( get_option( 'tarpbuilder_plus_options' )['tarpbuilder_plus_wndtaxbase_field'] ),
            'options'      => array(
                                  "standard" => "Standard", 
                                  "reduced" => "Reduced", 
                                  "zero"   => "Zero" ),
            'description'  => __( 'This adjust the Additional Fee tax rate only 
                              - not the product tax rate.', 'tarpbuilder' ),
            'tip'          => __( 'Choices are: standard | reduced | zero 
                              See Woocommerce Settings to set taxes', 'tarpbuilder' )
        )
    );
/* **************** CHECKBOXES **************** */

    // c7.) settings checkbox 
    add_settings_field(
        'tarpbuilder_plus_wndnada_field',
        esc_attr__('Activate Zero Entry', 'tarpbuilder'),
       'tarpbuilder_plus_wndnada_field_cb',
        'tarpbuilder_plus_options',
        'tarpbuilder_plus_options_section',
        array( 
            'type'        => 'checkbox',
            'option_group' => 'tarpbuilder_plus_options', 
            'name'        => 'tarpbuilder_plus_wndnada_field',
            'label_for'   => 'tarpbuilder_plus_wndnada_field',
            'value'       => (empty(get_option('tarpbuilder_plus_options')['tarpbuilder_plus_wndnada_field']))
            ? 0 : get_option('tarpbuilder_plus_options')['tarpbuilder_plus_wndnada_field'],
            'checked'     => (!isset(get_option('tarpbuilder_plus_options')['tarpbuilder_plus_wndnada_field']))
                                ? 0 : get_option('tarpbuilder_plus_options')['tarpbuilder_plus_wndnada_field'],
            'description' => __('Check to allow Add-To-Cart without selecting increments.', 
                            'tarpbuilder' ),
            'tip'         => __( 'Checking will override the default of requiring customers 
                            to select a length of time/increment.', 'tarpbuilder' ) 
        )
    ); 
    // c8.) settings checkbox 
    add_settings_field(
        'tarpbuilder_plus_wndmatch_field',
        esc_attr__('Allow Product Price to Match', 'tarpbuilder'),
        'tarpbuilder_plus_wndmatch_field_cb',
        'tarpbuilder_plus_options',
        'tarpbuilder_plus_options_section',
        array( 
            'type'        => 'checkbox',
            'option_group' => 'tarpbuilder_plus_options', 
            'name'        => 'tarpbuilder_plus_wndmatch_field',
            'label_for'   => 'tarpbuilder_plus_wndmatch_field',
            'value'       => (empty(get_option('tarpbuilder_plus_options')['tarpbuilder_plus_wndmatch_field']))
            ? 0 : get_option('tarpbuilder_plus_options')['tarpbuilder_plus_wndmatch_field'],
            'description' => __( 'Check to have product price the same as increment.', 
                            'tarpbuilder' ),
            'checked'     => (!isset(get_option('tarpbuilder_plus_options')['tarpbuilder_plus_wndmatch_field']))
                            ? 0 : get_option('tarpbuilder_plus_options')['tarpbuilder_plus_wndmatch_field'],
                                    
            'tip'         => __( 'All this really does is count the number of increments 
            the person selected in the product page and subtracts one (day) from the increments so 
            the first (day) is the price of the product. You still must add fees price to product data.', 'tarpbuilder' ) 
            )
    );
    /* **************** CONTENT HELPERS ***************** */
    
    // c12.) settings 
    add_settings_field(
        'tarpbuilder_content_before_html',
        esc_attr__('Before Content', 'tarpbuilder'),
        'tarpbuilder_content_before_html_cb',
        'tarpbuilder_content_options',
        'tarpbuilder_content_options_section',
        array(
            'option_group' => 'tarpbuilder_content_options', 
            'name'         => 'tarpbuilder_content_before_html',
            'value'        => (empty(get_option( 'tarpbuilder_content_options' )['tarpbuilder_content_before_html']))
                                ? '' : get_option( 'tarpbuilder_content_options' )['tarpbuilder_content_before_html'],
            'description'  => __( 'HTML or arbittrary text before the quantity field.', 'tarpbuilder' ),
            'tip'          => __( '', 'tarpbuilder' ),
        )
    );
    // c13.) settings 
    add_settings_field(
        'tarpbuilder_content_after_html',
        esc_attr__('After Content HTML', 'tarpbuilder'),
        'tarpbuilder_content_after_html_cb',
        'tarpbuilder_content_options',
        'tarpbuilder_content_options_section',
        array(
            'option_group' => 'tarpbuilder_content_options', 
            'name'         => 'tarpbuilder_content_after_html',
            'value'        => (empty(get_option( 'tarpbuilder_content_options' )['tarpbuilder_content_after_html']))
                                ? '' : get_option( 'tarpbuilder_content_options' )['tarpbuilder_content_after_html'],
            'description'  => __( 'HTML or arbittrary text after the quantity field or datepicker.', 'tarpbuilder' ),
            'tip'          => __( '', 'tarpbuilder' ),
        )
    );
    /* **************** INSTRUCTION SETTINGS **************** */    
    // c10.) instructions settings 
    add_settings_field(
        'tarpbuilder_admin_help_content',
        esc_attr__('TarpBuilder Support and Instructions', 'tarpbuilder'),
        'tarpbuilder_admin_help_content_field',
        'tarpbuilder_admin_help_content',
        'tarpbuilder_admin_help_content_section',
        array( 
            'option_group' => 'tarpbuilder_admin_help_content', 
            'id'           => 'tarpbuilder_admin_help_content_page'
        )
    );

} 

/**
 ** Section Callbacks
 *  $id, $title, $callback, $page
 */
// section heading cb
function tarpbuilder_plus_options_section_cb()
{    
printf( '<p class="untzr-sect">%s <span> %s</span> %s</p>',
        esc_attr__( 'Version: ', 'tarpbuilder' ),
        TARPBUILDER_PLUS_VER,
        esc_html__( 'Hover mouse over Circle gears for more information.', 'tarpbuilder' ) 
        . ' | <em class="untzr-em">* </em>' . esc_html( ' Means individual products can override field.', 'unitzr' )
    );
} 
// section content helpers
function tarpbuilder_content_options_cb()
{
    echo esc_html__( 'Add content before and after the TarpBuilder single product fields', 'tarpbuilder' );
}
// section heading cb
function tarpbuilder_admin_help_content_cb()
{    
    print( "TarpBuilder is created by Tradesouthwest in Phoenix, AZ" );
} 
