<?php

use yii\db\Schema;
use yii\db\Migration;

class m160322_164814_add_apply_status_and_time extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `answer`
ADD COLUMN `apply_status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '请假状态 0 正常使用 1 取消报名' AFTER `leave_msg`,
ADD COLUMN `cancel_apply_time` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '请假时间' AFTER `apply_status`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropColumn('answer', 'apply_status');
        $this->dropColumn('answer', 'cancel_apply_time');
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
