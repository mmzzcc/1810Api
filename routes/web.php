<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login','UserController@login');
Route::get('/index','UserController@index');
Route::get('/curlGet','DemoController@curlGet');
Route::post('/enclogin','DemoController@enclogin')->middleware('checkToken');
Route::post('/enclogin2','DemoController@enclogin2')->middleware('checkData');
Route::post('/getImageCodeUrl','DemoController@getImageCodeUrl');
Route::get('/showImageCode','DemoController@showImageCode');


Route::post('/curlPost','DemoController@curlPost');
Route::get('/getPost','DemoController@getPost');
Route::get('/getAccessToken','DemoController@getAccessToken');
Route::post('/createWechatMenu','DemoController@createWechatMenu');
Route::get('/guzzle','DemoController@guzzle');
Route::get('/requestData','DemoController@requestData');
Route::get('/encyprt','DemoController@encyprt');
Route::get('/rsaData','DemoController@rsaData');
Route::get('/sendData','DemoController@sendData');
Route::post('/responseData','DemoController@responseData');
Route::get('/o1','DemoController@o1');
Route::get('/checkCode','DemoController@checkCode');

//app开发
Route::post('/reg','UserController@reg');
Route::post('/login','UserController@login');
Route::get('/index','UserController@index')->middleware('checkToken');
Route::post('/forgetPassword','UserController@forgetPassword');
Route::get('/forgetPassword1','UserController@sendMail');
Route::get('/cron','CronController@cron');
Route::post('/ResetPassword','UserController@ResetPassword');
Route::get('/getToken','UserController@getToken');
Route::post('/accessToken','UserController@accessToken');
Route::get('/getWeather','UserController@getWeather');

Route::get('/text','UserController@text');
//资源控制器
Route::resource('text','TextController');
//mcool  api
Route::get('/mcool/api','McoolController@api');
Route::post('/mcool/apiLogin','McoolController@apiLogin');
Route::get('/mcool/reg','McoolController@reg');
Route::get('/mcool/checkEmail','McoolController@checkEmail');
Route::post('/mcool/doreg','McoolController@doreg');
Route::get('/mcool/weblogin','McoolController@weblogin');
Route::get('/mcool/ioslogin','McoolController@ioslogin');
Route::get('/mcool/webcenter','McoolController@webcenter');
Route::get('/mcool/ioscenter','McoolController@ioscenter');
Route::get('/mcool/getFileName','McoolController@getFileName');
Route::get('/mcool/checkCode','McoolController@checkCode');
Route::get('/mcool/randCode','McoolController@randCode');
Route::post('/mcool/doWeblogin','McoolController@doWeblogin');



//分段加密
Route::get('/mcool/str','McoolController@str');
Route::get('/mcool/encyprt','McoolController@encyprt');
Route::get('/mcool/decrypt','McoolController@decrypt');
//b卷
Route::get('/category/getCategory','CategoryController@getCategory');
Route::post('/category/categoryData','CategoryController@categoryData');

//7.22周考
//pc
Route::get('/login/pclogin','LoginController@pclogin');
Route::post('/login/doPclogin','LoginController@doPclogin');
Route::get('/login/pcindex','LoginController@pcindex');
//h5
Route::get('/login/h5login','LoginController@h5login');
Route::post('/login/doH5login','LoginController@doH5login');
Route::get('/login/h5index','LoginController@h5index');
//app
Route::get('/login/applogin','LoginController@applogin');
Route::post('/login/doApplogin','LoginController@doApplogin');
Route::get('/login/appindex','LoginController@appindex');
//检测方法--成功之后封中间件
Route::post('/login/checkpcLogin','LoginController@checkpcLogin');
Route::post('/login/checkh5Login','LoginController@checkh5Login');

//分表注册
Route::get('/reg/reg','RegController@reg');
Route::post('/reg/doreg','RegController@doreg');
Route::get('/reg/login','RegController@login');
Route::post('/reg/dologin','RegController@dologin');