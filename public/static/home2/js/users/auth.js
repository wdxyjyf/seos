$(function(){
	var userlevel = $('.userinfo').attr('data-level');
	if (userlevel == 3) {
		$('.level2').css('display','none');
		$('.level3').text('立即续费');
		$("#form3").append('<input type="hidden" name="ishas" value="1">'); 
	} else if(userlevel == 2) {
		$("#form2").append('<input type="hidden" name="ishas" value="1">'); 
		$('.level2').text('立即续费');
	}
});