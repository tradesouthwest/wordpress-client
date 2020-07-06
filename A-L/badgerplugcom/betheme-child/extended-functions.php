<?php 
/**
 * Exclusions for quote cart button
 * @uses   $page_id Must equal numeric value of catalog post_type page
 *
 * @see    https://developer.wordpress.org/reference/functions/is_page/
 * @author Larry @codeable
 */ 
function betheme_child_request_aquote_echo()
{   
    global $post;
    $parent_id        = $post->post_parent; 
    $post_parent_slug = get_post_field( 'post_name', $parent_id );
    $page_catalog = absint(3360);
    $page_request = absint(5359);
    // do nothing if
    if ( is_home() || is_front_page() || is_page( array( 
                                                    'products', 
                                                    'roll-protection-products',
                                                    'about',
                                                    'resources',
                                                    'services',
                                                    'industries',
                                                    'contact',
                                                    'component-recycling-program',
                                                    ) ) ) 
    { 
        echo '<a href="'. esc_url($action_link) .'" class="action_button'. esc_attr($action_class) .'" '. wp_kses_data($action_target) .'>'. wp_kses(mfn_opts_get('header-action-title'), mfn_allowed_html('button')) .'</a>';
    } 
    
        // Get quotes count in (cart)
        elseif( is_singular() || is_singular('single-product') ) 
        { 
        $url_icon = 'https://staging-badgerplugcom.kinsta.cloud/wp-content/uploads/2020/07/cart-icon_256x256.png';
        $quote_cnt = '';
        $quote_cnt = do_shortcode('[yith_ywraq_number_items class="qcnt" item_name="Item" item_plural_name="Items" show_url="no"]');
        
        // Clean and echo
        ob_start();
    
        echo '<a class="action_button yith-items-button" href="' . home_url( '/' ) . 'request-quote/" title="quotes">
    <span id="yithQuoteItem"><i class="iqcart"><img src="' . esc_url($url_icon) . '" height="14" width="16" alt=""/></i> 
    <span id="quoteItemQnty"> &nbsp;' . $quote_cnt . ' </span> 
    <span class="lnkspc"> &nbsp;' . __( " in Quote" ) . '</span></span></a>';
    
    $qbutton = ob_get_clean();
        
        echo $qbutton;

        }
        else
        {
    echo '<a href="'. esc_url($action_link) .'" class="action_button'. esc_attr($action_class) .'" '. wp_kses_data($action_target) .'>'. wp_kses(mfn_opts_get('header-action-title'), mfn_allowed_html('button')) .'</a>';
    }
    
}
add_action( 'betheme_child_request_aquote_hook', 'betheme_child_request_aquote_echo' );

/**
 * Get product attributes to display on yith-quote table
 * @param string $attribute_name Taxonomy name
 * @param string $product_id     Global value
 *
 * @return array                 Attribute value
 */
function betheme_child_get_product_terms_carton( $attribute_name, $product_id )
{

    $object_id = wc_attribute_taxonomy_id_by_name($attribute_name); 

    $attribute_key = wc_attribute_taxonomy_name($attribute_name);
    $product_terms = wc_get_product_terms($product_id, $attribute_key, array('fields' => 'names'));

     // check if not empty, then display
     if (!empty($product_terms)) {

         $attribute = array_shift($product_terms);
         return $attribute;
     } else {

         // no attribute under this name
         return '';
     }
} 
/**
 * Register and parse custom product meta data
 * 
 * _carton_cnt_label cartons_pallet_label_ carton_cnt_value_ cartons_pallet_value_
 * post.php?post=5506&action=edit add to woocommerce_order_items
 */
function betheme_child_register_ywraq_custom_attributes()
{
foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
return false;
}

function fix_no_orders_found_bug_for_woocommerce( $query_args ) {
  if ( isset( $query_args['post_status'] ) && empty( $query_args['post_status'] ) ) {
    unset( $query_args['post_status'] );
  }
  return $query_args;
}
//add_filter( 'request', 'fix_no_orders_found_bug_for_woocommerce', 1, 1 ); 
