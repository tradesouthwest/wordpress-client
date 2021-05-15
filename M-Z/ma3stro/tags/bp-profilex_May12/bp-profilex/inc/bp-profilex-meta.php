<?php 
/**
 * BP Profilex 
 * @since ver. 1.0.1
 */
 
/**
 * Allows an administrator to update his/her profile as well as all other
 * user profiles.
 *
 * @param int $user_id The ID of the user profile being edited.
 */
// only triggers when a user is viewing their own profile page. 
add_action('personal_options_update', 'bppx_user_profile_update_action');
// only triggers when a user is viewing another user’s profile 
add_action('edit_user_profile_update', 'bppx_user_profile_update_action');
function bppx_user_profile_update_action($user_id) 
{
    if ( !current_user_can( 'edit_user', $id ) )
        return false;

    if ( isset( $_POST['user_visitor_logged'] ) ) {
        update_user_meta($user_id, 'user_visitor_logged', $_POST['user_visitor_logged']);
    }
}

/**
 * render user profile fields
 *
 * @param   WP_User $user
 */
add_action('show_user_profile', 'bppx_user_profile_edit_action');
add_action('edit_user_profile', 'bppx_user_profile_edit_action');
function bppx_user_profile_edit_action(WP_User $user) 
{
    
    // retrieve user id.
    $userz_id  = bppx_profilex_get_userid();
    if( $userz_id ) : 

    echo '<table class="form-table">
			<tbody>
				<tr>
					<th><label for="user_visitor_logged">'. __('Community ID', 'buddypress') .'</label></th>
                    <td><input name="user_visitor_logged" type="text" 
                               id="user_visitor_logged" value="'. $userz_id .'" readonly></td>
				</tr>

			</tbody>
		</table>';
    else:
    
    echo '<table class="form-table bppx-profile-table">
			<tbody>
				<tr>
					<th><label for="user_visitor_logged">'. __('Activity ID', 'buddypress') .'</label></th>
                    <td><input name="user_visitor_logged" type="text" 
                               id="user_visitor_logged" value="' . $user->ID . '" readonly></td>
				</tr>

			</tbody>
		</table>';
    endif;

}
