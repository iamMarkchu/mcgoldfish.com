$(function() {
	var url = window.location.href;
	if(url.indexOf('#')){
		tmp = url.split('#');
		block = tmp[1];
		if($('h2[data-toggle=\''+block+'\']').length){
			sHeight = $('h2[data-toggle=\''+block+'\']').offset().top-$(document).scrollTop();
			$("html,body").animate(
				{
					scrollTop: sHeight
				}
			, 1000);
		}
	}
	$('.my-cate ul>li').hover(function(){
		if($(this).children('.pop-cate').hasClass('show')){
			$(this).children('.pop-cate').removeClass('show');
		}else{
			$('.my-cate ul>li').children('.pop-cate.show').removeClass('show');
			$(this).children('.pop-cate').addClass('show');
		}
	});
	$('.exp-menu').click(function(){
		$('.my-cate').css({
    		'width': '100%',
    		'top': '66px'
    	});
    	$(this).addClass('off');
	});
	$('.exp-menu.off').click(function(){
		$('.my-cate').css({
    		'width': '0',
    		'top': '0'
    	});
    	$(this).removeClass('off');
	});
	$('.good-vote').click(function(){
		that = $(this);
        articleid = $(this).attr('data-id');
        actiontype = 'do-article-vote';
        $.ajax({
        	url: '/ajax/action',
        	data: {id:articleid, action: actiontype},
        	method: 'POST',
        	success: function(data){
        		if(data == '1'){
        			var old = that.children('span.vote').html();
        			that.children('span.vote').html(parseInt(old)+1);
        		}else{
        			alert('您已经点过赞了！谢谢您的参与');
        		}
        	}
        });
	});
	$('.hot-comment').click(function(){
		var articleurl = $(this).parents('.panel-footer').siblings('.panel-body').find('a.data-article-url').attr('href');
		window.location.href = articleurl + "#comment-block";
	});
});
	