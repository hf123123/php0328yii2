<?php

namespace frontend\controllers;

use backend\models\User;
use frontend\models\Address;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\helpers\Json;

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
        return $this->render('register',['model'=>$model]);
    }
    //AJAX表单验证
    public function actionAjaxRegister()
    {
        $model = new Member();
        $model->scenario = Member::SCENARIO_REGISTER;
        //var_dump($model);exit;
        if($model->load(\Yii::$app->request->post()) && $model->validate() ){
            $model->save(false);
            //var_dump($model->getErrors());//    ['username'=>]
            //保存数据，提示保存成功

            return Json::encode(['status'=>true,'msg'=>'注册成功']);
        }else{
            //验证失败，提示错误信息

            return Json::encode(['status'=>false,'msg'=>$model->getErrors()]);
        }
    }


    //登录
    //用户登录
    public function actionLogin(){
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
    }


    //用户地址
    public function actionAddress()
    {

        $model = new Address();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
        }
        return $this->render('address',['model'=>$model]);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }


}
