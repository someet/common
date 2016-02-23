<?php

use yii\db\Schema;
use yii\db\Migration;

class m151116_050215_add_wechat_id extends Migration
{
    public function up()
    {
        $sql = <<<SQL
            ALTER TABLE `user`
            ADD COLUMN `wechat_id` VARCHAR(45) NULL DEFAULT NULL COMMENT '微信ID, 用于添加好友' AFTER `status`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropColumn('user', 'wechat_id');
        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
