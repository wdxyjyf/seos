<?php
namespace app\home\controller;
use think\Db;
use think\facade\Request;
use app\home\model\Users;

class Keywords extends Common{
	public function initialize(){
        parent::initialize();
    }

    //关键词热度查询页面
    public function hotindex() {
        set_time_limit(0);
        $userinfo = session('userinfo');
        if ($res = $this->redis->get('static_timer:keyword')) {
            $list = json_decode($res, 1);
        } else {
            $limitStart1 = rand(1,5000);
            $limitStart2 = rand(1,2000);
            $limitStart3 = rand(1,5000);
            $limitStart4 = rand(1,5000);
            $ban = Db::name('ban')->column('ban');
            // 搜索榜
            $list1 = db('seo_keyword_hotdig')
                    ->field('id,keyword,averagePv')
                    ->where('char_length(keyword)>=4 and char_length(keyword)<=10')
                    ->limit($limitStart1,40)
                    ->order('averagePv desc')
                    ->select();
            foreach ($list1 as $k=>$v) {
                foreach ($ban as $b) {
                    if (stripos($v['keyword'], $b) !== false) {
                        unset($list1[$k]);
                        continue 2;
                    }
                }
            }
            $list1 = array_slice($list1, 0, 10);
            // 新增词
            $list2 = db('seo_keyword_hotdig')
                    ->field('id,keyword,averagePv')
                    ->where('char_length(keyword)>=4 and char_length(keyword)<=10')
                    ->where('create_time', '>', strtotime('- 3 days'))
                    ->limit(40)
                    ->order('averagePv desc')
                    ->select();
            foreach ($list2 as $k=>$v) {
                foreach ($ban as $b) {
                    if (stripos($v['keyword'], $b) !== false) {
                        unset($list2[$k]);
                        continue 2;
                    }
                }
            }
            $list2 = array_slice($list2, 0, 10);
            // 热门排行榜
            $list3 = db('seo_keyword_hotdig')
                    ->field('id,keyword,averagePv')
                    ->where('char_length(keyword)>=4 and char_length(keyword)<=10')
                    ->limit($limitStart3,40)
                    ->order('averagePv desc')
                    ->select();
            foreach ($list3 as $k=>$v) {
                foreach ($ban as $b) {
                    if (stripos($v['keyword'], $b) !== false) {
                        unset($list3[$k]);
                        continue 2;
                    }
                }
            }
            $list3 = array_slice($list3, 0, 10);
            // 搜索词排行榜
            $list4 = db('seo_keyword_hotdig')
                    ->field('id,keyword,averagePv')
                    ->where('char_length(keyword)>=4 and char_length(keyword)<=10')
                    ->limit($limitStart4,40)
                    ->order('averagePv desc')
                    ->select();
            foreach ($list4 as $k=>$v) {
                foreach ($ban as $b) {
                    if (stripos($v['keyword'], $b) !== false) {
                        unset($list4[$k]);
                        continue 2;
                    }
                }
            }
            $list4 = array_slice($list4, 0, 10);
            $list = [
                    'list1'=>$list1,
                    'list2'=>$list2,
                    'list3'=>$list3,
                    'list4'=>$list4,
                ];
            $this->redis->set('static_timer:keyword', json_encode($list));
            $expireTime = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
            $this->redis->expireAt('static_timer:keyword', $expireTime);
        }

        $ban = Db::name('ban')->column('ban');
        foreach ($list as $k1=>$v1) {
            foreach ($v1 as $k2=>$v2) {
                foreach ($ban as $b) {
                    if (stripos($v2['keyword'], $b) !== false) {
                        unset($v1[$k2]);
                        continue 2;
                    }
                }
            }
            $list[$k1] = array_slice($v1, 0, 10);
        }
        // 违禁词替换开始
        // $ban = Db::name('ban')->column('ban');
        // foreach ($list as $k1=>$v1) {
        //     foreach ($v1 as $k2=>$v2) {
        //         $list[$k1][$k2]['keyword2'] = str_ireplace($ban, '*', $v2['keyword']);
        //     }
        // }
        // 违禁词替换结束

        $this->assign('list1',$list['list1']); 
        $this->assign('list2',$list['list2']); 
        $this->assign('list3',$list['list3']); 
        $this->assign('list4',$list['list4']); 
        $system = cache('System');
        $headtitle = '关键词指数，搜索量查询 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);

        $Keyword = '关键词指数查询,关键词搜索量查询,查关键词搜索量,关键词查询工具';
        $Desc = '在线查询关键词的计算机、移动端的七天日均搜索量，百度官方数据，保证关键词搜索量数据的准确性、权威性！怎么查关键词的搜索量？【搜一搜站长工具】关键词搜索量查询工具。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);
        if ($userinfo) {
            $querynum = Db::name('users_num')->where('userid',$userinfo['id'])->value('keyword_querynum');
            $querynum2 = Db::name('users_rule')->where('groupid',$userinfo['level'])->value('keyword_querynum');
            if ($querynum >= $querynum2) {
                $this->assign('stop', 1);
            }
        }
        $this->assign('CSS',['/static/home2/css/mob.css']);
        $this->assign('JS',['/static/home2/js/keyword/keywordhot.js']);
        return $this->fetch();
    }
    public function toplimit($type) {
        if (!$this->visit()) {
            return ['code'=>0, 'msg'=>'查询次数已达上限,请登录查询更多结果'];
        } else {
            $uid = session('usersid');
            if ($uid) {
                $info = Users::numInfo($uid);//记录用户次数表
                $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
                if ($type == 'keyword_querynum') {
                    if ($info['keyword_querynum'] >= $infolist['keyword_querynum']) {
                        return ['code'=>2];
                    }
                } elseif ($type == 'keyword_plquerynum'){
                    if ($info['keyword_plquerynum'] >= $infolist['keyword_plquerynum']) {
                        return ['code'=>2];
                    }
                }
            }
            $this->success();
        }
    }
    //关键词热度结果页面
    public function hotrecord() {
        if (!$this->visit()) {
            return abort(404, '页面异常');
        } 
        // echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];die;
        // echo $_SERVER['HTTP_REFERER'];die;
        $array = [];
        $keyword = trim(urlsafe_b64decode(input('keyword')));//接收要搜索的值
        if ($keyword) {
            $kw = str_replace(' ', '', $keyword);
        	$kwcount = mb_strlen($kw,'UTF8');
	        if (preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u", $kw) && $kwcount <=15) {
                if (session('usersid')) {
                    $ruleinfo = Users::numInfo(session('usersid'));//记录用户次数表
                    $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
                    if ($ruleinfo['keyword_querynum'] >= $infolist['keyword_querynum']) {
                        return $this->redirect('/keyword');
                    }
                    if (empty($ruleinfo)) {
                        Db::name('users_num')->insert([
                            'userid'           =>  session('usersid'),
                            'keyword_querynum'       => 1,
                        ]);
                    } else {
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
                $recordinfo = Db::name('seo_keyh_records')->where('keywordhotdig',$kw)->find();
			   	$result2 = Db::name('seo_keyh_records')->where(['keywordhotdig'=>$kw,'ipaddress'=>$ip])->max('create_time');
		        if (!$result2 || time() - $result2 > 86400) {
		            $intkeywordwords['keywordhotdig'] = $kw;
		            $intkeywordwords['create_time'] = time();
		            $intkeywordwords['ipaddress'] = getIp();
		            Db::name('seo_keyh_records')->insert($intkeywordwords);//保存客户查询的关键词热度
		        }
		        //从相关表里读取数据
                $result = Db::name('seo_keyword_hotdig')->field('averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where("keyword = '$kw'")->find();
                $ids = sphinx($kw, 'keywordhotdig', 5000);
                $resultlike = Db::name('seo_keyword_hotdig')->field('keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where('id2', 'in', $ids)->order('averagePv desc')->limit(10)->select();

                // 违禁词替换开始
                $ban = Db::name('ban')->column('ban');
                foreach ($resultlike as $k1=>$v1) {
                    $resultlike[$k1]['keyword2'] = str_ireplace($ban, '*', $v1['keyword']);
                }
                // 违禁词替换结束

		        // $resultlike = Db::name('seo_keyword_hotdig')->field('keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where('keyword', 'like', '%'.$kw.'%')->order('averagePv desc')->limit(10)->select();
		        if (empty($result)|| empty($recordinfo)) {
		            //没有数据,存到redis,python爬取
		            $array['word'] = 'all';
		            $array['kw'] = $kw;
                    $kwharr = $this->redis->lrange('KWH',0,-1);
                    if (!in_array(JSON($array), $kwharr)) {
                        $this->redis->rpush('KWH',JSON($array));
                    }
		        }
                $this->assign('on', $result?1:0);
                $this->assign('on2', $resultlike?1:0);
		        $system = cache('System');
		        $headtitle = $kw.' - 关键词查询 - '.$system['name'];
		        $this->assign('headtitle',  $headtitle);
		        $this->assign('hotlist', $result);   
		        $this->assign('hotlistlike', $resultlike);      
		        $this->assign('kw',$kw);  
                $this->assign('CSS',['/static/home2/css/mob.css']);
                $this->assign('JS',['/static/home2/js/keyword/keywordhotres.js','/static/home2/js/baidupush.js']);
		        $Keyword = $kw.','."关键词查询";
		        $Desc = '关键词'.$kw.'搜索量查询 ,相关关键词'.$kw.'的搜索量查询。';
		        $this->assign('Keyword', $Keyword);
                $this->assign('Desc',  $Desc);  
                // 广告
                $ad = $this->getAd(19);
                $this->assign('ad', $ad);

                return $this->fetch();
			} else {
                return $this->redirect('/keyword');
            }
        } else {
            return $this->redirect('/keyword');
        }
    }
    //ajax关键词热度结果页面
    public function ajaxhotrecord(){
        if (Request::isAjax()) {
            $kw = trim(input('keyword'));
            $time = 0;
            while (true) {
                $hotList  = Db::name('seo_keyword_hotdig')->field('averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where('keyword', $kw)->find();
                if ($hotList) break;
                else {
                    if ($time>5) break;
                    $time++;
                    sleep(1);
                }
            }
            return ['code'=>1, 'hotList'=>$hotList];
        }
    }
    // public function xgHotrecord(){
    //     $kw = trim(input('keyword'));
    //     $time2 = 0;
    //     $ids = sphinx($kw, 'keywordhotdig', 5000);
    //     while (true) {
    //         $hotList2  = Db::name('seo_keyword_hotdig')->field('keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where('id2', 'in', $ids)->order('averagePv desc')->limit(10)->select();
    //         // $hotList2  = Db::name('seo_keyword_hotdig')->field('keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where('keyword', 'like', '%'.$kw.'%')->order('averagePv desc')->limit(10)->select();
    //         if ($hotList2) break;
    //         else {
    //             if ($time2>6) break;
    //             $time2++;
    //             sleep(1);
    //         }
    //     }
    //     return ['code'=>1,'hotList2'=>$hotList2];
    // }

    
    //关键词热度批量查询页面
    public function hotpl() {
        if (Request::isAjax()) {
            if (session('usersid')) {
                $ruleinfo = Users::numInfo(session('usersid'));//记录用户次数表
                $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
                if ($ruleinfo['keyword_plquerynum'] >= $infolist['keyword_plquerynum']) {
                    return ['code'=>3, 'msg'=>'今日关键词批量查询次数已达上限，是否升级会员组获取更多次数'];
                }
                if (empty($ruleinfo)) {
                    Db::name('users_num')->insert([
                        'userid'           =>  session('usersid'),
                        'keyword_plquerynum'       => 1,
                    ]);
                } else {
                    if ($ruleinfo['keyword_plquerynum']) {
                        if ($ruleinfo['keyword_plquerynum'] < $infolist['keyword_plquerynum']) {
                            $ruleinfo['keyword_plquerynum'] += 1;
                            Db::name('users_num')->where('id',$ruleinfo['id'])->setField('keyword_plquerynum', $ruleinfo['keyword_plquerynum']);
                        } 
                    } else {
                        $ruleinfo['keyword_plquerynum'] = 1;
                        Db::name('users_num')->where('id',$ruleinfo['id'])->setField('keyword_plquerynum', $ruleinfo['keyword_plquerynum']);
                    }
                }
            }
            $kwharr = $this->redis->lrange('KWH',0,-1);
            $content = input('plhotkey');
            $keyword_plnum = input('keyword_plnum');
            $arr = array_unique($content);
            if (count($arr) > $keyword_plnum) return ['code' => 0, 'msg' => '最多填写'.$keyword_plnum.'行'];
            foreach ($arr as $k => $v) {
                $res = Db::name('seo_keyword_hotdig')->field('keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where('keyword',$v)->find();
                if(empty($res)){
                    $array['word'] = 'all';
                    $array['kw'] = $v;
                    if (!in_array(JSON($array), $kwharr)) {
                        $this->redis->rpush('KWH',JSON($array));
                    }
                    $info[$k+50] = ['keyword'=> $v, 'no'=>1];
                    $mcd[] = $v;
                }else{
                    $info[$k] = $res;
                }
            }
            $info = array_values($info);
            return ['code'=>1, 'list'=>$info,'tit'=>'关键词热度批量查询结果','mcd'=>$mcd];
        }
        if ($res = $this->redis->get('static_timer:keywords')) {
            $list = json_decode($res, 1);
        } else {
            $limitStart = rand(1,5000);
            $list = db('seo_keyword_hotdig')
                    ->where('char_length(keyword)>=4 and char_length(keyword)<=10')
                    ->order('averagePv desc')
                    ->limit($limitStart,40)
                    ->select();

            $this->redis->set('static_timer:keywords', json_encode($list));
            $expireTime = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
            $this->redis->expireAt('static_timer:keywords', $expireTime);
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
        //     $list[$k1]['keyword2'] = str_ireplace($ban, '*', $v1['keyword']);
        // }
        // 违禁词替换结束

        $dgorder = Users::userRule(0,0,'dgorder');//用户权限排序
        $this->assign('dgorder',$dgorder);
        $this->assign('list',$list);   
        $this->assign('tit', '热门关键词');
        $system = cache('System');
        $headtitle = '批量关键词指数，搜索量查询 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);   
        $Keyword = '关键词指数查询,关键词搜索量查询,查关键词搜索量,批量关键词查询工具';
        $Desc = '在线批量查询关键词的计算机、移动端的七天日均搜索量，百度官方数据，保证关键词搜索量数据的准确性、权威性！怎么批量查关键词的搜索量？【搜一搜站长工具】关键词搜索量查询工具。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $keyword_plsubmit = $this->querrplnum('keyword_plsubmit');
        $this->assign('keyword_plsubmit',$keyword_plsubmit);
        $this->assign('CSS',['/static/home2/css/mob.css','/static/home2/css/order.css']);
        $this->assign('JS',['/static/home2/js/keyword/keywordhotpl.js']);
        // 广告
        $ad = $this->getAd(20);
        $this->assign('ad', $ad);

        return $this->fetch(); 
    }

    public function hotplb() {
        $mcd = input('mcd');
        if (!$mcd) return ['code'=>0];
        $time = 0;
        while(true) {
            $data = [];
            foreach ($mcd as $k=>$m) {
                $res = db('seo_keyword_hotdig')->field('keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where('keyword', $m)->find();
                if ($res) {
                    $data[] = $res;
                    unset($mcd[$k]);
                }
            }
            if (!$data) {
                sleep(1);
                $time++;
                if ($time > 8) {
                    return ['code'=>0];
                    break;
                } 
            } else {
                return ['code'=>1, 'info'=>$data, 'mcd'=>$mcd];
                break;
            }
        }
    }

    //关键词挖掘页面
    public function digindex() {
        $userinfo = session('userinfo');
        if ($userinfo) {
            $querynum = Db::name('users_num')->where('userid',$userinfo['id'])->value('keyword_querynum');
            $querynum2 = Db::name('users_rule')->where('groupid',$userinfo['level'])->value('keyword_querynum');
            if ($querynum >= $querynum2) {
                $this->assign('stop', 1);
            }
        }
        // if (!$list = session('digindex_list')) {
        //     $list = Db::name('seo_keyword_hotdig')->field('id,keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->limit(20,10)->order('averagePv desc')->select();
        //     session('digindex_list', $list);
        // }
        if ($res = $this->redis->get('static_timer:dig')) {
            $list = json_decode($res, 1);
        } else {
            $limitStart = rand(1,5000);
            $list = db('seo_keyword_hotdig')
                    ->where('char_length(keyword)>=4 and char_length(keyword)<=10')
                    ->order('averagePv desc')
                    ->limit($limitStart,40)
                    ->select();
            
            $this->redis->set('static_timer:dig', json_encode($list));
            $expireTime = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
            $this->redis->expireAt('static_timer:dig', $expireTime);
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
        $headtitle = '相关词挖掘 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);     
        $Keyword = '相关词挖掘,关键词挖掘,关键词挖掘工具';
        $Desc = '在线网站关键词挖掘工具，快速挖掘网站相关关键词，支持pc电脑端和移动端关键词挖掘，【搜一搜站长工具】相关词挖掘工具。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/order.css']);
        $this->assign('JS',['/static/home2/js/keyword/keywordig.js']);
        return $this->fetch();
    }

    //关键词挖掘结果页面
    public function digrecord() {
        if (!$this->visit()) {
           return abort(404, '页面异常');
        } 
        $uid = session('usersid');
        $numinfo = Users::numInfo($uid);//记录用户次数表
        $keyword = trim(urlsafe_b64decode(input('keyword')));//接收要搜索的值
        $array = [];
        $page = input('page')?:1;
        $this->assign('page', $page);
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
	                        return $this->redirect('/dig');
	                    }
	                    if (empty($ruleinfo)) {
	                        Db::name('users_num')->insert([
	                            'userid'=> $uid,
	                            'keyword_querynum'=>1,
	                        ]);
	                    } else {
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
                }
                $ip = getIp();
                $recordinfo = Db::name('seo_keyd_records')->where('keywordhotdig',$kw)->find();
        		$result2 = Db::name('seo_keyd_records')->where(['keywordhotdig'=>$kw,'ipaddress'=> $ip])->max('create_time');
		        if (!$result2 || time() - $result2 > 86400) {
		            $intkeywordwords['keywordhotdig'] = $kw;
		            $intkeywordwords['create_time'] = time();
		            $intkeywordwords['ipaddress'] = getIp();
		            Db::name('seo_keyd_records')->insert($intkeywordwords);//保存客户查询的关键词热度
		        }
                $array['word'] = 'all';
                $array['kw'] = $kw;
                $kwharr = $this->redis->lrange('KWH',0,-1);
		        $info = Db::name('seo_keyword_hotdig')->where('keyword',$kw)->find();
                
                $ids = sphinx($kw, 'keywordhotdig', 5000);
                if (count($ids) == 5000) {
                    $this->assign('maxPage', 250);
                }
                $result = Db::name('seo_keyword_hotdig')->field('keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile,showReasons')->where('id2', 'in', $ids);
                $this->assign('sort', $sort?:1);
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
                if ($order && $uid) {
                    $result = $result->order($order)->paginate(20);
                } else{
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
                    if (!in_array(JSON($array), $kwharr)) {
                        $this->redis->rpush('KWH',JSON($array));
                    }
                } else {
                    if ($count == 0) {
                        if (!in_array(JSON($array), $kwharr)) {
                            $this->redis->rpush('KWH',JSON($array));
                        }
                    }
                }
		        
                // 用户登录与未登录展示条数
                if ($uid) {
                    $level = Users::userRule($page?:1);//用户权限展示条数
                    $this->assign('level',$level);
                } else {
                    $this->assign('level',2);
                }
                $dgorder = Users::userRule(0,0,'dgorder');//用户权限排序
                $this->assign('dgorder',$dgorder);

                $this->assign('on', $count?1:0);
		        $this->assign('list', $result); 
		        $system = cache('System');
		        $headtitle = $kw.' - 相关词挖掘 - '.$system['name'];
		        $this->assign('headtitle',  $headtitle); 
		        $this->assign('kw',  $kw);             
		        $Keyword = $kw.',关键词挖掘';
                $Desc = $kw.'的相关词挖掘，提供'.$kw.'相关的其他长尾关键词。';
		        $this->assign('Keyword', $Keyword);
		        $this->assign('Desc',  $Desc); 
                $this->assign('CSS',['/static/home2/css/order.css']);
                $this->assign('JS',['/static/home2/js/keyword/keywordigres.js','/static/home2/js/copy/clipboard.min.js','/static/home2/js/baidupush.js']);
	        } else {
                $this->redirect('/dig');
            }
	    } else {
            $this->redirect('/dig');
        }
        if (input('page')> $result->lastPage()) {
            return abort(404, '页面异常');
        }
        // 广告
        $ad = $this->getAd(11);
        $this->assign('ad', $ad);

	    return $this->fetch();
    }
    //ajax关键词挖掘结果页面
    // public function ajaxdigrecord(){
    //     if (Request::isAjax()) {
    //         $kw = trim(input('keyword'));
    //         $time = 0;
    //         $ids = sphinx($kw, 'keywordhotdig', 5000);
    //         while (true) {
    //             $digList = Db::name('seo_keyword_hotdig')->field('keyword,id2')->where('id2', 'in', $ids)->count();
    //             // $digList  = Db::name('seo_keyword_hotdig')->field('keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where('keyword', 'like', '%'.$kw.'%')->order('averagePv desc')->select();
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

    //竞价查询页面
    public function jjcxindex() { 
        if ($res = $this->redis->get('static_timer:compete')) {
            $list = json_decode($res, 1);
        } else {
            $limitStart = rand(1,5000);
            $list = db('seo_keyword_hotdig')
                    ->field('keyword,recommendPricePc,recommendPriceMobile,competition,showReasons')
                    ->where('char_length(keyword)>=4 and char_length(keyword)<=10')
                    ->order('averagePv desc')
                    ->limit($limitStart,40)
                    ->select();
            $this->redis->set('static_timer:compete', json_encode($list));
            $expireTime = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
            $this->redis->expireAt('static_timer:compete', $expireTime);
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
        foreach ($list as $k=>$v) {
            $list[$k]['xgnum'] = count(sphinx($v['keyword'], 'website_info_t', 1000));
        }
        // 违禁词替换开始
        // $ban = Db::name('ban')->column('ban');
        // foreach ($list as $k=>$v) {
        //     $list[$k]['keyword2'] = str_ireplace($ban, '*', $v['keyword']);
        // }
        // 违禁词替换结束
        $userinfo = session('userinfo');
        if ($userinfo) {
            $querynum = Db::name('users_num')->where('userid',$userinfo['id'])->value('keyword_querynum');
            $querynum2 = Db::name('users_rule')->where('groupid',$userinfo['level'])->value('keyword_querynum');
            if ($querynum >= $querynum2) {
                $this->assign('stop', 1);
            }
        }
        $this->assign('list',$list); 
        $system = cache('System');
        $headtitle = '竞价查询 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);     
        $Keyword = '竞价查询';
        $Desc = '竞价关键词查询工具,可以挖掘关键词的竞价价格，竞价公司数量，难度以及是否高质量关键词词；为企业网站优化提供更有力的参考';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc); 
        $this->assign('CSS',['/static/home2/css/mob.css']);
        $this->assign('JS',['/static/home2/js/keyword/keywordprice.js']);
        return $this->fetch();
    }
    //竞价查询结果页面
    public function jjcxrecord() { 
        if (!$this->visit()) {
            return abort(404, '页面异常');
        } 
        $keyword = trim(urlsafe_b64decode(input('keywords')));//接收要搜索的值
        if ($keyword) {
            $kw = str_replace(' ', '', $keyword);
        	$kwcount =  mb_strlen($kw,'UTF8');
	        if (preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u", $kw) && $kwcount <=15) {
                if (session('usersid')) {
                    $ruleinfo = Users::numInfo(session('usersid'));//记录用户次数表
                    $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
                    if ($ruleinfo['keyword_querynum'] >= $infolist['keyword_querynum']) {
                        return $this->redirect('/compete');
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
                $ip = getIp();
                $recordinfo = Db::name('seo_keyjj_records')->where('keywordhotdig',$kw)->find();
                $result2 = Db::name('seo_keyjj_records')->where(['keywordhotdig'=>$kw,'ipaddress'=>$ip])->max('create_time');
                if (!$result2 || time() - $result2 > 86400) {
                    $intkeywordwords['keywordhotdig'] = $kw;
                    $intkeywordwords['create_time'] = time();
                    $intkeywordwords['ipaddress'] = getIp();
                    Db::name('seo_keyjj_records')->insert($intkeywordwords);//保存客户查询的关键词热度
                }
                $array = [];
                //从相关表里读取数据
                $result = Db::name('seo_keyword_hotdig')->field('recommendPricePc,recommendPriceMobile,competition,showReasons')->where('keyword',$kw)->find();
                $this->assign('on', $result?1:0);
             
                $ids = sphinx($kw, 'keywordhotdig', 5000);
                $resultlike = Db::name('seo_keyword_hotdig')->field('keyword,recommendPricePc,recommendPriceMobile,competition,showReasons')->where('id2', 'in', $ids)->order('averagePv desc')->limit(10)->select();
                $this->assign('on2', $resultlike?1:0);
                // 违禁词替换开始
                $ban = Db::name('ban')->column('ban');
                foreach ($resultlike as $k=>$v) {
                    $resultlike[$k]['keyword2'] = str_ireplace($ban, '*', $v['keyword']);
                    $resultlike[$k]['xgnum'] = count(sphinx($v['keyword'], 'website_info_t', 1000));
                }
                // 违禁词替换结束
                if (empty($result) || empty($recordinfo)) {
                    $array['word'] = 'all';
                    $array['kw'] = $kw;
                    $kwharr = $this->redis->lrange('KWH',0,-1);
                    if (!in_array(JSON($array), $kwharr)) {
                        $this->redis->rpush('KWH',JSON($array));
                    }
                }

                $result['xgnum'] = count(sphinx($kw, 'website_info_t', 1000));
                $system = cache('System');
                $headtitle = $kw.' - 竞价查询 - '.$system['name'];
                $this->assign('headtitle',  $headtitle);
                $this->assign('jjlist', $result);   
                $this->assign('jjlistlike', $resultlike);  
                $this->assign('kw', $kw);     
                $Keyword = '竞价查询';
                $Desc = '关键词'.$kw.'相关竞价关键词查询结果,竞价网站查询。';
                $this->assign('Keyword', $Keyword);
                $this->assign('Desc',  $Desc);  
                $this->assign('CSS',['/static/home2/css/mob.css']);
                $this->assign('JS',['/static/home2/js/keyword/keywordprice2.js','/static/home2/js/baidupush.js']); 
                return $this->fetch();
            } else {
                return $this->redirect('/compete');
            }
        } else {
            return $this->redirect('/compete');
        }  
    }
    //ajax查询竞价结果
    public function bidPrice(){
        if (Request::isAjax()) {
            $kw = trim(input('keywords'));
            $priceList = Db::name('seo_keyword_hotdig')->field('recommendPricePc,recommendPriceMobile,competition,showReasons')->where('keyword', $kw)->find();
            if (!$priceList) {
                $time = 0;
                while (true) {
                    $priceList  = Db::name('seo_keyword_hotdig')->field('recommendPricePc,recommendPriceMobile,competition,showReasons')->where('keyword', $kw)->find();
                    if ($priceList) break;
                    else {
                        if ($time>5) break;
                        $time++;
                        sleep(1);
                    }
                }
            } 
            $priceList2 = Db::name('seo_keyword_hotdig')->field('keyword,recommendPricePc,recommendPriceMobile,competition,showReasons')->where('keyword', 'like', '%'.$kw.'%')->order('averagePv desc')->limit(10)->select();
            if (!$priceList2) {
                $time = 0;
                while (true) {
                    $priceList2  = Db::name('seo_keyword_hotdig')->field('keyword,recommendPricePc,recommendPriceMobile,competition,showReasons')->where('keyword', 'like', '%'.$kw.'%')->order('averagePv desc')->limit(10)->select();
                    if ($priceList2) break;
                    else {
                        if ($time>5) break;
                        $time++;
                        sleep(1);
                    }
                }
            } 
            // 违禁词替换开始
            $ban = Db::name('ban')->column('ban');
            foreach ($priceList2 as $k=>$v) {
                $priceList2[$k]['keyword'] = str_ireplace($ban, '*', $v['keyword']);
                $priceList2[$k]['xgnum'] = count(sphinx($v['keyword'], 'website_info_t', 1000));
            }
            // 违禁词替换结束
            return ['code'=>1, 'priceList'=>$priceList, 'priceList2'=>$priceList2];
        }
    }

    //导出数据excel
    public function daochu() {
        $uid = session('usersid');
        $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
        
        $kw = str_replace(' ', '', input('digkeywords'));
        $shuju = input('shuju');
        $sort = input('sort');

        $digkeywords = '搜一搜站长工具_'.$kw.'_相关词挖掘';
        $field = "
            keyword 关键词,
            averagePv 周搜索量,
            averagePvPc 周PC搜索量,
            averagePvMobile 周移动搜索量,
            averageDayPv 日搜索量,
            averageDayPvPc PC日搜索量,
            averageDayPvMobile 移动日搜索量,
            showReasons SEO理由
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

        $ids = sphinx($kw, 'keywordhotdig', 5000);
        $list = Db::name('seo_keyword_hotdig')->where('id2', 'in', $ids)->field($field)->limit($infolist['keyword_shownum'])->order($order)->select();
        $result = Db::name('seo_goods_spend')->where('uid',session('usersid') )->where('keywords',$kw )->where('keytype',1 )->value('create_time');
        if (!$result) {
            Db::name('seo_goods_spend')->insert([
                'uid'           =>  session('usersid'),
                'umobile'       =>  session('usersmobile'),
                'gid'           =>  0,
                'content'       =>  $kw.' : 相关词挖掘导出',
                'spendcode'     =>  $shuju?0:2,
                'create_time'   => time(),
                'keywords'      => $kw,
                'keytype'       => 1,
            ]);
        }
        if (!$shuju) {
            Db::name('users')->where('id', $uid)->setDec('point', 2);
        }
        $this->info2excel($list, $digkeywords);
    }

    //关键词查询结果扣除积分
    public function downlog() {
        if (Request::isAjax()) {
            $keyword = input('kw');
            $uid = session('usersid');
            $result = Db::name('seo_goods_spend')->where('uid',$uid )->where('keywords',$keyword)->where('keytype',1)->max('create_time');
            if (!$result) return ['code'=>1];//扣除积分操作
            else return ['code'=>time() - $result > 86400?1:0];
        }
    }

    //相关网站结果页面
    public function xgwebsite() {
        if (!$this->visit()) {
            return abort(404, '页面异常');
        } 
        $keyword = trim(urlsafe_b64decode(input('xgkeyword')));//接收要搜索的值
        if ($keyword) {
            $kw = str_replace(' ', '', $keyword);
            if (preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u", $kw)) {
                $ids = sphinx($kw, 'website_info_t', 1000);
                $result = Db::name('seo_website_info')->field('website_url,title,left(create_time,10) create_time,jj,(case 
                    when locate("亿",baidu_include) > 0 then cast(replace(replace(baidu_include,"亿",""),"万","000000") as signed)
                    else cast(replace(baidu_include,",","") as signed) end
                    ) as baidu_include')->where('id', 'in', $ids)->order('baidu_include desc')->select();
                $limit = 50;
                $page = input('page')?input('page'):1;
                $page = $page>10?1:$page;
                $result = page_array($limit, $page, $result);
                // 违禁词替换开始
                $ban = Db::name('ban')->column('ban');
                foreach ($result as $k=>$v) {
                    $result[$k]['title'] = str_ireplace($kw, "<em>{$kw}</em>", $v['title']);
                    $result[$k]['title'] = str_ireplace($ban, '*', $result[$k]['title']);
                }
                // 违禁词替换结束
                $fenye = Db::name('seo_website_info')->field('id')->where('id', 'in', $ids)->paginate($limit);
                //渲染
                $this->assign('list', $result); 
                $this->assign('fenye', $fenye);
                $this->assign('page', $page);
                $this->assign('limit', $limit);
                $system = cache('System');
                $headtitle = $kw.' - 相关网站 - '.$system['name'];
                $this->assign('headtitle',  $headtitle);  
                $Keyword = $kw.'相关网站,'.$kw.'网站排名';
                $Desc = $kw.'相关网站列表，根据网站综合值进行筛选排名结果，通过'.$kw.'可以看到每个'.$kw.'网站里面的优质网站有哪些。';
                $this->assign('Keyword', $Keyword);
                $this->assign('Desc',  $Desc); 
                $this->assign('kw',  $kw);   
                $this->assign('CSS',['/static/home2/css/order.css']);
                $this->assign('JS',['/static/home2/js/keyword/xgwebsite.js','/static/home2/js/copy/clipboard.min.js','/static/home2/js/baidupush.js']);
            } else {
                return abort(404, '页面异常');
            }
        } else {
            return abort(404, '页面异常');
        }
        return $this->fetch();
    }

    //相关网站导出数据excel
    public function xgwebexprot() {
        $kw = str_replace(' ', '', input('xgkeyword'));
        $shuju = input('shuju');
        $field = "
            website_url 网址,
            title 标题,
            IFNULL(baidu_include,0) 收录量,
            left(create_time, 10) 添加时间
        ";
        $xgkeyword = '搜一搜站长工具_'.$kw.'_相关网站导出';
        $ids = sphinx($kw, 'website_info_t', 1000);
        $list = Db::name('seo_website_info')->field($field)->where('id', 'in', $ids)->order('baidu_include desc')->select();
        $this->info2excel($list, $xgkeyword);
    }

    // 缓存id
    public function cacheIds(){
        set_time_limit(300);
        $stime = time();
        $num = 15;
        $ids = Db::name('seo_keyword_hotdig')
                ->where('char_length(keyword)>=4 and char_length(keyword)<=10')
                ->where('create_time', '>', strtotime('- 3 months'))
                ->order('averagePv DESC')
                ->limit(2000)
                ->column('id2');
        // 搜索榜
        $keys1 = array_rand($ids, $num);
        $ids1 = [];
        for($i=0; $i<$num; $i++){
            $ids1[] = $ids[$keys1[$i]];
        }
        $list1 = Db::name('seo_keyword_hotdig')->field('id,keyword,averagePv')->where('id2', 'in', $ids1)->order('averagePv DESC')->select();

        // 新增词
        $xzids = Db::name('seo_keyword_hotdig')
                ->where('char_length(keyword)>=4 and char_lengthf(keyword)<=10')
                ->where('create_time', '>', strtotime('- 3 days'))
                ->order('averagePv DESC')
                ->limit(2000)
                ->column('id2');
        $keys2 = array_rand($xzids, $num);
        $ids2 = [];
        for($i=0; $i<$num; $i++){
            $ids2[] = $xzids[$keys2[$i]];
        }
        $list2 = Db::name('seo_keyword_hotdig')->field('id,keyword,averagePv')->where('id2', 'in', $ids2)->order('averagePv DESC')->select();

        // 热门排行榜
        $keys3 = array_rand($ids, $num);
        $ids3 = [];
        for($i=0; $i<$num; $i++){
            $ids3[] = $ids[$keys3[$i]];
        }
        $list3 = Db::name('seo_keyword_hotdig')->field('id,keyword,averagePv')->where('id2', 'in', $ids3)->order('averagePv DESC')->select();

        // 搜索词排行榜
        $keys4 = array_rand($ids, $num);
        $ids4 = [];
        for($i=0; $i<$num; $i++){
            $ids4[] = $ids[$keys4[$i]];
        }
        $list4 = Db::name('seo_keyword_hotdig')->field('id,keyword,averagePv')->where('id2', 'in', $ids4)->order('averagePv DESC')->select();
        $list = [
            'list1'=>$list1,
            'list2'=>$list2,
            'list3'=>$list3,
            'list4'=>$list4,
        ];
        $this->redis->set('static_timer:keyword', json_encode($list));

        // 批量关键词
        $keys5 = array_rand($ids, $num);
        $ids5 = [];
        for($i=0; $i<$num; $i++){
            $ids5[] = $ids[$keys5[$i]];
        }
        $list5 = Db::name('seo_keyword_hotdig')->field('id,keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where('id2', 'in', $ids5)->order('averagePv DESC')->select();
        $this->redis->set('static_timer:keywords', json_encode($list5));

        // 相关词挖掘
        $keys6 = array_rand($ids, $num);
        $ids6 = [];
        for($i=0; $i<$num; $i++){
            $ids6[] = $ids[$keys6[$i]];
        }
        $list6 = Db::name('seo_keyword_hotdig')->field('id,keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where('id2', 'in', $ids6)->order('averagePv DESC')->select();
        $this->redis->set('static_timer:dig', json_encode($list6));

        // 长尾词挖掘
        $cwids = Db::name('seo_relevant_word')
                ->where('create_time', '>', strtotime('- 1 month'))
                ->order('averagePv DESC')
                ->limit(2000)
                ->column('id2');
        $keys7 = array_rand($cwids, $num);
        $ids7 = [];
        for($i=0; $i<$num; $i++){
            $ids7[] = $ids[$keys7[$i]];
        }
        $list7 = Db::name('seo_keyword_hotdig')->field('id,keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where('id2', 'in', $ids7)->order('averagePv DESC')->select();
        $this->redis->set('static_timer:related', json_encode($list7));

        // 竞价查询
        $keys8 = array_rand($ids, $num);
        $ids8 = [];
        for($i=0; $i<$num; $i++){
            $ids8[] = $ids[$keys8[$i]];
        }
        $list8 = Db::name('seo_keyword_hotdig')->field('keyword,recommendPricePc,recommendPriceMobile,competition,showReasons')->where('id2', 'in', $ids8)->select();
        $this->redis->set('static_timer:compete', json_encode($list8));
        $spend = time()-$stime;
        echo "缓存成功，消耗".$spend.'s，执行时间：'.date('Y-m-d H:i:s');
    }

    public function test1()
    {
        set_time_limit(0);
        ini_set('memory_limit','1024M');
        $list = Db::name('seo_website')->field('website_url')->order('id')->limit(950000,50000)->select();
        $this->info2excel($list, '域名20');
    }
}