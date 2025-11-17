<?php
/**
 * Template Name: Magazine Archive Page
 *
 * Displays magazine archive entries using the full-width layout.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'full-width' );
			?>
			<div class="entry-content issue-archive-grid">
				<?php render_issue_archive_issues(); ?>
			</div>
			<?php
		endwhile;
		?>
	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
