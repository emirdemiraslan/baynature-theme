//lazy loader
$(function() {
	$("img.lazy").lazyload({
		threshold: 900,
		effect : "fadeIn"
	});
});

//social buttons
//opens tweet and fb shares in new browser windows
/*function tweetBtn() {
    var tweetWindow = window.open("https://twitter.com/share?url=http://seangreene.github.io/bn-biodiversity&amp;text=The%20Weird,%20the%20Rare%20and%20the%20Ugly%20via@BayNature&amp;hashtags=biodiversity", "", "width=700, height=253");
}
function fbBtn() {
    var fbWindow = window.open("http://www.facebook.com/sharer.php?u=https://seangreene.github.io/bn-biodiversity", "", "width=700, height=253");
}*/

/*//some scripts for the navigation
var imageURL = [
	'media/illos/tarweed.jpg',
	'media/illos/missionblue.jpg',
	'media/illos/grouse.jpg',
	'media/illos/clapper-rail.jpg',
	'media/illos/azurina.jpg',
	'media/images/filler.jpg',
	'media/images/filler.jpg',
	'media/images/filler.jpg',
	'media/images/filler.jpg',
	'media/images/filler.jpg',
];

//loop to add illustrations
$('.nav-item img').each(function(i){
	$(this).attr("src", imageURL[i])
})*/

//nav-item height = img width
var cw = $('img').width();

//balancing lead box and museum card widths and heights
// var cw = $('.lead-box figure').width();
// $('.lead-box').css({'height':cw+'px'});
// $('.museum-card').css({'height':cw+'px'});

//opens more drawer and fades out the link
$('.more').click(function(){
	//opens drawer
	$('#more-drawer').animate({'height':'500px'}, {duration: 300});
	//fades out more hamburger
	$('.more').fadeOut("slow", "swing");
	//fades in close button
	$('.fa-times').fadeIn("slow", "swing");
	//makes the stories visible
	$('#more-drawer #more-stories').css({'display':'block'});
	//scrolls you to the bottom of the page as the drawer opens up
	$("html, body").animate({ scrollTop: $(document).height() }, "slow", "swing");
		return false;
});

//REMOVES THE NAV ITEMS WE'RE NOT USING FOR NOW, BECAUSE I AM BEING LAZY
//DELETE THESE AS STORIES ARE PUBLISHED
$('.nav6').remove();
$('.nav7').remove();
$('.nav8').remove();
$('.nav9').remove();
$('.nav10').remove();

$('.more6').remove();
$('.more7').remove();
$('.more8').remove();
$('.more9').remove();
$('.more10').remove();

//closes the more drawer and fadesin the more link
$('.fa-times').click(function(){
	//closes drawer
	$('#more-drawer').animate({'height':'0px'}, {duration:300});
	//fades back in hamburger
	$('.more').fadeIn("slow", "swing");
	//fades out close button
	$('.fa-times').fadeOut("slow", "swing");
	//hides the stories again
	$('#more-drawer #more-stories').css({'display':'none'});
});

$(document).ready(function(){
	if ( $('#more-stories div').hasClass('current_story') ) {
		$('.current_story a').contents().unwrap();
	}

});