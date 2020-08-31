<?php
namespace app\home\controller;
use think\facade\Request;

class Ipvsix extends Common{
	public function initialize(){
        parent::initialize();
    }
    public function toplimit()
    {
        if(!$this->visit()){
            return ['code'=>0, 'msg'=>'查询次数已达上限,请登录查询更多结果'];
            // $this->error('查询次数已达上限,请登录查询更多结果');
        }else{
            $this->success();
        }
    }
    //ipv6转ipv4页面
    public function change(){
        // ipv4转ipv6
        function getNormalizedIP($ip) {
            if (($ip == '0000:0000:0000:0000:0000:0000:0000:0001') OR ($ip == '::1')) {
                $ip = '127.0.0.1';
            }
            $ip = strtolower($ip);
            // remove unsupported parts
            if (($pos = strrpos($ip, '%')) !== false) {
                $ip = substr($ip, 0, $pos);
            }
            if (($pos = strrpos($ip, '/')) !== false) {
                $ip = substr($ip, 0, $pos);
            }
            $ip = preg_replace("/[^0-9a-f:\.]+/si", '', $ip);
            // check address type
            $is_ipv6 = (strpos($ip, ':') !== false);
            $is_ipv4 = (strpos($ip, '.') !== false);
            if ((!$is_ipv4) AND (!$is_ipv6)) {
                return false;
            }
            if ($is_ipv6 AND $is_ipv4) {
                // strip IPv4 compatibility notation from IPv6 address
                $ip = substr($ip, strrpos($ip, ':') + 1);
                $is_ipv6 = false;
            }
            if ($is_ipv4) {
                // convert IPv4 to IPv6
                $ip_parts = array_pad(explode('.', $ip), 4, 0);
                if (count($ip_parts) > 4) {
                    return false;
                }
                for ($i = 0; $i < 4; ++$i) {
                    if ($ip_parts[$i] > 255) {
                        return false;
                    }
                }
                $part7 = base_convert(($ip_parts[0] * 256) + $ip_parts[1], 10, 16);
                $part8 = base_convert(($ip_parts[2] * 256) + $ip_parts[3], 10, 16);
                $ip = '::ffff:'.$part7.':'.$part8;
            }
            // expand IPv6 notation
            if (strpos($ip, '::') !== false) {
                $ip = str_replace('::', str_repeat(':0000', (8 - substr_count($ip, ':'))).':', $ip);
            }
            if (strpos($ip, ':') === 0) {
                $ip = '0000'.$ip;
            }
            // normalize parts to 4 bytes
            $ip_parts = explode(':', $ip);
            foreach ($ip_parts as $key => $num) {
                $ip_parts[$key] = sprintf('%04s', $num);
            }
            $ip = implode(':', $ip_parts);
            return $ip;
        }
        //ipv6转ipv4
        if(Request::isAjax()) {
            $ip = input('changecont');
            if (input('type') == 2) {
                if (!preg_match("/^(25[0-5]|2[0-4]\d|[0-1]?\d?\d)(\.(25[0-5]|2[0-4]\d|[0-1]?\d?\d)){3}$/", $ip)) {
                    return ['code'=>0, 'msg'=>'请输入正确的IPV4地址'];
                }
                if(getNormalizedIP($ip)){
                    $changeval = getNormalizedIP($ip);
                    return ['code'=>1,'msg'=>'转换成功!','resval'=>$changeval];
                }else{
                    $changeval = '';
                    return ['code'=>0,'msg'=>'转换失败!','resval'=>$changeval];
                }
            } else {
               
                
                $str = str_replace(':', '', substr($ip, strpos($ip, 'ffff')+4));
               
                $arr = str_split($str);
                $res = (hexdec($arr[0])*16+hexdec($arr[1])).'.'.(hexdec($arr[2])*16+hexdec($arr[3])).'.'.(hexdec($arr[4])*16+hexdec($arr[5])).'.'.(hexdec($arr[6])*16+hexdec($arr[7]));
                return ['code'=>1,'msg'=>'转换成功!','resval'=>$res];
            }
            
        }
        $system = cache('System');
        $headtitle = 'IPV6地址转IPV4_'.$system['name'];
        $this->assign('headtitle',  $headtitle);

        $Keyword = 'ipv6转换';
        $Desc = 'IPV6转换工具可将IPV6地址转换为IPV4地址';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/ip.css']);
        $this->assign('JS',['/static/home2/js/ip.js']);
        return $this->fetch();
    }

    public function segment()
    {
        if (Request::isAjax()) {
            $changecont = trim(input('changecont'));
            $info = [];
            $preg = "/^((25[0-5]|2[0-4]\\d|[1]{1}\\d{1}\\d{1}|[1-9]{1}\\d{1}|\\d{1})($|(?!\\.$)\\.)){4}$/";
            if ($changecont == '') {
                return ['code'=>0];
            } elseif (strpos($changecont, ' ')) {
                return ['code'=>0];
            }

            if (strpos($changecont, '/')) {
                $arr = explode('/', $changecont);
                if (count($arr) != 2) {
                    return ['code'=>0];
                } elseif (!preg_match($preg, $arr[0])) {
                    return ['code'=>0];
                } else {
                    $ip1 = explode('.', $arr[0])[0].'.'.explode('.', $arr[0])[1].'.'.explode('.', $arr[0])[2];
                    $last = explode('.', $arr[0])[3];
                    $a = $last + 2;
                    switch ($arr[1]) {
                        case 29:
                            $b = $last + 6;
                            break;
                        case 28:
                            $b = $last + 14;
                            break;
                        case 27:
                            $b = $last + 30;
                            break;
                        case 26:
                            $b = $last + 62;
                            break;
                        case 24:
                            $b = $last + 254;
                            break;
                        default:
                            return ['code'=>0];
                    }
                }
            } elseif (strpos($changecont, '-')) {
                $arr = explode('-', $changecont);
                if (count($arr) != 2) {
                    return ['code'=>0];
                } elseif (!preg_match($preg, $arr[0]) || !preg_match($preg, $arr[1])) {
                    return ['code'=>0];
                } else {
                    $ip1 = explode('.', $arr[0])[0].'.'.explode('.', $arr[0])[1].'.'.explode('.', $arr[0])[2];
                    $ip2 = explode('.', $arr[1])[0].'.'.explode('.', $arr[1])[1].'.'.explode('.', $arr[1])[2];
                    $a = explode('.', $arr[0])[3];
                    $b = explode('.', $arr[1])[3];
                    if ($ip1 != $ip2 || $a >= $b) {
                        return ['code'=>0];
                    }
                }
            } else {
                return ['code'=>0];
            }
            $info = [];
            for ($i=$a; $i <=$b ; $i++) { 
                $info[] = $ip1.'.'.$i;
            }
            return ['code'=>1, 'info'=>$info];
        }
        $system = cache('System');
        $headtitle = 'IP段可用IP数目快速查询，批量查询生成IP_'.$system['name'];
        $this->assign('headtitle',  $headtitle);
        $Keyword = 'IP生成,IP分段';
        $Desc = '查询IP网关，可用IP数查询，查询IP段可用的IP的数量。';
        $this->assign('Keyword', $Keyword);
        $this->assign('Desc',  $Desc);  
        $this->assign('CSS',['/static/home2/css/ip.css']);
        $this->assign('JS',['/static/home2/js/segment.js']);
        return view();
    }

}
