<?php

namespace backend\controllers;

use backend\models\GoodsCategory;

class GoodsCategoryController extends \yii\web\Controller
{
    //显示
    public function actionIndex()
    {
        $models=GoodsCategory::find()->all();
        return $this->render('index',['models'=>$models]);
    }
    //删除商品分类
 /*public function actionDel($id)
 {
     $model = GoodsCategory::findOne(['id' => $id]);
     if ($model == null) {
         throw new NotFoundHttpException('商品分类不存在');
     }
     if (!$model->isLeaf()) {//判断是否是叶子节点，非叶子节点说明有子分类
         throw new ForbiddenHttpException('该分类下有子分类，无法删除');
     }
     $model->delete();
     \Yii::$app->session->setFlash('success', '删除成功');
     return $this->redirect(['index']);
 }*/
    public function actionDel($id){
        $model=GoodsCategory::findOne(['id'=>$id]);

//        var_dump($model->lft);exit;
        $lft=$model->lft;
        $rgt=$model->rgt;

        if($lft + 1 != $rgt){


            \Yii::$app->session->setFlash('danger','该分类下含有子目录，不能被删除');
            return $this->redirect(['index']);
        }else{
            $model->delete();
            \Yii::$app->session->setFlash('success','删除成功');
            return $this->redirect(['index']);
        }}

  /* /* //添加
    public function actionAdd2(){
        $model=new GoodsCategory();
        if($model->load(\Yii::$app->request->post())&& $model->validate()) {

            //判断是否是添加一级分类
            if($model->parent_id){
                //非一级分类
                $category = GoodsCategory::findOne(['id'=>$model->parent_id]);
                if($category){
                    $model->prependTo($category);
                }else{
                    throw new HttpException(404,'上级分类不存在');
                }
            }else{
                //一级分类
                $model->makeRoot();
            }
            \Yii::$app->session->setFlash('success','分类添加成功');
            return $this->redirect(['index']);
        }
        return $this->render('add',['model'=>$model]);
    }

    //测试
    public function actionZtree(){
        return $this->renderPartial('ztree');
    }*/

    //添加
    public function actionAdd(){
        $model=new GoodsCategory();
        if($model->load(\Yii::$app->request->post())&& $model->validate()) {

            //判断是否是添加一级分类
            if($model->parent_id){
                //非一级分类
                $category = GoodsCategory::findOne(['id'=>$model->parent_id]);
                if($category){
                    $model->prependTo($category);
                }else{
                    throw new HttpException(404,'上级分类不存在');
                }
            }else{
                //一级分类
                $model->makeRoot();
            }
            \Yii::$app->session->setFlash('success','分类添加成功');
            return $this->redirect(['index']);
        }
        //获取所有分类数据
        $categories=GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }
    //测试嵌套集合插件的用法
    public function actionTest()
    {
        //创建一个根节点
        /*$category = new GoodsCategory();
        $category->name = '家用电器';
        $category->makeRoot();*/
        //创建子节点
        /*$category2 = new GoodsCategory();
        $category2->name = '小家电';
        $category = GoodsCategory::findOne(['id'=>1]);
        $category2->parent_id = $category->id;
        $category2->prependTo($category);*/
        //删除节点
        //$cate = GoodsCategory::findOne(['id'=>6])->delete();
        echo '操作完成';
    }
    //测试ztree
    public function actionZtree()
    {
        //$this->layout = false;
        //不加载布局文件
        return $this->renderPartial('ztree');
    }


    //修改
    public function actionEdit($id){
        $model = GoodsCategory::findOne($id);
        if($model->load(\Yii::$app->request->post())&& $model->validate()) {

            //判断是否是添加一级分类
            if($model->parent_id){
                //非一级分类
                $category = GoodsCategory::findOne(['id'=>$model->parent_id]);
                if($category){
                    $model->prependTo($category);
                }else{
                    throw new HttpException(404,'上级分类不存在');
                }
            }else{
                //一级分类
                $model->makeRoot();
            }
            \Yii::$app->session->setFlash('success','分类添加成功');
            return $this->redirect(['index']);
        }
        //获取所有分类数据
        $categories=GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }
}
