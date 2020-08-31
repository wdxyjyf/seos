<?php
namespace app\api\controller;

use think\facade\Env;
use think\facade\Request;
use Db;

class Shoulu{
    public function getSl(){
        // redis实例化
        $redis = new \Redis();
        $redis->connect('127.0.0.1', '6379');
        $redis->auth('baiou615');
        $kw = input('url');//接收要搜索的值
        $sign = "INCLUDE:".uniqid();
        $data['urls'] = [$kw];
        $data['sign'] = $sign;
        $data['engine'] = '1';
        $data['platform'] = '1';
        $redis->rpush('INCLUDE', json_encode($data));
        $time = 0;
        $res = $redis->rpop($data['sign']);
        while(1){
            if ($time > 5) break;
            if ($res = $redis->rpop($data['sign'])) break;
            sleep(1);
            $time++; 
        }
        return $res ?: '';
    }
}