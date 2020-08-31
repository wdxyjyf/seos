$(function(){
  //回车按键
	$(document).keyup(function(event){
  　　if(event.keyCode ==13){
  　　　　$("#selecturl").trigger("click");
  　　}
  });

  $("#selecturl").click(function(){
      recordclick('beian_querynum');
  });
  
  var on = $('.hidedata').attr('data-on');
  var onearr = $('.hidedata').attr('data-onearr');
  var topurl = $('.hidedata').attr('data-topurl');
  if (on == 0) {
    $.post('/beian/'+onearr, {onearr: onearr,topurl:topurl}, function(res) {
      var redlist = res.redlist;
      var str = "<td>"+onearr+"</td>"
      str += "<td>"
        str += redlist.name?redlist.name:"<span style='color:#423da1'>未备案</span>"
        str += "</td><td>"
        str += redlist.name?redlist.nature:"<span style='color:#423da1'>未备案</span>"
        str += "</td><td>"
        str += redlist.name?redlist.record_num:"<span style='color:#423da1'>未备案</span>"
        str += "</td><td>"
        if (redlist.name) {
          str += redlist.status_time?redlist.status_time.substring(0,10):'<img src="/static/home2/images/wh2.png">'
        } else {
          str += "<span style='color:#423da1'>未备案</span>"
        }
        str += "</td><td>"
        str += redlist.name?redlist.create_time.substring(0,10)+'<a href="javascript:;" style="color:#0d8c21;margin-left:10px;">[更新]</a>':"<span style='color:#423da1'>未备案</span><a href='javascript:;' style='color:#0d8c21'>[更新]</a>"
      str += "</td>"
      $('#bainfo').html(str);
    })
  }

  $(document).on('click', 'a:contains(更新)', function(e){
    var bainfo = $('#bainfo').html()
    $('.uniquetab td:not(:first)').html('<img src="/static/home2/images/loading.gif" alt="" style="width:15px;cursor: pointer;line-height: 39px;">')
    $.post('/beian/'+onearr+'/1', {onearr: onearr,topurl:topurl, update:1}, function(res){
      var redlist = res.redlist;
      var str = "<td>"+onearr+"</td>"
      str += "<td>"
      if (redlist.length != 0) {
        str += redlist.name?redlist.name:"<span style='color:#423da1'>未备案</span>"
        str += "</td><td>"
        str += redlist.nature?redlist.nature:"<span style='color:#423da1'>未备案</span>"
        str += "</td><td>"
        str += redlist.record_num?redlist.record_num:"<span style='color:#423da1'>未备案</span>"
        str += "</td><td>"
        if (redlist.name) {
          str += redlist.status_time?redlist.status_time.substring(0,10):'<img src="/static/home2/images/wh2.png">'
        } else {
          str += "<span style='color:#423da1'>未备案</span>"
        }
        str += "</td><td>"
        str += redlist.name?redlist.create_time.substring(0,10)+'<a href="javascript:;" style="color:#0d8c21">[更新]</a>':"<span style='color:#423da1'>未备案</span><a href='javascript:;' style='color:#0d8c21'>[更新]</a>"
        str += "</td>"
        $('#bainfo').html(str);
      } else {
        $('#bainfo').html(bainfo);
      }
    })
  })

});