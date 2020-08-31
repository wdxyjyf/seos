 $(function(){
    //回车按键
    $(document).keydown(function(event){
    　　if(event.keyCode ==13){
    　　　　$("#selecthot").trigger("click");
            return false;
    　　}
    });

    var kw = $('.hotall').attr('data-kw');
    var on = $('.hotall').attr('data-on');
    var ontwo = $('.hotall').attr('data-ontwo');
    trhoverstyle(4);
    $("#selecthot").click(function(){
      $.post("/home/keywords/toplimit", {type:'keyword_querynum'}, function(res){
        if(res.code == 0){
          $.sendError(res.msg, 1000, function(){
            location.reload();
          })
          return false;
        }else if(res.code == 2) {
          selectError('今日关键词竞价查询次数已达上限，是否升级会员组获取更多次数');
        }else{
            var input = $(".jjcxkeywords").val().trim().replace(/\s/g,"");
            if(input == ''){
              $.sendWarning('请输入查询的关键词', 1000);
              return false;
            }
            if (!checkKeyword(input)) {
              $.sendError('格式错误,请输入中文,字母或数字', 1000);
              return false;
            }
            if(getByteLen(input)>30){
                $.sendError("关键词长度最大15个字符", 1000);
                return false;
            }
            location.href='/compete/'+BASE64.urlsafe_encode(input);
            // $('.search form').submit();
        }
      }, 'json')   
    });
    if (on == 0 || ontwo == 0) {
      $.post('/home/keywords/bidPrice', {keywords: kw}, function(res) {
        var list = res.priceList
        var list2 = res.priceList2
   
        if (list) {
          $('#pcjg').html(list.recommendPricePc?list.recommendPricePc:"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
          $('#xgnum').html(list.xgnum?list.xgnum:'0')
          $('#ydjg').html(list.recommendPriceMobile?list.recommendPriceMobile:"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
          $('#jgsl').html(list.competition?list.competition:"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")

          if (list.competition) {
            if (list.competition >=0 && list.competition <=5) {
              $('#jgnd').html('<button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-normal simple">简单</button>');
            } else if(list.competition >=6 && list.competition <=10){
              $('#jgnd').html('<button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-warm same">一般</button>');
            } else {
              $('#jgnd').html('<button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-danger diffcult">困难</button>');
            }
          } else {
            $('#jgnd').html("<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
          }
          
          if (list.showReasons) {
            $('#iszl').html('<button class="layui-btn  layui-btn-sm istrue">是</button>');
          } else {
            $('#iszl').html('<button class="layui-btn  layui-btn-sm layui-btn-normal">否</button>')
          }
        } else {
          $('#pcjg').html("<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
          $('#ydjg').html("<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
          $('#jgsl').html("<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
          $('#jgnd').html("<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
          $('#iszl').html("<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
        }
        // 竞价信息
        $('#jglist2').css('display','none');
        var str = '';
        // 关键词排行
        if (list2.length != 0) {
          for (var i in list2) {
            str += '<tr class="reslist">'
            str += '<td>'+list2[i]['keyword']+'</td>'
            str += '<td>'+list2[i]['recommendPricePc']+'</td>'
            str += '<td>'+list2[i]['recommendPriceMobile']+'</td>'
            str += '<td>'+list2[i]['xgnum']+'</td>'
            str += '<td>'+list2[i]['competition']+'</td>'
            if (list2[i]['competition']) {
              if (list2[i]['competition'] >=0 && list2[i]['competition']<=5) {
                str += '<td><button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-normal simple">简单</button></td>'
              } else if(list2[i]['competition'] >=6 && list2[i]['competition']<=10){
                str += '<td><button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-warm same">一般</button></td>'
              } else {
                str += '<td><button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-danger diffcult">困难</button></td>'
              }
            } else {
              str += "<td><img src='/static/home2/images/wh2.png' title='暂无数据'></td>"
            }
            if (list2[i]['showReasons']) {
              str += '<td><button class="layui-btn  layui-btn-sm istrue">是</button></td>'
            } else {
              str += '<td><button class="layui-btn  layui-btn-sm layui-btn-normal">否</button></td>'
            }
            str += '</tr>'
          }
          $("#content").append(str);
        } else {
          $(".record").css('display','block');
          $('.record').text('暂无搜索到 "'+kw+'" 的相关竞价数据,请稍后重试!')
        }
      })
    }
  });