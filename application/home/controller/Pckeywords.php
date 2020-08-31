<?php
namespace app\home\controller;

use think\Db;
use think\facade\Request;
use app\home\model\Users;

class Pckeywords extends Common{
	public function initialize(){
        parent::initialize();
    }
    public function toplimit($type)
    {
        if(!$this->visit()){
            return ['code'=>0, 'msg'=>'查询次数已达上限,请登录查询更多结果'];
        }else{
            $uid = session('usersid');
            if ($uid) {
                $info = Users::numInfo($uid);//记录用户次数表
                $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
                if ($type == 'rank_querynum') {
                    if ($info['rank_querynum'] >= $infolist['rank_querynum']) {
                        return ['code'=>2];
                    }
                } elseif ($type == 'rank_plquerynum'){
                    if ($info['rank_plquerynum'] >= $infolist['rank_plquerynum']) {
                        return ['code'=>2];
                    }
                }
            }
            $this->success();
        }
    }
    //pc端关键词排名页面
    public function pckeyindex(){
        $system = cache('System');
        $headtitle = 'PC端百度 - 好搜 - 搜狗排名查询 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);   
        $Keyword = 'PC端排名查询';
        $Desc = 'PC端网站排名查询工具，百度PC端排名查询工具，360搜索PC端排名查询工具，搜狗PC端排名查询工具';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/mob.css','/static/home2/css/select.css']);
        $this->assign('JS',['/static/home2/js/pckey/pcpm.js']);
        return $this->fetch();
    }
    //pc端关键词排名结果页面
    public function pcrecord(){
        if (!$this->visit()) {
            return abort(404, '页面异常');
        } 
        if (Request::isAjax()) {
            $pcsign = input('sign');
            if ($res = $this->redis->rpop($pcsign)) {
                $arr = json_decode($res, 1);
                if( $arr['rank'] == 1){
                    $arr['flow'] = round($arr['sousl'] * 0.9);
                }elseif($arr['rank'] == 2){
                   $arr['flow'] = round($arr['sousl'] * 0.8);
                }elseif($arr['rank'] == 3){
                   $arr['flow'] = round($arr['sousl'] * 0.7);
                }elseif($arr['rank'] == 4){
                   $arr['flow'] = round($arr['sousl'] * 0.6);
                }elseif($arr['rank'] == 5){
                   $arr['flow'] = round($arr['sousl'] * 0.5);
                }elseif($arr['rank'] == 6){
                   $arr['flow'] = round($arr['sousl'] * 0.4);
                }elseif($arr['rank'] == 7){
                   $arr['flow'] = round($arr['sousl'] * 0.3);
                }elseif($arr['rank'] == 8){
                   $arr['flow'] = round($arr['sousl'] * 0.2);
                }elseif($arr['rank'] == 9){
                   $arr['flow'] = round($arr['sousl'] * 0.1);
                }else{
                   $arr['flow'] = round($arr['sousl'] * 0);
                }   
                return $arr;
            } else {
                return ['code'=>0];
            }
            // $res = rpopList2(input('sign'), 1);
            // if(empty($res)){
            //     $arr = [];
            // }else{
            //     $arr = json_decode($res, 1);
            //     if( $arr['rank'] == 1){
            //         $arr['flow'] = round($arr['sousl'] * 0.9);
            //     }elseif($arr['rank'] == 2){
            //        $arr['flow'] = round($arr['sousl'] * 0.8);
            //     }elseif($arr['rank'] == 3){
            //        $arr['flow'] = round($arr['sousl'] * 0.7);
            //     }elseif($arr['rank'] == 4){
            //        $arr['flow'] = round($arr['sousl'] * 0.6);
            //     }elseif($arr['rank'] == 5){
            //        $arr['flow'] = round($arr['sousl'] * 0.5);
            //     }elseif($arr['rank'] == 6){
            //        $arr['flow'] = round($arr['sousl'] * 0.4);
            //     }elseif($arr['rank'] == 7){
            //        $arr['flow'] = round($arr['sousl'] * 0.3);
            //     }elseif($arr['rank'] == 8){
            //        $arr['flow'] = round($arr['sousl'] * 0.2);
            //     }elseif($arr['rank'] == 9){
            //        $arr['flow'] = round($arr['sousl'] * 0.1);
            //     }else{
            //        $arr['flow'] = round($arr['sousl'] * 0);
            //     }
            // }
            // return $arr;
        }
        $data = [];
        if (session('usersid')) {
            $ruleinfo = Users::numInfo(session('usersid'));//记录用户次数表
            $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
            if ($ruleinfo['rank_querynum'] >= $infolist['rank_querynum']) {
                return $this->redirect('/mrank');
            }
            if (empty($ruleinfo)) {
                Db::name('users_num')->insert([
                    'userid'           =>  session('usersid'),
                    'rank_querynum'       => 1,
                ]);
            } else {
                if ($ruleinfo['rank_querynum']) {
                    if ($ruleinfo['rank_querynum'] < $infolist['rank_querynum']) {
                        $ruleinfo['rank_querynum'] += 1;
                        Db::name('users_num')->where('id',$ruleinfo['id'])->setField('rank_querynum', $ruleinfo['rank_querynum']);
                    } 
                } else {
                    $ruleinfo['rank_querynum'] = 1;
                    Db::name('users_num')->where('id',$ruleinfo['id'])->setField('rank_querynum', $ruleinfo['rank_querynum']);
                }
            }
        }
        $kw = urlsafe_b64decode(input('keyword'));
        if (!input('url') || !$kw || !urlmatch(input('url')) || !stringmatch($kw)) {
            return abort(404, '页面异常');
        }
        if (input('post.engine')) {
            $data['engine_type'] = input('post.engine');
        } else {
            $data['engine_type'] = '0';
        }
        $data['engine_type'] = input('post.engine','0');
        $data['keyword'] = $kw;

    
         if($data['engine_type'] == 0){
            $type = '百度';
            $icon = 'icon-baidu';
        }elseif($data['engine_type'] == 1){
            $type = '360';
            $icon = 'icon-360';
        }elseif($data['engine_type'] == 2){
            $type = '搜狗';
            $icon = 'icon-sougou';
        }elseif($data['engine_type'] == 3){
            $type = '神马';
            $icon = 'icon-shenma';
        }
        $engine = $data['engine_type'];
        $keyres = db('seo_keyword_hotdig')->where('keyword',$data['keyword'])->value('averagePvPc')?:0;//搜索量
        $this->assign('sousl', $keyres);
        $data['sousl'] = $keyres;
        $data['target'] = endurl(trim(input('url')));
        
        $data['sign'] = 'YD:'.uniqid();
        $data['platform'] = '0';
        $this->redis->rpush('YDKEY', JSON($data));
        $this->assign('sign', $data['sign']);
        $this->assign('engine',$engine);     
        $system = cache('System');
        $headtitle = $type.'PC端'.' : '.' '.$data['keyword'].' '.'排名查询 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);  
        $this->assign('kw',$data['keyword']); 
        $this->assign('target',input('url'));   
        $this->assign('type',  $type);       
         $this->assign('icon',  $icon);      

        $Keyword = 'PC端排名查询';
        $Desc = 'PC端网站排名查询工具，百度PC端排名查询工具，360搜索PC端排名查询工具，搜狗PC端排名查询工具';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/mob.css','/static/home2/css/select.css']);
        $this->assign('JS',['/static/home2/js/pckey/pcpm.js']);
        // 广告
        $ad = $this->getAd(17);
        $this->assign('ad', $ad);

        return $this->fetch(); 
        
    }

    // 首页点击单个排名查询
    public function rankpc()
    {
        $data = Request::param();
        $data['target'] = endurl(trim($data['target']));
        $data['sign'] = 'YD:'.uniqid();
        $data['platform'] = '0';
        $data['sousl'] = db('seo_keyword_hotdig')->where('keyword', $data['keyword'])->value('averagePvPc')?:0;
        $res = $this->redis->rpush('YDKEY', JSON($data));
        if ($res) {
            return ['code'=>1, 'sign'=>$data['sign']];
        } else {
            return ['code'=>0];
        }
    }
    public function getOneRank(){
        $pcsign = input('sign');
        if ($res = $this->redis->rpop($pcsign)) {
            $arr = json_decode($res, 1)['rank'];
            return ['code'=>1,'paiming'=>$arr];
        } else {
            return ['code'=>0];
        }
    }
    // 首页点击一键排名查询
    public function putrankpc()
    {
        $data = Request::param();
        $url = trim($data['target']);
        $test = parse_url($url);   
        if(array_key_exists('host', $test)){
            $data['target'] =  $test['host'];
        }else{
            if(strpos($test['path'], '/')){
               $data['target'] = strstr($test['path'], '/', TRUE);;
            }else{
               $data['target'] =  $test['path'];  
            }
        }
        $data['sign'] = 'YD:'.uniqid();
        $data['platform'] = '0';
        $newarr = $data['keyword'];
        foreach ($newarr as $k => $v) {
            $kw = str_replace(' ', '', $v);
            $sousl[$k]= db('seo_keyword_hotdig')->where('keyword',$kw)->value('averagePvPc')?:0;
        }
        $data['sousl'] = $sousl;//pc周搜索量
        // dump($data);exit;
        $res = $this->redis->rpush('YDKEY', JSON($data));
        if ($res) {
            return ['code'=>1, 'sign'=>$data['sign']];
        } else {
            return ['code'=>0];
        }
    }

    public function getrankpc()
    {
        $sign = input('sign');
        $data = [];
        while ($res = $this->redis->rpop($sign)) {
            $data[] = json_decode($res);
        }
        return ['code'=>1, 'info'=>$data];
    }
    
    //pc端关键词批量排名页面
    public function pckeypl(){
        $system = cache('System');
        $headtitle = 'PC端百度 - 好搜 - 搜狗排名查询 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);     

        $Keyword = 'PC端批量排名查询';
        $Desc = 'PC端网站排名批量查询工具，百度PC端排名批量查询工具，360搜索PC端排名批量查询工具，搜狗PC端排名批量查询工具';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        // $level = Users::userRule(0,'pmexport');
        // $this->assign('level',$level);  
        $pmorder = Users::userRule(0,0,'pmorder');//用户权限排名排序
        $this->assign('pmorder',$pmorder);

        $rank_plsubmit = $this->querrplnum('rank_plsubmit');
        $this->assign('rank_plsubmit',$rank_plsubmit);
        $this->assign('CSS',['/static/home2/css/order.css','/static/home2/css/mob.css','/static/home2/css/select.css']);
        $this->assign('JS',['/static/home2/js/pckey/pcpl.js','/static/home2/js/copy/clipboard.min.js']);
        return $this->fetch();
        
    }

    public function pckeypla()
    {
        if(!$this->visit()){
            return ['code'=>0, 'msg'=>'查询次数已达上限,请登录查询更多结果'];
        }
        if (session('usersid')) {
            $ruleinfo = Users::numInfo(session('usersid'));//记录用户次数表
            $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
            if ($ruleinfo['rank_plquerynum'] >= $infolist['rank_plquerynum']) {
                return ['code'=>3, 'msg'=>'今日关键词排名批量查询次数已达上限，是否升级会员组获取更多次数'];
            }
            if (empty($ruleinfo)) {
                Db::name('users_num')->insert([
                    'userid'           =>  session('usersid'),
                    'rank_plquerynum'       => 1,
                ]);
            } else {
                if ($ruleinfo['rank_plquerynum']) {
                    if ($ruleinfo['rank_plquerynum'] < $infolist['rank_plquerynum']) {
                        $ruleinfo['rank_plquerynum'] += 1;
                        Db::name('users_num')->where('id',$ruleinfo['id'])->setField('rank_plquerynum', $ruleinfo['rank_plquerynum']);
                    } 
                } else {
                    $ruleinfo['rank_plquerynum'] = 1;
                    Db::name('users_num')->where('id',$ruleinfo['id'])->setField('rank_plquerynum', $ruleinfo['rank_plquerynum']);
                }
            }
        }
        $param = input('post.');//接受所有数据

        $content = [];//定义空数组
        $sousl = [];
        $url = trim($param['target']);
        $test = parse_url($url);
        
        if(array_key_exists('host', $test)){
            $url =  $test['host'];
        }else{
            if(strpos($test['path'], '/')){
               $url = strstr($test['path'], '/', TRUE);;
            }else{
               $url =  $test['path'];  
            }
            
        }
         $rank_plsubmit =  $param['rank_plnum'];
        $arr = explode("\n",trim($param['keyword']));
        $keyarr = [];
        if (!$arr) return ['code' => 0, 'msg' => '请输入关键词'];
        // if(count($arr) == 1) return ['code' => 0, 'msg' => '至少两个关键字'];
        foreach ($arr as $k => $v) {
            $kw = str_replace(' ', '', $v);
            if ($kw) {
                $arr2[] = $kw;
                if (!preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9\s\-\_\.\:]+$/u", $kw)) {
                    return ['code' => 0, 'msg' => '格式错误,请输入中文,字母或数字'];   
                }else{
                    if(mb_strlen($kw,'UTF8') > 15){
                        return ['code' => 0, 'msg' => '每行关键词长度最大15个字符'];
                    }
                }
                array_push($keyarr,$kw);
                $keyres = db('seo_keyword_hotdig')->where('keyword',$kw)->value('averagePvPc')?:0;
                $sousl[$k] = $keyres;
            }
        }
        if(count($arr2) > $rank_plsubmit) return ['code' => 0, 'msg' => '最多填写'.$rank_plsubmit.'行'];
        $keyword2 = implode("\n", $arr2);
        if($param['engine_type'] == 0){
            $engine_type = '百度';
        }elseif($param['engine_type'] == 1){
            $engine_type= '360';
        }elseif($param['engine_type'] == 2){
            $engine_type = '搜狗';
        }elseif($param['engine_type'] == 3){
            $engine_type = '神马';
        }
        $content['engine_name'] = $engine_type;
        $content['engine_type'] = $param['engine_type']?:'0';//搜索引擎类型
        $content['keyword'] = $keyarr;//关键字数组
        $content['target']  = $url;//url
        $content['platform'] = '0';//终端类型 0pc
        $content['sousl']  = $sousl;//pc周搜索量
        $content['sign'] = 'YD:'.uniqid();
        // dump($content);exit;
        setcookie('signpc', $content['sign'], time()+3600);
        
        $res = $this->redis->rpush('YDKEY', JSON($content));//提交redis
        return $res?['code'=>1, 'num'=>count($arr), 'sousl'=>$sousl, 'engine_type'=>$engine_type, 'keyword2'=>$keyword2]:['code'=>0];
    }

    public function pckeyplb()
    {
        $info = [];
        while ($res = $this->redis->rpop($_COOKIE["signpc"])) {
            $info[] = $res;
        }
        return $info?['code'=>1, 'list'=>$info]:['code'=>0];
    }

    //导出数据excel
    public function daochu()
    {
        $list = json_decode(input('shuju'), 1);
        Db::name('users')->where('id', session('usersid'))->setDec('point', 2);
        Db::name('seo_goods_spend')->insert([
                'uid'           =>  session('usersid'),
                'umobile'       =>  session('usersmobile'),
                'gid'           =>  0,
                'content'       =>  'PC端关键词批量查询统计下载',
                'spendcode'        =>  2,
                'create_time'   => time()
            ]);
        $this->info2excel($list, '搜一搜站长工具_PC端关键词批量查询结果');
    }
}
