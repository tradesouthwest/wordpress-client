<?php 

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Listeo Core Widget base
 */
class Listeo_Core_Widget extends WP_Widget {
/**
	 * Widget CSS class
	 *
	 * @access public
	 * @var string
	 */
	public $widget_cssclass;

	/**
	 * Widget description
	 *
	 * @access public
	 * @var string
	 */
	public $widget_description;

	/**
	 * Widget id
	 *
	 * @access public
	 * @var string
	 */
	public $widget_id;

	/**
	 * Widget name
	 *
	 * @access public
	 * @var string
	 */
	public $widget_name;

	/**
	 * Widget settings
	 *
	 * @access public
	 * @var array
	 */
	public $settings;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->register();

	}


	/**
	 * Register Widget
	 */
	public function register() {
		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

		
	}

	

	/**
	 * get_cached_widget function.
	 */
	public function get_cached_widget( $args ) {
		
		return false;

		$cache = wp_cache_get( $this->widget_id, 'widget' );

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return true;
		}

		return false;
	}

	/**
	 * Cache the widget
	 */
	public function cache_widget( $args, $content ) {
		$cache[ $args['widget_id'] ] = $content;

		wp_cache_set( $this->widget_id, $cache, 'widget' );
	}

	/**
	 * Flush the cache
	 * @return [type]
	 */
	public function flush_widget_cache() {
		wp_cache_delete( $this->widget_id, 'widget' );
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( ! $this->settings )
			return $instance;

		foreach ( $this->settings as $key => $setting ) {
			$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
		}

		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {

		if ( ! $this->settings )
			return;

		foreach ( $this->settings as $key => $setting ) {

			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];

			switch ( $setting['type'] ) {
				case 'text' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;			
				case 'checkbox' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="checkbox" <?php checked( esc_attr( $value ), 'on' ); ?> />
					</p>
					<?php
				break;
				case 'number' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;
				case 'dropdown' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>	
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
	
						<?php foreach ($setting['options'] as $key => $option_value) { ?>
							<option <?php selected($value,$key); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_attr($option_value); ?></option>	
						<?php } ?></select>
					
					</p>
					<?php
				break;
			}
		}
	}

	/**
	 * widget function.
	 *
	 * @see    WP_Widget
	 * @access public
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {}
}


/**
 * Featured listings Widget
 */
class Listeo_Core_Featured_Properties extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wp_post_types;

		$this->widget_cssclass    = 'listeo_core widget_featured_listings';
		$this->widget_description = __( 'Display a list of featured listings on your site.', 'listeo_core' );
		$this->widget_id          = 'widget_featured_listings';
		$this->widget_name        =  __( 'Featured Properties', 'listeo_core' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Featured Properties', 'listeo_core' ),
				'label' => __( 'Title', 'listeo_core' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 10,
				'label' => __( 'Number of listings to show', 'listeo_core' )
			)
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		

		ob_start();

		extract( $args );

		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$number = absint( $instance['number'] );
		$listings   = new WP_Query( array(
			'posts_per_page' => $number,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post_type' 	 => 'listing',
			'meta_query'     =>  array( 
				array(
					'key'     => '_featured',
					'value'   => 'on',
					'compare' => '=',
				),
				array('key' => '_thumbnail_id')
			)
		) );
	
		$template_loader = new Listeo_Core_Template_Loader;
		if ( $listings->have_posts() ) : ?>

			<?php echo $before_widget; ?>

			<?php if ( $title ) echo $before_title . $title . $after_title; ?>

			 <div class="widget-listing-slider dots-nav" data-slick='{"autoplay": true, "autoplaySpeed":3000}'>
				<?php while ( $listings->have_posts() ) : $listings->the_post(); ?>
					<div class="fw-carousel-item">
                        <?php
                       //     $template_loader->get_template_part( 'content-listing-compact' );  
                            $template_loader->get_template_part( 'content-listing-grid' );  
                        ?>
                    </div>
				<?php endwhile; ?>
			</div>

			<?php echo $after_widget; ?>

		<?php else : ?>

			<?php $template_loader->get_template_part( 'listing-widget','no-content' ); ?>

		<?php endif;

		wp_reset_postdata();

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}


/**
 * Save & Print listings Widget
 */
class Listeo_Core_Bookmarks_Share_Widget extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wp_post_types;

		$this->widget_cssclass    = 'listeo_core widget_buttons';
		$this->widget_description = __( 'Display a Bookmarks and share buttons.', 'listeo_core' );
		$this->widget_id          = 'widget_buttons_listings';
		$this->widget_name        =  __( 'Listeo Bookmarks & Share', 'listeo_core' );
		$this->settings           = array(
			'bookmarks' => array(
				'type'  => 'checkbox',
				'std'	=> 'on',
				'label' => __( 'Bookmark button', 'listeo_core' )
			),			
			'share' => array(
				'type'  => 'checkbox',
				'std'	=> 'on',
				'label' => __( 'Share buttons', 'listeo_core' )
			),
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		extract( $args );

		global $post;
		$share = (isset($instance['share'])) ? $instance['share'] : '' ; 
		$bookmarks = (isset($instance['bookmarks'])) ? $instance['bookmarks'] : '' ; 
		
		echo $before_widget; 
		
		?>
		<div class="listing-share margin-top-40 margin-bottom-40 no-border">

		<?php 
			if(!empty($bookmarks)):
			
				$nonce = wp_create_nonce("listeo_core_bookmark_this_nonce");
		
				$classObj = new Listeo_Core_Bookmarks;
				
				if( $classObj->check_if_added($post->ID) ) { ?>
					<button onclick="window.location.href='<?php echo get_permalink( get_option( 'listeo_bookmarks_page' ))?>'" class="like-button save liked" ><span class="like-icon liked"></span> <?php esc_html_e('Bookmarked','listeo_core') ?>
				</button> 
				<?php } else { 
					if(is_user_logged_in()){ ?>
						<button class="like-button listeo_core-bookmark-it"
							data-post_id="<?php echo esc_attr($post->ID); ?>" 
							data-confirm="<?php esc_html_e('Bookmarked!','listeo_core'); ?>"
							data-nonce="<?php echo esc_attr($nonce); ?>" 
							><span class="like-icon"></span> <?php esc_html_e('Bookmark this listing','listeo_core') ?></button> 
						<?php } else { 
							$popup_login = get_option( 'listeo_popup_login','ajax' ); 
							if($popup_login == 'ajax') { ?>
								<button href="#sign-in-dialog" class="like-button-notlogged sign-in popup-with-zoom-anim"><span class="like-icon"></span> <?php esc_html_e('Login To Bookmark Items','listeo_core') ?></button> 
							<?php } else { 
								$login_page = get_option('listeo_profile_page'); ?>
								<a href="<?php echo esc_url(get_permalink($login_page)); ?>" class="like-button-notlogged"><span class="like-icon"></span> <?php esc_html_e('Login To Bookmark Items','listeo_core') ?></a> 
							<?php } ?>		
					<?php } ?>
					
				<?php }

				$count = get_post_meta($post->ID, 'bookmarks_counter', true); 
				if ( $count ) : ?>
				<span id="bookmarks-counter"><?php printf( _n( '%s person bookmarked this place', '%s people bookmarked this place', $count, 'listeo_core' ), number_format_i18n( $count ) ); ?> </span>
				<?php endif; ?>
			<?php 
			endif;
			if(!empty($share)):  
					$id = $post->ID;
			        $title = urlencode($post->post_title);
			        $url =  urlencode( get_permalink($id) );
			        $summary = urlencode(listeo_string_limit_words($post->post_excerpt,20));
			        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'medium' );
			        if($thumb){
				        $imageurl = urlencode($thumb[0]);	
				    } else {
				    	$imageurl = false;
				    }
			        
			        ?>
			 		<ul class="share-buttons margin-bottom-0">
			          <li><?php echo '<a target="_blank" class="fb-share" href="https://www.facebook.com/sharer/sharer.php?u=' . $url . '"><i class="fa fa-facebook"></i> '.esc_html__('Share','listeo_core').'</a>'; ?></li>
			         <li><?php echo '<a target="_blank" class="twitter-share" href="https://twitter.com/share?url=' . $url . '&amp;text=' . esc_attr($summary ). '" title="' . __( 'Twitter', 'listeo_core' ) . '"><i class="fa fa-twitter"></i> Tweet</a></a>'; ?></li>
			        <li><?php echo '<a target="_blank"  class="pinterest-share" href="http://pinterest.com/pin/create/button/?url=' . $url . '&amp;description=' . esc_attr($summary) . '&media=' . esc_attr($imageurl) . '" onclick="window.open(this.href); return false;"><i class="fa fa-pinterest-p"></i> Pin It</a></a>'; ?></li>
			        </ul>
			
					<div class="clearfix"></div>
		
	 	<?php endif;
	 	?>
	 		</div>
	 	<?php
		echo $after_widget; 

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}


/**
 * Featured listings Widget
 */
class Listeo_Core_Contact_Vendor_Widget extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wp_post_types;

		$this->widget_cssclass    = 'listeo_core  boxed-widget message-vendor ';
		$this->widget_description = __( 'Display a Contact form.', 'listeo_core' );
		$this->widget_id          = 'widget_contact_widget_listeo';
		$this->widget_name        =  __( 'Listeo Contact Widget', 'listeo_core' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Message Vendor', 'listeo_core' ),
				'label' => __( 'Title', 'listeo_core' )
			),
				
			'contact' => array(
				'type'  => 'dropdown',
				'std'	=> '',
				'options' => $this->get_forms(),
				'label' => __( 'Choose contact form', 'listeo_core' )
			),			
		);
		$this->register();

		//add_filter( 'wpcf7_mail_components', array( $this, 'set_question_form_recipient' ), 10, 3 );

	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		
		global $post;
		$contact_enabled = get_post_meta( $post->ID, '_email_contact_widget', true );
		
		if( !$contact_enabled ) {
			return; 
		}

		ob_start();

		extract( $args );
	
		echo $before_widget; 
		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		?>
		<h3><i class="fa fa-envelope-o"></i> <?php echo $title ?></h3>
		<div class="row with-forms  margin-top-0">
			<?php
			if(get_post($instance['contact'] )){
			  echo do_shortcode( sprintf( '[contact-form-7 id="%s"]', $instance['contact'] ) );
			} else {
				echo 'Please choose "Contact Owner Widget" form in Appearance  → Widgets  (Single Listing Sidebar  → Listeo Contact Widget)'; echo ' <a href="http://www.docs.purethemes.net/listeo/knowledge-base/how-to-configure-message-vendor-form/">More information.</a>';
			}?>
		</div>

		<!-- Agent Widget / End -->
		<?php
		
		 echo $after_widget; 

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}

	public function get_forms() {
		$forms  = array( 0 => __( 'Please select a form', 'listeo_core' ) );

		$_forms = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => 'wpcf7_contact_form',
			)
		);

		if ( ! empty( $_forms ) ) {

			foreach ( $_forms as $_form ) {
				$forms[ $_form->ID ] = $_form->post_title;
			}
		}

		return $forms;
	}


}




/**
 * Save & Print listings Widget
 */
class Listeo_Core_Search_Widget extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wp_post_types;

		$this->widget_cssclass    = 'listeo_core widget_buttons';
		$this->widget_description = __( 'Display a Advanced Search Form.', 'listeo_core' );
		$this->widget_id          = 'widget_search_form_listings';
		$this->widget_name        =  __( 'Listeo Search Form', 'listeo_core' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Find New Home', 'listeo_core' ),
				'label' => __( 'Title', 'listeo_core' )
			),
			'action' => array(
				'type'  => 'dropdown',
				'std'	=> 'archive',
				'options' => array(
					'current_page' => __( 'Redirect to current page', 'listeo_core' ),
					'archive' => __( 'Redirect to listings archive page', 'listeo_core' ),
					),
				'label' => __( 'Choose form action', 'listeo_core' )
			),	
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}


		extract( $args );

		echo $before_widget; 
			$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
			if(isset($instance['action'])){
				$action  = apply_filters( 'listeo_core_search_widget_action', $instance['action'], $instance, $this->id_base);	
			}
			
			
			if ( $title ) {
				echo $before_title . $title;
				//if(isset($_GET['keyword_search'])) : echo '<a id="listeo_core_reset_filters" href="#">'.esc_html__('Reset Filters','listeo_core').'</a>'; endif;
			 	echo $after_title; 
			}
			$dynamic =  (get_option('listeo_dynamic_features')=="on") ? "on" : "off";

			if(isset($action) && $action == 'archive') {
				echo do_shortcode('[listeo_search_form dynamic_filters="'.$dynamic.'" 	more_text_open="'.esc_html__('More Filters','listeo_core').'" more_text_close="'.esc_html__('Close Filters','listeo_core').'" ajax_browsing="false" action='.get_post_type_archive_link( 'listing' ).']');
			} else {
				echo do_shortcode('[listeo_search_form  dynamic_filters="'.$dynamic.'" more_text_close="'.esc_html__('Close Filters','listeo_core').'" more_text_open="'.esc_html__('More Filters','listeo_core').'"]');
			}

		echo $after_widget; 

		


	}
}


/**
 * Booking Widget
 */
class Listeo_Core_Booking_Widget extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {

		// create object responsible for bookings
		$this->bookings = new Listeo_Core_Bookings_Calendar;

		$this->widget_cssclass    = 'listeo_core boxed-widget booking-widget margin-bottom-35';
		$this->widget_description = __( 'Shows Booking Form.', 'listeo_core' );
		$this->widget_id          = 'widget_booking_listings';
		$this->widget_name        =  __( 'Listeo Booking Form', 'listeo_core' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Booking', 'listeo_core' ),
				'label' => __( 'Title', 'listeo_core' )
			),
			
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		


		ob_start();

		extract( $args );
		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$queried_object = get_queried_object();
  		$packages_disabled_modules = get_option('listeo_listing_packages_options',array());
		if ( $queried_object ) {
		    $post_id = $queried_object->ID;

		  
			
			if(empty($packages_disabled_modules)) {
				$packages_disabled_modules = array();
			}

			$user_package = get_post_meta( $post_id,'_user_package_id',true );
			if($user_package){
				$package = listeo_core_get_user_package( $user_package );
			}

			$offer_type = get_post_meta( $post_id, '_listing_type', true );
		}
		
		if( in_array('option_booking',$packages_disabled_modules) ){ 
				
			if( isset($package) && $package->has_listing_booking() != 1 ){
				return;
			}
		}

		$_booking_status = get_post_meta($post_id, '_booking_status',true);{
			if(!$_booking_status) {
				return;
			}
		}
		echo $before_widget;
		if ( $title ) {		
			echo $before_title.'<i class="fa fa-calendar-check"></i> ' . $title . $after_title; 
		} 

		$days_list = array(
			0	=> __('Monday','listeo_core'),
			1 	=> __('Tuesday','listeo_core'),
			2	=> __('Wednesday','listeo_core'),
			3 	=> __('Thursday','listeo_core'),
			4 	=> __('Friday','listeo_core'),
			5 	=> __('Saturday','listeo_core'),
			6 	=> __('Sunday','listeo_core'),
		); 

		// get post meta and save slots to var
		$post_info = get_queried_object();

		$post_meta = get_post_meta( $post_info->ID );
		
		// get slots and check if not empty
		
		if ( isset( $post_meta['_slots_status'][0] ) && !empty( $post_meta['_slots_status'][0] ) ) {
			if ( isset( $post_meta['_slots'][0] ) ) {
				$slots = json_decode( $post_meta['_slots'][0] );
				if ( strpos( $post_meta['_slots'][0], '-' ) == false ) $slots = false;
			} else {
				$slots = false;	
			}
		} else {
			$slots = false;
		}
		// get opening hours
		if ( isset( $post_meta['_opening_hours'][0] ))
		{
			$opening_hours = json_decode( $post_meta['_opening_hours'][0], true );
		}

		if ( $post_meta['_listing_type'][0] == 'rental' || $post_meta['_listing_type'][0] == 'service' ) {

			// get reservations for next 10 years to make unable to set it in datapicker
			if( $post_meta['_listing_type'][0] == 'rental' ) {
				$records = $this->bookings->get_bookings( 
					date('Y-m-d H:i:s'),  
					date('Y-m-d H:i:s', strtotime('+3 years')), 
					array( 'listing_id' => $post_info->ID, 'type' => 'reservation' ),
					$by = 'booking_date', $limit = '', $offset = '',$all = '',
					$listing_type = 'rental' 
				);
				
	
			} else {

				$records = $this->bookings->get_bookings( 
					date('Y-m-d H:i:s'),  
					date('Y-m-d H:i:s', strtotime('+3 years')), 
					array( 'listing_id' => $post_info->ID, 'type' => 'reservation' ),
					'booking_date',
					$limit = '', $offset = '','owner' );	
				
			}
			

			// store start and end dates to display it in the widget
			$wpk_start_dates = array();
			$wpk_end_dates = array();
			if(!empty($records)) {
				foreach ($records as $record)
				{

					if( $post_meta['_listing_type'][0] == 'rental' ) {
					// when we have one day reservation
						if ($record['date_start'] == $record['date_end'])
						{
							$wpk_start_dates[] = date('Y-m-d', strtotime($record['date_start']));
							$wpk_end_dates[] = date('Y-m-d', strtotime($record['date_start'] . ' + 1 day'));
						} else {
							/**
							 * Set the date_start and date_end dates and fill days in between as disabled
							 */
							$wpk_start_dates[] = date('Y-m-d', strtotime($record['date_start']));
							$wpk_end_dates[] = date('Y-m-d', strtotime($record['date_end']));

							$period = new DatePeriod(
								new DateTime( date( 'Y-m-d', strtotime( $record['date_start'] . ' + 1 day') ) ),
								new DateInterval( 'P1D' ),
								new DateTime( date( 'Y-m-d', strtotime( $record['date_end'] ) ) )//. ' +1 day') ) )
							);

							foreach ($period as $day_number => $value) {
								$disabled_dates[] = $value->format('Y-m-d');  
							}

						} 
					} else {
								// when we have one day reservation
						if ($record['date_start'] == $record['date_end'])
						{
							$disabled_dates[] = date('Y-m-d', strtotime($record['date_start']));
						} else {
							
							// if we have many dats reservations we have to add every date between this days
							$period = new DatePeriod(
								new DateTime( date( 'Y-m-d', strtotime( $record['date_start']) ) ),
								new DateInterval( 'P1D' ),
								new DateTime( date( 'Y-m-d', strtotime( $record['date_end'] . ' +1 day') ) )
							);

							foreach ($period as $day_number => $value) {
								$disabled_dates[] = $value->format('Y-m-d');  
							}

						}
					}

				}
			}
			
				if ( isset( $wpk_start_dates ) )
				{
					?>
					<script>
						var wpkStartDates = <?php echo json_encode( $wpk_start_dates ); ?>;
						var wpkEndDates = <?php echo json_encode( $wpk_end_dates ); ?>;
					</script>
					<?php
				}
				if ( isset( $disabled_dates ) )	
				{
					?>
					<script>
						var disabledDates = <?php echo json_encode($disabled_dates); ?>;
					</script>
					<?php
				}
		} // end if rental/service
		

		if ( $post_meta['_listing_type'][0] == 'event') { 
			$max_tickets = (int) get_post_meta($post_info->ID,"_event_tickets",true);
			$sold_tickets = (int) get_post_meta($post_info->ID,"_event_tickets_sold",true); 
			$av_tickets = $max_tickets-$sold_tickets; 
			
			if($av_tickets<=0){?>
				<p id="sold-out"><?php esc_html_e('The tickets have sold out','listeo_core') ?></p></div>
			<?php
			return; }
			
		}
		?>
		
		<div class="row with-forms  margin-top-0" id="booking-widget-anchor" >	
			<form autocomplete="off" id="form-booking" data-post_id="<?php echo $post_info->ID; ?>" class="form-booking-<?php echo $post_meta['_listing_type'][0];?>" action="<?php echo esc_url(get_permalink(get_option( 'listeo_booking_confirmation_page' ))); ?>" method="post">

					<?php if ( $post_meta['_listing_type'][0] != 'event') { 
							$minspan = get_post_meta($post_info->ID,'_min_days',true); 
							//WP Kraken
							// If minimub booking days are not set, set to 2 by default
							if ( ! $minspan && $post_meta['_listing_type'][0] == 'rental' ) {
								$minspan = 2;
							}
						?>
					<!-- Date Range Picker - docs: http://www.daterangepicker.com/ -->
					<div class="col-lg-12">
						<input 
						type="text" 
						data-minspan="<?php echo ($minspan) ? $minspan : '0' ; ?>"
						id="date-picker" 
						readonly="readonly" 
						class="date-picker-listing-<?php echo esc_attr($post_meta['_listing_type'][0]); ?>" 
						autocomplete="off" 
						placeholder="<?php esc_attr_e('Date','listeo_core'); ?>" 
						value="" 
						data-listing_type="<?php echo $post_meta['_listing_type'][0]; ?>" />
					</div>

					<!-- Panel Dropdown -->
					<?php if ( $post_meta['_listing_type'][0] == 'service' &&   is_array( $slots ) ) { ?>
					<div class="col-lg-12">
						<div class="panel-dropdown time-slots-dropdown">
							<a href="#" placeholder="<?php esc_html_e('Time Slots','listeo_core') ?>"><?php esc_html_e('Time Slots','listeo_core') ?></a>

							<div class="panel-dropdown-content padding-reset">
								<div class="no-slots-information"><?php esc_html_e('No slots for this day','listeo_core') ?></div>
								<div class="panel-dropdown-scrollable">
									<input id="slot" type="hidden" name="slot" value="" />
									<input id="listing_id" type="hidden" name="listing_id" value="<?php echo $post_info->ID; ?>" />
									<?php foreach( $slots as $day => $day_slots) { 
										if ( empty( $day_slots )) continue;
										?>

										<?php foreach( $day_slots as $number => $slot) { 
										$slot = explode('|' , $slot); ?>
										<!-- Time Slot -->
										<div class="time-slot" day="<?php echo $day; ?>">
											<input type="radio" name="time-slot" id="<?php echo $day.'|'.$number; ?>" value="<?php echo $day.'|'.$number; ?>">
											<label for="<?php echo $day.'|'.$number; ?>">
												<p class="day"><?php echo $days_list[$day]; ?></p>
												<strong><?php echo $slot[0]; ?></strong>
												<span><?php echo $slot[1]; esc_html_e(' slots available','listeo_core') ?></span>
											</label>
										</div>
										<?php } ?>	

									<?php } ?>
								</div>
							</div>
						</div>
					</div>
					<?php } else if ( $post_meta['_listing_type'][0] == 'service'  ) { ?>
					<div class="col-lg-12">
						<input type="text" class="time-picker flatpickr-input active" placeholder="<?php esc_html_e('Time','listeo_core') ?>" id="_hour" name="_hour" readonly="readonly">
					</div>
					<?php if(get_post_meta($post_id,'_end_hour',true)) : ?>
						<div class="col-lg-12">
							<input type="text" class="time-picker flatpickr-input active" placeholder="<?php esc_html_e('End Time','listeo_core') ?>" id="_hour_end" name="_hour_end" readonly="readonly">
						</div>
						<?php 
					endif;
					$_opening_hours_status = get_post_meta($post_id, '_opening_hours_status',true);
					$_opening_hours_status = '';
					?>
						<script>
							var availableDays = <?php if($_opening_hours_status){ echo json_encode( $opening_hours, true ); } else { echo json_encode( '', true); }?>;
						</script>
					
					<?php } ?>
					
					<?php $bookable_services = listeo_get_bookable_services($post_info->ID); 

					if(!empty($bookable_services)) : ?>
						
						<!-- Panel Dropdown -->
						<div class="col-lg-12">
							<div class="panel-dropdown booking-services">
								<a href="#"><?php esc_html_e('Extra Services','listeo_core'); ?> <span class="services-counter">0</span></a>
								<div class="panel-dropdown-content padding-reset">
									<div class="panel-dropdown-scrollable">
									
									<!-- Bookable Services -->
									<div class="bookable-services">
										<?php 
										$i = 0;
										$currency_abbr = get_option( 'listeo_currency' );
										$currency_postion = get_option( 'listeo_currency_postion' );
										$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr); 
										foreach ($bookable_services as $key => $service) { $i++; ?>
											<div class="single-service <?php if(isset($service['bookable_quantity'])) : ?>with-qty-btns<?php endif; ?>"> 

												<input type="checkbox" autocomplete="off" class="bookable-service-checkbox" name="_service[<?php echo sanitize_title($service['name']); ?>]" value="<?php echo sanitize_title($service['name']); ?>" id="tag<?php echo esc_attr($i); ?>"/>
												
												<label for="tag<?php echo esc_attr($i); ?>">
													<h5><?php echo esc_html($service['name']); ?></h5>
													<span class="single-service-price"> <?php 
													if(empty($service['price']) || $service['price'] == 0) {
														esc_html_e('Free','listeo_core');
													} else {
													 	if($currency_postion == 'before') { echo $currency_symbol.' '; } 
															$price = $service['price'];
															if(is_numeric($price)){
																$decimals = get_option('listeo_number_decimals',2);
																echo number_format_i18n($price, $decimals);
															} else {
																echo esc_html($price); 	
															}
														if($currency_postion == 'after') { echo ' '.$currency_symbol; } 
													}
													?></span>
												</label>

												<?php if(isset($service['bookable_quantity'])) : ?>
												<div class="qtyButtons">
													<input type="text" class="bookable-service-quantity" name="_service_qty[<?php echo sanitize_title($service['name']); ?>]"  value="1">
												</div>
												<?php else: ?>
													<input type="hidden" class="bookable-service-quantity" name="_service_qty[<?php echo sanitize_title($service['name']); ?>]"  value="1">
												<?php endif; ?>

											</div>
										<?php } ?>
									</div>
									<div class="clearfix"></div>
									<!-- Bookable Services -->


									</div>
								</div>
							</div>
						</div>
						<!-- Panel Dropdown / End -->
						<?php 
					endif;
					$max_guests = get_post_meta($post_info->ID,"_max_guests",true); 
					$count_per_guest = get_post_meta($post_info->ID,"_count_per_guest",true); 
					if(get_option('listeo_remove_guests')){
						$max_guests = 1;
					}
					?>
					<!-- Panel Dropdown -->
					<div class="col-lg-12" <?php if($max_guests == 1){ echo 'style="display:none;"'; } ?>>
						<div class="panel-dropdown">
							<a href="#"><?php esc_html_e('Guests','listeo_core') ?> <span class="qtyTotal" name="qtyTotal">1</span></a>
							<div class="panel-dropdown-content" style="width: 269px;">
								<!-- Quantity Buttons -->
								<div class="qtyButtons">
									<div class="qtyTitle"><?php esc_html_e('Guests','listeo_core') ?></div>
									<input type="text" name="qtyInput" data-max="<?php echo esc_attr($max_guests); ?>" class="adults <?php if($count_per_guest) echo 'count_per_guest'; ?>" value="1">
								</div>
								
							</div>
						</div>
					</div>
					<!-- Panel Dropdown / End -->

			<?php } //eof !if event ?>

			<?php if ( $post_meta['_listing_type'][0] == 'event') { 
				$max_guests 	= (int) get_post_meta($post_info->ID,"_max_guests",true); 
				$max_tickets 	= (int) get_post_meta($post_info->ID,"_event_tickets",true);
				$sold_tickets 	= (int) get_post_meta($post_info->ID,"_event_tickets_sold",true); 
				$av_tickets 	= $max_tickets-$sold_tickets;
				if($av_tickets > $max_guests){
					$av_tickets = $max_guests;
				} 

				?><input 
						type="hidden" 
						id="date-picker" 
						readonly="readonly" 
						class="date-picker-listing-<?php echo esc_attr($post_meta['_listing_type'][0]); ?>" 
						autocomplete="off" 
						placeholder="<?php esc_attr_e('Date','listeo_core'); ?>" 
						value="<?php echo $post_meta['_event_date'][0]; ?>" 
						listing_type="<?php echo $post_meta['_listing_type'][0]; ?>" />
					<div class="col-lg-12 tickets-panel-dropdown">
						<div class="panel-dropdown">
							<a href="#"><?php esc_html_e('Tickets','listeo_core') ?> <span class="qtyTotal" name="qtyTotal">1</span></a>
							<div class="panel-dropdown-content" style="width: 269px;">
								<!-- Quantity Buttons -->
								<div class="qtyButtons">
									<div class="qtyTitle"><?php esc_html_e('Tickets','listeo_core') ?></div>
									<input type="text" name="qtyInput" <?php if($max_tickets>0){ ?>data-max="<?php echo esc_attr($av_tickets); ?>" <?php } ?>
									id="tickets" value="1">
								</div>
								
							</div>
						</div>
					</div>
					<?php $bookable_services = listeo_get_bookable_services($post_info->ID); 

					if(!empty($bookable_services)) : ?>
						
						<!-- Panel Dropdown -->
						<div class="col-lg-12">
							<div class="panel-dropdown booking-services">
								<a href="#"><?php esc_html_e('Extra Services','listeo_core'); ?> <span class="services-counter">0</span></a>
								<div class="panel-dropdown-content padding-reset">
									<div class="panel-dropdown-scrollable">
									
									<!-- Bookable Services -->
									<div class="bookable-services">
										<?php 
										$i = 0;
										$currency_abbr = get_option( 'listeo_currency' );
										$currency_postion = get_option( 'listeo_currency_postion' );
										$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr); 
										foreach ($bookable_services as $key => $service) { $i++; ?>
											<div class="single-service">
												<input type="checkbox" class="bookable-service-checkbox" name="_service[<?php echo sanitize_title($service['name']); ?>]" value="<?php echo sanitize_title($service['name']); ?>" id="tag<?php echo esc_attr($i); ?>"/>
												
												<label for="tag<?php echo esc_attr($i); ?>">
													<h5><?php echo esc_html($service['name']); ?></h5>
													<span class="single-service-price"> <?php 
													if(empty($service['price']) || $service['price'] == 0) {
														esc_html_e('Free','listeo_core');
													} else {
													 	if($currency_postion == 'before') { echo $currency_symbol.' '; } 
														echo esc_html($service['price']); 
														if($currency_postion == 'after') { echo ' '.$currency_symbol; } 
													}
													?></span>
												</label>

												<?php if(isset($service['bookable_quantity'])) : ?>
												<div class="qtyButtons">
													<input type="text" class="bookable-service-quantity" name="_service_qty[<?php echo sanitize_title($service['name']); ?>]" data-max="" class="" value="1">
												</div>
												<?php else: ?>
													<input type="hidden" class="bookable-service-quantity" name="_service_qty[<?php echo sanitize_title($service['name']); ?>]" data-max="" class="" value="1">
												<?php endif; ?>
											</div>
										<?php } ?>
									</div>
									<div class="clearfix"></div>
									<!-- Bookable Services -->


									</div>
								</div>
							</div>
						</div>
						<!-- Panel Dropdown / End -->
						<?php 
					endif; ?>
					<!-- Panel Dropdown / End -->
			<?php } ?>
                
				<?php if(!get_option('listeo_remove_coupons')): ?>
					<div class="col-lg-12 coupon-widget-wrapper">
						<a id="listeo-coupon-link" href="#"><?php esc_html_e('Have a coupon?','listeo_core'); ?></a>
						<div class="coupon-form">
								 
								<input type="text" name="apply_new_coupon" class="input-text" id="apply_new_coupon" value="" placeholder="<?php esc_html_e('Coupon code','listeo_core'); ?>"> 
								<a href="#" class="button listeo-booking-widget-apply_new_coupon"><div class="loadingspinner"></div><span class="apply-coupon-text"><?php esc_html_e('Apply','listeo_core'); ?></span></a>

						</div>
						<div id="coupon-widget-wrapper-output">
							<div  class="notification error closeable" ></div>
							<div  class="notification success closeable" id="coupon_added"><?php esc_html_e('This coupon was added','listeo_core'); ?></div>
						</div>
						<div id="coupon-widget-wrapper-applied-coupons">
							
						</div>
					</div>

					<input type="hidden" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_html_e('Coupon code','listeo_core'); ?>"> 
				<?php endif; ?>
				</div>
				    
				<!-- Book Now -->
				<input type="hidden" id="listing_type" value="<?php echo $post_meta['_listing_type'][0]; ?>" />
				<input type="hidden" id="listing_id" value="<?php echo $post_info->ID; ?>" />
				<input id="booking" type="hidden" name="value" value="booking_form" />
				<?php if(is_user_logged_in()) :
					
					if ($post_meta['_listing_type'][0] == 'event') { 
						$book_btn = esc_html__('Make a Reservation','listeo_core'); 
					} else { 
						if(get_post_meta($post_info->ID,'_instant_booking', true)){
							$book_btn = esc_html__('Book Now','listeo_core'); 	
						} else {
							$book_btn = esc_html__('Request Booking','listeo_core'); 	
						}
						
					}  ?>

					<a href="#" class="button book-now fullwidth margin-top-5"><div class="loadingspinner"></div><span class="book-now-text"><?php echo $book_btn; ?></span></a>

				<?php else : 
					$popup_login = get_option( 'listeo_popup_login','ajax' ); 
					if($popup_login == 'ajax') { ?>

						<a href="#sign-in-dialog" class="button fullwidth margin-top-5 popup-with-zoom-anim book-now-notloggedin"><div class="loadingspinner"></div><span class="book-now-text"><?php esc_html_e('Login to Book','listeo_core') ?></span></a>

					<?php } else { 

						$login_page = get_option('listeo_profile_page'); ?>
						<a href="<?php echo esc_url(get_permalink($login_page)); ?>" class="button fullwidth margin-top-5 book-now-notloggedin"><div class="loadingspinner"></div><span class="book-now-text"><?php esc_html_e('Login To Book','listeo_core') ?></span></a> 
					<?php } ?>
					
				<?php endif; ?>
	
				<?php if ($post_meta['_listing_type'][0] == 'event' && isset($post_meta['_event_date'][0])) { ?>
				<div class="booking-event-date">
					<strong><?php esc_html_e( 'Event date', 'listeo_core' ); ?></strong>
					<span><?php 
					
					$_event_datetime = $post_meta['_event_date'][0];
               		$_event_date = list($_event_datetime) = explode(' -', $_event_datetime);
 					
					echo $_event_date[0]; ?></span>
				</div>
				<?php } ?>	
				
				<?php 
					$currency_abbr = get_option( 'listeo_currency' );
					$currency_postion = get_option( 'listeo_currency_postion' );
					$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr); 
				?>
    <?php /* **************** added by larry@codeable ******************* into class-listeo-core-widgets */	if( function_exists('booking_validation_tsw_extend_listeo_booking_widget') ) : do_action('booking_validation_tsw_render_listeo_booking_widget', $post_id ); endif; ?>
				<div class="booking-estimated-cost" <?php if ($post_meta['_listing_type'][0] != 'event' ) { ?>style="display: none;"<?php } ?>>
					<?php if ($post_meta['_listing_type'][0] == 'event') { 
							$reservation_fee = (float) get_post_meta($post_info->ID,'_reservation_price',true);

							$normal_price = (float) get_post_meta($post_info->ID,'_normal_price',true);
							$event_default_price = $reservation_fee+$normal_price;
						}  ?>
					<strong><?php esc_html_e('Final Cost','listeo_core'); ?></strong>
					<span data-price="<?php if(isset($event_default_price)) { echo esc_attr($event_default_price); } ?>">
						<?php if($currency_postion == 'before') { echo $currency_symbol; } ?>
						<?php 
						if ($post_meta['_listing_type'][0] == 'event') {
						
							echo $event_default_price;
						} else echo '0'; ?>
						<?php if($currency_postion == 'after') { echo $currency_symbol; } ?>
					</span>
				</div>
				
				<div class="booking-estimated-discount-cost" style="display: none;">
					
					<strong><?php esc_html_e('Final Cost','listeo_core'); ?></strong>
					<span>
						<?php if($currency_postion == 'before') { echo $currency_symbol; } ?>
						
						<?php if($currency_postion == 'after') { echo $currency_symbol; } ?>
					</span>
				</div>
				<div class="booking-error-message" style="display: none;">
					<?php if($post_meta['_listing_type'][0] == 'service' && !$slots) {
						esc_html_e('Unfortunately we are closed at selected hours. Try different please.','listeo_core'); 
					} else {
						esc_html_e('Unfortunately this request can\'t be processed. Try different dates please.','listeo_core'); 
					} ?>
				</div>
		</form>
		<?php

		echo $after_widget; 

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}

/**
 * Booking Widget
 */
class Listeo_Core_Opening_Widget extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->widget_cssclass    = 'listeo_core boxed-widget opening-hours margin-bottom-35';
		$this->widget_description = __( 'Shows Opening Hours.', 'listeo_core' );
		$this->widget_id          = 'widget_opening_hours';
		$this->widget_name        =  __( 'Listeo Opening Hours', 'listeo_core' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Opening Hours', 'listeo_core' ),
				'label' => __( 'Title', 'listeo_core' )
			),
			
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		

		ob_start();
		
		extract( $args );
		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$queried_object = get_queried_object();
		$packages_disabled_modules = get_option('listeo_listing_packages_options',array());
			
		if ( $queried_object ) {
		    $post_id = $queried_object->ID;

			
			if(empty($packages_disabled_modules)) {
				$packages_disabled_modules = array();
			}

			$user_package = get_post_meta( $post_id,'_user_package_id',true );
			if($user_package){
				$package = listeo_core_get_user_package( $user_package );
			}
			$listing_type = get_post_meta( $post_id, '_listing_type', true );
		}

		if( !$listing_type  == 'service') {  
			return;
		}

		if( in_array('option_opening_hours',$packages_disabled_modules) ){ 
				
			if( isset($package) && $package->has_listing_opening_hours() != 1 ){
				return;
			}
		}
		$_opening_hours_status = get_post_meta($post_id, '_opening_hours_status',true);
		if(!$_opening_hours_status){
			return;
		}
		$has_hours = false;
		//check if has any horus saved
		$days = listeo_get_days(); 
		foreach ($days as $d_key => $value) {
				$opening_day = get_post_meta( $post_id, '_'.$d_key.'_opening_hour', true ); 
				$closing_day = get_post_meta( $post_id, '_'.$d_key.'_closing_hour', true ); 

				if( (!empty($opening_day) && $opening_day != "Closed")  || ( !empty($closing_day) && $closing_day != "Closed")) { 
					$has_hours = true;
				}
			}
		if(!$has_hours) {
			return;
		}
		echo $before_widget;
            if( listeo_check_if_open() ){ ?>
                <div class="listing-badge now-open"><?php esc_html_e('Now Open','listeo_core'); ?></div>
            <?php } else { ?>
                <div class="listing-badge now-closed"><?php esc_html_e('Now Closed','listeo_core'); ?></div>
        <?php 
        } 
		if ( $title ) {		
			echo $before_title.'<i class="sl sl-icon-clock"></i> ' . $title . $after_title; 
		} 
		?>
		<ul>
			<?php
			$clock_format = get_option('listeo_clock_format');

			foreach ($days as $d_key => $value) {
				$opening_day = get_post_meta( $post_id, '_'.$d_key.'_opening_hour', true ); 
				$closing_day = get_post_meta( $post_id, '_'.$d_key.'_closing_hour', true ); 

				?>
					
					<?php 

					if(is_array($opening_day)){	
						if(!empty($opening_day[0])) :

							echo '<li>'; echo esc_html($value); 
						
							echo '<span>';
							foreach ($opening_day as $key => $opening) {
								if(!empty($opening)){


									$closing = $closing_day[$key];
									
									if( $clock_format == 12 ){
										if(substr($opening, -1) !='M' && $opening != 'Closed'){
											$opening = DateTime::createFromFormat('H:i', $opening);
											if($opening){
												$opening = $opening->format('h:i A');
											}			
										}

										if(substr($closing, -1)!='M' && $closing != 'Closed'){
											
											$closing = DateTime::createFromFormat('H:i', $closing);
											if($closing){
												$closing = $closing->format('h:i A');
											}
											if($closing == '00:00') { $closing = '24:00'; }
										}
									} 
								
								?>
								
									<?php echo esc_html($opening); ?> 
									- 
									<?php  
									if( $clock_format == 12 && $closing == '12:00 AM'){
										echo  '12:00 PM';
									} else if ($clock_format != 12 && $closing == '00:00'){
										echo  '24:00';
									} else {
										echo esc_html($closing); 	
									}
									echo '<br>';
									?>
							<?php }
						}

							echo ' </span></li>';
						else: ?>
							<li><?php echo $value; ?><span><?php esc_html_e('Closed','listeo_core') ?></span>
						<?php endif;

					} else {

						//not array, old listings
						if(!empty($opening_day) && !empty($closing_day)) {
						echo '<li>'; echo esc_html($value); 
							if( $clock_format == 12 ){
								if(substr($opening_day, -1) !='M' && $opening_day != 'Closed'){
									$opening_day = DateTime::createFromFormat('H:i', $opening_day)->format('h:i A');			
								}

								if(substr($closing_day, -1)!='M' && $closing_day != 'Closed'){

									$closing_day = DateTime::createFromFormat('H:i', $closing_day)->format('h:i A');

									if($closing_day == '00:00') { $closing_day = '24:00'; }
								}
							} ?>
							<span>
								<?php echo esc_html($opening_day); ?> 
								- 
								<?php  
								if( $clock_format == 12 && $closing_day == '12:00 AM'){
									echo  '12:00 PM';
								} else if ($clock_format != 12 && $closing_day == '00:00'){
									echo  '24:00';
								} else {
									echo esc_html($closing_day); 	
								}
								
								?> </span>
						<?php } else { ?>
							<li><?php echo $value; ?><span><?php esc_html_e('Closed','listeo_core') ?></span>
						<?php } ?>

						</li>
					<?php } 
						 ?>
				

			<?php } //end foreach ?>
		</ul>
				
		<?php
		

		echo $after_widget; 

		$content = ob_get_clean();

		echo $content;

		
	}
}

/**
 * Booking Widget
 */
class Listeo_Core_Owner_Widget extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->widget_cssclass    = 'listeo_core widget_listing_owner boxed-widget margin-bottom-35';
		$this->widget_description = __( 'Shows Listing Owner box.', 'listeo_core' );
		$this->widget_id          = 'widget_listing_owner';
		$this->widget_name        =  __( 'Listeo Owner Widget', 'listeo_core' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Hosted By', 'listeo_core' ),
				'label' => __( 'Title', 'listeo_core' )
			),
			'phone' => array(
				'type'  => 'checkbox',
				'std'   => 'on',
				'label' => __( 'Phone number', 'listeo_core' )
			),
			'email' => array(
				'type'  => 'checkbox',
				'std'   => 'on',
				'label' => __( 'Email', 'listeo_core' )
			),
			'bio' => array(
				'type'  => 'checkbox',
				'std'   => 'on',
				'label' => __( 'Biographical info', 'listeo_core' )
			),
			'social' => array(
				'type'  => 'checkbox',
				'std'   => 'on',
				'label' => __( 'Social Sites profiles', 'listeo_core' )
			),
			'contact' => array(
				'type'  => 'checkbox',
				'std'   => 'on',
				'label' => __( 'Show Send message button', 'listeo_core' )
			),
			
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		// if ( $this->get_cached_widget( $args ) ) {
		// 	return;
		// }

		ob_start();

		extract( $args );
		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$queried_object = get_queried_object();
		if(!$queried_object){
			return;
		}
		$owner_id = $queried_object->post_author;
	
		if(!$owner_id) {
			return;
		}
		$owner_data = get_userdata( $owner_id );
		if ( $queried_object ) {
		    $post_id = $queried_object->ID;
			$listing_type = get_post_meta( $post_id, '_listing_type', true );
		}
		
		
		echo $before_widget;
            
		if ( $title ) {	?>
			<div class="hosted-by-title">
				<h4><span><?php echo $title; ?></span> <a href="<?php echo esc_url(get_author_posts_url( $owner_id )); ?>">
					<?php echo listeo_get_users_name($owner_id); ?></a></h4>
				<a href="<?php echo esc_url(get_author_posts_url( $owner_id )); ?>" class="hosted-by-avatar"><?php echo get_avatar( $owner_id, 56 );  ?></a>
			</div>
			
		<?php } 
		$show_bio = (isset($instance['bio']) && !empty($instance['bio'])) ? true : false ;

		if( $show_bio && !empty($owner_data->user_description) ){
			?>
			<div class="hosted-by-bio">
				<?php echo wpautop(esc_html($owner_data->user_description)); ?>	
			</div>
			

			<?php
		}
		
		$show_email = (isset($instance['email']) && !empty($instance['email'])) ? true : false ;
		$show_phone = (isset($instance['phone']) && !empty($instance['phone'])) ? true : false ;
		$show_social = (isset($instance['social']) && !empty($instance['social'])) ? true : false ;
		$visibility_setting = get_option('listeo_user_contact_details_visibility'); // hide_all, show_all, show_logged, show_booked,  
		if($visibility_setting == 'hide_all') {
			$show_details = false;
		} elseif ($visibility_setting == 'show_all') {
			$show_details = true;
		} else {
			if(is_user_logged_in() ){
				if($visibility_setting == 'show_logged'){
					$show_details = true;
				} else {
					$show_details = false;
				}
			} else {
				$show_details = false;
			}
		}	
		if($show_details){
			if(  $show_email || $show_phone ) {  ?>
				<ul class="listing-details-sidebar">
					<?php if($show_phone) {  ?>
						<?php if(isset($owner_data->phone) && !empty($owner_data->phone)): ?>
							<li><i class="sl sl-icon-phone"></i> <?php echo esc_html($owner_data->phone); ?></li>
						<?php endif; 
					} 
					if($show_email) { 	
						if(isset($owner_data->user_email)): $email = $owner_data->user_email; ?>
							<li><i class="fa fa-envelope-o"></i><a href="mailto:<?php echo esc_attr($email);?>"><?php echo esc_html($email);?></a></li>
						<?php endif; ?>
					<?php } ?>
					
				</ul>
			<?php }
		} else { 
			if($visibility_setting != 'hide_all') { ?>
			<p id="owner-widget-not-logged-in"><?php printf( esc_html__( 'Please %s sign %s in to see contact details.', 'listeo_core' ), '<a href="#sign-in-dialog" class="sign-in popup-with-zoom-anim">', '</a>' ) ?></p>
		<?php } 
		}?>
		<?php if($show_details && $show_social){ ?>
			<ul class="listing-details-sidebar social-profiles">
				<?php if(isset($owner_data->twitter) && !empty($owner_data->twitter)) : ?><li><a href="<?php echo esc_url($owner_data->twitter) ?>" class="twitter-profile"><i class="fa fa-twitter"></i> Twitter</a></li><?php endif; ?>
				<?php if(isset($owner_data->facebook) && !empty($owner_data->facebook)) : ?><li><a href="<?php echo esc_url($owner_data->facebook) ?>" class="facebook-profile"><i class="fa fa-facebook-square"></i> Facebook</a></li><?php endif; ?>
				<?php if(isset($owner_data->instagram) && !empty($owner_data->instagram)) : ?><li><a href="<?php echo esc_url($owner_data->instagram) ?>" class="instagram-profile"><i class="fa fa-instagram"></i> Instagram</a></li><?php endif; ?>
				<?php if(isset($owner_data->linkedin) && !empty($owner_data->linkedin)) : ?><li><a href="<?php echo esc_url($owner_data->linkedin) ?>" class="linkedin-profile"><i class="fa fa-linkedin"></i> LinkedIn</a></li><?php endif; ?>
				<?php if(isset($owner_data->youtube) && !empty($owner_data->youtube)) : ?><li><a href="<?php echo esc_url($owner_data->youtube) ?>" class="youtube-profile"><i class="fa fa-youtube"></i> YouTube</a></li><?php endif; ?>
				<?php if(isset($owner_data->whatsapp) && !empty($owner_data->whatsapp)) : ?><li><a href="<?php if(strpos($owner_data->whatsapp, 'http') === 0) { echo esc_url($owner_data->whatsapp); } else { echo "https://wa.me/".esc_attr($owner_data->whatsapp); } ?>" class="whatsapp-profile"><i class="fa fa-whatsapp"></i> WhatsApp</a></li><?php endif; ?>
				<?php if(isset($owner_data->skype) && !empty($owner_data->skype)) : ?><li>
					<a href="<?php if(strpos($owner_data->skype, 'http') === 0) { echo esc_url($owner_data->skype); } else { echo "skype:+".$owner_data->skype."?call"; } ?>" class="skype-profile"><i class="fa fa-skype"></i> Skype</a></li><?php endif; ?>
				
				<!-- <li><a href="#" class="gplus-profile"><i class="fa fa-google-plus"></i> Google Plus</a></li> -->
			</ul>
		<?php } ?>
			<?php 
			if(is_user_logged_in()):
				if((isset($instance['contact']) && !empty($instance['contact']))) : ?>
				<!-- Reply to review popup -->
				<div id="small-dialog" class="zoom-anim-dialog mfp-hide">
					<div class="small-dialog-header">
						<h3><?php esc_html_e('Send Message', 'listeo_core'); ?></h3>
					</div>
					<div class="message-reply margin-top-0">
						<form action="" id="send-message-from-widget" data-listingid="<?php echo esc_attr($post_id); ?>">
							<textarea 
							required
							data-recipient="<?php echo esc_attr($owner_id); ?>"  
							data-referral="listing_<?php echo esc_attr($post_id); ?>"  
							cols="40" id="contact-message" name="message" rows="3" placeholder="<?php esc_attr_e('Your message to ','listeo_core'); echo $owner_data->first_name; ?>"></textarea>
							<button class="button">
							<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i><?php esc_html_e('Send Message', 'listeo_core'); ?></button>	
							<div class="notification closeable success margin-top-20"></div>

						</form>
						
					</div>
				</div>


				<a href="#small-dialog" class="send-message-to-owner button popup-with-zoom-anim"><i class="sl sl-icon-envelope-open"></i> <?php esc_html_e('Send Message', 'listeo_core'); ?></a>
				<?php endif; ?>
			<?php endif; ?>
				
		<?php
		

		echo $after_widget; 

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}


/**
 * Core class used to implement a Recent Posts widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class Listeo_Recent_Posts extends WP_Widget {

    /**
     * Sets up a new Recent Posts widget instance.
     *
     * @since 2.8.0
     * @access public
     */
    public function __construct() {
        $widget_ops = array(
            'classname' => 'listeo_recent_entries',
            'description' => __( 'Your site&#8217;s most recent Posts.','listeo' ),
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'listeo-recent-posts', __( 'Listeo Recent Posts','listeo' ), $widget_ops );
        $this->alt_option_name = 'listeo_recent_entries';
    }

    /**
     * Outputs the content for the current Recent Posts widget instance.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     * @param array $instance Settings for the current Recent Posts widget instance.
     */
    public function widget( $args, $instance ) {
        if ( ! isset( $args['widget_id'] ) ) {
            $args['widget_id'] = $this->id;
        }

        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts','listeo' );

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
        if ( ! $number )
            $number = 5;
        $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

        /**
         * Filters the arguments for the Recent Posts widget.
         *
         * @since 3.4.0
         *
         * @see WP_Query::get_posts()
         *
         * @param array $args An array of arguments used to retrieve the recent posts.
         */
        $r = new WP_Query( apply_filters( 'widget_posts_args', array(
            'posts_per_page'      => $number,
            'no_found_rows'       => true,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true
        ) ) );

        if ($r->have_posts()) :
        ?>
        <?php echo $args['before_widget']; ?>
        <?php if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        } ?>
        <ul class="widget-tabs">
        <?php while ( $r->have_posts() ) : $r->the_post(); ?>
            <li>
                <div class="widget-content">
                    <?php if ( has_post_thumbnail() ) { ?>
                    <div class="widget-thumb">
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('listeo-post-thumb'); ?></a>
                    </div>
                    <?php } ?>

                    <div class="widget-text">
                        <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                        <span><?php echo get_the_date(); ?></span>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </li>
        <?php endwhile; ?>
        </ul>
        <?php echo $args['after_widget']; ?>
        <?php
        // Reset the global $the_post as this query will have stomped on it
        wp_reset_postdata();

        endif;
    }

    /**
     * Handles updating the settings for the current Recent Posts widget instance.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $new_instance New settings for this instance as input by the user via
     *                            WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Updated settings to save.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
        return $instance;
    }

    /**
     * Outputs the settings form for the Recent Posts widget.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $instance Current settings.
     */
    public function form( $instance ) {
        $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
?>
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','listeo' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

        <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:','listeo' ); ?></label>
        <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

        <p><input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
        <label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?','listeo' ); ?></label></p>
<?php
    }
}



/**
 * Booking Widget
 */
class Listeo_Coupon_Widget extends Listeo_Core_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->widget_cssclass    = 'listeo_core boxed-widget coupon-widget margin-bottom-35';
		$this->widget_description = __( 'Shows Listing Coupon.', 'listeo_core' );
		$this->widget_id          = 'widget_coupon';
		$this->widget_name        =  __( 'Listeo Coupon Widget ', 'listeo_core' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Coupon', 'listeo_core' ),
				'label' => __( 'Title', 'listeo_core' )
			),
			
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		

		ob_start();
		
		extract( $args );
		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$queried_object = get_queried_object();
		
		$packages_disabled_modules = get_option('listeo_listing_packages_options',array());
		
		if ( $queried_object ) {
		    $post_id = $queried_object->ID;
			$listing_type = get_post_meta( $post_id, '_listing_type', true );
			
			if(empty($packages_disabled_modules)) {
				$packages_disabled_modules = array();
			}

			$user_package = get_post_meta( $post_id,'_user_package_id',true );
			if($user_package){
				$package = listeo_core_get_user_package( $user_package );
			}
		}

	   
		if( in_array('option_coupons',$packages_disabled_modules) ){ 
				
			if( isset($package) && $package->has_listing_coupons() != 1 ){
				return;
			}
		}
		$_opening_hours_status = get_post_meta($post_id, '_coupon_section_status',true);
		if(!$_opening_hours_status){
			return;
		}
		//get coupon

		$coupon_id =  get_post_meta($post_id, '_coupon_for_widget',true);
		if(!($coupon_id)){
			return false;
		}

		$coupon_post = get_post($coupon_id);
		//$coupon = new WC_Coupon($coupon_id);
		if(!$coupon_post){
			return;
		}

		if($coupon_post){
			$coupon_data = new WC_Coupon($coupon_id);
		}

		

		//echo $before_widget;
           	$coupon_bg = get_post_meta($coupon_id,'coupon_bg-uploader-id',true);
			$coupon_bg_url = wp_get_attachment_url($coupon_bg); 
	
		?>
				<!-- Coupon Widget -->
			<div class="coupon-widget"  style="<?php if($coupon_bg): ?>background-image: url(<?php echo esc_url($coupon_bg_url); ?>); <?php endif; ?> margin:20px 0px;">
				<a class="coupon-top">
					
					<?php $coupon_amount = wc_format_localized_price( $coupon_data->get_amount());  
					$currency_abbr = get_option( 'listeo_currency' );
					$currency_postion = get_option( 'listeo_currency_postion' );
					$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);
					
					if( $coupon_data->get_discount_type() == 'fixed_product') { ?>
						<h3><?php echo sprintf( esc_html__('Get %1$s%2$s discount!','listeo_core'),$coupon_amount,$currency_symbol); ?></h3>
					<?php } else { ?>
						<h3><?php echo sprintf( esc_html__('Get %1$s%% discount!','listeo_core'),$coupon_amount); ?></h3>
					<?php } ?>

					
					<?php
					$expiry_date = $coupon_data->get_date_expires();
					if($expiry_date) : ?>
					<div class="coupon-valid-untill"><?php esc_html_e('Expires','listeo_core'); ?> <?php echo esc_html( $expiry_date->date_i18n( 'F j, Y' ) );  ?></div>
					<?php endif; ?>
					<?php if($coupon_data->get_description()) : ?>
						<div class="coupon-how-to-use"><?php echo $coupon_data->get_description(); ?></div>
					<?php endif; ?>
				</a>
				<div class="coupon-bottom">
					<div class="coupon-scissors-icon"></div>
					<div class="coupon-code"><?php echo $coupon_data->get_code(); ?></div>
				</div>
			</div>

		
				
		<?php
		

		//echo $after_widget; 

		$content = ob_get_clean();

		echo $content;

		
	}
}

register_widget( 'Listeo_Core_Featured_Properties' );
register_widget( 'Listeo_Core_Bookmarks_Share_Widget' );
register_widget( 'Listeo_Core_Booking_Widget' );
register_widget( 'Listeo_Core_Search_Widget' );
register_widget( 'Listeo_Core_Opening_Widget' );
register_widget( 'Listeo_Core_Owner_Widget' );
register_widget( 'Listeo_Core_Contact_Vendor_Widget' );
register_widget( 'Listeo_Recent_Posts' );
register_widget( 'Listeo_Coupon_Widget' );


function custom_get_post_author_email($atts){
	$value = '';
	global $post;
	$post_id = $post->ID;
	$email = get_post_meta($post_id,'_email',true);
	if(!$email){
		$object = get_post( $post_id );
		//just get the email of the listing author
		$owner_ID = $object->post_author;
		//retrieve the owner user data to get the email
		$owner_info = get_userdata( $owner_ID );
		if ( false !== $owner_info ) {
			$email = $owner_info->user_email;
		}
	}
  	return $email;
}
add_shortcode('CUSTOM_POST_AUTHOR_EMAIL', 'custom_get_post_author_email');
add_shortcode('LISTING_OWNER_EMAIL', 'custom_get_post_author_email');

//_email
function custom_get_post_listing_title($atts){
	$value = '';
	global $post;
	$post_id = $post->ID;
	if($post_id){
		$value = get_the_title($post_id);
	}
  return $value;
}
add_shortcode('LISTING_TITLE', 'custom_get_post_listing_title');

//_email
function custom_get_post_listing_url($atts){
	$value = '';
	global $post;
	$post_id = $post->ID;
	if($post_id){
		$value = get_permalink($post_id);
	}
  return $value;
}
add_shortcode('LISTING_URL', 'custom_get_post_listing_url');