<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/3
 * Time: 11:18
 */

namespace backend\controllers;


use backend\models\Admin;
use backend\models\ChPwForm;
use backend\models\LoginForm;
use backend\models\PasswordForm;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class AdminController extends Controller
{
    //添加管理员
    public function actionAdd(){
        //实例化模型
        $model=new Admin();
        //实例化响应
        $request=new Request();
        if($request->isPost){
            //加载请求的内容
            $model->load($request->post());
            if($model->validate()){
                    $model->password_hash=\yii::$app->security->generatePasswordHash($model->password_hash);
//                    var_dump($model->password);exit;
                    $model->save();

                    return $this->redirect(['admin/index']);

                    //默认情况下 保存是会调用validate方法  有验证码是，需要关闭验证
                }else{
                    //验证失败 打印错误信息
                    var_dump($model->getErrors());exit;
                }
            }


        //显示注册页面
        return $this->render('add',['model'=>$model]);
    }

    //修改
    public function actionEdit($id){
        //实例化模型
        $model = Admin::findOne(['id'=>$id]);
        //实例化响应
        $request=new Request();
        if($request->isPost){
            //加载请求的内容
            $model->load($request->post());
            if($model->validate()){
                $model->password_hash=\yii::$app->security->generatePasswordHash($model->password_hash);
//                    var_dump($model->password);exit;
                $model->save();

                return $this->redirect(['admin/index']);

                //默认情况下 保存是会调用validate方法  有验证码是，需要关闭验证
            }else{
                //验证失败 打印错误信息
                var_dump($model->getErrors());exit;
            }
        }


        //显示注册页面
        return $this->render('add',['model'=>$model]);
    }


//修改登陆者的密码
    public function actionChpw(){
        //判断是否是游客 有没有权限修改密码
        if( \Yii::$app->user->isGuest){
            \Yii::$app->session->setFlash('danger','对不起，您还未登录');
            return $this->redirect(['admin/login']);
        }
        $model=new ChPwForm();
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate() && $model->ChPw()){

                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['admin/index']);
            }
        }

        return $this->render('chpw',['model'=>$model]);


    }

    //删除
    public function actionDelete($id){
        $model=Admin::findOne($id);
        $model->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['admin/index']);
    }
    //登录
    public function actionLogin()
    {
        $model = new LoginForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->login()){
                \Yii::$app->session->setFlash('success','登录成功');
                return $this->redirect(['admin/index']);
            }
        }
        return $this->render('login',['model'=>$model]);
    }



    //显示
    public function actionIndex(){
       $models=Admin::find()->all();
       return $this ->render('index',['models'=>$models]);
    }






}