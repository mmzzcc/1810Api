<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>用户注册</title>
</head>
<body>
	邮箱：<input type="email" name="user_email" id="name1"><br>
	<input type="hidden" name="sid" value="<?=$data['unique_id']?>" id="name3">
	<img src="<?=$data['image_url']?>" alt="" id="codeImage" ><br> 
	请输入验证码结果<input type="text" id="name2"><br>
	<button id="send">send</button><br>
	请输入短信验证码<input type="text" name="code" id="code"><br>
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
					var flag=false;
					//name1是邮箱name2是图片验证码结果name3是sid
					var name1=$('#name1').val();
					var name2=$('#name2').val();
					var name3=$('#name3').val();
					//非空验证
					if (name1=='') {
						layer.msg('请填写邮箱');
						// flag=false;
						return false;
					}
					if (name2=='') {
						layer.msg('请填写图片验证码结果');
						// flag=false;
						return false;
					}
					//验证图片验证码是否正确
					$.ajax({
						url:'http://1810.oj8k.xyz/checkCode?code='+name2+"&sid="+name3,
						type:'get',
						async:false,
						success:function(res){
							layer.msg(res.font);
							if (res.code==2) {
								flag=false;
							}else{
								flag=true;
							}
						},
						dataType:'json'
					})
					if (flag==false) {
						return false;
					}else {
						//验证邮箱号唯一性 && 发送短信验证码
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
					}
				

				})
				//点击刷新验证码图片
				$('#codeImage').click(function(){
					var _this=$(this);
						$.ajax({
							url:'http://1810.oj8k.xyz/mcool/randCode',
							type:'get',
							async:false,
							success:function(res){
								_this.prop('src',res);
							},
						})
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
		
	return false;	
	})
</script>
