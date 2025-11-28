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

    // Enqueue full-width layout styles for singles, pages, and search
    wp_enqueue_style(
        'bn-single-full-width',
        get_stylesheet_directory_uri() . '/assets/css/single-full-width.css',
        array( 'bn-parent-style' ),
        '1.0.1'
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

// Enqueue Adobe Fonts (Typekit) - from Crate theme
add_action( 'wp_head', function() {
    ?>
    <script>
        (function(d) {
        var config = {
            kitId: 'knt2fsi',
            scriptTimeout: 3000,
            async: true
        },
        h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='https://use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
        })(document);
    </script>
    <style type="text/css">
        .wf-loading h1,
        .wf-loading h2,
        .wf-loading h3,
        .wf-loading nav a,
        .wf-loading p {
            /* Hide text while web fonts are loading */
            visibility: hidden;
        }
    </style>
    <?php
}, 1 );

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
        // Check if featured image position is 'hidden'
        $featured_image_position = function_exists( 'newspack_featured_image_position' ) ? newspack_featured_image_position() : '';
        
        if ( 'hidden' === $featured_image_position ) {
            // If hidden, treat as if there's no featured image
            $has_hero = false;
        } elseif ( function_exists( 'newspack_post_has_hero' ) ) {
            // Prefer theme helper if available.
            $has_hero = (bool) newspack_post_has_hero( get_post() );
        } else {
            // Design assumption: featured image renders as a full-width hero on singulars.
            $has_hero = true;
        }
    }

    // Also check for full-width page template with featured image
    if ( is_page_template( 'page-full-width.php' ) && has_post_thumbnail() ) {
        // Check if featured image position is 'hidden' for pages too
        $featured_image_position = function_exists( 'newspack_featured_image_position' ) ? newspack_featured_image_position() : '';
        if ( 'hidden' !== $featured_image_position ) {
            $has_hero = true;
        }
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
 * Force all single posts (including CPTs) to use the one-column wide template,
 * UNLESS a custom page template has been explicitly assigned.
 */
add_filter( 'single_template', function( $template ) {
    // Check if we're on a single post page (any post type)
    if ( is_single() ) {
        // Check if a custom page template has been assigned
        $page_template = get_page_template_slug();
        
        // If a custom template is assigned, respect it and don't override
        if ( ! empty( $page_template ) ) {
            return $template;
        }
        
        // Only apply single-wide.php if no custom template is assigned
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
    
    // Add full-width class for search results pages
    if ( is_search() || ( ! empty( $_GET['swps'] ) ) ) {
        $classes[] = 'search-full-width';
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
 * Add meta box for featured image beside background color option
 */
add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'bn_beside_bg_color',
        __( 'Featured Image Beside - Background Color', 'bn-newspack-child' ),
        'bn_beside_bg_color_callback',
        array( 'post', 'article' ),
        'side',
        'default'
    );
} );

/**
 * Meta box callback function for beside background color
 */
function bn_beside_bg_color_callback( $post ) {
    wp_nonce_field( 'bn_beside_bg_color_nonce', 'bn_beside_bg_color_nonce' );
    
    $value = get_post_meta( $post->ID, '_bn_beside_bg_color', true );
    $value = $value ? $value : '#333333';
    ?>
    <p style="color: #666; font-style: italic; margin-bottom: 10px;">
        <?php esc_html_e( 'This option only applies when "Beside article title" is selected as the Featured Image Position.', 'bn-newspack-child' ); ?>
    </p>
    <p>
        <label for="bn_beside_bg_color">
            <?php esc_html_e( 'Background Color', 'bn-newspack-child' ); ?>
        </label>
        <input type="text" id="bn_beside_bg_color" name="bn_beside_bg_color" value="<?php echo esc_attr( $value ); ?>" class="bn-color-picker" />
    </p>
    <p style="color: #666; font-size: 12px;">
        <?php esc_html_e( 'Choose the background color for the left section containing the title and post meta.', 'bn-newspack-child' ); ?>
    </p>
    <?php
}

/**
 * Save meta box data for beside background color
 */
add_action( 'save_post', function( $post_id ) {
    // Check nonce
    if ( ! isset( $_POST['bn_beside_bg_color_nonce'] ) || ! wp_verify_nonce( $_POST['bn_beside_bg_color_nonce'], 'bn_beside_bg_color_nonce' ) ) {
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
    if ( isset( $_POST['bn_beside_bg_color'] ) ) {
        $value = sanitize_hex_color( $_POST['bn_beside_bg_color'] );
        if ( $value ) {
            update_post_meta( $post_id, '_bn_beside_bg_color', $value );
        } else {
            // Delete the meta when color is cleared
            delete_post_meta( $post_id, '_bn_beside_bg_color' );
        }
    }
} );

/**
 * Enqueue WordPress color picker for the beside background color meta box
 */
add_action( 'admin_enqueue_scripts', function( $hook ) {
    // Only load on post edit screens
    if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
        return;
    }
    
    // Check if we're editing a post or article
    global $post;
    if ( ! $post || ! in_array( $post->post_type, array( 'post', 'article' ) ) ) {
        return;
    }
    
    // Enqueue color picker
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    
    // Add inline script to initialize color picker
    wp_add_inline_script( 'wp-color-picker', '
        jQuery(document).ready(function($) {
            $(".bn-color-picker").wpColorPicker();
        });
    ' );
} );

/**
 * Output custom background color for featured image beside layout
 */
add_action( 'wp_head', function() {
    if ( ! is_singular( array( 'post', 'article' ) ) ) {
        return;
    }
    
    // Check if the featured image position function exists
    if ( ! function_exists( 'newspack_featured_image_position' ) ) {
        return;
    }
    
    // Check if the featured image position is set to "beside"
    $position = newspack_featured_image_position();
    if ( 'beside' !== $position ) {
        return;
    }
    
    $post_id = get_the_ID();
    $bg_color = get_post_meta( $post_id, '_bn_beside_bg_color', true );
    
    if ( $bg_color ) {
        ?>
        <style id="bn-beside-bg-color">
    @media (min-width: 782px) {
        .single-featured-image-beside .featured-image-beside,
        .featured-image-beside {
            background-color: <?php echo esc_attr( $bg_color ); ?> !important;
        }
    }
</style>
        <?php
    }
}, 100 );

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

//TODO: Remove this before production   
/*
add_action('init', function() {
    if (isset($_GET['clear_pw_cache']) && current_user_can('manage_options')) {
        $cleared = bn_clear_paywall_cache();
        wp_die('Cleared ' . $cleared . ' paywall cache entries');
    }
});
*/

/**
 * Map custom 'subheading' field to Newspack's subtitle
 * This allows the custom 'subheading' post meta to appear in Newspack Content Loop blocks
 */
add_filter( 'get_post_metadata', 'bn_map_subheading_to_newspack_subtitle', 10, 4 );
function bn_map_subheading_to_newspack_subtitle( $value, $object_id, $meta_key, $single ) {
    // Only filter if requesting Newspack's subtitle meta key
    if ( 'newspack_post_subtitle' === $meta_key ) {
        // Remove the filter to avoid infinite loop
        remove_filter( 'get_post_metadata', 'bn_map_subheading_to_newspack_subtitle', 10 );
        
        // Get the custom subheading value
        $subheading = get_post_meta( $object_id, 'subheading', true );
        
        // Re-add the filter
        add_filter( 'get_post_metadata', 'bn_map_subheading_to_newspack_subtitle', 10, 4 );
        
        // Return the subheading if it exists
        if ( ! empty( $subheading ) ) {
            return $single ? $subheading : array( $subheading );
        }
    }
    
    return $value;
}

/**
 * Detect SearchWP queries and treat them as search queries
 * SearchWP uses custom parameters (swps, swp_form) instead of standard ?s=
 * This prevents the homepage template from loading for search results
 */
add_action( 'template_redirect', function() {
    // Check if this is a SearchWP query (not standard WordPress search)
    if ( ! is_admin() && isset( $_GET['swps'] ) && ! empty( $_GET['swps'] ) ) {
        // Set the query var so WordPress recognizes this as a search
        global $wp_query;
        $wp_query->is_search = true;
        $wp_query->is_home = false;
        $wp_query->is_front_page = false;
    }
}, 1 ); // Priority 1 to run very early

/**
 * Make SearchWP queries work with WP_Query
 * This ensures the search.php template receives the search results
 */
add_filter( 'pre_get_posts', function( $query ) {
    // Only run on main query, not admin, and when swps parameter exists
    if ( ! is_admin() && $query->is_main_query() && isset( $_GET['swps'] ) && ! empty( $_GET['swps'] ) ) {
        // Set the search query
        $query->set( 's', sanitize_text_field( $_GET['swps'] ) );
        $query->is_search = true;
        $query->is_home = false;
    }
    return $query;
}, 1 );

/**
 * Calculate reading time for a post
 * 
 * @param int $post_id Post ID. Defaults to current post.
 * @return int Reading time in minutes
 */
function bn_get_reading_time( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $post = get_post( $post_id );
    if ( ! $post ) {
        return 0;
    }
    
    // Get the post content
    $content = $post->post_content;
    
    // Strip shortcodes and HTML tags
    $content = strip_shortcodes( $content );
    $content = wp_strip_all_tags( $content );
    
    // Count words
    $word_count = str_word_count( $content );
    
    // Calculate reading time (words / 250)
    $reading_time = ceil( $word_count / 250 );
    
    // Minimum 1 minute
    return max( 1, $reading_time );
}

/**
 * Add inline styles for reading time display
 */
function bn_reading_time_styles() {
    if ( ! is_singular( array( 'post', 'article' ) ) ) {
        return;
    }
    ?>
    <style id="bn-reading-time-styles">
        /* Reading time styles */
        .bn-reading-time {
            text-align: center;
            margin: 1rem 0 0.5rem 0;
            font-size: 0.875rem;
            color: #666;
            line-height: 1.5;
        }
        
        @media (min-width: 600px) {
            .bn-reading-time {
                text-align: center;
                margin: 0.75rem 0;
            }
        }
        
        .bn-reading-time .reading-time-text {
            font-style: italic;
            font-size: 0.875rem;
        }
        
        /* Featured image behind variation */
        .single .featured-image-behind .bn-reading-time {
            color: rgba(255, 255, 255, 0.9);
        }
        
        /* Featured image beside variation */
        @media (min-width: 782px) {
            .single .featured-image-beside .entry-subhead .bn-reading-time {
                margin: 0.5rem 0;
            }
        }
        
        /* Entry subhead context */
        .entry-subhead .bn-reading-time {
            display: block;
            width: 100%;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'bn_reading_time_styles', 100 );