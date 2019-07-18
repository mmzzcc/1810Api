<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>用户注册</title>
</head>
<body>
	邮箱：<input type="email" name="user_email" id="name1"><br>
	<button id="send">send</button><br>
	验证码<input type="text" name="code" id="code"><br>
	密码：<input type="password" name="user_pwd" id="pass"><br>
	<button id="btn">reg</button>
</body>
</html>
<script type="text/javascript" src="{{asset('js/jquery.js')}}"></script>
<script type="text/javascript" src="{{asset('layui/layui.js')}}"></script>
<script>
	$(function(){
		layui.use(['layer'],function(){
			layer=layui.layer;
				//发送短信验证码  & 验证邮箱唯一性
				$('#send').click(function(){
					var name1=$('#name1').val();
						$.ajax({
						url:'http://1810.oj8k.xyz/mcool/checkEmail',
						type:'get',
						async:false,
						data:{user_email:name1},
						success:function(res){
							layer.msg(res.font);
							if (res.code==2) {
								return false;
							}
						},
						dataType:'json'
					})
				})
				$('#btn').click(function(){
				var name1=$('#name1').val();
				var code=$('#code').val();
				var pass=$('#pass').val();
					$.ajax({
					url:'http://1810.oj8k.xyz/text',
					type:'post',
					async:false,
					data:{user_email:name1,user_pwd:pass},
					success:function(res){
						layer.msg(res.font);
						if (res.code==1) {
							location.href="mcool/weblogin";
						}
					},
					dataType:'json'
				})
			})
		})
	return false;
		
	})
</script>
