<?php
/**
 * Register widget with WordPress.
 */
class Promocoder_Cat_Widget extends WP_Widget {

function __construct() {
    parent::__construct(
    // Base ID of widget
    'Promocoder_Cat_Widget',
    __( 'Promocoder Category Tag Widget', 'promocoder' ), // Name
	array(
        'description' => __( 'Category Widget for Promocoder Plugin',
                            'promocoder' ),
        ));
}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) 
	{
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
			if ( ! empty( $instance[ 'title' ] ) )
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) 
			. $args['after_title'];

			?><div class="promocoder-widget"><?php  
			$list = wp_list_categories( array( 
				'hide_empty' => 1, 
				'child_of'   => 0,
				'show_count' => 1,
 				'title_li'   => '',
				'taxonomy'   => 'promocoder_categories' 
				) );
			printf( $list ); 
			
	print( '</div>
	<div class="hrds-widget widget-divider">
			<hr>' );
/** 
 * Retrieve an array of objects for each term in post_tag taxonomy. 
 */
		$tags = get_terms( 'promocoder_tags', array(
						   'hide_empty' => false,
						) );
    if ( $tags > 0 ):  
		print(
		'<aside class="hrds-cats">');
	foreach ( $tags as $tag ) :
    echo '<span><a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" 
	title="' . esc_attr( $tag->name ) . '">' . esc_html( $tag->name ) . '</a> | </span>';
	endforeach;
	print( '</aside>
	</div>' );
	endif;
	// return after widget parts
    echo $args['after_widget'];
	}
	
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	// Widget Backend
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else { $title = __( 'New title', 'promocoder' ); }
    ?>

	
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'promocoder' ); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php
	}
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	return $instance;
	}
} // Ends class Promocoder_Cat_Widget
?>