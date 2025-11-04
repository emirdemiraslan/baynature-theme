<?php
/**
 * The template for displaying the current issue content template
 * Template Name: Magazine Archive Page
 *
 *
 * 11-14-2023
 * version 0.0
 */

defined( 'ABSPATH' ) || exit;
//ob_start();
get_header();
//$header = ob_get_clean();
//$header = preg_replace('#<title>(.*?)<\/title>#', '<title>TEST</title>', $header);
//echo "new header = ".$header;?>
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
<?php the_content(); ?>
<?php 
render_issue_archive_issues(  ); ?>
</div>


<?php get_footer(); ?>

