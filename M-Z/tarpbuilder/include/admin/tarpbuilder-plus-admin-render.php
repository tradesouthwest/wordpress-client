<?php 
/**
 * The admin-specific rendering of the plugin.
 *
 * @package    tarpbuilder
 * @subpackage /include
 * @author     Larry Judd <tradesouthwest@gmail.com>
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// d.) render admin page
function tarpbuilder_plus_options_page() 
{
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) return;
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'tarpbuilder_plus_options';
    ?>
    <div class="wrap wrap-tarpbuilder-admin">
    
    <h1><span id="SlwOptions" class="dashicons dashicons-admin-tools"></span> 
    <?php echo esc_html( 'TarBuilder Plus Plugin for Woocommerce' ); ?></h1>

    <h2 class="nav-tab-wrapper">
    <a href="?page=tarpbuilder-plus&tab=tarpbuilder_plus_options" 
       class="nav-tab <?php echo $active_tab == 'tarpbuilder_plus_options' ? 
       'nav-tab-active' : ''; ?>">
       <?php esc_html_e( 'Shopwise Options', 'tarpbuilder-plus' ); ?></a>
    <a href="?page=tarpbuilder-plus&tab=tarpbuilder_content_options" 
       class="nav-tab <?php echo $active_tab == 'tarpbuilder_content_options' ? 
       'nav-tab-active' : ''; ?>">
       <?php esc_html_e( 'Content Helpers', 'tarpbuilder-plus' ); ?></a>
    <a href="?page=tarpbuilder-plus&tab=tarpbuilder_admin_help_content" 
       class="nav-tab <?php echo $active_tab == 'tarpbuilder_admin_help_content' ? 
       'nav-tab-active' : ''; ?>">
       <?php esc_html_e( 'Help and Instructions', 'tarpbuilder-plus' ); ?></a></h2>

    <form action="options.php" method="post" id="tarpbuilderOptionsForm">
    
    <?php 
    //page=tarpbuilder&tab=tarpbuilder_options
    if( $active_tab == 'tarpbuilder_plus_options' ) { 
        settings_fields(    'tarpbuilder_plus_options' );
        do_settings_sections( 'tarpbuilder_plus_options' ); 
    } 
    if( $active_tab == 'tarpbuilder_content_options' ) { 
        settings_fields(     'tarpbuilder_content_options' );
        do_settings_sections( 'tarpbuilder_content_options' ); 
    }
    if( $active_tab == 'tarpbuilder_admin_help_content' ) { ?>
    <div class="tarpbuilder_admin_help">
    <?php 
        settings_fields(     'tarpbuilder_admin_help_content' );
        do_settings_sections( 'tarpbuilder_admin_help_content' ); ?>
        </div>
        <?php 
    } 
    if( $active_tab == 'tarpbuilder_content_options' || $active_tab == 'tarpbuilder_plus_options' ) {
        submit_button( 'Save Settings' ); 
    }

    ?>
    </form>
    <?php 
}