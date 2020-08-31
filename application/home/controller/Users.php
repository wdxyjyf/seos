<?php
namespace app\home\controller;
use clt\Lunar;
use think\facade\Env;
use app\home\model\Users as HomeUsers;
use think\facade\Request;
// use think\Db;
use pay\AlipaySubmit;
use Yansongda\Pay\Pay;

class Users extends Common{
    public function initialize(){
        parent::initialize();
    }

    //基本信息页面
    public function index(){
        if(empty(session('usersmobile'))){
            $this->redirect('/login');
            return false;
        }
        $userList = HomeUsers::userInfo();
        $this->assign('userList',$userList);
        $url = $this->yaoqingcode();
        $this->assign('yqurl',$url); 
        $system = cache('System');
        $headtitle = '个人中心 - '.$system['name'];
        $this->assign('headtitle',  $headtitle); 
        $Keyword = '个人中心';
        $Desc = '展示用户的基本信息,会员身份,用户权限';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/userinfo.css']);
        $this->assign('JS',['/static/home2/js/copy.js','/static/home2/js/copy/clipboard.min.js']);
        return $this->fetch();
    }
    //修改资料页面
    public function editinfo(){
        if(empty(session('usersmobile'))){
            $this->redirect('/login');
            return false;
        }
        $id = session('usersid');
        $list = HomeUsers::userInfo($id);
        $url = $this->yaoqingcode();
        $this->assign('yqurl',$url); 
        $this->assign('CSS',['/static/home2/css/userinfo.css','/static/admin/css/global.css','/static/common/css/font.css']);
        $this->assign('JS',['/static/common/js/angular.min.js','/static/home2/js/copy/clipboard.min.js','/static/home2/js/copy.js']);
        if(Request::isAjax()){
            $data = Request::except('file');
           
            $emailcode = $data['emailcode'];
            $user = db('users');
            $where2[] = ['id','<>',$data['id']];
            if($data['email']){
                $where2[] = ['email','=',$data['email']];
                $check_email = HomeUsers::where($where2)->find();
                if ($check_email) {
                    $result = ['code'=>0,'msg'=>'邮箱已存在'];
                }
            }
            if($emailcode != '') {
                $codetime = session('emailcodetime');
                $code = session('emailcode');
                if(time() - $codetime > 300){
                    $result = ['code' => 0, 'msg' => '验证码已过期'];
                }else{
                    if($emailcode !== $code){
                        $result = ['code' => 0, 'msg' => '验证码错误'];
                    }
                }
                unset($data['emailcode']);
                $data['email_validated'] = 1;
            } else {
                unset($data['emailcode']);
            }
            if ($user->update($data)!==false) {
                $result['msg'] = '资料修改成功!';
                $result['code'] = 1;
                session('emailcodetime',null);
                session('emailcode',null);
                session('userinfo',$data);
            } else {
                $result['msg'] = '资料修改失败!';
                $result['code'] = 0;
            }
            return $result;
        }else{
           
            $info = HomeUsers::get($id);
            $email_validated = $info['email_validated'];
            $this->assign('email_validated',  $email_validated); 
            $this->assign('info',json_encode($info,true));
            $system = cache('System');
            $headtitle = '个人中心 - 修改资料 - '.$system['name'];
            $this->assign('headtitle',  $headtitle); 

            $Keyword = '个人中心 - 修改资料';
            $Desc = '可以编辑用户的基本信息,会员头像,邮箱的验证';
            $this->assign('Keyword', $Keyword);
            $this->assign('Desc',  $Desc);  

            return $this->fetch();
        }
    }
    //权限与升级页面
    public function auth(){
        if(empty(session('usersmobile'))){
            $this->redirect('/login');
            return false;
        }
        $url = $this->yaoqingcode();
        $this->assign('yqurl',$url); 
        $list = db('users_group')->alias('ug')
                ->join('users_rule ur','ug.id = ur.groupid','left')
                ->field('ur.*,ug.title,ug.id')
                ->order('ug.id')
                ->limit(3)
                ->select();
        // dump($list);exit;
        $this->assign('ulone',  $list[0]);
        $this->assign('ultwo', $list[1]);
        $this->assign('ulthree', $list[2]);

        $system = cache('System');
        $headtitle = '个人中心 - 权限升级 - '.$system['name'];
        $this->assign('headtitle',  $headtitle); 

        $Keyword = '个人中心 - 权限升级';
        $Desc = '展示会员权限组的详细信息';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/userinfo.css']);
        $this->assign('JS',['/static/home2/js/copy.js','/static/home2/js/copy/clipboard.min.js','/static/home2/js/users/auth.js']);
        return $this->fetch();
    }
    //修改密码页面
    public function editpass(){
        if(empty(session('usersmobile'))){
            $this->redirect('/login');
            return false;
        }
        $url = $this->yaoqingcode();
        $this->assign('yqurl',$url); 
        if(request()->isPost()){
            $data = input('post.');
            if(session('usersid')){
                $data['id'] = session('usersid');
                $return = HomeUsers::updatePass($data);
                return ['code' => $return['code'], 'msg' => $return['msg'],'url'=>$return['url']];
            }
        }else{
            $system = cache('System');
            $headtitle = '个人中心 - 修改密码 - '.$system['name'];
            $this->assign('headtitle',  $headtitle); 

            $Keyword = '个人中心 - 修改密码';
            $Desc = '会员可以修改登录密码';
            $this->assign('Keyword', $Keyword);
            $this->assign('Desc',  $Desc);  
            $this->assign('CSS',['/static/home2/css/userinfo.css','/static/admin/css/global.css','/static/common/css/font.css']);
            $this->assign('JS',['/static/home2/js/copy.js','/static/home2/js/copy/clipboard.min.js','/static/home2/js/users/editpass.js']);
            return $this->fetch(); 
        }

    }
    //邀请好友列表页面
    public function friendlist(){
        if(empty(session('usersmobile'))){
            $this->redirect('/login');
            return false;
        }
        if(session('usersid')){
            $uid = session('usersid');
            $res = HomeUsers::yqUser($uid);
            $this->assign('userlist',$res); 
        }
        $url = $this->yaoqingcode();
        $this->assign('yqurl',$url); 

        $system = cache('System');
        $headtitle = '个人中心 - 邀请好友列表 - '.$system['name'];
        $this->assign('headtitle',  $headtitle); 

        $Keyword = '个人中心 - 邀请好友';
        $Desc = '展示会员邀请的好友列表';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/userinfo.css']);
        $this->assign('JS',['/static/home2/js/copy.js','/static/home2/js/copy/clipboard.min.js']);
        return $this->fetch();
    }

    //发送邮箱验证码
    public function sendEmailCode(){
        if(request()->isPost()){
            $data = input('post.emailyz');
            if(!is_email($data)){
                return json(['code' => 0, 'msg' => '邮箱格式错误']);
            }else{
                $return = HomeUsers::sendQqCode($data);
                return json(['code' => $return['code'], 'msg' => $return['msg']]); 
            }
        }else{
            return json(['code' => 0, 'msg' => '请求方式不合法']);
        }  
    }

    //邀请注册码
    public function yaoqingcode(){
        $data= base64_encode('njboseo'.session('usersid'));
        $url = config('url.pact').'://'.config('url.host').'/register?uid='.$data;
        return $url;
    }
    //升级详情页面
    public function payinfo(){
        if(empty(session('usersmobile'))){
            $this->redirect('/login');
            return false;
        }
        $type = input('type');
        $price = input('price');
        $level = session('userinfo.level');
        if (input('ishas')) {
            if ($level == 2) {
                $title = 'VIP用户 - 续费';
                $typetit = 4;
            } elseif($level == 3){
                $title = '企业用户 - 续费';
                $typetit = 5;
            }
        } else {
            if ($type == 2) {
                if ($level == 1) {
                    $title = '普通用户 - VIP用户';
                    $typetit = 1;
                }
            } else {
                if ($level == 1) {
                    $title = '普通用户 - 升级到企业用户';
                    $typetit = 2;
                } else {
                    $title = 'VIP用户 - 升级到企业用户';
                    $typetit = 3;
                }
            } 
        }
       
        $url = $this->yaoqingcode();
        $this->assign('yqurl',$url); 

        $system = cache('System');
        $headtitle = '个人中心 - 权限升级 - '.$system['name'];
        $this->assign('headtitle',  $headtitle); 

        $Keyword = '个人中心 - 权限升级 - ';
        $Desc = '会员升级详情页';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc); 

        $this->assign('price',$price);
        $this->assign('type',$type);
        $this->assign('title',$title);
        $this->assign('typetit',$typetit);
        $this->assign('CSS',['/static/home2/css/userinfo.css','/static/admin/css/global.css','/static/common/css/font.css']);
        $this->assign('JS',['/static/home2/js/copy.js','/static/home2/js/copy/clipboard.min.js', '/static/home2/js/qrcode.min.js']);
        return $this->fetch();
    }
    // 付款
    public function pay(){
        $gid = input('post.gid');
        $typetit = input('post.typetit');
        $pay_type = input('pay_type');//付款类型
        $groupInfo = db('users_group a')->join('users_rule b', 'a.id=b.groupid', 'left')->field('a.id,a.title,b.price')->where('a.id', $gid)->find();
        // 订单号
        $out_trade_no = session('userinfo.mobile').time();
        // 标题
        switch ($typetit) {
            case 1:
                $subject = '普通用户 - 升级到VIP用户';
                break;
            case 2:
                $subject = '普通用户 - 升级到企业用户';
                break;
            case 3:
                $subject = 'VIP用户 - 升级到企业用户';
                break;
            case 4:
                $subject = 'VIP用户 - 续费';
                break;
            case 5:
                $subject = '企业用户 - 续费';
                break;
            default:
                $subject = '升级-'.$groupInfo['title'];
        }
        
        // 金额
        $total_fee = $groupInfo['price'];
        $orderdata = [
            'uid'=>session('userinfo.id'),
            'mobile'=>session('userinfo.mobile'),
            'order_id'=>$out_trade_no,
            'title'=>$subject,
            'buy_time'=>12,
            'buy_level'=>$gid,
            'total'=>$total_fee,
            'status'=>0,
            'ordertype'=>$typetit,
            'addtime'=>time()
        ];
        if (!db('seo_order')->insert($orderdata)) {
            die('订单生成失败');
        }

        switch ($pay_type) {
            case "weixin":
                $wxConfig = config('weixin.');
                $wxConfig['notify_url'] = config('url.pact').'://'.config('url.host')."/home/users/wx_notify_url";
                $config_biz = [
                    'out_trade_no' => $out_trade_no,
                    'total_fee' => $total_fee*100, // **单位：分**
                    'body' => $subject,
                ];
                $pay = new Pay(['wechat'=>$wxConfig]);
                $code_url = $pay->driver('wechat')->gateway('scan')->pay($config_biz);
                return ['code'=>1, 'code_url'=>$code_url, 'order_id'=>$out_trade_no, 'a'=>$wxConfig['notify_url']];
            break;
            default:
                require_once(Env::get('EXTEND_PATH').'pay/alipay.config.php');
                /**************************请求参数**************************/
                //商户订单号，商户网站订单系统中唯一订单号，必填
                // $out_trade_no = $order_id;
        
                //订单名称，必填
                // $subject = $data['hostname'];
        
                //付款金额，必填
                // $total_fee = $data['total'];
        
                //商品描述，可空
                // $body = $_POST['WIDbody'];
                /**************************请求参数**************************/
        
                /************************************************************/
                //构造要请求的参数数组，无需改动
                $parameter = array(
                        "service"       => $alipay_config['service'],
                        "partner"       => $alipay_config['partner'],
                        "seller_id"  => $alipay_config['seller_id'],
                        "payment_type"  => $alipay_config['payment_type'],
                        "notify_url"    => $alipay_config['notify_url'],
                        "return_url"    => $alipay_config['return_url'],
                        
                        "anti_phishing_key"=>$alipay_config['anti_phishing_key'],
                        "exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
                        "out_trade_no"  => $out_trade_no,
                        "subject"   => $subject,
                        "total_fee" => $total_fee,
                        "body"  => '',
                        "_input_charset"    => trim(strtolower($alipay_config['input_charset']))       
                );
                /************************************************************/
        
                /*************发送请求**************/ 
                $alipaySubmit = new AlipaySubmit($alipay_config);
                $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
                echo $html_text;
                /*************发送请求**************/
            break;
        }
    }
    
    // 异步：处理订单
    public function notify_url(){
        if($this->request->isPost()){
            // 引入配置文件
            require_once(Env::get('EXTEND_PATH').'pay/alipay.config.php');
            // 接收支付宝服务器发送过来的数据
            $post = $this->request->post();
            // 得到sign的值
            $sign = $post['sign'];
            // 得到签名方式
            $sign_type = $post['sign_type'];
            // 获取商品价格
            $olist = db('seo_order')->where('order_id', $post['out_trade_no'])->find();

            
            $ordertype = $olist['ordertype'];
            $alipaySubmit = new AlipaySubmit($alipay_config);
            // 调用签名方法
            $res = $alipaySubmit->checkSign($post, $sign);
            // 判断：验证签名 交易成功状态 支付宝收的钱是否等于订单产品的钱 
            if($res && $post['trade_status'] == 'TRADE_SUCCESS' && $post['total_fee'] == $olist['total']){
                // 把支付宝返回的数据信息存储起来
                $strpay = [
                    'notify_time'=>$post['notify_time'],
                    'notify_type'=>$post['notify_type'],
                    'notify_id'=>$post['notify_id'],
                    'sign_type'=>$sign_type,
                    'sign'=>$sign,
                    'out_trade_no'=>$post['out_trade_no'],
                    'subject'=>$post['subject'],
                    'trade_no'=>$post['trade_no'],
                    'trade_status'=>$post['trade_status'],
                    'gmt_create'=>$post['gmt_create'],
                    'gmt_payment'=>$post['gmt_payment'],
                    'buyer_email'=>$post['buyer_email'],
                    'zonghe'=>$sign.'|-'.$sign_type,
                ];
                foreach($post as $k=>$v){
                    $strpay['zonghe'] .= '|-'.$v;
                }
                db('seo_pay')->insert($strpay);
            
                $updateData = [
                    'status'=>1,
                    'paytime'=>strtotime($post['gmt_payment']),
                    'opentime'=>time(),
                    'endtime'=>strtotime('+1 years')
                ];
                $orderRes = db('seo_order')->where('order_id', $post['out_trade_no'])->update($updateData);
                $userData = [
                    'level'=>$olist['buy_level'],
                    'endtime'=>strtotime('+1 years')
                ];
                $endtime = db('users')->where('id', $olist['uid'])->value('endtime');
                $xufei = strtotime(date('Y-m-d H:i:s', $endtime).' +1 years');

                if ($ordertype == 1) {
                    $userData['group'] = 2;
                    $userData['opentime'] = time();
                } elseif ($ordertype == 2) {
                    $userData['group'] = 3;
                    $userData['opentime'] = time();
                } elseif ($ordertype == 3) {
                    $userData['opentime'] = time();
                    $userData['group'] = 3;
                } elseif ($ordertype == 4) {
                    $userData['group'] = 2;
                    $userData['endtime'] = $xufei;
                } elseif ($ordertype == 5) {
                    $userData['group'] = 3;
                    $userData['endtime'] = $xufei;
                }
                $userRes = db('users')->where('id', $olist['uid'])->update($userData);
                return $orderRes && $userRes ? 'success' : 'fail';
            }
        }
    }

    // 同步通知
    public function return_url(){
        $userinfo = db('users')->find(session('usersid'));
        session('userinfo', $userinfo);
        $this->redirect('/homeuser');
    }
    
    // 定时器 users_num清空次数
    public function users_num_timer(){
        $res = db('users_num')
        ->where('id', 'in', db('users_num')->column('id'))
        ->update([
            'keyword_querynum'=>0,
            'keyword_exportnum'=>0,
            'keyword_plquerynum'=>0,
            'qz_querynum'=>0,
            'beian_querynum'=>0,
            'beian_plquerynum'=>0,
            'beian_exportnum'=>0,
            'includ_querynum'=>0,
            'includ_exportnum'=>0,
            'rank_querynum'=>0,
            'rank_plquerynum'=>0,
            'rank_exportnum'=>0,
            'wyc_querynum'=>0,
        ]);
        if ($res) {
            echo '执行成功';
        } else {
            echo '执行失败';
        }
        echo date('Y-m-d H:i:s');
    }

    // 获取Api
    public function browseapi()
    {
        $system = cache('System');
        $headtitle = 'API数据接口 - API接口列表 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);
        $Keyword = 'API数据接口,免费API数据调用, API接口';
        $Desc = '搜一搜站长工具免费向开发者提供seo信息查询的相关API数据接口。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);
        $this->assign('apis', db('seo_api')->select());
        $this->assign('CSS',['/static/home2/css/browseapi.css']);
        return $this->fetch();
    }

    // api详情页面
    public function apiinfo()
    {
        $aid = input('aid');
        $title = db('seo_api')->where('id',$aid)->value('title');
        $system = cache('System');
        $headtitle =  $title.' - API接口详情 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);
        $Keyword = 'API数据接口,免费API数据调用, API接口';
        $Desc = '搜一搜站长工具免费向开发者提供seo信息查询的相关API数据接口。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);
        $this->assign('api', db('seo_api')->find($aid));
        $this->assign('aid',$aid);
        $timesInfo = db('seo_api_times')->where(['uid'=>session('usersid'), 'aid'=>$aid])->find();
        $this->assign('timesInfo', $timesInfo);
        $this->assign('CSS',['/static/home2/css/mob.css','/static/home2/css/apiinfo.css']);
        $this->assign('JS',['/static/home2/js/users/apiinfo.js']);
        return $this->fetch();
    }

    // 获取Api
    public function payapi()
    {
        if(empty(session('usersmobile'))){
            $this->redirect('/login');
            return false;
        }
        $apiInfo = db('seo_api')->find(input('aid'));
        $this->assign('apiInfo', $apiInfo);
        $system = cache('System');
        $headtitle = '获取API - 订单确认 - '.$system['name'];
        $this->assign('headtitle',  $headtitle); 
        $Keyword = '获取API - 订单确认';
        $Desc = '获取API订单确认详情页';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc); 
        $this->assign('CSS',['/static/admin/css/global.css','/static/common/css/font.css']);
        $this->assign('JS',['/static/home2/js/copy/clipboard.min.js','/static/home2/js/users/payapi.js', '/static/home2/js/qrcode.min.js']);
        return $this->fetch();
    }

    // 付款
    public function pay2(){
        $aid = input('aid');
        $pay_type = input('pay_type');
        $apiInfo = db('seo_api')->find($aid);
        // 订单号
        $out_trade_no = session('userinfo.mobile').time();
        // 标题
        $subject = "API购买_".$apiInfo['title']."_".$apiInfo['num']."次";
        // 金额
        $total_fee = $apiInfo['price'];
        $orderdata = [
            'uid'=>session('userinfo.id'),
            'mobile'=>session('userinfo.mobile'),
            'aid'=>$aid,
            'order_id'=>$out_trade_no,
            'title'=>$subject,
            'buy_num'=>$apiInfo['num'],
            'total'=>$total_fee,
            'status'=>0,
            'addtime'=>time()
        ];
        if (!db('seo_api_order')->insert($orderdata)) {
            die('订单生成失败');
        }
        switch ($pay_type) {
            case "weixin":
                $wxConfig = config('weixin.');
                $wxConfig['notify_url'] = config('url.pact').'://'.config('url.host')."/home/users/wx_notify_url2";
                $config_biz = [
                    'out_trade_no' => $out_trade_no,
                    'total_fee' => $total_fee*100, // **单位：分**
                    'body' => $subject,
                ];
                $pay = new Pay(['wechat'=>$wxConfig]);
                $code_url = $pay->driver('wechat')->gateway('scan')->pay($config_biz);
                return ['code'=>1, 'code_url'=>$code_url, 'order_id'=>$out_trade_no];
            break;
            default:
                require_once(Env::get('EXTEND_PATH').'pay/alipay.config2.php');
                $parameter = array(
                        "service"       => $alipay_config['service'],
                        "partner"       => $alipay_config['partner'],
                        "seller_id"  => $alipay_config['seller_id'],
                        "payment_type"  => $alipay_config['payment_type'],
                        "notify_url"    => $alipay_config['notify_url'],
                        "return_url"    => $alipay_config['return_url'],
                        
                        "anti_phishing_key"=>$alipay_config['anti_phishing_key'],
                        "exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
                        "out_trade_no"  => $out_trade_no,
                        "subject"   => $subject,
                        "total_fee" => $total_fee,
                        "body"  => '',
                        "_input_charset"    => trim(strtolower($alipay_config['input_charset']))       
                );
                /************************************************************/
        
                /*************发送请求**************/ 
                $alipaySubmit = new AlipaySubmit($alipay_config);
                $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
                echo $html_text;
                /*************发送请求**************/ 
            break;
        }
    }
    
    // 异步：处理订单
    public function notify_url2(){
        if($this->request->isPost()){
            // 引入配置文件
            require_once(Env::get('EXTEND_PATH').'pay/alipay.config2.php');
            // 接收支付宝服务器发送过来的数据
            $post = $this->request->post();
            // 得到sign的值
            $sign = $post['sign'];
            // 得到签名方式
            $sign_type = $post['sign_type'];
            // 获取商品价格
            $olist = db('seo_api_order')->where('order_id', $post['out_trade_no'])->find();
            $ordertype = $olist['ordertype'];
            $alipaySubmit = new AlipaySubmit($alipay_config);
            // 调用签名方法
            $res = $alipaySubmit->checkSign($post, $sign);
            // 判断：验证签名 交易成功状态 支付宝收的钱是否等于订单产品的钱 
            if($res && $post['trade_status'] == 'TRADE_SUCCESS' && $post['total_fee'] == $olist['total']){
                // 把支付宝返回的数据信息存储起来
                $strpay = [
                    'notify_time'=>$post['notify_time'],
                    'notify_type'=>$post['notify_type'],
                    'notify_id'=>$post['notify_id'],
                    'sign_type'=>$sign_type,
                    'sign'=>$sign,
                    'out_trade_no'=>$post['out_trade_no'],
                    'subject'=>$post['subject'],
                    'trade_no'=>$post['trade_no'],
                    'trade_status'=>$post['trade_status'],
                    'gmt_create'=>$post['gmt_create'],
                    'gmt_payment'=>$post['gmt_payment'],
                    'buyer_email'=>$post['buyer_email'],
                    'zonghe'=>$sign.'|-'.$sign_type,
                ];
                foreach($post as $k=>$v){
                    $strpay['zonghe'] .= '|-'.$v;
                }
                db('seo_pay')->insert($strpay);
                // seo_api_order更新
                $updateData = [
                    'status'=>1,
                    'paytime'=>strtotime($post['gmt_payment']),
                ];
                $orderRes = db('seo_api_order')->where('order_id', $post['out_trade_no'])->update($updateData);
                // seo_api_times更新
                $timesInfo = db('seo_api_times')->where(['uid'=>$olist['uid'], 'aid'=>$olist['aid']])->find();
                if ($timesInfo) {
                    $timesRes = db('seo_api_times')->where(['uid'=>$olist['uid'], 'aid'=>$olist['aid']])->update([
                        'total_num'=>$timesInfo['total_num']+$olist['buy_num'],
                        'num'=>$timesInfo['num']+$olist['buy_num'],
                        'modify_time'=>time()
                    ]);
                } else {
                    $timesRes = db('seo_api_times')->insertGetId([
                        'uid'=>$olist['uid'],
                        'aid'=>$olist['aid'],
                        'total_num'=>$olist['buy_num'],
                        'num'=>$olist['buy_num'],
                        'create_time'=>time(),
                        'modify_time'=>time()
                    ]);
                }
                return $orderRes && $timesRes ? 'success' : 'fail';
            }
        }
    }

    // 同步通知
    public function return_url2(){
        $this->redirect('/browseapi');
    }

    // 微信异步回调
    public function wx_notify_url(Request $request)
    {
    	$xml = file_get_contents('php://input');
        $wxConfig = config('weixin.');
        $wxConfig['notify_url'] = config('url.pact')."://".config('url.host')."/home/users/wx_notify_url";
        $pay = new Pay(['wechat'=>$wxConfig]);
        $verify = $pay->driver('wechat')->gateway('scan')->verify($xml);
        if ($verify) {
            if ($verify['result_code'] == "SUCCESS" && $verify['return_code'] == "SUCCESS") {
                $olist = db('seo_order')->where('order_id', $verify['out_trade_no'])->find();
                $ordertype = $olist['ordertype'];
                if ($olist['status'] === 0) {
                    $updateData = [
                        'status'=>1,
                        'paytime'=>time(),
                        'opentime'=>time(),
                        'endtime'=>strtotime('+1 years'),
                        'pay_type'=>'weixin',
                    ];
                    $orderRes = db('seo_order')->where('order_id', $verify['out_trade_no'])->update($updateData);
                    $userData = [
                        'level'=>$olist['buy_level'],
                        'endtime'=>strtotime('+1 years')
                    ];
                    $endtime = db('users')->where('id', $olist['uid'])->value('endtime');
                    $xufei = strtotime(date('Y-m-d H:i:s', $endtime).' +1 years');
                    if ($ordertype == 1) {
                        $userData['group'] = 2;
                        $userData['opentime'] = time();
                    } elseif ($ordertype == 2) {
                        $userData['group'] = 3;
                        $userData['opentime'] = time();
                    } elseif ($ordertype == 3) {
                        $userData['opentime'] = time();
                        $userData['group'] = 3;
                    } elseif ($ordertype == 4) {
                        $userData['group'] = 2;
                        $userData['endtime'] = $xufei;
                    } elseif ($ordertype == 5) {
                        $userData['group'] = 3;
                        $userData['endtime'] = $xufei;
                    }
                    $userRes = db('users')->where('id', $olist['uid'])->update($userData);
                }
            }
        }
        echo $orderRes && $userRes ? 'SUCCESS' : 'ERROR';
    }

    // 微信异步回调
    public function wx_notify_url2(Request $request)
    {
    	$xml = file_get_contents('php://input');
        $wxConfig = config('weixin.');
        $wxConfig['notify_url'] = config('url.pact')."://".config('url.host')."/home/users/wx_notify_url2";
        $pay = new Pay(['wechat'=>$wxConfig]);
        $verify = $pay->driver('wechat')->gateway('scan')->verify($xml);
        if ($verify) {
            if ($verify['result_code'] == "SUCCESS" && $verify['return_code'] == "SUCCESS") {
                $olist = db('seo_api_order')->where('order_id', $verify['out_trade_no'])->find();
                if ($olist['status'] === 0) {
                    $updateData = [
                        'status'=>1,
                        'paytime'=>time(),
                        'pay_type'=>'weixin',
                    ];
                    $orderRes = db('seo_api_order')->where('order_id', $verify['out_trade_no'])->update($updateData);
                    // seo_api_times更新
                    $timesInfo = db('seo_api_times')->where(['uid'=>$olist['uid'], 'aid'=>$olist['aid']])->find();
                    if ($timesInfo) {
                        $timesRes = db('seo_api_times')->where(['uid'=>$olist['uid'], 'aid'=>$olist['aid']])->update([
                            'total_num'=>$timesInfo['total_num']+$olist['buy_num'],
                            'num'=>$timesInfo['num']+$olist['buy_num'],
                            'modify_time'=>time()
                        ]);
                    } else {
                        $timesRes = db('seo_api_times')->insertGetId([
                            'uid'=>$olist['uid'],
                            'aid'=>$olist['aid'],
                            'total_num'=>$olist['buy_num'],
                            'num'=>$olist['buy_num'],
                            'create_time'=>time(),
                            'modify_time'=>time()
                        ]);
                    }
                }
            }
        }
        echo $orderRes && $timesRes ? 'SUCCESS' : 'ERROR';
    }

    // 微信查单
    public function wx_find()
    {
        $order_id = input('order_id');
        $wxConfig = config('weixin.');
        $pay = new Pay(['wechat'=>$wxConfig]);
        $res = $pay->driver('wechat')->gateway('scan')->find($order_id);
        if ($res['trade_state'] == "SUCCESS") {
            $userinfo = db('users')->find(session('usersid'));
            session('userinfo', $userinfo);
            return true;
        } else {
            return false;
        }
    }

    
    public function test(){
        $wxConfig = config('weixin.');
        $wxConfig['notify_url'] = "https://yun.hatidc.com/home/recharge/wx_notify_url";
        $config_biz = [
            'out_trade_no' => time(),
            'total_fee' => 1, // **单位：分**
            'body' => '测试',
        ];
        $pay = new Pay(['wechat'=>$wxConfig]);
        $res = $pay->driver('wechat')->gateway('wap')->pay($config_biz);
        return $res;
    }

    
}