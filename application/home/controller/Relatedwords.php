<?php
namespace app\home\controller;

use app\home\model\Relatedwords as HomeRelatedwords;
use think\Db;
use think\facade\Request;
use app\home\model\Users;

class Relatedwords extends Common{
	public function initialize(){
        parent::initialize();
    }
    public function toplimit()
    {
        if(!$this->visit()){
            return ['code'=>0, 'msg'=>'查询次数已达上限,请登录查询更多结果'];
        }else{
            $uid = session('usersid');
            if ($uid) {
                $info = Users::numInfo($uid);//记录用户次数表
                $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
                if ($info['keyword_querynum'] >= $infolist['keyword_querynum']) {
                    return ['code'=>2];
                }
            }
            $this->success();
        }
    }
    //相关词页面
    public function index(){
        // $time = time();//当前时间戳
        // $threetime = strtotime('-7 days');//前一周的时间戳
        // $list = Db::name('seo_relevant_word')
        //     ->whereTime('create_time', 'between', [$threetime,  $time])
        //     ->limit(10)
        //     ->order('averagePv desc')
        //     ->select();
        // if (!$list = session('relatedwords_list')) {
        //     $list = HomeRelatedwords::limit(20,10)->field('id,keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->order('averagePv desc')->select();
        //     session('relatedwords_list', $list);
        // }
        if ($res = $this->redis->get('static_timer:related')) {
            $list = json_decode($res, 1);
        } else {
            $limitStart = rand(1,2000);
            $list = Db::name('seo_relevant_word')->limit($limitStart,20)
                    ->field('id,keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')
                    ->where('char_length(keyword)>=4 and char_length(keyword)<=10')
                    ->order('averagePv desc')
                    ->select();
            
            $this->redis->set('static_timer:related', json_encode($list));
            $expireTime = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
            $this->redis->expireAt('static_timer:related', $expireTime);
        }
        $ban = Db::name('ban')->column('ban');
        foreach ($list as $k=>$v) {
            foreach ($ban as $b) {
                if (stripos($v['keyword'], $b) !== false) {
                    unset($list[$k]);
                    continue 2;
                }
            }
        }
        $list = array_slice($list, 0, 10);
        // 违禁词替换开始
        // $ban = Db::name('ban')->column('ban');
        // foreach ($list as $k1=>$v1) {
        //     $list[$k1]['keyword'] = str_ireplace($ban, '*', $v1['keyword']);
        // }
        // 违禁词替换结束

        $this->assign('list',$list);   
        $system = cache('System');
        $headtitle = '长尾词挖掘 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);  
        $userinfo = session('userinfo');
        if ($userinfo) {
            $querynum = Db::name('users_num')->where('userid',$userinfo['id'])->value('keyword_querynum');
            $querynum2 = Db::name('users_rule')->where('groupid',$userinfo['level'])->value('keyword_querynum');
            if ($querynum >= $querynum2) {
                $this->assign('stop', 1);
            }
        }
        $Keyword = '长尾词挖掘,关键词挖掘,长尾词挖掘工具';
        $Desc = '在线长尾词挖掘工具,快速挖掘相关长尾词词库，支持pc电脑端和移动端长尾关键词挖掘，【搜一搜站长工具】长尾词挖掘工具。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);    
        $this->assign('CSS',['/static/home2/css/mob.css']);
        $this->assign('JS',['/static/home2/js/related/query.js']); 
        return $this->fetch();
    }

    //相关词结果页面
    public function selectres(){
        if (!$this->visit()) {
            return abort(404, '页面异常');
        } 
        $keyword = trim(urlsafe_b64decode(input('key')));//接收要搜索的值
        $uid = session('usersid');
        $page = input('page');
        if ($keyword) {
            $kw = str_replace(' ', '', $keyword);
            $kwcount = mb_strlen($kw,'UTF8');
            $sort = input('sort');
            if (preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u", $kw) && $kwcount <=15) {
                if ($uid) {
                    if (!$page && !$sort) {
                        $ruleinfo = Users::numInfo($uid);//记录用户次数表
                        $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
                        if ($ruleinfo['keyword_querynum'] >= $infolist['keyword_querynum']) {
                            return $this->redirect('/related');
                        }
                       
                        if ($ruleinfo['keyword_querynum']) {
                            if ($ruleinfo['keyword_querynum'] < $infolist['keyword_querynum']) {
                                $ruleinfo['keyword_querynum'] += 1;
                                Db::name('users_num')->where('id',$ruleinfo['id'])->setField('keyword_querynum', $ruleinfo['keyword_querynum']);
                            } 
                        } else {
                            $ruleinfo['keyword_querynum'] = 1;
                            Db::name('users_num')->where('id',$ruleinfo['id'])->setField('keyword_querynum', $ruleinfo['keyword_querynum']);
                        }
                    }
                }
                $ip = getIp();
                $recordinfo = Db::name('seo_relevant_records')->where('relevantkey',$kw)->find();
                $result2 = Db::name('seo_relevant_records')->where(['relevantkey'=>$kw,'ipaddress'=>$ip])->max('create_time');
                if (!$result2 || time() - $result2 > 86400) {
                    $intkeywordwords['relevantkey'] = $kw;
                    $intkeywordwords['create_time'] = time();
                    $intkeywordwords['ipaddress'] = getIp();
                    Db::name('seo_relevant_records')->insert($intkeywordwords);//保存客户查询的关键词热度
                }
                // $kwharr = $this->redis->lrange('XG',0,-1);
                $info = Db::name('seo_relevant_word')->where('keyword',$kw)->find();
                
                $ids = sphinx($kw, 'relevantword', 5000);
                $result = Db::name('seo_relevant_word')->field('keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where('id2', 'in', $ids);
                // $result = Db::name('seo_relevant_word')->field('keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where('keyword', 'like', '%'.$kw.'%');

                if ($sort == '1') {
                    $order = "averagePv desc";
                } elseif ($sort == '1s'){
                    $order = "averagePv asc";
                } elseif ($sort == '2') {
                    $order = "averagePvPc desc"; 
                } elseif ($sort == '2s') {
                    $order = "averagePvPc asc";
                } elseif ($sort == '3') {
                    $order = "averagePvMobile desc"; 
                } elseif ($sort == '3s') {
                    $order = "averagePvMobile asc";
                } elseif ($sort == '4') {
                    $order = "averageDayPv desc"; 
                } elseif ($sort == '4s') {
                    $order = "averageDayPv asc";
                } elseif ($sort == '5') {
                    $order = "averageDayPvPc desc"; 
                } elseif ($sort == '5s') {
                    $order = "averageDayPvPc asc";
                } elseif ($sort == '6') {
                    $order = "averageDayPvMobile desc"; 
                } elseif ($sort == '6s') {
                    $order = "averageDayPvMobile asc";
                }
                // 排序
                if ($order && $uid) {
                    // 如果用户已登录,则进行排序
                    $result = $result->order($order)->paginate(20);
                } else {
                   $result = $result->order('averagePv desc')->paginate(20);
                }

                // 违禁词替换开始
                $ban = Db::name('ban')->column('ban');
                $result = $result->each(function($item) use ($ban) {
                    $item['keyword2'] = str_ireplace($ban, '*', $item['keyword']);
                    return $item;
                });
                // 违禁词替换结束
                
                $count = count($result);
                if (empty($info) || empty($recordinfo)) {
                    $kwharr = $this->redis->lrange('XG',0,-1);
                    if (!in_array($kw, $kwharr) && !$result2) {
                        $this->redis->rpush('XG', $kw);
                    }
                } else {
                   if($count == 0){
                        $kwharr = $this->redis->lrange('XG',0,-1);
                        if (!in_array($kw, $kwharr)) {
                            $this->redis->rpush('XG', $kw);
                        }
                    } 
                }
                if ($uid) {
                    $level = Users::userRule($page?:1);//用户权限展示条数
                    $this->assign('level',$level);
                } else {
                    $this->assign('level',2);
                }
                $this->assign('sort', $sort?:1);
                $relorder = Users::userRule(0,0,'relorder');//用户权限排序
                $this->assign('relorder',$relorder);
                $this->assign('on', $count?1:0);
                $this->assign('list', $result);     
                $system = cache('System');
                $headtitle = $kw.' - 长尾词挖掘 - '.$system['name'];
                $this->assign('headtitle',  $headtitle);     
                $this->assign('kw', $kw);     

                $Keyword = $kw.',长尾词挖掘';
                $Desc = $kw.'的长尾关键词挖掘，提供'.$kw.'相关的其他长尾关键词。';
                $this->assign('Keyword', $Keyword);
                $this->assign('Desc',  $Desc);    
                $this->assign('CSS',['/static/home2/css/mob.css','/static/home2/css/order.css']);
                $this->assign('JS',['/static/home2/js/related/queryres.js','/static/home2/js/copy/clipboard.min.js','/static/home2/js/baidupush.js']); 
                // 广告
                $ad = $this->getAd(12);
                $this->assign('ad', $ad);
                
                return $this->fetch();
            } else {
                return $this->redirect('/related');
            }
        } else {
            return $this->redirect('/related');
        }
        if (input('page')> $result->lastPage()) {
           return abort(404, '页面异常');
        } 
    }

    //ajax关键词挖掘结果页面
    // public function ajaxrecord(){
    //     if (Request::isAjax()) {
    //         $kw = trim(input('key'));
    //         $time = 0;
    //         $ids = sphinx($kw, 'relevantword', 5000);
    //         while (true) {
    //             $digList = Db::name('seo_relevant_word')->field('id2,keyword')->where('id2', 'in', $ids)->count();
    //             // $digList  = Db::name('seo_relevant_word')->field('keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where('keyword', 'like', '%'.$kw.'%')->order('averagePv desc')->paginate(20,false,['query' => request()->param()]);
    //             if ($digList > 0) {
    //                 return ['code'=>1];
    //                 break;
    //             } else {
    //                 if ($time>5) {
    //                     return ['code'=>0];
    //                     break;
    //                 }
    //                 $time++;
    //                 sleep(1);
    //             }
    //         }
            
    //     }
    // }

    //导出数据excel
    public function daochu() {
        $uid = session('usersid');
        $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
        $kw = str_replace(' ', '', input('relatedwords'));
        $shuju = input('shuju');
        $sort = input('sort');
        $digkeywords = '搜一搜站长工具_'.$kw.'_长尾词挖掘';
        $field = "
            keyword 相关词,
            averagePv 周搜索量,
            averagePvPc 周PC搜索量,
            averagePvMobile 周移动搜索量,
            averageDayPv 日搜索量,
            averageDayPvPc PC日搜索量,
            averageDayPvMobile 移动日搜索量
        ";

        if ($sort == '1') {
            $order = "averagePv desc";
        } elseif ($sort == '1s'){
            $order = "averagePv asc";
        } elseif ($sort == '2') {
            $order = "averagePvPc desc"; 
        } elseif ($sort == '2s') {
            $order = "averagePvPc asc";
        } elseif ($sort == '3') {
            $order = "averagePvMobile desc"; 
        } elseif ($sort == '3s') {
            $order = "averagePvMobile asc";
        } elseif ($sort == '4') {
            $order = "averageDayPv desc"; 
        } elseif ($sort == '4s') {
            $order = "averageDayPv asc";
        } elseif ($sort == '5') {
            $order = "averageDayPvPc desc"; 
        } elseif ($sort == '5s') {
            $order = "averageDayPvPc asc";
        } elseif ($sort == '6') {
            $order = "averageDayPvMobile desc"; 
        } elseif ($sort == '6s') {
            $order = "averageDayPvMobile asc";
        }

        $ids = sphinx($kw, 'relevantword', 5000);

        $list = Db::name('seo_relevant_word')->where('id2', 'in', $ids)->field($field)->limit($infolist['keyword_shownum'])->order($order)->select();
        $result = Db::name('seo_goods_spend')->where('uid',session('usersid') )->where('keywords',$kw )->where('keytype',2)->value('create_time');
        if (!$result) {
            Db::name('seo_goods_spend')->insert([
                'uid'           =>  session('usersid'),
                'umobile'       =>  session('usersmobile'),
                'gid'           =>  0,
                'content'       =>  $kw.' : 长尾词挖掘导出',
                'spendcode'        =>  $shuju?0:2,
                'create_time'   => time(),
                'keywords'      => $kw,
                'keytype'       => 2,
            ]);
        }
        if (!$shuju) {
            Db::name('users')->where('id', $uid)->setDec('point', 2);
        }
        $this->info2excel($list, $digkeywords);
    }
}