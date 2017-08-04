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
use backend\models\RbacForm;
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
            $authManage = \Yii::$app->authManager;
            $model->load($request->post());
            if($model->validate()){
                    $model->password_hash=\yii::$app->security->generatePasswordHash($model->password_hash);
//                    var_dump($model->password);exit;
                    $model->save();
                if(is_array($model->roles)){
                    $authManage->revokeAll($model->id);
                    foreach ($model->roles as $roleName){
                        $role=$authManage->getRole($roleName);
                        if($role)$authManage->assign($role,$model->id);
                    }
                }
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
            $authManage = \Yii::$app->authManager;
            $model->load($request->post());
            if($model->validate()){
                $model->password_hash=\yii::$app->security->generatePasswordHash($model->password_hash);
//                    var_dump($model->password);exit;
                $model->save();
                if(is_array($model->roles)){
                    $authManage->revokeAll($model->id);
                    foreach ($model->roles as $roleName){
                        $role=$authManage->getRole($roleName);
                        if($role)$authManage->assign($role,$model->id);
                    }
                }
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
//用户登录
    public function actionLogin(){
        if(!\Yii::$app->user->isGuest){
            return $this->redirect(['admin/index']);
        }
        $model = new LoginForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->login()){
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

//权限设置,与角色关联
    public function actionRbac($id)
    {
        $model = new RbacForm();
        //找出管理员的名称，在视图里直观的显示
        $name = Admin::findOne($id)->username;
        //给模型赋值，便于分配角色
        $model->loadData($id);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->assignRole($id)){
                \Yii::$app->session->setFlash('success','权限设置成功');
                return $this->redirect('index');
            }
        }
        return $this->render('rbac',['model'=>$model,'name'=>$name]);
    }





}