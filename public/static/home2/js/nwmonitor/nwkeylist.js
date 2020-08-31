
    $(function(){
        $('.pagination a').each(function(i,v){
            var aval = $(v).attr('href').substr($(v).attr('href').lastIndexOf('=')+1);
            var url = $(v).attr('href').substr(0,$(v).attr('href').indexOf('?'));
            if (!$(v).hasClass('cur')) {
                $(v).attr('href', url)
            }
            $(v).click(function(){
                $('#nwForm input[name=page]').val(aval)
                $('#nwForm').attr('action',url);
                $('#nwForm').submit();
                return false;
            })
        })

        $('.kw').click(function(){
            $('#rank_form').attr('action', $(this).attr('href'))
            $('#rank_form').find('#rank_id').val($(this).attr('data-id'))
            $('#rank_form').submit()
            return false
        })


        if($('#etval').val() == 0 || $('#etval').val() == 2) {
            $('#laval option:eq(2)').css('display', 'none')
        }
        $('#etval').change(function(){
            $('#laval option:first()').prop('selected', 'selected')
            if($(this).val() == 0 || $(this).val() == 2) {
                $('#laval option:eq(2)').css('display', 'none')
            } else {
                $('#laval option:eq(2)').css('display', 'block')
            }
        })
        $(".guan").click(function(){
            document.location.reload();
        });
        $(".add").click(function () {
            $(".myMask").css({
                'display': 'block'
            });
            center($('.myDlg'));
            check($('.okbtn'), $('.close,.guan'));
        });
        var getwebid = $('.getweb').val();
        $(".okbtn").click(function() {
            var enginetype = $("#etval").val();
            var laval = $("#laval").val();
            var keywords = $("#keywords").val();
            var dmid = $("#dmid").val();
            var weburl  = $('#weburl').val();
            if(enginetype == 0){
                $.sendWarning('请选择搜索引擎', 1000);
                return false;
            }
            if(laval == 0){
                $.sendWarning('请选择终端类型', 1000);
                return false;
            }
            if(keywords == ''){
                $.sendWarning('请添加关键字', 1000);
                return false;
            }
            if(keywords.indexOf('\n') > -1){
                if(keywords == 0){
                    $.sendWarning('请添加关键字', 1000);
                    return false;
                }
            }
            $.post("/home/nwmonitor/addnwkey", {dmwebid:dmid,enginetype:enginetype,platform:laval,dmkeywords:keywords,weburl:weburl}, function(data){
                if(data.code == 0){
                    $.sendError(data.msg, 2000);
                    return false;
                }else if(data.code == 1){
                    $.sendSuccess(data.msg, 1000, function(){
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
                        document.location.href = '/weblist/'+search+'/'+getwebid;
                    })
                }else {
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

        // 批量删除
        $('#zong').change(function(){
            $('.list-rank input[type=checkbox]').prop('checked', $(this).prop('checked'))
        })

        $('.dels').click(function(){
            var kwids = $('.list-rank input[type=checkbox]:checked').map(function(i,v){
                return $(v).val()
            }).get()
            if (kwids.length === 0) {
                $.sendWarning('请先选择要删除的关键词', 1000);
                return false
            }
            $.sendConfirm({
                hideHeader: true,
                withCenter: true,
                msg: '你确认要删除吗?',
                button: {
                  confirm: '确认',
                  cancel: '取消'
                },
                onConfirm: function() {
                    $.post("/home/nwmonitor/dels", {kwids:kwids}, function(data){
                        if(data.code == 1){
                            $.sendSuccess(data.msg, 1000, function(){
                                document.location.reload();
                            })
                            
                        }else{
                            $.sendError(data.msg, 1000);
                        }
                    });
                },
                onCancel: function() {
                    return false;
                },
                onClose: function() {
                    return false;
                }
            }); 
        })
    });
    function delkeylist(id){
        $.sendConfirm({
            hideHeader: true,
            withCenter: true,
            msg: '你确认要删除吗?',
            button: {
              confirm: '确认',
              cancel: '取消'
            },
            onConfirm: function() {
                $.post("/home/nwmonitor/delkeylist", {id:id}, function(data){
                    if(data.code == 1){
                        $.sendSuccess(data.msg, 1000, function(){
                            document.location.reload();
                        })
                        
                    }else{
                        $.sendError(data.msg, 1000);
                    }
                });
            },
            onCancel: function() {
                return false;
            },
            onClose: function() {
                return false;
            }
        }); 
    }
    
    