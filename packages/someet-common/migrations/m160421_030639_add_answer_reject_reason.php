<?php

use yii\db\Schema;
use yii\db\Migration;

class m160421_030639_add_answer_reject_reason extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `answer` ADD COLUMN `reject_reason` varchar(255) NOT NULL DEFAULT 0 COMMENT '拒绝原因';
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('reject_reason');
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
