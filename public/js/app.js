$(function() {
	// var count = 10;
	// for (var i = 0; i <= count; i++) {
	// 	var liObj = $('.ready-clone').clone().removeClass('ready-clone').appendTo('.article-list>ul');
	// }

	$('.my-cate ul>li').hover(function(){
		if($(this).children('.pop-cate').hasClass('show')){
			$(this).children('.pop-cate').removeClass('show');
		}else{
			$('.my-cate ul>li').children('.pop-cate.show').removeClass('show');
			$(this).children('.pop-cate').addClass('show');
		}
	});
	$('.exp-menu').click(function(){
		if($('.my-cate').css('right') == '-70px')
			cate_distance = 0;
		else
			cate_distance = '-70px';

		$('.my-cate').animate({
			right: cate_distance
		}, 300);
	});
	
	$('.login-btn').click(function(){
		$('.page-login').toggleClass('show');
	});

	$('.comment-submit').click(function(){
		$('.commment-form').submit();
	});
	// if($(hljs)){
	// 	$('pre').each(function(i, block) {
	//     	hljs.highlightBlock(block);
	//   	});
	// }

});