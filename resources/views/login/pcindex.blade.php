<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Pc用户中心</title>
</head>
<body>
	<h1>PC用户中心</h1>
	@foreach($data as $v)
	<tr>
		<td>pc端用户:</td>
		<input type="hidden" name="id" value="{{$v['id']}}" id="user_id">
		<td>{{$v['user_name']}}</td>
	</tr>
	@endforeach
</body>
</html>
<script type="text/javascript" src="{{asset('js/jquery.js')}}"></script>
<script type="text/javascript" src="{{asset('layui/layui.js')}}"></script>
<script>
	$(function(){
		layui.use(['layer'],function(){
			layer=layui.layer;
		    setI=setInterval(timeLess,3000);
            function timeLess(){
            	var id=$('#user_id').val();
                $.ajax({
                    url:"/login/checkpcLogin",
                    data:{id:id},
                    method:"post",
                    success:function(res){
                        layer.msg(res.font);
                        if (res.code==2) {
                            clearInterval(setI);
                        	location.href="/login/pclogin";
                        }
                    },
                    dataType:"json"
                })
            }
		})
	})
</script>
