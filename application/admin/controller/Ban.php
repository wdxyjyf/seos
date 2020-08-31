<?php
namespace app\admin\controller;
use think\Db;
use think\facade\Request;
class Ban extends Common
{
    public function initialize(){
        parent::initialize();
    }
    // 违禁词设置
    //会员列表
    public function index(){
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('ban')
                ->where('ban','like',"%".$key."%")
                ->order('id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    public function addBan(){
        if(Request::isAjax()) {
            $ban = input('ban');
            $data = [];
            foreach ($ban as $k=>$v) {
                if (trim($v) && !Db::name('ban')->where('ban', trim($v))->find()) {
                    $data[] = ['ban'=>trim($v)];
                }
            }
            $data = array_unique($data, SORT_REGULAR);
            db('ban')->insertAll($data);
            $result['code'] = 1;
            $result['msg'] = '违禁词添加成功!';
            $result['url'] = url('index');
            return $result;
        }else{
            return $this->fetch('add');
        }
    }
    
    public function banDel(){
        db('ban')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }
    public function delall(){
        $map[] =array('id','IN',input('param.ids/a'));
        db('ban')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }
}