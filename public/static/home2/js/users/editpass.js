$(function(){
	layui.use(['form', 'layer'], function () {
		var form = layui.form, $ = layui.jquery;
		form.verify({                           
			pass: [/^[\S]{6,20}$/, '新密码必须6到20位，且不能出现空格']          
		}); 
		form.on('submit(submit)', function (data) {
		    var loading = layer.load(1, {shade: [0.1, '#fff']});         
		    $.post("upass", $(data.form).serialize(), function (res) {
		        layer.close(loading);
		        if (res.code > 0) {
		            layer.msg(res.msg, {time: 1800, icon: 1}, function () {
		                location.href = "/login";
		            });
		        } else {
		            layer.msg(res.msg, {time: 1800, icon: 2});
		        }
		    });
		})
	});     
})