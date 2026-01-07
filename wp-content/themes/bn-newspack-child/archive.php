<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Newspack
 */
get_header();

// Get sponsors for this taxonomy archive.
if ( function_exists( 'newspack_get_all_sponsors' ) ) {
	$all_sponsors         = newspack_get_all_sponsors( get_queried_object_id() );
	$native_sponsors      = newspack_get_native_sponsors( $all_sponsors );
	$underwriter_sponsors = newspack_get_underwriter_sponsors( $all_sponsors );
}

$feature_latest_post = get_theme_mod( 'archive_feature_latest_post', true );
$show_excerpt        = get_theme_mod( 'archive_show_excerpt', false );

// Hide author on collection category archives.
if ( class_exists( '\Newspack\Optional_Modules\Collections' ) &&
	\Newspack\Optional_Modules\Collections::is_module_active() &&
	is_tax( \Newspack\Collections\Collection_Category_Taxonomy::get_taxonomy() ) ) {
	add_filter( 'newspack_listings_hide_author', '__return_true' );
}
?>

	<section id="primary" class="content-area">

		<header class="page-header">
			<?php
			$category_image_id = 0;
			if ( is_category() ) {
				$term = get_queried_object();
				if ( $term ) {
					$image_field = get_field( 'category_image', 'category_' . $term->term_id );
                    // Fallback to just term object if the above fails
                    if ( ! $image_field ) {
                        $image_field = get_field( 'category_image', $term );
                    }

                    if ( $image_field ) {
                        if ( is_array( $image_field ) && isset( $image_field['ID'] ) ) {
                            $category_image_id = $image_field['ID'];
                        } elseif ( is_numeric( $image_field ) ) {
                            $category_image_id = $image_field;
                        }
                    }
				}
			}

			if ( $category_image_id ) :
				?>
				<div class="featured-image-behind">
					<div class="post-thumbnail">
						<?php echo wp_get_attachment_image( $category_image_id, 'newspack-featured-image-large' ); ?>
					</div>
					<div class="wrapper">
						<header class="entry-header">
							<div class="entry-subhead">
								<div class="entry-meta">
									<?php
									if ( ! empty( $native_sponsors ) ) {
										newspack_sponsor_label( $native_sponsors, null, true );
									}
									?>
								</div>
							</div>
							<h1 class="entry-title"><?php single_term_title(); ?></h1>
						</header>
					</div><!-- .wrapper -->
				</div><!-- .featured-image-behind -->

				<div class="archive-hero-meta">
					<?php
					$category_image_caption = wp_get_attachment_caption( $category_image_id );
					if ( $category_image_caption ) {
						$allowed_tags = wp_kses_allowed_html( 'post' );
						$allowed_tags['svg']  = array(
							'xmlns'   => true,
							'style'   => true,
							'viewbox' => true,
							'height'  => true,
							'width'   => true,
						);
						$allowed_tags['path'] = array(
							'd' => true,
						);
						echo '<div class="wp-caption-text">' . wp_kses( $category_image_caption, $allowed_tags ) . '</div>';
					}
					?>
					<?php if ( '' !== get_the_archive_description() ) : ?>
						<div class="newspack-post-subtitle">
							<?php echo wp_kses_post( wpautop( get_the_archive_description() ) ); ?>
						</div>
					<?php endif; ?>
					
					<?php
					if ( ! empty( $native_sponsors ) ) :
						// Get description for native archive sponsors.
						newspack_sponsor_archive_description( $native_sponsors );
					endif;
					
					if ( ! empty( $underwriter_sponsors ) ) {
						// Get info for underwriter archive sponsors.
						newspack_sponsored_underwriters_info( $underwriter_sponsors );
					}
					?>
				</div>
				<?php
			else :
				if ( is_author() ) {
					$queried       = get_queried_object();
					$author_avatar = '';
	
					if ( function_exists( 'coauthors_posts_links' ) ) {
						$author_avatar = coauthors_get_avatar( $queried, 120 );
					} else {
						$author_id     = get_query_var( 'author' );
						$author_avatar = get_avatar( $author_id, 120 );
					}
	
					if ( $author_avatar ) {
						echo wp_kses( $author_avatar, newspack_sanitize_avatars() );
					}
				}
				?>
				<span>
	
					<?php
					if ( ( is_category() || is_tag() ) && ! empty( $native_sponsors ) ) {
						// Get label for native archive sponsors.
						newspack_sponsor_label( $native_sponsors, null, true );
					}
					?>
	
					<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
	
					<?php do_action( 'newspack_theme_below_archive_title' ); ?>
	
					<?php
					if ( ( is_category() || is_tag() ) && ! empty( $native_sponsors ) ) :
						// Get description for native archive sponsors.
						newspack_sponsor_archive_description( $native_sponsors );
					elseif ( '' !== get_the_archive_description() ) :
						?>
						<div class="taxonomy-description">
							<?php echo wp_kses_post( wpautop( get_the_archive_description() ) ); ?>
						</div>
					<?php endif; ?>
	
					<?php
					if ( ( is_category() || is_tag() ) && ! empty( $underwriter_sponsors ) ) {
						// Get info for underwriter archive sponsors.
						newspack_sponsored_underwriters_info( $underwriter_sponsors );
					}
	
					if ( is_author() ) :
						// Get all of the author information.
						$author_id          = get_the_author_meta( 'ID' );
						$show_author_social = get_theme_mod( 'show_author_social', false );
						$show_author_email  = get_theme_mod( 'show_author_email', false );
						$author_social      = newspack_author_get_social_links( $author_id );
						$author_email       = get_the_author_meta( 'user_email', get_query_var( 'author' ) );
	
						// Don't output author-meta container unless it's populated.
						if ( ( $show_author_social && '' !== $author_social ) || ( $show_author_email && '' !== $author_email ) ) :
							?>
							<div class="author-meta">
								<?php
								if ( $show_author_email && '' !== $author_email ) :
									?>
									<a class="author-email" href="<?php echo 'mailto:' . esc_attr( $author_email ); ?>">
										<?php echo wp_kses( newspack_get_social_icon_svg( 'mail', 18 ), newspack_sanitize_svgs() ); ?>
										<?php echo esc_html( $author_email ); ?>
									</a>
								<?php endif; ?>
	
								<?php newspack_author_social_links( $author_id, 20 ); ?>
							</div><!-- .author-meta -->
	
						<?php endif; ?>
	
						<?php do_action( 'newspack_theme_below_author_archive_meta' ); ?>
	
					<?php endif; ?>
				</span>
			<?php endif; ?>

		</header><!-- .page-header -->

		<?php do_action( 'before_archive_posts' ); ?>

		<main id="main" class="site-main">

		<?php
		if ( have_posts() ) :
		?>
			<!-- Archive results styled like wpnbha Homepage Articles block -->
			<div class="wp-block-newspack-blocks-homepage-articles wpnbha archive-results-wpnbha is-style-borders show-image image-alignleft ts-3 is-1 mobile-stack show-category">
				<div data-posts>
				<?php
				// Start the Loop.
				while ( have_posts() ) :
					the_post();
					
					$post_id   = get_the_ID();
					$post_type = get_post_type();
					
					// Build article classes
					$article_classes   = array( 'archive-result-item' );
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
					
					bn_render_archive_article( get_post(), $article_classes, $categories );
					
				endwhile;
				?>
				</div>
			</div><!-- .wpnbha -->

			<?php
			// Previous/next page navigation.
			newspack_the_posts_navigation();

		// If no content, include the "No posts found" template.
		else :
			get_template_part( 'template-parts/content/content', 'none' );

		endif;
		?>
		</main><!-- #main -->
		<?php
		$archive_layout = get_theme_mod( 'archive_layout', 'default' );
		if ( 'default' === $archive_layout ) {
			get_sidebar();
		}
		?>
	</section><!-- #primary -->

<?php
get_footer();
