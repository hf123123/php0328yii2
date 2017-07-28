<?php
namespace backend\models;

use yii\base\Model;

class RoleForm extends Model
{
    public $name;
    public $description;
    public $permissions=[];

    public function rules()
    {
        return [
            [['name','description'],'required'],
            ['permissions','safe'],
            //角色名不能重复
            //['name']
        ];
    }
    public function attributeLabels()
    {
        return [
           'name'=>'名称',
            'description'=>'描述',
            'permissions'=>'权限'
        ];
    }
    public function validateName()
    {
        $authManage = \Yii::$app->authManager;
        if($authManage->getRole($this->name)){
            $this->addError('name','权限已存在');
        }

    }
}