<?php
/**
 * The Member Only Content template file.
 * Template Name: Member Only Content No Banner Template
 * Template Post Type: article, post
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Crate
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
		while ( have_posts() ) : the_post();

			get_template_part( 'template-parts/content', 'member-only-no-banner' );

		//the_post_navigation();

		// Comments are disabled - template call removed to hide comments from visitors
		// if ( comments_open() || get_comments_number() ) :
		// 	comments_template();
		// endif;

		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
