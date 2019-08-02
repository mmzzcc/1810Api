<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\User;
use App\Model\UserRelation;
/**
 * 处理注册登录的demo
 * @author MaZhiCheng
 * @package App\Http\Controllers  
 * @date 2019-07-25
 */
class RegController extends CommonController
{	
	/**
	 * 注册视图
	 * @return [type] [description]
	 */
	public function reg()
	{
		return view('reg.reg');
	}
	/**
	 * 处理注册 分表添加
	 * @return [type] [description]
	 */
	public function doreg()
	{	
		$data=request()->input();
		//对账号和密码进行非空验证
		if ( empty($data['user_name']) || empty($data['password']) ) {
			$this->abort('请填写完整信息','reg');exit;
		}
		//以上判断通过，代表可以注册，判断用户注册类型--1手机号。2邮箱注册。3用户名。
		if (is_numeric($data['user_name'])) {
			$reg_type=1;
		}elseif (strstr($data['user_name'],"@" )) {
			$reg_type=2;
		}else{
			$reg_type=3;
		}
		//账号唯一性验证
		$checkAccount=$this->_checkAccount($reg_type,$data);
		//返回true代表可以注册，false代表不可以注册
		if (!$checkAccount) {
			$this->abort('该账号已被注册','reg');exit;
		}
		//处理注册 1关联表入库 2用户入库
		$res=$this->_regUserRelation($reg_type,$data);
		//成功入库给出提示
    	if ($res) {
    		$this->abort('注册成功','login');exit;
    	}else{
    		$this->abort('注册失败','reg');exit;
    	}
	}
	/**
	 * 检测唯一性
	 * @param  [type] $reg_type [注册账号类型]
	 * @param  [type] $data     [客户端原数据]
	 * @return [type]           [description]
	 */
	private function _checkAccount($reg_type,$data)
	{	
		//将用户的信息crc32,进行关联表数据对比，匹配成功则注册过，反之 注册
		$relation_data=$this->account_hash($data['user_name']);
		// dd($relation_data);
        $UserRelation_Model = new UserRelation();
		$check_user=$UserRelation_Model
		->where(['user_name'=>$relation_data['str']])
		->whereOr(['user_tel'=>$relation_data['str']])
		->whereOr(['user_email'=>$relation_data['str']])
		->whereOr(['user_table'=>$relation_data['num']])
		->first();
		//没查到返回bool值 true
		if (!$check_user) {
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 1处理入库关联表  2处理用户入库
	 * @param  [type] $reg_type [注册账号类型]
	 * @param  [type] $data     [用户传过来的数据]
	 * @return [type]           [description]
	 */
	private function _regUserRelation($reg_type,$data)
	{
		//将用户传过来的注册信息crc32处理后返回 入库到关联表中
		//返回信息为数组 键str为cac32处理的数据 num为表名
        $relation_data=$this->account_hash($data['user_name']);
        $UserRelation_Model = new UserRelation();
        switch ($reg_type) {
        	case '1':
        		$UserRelation_Model->user_tel = $relation_data['str'];
        		break;
        	case '2':
        		$UserRelation_Model->user_email = $relation_data['str'];
        		break;
        	case '3':
        		$UserRelation_Model->user_name = $relation_data['str'];
        		break;
        	default:
        		break;
        }
        //写入表名数据
        $UserRelation_Model->user_table = $relation_data['num'];
        $result=$UserRelation_Model->save();
        //入库关联表成功后写注册逻辑 -- 入库用户信息
        if ($result) {
        	$user_model = new User();
        	//指定表名
        	$user_model->table='user_'.$relation_data['num'];
    		//按类型入库
	        	switch ($reg_type) {
	        	case '1':
	        		$user_model->user_tel = $data['user_name'];
	        		break;
	        	case '2':
	        		$user_model->user_email = $data['user_name'];
	        		break;
	        	case '3':
	        		$user_model->user_name = $data['user_name'];
	        		break;
	        	default:
	        		break;
        	}
        	$user_model->password = md5($data['password']);
        	$user_model->status = 1;
        	$res=$user_model->save();
        	//返回bool值通知用户
        	if ($res) {
        		return true;
        	}else{
        		return false;
        	}
        }
	}
	/**
	 * 登陆视图
	 * @return [type] [description]
	 */
	public function login()
	{
		return view('reg.login');
	}
	/**
	 * 处理多表查询登陆
	 * @return [type] [description]
	 */
	public function dologin()
	{
		$data=request()->all();
		//对账号和密码进行非空验证
		if (empty($data['user_name']) || empty($data['password'])) {
			$this->abort('请填写完整信息','login');exit;
		}
		//判断用户登录类型--1手机号。2邮箱注册。3用户名。
		if (is_numeric($data['user_name'])) {
			$reg_type=1;
		}elseif (strstr($data['user_name'],"@" )) {
			$reg_type=2;
		}else{
			$reg_type=3;
		}
		//剩下的交给登陆方法处理
		$this->_loginUserRelation($reg_type,$data);
	}
	/**
	 * 处理多表关联，登陆
	 * @param  [type] $reg_type [注册账号类型]
	 * @param  [type] $data     [客户端原数据]
	 * @return [type]           [description]
	 */
	private function _loginUserRelation($reg_type,$data)
	{
		//通过分类型之后的账号 生成cac32值 作为查询条件 进行多条件查询
        $relation_data=$this->account_hash($data['user_name']);
        $UserRelation_Model = new UserRelation();
        //通过判断用户登录类型，生成不同的where条件
        $where=[];
        switch ($reg_type) {
        	case '1':
        		$where=['user_tel'=>$relation_data['str']];
        		break;
        	case '2':
        		$where=['user_email'=>$relation_data['str']];
        		break;
        	case '3':
        		$where=['user_name'=>$relation_data['str']];
        		break;
        	default:
        		break;
        }
        //直接通过登录的账号查询出该用户在那张用户表中，进行身份验证
		$user_table=$UserRelation_Model->where($where)->value('user_table');
		if ($user_table) {
			//查到表名，根据表名核实用户信息
			$user_model = new User();
			//在关联表查出的用户所在表可能小于10 所以要判断一下，不同的表名不同拼接组合
			if ($user_table<10) {
				$user_model->table='user_0'.$user_table;
			}else{
				$user_model->table='user_'.$user_table;
			}
			//通过组装好的表名，可以准确的定位到该用户所在的用户表，直接多条件查询验证密码即可 whereOr多条件查询
			$user=$user_model
			->where(['user_name'=>$data['user_name']])
			->whereOr(['user_tel'=>$data['user_name']])
			->whereOr(['user_email'=>$data['user_name']])
			->first();
			//此处要加一层判断 以防 关联数据表写入成功但是用户没有入库成功 。
			if ($user) {
				//判断一下密码就o了
				if (md5($data['password'])==$user->password) {
					$this->abort('登陆成功','index');exit;
				}else{
					$this->abort('账号或密码错误','login');exit;
				}
			}else{
				$this->abort('该账号存在异常','login');exit;
			}
		}else{
			$this->abort('该用户还没有注册，请先注册','reg');exit;
		}
	}
}