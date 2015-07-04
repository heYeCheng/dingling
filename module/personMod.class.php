<?php
require_once('./module/api_userMod.class.php');

class personMod extends commonMod{

	public function index(){
		$apiUser = new api_userMod();

		$p_id = $this->in_cookie('pId', None, 1, 'True');  // 获取用户信息
		$info_res = $apiUser->get_info_user_mysql($p_id, 2);

		if ($info_res) {
			$this->assign("info", $info_res);
		}

		$url_code = $this->wx->produce_code_link('http://www.dinglingxy.com/register/upInfo');
		// $url_code = 'http://localhost/cheetah/register/upInfo?state=';
		$this->assign("url_code", $url_code);
		$this->display('person/info');
	}

	public function history(){
		$apiUser = new api_userMod();

		$p_id = $this->in_cookie('pId', None, 1, 'True');  // 获取用户订单信息
		$url = __URL__.'/history?p={page}';
		$info = $apiUser->get_info_order_mysql($p_id, $url);
		
		if ($info) {
			$this->assign("info", $info['data']);
			$this->assign("page", $info['page']);
		}else{
			$info = '';
		}

		$this->display('person/history');
	}

}


?>