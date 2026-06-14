<?php

// Prevent direct file access
defined('ABSPATH') or exit;

// Disable XMLRPC
add_filter('xmlrpc_enabled', '__return_false');

// Do not allow access to WordPress REST API for non-logged-in users
add_filter('rest_authentication_errors', static function ($result) {
    if (is_wp_error($result)) {
        return $result;
    }

    $request_path = (string) parse_url(urldecode($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
    $query_param = $_GET['rest_route'] ?? '';

    if (! is_user_logged_in() && (str_contains($request_path, '/wp-json/wp/v2/users') || $query_param === '/wp/v2/users')) {
        return new WP_Error(
            'rest_not_logged_in',
            'You are not currently logged in.',
            ['status' => 401]
        );
    }

    return $result;
});

// Prevent user enumeration via ?author=1 
add_action('init', static function() {
    if (isset($_GET['author'])) {
        unset($_GET['author']);
        unset($_REQUEST['author']);
    }
}, 1);

// Reject all login requests submitted within 2 seconds of loading the page
add_action('login_footer', static function () {
    ?><style>
        #wp-submit {
            background-color: #2271b1 !important;
            background-image: linear-gradient(to right, rgba(255, 255, 255, 0.28), rgba(255, 255, 255, 0.18)) !important;
            background-repeat: no-repeat !important;
            background-size: 0% 100% !important;
            border-color: #2271b1 !important;
            color: #fff !important;
        }

        #wp-submit.waiting-animate {
            background-size: 100% 100% !important;
            transition: background-size 2.5s linear !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let button = document.querySelector('#wp-submit');
            if (!button) {
                return;
            }
            requestAnimationFrame(() => {
                button.classList.add('waiting-animate');
                button.disabled = true;
            });

            window.setTimeout(() => {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'login-ok';
                input.value = '1';
                document.querySelector('#loginform').prepend(input);
                button.classList.remove('waiting-animate');
                button.disabled = false;
            }, 2500);
        })
        
    </script><?php
});

add_action('wp_authenticate', static function (&$username, &$password) {
    if (! isset($_POST['log'])) {
        return;
    }

    $js_timeout_check = ($_POST['login-ok'] ?? '') === '1';
    $csrf_check = parse_url($_SERVER['HTTP_ORIGIN'] ?? '', PHP_URL_HOST) === parse_url(home_url(), PHP_URL_HOST);

    if (!$js_timeout_check || ! $csrf_check) {
        wp_die("Sorry, we are unable to process your login request as we think you are a bot. Please try again if you're not.");
    }
}, 10, 2);