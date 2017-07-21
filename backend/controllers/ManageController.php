<?php

namespace backend\controllers;
use backend\models\ManagDetail;
use backend\models\Manage;
use yii\web\Request;

class ManageController extends \yii\web\Controller
{
    //显示
    public function actionIndex()
    {
        $models=Manage::find()->all();
        return $this->render('index',['models'=>$models]);
    }

    //添加
    public function actionAdd(){
        //实例化表单模型
        $model=new Manage();
        $model2=new ManagDetail();
        //接收表单数据保存到数据库
        $request=new Request();
        if($request->isPost){
            //加载表单数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                //验证通过,保存到数据表
               $model->create_time=time();
                $model->save();
                $model2->load($request->post());
                $model2->article_id=$model->id;
                if($model2->validate() && $model2->save()){
                    return $this->redirect('index');
                }
                //跳转到列表页
                return $this->redirect(['manage/index']);
            }else{
                //验证不通过，打印错误信息
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'model2'=>$model2]);

    }
    //修改
    public function actionEdit($id)
    {
        //实例化表单模型
        $model =Manage::findOne($id);
        //接收表单数据并保存到数据表
        $request = new Request();
        if($request->isPost){
            //加载表单数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                //验证通过,保存到数据表
                $model->create_time=time();
                $model->save();
                //跳转到列表页
                return $this->redirect(['manage/index']);
            }else{
                //验证不通过，打印错误信息
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model]);

    }
    //删除
    public function actionDelete($id){
        $model=Manage::deleteAll(['id'=>$id]);
        return $this->redirect(['manage/index']);
    }

}
