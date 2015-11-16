<?php

use yii\db\Schema;
use yii\db\Migration;

class m151116_095735_add_last_login_at extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `user`
ADD COLUMN `last_login_at` INT(11) NULL DEFAULT NULL COMMENT '最后一次登录时间' AFTER `wechat_id`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropColumn('user', 'last_login_at');
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
