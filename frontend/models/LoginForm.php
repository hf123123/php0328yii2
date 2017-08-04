<?php
namespace frontend\models;
use yii\base\Model;
use yii\web\IdentityInterface;

class LoginForm extends Model
{
    public $username;
    public $password_hash;
    public $code;
    public $remember;
    public function rules()
    {
        return [
            [['username','password_hash'],'required'],
            ['code','captcha'],
            ['remember','boolean']
        ];
    }
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password_hash'=>'密码',
            'code'=>'验证码',
            'remember'=>'保存登录信息'
        ];
    }
    //用户登录
    public function login(){
        //根据用户名查找用户
        $member = Member::findOne(['username'=>$this->username]);
        if($member){
            //验证密码
            if(\Yii::$app->security->validatePassword($this->password_hash,$member->password_hash)){
                //登录
                $member->last_login_ip=\Yii::$app->request->userIP;
                $member->last_login_time=time();
                $member->save();
                //自动登录
                $duration = $this->remember?7*24*3600:0;
                \Yii::$app->user->login($member,$duration);
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

