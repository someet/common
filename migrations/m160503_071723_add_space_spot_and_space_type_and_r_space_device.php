<?php

use yii\db\Migration;

class m160503_071723_add_space_spot_and_space_type_and_r_space_device extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `space_spot` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(180) NULL DEFAULT NULL COMMENT '地点名称',
  `area` VARCHAR(180) NULL DEFAULT NULL COMMENT '商圈',
  `address` VARCHAR(180) NULL DEFAULT NULL COMMENT '详细地址',
  `type_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '场地分类编号',
  `image` VARCHAR(180) NULL DEFAULT NULL COMMENT '图片',
  `router` VARCHAR(180) NULL DEFAULT NULL COMMENT '到达路线信息，例如公交地铁',
  `map_pic` VARCHAR(180) NULL DEFAULT NULL COMMENT '地图图片',
  `detail` TEXT(65535) NULL DEFAULT NULL COMMENT '地点详情',
  `contact` VARCHAR(180) NULL DEFAULT NULL COMMENT '场地负责人联系方式',
  `base_fee` VARCHAR(180) NULL DEFAULT NULL COMMENT '最低消费',
  `principal` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '官方负责人',
  `logo` VARCHAR(45) NULL DEFAULT NULL,
  `owner` VARCHAR(180) NULL DEFAULT NULL COMMENT '场地所有者名称/公司',
  `open_time` VARCHAR(180) NULL DEFAULT NULL COMMENT '开放时间',
  `longitude` DOUBLE NULL DEFAULT NULL COMMENT '经度',
  `latitude` DOUBLE NULL DEFAULT NULL COMMENT '纬度',
  `status` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = '空间地点';

CREATE TABLE IF NOT EXISTS `space_type` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL COMMENT '类型名称，例如酒吧，咖啡厅',
  `display_order` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '显示排序',
  `status` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态 0 默认值 10 可用 20 删除',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = '空间类型';

CREATE TABLE IF NOT EXISTS `r_spot_device` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `spot_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '空间编号',
  `device_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '设备编号',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = '空间设备关联';
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropTable('space_spot');
        $this->dropTable('space_type');
        $this->dropTable('r_spot_device');
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
