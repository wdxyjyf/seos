$(function(){
	//回车按键
	$(document).keyup(function(event){
	　　if(event.keyCode ==13){
	　　　　$("#selecthot").trigger("click");
	　　}
	});
	
		$(".xianshi2").hide()
		$(".xianshi").show()
		$(".ssb").click(
			function(){
				$('.online').css('display','block');
				$('.online2').css('display','none');
				$(".xianshi2").hide()
				$(".ssb").addClass('seing')
				$(".xianshi").show()
				$(".xzc").removeClass('seing')
			}
		)
		$(".xzc").click(
			function(){
				$('.online2').css('display','block');
				$('.online').css('display','none');
				$(".xianshi").hide()
				$(".xzc").addClass('seing')
				$(".xianshi2").show()
				$(".ssb").removeClass('seing')
			}
		)
		$('.averagePv1').each(function(i,v){
			if (i == 0) {
				averagePv1 = $(v).attr('averagePv');
				$(v).css('width', '300px');
			} else {
				$(v).css('width', averagePv1==0?"300px":($(v).attr('averagePv')/averagePv1)*300+'px')
			}
		})
		$('.averagePv2').each(function(i,v){
			if (i == 0) {
				averagePv2 = $(v).attr('averagePv');
				$(v).css('width', '300px');
			} else {
				$(v).css('width', averagePv2==0?"300px":($(v).attr('averagePv')/averagePv2)*300+'px')
			}
		})
		
		$("#selecthot").click(function(){
			hotclick('keyword_querynum');
		});
		var disable = $('.stopquery').val();
		if (disable == '1') {
			// $('.keyimg').each(function(i,v){
			// 	$(v).css('pointer-events','none')
			// })
			$('.keyimg').click(function(){
				selectError('今日关键词查询次数已达上限，是否升级会员组获取更多次数')
				return false
			})
		}
});