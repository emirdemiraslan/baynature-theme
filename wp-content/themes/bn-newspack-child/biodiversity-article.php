<html>
<head>
	<title><?php the_post(); the_title(); ?> - The Weird, the Rare and the Ugly - Bay Nature</title>
	
	
	<?php $id = get_the_ID(); $permalink = get_permalink($id); ?>
	<meta charset="utf-8"> 
	<meta name="viewport" content="initial-scale=1">
	<meta property="og:title" content="<?php the_title(); ?> - The Weird, the Rare and the Ugly"/>
	<meta property="og:description" content="<?php the_field('subheading'); ?>"/>
	<meta property="og:url" content="<?php echo $permalink; ?>"/>
	<meta property="og:site_name" content="Bay Nature"/>
	<meta property="fb:admins" content="1403244759"/>
	<meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/bn-biodiversity/media/illos/<?php echo $post->post_name; ?>.jpg"/>
	<?php /*
	<link rel="shortcut icon" href="https://3vswoj22h2l7nt08f1ye9o4r.wpengine.netdna-cdn.com/wp-content/themes/bay-nature-responsive/images/favicon.ico"/>
	*/ ?>
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico"/>
	<?php /*
	<link rel="stylesheet" href="https://3vswoj22h2l7nt08f1ye9o4r.wpengine.netdna-cdn.com/wp-content/themes/bay-nature-responsive/css/baynature.scss" type="text/css" media="screen, projection"/>
	*/ ?>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/baynature.css" type="text/css" media="screen, projection"/>
	<?php /*
	<link rel="stylesheet" href="https://3vswoj22h2l7nt08f1ye9o4r.wpengine.netdna-cdn.com/wp-content/themes/bay-nature-responsive/style.css?1435006863" type="text/css" media="screen, projection"/>
	*/ ?>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css?1435006863" type="text/css" media="screen, projection"/>
	<?php /*
	<link rel="stylesheet" href="https://3vswoj22h2l7nt08f1ye9o4r.wpengine.netdna-cdn.com/wp-content/themes/bay-nature-responsive/style-print.css" type="text/css" media="print"/>
	*/ ?>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style-print.css" type="text/css" media="print"/>
	<?php /*
	<link rel="stylesheet" href="https://3vswoj22h2l7nt08f1ye9o4r.wpengine.netdna-cdn.com/wp-content/themes/bay-nature-responsive/jquery.c2selectbox.css" type="text/css" media="screen"/>
	*/ ?>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/jquery.c2selectbox.css" type="text/css" media="screen"/>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    <!-- bootstrap css -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
	<!--leaflet-->
	<?php /*
	<link rel="stylesheet" href="https://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css" />
	*/ ?>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/biodiversity.css" type="text/css" media="screen, projection"/>

    <!--open sans headline font -->
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
	<!--libre baskerville font-->
	<link href='https://fonts.googleapis.com/css?family=Libre+Baskerville' rel='stylesheet' type='text/css'>
	<!-- PROJECT CSS FILE -->
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/bn-biodiversity/css/biodiversity-stylesheet.css">
    <script src="<?php echo get_template_directory_uri(); ?>/bn-biodiversity/js/jquery.lazyload.js"></script>

    <!-- EDIT-52 : biodiversity/stories/tegula/ -->
    <script src="<?php echo get_template_directory_uri(); ?>/bn-biodiversity/stories/tegula/js/gsap.js"></script>

	<style>
	.navbar-header {
		font-family: 'Open Sans', sans-serif;
	}
	.nav-head {
		float: left;
	}
	#fishFader {position:relative;}
	#fishFader img{position:absolute;z-index:1; width:100%}
	#second {display:none; z-index: -1;}
	#fishFader figcaption {position: absolute; bottom: -5;}
	#map-marsh {
		height: 400px;
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
	<!-- boostrap cdn -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

	<!--maps js -->
	<script src="https://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
	<script type="text/javascript" src="https://maps.stamen.com/js/tile.stamen.js?v1.3.0"></script>

	<!-- howler js for audio -->
	<script src="<?php echo get_template_directory_uri(); ?>/bn-biodiversity/js/howler.min.js"></script>
	<script src="<?php echo get_template_directory_uri(); ?>/bn-biodiversity/js/wavesurfer.js"></script>

	<?php $id = get_the_ID(); $permalink = get_permalink($id); ?>
	<script type="text/javascript">
	function tweetbtn() {
    	window.open("https://twitter.com/share?url=<?php echo $permalink; ?>&amp;text=<?php the_title(); ?>%20via%20@BayNature", "", "width=700, height=253");
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
	        	<img alt="Bay Nature" src="<?php echo get_template_directory_uri(); ?>/bn-biodiversity/css/bnlogo.png">
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

		<a 	href="mailto:?Subject=The Weird, the Rare and the Ugly&amp;Body=Check%20out%20these%20Bay%20Nature%20stories <?php echo $permalink; ?>">
			<button type="button" class="navbar-toggle nav-share hide-small" data-toggle="collapse" data-target=".share-collapse">
				<i class="fa fa-envelope fa-lg"></i>
			</button>
		</a>

    	<div class="clear"></div>

	</div>
</nav>

<header>
	<div class="row">

		<div class="col-sm-10 col-sm-offset-1 col-xs-12">

			<div class="museum-card col-md-6 col-sm-12 col-xs-12">

				<h1><?php the_title(); ?></h1>

				<div class="art-box visible-sm visible-xs">
					<figure>
						<img src="<?php echo get_template_directory_uri(); ?>/bn-biodiversity/media/illos/<?php global $post; echo $post->post_name; ?>.jpg" />
					</figure>

				</div>

				<h2><?php the_field('name'); ?></h2>

				<hr></hr>

				<div class="card-info">
					<p>Habitat: <span><?php the_field('habitat'); ?></span></p>
					<p>Population: <span><?php the_field('population'); ?></span></p>
					<p>Protection status: <span><?php the_field('protection_status'); ?></span></p>
				</div>
			</div>

			<div class="art-box col-md-6 col-sm-12 hidden-sm hidden-xs">
				<figure>
					<img src="<?php echo get_template_directory_uri(); ?>/bn-biodiversity/media/illos/<?php global $post; echo $post->post_name; ?>.jpg" />
				</figure>

			</div>

		</div>
	</div>
</header>

<article>
<div class="container">
	<div class="row">

		<div class="story">
			<h2><?php the_field('subheading'); ?></h2>
			<p class="byline">by <?php the_author_link(); ?></p>
			<?php the_content(); ?>
		</div>

	</div>
			
	<div class="row" >
		<button type="button" class="navbar-toggle more-text"  data-toggle="modal" data-target="#overlay">
			<span>More    </span> <i class="fa fa-bars fa-3x"></i> 
		</button>
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
						<img src="' . get_template_directory_uri() . '/bn-biodiversity/media/illos/' . $post->post_name . '.jpg">
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


<!--IMPORTANT: Project scripts: -->
<script src="<?php echo get_template_directory_uri(); ?>/bn-biodiversity/js/biodiversity-scripts.js"></script>


<script>
	$("img.lazy").lazyload({
		threshold: 900,
		effect : "fadeIn"
	});

	//sound player
	$(document).ready(function(){
		var wavesurfer = WaveSurfer.create({
		    container: '#wave',
		    waveColor: '#fdaaaa',
		    progressColor: '#ff0000'
		});

		var sound = new Howl({

			//note to self: can declare .ogg file here using comma
			urls: ['https://dh1rvgpokacch.cloudfront.net/atavist/9410/audio/raw/kekburcut-1403296688-31.mp3'],
			volume: 0.5,
		 //    onend: function() {
			//     $('i').replaceWith('<i class="fa fa-play"></i>');
			//     $('.audio-controller').removeClass('playing');
			//   },
		 //    onplay: function() {
			//     $('i').replaceWith('<i class="fa fa-pause"></i>');
			//     $('.audio-controller').addClass('playing');
			//   },
			// onpause: function(){
			//     $('i').replaceWith('<i class="fa fa-play"></i>');
			//     $('.audio-controller').removeClass('playing');					 
			// },
			// onloaderror: function(){
			//     $('i').replaceWith('<p style="font-family:Arial, sans-serif;">Sorry! This sound failed to load.</p>');
			// }
		});

		wavesurfer.load(sound['_src']);
		//wavesurfer.setVolume(0.5);

		wavesurfer.on('play', function(){
			    $('i').replaceWith('<i class="fa fa-pause"></i>');
			    $('.audio-controller').addClass('playing');
		});
		wavesurfer.on('pause', function(){
			    $('i').replaceWith('<i class="fa fa-play"></i>');
			    $('.audio-controller').removeClass('playing');	
		});
		wavesurfer.on('error', function(){
			    $('i').replaceWith('<p style="font-family:Arial, sans-serif;">Sorry! This sound failed to load.</p>');
		});
		wavesurfer.on('finish', function(){
			    $('i').replaceWith('<i class="fa fa-play"></i>');
			    $('.audio-controller').removeClass('playing');
		});		
		$('.audio-controller').on('click', function(){
		    if ( $(this).hasClass("playing") ) {
		    	//sound.pause();
		    	wavesurfer.pause();
		    } else {
				// sound.stop().play();
				wavesurfer.play();
		    }
		});
		
	})

</script>

</body>

</html>