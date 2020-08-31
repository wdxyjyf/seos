$(function(){
    //回车按键
    $(document).keydown(function(event){
    　　if(event.keyCode ==13){
    　　　　$("#selectqz").trigger("click");
    　　}
    });

    $("#selectqz").click(function(){
      $.post("/home/quanzhong/toplimit", {}, function(res){
        if(res.code == 0){
          $.sendError(res.msg, 1000, function(){
            location.reload();
          })
          return false;
        }else if (res.code == 2){
          selectError('今日词频查询次数已达上限，是否升级会员组获取更多次数');
        } else{
          var input = $.trim($(".qzweb").val());
          var input2 = $(".qzkey").val().trim().replace(/\s/g,"");
          if(input == ''){
            $.sendWarning('请输入网址', 1000);
            return false;
          }
          if (!checkqzurl(input)) {
            $.sendError('网站地址格式错误', 1000);
            return false;
          }
          if(input2 !== ''){
            if (!checkKeyword(input2)) {
              $.sendError('关键词格式错误,请输入中文,字母或数字', 1000);
              return false;
            }
            if( getByteLen(input2)>30){
                $.sendError('关键词长度最大15个字符', 1000);
                return false;
            }
          }
          input = input.replace('http://', '')
          input = input.replace('https://', '')
          $('.qzweb').val(input)
          input = urlFilter(input)
          if (input2 == '') {
            $('.search form').attr('action', '/rate/'+input)
            $('.search form').submit()
            // location.href='/rate/'+input;
            // $('form').attr('action', $('form').attr('action')+"?web="+input)
          } else {
            $('.search form').attr('action', '/rate/'+input+'/'+BASE64.urlsafe_encode(input2))
            $('.search form').submit()
            // location.href='/rate/'+input+'/'+input2;
            // $('form').attr('action', $('form').attr('action')+"?web="+input+"&key="+input2)
          }
          // $('.search form').submit();
        }
      }, 'json')
    });
    var type = $('.jsdata').attr('data-count');
    var cpurl= $('.qzweb').val();
    var cpkey = $('.qzkey').val();
    if ($('#isAjax').val() == 1) {


        if(type == 0){
        $.post('/home/quanzhong/qzrecord2', {qzurl: cpurl,qzkey:cpkey}, function(data) {
          if(data.code == 1){
            var cipin = data.cipin;
            onecipin(cipin,Date.parse(new Date())/1000);
            function onecipin(cipin, time){
              setTimeout(function(){
                $.post('/home/quanzhong/getOnePinl', {cipin:cipin}, function(res) {
                  if (res.code == 0 && (Date.parse(new Date())/1000 - time)<4) {
                    onecipin(cipin,time)
                  } else {
                    if(res.code == 0){
                      $('#qznum').html('0');
                      $('#cpnum').html('0');
                      $("#qzbtn").html('<button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-normal" style="background: #">暂无标准</button>');
                    }else{
                      if(res.list.frequency>=0  && res.list.frequency < 3){
                        $("#qzbtn").html('<button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-normal" style="background: #ffc34f">偏低</button>');
                      }else if(res.list.frequency >=3  && res.list.frequency <10){
                        $("#qzbtn").html('<button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-normal" style="background: #89ca82">标准</button>');
                      }else{
                        $("#qzbtn").html('<button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-normal" style="background: #FF9A63">偏高</button>');
                      }
                      $('#qznum').html(res.list.frequency+'%');
                      $('#cpnum').html(res.list.word_num);
                    }
                  }
                })
              }, 2000)
            }
          } 
        });
      }else{
        $.post('/home/quanzhong/qzrecord3', {qzurl: cpurl}, function(res) {
          if(res.code == 1){
            $('#qzimg').css('display','none');
            var str = '';
            var list = res.list;
            if(list.length==0){
              $('.qzarr').css('display','none');
              $('#qzres').css('display','block');
            }else{
              for (var i in list) {
                var res = list[i];      
                str += '<tr style="text-align: center;font-size: 14px;line-height: 40px;">'
                str += '<td>'+(parseInt(i)+1)+'</td>'
                str += '<td>'+res['keyword']+'</td>'
                str += '<td>'+res['sousl']+'</td>'
                str += '<td>'+res['frequency']+'%</td>'
                str += '<td>'+res['word_num']+'</td>'
                if(res['frequency'] >= 0 && res['frequency'] <3){
                  str += '<td><button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-normal" style="background: #ffc34f">偏低</button></td>'
                }else if(res['frequency'] >= 3 && res['frequency'] < 10){
                  str += '<td><button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-normal" style="background: #89ca82">标准</button></td>'
                }else{
                  str += '<td><button class="layui-btn layui-btn-sm layui-btn-radius layui-btn-normal" style="background: #FF9A63">偏高</button></td>'
                }
                str += '</tr>'            
              }
              $('.qzarr').append(str);
              trhoverstyle(5);
            }
          }
        });
      }
    }
    

    //判断是否存在网址标题.么有走ajax
    var istit = $('.jsdata').attr('data-tit');
    var qzurl= $('.jsdata').attr('data-endurl');
    if (istit == 0) {
      $.post('/home/quanzhong/qzredis', {qzurl: qzurl}, function(data) {
        console.log(data.code);
      });
    }
});