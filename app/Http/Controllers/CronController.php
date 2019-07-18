<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\User;
use App\Model\Email;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\CommonController;
class CronController extends CommonController
{	
	/**
	 * 检测发送的邮箱
	 * @return [type] [description]
	 */
	public function cron()
	{
		//查询要发送的邮箱
		$where=[
			'status'=>0
		];
		$email=Email::where($where)->orderBy('id','desc')->value('user_email');
		//发送验证码
		$code=$this->sendMail($email);
		if ($code) {
			//成功记录验证码并且修改数据库该邮箱状态
			redis::set('code',$code);
			$data=[
				'status'=>1,
				'send_time'=>time()
			];
			Email::where(['user_email'=>$email])->update($data);
		}else{

		}
	}
}