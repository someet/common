<?php

use yii\db\Migration;

class m160427_114136_add_space_spot_device extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `space_spot_device` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL COMMENT '名称',
  `icon` VARCHAR(180) NOT NULL DEFAULT 0 COMMENT '图标',
  `display_order` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `status` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态 0 未设置 10 可用 20 不可用',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = '地点设备'
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropTable("space_spot_device");
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
