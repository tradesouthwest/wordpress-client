<?php
/**
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */
// Custom quote starts at line 93.
global $woocommerce;

// WooCommerce cart

$show_cart = trim(mfn_opts_get('shop-cart'));
if ($show_cart == 1) {
	$show_cart = 'icon-bag-fine';
}
$has_cart = ($woocommerce && $show_cart) ? true : false;

// search

$header_search = mfn_opts_get('header-search');

// action button

$action_link = mfn_opts_get('header-action-link');

// WPML icon

if (has_nav_menu('lang-menu')) {
	$wpml_icon = true;
} elseif (function_exists('icl_get_languages') && mfn_opts_get('header-wpml') != 'hide') {
	$wpml_icon = true;
} else {
	$wpml_icon = false;
}

if ($has_cart || $header_search || $action_link || $wpml_icon) {
	echo '<div class="top_bar_right">';
		echo '<div class="top_bar_right_wrapper">';

			// WooCommerce cart 

			if ($has_cart) {
				echo '<a id="header_cart" href="'. esc_url(wc_get_cart_url()) .'"><i class="'. esc_attr($show_cart) .'"></i><span>'. esc_html($woocommerce->cart->cart_contents_count) .'</span></a>';
			}

			// search icon

			if ($header_search == 'input') {

				$translate['search-placeholder'] = mfn_opts_get('translate') ? mfn_opts_get('translate-search-placeholder', 'Enter your search') : __('Enter your search', 'betheme');

				echo '<a id="search_button" class="has-input">';
					echo '<form method="get" id="searchform" action="'. esc_url(home_url('/')) .'">';

						echo '<i class="icon-search-fine"></i>';
						echo '<input type="text" class="field" name="s" placeholder="'. esc_html($translate['search-placeholder']) .'" />';

						do_action('wpml_add_language_form_field');

						echo '<input type="submit" class="submit" value="" style="display:none;" />';

					echo '</form>';
				echo '</a>';

			} elseif ($header_search) {

				echo '<a id="search_button" href="#"><i class="icon-search-fine"></i></a>';

			}

			// languages menu

			get_template_part('includes/include', 'wpml');

			// action button

			if ($action_link) {
				$action_options = mfn_opts_get('header-action-target');

				if (isset($action_options['target'])) {
					$action_target = 'target="_blank"';
				} else {
					$action_target = false;
				}

				if (isset($action_options['scroll'])) {
					$action_class = ' scroll';
				} else {
					$action_class = false;
				}
	// Start @codeable Larry
	if( function_exists( 'betheme_child_request_aquote_echo' ) ) { 
		if ( ( is_single() && 'post' == get_post_type() ) || is_home() || is_front_page() 
			|| is_page( array( 
				'products', 
				'roll-protection-products',
				'steel-core-plugs',
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
		else { 
		// !ends @codeable Larry
	echo '<a href="'. esc_url($action_link) .'" class="action_button'. esc_attr($action_class) .'" '. wp_kses_data($action_target) .'>'. wp_kses(mfn_opts_get('header-action-title'), mfn_allowed_html('button')) .'</a>';
		// starts @codeable Larry
		} 
		// !ends @codeable Larry

				// echo '<div id="QButton" style="display:' . esc_attr( $btnclss ) . '">'; //@author Larry
				// echo '<a href="'. esc_url($action_link) .'" class="action_button'. esc_attr($action_class) .'" '. wp_kses_data($action_target) .'>'. wp_kses(mfn_opts_get('header-action-title'), mfn_allowed_html('button')) .'</a>';
				// echo '</div>'; // also 
				
            }

		echo '</div>';
	echo '</div>';
} 
