<?php
namespace app\home\controller;

use think\Db;
use app\admin\model\Goods as GoodsModel;


class Pointslog extends Common{
	public function initialize(){
        parent::initialize();
    }

    //积分兑换记录页面
    public function plist(){
        if(empty(session('usersmobile'))){
            $this->redirect('home/login/login');
            return false;
        }
        $umobile = session('usersmobile');
        $plist = Db::name('seo_goods_spend')
            ->where('umobile', $umobile)
            ->where('gid',0)
            ->order('id desc')
            ->paginate(5);
        $list = [];
        foreach ($plist as $key => $value) {
            $list[$key]['spendcode'] = $value['spendcode'];
            $list[$key]['content'] = $value['content'];
            $list[$key]['gname'] = '导出数据';
            $list[$key]['umobile'] = substr_replace($value['umobile'],'****',3,4);
            $list[$key]['create_time'] = date('Y-m-d',$value['create_time']);
        }
        // dump($list);exit;

        $page = $plist->render();
        $this->assign('page', $page);
        $this->assign('plist',$list);
        $data= base64_encode('njboseo'.session('usersid'));
        $url = $_SERVER['HTTP_HOST'].'/register'.'?uid='.$data;
        $this->assign('yqurl',$url); 

        $system = cache('System');
        $headtitle = '个人中心_积分兑换记录_'.$system['name'];
        $this->assign('headtitle',  $headtitle);

        $Keyword = '个人中心_积分兑换记录';
        $Desc = '展示会员在网站的下载记录';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc); 
        $this->assign('JS',['/static/home2/js/copy/clipboard.min.js','/static/home2/js/copy.js']);  
        return $this->fetch();
        
    }
}
