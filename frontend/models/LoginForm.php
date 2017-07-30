<?php
namespace frontend\models;
use yii\base\Model;
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
            'remember'=>'保存登录信息',
        ];
    }
    public function login()
    {
        $member = Member::findOne(['username'=>$this->username]);
        if($member){
            if(\Yii::$app->security->validatePassword($this->password,$member->password_hash)){
                \Yii::$app->user->login($member,$this->remember?7*24*3600:0);
                //更新最后登录时间和最后登录ip
                //ip2long() 将ip地址转换成整数    long2ip() 将整数转换成ip地址
                Member::updateAll(['last_login_time'=>time(),'last_login_ip'=>ip2long(\Yii::$app->request->userIP)],['id'=>$member->id]);
                return true;
            }else{
                $this->addError('password_hash','密码错误');
            }
        }else{
            $this->addError('username','用户不存在');
        }
        return false;
    }
}