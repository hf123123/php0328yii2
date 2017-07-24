<?php

namespace backend\controllers;

use backend\models\GoodsIntro;

class GoodsIntroController extends \yii\web\Controller
{

    //显示
    public function actionLook()
    {
        $models=GoodsIntro::find()->all();

        return $this->render('index',['models'=>$models]);
    }

}
