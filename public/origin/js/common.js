$(document).ready(function(){
	$('li.category').hover(
		function(){
			$('.nav_2').fadeIn(500);
		},
		function(){
			$('.nav_2').fadeOut(100);
		}
	);
});