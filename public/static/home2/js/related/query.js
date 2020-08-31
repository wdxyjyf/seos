$(function(){
	//回车按键
	$(document).keyup(function(event){
	　　if(event.keyCode ==13){
	　　　　$("#selectres").trigger("click");
	　　}
	});
	
    trhoverstyle(1);
    $("#selectres").click(function(){
      relateclick();
    });
    var disable = $('.stopquery').val();
	if (disable == '1') {
		// $('.keyimg').each(function(i,v){
		// 	$(v).css('pointer-events','none')
		// })
		$('.keyimg').click(function(){
            selectError('今日长尾词查询次数已达上限，是否升级会员组获取更多次数')
            return false
        })
	}
});