<?php
namespace app\admin\controller;
use app\admin\model\Users as UsersModel;
use think\facade\Request;
use app\admin\model\UsersGroup;
use app\admin\model\UsersRule;
use think\Db;
use app\admin\model\Goods;
use app\admin\model\Goodspend;
class Users extends Common{
    //会员列表
    public function index(){
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('users')->alias('u')
                ->join('users_group ul','u.level = ul.id','left')
                ->field('u.*,ul.title')
                ->where('u.email|u.mobile|u.username','like',"%".$key."%")
                ->order('u.id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            // dump($list);exit;
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['reg_time'] = date('Y-m-d H:i',$v['reg_time']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    public function addUser(){
        if(Request::isAjax()) {
            //构建数组
            $data = Request::except('file');
            // dump($data);exit;
            $check_user = UsersModel::get(['mobile'=>$data['mobile']]);
            if ($check_user) {
                return $result = ['code'=>0,'msg'=>'用户已存在!'];
            }
            if($data['email']){
                $check_email = UsersModel::get(['email'=>$data['email']]);
                if ($check_email) {
                    return $result = ['code'=>0,'msg'=>'该邮箱已注册!'];
                }
            }
            $data['reg_time'] = time();
            $level = explode(':',$data['level']);
            $data['level'] = $level[1];
            if ($data['level'] == 1) {
                $data['opentime'] = null;
                $data['endtime'] = null;
            } else {
                $data['opentime'] = time();
                if (strtotime($data['endtime']) - time() <0) {
                    return $result = ['code'=>0,'msg'=>'到期时间不能少于今天!'];
                } else {
                    $data['endtime'] = strtotime($data['endtime']);
                }
            }
            $data['password'] = md5($data['password']);
            // $province =explode(':',$data['province']);
            // $data['province'] = isset( $province[1])?$province[1]:'';
            // $city =explode(':',$data['city']);
            // $data['city'] = isset( $city[1])?$city[1]:'';
            // $district =explode(':',$data['district']);
            // $data['district'] = isset( $district[1])?$district[1]:'';
            db('users')->insert($data);
            $result['code'] = 1;
            $result['msg'] = '会员添加成功!';
            $result['url'] = url('index');
            return $result;
        }else{
            $user_level=db('users_group')->select();
            $this->assign('info','null');
            $this->assign('title',lang('add').lang('user'));
            $this->assign('user_level',json_encode($user_level,true));
            // $province = db('Region')->where ( array('pid'=>1) )->select ();
            // $this->assign('province',json_encode($province,true));
            // $city = db('Region')->where ( array('pid'=>$info['province']) )->select ();
            // $this->assign('city',json_encode($city,true));
            // $district = db('Region')->where ( array('pid'=>$info['city']) )->select ();
            // $this->assign('district',json_encode($district,true));

            return $this->fetch('edit');
        }
    }
    //设置会员状态
    public function usersState(){
        $id=input('post.id');
        $is_lock=input('post.is_lock');
        if(db('users')->where('id='.$id)->update(['is_lock'=>$is_lock])!==false){
            return ['status'=>1,'msg'=>'设置成功!'];
        }else{
            return ['status'=>0,'msg'=>'设置失败!'];
        }
    }
    public function edit(){
        if(Request::isAjax()){
            $data = Request::except('file');
            
            $user = db('users');
            $info = $user->where('id', $data['id'])->find();
           
            // $data = input('post.');
            $level =explode(':',$data['level']);
            $data['level'] = $level[1];
            if ($data['level'] == 1){
                $data['opentime'] = '';
                $data['endtime'] = '';
            } 
            $where[] = ['id','<>',$data['id']];
            if($data['mobile']){
                $where[] = ['mobile','=',$data['mobile']];
                $check_user = UsersModel::where($where)->find();
                if ($check_user) {
                    return $result = ['code'=>0,'msg'=>'用户已存在'];
                }
            }
            $where2[] = ['id','<>',$data['id']];
            if($data['email']){
                $where2[] = ['email','=',$data['email']];
                $check_email = UsersModel::where($where2)->find();
                if ($check_email) {
                    return $result = ['code'=>0,'msg'=>'邮箱已存在'];
                }
            }
            if ($data['endtime'] && !$info['opentime']){
                $data['endtime'] = strtotime($data['endtime']);
                $data['opentime'] = time();
            } else {
                $data['endtime'] = strtotime($data['endtime']);
            }
           
            // $province =explode(':',$data['province']);
            // $data['province'] = isset( $province[1])?$province[1]:'';
            // $city =explode(':',$data['city']);
            // $data['city'] = isset( $city[1])?$city[1]:'';
            // $district =explode(':',$data['district']);
            // $data['district'] = isset( $district[1])?$district[1]:'';
            if(empty($data['password'])){
                unset($data['password']);
            }else{
                $data['password'] = md5($data['password']);
            }
            if ($user->update($data)!==false) {
                $result['msg'] = '会员修改成功!';
                $result['url'] = url('index');
                $result['code'] = 1;
            } else {
                $result['msg'] = '会员修改失败!';
                $result['code'] = 0;
            }
            return $result;
        }else{
            $id=input('id');
            $user_level=db('users_group')->select();
            $info = UsersModel::get($id);
            $info['endtime'] = date('Y-m-d',$info['endtime']);
            // dump($info);exit;
            $this->assign('info',json_encode($info,true));
            $this->assign('title',lang('edit').lang('user'));
            $this->assign('user_level',json_encode($user_level,true));
            // $province = db('Region')->where ( array('pid'=>1) )->select ();
            // // $this->assign('province',json_encode($province,true));
            // $city = db('Region')->where ( array('pid'=>$info['province']) )->select ();
            // $this->assign('city',json_encode($city,true));
            // $district = db('Region')->where ( array('pid'=>$info['city']) )->select ();
            // $this->assign('district',json_encode($district,true));
            return $this->fetch();
        }
    }

    public function getRegion(){
        $Region=db("region");
        $pid = input("pid");
        $arr = explode(':',$pid);
        $map['pid']=$arr[1];
        $list=$Region->where($map)->select();
        return $list;
    }

    public function usersDel(){
        db('users')->delete(['id'=>input('id')]);
        db('oauth')->delete(['uid'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }
    public function delall(){
        $map[] =array('id','IN',input('param.ids/a'));
        db('users')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }

    //扣除积分操作
    public function spendPoint(){
        if(Request::isAjax()){
            $gid = input("goodid");
            if($gid){
                $arr = Goods::get($gid);
                $list = $arr->ticket;
                return $list;
            }else{
                Db::startTrans();
                try {
                    $data = input('post.');
                    if($data['spendcode'] > $data['point']){
                        $result = ['code'=>0,'msg'=>'原有积分不足'];
                    }else{
                        $point = $data['point'] - $data['spendcode'];
                        $res = UsersModel::update(['id' => $data['id'], 'point' => $point]);
                        if ($res!==false) {
                            $spendlog['uid'] = $data['id'];
                            $spendlog['umobile'] = $data['mobile'];
                            $spendlog['gid'] = $data['gid'];
                            $spendlog['spendcode'] = $data['spendcode'];
                            $spendlog['content'] = $data['content'];
                            $spendlog['create_time'] = time();
                            if (Goodspend::create($spendlog)) {
                                $result = ['code'=>1,'msg'=>'扣除成功!','url'=>url('index')];
                            } else {
                                $result = ['code'=>0,'msg'=>'扣除失败'];
                            }
                        } 
                    }
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    $result['msg'] = '扣除失败';
                    $result['code'] = 0;
                }
                return $result;
            }
            
        }else{
            $goodsinfo = db('seo_goods')->where('is_online',1)->order('sort')->select();
            $this->assign('goodsinfo',$goodsinfo);
            $id = input('id');
            $info = UsersModel::get($id);
            $this->assign('info',json_encode($info,true));
            $this->assign('title','扣除积分');
            return $this->fetch();
        }
    }
    //查询是否有商品
    public function allGoods(){
        $id = input('id');
        $point = input('point');
        $goodsinfo = db('seo_goods')->where('is_online',1)->order('sort')->select();
        if(empty($goodsinfo)){
            $result['msg'] = '暂无商品无法扣除积分!';
            $result['code'] = 0;
        }else{
            if($point <= 0){
                $result['msg'] = '积分不足!';
                $result['code'] = 0;
            }else{
                $result['url'] = url('spendPoint',array('id'=>$id));
                $result['code'] = 1;
            }
        }
        return $result;
    }

    /***********************************会员组***********************************/
    public function userGroup(){
        if(request()->isPost()){
            $userLevel=db('users_group');
            $list = UsersGroup::all();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list,'rel'=>1];
        }
        return $this->fetch();
    }
    //会员组添加
    public function groupAdd(){
        if(request()->isPost()){
            $data = input('post.');
            if (UsersGroup::grouptitle($data['title'])) {
                return ['code'=>0,'msg'=>'会员组添加失败，该名称已存在!','url'=>url('userGroup')];
            } else {
                $data['create_time']=time();
                UsersGroup::create($data);
                return ['code'=>1,'msg'=>'会员组添加成功!','url'=>url('userGroup')];
            }
        }else{
            $this->assign('title',lang('add')."会员组");
            $this->assign('info','null');
            return $this->fetch('groupForm');
        }
    }
    //会员组修改
    public function groupEdit(){
        if(request()->isPost()) {
            $data = input('post.');
           
            $where2[] = ['id','<>',$data['id']];
            if($data['title']){
                $where2[] = ['title','=',$data['title']];
                $title = UsersGroup::where($where2)->find();
                if ($title) {
                    return ['code'=>0,'msg'=>'会员组名称已存在!'];
                }
            }
            $where['id'] = $data['id'];
            UsersGroup::update($data,$where);
            $result['msg'] = '会员组修改成功!';
            $result['url'] = url('userGroup');
            $result['code'] = 1;
            return $result;
        }else{
            $id = input('param.group_id');
            $info = UsersGroup::get(['id'=>$id]);
            $this->assign('title',lang('edit')."会员组");
            $this->assign('info',json_encode($info,true));
            return $this->fetch('groupForm');
        }
    }
    //会员组删除
    public function groupDel(){
        $group_id=input('group_id');
        if (empty($group_id)){
            return ['code'=>0,'msg'=>'会员组ID不存在！'];
        }
        db('users_group')->where(array('id'=>$group_id))->delete();
        db('users_rule')->where('groupid',$group_id)->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }

    /********************************权限管理*******************************/
    //会员权限添加
    public function ruleAdd(){
        if(request()->isPost()){
            $data = input('post.');
            if (array_key_exists('id', $data)) {
                $where['id'] = $data['group_id'];
                UsersRule::update($data,$where);
                $result['msg'] = '会员权限编辑成功!';
                $result['url'] = url('userGroup');
                $result['code'] = 1;
                return $result;
            } else {
                $data['create_time']=time();
                UsersRule::create($data);
                $result['msg'] = '会员权限添加成功!';
                $result['url'] = url('userGroup');
                $result['code'] = 1;
            }
            return $result;
        }else{
            $id = input('get.group_id');
            $groupname = UsersGroup::groupname($id);
            $info = UsersRule::get(['groupid'=>$id]);
            if (empty($info)) {
                $this->assign('info','null');
                $this->assign('title',lang('add')."会员权限");
            } else {
                $this->assign('title',lang('edit')."会员权限");
                $this->assign('info',json_encode($info,true));
            }
            $this->assign('groupname',$groupname);
            $this->assign('groupid',$id);
            return $this->fetch('ruleForm');
        }
    }

    /***********************************邀请好友列表***********************************/
    public function userVisit(){
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('users')->alias('u')
                ->join('users uu', 'u.yaoqing_id=uu.id', 'left')
                ->field('u.id,u.yaoqing_id,u.mobile,u.reg_time')
                ->where('u.yaoqing_id','<>',0)
                ->where('u.mobile|uu.mobile','like',"%".$key."%")
                ->order('u.id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['reg_time'] = date('Y-m-d H:i',$v['reg_time']);
                $list['data'][$k]['yaoqing_id'] = UsersModel::getMobile($v['yaoqing_id']);
                $list['data'][$k]['point'] = 10;
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }



}