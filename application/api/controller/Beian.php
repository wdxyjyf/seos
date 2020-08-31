<?php
namespace app\api\controller;

use think\facade\Env;
use think\facade\Request;
use Db;

class Beian extends Common{
    public function initialize(){
        parent::initialize();
    }

    // 备案查询
    public function beian(){
        $url = input('url');
        $num = db('seo_api_times')->where(['uid'=>$this->userinfo['id'], 'aid'=>3])->value('num');
        if ($num <= 0) {
            $info = [
                'StateCode' =>  10005,
                'Reason'    =>  'appKey剩余请求次数不足',
                'Result'    =>  ''
            ];
            returnApi($info);
        }
        $test = parse_url($url);   
        if(array_key_exists('host', $test)){
            $onearr =  $test['host'];
        }else{
            if(strpos($test['path'], '/')){
               $onearr = strstr($test['path'], '/', TRUE);;
            }else{
                $onearr =  $test['path'];  
            }   
        }
        if (!urlmatch($onearr) && !ipmatch($onearr)) {
        	$info = [
                'StateCode' =>  10003,
                'Reason'    =>  '参数格式不正常',
                'Result'    =>  ''
            ];
        	returnApi($info);
        }
        $data = [];
        $data['url'] = $onearr;
        $data['topurl'] = $onearr;
        $data['update'] = 1;
        $data['beianinfo'] = 1;
        if (urlmatch($onearr)) {
        	$data['topurl'] = urlmatch($onearr);
            $where[] = $data['topurl'];
            if (strpos($data['topurl'], 'www.') === false) {
                $where[] = 'www.'.$data['topurl'];
            }
        } else {
            $where[] = $onearr;
        }
        $info = db('seo_website')->field('website_url,status_time,record_num,nature,name,create_time')->where('website_url', 'in', $where)->select();
        if ($info) {
        	if ($info['record_num']) {
        		$info = [
	                'StateCode' =>  1,
	                'Reason'    =>  '成功',
	                'Result'    =>  $info
	            ];
                if (input('nolimit') != 1) {
                    decApiTimes($this->userinfo['id'], 3);
                }
	        	returnApi($info);
        	} else {
        		$data['update'] = 1;
        	}
        }

        $this->redis->zadd('WEBURL',10000,JSON($data));
        sleep(1);
        $time = time()+10;

        while(1) {
        	$info = db('seo_website')->field('website_url,status_time,record_num,nature,name,create_time')->where('website_url', 'in', $where)->find();
        	if ($info['record_num'] || time()>$time) {
        		break;
        	}
        	sleep(1);
        }
        if ($info['record_num']) {
        	$info = [
                'StateCode' =>  1,
                'Reason'    =>  '成功',
                'Result'    =>  $info
            ];
            if (input('nolimit') != 1) {
                decApiTimes($this->userinfo['id'], 3);
            }
        	returnApi($info);
        } else {
        	$info = [
                'StateCode' =>  10004,
                'Reason'    =>  '未请求到相关数据',
                'Result'    =>  ''
            ];
        	returnApi($info);
        }
    }
}