<?php
namespace app\user\controller;
use think\Input;
use think\Controller;

class Index extends Controller{
    // public function initialize(){
    //     parent::initialize();

    // }
    public function index(){
    	$code = rand_string(4,1);
    	$mobile = '13467225517';
       	$res = smsVerify($mobile, $code, 'SMS_69790149');
       	print_r($res);die;
		if($res['status'] == 1){
		    echo "发送成功";
		}else{
		    echo "发送失败";

		}
    }
}