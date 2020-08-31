$(function () {
    //回车按键
    $(document).keydown(function(event){
    　　if(event.keyCode ==13){
    　　　　$("#selectweb").trigger("click");
            return false
    　　}
    });

    var keyword = $('.xgwebsite').val();//用户提交的关键词
    var userid = getUserinfo()[0];
    var userlevel = getUserinfo()[1];
    //复制
    function fuzhi() {
        $('#fuzhi').select()
        document.execCommand('copy')
    }
    //鼠标的移入移出
    trhoverstyle(1);
    // 点击查询按钮
    $("#selectweb").click(function () {
        var input = $('.xgwebsite').val().trim().replace(/\s/g,"");
        if(input == ''){
            $.sendWarning('请输入需要查询的关键词信息', 1000);
            return false;
        }
        if (!checkKeyword(input)) {
            $.sendError('格式错误,请输入中文,字母或数字', 1000);
            return false;
        }
        return location.href='/findsites/'+BASE64.urlsafe_encode(input);
    });
    // 导出按钮
    $('#daochu').click(function () {
        if (!userid) {
            $.sendWarning('请登录后操作', 1000);
            return false;
        } else {
            $.sendError('即将开放', 1000);
            return false
            fuzhi();
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
                                location.href = "/xgwebexprot?xgkeyword="+keyword+"&shuju=0"
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
                location.href = "/xgwebexprot?xgkeyword="+keyword+"&shuju=1"
                // $.post('/exporttype', {exporttype:'keyword_exportnum'}, function (data) {
                //     if (data.code == 1) {
                //        location.href = "/xgwebexprot?xgkeyword="+keyword+"&shuju=1"
                //     } else {
                //         $.sendConfirm({
                //             hideHeader: true,
                //             withCenter: true,
                //             msg: '今日相关网站导出次数已达上限，该操作需要2积分，确定要继续导出吗?',
                //             button: {
                //                 confirm: '确认',
                //                 cancel: '取消'
                //             },
                //             onConfirm: function () {
                //                 $.post('/getPoint', {}, function (point) {
                //                     if (point < 2) {
                //                         $.sendConfirm({
                //                             hideHeader: true,
                //                             withCenter: true,
                //                             msg: '积分不足，可邀请更多好友注册，获得VIP会员权限，是否复制邀请？',
                //                             button: {
                //                                 confirm: '确认',
                //                                 cancel: '取消'
                //                             },
                //                             onConfirm: function () {
                //                                 $.sendSuccess('已复制到剪切板', 1000)
                //                             },
                //                             onCancel: function () {
                //                                 return false;
                //                             },
                //                             onClose: function () {
                //                                 return false;
                //                             }
                //                         });
                //                         return false
                //                     } else {
                //                         location.href = "/dgdc?digkeywords="+keyword+"&shuju=0"
                //                     }
                //                 }, 'json')
                //             },
                //             onCancel: function () {
                //                 return false;
                //             },
                //             onClose: function () {
                //                 return false;
                //             }
                //         });   
                //     }
                // }, 'json')   
            }  
        }
    })
    
    /*分页栏点击page*/
    $('.pagination a').each(function(k,v){
        var aval = $(v).attr('href');
        var aval = aval.substr(aval.lastIndexOf('=')+1);
        var url = $(v).attr('href').substr(0,$(v).attr('href').indexOf('?'));
        if (!$(v).hasClass('cur')) {
            $(v).attr('href', url)
            $(v).click(function(){
                if (aval> 10) {
                    if (userid == '') {
                        $.sendWarning('请登录查看更多数据结果', 1000);
                        return false;
                    } else {
                        $.sendWarning('即将开放', 1000);
                        return false;
                    }
                }
                
                $('#xgwebForm input[name=page]').val(aval)
                $('#xgwebForm').attr('action', $('#xgwebForm').attr('action')+"/"+BASE64.urlsafe_encode(keyword));
                $('#xgwebForm').submit();
                return false;
            })
        }
    })
});