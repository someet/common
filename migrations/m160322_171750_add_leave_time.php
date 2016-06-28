<?php

use yii\db\Schema;
use yii\db\Migration;

class m160322_171750_add_leave_time extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `answer`
ADD COLUMN `leave_time` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '请假时间' AFTER `cancel_apply_time`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropColumn('answer', 'leave_time');
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
