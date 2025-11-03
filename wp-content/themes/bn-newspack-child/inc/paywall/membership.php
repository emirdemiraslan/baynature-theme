<?php
/**
 * Membership helpers (placeholder to be replaced by ported logic from crate theme).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return whether current visitor has access (subscriber/member).
 * Replace with real logic when ported.
 */
function bn_is_subscriber() {
    // TODO: Port actual membership/paywall auth checks from crate.
    return is_user_logged_in();
}



