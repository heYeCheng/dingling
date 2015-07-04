<?php
class api_userMod extends commonMod{

	//进行用户的认证登陆
	public function login($code){
		$data = $this->wx->vali_weixin_user($code); // 判断来源是否微信
		if ($data) {
			$openid = $data['openid'];
			$p_id = $this->check_user_openId($openid); // 判断这个 微信号是否注册了
			if ($p_id) {
				$this->set_login_mark($p_id, $code);
				return True;
			}
		}else{
			return False;
		}
	}

	//判断用户是否已经登陆了，这个验证只是普通验证，不需要涉及微信的 code 验证
	public function check_login_normal(){
		if (isset($_COOKIE['pId'])) {
			$p_id = $this->in_cookie('pId', None, 1, 'True');
			$key = $this->in_cookie('key', None, 2, 'True');

			$r = connectRedis($this->config['RD_HOST'], $this->config['RD_PORT'], $this->config['RD_PWD']);
			$data = $r->get($p_id);
			disconnect($r);

			if ($data) {
				$data = json_decode($data, True);
				if ($data['key'] == $key) {
					return True;
				}
			}
		}
		return False;
	}

	/**
	* 进行登陆标记，设置 cookie，存入 redis，表示此用户已经登陆
	* @param p_id  表示此用户在本系统的 id 编号
	* @param code  表示此用户本次登陆的 code，由微信提供
	* @param r  表示 redis ，若有值，表示此资源已存在，直接使用即可
	*/
	public function set_login_mark($p_id, $code, $r = ''){
		$key = md5($p_id.$code);

		$redis_json = json_encode(array('key' => $key, 'code' => $code));  // 将用户的 p_id 、key  存进redis 中，并设置生命期

		if (empty($r)) {
			$r = connectRedis($this->config['RD_HOST'], $this->config['RD_PORT'], $this->config['RD_PWD']);
			$r->set($p_id, $redis_json, EX_TIME_COOKIE);
			disconnect($r);
		}else{
			$r->set($p_id, $redis_json, EX_TIME_COOKIE);
		}
		setcookie('key', $key, time() + EX_TIME_COOKIE, __ROOT__);
		setcookie('pId', $p_id, time() + EX_TIME_COOKIE, __ROOT__);
	}

	/**
     * 根据 openid 判断有没有此用户，只返回用户的 id
     * @param openid 微信的 openid
     */ 
	public function check_user_openId($openid){
		$field = 'p_id';
		$con['openid'] = $openid;
		$res = $this->model->table($this->config['info_person'])->field($field)->where($con)->find();
		if ($res) {
			return $res['p_id'];
		}else{
			return '';
		}
	}

	/**
	* 用于用户注册，根据 code，获取用户的微信消息
	* @param r  表示 redis ，若有值，表示此资源已存在，直接使用即可
	*/
	public function get_info_user_weixin($code, $r){
		$data = $this->wx->vali_weixin_user($code); // 判断来源是否微信
		if ($data) {
			$openid = $data['openid'];
			$as_token = $r->get('token');  // 直接从 redis 里获取 token
			
			$data = $this->wx->get_user_info($as_token, $openid); // 判断来源是否微信
			return $data;
		}else{
			return False;
		}
	}

	/**
	* 用于查询用户信息，读取数据库，获取相关信息
	* @param p_id  表示此用户在本系统的 id 编号
	* @param type  表示此查询用户信息的详细程度， 1 表示普通信息，2 表示超详细信息
	*/
	public function get_info_user_mysql($p_id, $type = 1){
		$con = 'p_id ='.$p_id;
		if ($type == 1) {
			$field = 'p_id, name, addr, phone, s_id, s_name, point';
		}else{
			$field = 'p_id, name, addr, phone, pic, s_id, s_name, point';
		}
		$res = $this->model->table($this->config['info_person'])->field($field)->where($con)->find();
		return $res;
	}

	/**
	* 用于查询用户订单，读取数据库，获取相关信息
	* @param p_id  表示此用户在本系统的 id 编号
	* @param url  分页基准网址
	*/
	public function get_info_order_mysql($p_id, $url){
		$con = 'p_id ='.$p_id;
		$field = 'created, detail, total_fee, pay_type, consume_points';

		$page = new Page();
		$listRows = 10;//产品每页显示的信息条数
		// $url=__URL__.'?p={page}';//分页基准网址
		$cur_page = $page->getCurPage($url);
		$limit_start = ($cur_page-1)*$listRows;
		$limit=$limit_start.','.$listRows;

		$count = $this->model->table($this->config['order'])->where($con)->count();
		$res = $this->model->table($this->config['order'])->field($field)->where($con)->order('created desc')->limit($limit)->select();
		if ($res) {
			return array('data' => $res, 'page' => $page->show($url, $count, $listRows));
		}else{
			return False;
		}
	}

	/**
	* 用于查询用户订水情况，读取数据库，获取相关信息 collect_water
	* @param p_id  表示此用户在本系统的 id 编号
	* @param month  这个月
	*/
	public function get_statistic_water_mysql($p_id, $month){
		$con['p_id'] = $p_id;
		$con['month'] = $month;
		$res = $this->model->table($this->config['collect_water'])->where($con)->find();
		return $res;
	}

	/**
	* 用于设置用户订水情况，读取数据库，获取相关信息 collect_water
	* @param p_id  表示此用户在本系统的 id 编号
	* @param month  这个月
	* @param num  表示新增订水量
	* @param fee  表示新增订水费用
	* @param rank  表示本次排名
	* @param type  若为 0 ，表示本月第一次新增订水
	*/
	public function set_statistic_water_mysql($p_id, $month, $num, $fee, $rank, $type = 0){
		$data['num'] = $num;
		$data['fee'] = $fee;
		$data['rank'] = $rank;

		if ($type == 0) {
			$data['p_id'] = $p_id;
			$data['month'] = $month;
			$res = $this->model->table($this->config['collect_water'])->data($data)->insert();
		}else{
			$con['p_id'] = $p_id;
			$con['month'] = $month;
			$res = $this->model->table($this->config['collect_water'])->data($data)->where($con)->update();
		}
			
		return $res;
	}

	/**
	* 用于查询订水排名，读取数据库，获取相关信息 rank_water
	* @param order_num  订水数量
	*/
	public function get_rank_water_mysql($order_num){
		$con['num'] = $order_num;
		$field = 'ratio';

		$res = $this->model->table($this->config['rank_water'])->field($field)->where($con)->find();
		return $res;
	}

	/**
	* 用于设置订水排名，读取数据库，获取相关信息 rank_water
	* @param old_num  上一次订水数量, 若为 0 ，表示该用户是这个月第一次订水
	* @param new_num  新次订水数量
	*/
	public function set_rank_water_mysql($old_num, $new_num){
		if ($old_num == 0) {
			$sql = 'UPDATE `rank_water` SET `people` = `people` + 1 WHERE `num` ='.$new_num;
			$res = $this->model->query($sql);
		}else{
			$sql = 'UPDATE `rank_water` SET `people` = `people` + 1 WHERE `num` ='.$new_num;
			$res = $this->model->query($sql);
			$sql = 'UPDATE `rank_water` SET `people` = `people` - 1 WHERE `num` ='.$old_num;
			$res = $this->model->query($sql);
		}
		return $this->get_rank_water_mysql($new_num);
	}
	
}

?>