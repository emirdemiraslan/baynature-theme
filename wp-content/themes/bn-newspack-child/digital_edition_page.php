<?php
/**
 * Template Name: Digital Edition Page
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
	switch ( isVistorActiveDigitalSubscriber() ) {
		case 0:
			echo "<p>Please <a href='/my-account/'>log in to your account</a> to access your digital edition!</p>";
			break;
		case 1:
			echo "<p>You do not have a Digital Edition subscription. <a href='/product-category/subscriptions-renewals/'>Purchase a subscription now to get access!</a></p>";
			echo "<p>Please note: If you’ve just subscribed, it can take up to 24 hours for your Digital Edition subscription to become active.</p>";
			break;
		case 2:
			the_content();
			break;
	}
	?>
</div>
<?php get_footer(); ?>
