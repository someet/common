<?php

use yii\db\Schema;
use yii\db\Migration;

class m160128_092249_add_is_feedback_on_answer_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
            ALTER TABLE answer ADD COLUMN is_feedback tinyint(3) NOT NULL DEFAULT 0 COMMENT '0 未反馈  1 已反馈' AFTER is_send;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('answer', 'is_feedback');
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
