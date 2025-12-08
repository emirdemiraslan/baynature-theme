<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * Uses the custom 404 page content from Site Options if available,
 * with a one-column layout and SearchWP-styled search form.
 *
 * @package Bay Nature (Newspack Child)
 */

get_header();
?>

<section id="primary" class="content-area">
	<main id="main" class="site-main">

		<?php
		// Check if we have custom 404 page content (set up in site-options.php)
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'error-404 not-found' ); ?>>
					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<?php
						// Get the content and filter out any existing search forms
						$content = get_the_content();
						
						// Remove WordPress core search block
						$content = preg_replace( '/<!-- wp:search.*?\/-->/s', '', $content );
						
						// Remove any get_search_form() output patterns
						$content = preg_replace( '/<form[^>]*role="search"[^>]*>.*?<\/form>/s', '', $content );
						
						// Apply filters and output content
						echo apply_filters( 'the_content', $content );
						
						// Output the SearchWP styled search form
						bn_render_404_search_form();
						?>
					</div><!-- .entry-content -->
				</article><!-- .error-404 -->
				<?php
			endwhile;
		else :
			// Fallback if no custom 404 page is set
			?>
			<article class="error-404 not-found">
				<header class="entry-header">
					<h1 class="entry-title"><?php esc_html_e( 'Page Not Found', 'bn-newspack-child' ); ?></h1>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<p><?php esc_html_e( 'It looks like nothing was found at this location. Try searching for what you\'re looking for:', 'bn-newspack-child' ); ?></p>
					
					<?php bn_render_404_search_form(); ?>
				</div><!-- .entry-content -->
			</article><!-- .error-404 -->
			<?php
		endif;
		?>

	</main><!-- #main -->
</section><!-- #primary -->

<?php
get_footer();

/**
 * Render the SearchWP search form for the 404 page.
 * Uses the exact same form as the overlay menu.
 */
function bn_render_404_search_form() {
	?>
	<div class="bn-overlay-search-section bn-404-search-section">
		<?php
		if ( class_exists( '\\SearchWP\\Forms\\Frontend' ) ) {
			echo \SearchWP\Forms\Frontend::render( [ 'id' => 1 ] );
		} else {
			// Fallback to WordPress search if SearchWP is not available
			get_search_form();
		}
		?>
	</div>
	<script>
	(function() {
		var searchInput = document.querySelector('.bn-404-search-section input[type="search"], .bn-404-search-section input[name="swps"], .bn-404-search-section input[name="s"]');
		if (searchInput && !searchInput.placeholder) {
			searchInput.placeholder = '<?php echo esc_js( __( 'Search', 'bn-newspack-child' ) ); ?>';
		}
	})();
	</script>
	<?php
}
