<script src="/js/jquery-3.2.1.min.js"></script>
<link rel="stylesheet" href="/css/page.css">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>用户展示</title>
</head>
<body>
<div id="dvContent">
    <table border="1" width="60%" bgcolor="#e9faff" cellpadding="2">
        <p>用户展示</p>
        <tr align="center">
            <td>ID</td>
            <td >用户电话号码</td>
            <td>登陆错误次数</td>
        </tr>
        @foreach ($data as $v)
        <tr>
            <td>{{$v->u_id}}</td>
            <td>{{$v->user_email}}</td>
            <td>{{$v->error_num}}</td>
        </tr>
        @endforeach
        <tr>
             <td>{{ $data->appends($query)->links() }}</td>
        </tr>
    </table>
    
</div>
<div id="dvPassword" style="display:none">输入密码：
    <input type="password" id="txtPwd" />
    <input type="button" value="确定" onclick="check()"/>
</div>
<script>
    if (document.cookie.indexOf('lock=1') != -1) ShowContent(false);
    var delay = 60 * 1000,timer;//10s后锁定，修改delay为你需要的时间，单位毫秒
    function startTimer() {
        clearTimeout(timer);
        timer = setTimeout(TimerHandler, delay);
    }
    function TimerHandler() {
        document.cookie = 'lock=1';
        document.onmousemove = null;//锁定后移除鼠标移动事件
        ShowContent(false);
    }
    function ShowContent(show) {
        document.getElementById('dvContent').style.display = show ? 'block' : 'none';
        document.getElementById('dvPassword').style.display = show ? 'none' : 'block';
    }
    function check() {
        if (document.getElementById('txtPwd').value == 'mcool') {
            document.cookie = 'lock=0';
            ShowContent(true);
            startTimer()//重新计时
            document.onmousemove = startTimer; //重新绑定鼠标移动事件
        }
        else alert('密码不正确！！');
    }
    window.onload = function () {
        document.onmousemove = startTimer;
        startTimer();
    }
</script>
</body>
</html>
