<?php
namespace app\home\controller;

use think\Db;
use think\facade\Request;

class Index extends Common{
    public function initialize(){
        parent::initialize();
    }
    public function toplimit()
    {
        if(!$this->visit()){
            return ['code'=>0, 'msg'=>'查询次数已达上限,请登录查询更多结果'];
        }else{
            $this->success();
        }
    }
    public function index(){
        $adList = $this->getAd(5, 1);
        $system = cache('System');
        $headtitle = $system['title'].' - '.$system['name'];
        $this->assign('headtitle',  $headtitle);
        $this->assign('adList', $adList);

        if ($save_url = cookie('save_url')) {
            $this->assign('save_url', $save_url);
        }
        $Keyword = $system['key'];
        $Desc = $system['des'];
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);
        $this->assign('CSS',['/static/home2/css/indexurl.css']);
        $this->assign('JS',['/static/home2/js/index1.js']);
        return $this->fetch();
    }
    public function webrecord(){
        if (!$this->visit()) {
            return abort(404, '页面异常');
        } 
        //接收要搜索的值,获取主域名
        $kw = endurl(trim(strip_tags(input('website'))));
        $kw = strtolower($kw);
        // $is_update = input('post.is_update')?1:0;
        $is_update = cookie('is_update')?1:0;
        cookie('is_update', null);
        $data = [];
        $kwlist = [];
        $fuhao = [',', '|','、','|'];
        if (urlmatch($kw) || ipmatch($kw)) {
            if ($url = urlmatch($kw)) {
                $urlarr = "'www.".$url."'".','."'".$url."'";
                $data['topurl'] = urlmatch($kw);
            } else {
                $urlarr = "'".$kw."'";
                $data['topurl'] = $kw;
            }
            $urlip = getAddrByHost($kw);//ip
            $urlcity = getCity4($urlip); //地理位置
            //获取备案信息
            $results = Db::query($sql = "select website_url,start_time,end_time,record_num,nature,name,create_time from seo_website where website_url in (".$urlarr.")");
            if ($results) {
                if (count($results) == 2) {
                    $result = $results[0]['record_num']?$results[0]:$results[1];
                } else {
                    $result = $results[0];
                }
                if ($is_update) {
                    $data['update'] = 1;
                    $this->redis->zadd('BEIAN',10000,JSON($data));
                    unset($data['update']);
                    $beian_ct = $result['create_time'];
                    $this->assign('beian_ct', $beian_ct);
                    $result = [];
                }
            } else {
                $this->redis->zadd('BEIAN',10000,JSON($data));
            }

            //获取标题关键字
            $tkd = Db::name('seo_website_info')->field('title,keyword,description,jj,create_time,(case 
                    when locate("亿",baidu_include) > 0 then cast(replace(replace(baidu_include,"亿",""),"万","000000") as signed)
                    else cast(replace(baidu_include,",","") as signed) end
                    ) as baidu_include')->where('website_url', $kw)->find();
            if ($is_update) {
                $beianinfo_ct = $tkd['create_time'];
                $this->assign('beianinfo_ct', $beianinfo_ct);
                $tkd = [];
            }
            //获取标题
            $urltitle = $tkd['title']?:'';
            if ($result) {
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
            $data['url'] = $kw;
            if ($is_update) {
                $this->redis->del('TOP:'.$kw);
            }
            //读取redis 收录反链数据
            if($res = $this->redis->lrange('TOP:'.$kw, 0, -1)){
                $slflarr = json_decode($res[0], 1);
                if ($slflarr['bd_index_num'] != $tkd['baidu_include']) {
                	Db::name('seo_website_info')->where('website_url', $kw)->update(['baidu_include'=>$slflarr['bd_index_num']]);
                }
            } else {
                $slflarr = [];
                $this->redis->zadd('WEBURL',20000,JSON($data));
            }
            if ($result['start_time']) {
                $result['start_timey'] = date('Y',strtotime($result['start_time']));//备案时间年
                $result['start_timem'] = date('m',strtotime($result['start_time']));//备案时间月
                $result['start_timed'] = date('d',strtotime($result['start_time']));//备案时间日

                $result['end_timey'] = date('Y',strtotime($result['end_time']));//备案时间日
                $result['end_timem'] = date('m',strtotime($result['end_time']));//备案时间日
                $result['end_timed'] = date('d',strtotime($result['end_time']));//备案时间日
    
                $nowy = date('Y',time());
                $nowm = date('m',time());
                $nowd = date('d',time());
                if ($nowd - $result['start_timed'] < 0) {
                    $result['difftimed'] = 30 + $nowd - $result['start_timed'];
                    $nowm--;
                } else {
                    $result['difftimed'] = $nowd - $result['start_timed'];
                }
                if ($nowm - $result['start_timem'] < 0) {
                    $result['difftimem'] = 12 + $nowm - $result['start_timem'];
                    $nowy--;
                } else {
                    $result['difftimem'] = $nowm - $result['start_timem'];
                }
                $result['difftimey'] = $nowy- $result['start_timey'];
            }

            $ban = Db::name('ban')->column('ban');
            if ($tkd['title']) {
                $tkd['title'] = str_ireplace($ban, '*', $tkd['title']);
            }
            if ($tkd['description']) {
                $tkd['description'] = str_ireplace($ban, '*', $tkd['description']);
            }
            if ($tkd['keyword']) {
                foreach ($fuhao as $value) {
                    $keystring = str_replace($value, ',', $tkd['keyword']);
                }
                $keyall = explode(',', $keystring);
                $recs = [];
                foreach ($keyall as $key => $value) {
                	if (trim($value)) {
	                	$kwlist[$key]['sousl'] = Db::name('seo_keyword_hotdig')->where('keyword',$value)->value('averagePv')?:0;
	                    $xgs = sphinx($value, 'relevantword', 10000);
	                    $kwlist[$key]['xgnum'] = count($xgs)?:0;
	                    $kwlist[$key]['kw2'] = $value; 
	                    $kwlist[$key]['kw'] = str_ireplace($ban, '*', $value);
	                    $recs = array_merge(sphinx($value, 'website_info_t', 100), $recs);
                	}
                    
                }
                $tkd['keyword'] = str_ireplace($ban, '*', $tkd['keyword']);
            }

            $keywords = explode(',', $tkd['keyword']);
            $keywords = $keywords[0]?mb_substr($keywords[0],0,10):'';

            // 更多域名查询
            
            if ($recs) {
                $recs = array_unique($recs);
                shuffle($recs);
                $recs = array_splice($recs, 0, 12);
                $list = Db::name('seo_website_info')->field('website_url')->where('id', 'in', $recs)->select();
                if (count($list) < 12) {
                    $limitStart = rand(1,10000);
                    $listt = Db::name('seo_website_info')->field('website_url')->order('id desc')->limit($limitStart, 12-count($list))->select();
                    $list = array_merge($list, $listt);
                }
            } else {
                $limitStart = rand(1,10000);
                $list = Db::name('seo_website_info')->field('website_url')->order('id desc')->limit($limitStart, 12)->select();
            }
            
            $this->assign('list',  $list);

            $system = cache('System');
            $headtitle = $tkd['title'].' - '.$kw.' - 排名综合查询 - '.$system['name'];
            $Keyword = $keywords.",{$kw}信息查询";

            $Desc = $tkd['description']?:"$kw 网站综合排名查询，网站备案信息，收录信息。";
            if ($save_url = cookie('save_url')) {
                $this->assign('save_url', $save_url);
            }
            $this->assign('headtitle',  $headtitle);
            $this->assign('kw', $kw);
            $this->assign('onearr', $kw);
            $this->assign('urlarr', $urlarr);
            $this->assign('Keyword', $Keyword);
            $this->assign('Desc',  $Desc);
            $this->assign('weblist', $result); 
            $this->assign('kwlist', $kwlist);  
            $this->assign('on', $result?1:0);
            $this->assign('on3', $tkd?1:0);
            $this->assign('tkd', $tkd);
            $this->assign('urlcity', $urlcity);
            $this->assign('urlip', $urlip);
            $this->assign('sldata', $slflarr?1:0);
            $this->assign('slflarr', $slflarr);
            $this->assign('is_update', $is_update);
            $this->assign('CSS',['/static/home2/css/indexurl.css']);
            $this->assign('JS',['/static/home2/js/jquery.cookie.js','/static/home2/js/index.js','/static/home2/js/baidupush.js']);
            // 广告
            $ad = $this->getAd(14);
            $this->assign('ad', $ad);

            return $this->fetch();
        } else {
           return abort(404, '页面异常');
        }
    }
    //标题，关键字
    public function datainfo(){
        session_write_close();
        if (Request::isAjax()) {
            $urlarr = input('onearr');
            $is_update = input('is_update');
            $beianinfo_ct = input('beianinfo_ct');
            $titkeyinfo = Db::name('seo_website_info')->field('title,keyword,description,create_time')->where('website_url', $urlarr)->find();
            if (!$titkeyinfo || ($is_update && $titkeyinfo['create_time'] == $beianinfo_ct)) {
                $time = 0;
                while (true) {
                    $titkeyinfo  = Db::name('seo_website_info')->field('title,keyword,description,create_time')->where('website_url', $urlarr)->find();
                    if ($is_update && $titkeyinfo && $titkeyinfo['create_time'] != $beianinfo_ct) {
                    	break;
                    } elseif (!$is_update && $titkeyinfo) {
                    	break;
                    } elseif ($time > 7) {
                    	break;
                    } else {
                    	$time++;
                        sleep(1);
                    }

                    // if ($is_update) {
                    //     if ($titkeyinfo['create_time'] != $beianinfo_ct) break;
                    // } elseif ($titkeyinfo) break;
                    // else {
                    //     if ($time>7) break;
                    //     $time++;
                    //     sleep(1);
                    // }
                }
            } 
            $kwlist = [];
            $fuhao = [',', '|','、','|'];
            foreach ($fuhao as $f) {
                $titkeyinfo ['keyword'] = str_replace($f, ',', $titkeyinfo['keyword']);
            }
            $keyword = explode(',', $titkeyinfo['keyword']);
            foreach ($keyword as $key => $value) {
                if ($value) {
                    $kwlist[$key]['sousl'] = Db::name('seo_keyword_hotdig')->where('keyword',$value)->value('averagePv')?:0;//搜索量
                    $xgs = sphinx($value, 'relevantword', 10000);
                    $kwlist[$key]['xgnum'] = count($xgs)?:0;
                    $kwlist[$key]['kw'] = $value; 
                }
            }
            return ['code'=>1, 'titkeyinfo'=>$titkeyinfo, 'kwlist'=>$kwlist];
        }
    }
    // 获取备案年龄
    public function beianage(){
        session_write_close();
        if (Request::isAjax()) {
            $urlarr = input('urlarr');
            $is_update = input('is_update');
            $beian_ct = input('beian_ct');
            $results = Db::query($sql = "select start_time,end_time,create_time from seo_website where website_url in (".$urlarr.")");
            if (!$results) {
                $time = 0;
                while (true) {
                    $results = Db::query($sql = "select start_time,end_time,create_time from seo_website where website_url in (".$urlarr.")");
                    
                    if ($results) {
                        if (count($results) >1) {
                            $result = $results[1]['start_time']?$results[1]:$results[0];
                        } else {
                            $result = $results[0];
                        }
                        break;
                    } else {
                        if ($time>7) break;
                        $time++;
                        sleep(1);
                    }
                }
            } else {
                if (count($results) >1) {
                    $result = $results[1]['start_time']?$results[1]:$results[0];
                } else {
                    $result = $results[0];
                }
                if ($is_update && $result['create_time'] == $beian_ct) {
                    while (true) {
                        $results = Db::query($sql = "select start_time,end_time,create_time from seo_website where website_url in (".$urlarr.")");
                        if (count($results) >1) {
                            $result = $results[1]['start_time']?$results[1]:$results[0];
                        } else {
                            $result = $results[0];
                        }
                        if ($result['create_time'] != $beian_ct) break;
                        else {
                            if ($time>7) break;
                            $time++;
                            sleep(1);
                        }
                    }
                }
            }
            
            $ymd = 1;
            $age = '';
            if ($result['start_time']) {
                $result['start_time'] = date('Y-m-d',strtotime($result['start_time']));//备案时间
                $result['start_timey'] = date('Y',strtotime($result['start_time']));//备案时间年
                $result['start_timem'] = date('m',strtotime($result['start_time']));//备案时间月
                $result['start_timed'] = date('d',strtotime($result['start_time']));//备案时间日
                $result['end_time'] = date('Y年m月d日', strtotime($result['end_time']));

                $nowy = date('Y',time());
                $nowm = date('m',time());
                $nowd = date('d',time());

                if ($nowd - $result['start_timed'] < 0) {
                    $result['difftimed'] = 30 + $nowd - $result['start_timed'];
                    $nowm--;
                } else {
                    $result['difftimed'] = $nowd - $result['start_timed'];
                }
                if ($nowm - $result['start_timem'] < 0) {
                    $result['difftimem'] = 12 + $nowm - $result['start_timem'];
                    $nowy--;
                } else {
                    $result['difftimem'] = $nowm - $result['start_timem'];
                }

                $result['difftimey'] = $nowy- $result['start_timey'];
                $age = $result['difftimey']?$result['difftimey'].'年':'';
                $age .= $result['difftimem']?$result['difftimem'].'月':'';
                $age .= $result['difftimed']?$result['difftimed'].'天':'';
                $age .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;( 创建于'.$result['start_timey'].'年'.$result['start_timem'].'月'.$result['start_timed'].'日，将于'.$result['end_time'].'到期 )';
            }
            return ['code'=>1, 'ymd'=>$ymd,'age'=>$age];
        }
    }
    //获取备案信息
    public function beianinfo(){
        session_write_close();
        if (Request::isAjax()) {
            $urlarr = input('urlarr');
            $is_update = input('is_update');
            $beian_ct = input('beian_ct');
            $results = Db::query($sql = "select status_time,record_num,nature,name,create_time from seo_website where website_url in (".$urlarr.")");
            if (!$results) {
                $time = 0;
                while (true) {
                    $results = Db::query($sql = "select status_time,record_num,nature,name,create_time from seo_website where website_url in (".$urlarr.")");
                    if ($results) {
                        if (count($results) >1) {
                            $result = $results[1]['record_num']?$results[1]:$results[0];
                        } else {
                            $result = $results[0];
                        } 
                        break;
                    } else {
                        if ($time>7) break;
                        $time++;
                        sleep(1);
                    }
                }
            } else {
                if (count($results) >1) {
                    $result = $results[1]['record_num']?$results[1]:$results[0];
                } else {
                    $result = $results[0];
                } 

                if ($is_update && $result['create_time'] == $beian_ct) {
                    while (true) {
                        $results = Db::query($sql = "select status_time,record_num,nature,name,create_time from seo_website where website_url in (".$urlarr.")");
                        if (count($results) >1) {
                            $result = $results[1]['record_num']?$results[1]:$results[0];
                        } else {
                            $result = $results[0];
                        } 
                        if ($result['create_time'] != $beian_ct) break;
                        else {
                            if ($time>7) break;
                            $time++;
                            sleep(1);
                        }
                    }
                }
            }
            return ['code'=>1, 'result'=>$result?:[]];
        }
    }
    // 获取收录反链
    public function getInclude(){
        session_write_close();
        // ajax请求
        if (Request::isAjax()) {
            $onearr = input('onearr');
            if ($res = $this->redis->lrange('TOP:'.$onearr, 0, -1)) {
                $arr = json_decode($res[0], 1);
                return ['code'=>1, 'redlist'=>$arr];
            } else {
                return ['code'=>0];
            }
        }
    }

    // 保存历史记录
    public function save_url(){
        $url = input('url');
        if (!urlmatch($url) && !ipmatch($url)) {
            return ['code'=>0, 'msg'=>'网址格式不正确'];
        }
        if ($save_url = cookie('save_url')) {
            if (!in_array($url, $save_url)) {
                array_unshift($save_url, $url);
                $save_url = array_unique($save_url);
                if (count($save_url)>10) {
                    $save_url = array_slice($save_url, 0, 10);
                }
                $code = 1;
            } else {
                $code = 2;
            }
        } else {
            $save_url = [$url];
            $code = 1;
        }
        cookie('save_url', $save_url);
        return ['code'=>$code, 'save_url'=>$save_url];
    }

    // 删除历史记录
    public function del_url(){
        $url = input('url');
        $save_url = cookie('save_url');
        foreach ($save_url as $k=>$s) {
            if ($s == $url) {
                unset($save_url[$k]);
            }
        }
        cookie('save_url', $save_url);
        return ['code'=>1];
    }
}