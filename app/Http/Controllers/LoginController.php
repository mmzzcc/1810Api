<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\Login;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
class LoginController extends CommonController
{	

	/**
	 * 	项目需求
	 * 	PC和H5不可以同时在线
		安卓和IOS不可以同时在线
		PC和APP可以同时在线（限制一个终端，安卓或者苹果）
		H5和APP也可以同时在线。（限制一个终端，安卓或者苹果）
	 */

	/**
	 * pc视图
	 * @return [type] [description]
	 */
	public function pclogin()
	{
		return view('login.pclogin');
	}
	/**
	 * 处理pc登陆逻辑
	 * @return [type] [description]
	 */
	public function doPclogin()
	{
		//TODA
		$data=$_POST;
		$user=Login::where(['user_name'=>$data['user_name']])->first();
		if ($user) {
			if ($user['user_pwd']==$data['user_pwd']) {
				$token=md5(time());
				Redis::set('pc-token'.$user['id'],$token);
				Login::where(['id'=>$user['id']])->update(['user_token'=>$token]);
				$this->ok('登陆成功');
			}else{
				$this->no('账号或密码错误');
			}
		}else{
			$this->no('用户不存在');
		}
	}
	public function pcindex()
	{	
		$data=Login::get()->toArray();
		// dd($data);
		return view('login.pcindex',compact('data'));
	}

	/**
	 * h5登陆视图
	 * @return [type] [description]
	 */
	public function h5login()
	{
		return view('login.h5login');
	}
	/**
	 * 处理h5登陆逻辑
	 * @return [type] [description]
	 */
	public function doH5login()
	{
		//TODA
		$data=$_POST;
		$user=Login::where(['user_name'=>$data['user_name']])->first();
		if ($user) {
			if ($user['user_pwd']==$data['user_pwd']) {
				$token=md5(time());
				Redis::set('h5-token'.$user['id'],$token);
				Login::where(['id'=>$user['id']])->update(['user_token'=>$token]);
				$this->ok('登陆成功');
			}else{
				$this->no('账号或密码错误');
			}
		}else{
			$this->no('用户不存在');
		}
	}

	public function h5index()
	{
		$data=Login::get()->toArray();
		// dd($data);
		return view('login.h5index',compact('data'));
	}

	/**
	 * app视图
	 * @return [type] [description]
	 */
	public function applogin()
	{
		return view('login.applogin');
	}
	/**
	 * 处理app登陆逻辑
	 * @return [type] [description]
	 */
	public function doApplogin()
	{
		//TODA
		$data=$_POST;
		$user=Login::where(['user_name'=>$data['user_name']])->first();
		if ($user) {
			if ($user['user_pwd']==$data['user_pwd']) {
				$this->ok('登陆成功');
			}else{
				$this->no('账号或密码错误');
			}
		}else{
			$this->no('用户不存在');
		}
	}
	/*
	app视图展示
	 */
	public function appindex()
	{	
		$data=Login::get()->toArray();
		// dd($data);
		return view('login.appindex',compact('data'));
	}

	/**
	 * andlogin登陆视图
	 * @return [type] [description]
	 */
	public function andlogin()
	{
		return view('login.andlogin');
	}
	/**
	 * 处理安卓登陆逻辑
	 * @return [type] [description]
	 */
	public function doAndlogin()
	{
		//TODA
	}

	/**
	 * ioslogin登陆视图
	 * @return [type] [description]
	 */
	public function ioslogin()
	{
		return view('login.ioslogin');
	}
	/**
	 * 处理苹果陆逻辑
	 * @return [type] [description]
	 */
	public function doIoslogin()
	{
		//TODA
	}
	/**
	 * 检测pc h5登陆
	 * @return [type] [description]
	 */
	public function checkpcLogin()
	{
		$user_id=request()->input('id');
		$pc=redis::get('pc-token'.$user_id);
		$user_token=Login::where(['id'=>$user_id])->value('user_token');
		var_dump($pc);
		dd($user_token);
		if ($user_token!=$pc) {
			$this->no('该账号已在其他端登陆,pc端被迫下线');
		}

	}
	/**
	 * 检测pc h5登陆
	 * @return [type] [description]
	 */
	public function checkh5Login()
	{
		$user_id=request()->input('id');
		$h5=redis::get('h5-token'.$user_id);
		$user_token=Login::where(['id'=>$user_id])->value('user_token');
		if ($user_token!=$h5) {
			$this->no('该账号已在其他端登陆,h5端被迫下线');
		}
	}
}