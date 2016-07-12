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
});