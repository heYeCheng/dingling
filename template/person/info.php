<!doctype html>
<html lang="zh">
<head>
	<title>个人中心</title>
	<meta charset="utf-8">
	<meta id="viewport" name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/history.css">
	<script type="text/javascript" src="__PUBLIC__/js/base64.js"></script>
	<script type="text/javascript" src="__PUBLIC__/js/jquery-1.7.2.min.js"></script>
</head>
<body>
	<div class="head boderBottom">个人中心<span class="set" onclick="showChange()">修改</span></div>
	<div class="mainBox boderBottom person">
		<img src="{$info['pic']}">
		<div class="info">
			<div class="name">{$info['name']}</div>
			<div class="name">积分 <span>{$info['point']}</span></div>
		</div>
	</div>
	<a href="__ROOT__/person/history" class="btn">订单记录</a>

	<div class="other">
		<a href="__ROOT__/order/water">一键订水</a>
	</div>

	<div id="changeBox">
		<div class="head boderBottom">编辑个人信息<span class="set" onclick="upForm()">保存</span></div>
		<form  class="show_school">
			<input type="text" placeholder='宿舍号：13-422' id="room" value="{$info['addr']}">
			<input type="text" placeholder='手机号' id="phone" value="{$info['phone']}">
		</form>
	</div>
</body>

<script type="text/javascript">
	w = $('.other a').width() + 'px'
	$('.other a').height(w)
	$('.other a').css('line-height',w)

	function showChange(){
		$('#changeBox').show()
	}
	function upForm(){
		link = '{$url_code}'
		data = $('#room').val() + '@@' + $('#phone').val()
		data = BASE64.encoder(data);
		link = link.replace(/state=/gm,'state='+data);
		location.href = link
	}
</script>
</html>