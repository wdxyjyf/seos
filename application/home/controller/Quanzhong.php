<?php
namespace app\home\controller;

use think\Db;
use think\facade\Request;
use app\home\model\Users;
class Quanzhong extends Common{
	public function initialize(){
        parent::initialize();
    }
    public function toplimit() {
        if(!$this->visit()){
           return ['code'=>0, 'msg'=>'查询次数已达上限,请登录查询更多结果'];
        }else{
            $uid = session('usersid');
            if ($uid) {
                $info = Users::numInfo($uid);//记录用户次数表
                $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
                if ($info['qz_querynum'] >= $infolist['qz_querynum']) {
                    return ['code'=>2];
                }
            }
            $this->success();
        }
    }
    //词频权重页面
    public function index(){
        $system = cache('System');
        $headtitle = '在线词频统计查询分析-词频工具 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);     

        $Keyword = '词频,词频统计,词频分析,词频统计软件,词频工具,在线分词';
        $Desc = '在线网站中文分词及词频统计分析与词频制作工具，支持自定义网站任意页面分词网站内容词频，分词更准确，统计更精确！【搜一搜站长工具】词频分析工具。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);

        $limitStart = rand(1,20000);
        $list = Db::name('seo_website_info')->field('website_url')->order('id desc')->limit($limitStart, 12)->select();
        $this->assign('list',  $list);  
        $this->assign('CSS',['/static/home2/css/qz.css']);
        $this->assign('JS',['/static/home2/js/cpqz1.js']);
        return $this->fetch();
    }
    //词频权重结果页面
    public function qzrecord(){
        if (!$this->visit()) {
           return abort(404, '页面异常');
        } 
        $data = Request::param();
        if (session('usersid')) {
            $ruleinfo = Users::numInfo(session('usersid'));//记录用户次数表
            $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
            if ($ruleinfo['qz_querynum'] >= $infolist['qz_querynum']) {
                return $this->redirect('/rate');
            }

            if (empty($ruleinfo)) {
                Db::name('users_num')->insert([
                    'userid'           =>  session('usersid'),
                    'qz_querynum'       => 1,
                ]);
            } else {
                if ($ruleinfo['qz_querynum']) {
                    if ($ruleinfo['qz_querynum'] < $infolist['qz_querynum']) {
                        $ruleinfo['qz_querynum'] += 1;
                        Db::name('users_num')->where('id',$ruleinfo['id'])->setField('qz_querynum', $ruleinfo['qz_querynum']);
                    } 
                } else {
                    $ruleinfo['qz_querynum'] = 1;
                    Db::name('users_num')->where('id',$ruleinfo['id'])->setField('qz_querynum', $ruleinfo['qz_querynum']);
                }
            }
        }
        $qzurl = trim($data['qzweb'])?:trim($data['web']);//提交的网址
        $qzkey = str_replace(' ', '', urlsafe_b64decode($data['key']));//提交关键字
        if ($qzurl == '') {
            return abort(404, '页面异常');
        }
        
        $onearr = endurl($qzurl);
        // 获取网址的标题
        $urltitle = db('seo_website_info')->where('website_url',$onearr)->value('title')?:'';
        if ($urltitle) {
            $istitle = 1;
        } else {
            $istitle = 0;
        }
        $expire = "7";
        if($qzkey){//关键字存在
            $keyres = db('seo_keyword_hotdig')->where('keyword',$qzkey)->value('averagePv')?:0;
            $keynum = 0;
            if (strpos($qzurl,'http') === false && strpos($qzurl,'https') === false) {
                $qzurl = 'http://'.$qzurl;
            }
            if ($res = $this->redis->lrange($qzurl."_pinglv".$expire."_".$qzkey, 0, -1)[0]) {
                $info = json_decode($res, 1);
            } else {
                $info = [];
            }

        }else{
            $keynum = 1; 
            if (strpos($qzurl,'http') === false && strpos($qzurl,'https') === false) {
                $qzurl = 'http://'.$qzurl;
            }
            $res = $this->redis->lrange($qzurl."_pinglv".$expire, 0, -1)[0];
            if($res){
                $arr1 = json_decode($res, 1);
                foreach ($arr1['frequency'] as $k => $v) {
                    $averagePv = db('seo_keyword_hotdig')->where('keyword',$arr1['keyword'][$k])->find();
                    if(empty($averagePv)){
                        $sousl = 0;
                    }else{
                        $sousl = $averagePv['averagePv'];  
                    }
                    $arr1['frequency'][$k] = [
                        'frequency'=>$v,
                        'keyword'=>$arr1['keyword'][$k],
                        'sousl'=>$sousl,
                        'word_num'=>$arr1['word_num'][$k]
                    ];
                }
                $info = $arr1['frequency'];
            }else{
                $info = [];
            }
        }

        $this->assign('count', $keynum);  
        $this->assign('istitle', $istitle);
        $this->assign('onearr', $onearr);
        $system = cache('System');
        $headtitle = $onearr.' - 词频权重查询 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);  
        $this->assign('qzurl', $qzurl);  //网址 
        $this->assign('urltitle', $urltitle);  //网址标题
        $this->assign('qzkey', $qzkey);  //关键字
        $this->assign('keyres', $keyres);  //搜索量
        $this->assign('info', $info);  //搜索量
        $this->assign('isAjax', $info?0:1);  //搜索量
        // 更多网址查询
        $limitStart = rand(1,10000);
        $list = Db::name('seo_website_info')->field('website_url')->order('id desc')->limit($limitStart, 12)->select();
        $this->assign('list',  $list); 

        $Keyword = $onearr . '网站词频查询';
        $Desc = $onearr.' ,网站内容关键词词频权重查询。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc); 
        $this->assign('CSS',['/static/home2/css/qz.css']);
        $this->assign('JS',['/static/home2/js/cpqz.js','/static/home2/js/baidupush.js']);     
        // 广告
        $ad = $this->getAd(21);
        $this->assign('ad', $ad);
        
        return $this->fetch();  
    }
    //只提交网址
    public function qzrecord3(){
        if (Request::isAjax()) {
            $expire = "7";
            $arr['expire'] = $expire;
            $qzurl = input('qzurl');//网址
            $qzurl = str_replace('&amp;', '&', $qzurl);
            if(strpos($qzurl,'http') !== false){
                $arr['url'] = $qzurl;
            }elseif(strpos($qzurl,'https') !== false){
                $arr['url'] = $qzurl;
            }else{
                $arr['url'] = 'http://'.$qzurl;
            }
            if(!$this->redis->lrange($arr['url']."_pinglv".$expire,0,-1)){
                $this->redis->rpush('QZKEY', JSON($arr));
                $res = rpopList3($arr['url']."_pinglv".$expire,2)[0];
                if($res){
                    $arr1 = json_decode($res, 1);
                    foreach ($arr1['frequency'] as $k => $v) {
                        $averagePv = db('seo_keyword_hotdig')->where('keyword',$arr1['keyword'][$k])->find();
                        if(empty($averagePv)){
                            $sousl = 0;
                        }else{
                            $sousl = $averagePv['averagePv'];  
                        }
                        $arr1['frequency'][$k] = [
                            'frequency'=>$v,
                            'keyword'=>$arr1['keyword'][$k],
                            'sousl'=>$sousl,
                            'word_num'=>$arr1['word_num'][$k]
                        ];
                    }
                    $info = $arr1['frequency'];
                }else{
                    $info = [];
                }
            }else{
                $res = rpopList3($arr['url']."_pinglv".$expire,2)[0];
                if($res){
                    $arr1 = json_decode($res, 1);
                    foreach ($arr1['frequency'] as $k => $v) {
                        $averagePv = db('seo_keyword_hotdig')->where('keyword',$arr1['keyword'][$k])->find();
                        if(empty($averagePv)){
                            $sousl = 0;
                        }else{
                            $sousl = $averagePv['averagePv'];  
                        }
                        $arr1['frequency'][$k] = [
                            'frequency'=>$v,
                            'keyword'=>$arr1['keyword'][$k],
                            'sousl'=>$sousl,
                            'word_num'=>$arr1['word_num'][$k]
                        ];
                    }
                    $info = $arr1['frequency'];
                }else{
                    $info = [];
                }
            }
            return ['code'=>1, 'list'=>$info];
        }
    }
    //首页提交单个词频查询
    public function qzrecord2(){
        if (Request::isAjax()) {
            $expire = "7";
            $qzurl = input('qzurl');//网址
            $qzkey = input('qzkey');//提交关键字
            $arr['expire'] = $expire;
            if(strpos($qzurl,'http') !== false) {
                $arr['url'] = $qzurl;
            } elseif(strpos($qzurl,'https') !== false) {
                $arr['url'] = $qzurl;
            } else {
                $arr['url'] = 'http://'.$qzurl;
            }
            $arr['keyword'] = str_replace(' ', '', $qzkey);

            if(!$this->redis->lrange($arr['url']."_pinglv".$expire."_".$arr['keyword'],0,-1)) {
                $this->redis->rpush('QZKEY', JSON($arr));
            } 
            return ['code'=>1, 'cipin'=>$arr['url']."_pinglv".$expire."_".$arr['keyword']];
        } 
    }
    // 首页获取单个词频查询
    public function getOnePinl(){
        $onepinl = trim(input('cipin'));
        if ($res = $this->redis->lrange($onepinl, 0, -1)[0]) {
            return ['code'=>1,'list'=>json_decode($res)];
        } else {
            return ['code'=>0];
        }
    }
    //首页一键查询词频
    public function qzrecord4(){
        if (Request::isAjax()) {
            $expire = "31";
            $qzurl = input('qzurl');//网址
            $qzkeys = input('qzkeys');//提交关键字
            $arr['expire'] = $expire;
            $data = [];
            if(strpos($qzurl,'http') !== false) {
                $arr['url'] = $qzurl;
            } elseif(strpos($qzurl,'https') !== false) {
                $arr['url'] = $qzurl;
            } else {
                $arr['url'] = 'http://'.$qzurl;
            }
            foreach ($qzkeys as $qzkey) {
                $res = $this->redis->lrange($arr['url']."_pinglv".$expire."_".$qzkey, 0, -1)[0];
                if ($res) {
                    $data[] = json_decode($res);
                } else {
                    $arr['keyword'][] = $qzkey;
                }
            }
            if (count($data) < count($qzkeys)) {
                $this->redis->rpush('QZKEY', JSON($arr));
            }
            return ['code'=>1, 'info'=>$data];
        }
    }
    //网址和关键字  首页
    public function getqzrecord(){
        if (Request::isAjax()) {
            $expire = "31";
            $qzurl = input('qzurl');//网址
            $qzkeys = input('qzkeys');//提交关键字
            $arr = [];
            $data = [];
            if(strpos($qzurl,'http') !== false) {
                $arr['url'] = $qzurl;
            } elseif(strpos($qzurl,'https') !== false) {
                $arr['url'] = $qzurl;
            } else {
                $arr['url'] = 'http://'.$qzurl;
            }
            foreach ($qzkeys as $qzkey) {
                $res = $this->redis->lrange($arr['url']."_pinglv".$expire."_".$qzkey, 0, -1)[0];
                if ($res) {
                    $data[] = json_decode($res);
                }
            }
            return ['code'=>1, 'info'=>$data];
        }
    }

    // 词频查询后没有标题,提交redis
    public function qzredis(){
        if (Request::isAjax()) {
            $qzurl = endurl(trim(strip_tags(input('qzurl'))));
            if ($url = urlmatch($qzurl)) {
                $urlarr = "'www.".$url."'".','."'".$url."'";
                $data['topurl'] = urlmatch($qzurl);
            } else {
                $urlarr = "'".$qzurl."'";
                $data['topurl'] = $qzurl;
            }
            //获取备案信息
            $results = Db::query($sql = "select website_url,start_time,record_num,nature,name from seo_website where website_url in (".$urlarr.")");
            if (count($results) > 1) {
                $result = $results[0]['record_num']?$results[0]:$results[1];
            } else {
                $result = $results[0];
            }
            if ($result) {
                $tkd = Db::name('seo_website_info')->field('title,keyword,description,jj,create_time')->where("website_url in (".$urlarr.")")->find();
                if ($result['start_time'] && $tkd) {
                    $data['ishas'] = '1';
                } elseif (!$result['start_time'] && $tkd) {
                   $data['info'] = '2';
                } elseif ($result['start_time'] && !$tkd) {
                    $data['info'] = '1';
                } else{
                    $data['info'] = '3';
                }
            }            
            $data['url'] = $qzurl;
            if(!$this->redis->lrange('TOP:'.$qzurl, 0, -1)){
                $this->redis->zadd('WEBURL',20000,JSON($data));
                return ['code'=>1];
            } else {
               return ['code'=>0]; 
            }
        }
    }
}
