$(function(){
  $("#selecturl").click(function(){
      recordclick('beian_querynum');
  });
  
  var on = $('.hidedata').attr('data-on');
  var onearr = $('.hidedata').attr('data-onearr');
  var topurl = $('.hidedata').attr('data-topurl');
  var other = $('.hidedata').attr('data-other');
  if (on == 0) {
    $.post('/beian',{onearr: onearr,topurl:topurl}, function(res) {
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
    $.post('/beian', {onearr: onearr,topurl:topurl, update:1}, function(res){
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

  if (other == 0) {
      $.post('/home/recordquery/xgWebsite', {keyword: onearr}, function(res) {
        console.log(res);
        var list2 = res.otherlist
        // 关键词查询相关信息
        $('#hotlist2').css('display','none');
        var str = '';
        if (list2.length != 0) {
          for (var i in list2) {
            str += '<tr style="text-align: center;font-size: 14px;line-height: 45px;border:1px solid #f6f6fc;cursor:pointer">'
            str += '<td><a href="/rankweb?website='+list2[i]['website_url']+'">'+list2[i]['website_url']+'</a></td>'
            str += list2[i]['name']?'<td>'+list2[i]['name']+'</td>':'<td><span>未备案</span></td>'
            str += list2[i]['record_num']?'<td>'+list2[i]['record_num']+'</td>':'<td><span>未备案</span></td>'
            str += list2[i]['nature']?'<td>'+list2[i]['nature']+'</td>':'<td><span>未备案</span></td>'
            str += list2[i]['start_time']?'<td>'+list2[i]['start_time']+'</td>':'<td><img src="/static/home2/images/wh2.png" title="暂无数据"></td>'
            str += list2[i]['end_time']?'<td>'+list2[i]['end_time']+'</td>':'<td><img src="/static/home2/images/wh2.png" title="暂无数据"></td>'
            str += list2[i]['status_time']?'<td>'+list2[i]['status_time']+'</td>':'<td><span>未备案</span></td>'
            str += '</tr>'
          }
          $(".content").append(str);
        } else {
          $(".ores").css('display','block');
          $('.ores').text('暂无搜索到 "'+onearr+'" 的相关域名数据,请稍后重试!')
        }
      })
  }
});