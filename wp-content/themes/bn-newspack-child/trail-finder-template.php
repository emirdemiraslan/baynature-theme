<?php
/**
 * Template Name: Trail Finder
 *
 * This is the template that displays full width pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Crate
 */

get_header(); ?>
$browser = get_browser(null , true);

<style>
.trail-finder-content-shell {
	float:left;
	min-width: 300px;
	max-width: 1200px;
	width:1200px;
	background-color:#ffffff;
	position:relative;
	height:auto;
}
.footer-spacer {
	position:relative;
	height:400px;
}
#main {
	height:800px;
	margin: 0 auto;
}
@media only screen and (max-width: 768px) {
    #main	{ height: 800px; }
}
@media only screen and (max-width: 480px) {
    #main	{ height: 800px; }
}

@media only screen and (max-width: 1026px) {
    #main	{ height: 800px; }
}
/*   margin-bottom: 1.5rem;
 */
.intrinsic-container {
  position: relative;
  top: 0px;
  height: 0;
  margin: 0 auto;
  overflow: hidden;
  box-sizing: border-box;
  display:block;
  max-width:1026px;
}
 
/* 16x9 Aspect Ratio */
.intrinsic-container-16x9 {
  padding-bottom: 56.25%;
}

/* 9x16 Aspect Ratio */
.intrinsic-container-9x16 {
  padding-bottom: 177.7%;
}

/* 4x3 Aspect Ratio */
.intrinsic-container-4x3 {
  padding-bottom: 300%;
}
 
@media only screen and (max-width: 480px) {
    .intrinsic-container-4x3	{ padding-bottom: 350%; }
}
@media only screen and (max-width: 320px) {
    .intrinsic-container-4x3	{ padding-bottom: 400%; }
} 
@media only screen and (max-width: 375) {
    .intrinsic-container-4x3	{ padding-bottom: 450%; }
} 
@media only screen and (max-width: 414px) {
    .intrinsic-container-4x3	{ padding-bottom: 500%; }
} 

.intrinsic-container iframe {
  position: absolute;
  top:0;
  left: 0;
  width: 100%;
  height: 100%;
}

.gin-iframe{
  position: absolute;
  top:0;
  left: 0;
  width: 100%;
  height: 100%;
}

.tfclose {
	position: relative;
	top:20px;
}

/* new try */

</style>
<script type="text/javascript">
function clickme( it ) {
	// http://bit.ly/2kDpz8z
	var el = document.getElementById("trailfinder_iframe");
	el.src="https://websites.greeninfo.org/baynature/trailfinder/?search=1&attributes=&id=2kDpz8z"; // http://bit.ly/2kGCZQW //74624";
	alert("trail = " + el.src );
	//var el = document.frames("trailfinder_iframe"); //.document.getElementsByClassName("Brown").innerHTML;
	//var el = document.frames("trailfinder_frame"); //.getElementsByClassName("Brown").innerHTML;
}
</script>
<?php /* <div class="trail-finder-content-shell"> */ ?>
	<?php the_content("");  ?>
	<?php /*
	<div class="intrinsic-container intrinsic-container-4x3">
		<iframe  id="trailfinder_iframe" class= "gin-iframe" src="https://websites.greeninfo.org/baynature/trailfinder/"></iframe>
	</div>
  */
  //bn_print_r($browser);
  /*
*/ ?>
<iframe id="trailfinder_iframe"  frameborder="0" name="trailfinder_frame"  src="https://websites.greeninfo.org/baynature/trailfinder/" width="100%" height="100%"></iframe>
<br /><br /><br />
<?php newsletter_signup_banner("4", "Want suggestions for great outings? Enter your email now to get our biweekly newsletter"); ?>
<div class="tfclose">
<?php get_footer(); ?>
</div>
<script type="text/javascript">
var url = "https://websites.greeninfo.org/baynature/trailfinder/";

if (document.location.href.indexOf('?') > -1) {
url += document.location.href.substr(document.location.href.indexOf('?'));
alert("url = " + url);
}

console.log([ 'loading iframe', url ]);
jQuery('#trailfinder_iframe').prop('src',url);
//clickme(url);
</script>
<?php
get_footer();