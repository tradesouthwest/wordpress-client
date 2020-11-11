<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    meprprorata
 * @subpackage meprprorata/inc
 * @author     Larry Judd <tradesouthwest@gmail.com>
 * TODO add a field in the order table (admin side)
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'meprprorata_add_options_page' ); 
add_action( 'admin_init', 'meprprorata_register_admin_options' ); 

//create an options page
function meprprorata_add_options_page() 
{
   add_submenu_page(
       'tools.php',
        esc_html__( 'MemProRate', 'meprprorata' ),
        esc_html__( 'MemProRata', 'meprprorata' ),
        'manage_options',
        'meprprorata',
        'meprprorata_options_page',
        'dashicons-admin-tools' 
    );
}   
 
/** a.) Register new settings
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

// a.) register all settings groups
function meprprorata_register_admin_options() 
{
    //options pg
    register_setting( 'meprprorata_options', 'meprprorata_options' );
     

/**
 * b1.) options section
 */        
    add_settings_section(
        'meprprorata_options_section',
        esc_html__( 'Text Boxes and Options', 'meprprorata' ),
        'meprprorata_options_section_cb',
        'meprprorata_options'
    ); 
        // c.) settings 
    add_settings_field(
        'meprprorata_cstitle_field',
        esc_attr__('Label for Expiry Date', 'meprprorata'),
        'meprprorata_cstitle_field_cb',
        'meprprorata_options',
        'meprprorata_options_section',
        array( 
            'type'         => 'text',
            'option_group' => 'meprprorata_options', 
            'name'         => 'meprprorata_cstitle_field',
            'value'        => 
            esc_attr( get_option( 'meprprorata_options' )['meprprorata_cstitle_field'] ),
            'description'  => esc_html__( 'Shows next to renewal column.', 'meprprorata' )
        )
    );
    // c.) settings 
    add_settings_field(
        'meprprorata_expire_grace_field',
        esc_attr__('Number Days to Grace Period', 'meprprorata'),
        'meprprorata_expire_grace_field_cb',
        'meprprorata_options',
        'meprprorata_options_section',
        array( 
            'type'         => 'number',
            'option_group' => 'meprprorata_options', 
            'name'         => 'meprprorata_expire_grace_field',
'value' => empty(( get_option( 'meprprorata_options' )['meprprorata_expire_grace_field'] )) 
           ? '0' : get_option( 'meprprorata_options' )['meprprorata_expire_grace_field'], 
            'description'  => esc_html__( 'Format = 30', 'meprprorata' )
        )
    );
    // c.) settings 
    add_settings_field(
        'meprprorata_csdescription_field',
        esc_attr__('Grace Period Text', 'meprprorata'),
        'meprprorata_csdescription_field_cb',
        'meprprorata_options',
        'meprprorata_options_section',
        array( 
            'type'         => 'text',
            'option_group' => 'meprprorata_options', 
            'name'         => 'meprprorata_csdescription_field',
            'value'        => 
            esc_attr( get_option( 'meprprorata_options' )['meprprorata_csdescription_field'] ),
            'description'  => esc_html__( 'Show just after last field.', 'woocommerce' )
        )
    );

    // c.) settings 
    add_settings_field(
        'meprprorata_display_grace_field',
        esc_attr__('Message Displays x Days Before Grace', 'meprprorata'),
        'meprprorata_display_grace_field_cb',
        'meprprorata_options',
        'meprprorata_options_section',
        array( 
            'type'         => 'number',
            'option_group' => 'meprprorata_options', 
            'name'         => 'meprprorata_display_grace_field',
'value' => empty(( get_option( 'meprprorata_options' )['meprprorata_display_grace_field'] ) )
           ? '35' : get_option( 'meprprorata_options' )['meprprorata_display_grace_field'], 
            'description'  => esc_html__( 'Number of days prior to start showing message. Format = 37', 'meprprorata' )
        )
    );
    add_settings_field(
        'meprprorata_expiry_price_field',
        esc_attr__( 'expiry_price' ),
        'meprprorata_expiry_price_field_cb',
        'meprprorata_options',
        'meprprorata_options_section',
        array( 
            'type'         => 'text',
            'option_group' => 'meprprorata_options', 
            'name'         => 'meprprorata_expiry_price_field',
'value' => empty(( get_option( 'meprprorata_options' )['meprprorata_expiry_price_field'] ) )
           ? '20.00' : get_option( 'meprprorata_options' )['meprprorata_expiry_price_field'], 
            'description'  => esc_html__( 'Overdue cost. Format = 20', 'meprprorata' )
        )
    ); /*
    add_settings_field(
        'meprprorata_expiry_ids_field',
        esc_attr__( 'expiry_ids' ),
        'meprprorata_expiry_ids_field_cb',
        'meprprorata_options',
        'meprprorata_options_section',
        array( 
            'type'         => 'text',
            'option_group' => 'meprprorata_options', 
            'name'         => 'meprprorata_expiry_ids_field',
'value' => empty(( get_option( 'meprprorata_options' )['meprprorata_expiry_ids_field'] ) )
           ? '' : get_option( 'meprprorata_options' )['meprprorata_expiry_ids_field'], 
            'description'  => esc_html__( 'Overdue cost. Format = 20', 'meprprorata' )
        )
    );   */  
} 

/** 
 * name for 'branding' field
 * @since 1.0.0
 */
function meprprorata_cstitle_field_cb($args)
{  
   printf(
        '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" 
        value="%4$s" class="regular-text" />
        <span>%5$s <b class="wntip" title="tip"> ? </b></span>',
        $args['type'],
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description']
    );
}

/** 
 * year for 'year_expire'field
 * @since 1.0.0
 */
function meprprorata_expire_year_field_cb($args)
{  
   printf(
        '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" 
        value="%4$s" class="regular-text" />
        <span>%5$s <b class="wntip" title="tip"> ? </b></span>',
        $args['type'],
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description']
    );
}

/** 
 * num field
 * @since 1.0.0
 */
function meprprorata_expire_grace_field_cb($args)
{  
   printf(
        '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" 
        value="%4$s" class="regular-text" />
        <span>%5$s <b class="wntip" title="tip"> ? </b></span>',
        $args['type'],
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description']
    );
}

/** 
 * num days prior to display field
 * @since 1.0.0
 */
function meprprorata_display_grace_field_cb($args)
{  
   printf(
        '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" 
        value="%4$s" class="regular-text" />
        <span>%5$s <b class="wntip" title="tip"> ? </b></span>',
        $args['type'],
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description']
    );
}

/** 
 * name for 'branding' field
 * @since 1.0.0
 */
function meprprorata_csdescription_field_cb($args)
{  
   printf(
        '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" 
        value="%4$s" class="regular-text" />
        <span>%5$s <b class="wntip" title="tip"> ? </b></span>',
        $args['type'],
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description']
    );
}   
/** 
 * num field
 * @since 1.0.0
 */
function meprprorata_expiry_price_field_cb($args)
{  
   printf(
        '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" 
        value="%4$s" class="regular-text" />
        <span>%5$s <b class="wntip" title="tip"> ? </b></span>',
        $args['type'],
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description']
    );
}
/** 
 * name for 'branding' field
 * @since 1.0.0
 */
function meprprorata_expiry_ids_field_cb($args)
{  
   printf(
        '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" 
        value="%4$s" class="regular-text" />
        <span>%5$s <b class="wntip" title="tip"> ? </b></span>
        <p>%6$s</p>',
        $args['type'],
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description'],
        __( 'Insert ID numbers enclosed in apostrophes and separated by commas with NO comma at the end of the last number like this: &#39;1100&#39;, &#39;1101&#39;, &#39;1105&#39; ' ) 
    );
}   

//Query all memeberships. TODO quantify if parent or applicable type
/*
function meprprorata_list_qualified_products_cb($args)
{
   
    $descript =    $args['description'];    
    $ogroup = $args['option_group'];
    $oname = $args['name'];
    
    $post_type       = 'memberpressproduct';
    $post_type_object = get_post_type_object($post_type);
    $label = $post_type_object->label;
    $posts = get_posts( array( 'post_type'=> $post_type, 
                               'post_status'=> 'publish', 
                               'suppress_filters' => false, 
                               'posts_per_page'=>-1 
                               )
                            );
    echo '<label>'. $descript .' '. $label .' </label>';
    echo '<select name="'. $ogroup .'['. $oname .']" id="'. $ogroup .'-'. $oname .'">';
    echo '<option value = "" >All '.$label.' </option>';
    foreach ($posts as $post) {
$value = get_option('meprprorata_options')['meprprorata_list_qualified_products'];
    echo '<option value="'. $post->ID .'" 
    '. selected( $post->ID, $value ) .'>'. $post->post_title .'</option>';
    }
    echo '</select><br><div id="html-selected-products"></div>';

} */
/**
 * A custom sanitization function that will take the incoming input, and sanitize
 * the input before handing it back to WordPress to save to the database.
 *
 * @since    1.0.0
 *
 * @param    array    $input        The address input.
 * @return   array    $new_input    The sanitized input.
 */
function meprprorata_select_options_sanitize( $input ) {
	// Initialize the new array that will hold the sanitize values
	$new_input = array();
	// Loop through the input and sanitize each of the values
	foreach ( $input as $key => $val ) {
		
		$new_input[ $key ] = ( isset( $input[ $key ] ) ) ?
			sanitize_text_field( $val ) :
			'';
	}
	return $new_input;
}
/**
 ** Section Callbacks
 *  $id, $title, $callback, $page
 */
// section heading cb
function meprprorata_options_section_cb()
{    
print( '<hr>' );
} 


// d.) render admin page
function meprprorata_options_page() 
{
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) return;
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
    // add settings saved message with the class of "updated"
    add_settings_error( 'meprprorata_messages', 'meprprorata_message', 
                        esc_html__( 'Settings Saved', 'meprprorata' ), 'updated' );
    }
    // show error/update messages
    settings_errors( 'meprprorata_messages' );
     
    ?>
    <div class="wrap wrap-meprprorata-admin">
    
    <h1><span id="MPRTOptions" class="dashicons dashicons-admin-tools"></span> 
    <?php echo esc_html( 'Admin' ); ?></h1>
         
    <form action="options.php" method="post">
    <?php //page=meprprorata&tab=meprprorata_options
        settings_fields( 'meprprorata_options' );
        do_settings_sections( 'meprprorata_options' ); 
        
        submit_button( 'Save Settings' ); 
 
    ?>
    </form>
    <h4>Instructions</h4>
    <dl>
    <dt>Expires On</dt>
    <dd>Expiry date is using the expire date of the actual Membership or Subscription. This is why you do not see a choice for such on this page.</dd>
    <dt>Required Files</dt>
<dd>The 'subscriptions.php' file will go into your child-theme directory:<dd>
<dd>ex.: 'wp-content/themes/mytheme/memberpress/account/subscriptions.php'</dd>
<dd>Chances are that you already have this file installed, if this is a custom install.
Otherwise the plugin will not work correctly if you move the site or change themes and do not have the correct subscriptions.php file in your theme or child theme folder.</dd>
    </dl>
    <p>Required changes to account/subscriptions.php and checkout/form.php </p>
    </div>
<?php 
}