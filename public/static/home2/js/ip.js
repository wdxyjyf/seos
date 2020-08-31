$(function(){
    //回车按键
    $(document).keyup(function(event){
    　　if(event.keyCode ==13){
    　　　　$("#changebtn").trigger("click");
    　　}
    });

    layui.use('layer', function(){layer = layui.layer})
    $('.yuanwen,.wycwz').click(function(){
      if ($('.yuanwen').html() == "IPV6地址") {
        $('.yuanwen').html("IPV4地址")
        $('.wuck[name=changecont]').attr('placeholder', '请输入要转换的IPV4地址')
      } else {
        $('.yuanwen').html("IPV6地址")
        $('.wuck[name=changecont]').attr('placeholder', '请输入要转换的IPV6地址')
      }
      if ($('.wycwz').html() == "IPV6地址") {
        $('.wycwz').html("IPV4地址")
        $('.wuck[name=changecont]').attr('placeholder', '请输入要转换的IPV6地址')
      } else {
        $('.wycwz').html("IPV6地址")
        $('.wuck[name=changecont]').attr('placeholder', '请输入要转换的IPV4地址')
      }
    })
    $("#changebtn").click(function(){
      $.post("/home/ipvsix/toplimit", {}, function(res){
        if(res.code == 0){
          $.sendError(res.msg, 1000);
          return false;
        }else{
          var type = $('.yuanwen').html() == "IPV6地址"?1:2
          var wuck = $('.wuck').val();
          if(wuck == ''){
            if ($('.yuanwen').html() == "IPV6地址") {
              $.sendWarning('请输入要转换的IPV6地址', 1000);
              return false;
            }else{
              $.sendWarning('请输入要转换的IPV4地址', 1000);
              return false;
            }
          }
          var index = layer.load(1, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
          });
          $.post('ipchange', {changecont:wuck,type:type}, function(data){
            if(data.code == 0){
                layer.close(index)
                $.sendError(data.msg, 1000);
                return false;
            }else{
              layer.close(index)
              $('.wuck2').val(data.resval);
            }
          });
        }
      }, 'json') 
    })
  });