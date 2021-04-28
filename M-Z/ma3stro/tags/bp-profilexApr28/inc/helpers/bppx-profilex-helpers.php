<?php 
//add_action('bp_activity_post_form_options', 'where_activity_from', 10, 1);
// A2
//add_action('bppx_profilex_visitor_save_location', 'bppx_profilex_visitor_save_location_tag'); 
/** A2
 * TODO ==================
 * check on valid serial data stream to be sure only serializing once.
 */

function bppx_profilex_visitor_save_location_tag() 
{
    //global $post, $current_user;

    if ( ! isset( $_POST['bppx_location_tag_field'] ) 
        || ! wp_verify_nonce( $_POST['bppx_location_tag_field'], 'bppx_location_tag_action' ) ) 
    {

        print 'Sorry, your can only submit a valid form item.';
        exit;
    } 
    else {

        if( isset( $POST['bppx_location_field'] ) ) 
        { 
            //$bppx_rand       = rand(1000, 9999);
            $location_field  = $POST['bppx_location_field'];
            //$location_tag    = $location_field . '-' . time() . '-' . $bppx_rand);
            $postid          = $POST['bppx_postid_logged'];
            $bppx_userid_tag = $POST['bppx_userid_logged'];

            $data_logged = sanitize_text_field( $bppx_userid_tag . '_' . $location_field );
                    update_post_meta($postid, 'bppx_location_tag', $data_logged);
        }
    }
}

/** R1
 * Add Shortcode
 */ 
function bppx_profilex_visitor_form_location_tag() 
{   
    global $post;
    
    
    $post_id         = (empty( $post->ID )) ? get_queried_object_id() : $post->ID;
    $bppx_login_url  = site_url() . '/login/';
    $current_user_id = '554'; 

    if( isset( $POST['bppx_location_submit'] ) ) 
    {     
        if ( ! isset( $_POST['bppx_location_tag_field'] ) 
            || ! wp_verify_nonce( $_POST['bppx_location_tag_field'], 'bppx_location_tag_action' ) ) 
        {
            print 'Sorry, your can only submit a valid form item.';
            exit;
        } 
        
         
            //$bppx_rand       = rand(1000, 9999);
            $location_field  = $POST['bppx_location_field_location'];
            //$location_tag    = $location_field . '-' . time() . '-' . $bppx_rand);
            //$post_id          = $POST['bppx_postid_logged'];
            //$bppx_userid_tag = $POST['bppx_userid_logged'];

            $bppx_data_logged  = $location_field . '_' . $current_user_id;
            
            $bppx_old_value = get_post_meta($post_id, 'bppx_location_tag', true);

            if( ! $bppx_old_value ) { 
                add_post_meta( $post_id, 'bppx_location_tag', $bppx_data_logged);
            } else {
                update_post_meta( $post_id, 'bppx_location_tag', $bppx_data_logged, $bppx_old_value);
            }
            $bppx_data_logged     = null;
            exit;
       
    }

    ob_start();
    echo '<div id="bppxModal" class="bppx-modal" style="">
            <div class="bppx-modal-content">';

        $bppx_meta_value = empty(get_post_meta($post_id, 'bppx_location_tag', true)) ? '' 
                        : get_post_meta($post_id, 'bppx_location_tag', true);

            echo $post_id . ' ' . $current_user_id . ' ' . $bppx_meta_value;
            echo '<span class="bppx-close">&times;</span>
                <p><a href="' . esc_url( $bppx_login_url ) . '">Log In or Join</a></p>
            <form action="" method="post" id="bppx-location-form" class="bppx-form" 
                        name="bppx_location_form">
            <div id="tags-content"> 
            
                <label for="bppx_location_field_location" style="font-size:10px;color:#7ab">Location (city)
                <input type="text" id="bppx_location_field_location" 
                                 name="bppx_location_field_location" 
                        value="' . $bppx_meta_value . '" style="width:280px">
                </label>

                <input type="hidden" id="bppx_userid_logged" name="bppx_userid_logged" 
                    value="' . absint($current_user_id) . '">

                <label for="bppx_location_submit">
                <input type="submit" id="bppx_location_submit" name="bppx_location_submit" 
                        value="Add Location">
                </label>';
        echo wp_nonce_field( 'bppx_location_tag_action', 'bppx_location_tag_field' );	
        echo '<p>Please give us your location to help us find your city. This helps us to be more apt to visit your area by knowing we have fans that want to hear from us. 
Also please join Ma3stro.com by registering. This gives you more options to enjoy what we have to offer.
</p>';

    
        echo '</div>
            </form>

        </div>
    </div>';
   
} 

add_action( 'wp_footer', 'bppx_profilex_footer_scripts_modal');

function bppx_profilex_footer_scripts_modal()
{
 
?>

<script type="text/javascript" id="bppx-profilex-location">
// Get the modal
var modal = document.getElementById("bppxModal");
var btn = document.getElementById("bppxBtn");
var span = document.getElementsByClassName("bppx-close")[0];
var subm = document.getElementById("bppx_location_submit");

window.onload = function() {
  modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
subm.onclick = function() {
  modal.style.opacity = ".5";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>
<?php 

}
/**
 * Get post visitors
 *
 * @param int      $post_id Post id.
 * @param int|bool $limit No of visitor to get.
 *
 * @return array
 */
function bppx_profilex_visitors_get_post_visitors( $post_id, $limit = false ) {
	$post_visitors = get_post_meta( $post_id, '_rp_visitors', true );
	$post_visitors = $post_visitors ? $post_visitors : array();

	if ( $limit ) {
		return array_slice( $post_visitors, 0, $limit );
	}

	return $post_visitors;
}
/**
 * Add visitor to post visitors list.
 *
 * @param int $post_id Post id.
 * @param int $visitor_id Visitor id.
 */
function bppx_profilex_visitors_add_post_visitor( $post_id, $visitor_id ) {
	$post_visitors = rp_visitors_get_post_visitors( $post_id );

	$existed_key = array_search( $visitor_id, $post_visitors );

	if ( false !== $existed_key ) {
		unset( $post_visitors[ $existed_key ] );
	}

	array_unshift( $post_visitors, $visitor_id );

	update_post_meta( $post_id, '_rp_visitors', $post_visitors );
}
