=== Betheme Child Theme ===
Contributors: tradesouthwestgmailcom

The full documentation can be found in the 'documentation' folder located inside the download package.

== Change Log ==
line 42 cart.[php]
					<li class="inline-list-thumb product-thumbnail lw-md">
						<[?php
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

						if ( ! $product_permalink ) {
							echo $thumbnail; // PHPCS: XSS ok.
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
						}
						?]>
					</li>
