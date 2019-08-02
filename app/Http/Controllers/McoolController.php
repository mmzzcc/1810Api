<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\User;
use App\Model\UserToken;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use DB;
class McoolController extends CommonController
{	
	public function createString($param){
        if (!is_array($param) || empty($param)){
            return false;
        }
        ksort($param);
        $concatStr = '';
        foreach ($param as $k=>$v) {
            $concatStr .= $k.'='.$v.'&';
        }
        $concatStr = rtrim($concatStr, '&');
        return $concatStr;
    }
	/**
	 * 接口测试
	 * @return [type] [description]
	 */
	public function api()
	{
		return view('mcool.api');
	}

	/**
	 * 接口处理登陆 成功返回accesstoken
	 * @return [type] [description]
	 */
	public function apiLogin()
	{	
		$data=$_POST;
		// var_dump($data);die;
		$user=User::where(['user_name'=>$data['user_name']])->first();
		if ($user) {
			if ($user['user_pwd']==decrypt($data['user_pwd'])) {
				$token=md5(time());
				Redis::set('token'.$user['u_id'],$token);
				$usertoken=[
					'token'=>$token
				];
				session(['u_id'=>$user['u_id']]);
				$this->ok('登陆成功',1,$usertoken);
			}else{
				$this->no('账号或密码错误');
			}
		}else{
			$this->no('用户不存在');
		}
	}
	/**
	 * 注册视图
	 * @return [type] [description]
	 */
	public function reg()
	{
		$url="http://1810.oj8k.xyz/getImageCodeUrl";
		$data=[
		  'app_id'=>env('APP_ID')
		];
		$res=$this->curlPost($url,['data'=>$data['app_id']]);
		// var_dump($res);die;
		$data=json_decode($res,true);
		// dd($data);
		return view('mcool.reg',compact('data'));
	}

	/**
	 * 刷新验证码
	 * @return [type] [description]
	 */
	public function randCode()
	{
		$url="http://1810.oj8k.xyz/getImageCodeUrl";
		$app_id=env('APP_ID');
		$data=[
		  'app_id'=>$app_id
		];
		$res=$this->curlPost($url,['data'=>$data['app_id']]);
		$data=json_decode($res,true);
		return $data['image_url'];
	}
	/**
	 * 验证唯一性
	 * @return [type] [description]
	 */
	public function checkEmail()
	{
		$email=Request()->input('user_email');
		// var_dump($email);die;
		$user=User::where(['user_email'=>$email])->value('user_email');
		if ($user) {
			//已存在
			$this->no('邮箱已注册');die;
		}
		//在一分钟发一次这个判断之前 查一下这个邮箱是否发过 如果发过了就走这个。没有则不走
		$email_data=\DB::table('send_email')->orderBy('id','desc')->value('email');
		//一分钟发一次
		$sendtime=redis::get('sendtime');
		if (time()-$sendtime<60 && $email==$email_data) {
			$this->no('验证码一分钟只可以发一次');die;
		}
		//没有注册发送验证码
		$send=$this->sendMail($email);
		if ($send) {
			redis::set('sendtime',time());
			\DB::table('send_email')->insert(['email'=>$email]);
			$this->ok('验证码以发送到您的邮箱');die;
		}
	}

	/**
	 * 处理接口发过来的数据 处理注册
	 * @return [type] [description]
	 */
	public function doreg()
	{
		$data=$_POST;
		// dd($data);die;
		$signature=base64_decode($data['signature']);
		unset($data['signature']);
		// $str=json_decode($data['data'],true);
		$verify=openssl_verify($data['data'], $signature, openssl_get_publickey("file://".public_path('keys/pub.key')));
		if ($verify==1) {
			//写注册逻辑
			$user_data=json_decode($data['data'],true);
			$pass=password_hash($user_data['user_pwd'],PASSWORD_BCRYPT);
			// $pass1=password_verify($user_data['user_pwd'],$pass);
			// dd($pass1);
			$data=[
				'user_email'=>$user_data['user_email'],
				'user_pwd'=>$pass
			];
			$res=User::insert($data);
			if ($res) {
				$this->ok('注册成功');die;
			}else{
				$this->ok('注册失败');die;
			}
		}else{
			$this->no('验签失败');die;
			
		}
	}

	/**
	 * web端登陆
	 * @return [type] [description]
	 */
	public function weblogin()
	{	
		return view('mcool.weblogin');
		die;	
	}/**
	 * 处理登陆 错误三次锁定
	 */
	public function doWeblogin()
	{
		$user_name=Request('user_name');
		$user_pwd=Request('user_pwd');
		$where=[
			'user_email'=>$user_name
		];
    	$data=User::where($where)->first();
		if (!$data) {
			$this->no('用户不存在');
     		return;
		}
		$u_pwd=password_verify($user_pwd,$data['user_pwd']);
		$now=time();
		//错误次数
		$error_num=$data["error_num"];
		//错误时间
		$error_time=$data['error_time'];
		//密码错误
		if (!$u_pwd) {
			if ($now-$error_time>3600) {
				$errorData=[
					'error_num'=>1,
					'error_time'=>$now
				];
				//入库
					User::where(['u_id'=>$data['u_id']])->update($errorData);
					$this->no('邮箱或密码错误,您还有4次机会！');
					return;
			}else{
				//如果错误次数大于等于3
				if ($error_num>=5) {
					$this->no('账号异常 已锁定,请一小时后再试');
					return;
				}else{
					// 否则继续进行监听 错误信息更新
					$errorData=[
					'error_num'=>$error_num+1,
					'error_time'=>$now
				];
				//入库
				User::where(['u_id'=>$data['u_id']])->update($errorData);
				$count=5-($error_num+1);
				$this->no('邮箱或密码错误,您还有'.$count.'次机会！');
				return;
				}
			}
		}else{
			//密码正确
			if ($error_num>=5 && $now-$error_time<3600) {
				$errorTime=60-ceil(($now-$error_time)/60);
				$this->no('账号已锁定,请'.$errorTime.'分钟后登陆');
				return;
			}
			//登陆成功即代表已通过验证 给该用户错误信息清零
			$errorData=[
				'error_time'=>null,
				'error_num'=>0
			];
			User::where(['u_id'=>$data['u_id']])->update($errorData);
			$sessionInfo=[
				'u_id'=>$data['u_id'],
			];
			session(['userInfo'=>$sessionInfo]);
    		cache(['userName'=>$data['u_email']],60*24);
			$this->ok('登陆成功');
		}
	}
	/**
	 * iso端登陆
	 * @return [type] [description]
	 */
	public function ioslogin()
	{
		$user_name=Request()->input('user_name');
		$user_pwd=Request()->input('user_pwd');

		$user=User::where(['user_email'=>$user_name])->first();
		if (!$user) {
			echo "用户不存在";die;
		}
		//判断密码
		if ($user_pwd==$user['user_pwd']) {
			$token=md5(time().mt_rand(11111,99999));
			session(['token'=>$token]);
			redis::set('token',$token);
			echo"ios登陆成功";
		}else{
			echo "账号或密码错误";die;
		}
	}
	/**
	 * web端用户中心
	 * @return [type] [description]
	 */
	public function webcenter(Request $request)
	{	
		$query=$request->all()??'1';
		$data=User::paginate(2);
		return view('mcool/webcenter',compact('data','query'));
		
	}
	/**
	 * ios端用户中心
	 * @return [type] [description]
	 */
	public function ioscenter()
	{	
		$token=session('token');
		$cache_token=redis::get('token');
		// var_dump($token);echo "<br>";  var_dump($cache_token);die;
		if ($token==$cache_token) {
			$data=User::where(['status'=>1])->first()->toArray();
			return view('mcool/ioscenter',compact('data'));
		}else{
			echo "该账号已在其他端登陆";
		}
	}

	/**
	 * 分段加密
	 * @param  [type] $str [源数据]
	 * @return [type]      [description]
	 */
	public function encrypt($str)
	{
		$i = 0;
		$all='';
		while ($sub_str = substr($str, $i*117,117)) {
			openssl_public_encrypt($sub_str, $data, openssl_get_publickey("file://".public_path('keys/pub.key')),OPENSSL_PKCS1_PADDING);
			$all.=base64_encode($data);
			$i++;
		}
		return $all;
	}
	/**
	 * 分段解密
	 * @param  [type] $enc_str [加密数据]
	 * @return [type]          [description]
	 */
	public function decrypt($enc_str)
	{
		$i = 0;
		$all='';
		while ($sub_str = substr($enc_str, $i*344,344)) {
			$dec_str=base64_decode($sub_str);
			openssl_private_decrypt($dec_str, $data, openssl_get_privatekey("file://".public_path('keys/pri.pem')),OPENSSL_PKCS1_PADDING);
			$all.=$data;
			$i++;
		}
		return $all;
	}
	/**
	 * 测试分段加密解密
	 * @return [type] [description]
	 */
	public function str()
	{
		$str="12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890";
		$enc_str=$this->encrypt($str);
		var_dump($enc_str);
		$data=$this->decrypt($enc_str);
		dd($data);
	}
	/**
	 * 得到url文件扩展名
	 * @param  [type] $url [地址]
	 * @return [type]      [description]
	 */
	public function getExt($url)
	{	
		#第一步将url转成数组 第二步取出数组中带有文件名的一个，第三步因为是文件名肯定有.xxx所有.分割文件组成新数组，再用数组总条数-1的值作为原数组的键 给与返回
		$arr=parse_url($url);
		$file=basename($arr['path']);
		$ext=explode('.', $file);
		return $ext[count($ext)-1];

		// $ext=pathinfo($url,PATHINFO_EXTENSION);
		// return $ext;
	}
	/**
	 * 设置文件
	 * @return [type] [description]
	 */
	public function getFileName()
	{
		$a = '/a/b/c/d/e.php';
		$b = '/a/b/12/34/c.php';
		$this->getpathinfo($a, $b);
	}
	/**
	 * 算法函数
	 * @param  [type] $a [文件1]
	 * @param  [type] $b [文件2]
	 * @return [type]    [description]
	 */
	public function getpathinfo($a,$b)
	{
		$a2array = explode('/', $a);
		$b2array = explode('/', $b);
		$pathinfo = '';
		for( $i = 1; $i <= count($b2array)-2; $i++ ) {
		$pathinfo.=$a2array[$i] == $b2array[$i] ? '../' : $b2array[$i].'/';
		}
		print_R($pathinfo);
	}
}
