$(function(){
	$(".pmddd #content tr").css("cursor","pointer");
	$("#copyurl").click(function(){
		var yqurl = "搜一搜站长工具，大数据关键词挖掘，免费网站关键词排名监控，邀请注册即可获得VIP 会员！{$yqurl}";
            new ClipboardJS('#copyurl',
            	{text:function(trigger) { 
            		$.sendSuccess('复制成功', 1000);
            		return yqurl;
            	} 
            });
	});
});