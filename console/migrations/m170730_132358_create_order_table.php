<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order`.
 */
class m170730_132358_create_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('order', [
            'id' => $this->primaryKey(),
//            id	primaryKey
//member_id	int	用户id
            'member_id'=>$this->integer(),
//name	varchar(50)	收货人
            'name'=>$this->string(50),
//province	varchar(20)	省
            'province'=>$this->string(20),
//city	varchar(20)	市
            'city'=>$this->string(20),
//area	varchar(20)	县
            'area'=>$this->string(20),
//address	varchar(255)	详细地址
            'address'=>$this->string(255),
//tel	char(11)	电话号码
            'tel'=>$this->char(11),
//delivery_id	int	配送方式id
            'delivery_id'=>$this->integer(11),
//delivery_name	varchar	配送方式名称
            'delivery_name'=>$this->string(20),
//delivery_price	float	配送方式价格
            'delivery_price'=>$this->decimal(10,2),
//payment_id	int	支付方式id
            'payment_id'=>$this->integer(11),
//payment_name	varchar	支付方式名称
            'payment_name'=>$this->string(20),
//total	decimal	订单金额
            'total'=>$this->decimal(10,2),
//status	int	订单状态（0已取消1待付款2待发货3待收货4完成）
            'status'=>$this->integer(),
//trade_no	varchar	第三方支付交易号
            'trade_no'=>$this->string(100),
//create_time	int	创建时间
            'create_time'=>$this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('order');
    }
}
