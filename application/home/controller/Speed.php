<?php
namespace app\home\controller;

use think\facade\Env;
use think\facade\Request;
use Db;

class Speed extends Common{
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
    // 测网站速度
    public function index()
    {
        $system = cache('System');
        $headtitle = '网站测速_网速测试_'.$system['name'];
        $this->assign('headtitle',  $headtitle);  
        $Keyword = '测网站速度';
        $Desc = '快速精准实时网站速度查询；100%准确率！';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);     
        return $this->fetch();
    }
    
    function getInfo($sign, $time = 0){
        sleep(1);
        $data = [];
        while ($res = $this->redis->rpop($sign)) {
            $data[] = json_decode($res, 1);
        }
        if ($data) {
            return $data;
        } else {
            if ($time>2) {
                return [];
            } else {
                $time = $time+0.5;
                return $this->getInfo($sign, $time);
            }
        }
    }

    public function map()
    {
        if (Request::isAjax()){
            $sign = input('post.sign');
            $data = $this->getInfo($sign);
            if (!$data) {
                return ['code'=>0];
            } else {
                foreach ($data as $k=>$v) {
                    $place_name = $v['place_name'];
                    $city = end(explode('/', $place_name));
                    $api = "http://api.map.baidu.com/geocoder?address=".$city."&output=json&key=qBSEriRSKw8eyHApKfrNapBoAcprW2Gc"; 
                    $output = json_decode(httpRequest($api, 'get'), 1);
                    $data[$k]['city'] = $city;
                    $data[$k]['isp'] = explode('/', $v['isp'])[0];
                    $data[$k]['speed'] = sprintf("%.2f", ($v['down_speed']/1024/1024));
                    $data[$k]['down_size'] = sprintf("%.2f", ($v['down_size']/1024));
                    $data[$k]['connect_time'] = $v['connect_time']."s";
                    $data[$k]['ip'] = explode(':', $v['proxy_ip'])[0];
                    $data[$k]['lng'] = $output['result']['location']['lng'];
                    $data[$k]['lat'] = $output['result']['location']['lat'];
                    if ($v['code']>= '400' || $v['code'] == '000') {
                        $data[$k]['total_time'] = 100;
                    }
                   
                }
                return ['code'=>1, 'data'=>$data];
            }
        }

        $url = input('website');
        $headtitle = '"'.$url.'"网速测试_搜一搜站长工具';
        $this->assign('headtitle',  $headtitle);  
        if (strstr($url, 'http://')) {
            $url = str_replace('http://', '', $url);
        }
        if (strstr($url, 'https://')) {
            $url = str_replace('https://', '', $url);
        }
        $sign = 'SPEED_'.uniqid();
        $data = [
            'url'   =>  $url,
            'sign'  =>  $sign
        ];
        $res = $this->redis->rpush('SPEED', json_encode($data));
        $this->assign('sign', $sign);

         $idc = [
            "广源IDC【国内稳定高防服务器】",
            " 阿里云IDC",
            "亿网互联"
        ];
        $this->assign('idc', $idc);

        $system = cache('System');
        // $headtitle = '测网站速度_'.$system['name'];
        
        $Keyword = '测网站速度';
        $Desc = '快速精准实时网站速度查询；100%准确率！';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);
        return $this->fetch();
    }

    public function test(){
        $ids = '3632';
        $ids = explode("\r\n", $ids);

        $res = Db::name('seo_monitor_keywords')->where('id', 'in', $ids)->delete();
        dump($res);
    }

}