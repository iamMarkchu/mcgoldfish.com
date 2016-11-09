$(document).ready(function () {
  $('[data-toggle="offcanvas"]').click(function () {
    $('.row-offcanvas').toggleClass('active')
  });
  $('.nav-cate').hover(
  	function(){
  		$('.dropdown-menu').show();
  	},
  	function(){
  		$('.dropdown-menu').hide();
  	}
  );
  $('.my-comment-data').focus(
    function(){
        $('.my-comment-form').removeClass('hidden');
    });
  $('.my-search').click(function(){
     $(this).toggleClass('long');
  });
  var commentCount = 1;
  $('.my-comment-submit').click(function(){
      var comment = $('.my-comment-data').val();
      var username = $('.my-comment-usernanme').val();
      var useremail = $('.my-comment-useremail').val();
      var articleid = $('#articleid').val();
      $.ajax({
        url: "/ajax/ajaxComment",
        type: "POST",
        data: {action:1,comment:comment,username:username,useremail:useremail,articleid:articleid},
        success:function(data){
          if(data == "1"){
            var commentBody = $('.comment-body.clearfix.small-comment.hidden').clone().removeClass('hidden').addClass('comment_'+commentCount);
            commentBody.find('.comment-user').prepend('<i class="glyphicon glyphicon-user"></i>'+username+'&nbsp&nbsp');
            commentBody.find('.comment-content').append(comment);
            commentBody.appendTo('.comment-frame');
          }else{
            alert('失败');
          }
        }
      });
  });
  if($("#Glide").length>0){
      $("#Glide").glide({
        "paddings": "20%",
        "autoheight": true,
    });
  }
  if($("pre").length>0){
    hljs.initHighlightingOnLoad();
    $('pre').each(function(i, block) {
      hljs.highlightBlock(block);
    });
  }
});