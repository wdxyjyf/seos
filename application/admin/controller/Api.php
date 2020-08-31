<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Admin;
use think\captcha\Captcha;
use think\facade\Request;
class Api extends Controller
{
    // API订单列表
    public function api_index()
    {
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('seo_api_order')
                ->field('id,uid,mobile,order_id,title,buy_num,total,status,FROM_UNIXTIME(addtime) addtime,FROM_UNIXTIME(paytime) paytime')
                ->where('mobile|order_id','like',"%".$key."%")
                ->order('id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }

    public function api_delete()
    {
        db('seo_api_order')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }

    public function api_deleteAll()
    {
        $map[] =array('id','IN',input('param.ids/a'));
        db('seo_api_order')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }

    // API列表
    public function list()
    {
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = db('seo_api_times a')
                ->join('users b', 'a.uid=b.id', 'left')
                ->join('seo_api c', 'a.aid=c.id', 'left')
                ->field('a.id,a.uid,a.total_num,a.num,FROM_UNIXTIME(a.create_time) create_time,FROM_UNIXTIME(a.modify_time) modify_time,b.mobile,c.title')
                ->where('b.mobile|c.title','like',"%".$key."%")
                ->order('id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }

    public function times_delete()
    {
        db('seo_api_times')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }

    public function times_deleteAll()
    {
        $map[] =array('id','IN',input('param.ids/a'));
        db('seo_api_times')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }

    // API列表
    public function index()
    {
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = db('seo_api')
                ->field('id,api,title,description,status,num,price,img,example,FROM_UNIXTIME(create_time) create_time')
                ->where('title|description|api','like',"%".$key."%")
                ->order('id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }


    public function addapi(){
        if(Request::isAjax()) {
            $data = Request::except('file');
            $check_api = db('seo_api')->where('api', $data['api'])->find();
            if ($check_api) {
                return $result = ['code'=>0,'msg'=>'API地址已存在!'];
            }
            $check_title = db('seo_api')->where('title', $data['title'])->find();
            if ($check_title) {
                return $result = ['code'=>0,'msg'=>'API名称已存在!'];
            }
            $data['create_time'] = time();
            db('seo_api')->insert($data);
            $result['code'] = 1;
            $result['msg'] = 'API添加成功!';
            $result['url'] = url('index');
            return $result;
        }else{
            $this->assign('info', 'null');
            $this->assign('title',lang('add').'API');
            return $this->fetch('edit');
        }
    }

    public function editapi(){
        if(Request::isAjax()){
            $data = Request::except('file');
            $check_api = db('seo_api')->where('api', $data['api'])->where('id', 'neq', $data['id'])->find();
            if ($check_api) {
                return $result = ['code'=>0,'msg'=>'API地址已存在!'];
            }
            $check_title = db('seo_api')->where('title', $data['title'])->where('id', 'neq', $data['id'])->find();
            if ($check_title) {
                return $result = ['code'=>0,'msg'=>'API名称已存在!'];
            }
            if (db('seo_api')->update($data)!==false) {
                $result['msg'] = 'API修改成功!';
                $result['url'] = url('index');
                $result['code'] = 1;
            } else {
                $result['msg'] = 'API修改失败!';
                $result['code'] = 0;
            }
            return $result;
        }else{
            $id=input('id');
            $info = db('seo_api')->find($id);
            $info['endtime'] = date('Y-m-d',$info['endtime']);
            $this->assign('info',json_encode($info,true));
            $this->assign('title',lang('edit').'API');
            $this->assign('user_level',json_encode($user_level,true));
            return $this->fetch('edit');
        }
    }

    public function apis_delete()
    {
        db('seo_api')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }

    public function apis_deleteAll()
    {
        $map[] =array('id','IN',input('param.ids/a'));
        db('seo_api')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }

}