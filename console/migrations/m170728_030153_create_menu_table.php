<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m170728_030153_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->comment('名称'),
            'url' => $this->string()->comment('路由'),
            'parent_id' => $this->integer()->comment('上级分类'),
            'sort' => $this->integer()->comment('排序'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
