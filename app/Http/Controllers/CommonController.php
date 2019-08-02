<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;
/**
 * 公共方法
 * @author MaZhiCheng
 * package  App\Http\Controllers; 
 * @date 2019-07-25
 */
class CommonController extends Controller
{
    //内部方法
	public function abort($msg,$url){
		echo "<script>alert('{$msg}');location.href='{$url}'</script>";
	}
    //成功助手函数
    public function ok($font='操作成功',$code=1,$data=''){
        echo json_encode(['font'=>$font,'code'=>$code,'data'=>$data]);
        return;
    }
    //失败助手函数
    public function no($font='操作失败',$code=2){
        echo json_encode(['font'=>$font,'code'=>$code]);
        return;
    }
    /**
     * 发送验证码
     * @param  [type] $email [description]
     * @return [type]        [description]
     */
    public function sendMail($email)
    {
        $code=rand(1111,9999);
        $rer=Mail::send('user/forgetPassword',['code'=>$code,'name'=>$email],function($message)use($email){
            $message->subject('用户您好');
            $message->to($email);
        });
        return $code;
    }

     /**
      * 文件上传
      * @param  string $name [文件名]
      * @return [type]       [description]
      */
    public function upload($file=''){
        #检测文件上传中是否有错误
        if ($file->isValid()) {
            #获得文件扩展名
            $ext=$file->getClientOriginalExtension();
            #拼接文件名
            $fileName=md5(rand(1000,9999).time()).".".$ext;
            $path=$file->storeAs(date("Ymd"),$fileName);
            $pic=public_path()."/uploads/".$path;
            return $pic;
        }
    }
    /**
     * 无限极分类
     * @param  [type]  $cateInfo [description]
     * @param  integer $pid      [description]
     * @return [type]            [description]
     */
    public function getLeftCateInfo($cateInfo,$pid=0){
        // dd($cateInfo);
        $arr=[];
        foreach ($cateInfo as $k => $v) {
          if($v['pid']==$pid){
            $son=$this->getLeftCateInfo($cateInfo,$v['cate_id']);
            $v['data']=$son;
            $arr[]=$v;
          }
        }
        return $arr;
    }

    /**
     * 自定义POST方式curl请求
     * @param  [type] $url  [地址]
     * @param  [type] $data [数据]
     * @return [type]       [description]
     */
    public function curlPost($url,$data){
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,1);
        if(is_array($data)){
          curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($data));
        }else{
          curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        }
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $data=curl_exec($ch);
        curl_close($ch);
        return $data;
      }

    /**
     * 非对称加密
     * @param  [type] $str [原数据要求字符串类型]
     * @return [type]      [description]
     */
    public function pub_encrypt($str){
        $i = 0;
        $all='';
        while ($sub_str = substr($str, $i*117,117)) {
          openssl_public_encrypt($sub_str, $data, openssl_get_publickey('file://./pub.key'),OPENSSL_PKCS1_PADDING);
          $all.=base64_encode($data);
          $i++;
        }
        return $all;
      }
    /**
     * 非对称解密
     * @param  [type] $enc_str [加密的数据要求是字符串类型]
     * @return [type]          [description]
     */
    public function pub_decrypt($enc_str)
    {
      $i = 0;
      $all='';
      while ($sub_str = substr($enc_str, $i*344,344)) {
        $dec_str=base64_decode($sub_str);
        openssl_public_decrypt($dec_str, $data, openssl_get_publickey("file://./pub.key"),OPENSSL_PKCS1_PADDING);
        $all.=$data;
        $i++;
      }
      return $all;
    }


    /**
     * 生成签名
     * @param  [type] $data [原数据]
     * @return [type]       [description]
     */
    public function createSign($data){
        $app_key='18101810';
        //数组排序
        ksort($data);
        //生成标准请求字符串
        $str=http_build_query($data);
        //拼接appkey
        global $app_key;
        $str.='&app_key='.$app_key;
        //生成签名
        return md5($str);
      }
      /**
       * hash分表查询数据在哪个表中
       * @param  [type] $user_name [description]
       * @return [type]            [description]
       */
      public function reg_hash($user_name)
      {
          error_reporting(E_ALL^E_NOTICE);
          $hash=hash('md5', $user_name);
          $first_char=substr($hash, 0,1);
          if (!is_numeric($first_char)) {
            $table='user_'.base_convert($first_char, 16, 10);
          }else{
            $table='user_0'.$first_char;
          }
          return $table;
      }
      /**
       * 关联表查数据
       * @param  [type]  $account [用户名or手机号or邮箱]
       * @param  string  $table   [表名]
       * @param  integer $total   [表个数]
       * @return [type]           [description]
       */
      public function account_hash($account,$table='user_',$total=16)
      {
          $str=crc32($account);
          $num=$str%$total;
          if ($num<0) {
            $num=abs($num);
          }
          if (strlen($num)<=1) {
            $num='0'.$num;
          }
          $data['str']=$str;
          $data['num']=$num;
          return $data;
      }


}
