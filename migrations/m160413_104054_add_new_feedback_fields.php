<?php

use yii\db\Schema;
use yii\db\Migration;

class m160413_104054_add_new_feedback_fields extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity_feedback`
ADD COLUMN `sponsor_start1` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '态度友好 0 1 2 3 4 5 ' AFTER `sponsor_stars`,
ADD COLUMN `sponsor_start2` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '准备充分 0 1 2 3 4 5' AFTER `sponsor_start1`,
ADD COLUMN `sponsor_start3` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '现场控制 0 1 2 3 4 5 ' AFTER `sponsor_start2`,
ADD COLUMN `grade` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '活动评价等级 0 1 差评 2 中评 3 好评' AFTER `sponsor_start3`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropColumn('activity_feedback', 'sponsor_start1');
        $this->dropColumn('activity_feedback', 'sponsor_start2');
        $this->dropColumn('activity_feedback', 'sponsor_start3');
        $this->dropColumn('activity_feedback', 'grade');
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
