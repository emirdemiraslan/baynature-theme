<?php
/**
 * Menu registration and navigation helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register menu locations.
 */
add_action( 'after_setup_theme', function () {
    register_nav_menus( array(
        'primary'        => __( 'Primary Menu', 'bn-newspack-child' ),
        'header-utility' => __( 'Header Utility (Join/Donate)', 'bn-newspack-child' ),
        'popup'          => __( 'Popup Overlay Menu', 'bn-newspack-child' ),
        'topics'         => __( 'Topics Row', 'bn-newspack-child' ),
    ) );
} );

/**
 * Get Join and Donate URLs from settings or menu fallback.
 */
function bn_get_utility_urls() {
    $opts = get_option( 'bn_navigation_options', array() );
    return wp_parse_args( $opts, array(
        'join_url'   => '/join',
        'donate_url' => '/donate',
    ) );
}

/**
 * Add settings for Join/Donate URLs.
 */
add_action( 'admin_init', function () {
    register_setting( 'bn_navigation', 'bn_navigation_options', array(
        'type'    => 'array',
        'default' => array(
            'join_url'   => '/join',
            'donate_url' => '/donate',
        ),
    ) );

    add_settings_section( 'bn_nav_urls', __( 'Utility Button URLs', 'bn-newspack-child' ), '__return_false', 'bn_navigation' );

    add_settings_field( 'join_url', __( 'Join URL', 'bn-newspack-child' ), function () {
        $opts = get_option( 'bn_navigation_options', array() );
        $val  = isset( $opts['join_url'] ) ? $opts['join_url'] : '/join';
        echo '<input type="text" name="bn_navigation_options[join_url]" value="' . esc_attr( $val ) . '" class="regular-text" />';
    }, 'bn_navigation', 'bn_nav_urls' );

    add_settings_field( 'donate_url', __( 'Donate URL', 'bn-newspack-child' ), function () {
        $opts = get_option( 'bn_navigation_options', array() );
        $val  = isset( $opts['donate_url'] ) ? $opts['donate_url'] : '/donate';
        echo '<input type="text" name="bn_navigation_options[donate_url]" value="' . esc_attr( $val ) . '" class="regular-text" />';
    }, 'bn_navigation', 'bn_nav_urls' );
} );

/**
 * Add Navigation tab to Bay Nature settings page.
 */
add_action( 'admin_menu', function () {
    // Piggyback on the existing bn-settings page added by paywall/settings.php.
    // We'll enhance the page to show tabs in the next commit.
}, 99 );

/**
 * Add bn-header-menu-link class to header-utility menu links.
 */
add_filter( 'nav_menu_link_attributes', function ( $atts, $item, $args ) {
    if ( isset( $args->theme_location ) && 'header-utility' === $args->theme_location ) {
        $classes = isset( $atts['class'] ) ? $atts['class'] : '';
        $classes = $classes ? $classes . ' bn-header-menu-link' : 'bn-header-menu-link';
        $atts['class'] = $classes;
    }
    return $atts;
}, 10, 3 );

/**
 * Add bn-header-menu-item class to header-utility menu list items.
 */
add_filter( 'nav_menu_css_class', function ( $classes, $item, $args ) {
    if ( isset( $args->theme_location ) && 'header-utility' === $args->theme_location ) {
        $classes[] = 'bn-header-menu-item';
    }
    return $classes;
}, 10, 3 );

