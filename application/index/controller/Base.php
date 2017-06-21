<?php
namespace app\index\controller;

use think\Controller;

class Base extends Controller{
	public function _initialize(){
		// $array = array(
		// 	'http://vue.wxbuluo.com',
		// );
		// if(in_array($_SERVER['HTTP_ORIGIN'] , $array)){
		// 	header('Access-Control-Allow-Origin:' . $_SERVER['HTTP_ORIGIN']);
		// }
		// header('Access-Control-Allow-Methods: *');
		// header('Access-Control-Allow-Headers: x-requested-with,authorization');		
	}

	/**
	 * 获取JSSDK签名
	 * @author ning
	 * @DateTime 2016-08-08T14:28:51+0800
	 * @return   [type]                   [description]
	 */
	public function getSignpackage(){
		$url = input('post.url');
		$options = [
			'token'=>config('token'),
			'encodingaeskey'=>config('encodingaeskey'),
			'appid'=>config('appid'),
			'appsecret'=>config('appsecret')
		];
		$weObj = new \com\Wechat($options);
	    // $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	    // $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$signPackage = $weObj->getJsSign($url);
		return json($signPackage);
	}

	/**
	 * ajax返回成功信息
	 * @author ning
	 * @DateTime 2016-09-04T11:05:33+0800
	 * @param    [type]                   $data [description]
	 * @return   [type]                         [description]
	 */
	protected function ajaxSuccess($data)
	{
		return json(['status'=>200,'data'=>$data]);
	}

	/**
	 * ajax返回错误信息
	 * @author ning
	 * @DateTime 2016-09-04T11:06:04+0800
	 * @param    [type]                   $data [description]
	 * @return   [type]                         [description]
	 */
	protected function ajaxError($data)
	{
		return json(['status'=>400,'error'=>$data]);
	}

}