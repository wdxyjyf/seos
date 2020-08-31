$(function(){ 	
	/*点击绑定注册*/
	$("#wxregisterbut").click(function(){
	  	var rmobile = $("#rmobile").val();
	  	var rpass = $("#rpass").val();
	  	if(rmobile == ''){
	  		$.sendWarning('手机号不能为空', 1000);
	  		return false;
	  	}	
	  	var chars = /^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|16[0-9]{1}[0-9]{8}$|19[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$/;
	  	if (!chars.test(rmobile)) {
            $.sendError("手机格式错误,请输入正确的手机号", 1000);
            return false;
        }		  	
	  	if(rpass == ''){
	  		$.sendWarning('密码不能为空', 1000);
	  		return false;
	  	}
	  	$.post('/home/login/bindMobile', {mobile:rmobile, password:rpass}, function(data){
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