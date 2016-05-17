<?php

use yii\db\Schema;
use yii\db\Migration;

class m151012_120942_update_activity_fields extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity`
CHANGE COLUMN `starttime` `start_time` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '活动开始时间' ,
CHANGE COLUMN `endtime` `end_time` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '活动结束时间' ,
CHANGE COLUMN `groupcode` `group_code` VARCHAR(45) NOT NULL COMMENT '群二维码' ,
CHANGE COLUMN `isvolume` `is_volume` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 非系列 1 系列活动' ,
CHANGE COLUMN `isdigest` `is_digest` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 非精华 1 精华' ,
CHANGE COLUMN `responsi` `principal` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '负责人 0为未设置' ,
ADD COLUMN `is_top` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 正常 1 置顶' AFTER `is_digest`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151012_120942_update_activity_fields cannot be reverted.\n";

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
