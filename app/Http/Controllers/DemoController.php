<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
class DemoController extends Controller
{	
	//测试curl get方式请求
	public function curlGet()
	{
		echo 123;
		$url="https://www.baidu.com";
		//初始化
		$ch=curl_init($url);
		//设置参数
		// curl_setopt($ch, CURLOPT_URL, $ch);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		//执行命令
		curl_exec($ch);
		//关闭curl
		curl_close($ch);
	}
	//curl post方式接收数据
	public function curlPost()
	{	
		echo "收到的数据：";
		//接收解密数据
		// $text=decrypt($_POST);
		// var_dump($text);
		//接收表单
		// var_dump($_POST);

		//接收文件数据
		// var_dump($_FILES);

		//接收xml  json 数据
		// var_dump(file_get_contents("php:input"));
		// $data=file_get_contents("php://input");
		// $data=decrypt($data);
		// var_dump($data);
		die;
		//接收AES加密数据
		$key="mazhicheng";
		$iv="aaasssdddlkjhgfd";
		$data=file_get_contents("php://input");
		// dd($data);
		$dec_data=openssl_decrypt(base64_decode($data), 'AES-128-CBC', $key, OPENSSL_RAW_DATA,$iv) ;
		var_dump($dec_data);
	}
	//curl 发送post请求
	public function getPost()
	{	
		$url="http://1810.oj8k.xyz/curlPost";
		//设置post数据
		$post_data=[
			'name'=>'mcool',
			'sex'=>18
		];
		//初始化
		$ch=curl_init($url);
		//设置 抓取url
		// curl_setopt($ch, CURLOPT_URL, 1);
		//设置获取的信息以文件流的形式返回 而不是直接输出 0就是可用变量接收
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//设置post提交方式
		curl_setopt($ch, CURLOPT_POST, 1);
		//设置post数据    CURLOPT_POSTFIELDS
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		//输出错误信息
		// $msg=curl_error($ch);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//执行命令
		$data=curl_exec($ch);
		//关闭curl
		curl_close($ch);
		
	}
	//获取access_token
	public function getAccessToken()
	{	
		//定义所需要的变量
		$appId='wxa2d14d300a8bf43a';
		$secret='5a950fb1998753f3f9ba51e2714e0363';
		$url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appId.'&secret='.$secret.'';
		//初始化
		$ch=curl_init($url);
		//设置参数
		curl_setopt($ch, CURLOPT_HEADER, 0);
		//false可以变量输出
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//执行命令
		$data=curl_exec($ch);
		//关闭curl
		curl_close($ch);
		// var_dump($data);
		//得到结果：{"access_token":"22_0eeOtd1AdjYH5zFIswjuCpdDI5Q8UImY9xe4BN3PmiZdYezFxiEFHv1Y_C5Ul1_ZQRTF0jbb5sv29k_shnFp1D6g4lnr_GaTlyg62OczZLhkRWwEOawuoXu2um2zGn4fPWUlaRi3BqSTbEtDMRGbAAADIX","expires_in":7200}bool(true)
		return $data;
	}
	//生成微信自定义菜单
	public function createWechatMenu()
	{
		//得到access_token
		$access_token=$this->getAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
		 $post_data=[
            'button' => [
                [
                    'type'=>'view',
                    'name'=>'最新福利',
                    "url"=>"http://1809wangweilong.comcto.com/goods/detail/2",

                ],
                [
                    'type'=>'view',
                    'name'=>'点击签到',
                    'url'=>'http://1809wangweilong.comcto.com/wx/sign'
                ],
                [
                    "name"=>"发送位置",
                    "type"=> "location_select",
                    "key"=> "rselfmenu_2_0"
                ],
            ],
        ];
        //将数据转为json格式
		$post_data=json_encode($post_data,JSON_UNESCAPED_UNICODE);
        //初始化
		$ch=curl_init($url);
		//设置post提交方式
		curl_setopt($ch, CURLOPT_POST, 1);
		//设置post数据    CURLOPT_POSTFIELDS
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		//执行命令
		$data=curl_exec($ch);
		//关闭curl
		curl_close($ch);
		
		return $data;
	}

	//发送加密数据
	public function requestData(){
		$url="http://1810.oj8k.xyz/curlPost";
		$text=[
			'miwen'=>'计划有变，交易取消',
			'mcool'=>'今晚打老虎'
		];
		$text=encrypt(json_encode($text,JSON_UNESCAPED_UNICODE));
		// var_dump($text);
		//初始化
		$ch=curl_init($url);
		//设置参数
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $text);
		//执行程序
		curl_exec($ch);
		//关闭
		curl_close($ch);
		// echo "data";
	}

	//AES加密--7*/17
	public function encyprt()
	{
		$data=[
			'name'=>'zhangsan',
			'sex'=>'nan'
		];
		$key=env('ENCRYPT_KEY');
      	$iv=env('ENCRYPT_IV');
		$enc_data=openssl_encrypt(http_build_query($data), 'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv);
		$enc_data=base64_encode($enc_data);
		// var_dump($enc_data);die;
		$client=new Client;
		$url="http://1810.oj8k.xyz/curlPost";
		$response=$client->request('POST',$url,[
			'body'=>$enc_data
		]);

		echo $response->getBody();
	}

	//非对称加密
	public function rsaData(){
		//原数据
		$data="今晚打老虎";
		//加密
		$pri_key=openssl_get_privatekey("file://".public_path('keys/pri.pem'));
		openssl_private_encrypt($data, $enc_data, $pri_key);
		var_dump($enc_data);echo "<hr/>";
		//发送数据
		$client=new Client;
		$url="http://api.oj8k.xyz/receiveData";
		$response=$client->request('POST',$url,[
			'body'=>$enc_data
		]);

		echo $response->getBody();

	}


	//性感小练习121模式
	public function sendData(){
		/*
		1 发送端使用 对称加密方式加密数据 并使用私钥生成签名，将加密后的数据与签名发送给接收端
		2 接收端验证签名并解密数据，验签失败提示错误信息，验签成功后，使用对称加密加密数据，并使用私钥签名，将数据返回给发送端。
		3 发送端收到数据后，验证签名并解密数据
		 */
		//发
		$data="计划有变，交易取消";
		$key="mcool";
		$iv='1234567891234567';
		//对称加密
		$enc_data=openssl_encrypt($data,'AES-128-CBC',$key, OPENSSL_RAW_DATA,$iv);
		//得到私钥
		$pri_key=openssl_get_privatekey("file://".public_path('keys/pri.pem'));
		openssl_sign($enc_data, $signature, $pri_key);
		// var_dump($signature);die;
		//发送数据
		$client=new Client;
		$url="http://api.oj8k.xyz/receiveData1";
		$response=$client->request('POST',$url,[
			'form_params'=>['enc_data'=>$enc_data,'signature'=>$signature]
		]);
		echo $response->getBody();
	}
	//接收服务端发过来的数据
	public function responseData(){
		$enc_data1=$_POST;
		$pub_key=openssl_get_publickey('file://'.public_path('keys/pub.key'));
		// dd($pub_key);
		$verify = openssl_verify($enc_data1['enc_data1'], $enc_data1['signature1'], $pub_key);
		// dd($verify);
		if($verify == 1){
		    echo '客户端收到服务端的数据';
		    $iv='1234567891234567';
		    $key="mcool";
		    $dec_data1=openssl_decrypt($enc_data1['enc_data1'], 'AES-128-CBC', $key,OPENSSL_RAW_DATA,$iv);
		    var_dump($dec_data1);
		}else{
			echo "fail";
		}
	}

	//签名
	public function o1(){
		//源数据
		$arrParams = array(
		    'zas' => '今',
		    'ahfg' => '晚',
		    'ngfh' => "打",
		    'pwer' => "老",
		    'cwer' => '虎',
		);
		//生成字符串 键排序&拼接
		$str = $this->createString($arrParams);
		// var_dump($str);
		//签名
		openssl_sign($str, $signature, openssl_get_privatekey("file://".public_path('keys/pri.pem')));
		$sig=base64_encode($signature);
		$arrParams['signature']=$sig;
		// echo "<pre>";
		// var_dump($arrParams);die;
		//发送数据
		$client=new Client;
		$url="http://api.oj8k.xyz/o1";
		$response=$client->request('POST',$url,[
			'form_params'=>$arrParams
		]);
		echo $response->getBody();

	}
	/**
	 * 拼接字符串规则函数
	 * @param  [type] $param [数组]
	 * @return [type]        [description]
	 */
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

	// public function o1(){
	// 	$appId="2016092700605424";
	// 	$ali_gatewat="https://openapi.alipaydev.com/gateway.do";
	// 	//请求参数
	// 	$biz_content=[
	// 		'subject'=>'Nick SB 1299首发',
	// 		'out_trade_no'=>'1810'.rand(1111,9999).time(),
	// 		'total_amount'=>mt_rand(1111,9999),
	// 		'product_code'=>'QUICK_WAP_WAY'
	// 	];
	// 	$data=[
	// 		'app_id'=>$appId,
	// 		'method'=>'alipay.trade.wap.pay',
	// 		'charset'=>'utf-8',
	// 		'sign_type'=>'RSA2',
	// 		'timestamp'=>date('Y-m-d H:i:s'),
	// 		'version'=>'1.0',
	// 		'biz_content'=>$biz_content
	// 	];
	// 	//签名
	// 	openssl_sign($data, $signature, openssl_get_privatekey("file://".public_path('keys/pri.pem')));
	// 	$sign=base64_encode($signature);
	// 	$data['sign']=$sign;
	// 	//发送数据
	// 	$client=new Client;
	// 	$response=$client->request('POST',$ali_gatewat,[
	// 		'form_params'=>$data
	// 	]);
	// 	echo $response->getBody();

	// }

	public function enclogin(Request $request)
	{
		$arr=[
			'status'=>'1000',
			'msg'=>'success',
			'data'=>[
				'user_id'=>'100',
				'user_name'=>'zhangsan'
			]
		];
		return $arr;
	}

	public function enclogin2(Request $request)
	{
		$arr=[
			'status'=>'1000',
			'msg'=>'success',
			'data'=>[
				'user_id'=>'200',
				'user_name'=>'lisi'
			]
		];
		return $arr;
	}
}
public function getImageCodeUrl(Request $request)
{
	session_start();

	$sid=session_id();

	$image_url="http://1810.oj8k/showImageCode?sid".$sid;

	$data=[
		'image_url'=>$image_url,
		'unique_id'=>$sid
	];
}

public function showImageCode(Request $request)
{
	$sid=request('sid');

	session_id($sid);

	session_start();

	$rand=mt_rand(1000,9999);

	$_SESSION['code']=$rand;

	//输出一个图片
	header('Content-Type:image/png');
	//create the image 创建一个空的模板
	$im=imagecreatetruecolor(100, 30);

	//create some colors
	$white=imagecolorallocate($im, 255, 255, 255);
	$black=imagecolorallocate($im, 0, 0, 0);
	imagefilledrectangle($im, 0, 0, 399, 39, $white);

	$tetx=''.$rand;
	$font='C:\WINDOWS\FONTS\SEGOEPR.TTF';

	for ($i=0; $i < 4; $i++) { 
		imagettftext($im, 20, rand(-30,30), 15+20*$i, 25, $black, $font, $text[$i]);
	}

	imagepng($im);
	imagedestroy($im);
	exit;
}