<?php

use yii\db\Schema;
use yii\db\Migration;

class m160711_054457_change_feedback_content_limit extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity_feedback`
CHANGE COLUMN `feedback` `feedback` VARCHAR(2000) NOT NULL COMMENT '反馈内容' ;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m160711_054457_change_feedback_content_limit cannot be reverted.\n";

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
