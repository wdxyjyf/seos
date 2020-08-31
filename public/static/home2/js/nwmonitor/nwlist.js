    function delweblist(id, type) {
        $.sendConfirm({
            hideHeader: true,
            withCenter: true,
            msg: '你确认要删除吗?',
            button: {
                confirm: '确认',
                cancel: '取消'
            },
            onConfirm: function () {
                $.post("/home/nwmonitor/dellist", {id: id, type:type}, function (data) {
                    if (data.code == 1) {
                        $.sendSuccess(data.msg, 1000, function () {
                            document.location.reload();
                        })
                    } else {
                        $.sendError(data.msg, 1000);
                        return false;
                    }
                });
            },
            onCancel: function () {
                return false;
            },
            onClose: function () {
                return false;
            }
        });
    }
    $(function () {
        var nwnum = $('.nwlist').attr('data-num');
        var nwpage = $('.pagination .cur').text()
        var nwlimit = $('.nwlist').attr('data-limit');
        var son = $('.nwlist').attr('data-son');
        if (son >= 5) {
           $('.ft').css("margin-top","50px");
        } else {
            $('.ft').css("margin-top","300px");
        }
        if (nwnum) {
            $('.list-rank').each(function(i,v){
                if ($.inArray(parseInt($(v).attr('data-id')), nwnum) == -1) {
                    $(v).find('div:eq(2) a').css('color', 'red')
                    $(v).find('div:eq(3) a').attr('href', '#')
                    $(v).find('div:eq(3) a').click(function(){
                        selectError('您的会员组身份已到期，是否升级会员组获取更多权限');
                    })
                    $(v).find('.addkeyword').attr('isred', 1)
                } 
            }) 
        }
        var xu = (~~(nwpage ? nwpage : 1) - 1) * nwlimit
        $('.xulie').each(function (i, v) {
            $(v).text(~~i + 1 + xu)
            
        })

        if ($('#etval').val() == 0 || $('#etval').val() == 2) {
            $('#laval option:eq(2)').css('display', 'none')
        }
        $('#etval').change(function () {
            $('#laval option:first()').prop('selected', 'selected')
            if ($(this).val() == 0 || $(this).val() == 2) {
                $('#laval option:eq(2)').css('display', 'none')
            } else {
                $('#laval option:eq(2)').css('display', 'block')
            }
        })

        $(".okbtn2").click(function () {
            $('.myDlg2').css('display', 'none')
            var enginetype = $("#etval").val();
            var laval = $("#laval").val();
            var keywords = $("#keywords").val();
            var dmid = $(this).attr('data-id')
            var weburl = $(this).attr('data-val')
            var isred = $('.addkeyword').attr('isred')
            if (enginetype == 0) {
                $.sendWarning('请选择搜索引擎', 1000);
                return false;
            }
            if (laval == 0) {
                $.sendWarning('请选择终端类型', 1000);
                return false;
            }
            if (keywords == '') {
                $.sendWarning('请添加关键字', 1000);
                return false;
            }
            if (keywords.indexOf('\n') > -1) {
                if (keywords == 0) {
                    $.sendWarning('请添加关键字', 1000);
                    return false;
                }
            }
            $.post("/home/nwmonitor/addnwkey", {
                dmwebid: dmid,
                enginetype: enginetype,
                platform: laval,
                dmkeywords: keywords,
                weburl: weburl
            }, function (data) {
                if (data.code == 0) {
                    $.sendError(data.msg, 2000);
                    $('#etval').val('0');
                    $('#laval').val('0');
                    $('#keywords').val('');
                } else if(data.code == 1) {
                    $.sendSuccess(data.msg, 1000, function () {
                        if (!isred) {
                            if(data.info.ety ==1 && data.info.pty ==1){
                                var search = 'baidupc';
                            }else if(data.info.ety ==1 && data.info.pty ==2){
                                var search = 'baidum';
                            }else if(data.info.ety ==2 && data.info.pty ==1){
                                var search = 'haosou';
                            }else if(data.info.ety ==3 && data.info.pty ==1){
                                var search = 'sogoupc';
                            }else if(data.info.ety ==3 && data.info.pty ==2){
                                var search = 'sogoum';
                            }
                            document.location.href = '/weblist/'+search+'/'+dmid;
                        } else {
                            location.reload()
                        }
                    })
                } else {
                    $.sendConfirm({
                        hideHeader: true,
                        withCenter: true,
                        msg: data.msg,
                        button: {
                          confirm: '确认',
                          cancel: '取消'
                        },
                        onConfirm: function () {
                          location.href = "/authlist"
                        },
                        onCancel: function () {
                            document.location.reload();
                        },
                        onClose: function () {
                           document.location.reload();
                        }
                    });
                }
            });
        });
        var navtype = $('.nwlist').attr('data-nav');
        // 分页
        $('.pagination a').each(function (i, v) {
            var aval = $(v).attr('href').substr($(v).attr('href').lastIndexOf('=')+1);
            var url = $(v).attr('href').substr(0,$(v).attr('href').indexOf('?'));
            if (!$(v).hasClass('cur')) {
                $(v).attr('href', url)
            }
            $(v).click(function(){
                if (navtype) {
                    $('#monForm input[name=navtype]').val(navtype)
                }
                $('#monForm input[name=page]').val(aval)
                $('#monForm').attr('action',url);
                $('#monForm').submit();
                return false;
            })
            
        })
        // $(".ftt span:last").remove();
        $("#btnsearch1").click(function () {
            var searchname = $.trim($("#searchname").val());
            var urltype = $("#urltype").val();
            if (searchname == '') {
                $.sendWarning('请输入网址', 1000);
                return false;
            }
            if (!checkjkurl(searchname)) {
                $.sendError('网站地址格式错误', 1000);
                return false;
            }
            $('.tabs form').submit();
        });

        $(".guan").click(function () {
            document.location.reload();
        })
        //点击添加网络监控
        $(".add").click(function () {
            $("#webval2").val('');
            $("#webval2").css("border", " solid 1px #ddd");
            $("#webval2").removeAttr("readonly", "readonly");
            $(".myMask").css({
                'display': 'block'
            });
            center($('.myDlg'));
            check($('.okbtn'), $('.close,.guan'));
            $('.myDlg .title').text('添加监控网站');
            $('#btnsearch2').attr('data-id','');
        });
        //批量添加网络监控
        $(".addpl").click(function () {
            $(".myMask").css({
                'display': 'block'
            });
            center($('.myDlgpl'));
            check($('#pladdurl'), $('.close,.guan'));
            $('.myDlgpl .title').text('批量添加监控网站');

        });
        //点击添加词
        $(document).on("click", ".addkeyword", function () {
            $(".myMask").css({
                'display': 'block'
            });
            center($('.myDlg2'));
            check($('.okbtn2'), $('.close,.guan'));
            $('#wzdz').val($(this).attr('data-val'))
            $('.okbtn2').attr('data-id', $(this).attr('data-id'))
            $('.okbtn2').attr('data-val', $(this).attr('data-val'))
        });

        //点击修改网站名称
        $(document).on("click", ".updateweb", function () {
            // alert(111);return false;
            $('#btnsearch2').attr('data-id', $(this).attr('data-id'));
            //获取网址
            var webval = $(this).attr('data-val');
            $("#webval2").css("border", "0px");
            $("#webval2").val(webval);

            $("#webval2").attr("readonly", "readonly");
            $('.myDlg .title').text('修改网站名称');

            $(".myMask").css({
                'display': 'block'
            });
            center($('.myDlg'));
            check($('.okbtn'), $('.close,.guan'));
        });
        // var nwtype = $('.nwtype').val();
        //选项卡切换
        $(".link li").removeClass('active')
        if (navtype) {
            if (navtype =='baidupc') {
                var index = 1;
            }else if(navtype =='baidum'){
                var index = 2;
            }else if(navtype =='haosou'){
                var index = 3;
            }else if(navtype =='sogoupc'){
                var index = 4;
            }else if(navtype =='sogoum'){
                var index = 5;
            }
        }else{
            var index = 0;
        }
        $(".link li:eq(" + index + ")").addClass('active')

        // 执行单个添加
        $("#btnsearch2").click(function () {
            var dmId = $(this).attr('data-id');//获取数据id
            var webname = $("#webname").val();//网站名称
            var weburl = $("#webval2").val();//网站地址
            if (dmId) {
                if (webname == '') {
                    $.sendWarning('请输入网站名称', 1000);
                    return false;
                }
                if (webname.length > 8) {
                    $.sendWarning('网站名称字数超过限制', 1000);
                    $("#webname").val('')
                    return false;
                }
                //如果存在执行修改
                $.post("/home/nwmonitor/addeditlist", {id: dmId, webname: webname, weburl: weburl}, function (data) {
                    if (data.code == 1) {
                        $.sendSuccess(data.msg, 1000, function () {
                            document.location.reload();
                        })
                    } else if(data.code == 0) {
                        $.sendError(data.msg, 1000);
                    }
                });
            } else {
                if (webname == '') {
                    $.sendWarning('请输入网站名称', 1000);
                    return false;
                }
                if (webname.length > 8) {
                    $.sendWarning('网站名称字数超过限制', 1000);
                    $("#webname").val('')
                    return false;
                }
                if (weburl == '') {
                    $.sendWarning('请输入网站地址', 1000);
                    return false;
                }
                if (!checkjkurl(weburl)) {
                    $.sendError('网站地址格式错误，请输入正确的网址格式', 2000);
                    return false;
                }
                //否则执行添加
                $.post("/home/nwmonitor/addeditlist", {id: 0, webname: webname, weburl: weburl}, function (data) {
                    if (data.code == 1) {
                        $.sendSuccess(data.msg, 1000, function () {
                            document.location.reload();
                        })
                    } else if(data.code == 0){
                        $.sendError(data.msg, 1000);
                        $("#webname").val('');
                        return false;
                    } else {
                        selectError('当前会员组添加监控网址已达上限，是否升级会员组获取更多权限');
                    }
                });
            }
        });
        //执行批量添加
        $("#pladdurl").click(function () {
            $('.myDlgpl').css('display', 'none')
            var webplurl = $("#plurl").val();//网站地址
            if (webplurl == '') {
                $.sendWarning('请输入网站地址', 1000);
                return false;
            }
            if (webplurl.indexOf('\n') >= -1) {
                if (webplurl == 0) {
                    $.sendWarning('请输入网站地址', 1000);
                    return false;
                }
                $.post("/home/nwmonitor/addurlpl", {weburl: webplurl}, function (data) {
                    if (data.code == 1) {
                        $.sendSuccess(data.msg, 1000, function () {
                            document.location.reload();
                        })
                    } else if(data.code == 0){
                        $.sendError(data.msg, 1000);
                        return false;
                    } else{
                        selectError(data.msg);
                    }
                });
            }
        });

    });