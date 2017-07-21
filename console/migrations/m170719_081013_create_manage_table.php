<?php

use yii\db\Migration;

/**
 * Handles the creation of table `manage`.
 */
class m170719_081013_create_manage_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('manage', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->comment('名称'),
            'intro' => $this->text()->comment('简介'),
            'article_category_id' => $this->integer()->comment('文章分类ID'),
            'sort'=>$this->integer()->comment('排序'),
            'status'=>$this->smallInteger(2)->comment('状态'),
            'create_time'=>$this->integer()->comment('创建时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('manage');
    }
}
