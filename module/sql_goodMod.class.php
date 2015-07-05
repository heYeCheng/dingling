<?php
class sql_goodMod extends commonMod{

	/**
	* 用于展示产品，展示水
	*/
	public function show_water($s_id){
		$con['t_id'] = 1;
		$con['s_id'] = $s_id;
		$field = 'g_id, g_name, price';
		$res = $this->model->table($this->config['goods'])->field($field)->where($con)->select();
		return $res;
	}

	/**
	* 用于查询产品信息，读取数据库，获取相关信息
	* @param p_id  表示此用户在本系统的 id 编号
	*/
	public function get_info_good($g_id, $s_id = 0){
		if ($s_id > 0) {
			// $con = 'g_id ='.$g_id. ' and s_id ='.$s_id;
			$con = 'g_id ='.$g_id. ' and s_id in('.$s_id. ',0)';
		}else{
			$con = 'g_id ='.$g_id;
		}
		$field = 'f_id, a_id, g_name, price, point, consume_point';
		$res = $this->model->table($this->config['goods'])->field($field)->where($con)->find();
		return $res;
	}
}

?>