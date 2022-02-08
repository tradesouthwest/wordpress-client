<?php
/** 
 * Promocoder 
 * @package promocoder
 * @since 1.0.0
 */ 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	/**
 * Privatized Page 
 * @since 1.0.1
 * 
 */
function promocoder_privitized_page()
{
    $options = get_option('promocoder_settings'); 
    $chkpage = (empty($options['promocoder_privatize_dropdown'] )) 
               ? home_url() : $options['promocoder_privatize_dropdown'];
    
        return $chkpage;
}

/**
 * Public form to submit promo code into. Redirects to hidden page if true.
 * @since 1.0.3
 * @return HTML
 *
 */
function promocoder_front_promocoder_entry($atts='', $content=null)
{	
	$found_post_id = false;
	$cls           = 'promocoder-visible';
	$promopage_link = promocoder_privitized_page();

	ob_start();
	echo 
	'<div class="promocoder-wrapper check-promocode-entry">
		<header>
		    <h4>' . get_bloginfo("name") . '</h4>
		</header>
		<section class="promocoder-wrap">';
	if ($_SERVER["REQUEST_METHOD"] == "POST") : 
		echo 
		'<article>';

		if ( isset ( $_POST['check_promocode_action'] ) 
		&&  $_POST['check_promocode_action'] == "check_promocode" ) {
		
			$post_title = sanitize_text_field( $_POST['promocoder_check_promocode'] );
			$post_type  = 'promocoder';
	
			/* Attempt to find post id by post name if it exists */
			$found_post_title = get_page_by_title( $post_title, OBJECT, $post_type );
			$found_post_id = ( !empty ($found_post_title)) ? $found_post_title->ID   
								: "-1";
			
			if ( get_post_status( $found_post_id ) ){

				// update post
				$promocoder_tags = array( 'redeemed' );
				$post_id        = absint($found_post_id);
				wp_set_post_tags( $post_id, 
								   $promocoder_tags,
								   true // If true, don't delete existing tags, just add on.
								   ); 
				echo 
				'<div class="promocoder-wrapper check-promocode-entry">

					<div class="promocoder-displaylink">
					<p><a href="'. esc_url( get_page_link(absint($promopage_link)) ) .'" 
							class="promopage-link" 
							title="' . esc_html__( 'View Page', 'promocoder' ) . '">' 
							. esc_html__( 'View Page', 'promocoder' ) . '</a></p>
					</div>
				
				</div>';
				$cls = 'promocoder-hidden';

			} else {
				echo '<div>'. esc_html__('Please try entering code again.', 'promocoder' ) .'</div>';
			}
		}
		echo 
		'</article>';

	endif;
	?>
	
		<article id="PromocodeForm" class="<?php echo esc_attr($cls); ?>">
		<form id="new_promocode" name="new_promocode" method="post" action="">

		<table><tbody>
		<tr class="content-row">
			<td><label for="promocoder_check_promocode"><?php esc_html_e( 'Enter Code Please: ', 'promocoder' ); ?></label></td>
			<td><input type="text" id="promocoder_check_promocode" value="" tabindex="2" 
			name="promocoder_check_promocode" placeholder=""/></td>
		</tr>
		<tr class="submit-row">
			<td><input type="submit" value="<?php esc_html_e( 'Submit Link', 'promocoder' ); ?>" tabindex="6" id="submit" name="submit" /></td>
			<td><input type="hidden" name="check_promocode_action" value="check_promocode" />
		<?php //wp_nonce_field( 'promocode-entry' ); ?></td>
		</tr>
		</tbody></table>

		</form>
		</article>
	</section>

	</div>
<?php 
	$output = ob_get_clean();

		return $output;
}

/**
 * Save post metadata when a post is saved.
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 *
 */
function promocoder_front_post_creation($atts='', $content=null) 
{ 

	global $wpdb, $post; 

	$sub_success      = 'FAILURE' ;
	$default_category = 'General';
	
	if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) 
	&&  $_POST['action'] == "new_post") 
	{
	
		$errors = new WP_Error();
		
		// Do some minor form validation to make sure there is content
		if (isset ($_POST['title'])) {
			$title       =  $_POST['title'];
			} else {
			$errors->add('empty_title', __('<strong>Notice</strong>: Please enter a title for your post.', 'promocoder')
			);
		}
		//custom meta input
		if (isset ($_POST['promocoder_link'])) {
			$promocoder_link = $_POST['promocoder_link'];
			} else {
			$errors->add('empty_content', __('<strong>Notice</strong>: Please enter the contents of your post.', 'promocoder')
			);
		}
		//custom cat input
		if( isset( $_POST['promocoder_categories'] ) ) { 
			$promocoder_cat  = $_POST['promocoder_categories']; 
			$promocoder_cats = get_term_by( 'id', $promocoder_cat, 'promocoder_categories' );
			$promocoder_cat  = array(
		    	$promocoder_cats->slug
			);

			} else { 
			$promocoder_cat  = __( 'General', 'promocoder' );
		} 
		// custom tag input
		if( isset( $_POST['promocoder_tags'] ) ) {
			$promocoder_tag = $_POST['promocoder_tags'];
			} else {	
				$promocoder_tag =	'new';
		} 
			
		// ADD THE FORM INPUT TO $new_post ARRAY
		$new_post = array(
		'post_status'	=> 'publish', //get_option('promocoder_post_status'), 
		'post_type'  	=> 'promocoder',
		'post_title'	=> sanitize_title( $title ),
		'meta_input'    => array(
			'promocoder_link' => $promocoder_link,
			),
			//taxonomy input
			'tax_input'   =>	array( 
				'promocoder_categories' => $promocoder_cat,
				'promocoder_tags' => $promocoder_tag,
			),
        );

		//SAVE THE POST
        $query = $wpdb->prepare('SELECT ID FROM ' . $wpdb->posts . ' 
        WHERE post_title = %s', $title  );
		$wpdb->query( $query );
		if (!$wpdb->num_rows ) 
		{	
			if ( !$errors->get_error_code() ) 
			{ 
				$post_id = wp_insert_post( $new_post );
				//$object_id, $terms, $taxonomy, $append
				wp_set_object_terms( $post_id, 
									 $promocoder_cat, 
									 'promocoder_categories' );
				wp_set_post_tags( $post_id, 
								   $promocoder_tag,
								   true );
			}
				if ( $post_id ) 
					 $sub_success ='Success' ;
				/**
				 * promocoder_updated_post_send_email( $post_id );
				 * TODO
				 */ 
		}
	}
ob_start();
	if($sub_success == 'Success') { 
	/**
	 * This will redirect you to the newly created post
     * $post = get_post($post_id);
	 * wp_redirect($post->guid);
	 */	
	if( function_exists( 'promocoder_get_shortcoded_page' ) ) : 
		$pgwith_shortcode   = promocoder_get_shortcoded_page();
	else:
		$pgwith_shortcode = '';
	endif;

		echo '<div class="promocoder-success">' . __( 'Link curated succesfully.',
		 'post_new' ) 
		 . ' <a href="' . esc_url( $pgwith_shortcode ) . '">View List</a></div>';
		$sub_success = null;
	}            /* rmvd. sizeof($errors)>0 && */
	if (isset($errors) &&  $errors->get_error_code() ) :
		echo '<ul class="promocoder-errors">';
		foreach ($errors->errors as $error) {
			echo '<li>'.$error[0].'</li>';
		}
	echo '</ul>';
	endif; 
	//only logged in admins, editors can post from front end.	
	if ( is_user_logged_in() && current_user_can( 'edit_others_posts' ) ) 
	{ 
	//only show name if not admin
		$author = wp_get_current_user();
		if( current_user_can('administrator') ) 
		{ 
			$hrdscls = 'promocoder-hidden'; } 
		else { 
			$hrdscls = 'promocoder-inline-block'; }
	?>
<div class="promocoder-wrapper">
	<header>
	<h4><?php echo esc_html__( 'Add New Code ', 'promocoder' ); ?></h4>
	</header>
<form id="new_post" name="new_post" method="post" action="" enctype="multipart/form-data">
	<!-- post name -->
	<table><tbody><tr>
		<td><label for="title">Code: </label></td>
		<td><input type="text" id="title" value="" tabindex="2" name="title" /></td>
	</tr>

	<tr class="content">
		<td><label for="promocoder_link"><?php esc_html_e( 'Note: ', 'promocoder' ); ?></label></td>
		<td><input type="text" id="promocoder_link" value="" tabindex="3" 
		    name="promocoder_link" /></td>
	</tr>

	<tr class="category">
		<td><label for="promocoder_categories"><?php esc_html_e( 'Event/Promotion: ', 'promocoder' ); ?></label></td>
		<td><?php $taxonomy = 'promocoder_categories'; 
		promocoder_category_displayin_frontside( 'promocoder_categories'); 
		
		?></td>
	</tr>
	<tr class="submit">
		<td><input type="submit" value="<?php esc_html_e( 'Submit Link', 'promocoder' ); ?>" tabindex="6" id="submit" name="submit" /></td>
	<td>
    <input type="hidden" id="promocoder-tags" name="promocoder_tags" value="new" />
	<input type="hidden" name="action" value="new_post" />
	<?php wp_nonce_field( 'new-post' ); ?></td></tr></tbody></table>
</form>

<?php 
	} else { 
		echo '<h4>' . esc_html__( 'Please LogIn', 'promocoder' ) . '</h4> 
		<p><a href="' . wp_login_url( home_url() ) . '" 
		title="' . esc_attr__( 'Please LogIn', 'promocoder' ) . '" 
		class="hrds-btn btn btn-primary button button-primary">
		' . esc_html__( 'LogIn', 'promocoder' ) . '</a></p>'; 
	} 

	$output = ob_get_clean();

		return $output;
}

//promocoder_list shortcode
function promocoder_template_public_list($atts='', $content=null)
{
    global $post, $paged;

    if ( get_query_var('paged') ) $paged = get_query_var('paged');
    if ( get_query_var('page') ) $paged = get_query_var('page');
    
    $query = new WP_Query( array( 
		'post_type' => 'promocoder', 
		'paged'     => $paged
		) );

	ob_start();

    if( $query ) { 
		if ( !is_admin() && is_main_query() ) :    
    
	?>
	<div class="hrds-excerpts">
	
		<?php // grab layout value  
		$promocoder_layout     = 'promocoder_tall';
		if( $promocoder_layout == 'promocoder_tall' ) 
		{ ?>
		
		<?php include 'tmplt-promocoder-tall.php'; ?>

			<?php // display short list 
			} else { ?>
		
		<?php include 'tmplt-promocoder-short.php'; ?>
		
		<?php 
		}   //ends promocoder_layout choice 
		?>
		
		<?php  include 'nav-excerpt.php'; ?>
		
		<?php //ends all promocoder query
		endif; 
		$query = null;
			} else {
			esc_html_e( 'No listing in the promocoder taxonomy!', 'promocoder' ); 
		} ?>

	</div><div class="hrdsclearfix"></div>
    <?php //ends promocoder excerpts div 		
	$output = ob_get_clean();

		return $output;
} 
