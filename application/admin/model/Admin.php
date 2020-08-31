<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Admin extends Model
{
    protected $pk = 'admin_id';
    public function login($data,$code){
        if($code=='open'){
            if(!$this->check($data['vercode'])){
                return ['code' => 0, 'msg' => '验证码错误'];
            }
        }
        // 后台登陆区域限制
        $ip = $_SERVER['REMOTE_ADDR'];
        if ($ip != '127.0.0.1') {
            $areaInfo = getInfoByIp($ip);
            $province = $areaInfo['province'];
            $city = str_replace('市', '', $areaInfo['city']);
            if (!db('system')->where('permission', 'like', "%{$province}%")->find() && !db('system')->where('permission', 'like', "%{$city}%")->find()) {
                return ['code' => 0, 'msg' => '登陆错误'];
            }
        }

        $user=Db::name('admin')->where('username',$data['username'])->find();
        if($user) {
            if ($user['is_open']!==1){
                return ['code' => 0, 'msg' => '该账号被禁用,请联系超管'];
            }else{
                if($user['pwd'] == md5($data['password'])){
                    session('username', $user['username']);
                    session('aid', $user['admin_id']);
                    $avatar = $user['avatar'] == '' ? '/static/admin/images/0.jpg' : $user['avatar'];
                    session('avatar', $avatar);
                    return ['code' => 1, 'msg' => '登录成功!']; //信息正确
                }else{
                    return ['code' => 0, 'msg' => '用户名或者密码错误，重新输入!']; //密码错误
                }
            }
        }else{
            return ['code' => 0, 'msg' => '用户不存在!']; //用户不存在
        }
    }
    public function getInfo($admin_id){
        $info = Db::name('admin')->field('pwd',true)->find($admin_id);
        return $info;
    }
    public function check($code){
        return captcha_check($code);
    }

}

