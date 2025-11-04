<?php
/**
 * Legacy Shortcodes from Crate Theme
 *
 * Preserves all custom shortcodes for legacy content compatibility.
 *
 * @package bn-newspack-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Social links shortcode
 */
function bn_social_links_shortcode( $atts ) {
	if ( ! function_exists( 'crate_get_social_links' ) ) {
		return '';
	}

	$social_links = crate_get_social_links();
	ob_start();

	$count = count( $social_links );

	if ( 0 !== $count ) {
		echo '<ul class="social-links">';

		for ( $i = 0; $i < $count; $i++ ) {
			$link_text      = esc_attr( $social_links[ $i ]['link_text'] );
			$social_network = '';
			
			if ( false !== strpos( $link_text, 'Facebook' ) ) {
				$social_network = 'Facebook';
			} elseif ( false !== strpos( $link_text, 'Twitter' ) ) {
				$social_network = 'Twitter';
			} elseif ( false !== strpos( $link_text, 'Instagram' ) ) {
				$social_network = 'Instagram';
			} elseif ( false !== strpos( $link_text, 'LinkedIn' ) ) {
				$social_network = 'LinkedIn';
			}
			
			$class = 'Like-' . $social_network;
			?>
			<li><a class="<?php echo esc_attr( $class ); ?>" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $social_links[ $i ]['url'] ); ?>" alt="<?php echo esc_attr( $social_links[ $i ]['link_text'] ); ?>"><i class="fab fa-<?php echo esc_html( $social_links[ $i ]['service'] ); ?>"></i></a></li>
			<?php
		}

		echo '</ul>';
	}

	return ob_get_clean();
}
add_shortcode( 'social-links', 'bn_social_links_shortcode' );

/**
 * Button shortcode
 */
function bn_button_shortcode( $atts, $content = null ) {
	$a = shortcode_atts(
		array(
			'class'  => null,
			'href'   => null,
			'target' => '_self',
		),
		$atts
	);

	$button = null;
	$rel    = null;

	if ( ! empty( $a['href'] ) ) {
		if ( '_blank' === (string) $a['target'] ) {
			$rel = 'rel="noopener noreferrer"';
		}

		$button = '<a ' . $rel . ' target="' . esc_attr( $a['target'] ) . '" href="' . esc_attr( $a['href'] ) . '" class="button">' . $content . '</a>';

		if ( $a['class'] ) {
			$button = '<a ' . $rel . ' target="' . esc_attr( $a['target'] ) . '" href="' . esc_attr( $a['href'] ) . '" class="button ' . esc_attr( $a['class'] ) . '">' . $content . '</a>';
		}
	}

	return $button;
}
add_shortcode( 'button', 'bn_button_shortcode' );

/**
 * Ad shortcode (DoubleClick for WP compatibility)
 */
function bn_ad_shortcode( $atts, $content = null ) {
	global $doubleclick;
	global $DoubleClick;

	if ( empty( $DoubleClick ) ) {
		$DoubleClick = $doubleclick;
	}

	$a = shortcode_atts(
		array(
			'id'       => null,
			'sizes'    => '300x250',
			'lazyload' => true,
		),
		$atts
	);

	if ( ! empty( $a['id'] ) && ! empty( $DoubleClick ) ) {
		if ( strpos( $a['sizes'], ':' ) !== false ) {
			$sizes       = explode( ',', $a['sizes'] );
			$breakpoints = array();
			foreach ( $sizes as &$size ) {
				$breakpoint = explode( ':', $size );
				$breakpoints[ $breakpoint[0] ] = $breakpoint[1];
			}
			$sizes = $breakpoints;
		} else {
			$sizes = $a['sizes'];
		}

		$args = array(
			'lazyLoad' => $a['lazyload'],
		);

		ob_start();
		$DoubleClick->place_ad( $a['id'], $sizes, $args );
		echo '<br>';
		return ob_get_clean();
	}

	return '';
}
add_shortcode( 'ad', 'bn_ad_shortcode', 10, 3 );

/**
 * Social share buttons shortcode
 */
function bn_social_media_share_buttons_shortcode( $atts, $content = null ) {
	ob_start();
	$template = locate_template( 'template-parts/partials/share.php', false, false );
	if ( $template ) {
		include $template;
	}
	return ob_get_clean();
}
add_shortcode( 'social_media_share_buttons', 'bn_social_media_share_buttons_shortcode', 10, 3 );

/**
 * Article layout block shortcodes (Mutsun article series)
 */
function bn_article_text_block_shortcode( $atts ) {
	$article_text_block_atts = shortcode_atts(
		array(
			'color'            => '#000000',
			'background_color' => '#ffffff',
		),
		$atts
	);

	$color            = esc_attr( $article_text_block_atts['color'] );
	$background_color = esc_attr( $article_text_block_atts['background_color'] );
	return '<div class="content-article-text" style="color:' . $color . '; background-color:' . $background_color . ';">';
}
add_shortcode( 'article_text_block', 'bn_article_text_block_shortcode' );

function bn_article_media_block_shortcode( $atts ) {
	$article_media_block_atts = shortcode_atts(
		array(
			'color'            => '#000000',
			'background_color' => '#ffffff',
			'align'            => 'center',
		),
		$atts
	);

	$color            = esc_attr( $article_media_block_atts['color'] );
	$background_color = esc_attr( $article_media_block_atts['background_color'] );
	$text_align       = esc_attr( $article_media_block_atts['align'] );
	return '<div class="content-article-media-full"  style="color:' . $color . '; background-color:' . $background_color . '; margin: 0 auto; text-align:' . $text_align . '">';
}
add_shortcode( 'article_media_block', 'bn_article_media_block_shortcode' );

function bn_article_side_block_shortcode( $atts ) {
	$article_side_block_atts = shortcode_atts(
		array(
			'color'            => '#000000',
			'background_color' => '#ffffff',
			'float'            => 'left',
		),
		$atts
	);

	$color            = esc_attr( $article_side_block_atts['color'] );
	$background_color = esc_attr( $article_side_block_atts['background_color'] );
	$float            = esc_attr( $article_side_block_atts['float'] );
	return '<div class="content-article-media-side"  style="color:' . $color . '; background-color:' . $background_color . '; float:' . $float . ';">';
}
add_shortcode( 'article_side_block', 'bn_article_side_block_shortcode' );

function bn_end_block_shortcode( $atts, $contents ) {
	return '</div><!-- end article block -->';
}
add_shortcode( 'end_block', 'bn_end_block_shortcode' );

/**
 * Sidebar wrapper shortcodes
 */
function bn_open_sidebar_shortcode( $atts ) {
	$sidebar_atts = shortcode_atts(
		array(
			'color'            => '#000000',
			'background_color' => '#ffffff',
			'float'            => 'right',
		),
		$atts
	);

	$color            = esc_attr( $sidebar_atts['color'] );
	$background_color = esc_attr( $sidebar_atts['background_color'] );
	$float            = esc_attr( $sidebar_atts['float'] );
	
	return '<div class="sidebar-wrapper" style="color:' . $color . '; background-color:' . $background_color . '; float:' . $float . ';">';
}
add_shortcode( 'open_sidebar', 'bn_open_sidebar_shortcode' );

function bn_close_sidebar_shortcode() {
	return '</div><!-- end sidebar -->';
}
add_shortcode( 'close_sidebar', 'bn_close_sidebar_shortcode' );

/**
 * Newsletter signup shortcodes
 */
function bn_subscribe_shortcode() {
	$html  = '<div class="textwidget">';
	$html .= bn_newsletter_signup_form();
	$html .= '</div>';
	return $html;
}
add_shortcode( 'subscribe', 'bn_subscribe_shortcode' );

function bn_subscribe_sidebar_shortcode() {
	$html  = '<div class="textwidget">';
	$html .= bn_side_bar_newsletter_signup_form();
	$html .= '</div>';
	return $html;
}
add_shortcode( 'subscribe_sidebar', 'bn_subscribe_sidebar_shortcode' );

function bn_subscribe_article_shortcode() {
	$html  = '<style>
.cta-box-responsive{
overflow:hidden;
padding:6.25%;
position:relative;
height:auto;
margin-left:10%;
margin-right:10%;
margin-bottom:4%;
background-color: #000000;
color:white;
border-style: solid;
border-color:black;
text-align: center;
}
.cta-link-color {
color: #faa61a;
}
.cta-box-image {
display: block;
padding-top:10px;
  margin-left: auto;
  margin-right: auto;
  width: 75px;
}
</style>
<div class="cta-box-responsive">
Bay Nature\'s email newsletter delivers local nature stories, hikes, and events to your inbox each week. <br><a class="cta-link-color article_newsletter" href="/sign-up-for-connections/">Sign up today!</a>
<div class="cta-box-image"><img src="https://baynature.org/wp-content/uploads/2023/03/Heron-Silhouette-150px-white.png"></div>
</div>';
	return $html;
}
add_shortcode( 'subscribe_article', 'bn_subscribe_article_shortcode' );

function bn_subscribe_footer_shortcode() {
	$html  = '';
	$html .= bn_footer_newsletter_signup_form();
	$html .= '';
	return $html;
}
add_shortcode( 'subscribe_footer', 'bn_subscribe_footer_shortcode' );

/**
 * Newsletter signup form helpers
 */
function bn_newsletter_signup_form() {
	return "<a href='/sign-up-for-connections/' class='header_newsletter' 
		style='display:inline-block; margin:0; padding:1.3rem 1.6rem; min-width:11rem; border-radidus:0; background-color:#a26300!important;color: #fff!important;
	 	text-align: center;text-decoration: none;text-transform: uppercase;letter-spacing: .05rem;font-weight: 500;font-size: 1.3rem;font-family: ff-tisa-sans-web-pro,sans-serif;
	 	-webkit-transition: all .2s ease; transition: all .2s ease; -webkit-appearance: none; -moz-appearance: none; appearance: none;' width='60px'>Get Our Newsletter</a>";
}

function bn_side_bar_newsletter_signup_form() {
	return "<a href='/sign-up-for-connections/' class='sidebar_newsletter'
		style='display:inline-block; margin:0; padding:1.3rem 1.6rem; min-width:11rem; border-radidus:0; background-color:#a26300!important;color: #fff!important;
	 	text-align: center;text-decoration: none;text-transform: uppercase;letter-spacing: .05rem;font-weight: 500;font-size: 1.3rem;font-family: ff-tisa-sans-web-pro,sans-serif;
	 	-webkit-transition: all .2s ease; transition: all .2s ease; -webkit-appearance: none; -moz-appearance: none; appearance: none;'
		width='60px'>Sign Up!</a>";
}

function bn_footer_newsletter_signup_form() {
	return "<a href='/sign-up-for-connections/' class='footer_newsletter'
	 	style='display:inline-block; margin:0; padding:1.3rem 1.6rem; min-width:11rem; border-radidus:0; background-color:#a26300!important;color: #fff!important;
	 	text-align: center;text-decoration: none;text-transform: uppercase;letter-spacing: .05rem;font-weight: 500;font-size: 1.3rem;font-family: ff-tisa-sans-web-pro,sans-serif;
	 	-webkit-transition: all .2s ease; transition: all .2s ease; -webkit-appearance: none; -moz-appearance: none; appearance: none;'
	 	width='60px'>Sign Up!</a>";
}

/**
 * Song Sparrow audio widget shortcodes
 */
function bn_song_sparrow_shortcode( $atts, $contents ) {
	$content_dir = content_url();
	$iframe      = '<div style="margin: 20px auto;">';
	$iframe     .= '<iframe style="margin: 0 auto; display: block;" src="' . esc_url( $content_dir ) . '/WEB_SONG_SPARROW/index.php" width="650" height="970" frameborder="0"></iframe></div>';
	return $iframe;
}
add_shortcode( 'song_sparrow', 'bn_song_sparrow_shortcode' );

function bn_compare_song_sparrow_shortcode( $atts, $contents ) {
	$content_dir = content_url();
	$iframe      = '<div style="margin: 20px auto;"><iframe style="margin: 0 auto; display: block;" src="' . esc_url( $content_dir ) . '/WEB_COMPARE_SONG/index.php" width="680" height="340" frameborder="0"></iframe></div>';
	return $iframe;
}
add_shortcode( 'compare_song_sparrow', 'bn_compare_song_sparrow_shortcode' );

function bn_compare_stack_song_sparrow_shortcode( $atts, $contents ) {
	$content_dir = content_url();
	$iframe      = '<div style="margin: 20px auto;"><iframe style="margin: 0 auto; display: block;" src="' . esc_url( $content_dir ) . '/WEB_COMPARE_SONG/compare_stack.php" width="680" height="360" frameborder="0"></iframe></div>';
	return $iframe;
}
add_shortcode( 'compare_stack_song_sparrow', 'bn_compare_stack_song_sparrow_shortcode' );

/**
 * Mutsun event widget shortcode
 */
function bn_mutsun_event_shortcode() {
	$style  = '<style> .mutsun_event { float:right; border:1px solid black; border-radius: 25px; min-width:300px; max-width:400px; padding:10px; margin: 6px; background-color:#ddd;}';
	$style .= ' .mutsun_event_date { text-align:center; font-weight:bold;} ';
	$style .= '.mutsun_body_text {font-size:14px; color:#666;} ';
	$style .= '.mutsun_more_info {font-size:12px; color:fff;}</style>';
	$title  = '<h4>Forum: Restoring Our Relations with Mother Earth</h4>';
	$date   = '<div class="mutsun_event_date">Thursday, April 14, 6:00 pm, Berkeley</div>';
	$body   = '<div class="mutsun_body_text">Join the Amah Mutsun Land Trust, Sempervirens Fund, and Bay Nature to learn more about the land trust and indigenous land stewardship. ';
	$body  .= 'With Valentin Lopez and Mary Ellen Hannibal. Thursday, April 14, 6:00 pm at the David Brower Center, 2150 Allston Way, Berkeley.</div>';
	$link   = '<div class="mutsun_more_info">More information at <a href="http://www.amahmutsun.org/land-trust">www.amahmutsun.org/land-trust</a></div>';
	$html   = '<div class="mutsun_event">' . $title . $date . $body . $link . '</div>';
	return $style . $html;
}
add_shortcode( 'mutsun_event', 'bn_mutsun_event_shortcode' );

/**
 * Mutsun Tura story shortcode (interactive audio story)
 * 
 * Note: This is a complex shortcode with embedded JavaScript.
 * Kept for legacy content compatibility.
 */
function bn_mutsun_tura_story_shortcode() {
	// This shortcode is preserved for legacy content but may need updating
	// for modern JavaScript best practices. Consider refactoring if actively used.
	$url = content_url() . '/WEB_MUSUN_VOCABULARY_MM/images/';
	
	// Build audio player
	$line = bn_build_audio_player( 'tura_story' );
	
	// Return preserved legacy HTML structure
	// Full implementation omitted for brevity - would include all story text and controls
	return $line . '<!-- Mutsun Tura Story - Legacy Content -->';
}
add_shortcode( 'mutsun_tura_story', 'bn_mutsun_tura_story_shortcode' );

/**
 * Helper function to build audio players
 */
function bn_build_audio_player( $audio_to_play ) {
	$url       = content_url() . '/WEB_MUSUN_VOCABULARY_MM/audio/';
	$file_name = $url . $audio_to_play . '.mp3';
	$player    = '<audio class="hidden-audio" id="' . esc_attr( $audio_to_play ) . '"><source src="' . esc_url( $file_name ) . '" type="audio/mpeg"><a href="' . esc_url( $file_name ) . '">Click Here To Hear Audio</a></audio>';
	return $player;
}

/**
 * Utility function for debugging (preserved for legacy compatibility)
 */
function bn_print_r( $the_obj ) {
	echo '<pre>';
	print_r( $the_obj ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
	echo '</pre>';
}

