<?php
use AlibabaCloud\Client\AlibabaCloud;

use AlibabaCloud\Client\Exception\ClientException;

use AlibabaCloud\Client\Exception\ServerException;

use think\cache\Driver\Redis;
use itbdw\Ip\IpLocation;

function JSON($array) {
    arrayRecursive($array, 'urlencode', true);
    $json = json_encode($array);
    return urldecode($json);
}
//字符串截取,其他的...省略
function subtext($text, $length=10)
{
    if(mb_strlen($text, 'utf8') > $length)
    return mb_substr($text,0,$length,'utf8').' …';
    return $text;
}

//字符串截取,其他的...省略
function subtext2($text, $length=10)
{
    if(strlen($text) > $length)
    return substr($text,0,$length).' …';
    return $text;
}

/**
* $count 每页多少条数据
* $page  当前第几页
* $array 要分页的数组
*/
function page_array($count,$page,$array){
    $page=(empty($page))?'1':$page; #判断当前页面是否为空 如果为空就表示为第一页面 
    $start=($page-1)*$count; #计算每次分页的开始位置
    $totals=count($array);  
    $countpage=ceil($totals/$count); #计算总页面数
    $pagedata=array();
    $pagedata=array_slice($array,$start,$count);
    return $pagedata;  #返回查询数据
}


/**************************************************************
     *
     *  使用特定function对数组中所有元素做处理
     *  @param  string  &$array     要处理的字符串
     *  @param  string  $function   要执行的函数
     *  @return boolean $apply_to_keys_also     是否也应用到key上
     *  @access public
     *
     *************************************************************/
function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
{
    static $recursive_counter = 0;
    if (++$recursive_counter > 1000) {
        die('possible deep recursion attack');
    }
    foreach ($array as $key => $value) {
        if (is_array($value)) {
           arrayRecursive($array[$key], $function, $apply_to_keys_also);
        } else {
            $array[$key] = $function($value);
        }

        if ($apply_to_keys_also && is_string($key)) {
            $new_key = $function($key);
            if ($new_key != $key) {
                $array[$new_key] = $array[$key];
                unset($array[$key]);
            }
        }
    }
    $recursive_counter--;
}
//缓存
function savecache($name = '',$id='') {
    if($name=='Field'){
        if($id){
            $Model = db($name);
            $data = $Model->order('sort')->where('moduleid='.$id)->column('*', 'field');
            $name=$id.'_'.$name;
            $data = $data ? $data : null;
            cache($name, $data);
        }else{
            $module = cache('Module');
            foreach ( $module as $key => $val ) {
                savecache($name,$key);
            }
        }
    }elseif($name=='System'){
        $Model = db ( $name );
        $list = $Model->where(array('id'=>1))->find();
        cache($name, $list);
    }elseif($name=='Module'){
        $Model = db ( $name );
        $list = $Model->order('sort')->select ();
        $pkid = $Model->getPk ();
        $data = array ();
        $smalldata= array();
        foreach ( $list as $key => $val ) {
            $data [$val [$pkid]] = $val;
            $smalldata[$val['name']] =  $val [$pkid];
        }
        cache($name, $data);
        cache('Mod', $smalldata);
    }elseif($name == 'cm'){
        $list = db('category')
            ->alias('c')
            ->join('module m','c.moduleid = m.id')
            ->order('c.sort')
            ->field('c.*,m.title as mtitle,m.name as mname')
            ->select();
        cache($name, $list);
    }else{
        $Model = db ($name);
        $list = $Model->order('sort')->select ();
        $pkid = $Model->getPk();
        $data = array ();
        foreach ( $list as $key => $val ) {
            $data [$val [$pkid]] = $val;
        }
        cache($name, $data);
    }
    return true;
}

//正则验证url合法性
function urlmatch($url){
    $urlmatch ="/([\x{4e00}-\x{9fa5}]*-*\w*-*\w*\.(?:com\.cn|gov\.cn|edu\.cn|xz\.cn|cn|com\.hk|yn\.cn|org\.cn|net\.cn|tv|law|COM|CN|ORG|COM\.CN|la|cc|com|co|tk|gov|pw|me|edu|ws|mil|biz|name|pro|aero|coop|museum|org|top|vip|club|xin|shop|ltd|wang|online|store|so|net|xyz|art|auto|beer|center|chat|citic|city|cloud|company|cool|hk|design|email|fashion|fit|fun|fund|gold|group|guru|host|info|ink|kim|life|link|live|love|luxe|mobi|plus|press|pub|red|ren|run|show|site|social|sohu|space|team|tech|today|video|website|wiki|world|work|yoga|zone|in|us|ch|hn|cm|re|vc|gs|wf|fm|gl|ru|asia|ag|im|bs|si|io|cd|cx|ci|gg|tm|ml|li|ee|tw|cat|travel|tn|lu|ly|sc|ba|win|政务\.cn|政务|中信|中国|中文网|企业|佛山|信息|公司|公益|商城|商店|商标|在线|集团|移动|娱乐|广东|我爱你|手机|招聘|时尚|游戏|网址|网店|网络)\/?$)/isu";
    preg_match($urlmatch, $url, $match);
    return $match[0]?:false;
    // if ($match) {
    //     return $match;
    // } else {
    //     return false;
    // }
}
//正则验证url合法性
function urlmatchall($url){
    $urlmatch ="/((?:\w+\.)?[-\w]+\.(?:com\.cn|gov\.cn|edu\.cn|xz\.cn|cn|com\.hk|yn\.cn|org\.cn|net\.cn|tv|law|COM|CN|ORG|COM\.CN|la|cc|com|co|tk|gov|pw|me|edu|ws|mil|biz|name|pro|aero|coop|museum|org|top|vip|club|xin|shop|ltd|wang|online|store|so|net|xyz|art|auto|beer|center|chat|citic|city|cloud|company|cool|hk|design|email|fashion|fit|fun|fund|gold|group|guru|host|info|ink|kim|life|link|live|love|luxe|mobi|plus|press|pub|red|ren|run|show|site|social|sohu|space|team|tech|today|video|website|wiki|world|work|yoga|zone|in|us|ch|hn|cm|re|vc|gs|wf|fm|gl|ru|asia|ag|im|bs|si|io|cd|cx|ci|gg|tm|ml|li|ee|tw|cat|travel|tn|lu|ly|sc|ba|win|政务\.cn|政务|中信|中国|中文网|企业|佛山|信息|公司|公益|商城|商店|商标|在线|集团|移动|娱乐|广东|我爱你|手机|招聘|时尚|游戏|网址|网店|网络))/";
    preg_match_all($urlmatch, $url, $match);
    return $match[0];
}

//正则验证ip合法性
function ipmatch($ip) {
    $ipmatch ="/((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}/";
    if (!preg_match($ipmatch, $ip)) {
        return false;
    } else {
        return true;
    }
}

function stringmatch($string) {
    $stringmatch = "/^[\x{4e00}-\x{9fa5}A-Za-z0-9]+$/u";
    if (preg_match($stringmatch, $string)) {
        return true;
    } else {
        return false;
    }
}
//处理url最后一个/
function endurl($kw){
    $test = parse_url($kw);//获取主域名
    if(array_key_exists('host', $test)){
        $onearr =  $test['host'];
    }else{
        if(strpos($test['path'], '/')){
            $onearr = strstr($test['path'], '/', TRUE);
        }else{
            $onearr =  $test['path'];  
        }
    }
    return $onearr;
}
function style($title_style){
    $title_style = explode(';',$title_style);
    return  $title_style[0].';'.$title_style[1];
}
//请求返回
function callback($status = 0,$msg = '', $url = null, $data = ''){
    $data = array(
        'msg'=>$msg,
        'url'=>$url,
        'data'=>$data,
        'status'=>$status
    );
    return $data;
}

function getvalidate($info){
    $validate_data=array();
    if($info['minlength']) $validate_data['minlength'] = ' minlength:'.$info['minlength'];
    if($info['maxlength']) $validate_data['maxlength'] = ' maxlength:'.$info['maxlength'];
    if($info['required']) $validate_data['required'] = ' required:true';
    if($info['pattern']) $validate_data['pattern'] = ' '.$info['pattern'].':true';
    $errormsg='';
    if($info['errormsg']){
        $errormsg = ' title="'.$info['errormsg'].'"';
    }
    $validate= implode(',',$validate_data);
    $validate= 'validate="'.$validate.'" ';
    $parseStr = $validate.$errormsg;
    return $parseStr;
}
function string2array($info) {
    if($info == '') return array();
    eval("\$r = $info;");
    return $r;
}
function array2string($info) {
    if($info == '') return '';
    if(!is_array($info)){
        $string = stripslashes($info);
    }
    foreach($info as $key => $val){
        $string[$key] = stripslashes($val);
    }
    $setup = var_export($string, TRUE);
    return $setup;
}
//初始表单
function getform($form,$info,$value=''){
    $type = $info['type'];
    return  $form->$type($info,$value);
}
//文件单位换算
function byte_format($input, $dec=0){
    $prefix_arr = array("B", "KB", "MB", "GB", "TB");
    $value = round($input, $dec);
    $i=0;
    while ($value>1024) {
        $value /= 1024;
        $i++;
    }
    $return_str = round($value, $dec).$prefix_arr[$i];
    return $return_str;
}
//时间日期转换
function toDate($time, $format = 'Y-m-d H:i:s') {
    if (empty ( $time )) {
        return '';
    }
    $format = str_replace ( '#', ':', $format );
    return date($format, $time );
}
//地址id转换名称
function toCity($id){
    if (empty ( $id )) {
        return '';
    }
    $name = db('region')->where(['id'=>$id])->value('name');
    return $name;
}
function template_file($module=''){
    $viewPath = config('template.view_path');
    $viewSuffix = config('template.view_suffix');
    $viewPath = $viewPath ? $viewPath : 'view';
    $filepath = think\facade\Env::get('app_path').strtolower(config('default_module')).'/'.$viewPath.'/';
    $tempfiles = dir_list($filepath,$viewSuffix);
    $arr=[];
    foreach ($tempfiles as $key=>$file){
        $dirname = basename($file);
        if($module){
            if(strstr($dirname,$module.'_')) {
                $arr[$key]['value'] =  substr($dirname,0,strrpos($dirname, '.'));
                $arr[$key]['filename'] = $dirname;
                $arr[$key]['filepath'] = $file;
            }
        }else{
            $arr[$key]['value'] = substr($dirname,0,strrpos($dirname, '.'));
            $arr[$key]['filename'] = $dirname;
            $arr[$key]['filepath'] = $file;
        }
    }
    return  $arr;
}
function dir_list($path, $exts = '', $list= array()) {
    $path = dir_path($path);
    $files = glob($path.'*');
    foreach($files as $v) {
        $fileext = fileext($v);
        if (!$exts || preg_match("/\.($exts)/i", $v)) {
            $list[] = $v;
            if (is_dir($v)) {
                $list = dir_list($v, $exts, $list);
            }
        }
    }
    return $list;
}
function dir_path($path) {
    $path = str_replace('\\', '/', $path);
    if(substr($path, -1) != '/') $path = $path.'/';
    return $path;
}
function fileext($filename) {
    return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}
function checkField($table,$value,$field){
    $count = db($table)->where(array($field=>$value))->count();
    if($count>0){
        return true;
    }else{
        return false;
    }
}
/**
+----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
+----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function rand_string($len=6,$type='',$addChars='') {
    $str ='';
    switch($type) {
        case 0:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 1:
            $chars= str_repeat('0123456789',3);
            break;
        case 2:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
            break;
        case 3:
            $chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 4:
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
            break;
    }
    if($len>10 ) {//位数过长重复字符串一定次数
        $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
    }
    if($type!=4) {
        $chars   =   str_shuffle($chars);
        $str     =   substr($chars,0,$len);
    }else{
        // 中文随机字
        for($i=0;$i<$len;$i++){
            $str.= msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
        }
    }
    return $str;
}

/**
 * 验证输入的邮件地址是否合法
 */
function is_email($user_email)
{
    $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
    if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false) {
        if (preg_match($chars, $user_email)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * 验证输入的手机号码是否合法
 */
function is_mobile_phone($mobile_phone)
{
    $chars = "/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|16[0-9]{1}[0-9]{8}$|19[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$/";
    if (preg_match($chars, $mobile_phone)) {
        return true;
    }
    return false;
}

/**
 * 验证输入的密码是否合法
 */
function is_password($password)
{
    $chars = "/^[0-9a-zA-Z#-_*%$]{6,20}$/";
    if (preg_match($chars, $password)) {
        return true;
    }
    return false;
}
/**
 * 验证输入的值是否为正整数
 */
function is_intnum($int)
{
    $chars = "/^[1-9][0-9]*$/";
    if (preg_match($chars, $int)) {
        return true;
    }
    return false;
}
/**
 * 取得IP
 *
 * @return string 字符串类型的返回结果
 */
function getIp(){
    if (@$_SERVER['HTTP_CLIENT_IP'] && $_SERVER['HTTP_CLIENT_IP']!='unknown') {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (@$_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['HTTP_X_FORWARDED_FOR']!='unknown' && isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match('/^\d[\d.]+\d$/', $ip) ? $ip : '';
}

//字符串截取
function str_cut($sourcestr,$cutlength,$suffix='...')
{
    $returnstr='';
    $i=0;
    $n=0;
    $str_length=strlen($sourcestr);//字符串的字节数
    while (($n<$cutlength) and ($i<=$str_length))
    {
        $temp_str=substr($sourcestr,$i,1);
        $ascnum=Ord($temp_str);//得到字符串中第$i位字符的ascii码
        if ($ascnum>=224)    //如果ASCII位高与224，
        {
            $returnstr=$returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
            $i=$i+3;            //实际Byte计为3
            $n++;            //字串长度计1
        }
        elseif ($ascnum>=192) //如果ASCII位高与192，
        {
            $returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i=$i+2;            //实际Byte计为2
            $n++;            //字串长度计1
        }
        elseif ($ascnum>=65 && $ascnum<=90) //如果是大写字母，
        {
            $returnstr=$returnstr.substr($sourcestr,$i,1);
            $i=$i+1;            //实际的Byte数仍计1个
            $n++;            //但考虑整体美观，大写字母计成一个高位字符
        }
        else                //其他情况下，包括小写字母和半角标点符号，
        {
            $returnstr=$returnstr.substr($sourcestr,$i,1);
            $i=$i+1;            //实际的Byte数计1个
            $n=$n+0.5;        //小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($n>$cutlength){
        $returnstr = $returnstr . $suffix;//超过长度时在尾处加上省略号
    }
    return $returnstr;
}
//删除目录及文件
function dir_delete($dir) {
    $dir = dir_path($dir);
    if (!is_dir($dir)) return FALSE;
    $list = glob($dir.'*');
    foreach($list as $v) {
        is_dir($v) ? dir_delete($v) : @unlink($v);
    }
    return @rmdir($dir);
}
/**
 * CURL请求
 * @param $url 请求url地址
 * @param $method 请求方法 get post
 * @param null $postfields post数据数组
 * @param array $headers 请求header信息
 * @param bool|false $debug  调试开启 默认false
 * @return mixed
 */
function httpRequest($url, $method, $postfields = null, $headers = array(), $debug = false) {
    $method = strtoupper($method);
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
    curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
    switch ($method) {
        case "POST":
            curl_setopt($ci, CURLOPT_POST, true);
            if (!empty($postfields)) {
                $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
            }
            break;
        default:
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
            break;
    }
    $ssl = preg_match('/^https:\/\//i',$url) ? TRUE : FALSE;
    curl_setopt($ci, CURLOPT_URL, $url);
    if($ssl){
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
    }
    //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
    //curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ci, CURLINFO_HEADER_OUT, true);
    /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
    $response = curl_exec($ci);
    $requestinfo = curl_getinfo($ci);
    $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    if ($debug) {
        echo "=====post data======\r\n";
        var_dump($postfields);
        echo "=====info===== \r\n";
        print_r($requestinfo);
        echo "=====response=====\r\n";
        print_r($response);
    }
    curl_close($ci);
    return $response;
    //return array($http_code, $response,$requestinfo);
}
/**
 * @param $arr
 * @param $key_name
 * @return array
 * 将数据库中查出的列表以指定的 id 作为数组的键名
 */
function convert_arr_key($arr, $key_name)
{
    $arr2 = array();
    foreach($arr as $key => $val){
        $arr2[$val[$key_name]] = $val;
    }
    return $arr2;
}
//查询IP地址
function getCity($ip = ''){
    $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
    if(empty($res)){ return false; }
    $jsonMatches = array();
    preg_match('#\{.+?\}#', $res, $jsonMatches);
    if(!isset($jsonMatches[0])){ return false; }
    $json = json_decode($jsonMatches[0], true);
    if(isset($json['ret']) && $json['ret'] == 1){
        $json['ip'] = $ip;
        unset($json['ret']);
    }else{
        return false;
    }
    return $json;
}
function getCity2($ip = '') {
    if($ip){
        $url= "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        $ipp =json_decode(@file_get_contents($url));
        if($ipp->code=='1'){
            return false;
        }else {
            if ($ipp->data->region) {
                $cityayy = ['北京','天津','上海','重庆'];
                $cityayy2 = ['香港','澳门']; 
                $cityarr3 = ['内蒙古','新疆','西藏','广西','宁夏'];
                if(in_array($ipp->data->region,$cityayy)){
                    $city = $ipp->data->region."市";
                }elseif(in_array($ipp->data->region,$cityayy2)){
                    $city = $ipp->data->region."特别行政区";
                }elseif(in_array($ipp->data->region,$cityarr3)){
                    $city = $ipp->data->region."自治区".$ipp->data->city."市";
                }else{
                    $city = $ipp->data->region."省".$ipp->data->city."市";
                }
                return $city;
            } else {
               return false; 
            }
        }
    }else{
        return false;
    }
}
function get($url)  {  
   // 生成一个curl对象  
   $curl = curl_init(); 
   // 配置curl中的http协议->可配置的荐可以查PHP手册中的curl_  
   curl_setopt($curl, CURLOPT_URL, $url);  
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);  
   curl_setopt($curl, CURLOPT_HEADER, FALSE);  
   // 执行这个请求  
   return curl_exec($curl);  
}  
function getCity3($ip){
    if ($ip) {
        $url="www.baidu.com/s?wd=".$ip; 
        $data= get($url);
        $city_preg = '/<span class="c-gap-right">.*<\/span>.*  /U';
        preg_match_all($city_preg,$data,$list);
        $str = trim(strip_tags(strstr($list[0][0],'</span>')));
        return explode(" ",$str)[0]?:'';
    } else{
        return false;
    }
}
function getCity4($ip){
    if ($ip) {
        $array = IpLocation::getLocation($ip);
        return $array['area'];
    } else{
        return false;
    }
}
function getInfoByIp($ip){
    if ($ip) {
        $array = IpLocation::getLocation($ip);
        return $array;
    } else{
        return false;
    }
}
//判断图片的类型从而设置图片路径
function imgUrl($img,$defaul=''){
    if($img){
        if(substr($img,0,4)=='http'){
            $imgUrl = $img;
        }else{
            $imgUrl = $img;
        }
    }else{
        if($defaul){
            $imgUrl = $defaul;
        }else{
            $imgUrl = '/static/admin/images/tong.png';
        }

    }
    return $imgUrl;
}
/**
 * PHP格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}
/**
 * 判断当前访问的用户是  PC端  还是 手机端  返回true 为手机端  false 为PC 端
 *  是否移动端访问访问
 * @return boolean
 */
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        return true;

    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
    {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            return true;
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT']))
    {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        }
    }
    return false;
}


function is_weixin() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    } return false;
}

function is_qq() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'QQ') !== false) {
        return true;
    } return false;
}
function is_alipay() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
        return true;
    } return false;
}

/**
 * 获取用户信息
 * @param $user_id_or_name  用户id 邮箱 手机 第三方id
 * @param int $type  类型 0 user_id查找 1 邮箱查找 2 手机查找 3 第三方唯一标识查找
 * @param string $oauth  第三方来源
 * @return mixed
 */
function get_user_info($user_id_or_name,$type = 0,$oauth=''){
    $map = array();
    if($type == 0){
        $map[] = ['user_id','=',$user_id_or_name];
    }
    if($type == 1){
        $map[] = ['email','=',$user_id_or_name];
    }
    if($type == 2){
        $map[] = ['mobile','=',$user_id_or_name];
    }
    if($type == 3){
        $map[] = ['openid','=',$user_id_or_name];
        $map[] = ['oauth','=',$oauth];
    }
    if($type == 4){
        $map[] = ['unionid','=',$user_id_or_name];
        $map[] = ['oauth','=',$oauth];
    }
    if($type == 5){
        $map[] = ['nickname','=',$user_id_or_name];
    }
    $user = db('users')->where($map)->find();
    return $user;
}
/**
 * 过滤数组元素前后空格 (支持多维数组)
 * @param $array 要过滤的数组
 * @return array|string
 */
function trim_array_element($array){
    if(!is_array($array))
        return trim($array);
    return array_map('trim_array_element',$array);
}
/**
 * @param $arr
 * @param $key_name
 * @return array
 * 将数据库中查出的列表以指定的 值作为数组的键名，并以另一个值作为键值
 */
function convert_arr_kv($arr,$key_name,$value){
    $arr2 = array();
    foreach($arr as $key => $val){
        $arr2[$val[$key_name]] = $val[$value];
    }
    return $arr2;
}

/**
 * 邮件发送
 * @param $to    接收人
 * @param string $subject   邮件标题
 * @param string $content   邮件内容(html模板渲染后的内容)
 * @throws Exception
 * @throws phpmailerException
 */
function send_email($to,$subject='',$content=''){
    
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $arr = db('config')->where('inc_type','smtp')->select();
    $config = convert_arr_kv($arr,'name','value');

    $mail->CharSet  = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    //调试输出格式
    //$mail->Debugoutput = 'html';
    //smtp服务器
    $mail->Host = $config['smtp_server'];
    //端口 - likely to be 25, 465 or 587
    $mail->Port = $config['smtp_port'];

    if($mail->Port == '465') {
        $mail->SMTPSecure = 'ssl';
    }// 使用安全协议
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //发送邮箱
    $mail->Username = $config['smtp_user'];
    //密码
    $mail->Password = $config['smtp_pwd'];
    //Set who the message is to be sent from
    $mail->setFrom($config['smtp_user'],$config['email_id']);
    //回复地址
    //$mail->addReplyTo('replyto@example.com', 'First Last');
    //接收邮件方
    if(is_array($to)){
        foreach ($to as $v){
            $mail->addAddress($v);
        }
    }else{
        $mail->addAddress($to);
    }

    $mail->isHTML(true);// send as HTML
    //标题
    $mail->Subject = $subject;
    //HTML内容转换
    $mail->msgHTML($content);
    return $mail->send();
}
function safe_html($html){
    $elements = [
        'html'      =>  [],
        'body'      =>  [],
        'a'         =>  ['target', 'href', 'title', 'class', 'style'],
        'abbr'      =>  ['title', 'class', 'style'],
        'address'   =>  ['class', 'style'],
        'area'      =>  ['shape', 'coords', 'href', 'alt'],
        'article'   =>  [],
        'aside'     =>  [],
        'audio'     =>  ['autoplay', 'controls', 'loop', 'preload', 'src', 'class', 'style'],
        'b'         =>  ['class', 'style'],
        'bdi'       =>  ['dir'],
        'bdo'       =>  ['dir'],
        'big'       =>  [],
        'blockquote'=>  ['cite', 'class', 'style'],
        'br'        =>  [],
        'caption'   =>  ['class', 'style'],
        'center'    =>  [],
        'cite'      =>  [],
        'code'      =>  ['class', 'style'],
        'col'       =>  ['align', 'valign', 'span', 'width', 'class', 'style'],
        'colgroup'  =>  ['align', 'valign', 'span', 'width', 'class', 'style'],
        'dd'        =>  ['class', 'style'],
        'del'       =>  ['datetime'],
        'details'   =>  ['open'],
        'div'       =>  ['class', 'style'],
        'dl'        =>  ['class', 'style'],
        'dt'        =>  ['class', 'style'],
        'em'        =>  ['class', 'style'],
        'font'      =>  ['color', 'size', 'face'],
        'footer'    =>  [],
        'h1'        =>  ['class', 'style'],
        'h2'        =>  ['class', 'style'],
        'h3'        =>  ['class', 'style'],
        'h4'        =>  ['class', 'style'],
        'h5'        =>  ['class', 'style'],
        'h6'        =>  ['class', 'style'],
        'header'    =>  [],
        'hr'        =>  [],
        'i'         =>  ['class', 'style'],
        'img'       =>  ['src', 'alt', 'title', 'width', 'height', 'id', 'class'],
        'ins'       =>  ['datetime'],
        'li'        =>  ['class', 'style'],
        'mark'      =>  [],
        'nav'       =>  [],
        'ol'        =>  ['class', 'style'],
        'p'         =>  ['class', 'style'],
        'pre'       =>  ['class', 'style'],
        's'         =>  [],
        'section'   =>  [],
        'small'     =>  [],
        'span'      =>  ['class', 'style'],
        'sub'       =>  ['class', 'style'],
        'sup'       =>  ['class', 'style'],
        'strong'    =>  ['class', 'style'],
        'table'     =>  ['width', 'border', 'align', 'valign', 'class', 'style'],
        'tbody'     =>  ['align', 'valign', 'class', 'style'],
        'td'        =>  ['width', 'rowspan', 'colspan', 'align', 'valign', 'class', 'style'],
        'tfoot'     =>  ['align', 'valign', 'class', 'style'],
        'th'        =>  ['width', 'rowspan', 'colspan', 'align', 'valign', 'class', 'style'],
        'thead'     =>  ['align', 'valign', 'class', 'style'],
        'tr'        =>  ['rowspan', 'align', 'valign', 'class', 'style'],
        'tt'        =>  [],
        'u'         =>  [],
        'ul'        =>  ['class', 'style'],
        'video'     =>  ['autoplay', 'controls', 'loop', 'preload', 'src', 'height', 'width', 'class', 'style'],
        'embed'     =>  ['src', 'height','align', 'width', 'class', 'style','type','pluginspage','wmode','play','loop','menu','allowscriptaccess','allowfullscreen'],
        'source'    =>  ['src', 'type']
    ];
    $html = strip_tags($html,'<'.implode('><', array_keys($elements)).'>');
    $xml = new \DOMDocument();
    libxml_use_internal_errors(true);
    if (!strlen($html)){
        return '';
    }
    if ($xml->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html)){
        foreach ($xml->getElementsByTagName("*") as $element){
            if (!isset($elements[$element->tagName])){
                $element->parentNode->removeChild($element);
            }else{
                for ($k = $element->attributes->length - 1; $k >= 0; --$k) {
                    if (!in_array($element->attributes->item($k) -> nodeName, $elements[$element->tagName])){
                        $element->removeAttributeNode($element->attributes->item($k));
                    }elseif (in_array($element->attributes->item($k) -> nodeName, ['href','src','style','background','size'])) {
                        $_keywords = ['javascript:','javascript.:','vbscript:','vbscript.:',':expression'];
                        $find = false;
                        foreach ($_keywords as $a => $b) {
                            if (false !== strpos(strtolower($element->attributes->item($k)->nodeValue),$b)) {
                                $find = true;
                            }
                        }
                        if ($find) {
                            $element->removeAttributeNode($element->attributes->item($k));
                        }
                    }
                }
            }
        }
    }
    $html = substr($xml->saveHTML($xml->documentElement), 12, -14);
    $html = strip_tags($html,'<'.implode('><', array_keys($elements)).'>');
    return $html;
}

/**

  * 验证码(阿里云短信)

  */

function smsVerify($mobile, $code, $tempId)
{     
   AlibabaCloud::accessKeyClient('LTAIEEgEmCZbmGSv', 'jMKcrkRmT576VPVXGjYm3R7gWKFACw')
    ->regionId('cn-hangzhou')
    ->asGlobalClient();
    $data = [];
        try {
        $result = AlibabaCloud::rpcRequest()
                  ->product('Dysmsapi')
                  //->scheme('https') //https | http（如果域名是https，这里记得开启）
                  ->version('2017-05-25')
                  ->action('SendSms')
                  ->method('POST')
                  ->options([
                        'query'=> [
                            'PhoneNumbers'=> $mobile,
                            'SignName' => '白鸥网络',
                            'TemplateCode'=> $tempId,
                            'TemplateParam' => json_encode(['code'=>$code,'product'=>'搜一搜站长平台']),
                        ],
                    ])
                  ->request();
        $res = $result->toArray();
        if($res['Code'] == 'OK'){
            $data['status'] = 1;
            $data['info']   = $res['Message'];
        }else{
            $data['status'] = 0;
            $data['info']   = $res['Message'];
        }
        return $data;
    } catch (ClientException $e) {
        $data['status'] = 0;
        $data['info']  = $e->getErrorMessage();
        return $data;
    } catch (ServerException $e) {
        $data['status'] = 0;
        $data['info']  = $e->getErrorMessage();
        return $data;

    }
}

// 取list数据
function rpopList($key, $sec)
{
    
    sleep($sec);
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);
     $redis->auth('baiou615');
    if ($res = $redis->rpop($key)) {
        return $res;
    } else {
        return rpopList($key, $sec);       
    }
    
}

function rpopList2($key, $sec, $time=1)
{
    
    sleep($sec);
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);
     $redis->auth('baiou615');
    if ($res = $redis->rpop($key)) {
        return $res;
    } else {
        $time += $sec;
        return $time<=15?rpopList2($key, $sec, $time):[];       
    }
    
}

function rpopList3($key, $sec, $time=1)
{
    
    $redis = new \Redis();
     $redis->connect('127.0.0.1', 6379);
      $redis->auth('baiou615');
    if ($res = $redis->lrange($key, 0, -1)) {
        return $res;
    } else {
        sleep($sec);
        $time += $sec;
        return $time<=6?rpopList3($key, $sec, $time):[];       
    }
    
}


function rpopLists($key, $sec, $num, $k = 0, $data = [])
{
    sleep($sec);
    $redis = new \Redis();
     $redis->auth('baiou615');
    $redis->connect('127.0.0.1', 6379);
    while ($res = $redis->rpop($key)) {
        $data[] = $res;
        $k++;
    }
    if ($k <= $num) {
        return rpopLists($key, $sec, $num, $k, $data);
    }
    return $data;
    
}

function returnApi($arr) {
    die(json_encode($arr, JSON_UNESCAPED_UNICODE));
}

function decApiTimes($uid, $aid) {
    $num = db('seo_api_times')->where(['uid'=>$uid, 'aid'=>$aid])->value('num');
    db('seo_api_times')->where(['uid'=>$uid, 'aid'=>$aid])->update(['num'=>$num-1, 'modify_time'=>time()]);
}

function addApiLog($uid, $mobile, $api_id, $state_code, $reason, $param){
    Db::name('seo_api_log')->insert(compact('uid', 'mobile', 'api_id', 'state_code', 'reason', 'param'));
}

// 生成网站地图
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
    $content.=create_item($data);
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

function rpopInclude($key)
{
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->auth('baiou615');
    if ($res = $redis->lrange($key, 0, -1)) {
        return $res;
    } else {
        return [];      
    }
    
}
function getUrlname($ety,$pty){
    if($ety == 1 && $pty == 1){
        $name = '百度PC';
    }elseif($ety == 1 && $pty == 2){
        $name = '百度移动';
    }elseif($ety == 2 && $pty == 1){
        $name = '360PC';
    }elseif($ety == 2 && $pty == 2){
        $name = '360移动';
    }elseif($ety == 3 && $pty == 1){
        $name = '搜狗PC';
    }elseif($ety == 3 && $pty == 2){
        $name = '搜狗移动';
    }else{
        $name = '暂无分类';
    }
    return $name;
}
function baiduPush(){
    $urls = array(
        config('url.pact').'://'.config('url.host').$_SERVER['REQUEST_URI'],
    );
    $api = 'http://data.zz.baidu.com/urls?site='.config('url.host').'&token=6KEgZKZif88KEg8R';
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

function urlsafe_b64encode($string) {
    $data = base64_encode($string);
    $data = str_replace(array('+','/','='),array('-','_',''),$data);
    return $data;
}

function urlsafe_b64decode($string) {
    $data = str_replace(array('-','_'),array('+','/'),$string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}

function getAddrByHost($host, $timeout = 1) { 
    $query = `nslookup -timeout=$timeout -retry=1 $host`; 
    if(preg_match('/\nAddress: (.*)\n/', $query, $matches)) 
     return trim($matches[1]); 
    return $host; 
} 

//远程获取用户头像上传到服务器
function download_remote_pic($url,$openid){
    $header = [
        'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0',      
        'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',      
        'Accept-Encoding: gzip, deflate',
    ];  
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_ENCODING, 'gzip');  
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    $data = curl_exec($curl);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);  
    if ($code == 200) {//把URL格式的图片转成base64_encode格式的！      
       $imgBase64Code = "data:image/jpeg;base64," . base64_encode($data);  
    }  
    $img_content=$imgBase64Code;//图片内容  
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_content, $result)) {   
        $type = $result[2];//得到图片类型png?jpg?gif?   
        $new_file =  'uploads/wxavatar/'.$openid.".{$type}";   
        if (@file_put_contents($new_file, base64_decode(str_replace($result[1], '', $img_content)))) {  
            return $new_file; 
        }
    } 
}
function checkSign($url, $post,$app_secret){
    $str = '';
    $sign = $post['sign'];
    unset($post['sign']);
    foreach($post as $k => $v){
        $str .= $k.'='.$v.'&';
    }
    $str = substr($str, 0, -1);
    $str = $url.'?'.$str.$app_secret;
    return $sign == md5($str);
}