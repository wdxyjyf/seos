$(function(){
    //回车按键
    $(document).keydown(function(event){
    　　if(event.keyCode ==13){
    　　　　$("#selectqz").trigger("click");
    　　}
    });
  
    $("#selectqz").click(function(){
      $.post("/home/quanzhong/toplimit", {}, function(res){
        if(res.code == 0){
          $.sendError(res.msg, 1000, function(){
            location.reload();
          })
          return false;
        }else if (res.code == 2){
          selectError('今日词频查询次数已达上限，是否升级会员组获取更多次数');
        } else{
          var input = $.trim($(".qzweb").val());
          var input2 = $(".qzkey").val().trim().replace(/\s/g,"");
          if(input == ''){
            $.sendWarning('请输入网址', 1000);
            return false;
          }
          if (!checkqzurl(input)) {
            $.sendError('网站地址格式错误', 1000);
            return false;
          }
          if(input2 !== ''){
            if (!checkKeyword(input2)) {
              $.sendError('关键词格式错误,请输入中文,字母或数字', 1000);
              return false;
            }
            if( getByteLen(input2)>30){
                $.sendError('关键词长度最大15个字符', 1000);
                return false;
            }
          }
          input = input.replace('http://', '')
          input = input.replace('https://', '')
          $('.qzweb').val(input)
          input = urlFilter(input)
          if (input2 == '') {
            $('.search form').attr('action', '/rate/'+input)
            $('.search form').submit()
            // location.href='/rate/'+input;
            // $('form').attr('action', $('form').attr('action')+"?web="+input)
          } else {
            $('.search form').attr('action', '/rate/'+input+'/'+BASE64.urlsafe_encode(input2))
            $('.search form').submit()
            // location.href='/rate/'+input+'/'+input2;
            // $('form').attr('action', $('form').attr('action')+"?web="+input+"&key="+input2)
          }
          // $('.search form').submit();
        }
      }, 'json')
    });
});