<?php

use yii\db\Schema;
use yii\db\Migration;

class m151019_025118_add_send_at_on_answer_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `answer`
ADD COLUMN `send_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '消息发送时间' AFTER `is_send`;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('answer', 'send_at');

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
