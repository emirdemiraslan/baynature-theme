<?php
/**
 * Template part for displaying member-only content with featured image
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bn-newspack-child
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail() && is_singular() ) : ?>

	<figure class="featured-image">
		<div class="hero-wrap" style="background-image: url(<?php echo esc_url( get_the_post_thumbnail_url() ); ?>);">
			<?php the_post_thumbnail( 'large' ); ?>
		</div>
		<figcaption><?php the_post_thumbnail_caption(); ?></figcaption>
		
	</figure>

	<?php endif; ?>
	
	<?php 
	// Display issue bar if available (from Crate theme)
	if ( locate_template( 'template-parts/partials/issue-bar.php' ) ) {
		get_template_part( 'template-parts/partials/issue-bar' );
	}
	?>

	<header class="entry-header">
		<?php if ( is_singular() ) { ?>
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			<?php if ( get_post_meta( get_the_ID(), 'subheading', true ) ) { ?>
				<h2 class="entry-subtitle"><?php echo esc_html( get_post_meta( get_the_ID(), 'subheading', true ) ); ?></h2>
			<?php } ?>
		<?php } else { ?>
			<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
		<?php } ?>
		
		<?php if ( is_singular( array( 'post', 'article' ) ) ) { ?>
			<div class="entry-source">

				<span class="byline">
					<?php
						echo esc_html( 'by ' );
					if ( function_exists( 'coauthors_posts_links' ) ) {
						coauthors_posts_links();
					} else {
						the_author();
					}
					?>
				</span>

				<div class="meta-group">

					<div class="meta-date">
						<?php echo esc_html( get_the_date() ); ?>
					</div>

					<?php if ( get_post_meta( get_the_ID(), 'sponsor', true ) ) { ?>
						<div class="meta-sponsor">
							<?php if ( get_post_meta( get_the_ID(), 'sponsor_link', true ) ) { ?>
								<a href="<?php echo esc_url( get_post_meta( get_the_ID(), 'sponsor_link', true ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( 'Sponsored by ' . get_post_meta( get_the_ID(), 'sponsor', true ) ); ?></a>
							<?php } else { ?>
								<?php echo esc_html( 'Sponsored by ' . get_post_meta( get_the_ID(), 'sponsor', true ) ); ?>
							<?php } ?>
						</div>
					<?php } ?>

				</div><!-- .meta-group -->

			</div><!-- .entry-meta -->
		<?php } ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
		// Note: Paywall logic is now handled by the_content filter in inc/paywall/gate.php
		// This template just calls the_content() and the filter will inject the gate/CTA automatically
		the_content();
		?>
	</div><!-- .entry-content -->
	
	<?php 
	// Display author box if available (from Crate theme)
	if ( locate_template( 'template-parts/partials/author.php' ) ) {
		get_template_part( 'template-parts/partials/author' );
	}
	?>

	<?php 
	// Display subscribe CTA if available (from Crate theme)
	if ( locate_template( 'template-parts/partials/subscribe.php' ) ) {
		get_template_part( 'template-parts/partials/subscribe' );
	}
	?>

	<footer class="entry-footer">
		<div class="post-terms">
			<?php
				// Get ALL the terms across all taxonomies
			?>
		</div>

	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

