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

$position                 = isset( $attributes['contentPosition'] ) ? sanitize_key( $attributes['contentPosition'] ) : 'center';
$mobile_height            = isset( $attributes['mobileHeight'] ) ? esc_attr( $attributes['mobileHeight'] ) : '60vh';
$desktop_height           = isset( $attributes['desktopHeight'] ) ? esc_attr( $attributes['desktopHeight'] ) : '80vh';
$show_category            = isset( $attributes['showCategory'] ) ? (bool) $attributes['showCategory'] : true;
$show_date                = isset( $attributes['showDate'] ) ? (bool) $attributes['showDate'] : true;
$show_excerpt             = isset( $attributes['showExcerpt'] ) ? (bool) $attributes['showExcerpt'] : true;
$show_author              = isset( $attributes['showAuthor'] ) ? (bool) $attributes['showAuthor'] : true;
$overlay_gradient         = isset( $attributes['overlay'] ) ? esc_attr( $attributes['overlay'] ) : 'linear-gradient(180deg, rgba(0,0,0,0.5), rgba(0,0,0,0.3))';
$taxonomy                 = isset( $attributes['taxonomy'] ) ? sanitize_key( $attributes['taxonomy'] ) : 'category';
$image_position           = isset( $attributes['featuredImagePosition'] ) ? sanitize_key( $attributes['featuredImagePosition'] ) : 'behind';
$image_alignment          = isset( $attributes['imageAlignment'] ) ? sanitize_key( $attributes['imageAlignment'] ) : 'right';
$beside_background_raw    = isset( $attributes['besideBackgroundColor'] ) ? $attributes['besideBackgroundColor'] : '';
$beside_background_color  = $beside_background_raw ? sanitize_hex_color( $beside_background_raw ) : '';
$typography_scale_raw     = isset( $attributes['typographyScale'] ) ? $attributes['typographyScale'] : 1;
$typography_scale         = is_numeric( $typography_scale_raw ) ? (float) $typography_scale_raw : 1;

$featured_image_url = get_the_post_thumbnail_url( $post_id, 'full' );
$title              = get_the_title( $post );
$subheading         = get_post_meta( $post_id, 'subheading', true );
$permalink          = get_permalink( $post );

$terms        = get_the_terms( $post_id, $taxonomy );
$topic_output = '';
$topic_term   = null;
$topic_link   = '';

// Issue Key Logic for Article post type
if ( 'article' === get_post_type( $post_id ) ) {
    $issue_key = get_post_meta( $post_id, 'issue_key', true );
    if ( function_exists( 'bn_get_issue_name' ) ) {
        $issue_name = bn_get_issue_name( $issue_key );
        if ( $issue_name ) {
            $topic_output = $issue_name;
            $topic_link   = function_exists( 'bn_get_issue_url' ) ? bn_get_issue_url( $issue_key ) : home_url( '/magazine/' );
        }
    }
}

// Fallback to standard taxonomy term
if ( ! $topic_output && ! empty( $terms ) && ! is_wp_error( $terms ) ) {
    $topic_term   = array_shift( $terms );
    $topic_output = $topic_term ? $topic_term->name : '';
    $topic_link   = $topic_term ? get_term_link( $topic_term ) : '';
    if ( is_wp_error( $topic_link ) ) {
        $topic_link = '';
    }
}

$author_name = '';
if ( $show_author ) {
    $author      = get_user_by( 'ID', $post->post_author );
    $author_name = $author ? $author->display_name : '';
}

$date_display = '';
if ( $show_date ) {
    $date_display = get_the_date( '', $post_id );
}

// Build classes and style attributes shared by both layouts.
$classes = array(
    'bn-hero-header',
    'bn-hero-position-' . $position,
);

if ( 'beside' === $image_position ) {
    $classes[] = 'bn-hero-image-beside';
    if ( 'left' === $image_alignment ) {
        $classes[] = 'bn-hero-image-left';
    }
} else {
    $classes[] = 'bn-hero-image-behind';
}

$style = '--bn-hero-mobile-height:' . $mobile_height . ';--bn-hero-desktop-height:' . $desktop_height . ';--bn-hero-overlay:' . $overlay_gradient . ';';

if ( $beside_background_color ) {
    $style .= '--bn-hero-beside-bg:' . $beside_background_color . ';';
}

// Typography-related CSS variables.
$style .= '--bn-hero-scale:' . max( 0.3, min( 2, $typography_scale ) ) . ';';
$maybe_add_var = static function ( $attr_key, $var_name ) use ( $attributes, &$style ) {
    if ( ! empty( $attributes[ $attr_key ] ) ) {
        $style .= $var_name . ':' . esc_attr( $attributes[ $attr_key ] ) . ';';
    }
};

$maybe_add_var( 'titleColor', '--bn-hero-title-color' );
$maybe_add_var( 'titleBackgroundColor', '--bn-hero-title-bg' );

$maybe_add_var( 'categoryColor', '--bn-hero-category-color' );
$maybe_add_var( 'categoryBackgroundColor', '--bn-hero-category-bg' );

$maybe_add_var( 'subheadingColor', '--bn-hero-subheading-color' );
$maybe_add_var( 'subheadingBackgroundColor', '--bn-hero-subheading-bg' );

$maybe_add_var( 'authorColor', '--bn-hero-author-color' );
$maybe_add_var( 'authorBackgroundColor', '--bn-hero-author-bg' );

$maybe_add_var( 'dateColor', '--bn-hero-date-color' );
$maybe_add_var( 'dateBackgroundColor', '--bn-hero-date-bg' );

$wrapper_attributes = get_block_wrapper_attributes(
    array(
        'class' => implode( ' ', array_map( 'sanitize_html_class', $classes ) ),
        'style' => $style,
    )
);
?>

<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php if ( 'beside' === $image_position ) : ?>
        <div class="bn-hero-beside-inner">
            <div class="bn-hero-beside-text">
                <div class="bn-hero-content">
                    <?php if ( $show_category && $topic_output && $topic_link ) : ?>
                            <div class="issue-hero__kicker bn-hero-topic">
                                <a href="<?php echo esc_url( $topic_link ); ?>"><?php echo esc_html( $topic_output ); ?></a>
                            </div>
                    <?php endif; ?>

                    <h1 class="bn-hero-title">
                        <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                    </h1>

                    <?php if ( $show_excerpt && $subheading ) : ?>
                        <div class="bn-hero-excerpt"><?php echo esc_html( $subheading ); ?></div>
                    <?php endif; ?>

                    <?php if ( ( $show_author ) || $date_display ) : ?>
                        <div class="entry-meta">
                            <?php if ( $show_author ) : ?>
                                <?php
                                $authors = array();
                                if ( function_exists( 'get_coauthors' ) ) {
                                    $authors = get_coauthors( $post_id );
                                } else {
                                    $user = get_user_by( 'ID', $post->post_author );
                                    if ( $user ) {
                                        $authors[] = $user;
                                    }
                                }
                                ?>
                                <div class="byline-container">
                                    <?php
                                    if ( ! empty( $authors ) ) {
                                        echo get_avatar( $authors[0]->ID, 40 );
                                    }
                                    ?>
                                    <span class="byline">
                                        <span class="author-prefix"><?php echo esc_html__( 'By', 'bn-newspack-child' ); ?></span>
                                        <span class="author vcard">
                                            <?php
                                            foreach ( $authors as $index => $author ) {
                                                if ( $index > 0 ) {
                                                    echo $index === count( $authors ) - 1 ? esc_html__( ' and ', 'bn-newspack-child' ) : ', ';
                                                }
                                                $author_link = get_author_posts_url( $author->ID, $author->user_nicename );
                                                ?>
                                                <a href="<?php echo esc_url( $author_link ); ?>" class="url fn n" rel="author">
                                                    <?php echo esc_html( $author->display_name ); ?>
                                                </a>
                                                <?php
                                            }
                                            ?>
                                        </span>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if ( $date_display ) : ?>
                                <time class="entry-date published"><?php echo esc_html( $date_display ); ?></time>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ( $featured_image_url ) : ?>
                <div class="bn-hero-beside-image">
                    <img src="<?php echo esc_url( $featured_image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
                </div>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <?php if ( $featured_image_url ) : ?>
            <div class="bn-hero-bg" style="background-image: url(<?php echo esc_url( $featured_image_url ); ?>);" role="img" aria-label="<?php echo esc_attr( $title ); ?>"></div>
        <?php endif; ?>

        <div class="bn-hero-overlay"></div>

        <div class="bn-hero-content">
            <?php if ( $show_category && $topic_output && $topic_link ) : ?>
                    <div class="issue-hero__kicker bn-hero-topic">
                        <a href="<?php echo esc_url( $topic_link ); ?>"><?php echo esc_html( $topic_output ); ?></a>
                    </div>
            <?php endif; ?>

            <h1 class="bn-hero-title">
                <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
            </h1>

            <?php if ( $show_excerpt && $subheading ) : ?>
                <div class="bn-hero-excerpt"><?php echo esc_html( $subheading ); ?></div>
            <?php endif; ?>

            <?php if ( ( $show_author ) || $date_display ) : ?>
                <div class="entry-meta">
                    <?php if ( $show_author ) : ?>
                        <?php
                        $authors = array();
                        if ( function_exists( 'get_coauthors' ) ) {
                            $authors = get_coauthors( $post_id );
                        } else {
                            $user = get_user_by( 'ID', $post->post_author );
                            if ( $user ) {
                                $authors[] = $user;
                            }
                        }
                        ?>
                        <div class="byline-container">
                            <?php
                            if ( ! empty( $authors ) ) {
                                echo get_avatar( $authors[0]->ID, 40 );
                            }
                            ?>
                            <span class="byline">
                                <span class="author-prefix"><?php echo esc_html__( 'By', 'bn-newspack-child' ); ?></span>
                                <span class="author vcard">
                                    <?php
                                    foreach ( $authors as $index => $author ) {
                                        if ( $index > 0 ) {
                                            echo $index === count( $authors ) - 1 ? esc_html__( ' and ', 'bn-newspack-child' ) : ', ';
                                        }
                                        $author_link = get_author_posts_url( $author->ID, $author->user_nicename );
                                        ?>
                                        <a href="<?php echo esc_url( $author_link ); ?>" class="url fn n" rel="author">
                                            <?php echo esc_html( $author->display_name ); ?>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </span>
                            </span>
                        </div>
                    <?php endif; ?>
                    <?php if ( $date_display ) : ?>
                        <time class="entry-date published"><?php echo esc_html( $date_display ); ?></time>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

