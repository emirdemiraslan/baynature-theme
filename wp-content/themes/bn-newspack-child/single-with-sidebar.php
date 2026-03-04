<?php
/**
 * Template Name: With Sidebar
 * Template Post Type: post, page, article
 *
 * A two-column layout with a right sidebar (750 px main + 350 px sidebar).
 *
 * @package BN_Newspack_Child
 */

get_header();
?>

	<section id="primary" class="content-area with-sidebar-layout <?php echo esc_attr( newspack_get_category_tag_classes( get_the_ID() ) ); ?>">
		<main id="main" class="site-main">

			<?php
			while ( have_posts() ) :
				the_post();

				if ( in_array( newspack_featured_image_position(), array( 'large', 'behind', 'beside', 'above' ), true ) ) :
					get_template_part( 'template-parts/post/large-featured-image' );
				else :
				?>
					<header class="entry-header">
						<?php get_template_part( 'template-parts/header/entry', 'header' ); ?>
					</header>
				<?php endif; ?>

				<div class="with-sidebar-container">
					<div class="main-content">
						<?php
						if ( is_active_sidebar( 'article-1' ) && is_single() ) {
							dynamic_sidebar( 'article-1' );
						}

						if ( 'small' === newspack_featured_image_position() ) {
							newspack_post_thumbnail( 'newspack-featured-image-small' );
						}

						if ( is_page() ) {
							get_template_part( 'template-parts/content/content', 'page' );
						} else {
							get_template_part( 'template-parts/content/content', 'single' );
						}

						newspack_previous_next();

						if ( comments_open() || get_comments_number() ) {
							newspack_comments_template();
						}
						?>
					</div>

					<aside class="sidebar-right">
						<?php
						if ( is_active_sidebar( 'sidebar-with-sidebar' ) ) {
							dynamic_sidebar( 'sidebar-with-sidebar' );
						}
						?>
					</aside>
				</div>

			<?php endwhile; ?>

		</main>
	</section>

<?php
get_footer();
