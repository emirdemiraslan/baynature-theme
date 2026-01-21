<?php
/**
 * Template Name: Bay Nature Talks Paywall Page
 * Template Post Type: page, post
 *
 * Template for displaying Bay Nature Talks content with member access control.
 *
 * @package Bay Nature Newspack Child
 */

get_header();
?>

<section id="primary" class="content-area">
	<main id="main" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="page-hero">
						<div class="hero-wrap" style="background-image: url(<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>);">
							<div class="hero-overlay"></div>
							<div class="hero-content">
								<h1 class="entry-title"><?php the_title(); ?></h1>
								<?php if ( get_post_meta( get_the_ID(), 'subheading', true ) ) : ?>
									<p class="entry-subtitle"><?php echo esc_html( get_post_meta( get_the_ID(), 'subheading', true ) ); ?></p>
								<?php endif; ?>
							</div>
						</div>
						<?php if ( get_the_post_thumbnail_caption() ) : ?>
							<figcaption class="wp-caption-text">
								<?php echo wp_kses_post( get_the_post_thumbnail_caption() ); ?>
							</figcaption>
						<?php endif; ?>
					</figure>
				<?php else : ?>
					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
						<?php if ( get_post_meta( get_the_ID(), 'subheading', true ) ) : ?>
							<p class="entry-subtitle"><?php echo esc_html( get_post_meta( get_the_ID(), 'subheading', true ) ); ?></p>
						<?php endif; ?>
					</header>
				<?php endif; ?>

				<div class="entry-content">
					<?php 
					if ( bn_is_subscriber() ) {
						// Member has access - show full content
						the_content();

						wp_link_pages(
							array(
								'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'bn-newspack-child' ),
								'after'  => '</div>',
							)
						);
					} else {
						// Not a member - show paywall message
						$join_url = function_exists('get_field') ? get_field('paywall_become_a_member_link', 'option') : '';
						$join_url = $join_url ? $join_url : '/join';
						$login_url = function_exists('get_field') ? get_field('paywall_login_link', 'option') : '';
						$login_url = $login_url ? $login_url : 'https://baynature.app.neoncrm.com/np/clients/baynature/neonPage.jsp?pageId=38&';
						?>
						<div class="bn-paywall-message talks-paywall">
							<div class="bn-paywall-content">
								<h2><?php esc_html_e( 'Members-Only Content', 'bn-newspack-child' ); ?></h2>
								<p><?php esc_html_e( 'Bay Nature Talks are exclusive to our members. Join our community to access this event and more.', 'bn-newspack-child' ); ?></p>
								<div class="bn-paywall-actions">
									<a href="<?php echo esc_url( $join_url ); ?>" class="button button-primary">
										<?php esc_html_e( 'Become a Member', 'bn-newspack-child' ); ?>
									</a>
									<a href="<?php echo esc_url( $login_url ); ?>" class="button button-secondary">
										<?php esc_html_e( 'Sign In', 'bn-newspack-child' ); ?>
									</a>
								</div>
							</div>
						</div>
						<?php
					}
					?>
				</div><!-- .entry-content -->

			</article><!-- #post-<?php the_ID(); ?> -->

		<?php
		endwhile;
		?>

	</main><!-- #main -->
</section><!-- #primary -->

<style>
/* Bay Nature Talks Paywall Styles */
.talks-paywall {
	margin: 3rem auto;
	max-width: 640px;
	text-align: center;
}

.bn-paywall-content {
	background: #f8f9fa;
	border: 1px solid #e1e4e8;
	border-radius: 8px;
	padding: 3rem 2rem;
}

.bn-paywall-content h2 {
	font-size: 1.75rem;
	margin-bottom: 1rem;
	color: #333;
}

.bn-paywall-content p {
	font-size: 1.125rem;
	margin-bottom: 2rem;
	color: #666;
	line-height: 1.6;
}

.bn-paywall-actions {
	display: flex;
	gap: 1rem;
	justify-content: center;
	flex-wrap: wrap;
}

.bn-paywall-actions .button {
	display: inline-block;
	padding: 0.75rem 2rem;
	text-decoration: none;
	border-radius: 4px;
	font-weight: 600;
	font-size: 1rem;
	transition: all 0.2s ease;
}

.bn-paywall-actions .button-primary {
	background: #2c5f2d;
	color: white;
	border: 2px solid #2c5f2d;
}

.bn-paywall-actions .button-primary:hover {
	background: #1e4420;
	border-color: #1e4420;
}

.bn-paywall-actions .button-secondary {
	background: #fff;
	color: #2c5f2d !important;
	border: 2px solid #2c5f2d;
}

.bn-paywall-actions .button-secondary:hover {
	background: #f0f0f0;
}

@media (max-width: 600px) {
	.bn-paywall-actions {
		flex-direction: column;
	}
	
	.bn-paywall-actions .button {
		width: 100%;
	}
}
</style>

<?php
get_footer();
