<?php
/**
 * The template file for search results.
 * Handles both standard WordPress search and SearchWP plugin searches.
 *
 * @package Bay Nature (Newspack Child)
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main container" role="main">

		<?php
		// Check if this is a SearchWP query or standard WordPress search
		$search_query = '';
		if ( isset( $_GET['swps'] ) ) {
			// SearchWP query
			$search_query = sanitize_text_field( $_GET['swps'] );
		} elseif ( isset( $_GET['s'] ) ) {
			// Standard WordPress search
			$search_query = get_search_query();
		}

		if ( have_posts() ) :
		?>

			<div class="search-wrap">

				<h1 class="search-term">
					<?php 
					if ( $search_query ) {
						echo esc_html__( 'You searched for ', 'bn-newspack-child' ) . '<strong>' . esc_html( $search_query ) . '</strong>'; 
					} else {
						echo esc_html__( 'Search Results', 'bn-newspack-child' );
					}
					?>
				</h1>

				<?php 
				// Display search form
				if ( function_exists( 'get_search_form' ) ) {
					get_search_form();
				}
				?>

				<div class="search-results">

					<?php
					/* Start the Loop */
					while ( have_posts() ) :
						the_post();
						
						// Use parent theme's content-loop template if available
						if ( locate_template( 'template-parts/content-loop.php' ) ) {
							get_template_part( 'template-parts/content', 'loop' );
						} else {
							// Fallback to basic post display
							?>
							<article id="post-<?php the_ID(); ?>" <?php post_class( 'in-loop' ); ?>>
								
								<?php if ( has_post_thumbnail() ) : ?>
									<figure>
										<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
									</figure>
								<?php endif; ?>

								<div>
									<header class="entry-header">
										<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
										
										<div class="entry-meta">
											<span class="entry-date"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></span>
											<?php if ( function_exists( 'coauthors_posts_links' ) ) : ?>
												&nbsp;•&nbsp;<span class="entry-author"><?php esc_html_e( 'by ', 'bn-newspack-child' ); coauthors_posts_links(); ?></span>
											<?php endif; ?>
										</div>
									</header>

									<div class="entry-content">
										<?php the_excerpt(); ?>
									</div>
								</div>

							</article>
							<?php
						}

					endwhile;
					?>

				</div>

			</div><!-- .search-wrap -->

			<nav class="pagination">
				<?php
				// Pagination
				the_posts_pagination( array(
					'mid_size'  => 2,
					'prev_text' => __( '&laquo; Previous', 'bn-newspack-child' ),
					'next_text' => __( 'Next &raquo;', 'bn-newspack-child' ),
				) );
				?>
			</nav>

			<?php wp_reset_postdata(); ?>

		<?php
		else :
			// No results found
			if ( locate_template( 'template-parts/content-none.php' ) ) {
				get_template_part( 'template-parts/content', 'none' );
			} else {
				?>
				<section class="no-results not-found">
					<header class="page-header">
						<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'bn-newspack-child' ); ?></h1>
					</header>

					<div class="page-content">
						<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'bn-newspack-child' ); ?></p>
						<?php get_search_form(); ?>
					</div>
				</section>
				<?php
			}

		endif;
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();

