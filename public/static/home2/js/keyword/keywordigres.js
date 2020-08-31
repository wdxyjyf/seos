$(function () {
    //回车按键
    $(document).keydown(function(event){
    　　if(event.keyCode ==13){
    　　　　$("#selectdig").click();
            return false;
    　　}
    });
    
        var level = $('.alldata').attr('data-level');//用户权限对应展示的条数结果，1：显示，0：遮盖
        var keyword = $('.digkeywords').val();//用户提交的关键词
        var sort = $('.alldata').attr('data-sort');
        var userid = getUserinfo()[0];
        var userlevel = getUserinfo()[1];
        var dgorder = $('.alldata').attr('data-dgorder');

        // 根据用户权限遮盖数据
        if (level == 0) {
            $('.rulebox').css('display','block');
        }
        // 拼接查询词颜色
        $('.keyall').each(function(idx,item){
            var keywordtit = $(item).text();
            var reg = new RegExp(keyword,'g')
            $(item).html($(item).text().replace(reg, "<span style='color:#504cc1;font-weight:bold'>"+keyword+"</span>"))
        });
        
        $('.order').each(function(i,v){
            $(v).click(function(){
                $type = (~~i+1) == sort?(~~i+1)+'s':(~~i+1);
                orderule($type)
            })
        })
        //排序权限
        function orderule($type){
            if (!userid) {
                $.sendWarning('请登录后操作', 1000);
                return false;
            } else {
                if (dgorder == 0) {
                    selectError('普通用户暂不支持排序功能,确定要升级会员?');
                } else {
                    // location.href="/digr/"+keyword+'/'+$type;
                    $('#form').append('<input type="hidden" name="sort" value="'+sort+'" id="sort">')
                    $('#sort').val($type)
                    // console.log($type);return false;
                    $('#form').attr('action', "/dig/"+BASE64.urlsafe_encode(keyword));
                    $('#form').submit()
                }
            }
        }
        //复制
        function fuzhi() {
            $('#fuzhi').select()
            document.execCommand('copy')
        }
        
        //鼠标的移入移出
        trhoverstyle(1);
        // 点击查询按钮
        $("#selectdig").click(function () {
            hotclick('keywordig_querynum');
        });
        // 导出按钮
        $('#daochu').click(function () {
            if (!userid) {
                $.sendWarning('请登录后操作', 1000);
                return false;
            } else {
                if (level == 0) {
                    $.sendWarning('导出失败，没有数据可导出', 1000);
                    return false;
                } else { 
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
                                        location.href = "/dgdc?digkeywords="+keyword+"&shuju=0&sort="+sort
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
                               location.href = "/dgdc?digkeywords="+keyword+"&shuju=1&sort="+sort
                            } else {
                                $.sendConfirm({
                                    hideHeader: true,
                                    withCenter: true,
                                    msg: '今日关键词挖掘导出次数已达上限，该操作需要2积分，确定要继续导出吗?',
                                    button: {
                                        confirm: '确认',
                                        cancel: '取消'
                                    },
                                    onConfirm: function () {
                                        $.post('/getPoint', {}, function (point) {
                                            if (point < 2) {
                                                $.sendConfirm({
                                                    hideHeader: true,
                                                    withCenter: true,
                                                    msg: '积分不足，可邀请更多好友注册，获得VIP会员权限，是否复制邀请？',
                                                    button: {
                                                        confirm: '确认',
                                                        cancel: '取消'
                                                    },
                                                    onConfirm: function () {
                                                        $.sendSuccess('已复制到剪切板', 1000)
                                                    },
                                                    onCancel: function () {
                                                        return false;
                                                    },
                                                    onClose: function () {
                                                        return false;
                                                    }
                                                });
                                                return false
                                            } else {
                                                location.href = "/dgdc?digkeywords="+keyword+"&shuju=0&sort="+sort
                                            }
                                        }, 'json')
                                    },
                                    onCancel: function () {
                                        return false;
                                    },
                                    onClose: function () {
                                        return false;
                                    }
                                });   
                            }
                        }, 'json')   
                    }  
                } 
            }
        })
        
        /*分页栏点击page*/
        $('.pagination a').each(function(k,v){
            var aval = $(v).attr('href');
            var aval = aval.substr(aval.lastIndexOf('=')+1);
            if (!$(v).hasClass('cur')) {
                $(v).click(function(){
                    if (userid == '') {
                        if (aval> 10) {
                            $.sendWarning('请登录查看更多数据结果', 1000);
                            return false;
                        }
                    }
                    $('#dirForm input[name=sort]').val(sort)
                    $('#dirForm input[name=page]').val(aval)
                    $('#dirForm').attr('action', $('#dirForm').attr('action')+"/"+BASE64.urlsafe_encode(keyword));
                    $('#dirForm').submit();
                    return false;
                })
            }
        })

        $('.hover-button').hover(function(){
            $(this).find('div').show()
        }, function(){
            $(this).find('div').hide()
        })
    });