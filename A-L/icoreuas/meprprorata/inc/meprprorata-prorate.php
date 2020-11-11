<?php 
/**
 * @package meprprorata
 * @subpackage meprprorata/inc
 */
defined( 'ABSPATH' ) or exit;
/**
 * Check for expiry date and add - divide by 12 months to get prorate. 
 * Price out should temp change product price for one time trnx
 * 
 * @param string $product_id    Product ID
 * @param string $product_price Product original price amount
 * @param string $prorata       Calculate prorate new price
 *  MeprHooks::apply_filters('mepr-price-string', $price_str, $obj, $show_symbol);
 * $tmp_sub = new MeprSubscription(); $tmp_sub->price;
 */
//if( !function_exists( 'meprprorata_calculate_prorata_price' ) ) : 
function meprprorata_calculate_prorata_pricing( $post )
{
    global $post, $screens;
    //$screens = get_current_screen();
    $screens = ['post', 'memberpressproduct'];
    //if( is_single() && $screens ) : 
   
    $product_price = $exps_on = $prorata = $today = $start_date = $end_date = 
    $product_id    = $price_out = $price_str = $newprice = $remaining_months_default = '';
    $product       = new MeprProduct();
    $product_id    = esc_attr( $product->ID ); //get_post(get_the_ID());

    $prods         = array( '4500', '4504', '4758' );

    if( in_array( $product_id, $prods ) ) 
    { 
    
        //get product meta
        $product_price = get_post_meta( $product_id, 
                                        '_mepr_product_price', 
                                        true 
                                        );
        $original_price = get_post_meta( $product_id, 
                                         '_original_product_price', 
                                         true 
                                         );
        $txn_xpr        = get_post_meta( $product_id, 
                                         '_mepr_expire_fixed', 
                                         true 
                                         );
        /**
         * Keep price from (re)prorating current month rate.
         * Stops $_POST from being posted twice.
         */
        /*$product_value = isset( $_POST['_mepr_product_price'] ) 
         ? '1' : sanitize_text_field( $_POST['_mepr_product_price'] );
        */
        //calculate months 2629743sec 1 month (30.44 days) 
        $args   = meprprorata_get_remaining_months( $post->ID );
    $months = $args['remaining_months']; 
        $today      = date( 'Y-m-d' );
        $start_date = strtotime( $today );                  //epoch time
        $end_date   = strtotime( $txn_xpr );                //formatted Y-m-d
        $due_date   = ( $end_date - $start_date )/60/60/24; //exp date - now =days
    
        //$ per month     
            
            $prorata          = $original_price / 12;       //ttl divided by year
            $remaining_months = $due_date / 30;           //days to months
            $new_prod_price   = ($prorata * $months);
    $newprice   = number_format_i18n( ( $new_prod_price ), 2 );

    if( $remaining_months <= 1 ) 
        { 
   
        /**
         * Less than 1 solid month and price goes back to regular price.
         */
                update_post_meta( $product_id,  
                                  '_mepr_product_price', 
                                  sanitize_text_field( $original_price ) );

        } else {
        /**
         * More than 1 solid month and price gets prorated price.
         */     
                update_post_meta( $product_id, 
                                  '_mepr_product_price', 
                                  sanitize_text_field( $newprice ) );

        }
    }
     
//endif;
}    

//add_action( 'save_post', 'meprprorata_calculate_prorata_prices' );
add_action( 'mepr-membership-save-meta', 'meprprorata_calculate_prorata_pricing' );
//add_filter('format_price_string', 'meprprorata_calculate_prorata_dates' );

/**
 * Fires after the post time/date setting in the Publish meta box.
 *
 * @since 1.0.2
 * @since 1.0.3 Added the `$post` parameter.
 *
 * @param WP_Post $post WP_Post object for the current post.
 */
function meprprorata_original_price_meta_field($post) 
{
    global $post;
    $value  = get_post_meta( $post->ID, '_original_product_price', true);
    if( empty( $value ) ) $value = '0';
    $args   = meprprorata_get_remaining_months( $post->ID );
    $months = $args['remaining_months']; 

        printf( '<fieldset class="misc-pub-section">
        <label>FULL price is used to calulate prorate</label>
        <input id="%s" class="text-field" type="%s" name="%s" value="%s">
            </fieldset> %s',    
               'original_product_price',
               'text',
               'original_product_price',
               $value,
               $months . ' months left'
            );         
}
/**
 * Add meta box to specific editor pages
 * 
 * @param string | array $prods Ids of specific products to prorate.
 * 
 */
function meprprorata_original_price_add_meta_box($post)
{
    global $post;
    $screens = ['post', 'memberpressproduct'];
    $prods = array( '4500', '4504', '4758' );

    if( in_array( $post->ID, $prods ) ) 
    {
        add_meta_box(
            'original_product_price_id',              // Unique ID
            __('Enter the FULL price here'),          // Box title
            'meprprorata_original_price_meta_field',  // Content callback
            $screens,
            'side',
            'high' 
            );
    }
}
add_action('add_meta_boxes', 'meprprorata_original_price_add_meta_box' );
/**
 * Saves the incoming $_POST data to the post meta data (with the post
 * identified by the incoming ID) if and only if the value of the
 * data isn't empty.
 * 
 * @param    int    $post_id    The associated post ID
 */
function meprprorata_save_original_price_save_postdata($post_id )
{
/*
    $meta_value = $_POST['original_product_price'];
    if ( isset( $meta_value ) && 0 < strlen( trim( $meta_value ) ) ) {
        add_post_meta( $post_id, 
                       sanitize_text_field( '_original_product_price' ), 
                       $meta_value 
                       );
    }
*/
//if serialized    
if( array_key_exists( 'original_product_price', $_POST ) ) {
        update_post_meta( $post_id, '_original_product_price',
                              $_POST['original_product_price']
                        );
    }
}
add_action('save_post', 'meprprorata_save_original_price_save_postdata'); 

/**
 * Snippet to get remaining months
 */
function meprprorata_get_remaining_months($product_id)
{  
    $product    = new MeprProduct();
    $product_id = absint( $product->ID ); 
    if( !is_admin() ) return;

    $args       = array();
    $txn_xpr    = get_post_meta( $product_id, '_mepr_expire_fixed', true );
    $today      = date( 'Y-m-d' );
    $start_date = strtotime( $today );                  //epoch time
    $end_date   = strtotime( $txn_xpr );           
    $due_date   = ( $end_date - $start_date )/60/60/24; //exp date - now =days
   
    //get product
    $prod_price = get_post_meta( $product_id, '_mepr_product_price', true );
   
    $prorata          = $prod_price / 12;
    $remaining_months = ($due_date / 30);

    $args = array( 'remaining_months' => esc_attr( number_format_i18n( $remaining_months ) ),
                   'product_id' => $product_id
    );
    return $args;
}
