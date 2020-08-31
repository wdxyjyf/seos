<?php
 
namespace app\home\controller;
use think\Controller;
use think\facade\Session;
 
class Wxfollow extends Common{
 
    private $appId;
    private $appSecret;
 
    public function initialize(){
        parent::initialize();
        $this->appId = 'wxdf850d0b6a22a668';  
        $this->appSecret= '6163fa3e2863831e676b9f9d055a4015'; 
    }
     
    // 获取access_token
    public function reindex(){
        $appid  = 'wxdf850d0b6a22a668';
        $secret = '6163fa3e2863831e676b9f9d055a4015';
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
        $res = $this->curl_post($url);
        if ($res['access_token']) {
            Session::set('wxToken',$res['access_token']);
            Session::set('wxtokenTime',time());
        }
        return $res['access_token'];
    }
    
    //前端展示二维码
    public function wechatcode(){
      $rand = mt_rand(1,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
      $access_token = $this->reindex();
      $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
      $list = array(
          'action_name'  => 'QR_SCENE',
          'action_info' =>  array(
              'scene' => array(
                  'scene_id'  => $rand,
              ),
          ),    
      );
      // 获取ticket
      $res = $this->curl_post($url,json_encode($list),'POST');
      $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($res['ticket']);
      return ['code'=>1,'url'=> $url,'timetamp'=>'qrscene_'.$rand];
    }

    // //通过ticket换取二维码
    // public function wechatcode($ticket)
    // {
    //     $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
    //     return $this->getjson($url);
    // }

    public function wxrandcode(){
      $timemap = input('post.rand');
      $res =  $this->redis->rpop('wxinfo:'.$timemap);
      return ['info'=>json_decode($res, 1)];
    }

    // 
    public function curl_post($url, $data=null,$method='GET', $https=true)
    {
       // 创建一个新cURL资源 
       $ch = curl_init();   
       // 设置URL和相应的选项 
       curl_setopt($ch, CURLOPT_URL, $url);  
       //要访问的网站 //启用时会将头文件的信息作为数据流输出。
       curl_setopt($ch, CURLOPT_HEADER, false);   
       //将curl_exec()获取的信息以字符串返回，而不是直接输出。 
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     
       
       if($https){ 
          
           //FALSE 禁止 cURL 验证对等证书（peer's certificate）。 
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
           curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
 
           //验证主机 } 
           if($method == 'POST'){ 
               curl_setopt($ch, CURLOPT_POST, true); 
               
               //发送 POST 请求  //全部数据使用HTTP协议中的 "POST" 操作来发送。 
               curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
            }    
                // 抓取URL并把它传递给浏览器 
                $content = curl_exec($ch);   
                //关闭cURL资源，并且释放系统资源 
                curl_close($ch);   
             
                return json_decode($content,true);
 
         }
    }
 
    public function getjson($url,$data=null)
    {
        $curl = curl_init();
        
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_HEADER,false);   
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
 
        //不为空，使用post传参数，否则使用get
        if($data){
 
            curl_setopt($curl,CURLOPT_POST,1);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
 
        }
 
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        
        $output = curl_exec($curl);
        curl_close($curl);
 
        return $output;
 
    }
    
}