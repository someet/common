<?php

use yii\db\Schema;
use yii\db\Migration;

class m151009_090521_create_special_table extends Migration
{
    public function safeUp()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `special` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL COMMENT '标题',
  `desc` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '描述',
  `poster` VARCHAR(45) NOT NULL COMMENT '海报',
  `displayorder` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '显示排序',
  `sharetimes` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分享次数',
  `created_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '制作时间',
  `updated_at` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0 删除 10 草稿 20 正常',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = '活动专题表';
SQL;
        $this->execute($sql);
        return true;

    }

    public function safeDown()
    {
        $this->dropTable('special');
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
