<?php
namespace app\admin\model;

use think\Model;

class Goodspend extends Model
{
	protected $name = 'seo_goods_spend';
    protected $type       = [
        // 设置addtime为时间戳类型（整型）
        'create_time' => 'timestamp:Y-m-d H:i:s',
    ];
	

}