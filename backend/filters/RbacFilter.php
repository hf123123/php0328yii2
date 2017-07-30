<?php
/**
 * Created by PhpStorm.
 * User: Shinelon
 * Date: 2017/7/28
 * Time: 22:49
 */
namespace backend\filters;

use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class RbacFilter extends ActionFilter{
    //执行前
    public function beforeAction($action)
    {
        //验证用户没登录就到登录页面
        if (\Yii::$app->user->isGuest){
            return $action->controller->redirect(\Yii::$app->user->loginUrl);
        }
        if (!\Yii::$app->user->can($action->uniqueId)){
            throw new ForbiddenHttpException('权限不够');
        }
        return parent::beforeAction($action);
    }


}