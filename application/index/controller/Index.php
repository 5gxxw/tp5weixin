<?php
namespace app\index\controller;

use app\index\model\Cart;
use app\index\model\Goods;
use com\Wechat;
use think\Controller;
use think\Db;
use think\Exception;
use think\Session;

class Index extends Controller
{

    /**
     * @return \think\response\View
     * 假设登录
     */
    public function login()
    {
        //假设登录
        $id = 1;
        $user = [
            'id' => $id,
            'name' => '小明',
            'tel' => '13144554455',
        ];
        Session::set("user_{$id}",$user);
    }

    /**
     * 商品列表
     */
    public function index()
    {
        //从数据库读取商品列表信息
        $lists = Db::name('goods')->select();
        $this->assign('lists' ,$lists);
        return $this->fetch('list');
    }

    //商品详情页
    public function intro($id)
    {
        if(empty($id)){
            $this->redirect('index');
        }
        //根据商品id查询出商品数据
        $result = Db::name('goods')
            ->where(['id'=>$id])
            ->join('goods_intro','goods.id = goods_intro.goods_id')
            ->find();

        //查询出购物车信息
        $user = Session::get('user_1');
        if($user){
            $data = Db::name('cart')->where(['member_id'=>$user['id']])->select();
            $result['cart_num'] = count($data);
        }else{
            $result['cart_num'] = 0;
        }

        //将数据渲染
        $this->assign('result',$result);
        return $this->fetch();
    }


    /**
     * 加入购物车
     */
    public function cart()
    {
        if(request()->isAjax()){
            $data = input('post.');
            if(!is_numeric($data['goods_id']) || !is_numeric($data['num'])){
                $this->error('参数错误');
            }

            //判断是否登录,如果未登录就保存到cookie，登录，保存到数据表。这里我先直接保存到数据表。
            $user = Session::get('user_1');
            if(!$user){
                $this->login();
            }

            $cart = model('Cart');
            $result = $cart->updates($data,$user);
            if(!$result){
                $this->error($cart->getError());
            }else{
                $this->success('加入购物车成功','',$result);
            }
        }
    }

    /**
     * 购物车列表
     */
    public function cartList()
    {
        //判断是否登录
        if(!Session::get('user_1')){
            $this->login();
        }
        $user = Session::get('user_1');

        $result = Db::name('cart')
            ->alias('a')
            ->field('b.id as goods_id,a.id as cart_id,name,price,num,logo')
            ->join('goods b','a.goods_id = b.id')
            ->where(['member_id'=>$user['id']])->select();

        $this->assign('user',$user);
        $this->assign('result',$result);
        return $this->fetch();
    }





    /**
     * 删除购物车数据
     */
    public function cartDel()
    {
        $data = input('post.');

        if(!$data){
            $this->error('错误');
        }
        $data = Cart::destroy(['id' => $data['id']]);
        if($data){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }

    }

    /**
     * 输入数量,改变数量
     */
    public function change_num()
    {
        if(request()->isAjax()){
            $data = input('post.');
            if(!is_numeric($data['cart_id']) || !is_numeric($data['num'])){
                $this->error('参数错误');
            }else{
                $cart = model('Cart');
                $result = $cart->num($data);
                if ($result){
                    $this->success('修改成功!');
                }else{
                    $this->error('修改失败!');
                }
            }
        }else{
            $this->error('请求方式错误');
        }
    }





    /**
     * 立即购买和去结算
     */
    public function orderAdd()
    {
        //判断是否登录，如果未登录则先登录
        if(!Session::get('user_1')){
            $this->login();
        }
        $user = Session::get('user_1');
        $data = input('post.');

        //获取默认地址信息
        $address = Db::name('site')->where(['user_id'=>$user['id'],'status'=>1])->find();

        if (!$address){
            Session::set("data_{$user['id']}",$data);
            //跳转到添加收货地址
            $this->fetch('address_add');
        }

        $other = [];//存放商品总数量和总价格
        $order = model('Order');
        if(isset($data['cart_value'])){
            $result = $order->more($data,$other,$user);
        }else{
            $result = $order->one($data,$other);
        }

        //将数据保存到session
        Session::set("goods_shop_{$user['id']}",$result);

        $this->assign('address',$address);
        $this->assign('other',$result['other']);
        $this->assign('result',$result['result']);
        return $this->fetch('orderadd');
    }

    /**
     * 收货地址
     */
    public function address()
    {
        return $this->fetch();
    }

    /**
     * 提交订单
     */
    public function order()
    {
        $data = input('post.');
        $user = Session::get('user_1');
        //从session中获取购买的商品数据
        $goods_shop_data = Session::get("goods_shop_{$user['id']}");

        $order = model('Order');
        //组装订单数据
        $result = $order->info($data,$user,$goods_shop_data);
        if (!$result){
            $this->error($order->getError());
        }

        //开启事务
        Db::startTrans();

        try{
            //添加订单信息
            $order_id = $order->add($result);
            if (!$order_id){
                throw new Exception($order->getError());
            }

            //添加订单详情
            $detail = model('OrderDetail');
            $order_detail = $detail->detail_info($goods_shop_data,$order_id);
            if (!$order_detail){
                throw new Exception($detail->getError());
            }

            //减商品库存,加销量
            $goods = model('Goods');
            $data = $goods->stock($goods_shop_data);
            if (!$data){
                throw new Exception($goods->getError());
            }

            //删除购物车数据
            if(isset($goods_shop_data['result']['0']['cart_id'])){
                $cart = model('Cart');
                $info = $cart->del_cart($goods_shop_data);
                if (!$info){
                    throw new Exception($cart->getError());
                }
            }
            // 提交事务
            Db::commit();

        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error($e->getMessage(),'cartList');
        }
        $this->success('订单添加成功!','myOrder');
    }

    /**
     * 我的订单
     */
    public function myOrder()
    {

        if(!$user = Session::get('user_1')){
            $this->login();
        }
        
        //查询订单表信息
        $result = Db::name('Order')
            ->where(['member_id'=>$user['id'] , 'status'=>['>=',1]])
            ->select();
        //查询订单详情表信息
        foreach($result as &$v){
            $v['detail'] = Db::name('OrderDetail')->where(['order_id'=>$v['id']])->select();
        }
        $this->assign('result',$result);
        return $this->fetch('myorder');

    }

    /**
     * 测试用户网页授权
     * 1.判断用户是否登录,如果没有登录,到微信服务器获取信息
     */
    public function auth()
    {
        $openid = Session::get('openid');
        if (!$openid){
            
            $appid = config('appid');
            $callback = urlencode(config('callback'));
            $scope = config('scope');
            //跳转到微信服务器获取code,微信服务器会返回信息到回调地址.
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$callback.'&response_type=code&scope='.$scope.'&state=STATE#wechat_redirect';

            //保存当前地址
            Session::set('url1',url());

            $this->redirect($url);
        }
    }

    //回调地址,获得code,
    public function callback()
    {
        $data = input('get.');
        $code = $data['code'];
        $appid = config('appid');
        $appsecret = config('appsecret');
        //请求连接获取access_token,和openid
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';

        $curl = curl_init();
        //设置网址
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 );
        //获取到access_token,和openid
        $data = curl_exec($curl);
        curl_close($curl);
        dump($data);
    }

}
