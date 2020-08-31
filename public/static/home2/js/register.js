$(function(){
	var time = 60;
	function shijian(sj){
		if(time == 0){
			sj.removeAttribute('disabled');
			sj.value = '获取验证码';
			time =60;
			return;
		}else{
			sj.setAttribute('disabled',true);
			sj.value = '重新发送（'+ time +')';
			time--;
		}
		setTimeout(function(){
			shijian(sj)
		},1000)
	}
	/*点击发送验证码*/ 
	$("#get_code").click(function(){
		var sj = this;
		var rmobile = $("#rmobile").val();
		var register_token = $("#register_token").val();
		if(rmobile == ''){
			$.sendWarning('手机号不能为空', 1000);
	  		return false;	
	  	}
	  	if (!checkPhone(rmobile)) {
            $.sendError("手机格式错误,请输入正确的手机号", 1000);
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
		            	return false;
		            }
		        });	
            }else{
            	$.sendError(data.msg, 1000);
            	return false;
            }
        });
	});
	/*点击注册*/
	$("#registerbut").click(function(){
	  	var rmobile = $("#rmobile").val();
	  	var rpass = $("#rpass").val();
	  	var rcode = $("#rcode").val();
	  	var yaoqingid = $("#yaoqingid").val();
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
	  		$.sendWarning('您必须同意使用协议，否则将无法成为我们的用户', 1500);
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