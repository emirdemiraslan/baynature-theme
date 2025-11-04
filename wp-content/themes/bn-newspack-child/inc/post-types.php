<?php
/**
 * Custom Post Types and Taxonomies
 *
 * Registers legacy post types from the crate theme:
 * - biodiversity (Weird, Ugly, Rare series)
 * - article (separate from standard posts)
 *
 * @package bn-newspack-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom taxonomies.
 */
function bn_register_custom_taxonomies() {
	// Register 'picks' taxonomy (staff picks)
	register_taxonomy(
		'picks',
		array( 'post', 'article' ),
		array(
			'label'        => __( 'Picks', 'bn-newspack-child' ),
			'labels'       => array(
				'name'          => __( 'Picks', 'bn-newspack-child' ),
				'singular_name' => __( 'Pick', 'bn-newspack-child' ),
				'search_items'  => __( 'Search Picks', 'bn-newspack-child' ),
				'all_items'     => __( 'All Picks', 'bn-newspack-child' ),
				'edit_item'     => __( 'Edit Pick', 'bn-newspack-child' ),
				'update_item'   => __( 'Update Pick', 'bn-newspack-child' ),
				'add_new_item'  => __( 'Add New Pick', 'bn-newspack-child' ),
				'new_item_name' => __( 'New Pick Name', 'bn-newspack-child' ),
				'menu_name'     => __( 'Picks', 'bn-newspack-child' ),
			),
			'public'       => true,
			'hierarchical' => false,
			'show_ui'      => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'picks' ),
		)
	);

	// Register 'features' taxonomy
	register_taxonomy(
		'features',
		array( 'post', 'article' ),
		array(
			'label'        => __( 'Features', 'bn-newspack-child' ),
			'labels'       => array(
				'name'          => __( 'Features', 'bn-newspack-child' ),
				'singular_name' => __( 'Feature', 'bn-newspack-child' ),
				'search_items'  => __( 'Search Features', 'bn-newspack-child' ),
				'all_items'     => __( 'All Features', 'bn-newspack-child' ),
				'edit_item'     => __( 'Edit Feature', 'bn-newspack-child' ),
				'update_item'   => __( 'Update Feature', 'bn-newspack-child' ),
				'add_new_item'  => __( 'Add New Feature', 'bn-newspack-child' ),
				'new_item_name' => __( 'New Feature Name', 'bn-newspack-child' ),
				'menu_name'     => __( 'Features', 'bn-newspack-child' ),
			),
			'public'       => true,
			'hierarchical' => false,
			'show_ui'      => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'features' ),
		)
	);
}
add_action( 'init', 'bn_register_custom_taxonomies', 0 );

/**
 * Register 'biodiversity' custom post type.
 *
 * Used for the "Weird, Ugly, Rare" series.
 * Legacy template files: single-biodiversity.php, archive-biodiversity.php
 */
function bn_register_biodiversity_post_type() {
	$labels = array(
		'name'               => _x( 'Biodiversity', 'post type general name', 'bn-newspack-child' ),
		'singular_name'      => _x( 'Article', 'post type singular name', 'bn-newspack-child' ),
		'add_new'            => _x( 'Add New', 'article', 'bn-newspack-child' ),
		'add_new_item'       => __( 'Add New Article', 'bn-newspack-child' ),
		'edit_item'          => __( 'Edit Article', 'bn-newspack-child' ),
		'new_item'           => __( 'New Article', 'bn-newspack-child' ),
		'all_items'          => __( 'All Articles', 'bn-newspack-child' ),
		'view_item'          => __( 'View Article', 'bn-newspack-child' ),
		'search_items'       => __( 'Search Articles', 'bn-newspack-child' ),
		'not_found'          => __( 'No articles found', 'bn-newspack-child' ),
		'not_found_in_trash' => __( 'No articles found in the Trash', 'bn-newspack-child' ),
		'parent_item_colon'  => '',
		'menu_name'          => 'Biodiversity Series',
	);

	$args = array(
		'labels'       => $labels,
		'description'  => 'Biodiversity Series',
		'public'       => true,
		'menu_position' => 5,
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author' ),
		'has_archive'  => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-admin-post',
		'rewrite'      => array( 'slug' => 'biodiversity' ),
	);

	register_post_type( 'biodiversity', $args );
}
add_action( 'init', 'bn_register_biodiversity_post_type' );

/**
 * Register 'article' custom post type.
 *
 * Separate from standard WordPress posts.
 * Used for special articles and legacy content.
 */
function bn_register_article_post_type() {
	$labels = array(
		'name'          => 'Articles',
		'singular_name' => 'Article',
		'add_new'       => 'Add New Article',
		'add_new_item'  => 'Add New Article',
		'edit_item'     => 'Edit Article',
		'new_item'      => 'New Article',
	);

	$rewrite = array(
		'slug'         => 'article',
		'with_front'   => true,
		'hierarchical' => true,
	);

	$args = array(
		'label'         => 'Articles',
		'labels'        => $labels,
		'singular_label' => 'Article',
		'public'        => true,
		'show_ui'       => true,
		'has_archive'   => true,
		'capability_type' => 'post',
		'hierarchical'  => false,
		'rewrite'       => array( 'slug' => 'articles' ),
		'supports'      => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'page-attributes', 'post-formats' ),
		'show_in_rest'  => true,
		'taxonomies'    => array( 'category', 'post_tag', 'picks' ),
		'yarpp_support' => true,
		'menu_icon'     => 'dashicons-media-document',
	);

	register_post_type( 'article', $args );
}
add_action( 'init', 'bn_register_article_post_type' );

