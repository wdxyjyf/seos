<?php
namespace app\api\controller;

use think\facade\Env;
use think\facade\Request;
use Db;

class keywords extends Common{
    public function initialize(){
        parent::initialize();
    }

    public function hotrecord(){
        $kw = trim(input('keyword'));//接收要搜索的值
        $num = db('seo_api_times')->where(['uid'=>$this->userinfo['id'], 'aid'=>1])->value('num');
        if ($num <= 0) {
            $info = [
                'StateCode' =>  10005,
                'Reason'    =>  'appKey剩余请求次数不足',
                'Result'    =>  ''
            ];
            returnApi($info);
        }
        if ($kw) {
            $kwcount = mb_strlen($kw,'UTF8');
            if (preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u", $kw) && $kwcount <=15) {
                $result2 = Db::name('seo_keyh_records')->where('keywordhotdig',$kw)->where('ipaddress',getIp())->max('create_time');
                if (!$result2 || time() - $result2 > 86400) {
                    $intkeywordwords['keywordhotdig'] = $kw;
                    $intkeywordwords['create_time'] = time();
                    $intkeywordwords['ipaddress'] = getIp();
                    Db::name('seo_keyh_records')->insert($intkeywordwords);//保存客户查询的关键词热度
                }
                //从相关表里读取数据
                $result = Db::name('seo_keyword_hotdig')->field('keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')->where("keyword = '$kw'")->find();
                if (empty($result)) {
                    //没有数据,存到redis,python爬取
                    $array = [
                        'word'  =>  'all',
                        'kw'    =>  $kw,
                    ];
                    // dump($array);exit;
                    // $kwharr = $this->redis->lrange('KWH',0,-1);
                    // if (!in_array(JSON($array), $kwharr)) {
                    $this->redis->rpush('KWH',JSON($array));
                    // $this->redis->rpush('KWH',JSON($array));
                    // }
                    $time = 0;
                    while (1) {
                        $result = Db::name('seo_keyword_hotdig')
                                ->field('keyword,averagePv,averagePvPc,averagePvMobile,averageDayPv,averageDayPvPc,averageDayPvMobile')
                                ->where("keyword = '$kw'")
                                ->find();
                        if ($result) {
                            decApiTimes($this->userinfo['id'], 1);
                            // $info = [
                            //     'StateCode' =>  1,
                            //     'Reason'    =>  '成功',
                            //     'Result'    =>  $result
                            // ];
                            break;
                        } else {
                            if ($time>5) {
                                $info = [
                                    'StateCode' =>  10004,
                                    'Reason'    =>  '未请求到相关数据，请换一个词试试(本次不扣使用次数)',
                                    'Result'    =>  ''
                                ];
                                break;
                            }
                            sleep(1);
                            $time++;
                        }
                    }
                } else {
                    decApiTimes($this->userinfo['id'], 1);
                    $info = [
                        'StateCode' =>  1,
                        'Reason'    =>  '成功',
                        'Result'    =>  $result
                    ];
                }
            } else {
                $info = [
                    'StateCode' =>  10003,
                    'Reason'    =>  '参数格式不正常！',
                    'Result'    =>  ''
                ];
            }
        } else {
            $info = [
                'StateCode' =>  10002,
                'Reason'    =>  '缺少关键词参数！',
                'Result'    =>  ''
            ];
        }
        returnApi($info);
    }
}