<?php
namespace app\home\controller;
use think\Db;
use clt\Leftnav;
use think\Controller;
use app\common\taglib\IPRestrict;
use PHPExcel_IOFactory;
use PHPExcel;
use think\cache\Driver\Redis;
use app\home\model\Users;
use think\Log;

class Common extends Controller
{
    protected $pagesize;
    protected $redis;
    function __construct() {
        parent::__construct();
        //判断用户会员剩余天数,否则更新为免费用户
        $this->checkLevel();
    }
    public function initialize()
    {
        // redis实例化
        $redis = new \Redis();
        $redis->connect('127.0.0.1', '6379');
        $redis->auth('jyf123456');
        $this->redis = $redis;

        $system = cache('System');
        $this->assign('config',$system);
        
       // session_write_close(); 
        //友情链接
        $linkList = cache('linkList');
        if(!$linkList){
            $linkList = Db::name('link')->where('open',1)->order('sort asc')->select();
            cache('linkList', $linkList, 3600);
        }
        $this->assign('linkList', $linkList);
        // 悬浮广告
        $suspension = $this->getAd(10);
        $this->assign('suspension', $suspension);

        if (session('usersid')) {
            $yqurl = config('url.pact').'://'.config('url.host').'/register?uid='.base64_encode('njboseo'.session('usersid'));
            session('yqurl', $yqurl);
        }
    }
    //空操作
    public function _empty(){
        return $this->fetch('404.html');
        // return $this->error('空操作，返回上次访问页面中...');
    }

    //限制单ip、单用户的访问次数
    public function visit($type=''){
        if(!session('usersid')){
            $restrict = new IPRestrict();
            $arr = $restrict->requestCount($type);
            if($arr['code'] == 1){
                return false;
            }else{
                return true;
            }
        }else{
           return true;
        }

    }
    
    //判断用户会员剩余天数,否则更新为免费用户
    private function checkLevel() {
        if (session('usersid')) {
            $userinfo = Db::name('users')->where('id',session('usersid'))->find();
            if (!$userinfo) {
                session(null);
            } elseif ($userinfo['level'] != 1 && time() > $userinfo['endtime']) {
                $userinfo['level'] = 1;
                $userinfo['opentime'] = '';
                $userinfo['endtime'] = '';
                Db::name('users')->update($userinfo);
                session('userinfo', $userinfo);
            } else {
                session('userinfo', $userinfo);
            }
        }
    }
    function info2excel($list, $title=''){

        $keys = array_keys($list[0]);
        $lie = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        //导出表格
        $objExcel = new \PHPExcel();
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
        // 设置水平垂直居中
        $objExcel->getActiveSheet()->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objExcel->getActiveSheet()->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        // 字体和样式
        $objExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
        $objExcel->getActiveSheet()->getStyle('A2:AB2')->getFont()->setBold(true);
        $objExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        // 第一行、第二行的默认高度
        $objExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
        //设置某一列的宽度
        for ($i=0;$i<count($keys);$i++) {
            $objExcel->getActiveSheet()->getColumnDimension($lie[$i])->setWidth(20);
        }  

        //设置表头
        //  合并
        $objExcel->getActiveSheet()->mergeCells('A1:'.$lie[count($keys)-1].'1');
        $objActSheet = $objExcel->getActiveSheet(0);
        $objActSheet->setTitle($title.'统计');//设置excel的标题
        $objActSheet->setCellValue('A1',$title.'统计');
        for ($i=0;$i<count($keys);$i++) {
            $objActSheet->setCellValue($lie[$i].'2', $keys[$i]);
        }

        $baseRow = 3; //数据从N-1行开始往下输出 这里是避免头信息被覆盖
        foreach ($list as $r=>$d) {
            $i = $baseRow + $r;
            for ($j=0;$j<count($keys);$j++) {
                $objExcel->getActiveSheet()->setCellValue($lie[$j].$i,$d[$keys[$j]]);
            }
        }

        $objExcel->setActiveSheetIndex(0);
        //4、输出
        $objExcel->setActiveSheetIndex();
        header('Content-Type: applicationnd.ms-excel');
        $time=date('YmdHis');
        header("Content-Disposition: attachment;filename={$title}统计$time.xls");
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }

    public function getPoint()
    {
        echo Db::name('users')->where('id', session('usersid'))->value('point');
    }

    // 导出数据
    public function export2excel()
    {
        $list = json_decode(input('shuju'), 1);
        $title = input('title');
        $point = input('point');
        $uid = session('usersid');
        $insertData = [
            'uid'   =>  session('usersid'),
            'umobile'=> session('usersmobile'),
            'spendcode'=>$point,
            'create_time'=>time(),
            'content'=>$title,
        ];
        Db::name('seo_goods_spend')->insert($insertData);
        if ($point) {
            Db::name('users')->where('id',$uid)->setDec('point',$point);
            $this->info2excel($list, '搜一搜站长工具_'.$title);
        } else {
            $this->info2excel($list, '搜一搜站长工具_'.$title);
        }  
    }

    public function daochunum (){
        $export = input('exporttype');//接收导出类型
        $uid = session('usersid');
        $level = session('userinfo.level');
        $info = Users::ruleInfo($level);
        $keynum = Users::numInfo($uid);
        if ($keynum[$export]) {
            if ($keynum[$export] >= $info[$export]) {
                return ['code'=>0];//次数用完扣除积分
            } else {
                $keynum[$export] += 1;
                Db::name('users_num')->where('userid',$uid)->setField($export, $keynum[$export]);
                return ['code'=>1];
            }
        } else {
            $keynum[$export] = 1;
            Db::name('users_num')->where('userid',$uid)->setField($export, $keynum[$export]);
            return ['code'=>1];
        }
    }

    public function querrplnum($type) {
        $level = session('userinfo.level');
        if ($level) {
            $info = Users::ruleInfo($level);
            if ($info) {
                if ($type == 'includ_plsubmit') {
                    $res = $info['includ_plsubmit'];
                } elseif ($type == 'beian_plsubmit'){
                    $res = $info['beian_plsubmit'];
                } elseif ($type == 'rank_plsubmit'){
                    $res = $info['rank_plsubmit'];
                } elseif ($type == 'keyword_plsubmit'){
                    $res = $info['keyword_plsubmit'];
                }
            }
        } else {
            $res = 20;
        }
        return $res;
    }

    // 获取广告
    public function getAd($as_id, $duo = false){
        $where = 'as_id = '.$as_id.' and open = 1 and unix_timestamp(endtime) >'.time();
        if ($duo) {
            $ad = Db::name('ad')->where($where)->order('sort')->select();
        } else {
            $ad = Db::name('ad')->where($where)->order('sort')->find();
        }
        return $ad;
    }

    // 百度推送
    public function baiduPush($urls){
        $api = 'http://data.zz.baidu.com/urls?site='.$_SERVER['HTTP_HOST'].'&token=fapHUQOE97bypL4a';
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        return $result;
    }

    // 熊账号推送
	public function bearPush($urls, $type='realtime'){
		$api = 'http://data.zz.baidu.com/urls?appid=1649912815824870&token=WzwZnsFa7xPE6xOZ&type='.$type;
		$ch = curl_init();
		$options =  array(
		    CURLOPT_URL => $api,
		    CURLOPT_POST => true,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_POSTFIELDS => implode("\n", $urls),
		    CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
		);
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		return $result;
	}

    // 推送
    public function push(Log $log){
        session_write_close();
        $url = input('post.url');
        $urls = array($url);
        $logInfo['url'] = $url;
        $baiduPushList = $this->redis->zrange('baiduPushList', 0, -1);
        if (!in_array($url, $baiduPushList)) {
            $json1 = $this->baiduPush($urls);
            $arr1 = json_decode($json1, 1);
            if (!$arr1['error']) {
                $this->redis->zadd('baiduPushList', 1, $url);
            }
            $logInfo['百度推送'] = $json1;
        }

        $json2 = $this->bearPush($urls);
        $arr2 = json_decode($json2, 1);
        if ($arr2['success'] == 0) {
            $json2 = $this->bearPush($urls, 'batch');
        }
        $logInfo['熊掌号推送'] = $json2;
        $log->write($logInfo, 'baiduPush');
        return 'push done';
    }

    // 生成sitemap
    public function makeSiteMap(){
        $arr = Db::name('seo_website_info')->where('website_url', 'NOT REGEXP', "/((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}/")->orderRand()->limit(5000)->column('website_url');
        $count = count($arr);
        $str = '<?xml version="1.0" encoding="UTF-8"?>';
        $str .= '<urlset>';
        for ($i=0; $i <$count; $i++) { 
            if (!strstr($arr[$i], '&')) {
                $str .= '<url>';
                $str .= '<loc>'.$arr[$i].'</loc>';
                $str .= '<priority>1.0</priority>';
                $str .= '<lastmod>'.date('Y-m-d').'</lastmod>';
                $str .= '<changefreq>daily</changefreq>';
                $str .= '<priority>0.8</priority>';
                $str .= '</url>';
            }
        }
        $str .= '</urlset>';
        $res = file_put_contents('./sitemap/sitemap.xml', $str);
        echo "生成成功".date('Y-m-d H:i:s');
    } 
}
