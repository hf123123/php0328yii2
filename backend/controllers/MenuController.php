<?php

namespace backend\controllers;

use backend\models\Menu;
use yii\web\Request;

class MenuController extends \yii\web\Controller
{
    //显示列表
    //菜单列表
    public function actionIndex()
    {
        $models = Menu::find()->where(['parent_id'=>0])->all();
        return $this->render('index',['models'=>$models]);
    }
    //添加
    public function actionAdd(){
       $model=new Menu();
  if($model->load(\Yii::$app->request->post())&& $model->validate()){

      $model->save();
      \Yii::$app->session->setFlash('success','添加成功');
      return $this->redirect(['menu/index']);
  }
       return $this->render('add',['model'=>$model]);
    }

    //修改菜单
    public function actionEdit($id)
    {
        $model = Menu::findOne(['id'=>$id]);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //预防出现三层菜单
            if($model->parent_id && !empty($model->children)){
                $model->addError('parent_id','必须是顶级菜单');
            }else{
                $model->save();
                return $this->redirect(['index']);
            }
            //\Yii::$app->session->setFlash('success','菜单添加成功');
        }
        return $this->render('add',['model'=>$model]);
    }

    //删除
    public function actionDel($id){
        $model=Menu::findOne($id);
        $model->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['menu/index']);
    }


}
