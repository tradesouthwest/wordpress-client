<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * add_user_meta( $user_id, $meta_key, $meta_value, $unique )
 * https://developer.wordpress.org/plugins/users/working-with-user-metadata/
 * @hook edit_user_profile
 */
/**
 * The field on the editing screens.
 *
 * @param $user WP_User user object
 */
    add_action( 'show_user_profile', 'readlimit_user_profile_login_last' );
    add_action( 'edit_user_profile', 'readlimit_user_profile_login_last' );
    add_action( 'personal_options_update', 'readlimit_user_profile_login_last_save' );
    add_action( 'edit_user_profile_update', 'readlimit_user_profile_login_last_save' );

/**
 * Saves field to user_meta
 * $user_id, $meta_key, $meta_value, $prev_value = ''
 * @param $_POST data
 * @param int $user_id ID of user to assign image to
 */ 
function readlimit_user_profile_login_last( $user )
{ 

    $readonly = ( !current_user_can( 'edit_user', $user_id ) ) ? 'readonly' : '';
    $admintip = ( !current_user_can( 'edit_user', $user_id ) ) ? 'readonly' 
                : __('Admins may change this. Use "none" for default value.', 'readlimit');
    $readlimit_login_time = get_user_meta( $user->ID, 'last_login', true ); 
    $last_login      = ( $readlimit_login_time == '' ) ? 'never logged in' 
                            : date( 'Y-m-d H:i:s', $readlimit_login_time );
    ?>

    <h3><?php esc_html_e( 'Last login date and time', 'readlimit' ); ?></h3>
    <table class="form-table" role="presentation"><tbody>
    <tr class="user-last-login">
	<th><label for="last_login"><?php esc_html_e( 'Date/time', 'readlimit' ); ?></label></th>
	<td><input type="text" 
               name="last_login" 
               id="last_login" 
               value="<?php echo esc_attr($last_login); ?>" 
               class="regular-text" <?php print($readonly); ?>> <span class="wntip" 
               title="<?php echo esc_html($admintip) . $readlimit_login_time; ?>"> ? </span></td>
	</tr>
    </tbody></table>
    
    <?php 
	
}
/**
 * Saves rcp_expiration to user_meta
 * $user_id, $meta_key, $meta_value, $prev_value = ''
 * @param $_POST uploaded file
 * @param int $user_id ID of user to assign value to
 */ 
function readlimit_user_profile_login_last_save( $user )
{ 
//$user = get_userdata( $user_id );
//$user_id = get_current_user_id();
if ( !current_user_can( 'edit_user', $user->ID ) ) return false; 
	update_user_meta( $user->ID, 'last_login', 
                        $_POST['last_login'] );
}