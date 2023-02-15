<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit; ?>


		<p>
			<?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</p>

		<?php
			$customer_id = get_current_user_id();

			$get_addresses = apply_filters(
				'woocommerce_my_account_get_addresses',
				array(
					'billing' => __( 'Billing address', 'audioteria-wp' ),
				),
				$customer_id
			); 

			foreach ( $get_addresses as $name => $address_title ) : 
				$address = wc_get_account_formatted_address( $name );
			?>

				<div class="woocommerce-Address">
					<header class="woocommerce-Address-title title">
						<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit address-button"><?php echo $address ? esc_html__( 'Edit Details', 'audioteria-wp' ) : esc_html__( 'Add', 'audioteria-wp' ); ?></a>
					</header>
					<address>
						<?php
							echo $address ? wp_kses_post( $address ) : esc_html_e( 'You have not set up this type of address yet.', 'audioteria-wp' );
						?>
					</address>
				</div>

			<?php endforeach; ?>


