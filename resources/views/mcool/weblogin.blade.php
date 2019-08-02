<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>登陆</title>
</head>
<body>
	user_name:<input type="email" name="user_email" value="" id="username">
	password:<input type="password" name="user_pwd" id="pwd">
	<button id="btn">login</button>
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
						url:'http://1810.oj8k.xyz/mcool/doWeblogin',
						type:'post',
						data:{user_name:username,user_pwd:password},
						async:false,
						success:function(res){
							layer.msg(res.font);
							if (res.code==1) {
								//跳转
								location.href="/mcool/webcenter";
							}
						},
						dataType:'json'
				})
			})
		})
	return false;	
	})
</script>