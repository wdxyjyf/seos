$(function(){
  //回车按键
  $(document).keyup(function(event){
  　　if(event.keyCode ==13){
  　　　　$("#selectweb").trigger("click");
  　　}
  });
  //点击提交查询
  $("#selectweb").click(function(){
    $.post("/home/index/toplimit", {}, function(res){
      if(res.code == 0){
        $.sendError(res.msg, 1000, function(){
          location.reload();
        });
        return false;
      }else{
        var input = $.trim($(".website").val());
        if(input == ''){
          $.sendWarning('请输入要查询的网站', 1000);
          return false;
        }
        input = urlFilter(input)
        if (!checkUrl(input)) {
          if (!checkIp(input)) {
            $.sendError('网站地址格式错误', 1000);
            return false;
          }
        }
        location.href = '/'+input;
      }
    }, 'json')    
  });

  $('.website').focus(function (){
    $('.history').show()
  }).blur(function(){
    setTimeout(function() {
      $('.history').hide()
    }, 200)
  })

  $('.history').on('click', '.dd_url', function(e){
    $('.website').val($(e.target).attr('data-url'))
  })

  $('.history').on('click', '.dd_url button', function(e){
    var url = $(e.target).parent().attr('data-url')
    $.post('/home/index/del_url', {url:url}, function(data){
      if (data.code == 1) {
        $(e.target).parent().remove()
      }
    })
    return false
  })
});

function save_history(website, obj){
  var url = $(website).val()
  $.post('/home/index/save_url', {url:url}, function(data){
    if (data.code == 0) {
      $.sendError(data.msg, 1000);
    } else if (data.code == 1) {
      $(obj).after('<dd class="dd_url" data-url="'+url+'"><button>删除</button>'+url+'</dd>')
    }
  })
}