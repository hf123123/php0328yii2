<?php
namespace backend\controllers;
use backend\models\Brand;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;
class BrandController extends \yii\web\Controller
{
    //添加品牌
    public function actionAdd()
    {
        $model = new Brand();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->save();
            \Yii::$app->session->setFlash('success','品牌添加成功');
            return $this->redirect(['brand/index']);
        }
        return $this->render('add',['model'=>$model]);
    }
    //修改品牌
    public function actionEdit($id){
        $model = Brand::findOne(['id'=>$id]);
        if($model==null){//如果品牌不存在，则显示404页面
            throw new NotFoundHttpException('品牌不存在');
        }
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->save();
            \Yii::$app->session->setFlash('success','品牌添加成功');
            return $this->redirect(['brand/index']);
        }
        return $this->render('add',['model'=>$model]);
    }
    //品牌列表
    public function actionIndex()
    {
        //只显示未删除的品牌，status != -1
        $query = Brand::find()->where(['!=','status','-1']);
        $pager = new Pagination([
            'totalCount'=>$query->count(),
            'defaultPageSize'=>10,
        ]);
        $models = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }
    //删除品牌
    public function actionDel($id){
        $model = Brand::findOne(['id'=>$id]);
        //逻辑删除，只修改状态，不真实删除数据
        if($model){
            $model->status = -1;
            $model->save();
        }
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
                    //$action->output['fileUrl'] = $action->getWebUrl();//输出文件的相对路径
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //将图片上传到七牛云
                    $qiniu = new Qiniu(\Yii::$app->params['qiniu']);
                    $qiniu->uploadFile(
                        $action->getSavePath(), $action->getWebUrl()
                    );
                    $url = $qiniu->getLink($action->getWebUrl());
                    $action->output['fileUrl']  = $url;
                },
            ],
        ];
    }
    //测试七牛云文件上传
    public function actionQiniu()
    {
        $config = [
            'accessKey'=>'HLYqMA59-eHKCuQVl8ZIgLJLldTqZOWasShXC8fl',
            'secretKey'=>'i87T1FCGqv0P5Kvt-OmkfQiP-H3sfGJRDrVDg-wv',
            'domain'=>'http://otece8y8n.bkt.clouddn.com/',
            'bucket'=>'yii2shop',
            'area'=>Qiniu::AREA_HUADONG
        ];
        $qiniu = new Qiniu($config);
        $key = 'upload/2e/79/2e795418fcb72341d801d1fa70ca6fabc33444cb.png';
        //将图片上传到七牛云
        $qiniu->uploadFile(
            \Yii::getAlias('@webroot').'/upload/2e/79/2e795418fcb72341d801d1fa70ca6fabc33444cb.png',
            $key);
        //获取该图片在七牛云的地址
        $url = $qiniu->getLink($key);
        var_dump($url);
    }
}