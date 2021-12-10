<?php 
function delval_ajaxfdoe_add(){

	ob_start();

    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint(sanitize_text_field($_POST['p_id'])));
    $quantity   = empty($_POST['quantity']) ? 0 : wc_stock_amount(sanitize_text_field($_POST['quantity']));

    $product_status       = get_post_status($product_id);
    $variation_id         = empty($_POST['variation_id']) ? 0 : absint(sanitize_text_field($_POST['variation_id']));
	$vars = empty($_POST['variation_atts']) ? array() : json_decode(stripslashes($_POST['variation_atts']),TRUE);
    $variation            = is_null($vars) ||is_bool($vars) ? array() :$vars;
    $status               = true;
    $passed_validation    = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation );
    $cart_item_data = array();
    if ( class_exists( 'WC_Product_Addons_Display' )  ) {
    /*foreach($_POST as $attr => $val){
	    if(strpos($attr,'addons') === 0 ){
		    $cart_item_data[] = $val['addons'];
	    }
    }*/
        if(!$passed_validation ){
            $addon_error = 'addon_error';
        }
    }
	$product_quantity_new = $quantity;
    if ($passed_validation && 'publish' === $product_status) {
        $status               = false;
        $product_quantity_new = $quantity;
        if ($variation_id != 0) {
            $product = wc_get_product($product_id);
            $variation_product = wc_get_product($variation_id);
			if($variation_product->managing_stock() === true){
                $product_qty_in_cart = WC()->cart->get_cart_item_quantities();
                $max_to              = $variation_product->get_max_purchase_quantity();
                if (array_key_exists($variation_product->get_stock_managed_by_id(), $product_qty_in_cart)) {
                    $in_cart_total = $product_qty_in_cart[$variation_product->get_stock_managed_by_id()];
                } else {
                    $in_cart_total = 0;
                }
                $avalible = ($max_to - $in_cart_total);
                    if($avalible == 0){
                        $product_quantity_new = 0;
                        $overstock               = true;
					}
                    elseif ($quantity > $avalible) {
                        $product_quantity_new = $avalible;
                        $overstock               = true;
                    }elseif($quantity <= $avalible){
						$overstock   = false;
						$product_quantity_new = $quantity;
					}
            }
            elseif ($variation_product -> managing_stock() == 'parent') {
                $product_qty_in_cart = WC()->cart->get_cart_item_quantities();
                $max_to              = $variation_product->get_max_purchase_quantity();
                if (array_key_exists($variation_product->get_stock_managed_by_id(), $product_qty_in_cart)) {
                    $in_cart_total = $product_qty_in_cart[$variation_product->get_stock_managed_by_id()] ;
                } else {
                    $in_cart_total = 0;
                }
                    $avalible = ($max_to - $in_cart_total);

                    if($avalible == 0){
						$product_quantity_new = 0;
                        $overstock            = true;
						}
                    elseif ($quantity > $avalible) {
                        $product_quantity_new = $avalible;
                        $overstock               = true;
                    }elseif($quantity <= $avalible){
					    $overstock   = false;
						$product_quantity_new = $quantity;
					}
            }

        }
		// For not variable products
		else{
			$product = wc_get_product($product_id);
			if ($product->managing_stock()) {
                $product_qty_in_cart = WC()->cart->get_cart_item_quantities();
                $max_to              = $product->get_max_purchase_quantity();
                if (array_key_exists($product->get_stock_managed_by_id(), $product_qty_in_cart)) {
                    $in_cart_total = $product_qty_in_cart[$product->get_stock_managed_by_id()];
                } else {
                    $in_cart_total = 0;
                }
                
                $avalible = ($max_to - $in_cart_total);
				if($avalible == 0){
					$product_quantity_new = 0;
                    $overstock               = true;
				}
                elseif ($quantity > $avalible) {
                    $product_quantity_new = $avalible;
                    $overstock               = true;
                }elseif($quantity <= $avalible){
					$overstock   = false;
					$product_quantity_new = $quantity;
				}
            }else{
                $product_quantity_new = $quantity;
            }
		}

		//Check if sold individually
        if ( $product->is_sold_individually() ) {
            $cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );
            $cart_item_key =  WC()->cart->find_product_in_cart( $cart_id );
    	    $cart_item_data = (array) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id, $product_quantity_new  );
            $product_quantity_new      = apply_filters( 'woocommerce_add_to_cart_sold_individually_quantity', 1, $product_quantity_new, $product_id, $variation_id, $cart_item_data );
            $found_in_cart = apply_filters( 'woocommerce_add_to_cart_sold_individually_found_in_cart', $cart_item_key && WC()->cart->cart_contents[ $cart_item_key ]['quantity'] > 0, $product_id, $variation_id, $cart_item_data, $cart_id );

            foreach( WC()->cart->get_cart() as $cart_item ) {
				$product_in_cart = $cart_item['product_id'];
					if ( $product_in_cart === $product_id ){ $in_cart = true;}else{$in_cart = false;}
	        }
			if ( $found_in_cart || $in_cart) {
				$is_sold_indi = true;
				$product_quantity_new = 0;
            }
        }
        if ($product_quantity_new > 0) {
            $hash = WC()->cart->add_to_cart($product_id, $product_quantity_new, $variation_id, $variation);
            if (false != $hash) {
                do_action('woocommerce_ajax_added_to_cart', $product_id);
                do_action('woocommerce_update_cart_action_cart_updated');

                $data = array(
                    'success' => true,
					'overstock' => isset( $overstock) ? $overstock:false,
					'is_sold_indi'=> isset($is_sold_indi ) ? $is_sold_indi :false,
                    'status' => $status,
                    'product_quantity' => $product_quantity_new,
					'passed_vali' => true
                );
            }else{
				$data = array(
                    'success' => false,
					'overstock' => isset( $overstock) ? $overstock:false,
					'is_sold_indi'=> $product->is_sold_individually() ,
                    'status' => $status,
                    'product_quantity' => $product_quantity_new,
					'passed_vali' => false
                );
			}
        } else {

                    $data = array(
                        'error' => true,
                        'status' => $status,
                        'product_quantity' => $product_quantity_new,
						'overstock' => isset( $overstock) ? $overstock:false,
						'is_sold_indi' => isset($is_sold_indi ) ? $is_sold_indi :false,
					'passed_vali' => false
                    );
                }
            } else {
                $data = array(
                    'error' => true,
                    'status' => isset($addon_error) ? $addon_error : $status,
                    'product_quantity' => $product_quantity_new,
					'passed_vali' => false,
					'overstock' => isset( $overstock) ? $overstock : false,
					'is_sold_indi' => isset($is_sold_indi ) ? $is_sold_indi :false,
                );
            }
			$alert = ob_get_clean();
			$data1 = array_merge($data, array('alert' => $alert));
            wp_send_json($data1);
}
