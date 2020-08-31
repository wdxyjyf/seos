<?php
namespace app\api\controller;
use think\Db;
use clt\Leftnav;
use think\Controller;
use app\common\taglib\IPRestrict;
use PHPExcel_IOFactory;
use PHPExcel;
use think\cache\Driver\Redis;

class Common extends Controller
{
    protected $userinfo;
    protected $redis;
    public function initialize()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods:POST,GET");
        header("Access-Control-Allow-Headers:x-requested-with,content-type");
        header("Content-type:text/json;charset=utf-8");

        // redis实例化
        $redis = new \Redis();
        $redis->connect('127.0.0.1', '6379');
        $redis->auth('baiou615');
        $this->redis = $redis;

        // 获取key
        $key = input('key');
        $userinfo = db('users')->where('token', $key)->find();
        if (!$userinfo) {
            $info = [
                'StateCode' =>  10001,
                'Reason'    =>  '错误的请求KEY',
                'Result'    =>  ''
            ];
            returnApi($info);
        } else {
            $this->userinfo = $userinfo;
        }

    }
    //空操作
    public function _empty(){
        return $this->fetch(APP_PATH.'404.html');
        // return $this->error('空操作，返回上次访问页面中...');
    }
}
