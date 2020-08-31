<?php
namespace app\api\controller;

use think\facade\Env;
use think\facade\Request;
use think\Controller;
use Db;

class Askuser extends Controller{
    
    // 用户信息登录
    public function asklogin(){
        if (Request::isPost()) {
            $data = Request::param();
            $url = 'https://www.soyiso.net/api/askuser/asklogin';
            if ($data['account'] == '' || $data['password'] == '') {
                returnApi(['code'=>0,'msg'=>'参数缺失']);
            } else {
                $app_secret = 'sys'.substr(md5($data['account']), 15);
            }

            if($app_secret && checkSign($url, $data, $app_secret)){
                $user = Db::name('users')->where('mobile',$data['account'])->find();
                if($user) {
                    if ($user['is_lock']!==1){
                        returnApi(['code'=>0,'msg'=>'该账号被禁用']);
                    }else{
                        if($user['password'] == md5($data['password'])){
                            returnApi(['code'=>1,'msg'=>'登录成功','userinfo'=>$user]);
                        }else{
                            returnApi(['code'=>0,'msg'=>'密码错误，重新输入!']);
                        }
                    }
                }else{
                    returnApi(['code'=>0,'msg'=>'用户不存在']);
                }
            } else {
                returnApi(['code'=>0,'msg'=>'验签签名错误']);
            }

        } else {
            returnApi(['code'=>0,'msg'=>'请求方式错误']);
        }
    }

    // 用户信息注册
    public function askregister(){
        if (Request::isPost()) {
            $data = Request::param();
            $url = 'https://www.soyiso.net/api/askuser/askregister';
            if ($data['mobile'] == '' || $data['password'] == '') {
                returnApi(['code'=>0,'msg'=>'参数缺失']);
            } else {
                $app_secret = 'sys'.substr(md5($data['mobile']), 15);
            }
            if($app_secret && checkSign($url, $data, $app_secret)){
                $user = Db::name('users')->where('mobile',$data['mobile'])->find();
                if(empty($user)) {
                    if(is_mobile_phone($data['mobile']) === false){
                        returnApi(['code'=>0,'msg'=>'手机格式不正确']);
                    }
                    // 生成唯一token
                    $users['token'] = 'sys'.substr(md5($data['mobile'].time()), 15);
                    $users['yaoqing_id'] = 0;
                    $users['mobile'] = $data['mobile'];
                    $users['password'] = md5($data['password']);
                    $users['reg_time'] = time();
                    $users['level'] = 1;
                    $users['group'] = 1;
                    $users['username'] = $data['mobile'];
                    $userId = Db::name('users')->insertGetId($users);
                    if ($userId) {
                        $user = Db::name('users')->where('id',$userId)->find(); 
                        returnApi(['code'=>1,'msg'=>'注册成功','userinfo'=>$user]);
                    } else {
                        returnApi(['code'=>0,'msg'=>'注册失败']);
                    }
                }else{
                    returnApi(['code'=>0,'msg'=>'该用户已存在']);
                }
            } else {
                returnApi(['code'=>0,'msg'=>'验签签名错误']);
            }
        } else {
            returnApi(['code'=>0,'msg'=>'请求方式错误']);
        }
    }
}