$(function(){
    var userid = getUserinfo()[0];
    var userlevel = getUserinfo()[1];
    var rank_plsubmit = parseInt($('.pldata').attr('data-btn'));
    function fuzhi(){
      $('#fuzhi').select()
      document.execCommand('copy')
    }
    select_change('#ydtype');
    var shuju = []
    trhoverstyle(4);
    $(".keytj").click(function(){
      $.post("/home/mobilekeywords/toplimit", {type:'rank_plquerynum'}, function(res){
        if(res.code == 0){
            $.sendError(res.msg,1000);
            return false;
        }else if (res.code == 2){
          selectError('今日移动端排名批量查询次数已达上限，是否升级会员组获取更多次数');
        } else {
          var ydurl = $.trim($("#ydurl").val());
          var ydtype = $("#ydtype").val();
          var ydkeywords = $("#ydkeywords").val();
          var arrkw = ydkeywords.split(/[\n,]/g)
          if(ydurl == ''){
            $.sendWarning('请输入网址', 1000);
            return false;
          }
          ydurl = urlFilter(ydurl)
          if (!checkUrl(ydurl)) {
            $.sendError('网站地址格式错误', 1000);
            return false;
          }
          if(ydkeywords ==''){
            $.sendWarning('请输入关键词', 1000);
            return false;
          }else{
            if(ydkeywords.indexOf('\n') >= -1){
              if(ydkeywords == 0){
                $.sendWarning('请输入关键词', 1000);
                return false;
              }
              layui.use('layer', function(){
                var layer = layui.layer;
                $('.js').css('display','none');
                $.post("/a", {target:ydurl,keyword:ydkeywords,engine_type:ydtype,rank_plnum:rank_plsubmit}, function(data){
                  if (data.code == 0) {
                    $.sendWarning(data.msg, 1000);
                    return false;
                  }else{
                    $('#ydkeywords').val(data.keyword2)
                    $('#daochu').css('display', 'none');
                    $('#content').empty();
                    $('.sousuo').css('display','block');
                    $('#result').css('display','none');
                    $('#order').remove()
                    var str
                    var num = 0
                    shuju = []
                    for (var i in arrkw) {
                      // arrkw[i] = arrkw[i].replace(/\s/g,"")
                      arrkw[i] = arrkw[i].trim()
                      if (arrkw[i]) {
                        num++
                        var xuhao = ~~i+1
                        str += '<tr style="text-align: center;font-size: 14px;line-height: 40px;border-top:1px solid #f6f6fc">'
                        str += '<td class=xuhao>'+xuhao+'</td>'
                        str += '<td>'+ydurl+'</td>'
                        str += '<td>'+data.engine_type+'</td>'
                        str += '<td>'+'手机端'+'</td>'
                        str += '<td>'+arrkw[i]+'</td>'
                        str += '<td>'
                        str += data.sousl[i]?data.sousl[i]:'<img src="/static/home2/images/wh2.png" style="width:15px;cursor: pointer;line-height: 39px;" title="暂无查到搜索量">'
                        str += '</td><td id="rank'+arrkw[i]+'" class="norank" style="color: #423da1;font-weight: bold;"><img src="/static/home2/images/loading.gif" alt="" style="width:15px;cursor: pointer;line-height: 39px;"></td>'
                        str += '<td id="flow'+arrkw[i]+'" class="noflow"><img src="/static/home2/images/loading.gif" alt="" style="width:15px;cursor: pointer;line-height: 39px;"></td></tr>'

                        shuju.push({
                          '序号':xuhao,
                          '网址地址':ydurl,
                          '搜索引擎':data.engine_type,
                          '终端':'手机端',
                          '关键词':arrkw[i],
                          '移动端搜索量':data.sousl[i]?data.sousl[i]:'暂未查到搜索量',
                          '排名':'100+',
                          '预估流量':0
                        })
                      }  
                    }
                    
                    $('#content').append(str);
                    $("#content tr").addClass("cp");
                    $("#content tr").mouseover(function (){  
                        $(this).addClass('stty');  
                    }).mouseout(function (){  
                        $(this).removeClass('stty');  
                    });  

                    var num2 = 0;
                    var starttime = Date.parse(new Date())/1000
                    var timer = setInterval(function(){
                      if ((Date.parse(new Date())/1000 - starttime)>15) {
                        $('.norank').removeClass('norank').html('100+')
                        $('.noflow').removeClass('norank').html(0)
                        $('#daochu').css('display', 'block')
                        $('.zhousl').append('<img src="/static/home2/images/desc.png" id="order">')
                        clearInterval(timer)
                      }
                      $.post('/b', {}, function(data){
                        if (data.code == 1) {
                          starttime = Date.parse(new Date())/1000
                          var info = data.list;
                          num2 = parseInt(num2)+parseInt(info.length);
                          var flow = '';
                          for (var i in info) {
                              var res = JSON.parse(info[i]);
                              if(res['rank'] == 1){
                                flow = Math.round(res['sousl'] * 0.9);
                              }else if(res['rank'] == 2){
                                flow = Math.round(res['sousl'] * 0.8);
                              }else if(res['rank'] == 3){
                                flow = Math.round(res['sousl'] * 0.7); 
                              }else if(res['rank'] == 4){
                                flow = Math.round(res['sousl'] * 0.6);
                              }else if(res['rank'] == 5){
                                flow = Math.round(res['sousl'] * 0.5);
                              }else if(res['rank'] == 6){
                                flow = Math.round(res['sousl'] * 0.4);
                              }else if(res['rank'] == 7){
                                flow = Math.round(res['sousl'] * 0.3);
                              }else if(res['rank'] == 8){
                                flow = Math.round(res['sousl'] * 0.2);
                              }else if(res['rank'] == 9){
                                flow = Math.round(res['sousl'] * 0.1);
                              }else{
                                flow = Math.round(res['sousl'] * 0);
                              }

                              $('#rank'+res['keyword']).html(res['rank'])
                              $('#flow'+res['keyword']).html(flow)

                              for (var i in shuju) {
                                if (shuju[i]['关键词'] == res['keyword']) {
                                  shuju[i]['排名'] = res['rank']
                                  shuju[i]['预估流量'] = flow
                                }
                              }
                          }
                          if (num2>=num) {
                            $('#daochu').css('display', 'block')
                            $('.zhousl').append('<img src="/static/home2/images/desc.png" id="order">')
                            clearInterval(timer)
                          }
                        }
                      }, 'json')
                    }, 1500);
                  }
                }, 'json');
              }); 
            }else{
              $.sendError('批量查询格式不匹配', 1000);
              return false;
            }
          }
        }
      }, 'json') 
    });

    var pmorder = $('.pldata').attr('data-moborder');
    var numorder = 1;
    $(document).on('click', '#order', function(e){
      if (!userid) {
          $.sendWarning('请登录后操作', 1000);
          return false;
      } else {
          if (pmorder == 0) {
            selectError('普通用户暂不支持排序功能,确定要升级会员?');
          } else {
              thi = $(this).parent().index();
              if(numorder == 1){
                  numorder = 2; 
                  clickFun(thi);
                  //调用比较函数 升序
                  fSort(compare_down);
                  console.log(aTdCont);
                  //重新排序行
                  setTrIndex(thi);
              }else{
                  numorder = 1;
                  clickFun(thi);
                  //调用比较函数,降序
                  fSort(compare_up);
                  //重新排序行
                  setTrIndex(thi);
              }
          }
      }
    })
    //重新对TR进行排序
    var setTrIndex = function(tdIndex){
        for(i=0;i<aTdCont.length;i++){
           var trCont = aTdCont[i];
            $("#content tr").each(function() {
                var thisText = $(this).children("td:eq("+tdIndex+")").text();
                if(thisText == trCont){
                    $("#content").append($(this));
                }
            });  
        }
    } 
    //比较函数的参数函数
    var compare_down = function(a,b){
        if (b == '100+') {
          b = 100;
        }
        return a-b;
    } 
    var compare_up = function(a,b){
        if (a == '100+') {
          a = 100;
        }
        return b-a;
    } 
     //比较函数
    var fSort = function(compare){
        aTdCont.sort(compare);
    }
    //取出TD的值，并存入数组,取出前二个TD值；
    var fSetTdCont = function(thIndex){
        $("#content tr").each(function() {
            var tdCont = $(this).children("td:eq("+thIndex+")").text();
            aTdCont.push(tdCont);
        });
    }
    //点击时需要执行的函数
    var clickFun = function(thindex){
        aTdCont = [];
        //获取点击当前列的索引值
        var nThCount = thindex;
        //调用sortTh函数 取出要比较的数据
        fSetTdCont(nThCount);
    } 
    
    // 点击导出数据
    $('#daochu').click(function(){
      if (!userid) {
        $.sendWarning('请登录后操作', 1000)
      } else {
        fuzhi()
        if (userlevel == '1') {
          $.sendConfirm({
            hideHeader: true,
            withCenter: true,
            msg: '该操作需要2积分，你确定要执行该操作吗?',
            button: {
              confirm: '确认',
              cancel: '取消'
            },
            onConfirm: function() {
              $.post('/getPoint', {}, function(point){
                if(point<2) {
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
                  $('#daochuForm input[name=point]').val('2')
                  $('#daochuForm input[name=shuju]').val(JSON.stringify(shuju))
                  $('#daochuForm').submit()
                  // location.href = "/export2excel?shuju="+JSON.stringify(shuju)+"&title=移动端批量排名导出&point=2"
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
          $.post('/exporttype', {exporttype:'rank_exportnum'}, function (data) {
              if (data.code == 1) {
                $('#daochuForm input[name=point]').val('0')
                $('#daochuForm input[name=shuju]').val(JSON.stringify(shuju))
                $('#daochuForm').submit()
                // location.href = "/export2excel?shuju="+JSON.stringify(shuju)+"&title=移动端批量排名导出&point=0"
              } else {
                $.sendConfirm({
                  hideHeader: true,
                  withCenter: true,
                  msg: '今日排名批量导出次数已达上限，继续操作需要2积分，确定要导出吗?',
                  button: {
                    confirm: '确认',
                    cancel: '取消'
                  },
                  onConfirm: function() {
                    $.post('/getPoint', {}, function(point){
                      if(point<2) {
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
                        $('#daochuForm input[name=point]').val('2')
                        $('#daochuForm input[name=shuju]').val(JSON.stringify(shuju))
                        $('#daochuForm').submit()
                        // location.href = "/export2excel?shuju="+JSON.stringify(shuju)+"&title=移动端批量排名导出&point=2"
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
  });