<!doctype html>
<html lang="zh">
<head>
	<title>历史纪录</title>
	<meta charset="utf-8">
	<meta id="viewport" name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/history.css">
	<script type="text/javascript" src="__PUBLIC__/js/jquery-1.7.2.min.js"></script>
</head>
<body>
	<div class="head boderBottom">历史纪录</div>
	<div class="mainBox">
		<table class="showTable">
			<tr>
				<td>时间</td>
				<td>品牌</td>
				<td>数量</td>
				<td>消费方式</td>
			</tr>
			<?php if(is_array($info)) foreach($info as $vo){
					echo '<tr><td>'. $vo['created'] . '</td>';
					$json = json_decode($vo['detail'], True);
					echo '<td>'. $json[0]['gname']. '</td>';
					echo '<td>'. $json[0]['num']. '</td>';
					if ($vo['pay_type'] == 1) {
						echo '<td>积分-'. $vo['consume_points'] .'</td>';
					}else{
						echo '<td>'. $vo['total_fee'] .'元</td>';
					}
					echo '</tr>';
				}
			?>
		</table>
			
	</div>
	<div id="page">{$page}</div>
</body>
</html>