<?php //upgrade options
/**
 * @package meprprorata
 * @subpackage meprprorata/inc
 */
/**
 * below could be used for expiry date manual set to override Mepr 
 * 
 */
defined( 'ABSPATH' ) or exit;
function meprprorata_calculate_past_expiry_date( $args )
{
    global $post;
    $args       = array();
    $today = $start_date = $end_date = $over_date = $due_date = $today_date ='';
    //$mepr_expire_fixed = get_post_meta
    $xpr_date   = get_option('meprprorata_options' )['meprprorata_expire_year_field']; 
    $ntxn       = empty( $xpr_date ) ? '2019-09-01' : $xpr_date;
    $grace      = get_option('meprprorata_options' )['meprprorata_expire_grace_field']; 
    $gracedot   = empty( $grace ) ? '29' : $grace;  //funny this was written on Thansgiving
    // Declare two dates 
    $today      = date( 'Y-m-d' );
    $start_date = strtotime( $today ); 
    $end_date   = strtotime( $ntxn );
    //calc expiry options
    $due_date   = ($end_date - $start_date)/60/60/24; //exp date- now | int
    //expire date + grace 
    $over_date  = (($end_date - $start_date)/60/60/24) + $gracedot; 
    $today_day  = $start_date/60/60/24;              //now as integer
    $args       = array( 
                    'past_duedays' => $over_date,    //30 days over due
                    'dueday'       => $due_date,     //num days until exp
                    'start_date'   => $start_date,  
                    'due_days'     => $due_date,     //num days until due
                    'today_day'    => $today_day,
                    'grace_dot'    => $gracedot,
                 );
        //arguments to use in plugin actions
        return $args; 
}

/*
 * get post by slug
 * Helps us to find the custom post type product id.
 */
function meprprorata_get_post_by_slug( $slug )
{
    global $wpdb;
    $type = 'memberpressproduct';
	$post = $wpdb->get_var ( $wpdb->prepare ( "SELECT ID 
    FROM $wpdb->posts WHERE post_name = %s AND post_type='$type' 
    LIMIT 1", 
    $slug ) );
	if ($post)
		return get_post ( $post->ID );

/*
    $post = get_page_by_title( $slug, '', 'post' );
    if ( $post ) {
        $id = $post->ID;
    }
    else {
        $id = 0;
    }
  */      //return null;
}
/**
 * Events to query for
 * static $users_str = 'users';
 * static $transactions_str = 'transactions';
 * static $subscriptions_str = 'subscriptions';
 * //mepr-account-subscr-id mepr-account-product 3759
 */
function meprprorata_reference_account_info( )
{
    global $post;
    $prdID = $_GET[$prd->ID];
    //return null if empty
    $user_sub = $txn_xpr = $user_id = $exps_on = $prd_id = '';

    //$usr        = new MeprUser( $user_id );
    //$txn        = new MeprTransaction();
    //$subs       = new MeprSubscription(); //_mepr_subscriptions
    //$prds       = new MeprProduct();      // product_id=3759
    //subscriptions account template must be included to call $productName
    $slug       = sanitize_title_with_dashes( $productName ); 
    //meprprorata_get_post_by_slug( $slug ); 
    $prdID = (empty( $prdID ) ) ? '' : $prdID;
    $txn_xpr    = get_post_meta( $prdID, '_mepr_expire_fixed', true );
    /*
    $user_sub   = do_shortcode( '[mepr-account-info field="ID"]' );
    $user_id    = absint( $user_sub );
   */
    
    //$subscr_num = $subs->subscr_id;             //obj mp-sub-5bf8dfb3e2d65
    //$subscription_id = $txn->expiring_txn;
    //$exps_on    = $subs->expirecng_txn; 
    $today      = date( 'Y-m-d' );
    $start_date = strtotime( $today ); 
    $end_date   = strtotime( $txn_xpr );
    $due_date   = ($end_date - $start_date)/60/60/24; //exp date- now | int
    //array to return
    $info = array(
        'prd_id' => $prdID,
        'txn_xpr'  => $txn_xpr, 
        'exps_at'  => $due_date,      
    );
        return null;
}