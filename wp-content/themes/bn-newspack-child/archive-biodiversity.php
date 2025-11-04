<html>
<head>
	<title>Weird, Ugly, Rare</title>

	<meta charset="utf-8"> 
	<meta name="viewport" content="initial-scale=1">
	<meta property="og:title" content="The Weird, the Rare and the Ugly"/>
	<meta property="og:description" content="We relate to nature, and learn to value its conservation, by the stories we tell and hear about it. That’s a perilous proposition for the world’s non-charismatic plants and animals. This series showcases the stories of species it’s hard to tell stories about. They’re weird. They’re ugly. They’re rare."/>
	<meta property="og:url" content="https://baynature.org/biodiversity"/>
	<meta property="og:site_name" content="Bay Nature"/>
	<meta property="fb:admins" content="1403244759"/>
	<meta property="og:image" content="https://baynature.org/wp-content/uploads/2012/08/baynature-Orange-logo-150px.jpg"/>

	<?php /*
	<link rel="shortcut icon" href="https://3vswoj22h2l7nt08f1ye9o4r.wpengine.netdna-cdn.com/wp-content/themes/bay-nature-responsive/images/favicon.ico"/>
	*/ ?>
	<link rel="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.ico"/>

	<?php /*
	<link rel="stylesheet" href="https://3vswoj22h2l7nt08f1ye9o4r.wpengine.netdna-cdn.com/wp-content/themes/bay-nature-responsive/<?php echo get_stylesheet_directory_uri(); ?>/bn-biodiversity/css/baynature.scss" type="text/css" media="screen, projection"/>
	*/ ?>
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/css/baynature.css" type="text/css" media="screen, projection"/>


	<?php /*
	<link rel="stylesheet" href="https://3vswoj22h2l7nt08f1ye9o4r.wpengine.netdna-cdn.com/wp-content/themes/bay-nature-responsive/style.css?1435006863" type="text/css" media="screen, projection"/>
	*/ ?>
	<link rel="<?php echo get_stylesheet_directory_uri(); ?>/style.css?1435006863" type="text/css" media="screen, projection"/>

	<?php /*
	<link rel="stylesheet" href="https://3vswoj22h2l7nt08f1ye9o4r.wpengine.netdna-cdn.com/wp-content/themes/bay-nature-responsive/style-print.css" type="text/css" media="print"/>
	*/ ?>
	<link rel="<?php echo get_stylesheet_directory_uri(); ?>/style-print.css" type="text/css" media="print"/>
	<?php /*
	<link rel="stylesheet" href="https://3vswoj22h2l7nt08f1ye9o4r.wpengine.netdna-cdn.com/wp-content/themes/bay-nature-responsive/jquery.c2selectbox.css" type="text/css" media="screen"/>
	*/ ?>
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/jquery.c2selectbox.css" type="text/css" media="screen"/>


	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

	<link href="<?php echo get_stylesheet_directory_uri(); ?>/bn-biodiversity/css/bootstrap.min.css" rel="stylesheet">

	<!-- PROJECT CSS FILE -->
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/bn-biodiversity/css/biodiversity-stylesheet.css">

    <!--open sans headline font -->
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
	<!--libre baskerville font-->
	<link href='https://fonts.googleapis.com/css?family=Libre+Baskerville' rel='stylesheet' type='text/css'>
	<!--jane austen font for labels -->
	<link href="<?php echo get_stylesheet_directory_uri(); ?>/bn-biodiversity/css/fonts/jane-austen/style.css" rel='stylesheet' type='text/css'>


	<style>
	.navbar-header {
		font-family: 'Open Sans', sans-serif;
	}
	.nav-head {
		float: left;
	}

	/*navigation modal css*/
	.modal-header {
	border-bottom: 0px;
	}
	.modal-body {
		padding: 0 15px 15px 15px;
	}
	.modal-dialog {   
		width: 95%;
		margin: 0 auto;
	}
	h4.modal-title {
		font-family: "Open Sans", sans-serif;
		font-weight: bold;
		text-transform: uppercase;
	}
	#overlay ul {
		list-style: none;
		padding: 0;
	}
	#overlay .thumbnail {
		width: calc(24% - 10px);
		display: inline-block;
		margin: 5px;
	}
	#overlay .ovl-item {
		position: relative;
	}
	#overlay .ovl-item li { 
	   position: absolute; 
	   bottom: 5px; 
	   left: 0; 
	   width: 100%; 
	}
	#overlay a li {
		font-family: "Open Sans", sans-serif;
		font-weight: bold;
		text-transform: uppercase;
		color: black;
		font-size: 1.5em;
		line-height: 1.1em;
		text-align: center;
		background-color: rgba(255,255,255, .5);
		padding-top: 5px;
	}

	.more-text {
		float: right;
		position: static;
		bottom: 0;
		cursor: pointer;
	}
	.more-text span {
		font-size: 3em;
		color: #333;
		text-transform: uppercase;
	}
	@media (max-width: 768px){
		#overlay .thumbnail {
			width: calc(24% - 10px);
			display: inline-block;
			margin: 5px;
		}
		#overlay a li {
			font-size: 1.1em;
		}
	}
	@media (max-width: 425px){
		#overlay .thumbnail {
			width: calc(49% - 10px);
			display: inline-block;
			margin: 5px;
		}
		#overlay a li {
			font-size: 1.2em;
		}
	}
	@media (max-width: 320px){
		#overlay a li {
			font-size: 1em;
		}
	}
	</style>

	<!-- jquery cdn -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/bn-biodiversity/js/jquery.min.js"></script>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/bn-biodiversity/js/bootstrap.min.js"></script>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/bn-biodiversity/js/jquery.lazyload.js"></script>

    <?php $id = get_the_ID(); $permalink = get_permalink($id); ?>
	<script type="text/javascript">
	function tweetbtn() {
    	window.open("https://twitter.com/share?url=<?php echo $permalink; ?>&amp;text=The%20Weird,%20the%20Rare%20and%20the%20Ugly%20via@BayNature&amp;hashtags=biodiversity", "", "width=700, height=253");
	}
	function fbbtn() {
    	window.open("https://www.facebook.com/sharer.php?u=<?php echo $permalink; ?>", "", "width=700, height=253");
	}
	</script>
	<!-- Google Analytics -->
	<script type="text/javascript">
    
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-32668307-1', 'auto');
  ga('send', 'pageview');
  ga('require', 'displayfeatures');
  
  /* Track outbound links in Google Analytics */
  (function($) {
 
    "use strict";
 
    // current page host
    var baseURI = window.location.host;
 
    // click event on body
    $("body").on("click", function(e) {
 
      // abandon if link already aborted or analytics is not available
      if (e.isDefaultPrevented() || typeof ga !== "function") return;
 
      // abandon if no active link or link within domain
      var link = $(e.target).closest("a");
      if (link.length != 1 || baseURI == link[0].host) return;
 
      // cancel event and record outbound link
      e.preventDefault();
      var href = link[0].href;
      ga('send', {
        'hitType': 'event',
        'eventCategory': 'outbound',
        'eventAction': 'link',
        'eventLabel': href,
        'hitCallback': loadPage
      });
 
      // redirect after one second if recording takes too long
      setTimeout(loadPage, 1000);
 
      // redirect to outbound page
      function loadPage() {
        document.location = href;
      }
 
    });
 
  })(jQuery); // pass another library here if required
    
    </script>
</head>

<body>
<nav class="navbar navbar-default navbar-static-top" role="navigation">
	<div class="container-fluid">
		
		<div class="navbar-header navbar-left">
	    	<a id="logo" class="navbar-brand navbar-left hide-small" href="https://baynature.org" >
	        	<img alt="Bay Nature" src="<?php echo get_stylesheet_directory_uri(); ?>/bn-biodiversity/css/bnlogo.png">
	    	</a>
	    	<div class="nav-head navbar-link navbar-left">
	    		<a href="<?php echo get_bloginfo('url'); ?>/biodiversity">Weird, Ugly, Rare</a>
	    	</div>

    	</div>

    	<button type="button" class="navbar-toggle"  data-toggle="modal" data-target="#overlay">
			<i class="fa fa-bars fa-lg"></i>
    	</button>

		<button type="button" onclick="tweetbtn()" class="navbar-toggle nav-share" data-toggle="collapse" data-target=".share-collapse">
			<i class="fa fa-twitter fa-lg"></i>
		</button>

		<button onclick="fbbtn()" type="button" class="navbar-toggle nav-share" data-toggle="collapse" data-target=".share-collapse">
			<i class="fa fa-facebook fa-lg"></i>
		</button>

		<a 	href="mailto:?Subject=The Weird, the Rare and the Ugly&amp;Body=Check%20out%20these%20Bay%20Nature%20stories https://www.baynature.org/biodiversity">
			<button type="button" class="navbar-toggle nav-share hide-small" data-toggle="collapse" data-target=".share-collapse">
				<i class="fa fa-envelope fa-lg"></i>
			</button>
		</a>

    	<div class="clear"></div>

	</div>
</nav>

<header>

	<div class="row">
		<div class="intro-text col-sm-10 col-sm-offset-1 col-xs-12">

			<div>
				<h1>Weird, Ugly, Rare</h1>
			</div>

			<div class="subhead col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
				<p><em>We relate to nature, and learn to value its conservation, by the stories we tell and hear about it. That’s a perilous proposition for the world’s non-charismatic plants and animals. </em></p>
				<p><em>This series showcases the stories of species it’s hard to tell stories about.</em></p>
				<p><em>They’re weird. They’re ugly. They’re rare. </em></p>
			</div>

		</div>
	</div>
</header>
<article>


<div class="row">

	<div id="stories" class="species-boxes col-sm-10 col-sm-offset-1 col-xs-12"><?php
		$args = array( 'post_type' => 'biodiversity' );
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) : $loop->the_post();
			global $post;
			echo '
		<div class="story-container">
			<a href="' . $post->post_name . '">
			<div class="species-box">
				<img src="' . get_stylesheet_directory_uri() . '/bn-biodiversity/media/illos/' . $post->post_name . '.jpg" data-original="' . get_stylesheet_directory_uri() . '/bn-biodiversity/media/illos/' . $post->post_name . '.jpg" class="illustration lazy" />
				<div class="species-name label">
					<h3>'; the_field('name'); echo '</h3>
				</div>
			</div>
			</a>

			<div class="story-info">
				<div class="story-hed">
					<h2><a href="' . $post->post_name . '">'; the_title(); echo '</a></h2>
					<div class="species-data">
						<div class="habitat">
							<p>Habitat: '; the_field('habitat'); echo '</p>
						</div>
						<div class="population">
							<p>Population: <span class="highlight">'; the_field('population'); echo '</span></p>
						</div>
						<div class="status">
							<p>Status: <span class="highlight">'; the_field('protection_status'); echo '</span></p>
						</div>
					</div>
				</div>
			</div>
			
		</div>

		<div class="clear"></div>';
		endwhile; ?>
	</div><!-- end stories/species-box -->

</div><!--end row-->

<div style="padding-top: 30px;"></div>
<div class="row">

	<div class="about col-sm-6 col-sm-offset-3 col-xs-12" id="about">
		<h2 class="credits">About</h2>

		<p>'Weird, Ugly, Rare' is a series from <a href="https://baynature.org" target="_blank">Bay Nature</a>, a magazine dedicated to exploring the natural world of the San Francisco Bay Area.</p>
		<p><strong>Writer:</strong> <a href="https://www.twitter.com/ericsimons" target="_blank">Eric Simons</a></p>
		<p><strong>Production and development:</strong> <a href="https://www.twitter.com/seangreene89" target="_blank">Sean Greene</a> and Peuan Thinsan</p>
		<p><strong>Illustration:</strong> <a href="https://racheldiazbastin.com/" target="_blank">Rachel Diaz-Bastin</a></p>
		<p>The web design, illustration and photography for 'Weird, Ugly, Rare' was funded by a grant from the <a href="https://www.sej.org/initiatives/fund%20for%20environmental%20journalism/overview" target="_blank">Foundation for Environmental Journalism</a>.</p>

	</div>

	<div class="subscribe col-sm-6 col-sm-offset-3 col-xs-12" id="subscribe">

		 <form style="border:1px solid #ccc;padding:3px;text-align:center;" action="https://tinyletter.com/weirduglyrare" method="post" target="popupwindow" onsubmit="window.open('https://tinyletter.com/weirduglyrare', 'popupwindow', 'scrollbars=yes,width=800,height=600');return true">
		 	<h2 class="credits">Sign up<h2>
		 	<p>Receive an email when we publish the next story in 'Weird, Ugly, Rare.'</p>

		 	<div class="input-group col-sm-8 col-sm-offset-2">
		 		<p><input type="text" class="form-control" name="email" id="tlemail" placeholder="Email address" /></p>
				<span class="input-group-btn">
		 			<input type="hidden" value="1" name="embed"/>
		 			<p><button class="btn btn-default" type="submit">Subscribe</button></p>
				</span>

		 	</div>

		 </form>


	</div>

</div>

</div>

</article>

<div id="overlay" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="false">
        		<i class="fa fa-times fa-1x"></i>
        	</span>
        </button>
        <h4 class="modal-title">Weird, Ugly, Rare</h4>
      </div>
      <div class="modal-body">
      	<ul>
			<?php
	  			$args = array( 'post_type' => 'biodiversity' );
				$loop = new WP_Query( $args );
				$count = 1;
				while ( $loop->have_posts() ) : $loop->the_post();
					global $post;
					echo '
				<a href="' . site_url() . '/biodiversity/' . $post->post_name . '" class="thumbnail">
					<div class="ovl-item ovl' . $count . '">
						<li>';
						the_field('name');
						echo '</li>
						<img src="' . get_stylesheet_directory_uri() . '/bn-biodiversity/media/illos/' . $post->post_name . '.jpg">
					</div>
				</a>';
				$count++;
				endwhile;
				wp_reset_query(); ?>
		</ul>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
$(document).ready(function() {

	$("img.lazy").lazyload({
	    threshold: 900,
	    effect : "fadeIn"
	});

})

</script>
</body>
</html>