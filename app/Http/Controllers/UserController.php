<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\User;
use Illuminate\Support\Facades\Redis;
class UserController extends Controller
{
	/**
	 * 测试
	 * @return [type] [description]
	 */
	public function login(){
		// Redis::set('demo',123);
		// $res=Redis::get('demo');
		// dd($res);
		// echo "映射编辑";die;
		$data=[
		'user_name'=>'mcool',
		'user_pwd'=>'123'
		];
		$res=User::insertGetId($data);
		var_dump($res);
	}

	public  function index(){
		$data=User::get()->toArray();
		dd($data);
	}
		
}
