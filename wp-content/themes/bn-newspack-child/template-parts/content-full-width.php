<?php
/**
 * Template part for displaying full-width page content.
 *
 * Used by: page-full-width.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="entry-thumbnail">
			<?php the_post_thumbnail( 'post-thumbnail' ); ?>

			<?php if ( get_the_post_thumbnail_caption() ) : ?>
				<figcaption class="wp-caption-text">
					<?php echo wp_kses_post( get_the_post_thumbnail_caption() ); ?>
				</figcaption>
			<?php endif; ?>
		</figure>
	<?php endif; ?>

	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

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

