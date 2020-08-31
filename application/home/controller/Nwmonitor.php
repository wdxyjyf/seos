<?php
namespace app\home\controller;
use think\facade\Request;
use think\Db;
use app\home\model\Users;


class Nwmonitor extends Common {
    public function initialize(){
        parent::initialize();
    }
    // 未登录用户显示的页面
    public function login(){
    	if(session('usersmobile')){
            $this->redirect('/monitor');
            return false;
        }
        $system = cache('System');
        $headtitle = '网站排名监控 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);   
        $Keyword = '网站监控查询';
        $Desc = '用户根据自己的需求添加要监控的网站和关键字';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/nwmonitor.css','/static/home2/css/alter.css']);
        $this->assign('JS',['/static/home2/js/alert.js','/static/home2/js/nwmonitor/nwlogin.js']);
        return $this->fetch();
    }
    //网站列表页面
    public function nwlist() {
        if(empty(session('usersmobile'))){
            $this->redirect('/nwlogin');
            return false;
        }
        $uid = session('usersid');
        $userinfo = Users::userImg($uid);
        $ruleinfo = Users::ruleInfo(1);
        $weburlnum =  $ruleinfo['webmonitorl_urlnum'];//获取普通用户的监控网址数量
        $ruleinfo = Users::ruleInfo($userinfo['level']);
        $countall = $ruleinfo['webmonitorl_urlnum'];//可监控网址数量
        $countkey = $ruleinfo['webmonitor_keynum'];//可监控关键词数量
        $where = ['dm.uid'=>$uid];
        if ($searchname = input('searchname')) {
            $where['dm.weburl'] = $searchname;
        }
        $list = db('seo_monitor_website')->alias('dm')
            ->join('seo_monitor_keywords dc', 'dm.id = dc.dmwebid', 'left')
            ->field('dm.webname,dm.weburl,dm.create_time,dm.weburl,dm.collectnum,dm.flownum,dm.id,dc.dmkeywords,
                (case 
                when  dc.enginetype = 1 and dc.platform =1 then "百度PC"
                when  dc.enginetype = 1 and dc.platform =2 then "百度移动"
                when  dc.enginetype = 2 and dc.platform =1 then "360PC"
                when  dc.enginetype = 2 and dc.platform =2 then "360移动"
                when  dc.enginetype = 3 and dc.platform =1 then "搜狗PC"
                when  dc.enginetype = 3 and dc.platform =2 then "搜狗移动"
                else "暂无分类" end
                ) as type,
                dc.enginetype,dc.platform,dc.keyrank,dc.update_time uptime')
            ->where($where)
            ->order('dm.id desc');
        $lastDate = $list->max('dc.update_time');
        $list = $list->select();
        $list2 = [];
        foreach ($list as $key=>$l) {
            $keyranks = explode(',', $l['keyrank']);
            if (date('m-d', $l['uptime']) != date('m-d')) {
                // $keyranks[] = '101';
                $keyranks[] = end($keyranks);
            }
            $cha = 7-count($keyranks);
            if ($cha>0) {
                for ($i=0; $i <$cha ; $i++) { 
                    array_unshift($keyranks, 0);
                }
            } else {
                for ($i=0; $i <-$cha ; $i++) { 
                    array_shift($keyranks);
                }
            }
            foreach ($keyranks as $k => $v) {
                if ($v>0 && $v<=100) {
                    $keyranks[$k] = 1;
                } else {
                    $keyranks[$k] = 0;
                }
            }
            if ($l['type'] == "暂无分类") {
                $count = 0;
            } else {
                $count = 1;
            }
            if (!array_key_exists($l['weburl'], $list2)) {
                $list2[$l['weburl']] = $l;
                $list2[$l['weburl']]['num'] = $count;
                $list2[$l['weburl']]['type'] = [$l['type']=>$keyranks];
            } else {
                $list2[$l['weburl']]['num']++;
                if (array_key_exists($l['type'], $list2[$l['weburl']]['type'])) {
                    foreach ($list2[$l['weburl']]['type'][$l['type']] as $k1 => $v1) {
                        $list2[$l['weburl']]['type'][$l['type']][$k1] += $keyranks[$k1];
                    }
                } else {
                    if ($list2[$l['weburl']]['type']) {
                        $list2[$l['weburl']]['type'][$l['type']] = $keyranks;
                    } else {
                        $list2[$l['weburl']]['type'] = [$l['type']=>$keyranks];
                    }
                }
            }
            if ($lastDate) {
                for($i = 6; $i >=0; $i--){
                    $list2[$l['weburl']]['seventime'][6-$i] = date('m-d', $lastDate - $i*86400);
                }
            } else{
                for($i = 6; $i >=0; $i--){
                    $list2[$l['weburl']]['seventime'][6-$i] = date('m-d', strtotime('-'.$i.' days'));
                }
            }
            $list2[$l['weburl']]['keynum'] = $key+1;
        }
        if ($userinfo) {
            if ($userinfo['endtime']) {
                if ($userinfo['level'] == 1 && time() - $userinfo['endtime'] > 0) {//用户组1到期
                    array_multisort(array_column($list2,'create_time'),SORT_ASC,$list2);
                    $xuhao = db('seo_monitor_website')->alias('dm')
                        ->field('dm.id')
                        ->join('seo_monitor_keywords dc', 'dm.id = dc.dmwebid', 'left')
                        ->where(['dm.uid'=>$uid])
                        ->order('dm.id asc')
                        ->group('dm.id')
                        ->limit(0,$weburlnum)
                        ->select();
                    $this->assign('xuhao', json_encode(array_column($xuhao,'id')));
                } 
            }
        }
        //分页功能
        $limit = 5;

        $page = input('page')?input('page'):1;
        $list2 = page_array($limit, $page, $list2);
        // 收录
        foreach ($list2 as $key => $value) {
            $list2[$key]['baidu_include'] = Db::name('seo_website_info')->where('website_url',$key)->value('baidu_include')?:0;
        }
        $fenye = db('seo_monitor_website')->alias('dm')
            ->join('seo_monitor_keywords dc', 'dm.id = dc.dmwebid', 'left')
            ->where($where)
            ->order('dm.id desc')
            ->group('dm.id')
            ->paginate($limit);
        $on = count($list2) > 0?1:0;
        $count = count($list2);

        // 更新时间
        $udtime = Db::name('seo_monitor_keywords')->where('uid', $uid)->where('update_time', '>', strtotime(date('Y-m-d')))->min('update_time');
        if (!$udtime) {
            $udtime = Db::name('seo_monitor_keywords')->where('uid', $uid)->where('update_time', '>', strtotime(date('Y-m-d') . '- 1 day'))->min('update_time');
        }
        $this->assign('udtime', $udtime);
        $this->assign('list', $list2);   
        $this->assign('fenye', $fenye);
        $this->assign('on', $on);  
        $this->assign('limit', $limit);
        $this->assign('count', $count);
        $system = cache('System');
        $headtitle = '网站排名监控 - '.$system['name'];
        $this->assign('headtitle',  $headtitle);   

        $Keyword = '网站监控查询';
        $Desc = '用户根据自己的需求添加要监控的网站和关键字';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('countall',  $countall); 
        $this->assign('countkey',  $countkey); 
        $this->assign('lastDate', $lastDate);
        $this->assign('CSS',['/static/home2/css/nwmonitor.css','/static/home2/css/alter.css']);
        $this->assign('JS',['/static/home2/js/alert.js','/static/home2/js/echarts.js','/static/home2/js/nwmonitor/nwlist.js']);
        return $this->fetch();
    }
    //添加编辑网站监控列表
    public function addeditlist(){
        $uid = session('usersid');
        if(Request::isAjax()) {
            $data = Request::param();
            if($data['id'] == 0){//执行添加
                unset($data['id']);
                if(!$uid){
                    $result = ['code' =>0,'msg'=>'请先登录'];
                }else{
                    $level = session('userinfo.level');
                    $ruleinfo = Users::ruleInfo($level);
                    if ($ruleinfo) {
                        $count = Db::name('seo_monitor_website')->where('uid',$uid)->count();
                        if ($count >= $ruleinfo['webmonitorl_urlnum']) {
                            $result = ['code' =>2];
                        } else {
                            $data['uid'] =  $uid;
                            if(strpos($data['weburl'],"://")){
                                $data['weburl'] = substr($data['weburl'], strpos($data['weburl'],"://")+3);
                            }
                            $where['uid'] =  $uid;
                            $where['weburl'] =  $data['weburl'];
                            $info = Db::name('seo_monitor_website')->where($where)->find();
                            if(empty($info)){
                                $data['create_time'] = time();
                                $res = db('seo_monitor_website')->insert($data);
                                if($res !== false){
                                    $result = ['code' =>1,'msg'=>'网站列表添加成功'];
                                }else{
                                    $result = ['code' =>0,'msg'=>'网站列表添加失败'];
                                }
                            }else{
                               $result = ['code' =>0,'msg'=>'添加失败,该网站地址已存在']; 
                            }
                        }
                    }  
                }
            }else{//执行修改
                if(!$uid){
                   $result = ['code' =>0,'msg'=>'请先登录'];
                }else{
                    $res = db('seo_monitor_website')->update($data);
                    if($res !== false){
                        $result = ['code' =>1,'msg'=>'网站列表编辑成功'];
                    }else{
                        $result = ['code' =>0,'msg'=>'网站列表编辑失败'];
                    }
                }
            }
        }else{
            $result = ['code' =>0,'msg'=>'请求方式错误']; 
        }
        return $result;
    }
    //网站地址批量添加
    public function addurlpl(){
        $uid = session('usersid');
        $level = session('userinfo.level');
        $ruleinfo = Users::ruleInfo($level);
        $countall = $ruleinfo['webmonitorl_urlnum']?:1;
        $count = Db::name('seo_monitor_website')->where('uid',$uid)->count();
        if (Request::isAjax()) {
            $allurl = input('weburl');
            if (strpos($allurl,"\n")) {
                $arr = explode("\n",trim($allurl));
                $arr = array_map(function($v){
                    return trim(strtolower($v));
                }, $arr);
                $arr = array_unique($arr);
                if (count($arr) > $countall) {
                    return ['code' => 0, 'msg' => '当前会员组批量添加网站地址一次性最多'.$countall.'条'];   
                } else {
                    if((count($arr)+$count) > $countall){
                        return ['code' => 2,'msg'=>'当前会员组添加监控网址最多'.$countall.'条，是否升级会员组获取更多上限'];  
                    } else {
                        foreach ($arr as $key => $value) {
                            if (!preg_match("/^(?=^.{3,255}$)(http(s)?:\/\/)?(www\.)?[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+(:\d+)*(\/\w+\.\w+)*$/", trim($value))) {
                                return ['code' => 0, 'msg' => '网站地址格式错误，请输入正确的网址格式'];   
                            }
                            if(strpos(trim($value),"://")){
                                unset($arr[$key]);
                                $newval = substr(trim($value), strpos(trim($value),"://")+3);
                                array_push($arr,$newval);
                            }
                        }
                        foreach ($arr as $k => $v) {
                            $where['uid'] =  $uid;
                            $where['weburl'] =  trim($v);
                            $info = Db::name('seo_monitor_website')->where($where)->find();
                            if ($info) {
                                unset($arr[$k]);
                            } else {
                                $data[$k]['uid'] = $uid;
                                $data[$k]['create_time'] = time();
                                $data[$k]['weburl'] = trim($v);
                            }
                        }
                        if (empty($data)) {
                            return ['code' =>0,'msg'=>'添加失败,批量的网址已存在'];
                        } else {
                            $res =Db::name('seo_monitor_website')->insertAll($data);
                            if ($res !== false) {
                                return ['code' =>1,'msg'=>'批量添加成功'];
                            } else {
                                return ['code' =>0,'msg'=>'批量添加失败'];
                            }
                        }
                    }
                }
            } else {
                if (!preg_match("/^(?=^.{3,255}$)(http(s)?:\/\/)?(www\.)?[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+(:\d+)*(\/\w+\.\w+)*$/", trim($allurl))) {
                    return ['code' => 0, 'msg' => '网站地址格式错误，请输入正确的网址格式'];   
                }
                if(strpos(trim($allurl),"://")){
                    $allurl = substr(trim($allurl), strpos(trim($allurl),"://")+3);
                }
                if ($count + 1 > $countall) {
                    return ['code' => 2,'msg'=>'当前会员组添加监控网址最多'.$countall.'条，是否升级会员组获取更多上限'];  
                } else {
                    $where2['uid'] =  $uid;
                    $where2['weburl'] =  trim($allurl);
                    $info = Db::name('seo_monitor_website')->where($where2)->find();
                    if ($info) {
                        return ['code' =>0,'msg'=>'添加失败,该网站地址已存在']; 
                    } else {
                        $data['uid'] = $uid;
                        $data['weburl'] = $allurl;
                        $data['create_time'] = time();
                        $res = db('seo_monitor_website')->insert($data);
                        if($res !== false){
                            return ['code' =>1,'msg'=>'网站地址添加成功'];
                        }else{
                            return ['code' =>0,'msg'=>'网站地址添加失败'];
                        }
                    }
                }
            } 
        } else {
            return ['code' => 0, 'msg' => '请求方式不合法'];
        }
    }
    //切换网站监控列表
    public function changelist(){
        if(empty(session('usersmobile'))){
            $this->redirect('/nwlogin');
            return false;
        }
        $uid = session('usersid');
        $userinfo = Users::userImg($uid);
        $ruleinfo = Users::ruleInfo(1);
        $weburlnum =  $ruleinfo['webmonitorl_urlnum'];//获取普通用户的监控网址数量

        $arrnum = ["baidupc","baidum","haosou","sogoupc","sogoum"];
       
        $level = $userinfo['level'];
        $ruleinfo = Users::ruleInfo($level);
        $countall = $ruleinfo['webmonitorl_urlnum'];
        $countkey = $ruleinfo['webmonitor_keynum'];//可监控关键词数量

        if(input('param.')){
            $navtype = input('navtype');
            if(!in_array($navtype, $arrnum,TRUE)){
                return abort(404, '页面异常');
            }
            $this->assign('type', $navtype);
            $limit = 5;
            $page = input('page')?input('page'):1;
            $fenye = db('seo_monitor_website')->alias('dm')
                ->join('seo_monitor_keywords dc', 'dm.id = dc.dmwebid', 'left')
                ->where('dm.uid',$uid);

            $list = db('seo_monitor_website')->alias('dm')
                ->join('seo_monitor_keywords dc', 'dm.id = dc.dmwebid', 'left')
                ->field('dm.webname,dm.weburl,dm.create_time,dm.weburl,dm.collectnum,dm.flownum,dm.id,
                    (case 
                    when  dc.enginetype = 1 and dc.platform =1 then "百度PC"
                    when  dc.enginetype = 1 and dc.platform =2 then "百度移动"
                    when  dc.enginetype = 2 and dc.platform =1 then "360PC"
                    when  dc.enginetype = 2 and dc.platform =2 then "360移动"
                    when  dc.enginetype = 3 and dc.platform =1 then "搜狗PC"
                    when  dc.enginetype = 3 and dc.platform =2 then "搜狗移动"
                    else "暂无分类" end
                    ) as type,
                    dc.enginetype,dc.platform,dc.keyrank')
                ->where('dm.uid',$uid);
            $lastDate = $list->max(' dc.update_time');  
            if($navtype){
                if($navtype == 'baidupc'){//百度pc
                    $enginetype = 1;
                    $platform = 1;
                }elseif ($navtype == 'baidum') {//百度移动
                    $enginetype = 1;
                    $platform = 2;
                }elseif ($navtype == 'haosou') {//360搜索PC
                    $enginetype = 2;
                    $platform = 1;
                }elseif ($navtype == 'sogoupc') {//搜狗PC
                    $enginetype = 3;
                    $platform = 1;
                }elseif ($navtype == 'sogoum') {//搜狗移动
                    $enginetype = 3;
                    $platform = 2;
                }
                $list = $list->where('dc.enginetype', $enginetype)
                ->where('dc.platform',$platform);
                $fenye = $fenye->where('dc.enginetype', $enginetype)
                ->where('dc.platform',$platform);
            }
            $list = $list->order('dm.id desc')
                ->select();
                // dump($list);exit;
            $fenye = $fenye->order('dm.id desc')
                ->group('dm.id')
                ->paginate($limit);
            $this->assign('fenye', $fenye);
            // $this->assign('page', $page);
            $this->assign('limit', $limit);

            $this->assign('ety', $enginetype);
            $this->assign('pty', $platform);
            $list2 = [];
            foreach ($list as $key=>$l) {
                $keyranks = explode(',', $l['keyrank']);
                $cha = 7-count($keyranks);
                if ($cha>0) {
                    for ($i=0; $i <$cha ; $i++) { 
                        array_unshift($keyranks, 0);
                    }
                } else {
                    for ($i=0; $i <-$cha ; $i++) { 
                        array_shift($keyranks);
                    }
                }
                foreach ($keyranks as $k => $v) {
                    if ($v>0 && $v<=100) {
                        $keyranks[$k] = 1;
                    } else {
                        $keyranks[$k] = 0;
                    }
                }
                if ($l['type'] == "暂无分类") {
                    $count = 0;
                } else {
                    $count = 1;
                }
                if (!array_key_exists($l['weburl'], $list2)) {
                    $list2[$l['weburl']] = $l;
                    $list2[$l['weburl']]['num'] = $count;
                    $list2[$l['weburl']]['type'] = [$l['type']=>$keyranks];
                } else {
                    $list2[$l['weburl']]['num']++;
                    if (array_key_exists($l['type'], $list2[$l['weburl']]['type'])) {
                        foreach ($list2[$l['weburl']]['type'][$l['type']] as $k1 => $v1) {
                            $list2[$l['weburl']]['type'][$l['type']][$k1]+=$keyranks[$k1];
                        }
                    } else {
                        if ($list2[$l['weburl']]['type']) {
                            $list2[$l['weburl']]['type'][$l['type']] = $keyranks;
                        } else {
                            $list2[$l['weburl']]['type'] = [$l['type']=>$keyranks];
                        }
                    }
                }

                for($i = 6; $i >=0; $i--){
                    $list2[$l['weburl']]['seventime'][6-$i] = date('m-d', strtotime('-'.$i.' days'));
                }
            }
            if ($userinfo) {
                if ($userinfo['endtime']) {
                    if ($userinfo['level'] == 1 && time() - $userinfo['endtime'] > 0) {//用户组1到期
                        array_multisort(array_column($list2,'create_time'),SORT_ASC,$list2);
                        $xuhao = db('seo_monitor_website')->alias('dm')
                            ->field('dm.id')
                            ->join('seo_monitor_keywords dc', 'dm.id = dc.dmwebid', 'left')
                            ->where(['dm.uid'=>$uid])
                            ->order('dm.id asc')
                            ->group('dm.id')
                            ->limit(0,$weburlnum)
                            ->select();
                        $this->assign('xuhao', json_encode(array_column($xuhao,'id')));
                    } 
                }
            }
            $list2 = page_array($limit, $page, $list2);
            // 收录
            foreach ($list2 as $key => $value) {
                $list2[$key]['baidu_include'] = Db::name('seo_website_info')->where('website_url',$key)->value('baidu_include')?:0;
            }
            $on = count($list2) > 0?1:0;
            $count = count($list2);
            $this->assign('list',$list2);   
            $system = cache('System');
            $headtitle = '网站排名监控 - '.$system['name'];
            $this->assign('headtitle',  $headtitle);   
            $Keyword = '网站监控查询';
            $Desc = '用户根据自己的需求添加要监控的网站和关键字';
            $this->assign('Keyword', $Keyword);
            $this->assign('page', $page);
            $this->assign('Desc',  $Desc);  
            $this->assign('on', $on);
            $this->assign('count', $count);  
            $this->assign('countall',  $countall); 
            $this->assign('countkey',  $countkey); 
            $this->assign('lastDate', $lastDate);
            $this->assign('CSS',['/static/home2/css/nwmonitor.css','/static/home2/css/alter.css']);
            $this->assign('JS',['/static/home2/js/alert.js','/static/home2/js/echarts.js','/static/home2/js/nwmonitor/nwlist.js']);
            return $this->fetch('nwlist'); 
        }
    }
    //删除网站监控列表
    public function dellist(){
        $id = input('id');
        $type = input('type');
        $where['uid'] = session('usersid');
        $where['dmwebid'] = $id;
        if($type){
            if($type == 'baidupc'){//百度pc
                $where['enginetype'] = 1;
                $where['platform'] = 1;
            }elseif ($type == 'baidum') {//百度移动
                $where['enginetype'] = 1;
                $where['platform'] = 2;
            }elseif ($type == 'haosou') {//360搜索PC
                $where['enginetype'] = 2;
                $where['platform'] = 1;
            }elseif ($type == 'sogoupc') {//搜狗PC
                $where['enginetype'] = 3;
                $where['platform'] = 1;
            }elseif ($type == 'sogoum') {//搜狗移动
                $where['enginetype'] = 3;
                $where['platform'] = 2;
            } else{
                return ['code'=>0,'msg'=>'删除失败,参数错误']; 
            }
        }else{
            $where2['id'] = $id;
            $where2['uid'] = session('usersid');
            Db::name('seo_monitor_website')->where($where2)->delete();
        }
        Db::name('seo_monitor_keywords')->where($where)->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    //点击7天 30天 90天
    public function clickBtn(){
        $uid = session('usersid');
        if(Request::isAjax()) {
            $type = input('type');
            $alldata =  input('alldata');
            $list = db('seo_monitor_keywords')->alias('dc')
                ->field('dc.dmkeywords,dc.collectnum,dc.id,dc.dmwebid,dc.keyrank,dc.update_time')
                ->where('uid',$uid)
                ->where('dmwebid',$alldata['webid'])
                ->where('enginetype',$alldata['ety'])
                ->where('platform',$alldata['pty'])
                ->order('dc.id desc');
            // $lastDate = $list->max('update_time');
            $minDate = $list->where('update_time', '>', 0)->min('update_time');// 时间格式
            $today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

            $list = $list->select();
            $rank2 = [];//获取所有关键词的结果数据
            foreach ($list as $key => $value) {
                $rank2[$key]['keyrank'] = $value['keyrank'];
                $rank2[$key]['uptime'] = $value['update_time'];
            }
            if($type == 0){//一周
                // 时间格式
                $days = 7;
                if ($minDate < $today) {
                    for($i = 7; $i >=1; $i--){
                        $xarr[] = date('m-d', $today - $i*86400);
                    }
                } else {
                    for($i = 6; $i >=0; $i--){
                        $xarr[] = date('m-d', $today - $i*86400);
                    }
                }
                
            }elseif($type == 1){//一个月
                $days = 30;
                if ($minDate < $today) {
                    for($i = 30; $i >=1; $i--){
                        $xarr[] = date('m-d', $today - $i*86400);
                    }
                } else {
                    for($i = 29; $i >=0; $i--){
                        $xarr[] = date('m-d', $today - $i*86400);
                    }
                }
            }else{//三个月
                $days = 90;
                if ($minDate < $today) {
                    for($i = 90; $i >=1; $i--){
                        $xarr[] = date('m-d', $today - $i*86400);
                    }
                } else {
                    for($i = 89; $i >=0; $i--){
                        $xarr[] = date('m-d', $today - $i*86400);
                    }
                }
            }
            //拆分数据结果为二维数组
            $sevendata = [];
            foreach ($rank2 as $key => $value) {
                $del = $value['keyrank'];
                $arr = explode(',', $del);
                if ($minDate < $today && $value['uptime'] > $today) {
                    array_pop($arr);
                }
                for ($i= count($arr)-$days;$i<count($arr);$i++) {
                    $sevendata[$key][] = (int)$arr[$i]?:101;
                }
            }
            //统计前10 20 50 100 出现的次数
            $rank = [];
            foreach ($sevendata as $key => $value) {
                $res = array_map(function($n){
                    if ($n>50 && $n<=100) {
                        $k = 100;
                    }
                    if ($n>20 && $n<=50) {
                        $k = 50;
                    }
                    if ($n>10 && $n<=20) {
                        $k = 20;
                    }
                    if ($n>0 && $n<=10) {
                        $k = 10;
                    }
                    return $k?:0;
                },$value);
                for ($i=0;$i<count($value);$i++) {
                    if ($res[$i] == 0) {
                        $rank[$i]['no']++;
                    } elseif ($res[$i] == 10) {
                        $rank[$i][10]++;
                        $rank[$i][20]++;
                        $rank[$i][50]++;
                        $rank[$i][100]++;
                    } elseif ($res[$i] == 20) {
                        $rank[$i][20]++;
                        $rank[$i][50]++;
                        $rank[$i][100]++;
                    } elseif ($res[$i] == 50) {
                        $rank[$i][50]++;
                        $rank[$i][100]++;
                    } elseif ($res[$i] == 100) {
                        $rank[$i][100]++;
                    }
                }
            }
            //网址折线七天统计图数据
            $xinfo = [];
            foreach ($rank as $key => $value) {
                $xinfo[0][] = $value[100]?:0;
                $xinfo[1][] = $value[50]?:0;
                $xinfo[2][] = $value[20]?:0;
                $xinfo[3][] = $value[10]?:0;
            }

            $result['code'] = 1;
            $result['xarr'] =  $xarr;
            $result['xinfo'] = $xinfo;
            return $result;
        }
    }
    //关键词列表页面
    public function nwkeylist(){
        $uid = session('usersid');//获取用户id
        if(!$uid){
            $this->redirect('/nwlogin');
            return false;
        }
        $level = session('userinfo.level');//获取用户身份
        $ruleinfo = Users::ruleInfo($level);//根据用户组获取对应权限信息
        $countkey = $ruleinfo['webmonitor_keynum'];//可监控关键词数量
        $all = Request::param();//接收提交的数据
        $search = $all['search'];
        if ($search == 'baidupc') {
            $all['ety'] = 1;
            $all['pty'] = 1;
        } elseif($search == 'baidum'){
            $all['ety'] = 1;
            $all['pty'] = 2;
        }elseif($search == 'haosou'){
            $all['ety'] = 2;
            $all['pty'] = 1;
        }elseif($search == 'sogoupc'){
            $all['ety'] = 3;
            $all['pty'] = 1;
        }elseif($search == 'sogoum'){
            $all['ety'] = 3;
            $all['pty'] = 2;
        }
        $this->assign('ety', $all['ety']);
        $this->assign('pty', $all['pty']);
        $dmid = $all['webid'];//监控的网址id
        $weburl = db('seo_monitor_website')->where('id',$dmid)->value('weburl');//获取网址
        $urltitle = db('seo_website_info')->where('website_url', $weburl)->value('title')?:''; //获取网址标题
        $this->assign('dmid', $dmid);   
        $this->assign('weburl', $weburl);   
        $this->assign('search', $search);
        $name = getUrlname($all['ety'],$all['pty']);//获取引擎名称
        $list = db('seo_monitor_keywords')->alias('dc')
                ->field('dc.dmkeywords,dc.collectnum,dc.id,dc.dmwebid,dc.keyrank,dc.update_time')
                ->where('uid',$uid)
                ->where('dmwebid',$dmid)
                ->where('enginetype',$all['ety'])
                ->where('platform',$all['pty']);
        $minDate = db('seo_monitor_keywords')
                ->where('uid',$uid)
                ->where('dmwebid',$dmid)
                ->where('enginetype',$all['ety'])
                ->where('platform',$all['pty'])
                ->where('update_time', '>', 0)
                ->min('update_time');// 时间格式
        $today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        if ($minDate < $today) {
            for($i = 7; $i >=1; $i--){
                $xarr[] = date('m-d', $today - $i*86400);
            }
        } else {
            for($i = 6; $i >=0; $i--){
                $xarr[] = date('m-d', $today - $i*86400);
            }
        }
        // 分页功能
        $count = $list->count();
        $limit = 20;                // 每页条数
        $page = $all['page']?:1;     // 获取第几页
        $totalPage = ceil($count/$limit); // 总页码
        $page = $page>$totalPage?$totalPage:$page;
        $this->assign('page', $page);
        $this->assign('limit', $limit);
        $paginate = $list->paginate($limit);
        $this->assign('paginate', $paginate);
        $this->assign('countkey', $countkey);
        $list = $list->order('create_time desc')->select();
        // 判断通过网址传过来的值,网址id,引擎id,类别id,与当前用户id判断,如果为空,返回监控列表页
        if (empty($list)) {
            $this->redirect('/monitor');
        }
        $days = 7; //一星期
        $rank2 = [];//获取所有关键词的结果数据
        foreach ($list as $key => $value) {
            $rank2[$key]['keyrank'] = $value['keyrank'];
            $rank2[$key]['uptime'] = $value['update_time'];
        }

        $sevendata = [];
        // 每个关键词的数据
        foreach ($list as $key => $value) {
            if($value['keyrank'] == '' && $value['update_time']== ''){
                $list[$key]['ranking'] = 0;
                $list[$key]['sevendata'] = [0,0,0,0,0,0,0];
                $list[$key]['rankorder'] = 0;
                for($i = 6; $i >=0; $i--){
                    $list[$key]['seventime'][] = date('m-d', strtotime('-'.$i.' days'));
                }
            }else{
                if(strpos($value['keyrank'],',') !== false){
                    $endrank = substr($value['keyrank'],strrpos($value['keyrank'],',')+1);//获取最后一个数据
                    $list[$key]['ranking'] = $endrank == 101?"100+":$endrank;
                    if ($all['pty'] == 2 && $list[$key]['ranking'] == 51) {
                        $list[$key]['ranking'] = "50+";
                    }
                    $list[$key]['rankorder'] = $endrank;
                    $arr = explode(',', $value['keyrank']);//分割数据
                    $list[$key]['befrank'] = $arr[count($arr)-2] == 0?0:$endrank - $arr[count($arr)-2];
                    if ($value['update_time']){
                        if (date('m-d', $value['update_time']) != date('m-d')) {
                            // $arr[] = '101';
                            $arr[] = end($arr);
                            $k = 0;
                        } else {
                            $k = 1;
                        }
                    }
                    for ($i= count($arr)-$days;$i<count($arr);$i++) {
                        $list[$key]['seventime'][] = date('m-d', strtotime('-'.($days-$k).'days'));
                        $list[$key]['sevendata'][] = $arr[$i]?:0;
                        $k++;
                    }
                }else{
                    $list[$key]['ranking'] =  $value['keyrank'] == 101?"100+":$value['keyrank'];
                    $list[$key]['befrank'] = 0;
                    $list[$key]['rankorder'] = $value['keyrank'];
                    $list[$key]['sevendata'] = [0,0,0,0,0,0,intval($value['keyrank'])];
                    for($i = 6; $i >=0; $i--){
                        $list[$key]['seventime'][] = date('m-d', $minDate - $i*86400);
                    }
                }
            }
            $list[$key]['sousl'] = Db::table('seo_keyword_hotdig')->where('keyword',$value['dmkeywords'])->value('averagePv')?:0;
            $list[$key]['update_time'] = $value['update_time']?date('Y-m-d ',$value['update_time']):'暂无更新';
        }
        array_multisort(array_column($list,'rankorder'),SORT_ASC,$list);
        $list = page_array($limit, $page, $list);
        
        //拆分七天数据结果为二维数组
        foreach ($rank2 as $key => $value) {
            $del = $value['keyrank'];
            $arr = explode(',', $del);
            if ($minDate < $today && $value['uptime'] > $today) {
                array_pop($arr);
            }
            for ($i= count($arr)-$days;$i<count($arr);$i++) {
                $sevendata[$key][] = (int)$arr[$i]?:101;
            }
        }
        // dump($sevendata);exit;
        //统计前10 20 50 100 出现的次数
        $rank = [];
        foreach ($sevendata as $key => $value) {
            $res = array_map(function($n){
                if ($n>50 && $n<=100) {
                    $k = 100;
                }
                if ($n>20 && $n<=50) {
                    $k = 50;
                }
                if ($n>10 && $n<=20) {
                    $k = 20;
                }
                if ($n>0 && $n<=10) {
                    $k = 10;
                }
                return $k?:0;
            },$value);
            for ($i=0;$i<count($value);$i++) {
                if ($res[$i] == 0) {
                    $rank[$i]['no']++;
                } elseif ($res[$i] == 10) {
                    $rank[$i][10]++;
                    $rank[$i][20]++;
                    $rank[$i][50]++;
                    $rank[$i][100]++;
                } elseif ($res[$i] == 20) {
                    $rank[$i][20]++;
                    $rank[$i][50]++;
                    $rank[$i][100]++;
                } elseif ($res[$i] == 50) {
                    $rank[$i][50]++;
                    $rank[$i][100]++;
                } elseif ($res[$i] == 100) {
                    $rank[$i][100]++;
                }
            }
        }
        $xinfo = [];
        foreach ($rank as $key => $value) {
            $xinfo[0][] = $value[100]?:0;//前100名折线七天统计图
            $xinfo[1][] = $value[50]?:0;//前50名折线七天统计图
            $xinfo[2][] = $value[20]?:0;//前20名折线七天统计图
            $xinfo[3][] = $value[10]?:0;//前10名折线七天统计图
        }
        // 升降
        $ud = [];
        foreach ($sevendata as $v) {
            $end = end($v);
            $end2 = $v[count($v)-2];
            if ($end2>0 && $end2<=10) {
                if ($end>10 && $end<=20) {
                    $ud['10-']++;
                } elseif ($end>20 && $end<=50) {
                    $ud['10-']++;
                    $ud['20-']++;
                } elseif ($end>50 && $end<=100) {
                    $ud['10-']++;
                    $ud['20-']++;
                    $ud['50-']++;
                } elseif ($end>100) {
                    $ud['10-']++;
                    $ud['20-']++;
                    $ud['50-']++;
                    $ud['100-']++;
                }
            }
            if ($end2>10 && $end2<=20) {
                if ($end>0 && $end<=10) {
                    $ud['10+']++;
                } elseif ($end >20 && $end<=50) {
                    $ud['20-']++;
                } elseif ($end>50 && $end<=100) {
                    $ud['20-']++;
                    $ud['50-']++;
                } elseif ($end>100) {
                    $ud['20-']++;
                    $ud['50-']++;
                    $ud['100-']++;
                }
            }
            if ($end2>20 && $end2<=50) {
                if ($end>0 && $end<=10) {
                    $ud['10+']++;
                    $ud['20+']++;
                } elseif ($end>10 && $end<=20) {
                    $ud['20+']++;
                } elseif ($end>50 && $end<=100) {
                    $ud['50-']++;
                } elseif ($end>100) {
                    $ud['50-']++;
                    $ud['100-']++;
                }
            }
            if ($end2>50 && $end2<=100) {
                if ($end>0 && $end<=10) {
                    $ud['10+']++;
                    $ud['20+']++;
                    $ud['50+']++;
                } elseif ($end>10 && $end<=20) {
                    $ud['20+']++;
                    $ud['50+']++;
                } elseif ($end>20 && $end<=50) {
                    $ud['50+']++;
                } elseif ($end>100) {
                    $ud['100-']++;
                }
            }
            if ($end2>100 || $end2==0) {
                if ($end>0 && $end<=10) {
                    $ud['10+']++;
                    $ud['20+']++;
                    $ud['50+']++;
                    $ud['100+']++;
                } elseif ($end>10 && $end<=20) {
                    $ud['20+']++;
                    $ud['50+']++;
                    $ud['100+']++;
                } elseif ($end>20 && $end<=50) {
                    $ud['50+']++;
                    $ud['100+']++;
                } elseif ($end>50 && $end<=100) {
                    $ud['100+']++;
                }
            }
        }
        $this->assign('ud', $ud);
        $this->assign('name',$name);   
        $this->assign('list',  $list); 
        $this->assign('alldata',  $all);  

        $system = cache('System');
        $headtitle = $urltitle.' '.$weburl.' '.$name.'关键词排名趋势 - '.$system['name'];
        $this->assign('headtitle',  $headtitle); 
        $this->assign('dataarr', $xarr);   
        $this->assign('dataarrinfo', $xinfo);   

        $Keyword = '网站监控查询';
        $Desc = '用户根据自己的需求添加要监控的网站和关键字';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/nwmonitor.css','/static/home2/css/alter.css']);
        $this->assign('JS',['/static/home2/js/alert.js','/static/home2/js/echarts.js','/static/home2/js/nwmonitor/nwkeylist.js']);
        return $this->fetch();
    }
    //关键词添加
    public function addnwkey(){
        $uid = session('usersid');
        $allcount = Db::name('seo_monitor_keywords')->where('uid',$uid)->count();
        $level = session('userinfo.level');
        $ruleinfo = Users::ruleInfo($level);
        $countkey = $ruleinfo['webmonitor_keynum'];//可监控关键词数量
        if(request()->isPost()){
            $alllist = input('post.');
            if(!$uid){
                return ['code' =>0,'msg'=>'请先登录'];
            }else{
                if(strpos($alllist['dmkeywords'],"\n")){
                    $content = trim($alllist['dmkeywords']);
                    $arr = explode("\n",$content);
                    $arr = array_map(function($v){
                        return trim($v);
                    }, $arr);
                    $arr = array_unique($arr);
                    if (count($arr) > $countkey) {
                        return ['code' => 0, 'msg' => '当前会员组批量监控关键词一次性最多'.$countkey.'个'];   
                    } else{
                        if ((count($arr)+ $allcount) > $countkey) {
                            return ['code' => 2,'msg'=>'当前会员组最多监控'.$countkey.'个关键词'.',是否升级会员组获取更多权限'];
                        } else {
                            foreach ($arr as $k => $v) {
                                if (!preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9 ]+$/u", trim($v))) {
                                    return ['code' => 0, 'msg' => '关键词格式错误,请输入中文,字母或数字'];   
                                }else{
                                    if(mb_strlen(trim($v),'UTF8') > 15){
                                        return ['code' => 0, 'msg' => '每行关键词长度最大15个字符'];
                                    }
                                }
                                $data[$k]['uid'] = $uid;
                                $data[$k]['dmwebid'] = $alllist['dmwebid'];
                                $data[$k]['create_time'] = time();
                                $data[$k]['enginetype'] = $alllist['enginetype'];
                                $data[$k]['platform'] = $alllist['platform'];
                                $data[$k]['dmkeywords'] = trim($v);

                                $where['uid'] =  $uid;
                                $where['dmwebid'] =  $alllist['dmwebid'];
                                $where['enginetype'] =  $alllist['enginetype'];
                                $where['platform'] =  $alllist['platform'];
                                $where['dmkeywords'] =  trim($v);
                                $info = Db::name('seo_monitor_keywords')->where($where)->find();
                                if($info){
                                    unset($data[$k]);
                                }
                            }

                            $res =Db::name('seo_monitor_keywords')->insertAll($data);
                            if($res !== false){
                                return ['code' =>1,'msg'=>'关键词添加成功','info'=>[
                                'ety'   =>  $alllist['enginetype'],
                                'pty'   =>  $alllist['platform']
                            ]];
                            }else{
                                return ['code' =>0,'msg'=>'关键词添加失败'];
                            }
                        }
                    }
                }else{
                    $content = trim($alllist['dmkeywords']);
                    if (!preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9]+$/u", $content)) {
                        return ['code' => 0, 'msg' => '关键字格式错误,请输入中文,字母或数字'];   
                    }else{
                        if(mb_strlen($content,'UTF8') > 15){
                            return ['code' => 0, 'msg' => '每行关键词长度最大15个字符'];
                        }
                    }
                    if($allcount + 1 > $countkey){
                        return ['code' => 2,'msg'=>'当前会员组最多监控'.$countkey.'个关键词'.',是否升级会员组获取更多权限'];
                    } else{
                        $data['uid'] = $uid;
                        $data['dmwebid'] = $alllist['dmwebid'];
                        $data['create_time'] = time();
                        $data['enginetype'] = $alllist['enginetype'];
                        $data['platform'] = $alllist['platform'];
                        $data['dmkeywords'] = trim($alllist['dmkeywords']);

                        $where['uid'] =  $uid;
                        $where['dmwebid'] =  $alllist['dmwebid'];
                        $where['enginetype'] =  $alllist['enginetype'];
                        $where['platform'] =  $alllist['platform'];
                        $where['dmkeywords'] =  trim($alllist['dmkeywords']);
                        $info = Db::name('seo_monitor_keywords')->where($where)->find();
                        if(empty($info)){
                            $res = db('seo_monitor_keywords')->insert($data);
                            if($res !== false){
                                return ['code' =>1,'msg'=>'关键字添加成功', 'info'=>[
                                    'ety'   =>  $alllist['enginetype'],
                                    'pty'   =>  $alllist['platform']
                                ]];
                            }else{
                                return ['code' =>0,'msg'=>'关键字添加失败'];
                            }
                        }else{
                            return ['code' =>0,'msg'=>'添加失败,您已添加过该关键字'];
                        }  
                    }
                }
            }   
        }
    }

     //删关键词监控列表
    public function delkeylist(){
        db('seo_monitor_keywords')->where(array('id'=>input('id')))->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }

     //批量删除
    public function dels(){
        $ids = input('kwids');
        db('seo_monitor_keywords')->where('id', 'in', $ids)->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }

    //关键词趋势页面
    public function nwkeyimg(){ 
        if(empty(session('usersmobile'))){
            $this->redirect('/nwlogin');
            return false;
        }
        $data = Request::param();
        $onearr = endurl($data['url']);
        // 获取网址的标题
        $urltitle = db('seo_website_info')->where('website_url',$onearr)->value('title')?:'';

        if (!$data['rank_id'] || !$data['url'] || !$data['search']) {
            $this->redirect('/monitor');
        }
        $urlaa =  $data['url'];
        $search =  $data['search'];
        $info = db('seo_monitor_keywords')->where('id',$data['rank_id'])->find();
        $keyword = $info['dmkeywords'];
        if ($info['uid'] != session('usersid')) {
            $this->redirect('/monitor');
        }
        $days = 7; 
        if($info['keyrank']){//判断数据是否存在
            $arr = explode(',', $info['keyrank']);//分割数据
            if ($info['update_time']){
                if (date('m-d', $info['update_time']) != date('m-d')) {
                    // $arr[] = '101';
                    $arr[] = end($arr);
                    $k = 0;
                } else {
                    $k = 1;
                }
            }
            $res = [];
            for ($i= count($arr)-$days;$i<count($arr);$i++) {
                $res['dataarr'][] = date('m-d', strtotime('-'.($days-$k).'days'));
                $res['dataarrinfo'][] = $arr[$i]?:0;
                $k++;
            }
        }
        if ($search == 'baidupc') {
            $name = '百度PC';
        }elseif($search == 'baidum'){
            $name = '百度移动';
        }elseif($search == 'haosou'){
            $name = '360PC';
        }elseif($search == 'sogoupc'){
            $name = '搜狗PC';
        }elseif($search == 'sogoum'){
            $name = '搜狗移动';
        }
        $this->assign('dataarr', $res['dataarr']);   
        $this->assign('dataarrinfo', $res['dataarrinfo']);  
        $this->assign('dmwebid', $info['dmwebid']);
        $this->assign('name', $name);
        $this->assign('search', $search);
        $this->assign('dmkeywords', $keyword);   
        $this->assign('dmname', $data['url']); 
        $this->assign('id', $info['id']); 
        $system = cache('System');
        $headtitle = $urltitle.' '.$urlaa.' '.$keyword.' '.$name.' '.'排名历史 - '.$system['name'];
        $this->assign('headtitle',  $headtitle); 
        
        $Keyword = '网站监控查询';
        $Desc = '用户根据自己的需求添加要监控的网站和关键字';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/nwmonitor.css','/static/home2/css/alter.css']);
        $this->assign('JS',['/static/home2/js/alert.js','/static/home2/js/echarts.js']);
        return $this->fetch();
    }
    public function webImg(){
        if(Request::isAjax()) {
            $type = input('type');
            $dkeyid = input('dkeyid');
            if($type == 0){
                $days = 7;
            }elseif($type == 1){
                $days = 30;
            }else{
                $days = 90;
            }
            $info = db('seo_monitor_keywords')->where('id',$dkeyid)->find();
            if($info['keyrank']){//判断关键词结果是否存在
                $arr = explode(',', $info['keyrank']);//分割数据
                if ($info['update_time']){//获取更新时间
                    if (date('m-d', $info['update_time']) != date('m-d')) {//如果更新时间 不是今天最新时间
                        // $arr[] = '101';
                        $arr[] = end($arr);
                        $k = 0;
                    } else {
                        $k = 1;
                    }
                }
                $res = [];
                for ($i= count($arr)-$days;$i<count($arr);$i++) {
                    $res['dataarr'][] = date('m-d', strtotime('-'.($days-$k).'days'));
                    $res['dataarrinfo'][] = $arr[$i]?:0;
                    $k++;
                }
                return ['code'=>1,'xarr'=> $res['dataarr'],'xinfo'=> $res['dataarrinfo']];
            }
        }
    }

}

