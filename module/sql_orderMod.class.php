<?php
class sql_orderMod extends commonMod{
	/**
	* 根据经销商的id ，以及送货地址，获取对应的送货员信息
	* @param a_id  表示此经销商在本系统的 id 编号
	* @param addr  表示送货地址
	*/
	public function get_sender($a_id, $addr){
		preg_match_all('/\d+/', $addr, $send_addr);
		$send = $send_addr[0][0];

		$con = 'a_id=' .$a_id .' and addr like \'%'. $send .'%\'';
		$field = 'send_id, name';

		$info = $this->model->table($this->config['send'])->field($field)->where($con)->find();
		if (! $info) {
			// 要是没有匹配地址的送货员，就找一个该经销商的送货员
			$con = 'a_id=' .$a_id;
			$field = 'send_id, name';

			$info = $this->model->table($this->config['send'])->field($field)->where($con)->find();
		}
		return $info;
	}

}