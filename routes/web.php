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
Route::post('/getImageCodeUrl','DemoController@getImageCodeUrl')->middleware('checkData');

Route::post('/showImageCode','DemoController@showImageCode')->middleware('checkData');


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
//分段加密
Route::get('/mcool/str','McoolController@str');
Route::get('/mcool/encyprt','McoolController@encyprt');
Route::get('/mcool/decrypt','McoolController@decrypt');
//b卷
Route::get('/category/getCategory','CategoryController@getCategory');
Route::post('/category/categoryData','CategoryController@categoryData');
