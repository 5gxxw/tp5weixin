<?php


namespace app\index\model;


use think\Exception;
use think\Model;

class Goods extends Model
{
    /**
     * 减少库存,加销量
     * @param $result
     */
    public function stock($goods_shop_data)
    {

        foreach($goods_shop_data['result'] as $v){
            $goods = Goods::get($v['goods_id']);
            if($goods['stock'] < $v['num']){
                $this->error = '库存不足';
                return false;
            }
            $goods->stock = $goods['stock'] - $v['num'];
            $goods->sales = $goods['sales'] + $v['num'];
            $goods->save();
        }
        return true;
    }
}