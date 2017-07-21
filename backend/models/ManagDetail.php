<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "manag_detail".
 *
 * @property integer $article_id
 * @property string $content
 */
class ManagDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manag_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'article_id' => 'Article ID',
            'content' => '简介',
        ];
    }


}
