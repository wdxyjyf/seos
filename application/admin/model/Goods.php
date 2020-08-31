<?php
namespace app\admin\model;

use think\Model;

class Goods extends Model
{
	protected $name = 'seo_goods';
    protected $type       = [
        // 设置addtime为时间戳类型（整型）
        'create_time' => 'timestamp:Y-m-d H:i:s',
    ];
	
    public function getGoodname($id){
    	$goodname = Goods::where('id',$id)->value('goodname');
    	return $goodname;
    }
}