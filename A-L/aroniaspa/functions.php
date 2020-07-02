<?php
/**
 * storefront child theme functions.php file.
 * @package storefront-child
 */

//Storefront adds it's own stylesheet for child themes

// Put your custom PHP below
function storefront_child_theme_enqueue_styles() {

    $parent_style = 'storefront'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'storefront-child',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
    wp_register_style( 'strfrnt-entry-set', false );
    /* 
    wp_enqueue_script( 'storefrnt-child',
        get_stylesheet_directory_uri() . '/js/storefrnt-child.js',
                        array ( 'jquery' ),
                        '',
                        true);  */
}
add_action( 'wp_enqueue_scripts', 'storefront_child_theme_enqueue_styles' );

/**
 * Change the placeholder image
 */
add_filter('woocommerce_placeholder_img_src', 'storefront_child_woocommerce_placeholder_img_src');

function storefront_child_woocommerce_placeholder_img_src( $src ) {
	$upload_dir = wp_upload_dir();
	$uploads = untrailingslashit( $upload_dir['baseurl'] );
	// https://www.aroniaspa.co.za/wp-content/uploads/2018/09/foggy_birds_placholder.png
	
	$src = $uploads . '/2018/09/foggy_birds_placholder.png';
	 
	return $src;
} 

/**
* Remove "Default Sorting" Dropdown @ StoreFront Shop & Archive Pages
* 
* @author Larry Codeable
* @compatible Woo 3.4.3
*/
add_action( 'init', 'storefront_child_delay_category_ordering' );
function storefront_child_delay_category_ordering()
{   
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10 );

}


/**
 * Change the layout of sub sub category pages to include table
 * @author Larry Codeable
 * @compatible Woo 3.4.3
 * @array parent_cats= 7,10,8,18... archive tax-product_cat term-promotions term-18 
 */
add_action('woocommerce_before_shop_loop', 'storefront_child_woocommerce_identfy_cat', 10 );
function storefront_child_woocommerce_identfy_cat()
{
    global $post;
    
    $ispage_term      = false;
    $parent_id        = $post->post_parent; 
    $post_parent_slug = get_post_field( 'post_name', $parent_id );
    $pageis           = 'product-category'; 
    //$terms        = get_the_terms( $post->ID, 'product_cat' ); 
    $term_term_id = get_queried_object()->term_id; 
    if( $term_term_id > 0 ) $ispage_term = true;    
        // Check and see if the term is not a top-level parent. If so, apply shortcode 
        if( $ispage_term ) : 
        ob_start();

echo do_shortcode("[WPT_SHOP column_keyword='thumbnails,product_title,price,quantity,action' 
column_title='Package,Product Title,Price,Quantity,Action' 
mobile_hide='' product_cat_ids='" . $term_term_id . "' template='none'] "); 
        
        $html = ob_get_clean();
        
        printf( $html );

        //unset($term); //clean house 
       //add_action('wp_footer',
        endif; 

        return false;
} 

/**
 * Set inline style to enqued if parent has two children
 * @param $terms get_queried_object Taking long-route method to avoid getting ids.
 * @param string $ispage_term       Is true
 */
function storefront_child_detect_archive_pages()
{
 
    global $post;

    $term        = $styl = '';
    $ispage_term = false;

    // looking for term to display tables (archive tax-product_cat term-spa-packages term-7 )
    $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
    $parent = get_term($term->parent, get_query_var('taxonomy') ); // get parent term
    $children = get_term_children($term->term_id, get_query_var('taxonomy')); // get children

    if(($parent->term_id!="" && sizeof($children)>0)) {
        // has parent and child
        $ispage_term = false;
        }elseif(($parent->term_id!="") && (sizeof($children)==0)) {
        // has parent, no child
        $ispage_term = false;
        }elseif(($parent->term_id=="") && (sizeof($children)>0)) {
        // no parent, has child
        $ispage_term = true;
        }
    // be sure inline styles are not queued
    wp_dequeue_style( 'strfrnt-entry-set' );

    if( $ispage_term ) { 
        wp_enqueue_style( 'strfrnt-entry-set' );
    $styl .= '.archive.tax-product_cat.term-' . $term->term_id . ' .wpt_product_table_wrapper{display:none;} .archive.tax-product_cat.term-' . $term->term_id . ' ul.products.columns-3{display:block;}';
    } else { 
        wp_enqueue_style( 'strfrnt-entry-set' );
    $styl .= '.archive.tax-product_cat.term-' . $term->term_id . ' .wpt_product_table_wrapper{display:block;} .archive.tax-product_cat.term-' . $term->term_id . ' ul.products.columns-3{display:none;}';
    }
    
        wp_add_inline_style( 'strfrnt-entry-set', $styl ); 
        unset($term); //clean house 
    
}
add_action( 'wp_enqueue_scripts', 'storefront_child_detect_archive_pages', 15 );
