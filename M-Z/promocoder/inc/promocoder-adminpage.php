<?php
/**
 * Promocoder admin setting page
 * @since 1.0.0
 */  
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'admin_menu', 'promocoder_add_admin_menu' );
add_action( 'admin_init', 'promocoder_settings_init' );
/**
 * Add the top level menu page.
 * @param $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position
 */
function promocoder_add_admin_menu() {

    add_menu_page(  
    __( 'PromoCoder Settings', 'promocoder'), 
    __( 'Promo Coder', 'promocoder'),
    'manage_options', 
    'promocoder', 
    'promocoder_options_page'
    );

}

//settings and the options for admin
function promocoder_settings_init( ) {

    register_setting( 'promocoder', 'promocoder_settings' );

    add_settings_section(
        'promocoder_promocoder_section',
        __( 'Promocoder Curated List', 'promocoder' ),
        'promocoder_settings_section_callback',
        'promocoder'
    );
    //rediradmin_dropdown
    add_settings_field(
        'promocoder_privatize_dropdown',
        __( 'Privatize Page', 'promocoder' ),
        'promocoder_privatize_dropdown_render',
        'promocoder',
        'promocoder_promocoder_section'
    ); 
    /*add_settings_field(
        'promocoder_checkbox_1',
        __( 'Check to use Short list.', 'promocoder' ),
        'promocoder_checkbox_1_render',
        'promocoder',
        'promocoder_promocoder_section'
    ); */
    add_settings_field(
        'promocoder_color_field_3',
         __( 'Color for List Title', 'promocoder' ),
        'promocoder_color_field_3_render',
        'promocoder',
        'promocoder_promocoder_section'
    );
    add_settings_field(
        'promocoder_color_field_4',
         __( 'Color for List Links', 'promocoder' ),
        'promocoder_color_field_4_render',
        'promocoder',
        'promocoder_promocoder_section'
    ); 
    add_settings_field(
        'promocoder_color_field_5',
         __( 'Color for Main Background', 'promocoder' ),
        'promocoder_color_field_5_render',
        'promocoder',
        'promocoder_promocoder_section'
    ); 
    
    add_settings_field(
        'promocoder_text_field_0',
        __( 'What Page is shortcode &#39;promocoder_list&#39; on?', 'promocoder' ),
        'promocoder_text_field_0_render',
        'promocoder',
        'promocoder_promocoder_section'
    ); 
    //rediradmin_dropdown
    add_settings_field(
        'promocoder_rediradmin_dropdown',
        __( 'What Page is shortcode &#39;promocoder_submit_post&#39; on?', 'promocoder' ),
        'promocoder_rediradmin_dropdown_render',
        'promocoder',
        'promocoder_promocoder_section'
    ); 
    add_settings_field(
        'promocoder_checkbox_rediradmin',
        __( 'Redirect on Login', 'promocoder' ),
        'promocoder_checkbox_rediradmin_render',
        'promocoder',
        'promocoder_promocoder_section'
    ); 
}

/**
 * Render the branding colors option
 * @string $def = default color
 * @since  1.0.3
 */
function promocoder_color_field_3_render() 
{ 
    
    $def = "#002e63";
    $options = get_option('promocoder_settings'); 
    $promocoder_color_3 = (empty( $options['promocoder_color_field_3'] ) ) ?  
    $promocoder_color_3 = $def : 
    $promocoder_color_3 = $options['promocoder_color_field_3'];
    ?>
    <label class="olmin">
        <?php esc_html_e( 'Select color for title.', 'promocoder'  ); ?></label>
    <input type="text" 
           id="color_wrap" 
           name="promocoder_settings[promocoder_color_field_3]"
           class="promocoder-color-field" data-default-color="#002e63"
           value="<?php echo $promocoder_color_3; ?>"><br>
<?php     
}
/**
 * Render the branding colors option
 * @string $def = default color
 * @since  1.0.3
 */
function promocoder_color_field_4_render() 
{ 
    
    $def = "#3388bb";
    $options = get_option('promocoder_settings'); 
    $promocoder_color_4 = (empty( $options['promocoder_color_field_4'] ) ) ?  
    $promocoder_color_4 = $def : 
    $promocoder_color_4 = $options['promocoder_color_field_4'];
    ?>
    <label class="olmin">
        <?php esc_html_e( 'Select color for link.', 'promocoder'  ); ?></label>
    <input type="text" 
           id="color_wrap" 
           name="promocoder_settings[promocoder_color_field_4]"
           class="promocoder-color-field" data-default-color="#3388bb"
           value="<?php echo $promocoder_color_4; ?>"><br>
<?php     
}
/**
 * Render the branding colors option
 * @string $def = default color
 * @since  1.0.3
 */
function promocoder_color_field_5_render() 
{ 
    
    $def = "#fafafa";
    $options = get_option('promocoder_settings'); 
    $promocoder_color_5 = (empty( $options['promocoder_color_field_5'] ) ) ?  
    $promocoder_color_5 = $def : 
    $promocoder_color_5 = $options['promocoder_color_field_5'];
    ?>
    <label class="olmin">
        <?php esc_html_e( 'Select color', 'promocoder'  ); ?></label>
    <input type="text" 
           id="color_wrap" 
           name="promocoder_settings[promocoder_color_field_5]"
           class="promocoder-color-field" data-default-color="#fafafa"
           value="<?php echo $promocoder_color_5; ?>">
           <small><?php esc_html_e( '(Effects both content of single and content of list page)', 
           'promocoder' ); ?></small>
<?php     
}
/**
 * Render the branding colors option
 * @string $def = default color
 * @since  1.0.3
 */
function promocoder_color_field_6_render() 
{ 
    
    $def = "#fafafa";
    $options = get_option('promocoder_settings'); 
    $promocoder_color_6 = (empty( $options['promocoder_color_field_6'] ) ) ?  
    $promocoder_color_6 = $def : 
    $promocoder_color_6 = $options['promocoder_color_field_6'];
    ?>
    <label class="olmin">
        <?php esc_html_e( 'Select color', 'promocoder'  ); ?></label>
    <input type="text" 
           id="color_wrap" 
           name="promocoder_settings[promocoder_color_field_6]"
           class="promocoder-color-field" data-default-color="#fafafa"
           value="<?php echo $promocoder_color_6; ?>"><br>
<?php     
}
/**
 * checkbox for 'use shortcode' field
 * @since 1.0.1
 */
function promocoder_use_formpage_render() {
    $options = get_option('promocoder_settings'); 
    $promocoder_use_formpage = (empty($options['promocoder_use_formpage'] )) 
                          ? 0 : $options['promocoder_use_formpage']; ?>
 <p><input type="hidden" 
           name="promocoder_settings[promocoder_use_formpage]" 
           value="0" />
    <input name="promocoder_settings[promocoder_use_formpage]" 
           value="1" 
           type="checkbox" <?php echo esc_attr( 
           checked( 1, $promocoder_use_formpage, true ) ); ?> /> 	
    <?php esc_html_e( 'Check to not show favicons in the Tall list (Optional)', 'promocoder' ); ?></p>
    <small><?php esc_html_e( 'By removing favicons in your list it saves some bandwidth*', 
    'promocoder' ); ?> </small>
    <?php  
} 
/**
 * checkbox for 'excerpts height' field
 * @since 1.0.1
 */
function promocoder_checkbox_1_render() {
    $options = get_option('promocoder_settings'); 
    $promocoder_checkbox_1 = (empty($options['promocoder_checkbox_1'] )) 
                          ? 0 : $options['promocoder_checkbox_1']; ?>
 <p><input type="hidden" 
           name="promocoder_settings[promocoder_checkbox_1]" 
           value="0" />
    <input name="promocoder_settings[promocoder_checkbox_1]" 
           value="1" 
           type="checkbox" <?php echo esc_attr( 
           checked( 1, $promocoder_checkbox_1, true ) ); ?> /> 	
    <?php esc_html_e( 'Check to Show a lower profile list of links.', 'promocoder' ); ?></p>
    <small><?php esc_html_e( 'Shorter list displays only the title and the link.', 
    'promocoder' ); ?> </small>
    <?php  
}

/**
 * Retrieve or display list of pages as a dropdown (select list).
 * 
 * @uses wp_dropdown_pages
 */
function promocoder_privatize_dropdown_render()
{

global $post;
$label    = '';
$options  = get_option('promocoder_settings'); 
$shpage   = (empty($options['promocoder_privatize_dropdown'] )) 
                          ? '' : $options['promocoder_privatize_dropdown'];
    $dropdown_args = array(
    'post_type'        => 'page', 
    'selected'         => $shpage, 
    'name'             => 'promocoder_settings[promocoder_privatize_dropdown]', 
    'show_option_none' => '-- Select Page --', 
    'option_none_value' => '0',
    'sort_column'      => 'menu_order, post_title', 
    'echo'             => 0 
    ); 
    printf('<label class="form-control-select"><span 
        class="form-control-title">%s</span> %s</label>
        <p><small>%s </small>%s</p>', 
        $label, 
        wp_dropdown_pages($dropdown_args),
        esc_html__( 'Set to page you want to protect from Public View. User must have promocode 
            to view page.', 'promocoder' ),
        '' 
        ); 
}

/**
 * Retrieve or display list of pages as a dropdown (select list).
 * 
 * @uses wp_dropdown_pages
 */
function promocoder_text_field_0_render()
{
    $label    = '';
    $options = get_option('promocoder_settings'); 
    $shrtpage = (empty($options['promocoder_text_field_0'] )) 
                            ? '' : $options['promocoder_text_field_0'];
    $dropdown_args =  array(
        'post_type'        => 'page', 
        'selected'         => $shrtpage, 
        'name'             => 'promocoder_settings[promocoder_text_field_0]', 
        'show_option_none' => '-- Select Page --', 
        'option_none_value' => '0',
        'sort_column'      => 'menu_order, post_title', 
        'echo'             => 0 
    ); 
    printf('<label class="form-control-select"><span 
        class="form-control-title">%s</span> %s</label>
        <p><small>%s </small>%s</p>', 
        $label, 
        wp_dropdown_pages($dropdown_args),
        esc_html__( 'This must be set if you want a Return Link on the form 
            success.', 'promocoder' ),
        '' ); 
}

/**
 * Retrieve or display list of pages as a dropdown (select list).
 * 
 * @uses wp_dropdown_pages
 */
function promocoder_rediradmin_dropdown_render()
{
    $label    = '';
    $options = get_option('promocoder_settings'); 
    $shrtpage = (empty($options['promocoder_rediradmin_dropdown'] )) 
                            ? '' : $options['promocoder_rediradmin_dropdown'];

    $dropdown_args =  array(
        'post_type'        => 'page', 
        'selected'         => $shrtpage, 
        'name'             => 'promocoder_settings[promocoder_rediradmin_dropdown]', 
        'show_option_none' => '-- Select Page --', 
        'option_none_value' => '0',
        'sort_column'      => 'menu_order, post_title', 
        'echo'             => 0 
    ); 
    printf('<label class="form-control-select"><span 
        class="form-control-title">%s</span> %s</label>
        <p><small>%s </small>%s</p>', 
        $label, 
        wp_dropdown_pages($dropdown_args),
        esc_html__( 'Set page to be redirected upon login. Turn on or off below.', 
            'promocoder' ),
        '' ); 
}
/**
 * checkbox for 'redir admin' field
 * @since 1.0.1
 */
function promocoder_checkbox_rediradmin_render() {
    $options = get_option('promocoder_settings'); 
    $promocoder_checkbox_rediradmin = (empty($options['promocoder_checkbox_rediradmin'] )) 
                          ? 0 : $options['promocoder_checkbox_rediradmin']; ?>
 <p><input type="hidden" 
           name="promocoder_settings[promocoder_checkbox_rediradmin]" 
           value="0" />
    <input name="promocoder_settings[promocoder_checkbox_rediradmin]" 
           value="1" 
           type="checkbox" <?php echo esc_attr( 
           checked( 1, $promocoder_checkbox_rediradmin, true ) ); ?> /> 	
    <?php esc_html_e( 'Check to Redirect directly to Form Submit Page', 'promocoder' ); ?></p>
    <small><?php esc_html_e( 'Only works for logged In Administrators.', 
    'promocoder' ); ?> </small>
    <?php  
}
 
//render section
function promocoder_settings_section_callback()
{
    echo '<div></div>';
}

//create page
function promocoder_options_page() 
{
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) { 
        return;
    }
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
    // add settings saved message with the class of "updated"
    add_settings_error( 'promocoder_messages', 'promocoder_message', 
                    __( 'Settings Saved', 'promocoder' ), 'updated' );
    }
    // show error/update messages
    settings_errors( 'promocoder_messages' ); ?>

    <div class="promocoder_wrap wrap">
  
    <form action="options.php" method="post">
        <?php 
        settings_fields( 'promocoder' );
        do_settings_sections( 'promocoder' );
        submit_button(); 
        ?>
    </form>

<?php 
 
/**
 * documentation render
 */

ob_start(); 
?>

    <hr><div class="hrds-clearfix"></div>

<div style="max-width:749px;word-wrap:break-word;height: 100%;"> 
    <h2><?php esc_html_e( 'Helpful Information to get you started', 'promocoder' ); ?></h2>
<dl>
 	<dt><b><?php esc_html_e( 'Login Options', 'promocoder' ); ?></b></dt>
 	<dd><strong><span style="color: #000000;"><span class="info"><?php esc_html_e( 'Upon install of Promocoder you may want to set the option to redirect upon login so that you can go directly to the form page each time you login.', 'promocoder' ); ?></span><sup>*</sup></span></strong></dd>
 	<dd></dd>
 	<dt><b><?php esc_html_e( 'Setup', 'promocoder' ); ?></b></dt>
 	<dd><?php esc_html_e( 'This section describes how to install the plugin and get it working.', 'promocoder' ); ?></dd>
 	<dd><?php esc_html_e( '1. The shortcode to add inside of your post or page to show list is: ', 'promocoder' ); ?><br> [promocoder_list&#93; </dd>
 	
 	<dd><?php esc_html_e( '2. To add a front end form to a page use shortcode: ', 'promocoder' ); ?><br> [promocoder_submit_post&#93; </dd>
 	<dd><?php esc_html_e( '3. Go through all options in Promocoder Settings to activate what you want to use.', 'promocoder' ); ?></dd>

    <dt><b><?php esc_html_e( 'Color for List Title', 'promocoder' ); ?></b></dt>
 	<dd><?php esc_html_e( 'If field is left unchanged then the titles will stay the same as the theme default titles color.', 'promocoder' ); ?></dd>
 	<dt><b><?php esc_html_e( 'Color for List Links', 'promocoder' ); ?></b></dt>
 	<dd><?php esc_html_e( 'Colors can be set to help brand your page to match your theme.', 'promocoder' ); ?></dd>
 	<dt><b><?php esc_html_e( 'What Page is shortcode &#39;promocoder_list&#39; on?', 'promocoder' ); ?></b></dt>
 	<dd><?php esc_html_e( 'This must be set if you want a link on the form page. The link will show on the popup "Success" message after submitting your new link.', 'promocoder' ); ?></dd>
 	<dt><b><?php esc_html_e( 'What Page is shortcode &#39;promocoder_submit_post&#39; on?', 'promocoder' ); ?></b></dt>
 	<dd><?php esc_html_e( 'Set page to be redirected upon login. Turn on or off Using the next setting below.', 'promocoder' ); ?></a></dd>
 	<dd><small><?php esc_html_e( 'This is only a convenience to administrators to save time when you are in a hurry to add links.', 'promocoder' ); ?></small></dd>
 	<dt><b><?php esc_html_e( 'Redirect on Login', 'promocoder' ); ?></b></dt>
 	<dd><?php esc_html_e( 'Check to Redirect directly to Form Submit Page. Only works for logged In Administrators.', 'promocoder' ); ?></dd>
 	<dt><b><?php esc_html_e( 'Front Side Submit Form', 'promocoder' ); ?></b></dt>
 	<dd><?php esc_html_e( 'The form will allow you to add title, link and tags to your post, but, only Administrators and Editors will see the form on the public side of your Website. A Featured Image maybe added to every listing but you will need to do this from the editor so that you can control the assets for each picture. To add a front end form to a page use shortcode: ', 'promocoder' ); ?> [promocoder_submit_post&#93; <br></dd>
 </dl> <div class="hrds-clearfix"></div> 
</div>
<hr>
</div>
<?php 
echo ob_get_clean();
} 