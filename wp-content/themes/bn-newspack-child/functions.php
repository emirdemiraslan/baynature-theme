<?php
/**
 * Bay Nature (Newspack Child) bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Allow core/query block and its inner FSE blocks to bypass Newspack's removal script.
// Newspack parent theme actively unregisters FSE blocks via JS unless explicitly allowed here.
if ( ! defined( 'NEWSPACK_FSE_BLOCKS_ALLOWED' ) ) {
    define( 'NEWSPACK_FSE_BLOCKS_ALLOWED', array( 
        'core/query', 
        'core/post-featured-image', 
        'core/post-excerpt', 
        'core/post-content', 
        'core/post-date', 
        'core/post-terms', 
        'core/post-author', 
        'core/post-author-name',
        'core/query-title',
        'core/read-more'
    ) );
}

/**
 * Disable SearchWP's built-in Results Page template and use theme's search.php.
 * 
 * SearchWP adds a filter at PHP_INT_MAX that echoes HTML directly.
 * We must remove it on 'template_redirect' (before template_include runs).
 */
add_action( 'template_redirect', function() {
    if ( ! isset( $_GET['swps'] ) ) {
        return;
    }
    
    global $wp_filter;
    
    // Safety check
    if ( ! isset( $wp_filter['template_include'] ) ) {
        return;
    }
    
    // Get the WP_Hook object's callbacks
    $hook = $wp_filter['template_include'];
    
    // Remove all filters at very high priorities that could be SearchWP
    // SearchWP uses PHP_INT_MAX which is 9223372036854775807 on 64-bit systems
    foreach ( $hook->callbacks as $priority => $callbacks ) {
        // Target very high priority filters (above 1000000)
        if ( $priority > 1000000 ) {
            foreach ( $callbacks as $key => $callback ) {
                // Look for SearchWP's Frontend::render
                if ( is_array( $callback['function'] ) && count( $callback['function'] ) >= 2 ) {
                    $class = $callback['function'][0];
                    $method = $callback['function'][1];
                    
                    $class_str = is_object( $class ) ? get_class( $class ) : (string) $class;
                    
                    if ( strpos( $class_str, 'SearchWP' ) !== false && $method === 'render' ) {
                        remove_filter( 'template_include', $callback['function'], $priority );
                    }
                }
            }
        }
    }
}, 5 );

/**
 * Force search.php template for SearchWP queries.
 */
add_filter( 'template_include', function( $template ) {
    if ( ! isset( $_GET['swps'] ) ) {
        return $template;
    }
    
    $search_template = locate_template( 'search.php' );
    if ( $search_template ) {
        return $search_template;
    }
    
    return $template;
}, 999999 );

/**
 * Dequeue SearchWP's results page CSS since we use our own template.
 */
add_action( 'wp_enqueue_scripts', function() {
    if ( isset( $_GET['swps'] ) ) {
        wp_dequeue_style( 'searchwp-results-page' );
    }
}, 20 );

/**
 * Add 'search' body class for SearchWP queries so our CSS applies.
 */
add_filter( 'body_class', function( $classes ) {
    if ( isset( $_GET['swps'] ) ) {
        // Add search class
        $classes[] = 'search';
        $classes[] = 'search-results';
        
        // Remove classes that might conflict with search display
        $remove = array( 
            'home', 
            'newspack-front-page', 
            'page-template-template-home-hero',
            'page-template-template-home-hero-php',
            'has-hero-issue',
            'hide-page-title',
        );
        $classes = array_diff( $classes, $remove );
    }
    return $classes;
} );

/**
 * Restrict REST API to authenticated users only.
 * This keeps REST available for logged-in users (admin, editors, etc.)
 * while blocking public access to all endpoints.
 */
add_filter( 'rest_authentication_errors', function( $result ) {
    // Respect any existing authentication errors.
    if ( ! empty( $result ) ) {
        return $result;
    }

    // Allow REST API access for logged-in users (and in admin context).
    if ( is_user_logged_in() || is_admin() ) {
        return $result;
    }

    // Block unauthenticated requests.
    return new WP_Error(
        'rest_forbidden',
        __( 'REST API restricted to authenticated users.', 'bn-newspack-child' ),
        array( 'status' => 401 )
    );
} );

/**
 * Force featured image to be hidden for "No Banner" paywall template.
 * Overrides the per-post featured image position meta to 'hidden'.
 */
add_action( 'wp', function() {
    if ( is_singular() ) {
        $template = get_page_template_slug();
        if ( 'member_only_content_no_banner_template.php' === $template ) {
            // Override the post meta filter to return 'hidden' for featured image position
            add_filter( 'get_post_metadata', function( $value, $object_id, $meta_key, $single ) {
                if ( 'newspack_featured_image_position' === $meta_key && get_the_ID() === $object_id ) {
                    return $single ? 'hidden' : array( 'hidden' );
                }
                return $value;
            }, 10, 4 );
        }
    }
} );

// Register widget area for the "With Sidebar" template.
add_action( 'widgets_init', function () {
    register_sidebar( array(
        'name'          => __( 'With Sidebar — Right', 'bn-newspack-child' ),
        'id'            => 'sidebar-with-sidebar',
        'description'   => __( 'Widgets displayed on the right side of the "With Sidebar" template.', 'bn-newspack-child' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
} );

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

    // Enqueue With Sidebar layout styles when template is active
    if ( is_page_template( 'single-with-sidebar.php' ) ) {
        wp_enqueue_style(
            'bn-with-sidebar',
            get_stylesheet_directory_uri() . '/assets/css/with-sidebar.css',
            array( 'bn-parent-style' ),
            '1.0.0'
        );
    }
}, 20 );

// Enqueue Adobe Fonts (Typekit) - from Crate theme
add_action( 'wp_head', function() {
    ?>
    <script>
        (function(d) {
        var config = {
            /*kitId: 'knt2fsi',*/
            kitId: 'hiz3obw',
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
    add_theme_support( 'block-templates' );
    add_editor_style( 'https://use.typekit.net/hiz3obw.css' );
    add_editor_style( 'assets/css/sacha-styles.css' );
    add_editor_style( 'assets/css/editor-overrides.css' );
}

// Show font family selector by default in the block editor Typography panel.
add_filter( 'register_block_type_args', function( $args, $name ) {
    if ( isset( $args['supports']['typography'] ) ) {
        if ( ! isset( $args['supports']['typography']['__experimentalDefaultControls'] ) ) {
            $args['supports']['typography']['__experimentalDefaultControls'] = array();
        }
        $args['supports']['typography']['__experimentalDefaultControls']['fontFamily'] = true;
    }
    return $args;
}, 10, 2 );

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

    // Add has-hero-header class for category archives with a category image
    if ( is_category() ) {
        $term = get_queried_object();
        if ( $term ) {
            $image_field = get_field( 'category_image', 'category_' . $term->term_id );
            if ( ! $image_field ) {
                $image_field = get_field( 'category_image', $term );
            }
            
            if ( $image_field ) {
                $classes[] = 'has-hero-header';
            }
        }
    }

    return $classes;
} );

// Ensure headers render across classic and block templates by injecting after body open.
add_action( 'wp_body_open', function () {
    // Output once and as early as possible after <body> open for all templates.
    get_template_part( 'parts/header' );
}, 5 );

/**
 * Resolve the single-post template.
 *
 * Priority: per-post assignment > Customizer default > single-feature.php (One Column).
 * Biodiversity keeps its own template (single-biodiversity.php).
 */
add_filter( 'single_template', function( $template ) {
	if ( ! is_single() || 'biodiversity' === get_post_type() ) {
		return $template;
	}

	$per_post = get_page_template_slug();
	if ( ! empty( $per_post ) ) {
		return $template;
	}

	$customizer_default = get_theme_mod( 'post_template_default', '' );
	if ( ! empty( $customizer_default ) && 'default' !== $customizer_default ) {
		$resolved = locate_template( $customizer_default );
		if ( $resolved ) {
			return $resolved;
		}
	}

	$fallback = locate_template( 'single-feature.php' );
	return $fallback ? $fallback : $template;
}, 99 );

/**
 * Add the correct body class for whichever single-post template is active.
 */
add_filter( 'body_class', function( $classes ) {
	if ( is_single() && 'biodiversity' !== get_post_type() ) {
		$slug = get_page_template_slug();

		if ( empty( $slug ) ) {
			$customizer_default = get_theme_mod( 'post_template_default', '' );
			$slug = ( ! empty( $customizer_default ) && 'default' !== $customizer_default )
				? $customizer_default
				: 'single-feature.php';
		}

		$class = 'post-template-' . sanitize_html_class( basename( $slug, '.php' ) );
		$classes[] = $class;

		// Member-only templates use single-feature layout; add the body class so
		// Newspack's built-in alignwide / alignfull breakout rules apply.
		$member_only_layout_templates = array(
			'member_only_content_default_template.php',
			'member_only_content_no_banner_template.php',
		);
		if ( in_array( $slug, $member_only_layout_templates, true ) ) {
			$classes[] = 'post-template-single-feature';
		}

		$classes = array_diff( $classes, array( 'has-sidebar' ) );
	}
    
    // Add full-width class for search results pages
    if ( is_search() || ( ! empty( $_GET['swps'] ) ) ) {
        $classes[] = 'search-full-width';
        // Remove any sidebar-related classes
        $classes = array_diff( $classes, array( 'has-sidebar' ) );
    }

    // Force one-column layout on Pages when the sidebar is empty and using default template
    if ( is_page() && ! is_active_sidebar( 'sidebar-1' ) && ! get_page_template_slug() ) {
        $classes[] = 'post-template-single-wide';
        $classes = array_diff( $classes, array( 'has-sidebar' ) );
    }
    
    // Force one-column layout on 404 pages
    if ( is_404() ) {
        $classes[] = 'post-template-single-wide';
        $classes[] = 'error404-one-column';
        $classes = array_diff( $classes, array( 'has-sidebar', 'has-hero-header' ) );
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
 * Output custom navigation background color for pre-scroll state
 */
add_action( 'wp_head', function() {
    if ( ! is_singular( array( 'post', 'article', 'page' ) ) ) {
        return;
    }
    
    // Check for the navigation background ACF field
    $nav_bg = get_field( 'navigation_background' );
    
    if ( $nav_bg ) {
        ?>
        <style id="bn-navigation-background">
            .bn-header-bar-pre-scroll {
                background-color: <?php echo esc_attr( $nav_bg ); ?> !important;
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

/**
 * Include 'article' post type in category and tag archives
 */
add_action( 'pre_get_posts', function( $query ) {
    // Only modify the main query on the frontend
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    // Include 'article' post type in category, tag, and date archives
    if ( $query->is_category() || $query->is_tag() || $query->is_date() || $query->is_author() ) {
        $query->set( 'post_type', array( 'post', 'article' ) );
    }
} );

/**
 * Get readable issue name from issue key.
 * Format: v{year}n{season} -> {Season} Issue 20{year}
 * Example: v25n4 -> Fall Issue 2025
 *
 * @param string $issue_key The issue key code (e.g. v25n4)
 * @return string|null The formatted issue name or null if invalid format.
 */
function bn_get_issue_name( $issue_key ) {
    if ( ! $issue_key ) {
        return null;
    }

    // Pattern: v{YY}n{S}
    if ( preg_match( '/^v(\d{2})n(\d)$/', $issue_key, $matches ) ) {
        $year_short = $matches[1];
        $season_num = $matches[2];
        $year = '20' . $year_short;

        $seasons = array(
            '1' => 'Winter',
            '2' => 'Spring',
            '3' => 'Summer',
            '4' => 'Fall',
        );

        if ( isset( $seasons[ $season_num ] ) ) {
            return sprintf( '%s Issue %s', $seasons[ $season_num ], $year );
        }
    }

    return null;
}

/**
 * Get issue slug from issue key for URLs.
 * Format: v{year}n{season} -> {season}20{year}
 * Example: v25n4 -> fall2025
 *
 * @param string $issue_key The issue key code (e.g. v25n4)
 * @return string The formatted issue slug or 'archive' if invalid/missing.
 */
function bn_get_issue_slug( $issue_key ) {
	if ( ! $issue_key ) {
		return 'archive';
	}

	if ( preg_match( '/^v(\d{2})n(\d)$/', $issue_key, $matches ) ) {
		$year_short = $matches[1];
		$season_num = $matches[2];
		$year       = '20' . $year_short;

		$seasons = array(
			'1' => 'winter',
			'2' => 'spring',
			'3' => 'summer',
			'4' => 'fall',
		);

		if ( isset( $seasons[ $season_num ] ) ) {
			return $seasons[ $season_num ] . $year;
		}
	}

	return 'archive';
}

/**
 * Convert a URL issue slug into an issue key.
 * Format: {season}{year} -> v{year_short}n{season_num}
 * Example: winter2026 -> v26n1
 *
 * @param string $issue_slug URL issue slug.
 * @return string|null Issue key or null when slug is invalid.
 */
function bn_issue_slug_to_issue_key( $issue_slug ) {
	if ( ! is_string( $issue_slug ) || '' === $issue_slug ) {
		return null;
	}

	if ( ! preg_match( '/^(winter|spring|summer|fall)(20\d{2})$/i', $issue_slug, $matches ) ) {
		return null;
	}

	$season_to_num = array(
		'winter' => '1',
		'spring' => '2',
		'summer' => '3',
		'fall'   => '4',
	);

	$season = strtolower( $matches[1] );
	$year   = $matches[2];

	if ( ! isset( $season_to_num[ $season ] ) ) {
		return null;
	}

	return 'v' . substr( $year, -2 ) . 'n' . $season_to_num[ $season ];
}

/**
 * Redirect /magazine/{issue}/ to the matching issue page.
 *
 * Keeps /magazine/{issue}/{article-slug}/ intact for Article CPT single URLs.
 */
add_action( 'template_redirect', 'bn_redirect_issue_slug_landing', 1 );
function bn_redirect_issue_slug_landing() {
	if ( is_admin() || wp_doing_ajax() ) {
		return;
	}

	$path = wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
	$path = trim( (string) $path, '/' );

	// Match exactly one segment after /magazine/.
	if ( ! preg_match( '#^magazine/([^/]+)$#i', $path, $matches ) ) {
		return;
	}

	$issue_slug = sanitize_title( $matches[1] );
	$issue_key  = bn_issue_slug_to_issue_key( $issue_slug );

	if ( ! $issue_key ) {
		return;
	}

	$target_url = bn_get_issue_url( $issue_key );
	$home_magazine_url = home_url( '/magazine/' );

	// bn_get_issue_url falls back to /magazine/ when no issue page exists.
	if ( ! $target_url || untrailingslashit( $target_url ) === untrailingslashit( $home_magazine_url ) ) {
		$fallback_page = get_page_by_path( 'magazine-archive/bay-nature-' . $issue_slug );
		if ( $fallback_page instanceof WP_Post ) {
			$target_url = get_permalink( $fallback_page );
		}
	}

	if ( ! $target_url ) {
		return;
	}

	$current_url = home_url( '/' . $path . '/' );
	if ( untrailingslashit( $target_url ) === untrailingslashit( $current_url ) ) {
		return;
	}

	wp_safe_redirect( $target_url, 301 );
	exit;
}

/**
 * Redirect old /articles/{slug}/ URLs to new /magazine/{issue}/ structure.
 * This preserves SEO value from old links.
 */
add_action( 'template_redirect', 'bn_redirect_old_article_urls' );
function bn_redirect_old_article_urls() {
    // Only run on 404 pages (old URLs won't match new structure)
    if ( ! is_404() ) {
        return;
    }
    
    $request_uri = $_SERVER['REQUEST_URI'];
    
    // Check if this looks like an old /articles/ URL
    if ( preg_match( '#^/articles?/([^/]+)/?$#i', $request_uri, $matches ) ) {
        $post_slug = $matches[1];
        
        // Try to find the article by slug
        $article = get_page_by_path( $post_slug, OBJECT, 'article' );
        
        if ( $article ) {
            $new_url = get_permalink( $article->ID );
            wp_redirect( $new_url, 301 );
            exit;
        }
    }
}

/**
 * Redirect old post URLs (without category) to new structure (with category).
 * Old: /2019/01/25/post-slug/
 * New: /2019/01/25/category-slug/post-slug/
 */
add_action( 'template_redirect', 'bn_redirect_old_post_urls' );
function bn_redirect_old_post_urls() {
    // Only run on 404 pages
    if ( ! is_404() ) {
        return;
    }
    
    $request_uri = trim( $_SERVER['REQUEST_URI'], '/' );
    
    // Match old pattern: YYYY/MM/DD/post-slug
    if ( preg_match( '#^(\d{4})/(\d{2})/(\d{2})/([^/]+)/?$#', $request_uri, $matches ) ) {
        $year = $matches[1];
        $month = $matches[2];
        $day = $matches[3];
        $post_slug = $matches[4];
        
        // Try to find the post by slug
        $post = get_page_by_path( $post_slug, OBJECT, 'post' );
        
        if ( $post ) {
            // Verify the date matches (optional but safer)
            $post_date = get_the_date( 'Y/m/d', $post );
            if ( $post_date === "$year/$month/$day" ) {
                $new_url = get_permalink( $post->ID );
                wp_redirect( $new_url, 301 );
                exit;
            }
        }
    }
}

/**
 * Redirect old /magazine/archive/{slug}/ URLs to the correct permalink.
 * These are articles that previously had no issue_key and defaulted to "archive".
 */
add_action( 'template_redirect', 'bn_redirect_old_magazine_archive_urls' );
function bn_redirect_old_magazine_archive_urls() {
    if ( ! is_404() ) {
        return;
    }

    $request_uri = trim( $_SERVER['REQUEST_URI'], '/' );

    if ( ! preg_match( '#^magazine/archive/([^/]+)/?$#i', $request_uri, $matches ) ) {
        return;
    }

    $post_slug = sanitize_title( $matches[1] );

    $article = get_page_by_path( $post_slug, OBJECT, 'article' );
    if ( $article ) {
        wp_redirect( get_permalink( $article->ID ), 301 );
        exit;
    }

    $post = get_page_by_path( $post_slug, OBJECT, 'post' );
    if ( $post ) {
        wp_redirect( get_permalink( $post->ID ), 301 );
        exit;
    }
}

/**
 * Get the URL for a specific issue or the main magazine page.
 * 
 * @param string $issue_key The issue key code (e.g. v25n4)
 * @return string The URL for the issue or magazine archive.
 */
function bn_get_issue_url( $issue_key = null ) {
    // If issue key is provided, try to find a matching page by current_issue_key ACF field
    // Pages with "Magazine Issue Page" template have this field
    if ( $issue_key ) {
        $issue_pages = get_posts( array(
            'post_type'      => 'page',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'post_status'    => 'publish',
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'   => '_wp_page_template',
                    'value' => 'current_issue_template.php',
                ),
                array(
                    'key'   => 'current_issue_key',
                    'value' => $issue_key,
                ),
            ),
        ) );
        
        if ( ! empty( $issue_pages ) ) {
            return get_permalink( $issue_pages[0] );
        }
    }
    
    // Fallback to main magazine page
    if ( function_exists( 'bn_get_magazine_parent_page_id' ) ) {
        $parent_id = bn_get_magazine_parent_page_id();
        if ( $parent_id ) {
            return get_permalink( $parent_id );
        }
    }
    
    return home_url( '/magazine/' );
}

/**
 * Get the primary category for a post.
 * Uses Yoast SEO primary term if available, falls back to the first category.
 *
 * @param int $post_id Post ID. Defaults to current post.
 * @return WP_Term|null Primary category term object or null.
 */
function bn_get_primary_category( $post_id = 0 ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    if ( class_exists( 'WPSEO_Primary_Term' ) ) {
        $primary_term = new WPSEO_Primary_Term( 'category', $post_id );
        $category_id  = $primary_term->get_primary_term();
        if ( $category_id ) {
            $term = get_term( $category_id );
            if ( $term && ! is_wp_error( $term ) ) {
                return $term;
            }
        }
    }
    $cats = get_the_category( $post_id );
    return ! empty( $cats ) ? $cats[0] : null;
}

if ( ! function_exists( 'newspack_categories' ) ) {
    function newspack_categories() {
        // Check for Issue Key on Articles
        if ( 'article' === get_post_type() ) {
            $issue_key = get_post_meta( get_the_ID(), 'issue_key', true );
            $issue_name = bn_get_issue_name( $issue_key );
            
            if ( $issue_name ) {
                $issue_url = bn_get_issue_url( $issue_key );
                echo '<span class="cat-links"><a class="issue-cat-link" href="' . esc_url( $issue_url ) . '">' . esc_html( $issue_name ) . '</a></span>';
                $primary_cat = bn_get_primary_category( get_the_ID() );
                if ( $primary_cat ) {
                    echo '<span class="cat-links cat-links--primary"><a href="' . esc_url( get_category_link( $primary_cat->term_id ) ) . '">' . esc_html( $primary_cat->name ) . '</a></span>';
                }
                return;
            }
        }

        $categories_list     = '';
        $primary_cat_enabled = get_theme_mod( 'post_primary_category', true );

        // Only display Yoast primary category if set.
        if ( class_exists( 'WPSEO_Primary_Term' ) && $primary_cat_enabled ) {
            $primary_term = new WPSEO_Primary_Term( 'category', get_the_ID() );
            $category_id  = $primary_term->get_primary_term();
            if ( $category_id ) {
                $category = get_term( $category_id );
                if ( $category ) {
                    $categories_list = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" rel="category tag">' . $category->name . '</a>';
                }
            }
        }

        if ( ! $categories_list ) {
            /* translators: used between list items; followed by a space. */
            $categories_list = get_the_category_list( '<span class="sep">' . esc_html__( ',', 'newspack-theme' ) . ' </span>' );
        }

        if ( $categories_list ) {
            /* translators: 1: formatted categories list. */
            printf( '<span class="cat-links">%1$s</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}

/**
 * Filter the category list to display Issue Name for Articles.
 * This covers blocks or other components that bypass newspack_categories().
 */
function bn_filter_issue_category_list( $html, $post_id = 0 ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    if ( 'article' === get_post_type( $post_id ) ) {
        $issue_key = get_post_meta( $post_id, 'issue_key', true );
        $issue_name = bn_get_issue_name( $issue_key );
        
        if ( $issue_name ) {
            $issue_url = bn_get_issue_url( $issue_key );
            $output = '<a class="issue-cat-link" href="' . esc_url( $issue_url ) . '" rel="category tag">' . esc_html( $issue_name ) . '</a>';
            $primary_cat = bn_get_primary_category( $post_id );
            if ( $primary_cat ) {
                $output .= ' <span class="cat-sep">|</span> <a href="' . esc_url( get_category_link( $primary_cat->term_id ) ) . '" rel="category tag">' . esc_html( $primary_cat->name ) . '</a>';
            }
            return $output;
        }
    }
    
    return $html;
}
add_filter( 'the_category_list', 'bn_filter_issue_category_list', 10, 2 );

/**
 * Filter Newspack Blocks category output for articles.
 * This is the main hook used by Newspack Blocks (Homepage Articles, Carousel).
 */
function bn_filter_newspack_blocks_categories( $category_html ) {
    $post_id = get_the_ID();
    
    if ( 'article' === get_post_type( $post_id ) ) {
        $issue_key = get_post_meta( $post_id, 'issue_key', true );
        $issue_name = bn_get_issue_name( $issue_key );
        
        if ( $issue_name ) {
            $issue_url = bn_get_issue_url( $issue_key );
            $output = '<a class="issue-cat-link" href="' . esc_url( $issue_url ) . '">' . esc_html( $issue_name ) . '</a>';
            $primary_cat = bn_get_primary_category( $post_id );
            if ( $primary_cat ) {
                $output .= ' <span class="cat-sep">|</span> <a href="' . esc_url( get_category_link( $primary_cat->term_id ) ) . '">' . esc_html( $primary_cat->name ) . '</a>';
            }
            return $output;
        }
    }
    
    return $category_html;
}
add_filter( 'newspack_blocks_categories', 'bn_filter_newspack_blocks_categories', 10, 1 );

/**
 * Rewrite rule for /current-issue redirect.
 * Redirects to the latest magazine issue page.
 */
add_action( 'init', function() {
    add_rewrite_rule( '^current-issue/?$', 'index.php?bn_latest_issue_redirect=1', 'top' );
} );

add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'bn_latest_issue_redirect';
    return $query_vars;
} );

add_action( 'template_redirect', function() {
    if ( get_query_var( 'bn_latest_issue_redirect' ) ) {
        if ( function_exists( 'bn_get_latest_magazine_issue_page' ) ) {
            $latest_issue = bn_get_latest_magazine_issue_page();
            if ( $latest_issue ) {
                wp_safe_redirect( get_permalink( $latest_issue ) );
                exit;
            }
        }
        // Fallback if no issue found
        wp_safe_redirect( home_url( '/magazine/' ) );
        exit;
    }
} );

/**
 * Render an article in wpnbha style for search/archive pages.
 *
 * @param WP_Post $post            The post object.
 * @param array   $article_classes Article CSS classes.
 * @param array   $categories      Post categories.
 */
function bn_render_archive_article( $post, $article_classes = array(), $categories = array() ) {
	$post_id = $post->ID;
	
	// Build default classes if not provided
	if ( empty( $article_classes ) ) {
		$article_classes = array( 'archive-result-item' );
		$article_classes[] = 'type-' . get_post_type( $post_id );
		
		if ( has_post_thumbnail( $post_id ) ) {
			$article_classes[] = 'post-has-image';
		}
	}
	
	// Get categories if not provided
	if ( empty( $categories ) ) {
		$categories = get_the_category( $post_id );
	}
	?>
	<article id="post-<?php echo esc_attr( $post_id ); ?>" class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>">
		
		<?php if ( has_post_thumbnail( $post_id ) ) : ?>
			<figure class="post-thumbnail">
				<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" rel="bookmark" tabindex="-1" aria-hidden="true">
					<?php echo get_the_post_thumbnail( $post_id, 'newspack-article-block-landscape-small', array(
						'alt' => trim( wp_strip_all_tags( get_the_title( $post_id ) ) ),
					) ); ?>
				</a>
			</figure>
		<?php endif; ?>

		<div class="entry-wrapper">
			<?php
			// Display category or issue name for articles
			$category_displayed = false;
			
			// For article post type, show issue name + primary category
			if ( 'article' === get_post_type( $post_id ) ) {
				$issue_key = get_post_meta( $post_id, 'issue_key', true );
				$issue_name = function_exists( 'bn_get_issue_name' ) ? bn_get_issue_name( $issue_key ) : null;
				if ( $issue_name ) {
					$issue_url = function_exists( 'bn_get_issue_url' ) ? bn_get_issue_url( $issue_key ) : home_url( '/magazine/' );
					$primary_cat = function_exists( 'bn_get_primary_category' ) ? bn_get_primary_category( $post_id ) : null;
					?>
					<div class="cat-links">
						<a class="issue-cat-link" href="<?php echo esc_url( $issue_url ); ?>">
							<?php echo esc_html( $issue_name ); ?>
						</a>
						<?php if ( $primary_cat ) : ?>
							<span class="cat-sep">|</span>
							<a href="<?php echo esc_url( get_category_link( $primary_cat->term_id ) ); ?>">
								<?php echo esc_html( $primary_cat->name ); ?>
							</a>
						<?php endif; ?>
					</div>
					<?php
					$category_displayed = true;
				}
			}
			
			// Fall back to standard category display
			if ( ! $category_displayed ) {
				$primary_category = null;
				
				// Try to get Yoast primary category first
				if ( class_exists( 'WPSEO_Primary_Term' ) ) {
					$primary_term = new WPSEO_Primary_Term( 'category', $post_id );
					$category_id  = $primary_term->get_primary_term();
					if ( $category_id ) {
						$primary_category = get_term( $category_id );
					}
				}
				
				// Fall back to first category
				if ( ! $primary_category && ! empty( $categories ) ) {
					$primary_category = $categories[0];
				}
				
				if ( $primary_category && is_a( $primary_category, 'WP_Term' ) ) :
				?>
					<div class="cat-links">
						<a href="<?php echo esc_url( get_category_link( $primary_category->term_id ) ); ?>">
							<?php echo esc_html( $primary_category->name ); ?>
						</a>
					</div>
				<?php endif;
			}
			?>
			
			<h2 class="entry-title">
				<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" rel="bookmark">
					<?php echo esc_html( get_the_title( $post_id ) ); ?>
				</a>
			</h2>
			
			<p class="entry-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $post_id ), 30, '...' ) ); ?></p>
			
			<div class="entry-meta">
				<?php
				// Author display
				if ( function_exists( 'get_coauthors' ) ) :
					$coauthors    = get_coauthors( $post_id );
					$author_count = count( $coauthors );
					if ( $author_count > 0 ) :
					?>
					<span class="byline">
						<span class="author-prefix"><?php esc_html_e( 'by', 'bn-newspack-child' ); ?></span>
						<?php
						$i = 0;
						foreach ( $coauthors as $coauthor ) :
							$i++;
							$author_url = get_author_posts_url( $coauthor->ID, $coauthor->user_nicename );
							?>
							<span class="author vcard">
								<a class="url fn n" href="<?php echo esc_url( $author_url ); ?>">
									<?php echo esc_html( $coauthor->display_name ); ?>
								</a>
							</span><?php
							if ( $i < $author_count - 1 ) {
								echo ', ';
							} elseif ( $i === $author_count - 1 ) {
								esc_html_e( ' and ', 'bn-newspack-child' );
							}
						endforeach;
						?>
					</span>
					<?php
					endif;
				else :
					$author_id = get_post_field( 'post_author', $post_id );
					?>
					<span class="byline">
						<span class="author-prefix"><?php esc_html_e( 'by', 'bn-newspack-child' ); ?></span>
						<span class="author vcard">
							<a class="url fn n" href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
								<?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?>
							</a>
						</span>
					</span>
				<?php endif; ?>
				
				<time class="entry-date published" datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $post_id ) ); ?>">
					<?php echo esc_html( get_the_date( '', $post_id ) ); ?>
				</time>
			</div>
		</div><!-- .entry-wrapper -->
		
	</article>
	<?php
}

/*
Issue: alignfull/alignwide article images render upscaled
Full/wide body images on the single-feature article layout are upscaled from the 780px srcset candidate into the ~1280px column. Sitewide on that template (e.g. Summer 2026 climbing story, Spring 2026 "Seaside Subterfuge"). Heroes and in-column images are fine.

Cause: sizes is capped at the legacy single-feature width: sizes="(max-width: 780px) 100vw, 780px"
*/
add_filter( 'wp_content_img_tag', function( $filtered_image, $context, $attachment_id ) {

    if ( false === strpos( $filtered_image, 'alignfull' )

        && false === strpos( $filtered_image, 'alignwide' ) ) {

        return $filtered_image;

    }

    return preg_replace(

        '/sizes=("|\')[^"\']*\1/',

        'sizes="(max-width: 1280px) 100vw, 1280px"',

        $filtered_image

    );

}, 10, 3 );

/**
 * Output a JS variable on 404 pages for OptinMonster to check.
 * Prevents popups from firing on 404 error pages.
 */
add_action( 'wp_head', function() {
    if ( is_404() ) {
        echo '<script>var omIs404 = true;</script>';
    }
});