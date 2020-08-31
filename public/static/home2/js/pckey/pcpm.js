$(function(){
  //回车按键
  $(document).keyup(function(event){
  　　if(event.keyCode ==13){
  　　　　$("#selectbtn").trigger("click");
  　　}
  });
  select_change('#engine');
  $("#selectbtn").click(function(){
    $.post("/home/pckeywords/toplimit", {type:'rank_querynum'}, function(res){
      if(res.code == 0){
        $.sendError(res.msg, 1000, function(){
          location.reload();
        })
        return false;
      } else if (res.code == 2) {
        selectError('今日PC端排名查询次数已达上限，是否升级会员组获取更多次数');
      } else{
        //网址
        var pcurl = $.trim($(".pcurl").val());
        if(pcurl == ''){
          $.sendWarning('请输入网址', 1000);
          return false;
        }
        pcurl = urlFilter(pcurl)
        if (!checkUrl(pcurl)) {
          $.sendError('网站地址格式错误', 1000);
          return false;
        }
        //关键字
        var pckeywords = $(".pckeywords").val().trim().replace(/\s/g,"");
        if(pckeywords == ''){
          $.sendWarning('请输入关键词', 1000);
          return false;
        }
        if (!checkKeyword(pckeywords)) {
          $.sendError('关键词格式错误', 1000);
          return false;
        }
        if(getByteLen(pckeywords)>30){
            $.sendError("关键词长度最大15个字符", 1000);
            return false;
        }
        //执行提交
        var engine = $('[name=engine]').val()
        var url = pcurl
        var keyword = BASE64.urlsafe_encode(pckeywords)
        
        $('form').attr('action', $('form').attr('action')+"/"+url+"/"+keyword)
        $('.search-div form').submit();
      }
    }, 'json') 
  });
  var pckeywords = $(".pckeywords").val().trim().replace(/\s/g,"");
  var pcsign = $('.pmdata').attr('data-sign');
  var pcengine = $('.pmdata').attr('data-engine');
  onepc(pcsign,Date.parse(new Date())/1000);
  function onepc(pcsign, time){
      setTimeout(function(){
        $.post('', {sign:pcsign}, function(res) {
          if (res.code == 0 && (Date.parse(new Date())/1000 - time)<15) {
            onepc(pcsign,time)
          } else {
            if (pcengine == 0) {
              $('#rank').html(res.rank?'<span style="color:#423da1;font-weight:bold"><a style="color:#423da1;" target="_blank" href="https://www.baidu.com/s?wd='+pckeywords+'&rn=50">'+res.rank+'</span>':"暂无排名")
            } else {
              $('#rank').html(res.rank?'<span style="color:#423da1;font-weight:bold">'+res.rank+'</span>':"暂无排名")
            }
            $('#flow').html(res.flow?res.flow:"暂无流量")
          }
        })
      }, 1500)
  }
  $('select[name=engine] option').each(function(i,v){
    if ($(v).val() == pcengine) {
      $(v).prop('selected', 'selected')
    }
  })
});