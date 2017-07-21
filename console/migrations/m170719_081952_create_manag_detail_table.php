<?php

use yii\db\Migration;

/**
 * Handles the creation of table `manag_detail`.
 */
class m170719_081952_create_manag_detail_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('manag_detail', [
            'article_id' => $this->primaryKey(),
            'content' => $this->text()->comment('简介'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('manag_detail');
    }
}
