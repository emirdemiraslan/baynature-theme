<?php
/**
 * Block registration.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue block editor assets.
 */
add_action( 'enqueue_block_editor_assets', function () {
    $asset_file = get_stylesheet_directory() . '/build/index.asset.php';
    
    if ( ! file_exists( $asset_file ) ) {
        return;
    }
    
    $asset = include $asset_file;
    
    wp_enqueue_script(
        'bn-blocks-editor',
        get_stylesheet_directory_uri() . '/build/index.js',
        $asset['dependencies'],
        $asset['version'],
        true
    );
} );

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
        'mec-events-list',
    );

    foreach ( $blocks as $block ) {
        $block_path = $blocks_dir . '/' . $block;
        if ( file_exists( $block_path . '/block.json' ) ) {
            register_block_type( $block_path );
        }
    }
} );

