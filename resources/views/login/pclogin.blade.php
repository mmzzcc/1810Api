<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>PC端登陆</title>
</head>
<body>
	<div align="center">
		<h1>PC登陆</h1>
		user_name：<input type="text" name="user_name" id="username"><br>
		password:<input type="password" name="user_pwd" id="pwd"><br>
		<button id="btn">login</button>
	</div>
</body>
</html>
<script type="text/javascript" src="{{asset('js/jquery.js')}}"></script>
<script type="text/javascript" src="{{asset('layui/layui.js')}}"></script>
<script>
	$(function(){
		layui.use(['layer'],function(){
			layer=layui.layer;
			$('#btn').click(function(){
				var username=$('#username').val();
				var password=$('#pwd').val();
				if (username=='' || password=='') {
					layer.msg('填写完整信息');
					return false;
				}
					$.ajax({
						url:'http://1810.oj8k.xyz/login/doPclogin',
						type:'post',
						data:{user_name:username,user_pwd:password},
						async:false,
						success:function(res){
							layer.msg(res.font);
							if (res.code==1) {
								location.href="/login/pcindex";
							}
						},
						dataType:'json'
				})
			})
		})
	return false;	
	})
</script>	