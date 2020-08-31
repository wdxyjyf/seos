$(function(){
  	var keyword_plsubmit = parseInt($('.hotpl').attr('data-submit'));
  	var userid = getUserinfo()[0];
  	var userlevel = getUserinfo()[1];
  	var dgorder = $('.hotpl').attr('data-dgorder');
   	trhoverstyle(3);
    $("#subplhot").click(function(){
    	$.post("/home/keywords/toplimit", {type:'keyword_plquerynum'}, function(res){
			if(res.code == 0){
			    $.sendError(res.msg,1000);
			    return false;
			}else if (res.code == 2){
				selectError('今日关键词批量查询次数已达上限，是否升级会员组获取更多次数');
			} else {
				var input = $.trim($(".keysearch").val());
		        if(input == ''){
		          $.sendWarning('请输入查询的关键词', 1000);
		          return false;
		        }else{
		        	if(input.indexOf('\n') >= -1){
		        		if(input == 0){
		        			$.sendWarning('请输入查询的关键词', 1000);
		          			return false;
		        		}
		        		var arrpl = input.split("\n");
			            var arrpl1 = new Array
			            $.each(arrpl, function(i,v){
							var v1 = v.trim().replace(/\s/g, "");
							if (v1) {
								if (!checkKeyword(v1)) {
									$.sendError('关键词: '+v1+' 格式错误,请输入中文,字母或数字', 2000);
									return false;
								} 
								if (getByteLen(v1) >30) {
									$.sendError('关键词: '+v1+' 长度最大15个字符', 2000);
									return false;
								}
								arrpl1.push(v1)
							}
			            })
						$.post('/keywords', {plhotkey:arrpl1,keyword_plnum:keyword_plsubmit}, function(data){
							$('#fenx').empty();
				            $('#tit').text(data.tit);
				            if(data.code == 0){
				            	$.sendError(data.msg, 1000);
				            	return false;
				            }else if(data.code == 3) {
				                $.sendError(data.msg, 1000);
				            	return false;
				            }else if(data.code == 1){
				            	$('#tbody').empty();
				            	var mcd = data.mcd
				            	if (data.list) {
				                  	var str = '';
				                  	var info = data.list;
				                    shuju = []
				                    var ds = 0;
				                    for (var i in info) {
				                    	if (!info[i]['no']) {
					                        str += '<tr style="text-align: center;font-size: 14px;line-height: 40px;border-top:1px solid #f6f6fc" keywordpl='+info[i]['keyword']+'>'
						            		if (info[i]['keyword'].replace(/[^\x00-\xff]/g, "01").length > 10)
					                        {
					                            str += '<td><a class="keyall" href="javascript:;">'+info[i]['keyword'].substring(0,6)+"..."
					                        }else{
					                        	str += '<td><a class="keyall" href="javascript:;">'+info[i]['keyword']
											}
											str += `</a><div class="hover-button">
														搜
													<div>
														<ul>
															<li><a target="_blank" href="/dig/`+BASE64.urlsafe_encode(info[i]['keyword'])+`">相关词</a></li>
															<li><a target="_blank" href="/related/`+BASE64.urlsafe_encode(info[i]['keyword'])+`">长尾词</a></li>`
											if (getStrLength(info[i]['keyword']) <= 10) {
												str += `<li><a target="_blank" href="/findsites/`+BASE64.urlsafe_encode(info[i]['keyword'])+`">相关网站</a></li>`
											}
											str += '</ul></div></div></td>'
						            		str += '<td>'+info[i]['averagePv']+'</td>'
						            		str += '<td>'+info[i]['averagePvPc']+'</td>'
						            		str += '<td>'+info[i]['averagePvMobile']+'</td>'
						            		str += '<td>'+info[i]['averageDayPv']+'</td>'
						            		str += '<td>'+info[i]['averageDayPvPc']+'</td>'
						            		str += '<td>'+info[i]['averageDayPvMobile']+'</td>'
				                      	} else {
				                        	ds = 1;
				                        	str += '<tr style="text-align: center;font-size: 14px;line-height: 40px;border-top:1px solid #f6f6fc" keywordpl='+info[i]['keyword']+'>'
						            		if (info[i]['keyword'].replace(/[^\x00-\xff]/g, "01").length > 10)
					                        {
					                            str += '<td><a class="keyall" href="javascript:;">'+info[i]['keyword'].substring(0,6)+"..."
					                        }else{
					                        	str += '<td><a class="keyall" href="javascript:;">'+info[i]['keyword']
											}
											str += `</a><div class="hover-button">
														搜
													<div>
														<ul>
															<li><a target="_blank" href="/dig/`+BASE64.urlsafe_encode(info[i]['keyword'])+`">相关词</a></li>
															<li><a target="_blank" href="/related/`+BASE64.urlsafe_encode(info[i]['keyword'])+`">长尾词</a></li>`
											if (getStrLength(info[i]['keyword']) <= 10) {
												str += `<li><a target="_blank" href="/findsites/`+BASE64.urlsafe_encode(info[i]['keyword'])+`">相关网站</a></li>`
											}
											str += '</ul></div></div></td>'
						            		str += '<td><img src="/static/home2/images/loading.gif" alt="" style="width:15px;line-height: 39px;"></td>'
						            		str += '<td><img src="/static/home2/images/loading.gif" alt="" style="width:15px;line-height: 39px;"></td>'
						            		str += '<td><img src="/static/home2/images/loading.gif" alt="" style="width:15px;line-height: 39px;"></td>'
						            		str += '<td><img src="/static/home2/images/loading.gif" alt="" style="width:15px;line-height: 39px;"></td>'
						            		str += '<td><img src="/static/home2/images/loading.gif" alt="" style="width:15px;line-height: 39px;"></td>'
						            		str += '<td><img src="/static/home2/images/loading.gif" alt="" style="width:15px;line-height: 39px;"></td>'
				                      	} 
				                      	str += '</tr>'
				                	}
				            		$('#tbody').append(str);
				                  	// 鼠标悬浮表格样式
				                  	trhoverstyle(3);
					                if (ds == 1) {
					                    $('#daochu').css('display','none');
					                    $('.order').addClass('hide')
					                    getInfo(mcd);
					                } else {
					                	$('#daochu').css('display','block');
					                	$('.order').removeClass('hide')
					                }
				                }
				            } 
				        });
				       
				        
			        }else{
			        	$.sendError('批量查询格式不匹配', 1000);
			        	return false;
			        }
		       	}
			}
      	}, 'json') 
    });

	function getInfo(mcd){
		$.post('/home/keywords/hotplb', {mcd:mcd}, function(res){
			if (res.code == 0) {
				$('#daochu').css('display','block');
				$('img[src="/static/home2/images/loading.gif"]').parents('tr').each(function(i,v){
					$('.order').removeClass('hide')
					$(v).find('td:gt(0)').html("<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
					// $(v).find('td:last()').html("<span style='color:#423da1'>未备案</span>&nbsp;<a href='javascript:;' style='color:#0d8c21'>[更新]</a>")
				});
			}else{
				$.each(res.info, function(i, v){
					$('tr[keywordpl="'+v['keyword']+'"] td:eq(2)').html(v['averagePv']?v['averagePv']:"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
					$('tr[keywordpl="'+v['keyword']+'"] td:eq(3)').html(v['averagePvPc']?v['averagePvPc']:"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
					$('tr[keywordpl="'+v['keyword']+'"] td:eq(4)').html(v['averagePvMobile']?v['averagePvMobile']:"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
					$('tr[keywordpl="'+v['keyword']+'"] td:eq(5)').html(v['averageDayPv']?v['averageDayPv']:"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
					$('tr[keywordpl="'+v['keyword']+'"] td:eq(6)').html(v['averageDayPvPc']?v['averageDayPvPc']:"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
					$('tr[keywordpl="'+v['keyword']+'"] td:eq(7)').html(v['averageDayPvMobile']?v['averageDayPvMobile']:"<img src='/static/home2/images/wh2.png' style='width:15px;cursor: pointer;line-height: 39px;' title='暂无数据'>")
				})
				mcd = res.mcd;
				if (mcd.length == 0) {
					$('#daochu').css('display','block');
					$('.order').removeClass('hide')
				} else {
					getInfo(mcd)
				}
			}
		})
    }
    //排序
   
	$('.order').each(function(i,v){
        $(v).click(function(){
            orderule($(v));
        })
    })
    
    
    //排序权限
    var numorder = 1;
    function orderule(tt){
        if (!userid) {
            $.sendWarning('请登录后操作', 1000);
            return false;
        } else {
            if (dgorder == 0) {
                selectError('普通用户暂不支持排序功能,确定要升级会员?');
            } else {
            	thi = tt.parent().index();
				if(numorder == 1){
				  numorder = 2; 
				  clickFun(thi);
				  //调用比较函数 升序
				  fSort(compare_down);
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
    }

    //重新对TR进行排序
    var setTrIndex = function(tdIndex){
        for(i=0;i<aTdCont.length;i++){
           var trCont = aTdCont[i];
            $("#tbody tr").each(function() {
                var thisText = $(this).children("td:eq("+tdIndex+")").text();
                if(thisText == trCont){
                    $("#tbody").append($(this));
                }
            });  
        }
    } 
    //比较函数的参数函数
    var compare_down = function(a,b){
        if (b == '') {
          b = 0;
        }
        return a-b;
    } 
    var compare_up = function(a,b){
        if (a == '') {
          a = 0;
        }
        return b-a;
    } 
     //比较函数
    var fSort = function(compare){
        aTdCont.sort(compare);
    }
    //取出TD的值，并存入数组,取出前二个TD值；
    var fSetTdCont = function(thIndex){
        $("#tbody tr").each(function() {
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

	function fuzhi(){
      $('#fuzhi').select()
      document.execCommand('copy')
    }
    // 点击导出数据
    $('#daochu').click(function(){
      if (!userid) {
        $.sendWarning('请登录后操作', 1000)
      } else {
      	var shuju = [];
      	$('#tbody tr').each(function(i, v){
      		
            shuju.push({
              '关键词':$(v).find('td:eq(0)').text(),
              '周搜索量':$(v).find('td:eq(1)').text()?$(v).find('td:eq(1)').text():0,
              '周PC搜索量':$(v).find('td:eq(2)').text()?$(v).find('td:eq(2)').text():0,
              '周移动搜索量':$(v).find('td:eq(3)').text()?$(v).find('td:eq(3)').text():0,
              '日搜索量':$(v).find('td:eq(4)').text()?$(v).find('td:eq(4)').text():0,
              'PC日搜索量':$(v).find('td:eq(4)').text()?$(v).find('td:eq(4)').text():0,
              '移动日搜索量':$(v).find('td:eq(4)').text()?$(v).find('td:eq(4)').text():0
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

							// location.href = "/export2excel?shuju="+JSON.stringify(shuju)+"&title=批量关键词查询导出&point=1"
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
        	$.post('/exporttype', {exporttype:'keyword_exportnum'}, function (data) {
	            if (data.code == 1) {
	            	$('#daochuForm input[name=point]').val('0')
					$('#daochuForm input[name=shuju]').val(JSON.stringify(shuju))
					$('#daochuForm').submit()
	               // location.href = "/export2excel?shuju="+JSON.stringify(shuju)+"&title=批量关键词查询导出&point=0"
	            } else {
			        $.sendConfirm({
			            hideHeader: true,
			            withCenter: true,
			            msg: '今日批量关键词导出次数已达上限,该操作需要1积分，确定要继续导出吗?',
			            button: {
			              confirm: '确认',
			              cancel: '取消'
			            },
			            onConfirm: function() {
			            	$.post('/getPoint', function(point){
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
				                	// location.href = "/export2excel?shuju="+JSON.stringify(shuju)+"&title=批量关键词查询导出&point=1"
				                }
			            	}) 
			              
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
	
	$('#tbody').on('mouseenter', '.hover-button', function(e) {//绑定鼠标进入事件
		$(this).find('div').css('display', 'block')
	});
	$('#tbody').on('mouseleave', '.hover-button', function(e) {//绑定鼠标划出事件
		$(this).find('div').css('display', 'none')
	});
});

function getStrLength(str) {
	var cArr = str.match(/[^\x00-\xff]/ig);
	return str.length + (cArr == null ? 0 : cArr.length);
}