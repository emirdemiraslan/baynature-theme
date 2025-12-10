<?php
/**
 * Core gating filter and helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/membership.php';

/**
 * Determine whether the current post should be paywalled.
 */
function bn_should_paywall_post( $post ) {
    if ( ! $post || 'publish' !== $post->post_status ) {
        return false;
    }

    // Exceptions can be added later via options.
    $opts = bn_paywall_options();

    // Template-only check: respect classic editorial template assignment.
    $template_match = false;
    $tpl = get_page_template_slug( $post );
    $member_only_templates = array(
        'member_only_content_default_template.php',
        'member_only_content_no_banner_template.php',
    );
    if ( in_array( $tpl, $member_only_templates, true ) ) {
        $template_match = true;
    }

    // Automatic latest-N printed issues for Article CPT using issue_key meta field.
    $auto_match = false;
    if ( post_type_exists( 'article' ) && $post->post_type === 'article' ) {
        $issue = get_post_meta( $post->ID, 'issue_key', true );
        if ( ! empty( $issue ) ) {
            $latest = bn_latest_printed_issues( intval( $opts['latest_n'] ) );
            $auto_match = in_array( $issue, $latest, true );
        }
    }

    if ( $opts['mode'] === 'template' ) {
        return $template_match;
    }
    if ( $opts['mode'] === 'automatic' ) {
        return $auto_match;
    }
    return ( $template_match || $auto_match );
}

/**
 * Hook content filter to inject preview/CTA when gated.
 */
add_filter( 'the_content', function ( $content ) {
    if ( is_admin() || is_feed() ) {
        return $content;
    }

    // Only apply paywall on single article pages
    if ( ! is_single() ) {
        return $content;
    }

    global $post;
    
    // Only apply paywall to article post type
    if ( ! $post || $post->post_type !== 'article' ) {
        return $content;
    }

    // Bots get full content for SEO.
    if ( bn_is_bot() ) {
        return $content;
    }

    // Check if this post should be paywalled at all.
    // If not, return full content immediately.
    if ( ! bn_should_paywall_post( $post ) ) {
        return $content;
    }

    // Track this view for anonymous users BEFORE checking access.
    // This ensures the counter increments on every gated content access attempt.
    // Note: bn_has_free_views_remaining() uses <= comparison to allow exactly N free views.
    // Example with free_views=3: views are incremented to 1,2,3 and all pass (N <= 3),
    // then view 4 increments to 4 and fails (4 <= 3 is false).
    $should_track = ! is_user_logged_in();
    if ( $should_track ) {
        bn_track_paywall_view();
    }

    // If user has access, show full content.
    if ( bn_is_subscriber() ) {
        return $content;
    }

    // User doesn't have access; show preview + CTA.
    $opts = bn_paywall_options();
    $preview_paragraphs = max( 0, intval( $opts['preview_paragraphs'] ) );

    $parts = preg_split( '/(<\/p>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE );
    if ( ! is_array( $parts ) || empty( $parts ) ) {
        return $content;
    }

    $out = '';
    $para_count = 0;
    for ( $i = 0; $i < count( $parts ); $i += 2 ) {
        $chunk = $parts[ $i ];
        $closing = isset( $parts[ $i + 1 ] ) ? $parts[ $i + 1 ] : '';
        if ( trim( $chunk ) !== '' ) {
            $para_count++;
        }
        if ( $preview_paragraphs > 0 && $para_count > $preview_paragraphs ) {
            break;
        }
        $out .= $chunk . $closing;
    }

    // Build CTA with context - using ACF Site Options fields
    $paywall_greeting = function_exists( 'get_field' ) ? get_field( 'paywall_greeting', 'option' ) : '';
    $become_member_message = function_exists( 'get_field' ) ? get_field( 'paywall_become_a_member_message', 'option' ) : '';
    $become_member_link = function_exists( 'get_field' ) ? get_field( 'paywall_become_a_member_link', 'option' ) : '';
    $login_message = function_exists( 'get_field' ) ? get_field( 'paywall_login_message', 'option' ) : '';
    $login_link = function_exists( 'get_field' ) ? get_field( 'paywall_login_link', 'option' ) : '';
    
    // Fallbacks if fields are empty
    if ( empty( $paywall_greeting ) ) {
        $paywall_greeting = __( 'Become a member to continue reading', 'bn-newspack-child' );
    }
    if ( empty( $become_member_message ) ) {
        $become_member_message = __( 'Support independent environmental journalism in the San Francisco Bay Area.', 'bn-newspack-child' );
    }
    if ( empty( $become_member_link ) ) {
        $become_member_link = '/join';
    }
    if ( empty( $login_message ) ) {
        $login_message = __( 'Already a member?', 'bn-newspack-child' );
    }
    if ( empty( $login_link ) ) {
        $login_link = '/login';
    }
    
    $cta = '<div class="bn-paywall-cta bn-inline-paywall-cta">';
    $cta .= '<div class="bn-paywall-cta-inner">';
    $cta .= '<h3 class="bn-paywall-cta-heading">' . esc_html( $paywall_greeting ) . '</h3>';
    $cta .= '<p class="bn-paywall-cta-message">' . esc_html( $become_member_message ) . '</p>';
    $cta .= '<a href="' . esc_url( $become_member_link ) . '" class="bn-paywall-cta-button">' . esc_html__( 'Join / Renew', 'bn-newspack-child' ) . '</a>';
    $cta .= '<p class="bn-paywall-cta-login">' . esc_html( $login_message ) . ' <a href="' . esc_url( $login_link ) . '">' . esc_html__( 'Log in', 'bn-newspack-child' ) . '</a></p>';
    $cta .= '</div>';
    $cta .= '</div>';

    return $out . $cta;
}, 20 );

/**
 * Compute the latest N printed issues by scanning Article CPT and ACF Issue field.
 * Sorts issues by volume/number (e.g., v24n4 > v01n3) instead of post dates.
 * Result is cached in a transient for performance.
 */
function bn_latest_printed_issues( $n = 3 ) {
    $n = max( 0, intval( $n ) );
    $cache_key = 'bn_latest_issues_' . $n;
    $cached = get_transient( $cache_key );
    if ( is_array( $cached ) ) {
        return $cached;
    }

    $issues = array();
    $q = new WP_Query( array(
        'post_type'      => 'article',
        'posts_per_page' => 500,
        'post_status'    => 'publish',
        'no_found_rows'  => true,
        'fields'         => 'ids',
    ) );

    // Collect all unique issue_keys
    foreach ( $q->posts as $pid ) {
        $issue = get_post_meta( $pid, 'issue_key', true );
        if ( ! empty( $issue ) && ! in_array( $issue, $issues, true ) ) {
            $issues[] = $issue;
        }
    }

    // Sort issue_keys by parsing volume/number (e.g., v24n4 > v01n3)
    usort( $issues, function( $a, $b ) {
        // Parse format like "v01n3" into volume and number
        preg_match( '/v(\d+)n(\d+)/i', $a, $ma );
        preg_match( '/v(\d+)n(\d+)/i', $b, $mb );

        $vol_a = isset( $ma[1] ) ? intval( $ma[1] ) : 0;
        $num_a = isset( $ma[2] ) ? intval( $ma[2] ) : 0;
        $vol_b = isset( $mb[1] ) ? intval( $mb[1] ) : 0;
        $num_b = isset( $mb[2] ) ? intval( $mb[2] ) : 0;

        // Sort descending by volume first, then by number
        if ( $vol_a !== $vol_b ) {
            return $vol_b - $vol_a;
        }
        return $num_b - $num_a;
    } );

    $result = array_slice( $issues, 0, $n );
    set_transient( $cache_key, $result, HOUR_IN_SECONDS );
    return $result;
}

// Bust cache on Article save.
add_action( 'save_post_article', function () {
    global $wpdb;
    $keys = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_bn_latest_issues_%'" );
    if ( $keys ) {
        foreach ( $keys as $key ) {
            $name = str_replace( '_transient_', '', $key );
            delete_transient( $name );
        }
    }
} );

