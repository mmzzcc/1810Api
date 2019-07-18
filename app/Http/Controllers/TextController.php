<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class TextController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        echo __METHOD__;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        echo __METHOD__;

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        // echo __METHOD__;die;
        //非对称加密数据
        $post_data=request()->all();
       //生成字符串 键排序&拼接
        $str = json_encode($post_data);
        //签名
        openssl_sign($str, $signature, openssl_get_privatekey("file://".public_path('keys/pri.pem')));
        $sig=base64_encode($signature);
        $arrParams['signature']=$sig;
        $arrParams['data']=$str;
        //发送数据
        $client=new Client;
        $url="http://1810.oj8k.xyz/mcool/doreg";
        $response=$client->request('POST',$url,[
            'form_params'=>$arrParams
        ]);
        echo $response->getBody();
        

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        echo __METHOD__;

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        echo __METHOD__;

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        echo __METHOD__;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        echo __METHOD__;

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
}
