$(function(){
  //回车按键
  $(document).keyup(function(event){
  　　if(event.keyCode ==13){
  　　　　$("#selecthot").trigger("click");
  　　}
  });
  
    var kw = $('.hotall').attr('data-kw');
    var on = $('.hotall').attr('data-on');
    var ontwo = $('.hotall').attr('data-ontwo');
    trhoverstyle(2);
    $("#selecthot").click(function(){
      hotclick('keyword_querynum'); 
    });
    if (on == 0 ) {
      $.post('/home/keywords/ajaxhotrecord', {keyword: kw}, function(res) {
        console.log(res);
        var list = res.hotList
        if (list) {
          $('#zhou').html(list.averagePv?list.averagePv:0)
          $('#zhoupc').html(list.averagePvPc?list.averagePvPc:0)
          $('#zhouyd').html(list.averagePvMobile?list.averagePvMobile:0)
          $('#risol').html(list.averageDayPv?list.averageDayPv:0)
          $('#risolpc').html(list.averageDayPvPc?list.averageDayPvPc:0)
          $('#risolyd').html(list.averageDayPvMobile?list.averageDayPvMobile:0)
        } else {
          $('#zhou').html("<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
          $('#zhoupc').html("<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
          $('#zhouyd').html("<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
          $('#risol').html("<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
          $('#risolpc').html("<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
          $('#risolyd').html("<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
        }
      })
    }
    // if (ontwo == 0) {
    //   $.post('/home/keywords/xgHotrecord', {keyword: kw}, function(res) {
    //     console.log(res);
    //     var list2 = res.hotList2
    //     // 关键词查询相关信息
    //     $('#hotlist2').css('display','none');
    //     var str = '';
    //     if (list2.length != 0) {
    //       for (var i in list2) {
    //         str += '<tr style="text-align: center;font-size: 14px;line-height: 45px;border:1px solid #f6f6fc">'
    //         str += '<td><a href="/keyresult?keyword='+list2[i]['keyword']+'" style="color:#666;" class="ahref">'+list2[i]['keyword']+'</a></td>'
    //         str += '<td>'+list2[i]['averagePv']+'</td>'
    //         str += '<td>'+list2[i]['averagePvPc']+'</td>'
    //         str += '<td>'+list2[i]['averagePvMobile']+'</td>'
    //         str += '<td>'+list2[i]['averageDayPv']+'</td>'
    //         str += '<td>'+list2[i]['averageDayPvPc']+'</td>'
    //         str += '<td>'+list2[i]['averageDayPvMobile']+'</td>'
    //         str += '</tr>'
    //       }
    //       $(".ft").css("position","static");
    //       $(".content").append(str);
    //     } else {
    //       $(".record").css('display','block');
    //       $('.record').text('暂无搜索到 "'+kw+'" 的相关数据,请稍后重试!')
    //     }
    //   })
    // }
    $('.hover-button').hover(function(){
      $(this).find('div').show()
    }, function(){
      $(this).find('div').hide()
    })
});