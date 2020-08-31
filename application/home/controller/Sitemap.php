<?php
namespace app\home\controller;

use think\Db;

class Sitemap extends Common{
    public function makeSiteMap(){
        set_time_limit(0);
        $stime = time();
        $num = 4;
        $filelist = [];
        $path = "sitemap";
        $path = dir_path($path);
        // $list = glob($path.'*');
        // foreach ($list as $l) {
        //     if (!strstr($l, 'sitemap.xml')) {
        //         @unlink($l);
        //     }
        // }
        //首页
        $where = [];
        $where[] = ['title', '<>', ''];
        $where[] = ['keyword', '<>', ''];
        $where[] = ['description', '<>', ''];
        $count1 = Db::name('seo_website_info')->where($where)->where("right(website_url,1) NOT REGEXP '[0-9]'")->count();
        for ($i=1;$i<=$num;$i++) {
            $rand1 = rand(0, $count1-5000);
            $arr = Db::name('seo_website_info')->where($where)->where("right(website_url,1) NOT REGEXP '[0-9]'")->limit($rand1.",2000")->column('website_url');
            $str = $this->create($arr);
            $xml = $path.'index'.$i.'.xml';
            if (file_put_contents($xml, $str)) {
                $filelist[] = config('url.pact').'://'.config('url.host').'/'.$xml;
            }
        }
        // 关键词
        $count2 = Db::name('seo_keyword_hotdig')->max('id2');
        for ($i=1;$i<=$num;$i++) {
            $rand2 = rand(0, $count2-10000);
            $arr = Db::name('seo_keyword_hotdig')->where('id2', '>', $rand2)->where('char_length(keyword)<=8')->order('id2')->limit(2000)->column('keyword');
            $str = $this->create($arr, 'keyword', 1);
            $xml = $path.'keyword'.$i.'.xml';
            if (file_put_contents($xml, $str)) {
                $filelist[] = config('url.pact').'://'.config('url.host').'/'.$xml;
            }
        }
        // 相关词
        for ($i=1;$i<=$num;$i++) {
            $rand3 = rand(0, $count2-10000);
            $arr = Db::name('seo_keyword_hotdig')->where('id2', '>', $rand3)->where('char_length(keyword)<=8')->order('id2')->limit(2000)->column('keyword');
            $str = $this->create($arr, 'dig', 1);
            $xml = $path.'dig'.$i.'.xml';
            if (file_put_contents($xml, $str)) {
                $filelist[] = config('url.pact').'://'.config('url.host').'/'.$xml;
            }
        }
        // 长尾词
        $count4 = Db::name('seo_relevant_word')->max('id2');
        for ($i=1;$i<=$num;$i++) {
            $rand4 = rand(0, $count4-10000);
            $arr = Db::name('seo_relevant_word')->where('id2', '>', $rand4)->where('char_length(keyword)<=8')->order('id2')->limit(2000)->column('keyword');
            $str = $this->create($arr, 'related', 1);
            $xml = $path.'related'.$i.'.xml';
            if (file_put_contents($xml, $str)) {
                $filelist[] = config('url.pact').'://'.config('url.host').'/'.$xml;
            }
        }
        // 竞价
        for ($i=1;$i<=$num;$i++) {
            $rand5 = rand(0, $count2-10000);
            $arr = Db::name('seo_keyword_hotdig')->where('id2', '>', $rand5)->where('char_length(keyword)<=8')->order('id2')->limit(2000)->column('keyword');
            $str = $this->create($arr, 'compete', 1);
            $xml = $path.'compete'.$i.'.xml';
            if (file_put_contents($xml, $str)) {
                $filelist[] = config('url.pact').'://'.config('url.host').'/'.$xml;
            }
        }
        // 相关网站
        for ($i=1;$i<=$num;$i++) {
            $rand6 = rand(0, $count2-10000);
            $rand7 = rand(0, $count4-10000);
            $arr1 = Db::name('seo_keyword_hotdig')->where('id2', '>', $rand6)->where('char_length(keyword)<=8')->order('id2')->limit(1000)->column('keyword');
            $arr2 = Db::name('seo_relevant_word')->where('id2', '>', $rand7)->where('char_length(keyword)<=8')->order('id2')->limit(1000)->column('keyword');
            $arr = array_merge($arr1, $arr2);
            $str = $this->create($arr, 'findsites', 1); 
            $xml = $path.'findsites'.$i.'.xml';
            if (file_put_contents($xml, $str)) {
                $filelist[] = config('url.pact').'://'.config('url.host').'/'.$xml;
            }
        }
        // 备案
        for ($i=1;$i<=$num;$i++) {
            $rand1 = rand(0, $count1-5000);
            $arr = Db::name('seo_website_info')->where($where)->where("right(website_url,1) NOT REGEXP '[0-9]'")->limit($rand1.",2000")->column('website_url');
            $str = $this->create($arr, 'beian', 1);
            $xml = $path.'beian'.$i.'.xml';
            if (file_put_contents($xml, $str)) {
                $filelist[] = config('url.pact').'://'.config('url.host').'/'.$xml;
            }
        }
        $indexStr = $this->makeIndex($filelist);
        if (file_put_contents($path.'sitemap.xml', $indexStr)) {
            $spend_time = time()-$stime;
            echo "生成成功，".date('Y-m-d H:i:s')."，耗时".$spend_time."s";
        } else {
            echo "生成失败".date('Y-m-d H:i:s');
        }
    }

    public function create($arr, $prefix='', $enc=''){
        // xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        $str = '<?xml version="1.0" encoding="UTF-8"?>';
        $str .= '<urlset>';
        foreach ($arr as $v) {
           if (!strstr($v, '&')) {
                if ($enc) $v = urlsafe_b64encode($v);
                $str .= '<url>';
                if ($prefix) {
                    $str .= '<loc>'.config('url.pact').'://'.config('url.host').'/'.$prefix.'/'.$v.'</loc>';
                } else {
                    $str .= '<loc>'.config('url.pact').'://'.config('url.host').'/'.$v.'</loc>';
                }
                $str .= '<lastmod>'.date('Y-m-d').'</lastmod>';
                $str .= '<changefreq>daily</changefreq>';
                $str .= '<priority>0.8</priority>';
                $str .= '</url>';
            }
        }
        $str .= '</urlset>';
        return $str;
    }

    public function makeIndex($arr){
        // xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        $str = '<?xml version="1.0" encoding="UTF-8"?>';
        $str .= '<sitemapindex>';
        foreach ($arr as $v) {
            $str .= '<sitemap>';
            $str .= '<loc>'.$v.'</loc>';
            $str .= '<lastmod>'.date('Y-m-d').'</lastmod>';
            $str .= '</sitemap>';
        }
        $str .= '</sitemapindex>';
        return $str;
    }
}