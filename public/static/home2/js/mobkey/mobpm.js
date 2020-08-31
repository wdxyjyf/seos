$(function(){
  //回车按键
  $(document).keyup(function(event){
  　　if(event.keyCode ==13){
  　　　　$("#selectbtn").trigger("click");
  　　}
  });
  select_change('#engine');
  $("#selectbtn").click(function(){
    $.post("/home/mobilekeywords/toplimit", {type:'rank_querynum'}, function(res){
      if(res.code == 0) {
        $.sendError(res.msg, 1000, function(){
          location.reload();
        })
        return false;
      } else if (res.code == 2) {
        selectError('今日移动端排名查询次数已达上限，是否升级会员组获取更多次数');
      } else {
        //网址
        var ydurl = $.trim($(".ydurl").val());
        if(ydurl == ''){
          $.sendWarning('请输入网址', 1000);
          return false;
        }
        ydurl = urlFilter(ydurl)
        if (!checkUrl(ydurl)) {
          $.sendError('网站地址格式错误', 1000);
          return false;
        }
        //关键字
        var ydkeywords = $(".ydkeywords").val().trim().replace(/\s/g,"");
        if(ydkeywords == ''){
          $.sendWarning('请输入关键词', 1000);
          return false;
        }
        if (!checkKeyword(ydkeywords)) {
          $.sendError('格式错误,请输入中文,字母或数字', 1000);
          return false;
        }
        if(getByteLen(ydkeywords)>30){
            $.sendError("关键词长度最大15个字符", 1000);
            return false;
        }
        //执行提交
        var engine = $('[name=engine]').val()
        var url = ydurl
        var keyword = BASE64.urlsafe_encode(ydkeywords)
        $('form').attr('action', $('form').attr('action')+"/"+url+"/"+keyword);
        $('.search-div form').submit();
      }
    }, 'json') 
  });
  var mobsign = $('.mobdata').attr('data-sign');
  var mobengine = $('.mobdata').attr('data-engine');
  onemob(mobsign,Date.parse(new Date())/1000);
  function onemob(mobsign, time){
      setTimeout(function(){
        $.post('', {sign:mobsign}, function(res) {
          if (res.code == 0 && (Date.parse(new Date())/1000 - time)<15) {
            onemob(mobsign,time)
          } else {
            $('#rank').html(res.rank?'<span style="color:#423da1;font-weight:bold">'+res.rank+'</span>':"暂无排名")
            $('#flow').html(res.flow?res.flow:"暂无流量")
          }
        })
      }, 1500)
  }
  $('select[name=engine] option').each(function(i,v){
    if ($(v).val() == mobengine) {
      $(v).prop('selected', 'selected')
    }
  })
});