$(function(){
    var curDate = new Date();
    var onearr = $('#onearr').val();
    var urlarr = $('#urlarr').val();
    var keyword = $('.datejh').attr('data-key');
    var timestamp = curDate.getTime();//获取当前时间戳
    var preDate = $('.datejh').attr('date-one');//前一天时间戳
    var sevenDate = $('.datejh').attr('date-seven');//前七天时间戳
    var monthDate = $('.datejh').attr('date-month');//前30天时间戳
    var on = $('#urlarr').attr('data-son');//备案信息年龄是否存在
    var son3 = $('#urlarr').attr('data-onthrid');//标题关键字描述是否存在
    var slon = $('#urlarr').attr('data-slon');//判断收录反链是否存在
    var is_update = $('#is_update').val()
    var beian_ct = $('#beian_ct').val()
    var beianinfo_ct = $('#beianinfo_ct').val()
    //redis 收录反链
    if (slon == 0) {
      a(onearr, urlarr, Date.parse(new Date())/1000);
    }
    function a(onearr, urlarr, time){
      setTimeout(function(){
        $.post('/home/index/getInclude', {onearr: onearr, urlarr:urlarr}, function(res) {
          if (res.code == 0 && (Date.parse(new Date())/1000 - time)<7) {
            a(onearr, urlarr, time)
          } else {
            var redlist = res.redlist
            var str = "<td>"
            str += redlist&&redlist.baidu_index?redlist.baidu_index:'0'
            str += "</td><td><a href='http://www.baidu.com/s?wd="+onearr+"&rn=50' target='_blank' style='color: #0d8c21'>"
            str += redlist&&redlist.kuanzhao_time?redlist.kuanzhao_time:'0'
            str += "</a></td><td>"
            str += redlist&&redlist.index_num?redlist.index_num:'0'
            str += "</td><td><a href='http://www.baidu.com/s?wd=site"+encodeURIComponent(':'+onearr)+"&lm=1&gpc=stf="+preDate+","+timestamp+"|stftype=1' target='_blank' style='color: #0d8c21'>"
            str += redlist&&redlist.day_num?redlist.day_num:'0'
            str += "</a></td><td><a href='http://www.baidu.com/s?wd=site"+encodeURIComponent(':'+onearr)+"&lm=7&gpc=stf="+sevenDate+","+timestamp+"|stftype=1' target='_blank' style='color: #0d8c21'>"
            str += redlist&&redlist.week_num?redlist.week_num:'0'
            str += "</a></td><td><a href='http://www.baidu.com/s?wd=site"+encodeURIComponent(':'+onearr)+"&lm=30&gpc=stf="+monthDate+","+timestamp+"|stftype=1' target='_blank' style='color: #0d8c21'>"
            str += redlist&&redlist.mon_num?redlist.mon_num:'0'
            str += "</a></td><td><a href='http://www.baidu.com/s?wd=site"+encodeURIComponent(':'+onearr)+"' target='_blank' style='color: #0d8c21'>"
            str += redlist&&redlist.bd_index_num!=null?redlist.bd_index_num:'0'
            str += "</a></td>"
            $('#redlist1').html(str)

            str = "<td>收录</td><td><a href='http://www.baidu.com/s?wd=site"+encodeURIComponent(':'+onearr)+"' target='_blank' style='color: #0d8c21'>"
            str += redlist&&redlist.bd_index_num?redlist.bd_index_num:'0'
            str += "</a></td><td><a href='http://www.so.com/s?q=site"+encodeURIComponent(':'+onearr)+"' target='_blank' style='color: #0d8c21'>"
            str += redlist&&redlist.hao_index_num?redlist.hao_index_num:'0'
            str += "</a></td><td><a href='http://www.sogou.com/web?query=site"+encodeURIComponent(':'+onearr)+"' target='_blank' style='color: #0d8c21'>"
            str += redlist&&redlist.sougou_index_num?redlist.sougou_index_num:'0'
            str += "</a></td><td><img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'></td>"
            $('#shoulu').html(str)

            str = "<td>反链</td><td><a href='http://www.baidu.com/s?wd=domain"+encodeURIComponent(':'+onearr)+"' target='_blank' style='color: #0d8c21'>"
            str += redlist&&redlist.bd_fanlian?redlist.bd_fanlian:'0'
            str += "</a></td><td><a href='http://www.so.com/s?q="+encodeURIComponent('"'+onearr+'"')+"' target='_blank' style='color: #0d8c21'>"
            str += redlist&&redlist.hao_fanlian?redlist.hao_fanlian:'0'
            str += "</a></td><td><a href='http://www.sogou.com/web?query="+encodeURIComponent('"'+onearr+'"')+"' target='_blank' style='color: #0d8c21'>"
            str += redlist&&redlist.sougou_fanlian?redlist.sougou_fanlian:'0'
            str += "</a></td><td><img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'></td>"
            $('#fanlian').html(str)
          }
        })
      }, 500)
    } 
    //标题 关键字
    if (son3 == 0) {
      $.post('/home/index/datainfo', {onearr: onearr, urlarr:urlarr, is_update:is_update, beianinfo_ct:beianinfo_ct}, function(res) {
        var titkeyinfo = res.titkeyinfo
        var kwlist = res.kwlist
        // 网站标签信息
        $('#wt').html(titkeyinfo.title?titkeyinfo.title.slice(0,50)+"...":"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
        $('#wt').attr('title', titkeyinfo.title?titkeyinfo.title:'暂无数据')
        $('#wk').html(titkeyinfo.keyword?titkeyinfo.keyword.slice(0,80)+"...":"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
        $('#wk').attr('title', titkeyinfo.keyword?titkeyinfo.keyword:'暂无数据')
        $('#wd').html(titkeyinfo.description?titkeyinfo.description.slice(0,80)+"...":"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
        $('#wd').attr('title', titkeyinfo.description?titkeyinfo.description:'暂无数据')
        // 关键词排行
        if (kwlist.length != 0) {
          var kwliststr = '<tr style="font-size: 14px;line-height: 45px;background: #f6f6fc">'
          kwliststr += '<th width="17%">关键字</th>'
          kwliststr += '<th width="17%">搜索量</th>'
          kwliststr += '<th width="17%">相关词数量</th>'
          kwliststr += '<th width="17%">词频&nbsp;<span class="allssl" style="color: #423da1;font-size: 12px;font-weight: bold;cursor: pointer;">[一键查询]</span></th>'
          kwliststr += '<th width="17%">排名&nbsp;<span class="allcsl" style="color: #423da1;font-size: 12px;font-weight: bold;cursor: pointer;">[一键查询]</th>'
          kwliststr += '<th>预计流量&nbsp;<span style="color: #423da1;font-size: 12px;font-weight: bold;cursor: pointer;"></th>'
          kwliststr += '</tr>'
          for (var i in kwlist) {
            kwliststr += '<tr style="text-align: center;font-size: 14px;line-height: 38px;">'
            kwliststr += '<td><a class="kwa" href="keyword/'+kwlist[i]['kw']+'">'+kwlist[i]['kw']+'</a>'
            kwliststr += `</a><div class="hover-button">
                  搜
                <div>
                  <ul>
                    <li><a target="_blank" href="/dig/`+BASE64.urlsafe_encode(kwlist[i]['kw'])+`">相关词</a></li>
                    <li><a target="_blank" href="/related/`+BASE64.urlsafe_encode(kwlist[i]['kw'])+`">长尾词</a></li>`
            if (getStrLength(kwlist[i]['kw']) <= 10) {
              kwliststr += `<li><a target="_blank" href="/findsites/`+BASE64.urlsafe_encode(kwlist[i]['kw'])+`">相关网站</a></li>`
            }
            kwliststr += '</ul></div></div></td>'
            kwliststr += '<td style="cursor: pointer;">'+kwlist[i]['sousl']+'</td>'
            kwliststr += '<td style="cursor: pointer;"><a href="related/'+BASE64.urlsafe_encode(kwlist[i]['kw'])+'" title="点击查看更多相关数据" style="color: #423da1;font-weight: bold;">'+kwlist[i]['xgnum']+'</a></td>'
            kwliststr += '<td style="cursor: pointer;" class="ssl" data-info="'+kwlist[i]['kw']+'"> <a href="/rate/'+onearr+'/'+BASE64.urlsafe_encode(kwlist[i]['kw'])+'">查询</a></td>'
            kwliststr += '<td style="cursor: pointer;" class="xgcsl" data-info="'+kwlist[i]['kw']+'" data-num="'+kwlist[i]['sousl']+'">查询</td>'
            kwliststr += '<td ><img src="/static/home2/images/wh2.png" style="width:15px;cursor: pointer;line-height: 39px;" title="关键词预计流量"></td>'
            kwliststr += '</tr>'
          }
          $("#kwlist").html(kwliststr)
        }
      })
    }
    //备案信息
    if (on == 0) {
      $.post('/home/index/beianinfo', {onearr: onearr, urlarr:urlarr, is_update:is_update, beian_ct:beian_ct}, function(res) {
        var result = res.result
        $('#bah').html(result.record_num?'<a href="/beian/'+onearr+'" class="beianhao">'+result.record_num+'</a>':"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
        $('#baxz').html(result.nature?result.nature:"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
        $('#bamc').html(result.name?result.name:"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
      })
    }
    //备案年龄
    if (on == 0) {
      $.post('/home/index/beianage', {onearr: onearr, urlarr:urlarr, is_update:is_update, beian_ct:beian_ct}, function(res) {
        $("#ymd").html(res.ymd && res.age?res.age:'<img src="/static/home2/images/wh2.png" style="width:15px;cursor: pointer;line-height: 39px;" title="暂无数据">')
      })
    }
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
          if ($('.is-update').prop('checked')) {
            $.cookie('is_update', 1);
          }
          location.href = '/'+input;
        }
      }, 'json')    
    });
    // 点击单个词频查询
    // $(document).on('click', '.ssl', function(e){
    //   $(e.target).html("<img src='/static/home2/images/loading.gif' style='width:15px;cursor: pointer;line-height: 39px;'>");
    //   var url = keyword;
    //   var kw = $(e.target).attr('data-info');
    //   var th = $(e.target);
    //   $.post('/home/quanzhong/qzrecord2', {qzkey:kw,qzurl:url}, function(data) {
    //     if (data.code == 1) {
    //       var cipin = data.cipin;
    //       onecipin(cipin,Date.parse(new Date())/1000);
    //       function onecipin(cipin, time){
    //           setTimeout(function(){
    //             $.post('/home/quanzhong/getOnePinl', {cipin:cipin}, function(res) {
    //               if (res.code == 0 && (Date.parse(new Date())/1000 - time)<15) {
    //                 onecipin(cipin,time)
    //               } else {
    //                 if(res.list.length == 0){
    //                   th.html("重试");
    //                   th.css({"color":"#0d8c21"});
    //                 }else{
    //                   if(res.list.frequency == 0){
    //                     th.html('0');
    //                     th.css({"color":"#666"});
    //                     th.removeClass('ssl');
    //                   }else{
    //                     th.removeClass('ssl');
    //                     th.css({"color":"#666"});
    //                     th.html(res.list.frequency+'%');
    //                   }
    //                 }
    //               }
    //             })
    //           }, 2000)
    //       }
    //     } 
    //   }, 'json')
    // })
    // 点击单个排名查询
    $(document).on('click', '.xgcsl', function(e){
      $(e.target).html("<img src='/static/home2/images/loading.gif' style='width:15px;cursor: pointer;line-height: 39px;'>");
      var url = keyword;
      var kw = $(e.target).attr('data-info');
      var pmth = $(e.target);
      $.post('/rankpc', {keyword:kw,target:url,engine_type:0}, function(data){
        if(data.code ==1){
          var pcsign = data.sign;
          onepc(pcsign,Date.parse(new Date())/1000);
          function onepc(pcsign, time){
              setTimeout(function(){
                $.post('/home/pckeywords/getOneRank', {sign:pcsign}, function(res) {
                  if (res.code == 0 && (Date.parse(new Date())/1000 - time)<15) {
                    onepc(pcsign,time)
                  } else {
                    pmth.css({"color":"#666"});
                    pmth.removeClass('xgcsl');
                    if(res.paiming == 1){
                      var liul = Math.round(pmth.attr('data-num') * 0.9);
                    }else if(res.paiming == 2){
                      var liul = Math.round(pmth.attr('data-num') * 0.8);
                    }else if(res.paiming == 3){
                      var liul = Math.round(pmth.attr('data-num') * 0.7);
                    }else if(res.paiming == 4){
                      var liul = Math.round(pmth.attr('data-num') * 0.6);
                    }else if(res.paiming == 5){
                      var liul = Math.round(pmth.attr('data-num') * 0.5);
                    }else if(res.paiming == 6){
                      var liul = Math.round(pmth.attr('data-num') * 0.4);
                    }else if(res.paiming == 7){
                      var liul = Math.round(pmth.attr('data-num') * 0.3);
                    }else if(res.paiming == 8){
                      var liul = Math.round(pmth.attr('data-num') * 0.2);
                    }else if(res.paiming == 9){
                      var liul = Math.round(pmth.attr('data-num') * 0.1);
                    }else {
                      var liul = Math.round(pmth.attr('data-num') * 0);
                    }
                    pmth.next().html(liul);
                    if (res.paiming) {
                      pmth.html('<a href="http://www.baidu.com/s?wd='+kw+'&rn=50" target="_blank" style="color:#423da1;font-weight: bold;">'+res.paiming+'</a>');
                    } else {
                      pmth.html('<a href="http://www.baidu.com/s?wd='+kw+'&rn=50" target="_blank" style="color:#423da1;font-weight: bold;">100+</a>');
                    }
                    
                  }
                })
              }, 2000)
          }
        } else {
          pmth.html("重试");
          pmth.css({"color":"#0d8c21"});
        }
      }, 'json')
    })
    //一键查询词频方法
    function getallssl(allssl, url) {
      setTimeout(function(){
        $.post('/home/quanzhong/getqzrecord', {qzkeys:allssl, qzurl:url}, function(data){
          var info = data.info
          if (info) {
            $.each(info, function(i,v){
              $('.ssl[data-info="'+v.keyword+'"]').text(v.frequency+'%')
              $('.ssl[data-info="'+v.keyword+'"]').removeClass('ssl')
              allssl.splice($.inArray(v.keyword,allssl),1);
            })
          }
          if (allssl.length>0) {
            getallssl(allssl, url)
          }
        })
      },3000)
    }
    // 一键查询词频点击
    $(document).on('click', '.allssl', function(e){
      $("#kwlist tr").each(function() {
        $(this).find("td").eq(3).addClass('ssl');
      })
      $('.ssl').html("<img src='/static/home2/images/loading.gif' style='width:15px;cursor: pointer;line-height: 39px;'>");
      var url = keyword
      var allssl = $('.ssl').map(function(i,v){
        return $(this).attr('data-info')
      }).get()

      $.post('home/quanzhong/qzrecord4', {qzkeys:allssl, qzurl:url}, function(data){
        var info = data.info
        if (info) {
          $.each(info, function(i,v){
            $('.ssl[data-info="'+v.keyword+'"]').text(v.frequency+'%')
            $('.ssl[data-info="'+v.keyword+'"]').removeClass('ssl')
            allssl.splice($.inArray(v.keyword,allssl),1);
          })
        }
        if (allssl.length>0) {
          getallssl(allssl, url)
        }
      })
    })
     //一键查询排名方法
    function getallcsl(allcsl,sign,times) {
      setTimeout(function(){
        $.post('/home/pckeywords/getrankpc', {sign:sign}, function(data){
          var info = data.info
          if (info.length > 0) {
            $.each(info, function(i,v){
              $('.xgcsl[data-info="'+v.keyword+'"]').html('<a href="http://www.baidu.com/s?wd='+v.keyword+'&rn=50" target="_blank" style="color:#423da1;font-weight: bold;">'+v.rank+'</a>');
              var liul = 0;
              if (v.rank<10) {
                liul = Math.round($('.xgcsl[data-info="'+v.keyword+'"]').attr('data-num') * (1-(v.rank/10)))
              }
              $('.xgcsl[data-info="'+v.keyword+'"]').next().html(liul)
              $('.xgcsl[data-info="'+v.keyword+'"]').removeClass('xgcsl')
              allcsl.splice($.inArray(v.keyword,allcsl),1);
            })
          }
          if (allcsl.length>0) {
            if (times >= 8) {
              for (var i in allcsl) {
                $('.xgcsl[data-info='+allcsl[i]+']').html('<a href="http://www.baidu.com/s?wd='+allcsl[i]+'&rn=50" target="_blank" style="color:#423da1;font-weight: bold;">0</a>')
                $('.xgcsl[data-info='+allcsl[i]+']').next().html(0)
                $('.xgcsl[data-info='+allcsl[i]+']').removeClass('xgcsl')
              }
            } else {
              times++
              getallcsl(allcsl, sign, times)
            }
          }
        })
      },3000)
    }
     // 一键查询排名点击
    $(document).on('click', '.allcsl', function(e){
      $("#kwlist tr").each(function(){
        $(this).find("td").eq(4).addClass('xgcsl');
      })
      $('.xgcsl').html("<img src='/static/home2/images/loading.gif' style='width:15px;cursor: pointer;line-height: 39px;'>");
      var url = keyword
      var allcsl = $('.xgcsl').map(function(i,v){
        return $(this).attr('data-info')
      }).get()

      $.post('/home/pckeywords/putrankpc', {keyword:allcsl ,target:url ,engine_type:0}, function(data){
        if (data.code == 1) {
          getallcsl(allcsl, data.sign, 1)
        }
      })
    })

    $('.hover-button').hover(function(){
      $(this).find('div').show()
    }, function(){
      $(this).find('div').hide()
    })


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

function getStrLength(str) {
  var cArr = str.match(/[^\x00-\xff]/ig);
  return str.length + (cArr == null ? 0 : cArr.length);
}

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