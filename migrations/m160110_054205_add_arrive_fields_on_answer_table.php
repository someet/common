<?php

use yii\db\Schema;
use yii\db\Migration;

class m160110_054205_add_arrive_fields_on_answer_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `answer`
ADD COLUMN `arrive_status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '到达状态 0未到 1迟到 2准时' AFTER `join_noti_wechat_template_msg_id`,
ADD COLUMN `leave_status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 不请假 1 请假' AFTER `arrive_status`,
ADD COLUMN `leave_msg` VARCHAR(180) NULL DEFAULT '' COMMENT '请假理由' AFTER `leave_status`;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('answer', 'arrive_status');
        $this->dropColumn('answer', 'leave_status');
        $this->dropColumn('answer', 'leave_msg');
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
