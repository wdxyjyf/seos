<?php
namespace app\api\controller;

use think\facade\Env;
use think\facade\Request;
use Db;

class Monitor extends Common{
    public function initialize(){
        parent::initialize();
    }

    // 更新排名
    public function updatek(){
        $id = input('id');
        $rank = input('rank');
        $updateData = [
            'id'=>$id,
            'keyrank'=>$rank,
            'update_time'=>time()
        ];
        $res = Db::name('seo_monitor_keywords')->update($updateData);
        if ($res) {
            $info['StateCode'] = 1;
            $info['Reason'] = '更新成功';
        } else {
            $info['StateCode'] = 0;
            $info['Reason'] = '更新失败';
        }
        returnApi($info);
    }
    
    // 查询
    public function getk(){
        $enginetype = input('enginetype');
        $platform = input('platform');
        $limit = input('limit')?:0;
        $where[] = ['a.update_time', '<', strtotime(date('Y-m-d'))];
        if ($enginetype) {
            $where[] = ['a.enginetype', '=', $enginetype];
        }
        if ($platform) {
            $where[] = ['a.platform', '=', $platform];
        }
        $data = Db::name('seo_monitor_keywords a')->join('seo_monitor_website b', 'a.dmwebid = b.id', 'left')->where($where)->field('a.id,a.enginetype,a.platform,a.dmkeywords,a.keyrank,a.china_name,b.weburl')->limit($limit)->select();

        // echo Db::getLastSql();exit;
        if ($data) {
            $info['StateCode'] = 1;
            $info['Reason'] = '查询成功';
            $info['Result'] = $data;
        } else {
            $info['StateCode'] = 0;
            $info['Reason'] = '查询失败';
        }
        returnApi($info);
    }
}