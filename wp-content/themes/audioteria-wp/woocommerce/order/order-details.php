<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.6.0
 */

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
    return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();

?>
    <section class="woocommerce-order-details">
        <?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>
        <div class="heading">
            <h4 class="woocommerce-order-details__title"><?php esc_html_e( 'Order Summary', 'audioteria-wp' ); ?></h4>
        </div>
        <div class="woocommerce-table woocommerce-table--order-details shop_table order_details">
            <div class="woocommerce-order-overview__date date">
                <span><?php esc_html_e( 'Date:', 'audioteria-wp' ); ?></span>
                <span><?= wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>
            </div>
            <div class="inner-heading">
                <span class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'audioteria-wp' ); ?></span>
                <span class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'audioteria-wp' ); ?></span>
            </div>
            <?php
            do_action( 'woocommerce_order_details_before_order_table_items', $order );

            foreach ( $order_items as $item_id => $item ) {
                $product = $item->get_product();

                wc_get_template(
                    'order/order-details-item.php',
                    array(
                        'order'              => $order,
                        'item_id'            => $item_id,
                        'item'               => $item,
                        'show_purchase_note' => $show_purchase_note,
                        'purchase_note'      => $product ? $product->get_purchase_note() : '',
                        'product'            => $product,
                    )
                );
            }

            do_action( 'woocommerce_order_details_after_order_table_items', $order );
            ?>

            <?php
            foreach ( $order->get_order_item_totals() as $key => $total ) {
                if( 'payment_method' !== $key ){ ?>
                    <div <?= ('order_total' === $key ) ? 'class="order-review-total"' : '' ; ?>>
                        <span scope="row"><?= esc_html( $total['label'] ) ; ?></span>
                        <span><?= wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                    </div>
                <?php }
            } ?>
            <?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
    </section>

<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );
