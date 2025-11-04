<?php
/**
 * Theme setup and supports.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'after_setup_theme', function () {
    // Text domain for translations.
    load_child_theme_textdomain( 'bn-newspack-child', get_stylesheet_directory() . '/languages' );

    add_theme_support( 'editor-styles' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'post-thumbnails' );

    // Custom image sizes for mobile-first performance.
    add_image_size( 'bn-hero', 1920, 1080, true );
    add_image_size( 'bn-card', 800, 600, true );
    add_image_size( 'bn-thumb', 400, 300, true );
} );

// Enqueue theme assets.
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'bn-navigation', get_stylesheet_directory_uri() . '/assets/css/navigation.css', array(), '0.1.0' );
    wp_enqueue_style( 'bn-paywall-cta', get_stylesheet_directory_uri() . '/blocks/paywall-cta/style.css', array(), '0.1.0' );
    wp_enqueue_script( 'bn-overlay', get_stylesheet_directory_uri() . '/assets/js/overlay.js', array(), '0.1.0', true );
}, 30 );

// Load navigation components.
require_once __DIR__ . '/navigation/menus.php';

// Load block registration.
require_once __DIR__ . '/blocks.php';

// Load paywall components.
require_once __DIR__ . '/paywall/settings.php';
require_once __DIR__ . '/paywall/membership.php';
require_once __DIR__ . '/paywall/gate.php';

// Load custom post types (legacy crate theme compatibility).
require_once __DIR__ . '/post-types.php';

// Load legacy shortcodes (crate theme compatibility).
require_once __DIR__ . '/shortcodes.php';

// Load magazine functions (legacy crate theme compatibility).
require_once __DIR__ . '/magazine.php';

// Load digital edition functions (legacy crate theme compatibility).
require_once __DIR__ . '/digital-edition.php';

// Load filters and hooks (legacy crate theme compatibility).
require_once __DIR__ . '/filters.php';

// Inject overlay menu into footer on all pages.
add_action( 'wp_footer', function () {
	get_template_part( 'parts/header-overlay' );
}, 5 );


