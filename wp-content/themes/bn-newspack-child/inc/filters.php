<?php
/**
 * Theme Filters and Actions
 *
 * Legacy filters from crate theme for ACF, FacetWP, and other integrations.
 *
 * @package bn-newspack-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set FacetWP cache lifetime (if plugin is active).
 */
function bn_facetwp_cache_lifetime( $seconds ) {
	return 86400; // One day
}
add_filter( 'facetwp_cache_lifetime', 'bn_facetwp_cache_lifetime' );

/**
 * Filter related posts ACF relationship field.
 *
 * Includes both 'post' and 'article' post types in related posts.
 */
function bn_related_posts_filter( $args, $field, $post_id ) {
	$categories = get_the_category( $post_id );
	$tags       = get_the_tags( $post_id );

	$cat_ids = array();
	$tag_ids = array();

	if ( $categories ) {
		$cat_ids = wp_list_pluck( $categories, 'term_id' );
	}

	if ( $tags ) {
		$tag_ids = wp_list_pluck( $tags, 'term_id' );
	}

	$args['post_type']    = array( 'post', 'article' );
	$args['post__not_in'] = array( $post_id );
	
	if ( ! empty( $cat_ids ) ) {
		$args['category__in'] = $cat_ids;
	}
	
	if ( ! empty( $tag_ids ) ) {
		$args['tag__in'] = $tag_ids;
	}

	return $args;
}
add_filter( 'acf/fields/relationship/query/name=related_posts', 'bn_related_posts_filter', 10, 3 );

/**
 * Filter staff picked posts ACF relationship field.
 *
 * Includes both 'post' and 'article' post types.
 */
function bn_staff_picked_posts_filter( $args, $field, $post_id ) {
	$args['post_type'] = array( 'post', 'article' );
	return $args;
}
add_filter( 'acf/fields/relationship/query/name=staff_picked_posts', 'bn_staff_picked_posts_filter', 10, 3 );

/**
 * Add custom styles override based on ACF options.
 *
 * Applies seasonal color theming from site options.
 */
function bn_user_styles_override() {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$season          = get_field( 'current_season', 'option' );
	$primary_color   = get_field( $season . '_primary_color', 'option' );
	$secondary_color = get_field( $season . '_secondary_color', 'option' );

	if ( empty( $primary_color ) ) {
		return;
	}

	ob_start();
	?>
	<style type="text/css">
		.entry-content blockquote {
			color: <?php echo esc_attr( $primary_color ); ?>;
		}

		.entry-content p.has-drop-cap:not(:focus):first-letter,
		.entry-content p .dropcap {
			color: <?php echo esc_attr( $primary_color ); ?>;
		}

		.site-header .primary-nav,
		footer.site-footer {
			border-bottom-color: <?php echo esc_attr( $primary_color ); ?>;
		}
	</style>
	<?php
	$user_styles = ob_get_contents();
	ob_get_clean();

	echo $user_styles; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'bn_user_styles_override' );

/**
 * Disable XML-RPC for security.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Disable X-Pingback header for security.
 *
 * @param array $headers HTTP headers.
 * @return array Modified headers.
 */
function bn_disable_x_pingback( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'bn_disable_x_pingback' );

/**
 * Add co-authors to RSS feed (if Co-Authors Plus plugin is active).
 *
 * @param string $the_author Original author.
 * @return string Modified author string with co-authors.
 */
function bn_coauthors_in_rss( $the_author ) {
	if ( ! function_exists( 'coauthors' ) ) {
		return $the_author;
	}

	global $authordata;

	$out = array();
	foreach ( (array) get_coauthors() as $author ) {
		if ( ! empty( $author->user_nicename ) ) {
			$out[] = esc_html( $author->display_name );
		}
	}

	$output = ( count( $out ) > 1 ) ? implode( ', ', $out ) : $out[0];
	return $output;
}
add_filter( 'the_author', 'bn_coauthors_in_rss' );

/**
 * Display category name associated with post.
 *
 * @return string Category name or empty string.
 */
function bn_display_category_name() {
	$cat_array = get_the_category( get_the_ID() );
	
	if ( empty( $cat_array ) ) {
		return '';
	}

	$term_obj      = $cat_array[0];
	$main_cat_name = $term_obj->name;

	return esc_html( $main_cat_name );
}

/**
 * Add Gravity Forms full access to editors.
 */
function bn_add_grav_forms() {
	$role = get_role( 'editor' );
	if ( $role ) {
		$role->add_cap( 'gform_full_access' );
	}
}
add_action( 'admin_init', 'bn_add_grav_forms' );

/**
 * Limit file upload size for Tribe Events Community plugin.
 *
 * @return int Max file size in bytes (5MB).
 */
add_filter(
	'tribe_community_events_max_file_size_allowed',
	function() {
		return 5242880; // 5MB
	}
);

