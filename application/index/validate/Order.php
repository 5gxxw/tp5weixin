<?php

namespace app\index\validate;


use think\Validate;

class Order extends Validate
{
    protected $rule =   [
        'goods_id'  => 'require',
        'num'   => 'number',
    ];

    protected $message  =   [
        'goods_id.require' => '��ƷID����',
        'num.number'   => '��������',
    ];
}