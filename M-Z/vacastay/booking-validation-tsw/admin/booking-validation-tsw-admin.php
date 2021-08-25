<?php
/**
 * @since ver: 1.0.0
 * Author:     Tradesouthwest
 * Author      URI: http://tradesouthwest.com
 * @package    booking validation tsw
 * @subpackage admin/booking-validation-tsw-admin
*/
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'booking_valtsw_add_options_page' );  
    add_action( 'admin_init', 'booking_valtsw_register_admin_options' );
/**
 * Add an options page under the Settings submenu
 * $page_title, $menu_title, $capability, $menu_slug, $function-to-render, $icon_url, $position
 * @since  1.0.0
 */
function booking_valtsw_add_options_page() 
{
    add_menu_page(
        __( 'Booking Validation Settings', 'booking-valtsw' ),
        __( 'Booking ValTSW', 'onlist' ),
        'manage_options',
        'booking-valtsw',
        'booking_valtsw_options_page',
        'dashicons-admin-tools',
        '60'
    );
}
/** register a new sections and fields in the "onlist admin" page
 * section is primary options callback are _field
 */
function booking_valtsw_register_admin_options() 
{
    register_setting( 'booking_valtsw_primary', 'booking_valtsw_field' );
    /**
 * listings section
 */        
    add_settings_section(
        'booking_valtsw_section',
        'Booking ValTSW Options',
        'booking_valtsw_section_cb',
        'booking_valtsw_primary'
    ); 
    add_settings_field(
        'booking_valtsw_cutoff',
        __('Number of hours for refund', 'booking-validation-tsw'),
        'booking_valtsw_cutoff_cb',
        'booking_valtsw_primary',
        'booking_valtsw_section'
    );
    add_settings_field(
        'booking_valtsw_cutoff_text',
        __('Text in front of cutoff', 'booking-validation-tsw'),
        'booking_valtsw_cutoff_text_cb',
        'booking_valtsw_primary',
        'booking_valtsw_section'
    );
    add_settings_field(
        'booking_valtsw_fee_text',
        __('Text for Additional Fees', 'booking-validation-tsw'),
        'booking_valtsw_fee_text_cb',
        'booking_valtsw_primary',
        'booking_valtsw_section'
    );
}

function booking_valtsw_cutoff_cb()
{
    $options = get_option('booking_valtsw_field'); 
    $valtsw  = $options['booking_valtsw_cutoff']; 
    if( $valtsw == '' ) { $valtsw = ''; } 
?>
    <label class="olmin"><?php esc_html_e( 'Set amount of time for refund cutoff', 'booking-validation-tsw' ); ?></label>
    <input type="number" name="booking_valtsw_field[booking_valtsw_cutoff]" 
           value="<?php echo esc_attr( $valtsw ); ?>" 
           size="8"/>
    <?php
}
function booking_valtsw_cutoff_text_cb()
{
    $options = get_option('booking_valtsw_field'); 
    $valtsw  = ('' != $options['booking_valtsw_cutoff_text']) ? $options['booking_valtsw_cutoff_text'] : ''; 
?>
    <label class="olmin"><?php esc_html_e( 'Set amount of time for refund cutoff', 'booking-validation-tsw' ); ?></label>
    <input type="text" name="booking_valtsw_field[booking_valtsw_cutoff_text]" 
           value="<?php echo esc_attr( $valtsw ); ?>" 
           size="35"/>
    <?php
}
function booking_valtsw_fee_text_cb()
{
    $options    = get_option('booking_valtsw_field'); 
    $valtsw = ('' != ($options['booking_valtsw_fee_text'])) ? $options['booking_valtsw_fee_text'] : '';
?>
    <label class="olmin"><?php esc_html_e( 'Set text for Additional Fees', 'booking-validation-tsw' ); ?></label>
    <input type="text" name="booking_valtsw_field[booking_valtsw_fee_text]" 
           value="<?php echo esc_attr( $valtsw ); ?>" 
           size="35"/>
    <?php
}

// section content cb
function booking_valtsw_section_cb()
{    
    print( '<h4>' );
    esc_html_e( 'Modify Field Settings from Here. 
    Erase or leave blank to remove field.', 'booking-valtsw' );
    print( '</h4>' ); 
}




//render admin page
function booking_valtsw_options_page() 
{
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) return;
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
    // add settings saved message with the class of "updated"
    add_settings_error( 'booking_valtsw_messages', 'booking_valtsw_message', 
                        __( 'Settings Saved', 'booking-valtsw' ), 
                        'updated' );
    }
    // show error/update messages
    settings_errors( 'booking_valtsw_messages' );

?>
    <div class="wrap">
    
    <h1><div id="icon-options-general" class="icon32"></div>
    <?php echo esc_html( get_admin_page_title() ); ?></h1>
    <form action="options.php" method="post">
    <?php
     
        settings_fields( 'booking_valtsw_primary' );
        do_settings_sections( 'booking_valtsw_primary' ); 
     
        submit_button( 'Save Settings' ); ?>
    </form>
    
    </div>
<?php
}  



    
/**
 * Changing a meta title
 * @param  string        $key  The meta key
 * @param  WC_Meta_Data  $meta The meta object
 * @param  WC_Order_Item $item The order item object
 * @return string        The title
 */
//add_action( 'woocommerce_after_order_itemmeta', 'booking_validation_tsw_admin_order_item_custom', 10, 3 );
function booking_validation_tsw_admin_order_item_custom( $item_id, $item, $product ){
    // Only "line" items and backend order pages
    if( ! ( is_admin() && $item->is_type('line_item') ) )
        return;

    $security_deposit = $item->get_meta('_security_deposit'); // Get custom item meta data (array)
    $cleaning_fee     = $item->get_meta('_cleaning_fee'); 
    if( ! empty($security_deposit) ) {
        // Display a custom download button using custom meta for the link
        echo '<table><tbody><tr><td>Security Deposit ' . $security_deposit . '</td></tbody></table>';
    }
    if( ! empty($security_deposit) ) {
        // Display a custom download button using custom meta for the link
        echo '<table><tbody><tr><td>Cleaning Fee ' . $cleaning_fee . '</td></tbody></table>';
    }
    
} 

//add_action( 'woocommerce_after_order_itemmeta', 'booking_validation_tsw_order_item_custom_fields', 10, 2 );
function booking_validation_tsw_order_item_custom_fields( $item_id, $item ) {
    // Targeting line items type only
    if( $item->get_type() !== 'line_item' ) return;
     woocommerce_wp_text_input( array(
        'id'            => "_security_deposit[$item_id]",
        'label'         => __( 'Security Deposit', 'cfwc' ),
        'description'   => __( 'Change deposit', 'ctwc' ),
        'desc_tip'      => true,
        'class'         => 'order-item-field',
        'style'         => 'width:10em;padding:0px',
        'value'         => wc_get_order_item_meta( $item_id, '_security_deposit' ),
    ) );
     woocommerce_wp_text_input( array(
        'id'            => "_cleaning_fee[$item_id]",
        'label'         => __( 'Cleaning Fee', 'cfwc' ),
        'description'   => __( 'Change fee', 'ctwc' ),
        'desc_tip'      => true,
        'class'         => 'order-item-field',
        'style'         => 'width:10em;padding:0px',
        'value'         => wc_get_order_item_meta( $item_id, '_cleaning_fee' ),
    ) );
}

// Save the custom field value
//add_action('save_post', 'booking_validation_tsw_order_item_custom_fields_save', 100, 2 );
function booking_validation_tsw_order_item_custom_fields_save( $post_id, $post ){
    if ( 'shop_order' !== $post->post_type )
        return $post_id;

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    if ( ! current_user_can( 'edit_shop_order', $post_id ) )
        return $post_id;

    $order = wc_get_order( $post_id );
    foreach ( $order->get_items() as $item_id => $item ) {
        if( isset( $_POST['_security_deposit[$item_id]'] ) ) {
            $item->update_meta_data( '_security_deposit', sanitize_text_field( $_POST['_security_deposit[$item_id]'] ) );
            $item->save();
        }
        if( isset( $_POST['_cleaning_fee[$item_id]'] ) ) {
            $item->update_meta_data( '_cleaning_fee', sanitize_text_field( $_POST['cleaning_fee[$item_id]'] ) );
            $item->save();
        }
    }
    $order->save();
}

