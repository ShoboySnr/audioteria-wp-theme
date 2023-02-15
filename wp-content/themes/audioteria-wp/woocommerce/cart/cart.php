<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;
$exclude_ids = array();
$shop_url = esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) );
?>
<div class="wrapper">
	<?php do_action( 'woocommerce_before_cart' ); ?>

	<section class="shopping">
		<form class="woocommerce-cart-form shopping-bag" action="<?php echo $shop_url; ?>" method="post">
			<?php do_action( 'woocommerce_before_cart_table' ); ?>
				<div class="header">
					<?php
						$cart_items = WC()->cart->get_cart();
						$no_of_items = count($cart_items);
					?>
					<p class="title"><?= __('My Shopping Bag (', 'audioteria-wp') ?><?= $no_of_items ?><?= __(' Items)', 'audioteria-wp') ?></p>

					<a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>" title="Continue shopping"><?= __('Continue shopping', 'audioteria-wp') ?></a>
				</div>
				<?php do_action( 'woocommerce_before_cart_contents' ); ?>
				<hr>
				<div class="bag-items">
					<?php
					foreach ( $cart_items as $cart_item_key => $cart_item ) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							$exclude_ids[] = $cart_item['product_id'];

							$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							?>

							<article class="bag-item">
								<?php
									$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image_id(), $cart_item, $cart_item_key );
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
								?>
								<article class="content">
									<div>
										<a href="<?= $product_permalink ?>">
                                            <h3 data-title="<?php esc_attr_e( 'Product', 'audioteria-wp' ); ?>">
											<?php
												if ( ! $product_permalink ) {
													echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
												} else {
													echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '%s', $_product->get_name() ), $cart_item, $cart_item_key ) );
												}
											?>
                                            </h3>
                                        </a>
										<span class="price">
											<?php
												echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
											?>
										</span>
									</div>
									<p class="description">
										<?= apply_filters( 'audioteria_cart_item_description', $_product, $cart_item, $cart_item_key) ?>
                                    </p>
									<?= apply_filters( 'audioteria_cart_item_writer', $_product, $cart_item, 'writer');?>
									<div class="product-remove">
										<span class="price">
											<?php
                                            echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                                            ?>
										</span>
                                        <?= sprintf('<a href="%s" data-product_id="%s" class="button wc-forward remove">%s %s</a>',
                                            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                            esc_attr( $product_id ),
                                            __( '<svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.8541 0.646039C12.9007 0.692485 12.9376 0.747661 12.9629 0.808406C12.9881 0.869151 13.001 0.934272 13.001 1.00004C13.001 1.06581 12.9881 1.13093 12.9629 1.19167C12.9376 1.25242 12.9007 1.30759 12.8541 1.35404L1.85414 12.354C1.76026 12.4479 1.63292 12.5007 1.50014 12.5007C1.36737 12.5007 1.24003 12.4479 1.14614 12.354C1.05226 12.2602 0.999512 12.1328 0.999512 12C0.999512 11.8673 1.05226 11.7399 1.14614 11.646L12.1461 0.646039C12.1926 0.599476 12.2478 0.562533 12.3085 0.537327C12.3693 0.51212 12.4344 0.499146 12.5001 0.499146C12.5659 0.499146 12.631 0.51212 12.6918 0.537327C12.7525 0.562533 12.8077 0.599476 12.8541 0.646039Z" fill="#CB5715" stroke="#CB5715"/><path fill-rule="evenodd" clip-rule="evenodd" d="M1.14592 0.646039C1.09935 0.692485 1.06241 0.747661 1.0372 0.808406C1.012 0.869151 0.999023 0.934272 0.999023 1.00004C0.999023 1.06581 1.012 1.13093 1.0372 1.19167C1.06241 1.25242 1.09935 1.30759 1.14592 1.35404L12.1459 12.354C12.2398 12.4479 12.3671 12.5007 12.4999 12.5007C12.6327 12.5007 12.76 12.4479 12.8539 12.354C12.9478 12.2602 13.0005 12.1328 13.0005 12C13.0005 11.8673 12.9478 11.7399 12.8539 11.646L1.85392 0.646039C1.80747 0.599476 1.7523 0.562533 1.69155 0.537327C1.63081 0.51212 1.56568 0.499146 1.49992 0.499146C1.43415 0.499146 1.36903 0.51212 1.30828 0.537327C1.24754 0.562533 1.19236 0.599476 1.14592 0.646039Z" fill="#CB5715" stroke="#CB5715"/></svg>', 'audioteria-wp' ),
                                            esc_html__( ' Remove', 'audioteria-wp' )
                                        );
                                        ?>
									</div>
								</article>
							</article>
                        <?php }
					} ?>

					<?php do_action( 'woocommerce_cart_contents' ); ?>
				</div>
		</form>

		<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

		<section class="checkout">
			<?php
				/**
				 * Cart collaterals hook.
				 *
				 * @hooked woocommerce_cross_sell_display
				 * @hooked woocommerce_cart_totals - 10
				 */
				do_action( 'woocommerce_cart_collaterals' );

			?>

			<button type="button" class="button shopping-button" onclick="window.location.href='<?= $shop_url; ?>'"><?php esc_html_e( 'Continue Shopping', 'audioteria-wp' ); ?></button>

			<?php do_action( 'woocommerce_cart_actions' ); ?>

			<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>

			<?php do_action( 'woocommerce_after_cart_table' ); ?>
		</section>
	</section>

	<?php

	$relateproduct_id = $exclude_ids[0];
	$related_products = wc_get_related_products( $relateproduct_id, 4, $exclude_ids);

	if ( $related_products ) : ?>

		<section class="book-card-section">
			<h4><?= __('You might also be interested in these titles', 'audioteria-wp'); ?></h4>
			<div class="book-card-wrapper">

				<?php foreach ( $related_products as $related_product ) :

						$post_object = get_post( $related_product );

						setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

						wc_get_template_part( 'content', 'product' );

				endforeach;

				do_action( 'woocommerce_after_cart' ); ?>
			</div>
		</section>
	<?php endif; ?>
</div>

