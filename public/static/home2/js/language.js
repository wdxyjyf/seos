$(function(){
    //回车按键
    $(document).keyup(function(event){
    　　if(event.keyCode ==13){
    　　　　$("#changebtn").trigger("click");
    　　}
    });

    $("#changebtn").click(function(){
      $.post("/home/language/toplimit", {}, function(res){
        if(res.code == 0){
          $.sendError(res.msg, 1000, function(){
            location.reload();
          })
          return false;
        }else{
          var wuck = $('.wuck').val();
          if(wuck == ''){
            $.sendWarning('请输入要转化的文本', 1000);
            return false;
          }
          var allLength = getByteLen(wuck.trim());
          if(allLength>2000){
            $.sendError("转化限制字数1000字以内", 1000);
            return false;
          }
          $.post('/pinyin', {changecont:wuck}, function(data){
            if(data.code == 0){
                $.sendError(data.msg, 1000);
                return false;
            }else{
              $('.wuck2').val(data.resval);
            }
          });
        }
      }, 'json') 
      
    })
  });