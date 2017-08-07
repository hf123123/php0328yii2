<?php

namespace frontend\controllers;

//use app\models\ShopAddress;
use app\models\Address;
use app\models\Cart;
use app\models\OrderGoods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\Goods;
use frontend\models\Member;
use frontend\models\Order;
use yii\db\Exception;
use yii\web\Cookie;

class GoodsController extends \yii\web\Controller
{
    public $layout=false;
    public $enableCsrfValidation=false;
    public function actionIndex(){
        $GoodsCategory=GoodsCategory::find()->all();

        return $this->render('index',['goods_categories'=>$GoodsCategory]);
    }
    public function actionList($id){
        $goods=\backend\models\Goods::find()->where('goods_category_id='.$id)->all();
        return $this->render('list',['goods'=>$goods]);
    }
    public function actionGoods($id){
        $goods=\backend\models\Goods::findOne(['id'=>$id]);
        $goodsIntro=\backend\models\GoodsIntro::findOne(['goods_id'=>$id]);
        $model=\backend\models\GoodsGallery::findOne(['goods_id'=>$id]);
       // var_dump($model);exit;
        return $this->render('goods',['goods'=>$goods,'intro'=>$goodsIntro,'model'=>$model]);

    }


    //添加购物车成功页面
    public function actionAddToCart($goods_id,$amount)
    {
        //未登录
        if(\Yii::$app->user->isGuest){
            //如果没有登录就存放在cookie中
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [$goods_id=>$amount];
            }else{
                $carts = unserialize($cart->value);//[1=>99，2=》1]
                if(isset($carts[$goods_id])){
                    //购物车中已经有该商品，数量累加
                    $carts[$goods_id] += $amount;
                }else{
                    //购物车中没有该商品
                    $carts[$goods_id] = $amount;
                }
            }
            //将商品id和商品数量写入cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name'=>'cart',
                'value'=>serialize($carts),
                'expire'=>7*24*3600+time()
            ]);
            $cookies->add($cookie);
        }else{
            //用户已登录，操作购物车数据表
            $member_id = \Yii::$app->user->id;
            $model = Cart::findOne(['member_id'=>$member_id,'goods_id'=>$goods_id]);
            if($model){
                $model->amount +=$amount;
                $model->save();
            }else{
                $cartModel = new Cart();
                $cartModel->goods_id = $goods_id;
                $cartModel->amount = $amount;
                $cartModel->member_id = $member_id;
                $cartModel->save();
            }
        }
        return $this->redirect(['cart']);
    }



    //购物车页面
    public function actionCart()
    {
        $this->layout = false;
        //1 用户未登录，购物车数据从cookie取出
        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            //var_dump(unserialize($cookies->getValue('cart')));
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [];
            }else{
                $carts = unserialize($cart->value);
            }

            //获取商品数据
            $models =\backend\models\Goods::find()->where(['in','id',array_keys($carts)])->asArray()->all();
        }else{
            //2 用户已登录，购物车数据从数据表取
            $member_id=\Yii::$app->user->getId();
            $members=Cart::find()->where(['member_id'=>$member_id])->select('goods_id,amount')->asArray()->all();
            $models=[]; //用来存购物车的商品
            $carts=''; //用来存存购物商品个数
            foreach ($members as $member){
                $models[]=\backend\models\Goods::findOne(['id'=>$member['goods_id']]); //得到有哪些商品
                $carts[$member['goods_id']]=$member['amount']; // [商品id=>商品的个数]
            }
        }
        return $this->render('cart',['models'=>$models,'carts'=>$carts]);
    }

    //修改购物车数据
    public function actionAjaxCart()
    {
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');
        //数据验证
        if (\Yii::$app->user->isGuest) {
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if ($cart == null) {
                $carts = [$goods_id => $amount];
            } else {
                $carts = unserialize($cart->value);//[1=>99，2=》1]
                if (isset($carts[$goods_id])) {
                    //购物车中已经有该商品，更新数量
                    $carts[$goods_id] = $amount;
                } else {
                    //购物车中没有该商品
                    $carts[$goods_id] = $amount;
                }
            }
            //将商品id和商品数量写入cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name' => 'cart',
                'value' => serialize($carts),
                'expire' => 7 * 24 * 3600 + time()
            ]);
            $cookies->add($cookie);
            return 'success';
        }
    }


    //删除功能
    public function actionDelCart($id){
        if(\Yii::$app->user->isGuest){//没登录
            //先取出cookie中的购物车商品
            $cookies=\Yii::$app->request->cookies;//(读取信息request里面的cookie）
            $carts=unserialize($cookies->get('cart'));
            //var_dump($carts);exit;
            unset($carts[$id]);
            $cookies=\Yii::$app->response->cookies;
            //实例化cookie
            $cookie=new Cookie([
                'name'=>'cart',//cookie名
                'value'=>serialize($carts) ,//cookie值
                'expire'=>7*24*3600+time(),//设置过期时间
            ]);
            $cookies->add($cookie);//将数据保存到cookie

        }else{//已经登录
            //var_dump($id);exit;
            $member_id=\Yii::$app->user->identity->id;
            $model =Cart::find()
                ->andWhere(['member_id'=>$member_id])
                ->andWhere(['goods_id'=>$id])
                ->one();
            $model->delete();
        }
        //删除成功，跳转到购物车页面
        return $this->redirect(['index/cart']);
    }
    //显示订单页面
   /* public function actionOrder(){

        //先判断是否登陆，登录就读取购物车数据表，没有登录就跳转到登录页面
        if(\yii::$app->user->isGuest){
            return $this->redirect('/user/login');
        }else{
            $address=Address::find()->where(['user_id'=>\yii::$app->user->id])->all();
            $cars=Cart::find()->where(['member_id'=>\yii::$app->user->id])->all();
            return $this->render('goods/order');
        }
    }*/
    //订单状态
   /* public function actionOrder()
    {
        $address=Address::find()->where(['id'=>\yii::$app->user->id])->all();
        $cars=Cart::find()->where(['member_id'=>\yii::$app->user->id])->all();
        $member=Member::find()->where(['id'=>\Yii::$app->user->id])->all();
        return $this->render('order',['cars'=>$cars,'address'=>$address,'member'=>$member]);

    }*/
    //订单
    public function actionOrder()
    {
        $model = new Order();

        //先判断是否登陆，登录就读取购物车数据表，没有登录就跳转到登录页面
        if (\yii::$app->user->isGuest) {
            return $this->redirect(['user/login']);
        } else {
            $transaction = \Yii::$app->db->beginTransaction();
            //var_dump($model->getErrors());exit;
            //var_dump($model);exit;
            if ($model->load(\yii::$app->request->post()) && $model->validate()) {
                //开启事务
                //var_dump(111);exit;
                try {
                    //处理数据
                    $deliveries = Order::$deliveries;
                    $payment = Order::$pay;
                    $model->member_id = \yii::$app->user->id;

                    $address = Address::findOne(['id' => $model->address_id]);
                    $model->name = $address->name;
                    $model->province = $address->area;
                    $model->address = $address->detail;
                    //var_dump($model);exit;
                    $model->tel = $address->tel;
                    //var_dump($model);exit;
                    $model->delivery_name = $deliveries[$model->deliveries_id]['name'];
                    $model->delivery_price = $deliveries[$model->deliveries_id]['price'];
                    $model->delivery_id = $model->deliveries_id;
                    $model->payment_id = $model->pay_id;
                    $model->payment_name = $payment[$model->pay_id]['name'];
                    $model->trade_no = rand(10000, 99999);
                    $model->create_time = time();
                    $model->status = 1;
                    $model->total = $model->total_price;
//                  var_dump($model->member_id);exit;
                    $model->save();
//                    var_dump($model->getErrors());exit;
                    //操作order_goods表
                    //取出所有的数据，进行遍历
                    $cart = Cart::find()->where(['member_id' => \yii::$app->user->id])->all();
                    //var_dump(111);exit;
                    foreach ($cart as $v) {
                        $order = new OrderGoods();
                        $order->order_id = $model->id;
                        $order->goods_id = $v->goods_id;
//                      var_dump($order->goods_id);exit;
                        $order->goods_name = Goods::findOne(['id' => $v->goods_id])->name;
                        $order->logo = Goods::findOne(['id' => $v->goods_id])->logo;
                        $order->price = Goods::findOne(['id' => $v->goods_id])->shop_price;
                        //判断数据库商品数量，如果数量足够就减去对应的数量，如果没有就回滚
                        $goods = Goods::findOne(['id' => $v->goods_id]);
                        $order->amount = $v->amount;
                        $order->total = $v->amount * $order->price;
                        $order->save();
                        //判断大小
                        if ($v->amount < $goods->stock) {
                            $goods->stock = ($goods->stock) - ($v->amount);
                            $goods->save();
                        } elseif ($v->amount > ($goods->stock)) {
                            throw new Exception('商品库存不足，无法继续下单，请修改购物车商品数量');
                        }
                        //清空购物车数据
                        $v->delete();
                    }
                    $transaction->commit();
                    return $this->redirect('order-end');
                } catch (Exception $e) {
                    $transaction->rollBack();
                    return $this->redirect(['goods/order']);
                }
            } else {
             //var_dump(111);exit;
                $address = Address::find()->all();
                $cars = Cart::find()->where(['member_id' => \yii::$app->user->id])->all();
                return $this->render('order', ['model' => $model, 'cars' => $cars, 'address' => $address]);
            }
        }
    }
    //提交订单完成页面
    public function actionOrderEnd()
    {
        return $this->render('flow3');
    }
    //订单页面
    //查看订单信息
    public function actionOrdereds(){
        $model=Order::find()->where(['member_id'=>\yii::$app->user->id])->all();
        return $this->render('ordereds',['models'=>$model]);
    }

}
