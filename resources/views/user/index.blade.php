<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>用户展示</title>
</head>
<body>
	
</body>
</html>
<script type="text/javascript" src="{{asset('js/jquery.js')}}"></script>
<script type="text/javascript" src="{{asset('layui/layui.js')}}"></script>
<script type="text/javascript">
	$(function(){
		var userJsonStr = window.localStorage.getItem('userinfo');
		console.log(userJsonStr);	
		var userEntity = JSON.parse(userJsonStr);
		console.log(userEntity.account);
	})
</script>