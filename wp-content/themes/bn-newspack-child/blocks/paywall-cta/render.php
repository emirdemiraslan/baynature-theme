<?php
/**
 * Paywall CTA block rendering.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$heading     = isset( $attributes['heading'] ) ? $attributes['heading'] : __( 'Become a member to continue reading', 'bn-newspack-child' );
$message     = isset( $attributes['message'] ) ? $attributes['message'] : __( 'Support independent environmental journalism in the Bay Area.', 'bn-newspack-child' );
$button_text = isset( $attributes['buttonText'] ) ? $attributes['buttonText'] : __( 'Join Now', 'bn-newspack-child' );
$button_url  = isset( $attributes['buttonUrl'] ) ? $attributes['buttonUrl'] : '/join';

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'bn-paywall-cta-block' ) );
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="bn-paywall-cta-inner">
        <h3 class="bn-paywall-cta-heading"><?php echo esc_html( $heading ); ?></h3>
        <p class="bn-paywall-cta-message"><?php echo esc_html( $message ); ?></p>
        <a href="<?php echo esc_url( $button_url ); ?>" class="bn-paywall-cta-button"><?php echo esc_html( $button_text ); ?></a>
    </div>
</div>

