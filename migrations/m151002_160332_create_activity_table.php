<?php

use yii\db\Schema;
use yii\db\Migration;

class m151002_160332_create_activity_table extends Migration
{
    public function safeUp()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `activity` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_id` INT(11) UNSIGNED NOT NULL COMMENT '分类ID',
  `title` CHAR(80) NOT NULL COMMENT '标题',
  `desc` VARCHAR(255) NOT NULL COMMENT '描述',
  `poster` VARCHAR(255) NOT NULL COMMENT '海报',
  `week` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '星期 按照活动时间自动计算',
  `starttime` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '活动开始时间',
  `endtime` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '活动结束时间',
  `area` CHAR(10) NOT NULL COMMENT '范围, 比如雍和宫',
  `address` VARCHAR(255) NOT NULL COMMENT '活动详细地址',
  `details` TEXT(1000) NOT NULL COMMENT '活动详情',
  `groupcode` VARCHAR(45) NOT NULL COMMENT '群二维码',
  `longitude` DOUBLE NOT NULL COMMENT '经度',
  `latitude` DOUBLE NOT NULL COMMENT '纬度',
  `cost` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 免费 大于0 则收费',
  `peoples` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 不限制 >1 则为限制人数',
  `isvolume` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 非系列 1 系列活动',
  `isdigest` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 非精华 1 精华',
  `responsi` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '负责人 0为未设置',
  `created_at` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `updated_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '20 删除 40 草稿 60 审核不通过 80 审核通过 100 进行中 120 已结束 ',
  PRIMARY KEY (`id`))
COMMENT = '活动表';
SQL;
        $this->execute($sql);
        return true;

    }

    public function safeDown()
    {
        $this->dropTable('activity');
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
