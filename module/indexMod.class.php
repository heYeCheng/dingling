<?php
require_once('./module/api_userMod.class.php');

class indexMod extends commonMod{

	public function index(){
		$code = $this->in_get('code', '', 2, 'True');

		$apiUser = new api_userMod();
		$res = $apiUser->login($code);

		if ($res) {
			$this->redirect(__ROOT__.'/order/water');
		}else{
			$this->redirect(__ROOT__.'/register');
		}
	}
}
?>