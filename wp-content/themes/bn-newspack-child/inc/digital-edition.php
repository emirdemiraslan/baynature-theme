<?php
/**
 * Digital Edition Functions (SFS API Integration)
 *
 * Functions for digital edition paywall and subscriber verification.
 * Legacy compatibility from crate theme.
 *
 * @package bn-newspack-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get current user ID.
 *
 * @return int User ID or 0 if not logged in.
 */
function get_current_id() {
	if ( ! function_exists( 'wp_get_current_user' ) ) {
		return 0;
	}
	$user = wp_get_current_user();
	return ( isset( $user ) ? (int) $user->ID : 0 );
}

/**
 * Check if email is on whitelist.
 *
 * @param string $email_address Email to check.
 * @return bool True if whitelisted.
 */
function isWhiteListEmail( $email_address ) {
	$whitelist = array(
		'melissadhero@gmail.com',
		'clroskosz@me.com',
		'frnkcox6@gmail.com',
	);

	return in_array( $email_address, $whitelist, true );
}

/**
 * Check if email is a Bay Nature staff email.
 *
 * @param string $email_address Email to check.
 * @return bool True if Bay Nature email.
 */
function isBayNatureEmail( $email_address ) {
	$sub_str = explode( '@', $email_address );
	if ( isset( $sub_str[1] ) && 0 === strcmp( $sub_str[1], 'baynature.org' ) ) {
		return true;
	}
	return false;
}

/**
 * Call SFS API to check digital edition subscription.
 *
 * @param string $email_address Email to check.
 * @return int 0=not logged in, 1=logged in but no subscription, 2=active subscriber.
 */
function sfsAPI_isDES( $email_address ) {
	// Check whitelists first
	if ( isWhiteListEmail( $email_address ) ) {
		return 2;
	}
	if ( isBayNatureEmail( $email_address ) ) {
		return 2;
	}

	// Call SFS API
	$data   = array( 'Email' => $email_address );
	$result = callAPIGet( 'GET', $data );

	if ( empty( $result ) ) {
		return 1;
	}

	$dc_result = json_decode( $result );

	if ( ! isset( $dc_result->Edition ) || ! isset( $dc_result->Status ) ) {
		return 1;
	}

	// Check if user has active digital or bundle subscription
	if ( ( 'D' === $dc_result->Edition || 'B' === $dc_result->Edition ) && 'A' === $dc_result->Status ) {
		return 2;
	}

	return 1;
}

/**
 * Check if current visitor has active digital subscription.
 *
 * @return int 0=not logged in, 1=logged in but no subscription, 2=active subscriber.
 */
function isVistorActiveDigitalSubscriber() {
	if ( is_user_logged_in() ) {
		$user_id = get_current_id();

		if ( $user_id > 0 ) {
			$user_data     = get_userdata( $user_id );
			$email_address = $user_data->user_email;
			return sfsAPI_isDES( $email_address );
		}
	}
	return 0;
}

/**
 * Check if visitor has demo digital subscription access.
 *
 * @return int 0=not logged in, 2=demo access granted.
 */
function isVistorActiveDemoDigitalSubscriber() {
	if ( is_user_logged_in() ) {
		$user_id = get_current_id();

		if ( $user_id > 0 ) {
			$user_data     = get_userdata( $user_id );
			$email_address = $user_data->user_email;

			if ( 0 === strcmp( $email_address, 'de-promo@baynature.org' ) ) {
				return 2;
			}
		}
	}
	return 0;
}

/**
 * Call SFS API with GET request.
 *
 * @param string $method HTTP method.
 * @param array  $data Data to send.
 * @return string|false API response or false on error.
 */
function callAPIGet( $method, $data ) {
	// SFS API endpoint - this should be configured via admin settings or constants
	$api_url = defined( 'SFS_API_URL' ) ? SFS_API_URL : '';

	if ( empty( $api_url ) ) {
		return false;
	}

	$url = $api_url . '?' . http_build_query( $data );

	$args = array(
		'method'  => $method,
		'timeout' => 30,
		'headers' => array(
			'Content-Type' => 'application/json',
		),
	);

	$response = wp_remote_request( $url, $args );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = wp_remote_retrieve_body( $response );
	return $body;
}

/**
 * Process subscription orders and send to SFS.
 *
 * @param WC_Order $order WooCommerce order object.
 */
function processSubscriptionOrders( $order ) {
	// Subscription Product IDs
	$print_digital_bundle_product_id = 159828;
	$print_only_product_id           = 160548;
	$digital_only_product_id         = 159832;
	$gift_subscription_product_id    = 160549;

	// Implementation would process order and send to SFS API
	// Preserved as stub for legacy compatibility
	// This function should be updated with actual business logic
}

