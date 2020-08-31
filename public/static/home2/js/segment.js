$(function(){
    //回车按键
    $(document).keyup(function(event){
    　　if(event.keyCode ==13){
    　　　　$("#changebtn").trigger("click");
    　　}
    });

    layui.use('layer', function(){layer = layui.layer})
    $("#changebtn").click(function(){
      $.post("/home/ipvsix/toplimit", {}, function(res){
        if(res.code == 0){
          $.sendError(res.msg, 1000, function(){
            location.reload();
          })
          return false;
        }else{
          var wuck = $('.wuck').val()
          $.post('', {changecont:wuck}, function(data){
            if(data.code == 0){
              $.sendError('请输入正确格式的IP网段', 1000);
              return false;
            }else{
              var str = ''
              $.each(data.info, function(i,v) {
                str += v+"\n"
              })
              $('.wuck2').html('')
              $('.wuck2').append(str)
            }
          });
        }
      }, 'json') 
    })
});