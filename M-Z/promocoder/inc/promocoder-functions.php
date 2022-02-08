<?php
/**
 * @package Promocoder
 * @subpackage promocoder/inc/promocoder-functions
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Redirect user after successful login.
 *
 * @param string $redirect_to URL to redirect to.
 * @param string $request URL the user is coming from.
 * @param object $user Logged user's data.
 * @return string
 */

function promocoder_admin_login_redirect( $redirect_to, $request, $user ) 
{

    $rediradmin = ( empty( get_option('promocoder_settings')['promocoder_checkbox_rediradmin'] ) )
    ? '0' : get_option('promocoder_settings')['promocoder_checkbox_rediradmin']; 
    //is there a user to check?
    if (isset($user->roles) && is_array($user->roles)) {
        //check for subscribers
        if ( $rediradmin == '1' && in_array('administrator', $user->roles) ) {
            // redirect them to another URL, in this case, the homepage 
            $redirect_to = promocoder_get_shortcoded_page_rediradmin();
        }
    }

    return $redirect_to;
}
add_filter( 'login_redirect', 'promocoder_admin_login_redirect', 10, 3 );

/**
 * Find page link for form success message
 * @param string $shrtpage
 * @return $page link
 */
function promocoder_get_shortcoded_page()
{

    global $post;
    $shrtpage = ( empty( get_option('promocoder_settings')['promocoder_text_field_0'] ) )
                    ? '' : get_option('promocoder_settings')['promocoder_text_field_0']; 
    $page =  get_the_title( $shrtpage );
    
        return site_url('/') . sanitize_title_with_dashes($page);
}

/**
 * Find page link for form page
 * @param string $formpage
 * @return $page link
 */
function promocoder_get_shortcoded_page_rediradmin()
{

    global $post;
    $formpage = ( empty( get_option('promocoder_settings')['promocoder_rediradmin_dropdown'] ) )
                    ? '' : get_option('promocoder_settings')['promocoder_rediradmin_dropdown']; 
    $page =  get_the_title( $formpage );
    
        return site_url('/') . sanitize_title_with_dashes($page);
}

/**
 * @uses promocoder_settings option
 * Retrieves the value associated with option.
 */
function promocoder_activate_front_form()
{
    $promocoder_use_formpage = get_option( 'promocoder_settings' )['promocoder_use_formpage'];
    if( empty( $promocoder_use_formpage ) ) { $promocoder_use_formpage = absint(0); } 
    else { $promocoder_use_formpage = $promocoder_use_formpage; }

    if( $promocoder_use_formpage == absint(1) ) : 
        return true;
    endif;
}

/**
 * Privatized add query var to url 
 * @since 1.0.1
 * 
 */

function promocoder_privitized_query_vars_filter($vars) 
{
    $vars[] .= 'accessed';
    
        return $vars;
}
//add_filter( 'query_vars', 'promocoder_privitized_query_vars_filter' );

/**
 * Privatized Page 
 * @since 1.0.1
 * 
 */
function promocoder_privatized_page_finder()
{
    $options = get_option('promocoder_settings'); 
    $chkpage = (empty($options['promocoder_privatize_dropdown'] )) 
               ? '' : $options['promocoder_privatize_dropdown'];
    
        return $chkpage;
}

/**
 * Privatized Page 
 * @since 1.0.1
 * @uses $key = get_query_var( 'key1' ); on promopage
 */
function promocoder_privatized_page_queried($ref='')
{
    $findpage  = promocoder_privatized_page_finder();
    $promocode = 0;
    if( $promocode > 0 ) {  
        $rev_query = add_query_arg( array(
                                'accessed' => $promocode,
                                'time'     => time(),
                                ), $findpage 
                            );
    } else {
        $rev_query = esc_url( $ref );
    }
        return esc_url( $rev_query );
}

/**
 * @uses wp_get_term_list
 * Retrieves the terms associated with the term_id
 */
function promocoder_get_category_display()
{ 
 
    if ( 'promocoder' === get_post_type() ) {
        echo get_the_term_list( $post->ID, 'promocoder_categories', '', ', ' );
    }
}


/**
 * Display list of categories on front side form.
 * 
 * @param $args
 * @param 'promocoder_categories', array('promocoder')
 * $taxonomy, $object_type, $args
 * @uses wp_dropdown_categories
 */
function  promocoder_category_displayin_frontside( $taxonomy ) 
{    

    $taxonomy    = (empty ( $taxonomy ) ) ? 'promocoder_categories' : $taxonomy;
    $terms       = wp_get_post_terms( get_the_ID(), $taxonomy );
    $selected_id = '';
    if(isset($terms[0]->term_id)){
        $selected_id = $terms[0]->term_id;
    }
    $promocoder_cats = wp_dropdown_categories( array(
        'show_option_all'    => 'Choose a Category',
        'show_option_none'   => '',
        'orderby'            => 'ID', 
        'order'              => 'ASC',
        'show_count'         => 0,
        'hide_empty'         => 0, 
        'child_of'           => 0,
        'exclude'            => '',
        'echo'               => 1,
        'selected'           => $selected_id,
        'hierarchical'       => 1, 
        'name'               => 'promocoder_categories',    
        'id'                 => 'promocoder_categoriesDropdown',
        'class'              => 'form-no-clear',
        'depth'              => 0,
        'tab_index'          => 4,
        'taxonomy'           => $taxonomy,
        'hide_if_empty'      => true
    ) );
return $promocoder_cats;
}

/**
 * get tags list
 */
function promocoder_get_terms_tag_list()
{

global $post;
$terms = wp_get_post_terms($post->ID, 'promocoder_tags');
    if ( $terms ) { 
    
      $output = array();
      foreach ($terms as $term) {

$output[] = '<a href="' .get_term_link( $term->slug, 'promocoder_tags') .'">' .$term->name .'</a>'; 

      } 
    
    echo join( ", ", $output);
    }
}

/**
 * @param $promocoder_layout 
 * @uses get_option _checkbox_1
 * Determines if public layout is tall or short.
 */
function promocoder_get_layout_option()
{  
   $promocoder = get_option('promocoder_settings'); 
   $promocoder_layout = $promocoder['promocoder_checkbox_1']; 
   
   switch ($promocoder_layout) {
    case 0:
    $promocoder_layout = 'promocoder_tall';
    break;
    case 1: 
    $promocoder_layout = 'promocoder_short';
    break;
    default: 
    $promocoder_layout = 'promocoder_tall';
   }
   return $promocoder_layout;
}

/**
 * @param $promocoder_settings 
 * @uses get_option 
 * Adds styles to footer.
 */
function promocoder_inline_public_styles()
{ 
    $options = get_option('promocoder_settings'); 
    $promocoder_favicon = 'hidden'; 
    $promocoder_color_3 = (empty($options['promocoder_color_field_3'] ) )?  
    $promocoder_color_3 = '#002e63' : 
    $promocoder_color_3 = $options['promocoder_color_field_3'];
    $promocoder_color_4 = (empty($options['promocoder_color_field_4'] ) )?  
    $promocoder_color_4 = '#3388bb' : 
    $promocoder_color_4 = $options['promocoder_color_field_4'];
    $promocoder_color_5 = (empty($options['promocoder_color_field_5'] ) )?  
    $promocoder_color_5 = '#fafafa' : 
    $promocoder_color_5 = $options['promocoder_color_field_5'];
    $promocoder_color_6 = (empty($options['promocoder_color_field_6'] ) )?  
    $promocoder_color_6 = '#fafafa' : 
    $promocoder_color_6 = $options['promocoder_color_field_6'];
    $promocoder_qwerty = (empty($options['promocoder_icons_alphaqwerty'] ) )?  
    $promocoder_qwerty = '6' : 
    $promocoder_qwerty = $options['promocoder_icons_alphaqwerty'];

    $htm = ''; 
    $htm .= '.promocoder-fanning-panel,.promocoder-wrapper,.hrds-entry,.promocoder-single{background:' . $promocoder_color_5 . ';}.promocoder-sidebar{background:' . $promocoder_color_6 . ';}.hrds-favicos{visibility: ' . $promocoder_favicon . ';}.hrds-list-inline li a.hrds-link,li.hrds-inline a.hrds-link,p.hrds-inline a.hrds-link,.hrds-excerpts a, .promocoder-archive-title a:last-child{color:' . $promocoder_color_4 . '!important;}.hrds-excerpts span.title a, .hrds-excerpts span.title,.promocoder-archive-title a:first-child{color:' . $promocoder_color_3 .'!important;}ul.promocoder-pagination.pagination-alpha li i,.promocoder-fanning-panel ul li i{padding:4px;position: relative !important;top:' . $promocoder_qwerty . 'px;}';
    wp_register_style( 'promocoder-entry-set', false );
    wp_enqueue_style(   'promocoder-entry-set' );
    wp_add_inline_style( 'promocoder-entry-set', $htm );
}
add_action( 'wp_enqueue_scripts', 'promocoder_inline_public_styles' ); 
