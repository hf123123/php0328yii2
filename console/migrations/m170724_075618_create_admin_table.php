<?php

use yii\db\Migration;

/**
 * Handles the creation of table `admin`.
 */
class m170724_075618_create_admin_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('admin', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique()->comment('用户名'),
            'auth_key' => $this->string(32)->notNull()->comment('权限'),
            'password_hash' => $this->string()->notNull()->comment('密码'),
            'password_reset_token' => $this->string()->unique()->comment('重置密码'),
            'email' => $this->string()->notNull()->unique()->comment('邮箱'),
            'status' => $this->smallInteger()->notNull()->defaultValue(10)->comment(''),
            'created_at' => $this->integer()->notNull()->comment('创建时间'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
            'last_login_time' => $this->integer()->notNull()->comment('最后登录时间'),
            'last_login_ip' => $this->char(15)->notNull()->comment('最后登录IP'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('admin');
    }
}
