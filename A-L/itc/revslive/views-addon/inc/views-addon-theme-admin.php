<?php
/**
 * Prevent direct access to the file.
 * @subpackage views-addon/inc/views-addon-theme-admin.php
 * @since 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * views_addon Options Page
 *
 * Add options page for the plugin.
 *
 * @since 1.0
 */
function views_addon_custom_plugin_page() {

	add_options_page(
		__( 'Views Addon Options', 'views-addon' ),
		__( 'Views Addon TSW', 'views-addon' ),
		'manage_options',
		'views-addon',
		'views_addon_render_admin_page'
	);

}
add_action( 'admin_menu', 'views_addon_custom_plugin_page' );
add_action( 'admin_init', 'views_addon_register_admin_options' ); 
/**
 * Register settings for options page
 *
 * @since    1.0.0
 * 
 * a.) register all settings groups
 * Register Settings $option_group, $option_name, $sanitize_callback 
 */
function views_addon_register_admin_options() 
{
    
    register_setting( 'views_addon_options', 'views_addon_options' );
        
    //add a section to admin page
    add_settings_section(
        'views_addon_options_settings_section',
        '',
        'views_addon_options_settings_section_callback',
        'views_addon_options'
    );
    add_settings_field(
        'views_addon_heading_h1',
        __( 'Reserved Text', 'views-addon' ),
        'views_addon_heading_h1_cb',
        'views_addon_options',
        'views_addon_options_settings_section',
        array( 
            'type'        => 'text',
            'option_name' => 'views_addon_options', 
            'name'        => 'views_addon_heading_h1',
            'value'       => ( empty( get_option('views_addon_options')['views_addon_heading_h1'] )) 
                            ? '' : get_option('views_addon_options')['views_addon_heading_h1'],
            'default'     => '',
            'description' => esc_html__( 'Text Below Title of a Restricted Listing', 'views-addon' ),
            'tip'         => esc_attr__( 'The text to display just below title of a reserved assigned listing.', 'views-addon' )
        ) 
    );
    add_settings_field(
        'views_addon_heading_h2',
        __( 'Request Text', 'views-addon' ),
        'views_addon_heading_h2_cb',
        'views_addon_options',
        'views_addon_options_settings_section',
        array( 
            'type'        => 'text',
            'option_name' => 'views_addon_options', 
            'name'        => 'views_addon_heading_h2',
            'value'       => ( empty( get_option('views_addon_options')['views_addon_heading_h2'] )) 
                            ? '' : get_option('views_addon_options')['views_addon_heading_h2'],
            'default'     => '',
            'description' => esc_html__( 'Info above Request form', 'views-addon' ),
            'tip'         => esc_attr__( 'Try: To view the files and attachments, please fill out form below', 'views-addon' )
        ) 
    );    
    add_settings_field(
        'views_addon_linkout',
        __( 'Link Out', 'views-addon' ),
        'views_addon_linkout_cb',
        'views_addon_options',
        'views_addon_options_settings_section',
        array( 
            'type'        => 'text',
            'option_name' => 'views_addon_options', 
            'name'        => 'views_addon_linkout',
            'value'       => ( empty( get_option('views_addon_options')['views_addon_linkout'] )) 
                            ? '' : get_option('views_addon_options')['views_addon_linkout'],
            'default'     => '',
            'description' => esc_html__( 'Request Page URL. Page that contains shortcode &#91;request_link]', 'views-addon' ),
            'tip'         => esc_attr__( 'This would be the full url for the page containing the request shortcode. copy/paste from address bar', 'views-addon' )
        ) 
    );    
    add_settings_field(
        'views_addon_request_emails',
        __( 'Email to Send Requests To', 'views-addon' ),
        'views_addon_request_emails_cb',
        'views_addon_options',
        'views_addon_options_settings_section',
        array( 
            'type'        => 'text',
            'option_name' => 'views_addon_options', 
            'name'        => 'views_addon_request_emails',
            'value'       => ( empty( get_option('views_addon_options')['views_addon_request_emails'] )) 
                            ? '' : get_option('views_addon_options')['views_addon_request_emails'],
            'default'     => '',
            'description' => esc_html__( 'Enter eMail of user with Admin or Editor role.', 'views-addon' ),
            'tip'         => esc_attr__( 'This would be the email you want to recieve all Request Form entries to. Multiple emails must be separated by a comma. Leave blank to send to site Admin only.', 'views-addon' )
        ) 
    );
    add_settings_field(
        'views_addon_evalcf7_formused',
        __( 'Evaluation Form Shortcode', 'views-addon' ),
        'views_addon_evalcf7_formused_cb',
        'views_addon_options',
        'views_addon_options_settings_section',
        array( 
            'type'        => 'text',
            'option_name' => 'views_addon_options', 
            'name'        => 'views_addon_evalcf7_formused',
            'value'       => ( empty ( get_option('views_addon_options')['views_addon_evalcf7_formused'] )) 
                      ? '' : esc_attr( get_option('views_addon_options')['views_addon_evalcf7_formused'] ),
            'default'     => '',
            'description' => esc_html__( 'Shortcode of CF7 form usign for evaluation form', 'views-addon' ),
            'tip'         => esc_attr__( 'The shortcode should be entered here in the same format as you would use it on a page.', 'views-addon' )
        ) 
    );
    add_settings_field(
        'views_addon_eval_notice',
        __( 'Evaluation Form Notice', 'views-addon' ),
        'views_addon_eval_notice_cb',
        'views_addon_options',
        'views_addon_options_settings_section',
        array( 
            'type'        => 'text',
            'option_name' => 'views_addon_options', 
            'name'        => 'views_addon_eval_notice',
            'value'       => ( empty( get_option('views_addon_options')['views_addon_eval_notice'] )) 
                            ? '' : get_option('views_addon_options')['views_addon_eval_notice'],
            'default'     => '',
            'description' => esc_html__( 'Text before evaluation form', 'views-addon' ),
            'tip'         => esc_attr__( 'The text to display just above the contact form used for judges evaluation. Tip: Leave blank and add text into the contact form editor.', 'views-addon' )
        ) 
    );

    // views_addon_assigned_password @since 1.0.2
    /* 
    add_settings_field(
        'views_addon_assigned_password',
        __( 'Assigned Password Base', 'views-addon' ),
        'views_addon_assigned_password_cb',
        'views_addon_options',
        'views_addon_options_settings_section',
        array( 
            'type'        => 'text',
            'option_name' => 'views_addon_options', 
            'name'        => 'views_addon_assigned_password',
            'value'       => ( empty( get_option('views_addon_options')['views_addon_assigned_password'] )) 
                            ? '' : get_option('views_addon_options')['views_addon_assigned_password'],
            'default'     => '',
            'description' => esc_html__( 'Generate a BASE password for new judges. Limit 20 chars', 'views-addon' ),
            'tip'         => esc_attr__( 'This would be the base of a password which is followed by random Letters and/or Numbers.', 'views-addon' )
        ) 
    ); */
    /*
    add_settings_field(  
        'views_addon_checkbox_allow',  
        'Remove Plugin Stylesheet',  
        'views_addon_checkbox_allow_callback',  
        'views_addon_options',
        'views_addon_options_settings_section',
    ); */
}


/** 
 * render for '0' field
 * @since 1.0.2
 */
function views_addon_checkbox_allow_callback() {
    $options = get_option( 'views_addon_options' );
    $html = '<input type="checkbox" id="views_addon_options" 
    name="views_addon_options[views_addon_checkbox_allow]" value="1"' 
    . checked( 1, $options['views_addon_checkbox_allow'], false ) . '/>';
    $html .= '<label for="checkbox_example">'. __('Check box to REMOVE this plugin stylesheet. (Use to override hidden listings selectively.)', 'views-addon') .'</label>';
    
    echo $html;
}

/** 
 * render for '0' field
 * @since 1.0.0
 */
function views_addon_heading_h1_cb($args)
{  
    printf( '<fieldset>
    <p><span class="vmarg">%4$s </span><sup class="grctip" data-title="%5$s"><b>?</b></sup></p>
    <input id="%1$s" class="text-field" name="%2$s[%1$s]" type="%6$s" value="%3$s"/>
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
 * render for '0' field
 * @since 1.0.0
 */
function views_addon_heading_h2_cb($args)
{  
    printf( '<fieldset>
    <p><span class="vmarg">%4$s </span><sup class="grctip" data-title="%5$s"><b>?</b></sup></p>
    <input id="%1$s" class="text-field" name="%2$s[%1$s]" type="%6$s" value="%3$s"/>
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
 * render for '0' field
 * @since 1.0.0
 */
function views_addon_request_emails_cb($args)
{  
    printf( '<fieldset>
    <p><span class="vmarg">%4$s </span><sup class="grctip" data-title="%5$s"><b>?</b></sup></p>
    <input id="%1$s" class="text-field" name="%2$s[%1$s]" type="%6$s" value="%3$s"/>
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
 * render for '0' field
 * @since 1.0.0
 */
function views_addon_linkout_cb($args)
{  
    printf( '<fieldset>
    <p><span class="vmarg">%4$s </span><sup class="grctip" data-title="%5$s"><b>?</b></sup></p>
    <input id="%1$s" class="text-field" name="%2$s[%1$s]" type="%6$s" value="%3$s"/>
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
 * render for '0' field
 * @since 1.0.0
 */
function views_addon_evalcf7_formused_cb($args)
{  
    printf( '<fieldset>
    <p><span class="vmarg">%4$s </span><sup class="grctip" data-title="%5$s"><b>?</b></sup></p>
    <input id="%1$s" class="text-field" name="%2$s[%1$s]" type="%6$s" value="%3$s" />
    </fieldset>',
        $args['name'],
        $args['option_name'],
        esc_attr( $args['value'] ),
        $args['description'],
        $args['tip'],
        $args['type']
    );
}

/** 
 * render for '0' field
 * @since 1.0.0
 */
function views_addon_eval_notice_cb($args)
{  
    printf( '<fieldset>
    <p><span class="vmarg">%4$s </span><sup class="grctip" data-title="%5$s"><b>?</b></sup></p>
    <input id="%1$s" class="text-field" name="%2$s[%1$s]" type="%6$s" value="%3$s"/>
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
 * render for '0' field
 * @since 1.0.0
 */
function views_addon_assigned_password_cb($args)
{  
    printf(
    '<fieldset><p><span class="vmarg">%4$s </span><sup class="grctip" data-title="%5$s"><b>?</b></sup></p>
    <input id="%1$s" class="text-field" name="%2$s[%1$s]" type="%6$s" value="%3$s"/>
    </fieldset>',
        $args['name'],
        $args['option_name'],
        $args['value'],
        $args['description'],
        $args['tip'],
        $args['type']
    );
}

//callback for description of options section
function views_addon_options_settings_section_callback() 
{
	echo '<h2>' . esc_html__( 'TSW views-addon', 'views-addon' ) . '</h2>
    <p>' . esc_html__( 'This plugin requires the following plugins to be installed: 
    Business Directory Plugin By Business Directory Team. --- Instructions below settings', 'views-addon' ) . '</p>';
}

// display the plugin settings page
function views_addon_render_admin_page()
{
	// check if user is allowed access
    if ( ! current_user_can( 'manage_options' ) ) return;
    
	print( '<div class="wrap views-addon-wrap">
    <form action="options.php" method="post">' );

	// output security fields
	settings_fields( 'views_addon_options' );

	// output setting sections
	do_settings_sections( 'views_addon_options' );
	submit_button();

print( '</form></div>' ); 

echo '<div class="wrap">
<hr>
<h3>' . esc_html__( 'Instructions', 'views-addon' ) .'</h3>
<ul>
<li><h4>' . esc_html__( 'Views available to listings:', 'views-addon' ) .'</h4></li>
<li>' . __( '<strong><em>Assigned View</em></strong> - If current user is <strong>logged in</strong> and they are <em>the</em> Assigned Judge then they see everything in that assigned listing.', 'views-addon' ) .'</li>
<li>' . __( '<strong><em>Reserved View</em></strong> - If current user is logged in <strong>and they are NOT</strong> the Assigned Judge then they see a blank page with only the title and the tags (give opportunity to search for similar listing in same category.)', 'views-addon' ) .'</li>
<li>' . __( '<strong><em>Restricted View</em></strong> - If any listing <strong>is assigned</strong> to a judge, contents will never be seen by public. (No one see attachments/scripts without being assigned to play)', 'views-addon' ) .'</li>
</ul>
<hr>
<ul>
<li><strong>' . esc_html__( 'General Author Flow Overview:', 'views-addon' ) .'</strong></li>
<li>' . esc_html__( '1. Playwright submits anonymous form using Form from', 'views-addon' ) .' <b>Directory > Form Fields</b></li>
<li>' . esc_html__( '2. Admin recieves email of request and clicks on access link to authorize play.', 'views-addon' ) .' <em>Values sent from above form.</em></li>
<li>' . esc_html__( '3. Authorization, via admin email link, adds play to public view.', 'views-addon' ) .'</li>
<li><strong>' . esc_html__( '', 'views-addon' ) . '</strong></li>
<li></li>
</ul>
<hr>
<ul>
<li><strong>' . esc_html__( 'General Judge Flow Overview:', 'views-addon' ) .'</strong></li>
<li>' . esc_html__( '1. Judge requests play via form at bottom of play listing using Form from', 'views-addon' ) .' <b>Directory > Settings > Email > Templates: Listing Contact Message</b></li>
<li>' . esc_html__( '2. Admin recieves email of request and clicks on access link to authorize request.', 'views-addon' ) .' <em>Values sent from above form.</em></li>
<li>' . esc_html__( '3. Authorization can lock play from public view by assigning requesting-judge to Listing in the Publish Submit side-box.', 'views-addon' ) .' <em>Metabox is the Publish box in admin listing editor.</em></li>
<li><strong>' . esc_html__( 'Plays do not have to be made private. Judges eMail is used to make private automatically.', 'views-addon' ) . '</strong></li>
<li></li>
</ul>
<ul>
<li>';
printf( '<h4>%s</h4><p>%s <a href="%s" target="_blank" title="%s">%s</a> <img src="%s" title="opens in new tab" alt="opens in new tab" height="14" /></p>',
esc_html__( 'Tips', 'views-addon' ),
esc_html__( 'To validate your work visit', 'views-addon' ),
esc_url( 'http://www.css-validator.org/' ),
esc_attr__( 'css-validator.org', 'views-addon' ),
esc_html__( 'css-validator.org', 'views-addon' ),
esc_url( VIEWS_ADDON_URL . 'inc/external-link.png' )
);
echo '</li>
<li>Flow of Events: https://whimsical.com/itc-flow-of-events-WUfsKo3HtGf3JoRDvmu1Y9</li>
<li>' . esc_html__( 'To change Terms and Conditions link or text use the "itctheme_contact_form_extra_fields" function located in the child theme functions.php file.', 'views-addon' ) . '</li>
</ul>
<p>' . esc_html__( 'Plugin author: Larry Judd via Codeable.io', 'views-addon' ) .'</p>
</div>';
}
