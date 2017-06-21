<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/21
 * Time: 14:10
 */

namespace app\index\controller;


class Index
{
    public function index()
    {
        $options = [
            'token'=>config('token'),
            'encodingaeskey'=>config('encodingaeskey'),
            'appid'=>config('appid'),
            'appsecret'=>config('appsecret')
        ];
        $weObj = new \com\Wechat($options);
        //微信服务器验证 服务器
        $weObj->valid(true);

    }
}