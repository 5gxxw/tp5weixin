<?php


namespace app\index\model;


use think\Db;
use think\Model;
use think\Loader;
use think\Session;

class Order extends Model
{

    /**
     * 添加订单
     * @param $data
     */
    public function add($data)
    {
        $this->data($data);
        if ($this->save()){
            return $this->id;
        }else{
            $this->error = '订单添加失败';
            return false;
        }
    }

    /**
     * 立即购买
     * 查询单个商品信息
     * @param $data
     * @param $other
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public function one($data,$other)
    {

        $result = Db::name('goods')
            ->field('id as goods_id,name,price,logo')
            ->where(['id'=>$data['goods_id']])
            ->select();
        $result[0]['num'] = $data['number'];
        $other['count'] =  $data['number'] * $result[0]['price'];
        $other['goods_num'] = $result[0]['num'];

        $result = ['result'=>$result,'other'=>$other];

        return $result;
    }


    /**
     * 去结算
     * 查询多个购物车商品信息
     * @param $data
     * @param $other
     * @param $user
     * @return array
     */
    public function more($data,$other,$user)
    {
        $arr = explode(',',$data['cart_value']);
        $result = [];
        $count= '';
        $other['goods_num'] = '';
        //查询出需要结算的购物车的数据
        foreach($arr as $v){
            $order = Db::name('cart')
                ->field('cart.id as cart_id,goods_id,name,price,logo,num')
                ->where(['cart.id'=>$v,'member_id'=>$user['id']])
                ->join('goods','goods.id = cart.goods_id')
                ->find();
            $count += $order['num'] * $order['price'];
            $result[] = $order;
            $other['goods_num'] += $order['num'];
        }
        $other['count'] = $count;

        $result = ['result'=>$result,'other'=>$other];

        return $result;
    }

    /**
     * 组装订单信息
     * @param $data
     * @return bool
     */
    public function info($data,$user,$goods_shop_data)
    {
        if (!is_numeric($data['address_id'])){
            $this->error = '参数错误';
            return false;
        }
        if (strlen($data['guestbook']) > 50){
            $this->error = '文字超长';
            return false;
        }


        //收货地址信息
        $address = Db::name('site')->where(['id'=>$data['address_id']])->find();

        //生成订单编号
        $order['sn'] = date('YmdHis',time()).rand(100,999);
        //用户id
        $order['member_id'] = $user['id'];
        //收货地址
        $order['name'] = $address['name'];
        $order['province'] = $address['province'];
        $order['city'] = $address['city'];
        $order['area'] = $address['area'];
        $order['particular'] = $address['particular'];
        $order['tel'] = $address['tel'];
        $order['create_time'] = time();
        //订单总金额
        $order['price'] = $goods_shop_data['other']['count'];
        //状态
        $order['status'] = 1 ;
        //买家留言
        $order['guestbook'] = addslashes($data['guestbook']);

        return $order;
    }

}