$(function () {
    $('.link li').click(function(){
        $('.link li').removeClass('active')
        $(this).addClass('active')
        return false
    })
    $('#btnsearch1').click(function(){
        $.sendWarning('没有登录,请立即登录', 1000);
        return false;
    })
});