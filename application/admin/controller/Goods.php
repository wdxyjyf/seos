<?php
namespace app\admin\controller;
use think\Db;
use think\facade\Request;
use app\admin\model\Goods as GoodsModel;
class Goods extends Common
{
    public function initialize(){
        parent::initialize();
    }
    //商城列表
    public function index(){
        if(Request::isAjax()) {
            $key = input('post.key');
            $this->assign('testkey', $key);
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = Db::table('seo_goods')
                ->where('goodname', 'like', "%" . $key . "%")
                ->order('sort')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['create_time'] = date('Y-m-d H:s',$v['create_time']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    //添加商品
    public function add(){
        if(Request::isAjax()) {
            //构建数组
            $data = Request::except('file');
            $data['create_time'] = time();
            db('seo_goods')->insert($data);
            $result['code'] = 1;
            $result['msg'] = '添加成功!';
            $result['url'] = url('index');
            return $result;
        }else{
            $this->assign('title',lang('add').'商品');
            $this->assign('info','null');
            return $this->fetch('form');
        }
    }
    //修改商品
    public function edit(){
        if(Request::isAjax()) {
            $data = Request::except('file');
            db('seo_goods')->update($data);
            $result['code'] = 1;
            $result['msg'] = '修改成功!';
            $result['url'] = url('index');
            return $result;
        }else{
            $id=input('id');
            $goodsInfo=db('seo_goods')->where(array('id'=>$id))->find();
            $this->assign('info',json_encode($goodsInfo,true));
            $this->assign('title',lang('edit').'商品');
            return $this->fetch('form');
        }
    }
    //设置商品状态
    public function editState(){
        $id=input('post.id');
        $is_online=input('post.is_online');
        if(db('seo_goods')->where('id='.$id)->update(['is_online'=>$is_online])!==false){
            return ['status'=>1,'msg'=>'设置成功!'];
        }else{
            return ['status'=>0,'msg'=>'设置失败!'];
        }
    }
    //设置排序
    public function editSort(){
        $ad=db('seo_goods');
        $data = input('post.');
        if($ad->update($data)!==false){
            return $result = ['msg' => '操作成功！','url'=>url('index'), 'code' =>1];
        }else{
            return $result = ['code'=>0,'msg'=>'操作失败！'];
        }
    }
    public function del(){
        db('seo_goods')->where(array('id'=>input('id')))->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    public function delall(){
        $map[] =array('id','in',input('param.ids/a'));
        db('seo_goods')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }
    

    /***************************兑换记录*****************************/
    //兑换记录列表
    public function spendlog(){
        if(Request::isAjax()) {
            $key = input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = Db::table('seo_goods_spend')
                ->where('umobile', 'like', "%" . $key . "%")
                ->order('create_time desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['create_time'] = date('Y-m-d H:s',$v['create_time']);
                if($v['gid'] == 0){
                    $list['data'][$k]['goodname'] = '搜一搜站长工具 -- 导出数据';
                }else{
                    $list['data'][$k]['goodname'] = GoodsModel::getGoodname($v['gid']);
                }
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    public function dellog(){
        db('seo_goods_spend')->where(array('id'=>input('id')))->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    public function delalllog(){
        $map[] =array('id','in',input('param.ids/a'));
        db('seo_goods_spend')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }
   
}