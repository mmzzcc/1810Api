<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\User;
use App\Model\Email;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
class UserController extends CommonController
{	

	/**
	 * 注册
	 * @return [type] [description]
	 */
	public function reg(Request $request)
	{	
		
		session('user_name','mcool');
		var_dump(session('user_name'));
		die;

		// var_dump($_POST);die;
		$data=Request()->all();
		$file=$request->file('pic');
		dd($data);
		// 处理后台逻辑
		if (empty($file)) {
			// $this->no('请上传身份证照片');die;
			echo '请上传身份证照片';die;

		}
        foreach ($data as $key => $value) {
        	if (empty($value)) {
        		// $this->no('请填写完整信息');die;
        		echo "请填写完整信息";die;
        	}
        }
        //处理文件上传
        $user_pic=$this->upload($file);
        //密码加密入库
        $pass=password_hash($data['user_pwd'],PASSWORD_BCRYPT);
        $user_info=[
            'user_name'=>$data['user_name'],
            'user_pwd'=>$pass,
            'user_email'=>$data['user_email'],
            'add_time'=>time(),
            'user_tel'=>$data['user_tel'],
            'user_pic'=>$user_pic
        ];
        $res=User::insertGetId($user_info);
        if ($res) {
        	//生成appid和appsecret
        	$appid=md5(mt_rand(11111,99999).time());
        	$appsecret=md5(mt_rand(11111,99999)).time().mt_rand(111,999);
        	$data=[
        		'appid'=>$appid,
        		'appsecret'=>$appsecret
        	];
        	User::where(['u_id'=>$res])->update($data);
           $this->ok('注册成功');die;
        }else{
            $this->no('注册失败');die;
        }
	}
	/**
	 * 登录
	 * @return [type] [description]
	 */
	public function login()
	{
		$data=Request()->all();
		// dd($data);
        $account=$data['account'];
        $pwd=$data['password'];
        $user_data=User::where(['user_name'=>$account])->orwhere(['user_email'=>$account])->orwhere(['user_tel'=>$account])->first();
        if (!empty($user_data)) {
            $user_data=$user_data->toArray();
            if (!password_verify($pwd,$user_data['user_pwd'])) {
               $this->no('账号或密码错误');die;
            }else{
            	// //存用户信息
            	// $token=substr(md5($user_data['u_id'].str::random(8).rand(1111,9999)), 10,10);
            	// // echo $token; die;
            	// $u_id=$user_data['u_id'];
            	// redis::set('token'.$u_id,$token);
            	// $user_data['token']=$token;
             	//$this->ok('登陆成功',1,$user_data);die;
            	redis::set('user_id',$user_data['u_id']);
            	echo "登陆成功";die;
            }
        }else{
             $this->no('暂无该用户，请注册');die;
        }
	}
	/**
	 * 个人中心
	 * @return [type] [description]
	 */
	public function index(){
		$u_id=Request()->input('u_id');
		//查询用户信息
		$user_data=User::where(['u_id'=>$u_id])->first();
		if ($user_data) {
			$this->ok('身份验证成功',1,$user_data);die;
		}else{
			$this->no('无法获取身份信息。');die;
		}
	}
	/**
	 * 找回密码
	 * @return [type] [description]
	 */
	public function forgetPassword(){
		$email=Request()->input('email');
		// dd($email);
		$user_email=User::where(['user_email'=>$email])->first();
		if ($user_email) {
			//记录到email_list表中
			$email_data=[
				'user_email'=>$user_email->user_email,
				'add_time'=>time()
			];
			$res=Email::insert($email_data);
			if ($res) {
				$this->ok('已发送到您的邮箱,请查收');die;
			}else{
				$this->no('发送失败');die;
			}
		}else{
			$this->no('未查询到该用户请重试，或去注册。');die;
		}
	}
	/**
	 * 重置密码
	 */
	public function ResetPassword()
	{	
		//准备用户收到的验证码和缓存验证码
		$code=Request()->input('code');
		$cache_code=redis::get('code');
		var_dump($cache_code);
		//用户信息
		$email=Request()->input('email');
		$pass1=Request()->input('pass1');
		$pass2=Request()->input('pass2');

		if ($code!=$cache_code) {
				// $this->no('验证码错误，请重试');die;
			echo "验证码错误，请重试";die;
		}else{
			if ($pass1!=$pass2) {
				// $this->no('两次密码不一致');die;
				echo "两次密码不一致";die;
			}
			//更新入库
			$pass=password_hash($pass1,PASSWORD_BCRYPT);
			$user_data=[
				'user_pwd'=>$pass
			];
			$res=User::where(['user_email'=>$email])->update($user_data);
			if ($res) {
				echo "修改成功请重新登录。";die;
			}else{
				echo "修改失败,改账号可能被删除";die;
			}
		}
	}


	public function text(){
		$id=$_SERVER;
		echo "<pre>" ;var_dump($id);
	}
	/**
	 * 获取token
	 * @return [type] [description]
	 */
	public function getToken(){
		//TODA 业务逻辑 取到缓存中用户的id 通过用户id 得到appid与appsecret 调接口得到时效为2小时的token令牌
		$user_id=redis::get('user_id');
		$user_data=User::where(['u_id'=>$user_id])->first()->toArray();
		// dd($user_data);
		$appid=$user_data['appid'];
		$appsecret=$user_data['appsecret'];
		//发送接口请求
		$client = new Client;
		$url="http://1810.oj8k.xyz/accessToken";
		$response = $client->request('POST',$url,[
			'form_params'=>['appid'=>$appid,'appsecret'=>$appsecret]
		]);
		echo $response->getBody();
	}
	//给用户token
	public function accessToken(){
		//TODA 业务逻辑 用户通过调接口 给我们appid和appsecret 判断是否正确 并给用户唯一的token
		$data=$_POST;
		$user_id=redis::get('user_id');
		$user_data=User::where(['u_id'=>$user_id])->first()->toArray();
		if ($data['appid']==$user_data['appid'] && $data['appsecret']==$user_data['appsecret']) {
			//生成token
            $token=substr(md5($user_data['u_id'].str::random(20).mt_rand(1111,9999)), 8,18);
			redis::setex('token'.$user_id,7200,$token);
            return $token;
		}else{
			echo "appid或appsecret不匹配";
		}
	}
	/**
	 * 用户调取天气接口
	 * @return [type] [description]
	 */
	public function getWeather(){
		//TODA 业务逻辑 获得url的token令牌 与缓存对比 正确则调取天气接口展示 反之fail
		$user_id=redis::get('user_id');
		$cache_token=redis::get('token'.$user_id);
		$token=request('token');
		$city=request('city');
		if ($token==$cache_token) {
		$client = new Client;
		$url='http://api.k780.com/?app=weather.future&weaid='.$city.'&appkey=42229&sign=e96aa7adc8e6b08693e0488656c14518&format=json';
		$response = $client->request('GET',$url);
		$data=json_decode($response->getBody(),true);
		echo "<pre>";
		var_dump($data['result']);die;
		}else{
			echo "无效的token";
		}
	
	}
        
}
