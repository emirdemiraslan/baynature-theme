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
    if ( $tpl === 'member-only-content-default-template' || $tpl === 'templates/member-only-content-default-template.php' ) {
        $template_match = true;
    }

    // Automatic latest-N printed issues for Article CPT using ACF Issue field.
    $auto_match = false;
    if ( post_type_exists( 'article' ) && $post->post_type === 'article' ) {
        $issue = function_exists( 'get_field' ) ? get_field( 'issue', $post->ID ) : null;
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

    // Bots get full content for SEO.
    if ( bn_is_bot() ) {
        return $content;
    }

    global $post;
    if ( ! $post || ! bn_should_paywall_post( $post ) ) {
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

    // Build CTA with context.
    $join_url = '/join'; // Default
    if ( function_exists( 'bn_get_utility_urls' ) ) {
        $urls = bn_get_utility_urls();
        $join_url = isset( $urls['join_url'] ) ? $urls['join_url'] : '/join';
    }
    
    $cta = '<div class="bn-paywall-cta bn-inline-paywall-cta">';
    $cta .= '<div class="bn-paywall-cta-inner">';
    $cta .= '<h3 class="bn-paywall-cta-heading">' . esc_html__( 'Become a member to continue reading', 'bn-newspack-child' ) . '</h3>';
    $cta .= '<p class="bn-paywall-cta-message">' . esc_html__( 'Support independent environmental journalism in the San Francisco Bay Area.', 'bn-newspack-child' ) . '</p>';
    $cta .= '<a href="' . esc_url( $join_url ) . '" class="bn-paywall-cta-button">' . esc_html__( 'Join Now', 'bn-newspack-child' ) . '</a>';
    $cta .= '</div>';
    $cta .= '</div>';

    return $out . $cta;
}, 20 );

/**
 * Compute the latest N printed issues by scanning Article CPT and ACF Issue field.
 * Result is cached in a transient for performance.
 */
function bn_latest_printed_issues( $n = 3 ) {
    $n = max( 0, intval( $n ) );
    $cache_key = 'bn_latest_issues_' . $n;
    $cached = get_transient( $cache_key );
    if ( is_array( $cached ) ) {
        return $cached;
    }

    $map = array();
    $q = new WP_Query( array(
        'post_type' => 'article',
        'posts_per_page' => 500,
        'post_status' => 'publish',
        'no_found_rows' => true,
        'orderby' => 'date',
        'order' => 'DESC',
        'fields' => 'ids',
    ) );
    foreach ( $q->posts as $pid ) {
        $issue = function_exists( 'get_field' ) ? get_field( 'issue', $pid ) : null;
        if ( empty( $issue ) ) {
            continue;
        }
        $d = get_post_time( 'U', true, $pid );
        if ( ! isset( $map[ $issue ] ) || $d > $map[ $issue ] ) {
            $map[ $issue ] = $d;
        }
        if ( count( $map ) >= ( $n * 3 ) ) {
            // Heuristic: we likely saw enough issues; continue but keep small set.
        }
    }
    arsort( $map );
    $issues = array_slice( array_keys( $map ), 0, $n );
    set_transient( $cache_key, $issues, HOUR_IN_SECONDS );
    return $issues;
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



