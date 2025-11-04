<?php /*
Template Name: Mount Diablo
*/

//get_header();
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <!-- Use title if it's in the page YAML frontmatter -->
    <title>Bay Nature : Diablo Fire</title>
    <meta name="description" content="The Morgan Fire in 2013 turned 3,100 acres of Mount Diablo into what looked like a moonscape. But beneath the scorched earth lay the seeds of a remarkable, once-in-a-lifetime transformation. In the springs following the fire, plants bloomed that hadn't been seen on the mountain in decades -- and might not be seen again for another generation.">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Social Media Meta Tags -->
    <!-- replace info here -->
    <meta property="og:title" content="Bay Nature : Diablo Fire" />
    <meta property="og:type" content="baynature.org" />
    <meta property="og:url" content="https://www.baynature.org/diablo-fire/" />
    <meta property="og:image" content="https://example.com/image.jpg" />
    <meta property="og:description" content="Morgan Fire in 2013 turned 3,100 acres of Mount Diablo into what looked like a moonscape. But beneath the scorched earth lay the seeds of a remarkable, once-in-a-lifetime transformation. In the springs following the fire, plants bloomed that hadn't been seen on the mountain in decades -- and might not be seen again for another generation." />
    <meta property="og:site_name" content="Site Name" />
    <meta property="fb:admins" content="Facebook numeric ID" />


    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="Page Title">
    <meta name="twitter:description" content="Page description less than 200 characters">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image:src" content="https://www.example.com/image.jpg">
    
    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.ico">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/css/normalize.css">
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/css/main.css">
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/js/vendor/modernizr-2.6.1.min.js"></script>
    <script>
    function detectCSSAnimation() {
    	
		var animation = false;
    	var animationstring = 'animation';
    	var keyframeprefix = '';
    	var domPrefixes = 'Webkit Moz O ms Khtml'.split(' ');
    	var pfx  = '';
    	var elm = document.createElement('div');
    	var altUI = document.getElementById("altUI");
    	var sliderUI = document.getElementById("slider");

		if( elm.style.animationName !== undefined ) { 
			animation = true; 
			altUI.style.display = "none";  
			sliderUI.style.display = "block"; 
	    } else { 
			altUI.style.display = "block";
			sliderUI.style.display = "none";	    
		}
	    
	    //alert("A = " + animation);
	}

	function showNextUiRow () {
		var uiRowOne = document.getElementById("ui-row-one");
		var uiRowTwo = document.getElementById("ui-row-two");
		var uiRowThree = document.getElementById("ui-row-three");
		//alert("row 1 " + uiRowOne.style.display);
		//alert("row 2 " + uiRowTwo.style.display);
		//alert("row 3 " + uiRowThree.style.display);
		if (uiRowOne.style.display == "block") {
			// row one is visible - so make 2 visible
			uiRowOne.style.display = "none";
			uiRowTwo.style.display = "block";
			uiRowThree.style.display = "none";
			//alert("is one - change to two");
		}
		else {
			if (uiRowTwo.style.display == "block") {
				// row two is visible - so make 3 visible
				uiRowOne.style.display = "none";
				uiRowTwo.style.display = "none";
				uiRowThree.style.display = "block";
				//alert("is two change to three");

			}
			else {
				// three is visibe - so make one visible
				uiRowOne.style.display = "block";
				uiRowTwo.style.display = "none";
				uiRowThree.style.display = "none";
				//alert("is three change to one");
			}
		}
	}
    </script>
	
</head>
<body onload="detectCSSAnimation()">
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an outdated browser. <a href="https://browsehappy.com/">Upgrade your browser today</a> or <a href="https://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
            <![endif]-->

<div id="container">
	<div id="logo" style="float:left;">
		<a href="https://baynature.org">Back to <img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/bnLogo.png" alt="logo"></a>
	</div>
		<div id="sm" style="float:right;">
		<!-- Buttons start here. -->
		<?php get_template_part('partials/bn-social-media'); ?>
		<!-- Buttons end here -->
	</div><br>
	<div id="title">
		<h1 >Harvest of Fire: Burn to Bounty on Mount Diablo</h1>
	</div>
	<div id="description" class="hidden-lg	hidden-md">
		<p>The Morgan Fire in 2013 turned 3,100 acres of Mount Diablo into what looked like a moonscape. But beneath the scorched earth lay the seeds of a remarkable, once-in-a-lifetime transformation. In the springs following the fire, plants bloomed that hadn't been seen on the mountain in decades -- and might not be seen again for another generation.</p>
	</div>

	<div id="nav" class="hidden-xs" style="border: 0px solid blue;">
		<ul style="margin:5px 0px 5px 0px;">
			<li data-id="panelOne" class="time-nav">September 2013</li>
			<li data-id="panelTwo" class="time-nav">Spring 2014</li>
			<li data-id="panelThree" class="time-nav">Spring 2015</li>
		</ul>
	</div>

	<div id="main-image" class="hidden-xs" style="border: 0px solid red; padding:0px; text-align:center; min-width:12px;">
		<img id="spread" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/spread.jpg" style="position:relative; padding:0px;" width=800 height=400 alt="Scientific illustration of the Mount Diablo fire in three phases."/>
		<img id="panelOne" class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/panelOne.png">
		<img id="panelTwo" class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/panelTwo.png">
		<img id="panelThree" class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/panelThree.png">
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0000_Whispering-Bells.png">
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0001_Tarantula.png" >
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0002_Poppy.png" >
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0003_Pocket-Mouse.png" >
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0004_Mt-Diablo-Fairy-Lantern.png" >
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0005_Morning-Glory.png" >
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0006_Kellogs-Snapdragon.png" >
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0007_Fremont-Star-Lily.png" >
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0008_Fire-Poppy.png" >
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0009_CharcoalBeetle.png">
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0010_Chamise.png">
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0011_California-Thrasher.png" >
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0012_Brewers-Calandrinia.png" >
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0013_Ash-Throated-Flycatcher.png" >
		<img class="overlay" src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/overlay_0014_Alameda-Whipsnake.png" >

	</div>
	<div id="slider" style="display:none">
		<figure>
			<div id="specimen-nav">
				<div data-id="snake" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px">
					<div style="text-align:center">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Alameda-Whipsnake.png">
					<p>1 - Alameda Whipsnake</p>
					</div>
				</div>
				<div data-id="brewer" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Brewers-Calandrinia.png" width="125px"  height="100px"  >
					<p>2 - Brewer’s Red Maids</p>
				</div>
				<div data-id="californiatrasher" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/California-Thrasher.png" width="125px"  height="100px"  >
					<p>3 - California Thrasher</p>
				</div>
				<div data-id="chamise" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Chamise.png" width="125px"  height="100px" >
					<p>4 - Chamise</p>
				</div>
				<div data-id="charcoalbeetle" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px" >
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/CharcoalBeetle.png"  width="125px"  height="100px" >
					<p>5 - Charcoal Beetle</p>
				</div>
				<div data-id="fremont" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Fremont-Star-Lily.png"  width="125px"  height="100px" >
					<p>6 - Fremont Star Lily</p>
				</div>
				<div data-id="snapdragon" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Kellogs-Snapdragon.png" width="125px"  height="100px" >
					<p>7 - Kellog's Snapdragon</p>
				</div>
				<div data-id="morning" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Morning-Glory.png" width="125px"  height="100px" >
					<p>8 - Morning Glory</p>
				</div>
				<div data-id="fairylantern" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Mt-Diablo-Fairy-Lantern.png"  width="125px"  height="100px" >
					<p>9 - Mount Diablo Fairy Lantern</p>
				</div>
				<div data-id="californiapocket" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Pocket-Mouse.png"  width="125px"  height="100px" >
					<p>10 - California Pocket Mouse</p>
				</div>
				<div data-id="californiapoppy" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Poppy.png"  width="125px"  height="100px" >
					<p>11 - California Poppy</p>
				</div>
				<div data-id="tarantula" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Tarantula.png" width="125px"  height="100px" >
					<p>12 - Tarantula</p>
				</div>
				<div data-id="whispering" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Whispering-Bells.png" width="125px" height="100px">
					<p>13 - Whispering Bells</p>		
				</div>
				<div data-id="firepoppy" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Fire-Poppy.png" width="125px" height="100px">
					<p>14 - Fire Poppy</p>
				</div>
				<div data-id="ash" style="width:250px; height:200px; border:1px solid white; float:left;  padding-left: 100px; padding-top:40px">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Ash-Throated-Flycatcher.png" width="125px" height="100px">
					<p>15 - Ash Throated Flycatcher</p>
				</div>
			</div>
		</figure>
	</div>
	<div id="credits" style="padding:10px 20px 5px 20px; border: 0px solid blue">
		<small>Illustration by Laura Cunningham | Funding provided by LSA Associates, Mount Diablo Interpretive Association, Nomad Ecology, and Scott and Claudia Hein | Developed by Lo Benichou</small>
	</div>
	<div id="altUI" style="display:none">
	<a onclick="showNextUiRow()">click here to show more species choices in the image below.</a>
		<div id="specimen-nav" style="margin-top:5px;">
			<div id="ui-row-one" class="ui-row show-row" style="display:block">
				<div data-id="snake" style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Alameda-Whipsnake.png">
					<p>1 - Alameda Whipsnake</p>
				</div>
				<div data-id="brewer" style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Brewers-Calandrinia.png" >
					<p>2 - Brewer’s Red Maids</p>
				</div>
				<div data-id="californiatrasher" style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/California-Thrasher.png" >
					<p>3 - California Thrasher</p>
				</div>
				<div data-id="chamise" style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Chamise.png">
					<p>4 - Chamise</p>
				</div>
				<div data-id="charcoalbeetle"  style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/CharcoalBeetle.png" >
					<p>5 - Charcoal Beetle</p>
				</div>
				<div data-id="fremont" style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Fremont-Star-Lily.png" >
					<p>6 - Fremont Star Lily</p>
				</div>
			</div>
			<div id="ui-row-two" class="ui-row hide-row" style="display:none">
				<div data-id="snapdragon"  style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Kellogs-Snapdragon.png">
					<p>7 - Kellog's Snapdragon</p>
				</div>
				<div data-id="morning"  style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Morning-Glory.png">
					<p>8 - Morning Glory</p>
				</div>
				<div data-id="fairylantern" style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Mt-Diablo-Fairy-Lantern.png" >
					<p>9 - Mount Diablo Fairy Lantern</p>
				</div>
				<div data-id="californiapocket" style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Pocket-Mouse.png" >
					<p>10 - California Pocket Mouse</p>
				</div>
				<div data-id="californiapoppy" style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Poppy.png" >
					<p>11 - California Poppy</p>
				</div>
				<div data-id="tarantula" style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Tarantula.png">
					<p>12 - Tarantula</p>
				</div>
			</div>
			<div id="ui-row-three" class="ui-row hide-row" style="display:none">
				<div data-id="whispering" style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Whispering-Bells.png" width="200" height="237">
					<p>13 - Whispering Bells</p>
				</div>
				<div data-id="firepoppy" style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Fire-Poppy.png" width="122" height="237">
					<p>14 - Fire Poppy</p>
				</div>
				<div data-id="ash" style="float:left">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/img/Ash-Throated-Flycatcher.png" width="200" height="201">
					<p>15 - Ash Throated Flycatcher</p>
				</div>
			</div>
		</div>
	<div id="credits" style="height:30px;">
		<small>Illustration by Laura Cunningham | Funding provided by LSA Associates, Mount Diablo Interpretive Association, Nomad Ecology, and Scott and Claudia Hein | Developed by Lo Benichou</small>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="modalTitle"></h3>
      </div>
      <div class="modal-body">
      <div id="latin">
        </div>
        <div id="species_desc">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<style type="text/css">
/* fixes */
.featured-date-grid { margin-right: 8px; }
.featured-grid { height: auto; min-height: 485px; }
.featured-grid p small { font-size: 12px; line-height: 12px; color: #afafaf; }
.featured-title-grid h4 { margin: 0px; font-style: normal; }
.featured-title-grid { font-style: italic; }
.featured-author-grid { margin: 0 0 8px 0; color: #afafaf;}
.featured-author-grid .author { color: #7a7a7a;}
.featured-image-grid { margin: 0; padding: 0 0 1em 0; }

#bn-footer {
    clear: both;
    font-size: 14px;
    font-weight: bold;
    margin: 70px 0 40px 0;
    padding: 0 0 1em;
    text-transform: uppercase;
    text-align: center;
}
#bn-footer ul {
	padding: 0;
}
#bn-footer li {
    display: inline;
    padding-right: 0.9em;
    padding-left: 0.9em;
}
.cse .gsc-control-cse, .gsc-control-cse {
	background-color: #000;
	border: none;
	width: 300px;
	margin: 0 auto;
}

.dropcap {
    color: #E86223;
    font-family: $title-font;
    font-size: 124px;
    font-weight: 400;
    float: left;
    line-height: .75;
    margin: 10px 0px 0;
    padding: 0 5px 0;
}

.whiteText {
	color : #ffffff;
} 
.italicText {
	font-style: italic;
}
@-webkit-keyframes slidy {
0% { left: 0%; }
15% { left: 0%; }
20% { left: -100%; }
35% { left: -100%; }
40% { left: -200%; }
55% { left: -200%; }
60% { left: -300%; }
75% { left: -300%; }
80% { left: -400%; }
100% { left: -400%; }
}

body { margin: 0; } 
div#slider { overflow: hidden; }
div#slider figure img {  float: left; }
div#slider figure { 
  position: relative;
  width: 500%;
  margin: 0;
  left: 0;
  text-align: center;
  font-size: 30;
  animation: 30s slidy infinite; 
}

.ui-row {
	
	width : 100%;
}

.hide-row {
	display:none;
}

.show-row {
	display:block;
}

</style>

<div class="container">

<?php 
//echo "postID = $post->ID";
echo '<div class="featured-description"><p class="whiteText">'.get_post_meta($post->ID, 'mount_diablo', true) . '</p></div>';

echo '<div class="container">';
		/*echo '<h1>Stories from the Mountain</h1>';
		//wp_reset_postdata();
		
		//News and Updates loop, grid with thumbnails 
		$args = array (
			'category_name' => 'diablo-recovery',
			'posts_per_page'=> '-1',
			'post_type' 	=> 	'article'
		);
		
		
		$query = new WP_Query( $args);
		
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				echo '<div class="featured-grid">';		
					echo '<div class="featured-title-grid"><h4><a href="'.get_permalink().'">' .get_the_title().'</a></h4></div>';		
					echo '<div class="featured-author-grid">By ' .get_the_author().'</div>';
					echo '<div class="featured-date-grid">' .get_the_date().'</div>';
					
			  		if ( has_post_thumbnail() ) {		  
				  	  echo '
					  	<div class="featured-image-grid">
						  <a href="'.get_permalink().'"><img src="' . get_thumb_url(get_thumbnail_src($post->ID),300,200) . '" alt="" /></a>
					  	</div>
					  
				  	  ';
			  		}	
			  	  echo '<p>'. get_the_excerpt(). '</p>';	
			  echo '</div>';	
				
			}
		} else {
			echo '<h1> Sorry, no posts found</h1>';
		}*/
				echo '<h1>Stories from the Mountain</h1>';
		//wp_reset_postdata();
		
		//News and Updates loop, grid with thumbnails 
		$args = array (
			'category_name' => 'diablo-recovery',
			'posts_per_page'=> '-1',
			'post_type' 	=> 	'article'
		);
		
		
		$query = new WP_Query( $args);
		$count = 0;
		
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$count++;
				echo '<div class="col-md-4 featured-grid">';
				if ( has_post_thumbnail() ) {		  
				  	  echo '
					  	<div class="featured-image-grid">
						  <a href="'.get_permalink().'"><img src="' . get_thumb_url(get_thumbnail_src($post->ID),300,200) . '" alt="" /></a>
					  	</div>
					  
				  	  ';
			  		}else {
			  			//echo '<div style="clear: both;"></div>';
			  		}
					echo '<div class="featured-title-grid"><h4><a href="'.get_permalink().'">' .get_the_title().'</a></h4></div>';
					echo '<div class="featured-title-grid">'.get_field('subtitle').'</div>';			
					echo '<div class="featured-author-grid"><small>by <span class="author">' .get_the_author().'</span> | <span class="date">'.get_the_date().'</span></small></div>';
					//echo '<div class="featured-date-grid">' .get_the_date().'</div>';
			  	  echo '<p style="clear: both;">'. get_the_excerpt(). '<br /><small>' . get_the_category_list(" | ") . '</small></p>';
			  echo '</div>';	
				
			}
		} else {
			echo '<h1> Sorry, no posts found</h1>';
		}
		echo '</div>';
		wp_reset_postdata();

	echo '<div class="container">';
	//From the Magazine		
	   	 echo '<div class="col-md-6 featured-featured-story">';
	   	 echo '<h1>From the Magazine</h1>';
	   	 $post_number = 	get_post_meta($post->ID, 'featured_story_post_number', true);
	   	 $featured_post = get_post( $post_number);
	   	 $title = $featured_post->post_title;
	   	 //TODO: date =  F j, Y
	   	 $date = $featured_post->post_date;
	   	 $excerpt = $featured_post->post_excerpt;
	
	   	 $imgsrc = wp_get_attachment_image_src( get_post_thumbnail_id( $post_number ), "thumbnail");
	   	 //echo '<div class="featured-date-feed">' .$date.'</div>';

	   	 echo '<a href="'. get_permalink($post_number) .'">';
	   	 echo '<h2>' . $title . '</h2>';
	   	 echo '	<img src="'. $imgsrc[0] . '"';
	   	 echo '</a></a><p>'. $excerpt . '</p>';
	     echo '</div>';		

	
	// Events Calendar removed; not being used.
//	echo '<div class="featured-events">';
//	echo '<h3>Events around Mount Diablo</h3>';
	
		//Events tagged Mount Diablo from today - 6 months 


//		global $post;
//		$current_date = date('j M Y');
//		$end_date = date('j M Y', strtotime('180 days'));
//
//		$get_posts = tribe_get_events(array
//			(
//		'start_date'=>$current_date,
//		'end_date'=>$end_date,
//		'category_name' => 'diablo-recovery',
//		'posts_per_page'=>5) 
//	     );
//
//		foreach($get_posts as $post) { 
//		
//		echo '<li>';
//		echo '<div class="featured-date-feed">' . tribe_get_start_date().'</div>';
//		echo '<div>';
//		echo 	'<a href="' .  get_permalink() . '">'. get_the_title() . '</a>';
//		echo '</li>';
//	}
//		
//		echo '</div>';
//	echo '</div>';
//
//		wp_reset_query(); 
//
	
	//iNaturalist widget
	echo '<div class="col-md-6 inaturalist-widget">
	<p><strong>What are people spotting right now? Here are the most recent observations from the burn area in iNaturalist</strong></p>
	<style type="text/css" media="screen">
	.inat-widget { font-family: Georgia, serif; padding: 10px; line-height: 1;}
	.inat-widget-header {margin-bottom: 10px;}
	.inat-widget td {vertical-align: top; padding-bottom: 10px;}
	.inat-label { color: #888; }
	.inat-meta { font-size: smaller; margin-top: 3px; line-height: 1.2;}
	.inat-observation-body, .inat-user-body { padding-left: 10px; }
	.inat-observation-image {text-align: center;}
	.inat-observation-image, .inat-user-image { width: 48px; display: inline-block; }
	.inat-observation-image img, .inat-user-image img { max-width: 48px; }
	.inat-observation-image img { vertical-align: middle; }
	.inat-widget-small .inat-observation-image { display:block; float: left; margin: 0 3px 3px 0; height:48px;}
	.inat-label, .inat-value, .inat-user { font-family: "Trebuchet MS", Arial, sans-serif; }
	.inat-user-body {vertical-align: middle;}
	.inat-widget td.inat-user-body {vertical-align: middle;}
	</style>
	<div class="inat-widget">
	    <div class="inat-widget-header"><a href="https://www.inaturalist.org/" target="_blank"><img alt="iNaturalist.org" src="https://www.inaturalist.org/images/logo-small.gif?1394419672" /></a></div>
	  <script type="text/javascript" charset="utf-8" src="//www.inaturalist.org/observations.widget?layout=large&limit=3&order=desc&order_by=observed_on&place_id=60132" target="_blank"></script>
	  <table>
	    <tr class="inat-user">
	      <td class="inat-value" colspan="2">
	        <strong>
	            <a href="https://www.inaturalist.org/places/morgan-fire-perimeter" target="_blank">View more observations near Morgan Fire Perimeter on <nobr>iNaturalist.org »</nobr></a>
	        </strong>
	      </td>
	    </tr>
	  </table>
	</div>
	
	</div>';


echo '</div>';  // end of Section 3
		?>
	</div>





            <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
            <script>window.jQuery || document.write('<script src="bower_components/jquery/dist/jquery.min.js"><\/script>')</script>
            <script src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/js/plugins.js"></script>
            <script src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/js/main.js"></script>

            <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
            <script>
                var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
                (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
                    g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
                    s.parentNode.insertBefore(g,s)}(document,'script'));
                </script>
                <script>  
                   var polyfilter_scriptpath = 'css/polyfills/css-filters-polyfill/lib/';  
               </script>
               <script src="<?php echo get_stylesheet_directory_uri(); ?>/pages/diablo/css/polyfills/css-filters-polyfill/lib/cssParser.js"></script>
              
               <!-- Latest compiled and minified JavaScript -->
               <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>



<?php get_footer(); ?>
	