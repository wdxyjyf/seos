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
			        var sj = $('#get_code');
			        var rmobile = $("#rmobile").val();
			        var register_token = r.data.token;
			        if(rmobile == ''){
			          $.sendWarning('手机号不能为空', 1000);
			          vaptchaObj.reset()
			          return false; 
			        }
			        if (!checkPhone(rmobile)) {
			            $.sendError("手机格式错误,请输入正确的手机号", 1000);
			            vaptchaObj.reset()
			            return false;
			        }
			        $.post('/home/Login/beforePhoneCode', {mobile:rmobile}, function(data){
			            if(data.code == 1){
			              $.post('/home/Login/sendPhoneCode', {mobile:rmobile, type:1, register_token:register_token}, function(data){
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
			              $.sendError(data.msg, 1000);
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
						


			/*点击注册*/
			$("#registerbut").click(function(){
			  	var rmobile = $("#rmobile").val();
			  	var rpass = $("#rpass").val();
			  	var rcode = $("#rcode").val();
			  	var yaoqingid = $("#yaoqingid").val();
			  	console.log(yaoqingid);
			  	if(rmobile == ''){
			  		$.sendWarning('手机号不能为空', 1000);
			  		return false;
			  	}			  	
			  	if(rpass == ''){
			  		$.sendWarning('密码不能为空', 1000);
			  		return false;
			  	}
			  	if(rcode == ''){
			  		$.sendWarning('验证码不能为空', 1000);
			  		return false;
				}
				if (!$('#is_agree').prop('checked')) {
					$.sendWarning('请阅读并同意使用协议', 1000);
			  		return false;
				}
			  	$.post('/home/login/doRegister', {mobile:rmobile, password:rpass,code:rcode,yaoqingid:yaoqingid}, function(data){
		            if(data.code == 1){
		            	$.sendSuccess(data.msg, 1000);
		            	setTimeout(function(){
							location.href='/'
						},1000);
		            }else{
		            	$.sendError(data.msg, 1000);
		            	return false;
		            }
		        });
		        return false;
			});
			
        })