<?php

namespace AudioteriaWP\Core;
use AudioteriaWP\Data\AbstractProducts;

class WooComerce {

    public function __construct() {
        add_filter( 'woocommerce_catalog_orderby', [ $this, 'modify_catalog_order' ], 10, 1 );

        add_filter( 'woocommerce_get_catalog_ordering_args', [ $this, 'add_custom_ordering_args' ], 10, 1 );

        add_filter( 'woocommerce_product_get_rating_html', [ $this, 'remove_star_rating_html' ], 10, 3 );

        add_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'change_add_to_cart_text' ], 10, 2 );

        add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

        add_filter( 'woocommerce_product_single_add_to_cart_text', [$this, 'product_single_add_to_cart_text'], 10, 2 );

        add_filter('admin_head', [$this, 'hide_unwanted_fields_from_product_section']);

        remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

        add_action( 'woocommerce_register_form', [ $this, 'audioteria_extra_register_fields' ] );

        add_action( 'woocommerce_register_post', [ $this, 'audioteria_validate_extra_register_fields' ], 10, 3 );

        remove_action( 'woocommerce_register_form', 'wc_registration_privacy_policy_text', 20 );

        add_action( 'woocommerce_register_form', [ $this, 'audioteria_after_form_text' ], 20 );

        remove_action( 'woocommerce_before_customer_login_form', 'woocommerce_output_all_notices', 10 );

        add_action( 'woocommerce_login_form_start', 'woocommerce_output_all_notices', 10 );

        add_filter( 'login_errors', [ $this, 'audioteria_invalid_error' ] );

        add_action( 'woocommerce_created_customer', [ $this, 'audioteria_save_extra_register_fields' ] );

        add_filter( 'woocommerce_email_order_item_quantity', [ $this, 'audioteria_remove_qty' ] );

        add_action( 'woocommerce_order_item_meta_end', [ $this, 'audioteria_written_by' ], 10, 4 );

        add_filter( 'gettext', [ $this, 'audioteria_product_short_description' ], 10, 2 );

        add_filter( 'woocommerce_is_purchasable', [$this, 'audioteria_deny_purchase_if_already_purchased'], 9999, 2 );

        add_filter( 'woocommerce_cart_item_remove_link', [$this, 'remove_cart_item'], 9, 2 );

        add_action('woocommerce_checkout_before_customer_details', [$this, 'append_before_customer_details'], 10, 1);

        add_action('woocommerce_checkout_after_customer_details', [$this, 'append_after_customer_details'], 10, 1);

        add_action('woocommerce_checkout_after_order_review', [$this, 'append_after_order_review'], 10, 1);

        add_action('woocommerce_checkout_before_order_review_heading', [$this, 'append_before_order_review_heading'], 10, 1);

        add_action('woocommerce_checkout_before_order_review', [$this, 'append_before_order_review'], 10, 1);

        add_action('audioteria_woocommerce_after_custom_checkout_field', [$this, 'append_after_custom_checkout'], 10, 1);

        add_filter( 'wp_editor_settings', [$this, 'filter_wp_editor_settings'], 1, 2);

        add_filter( 'woocommerce_short_description', [$this, 'filter_woocommerce_short_description'], 10, 1 );

        add_action('admin_enqueue_scripts', [$this, 'enqueue_woocommerce_admin_scripts'], 10, 1);

        add_action( 'woocommerce_thankyou', [$this, 'audioteria_woocommerce_auto_complete_order'] );

        add_action( 'woocommerce_payment_complete', [$this, 'audioteria_set_completed_for_paid_orders'] );

        add_filter( 'wc_add_to_cart_message_html', '__return_false' );

        add_filter( 'woocommerce_cart_item_removed_notice_type', '__return_false' );
    }

    public function hide_unwanted_fields_from_product_section() {	?>
        <style>
            #woocommerce-product-images,
            #woocommerce-product-data .type_box,
            #woocommerce-product-data label[for="_virtual"],
            #woocommerce-product-data label[for="_downloadable"],
            #woocommerce-product-data .product_data_tabs li.inventory_options,
            #woocommerce-product-data .product_data_tabs li.shipping_options,
            #woocommerce-product-data .product_data_tabs li.linked_product_options,
            #woocommerce-product-data .product_data_tabs li.attribute_options,
            #woocommerce-product-data .product_data_tabs li.variations_options,
            #woocommerce-product-data .product_data_tabs li.advanced_options,
            #woocommerce-product-data .product_data_tabs li.marketplace-suggestions_options,
            #woocommerce-product-data .product_data_tabs div#inventory_product_data,
            #woocommerce-product-data .product_data_tabs div#shipping_product_data,
            #woocommerce-product-data .product_data_tabs div#linked_product_data,
            #woocommerce-product-data .product_data_tabs div#product_attributes,
            #woocommerce-product-data .product_data_tabs div#advanced_product_data,
            #woocommerce-product-data .product_data_tabs div#variable_product_options,
            #woocommerce-product-data .product_data_tabs div#marketplace_suggestions,
            #advanced-sortables #page-links-to {
                display:none !important;
            }
            #woocommerce-product-data > .postbox-header > h2.hndle.ui-sortable-handle {
                position: relative;
            }
            #woocommerce-product-data > .postbox-header > h2.hndle.ui-sortable-handle:after {
                content: "Product Price";
                display: block !important;
                background-color: #ffff;
                position: absolute;
                top: 0;
                line-height: 24px;
                padding: 10px;
            }
        </style>
    <?php }

    public function filter_wp_editor_settings( $settings, $editor_id ) {
        global $post;

        if ( $editor_id == 'excerpt' && get_post_type( $post ) == 'product' ) {

            $settings = array(
                'wpautop'             => ! has_blocks(),
                'media_buttons'       => false,
                'default_editor'      => '',
                'drag_drop_upload'    => false,
                'textarea_name'       => $editor_id,
                'textarea_rows'       => 40,
                'tabindex'            => '',
                'tabfocus_elements'   => ':prev,:next',
                'editor_css'          => '',
                'editor_class'        => 'audioteria-custom-product-description',
                'teeny'               => false,
                '_content_editor_dfw' => false,
                'tinymce'             => false,
                'quicktags'           => false,
                'textarea_maxlength'           => 30
            );
        }

        return $settings;
    }

    public function filter_woocommerce_short_description( $short_description ) {
        return wp_strip_all_tags( $short_description );
    }

    public function audioteria_product_short_description( $translation, $original ) {
        if ( 'Product short description' == $original ) {
            return __( 'Teaser', 'audioteria-wp' );
        }

        return $translation;
    }

    public function enqueue_woocommerce_admin_scripts() {
        global $post;

        if(isset($post->post_type) && $post->post_type == 'product') {
            wp_enqueue_script('audioteria-woocomerce-custom-admin-js', get_template_directory_uri() . '/js/admin.js', [], AUDIOTERIA_WP_THEME_VERSION, true );
        }

        if(isset($post->post_type) && $post->post_type == 'banners') {
            wp_enqueue_script('audioteria-woocomerce-custom-home-banner-js', get_template_directory_uri() . '/js/home-banner.js', [], AUDIOTERIA_WP_THEME_VERSION, true );
        }
    }

    public function audioteria_written_by( $item_id, $item, $order, $plain_text ) {

        $product_id = $item['product_id'];
        $product_obj = wc_get_product($product_id);
        $products_data = AbstractProducts::get_instance()->product_returned_schema($product_obj, AbstractProducts::get_instance()->taxonomy, AbstractProducts::get_instance()->genre, []);

        if(!empty($products_data)){
            $product_writer = $products_data['main_written_by'];
            if(!empty($product_writer)) {
                get_custom_product_meta_html($product_writer, 'Written by: ', 'writer', 'order-author');
            }
        }else {
            return '';
        }
    }

    public function append_before_customer_details() {
        ?>
        <div class="shopping">
        <section class="address-wrapper">
        <section class="billing">
        <div class="header">
            <p class="title"><?= __('Billing Details', 'audioteria-wp') ?></p>
        </div>
        <hr>
        <?php
    }

    public function append_after_customer_details() {
        ?>
        </section>
        <?php
    }

    public function append_before_order_review_heading() {
        ?>
        <section class="shopping-bag">
        <?php
    }

    public function append_after_order_review() {
        ?>
        </section>
        </section>
        <?php
    }

    public function append_before_order_review() {
        ?>
        <hr />
        <?php
    }

    public function audioteria_remove_qty() {
        return '';
    }

    public function audioteria_invalid_error( $error ) {
        global $error;

        return ' Incorrect login details. Try again';

    }

    public function audioteria_registration_form_end() {
        ob_start();
        ?>
        <p class="link-sign-in"><?= __( 'Already a member?', 'audioteria-wp' ) ?>
            <a title="sign in" href="<?= esc_url_raw( remove_query_arg( [ 'action'] ) ) ?>"> <?= __( 'Sign in', 'aodioteria-wp' ) ?> </a>
        </p>
        <?php
        echo ob_get_clean();
    }

    public function modify_catalog_order( $options ) {

        unset( $options['rating'], $options['price'], $options['price-desc'] );
        $options['menu_order'] = __( 'Sort By: Default', 'audioteria-wp' );
        $options['popularity'] = __( 'Sort By: Popular', 'audioteria-wp' );
        $options['date']       = __( 'Sort By: Latest', 'audioteria-wp' );
        $options['a-z']        = __( 'Sort By: A to Z', 'audioteria-wp' );
        $options['z-a']        = __( 'Sort By: Z to A', 'audioteria-wp' );

        return $options;
    }

    public function add_custom_ordering_args( $args ) {
        $order_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

        switch ( $order_value ) {
            case 'a-z':
                $args['orderby'] = 'title';
                $args['order']   = 'ASC';
                break;
            case 'z-a':
                $args['orderby'] = 'title';
                $args['order']   = 'DESC';
                break;
        }

        return $args;
    }

    public function remove_star_rating_html( $html, $rating, $count ) {
        return '';
    }

    public function change_add_to_cart_text( $text, $product ) {

        $prod_arr = [$product->get_id()];
        $purchase_check = AbstractProducts::get_instance()->has_bought_product($prod_arr);
        if($purchase_check && $text !== 'Add to cart' && $text !== 'Added to cart'){
            $text =  __( 'View', 'audioteria-wp' );
        }elseif ($text  == 'Add to cart'){
            $text =  __( 'Buy', 'audioteria-wp' );
        }elseif (is_account_page()){
            $text =  (WC()->cart->find_product_in_cart( WC()->cart->generate_cart_id( $product->get_id() ) )) ? __( 'Added to bag', 'audioteria-wp' ) : __( 'Add to bag', 'audioteria-wp' );
        }

        return $text;
    }

    public function audioteria_wc_registration_form() {
        global $wpdb;

        $success = '';

        if ( $_POST ) {

            $email      = esc_sql( $_POST['email'] );
            $firstname  = esc_sql( $_POST['firstname'] );
            $lastname   = esc_sql( $_POST['lastname'] );
            $dob        = esc_sql( $_POST['dob'] );
            $password   = $_POST['password'];
            $country    = esc_sql( $_POST['country'] );
            $city       = esc_sql( $_POST['city'] );
            $gender     = $_POST['gender'];
            $newsletter = $_POST['newsletter'] ?? '';


            $error = array();


            if ( empty( $email ) ) {

                $error['email_error'] = "Email Field Cannot be empty";
            } else if ( ! is_email( $email ) ) {
                $error['email_error'] = "Please Enter a Valid Email";
            } elseif ( email_exists( $email ) ) {
                $error['email_error'] = "Email Already Exists";
            }

            if ( empty( $firstname ) ) {
                $error['firstname_error'] = "First Name Field Cannot be Empty";
            }

            if ( empty( $lastname ) ) {
                $error['lastname_error'] = "Last Name Field Cannot be Empty";
            }

            if ( empty( $dob ) ) {
                $error['dob_error'] = "Date Of Birth Field Cannot be Empty";
            }

            if ( empty( $password ) ) {
                $error['password_error'] = "Password Field Cannot be Empty";
            }

            if ( empty( $country ) ) {
                $error['country_error'] = "Country Field Cannot be Empty";
            }

            if ( empty( $city ) ) {
                $error['city_error'] = "City Field Cannot be Empty";
            }

            if ( empty( $gender ) ) {
                $error['gender_error'] = "Gender Field Cannot be Empty";
            }

            if ( count( $error ) == 0 ) {

                $meta = array(
                    'dob'                  => $dob,
                    'country'              => $country,
                    'city'                 => $city,
                    'gender'               => $gender,
                    'newsletter'           => $newsletter,
                    'show_admin_bar_front' => false
                );


                $user = wp_create_user( $firstname, $password, $email );


                if ( ! is_wp_error( $user ) ) {
                    foreach ( $meta as $key => $val ) {
                        update_user_meta( $user, $key, $val );
                    }


                    $success = true;

                    $home   = home_url( '/my-account' );
                    $script = "<script>
                    setTimeout(function () {
                    window.location.href= '$home';
                    }, 3000);</script>";
                    echo $script;
                }
            }
        }
        ob_start();

        ?>
        <form method=" POST" id="audioteria_reg_custom">
            <?php if ( $success ) { ?>
                <p class="success-msg">
                    <?= __( 'Registration Successful, Please Login', 'audioteria-wp' ) ?>
                </p>
            <?php } ?>
            <input type="email" id="reg_email" name="email" placeholder="Email" value="<?= $_POST['email'] ?? '' ?>">
            <p class="d-none error-text"></p>
            <?php if ( isset( $error['email_error'] ) ) { ?>
                <small><?php echo $error['email_error'] ?></small>
            <?php } ?>

            <input type="text" id="reg_firstname" name="firstname" placeholder="First Name"
                   value="<?= $_POST['firstname'] ?? '' ?>">
            <p class="d-none error-text"></p>
            <?php if ( isset( $error['firstname_error'] ) ) { ?>
                <small><?php echo $error['firstname_error'] ?></small>
            <?php } ?>
            <input type="text" id="reg_lastname" name="lastname" placeholder="Last Name"
                   value="<?= $_POST['lastname'] ?? '' ?>">
            <p class="d-none error-text"></p>
            <?php if ( isset( $error['lastname_error'] ) ) { ?>
                <small><?php echo $error['lastname_error'] ?></small>
            <?php } ?>
            <input type="text" id="reg_date" onfocus="(this.type='date')" onblur="(this.type='text')" name="dob"
                   placeholder="date of birth" value="<?= $_POST['dob'] ?? '' ?>">
            <p class="d-none error-text"></p>
            <?php if ( isset( $error['dob_error'] ) ) { ?>
                <small><?php echo $error['dob_error'] ?></small>
            <?php } ?>
            <input type="password" id="reg_password" name="password" placeholder="Password"
                   value="<?= $_POST['password'] ?? '' ?>">
            <p class="d-none error-text"></p>
            <?php if ( isset( $error['password_error'] ) ) { ?>
                <small><?php echo $error['password_error'] ?></small>
            <?php } ?>
            <input type="text" id="reg_country" name="country" placeholder="Country"
                   value="<?= $_POST['country'] ?? '' ?>">
            <p class="d-none error-text"></p>
            <?php if ( isset( $error['country_error'] ) ) { ?>
                <small><?php echo $error['country_error'] ?></small>
            <?php } ?>
            <input type="text" id="reg_city" name="city" placeholder="city" value="<?= $_POST['city'] ?? '' ?>">
            <p class="d-none error-text"></p>
            <?php if ( isset( $error['city_error'] ) ) { ?>
                <small><?php echo $error['city_error'] ?></small>
            <?php } ?>
            <div class="split" id="gender">

                <div>
                    <label for="male">Male</label>
                    <input type="radio" id="male" name="gender" value="Male"
                        <?php echo ( isset( $_POST['gender'] ) == 'Male' ) ? 'checked' : ''; ?>>
                </div>
                <div>
                    <label for="female">Female</label>
                    <input type="radio" id="female" name="gender" value="Female"
                        <?php echo ( isset( $_POST['gender'] ) == 'Female' ) ? 'checked' : ''; ?>>
                </div>

            </div>
            <p class="d-none error-text"></p>
            <?php if ( isset( $error['gender_error'] ) ) { ?>
                <small><?php echo $error['gender_error'] ?></small>
            <?php } ?>
            <div class="update">
                <input type="checkbox" name="newsletter" <?= ( isset( $_POST['newsletter'] ) ) ? 'checked' : ''; ?>>
                <p><?= __( 'Sign up for emails to get updates on latest products and offer sales', 'audioteria-wp' ) ?></p>
            </div>
            <p class="consent">
                <?= printf('By creating this account you agree with our <a href="%s" title="privacy policy"> privacy policy</a>
            and <a href="%s" title="Term of use"> Terms of use</a>', get_permalink( get_page_by_title( 'Privacy Policy' ) ), get_permalink( get_page_by_title( 'Term of use' ) ) ) ?>
            </p>
            <?php if ( $success ) { ?>
                <p class="success-msg">
                    <?= __( 'Registration Successful', 'audioteria-wp' ) ?>
                </p>
            <?php } ?>
            <button type="submit" class="sign-up-btn" title="sign up">
                <?= __( 'Sign up', 'audioteria-wp' ) ?>
            </button>
        </form>
        <p class="link-sign-in"><?= __( 'Already a member?', 'audioteria-wp' ) ?>
            <a title="sign in"
               href="<?= esc_url_raw( add_query_arg( [ 'action' => 'register' ] ) ) ?>"><?= __( 'Sign in', 'audioteria-wp' ) ?> </a>
        </p>

        <?php
        return ob_get_clean();
    }

    public function append_after_custom_checkout() {
        ?>
        </div>
        <?php
    }

    public function remove_cart_item($cart_html, $cart_item_key) {
        return '';
    }

    public function audioteria_extra_register_fields() { ?>
        <input type="text" id="reg_firstname" name="billing_first_name" placeholder="First Name"
               value="<?= $_POST['billing_first_name'] ?? '' ?>">

        <input type="text" id="reg_lastname" name="billing_last_name" placeholder="Last Name"
               value="<?= $_POST['billing_last_name'] ?? '' ?>">


        <input type="text" id="reg_date" onfocus="(this.type='date')" onblur="(this.type='text')" name="billing_dob"
               placeholder="date of birth" value="<?= $_POST['billing_dob'] ?? '' ?>">

        <input type="password" placeholder="Password" name="password" id="reg_password" autocomplete="new-password"/>

        <input type="text" id="reg_country" name="billing_country" placeholder="Country"
               value="<?= $_POST['billing_country'] ?? '' ?>">

        <input type="text" id="reg_city" name="billing_city" placeholder="city"
               value="<?= $_POST['billing_city'] ?? '' ?>">


        <div class="split" id="gender">

            <div>
                <label for="male">Male</label>
                <input type="radio" id="male" name="billing_gender" value="Male"
                    <?php echo ( isset( $_POST['billing_gender'] ) == 'Male' ) ? 'checked' : ''; ?>>
            </div>
            <div>
                <label for="female">Female</label>
                <input type="radio" id="female" name="billing_gender" value="Female"
                    <?php echo ( isset( $_POST['billing_gender'] ) == 'Female' ) ? 'checked' : ''; ?>>
            </div>

        </div>

        <div class="update">
            <input type="checkbox"
                   name="billing_newsletter" <?= ( isset( $_POST['billing_newsletter'] ) ) ? 'checked' : ''; ?>>
            <p><?= __( 'Sign up for emails to get updates on latest products and offer sales', 'audioteria-wp' ) ?></p>
        </div>


        <?php
    }

    public function audioteria_validate_extra_register_fields( $username, $email, $validation_errors ) {
        if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
            $validation_errors->add( 'billing_first_name_error', __( 'First Name is required!', 'audioteria-wp' ) );
        }
        if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
            $validation_errors->add( 'billing_last_name_error', __( 'Last Name is required!', 'audioteria-wp' ) );
        }
        if ( isset( $_POST['billing_dob'] ) && empty( $_POST['billing_dob'] ) ) {
            $validation_errors->add( 'dob_error', __( 'Date Of Birth is required!', 'audioteria-wp' ) );
        }
        if ( isset( $_POST['password'] ) && empty( $_POST['password'] ) ) {
            $validation_errors->add( 'password_error', __( 'Password is required!', 'audioteria-wp' ) );
        }
        if ( isset( $_POST['billing_country'] ) && empty( $_POST['billing_country'] ) ) {
            $validation_errors->add( 'billing_country_error', __( 'Country Of Birth is required!', 'audioteria-wp' ) );
        }
        if ( isset( $_POST['billing_city'] ) && empty( $_POST['billing_city'] ) ) {
            $validation_errors->add( 'billing_city_error', __( 'City is required!', 'audioteria-wp' ) );
        }
        if ( isset( $_POST['billing_gender'] ) && empty( $_POST['billing_gender'] ) ) {
            $validation_errors->add( 'billing_gender_error', __( 'Gender is required!', 'audioteria-wp' ) );
        }
        if ( isset( $_POST['billing_newsletter'] ) && empty( $_POST['billing_newsletter'] ) ) {
            $validation_errors->add( 'billing_newsletter_error', __( 'Newsletter is required!', 'audioteria-wp' ) );
        }

        return $validation_errors;
    }

    public function audioteria_save_extra_register_fields( $customer_id ) {

        if ( isset( $_POST['billing_first_name'] ) ) {
            update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
            update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
        }

        if ( isset( $_POST['billing_last_name'] ) ) {
            update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
            update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
        }

        if ( isset( $_POST['billing_dob'] ) ) {
            update_user_meta( $customer_id, 'billing_dob', sanitize_text_field( $_POST['billing_dob'] ) );
        }

        if ( isset( $_POST['billing_country'] ) ) {
            update_user_meta( $customer_id, 'billing_country', sanitize_text_field( $_POST['billing_country'] ) );
        }

        if ( isset( $_POST['billing_city'] ) ) {
            update_user_meta( $customer_id, 'billing_city', sanitize_text_field( $_POST['billing_city'] ) );
        }
        if ( isset( $_POST['billing_gender'] ) ) {
            update_user_meta( $customer_id, 'billing_gender', sanitize_text_field( $_POST['billing_gender'] ) );
        }

        if ( isset( $_POST['billing_newsletter'] ) ) {
            update_user_meta( $customer_id, 'billing_newsletter', sanitize_text_field( $_POST['billing_newsletter'] ) );
        }
    }

    public function wad_registration_privacy_policy_text() {

        $text = '';
        $type = 'registration';
        switch ( $type ) {
            case 'registration':
                /* translators: %s privacy policy page name and link */
                $text = get_option( 'woocommerce_registration_privacy_policy_text', sprintf( __( 'By creating this account you agree with our privacy policy and Terms of use', 'audioteria-wp' ), '[privacy_policy]' ) );
                break;
        }

        return trim( apply_filters( 'woocommerce_get_privacy_policy_text', $text, $type ) );
    }

    public function audioteria_after_form_text() {
        ob_start();
        ?>
        <p class="consent">
            <?= sprintf('By creating this account you agree with our <a href="%s" title="privacy policy"> privacy policy</a>
            and <a href="%s" title="Term of use"> Terms of use</a>', get_permalink( get_page_by_title( 'Privacy Policy' ) ), get_permalink( get_page_by_title( 'Terms of Use' ) ) ); ?>
        </p>
        <?php
        echo ob_get_clean();
    }

    public function product_single_add_to_cart_text( $text, $product ) {
        $prod_arr = [$product->get_id()];
        $purchase_check = AbstractProducts::get_instance()->has_bought_product($prod_arr);
        if($purchase_check && $text !== 'Add to cart'){
            $text =  __( 'Download', 'audioteria-wp' );
        }elseif ( WC()->cart->find_product_in_cart( WC()->cart->generate_cart_id( get_the_ID() ) )){
            $text =  __( 'Added to bag', 'audioteria-wp' );
        }else{
            $text =  __( 'Add to bag', 'audioteria-wp' );
        }

        return $text;
    }

    public function disable_comments() {
        $post_types = get_post_types();
        foreach ( $post_types as $post_type ) {
            if ( post_type_supports( $post_type, 'comments' ) ) {
                if ( ! $post_type == 'product' ) {
                    remove_post_type_support( $post_type, 'comments' );
                    remove_post_type_support( $post_type, 'trackbacks' );
                }
            }
        }
    }

    public function audioteria_deny_purchase_if_already_purchased( $is_purchasable, $product ) {
        if ( is_user_logged_in() && wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) {
            $is_purchasable = false;
        }
        return $is_purchasable;
    }

    public function audioteria_woocommerce_auto_complete_order( $order_id ) {
        if ( ! $order_id ) {
            return;
        }

        $order = wc_get_order( $order_id );
        $order->update_status( 'completed' );
    }

    public function audioteria_set_completed_for_paid_orders( $order_id ) {

        $order = wc_get_order( $order_id );

        $order->update_status( ‘completed’ );

    }

    /**
     * Singleton poop.
     *
     * @return WooComerce
     */
    public static function get_instance() {
        static $instance = null;

        if ( is_null( $instance ) ) {
            $instance = new self();
        }

        return $instance;
    }
}
