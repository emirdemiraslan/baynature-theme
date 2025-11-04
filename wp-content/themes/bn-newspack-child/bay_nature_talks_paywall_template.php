<?php
/**
 * Template Name: Bay Nature Talks Paywall Page
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Crate
 */
get_header(); ?>
<header class="entry-header">
	<?php if ( is_singular() ) {
			the_title( '<h1 class="entry-title">', '</h1>' );
			if ( get_post_meta( get_the_ID(), 'subheading', true ) ) { ?>
				<h2 class="entry-subtitle"><?php echo esc_html( get_post_meta( get_the_ID(), 'subheading', true ) ); ?></h2>
	<?php } ?>
	<?php } else { ?>
			<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
		<?php } ?>
</header><!-- .entry-header -->

<div class="entry-content">
	<?php 
	if ( unlock_paywall() ) {
			the_content();
	}
	else {
		show_member_login_message("talks");
	}
	?>
</div>
<?php get_footer(); ?>
