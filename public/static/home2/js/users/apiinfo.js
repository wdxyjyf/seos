$(function(){
	$(".prodinfo-info li").click(function(){
		$(".prodinfo-info li a").removeClass('active')
		$(this).find('a').addClass('active')
		$('.info').css('display', 'none')
		$('.info-'+$(this).index()).css('display', 'block')
	})
});