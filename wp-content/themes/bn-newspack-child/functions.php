<?php
/**
 * Bay Nature (Newspack Child) bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Enqueue parent stylesheet after parent has registered it.
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'bn-parent-style', get_template_directory_uri() . '/style.css', array(), null );
}, 20 );

// Load theme setup and feature wiring.
require_once __DIR__ . '/inc/setup.php';



