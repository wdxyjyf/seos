<?php
namespace app\home\controller;
use think\Db;
use think\Request;
use think\Controller;
use think\facade\Env;
class Uploadimg extends Controller
{
    public function upload(){
        $fileKey = array_keys(request()->file());
        // 获取表单上传文件
        $img = request()->file($fileKey['0']);
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $img->validate(['ext' => 'jpg,png,gif,jpeg'])->move('uploads');
        if($info){
            $path = '/uploads/'.str_replace('\\','/',$info->getSaveName());
            // 成功上传后 获取上传信息
            return json_encode(['code' => 0, 'msg' => '上传成功!', 'url' =>$path]);
        }else{
            // 上传失败获取错误信息
            return json_encode(['code' => 1, 'msg' => $img->getError(), 'url' => '']);
        }
    }
    
    //多图上传
    public function upImages(){
        $fileKey = array_keys(request()->file());
        // 获取表单上传文件
        $file = request()->file($fileKey['0']);
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext' => 'jpg,png,gif,jpeg'])->move(Env::get('root_path') . 'public/uploads');
        if($info){
            $result['code'] = 0;
            $result['msg'] = '图片上传成功!';
            $path=str_replace('\\','/',$info->getSaveName());
            $result["src"] = '/uploads/'. $path;
            return $result;
        }else{
            // 上传失败获取错误信息
            $result['code'] =1;
            $result['msg'] = '图片上传失败!';
            return $result;
        }
    }
   
}