<?php
/**
 * Issue hero template part.
 *
 * Expects args passed via get_template_part third param:
 * - issue_id (int)
 * - layout (left|center|right)
 * - overlay_color (hex)
 * - overlay_opacity (0-100)
 * - show_excerpt (bool)
 * - custom_excerpt (string)
 * - show_meta (bool)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$issue_id = isset( $args['issue_id'] ) ? (int) $args['issue_id'] : 0;
if ( ! $issue_id ) {
    return;
}

$thumb_id = get_post_thumbnail_id( $issue_id );
$bg_url   = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'full' ) : '';

$layout = isset( $args['layout'] ) && in_array( $args['layout'], array( 'left', 'center', 'right' ), true ) ? $args['layout'] : 'left';

$excerpt = ! empty( $args['custom_excerpt'] ) ? $args['custom_excerpt'] : get_the_excerpt( $issue_id );

$cats = get_the_category( $issue_id );
$cat_name = ! empty( $cats ) ? $cats[0]->name : '';
$cat_id = ! empty( $cats ) ? $cats[0]->term_id : 0;
$cat_link = $cat_id ? get_category_link( $cat_id ) : '';

$authors = array();
if ( function_exists( 'get_coauthors' ) ) {
	$authors = get_coauthors( $issue_id );
} else {
	$author_id = (int) get_post_field( 'post_author', $issue_id );
	if ( $author_id ) {
		$authors[] = get_userdata( $author_id );
	}
}
$date        = get_the_date( '', $issue_id );

$overlay_color   = isset( $args['overlay_color'] ) ? $args['overlay_color'] : '#000000';
$overlay_opacity = isset( $args['overlay_opacity'] ) ? (int) $args['overlay_opacity'] : 55;
$is_article      = 'article' === get_post_type( $issue_id );

?>
<section class="issue-hero layout-<?php echo esc_attr( $layout ); ?> <?php echo $is_article ? 'issue-hero--article' : 'issue-hero--post'; ?>" aria-label="Featured issue">
    <div class="issue-hero__bg" style="<?php echo $bg_url ? 'background-image:url(' . esc_url( $bg_url ) . ');' : ''; ?>"></div>
    <div class="issue-hero__overlay" style="--overlay-color: <?php echo esc_attr( $overlay_color ); ?>; --overlay-opacity: <?php echo esc_attr( $overlay_opacity ); ?>;"></div>
    <div class="issue-hero__inner">
        <div class="issue-hero__content">
        <?php
        // Override category for 'article' post type
        if ( 'article' === get_post_type( $issue_id ) ) :
            $issue_key = get_post_meta( $issue_id, 'issue_key', true );
            $magazine_url = function_exists( 'bn_get_issue_url' ) ? bn_get_issue_url( $issue_key ) : home_url( '/magazine/' );
            ?>
            <div class="issue-hero__kicker">
                <a href="<?php echo esc_url( $magazine_url ); ?>"><?php esc_html_e( 'From the Magazine', 'bn-newspack-child' ); ?></a>
            </div>
            <?php
        elseif ( $cat_name ) :
            ?>
            <div class="issue-hero__kicker">
                <?php if ( $cat_link ) : ?>
                    <a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat_name ); ?></a>
                <?php else : ?>
                    <?php echo esc_html( $cat_name ); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <h1 class="issue-hero__title"><a href="<?php echo esc_url( get_permalink( $issue_id ) ); ?>"><?php echo esc_html( get_the_title( $issue_id ) ); ?></a></h1>

        <?php if ( ! empty( $args['show_excerpt'] ) && $excerpt ) : ?>
            <p class="issue-hero__excerpt"><?php echo esc_html( $excerpt ); ?></p>
        <?php endif; ?>

        <?php if ( ! empty( $args['show_meta'] ) ) : ?>
            <div class="issue-hero__meta">
			<?php if ( ! empty( $authors ) ) : ?>
					<span class="issue-hero__avatar-wrap">
						<?php echo get_avatar( $authors[0]->ID, 32, '', '', array( 'class' => 'issue-hero__avatar' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
				<?php endif; ?>
				<?php if ( ! empty( $authors ) ) : ?>
					<span class="issue-hero__author">
						By
						<?php
						foreach ( $authors as $index => $author ) {
							if ( $index > 0 ) {
								echo $index === count( $authors ) - 1 ? esc_html__( ' and ', 'bn-newspack-child' ) : ', ';
							}
							?>
							<a href="<?php echo esc_url( get_author_posts_url( $author->ID, $author->user_nicename ) ); ?>"><?php echo esc_html( $author->display_name ); ?></a>
							<?php
						}
						?>
					</span>
				<?php endif; ?>
                <?php if ( $date ) : ?>
                    <span class="issue-hero__date"> • <?php echo esc_html( $date ); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        </div>
    </div>
    <button class="issue-hero__scroll-indicator" aria-label="Scroll to next section">
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="angle-down" viewBox="0 0 1152 1896.0833">
            <path d="M1075 736q0 13-10 23l-466 466q-10 10-23 10t-23-10L87 759q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393 393-393q10-10 23-10t23 10l50 50q10 10 10 23z"></path>
        </svg>
    </button>
</section>


