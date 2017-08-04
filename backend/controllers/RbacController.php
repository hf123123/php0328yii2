<?php

namespace backend\controllers;

use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class RbacController extends \yii\web\Controller
{
    //添加权限
    public function actionAddPermission()
    {
        $model = new PermissionForm();
        $model->scenario = PermissionForm::SCENARIO_ADD;
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $authManager = \Yii::$app->authManager;
            //创建权限
            $permission = $authManager->createPermission($model->name);
            $permission->description = $model->description;
            //保存到数据表
            $authManager->add($permission);

            \Yii::$app->session->setFlash('success','权限添加成功');
            return $this->redirect(['permission-index']);
        }
        return $this->render('add-permission',['model'=>$model]);
    }
    //修改权限
    public function actionEditPermission($name)
    {
        //检查权限是否存在
        $authManage = \Yii::$app->authManager;
        $permission = $authManage->getPermission($name);
        if($permission == null){
            throw new NotFoundHttpException('权限不存在');
        }
        $model = new PermissionForm();
        if(\Yii::$app->request->isPost){
            if($model->load(\Yii::$app->request->post()) && $model->validate()){
                //将表单数据赋值给权限
                $permission->name = $model->name;
                $permission->description = $model->description;
                //更新权限
                $authManage->update($name,$permission);
                \Yii::$app->session->setFlash('success','权限修改成功');
                return $this->redirect(['permission-index']);
            }
        }else{
            //回显权限数据到表单
            $model->name = $permission->name;
            $model->description = $permission->description;
        }
        return $this->render('add-permission',['model'=>$model]);
    }
    //权限列表
    public function actionPermissionIndex()
    {
        //获取所有权限
        $authManager = \Yii::$app->authManager;
        $models = $authManager->getPermissions();
        return $this->render('permission-index',['models'=>$models]);
    }
    //删除权限
    public function actionDelPermission($name){
        $permission = \Yii::$app->authManager->getPermission($name);
        \Yii::$app->authManager->remove($permission);
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['permission-index']);
    }


/*《《《《《《《《《《《《《《《《《《《分割》》》》》》》》》》》》》》》》》》》》》》》》》》》》》》》*/


    //角色列表
    public function actionRoleIndex()
    {
        //获取所有权限
        $authManager = \Yii::$app->authManager;
        $models = $authManager->getRoles();
        return $this->render('role-index',['models'=>$models]);
    }

    //添加角色
    public function actionAddRole()
    {
        $model = new RoleForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //创建和保存角色
            $authManager = \Yii::$app->authManager;
            $role = $authManager->createRole($model->name);
            $role->description = $model->description;
            $authManager->add($role);
            //给角色赋予权限
            //var_dump($model);exit;
            if(is_array($model->permissions)){
                foreach ($model->permissions as $permissionName){
                    $permission = $authManager->getPermission($permissionName);
                    if($permission) $authManager->addChild($role,$permission);
                }
            }
            \Yii::$app->session->setFlash('success','角色添加成功');
            return $this->redirect(['role-index']);

        }

        return $this->render('add-role',['model'=>$model]);
    }

    //修改角色
    //修改角色
    public function actionEditRole($name)
    {
        $authManager = \Yii::$app->authManager;
        $role = $authManager->getRole($name);
        $model = new RoleForm();
        //表单权限多选回显
        //获取角色的权限
        $permissions = $authManager->getPermissionsByRole($name);
        $model->name = $role->name;
        $model->description = $role->description;
        $model->permissions = ArrayHelper::map($permissions,'name','name');
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $role->description = $model->description;
            $role->name = $model->name;
            $authManager->update($name,$role);
            //给角色赋予权限
            $authManager->removeChildren($role);
            if(is_array($model->permissions)){
                foreach ($model->permissions as $permissionName){
                    $permission = $authManager->getPermission($permissionName);
                    if($permission) $authManager->addChild($role,$permission);
                }
            }
            \Yii::$app->session->setFlash('success','角色修改成功');
            return $this->redirect(['role-index']);
        }
        return $this->render('add-role',['model'=>$model]);
    }



//删除权限
    public function actionDelRole($name){
        $permission = \Yii::$app->authManager->getRole($name);
        \Yii::$app->authManager->remove($permission);
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['role-index']);
    }





}
