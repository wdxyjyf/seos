<?php
namespace app\admin\model;
use think\Model;
class UsersGroup extends Model
{
    protected $type       = [
        // 设置addtime为时间戳类型（整型）
        'create_time' => 'timestamp:Y-m-d H:i:s',
    ];

    public function groupname($id) {
    	$info = UsersGroup::get($id);
    	return $info->title?$info->title:'未定义';  
    }

    public function grouptitle($name) {
    	$info = UsersGroup::where('title', $name)->find();
    	if ($info) {
    		return true;	
    	} else {
    		return false;
    	}
    }
} 