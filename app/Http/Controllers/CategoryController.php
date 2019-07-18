<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\CateModel;
use GuzzleHttp\Client;
class CategoryController extends CommonController
{
	/**
	 * 请求的接口
	 * @return [type] [description]
	 */
	public function getCategory()
	{
		$category_type=2;
		$is_show_level=0;
		$data=json_encode(['category_type'=>$category_type,'is_show_level'=>$is_show_level]);
		openssl_sign($data, $sign, openssl_get_privatekey("file://".public_path('keys/pri.pem')));
        $client=new Client;
		$url="http://1810.oj8k.xyz/category/categoryData";
		$response=$client->request('POST',$url,[
            'form_params'=>['data'=>$data,'sign'=>$sign]
        ]);
        echo $response->getBody();
	}
	/**
	 * 处理接口请求
	 * @return [type] [description]
	 */
	public function categoryData()
	{
		$data=$_POST;
		//解密
		$verify=openssl_verify($data['data'], $data['sign'], openssl_get_publickey("file://".public_path('keys/pub.key')));
		// dd($verify);
		if ($verify==1) {
			//验签成功 查数据
			$data=json_decode($data['data'],true);
			//不传值  给出初始值
			if(!isset($data['category_type'])){
				$data['category_type']=2;
			}
			if(!isset($data['is_show_level'])){
				$data['is_show_level']=0;
			}
			//展示全部
			if($data['category_type']==0){
				$cateData=CateModel::select('category_level','cate_name','cate_id','pid')->get()->toArray();
			}else{
				//展示层级
				if($data['is_show_level']==1){
					$cateData=CateModel::where('category_level','<=',$data['category_type'])->select('category_level','cate_name','cate_id','pid')->get()->toArray();
				}else{
					$cateData=CateModel::where('category_level',$data['category_type'])->select('category_level','cate_name','cate_id','pid')->get()->toArray();
				}
			}
			echo '<pre>';
			if($data['is_show_level']==1){
				$cateData=$this->getLeftCateInfo($cateData);
			}
			echo "<pre>";
			dd($cateData);
			
			//预防意外
			if(empty($cateData)){
				$this->no('没找到相关数据,试试其他的',1);
			}else{
				$this->ok('获取分类数据成功',0,$cateData);
			}
		}else{
			//验签失败
			echo "验签失败";
		}
	}
}