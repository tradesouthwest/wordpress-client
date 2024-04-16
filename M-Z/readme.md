Clients M thru Z

add_filter( 'woocommerce_cart_item_name', 'cart_product_title', 20, 3);

function cart_product_title( $title, $values, $cart_item_key ) {
    return $title . ' - ' . $values[ 'line_total' ] . '€';
}
woocommerce, get cart fragment for price from single product page

//primary function of the wc_fragment_refresh event is to update parts of the page that display dynamic information related to the WooCommerce cart.
function refresh_fragments() {
    console.log('fragments refreshed!');
    $( document.body ).trigger( 'wc_fragment_refresh' );
}
refresh_fragments();
setInterval(refresh_fragments, 60000);
//https://www.businessbloomer.com/woocommerce-get-cart-info-total-items-etc-from-cart-object/
