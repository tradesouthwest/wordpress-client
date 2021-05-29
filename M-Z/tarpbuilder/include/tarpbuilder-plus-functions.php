<?php 
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// add taxonomy term to body_class
function tarpbuilder_plus_custom_taxonomy_in_body_class( $classes ){
  if( is_singular( 'product' ) )
  {
    $custom_terms = get_the_terms(0, 'product_cat');
    if ($custom_terms) {
      foreach ($custom_terms as $custom_term) {
        $classes[] = 'product_cat_' . $custom_term->slug;
      }
    }
  }
  return $classes;
}
add_filter( 'body_class', 'tarpbuilder_plus_custom_taxonomy_in_body_class' );

// static function returns true if page is custom-tarp

function tarpbuilder_plus_contingencies()
{
	global $post;
	// get the product category term objects
	$terms = wp_get_post_terms( $post->ID, 'product_cat' );
	foreach ( $terms as $term ) $categories[] = $term->slug;
		if( in_array('custom-tarps', $categories) ) {
			return true;
		} else { 
				return false; }
 
}
remove_filter( 'get_terms', 'tarpbuilder_plus_contingencies', 10, 3 );

add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'tarpbuilder_plus_dropdown_choice_addclass' );
/**
 * Change the custom dropdown class name on the front end
 */
function tarpbuilder_plus_dropdown_choice_addclass( $args )
{
$tpconting = tarpbuilder_plus_contingencies();
    if( !$tpconting ) return;
        if( is_product() ) {
                $args['class'] = 'tbselect-variable';
        }  
        return $args;    
}


/* *********************************************** ADMIN PANEL */
/**
 * Create the section beneath the products tab
 * 
 * wp-admin/admin.php?page=wc-settings&tab=products&section=tarpbuilder-plus
 **/
add_filter( 'woocommerce_get_sections_products', 'tarpbuilder_plus_add_section' );
function tarpbuilder_plus_add_section( $sections ) {
	
	$sections['tarpbuilder-plus'] = __( 'TarpBuilder', 'tarpbuilder' );
	return $sections;
	
}
/**
 * Add settings to the specific above section
 */
add_filter( 'woocommerce_get_settings_products', 'tarpbuilder_plus_add_settings', 12, 2 );
function tarpbuilder_plus_add_settings( $settings, $current_section ) 
{
	$settings_tarpbuilder = array();
	/**
	 * Check the current section is what we want
	 **/
	if ( $current_section == 'tarpbuilder-plus' ) 
	{ 
		
		$settings_tarpbuilder = array(
		array(
		'title' => __( 'TarpBuilder Settings Section can be found here:', 'tarpbuilder' ),
		'type' => 'title',
		'id'   => 'tarpbuilder_link_title'
		),
	
		array( 
			
			'type' => 'checkbox',
			'class' => 'tarpbuilder-admin-hidden',
			'value' => 0,
        'desc' => '<a class="button wnd-centered" href="'. admin_url('options-general.php?page=tarpbuilder-plus') .'">TarpBuilder-Plus</a>',
		'id'   => 'tarpbuilder_link_topage'
		),
		
		array( 
			'type' => 'sectionend', 
			'id' => 'tarpbuilder-plus'
		),
		);	
    
		//generate_settings_html()
		return $settings_tarpbuilder; 
	

		/*
		* If not, return the standard settings
		*/
		} else {
			return $settings;
    }
}
