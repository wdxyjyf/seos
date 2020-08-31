<?php
namespace app\home\controller;
use think\Db;

class About extends Common{
	public function initialize(){
        parent::initialize();
    }

    //关于我们页面1
    public function about(){
        $system = cache('System');
        $headtitle = '关于我们_'.$system['name'];
        $this->assign('headtitle',  $headtitle);  
        $Keyword = '搜一搜站长简介';
        $Desc = '搜一搜站长网成立于2019年,主办单位是南京白鸥网络技术有限公司， 是一家专门针对中文站点提供服务的网站，主要为广大站长提供站长工具查询';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);   
        $this->assign('CSS',['/static/home2/css/about.css']);
        return $this->fetch();
    }

     //联系我们页面
    public function lxwm(){
        $system = cache('System');
        $headtitle = '联系我们_'.$system['name'];
        $this->assign('headtitle',  $headtitle);  
        $Keyword = '搜一搜站长联系我们';
        $Desc = '为了便于沟通及精准解决用户的问题，烦请在添加联系方式时将问题描述清楚及找到合适的客服来解决问题；您的反馈是我们不断优化前行的动力。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/about.css']);    
        return $this->fetch();
    }

    //使用协议页面
    public function agreement(){
        $system = cache('System');
        $headtitle = '使用协议_'.$system['name'];
        $this->assign('headtitle',  $headtitle);  
        $Keyword = '搜一搜站长联系我们';
        $Desc = '为了便于沟通及精准解决用户的问题，烦请在添加联系方式时将问题描述清楚及找到合适的客服来解决问题；您的反馈是我们不断优化前行的动力。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);    
        $this->assign('CSS',['/static/home2/css/about.css']);    
        return $this->fetch();
    }

    public function s(){
        set_time_limit(0);
        $str = file_get_contents('keywords.txt');
        $arr = explode("\r\n", $str);
        foreach ($arr as $v) {
            if (!Db::name('ban')->where('ban', $v)->find()) {
                Db::name('ban')->insert(['ban'=>$v]);
            }
        }
        echo '执行成功';
    }
}