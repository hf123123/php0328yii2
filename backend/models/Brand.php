<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "Brand".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property string $logo
 * @property integer $sort
 * @property integer $status
 */
class Brand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $imgFile;

    public static function getstatusOptions($hidden_del=true){


        $options=[
        -1=>'删除',0=>'隐藏',1=>'正常'
    ];
    if($hidden_del){
        unset($options['-1']);

    }
        return $options;

    }


    public static function tableName()
    {
        return 'Brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['intro'], 'string'],
            [['sort', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
            ['imgFile','file','extensions'=>['jpg','png','gif']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'intro' => '简介',
            'imgFile' => 'LOGO图片',
            'sort' => '排序',
            'status' => '状态',
        ];
    }

    public static function getstatusOption($i){

        $model=[
            -1=>'删除',0=>'隐藏',1=>'正常'
        ];
        return $model[$i];
    }
}
