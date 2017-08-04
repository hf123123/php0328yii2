<?php

namespace frontend\controllers;

//use app\models\ShopAddress;
use app\models\Address;
use app\models\Cart;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use frontend\models\Goods;
use frontend\models\Member;
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


    public function actionDelete($id){
        $model=Cart::findOne(['id'=>$id]);
        $model->delete();

        return $this->redirect('car');
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
    public function actionOrder()
    {
        $address=Address::find()->where(['id'=>\yii::$app->user->id])->all();
        $cars=Cart::find()->where(['member_id'=>\yii::$app->user->id])->all();
        $member=Member::find()->where(['id'=>\Yii::$app->user->id])->all();
        return $this->render('order',['cars'=>$cars,'address'=>$address,'member'=>$member]);

    }


}
