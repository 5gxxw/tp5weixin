<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/18
 * Time: 22:38
 */

namespace app\index\model;


use think\Model;

class OrderDetail extends Model
{
    /**
     * 组装订单详情信息
     * @param $goods_shop_data 购买商品的数据
     * @param $order_id 订单id
     * @return bool
     */
    public function detail_info($goods_shop_data,$order_id)
    {
        foreach($goods_shop_data['result'] as $v){
            $detail['order_id'] = $order_id;
            $detail['goods_id'] = $v['goods_id'];
            $detail['goods_name'] = $v['name'];
            $detail['goods_logo'] = $v['logo'];
            $detail['price'] = $v['price'];
            $detail['num'] = $v['num'];
            $detail['total_price'] = $v['price'] * $v['num'];
            $this->data($detail);
            if (!$this->isUpdate(false)->save()){
                $this->error = '关于{'.$v['name'].'}的订单详情添加失败';
                return false;
            }
        }
        return true;
    }

    /**
     * 关联订单表
     * @return \think\model\relation\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('Order','order_id');
    }
}