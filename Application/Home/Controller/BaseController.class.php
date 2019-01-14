<?php

namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class BaseController extends Controller{
	
	public function _initialize(){
	    getConfigs();
	   // die( C("CAIJI_KEY"));
		//检测登录状态
		$userid = session('user');
		if (C('is_weixin') == '1' && is_weixin()) {
			if(CONTROLLER_NAME!='Index'){
				if(empty($userid['id'])){
					$this->redirect('Index/index');
				}
			}
		} else {
			if(CONTROLLER_NAME!='Index' && CONTROLLER_NAME!='Run'){
				if(empty($userid['id'])){
					$this->redirect('Home/Index/login');
				}
			}
		}
		
		if (isset($userid['id'])) {
			$userinfo = M('user')->where("id = {$userid['id']}")->find();
		} else {
			$userinfo = array();
		}
		$this->assign('userinfo',$userinfo);
		$this->assign('version',VERSION);
	}
}
?>