<?php
namespace app\api\controller;

use think\facade\Env;
use think\facade\Request;
use Db;
class Redisapi{
	public function __construct()
	{
		$redis = new \Redis();
        $redis->connect('127.0.0.1', '6379');
        $redis->auth('baiou615');
        $this->redis = $redis;
	}
	public function getRedis()
	{
		$key = input('key');
		if (!$key) {
			$info = [
                'StateCode' =>  0,
                'Reason'    =>  '缺少参数'
            ];
            returnApi($info);
		}
		$value = $this->redis->get($key);
		if ($value) {
			$info = [
	            'StateCode' =>  1,
	            'Reason'    =>  '获取成功',
	            'data'		=>	$value
	        ];
		} else {
			$info = [
	            'StateCode' =>  0,
	            'Reason'    =>  '获取失败'
	        ];
		}
		returnApi($info);
	}

	public function setRedis()
	{
		$key = input('key');
		$value = input('value');
		if (!$key || !$value) {
			$info = [
                'StateCode' =>  0,
                'Reason'    =>  '缺少参数'
            ];
            returnApi($info);
		}
		$this->redis->set($key, $value);
		$info = [
            'StateCode' =>  1,
            'Reason'    =>  '存储成功'
        ];
        returnApi($info);
	}

}