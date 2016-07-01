<?php

use yii\db\Schema;
use yii\db\Migration;

class m160613_114503_add_device_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `app_device` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `platform` VARCHAR(64) NOT NULL DEFAULT '' COMMENT 'ios/android',
  `device_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '设备标识',
  `jiguang_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '极光id',
  `alias_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '极光别名，安卓同device_id, ios为设置极光推送别名',
  `apple_token` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '苹果token',
  `app_name` VARCHAR(64) NOT NULL DEFAULT '' COMMENT 'app名称',
  `app_version` VARCHAR(64) NOT NULL DEFAULT '' COMMENT 'app版本',
  `created_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上次访问时间',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1:开启 2:关闭',
  `jsd_show` TINYINT(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '1:显示 2:不显示',
  `device_model` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '设备型号',
  `push_provider` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '推送服务商 1:jpush',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idx_did` (`device_id` ASC),
  INDEX `idx_uid` (`user_id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropTable('app_device');
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
