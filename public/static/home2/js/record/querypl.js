 $(function(){
    var sessionid = $('.querypl').attr('data-id');//用户id
    var userlevel = $('.querypl').attr('data-level');
    var beian_plsubmit = parseInt($('.querypl').attr('data-plsubmit'));
    var shuju = [];
    // 清空
    $('.beians-empty').click(function(){
      $('.keysearch').val('')
    })
    // 网址提取
    $('.beians-extract').click(function(){
      var urlpl = $.trim($(".keysearch").val());
      $.post('/urlplc', {target:urlpl}, function(data) {
        if (data.code == 1) {
          $('.keysearch').val(data.arr)
        } else {
          $('.keysearch').val('')
        }
      })
    })
    // 提交
    layui.use(['layer'], function(){
      var layer = layui.layer
      $("#subplurl").click(function(){
        var load = layer.load(1)
        $.post("home/recordquery/toplimit", {type:'beian_plquerynum'}, function(res){
          if(res.code == 0){
            layer.close(load)
            $.sendError(res.msg,1000);
            return false;
          }else if (res.code == 2){
            layer.close(load)
            selectError('今日备案批量查询次数已达上限，是否升级会员组获取更多次数');
          } else {
            var urlpl = $.trim($(".keysearch").val());
            if(urlpl == ''){
              layer.close(load)
              $.sendWarning('请输入查询内容', 1000);
              return false;
            }
            $.post("/urlpla", {target:urlpl,beian_plnum:beian_plsubmit}, function(data){ 
              var mcd = data.mcd
              if (data.code == 0) {
                layer.close(load)
                $.sendWarning(data.msg, 1000);
                $('.js').css('display','block');
                return false;
              } else if(data.code == 3) {
                layer.close(load)
                $('.js').css('display','block');
                selectError(data.msg);
                return false;
              } else{
                $('.keysearch').val(data.arr)
                $('.js').css('display','none');
                $('#content').empty();
                $('.sousuo').css('display','none');
                $('#result').css('display','none');
                if (data.list) {
                  var str = '';
                  var info = data.list;
                  shuju = []
                  var ds = 0;
                  for (var i in info) {
                      $('.sousuo').css('display','block');
                      $('#result').css('display','none');
                      var xuhao = 1+~~i
                      if (!info[i]['no']) {
                        str += '<tr style="text-align: center;font-size: 14px;line-height: 40px;border-top:1px solid #f6f6fc" topurl='+info[i]['topurl']+' onearr='+info[i]['onearr']+'>'
                        str += '<td></td>'
                        str += '<td>'+xuhao+'</td>'
                        if (info[i]['onearr'].length>25) {
                          str += '<td title="'+info[i]['onearr']+'">'+info[i]['onearr'].substr(0,25)+'...</td>'
                        } else {
                          str += '<td>'+info[i]['onearr']+'</td>'
                        }
                        str += info[i]['name']?'<td>'+info[i]['name']+'</td>':"<td style='color:#423da1'>未备案</td>"
                        str += info[i]['name']?'<td>'+info[i]['nature']+'</td>':"<td style='color:#423da1'>未备案</td>"
                        str += info[i]['name']?'<td>'+info[i]['bah']+'</td>':"<td style='color:#423da1'>未备案</td>"
                        if (info[i]['name']) {
                          str += info[i]['time']?'<td>'+info[i]['time'].substring(0,10)+'</td>':"<td style='color:#423da1'><img src='/static/home2/images/wh2.png'></td>"
                        } else {
                          str += "<td style='color:#423da1'>未备案</td>"
                        }
                        str += info[i]['name']?'<td>'+info[i]['create_time'].substring(0,10)+'&nbsp;<a href="javascript:;" style="color:#0d8c21">[更新]</a></td>':"<td><span style='color:#423da1'>未备案</span>&nbsp;<a href='javascript:;' style='color:#0d8c21'>[更新]</a></td>"
                      } else {
                        ds = 1;
                        str += '<tr style="text-align: center;font-size: 14px;line-height: 40px;border-top:1px solid #f6f6fc" topurl='+info[i]['topurl']+' onearr='+info[i]['onearr']+'>'
                        str += '<td></td>'
                        str += '<td>'+xuhao+'</td>'
                        if (info[i]['onearr'].length>25) {
                          str += '<td title="'+info[i]['onearr']+'">'+info[i]['onearr'].substr(0,25)+'...</td>'
                        } else {
                          str += '<td>'+info[i]['onearr']+'</td>'
                        }
                        str += '<td><img src="/static/home2/images/loading.gif" alt="" style="width:15px;line-height: 39px;"></td>'
                        str += '<td><img src="/static/home2/images/loading.gif" alt="" style="width:15px;line-height: 39px;"></td>'
                        str += '<td><img src="/static/home2/images/loading.gif" alt="" style="width:15px;line-height: 39px;"></td>'
                        str += '<td><img src="/static/home2/images/loading.gif" alt="" style="width:15px;line-height: 39px;"></td>'
                        str += '<td><img src="/static/home2/images/loading.gif" alt="" style="width:15px;line-height: 39px;"></td>'
                      } 
                      str += '</tr>'
                  }
                  $('#content').append(str);
                  layer.close(load)
                  $("#content tr").css("cursor","pointer");
                  $('.zhousl').append('<img src="/static/home2/images/desc.png" id="order">')
                  // 鼠标悬浮表格样式
                  trhoverstyle(4);
                  if (ds == 1) {
                    $('#daochu').css('display','none')
                    $('.order').hide()
                    getInfo(mcd);
                  }
                }
              }
            }, 'json');
            $('#daochu').css('display','block')
            $('.order').show()
          }
        }, 'json') 
      });
    })

    function getInfo(mcd){
      $.post('/urlplb', {mcd:mcd}, function(res){
        if (res.code == 0) {
          $('#daochu').css('display','block')
          $('.order').show()
          $('img[src="/static/home2/images/loading.gif"]').parents('tr').each(function(i,v){
            $(v).find('td:gt(2)').html("<span style='color:#423da1'>未备案</span>")
            $(v).find('td:last()').html("<span style='color:#423da1'>未备案</span>&nbsp;<a href='javascript:;' style='color:#0d8c21'>[更新]</a>")
          })
        }else{
          $.each(res.info, function(i, v){
            $('tr[onearr="'+v['weburl']+'"] td:eq(3)').html(v['name']?v['name']:"<span style='color:#423da1'>未备案</span>")
            $('tr[onearr="'+v['weburl']+'"] td:eq(4)').html(v['name']?v['nature']:"<span style='color:#423da1'>未备案</span>")
            $('tr[onearr="'+v['weburl']+'"] td:eq(5)').html(v['name']?v['bah']:"<span style='color:#423da1'>未备案</span>")
            $('tr[onearr="'+v['weburl']+'"] td:eq(6)').html(v['name']?v['time'].substring(0,10):"<span style='color:#423da1'>未备案</span>")
            $('tr[onearr="'+v['weburl']+'"] td:eq(7)').html(v['name']?v['create_time'].substring(0,10)+'&nbsp;<a href="javascript:;" style="color:#0d8c21">[更新]</a></td>':"<span style='color:#423da1'>未备案</span>&nbsp;<a href='javascript:;' style='color:#0d8c21'>[更新]</a>")
          })
          mcd = res.mcd
          if (mcd.length == 0) {
            $('#daochu').css('display','block');
            $('.order').show()
          } else {
            getInfo(mcd)
          }
        }
      })
    }
    function fuzhi(){
      $('#fuzhi').select()
      document.execCommand('copy')
    }
    // 点击导出数据
    $('#daochu').click(function(){
      if (!sessionid) {
        $.sendWarning('请登录后操作', 1000);
        return false;
      } else {
        var shuju = []
        $('#content tr').each(function(i,v){
          shuju.push({
            '序号':$(v).find('td:eq(1)').text(),
            '网站域名':$(v).find('td:eq(2)').text(),
            '备案名称':$(v).find('td:eq(3)').text(),
            '备案性质':$(v).find('td:eq(4)').text(),
            '备案号':$(v).find('td:eq(5)').text(),
            '备案审核时间':$(v).find('td:eq(6)').text(),
            '缓存时间':$(v).find('td:eq(7)').text().replace('[更新]','')
          })
        })
        fuzhi()
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
                    // location.href = "/export2excel?shuju="+JSON.stringify(shuju)+"&title=备案批量导出&point=1"
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
          $.post('/exporttype', {exporttype:'beian_exportnum'}, function (data) {
              if (data.code == 1) {
                $('#daochuForm input[name=point]').val('0')
                $('#daochuForm input[name=shuju]').val(JSON.stringify(shuju))
                $('#daochuForm').submit()
                // location.href = "/export2excel?shuju="+JSON.stringify(shuju)+"&title=备案批量导出&point=0"
              } else {
                $.sendConfirm({
                  hideHeader: true,
                  withCenter: true,
                  msg: '今日备案批量导出次数已达上限，继续操作需要1积分，确定要导出吗?',
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
                        // location.href = "/export2excel?shuju="+JSON.stringify(shuju)+"&title=备案批量导出&point=1"
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

    $(document).on('click', 'a:contains(更新):not(.allupdate)', function(e){
      $.post("home/recordquery/toplimit", {type:'beian_querynum'}, function(res){
          if(res.code == 0){
            $.sendError(res.msg, 1000, function(){
              location.reload();
            }); 
            return false;
          } else if (res.code == 2){
              selectError('今日备案查询次数已达上限，是否升级会员组获取更多次数');
          } else{
            $('#daochu').css('display','none')
            $('.order').hide()
            var bainfo = $(e.target).parents('tr')
            var bahtml = bainfo.html()
            var onearr = bainfo.attr('onearr')
            var topurl = bainfo.attr('topurl')
            bainfo.find('td:gt(2)').html('<img src="/static/home2/images/loading.gif" alt="" style="width:15px;cursor: pointer;line-height: 39px;">')
            $.post('/beian/'+onearr+'/1', {onearr: onearr,topurl:topurl, update:1}, function(res){
              $('#daochu').css('display','block')
              $('.order').show()
              var redlist = res.redlist;
              var str = '<td></td><td>'+bainfo.find('td:eq(1)').html()+'</td>'
              str += "<td>"+onearr+"</td>"
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
                str += redlist.name?redlist.create_time.substring(0,10)+'&nbsp;<a href="javascript:;" style="color:#0d8c21">[更新]</a>':"<span style='color:#423da1'>未备案</span>&nbsp;<a href='javascript:;' style='color:#0d8c21'>[更新]</a>"
                str += "</td>"
                bainfo.html(str);
              } else {
                bainfo.html(bahtml);
              }
            })  
          }
      }, 'json')  
    })

    // 备案性质排序
    var or = '企业'
    $('.nature_order').click(function(){
      if ($('.querypl').attr('order_beian') == 0) {
        selectError('您暂无备案排序权限，是否升级会员组获取更多权限');
        return false
      }
      if (or == '企业') {
        or = '个人'
      } else {
        or = '企业'
      }
      var str1 = ''
      var str2 = ''
      $('#content').find('tr').each(function(i,v) {
        var na = $(v).find('td:eq(4)').text()
        if (na == or) {
          str1 += $(v).prop("outerHTML")
        } else {
          str2 += $(v).prop("outerHTML")
        }
      })
      var str = str1+str2
      $('#content').html(str)
    })

    // 缓存时间排序
    $('.time_order').click(function(){
      if ($('.querypl').attr('order_beian') == 0) {
        selectError('您暂无备案排序权限，是否升级会员组获取更多权限');
        return false
      }

      var order_arr = []
      var arr = $('#content').find('tr').map(function(i,v) {
        var t = Date.parse($(v).find('td:eq(7)').text().substring(0,10))
        var h = $(v).prop("outerHTML")
        return {t:t, h:h}
      }).get()
      arr.sort(function(a,b) {
        return b['t']-a['t']
      })
      var str = ''
      $.each(arr, function(i,v) {
        str += v.h
      })
      $('#content').html(str)
    })

    // 批量更新
    $('.allupdate').click(function(){
      $.post("home/recordquery/toplimit", {type:'beian_querynum'}, function(res){
        if(res.code == 0){
          $.sendError(res.msg, 1000, function(){
            location.reload();
          }); 
          return false;
        } else if (res.code == 2){
            selectError('今日备案查询次数已达上限，是否升级会员组获取更多次数');
        } else{
          $('#daochu').css('display','none')
          $('.order').hide()
          $('#content').find('tr').each(function(i,v){
            var bahtml = $(v).html()
            var onearr = $(v).attr('onearr')
            var topurl = $(v).attr('topurl')
            $(v).find('td:gt(2)').html('<img src="/static/home2/images/loading.gif" alt="" style="width:15px;cursor: pointer;line-height: 39px;">')
                $.post('/beian/'+onearr+'/1', {onearr: onearr,topurl:topurl, update:1}, function(res){
                $('#daochu').css('display','block')
                $('.order').show()
                var redlist = res.redlist;
                var str = '<td></td><td>'+$(v).find('td:eq(1)').html()+'</td>'
                str += "<td>"+onearr+"</td>"
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
                  str += redlist.name?redlist.create_time.substring(0,10)+'&nbsp;<a href="javascript:;" style="color:#0d8c21">[更新]</a>':"<span style='color:#423da1'>未备案</span>&nbsp;<a href='javascript:;' style='color:#0d8c21'>[更新]</a>"
                  str += "</td>"
                  $(v).html(str);
                } else {
                  $(v).html(bahtml);
                }
            })  
          })
        }
      }, 'json')  
    })
  });