<?php
/**
 * Plugin Name: ibericode SEO
 * Description: Custom lightweight SEO plugin to replace Yoast.
 * Version: 1.0.0
 * Author: ibericode
 * Requires PHP: 8.4
 */

namespace Ibericode\SEO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', __NAMESPACE__ . '\output_meta_tags', 1 );
add_filter( 'document_title_separator', __NAMESPACE__ . '\title_separator' );
add_filter( 'document_title_parts', __NAMESPACE__ . '\title_parts' );
add_filter( 'wp_robots', __NAMESPACE__ . '\robots_meta' );
add_action( 'template_redirect', __NAMESPACE__ . '\redirect_sitemaps', 1 );
add_action( 'init', __NAMESPACE__ . '\register_meta' );
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_editor_assets' );

function title_separator(): string {
    return '-';
}

function robots_meta( array $robots ): array {
    if ( is_singular() || is_front_page() ) {
        $robots['index'] = true;
        $robots['follow'] = true;
        $robots['max-snippet'] = '-1';
        $robots['max-image-preview'] = 'large';
        $robots['max-video-preview'] = '-1';
    } else {
        $robots['noindex'] = true;
    }
    return $robots;
}

function title_parts( array $parts ): array {
    if ( is_front_page() || is_home() ) {
        unset( $parts['tagline'] );
    }
    return $parts;
}

function redirect_sitemaps(): void {
    $req = $_SERVER['REQUEST_URI'] ?? '';
    
    if ( str_contains( $req, 'sitemap_index.xml' ) ) {
        wp_redirect( home_url( '/wp-sitemap.xml' ), 301 );
        exit;
    } elseif ( preg_match( '/post-sitemap(\d*)\.xml/', $req, $matches ) ) {
        $page = ! empty( $matches[1] ) ? $matches[1] : 1;
        wp_redirect( home_url( "/wp-sitemap-posts-post-{$page}.xml" ), 301 );
        exit;
    } elseif ( preg_match( '/page-sitemap(\d*)\.xml/', $req, $matches ) ) {
        $page = ! empty( $matches[1] ) ? $matches[1] : 1;
        wp_redirect( home_url( "/wp-sitemap-posts-page-{$page}.xml" ), 301 );
        exit;
    } elseif ( preg_match( '/category-sitemap(\d*)\.xml/', $req, $matches ) ) {
        $page = ! empty( $matches[1] ) ? $matches[1] : 1;
        wp_redirect( home_url( "/wp-sitemap-taxonomies-category-{$page}.xml" ), 301 );
        exit;
    }
}

function register_meta(): void {
    $post_types = get_post_types( [ 'public' => true ] );
    foreach ( $post_types as $post_type ) {
        register_post_meta( $post_type, '_ibericode_seo_description', [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => fn() => current_user_can( 'edit_posts' ),
        ] );
    }
}

function enqueue_editor_assets(): void {
    wp_enqueue_script(
        'ibericode-seo-editor',
        plugins_url( 'editor.js', __FILE__ ),
        [ 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data' ],
        '1.0.0',
        true
    );
}

function output_meta_tags(): void {
    if ( ! ( is_singular() || is_front_page() || is_home() ) ) {
        return;
    }

    $post_id = get_queried_object_id();
    
    // Description logic using null coalescing and modern ternary
    $desc = get_post_meta( $post_id, '_ibericode_seo_description', true )
         ?: get_post_meta( $post_id, '_yoast_wpseo_metadesc', true ) 
         ?: ( has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : '' )
         ?: ( is_front_page() ? get_bloginfo( 'description' ) : '' );
    
    // Fallback to content excerpt
    if ( ! $desc ) {
        $post = get_post( $post_id );
        if ( $post ) {
            $desc = wp_trim_words( strip_shortcodes( $post->post_content ), 30, '' );
        }
    }

    $desc = esc_attr( wp_strip_all_tags( $desc ) );

    $title     = wp_get_document_title();
    $url       = is_front_page() ? home_url( '/' ) : get_permalink( $post_id );
    $site_name = get_bloginfo( 'name' );

    echo "<meta name=\"description\" content=\"{$desc}\" />\n";

    $locale = get_locale();
    $type   = is_front_page() ? 'website' : 'article';
    
    echo "<meta property=\"og:locale\" content=\"" . esc_attr( $locale ) . "\" />\n";
    echo "<meta property=\"og:type\" content=\"{$type}\" />\n";
    echo "<meta property=\"og:title\" content=\"" . esc_attr( $title ) . "\" />\n";
    echo "<meta property=\"og:description\" content=\"{$desc}\" />\n";
    echo "<meta property=\"og:url\" content=\"" . esc_url( $url ) . "\" />\n";
    echo "<meta property=\"og:site_name\" content=\"" . esc_attr( $site_name ) . "\" />\n";

    if ( is_singular() && ! is_front_page() ) {
        $published_time = get_the_date( 'c', $post_id );
        $modified_time  = get_the_modified_date( 'c', $post_id );
        
        echo "<meta property=\"article:published_time\" content=\"" . esc_attr( $published_time ) . "\" />\n";
        echo "<meta property=\"article:modified_time\" content=\"" . esc_attr( $modified_time ) . "\" />\n";
        
        $author_id   = get_post_field( 'post_author', $post_id );
        $author_name = get_the_author_meta( 'display_name', $author_id );
        
        echo "<meta name=\"author\" content=\"" . esc_attr( $author_name ) . "\" />\n";
    }

    echo "<meta name=\"twitter:card\" content=\"summary_large_image\" />\n";
    echo "<meta name=\"twitter:site\" content=\"@dannyvankooten\" />\n";
    if ( is_singular() && ! is_front_page() ) {
        echo "<meta name=\"twitter:creator\" content=\"@dannyvankooten\" />\n";
    }

    // Determine the OG image using PHP 8 match expression
    $image = match ( true ) {
        has_post_thumbnail( $post_id )                => get_the_post_thumbnail_url( $post_id, 'large' ),
        function_exists( '\dvk26_default_og_image_url' ) => \dvk26_default_og_image_url(),
        (bool) get_theme_mod( 'custom_logo' )         => wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ),
        default                                       => '',
    };

    if ( $image ) {
        echo "<meta property=\"og:image\" content=\"" . esc_url( $image ) . "\" />\n";
        echo "<meta name=\"twitter:image\" content=\"" . esc_url( $image ) . "\" />\n";
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@graph'   => [],
    ];

    if ( is_singular() && ! is_front_page() && 'post' === get_post_type( $post_id ) ) {
        $schema['@graph'][] = [
            '@type'            => 'Article',
            '@id'              => $url . '#article',
            'isPartOf'         => [
                '@id' => $url . '#webpage',
            ],
            'author'           => [
                '@type' => 'Person',
                'name'  => $author_name ?? get_bloginfo( 'name' ),
            ],
            'headline'         => $title,
            'datePublished'    => $published_time ?? get_the_date( 'c', $post_id ),
            'dateModified'     => $modified_time ?? get_the_modified_date( 'c', $post_id ),
            'mainEntityOfPage' => [
                '@id' => $url . '#webpage',
            ],
            'publisher'        => [
                '@id' => home_url( '/#organization' ),
            ],
        ];
    }

    $schema['@graph'][] = [
        '@type'       => 'WebPage',
        '@id'         => $url . '#webpage',
        'url'         => $url,
        'name'        => $title,
        'isPartOf'    => [
            '@id' => home_url( '/#website' ),
        ],
        'description' => $desc,
    ];

    $schema['@graph'][] = [
        '@type'       => 'WebSite',
        '@id'         => home_url( '/#website' ),
        'url'         => home_url( '/' ),
        'name'        => $site_name,
        'description' => get_bloginfo( 'description' ),
        'publisher'   => [
            '@id' => home_url( '/#organization' ),
        ],
    ];

    echo '<script type="application/ld+json" class="ibericode-seo-schema">' . wp_json_encode( $schema ) . "</script>\n";
}
