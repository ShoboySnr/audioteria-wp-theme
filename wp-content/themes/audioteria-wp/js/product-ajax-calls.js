(function ($) {
    "use strict";

    var productAjax = {};

    productAjax.audioteria_user_wishlist_action = function (event) {
        event.preventDefault();

        $('.preloader').show();

        let wishlist_elem = $(this);
        const product_id = $(this).data('item-id');
        const user_id = $(this).data('user-id');
        const action = $(this).data('action');
        const nonce = $(this).parents('.others').data('nonce');
        const data = {
            user_id,
            product_id,
            action,
        }

        $.ajax({
            dataType: 'json',
            type: 'post',
            data: {
                data,
                nonce,
                action: 'audioteria_wp_user_wishlist_action'
            },
            url: audioteria_product_ajax_js.ajaxurl,
            success: function (response) {
                // console.log(response);
                if (response.status === true) {
                    if (response.data.wish_button !== '') {
                        $('#wishlist-action').replaceWith(response.data.wish_button);
                    }
                }
                $('.preloader').hide();
                setTimeout(() => { location.reload(); }, 1000);
            },
            error: function (qXhr, textStatus, errorMessage) {
                alert('An error occurred');
                $('.preloader').hide();
            }
        });
    }

    productAjax.audioteria_rate_product = function (event) {
        event.preventDefault();

        let submit_elem = $(this);

        const product_id = $(this).data('item-id');
        const user_id = $(this).data('user-id');
        const rating = $(this).parents('.rate-container').find('input#rating-value');
        const rating_value = rating.val();
        // console.log(rating_value);
        if(rating_value === null || rating_value === undefined || rating_value === 0 || rating_value === ""){
            alert('please select rating value');
        }

        const nonce = $(this).parents('.account-content-wrapper').data('nonce');

        const data = {
            user_id,
            product_id,
            rating_value,
        }

        $.ajax({
            dataType: 'json',
            type: 'post',
            data: {
                data,
                nonce,
                action: 'audioteria_wp_user_rating_action'
            },
            url: audioteria_product_ajax_js.ajaxurl,
            success: function (response) {
                console.log(response);
                if (response.status === true) {
                    $('.preloader').hide();
                    setTimeout(() => { location.reload(); }, 1000);
                }
            },
            error: function (qXhr, textStatus, errorMessage) {
                setTimeout(() => { location.reload(); }, 1000);
                $('.preloader').hide();
            }
        });
    }

    productAjax.init = function () {

        //wishlist action
        $(document).on('click', '#wishlist-action', productAjax.audioteria_user_wishlist_action);

        //rating action
        $(document).on('click', '#submit-rating', productAjax.audioteria_rate_product);

    }

    $(window).on('load', productAjax.init);

})(jQuery);
