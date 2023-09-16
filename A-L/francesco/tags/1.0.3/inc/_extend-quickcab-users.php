<?php
/**
 * Custom checkout fields disable
 * @see https://stackoverflow.com/questions/62351584/how-to-disable-fields-that-are-pre-filled-in-woocommerce-checkout
 * @return $fields 
 */
function extend_quickcab_woocommerce_billing_fields( $fields ) {   
    // Get current user
    $user = wp_get_current_user();

    // User id
    $user_id = $user->ID;

    // User id is found, make sure user is logged in
    if ( $user_id > 0 ) { 
        // Fields
        $read_only_fields = array ( 
            'billing_address_1',
            'billing_state' 
        );

        // Loop
        foreach ( $fields as $key => $field ) {     
            if( in_array( $key, $read_only_fields ) ) {
                // Get key value
                $key_value = get_user_meta($user_id, $key, true);

                if( strlen( $key_value ) > 0 ) {
                    $fields[$key]['custom_attributes'] = array(
                        'readonly' => 'readonly'
                    );
                }
            }
        }
    }

   return $fields;
}
//add_filter('woocommerce_billing_fields', 'extend_quickcab_woocommerce_billing_fields', 9, 1 );
//add_filter( 'woocommerce_checkout_fields', 'codeable_larry_no_phone_validation' );

function codeable_larry_no_phone_validation( $checkout_fields ) {
	unset( $checkout_fields[ 'billing' ][ 'billing_phone' ][ 'validate' ] );
	return $checkout_fields;
}
/**
 * Custom user profile fields.
 *
 * @param $user
 * @author Larry Judd
 */
function codeable_larry_custom_user_profile_fields( $user ) {
    echo '<h3 class="heading">'. esc_html__('Informazioni scuola', 'hello-elementor') .'</h3>';
    ?>
    <table class="form-table">
        <tr id="clcuA">
            <th><label for="comprehensive_school" title="comprehensive school"><?php esc_html_e( 'Istituto comprensivo', 'hello-elementor' ); ?></label></th>
            <td>
                <input type="text" name="comprehensive_school" id="comprehensive_school" value="<?php echo esc_attr( get_the_author_meta( 'comprehensive_school', $user->ID ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr id="clcuB">
            <th><label for="contact" title="contact"><?php esc_html_e( 'Contatto', 'hello-elementor' ); ?></label></th>
            <td>
                <input type="text" name="contact" id="contact" value="<?php echo esc_attr( get_the_author_meta( 'contact', $user->ID ) ); ?>" class="regular-text" />
                <br><small>contact</small>
            </td>
        </tr>
        <tr id="clcuC">
            <th><label for="school_name"  title="school name">
                <?php esc_html_e( 'Scuola per cui prenotare', 'hello-elementor' ); ?></label></th>
            <td>
                <input type="text" name="school_name" id="school_name" value="<?php echo esc_attr( get_the_author_meta( 'school_name', $user->ID ) ); ?>" class="regular-text" />
                <br><small>school name</small>
            </td>
        </tr>
        <tr id="clcuD">
            <th><label for="school_type"  title="school type"><?php esc_html_e( 'Istituzione scolastica', 'hello-elementor' ); ?></label></th>
            <td>
                <input type="text" name="school_type" id="school_type" value="<?php echo esc_attr( get_the_author_meta( 'school_type', $user->ID ) ); ?>" class="regular-text" />
                <br><small>istituto comprensivo, scuola primaria, scuola secondaria I grado, scuola dell'infanzia</small>
                <br><small>school type/educational institution</small>
            </td>
        </tr>
        <tr id="clcuE">
            <th><label for="district_name"  title="district"><?php esc_html_e( 'If District, Name', 'hello-elementor' ); ?></label></th>
            <td>
                <input type="text" name="district_name" id="" value="<?php echo esc_attr( get_the_author_meta( 'district_name', $user->ID ) ); ?>" class="regular-text" />
                <br><small>district name</small>
            </td>
        </tr>
    </table>
    <?php 
}
add_action( 'show_user_profile', 'codeable_larry_custom_user_profile_fields' );
add_action( 'edit_user_profile', 'codeable_larry_custom_user_profile_fields' );

/**
 * Save custom user profile fields.
 *
 * @param User Id $user_id
 */
function codeable_larry_save_custom_user_profile_fields( $user_id ) {
    if ( current_user_can( 'edit_user', $user_id ) ) {

        update_user_meta( $user_id, 'comprehensive_school', sanitize_text_field( $_POST['comprehensive_school'] ) );
        update_user_meta( $user_id, 'school_name', sanitize_text_field( $_POST['school_name'] ) );
        update_user_meta( $user_id, 'contact', sanitize_text_field( $_POST['contact'] ) );
        update_user_meta( $user_id, 'school_type', sanitize_text_field( $_POST['school_type'] ) );
        update_user_meta( $user_id, 'district_name', sanitize_text_field( $_POST['district_name'] ) );
    }
}
add_action( 'personal_options_update', 'codeable_larry_save_custom_user_profile_fields' );
add_action( 'edit_user_profile_update', 'codeable_larry_save_custom_user_profile_fields' );


/**
 * Get author meta for single profile page
 *
 * @param string $meta_key   Meta key
 * @param string $profile_id ID of user
 * @see https://developer.wordpress.org/reference/functions/get_the_author_meta/
 * @return string            Text values
 */
function extend_quickcab_author_meta($meta_key, $user_id)
{
    $user_id = get_current_user_id();
    $meta = get_the_author_meta( $meta_key, $user_id );
    $rtrn = ( '' != $meta ) ? $meta : '';

        return $rtrn;
}

/**
 * Add a custom field (in an order) to the emails
 */
add_filter( 'woocommerce_email_order_meta_fields', 'extend_quickcab_woocommerce_email_order_meta_fields', 10, 3 );

function extend_quickcab_woocommerce_email_order_meta_fields( $fields, $sent_to_admin, $order ) 
{
    $extquick_message_one = ('' !=( get_option('extend_quickcab_options')['extend_quickcab_message_one']))
    ? get_option('extend_quickcab_options')['extend_quickcab_message_one'] 
    : '';
    $fields['comprehensive_school'] = array(
        'label' => __( 'Istituto comprensivo' ),
        'value' => extend_quickcab_author_meta('comprehensive_school', null),
    );
    $fields['school_name'] = array(
        'label' => __( 'Scuola per cui prenotare' ),
        'value' => extend_quickcab_author_meta('school_name', null),
    );
    $fields['school_type'] = array(
        'label' => __( 'Istituzione scolastica' ),
        'value' => extend_quickcab_author_meta('school_type', null),
    );
    $fields['district_name'] = array(
        'label' => __( 'District name' ),
        'value' => extend_quickcab_author_meta('district_name', null),
    );
    $fields['cancel_message'] = array(
        'label' => __( 'Avviso di cancellazione' ),
        'value' => sanitize_text_field($extquick_message_one),
    );
    return $fields;
}

/**
 * Add the field to the checkout
 */
add_action( 'woocommerce_after_order_notes', 'extend_quickcab_checkout_field' );

function extend_quickcab_checkout_field( $checkout ) {
    $extquick_message_one = ('' !=( get_option('extend_quickcab_options')['extend_quickcab_message_one']))
    ? get_option('extend_quickcab_options')['extend_quickcab_message_one'] 
    : '';

    echo '<div id="extend_quickcab_comprehensive_checkout_field">
    <h2>' . __('Informazioni sulla scuola e sugli utenti') . '</h2>';

    woocommerce_form_field( 'comprehensive_school', array(
        'type'          => 'text',
        'class'         => array('woo-field-class form-row-wide'),
        'label'         => __('Istituto comprensivo'),
        'placeholder'   => '',
        ), $checkout->get_value( 'comprehensive_school' ));

    echo '</div>';
    echo '<div id="extend_quickcab_school_name_checkout_field">';

    woocommerce_form_field( 'school_name', array(
        'type'          => 'text',
        'class'         => array('woo-field-class form-row-wide'),
        'label'         => __('Scuola per cui prenotare'),
        'placeholder'   => '',
        ), $checkout->get_value( 'school_name' ));

    echo '</div>';
    echo '<div id="extend_quickcab_school_name_checkout_field">';

    woocommerce_form_field( 'school_type', array(
        'type'          => 'text',
        'class'         => array('woo-field-class form-row-wide'),
        'label'         => __('Istituzione scolastica'),
        'placeholder'   => '',
        ), $checkout->get_value( 'school_type' ));
    echo '</div>';
    woocommerce_form_field( 'district_name', array(
        'type'          => 'text',
        'class'         => array('woo-field-class form-row-wide'),
        'label'         => __('District name'),
        'placeholder'   => '',
        ), $checkout->get_value( 'district_name' ));
    echo '</div>';

    echo '<div id="extend_quickcab_cancel_message_checkout_field">';    
    woocommerce_form_field( 'cancel_message', array(
            'type'          => 'text',
            'class'         => array('woo-field-class form-row-wide noborder'),
            'label'         => __('Avviso di cancellazione'),
            'placeholder'   => '',
            'custom_attributes' => array('readonly'=>'readonly'),
            ), $extquick_message_one);

    echo '</div>';
    
}
/**
 * Process the checkout
 */
add_action('woocommerce_checkout_process', 'extend_quickcab_checkout_field_process');

function extend_quickcab_checkout_field_process() {
    // Check if set, if its not set add an error.
    if ( empty( $_POST['school_name'] ) ) {
        wc_add_notice( __( 'Please enter school name into this field.' ), 'error' );
    }
}
/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'extend_quickcab_checkout_field_update_order_meta' );

function extend_quickcab_checkout_field_update_order_meta( $order_id ) {
    $extquick_message_one = ('' !=( get_option('extend_quickcab_options')['extend_quickcab_message_one']))
    ? get_option('extend_quickcab_options')['extend_quickcab_message_one'] 
    : '';
    
    if ( ! empty( $_POST['comprehensive_school'] ) ) {
        update_post_meta( $order_id, '', sanitize_text_field( $_POST['comprehensive_school'] ) );
    }
    if ( ! empty( $_POST['school_name'] ) ) {
        update_post_meta( $order_id, '', sanitize_text_field( $_POST['school_name'] ) );
    }
    if ( ! empty( $_POST['school_type'] ) ) {
        update_post_meta( $order_id, '', sanitize_text_field( $_POST['school_type'] ) );
    }
    if ( ! empty( $_POST['distrcit_name'] ) ) {
        update_post_meta( $order_id, '', sanitize_text_field( $_POST['district_name'] ) );
    }
    if ( ! empty( $_POST['cancel_message'] ) ) {
    update_post_meta( $order_id, '', sanitize_text_field( $extquick_message_one ) );
    }
}
/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'extend_quickcab_checkout_field_display_admin_order_meta', 9);

function extend_quickcab_checkout_field_display_admin_order_meta($order){
    $user    = $order->get_user();
    $user_id = $order->get_user_id();
    echo '<p><strong>'.__('Istituto comprensivo').':</strong> ' . extend_quickcab_author_meta('comprehensive_school', $user_id) . '</p>';
    echo '<p><strong>'.__('Scuola per cui prenotare').':</strong> ' . extend_quickcab_author_meta('school_name', $user_id) . '</p>';
    echo '<p><strong>'.__('Istituzione scolastica').':</strong> ' . extend_quickcab_author_meta('school_type', $user_id) . '</p>';
    echo '<p><strong>'.__('District').':</strong> ' . extend_quickcab_author_meta('district_name', $user_id) . '</p>';
    //echo '<p><strong>'.__('').':</strong> ' . get_post_meta( $order->id, 'cancel_message', $user_id ) . '</p>';
}
