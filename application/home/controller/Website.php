<?php
namespace app\home\controller;

class Website extends Common{
	public function initialize(){
        parent::initialize();
    }

    //网站查询排名页面
    public function index(){
        // if(empty(session('usersmobile'))){
        //     $this->redirect('home/login/login');
        //     return false;
        // }
        return $this->fetch();
    }
}
