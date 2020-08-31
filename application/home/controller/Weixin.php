<?php
namespace app\home\controller;

use think\facade\Session;

//这是一个微信登录的处理类，用的是微信开放平台
class Weixin
{
    private $AppID = 'wxb4e5b5db6f2d4aba';//开发平台有
    private $AppSecret = '6ac0ec69442ba9ce3254912b429ab641';//开发平台也有
    private $Redirect_uri;//回调地址
    private $scope = 'snsapi_login';//这里不用动如果是微信登录

    public function __construct(){
        $this->Redirect_uri = config('url.pact').'://'.config('url.host').'/home/login/wechatLogin';
    }

    //前端请求这个接口，获取登录的url，这个url可以直接弹出带二维码的网页
    public function openWx() {
        return ['status' => 'success', 'url' => "https://open.weixin.qq.com/connect/qrconnect?appid=" . $this->AppID . "&redirect_uri=" . $this->Redirect_uri . "&response_type=code&scope=" . $this->scope . "&state=STATE#wechat_redirect"];
    }
    public function getUserAccessUserInfo($code) {
        if(empty($code)){
            $this->error('授权失败');
            // $baseUrl = request()->url(true);
            // $url = $this->getSingleAuthorizeUrl($baseUrl, "123");                
            // Header("Location: $url");
            // exit();
        }else{

            if(Session::get('accesstokenTime')){
                if (time() -  Session::get('accesstokenTime') >7200) {
                    $access_token = $this->getSingleAccessToken($code);
                } else {
                    $access_token = Session::get('accessToken');
                }
            } else {
                $access_token = $this->getSingleAccessToken($code);
            }
            return $this->getUserInfo($access_token);
        }
    }
    /**
     * 微信授权链接
     * @param  string $redirect_uri 要跳转的地址
     * @return [type]               授权链接
     */
    public function getSingleAuthorizeUrl($redirect_url = "",$state = '1') {
        $redirect_url = urlencode($redirect_url);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->AppID . "&redirect_uri=".$redirect_url."&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect"; 
    }

    /**
     * 获取token
     * @return [type] 返回token 
     */
    public function getSingleAccessToken($code) {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->AppID.'&secret='.$this->AppSecret.'&code='.$code.'&grant_type=authorization_code';    
        $access_token = $this->https_request($url);
        if ($access_token) {
            Session::set('accessToken',$access_token);
            Session::set('accesstokenTime',time());
        }
        return $access_token;     
    }
   
    /**
     * 发送curl请求
     * @param $url string
     * @param return array|mixed
     */
    public function https_request($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $AjaxReturn = curl_exec($curl);
        //获取access_token和openid,转换为数组
        $data = json_decode($AjaxReturn,true);
        curl_close($curl);
        return $data;
    }
     /**
     * @explain
     * 通过code获取用户openid以及用户的微信号信息
     * @return array|mixed
     * @remark
     * 获取到用户的openid之后可以判断用户是否有数据，可以直接跳过获取access_token,也可以继续获取access_token
     * access_token每日获取次数是有限制的，access_token有时间限制，可以存储到数据库7200s. 7200s后access_token失效
     **/
    public function getUserInfo($access_token = []) {
        if(!$access_token){
            return [
                'code' => 0,
                'msg' => '微信授权失败', 
            ];
        }
        $userinfo_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token['access_token'].'&openid='.$access_token['openid'].'&lang=zh_CN';
        $userinfo_json = $this->https_request($userinfo_url);
    
        //获取用户的基本信息，并将用户的唯一标识保存在session中
        if(!$userinfo_json){
            return [
                'code' => 0,
                'msg' => '获取用户信息失败！', 
            ];
        }
        return $userinfo_json;
    }
    //这里就是接收code还有state。用来做操作
//     public function getToken()
//     {
//         $code = $_GET['code'];
//         //判断是否授权
//         if (empty($code)){
//             $this->error('授权失败');
//         }
//         $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->AppID . '&secret=' . $this->AppSecret . '&code=' . $code . '&grant_type=authorization_code';
//         //获取token，为了获取access_token 如果没有就弹出错误
//         $token = json_decode(file_get_contents($token_url));

//         if (isset($token->errcode)) {
//             echo '<h1>错误：</h1>' . $token->errcode;
//             echo '<br/><h2>错误信息：</h2>' . $token->errmsg;
//             exit;
//         } else {
//             Session::set('accessToken',$token->access_token);
//             Session::set('accesstokenTime',time());
//         }
//         $access_token_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $this->AppID . '&grant_type=refresh_token&refresh_token=' . $token->refresh_token;
//         //获取access_token ，为了获取微信的个人信息，如果没有就弹出错误
//         $access_token = json_decode(file_get_contents($access_token_url));
//         if (isset($access_token->errcode)) {
//             echo '<h1>错误：</h1>' . $access_token->errcode;
//             echo '<br/><h2>错误信息：</h2>' . $access_token->errmsg;
//             exit;
//         }
//         $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token->access_token . '&openid=' . $access_token->openid . '&lang=zh_CN';
//     //获取用户信息
//         $user_info = json_decode(file_get_contents($user_info_url));
//         if (isset($user_info->errcode)) {
//             echo '<h1>错误：</h1>' . $user_info->errcode;
//             echo '<br/><h2>错误信息：</h2>' . $user_info->errmsg;
//             exit;
//         }
// //这里转换为数组
//         $rs = (array)$user_info;

//     //返回用户信息
//         return $rs;
}
