<?php

/**
 * Plugin Name: Audioteria JWT Auth
 * Plugin URI:  https://github.com/usefulteam/jwt-auth
 * Description: WordPress JWT Authentication for Audioteria.
 * Version:     1.0.0
 * Author:      Studio14 WordPress Devs
 * Author URI:  https://studio14online.co.uk
 * Text Domain: audioteria-jwt-auth
 *
 */

add_filter('jwt_auth_whitelist', function ($endpoints) {
    $whitelisted_endpoints = apply_filters('audioteria_wp_modify_auth_whitelist', [
        '/wp-json/audioteria-wp/v1/token',
        '/wp-json/audioteria-wp/v1/home',
        '/wp-json/audioteria-wp/v1/search',
        '/wp-json/audioteria-wp/v1/products/*',
        '/wp-json/audioteria-wp/v1/token/refresh',
        '/wp-json/audioteria-wp/v1/login',
        '/wp-json/audioteria-wp/v1/forget-password',
	    '/wp-json/audioteria-wp/v1/pages/*',

    ]);
    
    return array_unique(array_merge($endpoints, $whitelisted_endpoints));
});