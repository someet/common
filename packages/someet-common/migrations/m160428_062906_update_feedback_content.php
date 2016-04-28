<?php

use yii\db\Schema;
use yii\db\Migration;

class m160428_062906_update_feedback_content extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE activity_feedback CHANGE COLUMN grade grade tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '活动评价等级 0  未设置  3差评 2 中评  1 好评';
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m160428_062906_update_feedback_content cannot be reverted.\n";

        return false;
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
