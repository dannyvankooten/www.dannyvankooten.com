<?php
/**
 * Plugin Name: Disable Comments and Pings
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	foreach ( get_post_types() as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
} );

add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 20 );

add_filter( 'pre_option_default_comment_status', fn() => 'closed' );
add_filter( 'pre_option_default_ping_status', fn() => 'closed' );