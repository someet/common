<?php

use yii\db\Schema;
use yii\db\Migration;

class m160124_005330_add_four_co_founders extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity`
ADD COLUMN `co_founder1` INT(11) NULL DEFAULT 0 COMMENT '联合发起人1' AFTER `field8`,
ADD COLUMN `co_founder2` INT(11) NULL DEFAULT 0 COMMENT '联合发起人2' AFTER `co_founder1`,
ADD COLUMN `co_founder3` INT(11) NULL DEFAULT 0 COMMENT '联合发起人3' AFTER `co_founder2`,
ADD COLUMN `co_founder4` INT(11) NULL DEFAULT 0 COMMENT '联合发起人4' AFTER `co_founder3`;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('activity', 'co_founder1');
        $this->dropColumn('activity', 'co_founder2');
        $this->dropColumn('activity', 'co_founder3');
        $this->dropColumn('activity', 'co_founder4');
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
