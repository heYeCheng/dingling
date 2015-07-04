<!doctype html>
<html lang="zh">
<head>
	<title>下单成功啦</title>
	<meta charset="utf-8">
	<meta id="viewport" name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/order.css">
	<script type="text/javascript" src="__PUBLIC__/js/jquery-1.7.2.min.js"></script>
</head>
<body>
	<div class="head boderBottom">下单成功啦</div>
	<div class="mainBox">
		
		<div class="show">
			<div class="info"><span>{$coo_data['addr']}</span><span>{$coo_data['type_name']}</span><span>{$coo_data['num']}桶</span></div>
			<div class="title_suss">成功下单，您获得了 <span >{$coo_data['point']}</span> 积分</div>
			<img src="__PUBLIC__/image/1.jpg">
			<div class="info">您本月订水 {$coo_data['cur_num']} 桶，花费 {$coo_data['cur_fee']} 元</div>
			<div class="info">击败了大学城 {$coo_data['rank']}% 的学生</div>
		</div>
		<div class="box01">
			<a href="__ROOT__/person/" class="btn">个人中心</a>
			<a href="__ROOT__/person/history" class="btn">查看订单</a>
		</div>
			
	</div>

</body>
</html>