<?php
namespace backend\models;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
class RbacForm extends Model
{
    public $roles = [];
    public function rules()
    {
        return [
            ['roles','safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'roles'=>'角色',
        ];
    }
    //专业用于获取所有角色的方法
    public static function getRoleOptions()
    {
        return ArrayHelper::map(\Yii::$app->authManager->getRoles(),'name','description');
    }
    //根据表单传递的数据，分配角色给用户
    public function assignRole($id)
    {
        $auth = \Yii::$app->authManager;
        if(Admin::findOne($id)){
            //首先移除该用户的所有角色
            $auth->revokeAll($id);
            //根据数据分配角色给用户
            $roles = $this->roles;
            if($roles){
                foreach ($roles as $roleName){
                    $auth->assign($auth->getRole($roleName),$id);
                }
            }
            return true;
        }else{
            throw new NotFoundHttpException('管理员不存在');
        }
    }
    //给模型赋值
    public function loadData($id)
    {
        $auth = \Yii::$app->authManager;
        $roles = $auth->getRolesByUser($id);
        foreach ($roles as $role){
            $this->roles[] = $role->name;
        }
    }
}