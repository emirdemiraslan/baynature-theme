<?php
/**
 * Template Name: Donation Page
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Crate
 */

 //phpinfo();
?>

<?php
get_header('donate'); ?>

<!-- Charitable DDonation Page -->
<style>
.entry-header { padding-top: 10rem;}  
.donation_main {
	width:58%;
	padding:2%;
	float:left;
	background-color: #ffffff;
}
@media screen and (max-width: 782px) {
		.donation_main {
			width: 100%;
		}
	}

.donation_sidebar {
	width:38%;
	margin-left:4%;
	padding:2%;
	float:left;
	height:100%;
	background-color: #ffffff;
}
@media screen and (max-width: 782px) {
		.donation_sidebar {
			width: 100%;
		}
	}
.donation_image {
	background-color: #ffffff;
	background-image: url(https://baynature.org/wp-content/uploads/2019/11/IMG_2789-M.jpg); width: 100%; height: 100%; border: 1px solid black;
	background-repeat: no-repeat; 
}

.white-background {
	background-color: #ffffff;
}

.charitable-donation-options ul li {
	list-style-type: square;
}

.entry-content ol:not(ol) li:before, .entry-content ul:not(ol) li:before {
    content: "";
}

.entry-content {
	max-width: 100rem;
}
.italic_text {
	font-style: italic;
}
.bold_text {
	font-weight: bold;
}
.bn_contact_info {
	margin-left:0px;
}

.charitable-donation-form .charitable-donation-options {
	padding: 0 0;
}

.photo_credit {
	font-size: 1.5rem;
}
.center_text {
	text-align: center;
}
.gold_seal {
	width:120px; margin:auto;
}
</style>

	<article class="donation_image hero-wrap" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail() && is_singular() ) : ?>

	<figure class="featured-image">
		<div class="hero-wrap" style="background-image: url(<?php echo esc_url( get_the_post_thumbnail_url() ); ?>);">
			<?php the_post_thumbnail( 'large' ); ?>
		</div>
		<figcaption><?php the_post_thumbnail_caption(); ?></figcaption>
		
	</figure>

	<?php endif; ?>

	<header class="entry-header">
		<?php if ( is_singular() ) { ?>
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			<?php if ( get_post_meta( get_the_ID(), 'subheading', true ) ) { ?>
				<h2 class="entry-subtitle"><?php echo esc_html( get_post_meta( get_the_ID(), 'subheading', true ) ); ?></h2>
			<?php } ?>
		<?php } else { ?>
			<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
		<?php } ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<div class="donation_main">
		<?php the_content(); ?>
		</div>
		<div class="donation_sidebar">
			<h3><strong>Help Bay Nature Thrive</strong></h3>
			<p><span class="bold_text">Join the Publisher's Circle:</span> Give a gift of $500 or more and become a member of Bay Nature's Publisher's Circle. Members receive first notice of Bay Nature hikes and events, a free print subscription to Bay Nature magazine, invites to quarterly issue release parties, and the opportunity to help choose each <span class="italic_text">Bay Nature</span> cover.</p>
			<p><span class="bold_text">Become a Sustainer:</span> Make a monthly gift of $30 or more and get early notice of Bay Nature hikes and events plus a free print subscription to <span class="italic_text">Bay Nature </span>magazine.</p>
			<p><span class="bold_text">Give by mail</span><br>
			<span class ="bn_contact_info">Bay Nature</span><br><span class ="bn_contact_info">1328 6th Street, Suite 2</span><br><span class ="bn_contact_info">Berkeley, CA 94710</span></p>
			<p><span class="bold_text">Give by phone</span><br>
			<span class ="bn_contact_info">510-528-8550</span></p>
			<p><a href="/corporate-giving/">Corporate Giving</a>
			<br><a href="/planned-giving/">Planned Giving</a>
			<br><a href="/other-ways-to-give/">Other Ways to Give</a></p>
			<p><img src="https://baynature.org/wp-content/uploads/2020/11/award-logos-2020.jpg"></p>
			<p>Bay Nature is a 501(c)(3) tax-exempt organization and your donation is tax-deductible to the extent permitted by U.S. law. Our tax identification number is 76-0744881.</p>
			<p>When you donate to Bay Nature, you'll receive email updates from us. You can unsubscribe at any time.</p>
			<hr>
			<p class="photo_credit">Photo by Craig Lanway</p>
		</div>
	</div><!-- .entry-content -->

	<div style="height:40px">&nbsp;</div>
	<footer class="entry-footer">
		<div class="post-terms">
			<?php
				// Get ALL the terms across all taxonomies by passing get_taxonomies as second arg into the_terms()!
				//the_terms( get_the_id(), get_taxonomies( '', 'names' ), __('Posted in: '), '' );
			?>
		</div>

	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

<?php
get_footer('donation');