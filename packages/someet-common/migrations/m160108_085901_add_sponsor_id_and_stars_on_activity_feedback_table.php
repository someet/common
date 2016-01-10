<?php

use yii\db\Schema;
use yii\db\Migration;

class m160108_085901_add_sponsor_id_and_stars_on_activity_feedback_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        ALTER TABLE `activity_feedback` 
ADD COLUMN `sponsor_id` INT(11) NOT NULL DEFAULT 0 COMMENT '发起人id' AFTER `status`,
ADD COLUMN `sponsor_stars` TINYINT(3) NOT NULL DEFAULT 0 COMMENT '发起人评分' AFTER `sponsor_id`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m160108_085901_add_sponsor_id_and_stars_on_activity_feedback_table cannot be reverted.\n";

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
