<?php

namespace app\index\validate;

class Cart extends \think\Validate
{
    protected $rule =   [
        'goods_id'  => 'require|number',
        'num'   => 'require|number',
    ];

    protected $message  =   [
        'goods_id.require' => '商品id必须',
        'goods_id.number'   => '商品id必须为数字',
        'num.require'   => '商品数量必须',
        'num.number'   => '商品数量必须为数字'
    ];
}