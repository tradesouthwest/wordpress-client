<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'readlimit_add_options_page' ); 
add_action( 'admin_init', 'readlimit_register_admin_options' ); 
//create an options page
function readlimit_add_options_page() 
{
   add_submenu_page(
       'options-general.php',
        esc_html__( 'Read Limit', 'readlimit' ),
        esc_html__( 'Read Limit', 'readlimit' ),
        'manage_options',
        'readlimit',
        'readlimit_options_page',
        'dashicons-admin-tools' 
    );
}   
		
/**
 * @param readlimit_user_id        int        
 * @param readlimit_rcp_id,        int
 * @param readlimit_login_time,    string Last login when read
 * @param readlimit_article_read,  int    post_id
 * @param readlimit_readlimits,      int    epoch time limits
 * @param readlimit_rtdtitle,       string Expirey message
 * @param readlimit_weekly_reset   string Plugin options 
 */
 
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
function readlimit_register_admin_options() 
{
    //options pg
    register_setting( 'readlimit_options', 'readlimit_options' );
	/**
	 * b1.) options section
	 */        
	add_settings_section(
		'readlimit_options_section',
		esc_html__( 'Configuration and Settings', 'readlimit' ),
		'readlimit_options_section_cb',
		'readlimit_options'
	); 
		// c1.) settings 
	add_settings_field(
		'readlimit_rtdtitle',
		esc_attr__('Notice for Expired Article', 'readlimit'),
		'readlimit_rtdtitle_cb',
		'readlimit_options',
		'readlimit_options_section',
		array( 
			'type'         => 'textarea',
			'option_group' => 'readlimit_options', 
			'name'         => 'readlimit_rtdtitle',
			'value'        => (empty( get_option('readlimit_options' )['readlimit_rtdtitle'] )) 
								? 'expired' : esc_attr(get_option('readlimit_options')['readlimit_rtdtitle']),
			'description'  => esc_html__( 'Generic message for inline notices.', 'readlimit' ),
			'tip'          => esc_html__( 'Try: Article read time expired.', 'readlimit' ),
            'message' => esc_html__( 'Changes made to setting. Don\'t forget to save.', 'readlimit' )
		)
	);  
        // c6.) settings 
    add_settings_field(
        'readlimit_readlimits',
        esc_attr__('Time Limit Options', 'readlimit'),
        'readlimit_readlimits_cb',
        'readlimit_options',
        'readlimit_options_section',
         array(
            'type' => 'select',
            'option_group' => 'readlimit_options', 
            'name'         => 'readlimit_readlimits',
            'value'        => esc_attr( 
                              get_option( 'readlimit_options' )['readlimit_readlimits'] ),
            'options'      => array(
                                  "3600"  => "24 hours", 
                                  "7200"  => "48 hours", 
                                  "10800" => "72 hours" ),
            'description'  => esc_html__( 'This adjusts the Access time to read articles.', 'readlimit' ),
            'tip'  => esc_attr__( 'Choices are: ', 'readlimit' ),
            'message' => __( 'Changes made to setting. Don\'t forget to save.', 'readlimit' )
        )
    );
            // c6.) settings 
    add_settings_field(
        'readlimit_cutoffday',
        esc_attr__('Cut Off Day', 'readlimit'),
        'readlimit_cutoffday_cb',
        'readlimit_options',
        'readlimit_options_section',
         array(
            'type' => 'select',
            'option_group' => 'readlimit_options', 
            'name'         => 'readlimit_cutoffday',
            'value'        => esc_attr( 
                              get_option( 'readlimit_options' )['readlimit_cutoffday'] ),
            'options'      => array(
                                    "Sunday"    => "Sunday",
                                    "Monday"    => "Monday", 
                                    "Tuesday"   => "Tuesday", 
                                    "Wednesday" => "Wednesday",
                                    "Thursday"  => "Thursday",
                                    "Friday"    => "Friday",
                                    "Saturday"  => "Saturday" ),
            'description'  => esc_html__( 'This adjusts the Access time ending day.', 'readlimit' ),
            'tip'  => esc_attr__( 'Choices are: Days of the Week', 'readlimit' ),
            'message' => __( 'Changes made to setting. Don\'t forget to save.', 'readlimit' )
        )
    );
	
}

/* ====================== FIELDS ====================== */
/** c.1) Add fields 
 * name for 'label' field
 * @since 1.0.0
 */
function readlimit_rtdtitle_cb($args)
{  
   printf(
        '<textarea name="%2$s[%3$s]" id="%2$s-%3$s"  class="regular-text wide" columns="50" 
        value="%4$s"></textarea><strong class="wntip" title="%6$s">?</strong>
        <br><span class="wndspan">%5$s </span> <span class="inline-mezzage" style="">%7$s</span>',
        $args['type'],
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description'],
        $args['tip'],
        $args['message']
    );
}
/** 
 * 'select' field
 * @since 1.0.0
 */
function readlimit_readlimits_cb($args)
{  
  print('<label for="readlimit_readlimits">');
    if( ! empty ( $args['options'] && is_array( $args['options'] ) ) )
    { 
    $options_markup = '';
    $value = $args['value'];
        foreach( $args['options'] as $key => $label )
        {
            $options_markup .= sprintf( '<option value="%s" %s>%s</option>', 
            $key, selected( $value, $key, false ), $label );
        }
        printf( '<select id="%1$s-%2$s" class="regular-text" name="%1$s[%2$s]" >%3$s</select>',  
        $args['option_group'],
        $args['name'],
        $options_markup );
    }
        $tip = $args['tip']; $message = $args['message'];
        print('<strong class="wntip" title="' . esc_attr($tip) . '">?</strong>
        <span class="inline-mezzage">'. esc_html($message) . '</span></label>'); 
}
/** 
 * 'select' field
 * @since 1.0.0
 */
function readlimit_cutoffday_cb($args)
{  
  print('<label for="readlimit_cutoffday">');
    if( ! empty ( $args['options'] && is_array( $args['options'] ) ) )
    { 
    $options_markup = '';
    $value = $args['value'];
        foreach( $args['options'] as $key => $label )
        {
            $options_markup .= sprintf( '<option value="%s" %s>%s</option>', 
            $key, selected( $value, $key, false ), $label );
        }
        printf( '<select id="%1$s-%2$s" class="regular-text" name="%1$s[%2$s]" >%3$s</select>',  
        $args['option_group'],
        $args['name'],
        $options_markup );
    }
        $tip = $args['tip']; $message = $args['message'];
        print('<strong class="wntip" title="' . esc_attr($tip) . '">?</strong>
        <span class="inline-mezzage">'. esc_html($message) . '</span></label>'); 
}

/**
 ** Section Callbacks
 *  $id, $title, $callback, $page
 */
// section heading cb
function readlimit_options_section_cb()
{    
    echo '<h4>'. esc_html__( 'Readlimit Information and Configuration Instructions', 
    'readlimit' ) .'</h4>'; 
    $readlimit_date = get_option( 'readlimit_date_plugin_activated' ); 
    echo '<p>' . esc_html__( 'This plugin last activated on: ', 
    'readlimit' ) . '<code>'. esc_html($readlimit_date) .'</code></p>';
	echo '<p>Ver.: ' . READLIMIT_VER . '</p>';
} 


// d.) render admin page
function readlimit_options_page() 
{
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) return;
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    ?>
    <div class="wrap wrap-readlimit-admin">
    
    <h1><span id="SlwOptions" class="dashicons dashicons-admin-tools"></span> 
    <?php echo esc_html( 'Read Limit Options', 'readlimit' ); ?></h1>
         
    <form id="ReadLimitForm" action="options.php" method="post">
    <?php //page=readlimit&tab=readlimit_options
        settings_fields( 'readlimit_options' );
        do_settings_sections( 'readlimit_options' ); 
        
        submit_button( 'Save Settings' ); 

    ?>
    </form>
	<h2><?php esc_html_e( 'Additional Information', 'readlimit' ); ?></h2>
	<p><?php esc_html_e( 'There are these standard types of CPTs to include:', 'readlimit' ); ?></p>
    <h6><?php esc_html_e( 'Newsletter', 'readlimit' ); ?></h6>
	<h4><?php esc_html_e( 'Currently active: ', 'readlimit' ); ?></h4>
	<footer><?php 
	// further instructions 
    if( function_exists( 'readlimit_access_check_subscriber_status' ) ) : 

        readlimit_access_check_subscriber_status();

    endif;
    // test values only
    $rcp_expiration = get_user_meta( 1, 'rcp_expiration', true );
    $rcp_subscription_length = rcp_calculate_subscription_expiration( 2 );
    $new_expr_date = date( "Y-m-d 23:59:59", strtotime('next Sunday') );
    $new_expr_epoc = strtotime('next Sunday');
    echo '<p><b>Admin Vals</b> expnew_formatted ' . $new_expr_date . ' epocnew ' .$new_expr_epoc . ' um rcp_ex ' . $rcp_expiration . ' sub_lngth ' . $rcp_subscription_length . '</p>';
	?></footer>
    </div>
	<?php 
} 