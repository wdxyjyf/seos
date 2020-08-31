<?php
namespace app\admin\controller;
class Sitemap extends Common
{
    public function index(){
        if(request()->isPost()) {
            $link=db('seo_sitemap')->select();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$link,'rel'=>1];
        }
        return $this->fetch();
    }
   
    //添加网站地图
    public function add(){
        if(request()->isPost()){
            $data = input('post.');
            $data['lastmod'] = date('Y-m-d H:i:s');
            db('seo_sitemap')->insert($data);
            $result['code'] = 1;
            $result['msg'] = '网站地图添加成功!';
            $result['url'] = url('index');
            $res = db('seo_sitemap')->field('loc,priority,lastmod,changefreq')->select()?:[];//搜索量
            makeXML($res);
            return $result;
        }else{
            $this->assign('title','添加网站地图');
            $this->assign('info','null');
            return $this->fetch('form');
        }
    }
    //修改网站地图
    public function edit(){
        if(request()->isPost()){
            $data = input('post.');
            db('seo_sitemap')->update($data);
            $res = db('seo_sitemap')->field('loc,priority,lastmod,changefreq')->select()?:[];//搜索量
            makeXML($res);
            $result['code'] = 1;
            $result['msg'] = '网站地图修改成功!';
            $result['url'] = url('index');
            return $result;
        }else{
            $id=input('param.id');
            $info=db('seo_sitemap')->where(array('id'=>$id))->find();
            $this->assign('info',json_encode($info,true));
            $this->assign('title','修改网站地图');
            return $this->fetch('form');
        }
    }
    public function del(){
        db('seo_sitemap')->where(array('id'=>input('id')))->delete();
        $res = db('seo_sitemap')->field('loc,priority,lastmod,changefreq')->select()?:[];//搜索量
        if (!empty($res)) {
            makeXML($res);
        }
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    public function delall(){
        $map[] =array('id','IN',input('param.ids/a'));
        db('seo_sitemap')->where($map)->delete();
        $res = db('seo_sitemap')->field('loc,priority,lastmod,changefreq')->select()?:[];//搜索量
        if (!empty($res)) {
            makeXML($res);
        }
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }
}