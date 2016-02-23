<?php

use yii\db\Schema;
use yii\db\Migration;

class m160119_104630_add_activity_ext_fields extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        ALTER TABLE `activity`
ADD COLUMN `field1` TEXT NULL DEFAULT NULL COMMENT '自定义字段1' AFTER `content`,
ADD COLUMN `field2` TEXT NULL DEFAULT NULL COMMENT '自定义字段2' AFTER `field1`,
ADD COLUMN `field3` TEXT NULL DEFAULT NULL COMMENT '自定义字段3' AFTER `field2`,
ADD COLUMN `field4` TEXT NULL DEFAULT NULL COMMENT '自定义字段4' AFTER `field3`,
ADD COLUMN `field5` TEXT NULL DEFAULT NULL COMMENT '自定义字段5' AFTER `field4`,
ADD COLUMN `field6` TEXT NULL DEFAULT NULL COMMENT '自定义字段6' AFTER `field5`,
ADD COLUMN `field7` TEXT NULL DEFAULT NULL COMMENT '自定义字段7' AFTER `field6`,
ADD COLUMN `field8` TEXT NULL DEFAULT NULL COMMENT '自定义字段8' AFTER `field7`;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('activity', 'field1');
        $this->dropColumn('activity', 'field2');
        $this->dropColumn('activity', 'field3');
        $this->dropColumn('activity', 'field4');
        $this->dropColumn('activity', 'field5');
        $this->dropColumn('activity', 'field6');
        $this->dropColumn('activity', 'field7');
        $this->dropColumn('activity', 'field8');
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
