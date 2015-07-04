<!doctype html>
<html lang="zh">
<head>
	<title>下单订水</title>
	<meta charset="utf-8">
	<meta id="viewport" name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/order.css">
	<script type="text/javascript" src="__PUBLIC__/js/jquery-1.7.2.min.js"></script>
</head>
<body>
	<div class="head boderBottom">下单订水</div>
	<div class="mainBox boderBottom">
		<div class="title">宿舍号</div>
		<div class="product">
			<?php if(is_array($res)) foreach($res as $vo){ ?>
				<div class="box" data-id="{$vo['g_id']}">{$vo['g_name']}</div>
			<?php } ?>
		</div>
		<div class="count"><span class='min'>-</span><span class='num'>1</span><span class='plus'>+</span></div>
	</div>

	<div class="info">
		<p>本次订单可用 100 积分抵扣</p>
		<input type="checkbox" id="usePoint">使用积分 <span class='num'>剩余积分：</span><span class='left_point'>{$u_info['point']}</span>
	</div>

	<div class="foot">
		<input type="hidden" id="chooseGood">
		<form id="upForm" action="__URL__/book" method="get" enctype="text/plain" style="display:none">
			<input type="hidden" id="shopCar" name="shopCar">
		</form>
		<button class="submit" onclick="upForm()">提交</button>
	</div>
</body>
<script type="text/javascript">
	w = $('.product .box').width() + 'px'
	$('.product .box').height(w)
	$('.product .box').css('line-height',w)

	$(".product .box").each(function(){
    	$(this).click(function(){
    		$(".product .box").each(function(){
    			$(this).removeClass('chosen')
    		});
    		$(this).addClass('chosen')
    		$('#chooseGood').val($(this).data('id'))
    	});	
	});

	$('div.box:nth-child(1)').click()

	// 设置订水数目
	$('.min').click(function(){
		num = parseInt($('.count .num').html())
		if (num>1) {
			$('.count .num').html(num - 1)
		};
	});

	$('.plus').click(function(){
		num = parseInt($('.count .num').html())
		if (num<4) {
			$('.count .num').html(num + 1)
		};
	});

	function upForm(){
		num = parseInt($('.count .num').html())
		id = $('#chooseGood').val()
		if (! id) {
			alert('请选择品牌')
		};
		val = $('#usePoint').attr("checked")
		if (val == 'checked') {
			str = '{"p_type":1,"order":['
		}else{
			str = '{"p_type":0,"order":['
		}
		
		str += '{"num":' + num + ',"gid":' + id + '}]}'
		$('#shopCar').val(encodeURI(str))
		$('#upForm').submit()
		// alert(str)
	}
	
</script>
</html>