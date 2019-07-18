<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>测试接口</title>
</head>
<body>
	账号：<input type="text" name="user_name" id="name1">
	密码：<input type="password" name="user_pwd" id="pass">
	token:<span id="token"></span><br>
	<button id="btn">login</button>
</body>
</html>
<script type="text/javascript" src="{{asset('js/jquery.js')}}"></script>
<script type="text/javascript" src="{{asset('layui/layui.js')}}"></script>
<script>
	$(function(){
		$('#btn').click(function(){
			var name1=$('#name1').val();
			var pass=$('#pass').val();
				$.ajax({
				url:'http://1810.oj8k.xyz/text',
				type:'post',
				async:false,
				beforeSend:function(request){
					request.setRequestHeader('X-ORIGIN',"mcool");
				},
				data:{user_name:name1,user_pwd:pass},
				success:function(res){
					console.log(res);
					alert(res.font);
					if (res.code==1) {
						$('#token').text(res.data.token);
					}
				},
				dataType:'json'
			})
		})
		
	})
</script>
