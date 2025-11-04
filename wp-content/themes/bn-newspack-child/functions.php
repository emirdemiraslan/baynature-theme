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
    
    // Enqueue Sacha-inspired styles
    wp_enqueue_style( 
        'bn-sacha-styles', 
        get_stylesheet_directory_uri() . '/assets/css/sacha-styles.css', 
        array( 'bn-parent-style' ), 
        '1.0.0' 
    );
}, 20 );

// Load theme setup and feature wiring.
require_once __DIR__ . '/inc/setup.php';

/**
 * Sacha-style theme customizations
 */

// Add Sacha-style setup actions
add_action( 'after_setup_theme', 'bn_sacha_setup', 12 );
function bn_sacha_setup() {
    // Remove the default editor styles
    remove_editor_styles();
    // Add our Sacha-inspired editor styles
    add_editor_style( 'assets/css/sacha-styles.css' );
}

// Load Sacha-style customization functions
if ( file_exists( __DIR__ . '/inc/sacha/child-color-patterns.php' ) ) {
    require_once __DIR__ . '/inc/sacha/child-color-patterns.php';
    require_once __DIR__ . '/inc/sacha/child-typography.php';
    
    /**
     * Display custom color CSS in customizer and on frontend.
     */
    function bn_sacha_custom_colors_css_wrap() {
        // Only bother if we haven't customized the color.
        if ( ( ! is_customize_preview() && 'default' === get_theme_mod( 'theme_colors', 'default' ) ) || is_admin() ) {
            return;
        }
        ?>
        <style type="text/css" id="custom-theme-colors-bn-sacha">
            <?php echo bn_sacha_custom_colors_css(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </style>
        <?php
    }
    add_action( 'wp_head', 'bn_sacha_custom_colors_css_wrap' );
    
    /**
     * Display custom font CSS in customizer and on frontend.
     */
    function bn_sacha_typography_css_wrap() {
        if ( is_admin() || ( ! get_theme_mod( 'font_body', '' ) && ! get_theme_mod( 'font_header', '' ) && ! get_theme_mod( 'accent_allcaps', true ) ) ) {
            return;
        }
        ?>
        <style type="text/css" id="custom-theme-fonts-bn-sacha">
            <?php echo bn_sacha_custom_typography_css(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </style>
        <?php
    }
    add_action( 'wp_head', 'bn_sacha_typography_css_wrap' );
    
    /**
     * Enqueue supplemental block editor styles.
     */
    function bn_sacha_editor_customizer_styles() {
        // Check for color or font customizations.
        $theme_customizations = '';
        
        if ( 'custom' === get_theme_mod( 'theme_colors' ) ) {
            // Include color patterns.
            $theme_customizations .= bn_sacha_custom_colors_css();
        }
        
        if ( get_theme_mod( 'font_body', '' ) || get_theme_mod( 'font_header', '' ) || get_theme_mod( 'accent_allcaps', true ) ) {
            $theme_customizations .= bn_sacha_custom_typography_css();
        }
        
        // If there are any, add those styles inline.
        if ( $theme_customizations ) {
            // Enqueue a non-existant file to hook our inline styles to:
            wp_register_style( 'bn-sacha-editor-inline-styles', false );
            wp_enqueue_style( 'bn-sacha-editor-inline-styles' );
            // Add inline styles:
            wp_add_inline_style( 'bn-sacha-editor-inline-styles', $theme_customizations );
        }
    }
    add_action( 'enqueue_block_editor_assets', 'bn_sacha_editor_customizer_styles' );
}



