$(document).ready(function(){
    $(".ftt span:last").remove();
    jQuery(".nav1").slide({
      type: "menu",
      titCell: ".n",
      targetCell: ".sub1",
      effect: "slideDown",
      delayTime: 300,
      triggerTime: 100,
      returnDefault: true
    });
    jQuery(".nav").slide({
        type: "menu",
        titCell: ".m",
        targetCell: ".sub",
        effect: "slideDown",
        delayTime: 300,
        triggerTime: 100,
        returnDefault: true
    });

    $('.pay').click(function(){
      if ($('input[name=pay_type]:checked').val() == 'weixin') {
        $.post($(this).attr('href'), {pay_type:'weixin', gid:$('input[name=gid]').val()}, function(data) {
          if (data.code == 1) {
            showMask();
            new QRCode(document.getElementById("qrcode"),{
              text:  data.code_url,
              width: 200,
              height: 200
            });
            $('.unshow').removeClass('hide')
            var t = setInterval(function(){
              $.post('/home/users/wx_find?order_id='+data.order_id, function(data){
                if (data) {
                  clearInterval(t)
                  hideMask();
                  $('#qrcode').empty()
                  $('.unshow').addClass('hide')
                  $.sendSuccess('购买成功', 1000, function(){
                    location.href="/homeuser"
                  })
                }
              })
            }, 2000)
          }
        })
        return false
      } else{
        $('#pform').submit();
        return false;
      }
    })

    $('.qr-close').click(function(){
      hideMask();
      $('#qrcode').empty()
      $('.unshow').addClass('hide')
      $('.pay').removeAttr("disabled");
    })
});
//显示遮罩层    
function showMask(){     
  $("#mask").css("height",$(window).height());     
  $("#mask").css("width",$(window).width());     
  $("#mask").show();     
}  
//隐藏遮罩层  
function hideMask(){     
  $("#mask").hide();     
} 

//验证用户手机号
function checkPhone(obj){
  // var chars = /^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|16[0-9]{1}[0-9]{8}$|19[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$/;
	var chars = /^1[0-9]{10}$/;  
	if (!chars.test(obj)) {
	   return false;
	} else {
		return true;
	}
}

//验证url
function checkUrl(obj) {
  obj = obj.toLowerCase();
	var myreg =/([\u4e00-\u9fa5]*-*\w*-*\w*\.(?:com\.cn|gov\.cn|edu\.cn|xz\.cn|cn|com\.hk|yn\.cn|org\.cn|net\.cn|tv|law|COM|CN|ORG|COM\.CN|la|cc|com|co|tk|gov|pw|me|edu|ws|mil|biz|name|pro|aero|coop|museum|org|top|vip|club|xin|shop|ltd|wang|online|store|so|net|xyz|art|auto|beer|center|chat|citic|city|cloud|company|cool|hk|design|email|fashion|fit|fun|fund|gold|group|guru|host|info|ink|kim|life|link|live|love|luxe|mobi|plus|press|pub|red|ren|run|show|site|social|sohu|space|team|tech|today|video|website|wiki|world|work|yoga|zone|in|us|ch|hn|cm|re|vc|gs|wf|fm|gl|ru|asia|ag|im|bs|si|io|cd|cx|ci|gg|tm|ml|li|ee|tw|cat|travel|tn|lu|ly|sc|ba|win|政务\.cn|政务|中信|中国|中文网|企业|佛山|信息|公司|公益|商城|商店|商标|在线|集团|移动|娱乐|广东|我爱你|手机|招聘|时尚|游戏|网址|网店|网络)\/?$)/;
 	if (!myreg.test(obj)) {
		return false;
  	} else {
  		return true;
  	}
}

//验证url
function checkIp(obj) {
  var myreg =/((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}/g;
  if (!myreg.test(obj)) {
    return false;
  } else {
    return true;
  }
}

//验证输入的字符长度
function getByteLen(val) {
    var len = 0;
    for (var i = 0; i < val.length; i++) {
        var a = val.charAt(i);
        if (a.match(/[^\x00-\xff]/ig) != null) {
            len += 2;
        } else {
            len += 1;
        }
    }
    return len;
}
//错误提示信息
function selectError(string){
	$.sendConfirm({
		hideHeader: true,
		withCenter: true,
		msg: string,
		button: {
		  confirm: '确认',
		  cancel: '取消'
		},
		onConfirm: function () {
		  location.href = "/authlist"
		},
		onCancel: function () {
		  return false;
		},
		onClose: function () {
		  return false;
		}
  	});
  	return false;
}
//验证关键字
function checkKeyword(obj){
	var all = /^[A-Za-z0-9\u4e00-\u9fa5]+$/;
	if (!all.test(obj)) {
    return false;
	} else {
		return true;
	}
}

//验证监控网址的url
function checkjkurl(obj){
	var myreg = /^(?=^.{3,255}$)(http(s)?:\/\/)?(www\.)?[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+(:\d+)*(\/\w+\.\w+)*$/;
	if (!myreg.test(obj)) {
		return false;
  	} else {
  		return true;
  	}
}
//验证权重网址
function checkqzurl(obj){
	var myreg =/^(?:([A-Za-z]+):)?(\/{0,3})([0-9.\-A-Za-z]+)(?::(\d+))?(?:\/([^?#]*))?(?:\?([^#]*))?(?:#(.*))?$/;
	if (!myreg.test(obj)) {
		return false;
  	} else {
  		return true;
  	}
}
// 排序
function compare1(property){
	    return function(a,b){
	        var value1 = a[property];
	        var value2 = b[property];
	        return value2 - value1;
	    }
  	}
function compare2(property){
    return function(a,b){
        var value1 = a[property];
        var value2 = b[property];
        return value1 - value2;
    }
}

// 备案查询(单个)
function recordclick(_type){
    $.post("/home/recordquery/toplimit", {type:_type}, function(res){
        if(res.code == 0) {
          $.sendError(res.msg, 1000, function(){
            location.reload();
          }); 
          return false;
        } else if (res.code == 2) {
          selectError('今日备案查询次数已达上限，是否升级会员组获取更多次数');
        } else {
          var input = $(".beianurl").val().replace(/\s/g,"");
          if(input == ''){
            $.sendWarning('请输入要查询的网站地址', 1000); 
            return false;
          }
          input = urlFilter(input)
          if (!checkUrl(input)) {
            if (!checkIp(input)) {
              $.sendError('网站地址格式错误', 1000);
              return false;
            }
          }
          location.href='/beian/'+input;
          // $('.search form').submit();
        }
    }, 'json') 
}
//关键词点击
function hotclick(_type){
    if (_type == 'keyword_querynum') {
        var node = ".hotkeywords";
        var msg1 = "请输入需要查询的关键词信息";
        var msg2 = "关键词长度最大15个字符";
    } else if(_type == 'keywordig_querynum') {
        var node = ".digkeywords";
        var msg1 = "请输入要挖掘的相关词";
        var msg2 = "挖掘的关键词长度最大15个字符";
    } else if(_type == 'keyword_price') {
        var node = ".selinput";
        var msg1 = "请输入要竞价查询的关键词";
        var msg2 = "关键词长度最大15个字符";
    }
    $.post("/home/keywords/toplimit", {type:'keyword_querynum'}, function(res){
        if(res.code == 0){
            $.sendError(res.msg, 1000, function(){
                location.reload();
            })
            return false;
        }else if (res.code == 2){
            selectError('今日关键词查询次数已达上限,是否升级会员组获取更多次数');
        } else {
            var input = $(node).val().trim().replace(/\s/g,"");
            if(input == ''){
                $.sendWarning(msg1, 1000);
                return false;
            }
            if (!checkKeyword(input)) {
                $.sendError('格式错误,请输入中文,字母或数字', 1000);
                return false;
            }
            if(getByteLen(input) > 30){
                $.sendError(msg2, 1000);
                return false;
            }
            subredirect(node);
            // $('.search form').submit();
        }
    }, 'json') 
}
function subredirect(_type){
    var input = $(_type).val().trim().replace(/\s/g,"");
    if (_type == '.hotkeywords') {
       return location.href='/keyword/'+BASE64.urlsafe_encode(input);
    } else if(_type == '.digkeywords') {
       return location.href='/dig/'+BASE64.urlsafe_encode(input);
    } else if(_type == '.selinput') {
       return location.href='/compete/'+BASE64.urlsafe_encode(input);
    }
}
//相关词点击查询
function relateclick(){
    $.post("/home/relatedwords/toplimit", {}, function(res){
      if(res.code == 0){
        $.sendError(res.msg, 1000, function(){
          location.reload();
        })
        return false;
      } else if(res.code == 2) {
        selectError('今日长尾词查询次数已达上限，是否升级会员组获取更多次数');
      }else{
        var input = $(".selinput").val().trim().replace(/\s/g,"");
        if(input == ''){
          $.sendWarning('请输入要挖掘的长尾词', 1000);
          return false;
        }
        if (!checkKeyword(input)) {
          $.sendError('格式错误,请输入中文,字母或数字', 1000);
          return false;
        }
        if(getByteLen(input)>30){
            $.sendError("长尾词长度最大15个字符", 1000);
            return false;
        }
        location.href='/related/'+BASE64.urlsafe_encode(input);
        // $('.search form').submit();
        
      }
    }, 'json')
}
// 鼠标悬浮表格样式
function trhoverstyle(type){
    if (type == 1) {
        var node = ".pmddd #content tr";
    } else if(type == 2){
        var node = ".content tr";
    } else if(type == 3){
        var node = "#tbody tr";
    } else if(type == 4) {
        var node = "#content tr";
    } else if(type == 5) {
        var node = ".qzarr tr";
    } 
    $(node).css("cursor","pointer");
    //鼠标的移入移出
    $(document).on('mouseover', node, function(){
      $(this).addClass('stty');  
    })
    $(document).on('mouseout', node, function(){
      $(this).removeClass('stty');   
    })
    // $(node).mouseover(function (){  
    //     $(this).addClass('stty');  
    // }).mouseout(function (){  
    //     $(this).removeClass('stty');  
    // });  
}
//获取用户信息节点
function getUserinfo(){
    var userarr = [];
    userarr.push($('.userinfo').attr('data-id'));
    userarr.push($('.userinfo').attr('data-level'));
    userarr.push($('.userinfo').attr('data-ydurl'));
    return userarr;
}
// 移动排名,pc排名
function pcmobCommon(a,b){
  $.post('', {sign:a}, function(data){
    $('#rank').html(data?'<span style="color:#423da1;font-weight:bold">'+data.rank+'</span>':"暂无排名")
    $('#flow').html(data?data.flow:"暂无流量")
  })
  $('select[name=engine] option').each(function(i,v){
    if ($(v).val() == b) {
      $(v).prop('selected', 'selected')
    }
  })
} 
function urlFilter(url){
  url = url.replace('http://', '')
  url = url.replace('https://', '')
  if (url.indexOf('?') != '-1') {
    url = url.slice(0, url.indexOf('?'))
  }
  if (url.indexOf('/') != '-1') {
    url = url.slice(0, url.indexOf('/'))
  }
  return url;
}

function select_change(type){
  $('.xiala').click(function(){
    var data_val = $(this).attr('data-value');
    if (data_val == 1) {
        $('.dropdown-menu').show();
        $(this).attr('data-value','2');
    } else {
        $('.dropdown-menu').hide();
        $(this).attr('data-value','1');
    }
  })
  // 点击除按钮和弹框之外任意地方隐藏表情
  $("body").click(function (e) {
      if (!$(e.target).closest(".xiala,.dropdown-menu").length) {
          $('.dropdown-menu').hide();
          $('.xiala').attr('data-value','1');
      }
  });

  $('.dropdown-menu li').click(function(){
    $('.xiala p').attr('class', 'home_icons '+$(this).attr('data-icon'))
    $(type).val($(this).attr('data-engine'));
    $('.xiala').attr('data-value','2');
    $('.dropdown-menu').hide()
  })
}
