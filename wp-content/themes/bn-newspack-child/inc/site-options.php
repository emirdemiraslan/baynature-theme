<?php
/**
 * Site Options functionality for Bay Nature (Newspack Child) theme.
 * Adapted from Crate theme's site-options.inc
 */

/**
 * Add Site Options page (to be populated via ACF).
 */
function bn_add_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}
	acf_add_options_page( array(
		'page_title' => 'Site Options',
		'menu_slug'  => 'bn-site-options',
	) );
}
add_action( 'acf/init', 'bn_add_options_page' );

/**
 * Return the URL for the Site Options page.
 */
function bn_get_options_page_url() {
	return get_admin_url( 'admin.php?page=bn-site-options' );
}


//-----------------------------------------------------------------------------
// "Display Options" tab
//-----------------------------------------------------------------------------

/**
 * Add a query var that signals an intentional request for the error page.
 *
 * This lets us distinguish between requests for the error page made by end
 * users (by attempting to visit the error page's URL), which should be
 * defeated, and requests for the error page made by the template_redirect hook
 * below, which should be allowed to go through.
 */
function bn_error_page_query_vars( $vars ) {
	$vars[] = 'is_custom_404';
	return $vars;
}
add_filter( 'query_vars', 'bn_error_page_query_vars' );

/**
 * Make the custom error page (if any) accessible in the 404.php template.
 *
 * If a custom error page has been set on the Site Options page, then for
 * requests that result in 404 errors, replace the global $wp_query with a
 * query for the error page, in order to make its content accessible in the
 * 404.php template.
 * Then set $wp_query->is_404 so that the 404.php template still gets used --
 * otherwise we'd end up using page.php or singular.php. Setting $is_404 also
 * ensures that the is_404() conditional tag behaves as expected.
 */
function bn_error_page_template_redirect() {

	// If the current query actually found something, then it's none of our
	// business. Bail.
	if ( ! is_404() ) {
		return;
	}

	// Has an error page been specified?
	if ( $error_page_id = bn_get_error_page_id() ) {

		// Replace the global $wp_query.
		global $wp_query;
		$wp_query = new WP_Query( array(
			'page_id' => $error_page_id,
			'is_custom_404' => true,
		) );

		// Our new $wp_query has (presumably) found a page, so it'll have is_404
		// set to false -- but we want WP to use the 404 template, not the single
		// page template.
		// WP_Query::set_404() will set is_404 to true, while also resetting all
		// the other query flags, including `is_singular` and `is_page`. If we just
		// set $wp_query->is_404 to true by hand, then these flags would still be
		// true as well, and I can't in good conscience unleash such an abomination
		// into the world.
		$wp_query->set_404();

		// Now, calls to have_posts() and the_post(), etc. from within the 404.php
		// template will be made to a single-page query for the custom error page,
		// so 404.php can be written using the Loop, just like any other page
		// template.
	}
}
add_action( 'template_redirect', 'bn_error_page_template_redirect' );

/**
 * Add a condition to all queries' WHERE clauses that will exclude the custom
 * error page -- except intentional requests for the error page, as indicated
 * by the is_custom_404 query var.
 */
function bn_error_page_posts_where( $where, $query ) {

	// Don't mess with admin queries -- users need to be able to find and edit
	// the error page in wp-admin!
	if ( is_admin() ) {
		return $where;
	}

	// Don't mess with custom error requests.
	if ( $query->get( 'is_custom_404' ) ) {
		return $where;
	}

	// Don't mess with ACF field queries either, or we could end up in an
	// infinite loop.
	if ( in_array( 'acf-field', (array) $query->get( 'post_type' ) ) ) {
		return $where;
	}
	if ( in_array( 'acf-field-group', (array) $query->get( 'post_type' ) ) ) {
		return $where;
	}

	global $wpdb;

	// If an error page has been set, explicitly exclude that page ID.
	if ( $error_page_id = bn_get_error_page_id() ) {
		$where .= $wpdb->prepare( " AND ({$wpdb->posts}.ID != %d)", $error_page_id );
	}

	return $where;
}
add_action( 'posts_where', 'bn_error_page_posts_where', 10, 2 );

/**
 * Display a message when viewing the custom error page in wp-admin, indicating
 * that it has been set as the error page.
 */
function bn_error_page_admin_notices() {

	// If an error page has been set...
	if ( $error_page_id = bn_get_error_page_id() ) {

		// Get info about the current admin screen.
		$screen = get_current_screen();

		// Is the user editing a page?
		if ( 'page' === $screen->id ) {

			// Is the page being edited the custom error page?
			if ( isset( $_GET['post'] ) && $error_page_id == $_GET['post'] ) {

				// Output a message.
				?>
				<div class="notice notice-info">
					<p><?php esc_html_e( 'This page is set to be used as the error page for this site. It will not be directly accessible; instead, its content will be displayed when a user tries to access a page or file that doesn\'t exist.', 'bn-newspack-child' ); ?></p>
					<p><?php printf(
						esc_html__( 'Visit the %s to change this setting.', 'bn-newspack-child' ),
						'<a href="' . esc_url( bn_get_options_page_url() ) . '">' . esc_html__( 'Site Options page', 'bn-newspack-child' ) . '</a>'
					); ?></p>
				</div>
				<?php
			}
		}
	}
}
add_action( 'admin_notices', 'bn_error_page_admin_notices' );

/**
 * Get the ID for the custom error page, if any.
 */
function bn_get_error_page_id() {
	if ( ! function_exists( 'get_field' ) ) {
		return false;
	}
	return get_field( 'page_for_404', 'option' );
}

/**
 * If a default post thumbnail has been specified, use it when there's no post
 * thumbnail available for a given post.
 */
function bn_default_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

	// If this post has a thumbnail, just return the real thumbnail HTML.
	if ( $post_thumbnail_id && ! empty( $html ) ) {
		return $html;
	}

	// If a default post thumbnail has been set for this post/post type, use it.
	if ( $default_thumbnail_id = bn_get_post_thumbnail_id( $post_id ) ) {
		$html = wp_get_attachment_image( $default_thumbnail_id, $size, false, $attr );
	}

	return $html;
}
add_filter( 'post_thumbnail_html', 'bn_default_post_thumbnail_html', 10, 5 );

/**
 * Short-circuited version of has_post_thumbnail().
 *
 * Always returns true for post types for which a default thumbnail has been set.
 */
function bn_has_post_thumbnail( $post = null ) {
	return (bool) bn_get_post_thumbnail_id( $post );
}

/**
 * Short-circuited version of get_post_thumbnail_id().
 *
 * Always returns the default thumbnail ID for post types for which a default
 * thumbnail has been set.
 */
function bn_get_post_thumbnail_id( $post = null ) {

	// First, check for a real thumbnail, just in case.
	if ( $real_thumbnail_id = get_post_thumbnail_id( $post ) ) {
		return $real_thumbnail_id;
	}

	// Get the post, or bail if we can't.
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	// First, check for a post-type-specific default thumbnail.
	$default_thumbnail_id = get_field( "default_{$post->post_type}_thumbnail_id", 'option' );
	// If no post-type-specific thumbnail option was found or set, then fall back
	// on the default thumbnail for Posts.
	if ( ! $default_thumbnail_id ) {
		$default_thumbnail_id = get_field( 'default_post_thumbnail_id', 'option' );
	}

	return $default_thumbnail_id;
}

/**
 * Set the post excerpt length to the number of words set on the Site Options
 * page (if any).
 */
function bn_excerpt_length( $length ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $length;
	}
	if ( 'words' === get_field( 'excerpt_length_unit', 'option' ) ) {
		if ( $custom_length = get_field( 'excerpt_length', 'option' ) ) {
			$length = (int) $custom_length;
		}
	}
	return $length;
}
add_filter( 'excerpt_length', 'bn_excerpt_length' );

/**
 * Add the custom Read More link text (if any) to auto-generated excerpts.
 */
function bn_excerpt_more( $more_text ) {
	$more_text = __( ' &hellip; ', 'bn-newspack-child' );
	if ( function_exists( 'get_field' ) && ( $more_link_text = get_field( 'excerpt_link_text', 'option' ) ) ) {
		$more_text .= '<a href="' . esc_attr( get_permalink() ) . '">' . esc_html( $more_link_text ) . '</a>';
	}
	return $more_text;
}
add_filter( 'excerpt_more', 'bn_excerpt_more' );

/**
 * Re-trim post excerpts to a specific number of *characters*, instead of
 * words, if we've been asked to do so.
 */
function bn_trim_excerpt_characters( $trimmed, $raw_excerpt ) {

	// Per wp_trim_excerpt() behavior, if there *is* a raw excerpt, return it.
	if ( '' !== $raw_excerpt ) {
		return $raw_excerpt;
	}

	if ( ! function_exists( 'get_field' ) ) {
		return $trimmed;
	}

	// If we're supposed to be trimming by words and not characters, bail.
	if ( 'chars' !== get_field( 'excerpt_length_unit', 'option' ) ) {
		return $trimmed;
	}

	// Get the number of characters to trim to.
	$excerpt_length = (int) get_field( 'excerpt_length', 'option' );

	// If we haven't been told how many characters to trim to, bail.
	if ( ! $excerpt_length ) {
		return $trimmed;
	}

	// Apply filters/etc from wp_trim_excerpt().
	$text = get_the_content( '' );
	$text = strip_shortcodes( $text );
	$text = apply_filters( 'the_content', $text );
	$text = str_replace( ']]>', ']]&gt;', $text );

	// Strip tags and condense whitespace like wp_trim_words().
	$text = wp_strip_all_tags( $text );
	$text = preg_replace( '/[\n\r\t ]+/', ' ', $text );

	// Trim the excerpt to the number of characters given.
	$trimmed_hard = substr( $text, 0, $excerpt_length );

	// Get the "more" text/link to append to the excerpt.
	$excerpt_more = apply_filters( 'excerpt_more', ' [&hellip;]', 'bn-newspack-child' );

	// NOTE: We're trimming by words in an effort to keep all words whole. If you
	// don't care about your excerpts looking "like thi", you can skip the below
	// and just `return $trimmed_hard . $excerpt_more;`.
	// Get the number of words this corresponds to.
	$word_count = str_word_count( $trimmed_hard );
	// Decrement by one, since the chances are very good that $trimmed_hard ends
	// in the middle of a word.
	$word_count -= 1;
	// TrimSpa, baby!
	$trimmed_soft = wp_trim_words( $text, $word_count, $excerpt_more );

	return $trimmed_soft;
}
add_filter( 'wp_trim_excerpt', 'bn_trim_excerpt_characters', 10, 2 );


//-----------------------------------------------------------------------------
// "Text" tab
//-----------------------------------------------------------------------------

/**
 * Return the copyright text as specified on the Site Options page.
 */
function bn_get_copyright_text() {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}
	$copyright_text = get_field( 'copyright_text', 'option' );
	// Replace [year] with the year.
	$copyright_text = str_replace( '[year]', date( 'Y' ), $copyright_text );
	return $copyright_text;
}

/**
 * Output the copyright text as specified on the Site Options page.
 */
function bn_copyright_text() {
	// Escape output (allowing basic markp) & prettify dashes, apostrophes, etc.
	echo wp_kses_post( wptexturize( bn_get_copyright_text() ) );
}

/**
 * Return the contact info text as specified on the Site Options page.
 */
function bn_get_contact_info() {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}
	return get_field( 'contact_info', 'option' );
}

/**
 * Output the contact info as specified on the Site Options page.
 */
function bn_contact_info() {
	// Escape output (ACF takes care of wptexturize-ation, shortcodes, etc).
	echo wp_kses_post( bn_get_contact_info() );
}


//-----------------------------------------------------------------------------
// "URLs" tab
//-----------------------------------------------------------------------------

/**
 * Return an array of social links.
 *
 * @param array $services An array of service names ('facebook', 'twitter', etc.)
 *                      to return links for, if links have been set in the Site
 *                      Options page. Omit or leave empty to return all links
 *                      provided.
 * @return array An array of arrays, each second-dimension array containing the
 *               keys 'service', 'handle', and 'url'. Note that 'service' and
 *               'handle' may be empty.
 */
function bn_get_social_links( $services = false ) {

	if ( ! function_exists( 'have_rows' ) || ! function_exists( 'get_sub_field' ) ) {
		return array();
	}

	// Make sure $services is an array.
	if ( $services ) {
		$services = (array) $services;
	} else {
		$services = array();
	}

	// We'll collect the links in this variable.
	$links = array();

	// Iterate over 'social_media_urls' rows...
	while ( have_rows( 'social_links', 'option' ) ) {
		the_row();

		// Get all sub-fields.
		$link = array(
			'service'   => get_sub_field( 'service' ),
			'link_text' => get_sub_field( 'link_text' ),
			'url'       => get_sub_field( 'url' ),
		);

		if ( $services && ! empty( $services ) ) {
			if ( ! in_array( $link['service'], $services, true ) ) {
				// If an array of services was passed as an argument and this link's
				// service wasn't one of them, skip it.
				continue;
			}
		}

		// If we got this far, add this link to the return array.
		$links[] = $link;
	}

	return $links;
}

/**
 * Output the social links as a nice UL with some helpful classing.
 *
 * @param string $class_prefix (default: 'icon-') prefix for html class generated by service name and applied to li elements
 * @param string $link_target (default: '_blank') html target attribute value for anchors on social links
 * @param string $ul_class (default: 'menu menu-social-links') css classes applied to enclosing ul
 * @param string $ul_id (default: '') html id attribute applied to encolding ul
 * @return void
 */
function bn_social_links( $class_prefix = 'icon-', $link_target = '_blank', $ul_class = 'menu menu-social-links', $ul_id = '' ) {

	$social_links = bn_get_social_links();
	$ul_id_html = '';

 	if ( count( $social_links ) ) {

	 	// Give the UL an ID if specified
	 	if ( ! empty( $ul_id ) ) {
			$ul_id_html = 'id="' . sanitize_html_class( $ul_id ) . '"';
	 	}

	 	// Variable $classes can be a string or an array. Make it an array and sanitize it.
	 	$classes = ( ! is_array( $ul_class ) ) ? explode( ' ', $ul_class ) : $ul_class ;
	 	$classes = array_map( 'sanitize_html_class', $classes );

	 	// Output the UL element
	 	echo '<ul class="' . implode( ' ', apply_filters( 'bn_social_links_ul_classes', $classes ) ) . '" ' . $ul_id_html . ">";

	 	// Loop through social links and output them
	 	foreach ( $social_links as $link ) {
		 	$li_class = sanitize_html_class( $class_prefix . ( ! empty( $link['service'] ) ? $link['service'] : 'unknown' ) );
		 	$li_class = apply_filters( 'bn_social_links_li_class', $li_class );
		 	echo "<li class='$li_class'><a href='" . esc_url( $link['url'] ) . "' target='" . esc_attr( $link_target ) . "'>";
		 	echo "<span>" . esc_html( $link['link_text'] ) . "</span>";
		 	echo "</a></li>";
	 	}

	 	// We're done, close the UL
	 	echo "</ul>";

 	}
}

/**
 * Return the Donate URL as specified on the Site Options page.
 */
function bn_get_donate_url() {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}
	return get_field( 'donate_url', 'option' );
}

/**
 * Output the Donate URL as specified on the Site Options page.
 */
function bn_donate_url() {
	echo esc_url( bn_get_donate_url() );
}


//-----------------------------------------------------------------------------
// "Integrations" tab
//-----------------------------------------------------------------------------

/**
 * Return the Facebook App ID as specified on the Site Options page.
 */
function bn_get_fb_app_id() {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}
	return get_field( 'facebook_app_id', 'option' );
}

/**
 * Output the Facebook App ID as specified on the Site Options page.
 */
function bn_fb_app_id() {
	echo esc_attr( bn_get_fb_app_id() );
}

/**
 * Return the Google Maps API key as specified on the Site Options page.
 */
function bn_get_google_maps_api_key() {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}
	return get_field( 'google_maps_api_key', 'option' );
}

/**
 * Output the Google Maps API key as specified on the Site Options page.
 *
 * Output will be urlencode'd, since it looks like this usually gets passed in
 * via a GET param in a URL somewhere.
 */
function bn_google_maps_api_key() {
	echo rawurlencode( bn_get_google_maps_api_key() );
}

/**
 * Tell ACF about the Google Maps API key, in case it uses a location field.
 *
 * For details see https://www.advancedcustomfields.com/resources/google-map/
 */
function bn_add_google_maps_key_to_acf() {
	if ( ! function_exists( 'acf_update_setting' ) ) {
		return;
	}
	if ( ! empty( bn_get_google_maps_api_key() ) ) {
		acf_update_setting( 'google_api_key', bn_get_google_maps_api_key() );
	}
}
add_action( 'acf/init', 'bn_add_google_maps_key_to_acf' );


/**
 * Display Google Analytics in the Head, at a higher priority (5)
 */
function bn_add_google_analytics() {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}
	$key = get_field( 'google_analytics_key', 'option' );
	$maybe_enable = get_field( 'google_analytics_status', 'option' );

	if ( ! empty( $key ) && $maybe_enable ) {
	?>

		<!-- Google Analytics -->
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			ga('create', '<?php echo rawurlencode( $key ); ?>', 'auto' );

			ga('send', 'pageview');
			});
		</script>
		<!-- End Google Analytics -->

	<?php
	}
}
//add_action( 'wp_head', 'bn_add_google_analytics', 5 );