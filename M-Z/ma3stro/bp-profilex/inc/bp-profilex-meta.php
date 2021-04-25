<?php 
/**
 * Find user id regardless.
 * 
 * @param string $user_id
 * @return int 
 */
function bppx_profilex_get_userid()
{
    $user_id = get_current_user_id();
    if ( !isset( $user_id ) ) { $user_id = bbp_get_user_id( 0, true, false ); }
    if ( $user_id == '' ) { $user_id = '554'; }
    
        return $user_id;
}
/**
 * Find user location regardless.
 * 
 * @param string $user_id
 * @return int 
 */
function bppx_profilex_get_location($userid)
{
    global $post;
    $location = $user_city = '';
    //$userid            = bppx_profilex_get_userid();
    $statez    = empty(bp_get_profile_field_data('field=State or Province&user_id=' .$userid) ) 
        ? $user_city : bp_get_profile_field_data('field=State or Province&user_id=' .$userid); 
      
    $bppx_location_meta = empty(get_post_meta($post->ID, 'bppx_location_tag', true)) ? $statez  
                              : get_post_meta($post->ID, 'bppx_location_tag', true); 
    $bppx_location      = ( ''!= $bppx_location_meta ) ? $bppx_location_meta : $statez; 
    $location           = ( ''!= $bppx_location ) ? $bppx_location : 'unspecified';
        
        return $location;
}
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
    $query = explode('&', $_SERVER['QUERY_STRING']);
            $params = array();
            if(!empty($query[0])){
                foreach( $query as $param ){
                list($name, $value) = explode('=', $param, 2);
                $params[urldecode($name)][] = urldecode($value);
                }
            }
    // retrieve first param of query only.
    $user_id  = $params['user_id'][0];
    if( $user_id ) : 

    echo '<table class="form-table">
			<tbody>
				<tr>
					<th><label for="user_visitor_logged">'. __('Community ID', 'buddypress') .'</label></th>
                    <td><input name="user_visitor_logged" type="text" 
                               id="user_visitor_logged" value="'. $user_id .'" readonly></td>
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
