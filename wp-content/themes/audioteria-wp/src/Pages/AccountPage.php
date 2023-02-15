<?php

namespace AudioteriaWP\Pages;

use AudioteriaWP\Data\AbstractProducts;

class AccountPage
{

    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'frontend_scripts']);
        add_filter( 'woocommerce_account_menu_items', [$this, 'audioteria_account_menu_items'], 10 );
        add_action( 'init', [$this, 'remove_product_from_account_wishlist'] );
        add_action( 'woocommerce_account_customer-wishlist_endpoint', [$this, 'audioteria_account_wishlist_endpoint_content'] );
        add_action( 'woocommerce_account_update-password_endpoint', [$this, 'audioteria_account_update_password_endpoint_content'] );
        add_action( 'woocommerce_customer_account_wishlist', [$this, 'audioteria_account_wishlist_content'] );
        add_filter( 'woocommerce_account_dashboard', [$this, 'display_customer_purchase']);
        remove_action( 'woocommerce_single_product_summary', [$this, 'woocommerce_template_single_title'], 5 );
        add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 5 );
        add_filter( 'woocommerce_billing_fields', [$this, 'audioteria_customise_default_billing_fields'], 10, 1 );
        add_action( 'woocommerce_edit_account_form', [$this, 'add_dob_to_edit_account_form'] );
        add_action( 'woocommerce_save_account_details', [$this, 'save_dob_account_details'], 12, 1 );
    }

    public function frontend_scripts()
    {
        if(is_account_page()) {
            wp_enqueue_script( 'audioteria-wp-profile-tabs', get_template_directory_uri() . '/js/profile-tabs.js', array(), AUDIOTERIA_WP_THEME_VERSION, true );
            wp_enqueue_style( 'dashicons' );
        }
    }

    public function audioteria_account_menu_items( $items ) {
        $items['dashboard'] = __( 'Purchase History', 'audioteria-wp' );
        $items['edit-address'] = __( 'Billing Details', 'audioteria-wp' );
        $items['customer-logout'] = __( 'Sign out', 'audioteria-wp' );
        unset($items['orders'], $items['downloads'], $items['edit-account'], $items['payment-methods']);
        $ordered_array = [
            'customer-wishlist' => __( 'My Wishlist', 'audioteria-wp' ),
            'edit-account'    => __( 'Account Details', 'audioteria-wp' ),
            'update-password'    => __( 'Change Password', 'audioteria-wp' ),
        ];


        return array_slice( $items, 0, 1, true ) +
            $ordered_array +
            array_slice( $items, 1, count( $items ), true );
    }

    public function remove_product_from_account_wishlist() {

        add_rewrite_endpoint( 'customer-wishlist', EP_PAGES );
        add_rewrite_endpoint( 'update-password', EP_PAGES );

        if(isset($_GET['audioteria-action']) && $_GET['audioteria-action'] == 'remove-from-wishlist' && !empty($_GET['id'])) {
            $user_id = get_current_user_id();
            $product_id = $_GET['id'];
            $redirect_url = remove_query_arg(['audioteria-action','id']);

            try {
                AbstractProducts::get_instance()->remove_from_customer_wishlist($user_id, $product_id);

                wc_add_notice( __('Product successfully removed from your wishlist', 'audioteria-wp'));
                wp_safe_redirect($redirect_url);
                exit;

            } catch (\Exception $e) {
                wc_add_notice( __('This Product could not be removed from your wishlist', 'audioteria-wp'), 'error');
                wp_safe_redirect($redirect_url);
                exit;
            }
        }

        if(isset($_GET['audioteria-action']) && $_GET['audioteria-action'] == 'add-to-wishlist' && !empty($_GET['id'])) {
            $user_id = get_current_user_id();
            $product_id = $_GET['id'];
            $redirect_url = remove_query_arg(['audioteria-action','id']);

            try {
                AbstractProducts::get_instance()->add_to_customer_wishlist($user_id, $product_id);

                wc_add_notice( __('Product successfully added from your wishlist', 'audioteria-wp'));
                wp_safe_redirect($redirect_url);
                exit;

            } catch (\Exception $e) {
                wc_add_notice( __('This Product could not be addeded from your wishlist', 'audioteria-wp'), 'error');
                wp_safe_redirect($redirect_url);
                exit;
            }
        }

        if(isset($_GET['audioteria-action']) && $_GET['audioteria-action'] == 'download-products' && !empty($_GET['id'])) {
            $user = get_userdata(get_current_user_id());
            $user_email = $user->user_email;
            $product_id = $_GET['id'];

            //check if the current user has access to the product
            $is_product_bought = AbstractProducts::get_instance()->has_bought_product([$product_id]);

            $redirect_url = remove_query_arg(['audioteria-action','id']);

            if (!$is_product_bought) {
                wc_add_notice( __('You do not have access to this product', 'audioteria-wp'), 'error');
                wp_safe_redirect($redirect_url);
                exit;
            }

            $episodes = apply_filters('audioteria_wp_modify_episodes_fields', get_field( 'episodes', $product_id ) );

            $file_name = $user_email. '_' .$product_id.'.zip';

            $episodes_array = AbstractProducts::get_instance()->return_downloadable_episodes($episodes,  false);

            if(!empty($episodes_array)) {
                $this->download_episodes($episodes_array, $file_name);
            }

            wc_add_notice( __('There  was a problem downloading your files', 'audioteria-wp'), 'error');
            wp_safe_redirect($redirect_url);
            exit;
        }

    }

    public function download_episodes($download_urls, $file_name = 'download.zip') {
        $zip = new \ZipArchive;
        $zip->open($file_name, \ZipArchive::CREATE);
        foreach ($download_urls as $file) {
            if(file_exists($file)) {
                $zip->addFromString(basename($file),  file_get_contents($file));
            }
        }

        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Disposition:attachment; filename='.$file_name);
        header("Content-length: " . filesize($file_name));
        readfile("$file_name");
        unlink($file_name);
        exit();
    }

    public function audioteria_account_wishlist_endpoint_content() {
        wc_get_template( 'myaccount/customer-wishlist.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) );
    }

    public function audioteria_account_update_password_endpoint_content() {
        wc_get_template( 'myaccount/update-password.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) );
    }

    public function display_customer_purchase(){
        $customer_order_products = AbstractProducts::get_instance()->get_user_purchases();
        $current_user = wp_get_current_user();
        $user_id      = $current_user->ID;

        if(!empty($customer_order_products)) {
            ob_start();?>
        <div class="account-content-wrapper" data-nonce="<?= wp_create_nonce("audioteria_rate_purchased_product_nonce") ?>">
            <?php foreach ($customer_order_products as $customer_order_product) { ?>

                <div class="account-content-item">
                    <a href="<?= get_permalink($customer_order_product['id']) ;?>" title="<?= $customer_order_product['name']; ?>">
                        <img src="<?= $customer_order_product['thumbnails']['large'] ?>" alt="<?= $customer_order_product['name'] ?>">
                    </a>
                    <div class="account-content-item-detail">
                        <div class="order-details">
                            <p><?= __('Ordered on:', 'audioteria-wp') ?> <?= $customer_order_product['order_date'] ?></p>
                            <p><?= __('Order No.', 'audioteria-wp') ?>  <?= $customer_order_product['order_id'] ?></p>
                        </div>
                        <div class="order-title">
                            <p><a href="<?= get_permalink($customer_order_product['id']) ;?>" title="<?= $customer_order_product['name']; ?>"><?= $customer_order_product['name']; ?></a></p>
                            <p class="order-amount"><?= get_woocommerce_currency_symbol(); ?><?= $customer_order_product['price']['regular_price'] ?></p>
                        </div>
                        <?php if(!empty($customer_order_product['main_written_by'])) {
                            $writers = $customer_order_product['main_written_by'];
                            get_custom_product_meta_html( $writers, 'Written by: ', 'writer', 'order-text');
                        } ?>
                        <?php if(!empty($customer_order_product['main_size'])) { ?><p class="order-text"><?= __('Size: ', 'audioteria-wp') ?><?= $customer_order_product['main_size'] ?></p><?php } ?>
                        <div class="order-links">
                            <?php if(!empty($customer_order_product['downloads_url'])) {
                                $download_action = add_query_arg(['audioteria-action' => 'download-products', 'id' => $customer_order_product['id']])
                                ?>
                                <a class="order-link" href="<?= $download_action ?>" title="<?= $customer_order_product['name'] ?>"><?= __('Download', 'audioteria-wp') ?></a>
                            <?php }
                            $find_comments = get_comments(['post_id' => $customer_order_product['id'], 'author_email' => $current_user->user_email]);
                            if(empty($find_comments[0])) {?>
                            <button type="button" class="order-rate"><?= __('Rate this product', 'audioteria-wp') ?></button>
                        </div>
                        <form class="rate-modal" method="POST"  style="display: none;">
                            <div class="rate-container">
                                <p><?= __('Enjoying Audioteria? ', 'audioteria-wp') ?></p>
                                <h3><?= $customer_order_product['name'] ?></h3>
                                <p><?= __('Tap a star to rate this content', 'audioteria-wp') ?></p>
                                <div class="rate-stars">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="40" height="40" viewBox="0 0 47.94 47.94" style="enable-background:new 0 0 47.94 47.94;" xml:space="preserve">
                                <path style="stroke: #CB5715;" d="M26.285,2.486l5.407,10.956c0.376,0.762,1.103,1.29,1.944,1.412l12.091,1.757  c2.118,0.308,2.963,2.91,1.431,4.403l-8.749,8.528c-0.608,0.593-0.886,1.448-0.742,2.285l2.065,12.042  c0.362,2.109-1.852,3.717-3.746,2.722l-10.814-5.685c-0.752-0.395-1.651-0.395-2.403,0l-10.814,5.685  c-1.894,0.996-4.108-0.613-3.746-2.722l2.065-12.042c0.144-0.837-0.134-1.692-0.742-2.285l-8.749-8.528  c-1.532-1.494-0.687-4.096,1.431-4.403l12.091-1.757c0.841-0.122,1.568-0.65,1.944-1.412l5.407-10.956  C22.602,0.567,25.338,0.567,26.285,2.486z"></path>
                            </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="40" height="40" viewBox="0 0 47.94 47.94" style="enable-background:new 0 0 47.94 47.94;" xml:space="preserve">
                                <path style="stroke: #CB5715;" d="M26.285,2.486l5.407,10.956c0.376,0.762,1.103,1.29,1.944,1.412l12.091,1.757  c2.118,0.308,2.963,2.91,1.431,4.403l-8.749,8.528c-0.608,0.593-0.886,1.448-0.742,2.285l2.065,12.042  c0.362,2.109-1.852,3.717-3.746,2.722l-10.814-5.685c-0.752-0.395-1.651-0.395-2.403,0l-10.814,5.685  c-1.894,0.996-4.108-0.613-3.746-2.722l2.065-12.042c0.144-0.837-0.134-1.692-0.742-2.285l-8.749-8.528  c-1.532-1.494-0.687-4.096,1.431-4.403l12.091-1.757c0.841-0.122,1.568-0.65,1.944-1.412l5.407-10.956  C22.602,0.567,25.338,0.567,26.285,2.486z"></path>
                            </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="40" height="40" viewBox="0 0 47.94 47.94" style="enable-background:new 0 0 47.94 47.94;" xml:space="preserve">
                                <path style="stroke: #CB5715;" d="M26.285,2.486l5.407,10.956c0.376,0.762,1.103,1.29,1.944,1.412l12.091,1.757  c2.118,0.308,2.963,2.91,1.431,4.403l-8.749,8.528c-0.608,0.593-0.886,1.448-0.742,2.285l2.065,12.042  c0.362,2.109-1.852,3.717-3.746,2.722l-10.814-5.685c-0.752-0.395-1.651-0.395-2.403,0l-10.814,5.685  c-1.894,0.996-4.108-0.613-3.746-2.722l2.065-12.042c0.144-0.837-0.134-1.692-0.742-2.285l-8.749-8.528  c-1.532-1.494-0.687-4.096,1.431-4.403l12.091-1.757c0.841-0.122,1.568-0.65,1.944-1.412l5.407-10.956  C22.602,0.567,25.338,0.567,26.285,2.486z"></path>
                            </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="40" height="40" viewBox="0 0 47.94 47.94" style="enable-background:new 0 0 47.94 47.94;" xml:space="preserve">
                                <path style="stroke: #CB5715;" d="M26.285,2.486l5.407,10.956c0.376,0.762,1.103,1.29,1.944,1.412l12.091,1.757  c2.118,0.308,2.963,2.91,1.431,4.403l-8.749,8.528c-0.608,0.593-0.886,1.448-0.742,2.285l2.065,12.042  c0.362,2.109-1.852,3.717-3.746,2.722l-10.814-5.685c-0.752-0.395-1.651-0.395-2.403,0l-10.814,5.685  c-1.894,0.996-4.108-0.613-3.746-2.722l2.065-12.042c0.144-0.837-0.134-1.692-0.742-2.285l-8.749-8.528  c-1.532-1.494-0.687-4.096,1.431-4.403l12.091-1.757c0.841-0.122,1.568-0.65,1.944-1.412l5.407-10.956  C22.602,0.567,25.338,0.567,26.285,2.486z"></path>
                            </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="40" height="40" viewBox="0 0 47.94 47.94" style="enable-background:new 0 0 47.94 47.94;" xml:space="preserve">
                                <path style="stroke: #CB5715;" d="M26.285,2.486l5.407,10.956c0.376,0.762,1.103,1.29,1.944,1.412l12.091,1.757  c2.118,0.308,2.963,2.91,1.431,4.403l-8.749,8.528c-0.608,0.593-0.886,1.448-0.742,2.285l2.065,12.042  c0.362,2.109-1.852,3.717-3.746,2.722l-10.814-5.685c-0.752-0.395-1.651-0.395-2.403,0l-10.814,5.685  c-1.894,0.996-4.108-0.613-3.746-2.722l2.065-12.042c0.144-0.837-0.134-1.692-0.742-2.285l-8.749-8.528  c-1.532-1.494-0.687-4.096,1.431-4.403l12.091-1.757c0.841-0.122,1.568-0.65,1.944-1.412l5.407-10.956  C22.602,0.567,25.338,0.567,26.285,2.486z"></path>
                            </svg>
                                </div>
                                <input type="text" name="rating-<?= $customer_order_product['id']; ?>" id="rating-value" value="" hidden="">
                                <div class="rate-buttons">
                                    <button class="rate-cancel" type="button">
                                        <?= __('Cancel', 'audioteria-wp') ?>
                                    </button>
                                    <button class="rate-submit" id="submit-rating" data-item-id="<?= $customer_order_product['id']; ?>" data-user-id="<?= $user_id; ?>">
                                        <?= __('Confirm', 'audioteria-wp') ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <?php } else {
                        $order_product = wc_get_product($customer_order_product['id']);
                        $rating_count = $order_product->get_rating_count();
                        $review_count = $order_product->get_review_count();
                        $average      = (int) $order_product->get_average_rating();
                        if ($average >= 0) { ?>
                        <div class="order-rating">
                            <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M17.5683 6.55172L12.8573 5.86705L10.7514 1.59761C10.6938 1.48072 10.5992 1.38609 10.4823 1.32857C10.1891 1.18384 9.8329 1.30445 9.68632 1.59761L7.58036 5.86705L2.86933 6.55172C2.73944 6.57027 2.62069 6.6315 2.52978 6.72428C2.41986 6.83725 2.35929 6.98924 2.36138 7.14684C2.36347 7.30445 2.42804 7.45478 2.54091 7.5648L5.9494 10.8879L5.14413 15.5804C5.12525 15.6896 5.13733 15.8019 5.179 15.9045C5.22067 16.0071 5.29027 16.096 5.3799 16.1611C5.46953 16.2262 5.57561 16.2649 5.68611 16.2728C5.79661 16.2807 5.90711 16.2574 6.00507 16.2057L10.2188 13.9903L14.4326 16.2057C14.5476 16.267 14.6812 16.2874 14.8093 16.2651C15.1321 16.2094 15.3492 15.9033 15.2935 15.5804L14.4883 10.8879L17.8968 7.5648C17.9895 7.47389 18.0508 7.35513 18.0693 7.22525C18.1194 6.90054 17.8931 6.59996 17.5683 6.55172Z"
                                    <?= $average >= 1 ? 'fill="#CB5715"': 'stroke="#CB5715" fill="#ffffff"' ?> />
                            </svg>
                            <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M17.5683 6.55172L12.8573 5.86705L10.7514 1.59761C10.6938 1.48072 10.5992 1.38609 10.4823 1.32857C10.1891 1.18384 9.8329 1.30445 9.68632 1.59761L7.58036 5.86705L2.86933 6.55172C2.73944 6.57027 2.62069 6.6315 2.52978 6.72428C2.41986 6.83725 2.35929 6.98924 2.36138 7.14684C2.36347 7.30445 2.42804 7.45478 2.54091 7.5648L5.9494 10.8879L5.14413 15.5804C5.12525 15.6896 5.13733 15.8019 5.179 15.9045C5.22067 16.0071 5.29027 16.096 5.3799 16.1611C5.46953 16.2262 5.57561 16.2649 5.68611 16.2728C5.79661 16.2807 5.90711 16.2574 6.00507 16.2057L10.2188 13.9903L14.4326 16.2057C14.5476 16.267 14.6812 16.2874 14.8093 16.2651C15.1321 16.2094 15.3492 15.9033 15.2935 15.5804L14.4883 10.8879L17.8968 7.5648C17.9895 7.47389 18.0508 7.35513 18.0693 7.22525C18.1194 6.90054 17.8931 6.59996 17.5683 6.55172Z"
                                    <?= $average >= 2 ? 'fill="#CB5715"': 'stroke="#CB5715" fill="#ffffff"' ?> />
                            </svg>
                            <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M17.5683 6.55172L12.8573 5.86705L10.7514 1.59761C10.6938 1.48072 10.5992 1.38609 10.4823 1.32857C10.1891 1.18384 9.8329 1.30445 9.68632 1.59761L7.58036 5.86705L2.86933 6.55172C2.73944 6.57027 2.62069 6.6315 2.52978 6.72428C2.41986 6.83725 2.35929 6.98924 2.36138 7.14684C2.36347 7.30445 2.42804 7.45478 2.54091 7.5648L5.9494 10.8879L5.14413 15.5804C5.12525 15.6896 5.13733 15.8019 5.179 15.9045C5.22067 16.0071 5.29027 16.096 5.3799 16.1611C5.46953 16.2262 5.57561 16.2649 5.68611 16.2728C5.79661 16.2807 5.90711 16.2574 6.00507 16.2057L10.2188 13.9903L14.4326 16.2057C14.5476 16.267 14.6812 16.2874 14.8093 16.2651C15.1321 16.2094 15.3492 15.9033 15.2935 15.5804L14.4883 10.8879L17.8968 7.5648C17.9895 7.47389 18.0508 7.35513 18.0693 7.22525C18.1194 6.90054 17.8931 6.59996 17.5683 6.55172Z"
                                    <?= $average >= 3 ? 'fill="#CB5715"': 'stroke="#CB5715" fill="#ffffff"' ?> />
                            </svg>
                            <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M17.5683 6.55172L12.8573 5.86705L10.7514 1.59761C10.6938 1.48072 10.5992 1.38609 10.4823 1.32857C10.1891 1.18384 9.8329 1.30445 9.68632 1.59761L7.58036 5.86705L2.86933 6.55172C2.73944 6.57027 2.62069 6.6315 2.52978 6.72428C2.41986 6.83725 2.35929 6.98924 2.36138 7.14684C2.36347 7.30445 2.42804 7.45478 2.54091 7.5648L5.9494 10.8879L5.14413 15.5804C5.12525 15.6896 5.13733 15.8019 5.179 15.9045C5.22067 16.0071 5.29027 16.096 5.3799 16.1611C5.46953 16.2262 5.57561 16.2649 5.68611 16.2728C5.79661 16.2807 5.90711 16.2574 6.00507 16.2057L10.2188 13.9903L14.4326 16.2057C14.5476 16.267 14.6812 16.2874 14.8093 16.2651C15.1321 16.2094 15.3492 15.9033 15.2935 15.5804L14.4883 10.8879L17.8968 7.5648C17.9895 7.47389 18.0508 7.35513 18.0693 7.22525C18.1194 6.90054 17.8931 6.59996 17.5683 6.55172Z"
                                    <?= $average >= 4 ? 'fill="#CB5715"': 'stroke="#CB5715" fill="#ffffff"' ?> />
                            </svg>
                            <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M17.5683 6.55172L12.8573 5.86705L10.7514 1.59761C10.6938 1.48072 10.5992 1.38609 10.4823 1.32857C10.1891 1.18384 9.8329 1.30445 9.68632 1.59761L7.58036 5.86705L2.86933 6.55172C2.73944 6.57027 2.62069 6.6315 2.52978 6.72428C2.41986 6.83725 2.35929 6.98924 2.36138 7.14684C2.36347 7.30445 2.42804 7.45478 2.54091 7.5648L5.9494 10.8879L5.14413 15.5804C5.12525 15.6896 5.13733 15.8019 5.179 15.9045C5.22067 16.0071 5.29027 16.096 5.3799 16.1611C5.46953 16.2262 5.57561 16.2649 5.68611 16.2728C5.79661 16.2807 5.90711 16.2574 6.00507 16.2057L10.2188 13.9903L14.4326 16.2057C14.5476 16.267 14.6812 16.2874 14.8093 16.2651C15.1321 16.2094 15.3492 15.9033 15.2935 15.5804L14.4883 10.8879L17.8968 7.5648C17.9895 7.47389 18.0508 7.35513 18.0693 7.22525C18.1194 6.90054 17.8931 6.59996 17.5683 6.55172Z"
                                    <?= $average >= 5 ? 'fill="#CB5715"': 'stroke="#CB5715" fill="#ffffff"' ?> />
                            </svg>
                        </div>
                    </div>
                    <?php }
                    } ?>
                </div>
                </div>

            <?php } ?>
            </div>
            <?php echo ob_get_clean();
        } else {
            ob_start(); ?>
            <div class="wishlist-empty">
                <svg width="100" height="100" viewBox="0 0 100 100" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M95.4687 29.8438C88.4375 10.9375 70.1562 11.7188 59.2187 18.2813L50.9375 35.9375L66.5625 34.5313L50.4687 55.1563L63.5937 51.7188L55.3125 84.375L52.8125 62.3438L34.2187 66.25L48.4375 43.75L36.25 42.0313L44.6875 22.6563C34.6875 12.6563 8.74998 10.7813 3.74998 34.8438C-2.34377 63.4375 46.4062 81.0938 55.1562 85.9375V86.0938H55.4687V85.9375C68.4375 77.3438 104.375 53.9063 95.4687 29.8438Z"
                            fill="#C4C4C4" />
                </svg>
                <p><?= __('Looks like your purchase history is empty', 'audioteria-wp') ?></p>
            </div>
            <?php  echo ob_get_clean();
        }
    }

    public function audioteria_account_wishlist_content(){
        $wishlist_items = AbstractProducts::get_instance()->get_customer_wishlist();
        $current_user = wp_get_current_user();
        $user_id      = $current_user->ID;
        add_filter('woocommerce_loop_add_to_cart_args', '');
        if(!empty($wishlist_items)) {
            $no_wishlist_items = count($wishlist_items);
            ob_start();?>

            <div class="account-content-wrapper wishlist-wrapper">
                <p class="wishlist-number"><?= __('You have ', 'audioteria-wp') ?><?= $no_wishlist_items ?> <?= __('saved products', 'audioteria-wp') ?></p>
                <?php
                foreach ($wishlist_items as $wishlist_item) {
                    $remove_query_args = add_query_arg(['audioteria-action' => 'remove-from-wishlist', 'id' => $wishlist_item['id']]);
                    $product = wc_get_product($wishlist_item['id']);
                    $class = implode(
                        ' ',
                        array_filter(
                            array(
                                'button order-link',
                                'product_type_' . $product->get_type(),
                                $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                                $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
                            )
                        )
                    );
                    $attributes = array(
                        'data-product_id'  => $product->get_id(),
                        'data-product_sku' => $product->get_sku(),
                        'aria-label'       => $product->add_to_cart_description(),
                        'rel'              => 'nofollow',
                    );
                    ?>
                    <div class="account-content-item wishlist-item">
                        <a href="<?= get_permalink($wishlist_item['id']); ?>" title="<?= $wishlist_item['name'] ?>">
                            <img src="<?= $wishlist_item['thumbnails']['medium'] ?>" alt="<?= $wishlist_item['name'] ?>">
                        </a>
                        <div class="account-content-item-detail">
                            <div class="wishlist-title order-title">
                                <p><a href="<?= get_permalink($wishlist_item['id']); ?>" title="<?= $wishlist_item['name'] ?>"><?= $wishlist_item['name'] ?></a></p>
                                <?php
                                if(!empty($wishlist_item['price']['regular_price'])) {
                                    ?>
                                    <p class="order-amount"><?= get_woocommerce_currency_symbol(); ?><?=  $wishlist_item['price']['regular_price'] ?></p>
                                <?php } ?>
                            </div>
                            <p class="wishlist-desc"><?= limit_character($wishlist_item['description'], 120) ?></p>
                            <?php if(!empty($wishlist_item['main_written_by'])) {
                                $writers = $wishlist_item['main_written_by'];
                                get_custom_product_meta_html( $writers, 'Written by: ', 'writer', 'order-text');
                            } ?>

                            <div class="wishlist-links others" data-nonce="<?= wp_create_nonce("audioteria_wp_wishlist_action"); ?>">
                                <?php $args = [];
                                $cart_button_text = file_get_contents(AUDIOTERIA_ASSETS_ICONS_DIR. '/bag-icon.svg');
//                                $cart_button_text .=  __('Add to bag', 'audioteria-wp');
                                $extra_class = (WC()->cart->find_product_in_cart(WC()->cart->generate_cart_id($product->get_id()))) ? ' added' : '';
                                echo apply_filters(
                                    'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
                                    sprintf(
                                        '<button onclick="document.location.href=\'%s\'" data-quantity="%s" class="%s" %s >%s %s</button>',
                                        esc_url( $product->add_to_cart_url() ),
                                        esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
                                        esc_attr( isset( $args['class'] ) ? $args['class'] .$extra_class : $class . $extra_class ),
                                        isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : wc_implode_html_attributes($attributes),
                                        $cart_button_text,
                                        apply_filters( 'woocommerce_product_add_to_cart_text', $product->add_to_cart_text(), $product )
                                    ),
                                    $product,
                                    $args
                                );
                                ?>

                                <button id="wishlist-action" data-user-id="<?= $user_id ?>" data-item-id="<?= $wishlist_item['id']; ?>" data-action="remove" class="wishlist-remove" title="Remove">
                                    <?php include(AUDIOTERIA_ASSETS_ICONS_DIR. '/remove-icon.svg'); ?>
                                    <?=  __('Remove', 'audioteria-wp'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </div>

            <?php echo ob_get_clean();
        } else {
            ob_start(); ?>
            <div class="wishlist-empty">
                <svg width="100" height="100" viewBox="0 0 100 100" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M95.4687 29.8438C88.4375 10.9375 70.1562 11.7188 59.2187 18.2813L50.9375 35.9375L66.5625 34.5313L50.4687 55.1563L63.5937 51.7188L55.3125 84.375L52.8125 62.3438L34.2187 66.25L48.4375 43.75L36.25 42.0313L44.6875 22.6563C34.6875 12.6563 8.74998 10.7813 3.74998 34.8438C-2.34377 63.4375 46.4062 81.0938 55.1562 85.9375V86.0938H55.4687V85.9375C68.4375 77.3438 104.375 53.9063 95.4687 29.8438Z"
                            fill="#C4C4C4" />
                </svg>
                <p><?= __('Looks like your wishlist is empty', 'audioteria-wp') ?></p>
            </div>
            <?php echo ob_get_clean();
        }
    }

    public function audioteria_customise_default_billing_fields( $fields ) {

        $fields['billing_country']['label'] = __('Country', 'audioteria-wp');
        $fields['billing_country']['placeholder'] = 'Select Country';

        return $fields;
    }
    public function add_dob_to_edit_account_form() {
        $user = wp_get_current_user();
        $dob = $user->dob_field;
        $return_date = [
            'day' => '',
            'month' => '',
            'year' => ''
        ];
        $get_all_days_months_years = get_all_days_months_years();
        if(!empty($dob)){
            $dob_format = \DateTime::createFromFormat('Y-n-d', $dob);
            $dob_array = explode( '-', $dob, 3);
            $return_date['day'] = (int) $dob_format->format('d');
            $return_date['month'] = $dob_format->format('n');
            $return_date['year'] = $dob_format->format('Y');
        }
        // DOB Field
        ?>
        <p class="date"><?= __('Date of Birth', 'audioteria-wp') ?></p>
        <div class="date-select-wrapper">
            <div class="date-select">
                <select name="dob_day" class="woocommerce-Input woocommerce-Input--select input-select">
                    <option value=""><?= __('Day', 'audioteria-wp') ?></option>
                    <?php
                    $days = $get_all_days_months_years['days'];
                    foreach ($days as $day) {
                        ?>
                        <option value="<?= $day; ?>" <?php selected( $day, $return_date['day']) ?>><?= $day; ?></option>
                        <?php
                    }
                    ?>
                </select>
                <?php include(AUDIOTERIA_ASSETS_ICONS_DIR. '/angle-down.svg'); ?>
            </div>
            <div class="date-select">
                <select name="dob_month" id="month" class="woocommerce-Input woocommerce-Input--select input-select">
                    <option value=""><?= __('Month', 'audioteria-wp') ?></option>
                    <?php
                    $months = $get_all_days_months_years['months'];
                    foreach ($months as $key => $value) {
                        ?>
                        <option value="<?= $key; ?>"  <?php selected($key, $return_date['month']) ?>><?= $value; ?></option>
                    <?php } ?>
                </select>
                <?php include(AUDIOTERIA_ASSETS_ICONS_DIR. '/angle-down.svg'); ?>
            </div>
            <div class="date-select">
                <select name="dob_year" id="year" class="woocommerce-Input woocommerce-Input--select input-select">
                    <option value=""><?= __('Year', 'audioteria-wp') ?></option>
                    <?php
                    $years = $get_all_days_months_years['years'];
                    foreach ($years as $key => $value) {
                        ?>
                        <option value="<?= $key; ?>" <?php selected($key, $return_date['year']) ?>><?= $value; ?></option>
                    <?php } ?>
                </select>
                <?php include(AUDIOTERIA_ASSETS_ICONS_DIR. '/angle-down.svg'); ?>
            </div>
            <div class="clear"></div>
        </div>
        <?php
    }

    public function save_dob_account_details( $user_id ) {
        if(isset($_POST['dob_day']) && isset($_POST['dob_month']) && isset($_POST['dob_year']) ) {
            if (!empty($_POST['dob_day']) && !empty($_POST['dob_month']) && !empty($_POST['dob_year'])) {
                $day_value = (int)sanitize_text_field($_POST['dob_day']);
                $month_value = (int)sanitize_text_field($_POST['dob_month']);
                $year_value = (int)sanitize_text_field($_POST['dob_year']);
                $date = new \DateTime();
                $date->setDate($year_value, $month_value, $day_value);
                $dob_value = $date->format('Y-n-d');
                update_user_meta($user_id, 'dob_field', $dob_value);
            } else {
                update_user_meta($user_id, 'dob_field', '');
            }
        }
    }

    /**
     * @return AccountPage
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}
