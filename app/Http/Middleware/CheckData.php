<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Redis;
class CheckData
{   
    private $error_count=150;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        #接口防刷--【1分钟内接口不能超过150次。超过加入黑名单一小时】
        $black_result=$this->_checkApiAccessCount($request);

        if ($black_result['status']!='1000') {
          return response($black_result);
        }
        #接收客户端传递过来的数据
        # 1、对数据解密
        $data=$this->_AesPriDecrypt($request);
        # 2、验证数据的签名，防止篡改
        $check=$this->_CheckSign($request,$data);
        // var_dump($check);die;
        if ($check['status']=='1000') {
          # 3、做数据替换，方便控制器使用
          $request->request->replace($data);
          $response=$next($request);
          #接收返回的数据，对返回的数据进行加密
          $api_response=[];
          $api_response['data']=$this->_AesPriEncrypt($response->original);
          $api_response['sign']=$this->createServiceSign($response->original,$data['app_id']);
          // var_dump($api_response);die;
          return response($api_response);
        }else{
          #对返回的数据验签
          return response($check);

        }
    }
    /**
     * 非对称
     * 加密服务端的数据
     * @param  [type] $data [数据]
     * @return [type]       [description]
     */
    private function _AesPriEncrypt($data)
    {
      $str=json_encode($data,JSON_UNESCAPED_UNICODE);
      $i = 0;
      $all='';
      while ($sub_str = substr($str, $i*117,117)) {
        openssl_private_encrypt($sub_str, $data, openssl_get_privatekey("file://".public_path('keys/pri.pem')),OPENSSL_PKCS1_PADDING);
        $all.=base64_encode($data);
        $i++;
      }
      return $all;
    }
    /**
     * 非对称
     * 解密客户端的数据
     */
    private function _AesPriDecrypt($request)
    {
      $i = 0;
      $all='';
      while ($sub_str = substr($request->post('data'), $i*344,344)) {
        $dec_str=base64_decode($sub_str);
        openssl_private_decrypt($dec_str, $data, openssl_get_privatekey("file://".public_path('keys/pri.pem')),OPENSSL_PKCS1_PADDING);
        $all.=$data;
        $i++;
      }
        return json_decode($all,true);
    }
    /**
     * 生成签名
     * @return [type] [description]
     */
    private function createServiceSign($data,$app_id)
    {
      $data['app_id']=$app_id;
      //定义允许调用接口的appid和key
      $app_arr=[
        '1810'=>'18101810'
      ];
      // var_dump($data);die;
      $app_id=$data['app_id'];
      if (empty($app_arr[$app_id])) {
        return $this->fail('appid error !');
      }

      ksort($data);
      $json_str=http_build_query($data).'&app_key='.$app_arr[$app_id];
      return md5($json_str);
    }
    /**
     * 验证客户端的签名
     * @return [type] [description]
     */
    private function _CheckSign($request,$data)
    {
      //定义允许调用接口的appid和key
      $app_arr=[
        '1810'=>'18101810'
      ];
      $app_id=$data['app_id'];
      if (empty($app_arr[$app_id])) {
        return $this->fail('check sign fail app_id error');
      }
      //1 ksort
      ksort($data);
      // 2json
      $json_str=http_build_query($data);
      //拼接
      $json_str.='&app_key='.$app_arr[$app_id];
      $server_sign=md5($json_str);
      // var_dump($server_sign);var_dump($request->post('sign'));die;
      //验证客户端和服务端生成的签名是否一致
      if ($server_sign!=$request->post('sign')) {
        return $this->fail('check sign fail');
      }else{
        return $this->success();
      }
    }
    /**
     * 成功回复
     * @param  string $code [状态码]
     * @param  array  $data [数据]
     * @param  string $msg  [回复信息]
     * @return [type]       [description]
     */
    public function success($code='1000',$data=[],$msg='success')
    {
      return $this->output($code,$data,$msg);
    }
    /**
     * 失败回复
     * @param  string $code [状态码]
     * @param  array  $data [数据]
     * @param  string $msg  [回复信息]
     * @return [type]       [description]
     */
    public function fail($code='1',$data=[],$msg='fail')
    {
      return $this->output($code,$data,$msg);
    }
    /**
     * 统一回复
     * @param  string $code [状态码]
     * @param  array  $data [数据]
     * @param  string $msg  [回复信息]
     * @return [type]       [description]
     */
    public function output($code='1000',$data=[],$msg='success')
    {
      $arr=[
          'status'=>$code,
          'msg'=>$msg,
          'data'=>$data
      ];
      return $arr;
    }
    /**
     * 检测是否超过接口调用上线
     * 1分钟不可以超过150次
     */
    public function _checkApiAccessCount($request)
    {
      $ip=$request->ip();
      $ip_key='IP'.$ip;

      #先判断ip是否存在黑名单中，如果在就不让继续访问
      $black_key='black_list';
      //取出黑名单的第一位和最后一个 就 全部
      $black_list=Redis::zRange($black_key,0,-1);

      if (in_array($ip,$black_list)) {
        #判断进入黑名单的时候是否超过一小时
        $join_time=Redis::zScore($black_key,$ip);
        #不超过1小时 不准许访问
        if (time()-$join_time<3600) {
          return $this->fail('error:Access is temporarily unavailable. Please try again later.');
        }else{
          #超过1小时 移除黑名单
          Redis::zRem($black_key,$ip);
        }
      }
      //记录访问次数
      $count=Redis::incr($ip_key);
      #第一次访问设置有效时间1分钟
      if ($count==1) {
        Redis::expire($ip_key,60);
      }
      #超过限制数，加入黑名单
      if ($count>=$this->error_count) {
        #加入黑名单
        Redis::zAdd($black_key,time(),$ip);

        return $this->fail('error:Visits are too frequent and have joined the cottage. Try again in an hour.');
      }
        return $this->success();
    }
}