<?php
namespace app\home\model;
use think\Model;
use think\Db;
use think\facade\Session;
class Users extends Model
{
    protected $pk = 'id';
    protected $name = 'users';

    //发送阿里短信验证码
    public function sendCode($data){
        if ($data['register_token']) {
            if (session('vaptcha_register_token') != $data['register_token']) {
                return ['code' => 0, 'msg' => '请求方式不合法'];
            }
        }
        if ($data['findpass_token']) {
            if (session('vaptcha_register_token') != $data['findpass_token']) {
                return ['code' => 0, 'msg' => '请求方式不合法'];
            } 
        }
 
        $code = rand_string(4,1);
        $mobile = $data['mobile'];
        if($data['type'] == 1){//注册用户
            #注册时发送验证码前判断是否注册过
            if (db('users')->where('mobile', $mobile)->find()) {
                return ['code' => 0, 'msg' => '该用户已注册'];
            }
            $res = smsVerify($mobile, $code, 'SMS_69790149');
        }elseif($data['type'] == 2){//找回密码
            #找回密码前判断是否有该用户
            if($user = Db::name('users')->where('mobile',$data['mobile'])->find()){
                if ($user['is_lock']!==1){
                    return ['code' => 0, 'msg' => '该账号被禁用'];
                }
            }else{
                return ['code' => 0, 'msg' => '用户不存在!']; 
            }
            $res = smsVerify($mobile, $code, 'SMS_69935104');
        }
        if($res['status'] == 1){
            $time = time();
            if($data['type'] == 1){//注册用户
                Session::set($mobile.'registercode',$code);
                Session::set($mobile.'registercodetime',$time);
            }else{//找回密码
                Session::set($mobile.'findpasscode',$code);
                Session::set($mobile.'findpasscodetime',$time);
            }
            session('register_token', null);
            session('findpass_token', null);
            return ['code' => 1, 'msg' => '验证码已发送,5分钟内有效'];
        }else{
            return ['code' => 0, 'msg' => '验证码发送失败'];
        }
    }

    //通过qqstmp发送邮箱验证码
    public function sendQqCode($data){
        $code = rand_string(4,1);
        $time = time();
        $arr = Db::name('config')->where('inc_type','smtp')->select();
        $config = convert_arr_kv($arr,'name','value');
        $content = $config['test_eamil_info'].$code;
        $send = send_email($data, 'SEO站长平台邮箱验证',$content);
        if ($send) {
            session('emailcode',$code);
            session('emailcodetime', $time);
            return ['code' => 1, 'msg' => '邮件发送成功,5分钟内有效！'];
        } else {
            return ['code' => 0, 'msg' => '邮件发送失败！'];
        }
    }

    //判断用户登录
    public function login($data){
        $user=Db::name('users')->where('mobile',$data['mobile'])->find();
        if($user) {
            if ($user['is_lock']!==1){
                return ['code' => 0, 'msg' => '该账号被禁用'];
            }else{
                if($user['password'] == md5($data['password'])){
                    if ($user['level'] != 1) {
                        if (time()- $user['endtime'] > 0) {
                            $user['level'] = 1;
                            Db::name('users')->where('id', $user['id'])->update($user);
                            session('userinfo', $user);
                        }
                    }
                    session('usersmobile', $user['mobile']);
                    session('usersid', $user['id']);
                    session('userinfo', $user);
                    $yqurl = config('url.pact').'://'.config('url.host').'/register?uid='.base64_encode('njboseo'.$user['id']);
                    session('yqurl', $yqurl);
                    return ['code' => 1, 'msg' => '登录成功!']; //信息正确
                }else{
                    return ['code' => 0, 'msg' => '用户名或者密码错误，重新输入!']; //密码错误
                }
            }
        }else{
            return ['code' => 0, 'msg' => '用户不存在!']; //用户不存在
        }
    }

    //判断用户注册
    public function register($data){
        $user = Db::name('users')->where('mobile',$data['mobile'])->find();
        if(empty($user)) {
            if(is_mobile_phone($data['mobile']) === false){
                return ['code' => 0, 'msg' => '手机格式不正确'];
            }
            if(is_password($data['password']) === false){
                return ['code' => 0, 'msg' => '密码为6-20字母数字下划线组合'];
            }
            $codetime = Session::get($data['mobile'].'registercodetime');
            $code = Session::get($data['mobile'].'registercode');
            if(time() - $codetime > 300){
                return ['code' => 0, 'msg' => '验证码已过期'];
            }else{
                if($data['code'] !== $code){
                    return ['code' => 0, 'msg' => '验证码错误'];
                }
            }
            // 生成唯一token
            $users['token'] = 'sys'.substr(md5($data['mobile'].time()), 15);
            
            if($data['yaoqingid'] == 0){
                $users['yaoqing_id'] = 0;
                $users['mobile'] = $data['mobile'];
                $users['password'] = md5($data['password']);
                $users['reg_time'] = time();
                $users['level'] = 1;
                $users['group'] = 1;
                $users['openid'] = $data['openid'];
                $users['sex'] = $data['sex']?:0;
                $users['headimgurl'] = $data['headimgurl'];
                $users['unionid'] = $data['unionid'];
                $users['province'] = $data['province'];
                $users['city'] = $data['city'];
                $users['country'] = $data['country'];
                $users['language'] = $data['language'];
                $users['username'] = $data['nickname'];
                $userId = Db::name('users')->insertGetId($users);
            }else{
                $users['yaoqing_id'] = $data['yaoqingid'];
                $users['mobile'] = $data['mobile'];
                $users['password'] = md5($data['password']);
                $users['reg_time'] = time();
                $users['level'] = 1;
                $users['group'] = 1;
                $userId = Db::name('users')->insertGetId($users);
                if($userId !== false){
                    $userinfo = Db::name('users')->where('id',$data['yaoqingid'])->find();
                    $count = Db::name('users')->where('yaoqing_id',$data['yaoqingid'])->count();
                    if ($count <= 50) {
                        if ($userinfo['level'] > 1) {
                            $userinfo['endtime'] += 86400;
                        } else {
                            $userinfo['level'] = 2;
                            $userinfo['opentime'] = time();
                            $userinfo['endtime'] = time()+86400;
                        }
                        db('users')->update($userinfo);
                    }
                    session('yqcode',null);
                }
            }
            if ($userId) {
                session('wxinfo', null);
                $user=Db::name('users')->where('id',$userId)->find(); 
                session('userinfo', $user);
                $num = ['userid' => $userId];
                Db::name('users_num')->insert($num);
                session('usersmobile', $data['mobile']);
                session('usersid', $userId);
                Session::delete($data['mobile'].'registercodetime');
                Session::delete($data['mobile'].'registercode');
                return ['code' => 1, 'msg' => '注册成功!']; 
            } else {
                return ['code' => 0, 'msg' => '注册失败!']; 
            }
        }else{
            return ['code' => 0, 'msg' => '该用户已存在!']; //用户已存在
        }
    }

    //执行用户修改密码
    public function updatePass($data){
        $user=Db::name('users')->where('id',$data['id'])->find();
        if($user) {
            if($user['password'] == md5($data['oldpass'])){
                if($data['newpass'] == $data['confirmpass']){
                    $user['password'] = md5($data['newpass']);
                    db('users')->update($user);
                    session('usersmobile',null);
                    session('usersid',null);
                    session('userinfo', null);
                    session('yqurl', null);
                    return ['code' => 1, 'msg' => '密码修改成功','url'=>url('/home/login/login')]; //信息正确
                }else{
                    return ['code' => 0, 'msg' => '新密码再次确认错误']; //密码错误
                }
            }else{
                return ['code' => 0, 'msg' => '旧密码输入错误']; //密码错误
            }
        }else{
            return ['code' => 0, 'msg' => '用户不存在!']; //用户不存在
        }
    }
    //判断用户是否存在
    public function isUser($data){
        $user=Db::name('users')->where('mobile',$data['mobile'])->find();
        if($user){
            if ($user['is_lock']!==1){
                return ['code' => 0, 'msg' => '该账号被禁用'];
            }else{
                return ['code' => 1, 'msg' => '请求成功'];
            }
        }else{
            return ['code' => 0, 'msg' => '用户不存在!']; 
        }
    }
    //执行用户找回密码
    public function findPass($data){
        $user = Db::name('users')->where('mobile',$data['mobile'])->find();
        if($user) {
            if(is_password($data['password']) === false){
                return ['code' => 0, 'msg' => '密码为6-20字母数字下划线组合'];
            }
            $codetime = Session::get($data['mobile'].'findpasscodetime');
            $code = Session::get($data['mobile'].'findpasscode');
            if(time() - $codetime > 300){
                return ['code' => 0, 'msg' => '验证码已过期'];
            }else{
                if($data['code'] !== $code){
                    return ['code' => 0, 'msg' => '验证码错误'];
                }
            }
            $user['password'] = md5($data['password']);
            db('users')->update($user);
            Session::delete($data['mobile'].'findpasscodetime');
            Session::delete($data['mobile'].'findpasscode');
            return ['code' => 1, 'msg' => '密码修改成功'];
        }else{
            return ['code' => 0, 'msg' => '用户不存在!']; //用户不存在
        }
    }

    //获取用户邀请好友列表
    public function yqUser($uid){
        $user = Db::name('users')->where('yaoqing_id',$uid)->order('reg_time desc')->limit(5)->select();
        if(!empty($user)){
            foreach ($user as $key => $value) {
                $userlist[$key]['reg_time'] = date('Y-m-d',$value['reg_time']);
                $userlist[$key]['mobile'] = substr_replace($value['mobile'],'****',3,4);
            }
        }else{
            $userlist = [];
            
        }
        return $userlist; 
    }

    //获取用户基本资料
    public function userInfo(){
        $usersid = session('usersid');
        if($usersid){
            //用户信息
            $userList = Db::name('users')->alias('u')
                ->join('users_group ug','u.level = ug.id','left')
                ->field('u.mobile,u.email,u.point,u.reg_time,u.money,u.avatar,u.headimgurl,u.username,u.level,u.opentime,u.endtime,ug.title,u.token')
                ->where('u.id', $usersid)
                ->find();
            if ($userList['opentime'] == "") {
                $userList['opentime'] = "暂未开通任何业务";
            } else {
                $userList['opentime'] = date('Y-m-d',$userList['opentime']);
            }
            if ($userList['endtime'] == "") {
                $userList['endtime'] = "永久免费使用";
            } else {
                if (time() - $userList['endtime'] > 0) {
                    $userList['endtime'] = date('Y-m-d',$userList['endtime']).' (已到期)';
                } else {
                    $userList['endtime'] = date('Y-m-d',$userList['endtime']);
                }
            }
            $userList['reg_time'] = date('Y-m-d',$userList['reg_time']);
        } else {
            $userList = [];
        }
        return $userList;
    }

    //获取用户基本信息
    public function userImg($uid){
        if($uid){
            //用户信息
            $userimg = Db::name('users')->alias('u')
                ->where('u.id', $uid)
                ->find();
            if (!$userimg) {
                $userimg = [];
            }
        } 
        return $userimg;
    }
    //根据用户id获取次数
    public function numInfo($userid) {
        if ($userid) {
            $numinfo = Db::name('users_num')->where('userid',$userid)->find();
        } else {
            $numinfo = [];
        }
        return $numinfo;
    }
    //根据用户组获取对应权限信息
    public function ruleInfo($level) {
        if ($level) {
            $ruleinfo = Db::name('users_rule')->where('groupid',$level)->find();
        } else {
            $ruleinfo = [];
        }
        return $ruleinfo;
    }
    //用户权限
    public function userRule($page='',$export='',$order=''){
        $level = session('userinfo.level');
        $ruleinfo = Db::name('users_rule')->where('groupid',$level)->find();
        if ($ruleinfo){
            // 展示条数
            if ($page > 0) {
                $keyword_digshow = $ruleinfo['keyword_shownum'];
                if ($page > $keyword_digshow / 20) {
                    $res = 0;
                } else {
                    $res = 1;
                }
            } 
            // 导出权限
            if ($export == 'baexport') {
                $beian_exportrule = $ruleinfo['beian_exportrule'];
                if ($beian_exportrule == 0) {
                    $res = 0;
                } else {
                    $res = 1;
                }
            } elseif($export == 'pmexport') {
                $rank_exportrule = $ruleinfo['rank_exportrule'];
                if ($rank_exportrule == 0) {
                    $res = 0;
                } else {
                    $res = 1;
                }
            }
            // 排序权限
            if ($order == 'baorder') {
                $order_beian = $ruleinfo['order_beian'];
                if ($order_beian == 0) {
                    $res = 0;
                } else {
                    $res = 1;
                }
            } elseif($order == 'pmorder') {
                $order_rank = $ruleinfo['order_rank'];
                if ($order_rank == 0) {
                    $res = 0;
                } else {
                    $res = 1;
                }
            } elseif($order == 'slorder') {
                $order_includ = $ruleinfo['order_includ'];
                if ($order_includ == 0) {
                    $res = 0;
                } else {
                    $res = 1;
                }
            } elseif($order == 'dgorder') {
                $order_keydig = $ruleinfo['order_keydig'];
                if ($order_keydig == 0) {
                    $res = 0;
                } else {
                    $res = 1;
                }
            } elseif($order == 'relorder') {
                $order_relate = $ruleinfo['order_relate'];
                if ($order_relate == 0) {
                    $res = 0;
                } else {
                    $res = 1;
                }
            } 
        }
        return $res;
    }

    // 增加查询次数
    public function addUsersNum($uid, $type){
        $res = Db::name('users_num')->where('userid', $uid)->setInc($type);
        return $res ? true : false;
    } 
}

