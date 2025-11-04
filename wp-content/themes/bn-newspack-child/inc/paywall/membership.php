<?php
/**
 * Membership and access control.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check if the current visitor has subscriber/member access.
 *
 * Priority order:
 * 1. Filter override (for plugin integration)
 * 2. User role check (logged-in users with subscriber/member/administrator roles)
 * 3. Cookie-based free views for anonymous users
 *
 * @return bool True if user has access, false otherwise.
 */
function bn_is_subscriber() {
    // Allow plugins to override (e.g., WooCommerce Subscriptions, Paid Memberships Pro).
    $override = apply_filters( 'bn_is_subscriber_override', null );
    if ( null !== $override ) {
        return (bool) $override;
    }

    // Check if user is logged in with appropriate role.
    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();
        $allowed_roles = apply_filters( 'bn_subscriber_roles', array( 'subscriber', 'member', 'administrator', 'editor', 'author' ) );
        
        foreach ( $allowed_roles as $role ) {
            if ( in_array( $role, (array) $user->roles, true ) ) {
                return true;
            }
        }
        
        // Logged-in user without required role should be denied.
        return false;
    }

    // Anonymous users: check cookie-based free views.
    return bn_has_free_views_remaining();
}

/**
 * Optional override via master/staff share keys and cookie.
 * If a valid unlock is present, we treat the visitor as having access.
 */
add_filter( 'bn_is_subscriber_override', function ( $current ) {
    if ( null !== $current ) {
        return $current;
    }
    return bn_paywall_has_unlock() ? true : null;
}, 5 );

/**
 * Check if the request has a valid unlock (cookie, master key, or non-expired staff share key).
 */
function bn_paywall_has_unlock() {
    // Cookie takes precedence once set.
    if ( bn_paywall_cookie_is_valid() ) {
        return true;
    }

    // Master key in utm_campaign sets cookie and unlocks.
    if ( bn_paywall_master_key_in_request() ) {
        bn_paywall_set_cookie();
        return true;
    }

    // Staff share key with valid expiration unlocks.
    if ( bn_paywall_staff_key_is_valid() ) {
        return true;
    }

    return false;
}

/** Get ACF option field safely. */
function bn_pw_opt( $key, $default = '' ) {
    return function_exists( 'get_field' ) ? get_field( $key, 'option' ) : $default;
}

/** Validate PW_KEY cookie against master key. */
function bn_paywall_cookie_is_valid() {
    $master_key = (string) bn_pw_opt( 'master_key', '' );
    if ( empty( $master_key ) ) {
        return false;
    }
    if ( isset( $_COOKIE['PW_KEY'] ) ) {
        $val = (string) $_COOKIE['PW_KEY'];
        return strpos( $val, $master_key ) !== false;
    }
    return false;
}

/** Check if request utm_campaign contains master key. */
function bn_paywall_master_key_in_request() {
    $master_key = (string) bn_pw_opt( 'master_key', '' );
    if ( empty( $master_key ) ) {
        return false;
    }
    if ( isset( $_GET['utm_campaign'] ) ) {
        $utm = (string) $_GET['utm_campaign'];
        return strpos( $utm, $master_key ) !== false;
    }
    return false;
}

/** Set PW_KEY cookie with configured expiration. */
function bn_paywall_set_cookie() {
    $master_key = (string) bn_pw_opt( 'master_key', '' );
    if ( empty( $master_key ) ) {
        return;
    }
    $days = intval( bn_pw_opt( 'paywall_cookie_expiration_in_n_days', 3 ) );
    if ( $days <= 0 ) {
        $days = 3;
    }
    $expiry = time() + ( $days * DAY_IN_SECONDS );
    if ( ! headers_sent() ) {
        setcookie( 'PW_KEY', $master_key, $expiry, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
        $_COOKIE['PW_KEY'] = $master_key;
    }
}

/** Validate staff share key with expiration (year, month, day) from ACF options. */
function bn_paywall_staff_key_is_valid() {
    $staff_key = (string) bn_pw_opt( 'staff_sharing_key', '' );
    if ( empty( $staff_key ) ) {
        return false;
    }

    if ( ! isset( $_GET['utm_campaign'] ) ) {
        return false;
    }
    $utm = (string) $_GET['utm_campaign'];
    if ( strpos( $utm, $staff_key ) === false ) {
        return false;
    }

    $year  = intval( bn_pw_opt( 'sharing_key_expiration_year', 0 ) );
    $month = max( 1, min( 12, intval( bn_pw_opt( 'sharing_key_expiration_month', 0 ) ) ) );

    // Compute max day for the given month (non-leap: match original logic)
    $max_day = 31;
    switch ( $month ) {
        case 4: case 6: case 9: case 11:
            $max_day = 30; break;
        case 2:
            $max_day = 28; break;
        default:
            $max_day = 31; break;
    }
    $day = intval( bn_pw_opt( 'sharing_key_expiration_day', 0 ) );
    if ( $day > $max_day ) {
        $day = $max_day;
    }

    if ( $year <= 0 || $month <= 0 || $day <= 0 ) {
        return false;
    }

    // Current date (yy matches original y format, but compare using full Y for safety)
    $now = current_time( 'timestamp' );
    $now_y = intval( gmdate( 'Y', $now ) );
    $now_m = intval( gmdate( 'n', $now ) );
    $now_d = intval( gmdate( 'j', $now ) );

    if ( $now_y > $year ) {
        return false;
    }
    if ( $now_y < $year ) {
        return true;
    }
    if ( $now_m > $month ) {
        return false;
    }
    if ( $now_m < $month ) {
        return true;
    }
    // same month and year
    return ( $day > $now_d );
}

/**
 * On init, if master key is present in request, set the cookie for subsequent visits.
 */
add_action( 'init', function () {
    if ( bn_paywall_master_key_in_request() ) {
        bn_paywall_set_cookie();
    }
}, 0 );

/**
 * Check if anonymous user has free views remaining.
 *
 * @return bool True if free views remain, false otherwise.
 */
function bn_has_free_views_remaining() {
    $opts = bn_paywall_options();
    $free_views = isset( $opts['free_views'] ) ? intval( $opts['free_views'] ) : 3;
    
    // If free views disabled, deny access.
    if ( $free_views <= 0 ) {
        return false;
    }

    $cookie_name = 'bn_paywall_views';
    $views_count = isset( $_COOKIE[ $cookie_name ] ) ? intval( $_COOKIE[ $cookie_name ] ) : 0;

    // Use <= to allow exactly N free views (since tracking happens before checking).
    // With free_views=3: views 1,2,3 are allowed (1<=3, 2<=3, 3<=3), view 4 is blocked (4<=3 is false).
    return $views_count <= $free_views;
}

/**
 * Track a paywalled content view for anonymous users.
 *
 * Increments the cookie counter when gated content is viewed.
 * Called automatically by the content filter.
 */
function bn_track_paywall_view() {
    // Only track for anonymous users.
    if ( is_user_logged_in() ) {
        return;
    }

    $cookie_name = 'bn_paywall_views';
    $views_count = isset( $_COOKIE[ $cookie_name ] ) ? intval( $_COOKIE[ $cookie_name ] ) : 0;
    $views_count++;

    // Set cookie for 30 days, but only if headers haven't been sent yet.
    if ( ! headers_sent() ) {
        $expiry = time() + ( 30 * DAY_IN_SECONDS );
        setcookie( $cookie_name, $views_count, $expiry, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
    }
    
    // Update $_COOKIE immediately so subsequent checks in the same request use the new value.
    $_COOKIE[ $cookie_name ] = $views_count;
}

/**
 * Check if current request is from a search engine bot.
 *
 * @return bool True if bot, false otherwise.
 */
function bn_is_bot() {
    if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
        return false;
    }

    $user_agent = strtolower( $_SERVER['HTTP_USER_AGENT'] );
    $bot_patterns = array(
        'googlebot',
        'bingbot',
        'slurp',          // Yahoo
        'duckduckbot',
        'baiduspider',
        'yandexbot',
        'facebookexternalhit',
        'twitterbot',
        'linkedinbot',
        'whatsapp',
        'telegrambot',
    );

    foreach ( $bot_patterns as $pattern ) {
        if ( strpos( $user_agent, $pattern ) !== false ) {
            return true;
        }
    }

    return false;
}

/**
 * Register a custom 'member' role on theme activation.
 *
 * This provides a dedicated role for paying members separate from WordPress 'subscriber'.
 */
function bn_register_member_role() {
    if ( ! get_role( 'member' ) ) {
        add_role( 'member', __( 'Member', 'bn-newspack-child' ), array(
            'read' => true,
        ) );
    }
}
add_action( 'after_switch_theme', 'bn_register_member_role' );

/**
 * Helper to check membership status for a specific user ID.
 *
 * @param int $user_id User ID to check.
 * @return bool True if user is a member, false otherwise.
 */
function bn_user_is_member( $user_id ) {
    $user = get_userdata( $user_id );
    if ( ! $user ) {
        return false;
    }

    $allowed_roles = apply_filters( 'bn_subscriber_roles', array( 'subscriber', 'member', 'administrator', 'editor', 'author' ) );
    
    foreach ( $allowed_roles as $role ) {
        if ( in_array( $role, (array) $user->roles, true ) ) {
            return true;
        }
    }

    return false;
}



