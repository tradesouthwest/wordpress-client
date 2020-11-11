<?php 
/**
 * @package meprprorata
 * @subpackage meprprorata/inc
 */
defined( 'ABSPATH' ) or exit;
/**
 * added to validate parent sub
 * L Judd @codeable
 * @param $has_parent string | BOOL Checks is ancestor to avoid day counting
 * @param $rnstr1     string        Renewal Url of page to renew.
 * @param $rnstr0     string        Reserved for messages.
 * @param $is_xpired  string | int  Expiry days + grace days 
 * @param $xpired     string | BOOL Converts days (late)
 * 
 */

//$MpprProd = new MeprProduct();
function meprprorata_get_current_subscription_links( $prdID )
{
    //$notice_prior = get_option('meprprorata_options' )['meprprorata_display_grace_field'];
    $grace     = get_option('meprprorata_options' )['meprprorata_expire_grace_field'];
    $gracedot  = empty( $grace ) ? '31' : $grace;
    $txn_xpr   = get_post_meta( $prdID, '_mepr_expire_fixed', true ); //formatted time
    $today     = date( 'Y-m-d' );
    $start_date   = strtotime( $today );                //epoch time
    $end_date     = strtotime( $txn_xpr );
    $due_date     = ($end_date - $start_date)/60/60/24; //exp days | int -2
    $exps_at_text = ($due_date + ( $notice_prior * 1 ));
    $old_url      =  MeprUtils::get_permalink($prdID); 
    $new_url      =  preg_replace('{/$}', '', $old_url);
    //$prd_url      = meprprorata_add_request_redirect( $prdID );
    ob_start();
    if( $exps_at_text < absint( 1 ) ) 
    { $prd_url = $new_url . '-renewal-expired'; 
        } 
        else
        { $prd_url = $prd->url(); 
    } 
        $redirect = esc_url( $prd_url );

        echo $redirect; 
        $parse_url = ob_get_clean();
        return $parse_url;
}

/**
 * Build redirect for expired txns //DEPRECATED
 * 
 * $txn        = new MeprTransaction();
 * $subs       = new MeprSubscription(); //_mepr_subscriptions
 * $prds       = new MeprProduct();      // product_id=3759
 * 
 * Subscriptions account template must be included to call $productName
 */
function meprprorata_add_request_redirect( $prdID )
{

    $old_url =  $permalink = MeprUtils::get_permalink($prdID);
    $new_url = 'expired';
    $redirect = esc_url( $old_url . '-' . $new_url );
        
        return $redirect;
}
/**
 * After Sept 31, 20$ fee is added to Renewal
 * hook: MeprTransaction Model 
 *
 * @param $txn_xpr      string Checks is fixed sub. 'mepr-subscriptions'
 * @param $rnstr        string Renewal string Url of page to renew.
 * @param $due_date     int    Numbr of days until due @uses [exps_at]
 * @param $is_xpired    BOOL   The due date w/out grace is past
 * @param $is_xpiring   int    The numbr grace days be4 xpired
 * @return array
 * 
 * @since 1.0.2
 * 
 * $current_post->post_type == 'memberpressproduct' 
 * 
 * @uses string $args Args from calculate_expiry_date function 
 */
function meprprorata_get_current_day_number( $prdID )
{   
    global $post;
    $info = array();

    $user_sub = $txn_xpr = $user_id = $due_date = $old_url = '';
    //subscriptions account template must be included to call $productID
    $prdID      = (empty( $prdID ) ) ? '' : $prdID;
    $txn_xpr    = get_post_meta( $prdID, '_mepr_expire_fixed', true );
    $today      = date( 'Y-m-d' );
    $start_date = strtotime( $today ); 
    $end_date   = strtotime( $txn_xpr );
    $due_date   = ($end_date - $start_date)/60/60/24; //exp date- now | int
    //array to return
    $info = array(
        'prd_id'   => $prdID,
        'txn_xpr'  => $txn_xpr, 
        'exps_at'  => $due_date,      
    );
    
    $ntcds    = get_option('meprprorata_options' )['meprprorata_display_grace_field']; 
    $expTitle = get_option('meprprorata_options' )['meprprorata_cstitle_field']; 
    $expDescr = get_option('meprprorata_options' )['meprprorata_csdescription_field'];
    $grace    = get_option('meprprorata_options' )['meprprorata_expire_grace_field']; 
    $ntcds    = empty( $ntcds ) ? '29' : $ntcds;
    $gracedot = empty( $grace ) ? '29' : $grace;  //funny this was written on Thansgiving
    $expDscri = empty( $expDescr ) ? 'Grace Period Days' : $expDescr;
    $expText  = empty( $expTitle ) ? 'Days until Expiry' : $expTitle;
    //$old_url = meprprorata_get_product_premalink( $prdID );
        //if-no. of due days is less then $gracedot forward, show message
        if( $info['exps_at'] <= $ntcds )
            { $disp       = 'block'; } 
             else { $disp = 'none'; }
            $exps_at_text = $info['exps_at'] + ( $gracedot - 1 );
        if( $exps_at_text < 1 ) $exps_at_text = __( 'Overdue Fee Applies' );
        if( $exps_at_text < 1 ) { $spans = 'none'; } else { $spans = 'block'; }
       
        /**
         * 2nd line (p) shows only before due date.
         * 3rd line is for debug only.  
         */    
            printf( '
            <p class="mppr-text mppr-days"> %s: <strong>%s</strong></p>
            <p class="mppr-text mppr-fee" style="display:%s">%s: %s <span style="display: %s">days</span></p>
            <p class="mppr-text mppr-subs" style="display:none">pid: %s | cal: %s | days: %s</p>', 
                    esc_html( $expText ),
                    intval(   $info['exps_at'] ),
                    esc_attr( $disp ),
                    esc_html( $expDscri ),
                    $exps_at_text,
                    $spans,
                    $info['prd_id'],
                    $info['txn_xpr'],
                    $info['exps_at']
                  ); 
}

/**
 * Gets memeber's joined-on date
 * 
 * @uses shortcode 
 * @return echo notice text
 */ 
add_action( 'mepr_before_account_subscriptions', 'meprprorata_get_membership_since', 9, 1 );
function meprprorata_get_membership_since()
{

    $since = '';
    ob_start();
    //$info     = meprprorata_reference_account_info( $info );
    $since = do_shortcode( '[mepr-account-info field="user_registered"]' );
    printf( '<p class="mppr-text mppr-since">Member Since: %s</p>',
    esc_html( $since )
    );
    $out = ob_get_clean();
    echo $out;
}
/*
 * get post by slug
 * Helps us to find the custom post type product id.
 */
function meprprorata_get_post_by_slug( $slug )
{
    $product = '';
    $slug = '';

    $post_slug = get_page_by_title( sanitize_title_with_dashes( $slug ), OBJECT, 'memberpressproduct' );
    $product = new MeprProduct($post_slug->ID);
    $clean_id = get_post_meta ( $product->ID, '_product_id', true );
    return $clean_id;
}