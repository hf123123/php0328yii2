<?php
namespace backend\models;

use yii\base\Model;

class  ChPwForm extends Model{

    public  $newpassword;
    public  $renewpassword;
    public  $password_hash;

    public function rules()
    {
        return [
            [['newpassword','password_hash','renewpassword'],'required'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'newpassword'=>'新密码',
            'password_hash'=>'旧密码',
            'renewpassword'=>'确认密码'
        ];
    }

    public function ChPw(){
//        var_dump(\Yii::$app->user->identity);exit;
        $username=\Yii::$app->user->identity->username;
//        var_dump(\Yii::$app->user->identity->username);exit;
        //1.1 通过用户名查找用户
        $admin =Admin::findOne(['username'=>$username]);
//        var_dump($name);exit;
        if($admin){
            //用户存在 对比用户密码
            if(\Yii::$app->security->validatePassword($this->password_hash,$admin->password_hash)){
                //判断新密码和确认密码是否一致
                if($this->newpassword == $this->renewpassword) {
//                    var_dump($this->newpassword == $this->renewpassword);exit;
                    //判断新密码和旧密码是否一致
                    if(!\Yii::$app->security->validatePassword($this->newpassword,$admin->password_hash)) {
                        //保存新密码
                        $admin->password_hash = \Yii::$app->security->generatePasswordHash($this->newpassword);
                        $admin->save();

                        \Yii::$app->user->login($admin);

                        return true;
                    }else{
                        $this->addError('password_hash','新旧密码不能一致');
                    }
                }else{
                    $this->addError('password_hash','两次密码输入不一致');

                }

            }else{
                $this->addError('password_hash','密码错误');
            }
        }else{
            //用户不存在,提示 用户不存在 错误信息
            $this->addError('username','用户名不存在');
        }
        return false;
    }
}