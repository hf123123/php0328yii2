<?php

namespace frontend\controllers;

use app\models\Address;
use app\models\Cart;
use backend\models\Goods;
use Behat\Gherkin\Loader\YamlFileLoader;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\captcha\CaptchaAction;
use yii\helpers\Json;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class UserController extends \yii\web\Controller
{
    public $layout = false;
    //关闭csrf验证
    public $enableCsrfValidation = false;

    //用户注册
    public function actionRegister()
    {
        $model = new Member();
        $model->scenario = Member::SCENARIO_REGISTER;
        return $this->render('register', ['model' => $model]);
    }

    //AJAX表单验证
    public function actionAjaxRegister()
    {
        $model = new Member();
        $model->scenario = Member::SCENARIO_REGISTER;
        //var_dump($model);exit;
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $model->save(false);
            //var_dump($model->getErrors());//    ['username'=>]
            //保存数据，提示保存成功

            return Json::encode(['status' => true, 'msg' => '注册成功']);
        } else {
            //验证失败，提示错误信息

            return Json::encode(['status' => false, 'msg' => $model->getErrors()]);
        }
    }

    //显示首页
    public function actionIndex()
    {

        return $this->render('index');
    }



    //登录
    /*public function actionLogin(){
        if(!\Yii::$app->user->isGuest){
            return $this->redirect(['user/index']);
        }
        $model = new LoginForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->login()){
                return $this->redirect(['user/index']);
            }
        }
        return $this->render('login',['model'=>$model]);
    }*/

    //用户登录
    public function actionLogin(){
        $model = new LoginForm();
        $request = new Request();
        if($request->isPost) {
            $model->load($request->post());
            if ($model->validate() && $model->login()) {
                //var_dump(111);exit;
                $cookies = \Yii::$app->request->cookies;
                $cart = $cookies->get('cart');
                $member_id = \Yii::$app->user->id;
                if($cart!=null){
                    $carts = unserialize($cart->value);
                    foreach($carts as $key=>$values){
                        $cartModel = Cart::findOne(['goods_id'=>$key,'member_id'=>$member_id]);
                        if($cartModel){
                            //如果数据表已经有这个商品,就合并cookie中的数量
                            $cartModel->amount += $values;
                            $cartModel->save();
                        }else{
                            $cartMo = new Cart();
                            $cartMo->goods_id = $key;
                            $cartMo->amount = $values;
                            $cartMo->member_id = $member_id;
                            $cartMo->save();
                            //var_dump($cartMo->getErrors());exit;
                        }
                    }
                    //清除cookie
                    \Yii::$app->response->cookies->remove('cart');
                }
                //登录成功
                return $this->redirect(['user/index']);
            }
        }
        return $this->render('login',['model'=>$model]);
    }
//    public function actionLogin()
//    {
//        if (!\Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }
//        $model = new LoginForm();
//        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
//            //用户登录成功
//            //获取cookie中的购物车数据，
//            $cookies = \Yii::$app->request->cookies;
//            $cart = unserialize($cookies->getValue('cart'));
//            if ($cart) {
//                //var_dump($cart);exit;
//                //循环遍历购物车数据
//                foreach ($cart as $key => $amount) {
//                    //(使用goods_id作为查询条件，member_id)
//                    $model = Cart::findOne(['goods_id' => $key, 'member_id' => \yii::$app->user->id]);
//                    if ($model) {
//                        //如果数据表已经有这个商品,就合并cookie中的数量
//                        $model->amount += $amount;
//                    } else {
//                        //如果数据表没有这个商品,就添加这个商品到购物车表
//                        $new = new Cart();
//                        $new->member_id = \yii::$app->user->id;
//                        $new->goods_id = $key;
//                        $new->amount = $amount;
//                        $new->save(false);
//                    }
//                    \yii::$app->response->cookies->remove('cart');
//                }
//            }
//            return $this->redirect(['user/index']);
//        } else {
//            return $this->render('login', ['model' => $model]);
//        }
//    }



    //添加
    //地址
    public function actionAddress(){
        $model = new Address();
        //var_dump($_POST);exit;
        $models = Address::find()->all();

        if($model->load(\Yii::$app->request->post()) && $model->validate()){


            $model->save();
            return $this->redirect(['user/address']);
        }
        //var_dump($model,$model->getErrors());exit;
        return $this->render('address',['model'=>$model,'models'=>$models,]);
    }

    //修改收获地址
    public function actionEditAddress($id){
        $model = Address::findOne(['id'=>$id]);
        $models = Address::find()->all();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){

            $model->save();
            return $this->redirect(['user/address']);
        }
        return $this->render('address',['model'=>$model,'models'=>$models,]);

    }

    //删除收货地址
    public function actionDeleteAddress($id){
        $model = Address::findOne(['id'=>$id]);
        $model->delete();
        return $this->redirect(['user/address']);
    }
    //收获地址
    public function actionIndexAddress()
    {
        // $model = new Address();

        return $this->render('address');
    }
    //发送短信进行验证
    public function actionSendsms($telnum)
    {
        //$tel = \Yii::$app->request->post('tel');
        if(!preg_match('/^1[34578]\d{9}$/',$telnum)){
            echo '电话号码不正确';
            exit;
        }
        $code = rand(1000,9999);
        $res = \Yii::$app->sms->setPhoneNumbers($telnum)->setTemplateParam(['code'=>$code])->send();

        \Yii::$app->session->set('code_'.$telnum ,$code);
        return Json::encode($res);
    }


    //测试发送短信功能
//    public function actionTestSms()
//    {
//        $code = rand(1000,9999);
//        $tel = '15659575112';
//        $res = \Yii::$app->sms->setPhoneNumbers($tel)->setTemplateParam(['code'=>$code])->send();
//        //将短信验证码保存redis（session，mysql）
//        \Yii::$app->session->set('code_'.$tel,$code);
//        //验证
//        $code2 = \Yii::$app->session->get('code_'.$tel);
//        if($code == $code2){
//        }
//        //\Yii::$app->session->set('tel',$tel);
//        var_dump($res);
//
//    }
    /*//添加购物车成功页面
    public function actionAddToCart($goods_id,$amount){
            //商品id 数量
        $cart=[$goods_id=>$amount];
        $cookies=\Yii::$app->response->cookies;
        $cookie=new Cookie([
            'name'=>'cart',
            'value'=>serialize($cart),
            'expire'=>'',
        ]);
        $cookies->add($cookie);
        var_dump($cookies->get('cart'));
    }
    //购物车页面
    public function actionCart(){
                $cookies=\Yii::$app->request->cookies;
                var_dump($cookies->get('cart'));
    }*/
    //添加到购物车
    public function actionAddCart($goods_id,$amount){
        //用户没有登录的情况
        if(\Yii::$app->user->isGuest){
            //商品id  商品数量

            //没有登录就存放在cookie中
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [$goods_id=>$amount];
            }else{
                $carts = unserialize($cart->value);
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
            //已经登录
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
    public function actionCart(){
        //判断是否登陆
        if(\Yii::$app->user->isGuest){
            //没登陆得到cookie数据
            $cookies=\Yii::$app->request->cookies;
            $cart=$cookies->get('cart');
            //var_dump($cart);exit;
            if($cart==null){
                $carts=[];
            }else{
                $carts=unserialize($cart->value);

            }
            //var_dump($carts);exit;
            //获取商品
            $models=Goods::find()->andwhere(['in','id',array_keys($carts)])->all();
            //var_dump($models);exit;
        }
        //登陆下
        else{
            $member_id=\Yii::$app->user->getId();
            $members=Cart::find()->where(['member_id'=>$member_id])->select('goods_id,amount')->asArray()->all();
            $models=[]; //用来存购物车的商品
            $carts=''; //用来存存购物商品个数
            foreach ($members as $member){
                $models[]=Goods::findOne(['id'=>$member['goods_id']]); //得到有哪些商品
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

        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [$goods_id=>$amount];
            }else{
                $carts = unserialize($cart->value);//[1=>99，2=》1]
                if(isset($carts[$goods_id])){
                    //购物车中已经有该商品，更新数量
                    $carts[$goods_id] = $amount;
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
 e
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
