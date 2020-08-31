 $(function(){
    $("#selectweb").click(function(){
      $.post("home/speed/toplimit", {}, function(res){
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
          if (!checkUrl(input)) {
            $.sendError('网站地址格式错误', 1000);
            return false;
          }
          $('.search form').submit();
          
        }
      }, 'json') 
    });
    $(".search form").keypress(function(e) {
      if (e.which == 13) {
        return false;
      }
    });

    var speed = $('.speed').attr('data-sign')

    getInfo(speed);
    var geoCoordMap = {};
    var datavalue = []
    var paixu = []
    
    function getInfo(speed){
      $.post('', {sign:speed}, function(data){
          if (data.code == 1) {
            var info = data.data
            $('#content').css('width', '500px')
            $('#content').css('overflow', 'auto')
            $.each(info, function(i,v) {
              var str = "<tr style='text-align: center;font-size: 14px;line-height: 45px;border-top:1px solid #f6f6fc'><td></td>"
              str += "<td>"+v.ip+"</td>"
              str += "<td>"+v.city+"</td>"
              str += "<td>"+v.connect_time+"</td>"
              str += "<td>"+v.down_size+"kb</td>"
              str += "<td data="+v.down_speed+">"+v.speed+"m/s</td>"
              str += "<td>"+v.isp+"</td>"
              // str += "<td>"+v.nslookup_time+"s</td>"
              str += "<td>"
              str += v.total_time==100?"<span style='color:red;'>超时</span>":v.total_time+"s"
              str += "</td></tr>"
           
              paixu.push(v.down_speed)
              paixu.sort(sortNumber)
              var index = paixu.indexOf(v.down_speed)
              if (index == 0) {
                $('#content').prepend(str)
              } else {
                var newindex = index-1
                $('#content tr:eq('+newindex+')').after(str)
              }
              $('#content tr').each(function(i,v){
                var xuhao = $(this).index()+1
                if (xuhao == 1) {
                  $(v).find('td:eq(0)').addClass('sort sort1')
                } else if (xuhao == 2) {
                  $(v).find('td:eq(0)').addClass('sort sort2')
                } else if (xuhao == 3) {
                  $(v).find('td:eq(0)').addClass('sort sort3')
                } else {
                  $(v).find('td:eq(0)').removeClass('sort')
                  $(v).find('td:eq(0)').removeClass('sort1')
                  $(v).find('td:eq(0)').removeClass('sort2')
                  $(v).find('td:eq(0)').removeClass('sort3')
                }

                $(v).find('td:eq(0)').text($(this).index()+1)
              })
              
              // 图形
              geoCoordMap[v.city] = [v.lng,v.lat]
              datavalue.push({name: v.city, value: v.total_time})
              tuxing(geoCoordMap, datavalue)
            })
            getInfo()
          } else {
            $('.loading').css('display', 'none');
          }
      })
    }
    
    // 地图
      function tuxing(geoCoordMap, datavalue){
        if (datavalue.length == 1) {
          $('#down').css('display', 'block')
        }
          var convertData = function (data) {
              var res = [];
              for (var i = 0; i < data.length; i++) {
                  var geoCoord = geoCoordMap[data[i].name];
                  if (geoCoord) {
                      res.push({
                          name: data[i].name,
                          value: geoCoord.concat(data[i].value)
                      });
                  }
              }
              return res;
          };

          option = {
              backgroundColor: '#fff',
              title: {
                  text: '网站测速热力图',
                  x:'center',
                  textStyle: {
                      color: '#423da1'
                  }
              },
              tooltip: {
                  trigger: 'item',
                  formatter: function (params) {
                    if (params.value[2] == 100) {
                      return params.name + ' : ' + '超时';
                    } else {
                      return params.name + ' : ' + params.value[2]+'s';
                    }
                    
                  }
              },
              legend: {
                  orient: 'vertical',
                  y: 'bottom',
                  x:'right',
                  data:['pm2.5'],
                  textStyle: {
                      color: '#fff'
                  }
              },
              visualMap: {
                  inverse:true,
                  show : true,
                  itemHeight: 20,
                  x: '20',
                  y: 'bottom',
                  splitList: [
                      {start: 0, end:0.2, label:"<=0.1s"},
                      {start: 0.2, end: 0.4, label:"0.2~0.4s"},
                      {start: 0.4, end: 0.6, label:"0.4~0.8s"},
                      {start: 0.6, end: 0.8, label:"0.6~0.8s"},
                      {start: 0.8, end: 1.0, label:"0.8~1.0s"},
                      {start: 1.0, label:">1s"},
                  ],
                  color: ['#e61610', '#f69833', '#f6ed44','#bef663', '#42dd3f', '#24aa1d'],
                  textStyle: {
                      color: 'black'
                  }
              },
              geo: {
                  map: 'china2',
                  label: {
                      emphasis: {
                          show: false
                      }
                  },
                  itemStyle: {
                      normal: {
                          areaColor: '#323c48',
                          borderColor: '#111'
                      },
                      emphasis: {
                          areaColor: '#2a333d'
                      }
                  }
              },
              series: [
                  {
                      // name: 'pm2.5',
                      type: 'scatter',
                      coordinateSystem: 'geo',
                      data: convertData(datavalue),
                      symbolSize: 12,
                      label: {
                          normal: {
                              show: false
                          },
                          emphasis: {
                              show: false
                          }
                      },
                      itemStyle: {
                          emphasis: {
                              borderColor: '#fff',
                              borderWidth: 1
                          }
                      }
                  }
              ]
          }
          // 渲染
          var myChart = echarts.init(document.getElementById('map'));
          myChart.setOption(option);
      }


      function sortNumber(a,b){
        return b-a
      }
    

  });