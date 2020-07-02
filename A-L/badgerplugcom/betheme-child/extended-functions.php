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
    if ( is_home() || is_front_page() || is_page( array( 
                                                    'about',
                                                    'resources',
                                                    'services',
                                                    'industries',
                                                    'contact',
                                                    'component-recycling-program',
                                                    'products'
                                                ) ) 
        || 'products' == $post->post_parent_slug || 'products'== $post->post_parent_slug > 0 )
     { 
            return; 
        
    } 

    if ( is_page($page_catalog) || $page_catalog == $post->post_parent  
    || is_page($page_request) || $page_request == $post->post_parent ) : 
    
    // Get quotes count in (cart)
    
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
    else: 
        echo '';
    endif;
    
}
add_action( 'betheme_child_request_aquote_hook', 'betheme_child_request_aquote_echo' );

/** 
 *Looking for page page-id- [woocommerce-cart] woocommerce-page yith-request-a-quote-page  
 * @author Larry @codeable
 * @return Boolean Set to false since orange button should not show on these pages.
 */ 
function betheme_child_check_for_display_button()
{
    global $post;
     
    $page_prods       = absint(2852); //id of products page    
    // Check if on the right page(s) to show orange button on
    if ( is_page($page_prods) || $page_prods == $post->post_parent
        || $page_prods == $post->post_parent > 0 )
    {
   return true;
    } else {
    return false; 
    }
}
    
/**
 * Find if post has a parent in particular category
 * @author Larry @codeable
 */
function betheme_child_get_posts_children($parent_id)
{
    $children = array();
    // grab the posts children
    $posts = get_posts( array( 'numberposts' => -1, 
                                'post_status' => 'publish', 
                                //'post_type' => 'any', 
                                'post_parent' => $parent_id, 
                                'suppress_filters' => false 
                            ) );
    // now grab the grand children
    foreach( $posts as $child ){
        // recursion!! hurrah
        $gchildren = betheme_child_get_posts_children($child->ID);
        // merge the grand children into the children array
        if( !empty($gchildren) ) {
            $children = array_merge($children, $gchildren);
        }
    }
    // merge in the direct descendants we found earlier
    $children = array_merge($children,$posts);
    
        return $children;
} 
function betheme_child_valid_attribute_name( $attribute_name ) {
    if ( strlen( $attribute_name ) >= 28 ) {
            return new WP_Error( 'error', sprintf( __( 'Slug "%s" is too long (28 characters max). Shorten it, please.', 'woocommerce' ), sanitize_title( $attribute_name ) ) );
    } elseif ( wc_check_if_attribute_name_is_reserved( $attribute_name ) ) {
            return new WP_Error( 'error', sprintf( __( 'Slug "%s" is not allowed because it is a reserved term. Change it, please.', 'woocommerce' ), sanitize_title( $attribute_name ) ) );
    }

    return true;
}
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
         return 'na';
     }
} 
