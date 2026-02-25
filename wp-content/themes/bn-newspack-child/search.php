<?php
/**
 * The template file for search results.
 * Handles both standard WordPress search and SearchWP plugin searches.
 * Styled to match Newspack Homepage Articles block.
 *
 * @package Bay Nature (Newspack Child)
 */

get_header();

// Determine search type and query
$is_searchwp    = isset( $_GET['swps'] ) && class_exists( '\\SearchWP\\Query' );
$search_query   = '';
$search_results = array();
$max_pages      = 1;
$current_page   = 1;
$per_page       = 10;

if ( $is_searchwp ) {
	// SearchWP search
	$search_query = sanitize_text_field( $_GET['swps'] );
	$current_page = isset( $_GET['swppg'] ) ? absint( $_GET['swppg'] ) : 1;
	
	$searchwp_query = new \SearchWP\Query( $search_query, array(
		'engine'   => 'default',
		'fields'   => 'all',
		'page'     => $current_page,
		'per_page' => $per_page,
	) );
	
	$search_results = $searchwp_query->get_results();
	$max_pages      = $searchwp_query->max_num_pages;
	$has_results    = ! empty( $search_results );
} else {
	// Standard WordPress search
	$search_query = get_search_query();
	$has_results  = have_posts();
}
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main container" role="main">

		<?php if ( $has_results ) : ?>

			<div class="search-wrap">

				<h1 class="search-term">
					<?php esc_html_e( 'Search Bay Nature', 'bn-newspack-child' ); ?>
				</h1>

				<?php 
				// Display search form with pre-filled value
				?>
				<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label for="search-field-main" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'bn-newspack-child' ); ?></label>
					<input type="search" id="search-field-main" class="search-field" placeholder="<?php esc_attr_e( 'Search &hellip;', 'bn-newspack-child' ); ?>" value="<?php echo esc_attr( $search_query ); ?>" name="s" />
					<button type="submit" class="search-submit">
						<svg class="svg-icon" width="28" height="28" aria-hidden="true" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" /><path d="M0 0h24v24H0z" fill="none" /></svg>
						<span class="screen-reader-text"><?php esc_html_e( 'Search', 'bn-newspack-child' ); ?></span>
					</button>
				</form>

				<!-- Search results styled like wpnbha Homepage Articles block -->
				<div class="wp-block-newspack-blocks-homepage-articles wpnbha search-results-wpnbha is-style-borders show-image image-alignleft ts-3 is-1 mobile-stack show-category">
					<div data-posts>

					<?php
					if ( $is_searchwp ) :
						// SearchWP results loop
						foreach ( $search_results as $result ) :
							// Handle different result types
							if ( $result instanceof \WP_Post ) {
								$post = $result;
							} elseif ( isset( $result->id ) ) {
								$post = get_post( $result->id );
							} else {
								continue;
							}
							
							if ( ! $post ) {
								continue;
							}
							
							setup_postdata( $post );
							$post_id   = $post->ID;
							$post_type = get_post_type( $post );
							
							// Build article classes
							$article_classes   = array( 'search-result-item' );
							$article_classes[] = 'type-' . $post_type;
							
							if ( has_post_thumbnail( $post_id ) ) {
								$article_classes[] = 'post-has-image';
							}
							
							// Add term classes (categories/tags)
							$categories = get_the_category( $post_id );
							if ( ! empty( $categories ) ) {
								foreach ( $categories as $category ) {
									$article_classes[] = 'category-' . $category->slug;
								}
							}
							
							// Check if this is a "From the Magazine" category post
							if ( ! empty( $categories ) ) {
								foreach ( $categories as $cat ) {
									if ( stripos( $cat->slug, 'magazine' ) !== false || stripos( $cat->name, 'magazine' ) !== false ) {
										$article_classes[] = 'category-from-the-magazine';
										break;
									}
								}
							}
							
							bn_render_search_result_article( $post, $article_classes, $categories );
							
						endforeach;
						wp_reset_postdata();
						
					else :
						// Standard WordPress loop
						while ( have_posts() ) :
							the_post();
							
							$post_id   = get_the_ID();
							$post_type = get_post_type();
							
							// Build article classes
							$article_classes   = array( 'search-result-item' );
							$article_classes[] = 'type-' . $post_type;
							
							if ( has_post_thumbnail() ) {
								$article_classes[] = 'post-has-image';
							}
							
							// Add term classes (categories/tags)
							$categories = get_the_category( $post_id );
							if ( ! empty( $categories ) ) {
								foreach ( $categories as $category ) {
									$article_classes[] = 'category-' . $category->slug;
								}
							}
							
							// Check if this is a "From the Magazine" category post
							if ( ! empty( $categories ) ) {
								foreach ( $categories as $cat ) {
									if ( stripos( $cat->slug, 'magazine' ) !== false || stripos( $cat->name, 'magazine' ) !== false ) {
										$article_classes[] = 'category-from-the-magazine';
										break;
									}
								}
							}
							
							bn_render_search_result_article( get_post(), $article_classes, $categories );
							
						endwhile;
					endif;
					?>

					</div>
				</div><!-- .wpnbha -->

			</div><!-- .search-wrap -->

			<nav class="pagination">
				<?php
				if ( $is_searchwp && $max_pages > 1 ) :
					// SearchWP pagination
					$pagination_args = array(
						'base'      => add_query_arg( 'swppg', '%#%' ),
						'format'    => '',
						'current'   => $current_page,
						'total'     => $max_pages,
						'prev_text' => __( '&laquo; Previous', 'bn-newspack-child' ),
						'next_text' => __( 'Next &raquo;', 'bn-newspack-child' ),
						'mid_size'  => 2,
					);
					echo '<div class="nav-links">' . paginate_links( $pagination_args ) . '</div>';
				else :
					// Standard WordPress pagination
					the_posts_pagination( array(
						'mid_size'  => 2,
						'prev_text' => __( '&laquo; Previous', 'bn-newspack-child' ),
						'next_text' => __( 'Next &raquo;', 'bn-newspack-child' ),
					) );
				endif;
				?>
			</nav>

			<?php wp_reset_postdata(); ?>

		<?php else : ?>

			<div class="search-wrap">
				<h1 class="search-term">
					<?php esc_html_e( 'Search Bay Nature', 'bn-newspack-child' ); ?>
				</h1>

				<?php 
				// Display search form with pre-filled value
				?>
				<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label for="search-field-no-results" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'bn-newspack-child' ); ?></label>
					<input type="search" id="search-field-no-results" class="search-field" placeholder="<?php esc_attr_e( 'Search &hellip;', 'bn-newspack-child' ); ?>" value="<?php echo esc_attr( $search_query ); ?>" name="s" />
					<button type="submit" class="search-submit">
						<svg class="svg-icon" width="28" height="28" aria-hidden="true" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" /><path d="M0 0h24v24H0z" fill="none" /></svg>
						<span class="screen-reader-text"><?php esc_html_e( 'Search', 'bn-newspack-child' ); ?></span>
					</button>
				</form>

				<section class="no-results not-found">
					<header class="page-header">
						<h2 class="page-title"><?php esc_html_e( 'Nothing Found', 'bn-newspack-child' ); ?></h2>
					</header>

					<div class="page-content">
						<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'bn-newspack-child' ); ?></p>
					</div>
				</section>
			</div>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();

/**
 * Render a search result article.
 *
 * @param WP_Post $post            The post object.
 * @param array   $article_classes Article CSS classes.
 * @param array   $categories      Post categories.
 */
function bn_render_search_result_article( $post, $article_classes, $categories ) {
	$post_id = $post->ID;
	?>
	<article id="post-<?php echo esc_attr( $post_id ); ?>" class="<?php echo esc_attr( implode( ' ', $article_classes ) ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>">
		
		<?php if ( has_post_thumbnail( $post_id ) ) : ?>
			<figure class="post-thumbnail">
				<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" rel="bookmark" tabindex="-1" aria-hidden="true">
					<?php echo get_the_post_thumbnail( $post_id, 'newspack-article-block-landscape-small', array(
						'alt' => trim( wp_strip_all_tags( get_the_title( $post_id ) ) ),
					) ); ?>
				</a>
			</figure>
		<?php endif; ?>

		<div class="entry-wrapper">
			<?php
			// Display category or issue name for articles
			$category_displayed = false;
			
			// For article post type, show issue name + primary category
			if ( 'article' === get_post_type( $post_id ) ) {
				$issue_key = get_post_meta( $post_id, 'issue_key', true );
				$issue_name = function_exists( 'bn_get_issue_name' ) ? bn_get_issue_name( $issue_key ) : null;
				if ( $issue_name ) {
					$issue_url = function_exists( 'bn_get_issue_url' ) ? bn_get_issue_url( $issue_key ) : home_url( '/magazine/' );
					$primary_cat = function_exists( 'bn_get_primary_category' ) ? bn_get_primary_category( $post_id ) : null;
					?>
					<div class="cat-links">
						<a class="issue-cat-link" href="<?php echo esc_url( $issue_url ); ?>">
							<?php echo esc_html( $issue_name ); ?>
						</a>
						<?php if ( $primary_cat ) : ?>
							<span class="cat-sep">|</span>
							<a href="<?php echo esc_url( get_category_link( $primary_cat->term_id ) ); ?>">
								<?php echo esc_html( $primary_cat->name ); ?>
							</a>
						<?php endif; ?>
					</div>
					<?php
					$category_displayed = true;
				}
			}
			
			// Fall back to standard category display
			if ( ! $category_displayed ) {
				$primary_category = null;
				
				// Try to get Yoast primary category first
				if ( class_exists( 'WPSEO_Primary_Term' ) ) {
					$primary_term = new WPSEO_Primary_Term( 'category', $post_id );
					$category_id  = $primary_term->get_primary_term();
					if ( $category_id ) {
						$primary_category = get_term( $category_id );
					}
				}
				
				// Fall back to first category
				if ( ! $primary_category && ! empty( $categories ) ) {
					$primary_category = $categories[0];
				}
				
				if ( $primary_category && is_a( $primary_category, 'WP_Term' ) ) :
				?>
					<div class="cat-links">
						<a href="<?php echo esc_url( get_category_link( $primary_category->term_id ) ); ?>">
							<?php echo esc_html( $primary_category->name ); ?>
						</a>
					</div>
				<?php endif;
			}
			?>
			
			<h2 class="entry-title">
				<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" rel="bookmark">
					<?php echo esc_html( get_the_title( $post_id ) ); ?>
				</a>
			</h2>
			
			<p class="entry-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $post_id ), 30, '...' ) ); ?></p>
			
			<div class="entry-meta">
				<?php
				// Author display
				if ( function_exists( 'get_coauthors' ) ) :
					$coauthors    = get_coauthors( $post_id );
					$author_count = count( $coauthors );
					if ( $author_count > 0 ) :
					?>
					<span class="byline">
						<span class="author-prefix"><?php esc_html_e( 'by', 'bn-newspack-child' ); ?></span>
						<?php
						$i = 0;
						foreach ( $coauthors as $coauthor ) :
							$i++;
							$author_url = get_author_posts_url( $coauthor->ID, $coauthor->user_nicename );
							?>
							<span class="author vcard">
								<a class="url fn n" href="<?php echo esc_url( $author_url ); ?>">
									<?php echo esc_html( $coauthor->display_name ); ?>
								</a>
							</span><?php
							if ( $i < $author_count - 1 ) {
								echo ', ';
							} elseif ( $i === $author_count - 1 ) {
								esc_html_e( ' and ', 'bn-newspack-child' );
							}
						endforeach;
						?>
					</span>
					<?php
					endif;
				else :
					$author_id = get_post_field( 'post_author', $post_id );
					?>
					<span class="byline">
						<span class="author-prefix"><?php esc_html_e( 'by', 'bn-newspack-child' ); ?></span>
						<span class="author vcard">
							<a class="url fn n" href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
								<?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?>
							</a>
						</span>
					</span>
				<?php endif; ?>
				
				<time class="entry-date published" datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $post_id ) ); ?>">
					<?php echo esc_html( get_the_date( '', $post_id ) ); ?>
				</time>
			</div>
		</div><!-- .entry-wrapper -->
		
	</article>
	<?php
}
