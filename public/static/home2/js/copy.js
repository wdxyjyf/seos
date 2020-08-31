$(function(){
    var url = getUserinfo()[2];
    $(".pmddd #content tr").css("cursor","pointer");
	$("#copyurl").click(function(){
		var yqurl = "搜一搜站长工具，大数据关键词挖掘，免费网站关键词排名监控，邀请注册即可获得VIP 会员！"+url;
        new ClipboardJS('#copyurl',
        	{text:function(trigger) { 
        		$.sendSuccess('复制成功',1000);
        		return yqurl;
        	} 
        });
	});

    $('#token').click(function(){
        $('#tokeninput').select()
        document.execCommand('copy')
        $.sendSuccess('复制成功', 1000)
    })

    $('.pagination a').each(function(k,v){
        var aval = $(v).attr('href');
        var aval = aval.substr(aval.lastIndexOf('=')+1);
        if (!$(v).hasClass('cur')) {
            $(v).click(function(){
                $(v).attr('href','/plist/'+aval);
            })
        }
    })
	
});