<?php
namespace backend\models;

use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password_hash;
    //记住我
    public $rememberMe;

    public function rules()
    {
        return [
            [['username','password_hash'],'required'],
            ['rememberMe','boolean']
        ];
    }

    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password_hash'=>'密码',
            'rememberMe'=>'记住我'
        ];
    }

    //用户登录
    public function login(){
        //1 根据用户名查找用户
        $admin = Admin::findOne(['username'=>$this->username]);

        if($admin){
            //2 验证密码

            if(\Yii::$app->security->validatePassword($this->password_hash,$admin->password_hash)){
                //3 登录
                $admin->last_login_ip=\Yii::$app->request->userIP;
                $admin->last_login_time=time();

                $admin->save();

                //自动登录

                $duration = $this->rememberMe?7*24*3600:0;

                \Yii::$app->user->login($admin,$duration);


                return true;
            }else{
                $this->addError('password_hash','密码不正确');
            }
        }else{
            $this->addError('username','用户名不存在');
        }

        return false;
    }
}