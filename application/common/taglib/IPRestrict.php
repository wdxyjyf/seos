<?php
namespace app\common\taglib;
use think\cache\Driver\Redis;
/**
 * PHP实现redis限制单ip、单用户的访问次数功能
 * Created by PhpStorm.
 * User: mingzhanghui
 * Date: 2018-09-14
 * Time: 14:18
 */
class IPRestrict {
    /**
     * @var Redis
     */
    private static $redis;
    /**
     * @var string
     * 取客户端真实ip地址作为key
     */
    private static $realip;
 
    public function __construct($host = '127.0.0.1', $port = 6379, $auth = null) {
        self::$redis = new \Redis();
        self::$redis->connect('127.0.0.1', '6379');
        self::$redis->auth('baiou615');
        $auth && self::$redis->auth($auth);
        self::$realip = "time:".self::getRealIP();
    }
 
    /**
     * 取得客户端IP地址
     * @return string
     */
    public static function getRealIP() {
        $realip = "";
        if (isset($_SERVER)) {
            foreach(['HTTP_X_FORWARED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'] as $name) {
                if (isset($_SERVER[$name])) {
                    $realip = $_SERVER[$name];
                    break;
                }
            }
        } else {
            $realip = getenv('HTTP_X_FORWARDED_FOR') || getenv('HTTP_CLIENT_IP') || getenv('REMOTE_ADDR');
        }
        return $realip;
    }
 
    /**
     * 记录该ip的访问次数 也可改成用户id
     * @param int $limit  限制指定时间内的访问磁珠
     * @param int $time  限制时间为60秒
     * @return int
     * @throws HttpException
     */
    public function requestCount($type = '', $limit = 3000, $time = 86400) {
        $key = self::$realip;
        if ($type === 'trans') {
            $key = "trans:".self::getRealIP();
            $limit = 10;
        }
        $redis = self::$redis;

        $exists = $redis->exists($key);
        $redis->incrby($key, 1);
        if ($exists) {
            $count = $redis->get($key);
            if ($count > $limit) {
                $res = ['code' =>1,'msg'=>'请求次数已用完,请登录进行更多操作!'];
                return $res;
                // throw new Exception('请求太频繁，请稍后再试!'.$count);
            }
        } else {
            // 首次计数 设定过期时间
            $redis->expire($key, $time);
        }
        $res = ['code' =>0];
        return $res;
        // return $redis->get($key);
    }
}
