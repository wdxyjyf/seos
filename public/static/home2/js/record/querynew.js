$(function () {
	//全选或全不选
	$("#all").click(function(){   
    	if(this.checked){   
        	$(".fuxuan :checkbox").prop("checked", true);  
    	}else{   
			$(".fuxuan :checkbox").prop("checked", false);
    	}   
 	}); 
	//设置全选复选框
	$(".fuxuan :checkbox").click(function(){
		allchk();
	});
 	//点击时间添加颜色样式
 	$(".timelist li span").click(function() {
 		$(".timelist li span").removeClass('default2')
 		$(this).addClass('default2')
    });
    //点击省市添加颜色样式
 	$(".province li span").click(function() {
 		$(".province li span").removeClass('default')
 		$(this).addClass('default')
    });
 	//点击自定义显示时间
 	$('.zdy').click(function(){
 		$(this).next().css('display', 'block');
 	})
 	$('.timelist li:lt(3)').click(function(){
 		$("input[name=startime]").val('');
 		$("input[name=endtime]").val('');
 		$('.timelist li:last()').css('display', 'none')
 	})
 	//layui时间格式
 	layui.use('laydate', function(){
 		var laydate = layui.laydate
 		laydate.render({
		    elem: '.start'
		});
 		laydate.render({
		    elem: '.end'
		});
 	})
 	//鼠标悬浮样式
 	trhoverstyle(1);
 	// 点击提交
 	$("#beianbtn").click(function(){  
 		$('.cityval').val($('.default').attr('data-cname'));//省市区
 		var time = $('.default2').attr('data-time');
 		if (time == '4') {
 			if ($('.start').val() == '') {
 				$.sendError('请选择开始时间', 1000);
	            return false;
 			}
 			if ($('.end').val() == '') {
 				$.sendError('请选择结束时间', 1000);
	            return false;
 			}
 			if ($('.end').val() < $('.start').val()) {
 				$.sendError('请选择正确的时间区间', 1000);
	            return false;
 			}
 			$('.beiantimeval').val(time);
 		} else {
 			$('.beiantimeval').val($('.default2').attr('data-time'));  //备案时间
 		}
 		if ($('.urlname').val() != '') {
 			var urlval = $('.urlname').val().trim().replace(/\s/g,"");
 			if (!checkUrl(urlval) && !checkKeyword(urlval)) {
 				if (!checkIp(urlval)) {
 					$.sendError('请输入正确的格式', 1000);
	            	return false;
 				}
	        }
 			$('.urlname').val(urlval);
 		}
 		//执行提交
 		if ($('.default').attr('data-id') == 0) {
 			var city = '全国'
 		} else {
 			var city = $('.default').attr('data-cname');
 		}
 		$('.cityval').val(city);
        // $('.newform').attr('action', $('.newform').attr('action')+"?city="+city)
 		$('.newform').submit();
 	});
 	//提交后的样式
 	var province = $('.fetchdata').attr('data-province');
 	var timestyle = $('.fetchdata').attr('data-time');
 	if(!timestyle) {
 		$('.alltime').addClass('default2');
 	} else {
 		$(".timelist li span").each(function(i,v){
 			$(v).removeClass('default2');
	 		if (timestyle == $(v).attr('data-time')) {
	 			$(v).addClass('default2');
	 		} 
	 		if (timestyle == 4) {
	 			$('.timebox').css('display','block');
	 		}
		});
 	}
 	if(!province) {
 		$('.allprovince').addClass('default');
 	} else {
 		$(".province li span").each(function(i,v){
 			$(v).removeClass('default');
	 		if (province == $(v).attr('data-cname')) {
	 			$(v).addClass('default');
	 		} 
		});
 	}
 	
 	// $('.pagination a').click(function(){
 	// 	$('.pagestyle [name=page]').val($(this).text())
 	// 	$('.pagestyle [name=nature]').val($('.fuxuan [type=checkbox]:checked').map(function(i,v){return $(v).val()}).get().join(','))
 	// 	$('.pagestyle [name=city]').val($('.province .default').text())
 	// })
 	
 	// $('.pagination a').each(function(i,v) {
 	// 	if ($(v).attr('href')) {
 	// 		var pageUrl = '';
 	// 		arr = $(v).attr('href').split("&");
 	// 		$.each(arr, function(ii,vv) {
 	// 			if (vv.substr(vv.length-1,1) != "=") {
 	// 				pageUrl += vv+"&"
 	// 			}
 	// 		})
 	// 		$(v).attr('href', pageUrl.substr(0, pageUrl.length-1))
 	// 	}
	 // })
	 

	/*分页栏点击page*/
    $('.pagination a').each(function(k,v){
        var aval = $(v).attr('href');
        var aval = aval.substr(aval.lastIndexOf('=')+1);
        var beian_maxpage = $('.beian_maxpage').val()
        if (!$(v).hasClass('cur')) {
            $(v).click(function(){
				if (aval-beian_maxpage >0) {
					selectError('您当前最多可以查看'+beian_maxpage+'页，是否升级会员组查看更多数据');
					return false
				}
            })
        }
    })
 	
}); 
function allchk(){
	var chknum = $(".fuxuan :checkbox").size();//选项总个数
	var chk = 0;
	$(".fuxuan :checkbox").each(function () {  
        if($(this).prop("checked")==true){
			chk++;
		}
    });
	if(chknum==chk){//全选
		$("#all").prop("checked",true);
	}else{//不全选
		$("#all").prop("checked",false);
	}
}


