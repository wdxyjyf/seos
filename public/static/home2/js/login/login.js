$(function(){
   	/*点击登录*/
	$("#loginbut").click(function(){
	  	var lmobile = $("#lmobile").val();
	  	var lpass = $("#lpass").val();
	  	if(lmobile == ''){
	  		$.sendWarning('手机号不能为空', 1000);
	  		return false;
	  	}			  	
	  	if(lpass == ''){
	  		$.sendWarning('密码不能为空', 1000);
	  		return false;
	  	}
	  	$.post('/home/login/doLogin', {mobile:lmobile, password:lpass}, function(data){
            if(data.code == 1){
            	$.sendSuccess(data.msg, 1000);
            	setTimeout(function(){
					location.href= '/';
				},1000);
            }else{
            	$.sendError(data.msg, 1000);
            }
        });
        return false;
	});
	$.post('/wechatcode', {}, function(data){
  		var img = data.url;
  		var timetamp = data.timetamp;
       	$('.weixinimg').attr('src',img);
       	$('.weixinimg').attr('data-code',timetamp);
       	$('.weixinimg').css('display', 'inline');
       	var time = setInterval(function() {
            $.post('/wxrandcode', {rand:timetamp}, function(res){
            	var info = res.info;
            	$.post('/wxcodeLogin', {info:info}, function(res){
            		if (res.code == 1){
            			clearInterval(time);
            			location.href = '/';
            		} else if(res.code == 2){
            			clearInterval(time);
            			location.href = res.url;
            		} 
            	})
            });
        }, 5000);
    });
	$(".wxlogin").click(function(){
		$('#login_container').css('display','block');
		$('#login_mobile').css('display','none');
		$('.wxlogin').addClass('on');
		$('.moblogin').removeClass('on');
		$.post('/wechatcode', {}, function(data){
	  		var img = data.url;
	  		var timetamp = data.timetamp;
           	$('.weixinimg').attr('src',img);
           	$('.weixinimg').attr('data-code',timetamp);
           	$('.weixinimg').css('display', 'inline');
           	var time = setInterval(function() {
	            $.post('/wxrandcode', {rand:timetamp}, function(res){
	            	var info = res.info;
	            	$.post('/wxcodeLogin', {info:info}, function(res){
	            		if (res.code == 1){
	            			clearInterval(time);
	            			location.href = '/';
	            		} else if(res.code == 2){
	            			clearInterval(time);
	            			location.href = res.url;
	            		} 
	            	})
	            });
	        }, 5000);
        });


		// var content =".impowerBox .qrcode {width: 220px;}.impowerBox .title {display: none;}.impowerBox .info {width: 200px;} .impowerBox .status {text-align: center;}";
  //   	var blob = new Blob([content],{type: "text/css;charset=utf-8"});
  //   	var reader = new FileReader();
  //   	reader.readAsDataURL(blob);
	 //    reader.onload = function(e) {
	 //      var wxqrcode = new WxLogin({
	 //        self_redirect: false, //true将页面跳转放在ifream里面   false直接跳转到要跳转的页面
	 //        id: "login_container",
	 //        appid: "wxb4e5b5db6f2d4aba",
	 //        scope: "snsapi_login",
	 //        redirect_uri:'https://www.seos.vip/home/login/wechatLogin',
	 //        state:'STATE',
	 //        href: this.result
	 //      });
	 //    };
	});
	$(".moblogin").click(function(){
		$('#login_container').css('display','none');
		$('#login_mobile').css('display','block');
		$('.wxlogin').removeClass('on');
		$('.moblogin').addClass('on');
	});
	// 登录切换
 	//    $('.aui-form-header-item').click(function(){
	// 	if ($(this).index() == 1) {
	// 		$('#registform').hide()
	// 		$('.wxewm').show()
	// 	} else {
	// 		$('#registform').show()
	// 		$('.wxewm').hide()
	// 	}
	// 	$('.aui-form-header-item').removeClass('on')
	// 	$(this).addClass('on')
	// })

	//点击微信小图标
	// $(".wximg").click(function(){
	//   	$.post('/home/weixin/openWx', {}, function(data){
  //           location.href= data.url;
 //        });
	// });

})