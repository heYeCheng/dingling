<?php
require_once('./module/api_userMod.class.php');
require_once('./module/sql_goodMod.class.php');
require_once('./module/sql_orderMod.class.php');

class orderMod extends commonMod{
	private $apiUser = Null;
	private $p_id = 0;

	private function init(){
		$apiUser = new api_userMod();

		$cur_action = $_GET['_action'];
		if ($cur_action != 'suss') {
			if (isset($_GET['code'])) {
				$code = $this->in_get('code', None, 2, 'True');
				$flag = $apiUser->login($code);
				if ($flag) {
					$this->p_id = $flag;
				}else{
					# 此用户没注册，跳转到注册页面
					$this->redirect(__ROOT__.'/register');
				}
			}else{
				$check_res = $apiUser->check_login_normal();
				if (!$check_res) {
					##################
					echo "cookie 已过期需要重新登陆";
					$this->redirect(__ROOT__.'/index');
					exit();
				}else{
					$this->p_id = $check_res;
				}
			}
			$this->apiUser = $apiUser;
		}
	}

	// 展示订水页面
	public function water(){
		$sqlGood = new sql_goodMod();
		$this->init();
		$p_id = $this->p_id;
		$user_info = $this->apiUser->get_info_user_mysql($p_id);
		$res = $sqlGood->show_water($user_info['s_id']);
		$this->assign("res", $res);
		$this->assign("u_info", $user_info);
		$this->display('order/water');
	}
	
	/**
	* 提交订水订单
	* 1、获取用户信息 +  循环获取商品信息
	* 2、判断支付方式，1 为线下支付， 2 为积分兑换 (1、判断商品是否允许积分兑换  2、判断积分是否充足）
	* 3、下单 （总订单 + 订单详情）
	* 4、增加积分
	* 5、刷新订水数量 (collec_water  、 rank_water)
	* 6、查询订水排名 (collec_water)
	*/
	public function book(){
		$sqlGood = new sql_goodMod();
		$this->init();
		$shopCar = $this->in_get('shopCar', None, 2, 'True');
		$p_id = $this->p_id;
		$user_res = $this->apiUser->get_info_user_mysql($p_id);  // 获取用户信息

		$shopCar = urldecode($shopCar);
		// $order_json = json_decode('{"p_type":0,"order":[{"num":2,"gid":2}]}', True);
		$order_json = json_decode($shopCar, True);

		$pay_type = intval($order_json['p_type']);
		$order = $order_json['order'];

		if ($user_res) {
			$tempArr = array('num' => 0, 'total_fee' => 0, 'points' => 0, 'consume_points' => 0);  // 用于计算总订单的详细相关信息

			// 用于处理订单数据
			foreach ($order as $key => $value) {
				$gid = intval($value['gid']);
		
				$g_res = $sqlGood->get_info_good($gid, $user_res['s_id']); // 获取商品信息
				$con_flag = True;

				if ($g_res) {
					$user_res['g_id'] = $g_res['f_id'];  // 获取品牌的id
					$user_res['a_id'] = $g_res['a_id'];  // 获取经销商的 id
					$user_res['g_name'] = $g_res['g_name'];  // 获取商品名称

					$num = intval($value['num']);

					$tempArr['num'] += $num;
					$tempArr['total_fee'] += $g_res['price'] * $num;
					$tempArr['points'] += $g_res['point'] * $num;
					$con_point = $g_res['consume_point'];
					if ($con_point < 1) {
						$con_flag = False;
					}
					$tempArr['consume_points'] += $con_point * $num;
				}else{
					$this->alert('对不起，此商品暂时缺货');
				}
			}

			// 将订单数据存储到数据库中
			if ($pay_type == 0) {
				# 表示使用线下支付
				$this->write_order($user_res, $tempArr, 0);
				$finalPoint = $user_res['point'] + $tempArr['points'];
			}else if ($pay_type == 1) {
				# 表示积分抵扣
				if ($con_flag) {
					if ($user_res['point'] < $tempArr['consume_points']) {
						$this->alert('对不起，您的积分不足抵扣');
					}else{
						$this->write_order($user_res, $tempArr, 1);
						$finalPoint = $user_res['point'] - $tempArr['consume_points'] + $tempArr['points'];
					}
				}else{
					$this->alert('对不起，此商品暂不支持积分兑换');
				}
			}

			// 增加积分
			$up_data['point'] = $finalPoint;
			$up_con['p_id'] = $p_id;
			$this->model->table($this->config['info_person'])->data($up_data)->where($up_con)->update();

			// 更新个人这个月订水用量情况 && 设置个人这个月订水数量
			$this_Month = date('Y-m');
			$order_res = $this->apiUser->get_statistic_water_mysql($p_id, $this_Month);
			if ($order_res) {
				$cur_num = $order_res['num'] + $tempArr['num'];  // 目前总订水量
				$cur_fee = $order_res['fee'] + $tempArr['total_fee'];  // 目前费用支出

				$new_ratio = $this->apiUser->set_rank_water_mysql($order_res['num'], $cur_num);
				if ($new_ratio) {
					$this->apiUser->set_statistic_water_mysql($p_id, $this_Month, $cur_num, $cur_fee, $new_ratio['ratio'], 1);
				}
			}else{
				$cur_num = $tempArr['num'];  // 目前总订水量
				$cur_fee = $tempArr['total_fee'];  // 目前费用支出

				$new_ratio = $this->apiUser->set_rank_water_mysql(0, $cur_num);
				if ($new_ratio) {
					$this->apiUser->set_statistic_water_mysql($p_id, $this_Month, $cur_num, $cur_fee, $new_ratio['ratio']);
				}
			}

			$this->set_cookie('point', $tempArr['points']);
			$this->set_cookie('addr', $user_res['addr']);
			$this->set_cookie('type_name', $g_res['g_name']);
			$this->set_cookie('num', $tempArr['num']);

			$this->set_cookie('cur_num', $cur_num);
			$this->set_cookie('cur_fee', $cur_fee);
			$this->set_cookie('rank', $new_ratio['ratio']);

			$this->redirect(__URL__.'/suss');

			// over
		}else{
			$this->alert('对不起，请您进行注册');
		}
	}

	// 将订单存储到数据库中  order 表中
	private function write_order($user_res, $tempArr, $type){
		$this->init();
		$sqlOrder = new sql_orderMod();

		$ins_data['o_id'] = 'E'.uniqid();
		$ins_data['p_id'] = $user_res['p_id'];
		$ins_data['name'] = $user_res['name'];
		$ins_data['addr'] = $user_res['addr'];
		$ins_data['phone'] = $user_res['phone'];
		$ins_data['a_id'] = $user_res['a_id'];
		$ins_data['s_id'] = $user_res['s_id'];
		$ins_data['s_name'] = $user_res['s_name'];
		$ins_data['g_id'] = $user_res['g_id'];
		$ins_data['g_name'] = $user_res['g_name'];
		$ins_data['num'] = $tempArr['num'];
		$ins_data['points'] = $tempArr['points'];
		$ins_data['total_fee'] = $tempArr['total_fee'];
		$ins_data['created'] = date('Y-m-d h:i:s');
		$ins_data['send_status'] = 0;
		$ins_data['pre_pay'] = 0;
		$ins_data['pre_num'] = 0;
		$ins_data['pre_left_num'] = 0;

		$send_res = $sqlOrder->get_sender($user_res['a_id'], $user_res['addr']);
		$ins_data['send_id'] = $send_res['send_id'];
		$ins_data['send_name'] = $send_res['name'];

		if ($type == 0) {
			$ins_data['pay_type'] = 0;
		}else if ($type == 1) {
			$ins_data['pay_type'] = 1;
			$ins_data['consume_points'] = $tempArr['consume_points'];
		}

		$res = $this->model->table($this->config['order'])->data($ins_data)->insert();

		if (!$res) {
			$this->alert('对不起，网络暂时除了点小问题');
		}
	}

	// 订单成功返回页面
	public function suss(){
		if (isset($_COOKIE['point'])) {
			$this->set_cookie('point', $point, -1, __ROOT__);

			$this->assign("coo_data", $_COOKIE);
			$this->display('order/suss');
		}else{
			echo "此页面已经过期";
			// $this->set_cookie('point','10', -1, __ROOT__);
		}

			
	}

	public function index(){
		$this->checkLogin(2);
	}
}

?>