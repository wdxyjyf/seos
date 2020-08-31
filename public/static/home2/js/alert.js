//弹窗
function center(obj) {
	var screenWidth = $(window).width();
	var screenHeight = $(window).height();
	var scrolltop = $(document).scrollTop();
	var objLeft = (screenWidth - obj.width()) / 2;
	var objTop = (screenHeight - obj.height()) / 2 + scrolltop;
	obj.css({
		left: objLeft + 'px',
		top: "40%",
		top: objTop + 'px',
		'display': 'block'
	});
	/*$(window).resize(function() {
		screenWidth = $(window).width();
		screenHeight = $(window).height();
		scrolltop = $(document).scrollTop();
		objLeft = (screenWidth - obj.width()) / 2;
		objTop = (screenHeight - obj.height()) / 2 + scrolltop;
		obj.css({
			left: objLeft + 'px',
			top:"40%",
			top: objTop + 'px',
			'display': 'block'
		});
	});*/
	//滚动条
	$(window).scroll(function() {
		screenWidth = $(window).width();
		screenHeight = $(window).height();
		scrolltop = $(document).scrollTop();
		objLeft = (screenWidth - obj.width()) / 2;
		objTop = (screenHeight - obj.height()) / 2 + scrolltop;
		obj.css({
			left: objLeft + 'px',
			top: "40%",
			top: objTop + 'px'
		});
	});
}

function check(obj1, obj2) {
	obj1.click(function() {
		//obj.remove();
		close($('.myMask'), $('.myDlg'));

	});
	obj2.click(function() {
		close($('.myMask'), $('.myDlg'));

	});
}

function close(obj1, obj2) {
	obj1.hide();
	obj2.hide();
}
/*$(".myMask").click(function() {
	close($('.myMask'), $('.myDlg'));
})*/
$(function() {
	$(".mackbg,.search").click(function() {
		$(".myMask").hide();
		$(".myDlg").hide();
	})

})