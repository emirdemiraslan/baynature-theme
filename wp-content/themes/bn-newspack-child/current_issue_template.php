<?php
/**
 * The template for displaying the current issue content template
 * Template Name: Magazine Issue Page
 *
 *
 * 11-14-2023
 * version 0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) :
			the_post();

			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<header class="entry-header entry-header-centered">
					<?php
					the_title( '<h1 class="entry-title">', '</h1>' );

					$subheading = get_post_meta( get_the_ID(), 'subheading', true );

					if ( $subheading ) :
						?>
						<h2 class="entry-subtitle"><?php echo esc_html( $subheading ); ?></h2>
					<?php endif; ?>
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

					<a href="https://baynature.app.neoncrm.com/np/clients/baynature/giftstore.jsp">Buy a print copy of this issue at the BN Store</a><br><br>
					<?php
					$issueKey = get_field( 'current_issue_key' );
					//echo "issue key = ".$issueKey;
					currentIssueRenderPosts( $issueKey );
					?>
				</div><!-- .entry-content -->

			</article><!-- #post-<?php the_ID(); ?> -->

		<?php endwhile; ?>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

