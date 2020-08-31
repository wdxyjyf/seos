<?php
namespace app\api\controller;

use think\facade\Env;
use think\facade\Request;
use Db;

class Wyc extends Common{
    public function initialize(){
        parent::initialize();
    }

    public function wycrecord(){
        set_time_limit(0);
        $kw = trim(input('changearticle'));//接收要搜索的值
        $num = db('seo_api_times')->where(['uid'=>$this->userinfo['id'], 'aid'=>2])->value('num');
        if ($num <= 0) {
            $info = [
                'StateCode' =>  10005,
                'Reason'    =>  'appKey剩余请求次数不足',
                'Result'    =>  ''
            ];
            returnApi($info);
        }
        if ($kw) {
            if(input('nolimit')) {
                if (input('nolimit') != 1) {
                    if(mb_strlen($kw, 'UTF-8')>3000) {
                        $info = [
                            'StateCode' =>  10003,
                            'Reason'    =>  '请求字数超过限制',
                            'Result'    =>  ''
                        ];
                        returnApi($info);
                    } 
                }
            } else {
                if(mb_strlen($kw, 'UTF-8')>3000) {
                    $info = [
                        'StateCode' =>  10003,
                        'Reason'    =>  '请求字数超过限制',
                        'Result'    =>  ''
                    ];
                    returnApi($info);
                } 
            }
            
            $data['transcont'] = str_replace('"', '\"', $kw);
            $data['transcont'] = str_replace("\r", '', $kw);
            $data['sign'] = 'false:'.uniqid();
            $res = $this->redis->rpush('original_text', JSON($data));
            $time = 0;
            while(1){
                if ($res = $this->redis->rpop($data['sign'])) {
                	decApiTimes($this->userinfo['id'], 2);
                    $info = [
                        'StateCode' =>  1,
                        'Reason'    =>  '成功',
                        'Result'    =>  $res
                    ];
                    break;
                }
                if (input('nolimit') != 1) {
                    if ($time>20) {
                        $info = [
                            'StateCode' =>  10004,
                            'Reason'    =>  '未请求到相关数据，请稍后试试(本次不扣使用次数)',
                            'Result'    =>  ''
                        ];
                        break;
                    }
                }
                sleep(1);
                $time++; 
            }
        } else {
            $info = [
                'StateCode' =>  10002,
                'Reason'    =>  '缺少伪原创参数！',
                'Result'    =>  ''
            ];
        }
        addApiLog($this->userinfo['id'], $this->userinfo['mobile'], 2, $info['StateCode'], $info['Reason'], mb_substr($kw, 0, 200));
        returnApi($info);
    }
}