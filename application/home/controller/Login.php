<?php
namespace app\home\controller;
use think\Controller;
use app\home\model\Users;
use clt\Lunar;
use think\facade\Request;
use app\home\controller\Weixin;
use think\Db;

class Login extends Common{
    public function initialize(){
        parent::initialize();
    }
    //注册页
    public function register(){
        if(session('usersmobile')){
            $this->redirect('/');
            return false;
        }
        $data = input('get.uid');
        if($data){
            $jmcode = substr(base64_decode($data),7);
            if($jmcode){
                $yqcode = $jmcode;
                session('yqcode',$yqcode);
                return $this->redirect('/');
            }else{
                $this->_empty();
            }
        }else{
            if(session('yqcode')){
                $yqcode = session('yqcode');
            }else{
                $yqcode = 0;
            }
            $this->assign('userid',$yqcode); 
            $system = cache('System');
            $headtitle = '注册账号_'.$system['name'];
            $this->assign('headtitle',  $headtitle); 
            $Keyword = '搜一搜站长工具 用户注册';
            $Desc = '搜一搜站长工具用户注册';
            $this->assign('Keyword', $Keyword);
            $this->assign('Desc',  $Desc);
            $this->assign('CSS',['/static/home2/css/login.css', '/static/home2/css/vaptcha.css']);
            $this->assign('JS',['/static/home2/js/login/register.js', '/static/home2/js/vaptcha/v3.js']);      
            return $this->fetch();  
        }
    }

     //注册时发送验证码前判断是否注册过
    public function beforePhoneCode(){
        if(request()->isPost()){
            $mobile = trim(input('post.mobile'));//接受手机号
            $user=Db::name('users')->where('mobile',$mobile)->value('id');
            if (!$user) {
                return ['code' => 1];
            } else{
                return ['code' => 0,'msg' => '该用户已注册'];
            }
        }else{
            return ['code' => 0, 'msg' => '请求方式不合法'];
        }  
    }
    //发送短信验证码
    public function sendPhoneCode(){
        if(request()->isPost()){
            $data = input('post.');
            if (!is_mobile_phone(trim($data['mobile']))) {
                return false;
            }
            $nums = [1,2];
            if(!in_array($data['type'],$nums)) {
                return false;
            }
            $return = Users::sendCode($data);
            return ['code' => $return['code'], 'msg' => $return['msg']];
        }else{
            return ['code' => 0, 'msg' => '请求方式不合法'];
        }  
    }
    //执行注册
    public function doRegister(){
        if(request()->isPost()){
            $data = input('post.');
            $username = session('wxinfo');
            if (!empty($username)) {
                $data['openid'] = $username['openid'];
                $data['sex'] = $username['sex']?:0;
                $data['headimgurl'] = $username['headimgurl'];
                $data['unionid'] = $username['unionid'];
                $data['province'] = $username['province'];
                $data['city'] = $username['city'];
                $data['country'] = $username['country'];
                $data['language'] = $username['language'];
                $data['nickname'] = $username['nickname'];
            }
            $return = Users::register($data);
            return ['code' => $return['code'], 'msg' => $return['msg']];
        }else{
            return ['code' => 0, 'msg' => '请求方式不合法'];
        }  
    }
    //登录页
    public function login(){
        if(session('usersmobile')){
            $this->redirect('/');
            return false;
        }
        $system = cache('System');
        $headtitle = '用户登录_'.$system['name'];
        $this->assign('headtitle',  $headtitle);   
        $Keyword = '搜一搜站长工具 用户登录';
        $Desc = '搜一搜站长工具用户登录';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);
        $this->assign('CSS',['/static/home2/css/login.css']);
        $this->assign('JS',['/static/home2/js/login/login.js','https://res.wx.qq.com/connect/zh_CN/htmledition/js/wxLogin.js']);     
        return $this->fetch();
    }
    //执行登录
    public function doLogin(){
        if(request()->isPost()){
            $data = input('post.');
            $return = Users::login($data);
            return ['code' => $return['code'], 'msg' => $return['msg']];
        }else{
            return ['code' => 0, 'msg' => '请求方式不合法'];
        }  
    }
    //退出登录
    public function logout(){
        //清空session
        session('usersmobile',null);
        session('usersid',null);
        session('userinfo',null);
        session('session_start_time',null);
        $this->redirect('/login');
    }
    //查询用户是否存在
    public function findUser(){
        if(request()->isPost()){
            $data = input('post.');
            $return = Users::isUser($data);
            return ['code' => $return['code'], 'msg' => $return['msg']];
        }else{
            return ['code' => 0, 'msg' => '请求方式不合法'];
        }  
    }
    //找回密码
    public function forgotpass(){
        if(request()->isPost()){
            $data = input('post.');
            $return = Users::findPass($data);
            return ['code' => $return['code'], 'msg' => $return['msg']];
        }else{
            $system = cache('System');
            $headtitle = '找回密码_'.$system['name'];
            $this->assign('headtitle',  $headtitle); 
            $Keyword = '搜一搜站长工具 用户找回密码';
            $Desc = '搜一搜站长工具用户找回密码';
            $this->assign('Keyword', $Keyword);
            $this->assign('Desc',  $Desc);
            $this->assign('CSS',['/static/home2/css/login.css', '/static/home2/css/vaptcha.css']);
            $this->assign('JS',['/static/home2/js/login/loginfpass.js', '/static/home2/js/vaptcha/v3.js']);          
            return $this->fetch();
        }  
    }
    //微信登录页面
    public function wxlogin(){
        return $this->fetch();
    }

    //扫描二维码获取微信用户信息
    public function wxcodeLogin(){
        $wxinfo = input('post.info');
        if ($wxinfo) {
            if($wxinfo['subscribe'] == 1){
                $user = db('users')->where('unionid',$wxinfo['unionid'])->find();
                if (!$user) {
                    session('wxinfo',$wxinfo);
                    return ['code'=>2,'url'=>'/wxregister'];
                } else {
                    $user['headimgurl'] = download_remote_pic($wxinfo['headimgurl'],$wxinfo['openid']);
                    Db::name('users')->update($user);
                    session('usersmobile', $user['mobile']);
                    session('usersid', $user['id']);
                    session('userinfo', $user);
                    return ['code'=>1];
                }
            } else{
                return ['code'=>0];
            }
        }
    }

    // //微信绑定手机页面
    public function wxregister(){
        $system = cache('System');
        $headtitle = '微信绑定手机_'.$system['name'];
        $this->assign('headtitle',  $headtitle); 
        $Keyword = '搜一搜站长工具 用户微信绑定手机';
        $Desc = '搜一搜站长工具用户微信绑定手机';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);
        $this->assign('CSS',['/static/home2/css/login.css']);
        $this->assign('JS',['/static/home2/js/login/wxregister.js']);
        return $this->fetch();
    }

    //微信执行绑定
    public function bindMobile(){
        $data = input('post.');
        $user=Db::name('users')->where('mobile',$data['mobile'])->find();
        if($user) {
            if ($user['is_lock']!==1){
                return ['code' => 0, 'msg' => '该账号被禁用'];
            }else{
                if($user['password'] == md5($data['password'])){
                    $wxinfo = session('wxinfo');
                    $user['openid'] = $wxinfo['openid'];
                    $user['username'] = $wxinfo['nickname'];
                    $user['headimgurl'] = download_remote_pic($wxinfo['headimgurl'],$wxinfo['openid']);
                    $user['sex'] = $wxinfo['sex'];
                    $user['unionid'] = $wxinfo['unionid'];
                    $user['province'] = $wxinfo['province'];
                    $user['city'] = $wxinfo['city'];
                    $user['country'] = $wxinfo['country'];
                    $user['language'] = $wxinfo['language'];
                    $res = db('users')->update($user);
                    if ($res !== false) {
                        session('wxinfo',null);
                    }
                    session('usersmobile', $user['mobile']);
                    session('usersid', $user['id']);
                    session('userinfo', $user);
                    session('session_start_time',time());
                    $yqurl = config('url.pact').'://'.config('url.host').'/register?uid='.base64_encode('njboseo'.$user['id']);
                    session('yqurl', $yqurl);
                    return ['code' => 1, 'msg' => '绑定成功!']; //信息正确
                }else{
                    return ['code' => 0, 'msg' => '用户名或者密码错误，重新输入!']; //密码错误
                }
            }
        }else{
            return ['code' => 0, 'msg' => '用户不存在!']; //用户不存在
        }
    }

    public function vaptcha_check(){
        $token = input('post.token');
        if ($token) {
            session('vaptcha_register_token', $token);
            return $this->success('验证通过', '', ['token'=>$token]);
        } else {
            return $this->error('验证失败');
        }
    }
}