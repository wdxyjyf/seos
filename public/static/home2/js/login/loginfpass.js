$(function(){
    	var time = 60;
		function shijian(sj){
			if(time == 0){
				sj.removeAttr('disabled')
				sj.val('获取验证码')
				time =60;
				return;
			}else{
				sj.attr('disabled', true)
				sj.val('重新发送（'+ time +')')
				time--;
			}
			setTimeout(function(){
				shijian(sj)
			},1000)
		}
		/*点击发送验证码*/ 
		vaptcha({
		    vid: '5e65a297f9f92166464af28a', // 验证单元id
		    type: 'invisible', // 显示类型 隐藏式
		    scene: 0, // 场景值 默认0
		    offline_server: '/home/login/vaptcha' //离线模式服务端地址
		}).then(function (vaptchaObj) {
		    obj = vaptchaObj;
		    vaptchaObj.listen('pass', function() {
		    var data = {
		      token: vaptchaObj.getToken()
		    }
		    $.post('/home/login/vaptcha_check',data, function(r) {
		      if (r.code == 1) {
		        var sj = $("#get_code");
				var fmobile = $("#mobile").val();
				var findpass_token = r.data.token;
				if(fmobile == ''){
					$.sendWarning('手机号不能为空', 1000);
					vaptchaObj.reset()
			  		return false;	
			  	}
			  	if (!checkPhone(fmobile)) {
		            $.sendError("手机格式错误,请输入正确的手机号", 1000);
		            vaptchaObj.reset()
		            return false;
		        }
			  	$.post('/home/Login/findUser', {mobile:fmobile}, function(res){
		            if(res.code == 1){
		            	$.post('/home/Login/sendPhoneCode', {mobile:fmobile, type:2,findpass_token:findpass_token}, function(data){
				            if(data.code == 1){
				            	$.sendSuccess(data.msg, 1000);
				            	shijian(sj);				  			
				            }else{
				            	$.sendError(data.msg, 1000);
				            	vaptchaObj.reset()
				            	return false;
				            }
				        });	
		            }else{
		            	$.sendError(res.msg, 1000);
		            	vaptchaObj.reset()
		            	return false;
		            }
		        });
		      }
		    })
		  })
		})
		$('#get_code').on('click',function(){
		  obj.validate();
		})

		/*提交找回密码*/
		$("#forgotpass").click(function(){
		  	var mobile = $("#mobile").val();
		  	var password = $("#password").val();
		  	var phonecode = $("#phonecode").val();
		  	if(mobile == ''){
		  		$.sendWarning('手机号不能为空', 1000);
		  		return false;
		  	}			  	
		  	if(password == ''){
		  		$.sendWarning('密码不能为空', 1000);
		  		return false;
		  	}
		  	if(phonecode == ''){
		  		$.sendWarning('验证码不能为空', 1000);
		  		return false;
		  	}
		  	$.post('/findpass', {mobile:mobile, password:password,code:phonecode}, function(data){
	            if(data.code == 1){
	            	$.sendSuccess(data.msg, 1000);
	            	setTimeout(function(){
						location.href="/login" 
					},1000);
	            	
	            }else{
	            	$.sendError(data.msg, 1000);
	            	return false;
	            }
	        });
	        return false;
		});
    })