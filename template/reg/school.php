<!doctype html>
<html lang="zh">
<head>
	<title>选择所在高校</title>
	<meta charset="utf-8">
	<meta id="viewport" name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/reg.css">
	<script type="text/javascript" src="__PUBLIC__/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="__PUBLIC__/js/base64.js"></script>
</head>
<body>
	<div class="head boderBottom">选择所在高校</div>
	<div class="mainBox" id="first">
		<div class="show_school">
			<?php if(is_array($res)) foreach($res as $vo){ ?>
				<div class="box" onclick="clickBox({$vo['s_id']})">{$vo['name']}</div>
			<?php } ?>
		</div>
		<div class="foot">
			<div class="title boderBottom">我学校怎么没有？我也要！</div>
			<input type="text" placeholder='您所在的学校名' value="">
			<button class="submit">提交</button>
		</div>
	</div>

	<div class="mainBox" id="second">
		<form id="reg" class="show_school">
			<input type="hidden" id="schId" value="">
			<input type="text" placeholder='宿舍号：13-422' id="room" value="">
			<input type="text" placeholder='手机号' id="phone" value="">
		</form>
		<div class="foot">
			<button class="submit" onclick="upForm()">提交</button>
		</div>
	</div>

</body>
<script type="text/javascript">
	w = $('.show_school .box').width() + 'px'
	$('.show_school .box').height(w)
	$('.show_school .box').css('line-height',w)

	function clickBox(id){
		$('#schId').val(id)
		$('#first').hide('300')
		$('#second').show('300')
	}

	function upForm(){
		link = '{$url_code}'
		data = $('#schId').val() + '@@' + $('#room').val() + '@@' + $('#phone').val()
		data = BASE64.encoder(data);
		link = link.replace(/state=/gm,'state='+data);
		location.href = link
	}
</script>
</html>