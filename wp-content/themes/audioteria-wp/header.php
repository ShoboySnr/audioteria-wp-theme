<?php


/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Audioteria
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class( apply_filters( 'audioteria_modify_body_class', '' ) ); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
    <header class="home-header">
        <nav class="">
			<?php
			wp_nav_menu(
				[
					'theme_location' => 'top-menu',
					'menu_id'        => 'top-menu',
					'container'      => false,
					'items_wrap'     => '<ul class="nav-top">%3$s</ul>',
					'add_li_class'   => 'nav-top-link',
					'fallback_cb'    => false,
				]
			);
			?>
            <div class="nav-main-wrapper">
                <div class="nav-main">
                    <a href="<?= home_url( "/" ); ?>" rel="home" title="<?= get_bloginfo( 'name', 'display' ) ?>">
						<?php
						$custom_logo_id     = get_theme_mod( 'custom_logo' );
						$custom_mobile_logo = get_theme_mod( 'custom_mobile_logo' );

						$image = wp_get_attachment_image_src( $custom_logo_id, 'full' );

						$image_mobile = wp_get_attachment_image_src( $custom_mobile_logo, 'full' );
						?>
                        <img class="nav-main-logo" src="<?= isset( $image[0] ) ? $image[0] : AUDIOTERIA_CUSTOM_LOGO; ?>"
                             alt="<?= get_bloginfo( 'name', 'display' ) ?>"
                             title="<?= get_bloginfo( 'name', 'display' ) ?>">
                        <img class="nav-main-logo-mobile"
                             src="<?= isset( $image_mobile[0] ) ? $image_mobile[0] : AUDIOTERIA_CUSTOM_MOBILE_LOGO; ?>"
                             alt="<?= get_bloginfo( 'name', 'display' ) ?>"
                             title="<?= get_bloginfo( 'name', 'display' ) ?>">
                    </a>


                    <div class="nav-main-links">
                        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>"
                              id="searchform"
                              class="searchform">
                            <div class="nav-search">
                                <input type="text" placeholder="<?= __( 'Titles, Genre, Actors', 'audioteria-wp' ) ?>"
                                       value="<?= ! empty( $_GET['s'] ) ? $_GET['s'] : '' ?>" name="s" id="search-form"
                                       type="text" autocomplete="off">
								<?= file_get_contents( AUDIOTERIA_ASSETS_ICONS_DIR . '/search-icon-white.svg' ); ?>
                                <input type="hidden" name="post_type" value="product"/>
                            </div>
                        </form>

                        <a class="profile" href="<?= get_permalink( get_page_by_path( 'my-account' ) ) ?>">
                            <svg class="nav-icons" width="22" height="24" viewBox="0 0 22 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M11 11.8775C13.9916 11.8775 16.4167 9.4524 16.4167 6.46086C16.4167 3.46931 13.9916 1.04419 11 1.04419C8.0085 1.04419 5.58337 3.46931 5.58337 6.46086C5.58337 9.4524 8.0085 11.8775 11 11.8775Z"
                                        stroke="white" stroke-width="2"/>
                                <path
                                        d="M16.4167 14.0442H16.798C17.59 14.0444 18.3547 14.3338 18.9484 14.8581C19.542 15.3823 19.9238 16.1053 20.022 16.8912L20.4456 20.2755C20.4837 20.5804 20.4565 20.8899 20.3659 21.1835C20.2752 21.477 20.1232 21.748 19.9198 21.9783C19.7164 22.2086 19.4664 22.393 19.1863 22.5193C18.9062 22.6456 18.6024 22.7109 18.2952 22.7109H3.70486C3.39761 22.7109 3.09385 22.6456 2.81376 22.5193C2.53366 22.393 2.28363 22.2086 2.08027 21.9783C1.8769 21.748 1.72484 21.477 1.63418 21.1835C1.54352 20.8899 1.51634 20.5804 1.55444 20.2755L1.97694 16.8912C2.0752 16.1049 2.45731 15.3817 3.05143 14.8574C3.64554 14.3331 4.41073 14.0439 5.20311 14.0442H5.58336"
                                        stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <span class="dropdown-back" id="mini-cart-dropdown">
                            <svg class="nav-icons" width="21" height="24" viewBox="0 0 21 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg"><path
                                        d="M2.1427 10.1276C2.1846 9.60553 2.42161 9.11838 2.80652 8.76319C3.19144 8.408 3.69602 8.21083 4.21978 8.21094H16.7802C17.304 8.21083 17.8085 8.408 18.1935 8.76319C18.5784 9.11838 18.8154 9.60553 18.8573 10.1276L19.6937 20.5443C19.7167 20.8309 19.6802 21.1192 19.5863 21.3911C19.4924 21.6629 19.3432 21.9123 19.1482 22.1237C18.9532 22.3351 18.7165 22.5037 18.4531 22.6192C18.1897 22.7346 17.9053 22.7942 17.6177 22.7943H3.38228C3.0947 22.7942 2.81025 22.7346 2.54684 22.6192C2.28343 22.5037 2.04677 22.3351 1.85175 22.1237C1.65674 21.9123 1.50759 21.6629 1.41371 21.3911C1.31982 21.1192 1.28323 20.8309 1.30624 20.5443L2.1427 10.1276V10.1276Z"
                                        stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path
                                        d="M14.6667 11.3359V6.1276C14.6667 5.02254 14.2277 3.96273 13.4463 3.18133C12.6649 2.39992 11.6051 1.96094 10.5 1.96094C9.39497 1.96094 8.33516 2.39992 7.55376 3.18133C6.77236 3.96273 6.33337 5.02254 6.33337 6.1276V11.3359"
                                        stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <div class="basket-item-count">
                                <span class="cart-items-count count">
                                <?php echo WC()->cart->get_cart_contents_count(); ?>
                                </span>
                            </div>
                        </span>
                        <div class="audioteria-mini-cart" id="audioteria-mini-cart-dropdown">
                            <button id="close-mini-cart">&times;</button>
							<?php woocommerce_mini_cart(); ?>
                        </div>
                        <a class="favourites"
                           href="<?= wc_get_endpoint_url( 'customer-wishlist', '', wc_get_page_permalink( 'myaccount' ) ); ?>">
                            <svg class="nav-icons" width="26" height="27" viewBox="0 0 26 27" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M18.6026 4.12756C16.6303 4.12756 14.7373 5.04571 13.5018 6.49661C12.2662 5.04571 10.3733 4.12756 8.40096 4.12756C4.90973 4.12756 2.16663 6.87067 2.16663 10.3619C2.16663 14.6466 6.02058 18.1378 11.8582 23.4427L13.5018 24.9276L15.1454 23.4313C20.983 18.1378 24.8369 14.6466 24.8369 10.3619C24.8369 6.87067 22.0938 4.12756 18.6026 4.12756ZM13.6151 21.7537L13.5018 21.8671L13.3884 21.7537C7.99289 16.8683 4.43366 13.6378 4.43366 10.3619C4.43366 8.09487 6.13393 6.39459 8.40096 6.39459C10.1466 6.39459 11.8468 7.51677 12.4476 9.06969H14.5673C15.1567 7.51677 16.857 6.39459 18.6026 6.39459C20.8696 6.39459 22.5699 8.09487 22.5699 10.3619C22.5699 13.6378 19.0107 16.8683 13.6151 21.7537Z"
                                        fill="white"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
		<?php do_action( 'audioteria_wp_before_close_header' ); ?>
    </header>