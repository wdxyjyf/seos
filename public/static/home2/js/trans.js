$(function () {
    //回车按键
    $(document).keydown(function(event){
    　　if(event.keyCode ==13){
    　　　　$("#changebtn").trigger("click");
    　　}
    });
    
    $("#changebtn").click(function () {
        $.post("/home/falseoriginal/toplimit", {type:"trans"}, function(res){
            if(res.code == 0){
              $.sendError(res.msg, 1000);
              return false;
            } else if (res.code == 2){
                selectError('今日伪原创次数已达上限，是否升级会员组获取更多次数');
            } else{
                var reg = new RegExp('"',"g");//g,表示全部替换。
                var wuck = $('.wuck').val().replace(reg,"'");
                if(wuck == ''){
                    $.sendWarning('请输入要转换的文章内容', 1000);
                    return false;
                }
                var allLength = getByteLen(wuck.trim());
                if(allLength > $('.wuck').attr('maxnum')*2){
                    var msg = "<a href='/login' style='color:#423da1;font-weight:bold;'>登陆</a>"
                    if ($('.wuck').attr('is_login') == 1) {
                        if ($('.wuck').attr('is_max') == 1) {
                            msg = "<a href='http://wpa.qq.com/msgrd?v=3&amp;uin=3004343521&amp;site=qq&amp;menu=yes' style='color:#423da1;font-weight:bold;' target='_blank'>联系客服</a>"
                        } else {
                            msg = "<a href='/authlist' style='color:#423da1;font-weight:bold;'>升级会员</a>"
                        }
                    }
                    $.sendError("转换的文章内容长度最多"+$('.wuck').attr('maxnum')+"个字，<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;如需更多字数上限请"+msg+"。", 5000);
                    return false;
                } else {
                    layui.use('layer', function(){
                        var layer = layui.layer;
                        var index = layer.load(1, {
                            shade: [0.1,'#fff'] ,//0.1透明度的白色背景
                            offset: ['45%','46%'],
                        });
                        $.post("/wyc", {transform:wuck}, function(res){ 
                            if(res.code == 1){
                                var sign = res.sign;
                                wycajax(sign,Date.parse(new Date())/1000);
                            }
                        }, 'json')
                        function wycajax(sign, time){
                            setTimeout(function(){
                                $.post('home/falseoriginal/transForm', {sign:sign}, function(res) {
                                  if (res.code == 0 && (Date.parse(new Date())/1000 - time)<15) {
                                    wycajax(sign,time)
                                  } else {
                                    layer.close(index);
                                    if (res.code == 0) {
                                        $.sendError(res.msg, 1000);
                                        return false;  
                                    } else {
                                        $('.wuck2').val(res.info);
                                    }
                                  }
                                })
                            }, 1500)
                        }
                    });
                }  
            }
        }, 'json') 
    })
});