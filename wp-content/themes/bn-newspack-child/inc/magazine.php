<?php
/**
 * Magazine Issue and Archive Functions
 *
 * Functions for displaying magazine issues and archives.
 * Legacy compatibility from crate theme.
 *
 * @package bn-newspack-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if a post is in a magazine issue.
 *
 * @param int $post_id Post ID to check.
 * @return bool True if post is in magazine, false otherwise.
 */
function is_in_magazine( $post_id ) {
	$issue_key = get_post_meta( $post_id, 'issue_key', true );

	if ( 'null' === $issue_key || empty( $issue_key ) ) {
		return false;
	}

	return true;
}

/**
 * Get current week dates.
 *
 * @return array Array with 'monday' and 'sunday' timestamps.
 */
function current_week() {
	// set current timestamp
	$today = time();
	$w     = array();

	// calculate the number of days since Monday
	$dow    = date( 'w', $today );
	$offset = $dow - 1;

	if ( $offset < 0 ) {
		$offset = 6;
	}

	// calculate timestamp from Monday to Sunday
	$monday = $today - ( $offset * 86400 );
	$sunday = $monday + ( 6 * 86400 );

	// return current week array
	$w['monday'] = $monday;
	$w['sunday'] = $sunday;

	return $w;
}

/**
 * Get current week date formatted.
 *
 * @return string Formatted date string.
 */
function current_week_date_format() {
	$current_week = current_week();

	$date_string  = date( 'F j', $current_week['monday'] );
	$date_string .= '-';
	$date_string .= date( 'F j', $current_week['sunday'] );

	return $date_string;
}

/**
 * Render posts for a specific magazine issue.
 *
 * @param string $key Issue key (e.g., 'v23n2').
 */
function currentIssueRenderPosts( $key ) {
	global $wpdb;
	global $post;

	// Query published posts for this issue
	$querystr = "
	SELECT wposts.*
	FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
	WHERE (wposts.post_status = 'publish')
	AND wposts.ID = wpostmeta.post_id
	AND wpostmeta.meta_key = 'issue_key'
	AND wpostmeta.meta_value = '$key'
	AND post_type = 'article'
	ORDER BY wpostmeta.meta_value DESC
	";
	render_current_issue_content( $querystr, true, $key );

	// Query future posts for this issue
	$querystr = "
	SELECT wposts.*
	FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
	WHERE (wposts.post_status = 'future')
	AND wposts.ID = wpostmeta.post_id
	AND wpostmeta.meta_key = 'issue_key'
	AND wpostmeta.meta_value = '$key'
	AND post_type = 'article'
	ORDER BY wpostmeta.meta_value DESC
	";
	render_current_issue_content( $querystr, false, $key );
}

/**
 * Render magazine issue content.
 *
 * @param string $querystr SQL query string.
 * @param bool   $show_title Whether to show issue title.
 * @param string $key Issue key.
 */
function render_current_issue_content( $querystr, $show_title, $key ) {
	global $wpdb;
	global $post;
	?>
	<style type="text/css">
	.issue-content { grid-template-columns: repeat(2,1fr); grid-gap:40px 30px; display: grid;}
	.featured-date-grid { margin-right: 10px; }
	.featured-grid p small { font-size: 12px; line-height: 12px; color: #afafaf; }
	.featured-title-grid h4 { margin: 0px; font-style: normal; }
	.featured-title-grid { font-style: italic; }
	.featured-author-grid { margin: 0 0 8px 0; color: #afafaf;}
	.featured-author-grid .author { color: #7a7a7a;}
	.featured-image-grid { margin: 0; padding: 0 0 1em 0; }
	.section-three { padding: 0; }
	.section-three-area-one { overflow: scroll; }

	@media (min-width: 650px) {
	  .woocommerce.single-product main.container .issue-content {
		-ms-grid-columns:(1fr)[3];
		grid-template-columns: repeat(2,1fr);
		grid-gap: 40px 30px
	  }
	}
	</style>
	<?php
	$pageposts          = $wpdb->get_results( $querystr, OBJECT ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$count              = 0;
	$percent_similarity = 0;
	$threshold          = 80;

	if ( $pageposts ) :
		?>
		<?php if ( $show_title ) { ?>
		<h2 class="issue-content-title">Issue Content</h2>
		<?php } ?>
		<div class="issue-content">
		<?php foreach ( $pageposts as $post ) : ?>
			<?php
			setup_postdata( $post );
			$subtitle = get_field( 'subtitle' );
			$excerpt  = get_the_excerpt();
			similar_text( $excerpt, $subtitle, $percent_similarity );
			$is_draft = get_post_status() === 'future' ? true : false;
			$article  = $is_draft ? ( get_the_title() ) : '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
			$count++;
			echo '<div class="featured-grid">';
			if ( has_post_thumbnail() && $is_draft === false ) {
				echo '<div class="featured-image-grid">
				<a href="' . esc_url( get_permalink() ) . '"><img src="' . esc_url( get_the_post_thumbnail_url( $post->ID ) ) . '" alt="' . esc_attr( get_the_title() ) . '" /></a>
				</div>';
			} else {
				echo '<div class="featured-image-grid">
				<img src="' . esc_url( get_the_post_thumbnail_url( $post->ID ) ) . '" alt="' . esc_attr( get_the_title() ) . '" />
				</div>';
			}
			echo '<div class="featured-title-grid"><h4>' . $article . '</h4></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<div class="featured-title-grid">' . ( $percent_similarity >= $threshold ? '' : esc_html( $subtitle ) ) . '</div>';
			echo '<div class="featured-author-grid"><small>by ';
			echo '<span class="author">';
			if ( function_exists( 'coauthors_posts_links' ) ) {
				coauthors_posts_links();
			} else {
				the_author_posts_link();
			}
			echo '</span> | <span class="date">' . esc_html( get_the_date() ) . '</span></small></div>';
			echo '</div>';
		endforeach;
			?>
		</div>
	<?php endif; ?>
	<?php
	wp_reset_postdata();
}

/**
 * Render issue archive page (all magazine issues).
 */
function render_issue_archive_issues() {
	?>
	<style type="text/css">
	.issue-content { grid-template-columns: repeat(3,1fr); grid-gap:40px 30px; display: grid;}
	.featured-date-grid { margin-right: 10px; }
	.featured-grid p small { font-size: 10px; line-height: 12px; color: #afafaf; }
	.featured-title-grid h4 { margin: 0px; font-style: normal; }
	.featured-title-grid { font-style: italic; }
	.featured-author-grid { margin: 0 0 8px 0; color: #afafaf;}
	.featured-author-grid .author { color: #7a7a7a;}
	.featured-image-grid { margin: 0; padding: 0 0 1em 0; }
	.section-three { padding: 0; }
	.section-three-area-one { overflow: scroll; }

	@media (min-width: 650px) {
	  .woocommerce.single-product main.container .issue-content {
		-ms-grid-columns:(1fr)[3];
		grid-template-columns: repeat(2,1fr);
		grid-gap: 40px 30px
	  }
	}
	</style>
	<div class="issue-content">
	<?php
	global $post;

	$paged       = 1;
	$child_pages = new WP_Query(
		array(
			'post_type'      => 'page',
			'posts_per_page' => 200,
			'post_parent'    => bn_get_magazine_parent_page_id(),
			'paged'          => $paged,
			'orderby'        => 'menu_order',
			'order'          => 'DESC',
		)
	);

	$count = 0;
	if ( $child_pages->have_posts() ) :
		while ( $child_pages->have_posts() ) :
			$child_pages->the_post();
			$count++;
			echo '<div class="featured-grid">';
			if ( has_post_thumbnail() ) {
				echo '<div class="featured-image-grid">
				<a href="' . esc_url( get_permalink() ) . '"><img src="' . esc_url( get_the_post_thumbnail_url( $post->ID ) ) . '" alt="' . esc_attr( get_the_title() ) . '" /></a>
				</div>';
			}
			echo '<div class="featured-title-grid"><h4><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h4></div>';
			echo '<div class="featured-title-grid">' . esc_html( get_the_excerpt() ) . '</div>';
			echo '</div>';
		endwhile;
	endif;
	wp_reset_postdata();
	?>
	</div>
	<?php
}

/**
 * Get the parent page ID that stores individual magazine issues.
 *
 * Defaults to the page with slug "magazine" but can be overridden by the
 * bn_magazine_parent_page_id filter to support alternate site structures.
 *
 * @return int
 */
function bn_get_magazine_parent_page_id() {
	static $parent_id = null;

	if ( null !== $parent_id ) {
		return $parent_id;
	}

	$parent_id = 0;

	// Prefer whichever page is currently assigned the magazine archive template.
	$archive_page_ids = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'meta_key'               => '_wp_page_template',
			'meta_value'             => 'magazine_archive_page.php',
			'orderby'                => array(
				'menu_order' => 'DESC',
				'date'       => 'DESC',
			),
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'suppress_filters'       => false,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( ! empty( $archive_page_ids ) ) {
		$parent_id = (int) $archive_page_ids[0];
	}

	// Fall back to a page slug if no template match was found.
	if ( ! $parent_id ) {
		$archive_page = get_page_by_path( 'magazine-archive' );
		if ( $archive_page instanceof WP_Post ) {
			$parent_id = (int) $archive_page->ID;
		}
	}

	// As a last resort, try the main Magazine landing page.
	if ( ! $parent_id ) {
		$magazine_page = get_page_by_path( 'magazine' );
		if ( $magazine_page instanceof WP_Post ) {
			$parent_id = (int) $magazine_page->ID;
		}
	}

	$parent_id = (int) apply_filters( 'bn_magazine_parent_page_id', $parent_id );

	return $parent_id;
}

/**
 * Locate the latest magazine issue page.
 *
 * Attempts to find the most recently ordered child of the magazine parent page.
 * Falls back to the newest page using the Magazine Issue template.
 *
 * @return WP_Post|null
 */
function bn_get_latest_magazine_issue_page() {
	static $latest_issue = false;

	if ( false !== $latest_issue ) {
		return $latest_issue;
	}

	$latest_issue = null;
	$parent_id    = bn_get_magazine_parent_page_id();

	if ( $parent_id ) {
		$issue_query = new WP_Query(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'post_parent'    => $parent_id,
				'posts_per_page' => 1,
				'orderby'        => array(
					'menu_order' => 'DESC',
					'date'       => 'DESC',
				),
				'no_found_rows'  => true,
			)
		);

		if ( $issue_query->have_posts() ) {
			$latest_issue = $issue_query->posts[0];
		}
	}

	if ( ! $latest_issue ) {
		$template_query = new WP_Query(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'meta_key'       => '_wp_page_template',
				'meta_value'     => 'current_issue_template.php',
				'orderby'        => array(
					'date' => 'DESC',
				),
				'no_found_rows'  => true,
			)
		);

		if ( $template_query->have_posts() ) {
			$latest_issue = $template_query->posts[0];
		}
	}

	if ( $latest_issue ) {
		$latest_issue = apply_filters( 'bn_latest_magazine_issue_page', $latest_issue );
	}

	return $latest_issue;
}

/**
 * Extract the first inline image from a block of post content.
 *
 * @param string $content Post content.
 * @return array|null {
 *     @type string $src Image source URL.
 *     @type string $alt Alt text if provided.
 * }
 */
function bn_get_first_issue_image_from_content( $content ) {
	if ( empty( $content ) ) {
		return null;
	}

	if ( preg_match( '/<img[^>]+>/i', $content, $image_tag_matches ) ) {
		$image_tag = $image_tag_matches[0];
		$image_src = '';
		$image_alt = '';

		if ( preg_match( '/src=["\']([^"\']+)["\']/', $image_tag, $src_matches ) ) {
			$image_src = $src_matches[1];
		}

		if ( preg_match( '/alt=["\']([^"\']*)["\']/', $image_tag, $alt_matches ) ) {
			$image_alt = $alt_matches[1];
		}

		if ( $image_src ) {
			return array(
				'src' => $image_src,
				'alt' => $image_alt,
			);
		}
	}

	return null;
}

/**
 * Fetch cover image data for the latest magazine issue.
 *
 * @param string $size Image size handle to request.
 * @return array|null {
 *     @type string $url         Image source URL.
 *     @type int    $image_id    Attachment ID for the cover.
 *     @type int    $issue_id    Post ID of the magazine issue page.
 *     @type string $issue_url   Permalink for the issue page.
 *     @type string $issue_title Title of the issue page.
 * }
 */
function bn_get_latest_magazine_cover_data( $size = 'medium' ) {
	static $cache = array();

	$size = $size ?: 'medium';

	if ( array_key_exists( $size, $cache ) ) {
		return $cache[ $size ];
	}

	$cache[ $size ] = null;

	$latest_issue = bn_get_latest_magazine_issue_page();

	if ( ! $latest_issue instanceof WP_Post ) {
		return null;
	}

	$thumbnail_id = get_post_thumbnail_id( $latest_issue->ID );
	$cover_url    = '';
	$cover_alt    = '';

	if ( $thumbnail_id ) {
		$cover_url = wp_get_attachment_image_url( $thumbnail_id, $size );
		$cover_alt = trim( get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) );
	}

	if ( ! $cover_url ) {
		$inline_image = bn_get_first_issue_image_from_content( $latest_issue->post_content );
		if ( $inline_image && ! empty( $inline_image['src'] ) ) {
			$cover_url = $inline_image['src'];
			if ( ! empty( $inline_image['alt'] ) ) {
				$cover_alt = $inline_image['alt'];
			}
		}
	}

	if ( ! $cover_url ) {
		return null;
	}

	$cover_data = array(
		'url'         => $cover_url,
		'image_id'    => $thumbnail_id ? (int) $thumbnail_id : 0,
		'issue_id'    => (int) $latest_issue->ID,
		'issue_url'   => get_permalink( $latest_issue ),
		'issue_title' => get_the_title( $latest_issue ),
		'image_alt'   => $cover_alt,
	);

	$cover_data   = apply_filters( 'bn_latest_magazine_cover_data', $cover_data, $latest_issue, $size );
	$cache[ $size ] = $cover_data;

	return $cache[ $size ];
}

