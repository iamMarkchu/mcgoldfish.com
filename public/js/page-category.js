$(function(){
	for (var i = 9; i >= 0; i--) {
		$('.category-article-list>ul>li:first-child').clone().appendTo('.category-article-list>ul');
	}
});