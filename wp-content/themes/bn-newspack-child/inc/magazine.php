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
			'post_parent'    => 222465, // Parent page ID for magazine issues
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

