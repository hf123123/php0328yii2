<?php

namespace backend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use backend\models\GoodsSearchForm;
use flyok666\uploadifive\UploadAction;
use yii\data\Pagination;
use flyok666\qiniu\Qiniu;
use yii\web\NotFoundHttpException;

class GoodsController extends \yii\web\Controller
{
    //显示
    public function actionIndex()
    {
        $model = new GoodsSearchForm();

        $query = Goods::find();

        //接收表单提交的查询参数
        $model->search($query);


        //商品名称含有"耳机"的  name like "%耳机%"
        //$query = Goods::find()->where(['like','name','耳机']);
        $pager = new Pagination([
            'totalCount'=>$query->count(),
            'pageSize'=>5
        ]);

        $models = $query->limit($pager->limit)->offset($pager->offset)->all();


        return $this->render('index',['models'=>$models,'pager'=>$pager,'model'=>$model]);
    }
    //添加
    public function actionAdd()
    {
        $model = new Goods();
        $model2 = new GoodsIntro();
        if($model->load(\Yii::$app->request->post()) && $model2->load(\Yii::$app->request->post())){

            if($model->validate() && $model2->validate()){
                ///自动生成sn
                $day = date('Y-m-d');
                $goodsCount = GoodsDayCount::findOne(['day'=>$day]);
                if($goodsCount==null){
                    $goodsCount = new GoodsDayCount();
                    $goodsCount->day = $day;
                    $goodsCount->count = 0;
                    $goodsCount->save();
                }
                $model->sn = date('Ymd').sprintf("%04d",$goodsCount->count+1);

                $model->save();
                $model2->goods_id = $model->id;
                $model2->save();
                $goodsCount->count++;
                $goodsCount->save();

                \Yii::$app->session->setFlash('success','商品添加成功,请添加商品相册');
                return $this->redirect(['goods/index','id'=>$model->id]);
            }
        }
        //获取所有分类数据
        $categories=GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->render('add',['model'=>$model,'model2'=>$model2,'categories'=>$categories]);
    }

    //修改
    public function actionEdit($id){

        $model = Goods::findOne($id);
        if($model->load(\Yii::$app->request->post())&& $model->validate()) {

            $model->create_time=time();
            $model ->save();
            \Yii::$app->session->setFlash('success','修改成功');
            return $this->redirect(['index']);


        }
        //获取所有分类数据
        $categories=GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }



    //删除

    public function actionDelete($id){
        $model=Goods::findOne($id);
        $model->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['goods/index']);
    }



    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,//如果文件已存在，是否覆盖
                /* 'format' => function (UploadAction $action) {
                     $fileext = $action->uploadfile->getExtension();
                     $filename = sha1_file($action->uploadfile->tempName);
                     return "{$filename}.{$fileext}";
                 },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },//文件的保存方式
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    $action->output['fileUrl'] = $action->getWebUrl();//输出文件的相对路径

                },
            ],
        ];
    }


    public function actionss()
    {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ]
        ];
    }

    public function actionsss() {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    "imageUrlPrefix"  => "",//图片访问路径前缀
                    "imagePathFormat" => "/upload/{yyyy}{mm}{dd}/{time}{rand:6}" ,//上传保存路径
                    "imageRoot" => \Yii::getAlias("@webroot"),
                ],
            ],

            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload/logo',
                'baseUrl' => '@web/upload/logo',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                /*'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filename = sha1_file($action->uploadfile->tempName);
                    return "{$filename}.{$fileext}";
                },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "/{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png','gif'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    //图片上传成功的同时，将图片和商品关联起来
                    $model = new GoodsGallery();
                    $model->goods_id = \Yii::$app->request->post('goods_id');
                    $model->path = $action->getWebUrl();
                    $model->save();
                    $action->output['fileUrl'] = $model->path;
                    $action->output['id'] = $model->id;

                },
            ],
        ];
    }
    public function actionView($id)
    {
        $model = Goods::findOne(['id'=>$id]);
        if($model==null){
            throw new NotFoundHttpException('商品不存在');
        }
        return $this->render('view',['model'=>$model]);

    }

}
