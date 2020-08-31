<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Admin;
use think\captcha\Captcha;
class Order extends Controller
{
    // 订单列表
    public function index()
    {
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('seo_order')
                ->field('id,uid,mobile,order_id,title,buy_time,total,status,FROM_UNIXTIME(addtime) addtime,FROM_UNIXTIME(paytime) paytime')
                ->where('mobile|order_id','like',"%".$key."%")
                ->order('id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }

    public function delete()
    {
        db('seo_order')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }

    public function deleteAll()
    {
        $map[] =array('id','IN',input('param.ids/a'));
        db('seo_order')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }
}