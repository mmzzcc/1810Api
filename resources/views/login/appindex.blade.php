<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>APP用户中心</title>
</head>
<body>
    <h1>App用户中心</h1>
	@foreach($data as $v)
	<tr>
		<td>app端用户:</td>
		<input type="hidden" name="id" value="{{$v['id']}}" id="user_id">
		<td>{{$v['user_name']}}</td>
	</tr>
	@endforeach
</body>
</html>
<script type="text/javascript" src="{{asset('js/jquery.js')}}"></script>
<script type="text/javascript" src="{{asset('layui/layui.js')}}"></script>

