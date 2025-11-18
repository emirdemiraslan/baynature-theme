<?php
/**
 * Member Only Content Default Template
 * Template Name: Member Only Content Default Template
 * Template Post Type: article, post
 *
 * This template is ONLY used as an identifier for paywall logic.
 * It uses the same layout as single-wide.php but triggers the paywall.
 *
 * @package bn-newspack-child
 */

// This template just loads single-wide.php with the same layout
// The paywall logic in inc/paywall/gate.php will detect this template
// and apply the content gate automatically via the_content filter

// TODO: Remove this debug after testing
// Temporary debug
global $post;
echo '<!-- DEBUG: ';
echo 'Post ID: ' . get_the_ID() . ', ';
echo 'Post Type: ' . get_post_type() . ', ';
echo 'Template: ' . get_page_template_slug() . ', ';
echo 'Should paywall: ' . ( bn_should_paywall_post( $post ) ? 'YES' : 'NO' ) . ', ';
echo 'Is subscriber: ' . ( bn_is_subscriber() ? 'YES' : 'NO' );
echo ' -->';
locate_template( 'single-wide.php', true );

