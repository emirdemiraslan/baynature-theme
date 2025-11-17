<?php
/**
 * Child theme header wrapper that renders the custom template part.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$header_sticky = get_theme_mod( 'header_sticky', false );

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php
wp_body_open();
do_action( 'before_header' );
?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'bn-newspack-child' ); ?></a>

	<?php if ( is_active_sidebar( 'header-2' ) ) : ?>
		<div class="header-widget above-header-widgets">
			<div class="wrapper">
				<?php dynamic_sidebar( 'header-2' ); ?>
			</div>
		</div>
	<?php endif; ?>

	<header id="masthead" class="site-header">
		<!-- Header markup is injected at wp_body_open for universal coverage (classic + block templates). -->
	</header><!-- #masthead -->

	<?php
do_action( 'after_header' );

if (
	apply_filters( 'bn_show_breadcrumbs', false )
	&& function_exists( 'yoast_breadcrumb' )
	&& ! is_page_template( 'page-full-width.php' )
	&& ! is_page_template( 'current_issue_template.php' )
) {
	yoast_breadcrumb( '<div class="site-breadcrumb desktop-only"><div class="wrapper">', '</div></div>' );
}

if ( is_active_sidebar( 'header-3' ) ) :
	?>
		<div class="header-widget below-header-widgets">
			<div class="wrapper">
				<?php dynamic_sidebar( 'header-3' ); ?>
			</div>
		</div>
	<?php endif; ?>

	<div id="content" class="site-content">

