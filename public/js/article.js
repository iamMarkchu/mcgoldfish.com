$(function(){
	if($('pre').length > 0){
		// $('pre code').each(function(i, block) {
	 	//    	hljs.highlightBlock(block);
	 	//  	});
	 	//  	
	 	hljs.initHighlightingOnLoad();
	}
	$('.comment-submit').click(function(){
		$('.commment-form').submit();
	});
});