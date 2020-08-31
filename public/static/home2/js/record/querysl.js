   $(function(){
    var includ_plsubmit = parseInt($('.alldata').attr('data-plsubmit'));
    var engine = 0
    var engine_name = ''
    var platform_name = ''
    var userid = getUserinfo()[0];
    var userlevel = getUserinfo()[1];
    $('.engine').click(function(){
      $('.engine').removeClass('cur_engine')
      $(this).addClass('cur_engine')
      engine = $(this).attr('engine')
      if (engine == 1) {
        engine_name = "百度"
      } else if (engine == 2) {
        engine_name = "好搜"
      } else if (engine == 3) {
        engine_name = "搜狗"
      }
    })
    var platform = $('.alldata').attr('data-plat');
    if (platform == 1) {
      platform_name = 'PC端'
    } else if(platform == 2) {
      platform_name = '移动端'
    }
    trhoverstyle(4);
    function getinclude(sign, count){
      setTimeout(function(){
        $.post('/getinclude', {sign:sign}, function(data){
            for (var i=0;i<data.inc.length;i++) {
              var inc = eval('(' + data.inc[i] + ')')
              var url = ''
              if (engine == 1) {
                url = "http://www.baidu.com/s?wd=site%3A"+inc['url']
              } else if (engine == 2) {
                url = "https://www.so.com/s?q=site%3A"+inc['url']
              } else if (engine == 3) {
                url = "http://www.sogou.com/web?query=site%3A"+inc['url']
              }
              var include = "<a href='"+url+"' target='_black' style='color: #3385ff'>" + String(inc['include']).replace(',' ,'').replace(',' ,'').replace(',' ,'') + "</a>"
              $('td[data-url="'+inc['url']+'"]').html(include)
            }
            var newcount = count - data.inc.length
            if (newcount > 0) {
              getinclude(sign, newcount)
            }
            $('#daochu').css('display', 'block')
            $('.hide').show()
        })
      }, 1500)
    }

    $(".keytj").click(function(){
      $.post("home/recordquery/toplimit", {type:'includ_querynum'}, function(res){
        if(res.code == 0){
            $.sendError(res.msg,1000);
            return false;
        }else if (res.code == 2){
          selectError('今日收录查询次数已达上限，是否升级会员组获取更多次数');
        } else {
          var ydkeywords = $.trim($("#ydkeywords").val());
          if (!ydkeywords) {
            $.sendError('请输入网站地址', 1000);
            return false;
          }
          $.post('/urlplc', {target:ydkeywords}, function(data) {
            if (data.code == 1) {
              $('#ydkeywords').val(data.arr)
              ydkeywords = data.arr

              var arrkw1 = ydkeywords.split(/[\n,]/g)
              var arrkw = []
              $.each(arrkw1, function(i,v){
                if ($.trim(v)) {
                  arrkw.push(v)
                }
              })
              for (var i = 0; i<arrkw.length; i++) {
                if(arrkw[i].indexOf(" ")!=-1){
                  $.sendError('网站'+arrkw[i]+'不能包含空格', 1000);
                  return false;
                }
                if (!checkUrl(arrkw[i])) {
                  if (!checkIp(arrkw[i])) {
                    $.sendError('网站'+arrkw[i]+'格式错误', 1000);
                    return false;
                  }
                }
                arrkw[i] = arrkw[i].replace('http://', '')
                arrkw[i] = arrkw[i].replace('https://', '')
              }
              if (arrkw.length == 0) {
                $.sendError('请输入网站地址', 1000);
                return false;
              }
              if (arrkw.length>includ_plsubmit) {
                $.sendError('一次最多可提交'+includ_plsubmit+'条', 1000);
                return false;
              }
              if (!engine) {
                $.sendWarning('请选择搜索引擎', 1000);
                return false;
              }

              layui.use('layer', function(){
                var layer = layui.layer;
                $('.js').css('display','none');
                $.post("/putinclude", {urls:arrkw,engine:engine,platform:1}, function(data){
                  if (data.code == 1) {
                    $('.js').css('display', 'none')
                    $('.sousuo').css('display', 'block')
                    var str = ''
                    $.each(data.urls, function(i,v){
                      str += "<tr style='text-align: center;font-size: 14px;line-height: 40px;border-top:1px solid #f6f6fc'>"
                      str += "<td>"+(~~i+1)+"</td>"
                      if (v.jj == 1) {
                        str += "<td><img src='/static/home2/images/rz1.png' style='height:16px' title='已认证'>"+v.url+"</td>"
                      } else {
                        str += "<td><img src='/static/home2/images/rz0.png' style='height:16px' title='未认证'>"+v.url+"</td>"
                      }
                      if (v.days) {
                        str += "<td data="+v.days+">"+v.days+"天</td>"
                      } else {
                        str += "<td data="+v.days+" style='color:#504cc1;font-weight:bold;'>未知</td>"
                      }
                      
                      str += "<td>"+engine_name+"</td>"
                      str += "<td>"+platform_name+"</td>"
                      str += "<td data-url='"+v.url+"'><img src='/static/home2/images/loading.gif' style='width:15px;cursor:pointer;line-height:39px;'></td>"
                      str += "</tr>"
                    })
                    $('#content').empty().append(str)
                    getinclude(data.sign, data.urls.length)
                  } else {
                    $.sendError(data.msg, 1000);
                    return false;
                  }
                }, 'json');
              }); 
            } else {
              $('#ydkeywords').val(' ')
              $.sendError('查询内容不包含网址', 1000);
              return false;
            }
          })
        }
      }, 'json') 
    });
    

    function fuzhi(){
      $('#fuzhi').select()
      document.execCommand('copy')
    }

    // 点击导出数据
    $('#daochu').click(function(){
      if (!userid) {
        $.sendWarning('请登录后操作', 1000)
      } else {
        fuzhi()
        var shuju = []
        $('#content tr').each(function(i, v){    
          shuju.push({
            '序号':$(v).find('td:eq(0)').text(),
            '网站地址':$(v).find('td:eq(1)').text(),
            '建站时间':$(v).find('td:eq(2)').text(),
            '搜索引擎':$(v).find('td:eq(3)').text(),
            '终端':$(v).find('td:eq(4)').text(),
            '收录':$(v).find('td:eq(5)').text()
          })
        })
        if (userlevel == '1') {
          $.sendConfirm({
            hideHeader: true,
            withCenter: true,
            msg: '该操作需要1积分，你确定要执行该操作吗?',
            button: {
              confirm: '确认',
              cancel: '取消'
            },
            onConfirm: function() {
              $.post('/getPoint', {}, function(point){
                if(point<1) {
                  $.sendConfirm({
                    hideHeader: true,
                    withCenter: true,
                    msg: '积分不足，可邀请更多好友注册，获得VIP会员权限，是否复制邀请？',
                    button: {
                      confirm: '确认',
                      cancel: '取消'
                    },
                    onConfirm: function() {
                      $.sendSuccess('已复制到剪切板', 1000)
                    },
                    onCancel: function() {
                        return false;
                    },
                    onClose: function() {
                        return false;
                    }
                  }); 
                  return false
                } else {
                  $('#daochuForm input[name=point]').val('1')
                  $('#daochuForm input[name=shuju]').val(JSON.stringify(shuju))
                  $('#daochuForm').submit()
                  // location.href = "/export2excel?shuju="+JSON.stringify(shuju)+"&title=网站收录查询结果&point=1"
                }
              }, 'json')
            },
            onCancel: function() {
                return false;
            },
            onClose: function() {
                return false;
            }
          }); 
        } else {
          $.post('/exporttype', {exporttype:'includ_exportnum'}, function (data) {
              if (data.code == 1) {
                $('#daochuForm input[name=point]').val('0')
                $('#daochuForm input[name=shuju]').val(JSON.stringify(shuju))
                $('#daochuForm').submit()
                // location.href = "/export2excel?shuju="+JSON.stringify(shuju)+"&title=网站收录查询结果&point=0"
              } else {
                $.sendConfirm({
                  hideHeader: true,
                  withCenter: true,
                  msg: '今日收录导出次数已达上限，继续操作需要1积分，确定要导出吗?',
                  button: {
                    confirm: '确认',
                    cancel: '取消'
                  },
                  onConfirm: function() {
                    $.post('/getPoint', {}, function(point){
                      if(point<1) {
                        $.sendConfirm({
                          hideHeader: true,
                          withCenter: true,
                          msg: '积分不足，可邀请更多好友注册，获得VIP会员权限，是否复制邀请？',
                          button: {
                            confirm: '确认',
                            cancel: '取消'
                          },
                          onConfirm: function() {
                            $.sendSuccess('已复制到剪切板', 1000)
                          },
                          onCancel: function() {
                              return false;
                          },
                          onClose: function() {
                              return false;
                          }
                        }); 
                        return false
                      } else {
                        $('#daochuForm input[name=point]').val('1')
                        $('#daochuForm input[name=shuju]').val(JSON.stringify(shuju))
                        $('#daochuForm').submit()
                        // location.href = "/export2excel?shuju="+JSON.stringify(shuju)+"&title=网站收录查询结果&point=1"
                      }
                    }, 'json')
                  },
                  onCancel: function() {
                      return false;
                  },
                  onClose: function() {
                      return false;
                  }
                }); 
              }
          }, 'json')  
        }
      }
    })
    var slorder = $('.alldata').attr('data-slorder');
    $('.rank').click(function(){
      if (!userid) {
        $.sendWarning('请登录后操作', 1000);
        return false;
      } else {
        if (slorder == 0) {
            selectError('普通用户暂不支持排序功能,确定要升级会员?');
        } else {
          var arr = []
          var data = []
          $('#content tr').each(function(i,v) {
            var td = $(v).find('td:eq(5)')
            arr.push({
              td1: $(v).find('td:eq(1)').prop('outerHTML'),
              td2: $(v).find('td:eq(2)').prop('outerHTML'),
              td5: $(v).find('td:eq(5)').prop('outerHTML'),
              sl: parseInt(td.text()),
            })
          })
          if ($(this).attr('src') == "/static/home2/images/asc.png") {
            $(this).attr('src' , '/static/home2/images/desc.png')
            data = arr.sort(compare1('sl'))
          } else {
            $(this).attr('src' , '/static/home2/images/asc.png')
            data = arr.sort(compare2('sl'))
          }
          $('#content tr').each(function(i,v){
            $(v).find('td:eq(1)').prop('outerHTML', data[i]['td1'])
            $(v).find('td:eq(2)').prop('outerHTML', data[i]['td2'])
            $(v).find('td:eq(5)').prop('outerHTML', data[i]['td5'])
          })
        }
      }
    })

    $('.days').click(function(){
      if (!userid) {
        $.sendWarning('请登录后操作', 1000);
        return false;
      } else {
        if (slorder == 0) {
            selectError('普通用户暂不支持排序功能,确定要升级会员?');
        } else {
          var arr = []
          var data = []
          $('#content tr').each(function(i,v) {
            var td = $(v).find('td:eq(2)')
            arr.push({
              td1:$(v).find('td:eq(1)').prop('outerHTML'),
              td2:$(v).find('td:eq(2)').prop('outerHTML'),
              td5:$(v).find('td:eq(5)').prop('outerHTML'),
              days:parseInt(td.attr('data'))
            })
          })
          if ($(this).attr('src') == "/static/home2/images/asc.png") {
            $(this).attr('src' , '/static/home2/images/desc.png')
            data = arr.sort(compare1('days'))
          } else {
            $(this).attr('src' , '/static/home2/images/asc.png')
            data = arr.sort(compare2('days'))
          }
          $('#content tr').each(function(i,v){
            $(v).find('td:eq(1)').prop('outerHTML', data[i]['td1'])
            $(v).find('td:eq(2)').prop('outerHTML', data[i]['td2'])
            $(v).find('td:eq(5)').prop('outerHTML', data[i]['td5'])
          })
        }
      }
    })
  });