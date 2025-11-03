<?php
/**
 * Hero Header block server-side rendering.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$post_id = isset( $attributes['postId'] ) ? absint( $attributes['postId'] ) : 0;
if ( ! $post_id ) {
    // Fallback: latest post.
    $latest = get_posts( array( 'posts_per_page' => 1, 'post_status' => 'publish' ) );
    if ( ! empty( $latest ) ) {
        $post_id = $latest[0]->ID;
    }
}

if ( ! $post_id ) {
    return '';
}

$post = get_post( $post_id );
if ( ! $post ) {
    return '';
}

$position         = isset( $attributes['contentPosition'] ) ? sanitize_key( $attributes['contentPosition'] ) : 'center';
$mobile_height    = isset( $attributes['mobileHeight'] ) ? esc_attr( $attributes['mobileHeight'] ) : '60vh';
$desktop_height   = isset( $attributes['desktopHeight'] ) ? esc_attr( $attributes['desktopHeight'] ) : '80vh';
$show_excerpt     = isset( $attributes['showExcerpt'] ) ? (bool) $attributes['showExcerpt'] : true;
$show_author      = isset( $attributes['showAuthor'] ) ? (bool) $attributes['showAuthor'] : true;
$overlay_gradient = isset( $attributes['overlay'] ) ? esc_attr( $attributes['overlay'] ) : 'linear-gradient(180deg, rgba(0,0,0,0.5), rgba(0,0,0,0.3))';
$taxonomy         = isset( $attributes['taxonomy'] ) ? sanitize_key( $attributes['taxonomy'] ) : 'category';

$featured_image_url = get_the_post_thumbnail_url( $post_id, 'full' );
$title              = get_the_title( $post );
$excerpt            = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( $post->post_content, 30 );
$permalink          = get_permalink( $post );

$terms        = get_the_terms( $post_id, $taxonomy );
$topic_output = '';
if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
    $topic_output = esc_html( $terms[0]->name );
}

$author_name = '';
if ( $show_author ) {
    $author      = get_user_by( 'ID', $post->post_author );
    $author_name = $author ? $author->display_name : '';
}

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'bn-hero-header bn-hero-position-' . $position,
    'style' => '--bn-hero-mobile-height:' . $mobile_height . ';--bn-hero-desktop-height:' . $desktop_height . ';--bn-hero-overlay:' . $overlay_gradient . ';',
) );
?>

<div <?php echo $wrapper_attributes; ?>>
    <?php if ( $featured_image_url ) : ?>
        <div class="bn-hero-bg" style="background-image: url(<?php echo esc_url( $featured_image_url ); ?>);" role="img" aria-label="<?php echo esc_attr( $title ); ?>"></div>
    <?php endif; ?>

    <div class="bn-hero-overlay"></div>

    <div class="bn-hero-content">
        <?php if ( $topic_output ) : ?>
            <div class="bn-hero-topic"><?php echo esc_html( $topic_output ); ?></div>
        <?php endif; ?>

        <h1 class="bn-hero-title">
            <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
        </h1>

        <?php if ( $show_excerpt && $excerpt ) : ?>
            <div class="bn-hero-excerpt"><?php echo esc_html( $excerpt ); ?></div>
        <?php endif; ?>

        <?php if ( $show_author && $author_name ) : ?>
            <div class="bn-hero-meta">
                <span class="bn-hero-author"><?php echo esc_html__( 'By', 'bn-newspack-child' ) . ' ' . esc_html( $author_name ); ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>

