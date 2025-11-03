<?php
/**
 * Block registration.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register all custom blocks.
 */
add_action( 'init', function () {
    $blocks_dir = get_stylesheet_directory() . '/blocks';
    if ( ! is_dir( $blocks_dir ) ) {
        return;
    }

    $blocks = array(
        'hero-header',
        'featured-issue',
        'latest-news-rail',
        'newsletter-signup',
        'events-teaser',
        'featured-trail',
        'paywall-cta',
        'author-box',
    );

    foreach ( $blocks as $block ) {
        $block_path = $blocks_dir . '/' . $block;
        if ( file_exists( $block_path . '/block.json' ) ) {
            register_block_type( $block_path );
        }
    }
} );

