<?php
require_once('./module/api_userMod.class.php');

class registerMod extends commonMod{
	public function bb(){
		// 获取 token 
		$url_code = $this->wx->get_base_token();
		dump($url_code);
	}
	
	public function index(){
		$res = $this->model->table($this->config['school'])->select();
		$this->assign("res", $res);

		$url_code = $this->wx->produce_code_link('http://www.dinglingxy.com/register/reg');
		$this->assign("url_code", $url_code);
		$this->display('reg/school');
	}

	public function reg(){
		$state = $this->in_get('state', None, 2, 'True');
		$wx_data = base64_decode($state);
		$code = $this->in_get('code', None, 2, 'True');

		$wx_data_arr = explode('@@', $wx_data);
		$schId = intval($wx_data_arr[0]);
		$room = in($wx_data_arr[1]);
		$phone = in($wx_data_arr[2]);
		$msg = Check::rule(array(check::mobile($phone),'手机电话号码格式不对'));

		if ($msg != 1) {
			$this->alert('您的手机号码不正确');
		}
		
		$r = connectRedis($this->config['RD_HOST'], $this->config['RD_PORT'], $this->config['RD_PWD']);

		$apiUser = new api_userMod();
		$wx_res = $apiUser->get_info_user_weixin($code, $r);
		if (!$wx_res) {
			$this->alert('请用微信登陆');
		}

		$check_con['openid'] = $wx_res['openid'];
		$field = 'p_id';
		$check_res = $this->model->table($this->config['info_person'])->field($field)->where($check_con)->find(); 
		if ($check_res) {
			// 如果此微信号已经存在，直接登陆
			$apiUser->set_login_mark($check_res['p_id'], $code, $r);
			disconnect($r);
			$this->redirect(__ROOT__.'/order/water');
		}else{
			// 否则插入数据
			$data['s_id'] = $schId;
			$res = $this->model->table($this->config['school'])->where($data)->find();
			if ($res) {
				$data['name'] = $wx_res['nickname'];
				$data['addr'] = $room;
				$data['phone'] = $phone;
				$data['openid'] = $wx_res['openid'];
				$data['pic'] = $wx_res['headimgurl'];
				$data['s_name'] = $res['name'];
				$data['sex'] = $wx_res['sex'];
				$data['city'] = $wx_res['city'];
				$data['province'] = $wx_res['province'];
				$data['point'] = 100;
				$res = $this->model->table($this->config['info_person'])->data($data)->insert(); 
				if ($res) {
					$apiUser->set_login_mark($res, $code, $r);
					disconnect($r);
					setcookie('susCode', time(), time() + EX_TIME_COOKIE, __ROOT__);
					$this->redirect(__URL__.'/suss');
				}else{
					disconnect($r);
					$this->alert('对不起，网络暂时出了点小问题');
				}
			}else{
				disconnect($r);
				$this->alert('对不起，贵校暂时没有开通此服务');
			}
		}
	}


	public function upInfo(){
		$state = $this->in_get('state', None, 2, 'True');
		$wx_data = base64_decode($state);
		$con['p_id'] = $this->in_cookie('pId', None, 1, 'True');  // 获取用户订单信息

		$wx_data_arr = explode('@@', $wx_data);
		$room = in($wx_data_arr[0]);
		$phone = in($wx_data_arr[1]);
		$msg = Check::rule(array(check::mobile($phone),'手机电话号码格式不对'));

		if ($msg != 1) {
			$this->alert('您的手机号码不正确');
		}

		$data['addr'] = $room;
		$data['phone'] = $phone;

		$res = $this->model->table($this->config['info_person'])->data($data)->where($con)->update();
		if ($res) {
			$this->alert('个人信息更新成功', __ROOT__.'/person');
		}else{
			$this->alert('服务器暂时出了点小差');
		}
	}
	public function suss(){
		// if (isset($_COOKIE['susCode'])) {
		if (1) {
			setcookie('susCode', time(), -1, __ROOT__);
			$url_code = $this->wx->produce_code_link('http://www.dinglingxy.com/order/water');
			$this->assign('url', $url_code);
			$this->display('reg/suss');
		}else{
			echo "此页面已经过期";
		}
		
	}

}
?>