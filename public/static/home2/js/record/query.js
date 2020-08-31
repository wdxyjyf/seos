$(function(){
	//回车按键
	$(document).keyup(function(event){
	　　if(event.keyCode ==13){
	　　　　$("#selecturl").trigger("click");
	　　}
	});
  
	var disable = $('.stopquery').val();
	if (disable == '1') {
		// $('.beianone').each(function(i,v){
		// 	$(v).css('pointer-events','none')
		// })
		$('.beianone').click(function(){
            selectError('今日备案查询次数已达上限，是否升级会员组获取更多次数')
            return false
        })
	}
  	$("#selecturl").click(function(){
    	recordclick('beian_querynum');
  	});
})
