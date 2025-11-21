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
        'member-only-content-default-template',
        'member-only-content-no-banner-template',
        'member_only_content_default_template.php',
        'member_only_content_no_banner_template.php',
        'templates/member-only-content-default-template.php',
        'templates/member-only-content-no-banner-template.php',
    );
    if ( in_array( $tpl, $member_only_templates, true ) ) {
        $template_match = true;
    }

    // Automatic latest-N printed issues for Article CPT using ACF Issue field.
    $auto_match = false;
    if ( post_type_exists( 'article' ) && $post->post_type === 'article' ) {
        $issue = function_exists( 'get_field' ) ? get_field( 'issue_key', $post->ID ) : null;
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
    //TODO: Remove this before production
    // TEMPORARY DEBUG - Remove after testing
    /*
    $opts = bn_paywall_options();
    error_log('=== PAYWALL DEBUG START ===');
    error_log('Mode from options: ' . print_r($opts['mode'], true));
    error_log('Latest N setting: ' . print_r($opts['latest_n'], true));
    error_log('Free views setting: ' . print_r($opts['free_views'], true));
    error_log('Post ID: ' . ($post ? $post->ID : 'NULL'));
    error_log('Post type: ' . ($post ? $post->post_type : 'NULL'));
    error_log('ACF get_field exists?: ' . (function_exists('get_field') ? 'YES' : 'NO'));
    
    if ($post && $post->post_type === 'article') {
        $issue = function_exists('get_field') ? get_field('issue_key', $post->ID) : null;
        error_log('Article issue field: ' . print_r($issue, true));
        
        if (empty($issue)) {
            error_log('WARNING: Article has NO issue field set!');
        } else {
            $latest = bn_latest_printed_issues($opts['latest_n']);
            error_log('Latest ' . $opts['latest_n'] . ' issues: ' . print_r($latest, true));
            $is_in_latest = in_array($issue, $latest, true);
            error_log('Is article issue in latest?: ' . ($is_in_latest ? 'YES' : 'NO'));
        }
    }
    
    if ( ! $post || ! bn_should_paywall_post( $post ) ) {
        error_log('bn_should_paywall_post returned FALSE - NO PAYWALL');
        error_log('=== PAYWALL DEBUG END ===');
        return $content;
    }
    
    error_log('bn_should_paywall_post returned TRUE - PAYWALL SHOULD TRIGGER');
    error_log('Is subscriber?: ' . (bn_is_subscriber() ? 'YES' : 'NO'));
    error_log('Has free views?: ' . (bn_has_free_views_remaining() ? 'YES' : 'NO'));
    error_log('=== PAYWALL DEBUG END ===');

    */

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
        $issue = function_exists( 'get_field' ) ? get_field( 'issue_key', $pid ) : null;
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

//TODO: Remove this before production
/**
 * DEBUG HELPER: Clear all paywall transients manually.
 * Call this from browser console or use: bn_clear_paywall_cache()
 */
/*
function bn_clear_paywall_cache() {
    global $wpdb;
    $keys = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_bn_latest_issues_%'" );
    $cleared = 0;
    if ( $keys ) {
        foreach ( $keys as $key ) {
            $name = str_replace( '_transient_', '', $key );
            delete_transient( $name );
            $cleared++;
            error_log( 'Cleared transient: ' . $name );
        }
    }
    error_log( 'Total transients cleared: ' . $cleared );
    return $cleared;
}
*/


