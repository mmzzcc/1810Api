<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;
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
}
