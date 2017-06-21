<?php

namespace app\index\model;
use think\Model;
use think\Loader;
use think\Session;
use think\Validate;

class Cart extends Model
{

    /**
     * 加入购物车
     * @param $data 加入购物车数据
     * @param $user 用户信息
     * @return bool|int
     */
    public function updates($data,$user){

        $validate = Loader::validate('Cart');

        if(!$validate->check($data)){
            //验证失败
            $this->error = $validate->getError();
            return false;
        }

        //查询购物车表是否已经有该商品
        $result = Cart::get(['goods_id'=>$data['goods_id'],'member_id'=>$user['id']]);

        if(!$result){
            //没有,直接添加
            $data['member_id'] = $user['id'] ;
            $this->data($data);
            $this->allowField(true)->save();
        }else{
            //存在,更新数量
            $data['num'] = $data['num'] + $result['num'] ;
            $this->allowField(true)->save($data,['id'=>$result['id']]);
        }

        //查询出该用户购物车的数量
        $num =count( Cart::all(['member_id'=>$user['id']]));
        return $num;
    }

    /**
     * 改变购物车数量
     */
    public function num($data)
    {
        return $this->save(
            ['num' => $data['num']],
            ['id' => $data['cart_id']]
        );
    }

    /**
     * 删除购物车数据
     * @param $goods_shop_data 购物车数据
     */
    public function del_cart($goods_shop_data)
    {
        foreach($goods_shop_data['result'] as $v){
            $num = self::destroy($v['cart_id']);
            if (!$num){
                $this->error = '购物车id为'.$v['cart_id'].'的数据删除失败';
                return false;
            }
        }
        return true;
    }
}