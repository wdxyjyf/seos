vaptcha({
    vid: '5e65a297f9f92166464af28a', // 验证单元id
    type: 'click', // 显示类型 点击式
    scene: 0, // 场景值 默认0
    container: '#vaptchaContainer', // 容器，可为Element 或者 selector
    offline_server: '/home/login/vaptcha' //离线模式服务端地址

    //可选参数
    //lang: 'zh-CN', // 语言 默认zh-CN,可选值zh-CN,en,zh-TW
    //https: true, // 使用https 默认 true
    //style: 'dark' //按钮样式 默认dark，可选值 dark,light
    //color: '#57ABFF' //按钮颜色 默认值#57ABFF
}).then(function (vaptchaObj) {
    obj = vaptchaObj;//将VAPTCHA验证实例保存到局部变量中
    vaptchaObj.render()// 调用验证实例 vpObj 的 render 方法加载验证按钮

    //获取token的方式一：
    //vaptchaObj.renderTokenInput('.login-form')//以form的方式提交数据时，使用此函数向表单添加token值

    //获取token的方式二：
    vaptchaObj.listen('pass', function() {
    // 验证成功进行后续操作
    var data = {
      //表单数据
      token: vaptchaObj.getToken()
    }
    $.post('login',data, function(r) {
      if(r.code !== 200) {
        console.log('登录失败');
        vaptchaObj.reset(); //重置验证码
      }
    })
  })

  //关闭验证弹窗时触发
  vaptchaObj.listen('close', function() {
  //验证弹窗关闭触发
  })
})