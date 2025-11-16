<?php
/**
 * Template part for displaying full-width page content.
 *
 * Used by: page-full-width.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get title position preference (default: overlay)
$title_position = get_post_meta( get_the_ID(), '_bn_title_position', true );
$title_position = $title_position ? $title_position : 'overlay';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="page-hero">
			<div class="hero-wrap" style="background-image: url(<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>);">
				<div class="hero-overlay"></div>
				<?php if ( $title_position === 'overlay' ) : ?>
					<div class="hero-content">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( get_the_post_thumbnail_caption() ) : ?>
				<figcaption class="wp-caption-text">
					<?php echo wp_kses_post( get_the_post_thumbnail_caption() ); ?>
				</figcaption>
			<?php endif; ?>
		</figure>
		
		<?php if ( $title_position === 'below' ) : ?>
			<header class="entry-header entry-header-below-hero">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</header><!-- .entry-header -->
		<?php endif; ?>
	<?php else : ?>
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'bn-newspack-child' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->

