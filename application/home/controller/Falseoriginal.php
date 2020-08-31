<?php
namespace app\home\controller;
use think\facade\Request;
use app\home\model\Users;

class Falseoriginal extends Common{
	public function initialize(){
        parent::initialize();
    }
    public function toplimit()
    {
        if ($uid = session('usersid')) {
            $info = Users::numInfo($uid);//记录用户次数表
            $infolist = Users::ruleInfo(session('userinfo.level'));//用户组获取对应权限信息
            if ($info['wyc_querynum'] >= $infolist['wyc_times']) {
                return ['code'=>2];
            }
        } else {
            $type = input('type');
            if(!$this->visit($type)){
                return ['code'=>0, 'msg'=>'查询次数已达上限,请登录查询更多结果'];
            }
        }
        $this->success();
    }

    //伪原创页面
    public function index(){
        $system = cache('System');
        $headtitle = '在线伪原创_'.$system['name'];
        $this->assign('headtitle',  $headtitle);     

        $Keyword = '在线伪原创,伪原创工具,伪原创软件';
        $Desc = '在线伪原创工具，为内容运营，自媒体运营，小说写作打造快速，覆盖全，质量高的伪原创内容生态圈。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/weiyuanc.css']);
        $this->assign('JS',['/static/home2/js/trans.js']);
        if(Request::isAjax()) {
            $data  = [];
            $data['transcont'] = input('transform');
            $data['sign'] = 'false:'.uniqid();
            $this->redis->rpush('original_text', JSON($data));
            return ['code'=>1,'sign'=>$data['sign']];
        }
        // 字数权限
        $wycInfo = db('users a')->join('users_rule b', 'a.level = b.groupid', 'left')->field('b.price, b.wyc_num')->where('a.id', session('userinfo.id'))->find();
        $maxPrice = db('users_rule')->max('price');
        $ruleInfo = [
            'wyc_num'=>$wycInfo['wyc_num'],
            'is_max' =>$wycInfo['price'] == $maxPrice?1:0,
        ];
        $this->assign('rule', $ruleInfo);
        // 广告
        $ad = $this->getAd(13);
        $this->assign('ad', $ad);
        return $this->fetch();
    }
    // 提交结果
    public function transForm(){
        if(Request::isAjax()) {
            $sign = input('sign');
            if ($res = $this->redis->rpop($sign)) {
                if ($uid = session('usersid')) {
                    Users::addUsersNum($uid, 'wyc_querynum');
                }
                return ['code'=>1, 'info'=>$res];
            } else {
                return ['code'=>0, 'msg'=>'服务器当前使用人数过多，请稍后重试。'];
            }
        }
    }
    // // 提交结果
    // public function transForm(){
    //     set_time_limit(0);
    //     if(Request::isAjax()) {
    //         $data  = [];
    //         $data['transcont'] = input('transform');
    //         $data['sign'] = 'false_'.uniqid();
    //         $this->redis->rpush('original_text', JSON($data));
    //         $time = 0;
    //         while(1){
    //             if ($res = $this->redis->rpop($data['sign'])) {
    //                 return ['code'=>1, 'info'=>$res];
    //                 break;
    //             }
    //             if ($time>20) {
    //                 return ['code'=>0, 'msg'=>'系统繁忙，请稍后重试'];
    //             }
    //             sleep(1);
    //             $time++; 
    //         }
    //     }
    // }
}
