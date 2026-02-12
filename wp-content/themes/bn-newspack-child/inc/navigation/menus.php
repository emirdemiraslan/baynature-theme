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
        'join_url'          => '/join',
        'donate_url'        => '/donate',
        'overlay_intro_text' => '',
        'magazine_cover_image' => '',
    ) );
}

/**
 * Add settings for Join/Donate URLs and Overlay intro text.
 */
add_action( 'admin_init', function () {
    register_setting( 'bn_navigation', 'bn_navigation_options', array(
        'type'    => 'array',
        'default' => array(
            'join_url'          => '/join',
            'donate_url'        => '/donate',
            'overlay_intro_text' => '',
            'magazine_cover_image' => '',
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

    add_settings_section( 'bn_overlay_settings', __( 'Overlay Menu', 'bn-newspack-child' ), '__return_false', 'bn_navigation' );

    add_settings_field( 'overlay_intro_text', __( 'Intro Text', 'bn-newspack-child' ), function () {
        $opts = get_option( 'bn_navigation_options', array() );
        $val  = isset( $opts['overlay_intro_text'] ) ? $opts['overlay_intro_text'] : '';
        echo '<textarea name="bn_navigation_options[overlay_intro_text]" rows="4" class="large-text">' . esc_textarea( $val ) . '</textarea>';
        echo '<p class="description">' . esc_html__( 'Intro text displayed at the top of the overlay menu.', 'bn-newspack-child' ) . '</p>';
    }, 'bn_navigation', 'bn_overlay_settings' );

    add_settings_field( 'magazine_cover_image', __( 'Magazine Cover Image', 'bn-newspack-child' ), function () {
        $opts = get_option( 'bn_navigation_options', array() );
        $image_id = isset( $opts['magazine_cover_image'] ) ? intval( $opts['magazine_cover_image'] ) : 0;
        $image_url = '';
        if ( $image_id ) {
            $image_url = wp_get_attachment_image_url( $image_id, 'medium' );
        }
        ?>
        <div class="bn-magazine-cover-upload">
            <input type="hidden" id="magazine_cover_image" name="bn_navigation_options[magazine_cover_image]" value="<?php echo esc_attr( $image_id ); ?>" />
            <div class="bn-magazine-cover-preview" style="margin-bottom: 10px;">
                <?php if ( $image_url ) : ?>
                    <img src="<?php echo esc_url( $image_url ); ?>" style="max-width: 200px; height: auto; display: block; margin-bottom: 10px;" />
                <?php endif; ?>
            </div>
            <button type="button" class="button bn-upload-magazine-cover-button"><?php esc_html_e( 'Select Image', 'bn-newspack-child' ); ?></button>
            <?php if ( $image_id ) : ?>
                <button type="button" class="button bn-remove-magazine-cover-button" style="margin-left: 10px;"><?php esc_html_e( 'Remove Image', 'bn-newspack-child' ); ?></button>
            <?php endif; ?>
            <p class="description"><?php esc_html_e( 'Magazine cover Fallback image displayed in the Print Edition section of the overlay menu if latest magazine cover image can not be found.', 'bn-newspack-child' ); ?></p>
        </div>
        <?php
    }, 'bn_navigation', 'bn_overlay_settings' );
} );

/**
 * Enqueue media uploader scripts for magazine cover image.
 */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    // Only load on the theme settings page
    if ( 'appearance_page_bn-settings' !== $hook ) {
        return;
    }
    
    // Check if we're on the navigation tab
    $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'paywall';
    if ( 'navigation' !== $active_tab ) {
        return;
    }
    
    wp_enqueue_media();
    wp_add_inline_script( 'jquery', '
        jQuery(document).ready(function($) {
            var mediaUploader;
            
            $(".bn-upload-magazine-cover-button").on("click", function(e) {
                e.preventDefault();
                
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                mediaUploader = wp.media({
                    title: "Select Magazine Cover Image",
                    button: {
                        text: "Use this image"
                    },
                    multiple: false
                });
                
                mediaUploader.on("select", function() {
                    var attachment = mediaUploader.state().get("selection").first().toJSON();
                    $("#magazine_cover_image").val(attachment.id);
                    $(".bn-magazine-cover-preview").html("<img src=\"" + attachment.url + "\" style=\"max-width: 200px; height: auto; display: block; margin-bottom: 10px;\" />");
                    if (!$(".bn-remove-magazine-cover-button").length) {
                        $(".bn-upload-magazine-cover-button").after("<button type=\"button\" class=\"button bn-remove-magazine-cover-button\" style=\"margin-left: 10px;\">Remove Image</button>");
                    }
                });
                
                mediaUploader.open();
            });
            
            $(document).on("click", ".bn-remove-magazine-cover-button", function(e) {
                e.preventDefault();
                $("#magazine_cover_image").val("");
                $(".bn-magazine-cover-preview").html("");
                $(this).remove();
            });
        });
    ' );
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

/**
 * Copy custom menu item CSS classes onto <a> tags for the popup overlay menu.
 *
 * Note: WordPress stores "CSS Classes" on the menu item (<li>) by default; this
 * mirrors user-entered classes onto the link itself for tracking hooks (GTM).
 */
add_filter( 'nav_menu_link_attributes', function ( $atts, $item, $args ) {
    if ( ! isset( $args->theme_location ) ) {
        return $atts;
    }

    $theme_location = $args->theme_location;

    if ( 'popup' === $theme_location ) {
        $base_class = 'bn-overlay-goto-link';
    } elseif ( 'header-utility' === $theme_location ) {
        // This is already added above, but including here keeps the behavior consistent
        // if that filter changes order or is removed later.
        $base_class = 'bn-header-menu-link';
    } else {
        return $atts;
    }

    $raw_classes = array_filter( (array) $item->classes );
    $classes     = array_map( 'sanitize_html_class', $raw_classes );

    // Keep only user-entered classes (exclude WP-generated ones like menu-item-123, current-menu-item, etc).
    $custom_classes = array_values( array_filter(
        $classes,
        function ( $c ) {
            return ! preg_match(
                '/^(menu-item($|-)|current[-_]|current_page_|page_item|menu-item-type-|menu-item-object-)/',
                (string) $c
            );
        }
    ) );

    $existing = array();
    if ( isset( $atts['class'] ) && is_string( $atts['class'] ) ) {
        $existing = preg_split( '/\s+/', trim( $atts['class'] ) );
        $existing = array_filter( array_map( 'sanitize_html_class', (array) $existing ) );
    }

    $merged = array_values( array_unique( array_filter( array_merge(
        $existing,
        array( $base_class ),
        $custom_classes
    ) ) ) );

    $atts['class'] = implode( ' ', $merged );

    return $atts;
}, 10, 3 );

