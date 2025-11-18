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

    // Enqueue Homepage Hero stylesheet and script when template is used or on front page
    if ( is_page_template( 'template-home-hero.php' ) || is_front_page() ) {
        wp_enqueue_style(
            'bn-home-hero',
            get_stylesheet_directory_uri() . '/assets/css/home-hero.css',
            array( 'bn-parent-style' ),
            '1.0.0'
        );

        wp_enqueue_script(
            'bn-home-hero',
            get_stylesheet_directory_uri() . '/assets/js/home-hero.js',
            array(),
            '1.0.0',
            true
        );
    }
}, 20 );

// Load theme setup and feature wiring.
require_once __DIR__ . '/inc/setup.php';

// Load site options functionality
require_once __DIR__ . '/inc/site-options.php';

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



// Register ACF local fields for Homepage with Hero Issue template
add_action( 'acf/init', function () {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key'   => 'group_bn_homepage_hero_issue',
        'title' => 'Homepage Hero Issue',
        'fields' => array(
            array(
                'key' => 'field_bn_hero_issue_post',
                'label' => 'Issue Post',
                'name' => 'hero_issue_post',
                'type' => 'post_object',
                'post_type' => array( 'post', 'article' ),
                'return_format' => 'id',
                'ui' => 1,
            ),
            array(
                'key' => 'field_bn_hero_content_layout',
                'label' => 'Content Layout',
                'name' => 'hero_content_layout',
                'type' => 'select',
                'choices' => array(
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right',
                ),
                'default_value' => 'left',
                'ui' => 1,
            ),
            array(
                'key' => 'field_bn_hero_overlay_color',
                'label' => 'Overlay Color',
                'name' => 'hero_overlay_color',
                'type' => 'color_picker',
                'default_value' => '#000000',
            ),
            array(
                'key' => 'field_bn_hero_overlay_opacity',
                'label' => 'Overlay Opacity (%)',
                'name' => 'hero_overlay_opacity',
                'type' => 'number',
                'default_value' => 55,
                'min' => 0,
                'max' => 100,
            ),
            array(
                'key' => 'field_bn_hero_show_excerpt',
                'label' => 'Show Excerpt',
                'name' => 'hero_show_excerpt',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 1,
            ),
            array(
                'key' => 'field_bn_hero_custom_excerpt',
                'label' => 'Custom Excerpt',
                'name' => 'hero_custom_excerpt',
                'type' => 'textarea',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_bn_hero_show_excerpt',
                            'operator' => '==',
                            'value' => '1',
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_bn_hero_show_author_date',
                'label' => 'Show Author & Date',
                'name' => 'hero_show_author_date',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'template-home-hero.php',
                ),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ) );
} );

// Add body class to signal hero-issue template being used (front page shim or direct template)
add_filter( 'body_class', function ( $classes ) {
    if ( is_front_page() || is_page_template( 'template-home-hero.php' ) ) {
        $classes[] = 'has-hero-issue';
    }
    return $classes;
} );

// Add body class for pages/singulars that render a full-width hero via featured image.
add_filter( 'body_class', function ( $classes ) {
    $has_hero = false;

    if ( is_singular() && has_post_thumbnail() ) {
        if ( function_exists( 'newspack_post_has_hero' ) ) {
            // Prefer theme helper if available.
            $has_hero = (bool) newspack_post_has_hero( get_post() );
        } else {
            // Design assumption: featured image renders as a full-width hero on singulars.
            $has_hero = true;
        }
    }

    // Also check for full-width page template with featured image
    if ( is_page_template( 'page-full-width.php' ) && has_post_thumbnail() ) {
        $has_hero = true;
    }

    if ( $has_hero ) {
        $classes[] = 'has-hero-header';
    }

	// The Magazine Issue template never renders a hero, so ensure transparent header styles stay disabled.
	if ( is_page_template( 'current_issue_template.php' ) ) {
		$classes = array_diff( $classes, array( 'has-hero-header' ) );
	}

    return $classes;
} );

// Ensure headers render across classic and block templates by injecting after body open.
add_action( 'wp_body_open', function () {
    // Output once and as early as possible after <body> open for all templates.
    get_template_part( 'parts/header' );
}, 5 );

/**
 * Force all single posts (including CPTs) to use the one-column wide template.
 */
add_filter( 'single_template', function( $template ) {
    // Check if we're on a single post page (any post type)
    if ( is_single() ) {
        // Locate the single-wide.php template in parent theme
        $one_column_template = locate_template( 'single-wide.php' );
        
        // If found, use it; otherwise fall back to default
        if ( $one_column_template ) {
            return $one_column_template;
        }
    }
    
    return $template;
}, 99 );

/**
 * Add body class for one-column template so Newspack's CSS applies correctly.
 */
add_filter( 'body_class', function( $classes ) {
    if ( is_single() ) {
        // Add the class that Newspack uses for one-column layout
        $classes[] = 'post-template-single-wide';
        // Remove any sidebar-related classes
        $classes = array_diff( $classes, array( 'has-sidebar' ) );
    }
    return $classes;
} );

/**
 * Hide comments from frontend visitors while keeping admin functionality
 */
// Close comments on the frontend (prevent new comments from being submitted)
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );

// Hide existing comments from display
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

// Hide comment counts in post meta and listings
add_filter( 'get_comments_number', '__return_zero', 10, 2 );

// Prevent comment form from displaying
add_filter( 'comment_form_defaults', function( $defaults ) {
    if ( ! is_admin() ) {
        $defaults['title_reply'] = '';
        $defaults['comment_field'] = '';
        $defaults['submit_button'] = '';
    }
    return $defaults;
}, 20 );

/**
 * Add meta box for full-width page title position option
 */
add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'bn_title_position',
        __( 'Title Position', 'bn-newspack-child' ),
        'bn_title_position_callback',
        'page',
        'side',
        'default'
    );
} );

/**
 * Meta box callback function
 */
function bn_title_position_callback( $post ) {
    wp_nonce_field( 'bn_title_position_nonce', 'bn_title_position_nonce' );
    
    $value = get_post_meta( $post->ID, '_bn_title_position', true );
    $value = $value ? $value : 'overlay';
    
    $is_full_width = get_page_template_slug( $post->ID ) === 'page-full-width.php';
    
    if ( ! $is_full_width ) {
        echo '<p style="color: #666; font-style: italic;">' . esc_html__( 'This option only applies to pages using the "Full Width" template.', 'bn-newspack-child' ) . '</p>';
    }
    ?>
    <p>
        <label>
            <input type="radio" name="bn_title_position" value="overlay" <?php checked( $value, 'overlay' ); ?> />
            <?php esc_html_e( 'On Hero Image (Overlay)', 'bn-newspack-child' ); ?>
        </label>
    </p>
    <p>
        <label>
            <input type="radio" name="bn_title_position" value="below" <?php checked( $value, 'below' ); ?> />
            <?php esc_html_e( 'Below Hero Image', 'bn-newspack-child' ); ?>
        </label>
    </p>
    <p style="color: #666; font-size: 12px;">
        <?php esc_html_e( 'Choose where to display the page title when a featured image is set.', 'bn-newspack-child' ); ?>
    </p>
    <?php
}

/**
 * Save meta box data
 */
add_action( 'save_post', function( $post_id ) {
    // Check nonce
    if ( ! isset( $_POST['bn_title_position_nonce'] ) || ! wp_verify_nonce( $_POST['bn_title_position_nonce'], 'bn_title_position_nonce' ) ) {
        return;
    }
    
    // Check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Check permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    // Save the data
    if ( isset( $_POST['bn_title_position'] ) ) {
        $value = sanitize_text_field( $_POST['bn_title_position'] );
        update_post_meta( $post_id, '_bn_title_position', $value );
    }
} );

/**
 * Add 'article' post type support for Newspack Featured Image Position options.
 * This enables the "Featured Image Position" sidebar panel in the block editor
 * for article posts, with options like Default, Behind, Beside, etc.
 */
add_filter( 'newspack_theme_featured_image_post_types', function( $post_types ) {
    $post_types[] = 'article';
    return $post_types;
} );

/**
 * Enable post templates for article post type.
 * This makes the "Template" dropdown available in the editor sidebar,
 * allowing articles to use different page templates like posts can.
 */
add_action( 'init', function() {
    add_post_type_support( 'article', 'page-templates' );
}, 20 );

