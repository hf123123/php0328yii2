<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170801_085559_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'member_id' => $this->integer()->notNull()->comment('所属用户'),
            'name'=>$this->string(100)->notNull()->comment('收货人'),
            'province'=>$this->string(100)->notNull()->comment('省'),
            'city'=>$this->string(100)->notNull()->comment('市'),
            'area'=>$this->string(100)->notNull()->comment('县'),
            'detail'=>$this->string(255)->notNull()->comment('详细地址'),
            'tel'=>$this->char(11)->notNull()->comment('手机号码'),
            'is_default'=>$this->smallInteger(1)->comment('是否默认地址'),
        ]);
    }


    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
