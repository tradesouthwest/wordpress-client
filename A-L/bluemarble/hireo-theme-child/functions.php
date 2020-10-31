<?php
/**
 * Author Codeable Larry
 * Version: 1.0.1
 */
add_action( 'wp_enqueue_scripts', 'hireo_child_enqueue_child_theme_styles');
function hireo_child_enqueue_child_theme_styles() 
{
    $cver = time();
    wp_enqueue_style('hireo-theme-style', get_template_directory_uri() .'/style.css');
    wp_enqueue_style('hireo-theme-child', get_stylesheet_directory_uri() .'/style.css', 
					array('hireo-theme-style'), $cver );
}

/**
 * Add body class to handle CSS
 * WP Core
 */
add_filter('body_class','hireo_child_class_names');
function hireo_child_class_names($classes) 
{
    $classes[] = (empty(hireo_child_get_user_role())) 
                ? 'no-role' : hireo_child_get_user_role();
    if( is_user_logged_in() ) {
        $classes[] = 'loggedin-membership-class';
    } else {
        $classes[] = 'loggedout-membership-class';
    }
        
        return $classes;
}
function hireo_child_get_user_role() 
{
    global $current_user;
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);

        return $user_role;
}
/**
 * Determine price by role
 */
function hireo_child_pricing_table_prices()
{
    $role = hireo_child_get_user_role();
    if ( $role == 'subscriber' ){
        $price = absint('0'); } else {
        $price = esc_html( 'See Plan' ); 
        }

            return $price;
}
/**
 * Find formatted expiry date
 */    
function hireo_child_expiry_formatted_date()
{
    $membership_renewson = '';
    $auth = SwpmAuth::get_instance();
    $expiry_date = $auth->get_expire_date();
    
    if ($expiry_date !== 'Never') {
        $timestamp    = strtotime( $expiry_date );
        $closing_format_day = date('d.m.Y', $timestamp);
        $membership_renewson = $closing_format_day;
        } else {
            $membership_renewson = $expiry_date;
        }

        return $membership_renewson;
}
/**
 * Extending Simple Membership plugin
 * @since 1.0.1
 * $post['post_type']   = 'swpm_transactions';
 */ 
function hireo_child_profile_dashboard_addon()
{
//global $member_id;
    $subsc_starts_formatted = $membership_alias = $membership_renewson = $membership_price = '';

    $user_record = SwpmMemberUtils::get_user_by_id( $member_id );
    //$mget_price = SwpmTransactions::get_transaction_row_by_subscr_id ($subscr_id);

    $membership_price       = (empty(hireo_child_pricing_table_prices() ) ) 
                              ? '' : hireo_child_pricing_table_prices();
    $subsc_starts_formatted = SwpmUtils::get_formatted_date_according_to_wp_settings( $user_record->subscription_starts );
    $membership_alias       = SwpmMemberUtils::get_logged_in_members_level_name();
    $membership_renewson    = hireo_child_expiry_formatted_date();
    $membership_renewson    = date_i18n( get_option( 'date_format' ), strtotime( $membership_renewson ));
    //SwpmMemberUtils::get_formatted_expiry_date_by_user_id($swpm_id);
    //SwpmMemberUtils::get_logged_in_members_'payment_amount'();
    
    ob_start();
    ?>
    <p><strong><?php _e('Membership Type: ', 'hireo-theme'); ?></strong> <span><?php echo esc_html( $membership_alias ); ?></span></p>
    <p><strong><?php _e('Start Date: ', 'hireo-theme'); ?></strong> <span><?php echo esc_html( $subsc_starts_formatted ); ?></span></p>
    <p><strong><?php _e('Renewal date:', 'hireo-theme'); ?><sup>*</sup> </strong> <span><?php echo esc_html( $membership_renewson ); ?></span></p>
    
    <sup>*</sup><small><?php _e('Renewal date uses a 30 day month, to Monthly Members.', 'hireo-theme'); ?></small>
    <?php 
    return ob_get_clean();
}
