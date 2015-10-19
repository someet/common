<?php

use yii\db\Schema;
use yii\db\Migration;

class m151019_032728_add_activity_fields extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity`
DROP COLUMN `principal`,
DROP COLUMN `is_digest`,
DROP COLUMN `is_volume`,
DROP COLUMN `group_code`,
DROP COLUMN `end_time`,
DROP COLUMN `start_time`,
CHANGE COLUMN `type_id` `type_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类ID' ,
ADD COLUMN `start_time` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '活动开始时间' AFTER `week`,
ADD COLUMN `end_time` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '活动结束时间' AFTER `start_time`,
ADD COLUMN `group_code` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '群二维码' AFTER `details`,
ADD COLUMN `cost_list` VARCHAR(255) NOT NULL DEFAULT 0 COMMENT '收费明细 当收费模式有值' AFTER `cost`,
ADD COLUMN `is_volume` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 非系列 1 系列活动' AFTER `peoples`,
ADD COLUMN `is_digest` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 非精华 1 精华' AFTER `is_volume`,
ADD COLUMN `principal` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '负责人 0为未设置' AFTER `is_top`,
ADD COLUMN `review` TEXT NULL DEFAULT NULL COMMENT '活动回顾' AFTER `principal`;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m151019_032728_add_activity_fields cannot be reverted.\n";

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
