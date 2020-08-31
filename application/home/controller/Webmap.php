<?php
namespace app\home\controller;

use think\Controller;
use think\Db;

class Webmap extends Controller{
	
    public function sitemapxml(){
        $res = db('seo_sitemap')->field('loc,priority,lastmod,changefreq')->select()?:[];//搜索量
        $this->makeXML($res);
    }

    function makeXML($res){
       $content='<?xml version="1.0" encoding="UTF-8"?>
       <urlset
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
       ';
        $data_array = [];
     
       $data_array= $res;
       foreach($data_array as $data){
        $content.=$this->create_item($data);
       }
       $content.='</urlset>';
       file_put_contents('sitemap.xml', $content);
       
    }
    function create_item($data){
        $item="<url>\n";
        $item.="<loc>".$data['loc']."</loc>\n";
        $item.="<priority>".$data['priority']."</priority>\n";
        $item.="<lastmod>".$data['lastmod']."</lastmod>\n";
        $item.="<changefreq>".$data['changefreq']."</changefreq>\n";
        $item.="</url>\n";
        return $item;
     }

    
}
