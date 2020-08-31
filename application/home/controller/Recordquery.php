<?php
namespace app\home\controller;

use think\Db;
use think\facade\Request;
use app\home\model\Users;
use app\home\model\City;
use app\home\controller\Pagess;

class Recordquery extends Common{
	public function initialize(){
        parent::initialize();
    }
    //备案查询页面
    public function rselect(){
        $userinfo = session('userinfo');
        $limitStart = rand(1,10000);
        $list = Db::name('seo_website_info')->field('website_url')->order('id desc')->limit($limitStart, 12)->select();
        $system = cache('System');
        $headtitle = '网站备案查询 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);  
        $Keyword = '备案查询，网站备案查询，icp备案查询';
        $Desc = '为站长提供便捷的网站备案查询工具，本工具更新查询速度快，方便站长实时查询网站备案情况，用户可以通过域名查询该域名是否有备案及相关的ICP备案许可信息。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('list',  $list);  
        if ($userinfo) {
            $querynum = Db::name('users_num')->where('userid',$userinfo['id'])->value('beian_querynum');
            $querynum2 = Db::name('users_rule')->where('groupid',$userinfo['level'])->value('beian_querynum');
            if ($querynum >= $querynum2) {
                $this->assign('stop', 1);
            }
        } 
        $this->assign('CSS',['/static/home2/css/mob.css']);
        $this->assign('JS',['/static/home2/js/record/query.js']); 
        return $this->fetch();
    }
    public function toplimit($type){
        if(!$this->visit()){
            return ['code'=>0, 'msg'=>'查询次数已达上限,请登录查询更多结果'];
        }else{
            $uid = session('usersid');
            if ($uid) {
                $info = Users::numInfo($uid);//记录用户次数表
                $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
                if ($type == 'beian_querynum') {
                    if ($info['beian_querynum'] >= $infolist['beian_querynum']) {
                        return ['code'=>2];
                    }
                } elseif ($type == 'beian_plquerynum'){
                    if ($info['beian_plquerynum'] >= $infolist['beian_plquerynum']) {
                        return ['code'=>2];
                    }
                } elseif ($type == 'includ_querynum') {
                    if ($info['includ_querynum'] >= $infolist['includ_querynum']) {
                        return ['code'=>2];
                    }
                }
            }
            $this->success();
        }
    }

    //备案查询结果页面
    public function urlrecord(){ 
        session_write_close();
        if (!$this->visit()) {
            return abort(404, '页面异常');
        } 
        if (Request::isAjax()) {
            $data['topurl'] = input('topurl');
            $onearr = input('onearr');
            if (ipmatch($onearr)) {
                $urlarr = "'".$onearr."'";
            } else {
                $urlarr = "'".$onearr."'".','."'www.".$data['topurl']."'";
            }
            if (input('update')) {
                $data['update'] = 1;
                $create_time = Db::query($sql = "select create_time from seo_website where website_url in (".$urlarr.")")[0]['create_time'];
            }
            $this->redis->zadd('BEIAN',10000,JSON($data));
            $time = 0;
            while (true) {
                if ($result = Db::query($sql = "select website_url,status_time,record_num,nature,name,create_time from seo_website where website_url in (".$urlarr.")")[0]) {
                    if (input('update')) {
                        if ($result['create_time'] == $create_time) {
                            sleep(1);
                            $time++;
                        } else {
                            break;
                        }
                    } else {
                        if (!$result['name']) {
                            sleep(1);
                            $time++;
                        } else {
                            break;
                        }
                    }
                } else {
                    sleep(1);
                    $time++;
                }
                if ($time>4) {
                    $result = [];break;
                }
            }
            return ['code'=>1, 'redlist'=>$result];
        }
        $kw = endurl(trim(strip_tags(input('url'))));//接收要搜索的值
        if (session('usersid')) {
            $ruleinfo = Users::numInfo(session('usersid'));//记录用户次数表
            $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
            if ($ruleinfo['beian_querynum'] >= $infolist['beian_querynum']) {
                return $this->redirect('/beian');
            }
            if (empty($ruleinfo)) {
                Db::name('users_num')->insert([
                    'userid'  =>  session('usersid'),
                    'beian_querynum'=> 1,
                ]);
            } else {
                if ($ruleinfo['beian_querynum']) {
                    if ($ruleinfo['beian_querynum'] < $infolist['beian_querynum']) {
                        $ruleinfo['beian_querynum'] += 1;
                        Db::name('users_num')->where('id',$ruleinfo['id'])->setField('beian_querynum', $ruleinfo['beian_querynum']);
                    } 
                } else {
                    $ruleinfo['beian_querynum'] = 1;
                    Db::name('users_num')->where('id',$ruleinfo['id'])->setField('beian_querynum', $ruleinfo['beian_querynum']);
                }
            }
        }
        if (urlmatch($kw) || ipmatch($kw)) {
            if ($url = urlmatch($kw)) {
                $urlarr = "'".$url."'".','."'www.".$url."'";
                $topurl = $url;
            } else {
                $urlarr = "'".$kw."'";
                $topurl = $kw;
            }
            $result = Db::query($sql = "select id,website_url,status_time,record_num,nature,name,create_time from seo_website where website_url in (".$urlarr.")")[0];
            if ($result['record_num']) {
                if(strpos($result['record_num'],'-') !== false){ 
                    $record_num = substr($result['record_num'],0,strrpos($result['record_num'],"-"));
                }else{
                     $record_num = $result['record_num'];
                }
               
                // $otherids = sphinx($record_num, 'website', 5000);
                // if ($otherids) {
                //     foreach($otherids as $k=>$v) {
                //         if($result['id'] == $v) unset($otherids[$k]);
                //     }
                //     if (empty($otherids)) {
                //         $other = 0;
                //     } else {
                //         for($i=0;$i<count($otherids);$i++){
                //             $uname = $uname."'".$otherids[$i]."',";
                //         }
                //         $the_uname ="id in(".$uname."'')";
                //         $otherlist = Db::query($sql = "select website_url,record_num,nature,name,left(status_time,10) status_time,left(start_time,10) start_time,left(end_time,10) end_time from seo_website where ".$the_uname." order by status_time desc");
                //         $other = 1;
                //     }
                // } else {
                //     $other = 0;
                // }
                // dump($otherids);die;
               
                $otherlist = Db::query($sql = "select website_url,record_num,nature,name,left(status_time,10) status_time,left(start_time,10) start_time,left(end_time,10) end_time from seo_website where id != '".$result['id']."' and record_num like '{$record_num}%' order by status_time desc");

            } 
            $this->assign('other', $otherlist?1:0);
            $this->assign('otherlist', $otherlist);
            $this->assign('on', $result?1:0);
            $system = cache('System');
            $headtitle = $kw.' - 网站备案查询 - '.$system['name'];
            $limitStart = rand(1,10000);
            $list = Db::name('seo_website_info')->field('website_url')->order('id desc')->limit($limitStart, 12)->select();
            $this->assign('list',  $list);
            $this->assign('headtitle',  $headtitle);
            $this->assign('urlinfo',$result);    
            $this->assign('kw',$kw);  
            $this->assign('onearr',$kw);  
            $this->assign('topurl',$topurl);  

            $Keyword = '备案查询，网站备案查询，icp备案查询';
            $Desc =  $kw. $result['name'] .' 网站ICP备案查询信息，网站备案查询结果。';
            $this->assign('Keyword', $Keyword);
            $this->assign('Desc',  $Desc);    
            $this->assign('CSS',['/static/home2/css/mob.css']);
            $this->assign('JS',['/static/home2/js/record/queryres.js','/static/home2/js/baidupush.js']);  
            return $this->fetch();
        } else {
            return abort(404, '页面异常');
        }
    }

    // public function xgWebsite(){
    //     $kw = trim(input('keyword'));
    //     $time2 = 0;
    //     $otherids = sphinx($kw, 'website', 5000);
    //     while (true) {
    //         $oarr = implode(',', $otherids);
    //         $otherlist = Db::name('seo_website')->field('website_url,record_num,nature,name,left(status_time,10) status_time,left(start_time,10) start_time,left(end_time,10) end_time')->where('id', 'in', $otherids)->select();
    //         // $otherlist = Db::query($sql = "select website_url,record_num,nature,name,left(status_time,10) status_time,left(start_time,10) start_time,left(end_time,10) end_time from seo_website where (id in (".$oarr."))");
    //         if ($otherlist) break;
    //         else {
    //             if ($time2>5) break;
    //             $time2++;
    //             sleep(1);
    //         }
    //     }
    //     return ['code'=>1,'otherlist'=>$otherlist];
    // }

    //最新域名分页列表
    public function datapage() {
        $beianlist = Db::name('seo_website')->field('website_url,record_num,nature,name,status_time')
            ->where('name','not null')
            ->where('nature','not null')
            ->where('nature','not null')
            ->where('nature','not null')
            ->order('id desc')
            ->paginate(10,false,['query' => request()->param()]);
        $this->assign('beianlist',$beianlist);
        return $this->fetch();
    }
    //最新备案域名
    public function newrecord(){
        $system = cache('System');
        // 每页数据
        $showrow = 20;
        // 接受表单值
        $page = input('page')?:1;
        if (session('userinfo')) {
            $beian_maxpage = Db::name('users_rule')->where('groupid', session('userinfo.level'))->value('beian_maxpage');
        } else {
            $beian_maxpage = 10;
        }
        $this->assign('beian_maxpage', $beian_maxpage);
        $page = $page > $beian_maxpage ? $beian_maxpage : $page;
        $city = input('city');
        $nature = input('nature');
        $beiantime = input('beiantime');
        $startime = input('startime');
        $endtime = input('endtime');
        $url_name = input('url_name');
        // 分页跳转的url 
        $url = "?page={page}";
        // sql查询条件
        $where = '1=1';
        // 如果存在省市筛选条件
        if ($city) {
            if ($city != '全国') {
                $shortname = City::provinceShort($city);
                // $where .= ' and (name like "%'.$city.'%" or record_num like "%'.$shortname.'%")'; 
                $ids1 = sphinx($city, 'website', 5000);
                $ids2 = sphinx($shortname, 'website', 5000);
                $ids3 = array_keys(array_flip($ids1)+array_flip($ids2));
                // $where .= ' and (id in ('. implode(',', $ids3) .'))'; 

                $headtitle = $city.' 最新备案查询 - '.$system['name'];
            } else {
                $headtitle = '全国 最新备案查询 - '.$system['name'];
            }
            $url .= "&city=".$city;
        } else {
            $headtitle = '最新备案查询 - '.$system['name']; 
        }
        // 备案性质筛选
        if ($nature) {
            if (is_array($nature)) {
                $where .= ' and (nature in ("'.join('","', $nature).'"))';
                $this->assign('nature', $nature);
                $url .= "&nature=".implode(',',$nature);
            } else {
                $naturearr = explode(',',$nature);
                $where .= ' and (nature in ("'.join('","', $naturearr).'"))';
                $this->assign('nature', $naturearr);
                $url .= "&nature=".$nature;
            }
        }
        // 备案时间筛选
        if ($startime) {
            $where .= ' and (status_time between '.'"'.$startime.' 00:00:00'.'"'.' and ' .'"'.$endtime.' 00:00:00'.'"'.')';
            $this->assign('starttime',$startime);
            $this->assign('endtime', $endtime);
            $url .= "&beiantime=".$beiantime."&startime=".$startime."&endtime=".$endtime;
        } elseif ($beiantime && $beiantime != 4) {
            $where .= ' and (status_time >= "'. date('Y-m-d', strtotime('-'.$beiantime.' days')).'")';
            $url .= "&beiantime=".$beiantime;
        }
        // 搜索框筛选
        if ($url_name) {
            // $where .= ' and (name like "%'.$url_name.'%" or website_url like "%'.$url_name.'%")'; 
            $ids4 = sphinx($url_name, 'website', 5000);
            $this->assign('kw', $url_name);
            $url .= "&url_name=".$url_name;
        }
        if ($ids3) {
            if ($ids4) {
                if (array_intersect($ids3, $ids4)) {
                    $where .= ' and (id in ('. implode(',', array_intersect($ids3, $ids4)) .'))'; 
                } else {
                    $where .= ' and 1=0'; 
                }
            } else {
                $where .= ' and (id in ('. implode(',', $ids3) .'))'; 
            }
        } else {
            if ($ids4) {
                $where .= ' and (id in ('. implode(',', $ids4) .'))'; 
            }
        }
        // if ($where === '1=1') {
        //     $where = 'record_num is not null';
        // } 
        $sql_z = "select id from seo_website where $where";
        // 根据条件计算分页总数量
        $total = Db::name('seo_website')->where($where)->count();
        if (!$page && $total != 0 && $page > ceil($total / $showrow)) {
            $page = ceil($total / $showrow);
        }
        if ($total > 5000) {
            $total = 5000;
            $this->assign('maxPage', 250);
        }
        //总记录数大于每页显示数，显示分页
        if ($total > $showrow) {
            $pagess = new pagess($total, $showrow, $page, $url, 4);
            $fenye = $pagess->myde_write();
        }
        // 分页数量 20,20
        $limitnum = ($page - 1) * $showrow;
        // 优化查询语句
        $sql = "select website_url,record_num,nature,name,left(status_time,10) status_time from seo_website a inner join ($sql_z order by status_time desc limit $limitnum,$showrow) b on a.id = b.id";
        $beianlist = Db::query($sql);
        // 省市区遍历
        if (!$provinces = session('provinces')) {
            $provinces = Db::name('city')->field('city_id,city_zh')->where('p_id',0)->select();
            session('provinces', $provinces);
        }
        $this->assign('provinces',$provinces);
        $this->assign('fenye',$fenye);
        $this->assign('beianlist',$beianlist);
        $this->assign('cityid', $city);
        $this->assign('beiantime',$beiantime);
        $this->assign('headtitle',  $headtitle);  
        $Keyword = '最新备案查询,备案查询,域名备案查询';
        $Desc = '网站最新备案域名查询工具，为站长用户提供最新的备案信息查询，动态查询备案信息，可查询最近历史的备案信息库。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/newrecord.css']);
        $this->assign('JS',['/static/home2/js/record/querynew.js','/static/home2/js/baidupush.js']); 
        return $this->fetch();
    }
    //最新备案域名备份
    public function newrecordBak(){
        $system = cache('System');
        $beianlist = Db::name('seo_website')->field('website_url,record_num,nature,name,status_time')->where('status_time is not null and record_num is not null and nature is not null and name is not null');
        $param = Request::param();
        if ($param) {
            $time =  date('Y-m-d').' 00:00:00';
            // 判断是否提交备案性质
            if (array_key_exists('nature',$param)) {
                $this->assign('nature', $param['nature']);
                if (count($param['nature']) != 5) {
                    $beianlist = $beianlist->where('nature', 'in', $param['nature']);
                }
            }
            // 判断是否提交省市
            if ($param['city'] != '全国') {
                // $name = City::provinceName($param['city']);
                $shortname = City::provinceShort($param['city']);
                $beianlist = $beianlist->where('name', 'like', '%'.$param['city'].'%')->where('record_num','like','%'.$shortname.'%');
            } 
            // 判断是否提交网址
            if ($param['url_name'] != '') {
                $this->assign('kw', $param['url_name']);
                $beianlist = $beianlist->where('name|website_url', 'like', '%'.$param['url_name'].'%');
            }
            // 判断是否提交备案时间
            if ($param['beiantime'] != 0) {
                if ($param['beiantime'] == 7) {
                    $seventime = date( "Y-m-d", strtotime('-7 days')).' 00:00:00';
                    $beianlist = $beianlist->whereTime('status_time', 'between', [$seventime,  $time]);
                } elseif($param['beiantime'] == 30) {
                    $monthtime = date( "Y-m-d", strtotime('-30 days')).' 00:00:00';
                    $beianlist = $beianlist->whereTime('status_time', 'between', [$monthtime,  $time]);
                } else {
                    if ($param['startime'] == $param['endtime']) {
                        $starttime = $param['startime'].' 00:00:00';
                        $endtime = $param['endtime'].' 23:59:59';
                    } else {
                        $starttime = $param['startime'].' 00:00:00';
                        $endtime = $param['endtime'].' 00:00:00';
                    }
                    $this->assign('starttime',$param['startime']);
                    $this->assign('endtime',$param['endtime']);
                    $beianlist = $beianlist->whereTime('status_time', 'between', [$starttime,  $endtime]);
                }
            }
        }
        // 省市区
        $provinces = Db::name('city')->field('city_id,city_zh')->where('p_id',0)->select();
        $this->assign('provinces',$provinces);

        $beianlist = $beianlist->field('website_url,record_num,nature,name,left(status_time,10) status_time')->order('status_time desc')->paginate(20,false,['query' => request()->param()]);
        $listnum = count($beianlist);
        $this->assign('cityid', $param['city']);
        $this->assign('beiantime',$param['beiantime']);
        $this->assign('listnum',$listnum);
        $this->assign('beianlist',$beianlist);

        if ($param) {
            if (array_key_exists('city', $param)) {
                if ($param['city'] != '全国') {
                    $headtitle = '"'.$param['city'].'"'.' 最新备案查询_'.$system['name'];
                } else {
                    $headtitle = '"全国" 最新备案查询_'.$system['name'];
                }
            } else {
                $headtitle = '最新备案查询_'.$system['name']; 
            }
        } else {
            $headtitle = '最新备案查询_'.$system['name']; 
        }
        $this->assign('headtitle',  $headtitle);  
        $Keyword = '最新备案查询,备案查询,域名备案查询';
        $Desc = '网站最新备案域名查询工具，为站长用户提供最新的备案信息查询，动态查询备案信息，可查询最近历史的备案信息库。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/newrecord.css']);
        $this->assign('JS',['/static/home2/js/record/querynew.js']); 
        return $this->fetch();
    }
    //备案批量页面
    public function urlpl(){
        $system = cache('System');
        $headtitle = '网站备案批量查询 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);     
        $Keyword = '网站备案批量查询';
        $Desc = '批量网站备案信息查询，实时查询网站备案，更新ICP备案信息';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc); 
        $beian_plsubmit = $this->querrplnum('beian_plsubmit');
        if (session('userinfo')) {
            $order_beian = Db::name('users_rule')->where('groupid', session('userinfo.level'))->value('order_beian');
        } else {
            $order_beian = 0;
        }
        $this->assign('order_beian', $order_beian);
        $this->assign('beian_plsubmit',$beian_plsubmit);
        $this->assign('CSS',['/static/home2/css/order.css','/static/home2/css/mob.css']);
        $this->assign('JS',['/static/home2/js/record/querypl.js']); 
        return $this->fetch();
    }

    //备案批量查询页面
    public function urlpla() {
        if (!$this->visit()) {
            return ['code'=>0, 'msg'=>'查询次数已达上限,请登录查询更多结果'];
        }
        if(Request::isAjax()) {
            $beian_plsubmit = input('post.beian_plnum');
            $key = strtolower(input('post.target'));
            if (session('usersid')) {
                $ruleinfo = Users::numInfo(session('usersid'));//记录用户次数表
                $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
                if ($ruleinfo['beian_plquerynum'] >= $infolist['beian_plquerynum']) {
                    return ['code'=>3, 'msg'=>'今日备案批量查询次数已达上限，是否升级会员组获取更多次数'];
                } else {
                    if ($ruleinfo['beian_plquerynum']) {
                        if ($ruleinfo['beian_plquerynum'] < $infolist['beian_plquerynum']) {
                            $ruleinfo['beian_plquerynum'] += 1;
                            Db::name('users_num')->where('id',$ruleinfo['id'])->setField('beian_plquerynum', $ruleinfo['beian_plquerynum']);
                        } 
                    } else {
                        $ruleinfo['beian_plquerynum'] = 1;
                        Db::name('users_num')->where('id',$ruleinfo['id'])->setField('beian_plquerynum', $ruleinfo['beian_plquerynum']);
                    }
                }
            }
            $sousl = [];
            $mcd = [];
            $data = [];
            $key = urlmatchall($key);
            $arr = array_unique($key);
            if(count($arr) > $beian_plsubmit) $arr = array_slice($arr, 0, $beian_plsubmit);
            foreach ($arr as $k => $v) {
                $onearr = endurl($v);
                if (!ipmatch($onearr)) {
                    $url1 = urlmatch($onearr);
                    if (substr($url1, 0, 4) === "www.") {
                        $url2 = substr($url1, 4);
                        $topurl = $url2;
                    } else {
                        $topurl = $url1;
                        $url2 = 'www.'.$url1;
                    }
                } else {
                    $url1 = $onearr;
                    $url2 = $onearr;
                    $topurl = $onearr; 
                }
                $res = Db::name('seo_website')->field('website_url weburl,record_num bah,nature,name,status_time time,create_time')->where('website_url', $url1)->find();
                if (!$res || !$res['bah']) {
                    $res = Db::name('seo_website')->field('website_url weburl,record_num bah,nature,name,status_time time,create_time')->where('website_url', $url2)->find();
                }
                if ($res) {
                    if ($res['bah']) {
                        $sousl[$k] = $res;
                        $sousl[$k]['onearr'] = $onearr;
                        $sousl[$k]['topurl'] = $topurl;
                    } else {
                        $sousl[$k+1000] = $res;
                        $sousl[$k+1000]['onearr'] = $onearr;
                        $sousl[$k+1000]['topurl'] = $topurl;
                    }
                } else {
                    $data['topurl'] = $topurl;
                    $this->redis->zadd('BEIAN',$k,JSON($data));
                    $sousl[$k+2000] = [
                        'weburl' => $onearr,
                        'no' => 1,
                        'onearr' => $onearr,
                        'topurl' => $topurl,
                        'bah' => NULL,
                    ];
                    $mcd[] = $onearr;
                }
            }
            ksort($sousl);
            $sousl = array_values($sousl);
            $baorder = Users::userRule(0,0,'baorder');//用户权限备案排序
            // if ($baorder == 1) {
            //     array_multisort(array_column($sousl,'bah'),SORT_DESC,$sousl);
            // }
            return ['code'=>1, 'list'=>$sousl, 'mcd'=>$mcd, 'arr'=>implode("\n", $arr)];
        }
    }

    public function urlplb() {
    	session_write_close();
        $mcd = input('mcd');
        if (!$mcd) return ['code'=>0];
        $time = 0;
        while(true) {
            $data = [];
            foreach ($mcd as $k=>$m) {
                if (!ipmatch($m)) {
                    if (strpos($m,'www.') === false) {
                        $m = 'www.'.$m;
                    }
                }
                $res = Db::name('seo_website')->field('website_url as weburl,record_num as bah,nature,name,status_time as time,create_time')->where('website_url', $m)->find();
                if ($res['name']) {
                    $data[] = $res;
                    unset($mcd[$k]);
                }
            }
            if (!$data) {
                if ($time >5) {
                    return ['code'=>0];
                    break;
                }
                sleep(1);
                $time++;
            } else {
                return ['code'=>1, 'info'=>$data, 'mcd'=>$mcd];
                break;
            }
        }
    }

    //网址提取
    public function urlplc(){
        if(Request::isAjax()) {
            $key = strtolower(input('post.target'));
            $key = urlmatchall($key);
            $arr = array_unique($key);
            return ['code'=>$arr ? 1 : 0, 'arr'=>implode("\n", $arr)];
        }
    }
    
    //导出数据excel
    public function daochu() {
        $list = json_decode(input('shuju'), 1);
        $this->info2excel($list, '搜一搜站长工具_备案批量查询结果');
    }

    // 批量查收录
    public function pcincludes() {
        $includ_plsubmit = $this->querrplnum('includ_plsubmit');
        $this->assign('includ_plsubmit',$includ_plsubmit);

        $system = cache('System');
        $headtitle = '网站收录查询 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);  
        $Keyword = '网站收录查询';
        $Desc = '网站收录批量查询工具；快速查询网站收录数量。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);     
        $this->assign('platform', 1);  
        $slorder = Users::userRule(0,0,'slorder');//用户权限排序
        $this->assign('slorder',$slorder);
        $this->assign('CSS',['/static/home2/css/mob.css']);
        $this->assign('JS',['/static/home2/js/copy/clipboard.min.js','/static/home2/js/record/querysl.js']); 
        return $this->fetch();
    }

    // 批量查收录移动端
    public function mincludes() {
        $system = cache('System');
        $headtitle = '网站收录查询 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);  
        $Keyword = '网站收录查询';
        $Desc = '网站收录批量查询工具；快速查询网站收录数量。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);   
        $this->assign('platform', 2);  
        return $this->fetch('recordquery_pcincludes');
    }

    public function putinclude() {
        if (!$this->visit()) {
            return ['code'=>0, 'msg'=>'查询次数已达上限,请登录查询更多结果'];
        }
        if(Request::isAjax()) {
            $data = input('post.');
            if (session('usersid')) {
                $ruleinfo = Users::numInfo(session('usersid'));//记录用户次数表
                $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
                if ($ruleinfo['includ_querynum']) {
                    if ($ruleinfo['includ_querynum'] >= $infolist['includ_querynum']) {
                        return ['code'=>3, 'msg'=>'今日收录批量查询次数已达上限，是否升级会员组获取更多次数'];
                    } else {
                        $ruleinfo['includ_querynum'] += 1;
                        Db::name('users_num')->where('id',$ruleinfo['id'])->setField('includ_querynum', $ruleinfo['includ_querynum']);
                    }
                } else {
                    $ruleinfo['includ_querynum'] = 1;
                    Db::name('users_num')->where('id',$ruleinfo['id'])->setField('includ_querynum', $ruleinfo['includ_querynum']);
                }
            }
            $data['urls'] = array_unique($data['urls']);
            $urlInfo = [];
            foreach ($data['urls'] as $k=>$v) {
                $urlInfo[$k]['url'] = $v;
                $url = urlmatch($v);
                $urlarr = "'www.".$url."'".','."'".$url."'";
                $urlInfo[$k]['jj'] = Db::name('seo_website_info')->where('website_url in ('.$urlarr.')')->column('jj')[0];
                $start_time = Db::name('seo_website')->where('website_url in ('.$urlarr.')')->column('start_time')[0];
                if ($start_time) {
                    $days = floor((time() - strtotime($start_time)) / 86400);
                } else {
                    $days = 0;
                }
                $urlInfo[$k]['days'] = $days;
            }
            $sign = "INCLUDE:".uniqid();
            $data['sign'] = $sign;
            $res = $this->redis->rpush('INCLUDE', json_encode($data));
            if ($res) return ['code'=>1, 'sign'=>$sign, 'urls'=>$urlInfo];
        }
    }
    public function getinclude() {
        if(Request::isAjax()) {
            $sign = input('post.sign');
            $data = [];
            while($inc = $this->redis->rpop($sign)) {
                $data[] = $inc;
            }
            return ['inc'=>$data];
        }
    }

}
