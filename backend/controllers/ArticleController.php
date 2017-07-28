<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\Manage;
use yii\web\Request;
use yii\web\UploadedFile;

class ArticleController extends \yii\web\Controller
{
    //添加
    public function actionAdd()
    {
        //实例化表单模型
        $model = new Article();
        //接收表单数据并保存到数据表
        $request = new Request();
        if($request->isPost){
            //加载表单数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                //验证通过,保存到数据表
                $model->save();
                //跳转到列表页
                return $this->redirect(['article/index']);
            }else{
                //验证不通过，打印错误信息
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model/*,'items'=>$items*/]);

    }


    //显示
    public function actionIndex()
    {
        $models=Article::find()->all();
        return $this->render('index',['models'=>$models]);
    }

    //修改
    public function actionEdit($id)
    {
        //实例化表单模型
        $model =Article::findOne($id);
        //接收表单数据并保存到数据表
        $request = new Request();
        if($request->isPost){
            //加载表单数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                //验证通过,保存到数据表
                $model->save();
                //跳转到列表页
                return $this->redirect(['article/index']);
            }else{
                //验证不通过，打印错误信息
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model/*,'items'=>$items*/]);

    }




    //删除
    public function actionDelete($id){
        $model=Article::findOne($id);
        $model->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['article/index']);
    }
}
