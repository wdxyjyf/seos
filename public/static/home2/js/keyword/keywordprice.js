$(function(){
    //回车按键
    $(document).keyup(function(event){
    　　if(event.keyCode ==13){
    　　　　$("#selectres").trigger("click");
    　　}
    });

    //注意进度条依赖 element 模块，否则无法进行正常渲染和功能性操作
    layui.use('element', function(){
      var element = layui.element;
    });
    trhoverstyle(1);
    $("#selectres").click(function(){
      hotclick('keyword_price');
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