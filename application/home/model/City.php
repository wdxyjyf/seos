<?php
namespace app\home\model;
use think\Model;
use think\Db;
class City extends Model {
    //根据id获取城市名字
    public function provinceName($id){
        if($id){
            //城市,名字
            $name = Db::name('city')->where('city_id',$id)->value('city_zh');
            return $name;
        } 
    }
    
    //根据id获取城市简称
    public function provinceShort($name){
        if($name){
            //城市,名字
            $cityname = Db::name('city')->where('city_zh',$name)->value('city_short');
            return $cityname;
        } 
    }
   
}

